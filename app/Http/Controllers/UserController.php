<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\MailVerification;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function loadRegister()
    {
        return view('register');
    }

    public function studentRegister(Request $request)
    {
        $request->validate([
            'name' => 'string|required',
            'email' => 'string|email|required|max:100|unique:users',
            'password' => 'string|required|confirmed|min:6'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect("/verification/".$user->id);
    }

    public function loadLogin()
    {
        if(Auth::user())
        {
            return redirect('/dashboard');
        }
        return view('login');
    }

    // public function sendOtp($user)
    // {
    //     $otp = rand(100000,99999);
    //     $time = now();

    //     $email = EmailVerification::updateOrCreate([
    //         ['email' => $user->email],
    //         [
    //             'email' => $user->email,
    //             'otp' => $otp,
    //             'created_at' => $time
    //         ]
    //     ]);

    //     $data['email'] = $user->email;
    //     $data['title'] = 'Mail Verification';

    //     $data['body'] = 'Your OTP is:-  '.$otp;

    //     Mail::send('mailVerification',['data'=>$data],function($message) use ($data){
    //         $message->to($data['email'])->subject($data['title']);
    //     });
    // }

    public function sendOtp($user)
    {
        $otp = rand(100000, 999999);
        $time = now();

        $emailVerification = EmailVerification::updateOrCreate(
            ['email' => $user->email],
            [
                'user_id' => $user->id, // Assuming 'id' is the primary key of the 'users' table
                'email' => $user->email,
                'otp' => $otp,
                'created_at' => $time
            ]
        );

        $data['email'] = $user->email;
        $data['title'] = 'Mail Verification';
        $data['body'] = 'Your OTP is: ' . $otp;

        // Assuming you have a MailVerification Mailable class
        Mail::to($data['email'])->send(new MailVerification($data));
        return response()->json(['message' => 'Verification email sent successfully'], 200);
        // Add any additional logic or response as needed
    }

    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'string|required|email',
            'password' => 'string|required'
        ]);

        $userCredential = $request->only('email', 'password');
        $userData = User::where('email', $request->email)->first();

        if($userData && $userData->is_verified == 0)
        {
            $this->sendOtp($userData);
            return redirect("/verification/".$userData->id);
        }
        else if(Auth::attempt($userCredential)){
            return redirect('/dashboard');
        }
        else{
            return back()->with('error', 'Username & password is incorrect');
        }
    }

    public function loadDashboard()
    {
        if(Auth::user()){
            return view('dashboard');
        }
        return redirect('/');
    }

    public function verification($id)
    {
        $user = User::where('id',$id)->first();
        if(!$user || $user->is_verified == 1){
            return redirect('/');
        }
        $email = $user->email;

        $this->sendOtp($user);

        return view('verification',compact('email'));
    }

    public function verifiedOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $otpData = EmailVerification::where('otp', $request->otp)->first();

        if (!$otpData) {
            return response()->json(['success' => false, 'msg' => 'You entered the wrong OTP']);
        } else {
            $currentTime = now(); // Use Carbon's now() method to get the current time
            $time = $otpData->created_at; // Do not convert created_at to timestamp

            if ($currentTime->greaterThanOrEqualTo($time) && $time->greaterThanOrEqualTo($currentTime->subSeconds(90))) {
                User::where('id', $user->id)->update([
                    'is_verified' => 1
                ]);
                return response()->json(['success' => true, 'msg' => 'Mail has been verified']);
            } else {
                return response()->json(['success' => false, 'msg' => 'Your OTP has expired']);
            }
        }
    }

    public function resendOtp(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        $otpData = EmailVerification::where('email',$request->email)->first();

        $currentTime = now();
        $time = $otpData->created_at;

        if($currentTime >= $time && $time >= $currentTime - (90+5)){
            return response()->json(['success' => false, 'msg' => 'Please Try after some time']);
        }
        else{
            $this->sendOtp($user);
            return response()->json(['success' => true, 'msg' => 'OTP has been sent']);
        }
    }
}
