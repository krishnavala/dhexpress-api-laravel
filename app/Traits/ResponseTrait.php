<?php

namespace App\Traits;

use Cache;
use Illuminate\Http\UploadedFile;
use Log;

trait ResponseTrait
{
    public function sendSuccessResponse($message = "", $code = 200, $data = null, $other_data = null)
    {
        $jsonData = array();
        $jsonData['error'] = false;
        $jsonData['status'] = $code;
        $jsonData['message'] = $message;
        $jsonData['result'] = $data;
        return response()->json($jsonData, $code);
    }
    
    public function sendFailResponse($message = "", $code = 422, $data = null, $logMessage = "")
    {
        //show on log page
        if(!empty($logMessage)) {
            Log::info($logMessage);
            Log::error($data);
        }

        $jsonData = array();
        $jsonData['error'] = true;
        $jsonData['status'] = $code;
        $jsonData['message'] = $message;
        $jsonData['result'] = $data;
        return response()->json($jsonData, $code);
    }

    public function sendServerFailResponse($message = "", $code = 500, $data = null,$logMessage = "")
    {
        //show on log page
        if(!empty($logMessage)) {
            Log::info($logMessage);
            Log::error($data);
        }
        $jsonData = array();
        $jsonData['error'] = true;
        $jsonData['status'] = $code;
        $jsonData['message'] = $message;
        $jsonData['result'] = null;
        return response()->json($jsonData, $code);
    }
}
