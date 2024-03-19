<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Models\Product;
use App\Models\Category;
use App\Models\Country;
use App\Models\ProductImage;
use App\Models\FlashSaleProduct;
use App\Models\ProductDetail;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception, Crypt, Str, Storage;
use App\Models\Unit;
use App\Models\Cart;
use App\Models\Status;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use App\Models\ProductType;

class ProductController extends Controller
{
    use UploadFileTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        return view('admin.product.index');
    }

    public function productList(Request $request)
    {
        try {
            $columns = array(
                0 => 'id',
                2 => 'name',
                3 => 'category',
                4 => 'sku',
                5 => 'price',
                6 => 'status'
            );
            $orderVal = isset($columns[$request->input('order.0.column')]) ? $columns[$request->input('order.0.column')] : null;
            $direction = $request->input('order.0.dir');
            $sarchVal = !empty($request->input('search.value')) ? $request->input('search.value') : null;
            $limit = $request->input('length');
            $start = $request->input('start');
            $ative=Product::ACTIVE;
            $inative=Product::INACTIVE;
            $productData = Product::leftJoin('product_variants', function($query) {
                    $query->on('product_variants.product_id','=','products.id')
                        ->whereRaw('product_variants.id IN (select MAX(pv2.id) from product_variants as pv2 join products as p2 on p2.id = pv2.product_id group by p2.id)');
                })
                ->leftJoin('categories', function($query1) {
                    $query1->on('categories.id','=','products.category_id');
                })
                ->select('products.id','products.status','products.uuid','products.name','products.sku','product_variants.price','product_variants.product_id','categories.name as category_name');
            if (!empty($orderVal)) {
               
                if($orderVal=='price'){
                    $orderVal='product_variants.'.$orderVal;
                }elseif($orderVal=='category'){
                    $orderVal='categories.name';
                }else{
                    $orderVal='products.'.$orderVal;
                }
                $productData = $productData->orderBy($orderVal, $direction);
            }else{
                $productData = $productData->orderBy('products.id', 'desc');
            }
            $totalData = $productData->count();
            if (!empty($sarchVal)) {
                $productData = $productData->where(function ($q) use ($sarchVal,$ative,$inative) {
                    $q->orWhere("products.name", 'LIKE', "%$sarchVal%");
                    $q->orWhere("categories.name", 'LIKE', "%$sarchVal%");
                    $q->orWhere("product_variants.price", 'LIKE', "%$sarchVal%");
                    $q->orWhere("products.sku", 'LIKE', "%$sarchVal%");
                    if(strtolower($sarchVal) =='active')
                    $q->orWhere("products.status", 'LIKE', "%$ative%");
                    if(strtolower($sarchVal) =='in active')
                    $q->orWhere("products.status", 'LIKE', "%$inative%");
                });
            }   

            $totalaFilterData = $productData->count();
            
            $productData = $productData->offset($start)->limit($limit)->get();
            
            $pdata = [];
            if (count($productData)) {
                foreach ($productData as $pkey => $pval) {
                    $data = [];
                    $status=__('forms.product.inactive');
                    if($pval->status == Product::ACTIVE){
                        $status=__('forms.product.active');
                    }
                    $data['id'] = $pval->id;
                    $data['name'] = $pval->name;
                    $data['category_name'] = $pval->category_name;
                    $data['sku'] = $pval->sku;
                    $data['price'] = $pval->price;
                    $data['image'] = "<img  src='".$pval->product_image_url."' alt='Product Image' style='width:100px;height:80px;'>";
                    $data['price'] = $pval->price;
                    $data['status'] = $status;
                    $editUrl = "<a href='" . route('admin.product.edit', $pval->uuid)  . "'><i class='fas fa-pencil-alt' ></i></a>";
                    $data['detail'] = $editUrl;
                    $pdata[] = $data;
                }
            }
            $jsonData = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalData,
                "recordsFiltered" => $totalaFilterData,
                "data" => $pdata
            );
            return response()->json($jsonData);
        } catch (Exception $ex) {
            Log::info('Exception while product list');
            Log::error($ex);
            $response['status'] = false;
            $response['message'] = __('admin_message.admin_exception_message');
            return response()->json($response);
        }
    }

    
    public function storeProduct(Request $request) {
       
        DB::beginTransaction();
        try {
            $uuId = $request->uuid ?? 0;
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'sku' => 'required',
                'category_id' => 'required',
                'description' => 'required',
                'price_variation[*]' => 'required',
                'product_detail[*]' => 'required',
                'images[*]' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
            ]);
            
            if ($validator->fails()) {
                notify()->error(__('admin_message.exception_message'));
              
                return redirect()->back()->withErrors($validator)->withInput();
            }
           
            $userId = Auth::user()->id;
            $name = $request->name ?? NULL;
            $sku = $request->sku ?? NULL;
            $categoryId = $request->category_id ?? NULL;
            $brand = $request->brand ?? NULL;
            $description = $request->description ?? NULL;
            $shortDescription = $request->short_description ?? NULL;
            $manufacturerDetail = $request->manufacturer_detail ?? NULL;
            $productTypeId = $request->product_type_id ?? NULL;
            $status = $request->status ?? Product::ACTIVE;
            
           
            $imageArr = !empty($request->images)?$request->images:[];
            $removeAttachment = $request->remove_attachment ?? NULL;
            $priceVariationArr = !empty($request->price_variation)?$request->price_variation:[];

            $productinfo = new Product;
            if (!empty($uuId)) {
                $productinfo = Product::where('uuid',$uuId)->first();
                $message = __('admin_message.product_edit_success');
                if (empty($productinfo)) {
                    $productinfo = new Product;
                    $message = __('admin_message.product_add_success');
                    $productinfo->uuid = Str::uuid();
                }
            }else{
                $productinfo->uuid = Str::uuid();
                $message = __('admin_message.product_add_success');
            }
            $productinfo->name = $name;
            $productinfo->created_by = $userId;
            
            $productinfo->sku = $sku;
            $productinfo->category_id = $categoryId;
            $productinfo->brand = $brand;
            $productinfo->country_id = Country::Botswana;
            $productinfo->manufacturer_detail = $manufacturerDetail;
            $productinfo->description = $description;
            $productinfo->short_description = $shortDescription;
            $productinfo->status = $status;
            $productinfo->product_type_id = $productTypeId;
            
            $productinfo->save();
            $productId = $productinfo->id;
          
            //save new images start
            $removeImageArr=[];
            $removeAttachment = json_decode($removeAttachment, true);
            if(!empty($removeAttachment)){
                $removeImageArr=array_column($removeAttachment, 'FileName');
            }
            if(count($imageArr)) {
                foreach($imageArr as $ikey => $ifiles) {
                    $inRemoveArray = in_array($ifiles->getClientOriginalName(), $removeImageArr);
                    $dirName = "product-images";
                    if (!empty($ifiles) && !$inRemoveArray) {
                        $fileResponse = $this->uploadFileOnLocal($ifiles, $dirName);
                        if (!empty($fileResponse['status']) && $fileResponse['file_name']) {
                            $productImage = $fileResponse['file_name'];
                            $prodImage = new ProductImage;
                            $prodImage->product_id = $productId;
                            $prodImage->name = $productImage;
                            $prodImage->save();
                        }
                    }
                }
            }
            //save product price variation start
            if(count($priceVariationArr)) {
                $productVariationsIds=[];
                foreach($priceVariationArr as $priceVarKey => $priceVarVal) {
                    if(!empty($priceVarVal['price_variation_id'])){
                        $prodVariation = ProductVariant::where('id',$priceVarVal['price_variation_id'])->first();
                        if (!isset($prodVariation->id)) {
                            $prodVariation = new ProductVariant;
                        }
                    }else{
                        $prodVariation = new ProductVariant;
                    }
                    $prodVariation->product_id = $productId;
                    $prodVariation->price = $priceVarVal['price'];
                    $prodVariation->available_stock = $priceVarVal['available_stock'];
                    $prodVariation->quantity = $priceVarVal['quantity'];
                    $prodVariation->unit_id = $priceVarVal['unit_id'];
                    $prodVariation->expiry_date = $priceVarVal['expiry_date'];
                    $prodVariation->status = $priceVarVal['status'];
                    $prodVariation->save();
                    $productVariationsIds[]=$prodVariation->id;
                }
                if(!empty($productVariationsIds)){
                    // Cart::where('product_id', $productId)->whereNotIn('product_variations_id',$productVariationsIds)->delete();
                    ProductVariant::where('product_id',$productId)->whereNotIn('id',$productVariationsIds)->delete();
                }
            }
            //save product price variation end
            DB::commit();
            notify()->success($message);
            return redirect()->route('admin.product.index');
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info('Exception while storing product');
            Log::error($ex);
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back()->withInput();
        }
    }

    public function deleteProduct(Request $request)
    {
        try {
            $productId = Product::where('uuid',$request->uuid)->pluck('id');
            $productVariation = ProductVariant::where('product_id',$productId)->get();
            if(empty($productVariation)) {
                $response['status'] = false;
                $response['message'] = __('admin_message.product_not_find');
                return response()->json($response);
            }

            Product::where('id',$productId)->delete();
            ProductVariant::where('product_id',$productId)->delete();
            $response = [];
            $response['status'] = true;
            $response['message'] = __('admin_message.product_delete_success');
            return response()->json($response);
        } catch (Exception $ex) {
            Log::info('Error in Product delete');
            Log::error($ex);
            $response = [];
            $response['status'] = false;
            $response['message'] = __('admin_message.exception_message');
            return response()->json($response);
        }
    }


    public function addPriceVariation(Request $request) {
        $response = [];
        try {
            $count = !empty($request->count)?$request->count:0;
            $unitData = Unit::pluck('name', 'id');
            $varioanTypeHtml = view('admin.product.variation-type-list',compact('count','unitData'))->render();
            $response['status'] = true;
            $response['html'] = $varioanTypeHtml;
            $response['message'] = __('admin_message.success');
            return response()->json($response);
        } catch (\Exception $ex) {
            Log::info('Exception while load price variation');
            Log::error($ex);
            $response['status'] = false;
            $response['message'] = __('admin_message.admin_exception_message');
            return response()->json($response);
        }
    }

   
    public function productForm(Request $request, $uuid = null) {
        
        $uuId = $request->uuid ?? 0;
        $defaultImage = asset(config('constant.no_image'));
        $mainCategory = '';
        $productTypes  = '';

        if (!empty($uuId)) {
            $productInfo = Product::with(['product_images','product_variations'])->withCount('product_variations')->withCount('product_detail')->where('uuid',$uuId)->first();
            if (empty($productInfo)) {
                return view('errors.404');
            }
            $sku=$productInfo->sku;
            $formTitle = __('pages.product.edit');
        } else {
            $productInfo = new Product;
            $sku = '01';
            $formTitle = __('pages.product.add');
        }
        $productVariantionsCount=$productInfo->product_variations_count ?? 0;
        $unitData = '';
        
        return view('admin.product.form',compact('formTitle','defaultImage','mainCategory','sku','productInfo','uuId','productVariantionsCount','unitData','productTypes'));
    }
    public function deleteProductImage(Request $request)
    {
        try {
            $id =$request->product_img_id ??  0;
            $name =$request->name ??  0;    
            ProductImage::where('id',$id)->delete();
            $file = env('STORAGE_URL') .'uploads/product-images/'.$name;
            
            if(Storage::exists($file)){
                Storage::delete($file);
            }
            $response = [];
            $response['status'] = true;
            $response['message'] = __('admin_message.delete_product_image');
            return response()->json($response);
        } catch (Exception $ex) {
            Log::info('Error in delete file');
            Log::error($ex);
            $response = [];
            $response['status'] = false;
            $response['message'] = __('admin_message.exception_message');
            return response()->json($response);
        }
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function importExcelCSV(Request $request) 
    {
        //Validate File
        $validatedData = $request->validate([
           'file' => 'required|mimes:xlsx',
        ],[
            'file.required' => 'The file is required.']);

        try {
            Excel::import(new ProductImport,$request->file('file'));
        } catch (ValidationException $e) {
            Log::info( $e->getMessage());
            notify()->error( $e->getMessage()); 
            return redirect()->back()->withErrors($e->errors());
        }
        notify()->success(__('admin_message.product_import_success')); 
        return redirect('admin/products');
    }

    public function downloadSampleExcelFile(){
        $pathToFile = public_path('admin/smaple-excel/Sample.xlsx');

        $headers = array(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );

        return response()->download($pathToFile, 'Sample.xlsx', $headers);
    }
}
