<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Country;
use App\Models\Unit;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Storage;
use App\Traits\UploadFileTrait;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Response;
use Log, DB;
use File;

class ProductImport implements ToModel, WithValidation, SkipsEmptyRows, WithHeadingRow
{
    use UploadFileTrait;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try{
            if (empty(array_filter($row))) {
                return null;
            }
                $categoryName = $row['category'];
                $slugifyCategory = slugify($categoryName);

                $category = Category::where('slug',$slugifyCategory)->first();
                if(empty($category)){
                    //Save Product New Category
                    $category = new Category;
                    $category->uuid = getUuid();
                    $category->name = $categoryName;
                    $category->slug = $slugifyCategory;
                    $category->parent_id = Category::CATEGORY;
                    $category->status = Category::ACTIVE;
                    $category->save();
                }
                
                $productName = str_replace(' ','',strtolower($row['product_name']));
                
                $product = Product::whereRaw(DB::raw("REPLACE(LOWER(`name`),' ','') = '{$productName}'"))->first();
                $status = $this->getStatus($row['status']);

                if(empty($product)){
                    //Storage Save Product Image 
                    $myfile = $row['product_image'];
                    $fileName = substr($myfile, strrpos($myfile, '/') + 1);

                    $fileContents = file_get_contents($myfile); 
                    $storagePath = "/uploads/product-images/$fileName";
                    $putContent = Storage::disk('public')->put($storagePath, $fileContents);

                    //Save Product Data
                    $product = new Product;
                    $product->uuid = getUuid();
                    $product->sku = Product::generateSKU();
                    $product->name = $row['product_name'];
                    $product->category_id = $category->id;
                    $product->country_id = Country::Botswana;
                    $product->brand = $row['brand'];
                    $product->status = $status;
                    $manufacturer_detail = $row['manufacturer_detail'];
                    $product->manufacturer_detail = "<p>$manufacturer_detail</p>";
                    $short_description = $row['short_description'];
                    $product->short_description = "<p>$short_description</p>";
                    $description = $row['description'];
                    $product->description = "<p>$description</p>";
                    $product->created_by = 1;
                    $product->save();

                    //Save Product Image
                    $productImage = new ProductImage;
                    $productImage->product_id = $product->id;
                    $productImage->name = $fileName;
                    $productImage->is_feature = ProductImage::FEATUREIMAGE;
                    $productImage->save();
                }else{
                    $product->category_id = $category->id;
                    $product->brand = $row['brand'];
                    $product->status = $status;
                    $manufacturer_detail = $row['manufacturer_detail'];
                    $product->manufacturer_detail = "<p>$manufacturer_detail</p>";
                    $short_description = $row['short_description'];
                    $product->short_description = "<p>$short_description</p>";
                    $description = $row['description'];
                    $product->description = "<p>$description</p>";
                    $product->save();

                    //Storage Save Product Image 
                    $myfile = $row['product_image'];
                    
                    $fileName = substr($myfile, strrpos($myfile, '/') + 1);

                    $productImage = ProductImage::where('product_id',$product->id)->first();

                    if($productImage->name != $fileName){
                        $fileContents = file_get_contents($myfile);     
                        $storagePath = "/uploads/product-images/$fileName";
                        $putContent = Storage::disk('public')->put($storagePath, $fileContents);

                        $productImage->name = $fileName;
                        $productImage->is_feature = ProductImage::FEATUREIMAGE;
                        $productImage->save();
                    }
                }

                $variantStatus = $this->getStatus($row['product_status']);
                $unit = $this->getUnit($row['unit']);
                $productVariant = ProductVariant::where('product_id',$product->id)->where('quantity',$row['quantity'])->where('unit_id',$unit)->first();

                if(empty($productVariant)){
                    //Save Product Variant
                    $productVariant = new ProductVariant;
                    $productVariant->product_id = $product->id;
                    $productVariant->quantity = $row['quantity'];
                    $productVariant->unit_id = $unit;
                    $productVariant->price = $row['price'];
                    $productVariant->expiry_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expiry_date']);
                    $productVariant->available_stock = $row['available_stock'];
                    $productVariant->status = $variantStatus;
                    $productVariant->save();
                }else{
                    //Update Product Variant
                    $productVariant->available_stock = $row['available_stock'] + $productVariant->available_stock;
                    $productVariant->price = $row['price'];
                    $productVariant->status = $variantStatus;
                    $productVariant->expiry_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expiry_date']);
                    $productVariant->save();
                }
                DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            Log::info('Exception while import products');
            Log::error($ex);
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back();
        }
        
    }

    public function rules(): array
    {
        return [
            '*.category' => 'required',
            '*.product_name' => 'required',
            '*.status' => 'required',
            '*.product_image' => [
                'required',
                'url',
                'ends_with:jpg,png,jpeg',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail("$attribute is not a valid URL");
                    }else{
                        $response = Http::get($value);
                        if (!$response->ok() ) {
                            $fail('The '.$attribute.' must be a valid URL that is open on the server.');
                        }
                    }
                },
            ],
            '*.manufacturer_detail' => 'required',
            '*.brand' => 'required',
            '*.short_description' => 'required',
            '*.description' => 'required',
            '*.product_status' => 'required',
            '*.unit' => 'required',
            '*.quantity' => 'required|numeric|min:0.00|max:99999.99',
            '*.available_stock' => 'required|numeric|min:0.00|max:99999.99',
            '*.price' => 'required|numeric|min:0.00|max:99999.99',
            '*.expiry_date' => 'required'
            
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        throw new ValidationException(null, $failures);
    }

    public function customValidationMessages()
    {
        return [
            '*.category.required' => 'The Category field is required.',
            '*.product_name.required' => 'The Product Name field is required.',
            '*.status.required' => 'The Status field is required.',
            '*.product_image.required' => 'The Product Image field is required.',
            '*.product_image.url' => 'The Product Image field is must be URL.',
            '*.product_image.ends_with' => 'The Product Image field is must be end with jpg,png,jpeg.',
            '*.manufacturer_detail.required' => 'The Manufacturer Detail field is required.',
            '*.brand.required' => 'The Brand field is required.',
            '*.short_description.required' => 'The Short Description field is required.',
            '*.description.required' => 'The Description field is required.',
            '*.product_status.required' => 'The Product Status field is required.',
            '*.unit.required' => 'The Unit field is required.',
            '*.quantity.required' => 'The Quantity field is required.',
            '*.quantity.numeric' => 'The Quantity field is must be numeric.',
            '*.quantity.min' => 'The Price field is must be greater than or equal to 0.',
            '*.quantity.max' => 'The Price field is must be less than or equal to 99999.99.',
            '*.available_stock.required' => 'The Available Stock field is required.',
            '*.available_stock.numeric' => 'The Available Stock field is must be numeric.',
            '*.available_stock.min' => 'The Available Stock field is must be greater than or equal to 0.',
            '*.available_stock.max' => 'The Available Stock field is must be less than or equal to 99999.99.',
            '*.price.required' => 'The Price field is required.',
            '*.price.numeric' => 'The Price field is must be numeric.',
            '*.price.min' => 'The Price field is must be greater than or equal to 0.',
            '*.price.max' => 'The Price field is must be less than or equal to 99999.99.',
            '*.expiry_date.required' => 'The Expiry Date field is required.'
        ];
    }
    
    public function onRow(Collection $row)
    {
        // Remove blank rows
        if ($row->filter()->isEmpty()) {
            return null;
        }

        // Apply validation rules
        $validator = \Validator::make($row->toArray(), $this->rules(), $this->customValidationMessages());

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        return $row;
    }

    public function getStatus($status){
        if($status == 'Active'){
            return Product::ACTIVE;
        }
        return Product::INACTIVE;
    }

    public function getUnit($unit){
        if($unit == 'Piece'){
            return Unit::PIECE;
        }else if($unit == 'Tablets'){
            return Unit::TABLETS;
        }else if($unit == 'ML'){
            return Unit::ML;
        }else if($unit == 'GM'){
            return Unit::GM;
        }
    }
}
