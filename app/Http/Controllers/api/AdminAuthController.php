<?php

namespace App\Http\Controllers\api;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Mail\OtpVerificationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Guards\TokenGuard;

class AdminAuthController extends Controller
{

    private $success = false;
    private $message = '';
    private $data = [];
    //
    public function adminRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:admin_users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                // 'message' => $validator->errors()->toJson() 
                'message' => 'Email already exist',

            ], 400);
        }

        $user = AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            // 'country' => $request->country,
            // 'mobile_number' => $request->mobile_number,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('Token')->accessToken;
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'somthing Wrong',], 422);
        }
        return response()->json([
            'success' => true,
            'message' => 'login successfull',
            'user' => $user,
            'token' => $token,
        ], 200);
    }


    public function adminlogin(Request $request)
    {
        $user = AdminUser::where("email", request('email'))->first();
        if(!isset($user)){
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
                'message' => 'Please Check your Credentials'
            ], 401);        }
        if (!Hash::check(request('password'), $user->password)) {
            return "Incorrect password";
        } 
        $tokenResult = $user->createToken('admin_users');
        // $user->token_type = 'Bearer';
        return response()->json([
            'success' => true,
            'message' => 'login successfull',
            'user' => $user,
            'token'=>  $tokenResult->accessToken,

        ], 200);
    }

    public function userinfo()
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

    public function logout()
    {

        auth()->guard('api')->logout();

        return response()->json([
            'Success' => true,
            'message' => 'User successfully signed out'

        ], 200);
    }
    // public function otpVerification(Request $request)
    // {
    //     $otp = $request->input('otp');
    //     $id = $request->user()->id;
    //     if (!empty($otp)) {
    //         $obj = AdminUser::where('otp_number', $otp)->where('id', $id)->first();
    //         if (!empty($obj)) {
    //             $obj->otp_verify = 1;
    //             $obj->save();
    //             $user = AdminUser::find($id);
    //             $token = $user->createToken('assessment')->accessToken;
    //             $user = $user->toArray();
    //             $this->data['token'] = 'Bearer ' . $token;
    //             $this->data['user'] = $user;
    //             $this->message = ' successfully';
    //             $this->success = true;
    //         } else {
    //             $this->message = 'Please enter valid otp number';
    //             $this->success = false;
    //         }
    //     }

    //     return response()->json(['success' => $this->success, 'message' => $this->message, 'data' => $this->data]);
    // }

    // public function forgotPassword(Request $request)
    // {
    //     $user = $request->email;
    //     $checkEmail = AdminUser::where('email', $user)->first();
    //     if ($checkEmail) {
    //         $otp = rand(100000, 999999);
    //         $checkEmail->otp_number = $otp;
    //         $checkEmail->update();
    //         Mail::to($request->email)->send(new OtpVerificationMail($otp));
    //         $token = $checkEmail->createToken('assessment')->accessToken;
    //         $this->$checkEmail['token'] = 'Bearer ' . $token;
    //         return response()->json([
    //             'success' => 'true', 'message' => 'Otp sent successfully. Please check your email!',
    //             'data' => $data = ([
    //                 'token' => $token
    //             ])
    //         ]);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'this email is not exits']);
    //     }
    // }


    public function adminProfile(Request $request)
    {
//         $user = Auth::guard('api')->user();
// dd( $user);
        $id = $request->user()->id;
        $obj = AdminUser::find($id);
        if ($obj) {
            if (!empty($request->input('name'))) {
                $obj->name = $request->input('name');
            }
            if (!empty($request->input('email'))) {
                $obj->email = $request->input('email');
            }
            if (!empty($request->input('password'))) {
                $obj->password = Hash::make($request->input('password'));
            }
            if (!empty($request->input('role_id'))) {
                $obj->role_id = $request->input('role_id');
            }
            if ($obj->save()) {
                $this->data = $obj;
                $this->success = true;
                $this->message = 'Profile is updated successfully';
            }
        }

        return response()->json(['success' => $this->success, 'message' => $this->message, 'data' => $this->data,]);
    }

    // public function updatePassword(Request $request)
    // {
    //     $this->validate($request, [
    //         'email' => 'required',
    //         'password' => 'required|min:6',
    //         'confirm_password' => 'required|same:password'
    //     ]);
    //     $user = AdminUser::where('email', $request->email)->where('otp_verify', 1)->first();
    //     // $user = User::where('email', $email)->where('otp_verify', 1)->first();

    //     if ($user) {
    //         // $user['is_verified'] = 0;
    //         // $user['token'] = '';
    //         $user['password'] = Hash::make($request->password);
    //         $user->save();
    //         return response()->json(['success' => 'True', 'message' => 'Success! password has been changed',]);
    //     }
    //     return response()->json(['success' => false, 'message' => 'Failed! something went wrong',]);
    // }
}