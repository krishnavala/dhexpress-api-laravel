<?php

namespace App\Validators;
use Auth;

class UserValidator extends ModelValidator
{
    private $userid; 
    public function __construct()
    {
        $this->userid = (Auth::check())?Auth::user()->id:0;
    }

    // user register rule
    private $userRegisterRules = [
        'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
        'device_type' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
    ];

    // user login rule
    private $userLoginRules = [
        'mobile' => 'required',
        'device_type' => 'required',
        'device_id' => 'required',
        'device_token' => 'required'
    ];
    private $userLoginMessage = [
        'mobile.required' => 'The phone number is required.',
        'device_type.required' => 'The device type is required',
        'device_id.required' => 'The device Id is required',
        'device_token.required' => 'The device token is required'
    ];

    // user social login register rule
    private $userSocialLoginRegisterRules = [
        'email' => 'email',
        'social_type' => 'required',
        'social_id' => 'required',
        'device_type' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
    ];

    // user change password rule
    private $userChangePasswordRules = [
        'old_password' => 'required',
        'new_password' => 'required',
        'confirm_password' => 'required|same:new_password',
    ];

    // user forgot password rule
    private $userForgotPasswordRules = [
        'email' => 'required|email|exists:users,email'
    ];

    // user reset password rule
    private $userResetPasswordRules = [
        'email' => 'required|email|exists:users,email',
        'new_password' => 'required',
        'confirm_password' => 'required|same:new_password',
    ];

    // user profile rule
    private $userProfileRules = [];
    
    // user register rule
    public function validateRegister($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userRegisterRules);
    }

    // user login rule
    public function validateUserLogin($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userLoginRules,$this->userLoginMessage);
    }

    // user social login/register rule
    public function validateUserSocialLogin($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userSocialLoginRegisterRules);
    }

    // user social login/register rule
    public function validateUserChangePassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userChangePasswordRules);
    }

    // user social login/register rule
    public function validateUserForgotPassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userForgotPasswordRules);
    }

    // user reset password rule
    public function validateUserResetPassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userResetPasswordRules);
    }

    // user reset password rule
    public function validateUserProfile($inputs)
    {
        $this->userProfileRules = [
            'name' => 'required',
            // 'mobile' => 'required',
            'email' => 'email',
            'state_id' => 'required|exists:state,id',
            //'city_id' => 'required',
            'address' => 'required',
            // 'type' => 'in:true,false',
            'medical_insurance_name' => 'required_if:is_medical_insurance,==,1|nullable',
            'medical_insurance_number' => 'required_if:is_medical_insurance,==,1|nullable',

        ];
        return parent::validateLaravelRules($inputs, $this->userProfileRules,$this->userProfileRulesMessage);
    }
    private $userProfileRulesMessage = [
        'email.required' => 'Email adress is required.',
        'state_id.required' => 'Please selcet state first.',
        'state_id.exists'   => 'Please provide valid state id.',
        // 'city_id.required' => 'Please selcet city first.',
        'address.required' => 'Address is required.',
        'type.in'       => 'Please provide type true or false.',
    ];
    //Validate city
    public function validatestate($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->cityRules,$this->cityRulesMessage);
    }
    
    private $cityRules = [
        'state_id' => 'required',
    ];
    private $cityRulesMessage = [
        'state_id.required' => 'Please selcet state first.',
    ];
    //Validate Detail API
    public function validateDeviceDetail($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->deviceDetailRules,$this->deviceDetailRulesMessage);
    }
    
    private $deviceDetailRules = [
        'device_type'  => 'required|in:android,ios',
        'device_id'    => 'required',
        'device_token' => 'required',
    ];
    private $deviceDetailRulesMessage = [
        'device_type.required' => 'Please enter device type is android or ios.',
        'device_id.required' => 'Device ID is required.',
        'device_token.required' => 'Device token is required.',
    ];
     //Validate stateRestricted
     public function validateStateRestricted($inputs)
     {
         return parent::validateLaravelRules($inputs, $this->stateRestrictedRules,$this->stateRestrictedRulesMessage);
     }
     
     private $stateRestrictedRules = [
         'state_id' => 'required',
     ];
     private $stateRestrictedRulesMessage = [
         'state_id.required' => 'Please selcet state first.',
     ];
}
