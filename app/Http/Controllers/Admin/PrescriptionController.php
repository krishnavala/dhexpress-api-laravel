<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;
use Exception, Crypt, Str;

use App\Models\ProductVariant;
use App\Models\EntityType;
use App\Models\OrderStatus;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\UserAddress;
use App\Jobs\Prescription\PrescriptionStatusChange;
use App\Models\User;
use App\Jobs\Order\OrderStatusChange;
use App\Models\OrderType;

class PrescriptionController extends Controller
{
    use ResponseTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        return view('admin.prescription.index');
    }

    public function prescriptionList(Request $request)
    {
        try {
            $columns = array(
                0 => 'users.name',
                1 => 'status',
                2 => 'updated_at',
                3 => 'detail'
            );
            $prescriptionVal = $columns[$request->input('order.0.column')] ?? NULL;
            $direction = $request->input('order.0.dir') ?? NULL;
            $sarchVal = $request->input('search.value') ?? NULL;
            $limit = $request->input('length') ?? NULL;
            $start = $request->input('start') ?? NULL;
            $prescriptionData = Prescription::with('User:id,name,deleted_at')->whereHas('User');
            if (!empty($prescriptionVal)) {
                $prescriptionData = $prescriptionData->orderBy($prescriptionVal, $direction);
            }
            else{
                $prescriptionData = $prescriptionData->latest();
            }
            $totalData = $prescriptionData->count();
            if (!empty($sarchVal)) {
                $prescriptionData = $prescriptionData->where(function ($q) use ($sarchVal) {
                    $q->whereHas('User',fn($q) => $q->where('name','LIKE', "%$sarchVal%"));
                    $q->orWhere("prescriptions.updated_at", 'LIKE', "%$sarchVal%");
                    $stausToLower = strtolower(trim($sarchVal));
                    if(strstr(Prescription::STATUS_APPROVED,$stausToLower)) 
                    {
                        $prescriptionStatus = Prescription::APPROVED;
                    }
                    else if(strstr(Prescription::STATUS_REJECTED,$stausToLower))  
                    {
                        $prescriptionStatus = Prescription::REJECTED;
                    }
                    else if(strstr(Prescription::STATUS_PENDING,$stausToLower)) 
                    {
                        $prescriptionStatus = Prescription::PENDING;
                    }
                    if(isset($prescriptionStatus))
                    {
                        $q->orWhere('status', $prescriptionStatus);
                    }
                });
            }
            $totalaFilterData = $prescriptionData->count();
            
            $prescriptionData = $prescriptionData->offset($start)->limit($limit)->get();
            
            $pdata = [];
            if (count($prescriptionData)) {
                foreach ($prescriptionData as $key => $val) {
                    $data = [];
                    $status = __("general.pending");
                    if ($val->status == Prescription::APPROVED) {
                        $status = __("general.approved");
                    }else if ($val->status == Prescription::REJECTED) {
                        $status = __("general.declined");
                    }
                    $data['name'] = $val->User->name ?? "-";
                    $data['status'] = $status;
                    $data['date'] = getDateFormate($val->created_at);
                    $rejectButton = "<button class='btn btn-danger update_prescription btn-sm mr-1' data-id='" . Crypt::encrypt($val->id) . "'  data-status='" . Prescription::REJECTED. "' data-name='decline' title='Decline' class='update_prescription text-danger btn-label'><i class='fas fa-times'></i></button>";
                    if ($val->status == Prescription::PENDING) {
                        $button = $rejectButton;
                    }else if ($val->status == Prescription::REJECTED || $val->status == Prescription::APPROVED) {
                        $button = '';
                    }
                    $viewUrl ="<a class='btn btn-primary btn-sm' href='" . route('admin.prescription.view', Crypt::encrypt($val->id))  . "' title='View Details' class='text-default btn-label'><i class='fa fa-eye'></i></a>";
                    

                    $data['detail'] = $viewUrl.' '.$button;
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
            $response['status'] = false;
            $response['message'] = __('admin_message.admin_exception_message');
            return response()->json($response);
        }
    }

    public function prescriptionForm($id) 
    {
        try
        {
            $id = !empty($id) ? Crypt::decrypt($id) : 0;
            $defaultImage = asset(config('constant.no_image')) ?? NULL;

            $prescriptionInfo = Prescription::select('id','status','user_id','remarks','user_address_id')->with('User:id,name,mobile,email,is_medical_insurance,medical_insurance_name,medical_insurance_number')->with('prescriptionImages:id,prescription_id,image')->where('id',$id)->first();
            $userAddressId = NULL;
            $prescriptionUserInfo = NULL;
            if($prescriptionInfo && $prescriptionInfo->User)
            {
            $prescriptionUserInfo = $prescriptionInfo->User;
            $userAddressId = $prescriptionInfo->user_address_id ?? NULL;
            }
            $productVariantInfo = ProductVariant::active()->select('id','unit_id','product_id','quantity','price')->with('Product:id,name')->whereHas('Product', function($q) {
                $q->active();
            })->where('available_stock','>',ProductVariant::AVAILABLE_STOCK)->get();

            $orderInfo = Order::with('orderProducts:order_id,product_id,product_variant_id,price,quantity')->select('id','delivery_charge','discount_price','sub_total','total','offer_id')->where('user_type',EntityType::Admin)->whereHas('orderPrescription',function ($orderInfoQuery) use ($id){
                return $orderInfoQuery->where('prescription_id', $id);
            })->orderBy('id','desc')->first();

            $orderProductVariant = [];
            if($orderInfo && $orderInfo->orderProducts)
            {
                $orderProductVariant =  array_column($orderInfo->orderProducts->toArray(),'product_variant_id');
            }
            $prescriptionPendingStatus = Prescription::PENDING;
            $isMedicalInsurance = Prescription::IS_MEDICAL_INSURANCE;
            return view('admin.prescription.view',compact('prescriptionUserInfo','productVariantInfo','prescriptionInfo','defaultImage','userAddressId','orderInfo','orderProductVariant','prescriptionPendingStatus','isMedicalInsurance'));
        } catch (Exception $ex) {
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back()->withInput();
        }
    }

    public function updatePrescription(Request $request) {
        DB::beginTransaction();
        try {
            $id = !empty($request->id) ? Crypt::decrypt($request->id):NULL;
            $status = $request->status ?? Prescription::PENDING;
            $statusType = $request->statusType ?? NULL;
            $rejectReason = $request->reject_reason ?? NULL;

            $prescriptionInfo = Prescription::where('id',$id)->first();

            $prescriptionInfo->status = $status;
            if(!empty($rejectReason)){
                $prescriptionInfo->reject_reason = $rejectReason;
            }
            $prescriptionInfo->save();
            DB::commit();

            if(empty($rejectReason) && $statusType == Prescription::STATUS_REJECT){
                $response = [];
                $response['status'] = false;
                $response['message'] = __('admin_message.add_decline_reason');
                return response()->json($response);
            }
            $customer = User::select('id','name','email')->where('id',$prescriptionInfo->user_id)->first();
            if (!empty($customer)) {
                PrescriptionStatusChange::dispatchAfterResponse($customer,$prescriptionInfo);
            }
            $message = __('admin_message.prescription_update_success');
            if(!empty($statusType))
            {
                $response = [];
                $response['status'] = true;
                $response['message'] = $message;
                return response()->json($response);
            }

            notify()->success($message);
            return redirect()->route('admin.prescription.index');
        }
        
        catch (\Exception $ex) {
            DB::rollBack();
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back()->withInput();
        }
    }
    public function prescriptionSubtotal(Request $request) {
        try {
           $subTotal = $request->subTotal ?? 0;
           $quantity = $request->quantity ?? NULL;
           $productID = $request->productID ?? NULL;
           $productVariantID = $request->productVariantID ?? NULL;
           $productVariantName = $request->productVariantName ?? NULL;
           $data = [];
           $data['subTotal']        = round(Prescription::DEFAULT_PRODUCT_TOTAL,2);
           $data['discount']        = round(Prescription::DEFAULT_PRODUCT_TOTAL,2);
           $data['deliveryCharges'] = round(getConfigValue('Delivery-Charges'),2);
           $data['total']           = round(Prescription::DEFAULT_PRODUCT_TOTAL,2);
           $data['status'] = true;
           $data = geTotalPriceData($subTotal);
           
           if(!empty($quantity) && !empty($productID) && !empty($productVariantID)){
                $chkDBStock = ProductVariant::active()->select('available_stock')->where('available_stock','>=',$quantity)->where('product_id',$productID)->where('id',$productVariantID)->first();
                if(empty($chkDBStock))
                {
                    $response = [];
                    $response['status'] = false;
                    $response['title'] = $productVariantName;
                    $response['message'] = __('admin_message.product_variant_stock_error');
                    return response()->json($response);
                }
                return $this->sendSuccessResponse(__('api_messages.success'),200,$data);
           }
           else{
           return $this->sendSuccessResponse(__('api_messages.success'),200,$data);
           }
        }
        catch (\Exception $ex) {
            return $this->sendServerFailResponse(__('admin_message.exception_message'));
        }
    }

    public function savePrescriptionOrder(Request $request) {
        try {
            $requestData = $request->all();        
            $form_data=  array();
            parse_str($requestData['form_data'], $form_data);

            //generate Order Number;
            $orderNumber = generateOrderNumber();

            //request parameters
            $subTotal  = $form_data['countSubTotal'] ?? NULL;
            $discountPrice = $form_data['discountPrice'] ?? NULL;
            $deliveryCharges = $form_data['deliveryCharges'] ?? NULL;
            $totalPrice  = $form_data['totalPrice'] ?? NULL;
            $expectedDeliveryDate = $request->input('expectedDeliveryDate') ?? NULL;
            $expectedDeliveryDate = date('Y-m-d', strtotime($expectedDeliveryDate . ' +1 day'));
            $userAddressId = $form_data['userAddressId'] ?? NULL;
            $userId = $form_data['userId'] ?? NULL; 
            $cartData =  $form_data['cart'] ?? []; 
            $productCount = count($cartData);
            $offerId   = !empty($form_data['offerID']) ? $form_data['offerID'] : NULL;
            $prescriptionID = !empty($form_data['id']) ? Crypt::decrypt($form_data['id']):NULL;
            $remarks = $form_data['remarks'] ?? NULL;
            $addressHistory=NULL; //orderID
            $orderID  = $form_data['orderID'] ?? NULL; 

            //If delivery address is empty
            $userAddress=UserAddress::where('user_id',$userId)->where('id',$userAddressId)->first();
            $userInformation=User::where('id',$userId)->select('mobile')->first();

            if(!$userAddress){
            $response = [];
            $response['status'] = false;
            $response['message'] = __('admin_message.delivery_user_address_not_found');
            return response()->json($response);
            }
            
            if(isset($userAddress->state_id) && getStateRestricted($userAddress->state_id)){
                $response = [];
                $response['status'] = false;
                $response['message'] = __('admin_message.prescription_state_restricted_found');
                return response()->json($response);
            }
            

            if(empty($expectedDeliveryDate)){
                $response = [];
                $response['status'] = false;
                $response['message'] = __('admin_message.expected_delivery_invalid');
                return response()->json($response);
            }
            if($productCount == 0){
                $response = [];
                $response['status'] = false;
                $response['message'] = __('admin_message.add_product_variant_error');
                return response()->json($response);
            }
            $addressHistory=json_encode($userAddress,true);
            $userInfoJson=json_encode($userInformation,true);
            
            $dbPrescription = Prescription::findOrFail($prescriptionID);
            if($dbPrescription)
            {
                $dbPrescription->remarks = $remarks;
                if($dbPrescription->status == Prescription::PENDING)
                {
                    $dbPrescription->status = Prescription::APPROVED;
                    $customer = User::select('id','name','email')->where('id',$dbPrescription->user_id)->first();
                    if (!empty($customer)) {
                        PrescriptionStatusChange::dispatchAfterResponse($customer,$dbPrescription);
                    }
                }
                $dbPrescription->save();
            }

            //Save order details:
            $order = new Order;
            $order->uuid = Str::uuid();
            $order->order_number = $orderNumber;
            $order->total = $totalPrice;
            $order->sub_total = $subTotal;
            $order->discount_price = $discountPrice;
            $order->delivery_charge = $deliveryCharges;
            $order->expected_delivery_date = $expectedDeliveryDate;
            $order->status_id = OrderStatus::ACCEPT;
            $order->total_item = $productCount;
            $order->user_address_id = $userAddressId;
            $order->offer_id = $offerId;
            $order->prescription_id = $prescriptionID;
            $order->created_by = $userId;
            $order->user_type = EntityType::Admin;
            $order->address_history = $addressHistory;
            $order->user_information = $userInfoJson;
            $order->order_type_id = OrderType::FEOM_PRESCRIPTION;
            
            $order->save();

            $orderID = $order->id;
            //Save order status history 
            OrderHistory::updateOrCreate([
                'order_id' => $orderID,
                'status_id' => $order->status_id,
            ]);

            $toUser =User::select('id','name','email')->where('id',$userId)->first();
            //send push notification to user:
            if(isset($toUser->id)){    
                $title = __("push_notification.prescription.order_create.title");
                $body = __("push_notification.prescription.order_create.body");
                $type = __("push_notification.prescription.order_create.type");
                $mainBody = "#{$orderNumber} {$body}";
                $orderDetails=[
                    'title' =>$title,
                    'body' =>$mainBody,
                    'type' =>$type,
                ];
                $data=[
                    'title' =>$orderDetails['title'],
                    'body' =>$orderDetails['body'],
                    'type' =>$orderDetails['type'],
                    'toUser' =>$toUser,
                    'senderUser' =>Auth::user(),
                    'order'=> $order,
                ];
                // sendUpdateStatusToUser($data);
                OrderStatusChange::dispatchAfterResponse($data);
            }
            //Save order product
            foreach($cartData as $key => $value){
                $productInfo = Product::where('id',$value['product_id'])->first();
                $productVariantInfo = ProductVariant::where('id',$value['product_variant_id'])->first();
                $history=[
                    'product' => $productInfo->toArray(),
                    'productVariant' =>$productVariantInfo->toArray(),
                    'userAddressInfo' =>$userAddress->toArray()
                ];
                $quantity = $value['quantity'];
                $price = $value['price'];

                    $orderProduct = new OrderProduct;
                    $orderProduct->uuid = Str::uuid();
                    $orderProduct->order_id = $orderID;
                    $orderProduct->product_id = $value['product_id'];
                    $orderProduct->product_variant_id = $value['product_variant_id'];
                    $orderProduct->quantity = $quantity;
                    $orderProduct->price = $price*$quantity;
                    if(!empty($history)){
                        $orderProduct->order_history =json_encode($history,true);
                    }
                    $orderProduct->save();
                }

            //Logic For Remove Old Order Product
            $requestProductVariant = array_column($cartData,'product_variant_id');
            $chkRemoveOrderProduct = OrderProduct::whereNotIn('product_variant_id',$requestProductVariant)->where('order_id',$orderID);
            if(!empty($chkRemoveOrderProduct) && $chkRemoveOrderProduct->count() > 0)
            {
                $chkRemoveOrderProduct->delete();
            }   

            DB::commit();
            $response = [];
            $response['status'] = true;
            $response['message'] = __('admin_message.order_updated');
            return response()->json($response);
        }
        catch (\Exception $ex) {
            return $this->sendServerFailResponse(__('admin_message.exception_message'));
        }
    }
}
