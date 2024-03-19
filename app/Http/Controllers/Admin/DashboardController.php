<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;

class DashboardController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
    }

   
    public function index()
    {
        $userCount = User::count();
        $customerCount =  Customer::count();
        $pdfCount =Customer::query()->has('customerPdf')->count();
        $pendingOrderCount = 0;
        $userCountWithMedicalIns = 0;
        $device['huawei'] =  0;
        $device['android'] =  0;
        $device['ios'] =  0;
        return view('admin.dashboard.index',compact('userCount','customerCount','pdfCount'));
    }
   

}
