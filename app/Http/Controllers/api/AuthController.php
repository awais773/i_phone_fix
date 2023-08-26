<?php

namespace App\Http\Controllers\api;

use Closure;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\TutorSuperbMail;
use Laravel\Passport\Passport;
use App\Mail\OtpVerificationMail;
use App\Mail\OtpVerificationMails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    private $success = false;
    private $message = '';
    private $data = [];
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'success' => false,
                // 'message' => $validator->errors()->toJson() 
                'message' => 'Email already exist',

            ], 400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'profile_picture' => $request->profile_picture,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('Token')->accessToken;
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Failed to generate token'], 422);
        }
        return response()->json([
            'success' => true,
            'message' => 'login successfull',
            'user' => $user,
            'token' => $token,
        ], 200);
    }


 public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('Token')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'login successfull',
                'user' => User::find(Auth::id()),
                'token' => $token,
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
                'message' => 'Please Check your Credentials'
            ], 401);
        }
    }



   

    public function logout()
    {
        // auth()->guard('api')->logout();
        auth()->user()->token()->revoke();
        return response()->json([
            'Success' => true,
            'message' => 'User successfully signed out'

        ], 200);
    }
    
   public function otpVerification(Request $request)
    {
        $otp = $request->input('otp');
        $email = $request->input('email');
        
        $this->success = false;
        $this->message = 'Please enter a valid OTP number';
        $this->data = [];
        
        // Check if OTP and email are provided
        if (!empty($otp) && !empty($email)) {
            // Find the user by matching 'otp_number' and 'email'
            $user = User::where('otp_number', $otp)->where('email', $email)->first();
            
            if ($user) {
                $user->otp_verify = 1;
                $user->save();                
                $token = $user->createToken('assessment')->accessToken;                
                $userData = $user->toArray();
                $this->data['token'] = 'Bearer ' . $token;
                $this->data['user'] = $userData;                
                $this->success = true;
                $this->message = 'Verification successful';
            }
        }
        
        return response()->json([
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data
        ]);
    }
    

    public function forgotPassword(Request $request)
    {
        $user = $request->email;
        $checkEmail = User::where('email', $user)->first();
        if ($checkEmail) {
            $otp = rand(100000, 999999);
            $checkEmail->otp_number = $otp;
            $checkEmail->update();
            Mail::to($request->email)->send(new OtpVerificationMails($otp));
            $token = $checkEmail->createToken('assessment')->accessToken;
            $this->$checkEmail['token'] = 'Bearer ' . $token;
            return response()->json([
                'success' => 'true', 'message' => 'Otp sent successfully. Please check your email!',
                'data' => $data = ([
                    'token' => $token
                ])
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'this email is not exits']);
        }
    }


   public function updateProfile(Request $request,$id)
    {
        $obj = User::find($id);       
         if ($obj) {
            if (!empty($request->input('profile_picture'))) {
                $obj->profile_picture = $request->input('profile_picture');
            }
        
            if (!empty($request->input('name'))) {
                $obj->name = $request->input('name');
            }
            if (!empty($request->input('email'))) {
                $obj->email = $request->input('email');
            }
            if (!empty($request->input('password'))) {
                $obj->password = Hash::make($request->input('password'));
            }
            if (!empty($request->input('role'))) {
                $obj->role = $request->input('role');
            }
            if (!empty($request->input('type'))) {
                $obj->type = $request->input('type');
            }
            if ($obj->save()) {
                $this->data = $obj;
                $this->success = true;
                $this->message = 'Profile is updated successfully';

            }
        }

        return response()->json(['success' => $this->success, 'message' => $this->message, 
        
        'data' => $this->data,
        
    
    ]);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password'
        ]);
    
        $user = User::where('email', $request->email)->where('otp_verify', 1)->first();
    
        if ($user) {
            if ($user->social_type === 'google') {
                $user->social_type = 'both';
            } elseif ($user->social_type === null) {
                $user->social_type = null;
            }
    
            $user->password = Hash::make($request->password);
            $user->save();
    
            return response()->json(['success' => true, 'message' => 'Success! Password has been changed']);
        }
    
        return response()->json(['success' => false, 'message' => 'Failed! Something went wrong']);
    }
    
    
    public function passwordChanged(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:8', // Add validation for the new password and password confirmation
        ]);

        $user = Auth::user();
        if ($user) {
            // Check if the old password is correct
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();

                return response()->json(['success' => true, 'message' => 'Success! Password has been changed']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed! Old password is incorrect']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Failed! Something went wrong']);
    }

    public function user()
    {
        $data = User::get();
        if (is_null($data)) {
            return response()->json('data not found');
        }
        return response()->json([
            'success' => true,
            'message' => 'All Data susccessfull',
            'data' => $data,
        ]);
    }
    

    public function delete($id)
    {
        $User = User::find($id);
        if (!empty($User)) {
            $User->delete();
            return response()->json([
                'success' => true,
                'message' => ' delete successfuly',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'something wrong try again ',
            ]);
        }
    }

         public function status($id)
          {
                $User = User::find($id);
                if ($User->active) {
                    $User->active = false;
                } else {
                    $User->active = true;
                }
                if (!empty($User)) {
                    $User->update();
                    return response()->json([
                        'success'=>true,
                        'message'=>'  Status Changed successfuly',
                        'data' => $User,
                    ],200);
                }
                else {
                    return response()->json([
                        'success'=>false,
                        'message'=>'something wrong try again ',
                    ]);
                }  
            }
                        
            
            public function show($id)
            {
                $user = User::where('id', $id)->first();
                if (is_null($user)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data not found'
                    ], 404);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Data retrieval successful',
                    'data' => $user,
                ]);
            }


            public function resendEmail(Request $request)
            {
                $userId = $request->input('id');                
                $user = User::find($userId);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found',
                    ]);
                }
                $email = 'https://besttutorforyou.com/verifytutor/' . $userId;
                // Send the email
                Mail::to($user->email)->send(new OtpVerificationMail($email));
                return response()->json([
                    'success' => true,
                    'message' => 'Email sent successfully',
                ]);
            }

            
                




       
    }


