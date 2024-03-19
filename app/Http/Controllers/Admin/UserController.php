<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception, Hash, Auth, Crypt;
use App\Models\EntityType;
use App\Models\DeviceDetail;
use App\Models\UserWishlist;
use App\Models\Cart;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show user list
     * 
     */
    public function index()
    {
        return view('admin.user.index');
    }

    /**
     * Get User List by AJax
     */
    public function userList(Request $request)
    {
        $columns = array(
            0 => 'name',
            1 => 'email',
            2 => 'created_at',
            3 => 'action',
        );
        $sarchVal = !empty($request->input('search.value')) ? $request->input('search.value') : null;
        $orderVal = isset($columns[$request->input('order.0.column')]) ? $columns[$request->input('order.0.column')] : null;
        $direction = $request->input('order.0.dir');
        $limit = $request->input('length');
        $start = $request->input('start');

        // $userList = User::where('role_id',1)->orwhereNULL('role_id');
        $userList = User::select('id','email',DB::raw('DATE(created_at) as created_at'),'name');
        $totalUser = $userList->count(); 

        if (!empty($sarchVal)) {
            $userList = $userList->where(function ($q) use ($sarchVal) {
                $q->where("name", 'LIKE', "%$sarchVal%");
                $q->orwhere("email", 'LIKE', "%$sarchVal%");
            });
        }
        $totalFilterUser = $userList->count();
        $userList = $userList->offset($start)->limit($limit);
        if (!empty($orderVal)) {
            $userList = $userList->orderBy($orderVal, $direction);
        }
        $userList = $userList->get();

        $pdata = [];
        if (count($userList) > 0) {
            $i = 1;
            foreach ($userList as $key => $pval) {
                $data = [];
                $data['id'] = $pval->id;
                $data['name'] = $pval->name ?? "-";
                $data['email'] = $pval->email ?? "-";
                $data['mobile'] = $pval->mobile;
                $data['created_at'] = date("M d, Y", strtotime($pval->created_at));
                $delUrl = "<a href='#' data-id='" . Crypt::encrypt($pval->id) . "' class='delete_user ml-2' style='text-decoration:none;'><i class='fas fa-trash'></i></a>";
                $data['detail'] = $delUrl;
                $pdata[] = $data;
                $i++;
            }
        }
        $jsonData = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalUser,
            "recordsFiltered" => $totalFilterUser,
            "data" => $pdata,
        ); 

        return response()->json($jsonData);
    }

    /**
     * Delete user from system
     */
    public function userDelete(Request $request) {
        DB::beginTransaction();
        try {
            $userId = Crypt::decrypt($request['id']);
            $user=User::where('id', $userId)->first();
           
            if(!empty($user->mobile)){
                $user->mobile=NULL;
                $user->save();
                $user->AauthAcessToken()->delete();
                $deviceData = DeviceDetail::where("user_id" , $user->id)->delete();
                $deleteUser = User::where('id', $user->id)->delete();
                UserWishlist::where('user_id' , $user->id)->delete();
                Cart::where('user_id', $user->id)->delete();
            }
            $response = [];
            if($deleteUser) {
                DB::commit();
                $response['status'] = true;
                $response['message'] = __('admin_message.user_delete_success');
            } else {
                DB::rollback();
                $response['status'] = false;
                $response['message'] = __('admin_message.user_delete_error');
            }
            return response()->json($response);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::info($ex);
            $response['status'] = false;
            $response['message'] = __('admin_message.exception_message');
            return response()->json($response);
        }
    }
}
