<?php

namespace App\Http\Middleware;
use Closure;
use Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use \App\Models\User;
use \App\Models\Role;

class AdminMiddleware extends Middleware
{
    
    public function handle($request, Closure $next, ...$guards)
    {
       
        // if(Auth::check()){
        //     $superAdmin=User::SUPERADMIN;
        //     $message = __('admin_message.not_admin_access');
        //     $user=User::where('entity_type_id',$superAdmin)->where('id',Auth::user()->id)->first();
        //     if(!empty($user->id)){
        //         return $next($request);
        //     }else{
        //         Auth::logout();
        //         return redirect('admin/login')->with('message', $message);
        //     }
        // }else{
        //     return redirect('admin/login')->with('message', $message);
        // }
        return $next($request);
    }
   
}
