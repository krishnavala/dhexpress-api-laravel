<?php

use Kreait\Firebase\Messaging\CloudMessage;
use App\Notifications\Order\SendUpdateStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\DeviceDetail;
use App\Models\OrderStatus;
use App\Models\DiscountType;
use App\Models\Config;
use App\Models\Offer;
use App\Models\Prescription;
use App\Models\StatesRestricted;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\OpeningHour;
use App\Models\OrderType;

function getDateTime()
{
    $date = Carbon::now("UTC")->format("Y-m-d H:i:s");
    return $date;
}

function getUuid()
{
    $uuid = DB::select('SELECT uuid() as uid');
    return $uuid[0]->uid;
}

function getUtcDate()
{
    $date = Carbon::now("UTC")->format("Y-m-d");
    return $date;
}

function getWeekday()
{
    $week_day = date('l');
    return $week_day;
}

function getTime()
{
    $time = date('H:i:s');
    return $time;
}

function getHour()
{
    $time = date('H:i');
    return $time;
}

function getDefaultImage()
{
    return asset('assets/img/default.jpeg');
}

function sendPushNotification($title, $body, $data, $deviceToken)
{
    $return = false;
    $url = "https://fcm.googleapis.com/fcm/send";

    $token = $deviceToken;
    $serverKey = env('FCM_SERVER_KEY');

    $clickAction = "FLUTTER_NOTIFICATION_CLICK";
    $notification = ['title' => $title, 'body' => $body];
    $notificationData = $data;
    $arrayToSend = ['registration_ids' => $token, 'data' => $notificationData, 'notification' => $notification, "click_action" => $clickAction];

    $json = json_encode($arrayToSend);

    $headers = [];
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key=' . $serverKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Send the request
    if (curl_exec($ch)) {
        $return = true;
    }
    curl_close($ch);
    return $return;
}

// send single push notificatin using cloud messaging
function sendPushNotificationNew($notification, $notificationData, $deviceToken)
{
    $messaging = app('firebase.messaging');
    $clickAction = "FLUTTER_NOTIFICATION_CLICK";

    $message = CloudMessage::fromArray([
        'token' => $deviceToken,
        'notification' => $notification,
        'data' => $notificationData,
        'click_action' => $clickAction,
    ]);
    $messaging->send($message);

    return true;
}

// send multiple push notificatin using cloud messaging
function sendMulticastPushNotification($notification, $notificationData, $deviceTokens)
{
    $messaging = app('firebase.messaging');
    $clickAction = "FLUTTER_NOTIFICATION_CLICK";

    try {
        $message = CloudMessage::fromArray([
            'notification' => $notification,
            'data' => $notificationData,
            'click_action' => $clickAction,
        ]);
        $messaging->sendMulticast($message, $deviceTokens);

        /*
        $report = $messaging->sendMulticast($message, $deviceTokens);

        Log::info('Successful sends: ' . $report->successes()->count() . PHP_EOL);
        Log::info('Failed sends: ' . $report->failures()->count() . PHP_EOL);

        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $failure) {
                Log::info('error:'. $failure->error()->getMessage() . PHP_EOL);
            }
        } */
    } catch (Exception $e) {
        Log::info($e->getMessage());
    }

    // return true;
}

function thousandsFormat($num)
{
    if ($num > 1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }

    return $num;
}
function slugify($text, string $divider = '-')
{
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, $divider);
    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);
    // lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}
function getDateFormate($datetime)
{
    $format = 'Y-m-d H:i:s';
    $date = Carbon::createFromFormat($format, $datetime, "UTC");
    return $date->format('d-m-Y');
}
//this function converts string from UTC time zone to current user timezone
function convertUTCTimeToUserzone($datetime, $format = 'Y-m-d H:i:s')
{
    $userTimeZone = config('app.user_timezone');
    if (empty($datetime)) {
        return '';
    }
    $utcTime = Carbon::createFromFormat('Y-m-d H:i:s', $datetime, 'UTC'); // Replace with your UTC time
    $userTime = $utcTime->setTimezone($userTimeZone);
    $data['formattedDate'] = $userTime->format('d-m-Y');
    $data['formattedTime'] = $userTime->format('g:i A');
    return $data;

}
function getDateWithoutTimeFormate($datetime)
{
    $format = 'Y-m-d';
    $date = Carbon::createFromFormat($format, $datetime, "UTC");
    return $date->format('d-m-Y');
}

function getOrderStatusDetails($order)
{

    $statusId = $order->status_id;
    $order_number = $order->order_number;
    if ($statusId == OrderStatus::DELIVERED) {
        $title = __("push_notification.delivered.title");
        $body = __("push_notification.delivered.body");
        $type = __("push_notification.delivered.type");
    } elseif ($statusId == OrderStatus::REJECT) {
        $title = __("push_notification.reject.title");
        $body = __("push_notification.reject.body");
        $type = __("push_notification.reject.type");
    } elseif ($statusId == OrderStatus::INPROGRESS) {
        $title = __("push_notification.inprogress.title");
        $body = __("push_notification.inprogress.body");
        $type = __("push_notification.inprogress.type");
    } else {
        $title = __("push_notification.accept.title");
        $body = __("push_notification.accept.body");
        $type = __("push_notification.accept.type");
    }
    $mainBody = "#{$order_number} {$body}";
    $data = [
        'title' => $title,
        'body' => $mainBody,
        'type' => $type,
    ];
    return $data;
}
function getPendingOrder()
{
    return 0;
}
function getConfigValue($key)
{
    $configs = Config::pluck('value', 'key');
    if ($configs[$key]) {
        return $configs[$key];
    } else {
        return 0.00;
    }
}

function geTotalPriceData($subTotal)
{
    $discountAmount = 0;
    $offerId = 0;
    $userTimezone = config('app.user_timezone');
    $currentDate = carbon::now($userTimezone)->format('Y-m-d');
    //Get offer
    $offer = Offer::where('minimum_order_value', '<=', $subTotal)
        ->where('maximum_order_value', '>=', $subTotal)
        ->where('status', Offer::ACTIVE)
        // ->whereRaw(DB::raw("DATE(CONVERT_TZ(`start_date`,'UTC','{$userTimezone}')) <= '{$currentDate}'"))
        // ->whereRaw(DB::raw("DATE(CONVERT_TZ(`end_date`,'UTC','{$userTimezone}')) >= '{$currentDate}'"))
        ->whereRaw(DB::raw("DATE(`start_date`) <= '{$currentDate}'"))
        ->whereRaw(DB::raw("DATE(`end_date`) >= '{$currentDate}'"))
        ->first();

    //Get discountAmount
    if ($offer && $offer->discount_type) {
        $offerDiscount = $offer->discount;
        $offerId = $offer->id;
        if ($offer->discount_type == DiscountType::PERCENTAGE) {
            if (!empty($offerDiscount)) {
                $discountAmount = ($offerDiscount / 100) * $subTotal;
            }
        } else {
            $discountAmount = $offer->discount;
        }
    }
    //update variabel
    $data['subTotal']        = round($subTotal, 2);
    $data['discount']        = round($discountAmount, 2);
    $data['deliveryCharges'] = round(getConfigValue('Delivery-Charges'), 2);
    $data['offerId']         = $offerId;
    $data['total']           =  round(($data['subTotal'] - $data['discount']) + $data['deliveryCharges']);
    return $data;
}
function generateOrderNumber()
{
    $configOrder = \App\Models\ConfigOrder::firstOrCreate(['key' => 'order_number']);
    $orderNumber = !empty($configOrder->value) ? $configOrder->value : 1000;
    $newOrderNumber = $orderNumber + 1;
    $configOrder->value = $newOrderNumber;
    $configOrder->save();
    return $newOrderNumber;
}
function sendUpdateStatusToUser($data)
{
    try {
        if (!empty($data)) {
            $title = $data['title'];
            $body = $data['body'];
            $type = $data['type'];
            $toUser = $data['toUser'];
            $senderUser = $data['senderUser'];
            $mainTitle = "{$senderUser->user_name} {$title}";
            $order_id = $data['order']->id;
            if($data['order']->status_id == OrderStatus::REJECT){
                $notificationBody=__("push_notification.reject.body");
                $body = "#{$data['order']->order_number} {$notificationBody}";
            }
            //Send notification to user table
            $toUser->notify((new SendUpdateStatusNotification($toUser, $senderUser->id, $order_id, $senderUser->name, $mainTitle, $body, $type)));
            //Send notification to user device
            $deviceDetail = DeviceDetail::where('user_id', $toUser->id)->whereNot('is_huawei_device', DeviceDetail::IS_HUAWEI_DEVICE)->pluck('device_token');
            $deviceTokens = array();
            foreach ($deviceDetail as $value) {
                $deviceTokens[] = $value;
            }
            if (count($deviceTokens) > 0) {
                $notification = [];
                $notification['title'] = $mainTitle;
                $notification['body'] = $data['body'];
                $data = [
                    'title' => $notification['title'],
                    'body' =>  $notification['body'],
                    'type' => $type,
                    'order_id' => $order_id,
                ];
                sendMulticastPushNotification($notification, $data, $deviceTokens);
            }
           
            //Send notification to user huawei device
            $huaweiDeviceDetail =DeviceDetail::where('user_id', $toUser->id)->where('is_huawei_device', DeviceDetail::IS_HUAWEI_DEVICE);
            $huaweiDeviceDetail= $huaweiDeviceDetail->pluck('device_token');
            $huaweiDeviceTokens = array();
            foreach ($huaweiDeviceDetail as $value) {
                $huaweiDeviceTokens[] = $value;
            }
            if (count($huaweiDeviceTokens) > 0) {
                $notification = [];
                $notification['title'] = $mainTitle;
                $notification['body'] = $data['body'];
                $data = [
                    'title' => $notification['title'],
                    'body' =>  $notification['body'],
                    'type' => $type,
                    'order_id' => $order_id,
                ];
                sendPushNotificationWithGuzzle($data, $huaweiDeviceTokens);
            }
        }
    } catch (\Exception $ex) {
    }
}

function getPrescriptionStatusDetails($prescription)
{

    $statusId = $prescription->status;
    if ($statusId == Prescription::APPROVED) {
        $title = __("push_notification.prescription.accept.title");
        $body = __("push_notification.prescription.accept.body");
        $type = __("push_notification.prescription.accept.type");
    } elseif ($statusId == Prescription::REJECTED) {
        $title = __("push_notification.prescription.reject.title");
        $body = __("push_notification.prescription.reject.body");
        $type = __("push_notification.prescription.reject.type");
    }
    $data = [
        'title' => $title,
        'body' => $body,
        'type' => $type,
    ];
    return $data;
}

function getPendingPrescription()
{
    return 0;
}

function getStateRestricted($stateId)
{
    //check state restricted or not
    $statesRestricted = StatesRestricted::where('state_id', $stateId)->exists();
    if ($statesRestricted) {
        return true;
    }
    return false;
}

function mediaFileExists($fileName, $dirName)
{
    $imagePath = "/uploads/$dirName/$fileName";
    if (Storage::disk('public')->exists($imagePath)) {
        return false;
    }
    return true;
}
function sendPushNotificationWithGuzzle($notificationData,$devieTokenArray)
{
    try {
        $accessTokenURL = config('app.push_notification.huawei.access_token_url');
        $grantType = config('app.push_notification.huawei.grant_type');
        $clientID = config('app.push_notification.huawei.client_id');
        $appID=$clientID;  // $clientId is same as appID
        $clientSecret = config('app.push_notification.huawei.client_secret');
        //API for obtaining the access(bearer) token(OAuth2.0)
        $accessTokenResponse = Http::connectTimeout(60)->timeout(60)->asForm()->post($accessTokenURL, [
            'grant_type'    => $grantType,
            'client_id'     => $clientID,
            'client_secret' => $clientSecret,
        
        ]);
        //API for sending push messages
        if($accessTokenResponse->successful()){
            $accessToken=$accessTokenResponse['access_token'] ?? '';
            $mataData = json_encode( $notificationData,JSON_UNESCAPED_SLASHES);
            $android   = [
                "fast_app_target"   => 2, 
                "collapse_key"      => -1,
                "delivery_priority" => "HIGH"
            ];
            $messageBody = [
                "data"   => $mataData,
                "android"=> $android,
                "token"  => $devieTokenArray
            ];
            $payload = [
                "validate_only" => false,
                "message"       => $messageBody
            ];
            $requestPayload = json_encode($payload,JSON_UNESCAPED_SLASHES);
            $sendPushURL='https://push-api.cloud.huawei.com/v1/'.$appID.'/messages:send';
            $response=  Http::withHeaders(["Content-Type" => "application/json"])
                        ->withToken($accessToken)
                        ->connectTimeout(60)
                        ->withBody($requestPayload,'application/json')
                        ->post($sendPushURL);
        }
    } catch (\Exception $ex) {
        Log::info($ex);
    }
}
function getWeekDayName($weekday_number)
{
    $openingHour=OpeningHour::select('weekday')->where('weekday_number',$weekday_number)->first();
    if(isset($openingHour->weekday)){
        return $openingHour->weekday;
    }else{
        return null;
    }

}
function getOrderDateLabel($orderDate)
{
    $orderDateFormated = Carbon::createFromFormat('Y-m-d', $orderDate);
    if($orderDateFormated->isTomorrow()){
        return $returnDataArray['orderDateLabel'] ='tomorrow';
    }else if($orderDateFormated->isToday()){
        return $returnDataArray['orderDateLabel'] ='today';
    }else{
        $formattedDate = $orderDateFormated->format('F j, Y');
        return $returnDataArray['orderDateLabel'] =$formattedDate;
    }
}
function getOrderTimeLabel($hour)
{
    if($hour > 11 && $hour <= 16) {
        return "afternoon";
    }
    else if($hour > 16 && $hour <= 23) {
        return "evening";
    }else{
        return "morning";
    }
}
function geOrderProductTotalPriceData($subTotal,$order)
{
    $discountAmount = 0;
    $offerId = 0;
    $userTimezone = config('app.user_timezone');
    $currentDate = carbon::now($userTimezone)->format('Y-m-d');
    //Get offer
    $discountAmount=$order->discount_price ?? 0;
    $deliveryCharges=$order->delivery_charge ?? 0;
    if($discountAmount>$subTotal){
        $discountAmount=$subTotal;
    }
    //update variabel
    $data['subTotal']        = round($subTotal, 2);
    $data['discount']        = $discountAmount;
    $data['deliveryCharges'] = $deliveryCharges;
    $data['offerId']         = $offerId;
    $data['total']           =  round(($data['subTotal'] - $data['discount']) + $data['deliveryCharges']);
    return $data;
}

function getOrderType($ID){
    $OrderType=OrderType::where('id',$ID)->first();
    if(isset($OrderType->id)){
        return $OrderType->name ?? NULL;
    }
}