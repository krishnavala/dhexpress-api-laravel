<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerPdf;
use Illuminate\Support\Facades\Log;
use Exception, PDF;



class PDFDataController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
    }

 
    public function index($id = null)
    {
        $title = __('pages.pdf.title');
        $download_title = __('pages.pdf.download');
        $download_route = route('admin.customer-pdf.download');
        $customerInfo = null;
        return view('admin.customer.index-pdf', compact('id', 'title', 'customerInfo','download_title','download_route'));
    }

    public function customerPDFList(Request $request, $id = null)
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
            ->has('customerPdf')
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

                   $editUrl = "<a href='" . route('admin.customer.manage.form', $pval->uuid)  . "'><i class='fas fa-pencil-alt' ></i></a>";
                   $delUrl = "<a href='#' data-id='" . $pval->uuid . "' class='delete_customer ml-2' style='text-decoration:none;'><i class='fas fa-trash'></i></a>";
                    $data['action'] = $editUrl . $delUrl;
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
    
    public function downloadPDF(Request $request)
    {
        try {
            $customerList = Customer::with(['customerDetail'])->has('customerPdf')->get();
            if(count( $customerList) == 0 ){
                notify()->error('The customer was not found in the PDF list..');
                return redirect()->back();
            }
            $pdfContent = PDF::loadView('pdf',compact('customerList'));
            //  return view('pdf', compact('customerList'));
            return $pdfContent->download('invoice.pdf');
           
        } catch (Exception $ex) {
            Log::info('Exception while downloading pdf page');
            Log::error($ex);
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back();
        }
    }
    public function pdfDelete(Request $request)
    {
        try {
            $customerUuId = $request->customer_uuid ?? '';
            $customer = Customer::where('uuid', $customerUuId)->first();
           
            if(empty($customer->id)) {
                $response['status'] = false;
                $response['message'] = __('admin_message.customer_not_find');
                return response()->json($response);
            }
            $CustomerPdf = CustomerPdf::where('customer_id', $customer->id)->delete();
            $response = [];
            $response['status'] = true;
            $response['message'] = __('admin_message.pdf_msg.delete');
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
