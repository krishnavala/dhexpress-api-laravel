<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerPdf;
use App\Models\CustomerDetail;
use App\Models\Product;
use App\Traits\UploadFileTrait;
use App\Http\Requests\Admin\CustomerRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception, Crypt,Str;


class CustomerController extends Controller
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

    /**
     * Show user list
     * 
     */
    public function index($id = null)
    {
        $title = __('pages.customer.title');
        $add_title = __('pages.customer.add');
        $add_route = route('admin.customer.manage.form');
        $customer_name = $customer_route = null;
        $customerInfo = null;
        if (!empty($id)) {
            $title = __('pages.sub_customer.title');
            $add_title = __('pages.sub_customer.add');
            $add_route = route('admin.customer.add', $id);
            $customerInfo = Customer::find($id);
            $customer_name = !empty($customerInfo) ? "(" . $customerInfo->name . ")" : null;
            $customer_route = route('admin.customer');
        }
        return view('admin.customer.index', compact('id', 'title', 'add_title', 'add_route', 'customer_name', 'customer_route','customerInfo'));
    }

    public function customerList(Request $request, $id = null)
    {
        try {
            $columns = array(
                0 => 'customer_code',
                1 => 'group',
                2 => 'customer_name',
                3 => 'pin_code',
                4 => 'contact_no',
                5 => 'invoice',
            );
            $orderVal = isset($columns[$request->input('order.0.column')]) ? $columns[$request->input('order.0.column')] : null;
            $direction = $request->input('order.0.dir');
            $sarchVal = !empty($request->input('search.value')) ? $request->input('search.value') : null;
            $limit = $request->input('length');
            $start = $request->input('start');

            $customerContent = Customer::query()
            ->join('customer_detail', 'customers.id', '=', 'customer_detail.customer_id');

            if (!empty($orderVal)) {
                if($orderVal == 'customer_code'){
                    $customerContent = $customerContent->orderBy('customers.'.$orderVal, $direction);
                }else{
                    $customerContent = $customerContent->orderBy('customer_detail.'.$orderVal, $direction);
                }
            }
            else{
                $customerContent = $customerContent->orderBy('customers.id','DESC');
            }
            $totalacustomerContentContent = $customerContent->count();
            if (!empty($sarchVal)) {
                $customerContent = $customerContent->where(function ($q) use ($sarchVal) {
                    $q->orWhere("customer_code", 'LIKE', "%$sarchVal%");
                    $q->orWhere("group", 'LIKE', "%$sarchVal%");
                    $q->orWhere("customer_name", 'LIKE', "%$sarchVal%");
                    $q->orWhere("pin_code", 'LIKE', "%$sarchVal%");
                    $q->orWhere("contact_no", 'LIKE', "%$sarchVal%");
                    $q->orWhere("invoice", 'LIKE', "%$sarchVal%");
                });
            }
            $totalaFiltercustomerContent = $customerContent->count();
            $customerContent = $customerContent->offset($start)->limit($limit)->get();
            
            $pdata = [];
            if (count($customerContent)) {
                foreach ($customerContent as $pkey => $pval) {
                    $data = [];
                 
                   $data['customer_code'] = $pval->customer_code ?? '-';
                   $data['group'] = $pval->group ?? '-';
                   $data['customer_name'] = $pval->customer_name ?? '-';
                   $data['pin_code'] = $pval->pin_code ?? '-';
                   $data['contact_no'] = $pval->contact_no ?? '-';
                   $data['invoice'] = $pval->invoice ?? '-';

                    
                    
                        // $editUrl = "<a href='" . route('admin.customer.edit', $pval->uuid)  . "' class='ml-2'><i class='fas fa-pencil-alt' ></i></a>";
                    $editUrl = "<a href='" . route('admin.customer.manage.form', $pval->uuid)  . "' class='btn btn-primary'><i class='fas fa-pencil-alt' ></i></a>";
                    $delUrl = "<a href='#' data-id='" . $pval->uuid . "' class='delete_customer btn btn-danger ml-2' style='text-decoration:none;'><i class='fas fa-trash'></i></a>";

                    $data['action'] =  $editUrl . $delUrl;
                    $pdata[] = $data;
                }
            }
            $jsonData = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalacustomerContentContent,
                "recordsFiltered" => $totalaFiltercustomerContent,
                "data" => $pdata
            );
            return response()->json($jsonData);
        } catch (Exception $ex) {
            $response['status'] = false;
            $response['message'] = __('admin_message.exception_message');
            return response()->json($response);
        }
    }
    public function customerForm(Request $request) {
        try {
            $uuId = $request->uuid ?? 0;
            $customerID =0;
            if (!empty($uuId)) {
                $customer = Customer::where('uuid', $uuId)->with('customerDetail')->first();
                if (empty($customer)) {
                    return view('errors.404');
                }else{
                    $customerID =$customer->id ?? '';
                }
            }
            $formTitle = __('pages.customer.edit');
            if (empty($uuId)) {
                $customer = new Customer;
                $formTitle = __('pages.customer.add');
            }
           
            return view('admin.customer.form', compact('customer', 'formTitle', 'uuId', 'customerID'));

        } catch (\Exception $ex) {
            Log::info('Exception while manage customer');
            Log::error($ex);
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back();
        }
    }
   

    public function store(Request $request)
    {
       
     
        DB::beginTransaction();
        try {
            $customerID = $request->customerID ?? '';
            $uuid = $request->uuid ?? '';
            $type = $request->type ?? Customer::SAVE;
            $customerCode = $request->customer_code ?? NULL;
            $group = $request->group ?? NULL;
            $customerName = $request->customer_name ?? NULL;
            $address = $request->address ?? NULL;
            $pinCode = $request->pin_code ?? NULL;
            $contactNo = $request->contact_no ?? NULL;
            $invoice = $request->invoice ?? NULL;
            $remarks = $request->remarks ?? NULL;

            DB::enableQueryLog();
            $customer = Customer::where('id', $customerID)->first();
            $message = __('admin_message.customer_msg.update');

            if($customer && $customer->uuid && $customer->customer_code){
                $customerCode = $customer->customer_code;
                $uuid = $customer->uuid;
                $message = __('admin_message.customer_msg.updated');
            }else{
                $customer = new Customer;
                $uuid =  Str::uuid(); 
                $message = __('admin_message.customer_msg.add');
            }
           
            $customer->uuid =  $uuid;
            $customer->customer_code = $customerCode;
            $customer->save();
            $customerId = $customer->id;

            if (!empty($customerId)) {
                $customerDetail = CustomerDetail::where('customer_id', $customerId)->first();
                if (!isset($customerDetail->id)) {
                    $customerDetail = new CustomerDetail;
                }

                $customerDetail->group = $group;
                $customerDetail->customer_name = $customerName;
                $customerDetail->address = $address;
                $customerDetail->pin_code = $pinCode;
                $customerDetail->contact_no = $contactNo;
                $customerDetail->invoice = $invoice;
                $customerDetail->remarks = $remarks;
                $customerDetail->customer_id = $customerId;
                $customerDetail->save();

                //MOVE TO PDF LIST;
                if ($type == Customer::SAVE_AND_MOVE) {
                    CustomerPdf::updateOrCreate(['customer_id' => $customerId], []);
                }
            }
            DB::commit();
            notify()->success($message);
           
            return redirect()->route('admin.customer');
            
        } catch (Exception $ex) {
            DB::rollback();
            Log::info('Exception while storing category page');
            Log::error($ex);
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back();
        }
    }

    
    public function customerSearch(Request $request)
    {
        try {
            $searchCode = $request->searchCode ?? NULL;
            $customer = Customer::where('customer_code', $searchCode)->with('customerDetail')->first();
           
            if(empty($customer->id)) {
                $response['status'] = false;
                $response['message'] = __('admin_message.customer_code_not_find');
                return response()->json($response);
            }
            $response = [];
            $response['status'] = true;
            $response['customer'] =$customer;
            $response['message'] = __('admin_message.customer_msg.delete');
            return response()->json($response);
        } catch (Exception $ex) {
            Log::info('Error in delete customer');
            Log::error($ex);
            $response = [];
            $response['status'] = false;
            $response['message'] = __('admin_message.exception_message');
            return response()->json($response);
        }
    }
    public function customerDelete(Request $request)
    {
        try {
            $customerUuId = $request->customer_uuid ?? '';
            $customer = Customer::where('uuid', $customerUuId)->first();
           
            if(empty($customer->id)) {
                $response['status'] = false;
                $response['message'] = __('admin_message.customer_not_find');
                return response()->json($response);
            }
            $customer->delete();
            $response = [];
            $response['status'] = true;
            $response['message'] = __('admin_message.customer_msg.delete');
            return response()->json($response);
        } catch (Exception $ex) {
            Log::info('Error in delete customer');
            Log::error($ex);
            $response = [];
            $response['status'] = false;
            $response['message'] = __('admin_message.exception_message');
            return response()->json($response);
        }
    }
}
