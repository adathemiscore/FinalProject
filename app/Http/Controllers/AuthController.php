<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Illuminate\Database\UniqueConstraintViolationException ;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\LoginAudit;
use App\Models\User;
use App\Models\Token;
use App\Models\Product;
use App\Http\Controllers\SellerController;
use Symfony\Component\Mailer\Exception\TransportException;
use Reminder;
use Mail;
use Carbon\Carbon;



class AuthController extends Controller
{
    //

    public function register(){
        return view('authentication.register');
    }

    public function postregister(Request $request){

        
        $this->validate($request,[
            'email'=>'required',
            'password'=>'required|confirmed|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
            'password_confirmation'=>'required|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
            'first_name'=>'required|string|max:20',
            'last_name'=>'required|string|max:30',
            'address'=>'required|string|max:50',
            'phone'=>'required|numeric|max:999999999999999|gt:0',
            'role'=>'required',
            'gender'=>'required'
        ]);

        // return $request->all();
        $credentials = [
            'email'=>request('email'),
            'password'=>request('password'),
            'first_name'=> preg_replace('/[^a-zA-Z0-9\s]/', '',request('first_name')),
            'last_name'=> preg_replace('/[^a-zA-Z0-9\s]/', '', request('last_name')),
            'address'=> preg_replace('/[^a-zA-Z0-9\s]/', '', request('address')),
            'phone'=>request('phone'),
            'role'=>request('role'),
            'gender'=>request('gender')
        ];

        try {
            // $usercondition = User::where('email', '=', request('email'))
            //     // ->where('role', '=', request('role'))
            //     ->where('role', '=', 'buyer')
            //     ->get();

            // $userByEmail = User::where('email', $request->email)->first();
           
            // // dd($userByEmail->email,$userByEmail->role);

            // $emailcheck = $userByEmail->email == $request->email;
            // $rolecheck = $userByEmail->role == $request->role;

            // if($emailcheck && $rolecheck){
            //     return redirect()->back()->with('flashMessage', 'Account Already Exist');

            // }
            
            $user = Sentinel::registerAndActivate($credentials);
            $slug = request('role');
            $role = Sentinel::findRoleBySlug($slug);
            $role->users()->attach($user);
            
                // return redirect()->back()->with('flashMessageSuccess', 'Account Created Successfully');
            return redirect('/')->with('flashMessageSuccess', 'Account Created Successfully');
        } catch (UniqueConstraintViolationException $e) {

            return redirect()->back()->with('flashMessage', 'Email Already Exist');
        }
    }

    public function postlogin(Request $request){

        // return $request->all();
        try {

            $user = User::where('email', '=', $request->email)->first();
            if(!$user){
                return redirect()->back()->with('flashMessage', "User dosen't Exist");
            }
            
            $sentinelUser = Sentinel::findById($user->id);

            if($sentinelUser->role == 'admin'){
                return redirect('/adminlogin')->with('flashMessage', 'UnAuthorized Access');
            }


            $remember = false;

            if(isset($request->remember)){
                $remember = true;
            }


            //code...
            if(Sentinel::authenticate($request->all(), $remember)){
                $slug = Sentinel::getUser()->roles()->first()->slug;

                if($slug == 'seller'){
                    $currentDateTime = date('Y-m-d H:i:s');
                    $loginsession = new LoginAudit();
                    $loginsession->user_id = Sentinel::getUser()->id;
                    $loginsession->user_type = Sentinel::getUser()->roles()->first()->slug;
                    $loginsession->event = 'Login';
                    $loginsession->status = 'Success';
                    $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->ip =  $request->ip();
                    $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->save();

                    $product = Product::where('seller_id', '=', Sentinel::getUser()->id)->get()->count();

                    // dd($product);
                    return redirect('/seller');
                   

                }else if($slug == 'buyer'){
                    $currentDateTime = date('Y-m-d H:i:s');
                    $loginsession = new LoginAudit();
                    $loginsession->user_id = Sentinel::getUser()->id;
                    $loginsession->user_type = Sentinel::getUser()->roles()->first()->slug;
                    $loginsession->event = 'Login';
                    $loginsession->status = 'Success';
                    $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->ip =  $request->ip();
                    $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->save();
        
                    return redirect('/buyer');
                }
            }else{
                $user = User::where('email', '=', $request->email)->first();
                // dd($user);
                $currentDateTime = date('Y-m-d H:i:s');
                $loginsession = new LoginAudit();
                $loginsession->user_id = $user->id;
                $loginsession->user_type = $user->role;
                $loginsession->event = 'Login';
                $loginsession->status = 'failed';
                $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->ip =  $request->ip();
                $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->save();
                return redirect()->back()->with('flashMessage', 'Wrong Credentials. Try again later');
            }
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            return redirect()->back()->with('flashMessage', "You are banned for $delay seconds.");
        } catch(NotActivatedException $e){
            // $delay = $e->getDelay();
            return redirect()->back()->with('flashMessage', "Your account is not activated");
        } 
      
    }

    public function logout(Request $request){
        
        $currentDateTime = date('Y-m-d H:i:s');
        $loginsession = new LoginAudit();
        $loginsession->user_id = Sentinel::getUser()->id;
        $loginsession->user_type = Sentinel::getUser()->roles()->first()->slug;
        $loginsession->event = 'Logout';
        $loginsession->status = 'Success';
        $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));        ;
        $loginsession->ip =  $request->ip();
        $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
        $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
        $loginsession->save();

        Sentinel::logout();
        return redirect('/');
    }

    public function logoutadmin(Request $request){
        $currentDateTime = date('Y-m-d H:i:s');
        $loginsession = new LoginAudit();
        $loginsession->user_id = Sentinel::getUser()->id;
        $loginsession->user_type = Sentinel::getUser()->roles()->first()->slug;
        $loginsession->event = 'Logout';
        $loginsession->status = 'Success';
        $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));        ;
        $loginsession->ip =  $request->ip();
        $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
        $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
        $loginsession->save();

        Sentinel::logout();
        return redirect('/adminlogin');
    }

    public function forgotpassword(){
        return view('authentication.forgotpassword');
    }


    public function forgotpasswordreset(Request $request){
        // return $request->all();

        $this->validate($request,[
            'email'=>'required',
        ]);

        $user = User::where('email', '=', $request->email)->first();

        
        if(!$user){
            return redirect()->back()->with('flashMessageSuccess', 'Reset code was sent to your email');
        }
        
        // dd($user->role);
        
        $sentinelUser = Sentinel::findById($user->id);
        $reminder = Reminder::exists($sentinelUser) ?:  Reminder::create($sentinelUser);
        // dd($reminder);

        try {
            $this->sendresetpassword($user, $reminder->code);
        } catch (\Throwable $th) {
            // throw $th;
            // dd($th);
            return redirect()->back()->with('flashMessage', 'A mail has already been sent');
        }
        // dd($user, $randomNumber);

        if($user->role == 'admin'){
            return redirect('/adminlogin')->with('flashMessageSuccess', 'Reset code was sent to your email');
        }
        return redirect('/')->with('flashMessageSuccess', 'Reset code was sent to your email');
    }

    
    private function sendresetpassword($user, $code){
        Mail::send('email.password', [
            'user'=>$user,
            'code'=>$code,
        ], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject("Forgot Password");
        });
    }

    public function resetpassword($email, $resetcode){
        // return "$email : $resetcode";
        $user = User::byEmail($email);
        
        if(!$user){
            abort(404);
        }
        
        $sentinelUser = Sentinel::findById($user->id);

        // $reminderget = Reminder::where('user_id', '=', $sentinelUser->id)->get();
        
        // print_r();


        if($reminder = Reminder::where('user_id', '=', $sentinelUser->id)->latest()->first()){
            // dd($reminder);
            if($resetcode == $reminder['code']){
                return view('authentication.resetpassword');
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }

    public function postresetpassword(Request $request, $email, $resetcode){
        // return $request->all();

        $this->validate($request, [
            'password'=>'required|confirmed|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
            'password_confirmation'=>'required|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
        ]);

        $user = User::byEmail($email);
        
        if(!$user){
            abort(404);
        }
        
        $sentinelUser = Sentinel::findById($user->id);


        // $reminderget = Reminder::where('user_id', '=', $sentinelUser->id)->get();
        
        // print_r();


        if($reminder = Reminder::where('user_id', '=', $sentinelUser->id)->latest()->get()->first()){
            // dd($reminder['completed']);

            if($reminder['completed'] == 1){
                return abort(404);
            }

            if($resetcode == $reminder['code']){
                Reminder::complete($sentinelUser, $resetcode, $request->password);

                if($sentinelUser->role == 'admin'){
                    return redirect('/adminlogin')->with('flashMessageSuccess', 'Password reset successfully. Please login with your new password');
                }else{
                    return redirect('/')->with('flashMessageSuccess', 'Password reset successfully. Please login with your new password');
                }
            }else{
                if($sentinelUser->role == 'admin'){
                    return redirect('/adminlogin');
                }
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }



   



    public function postadmintoken(Request $request){
        $this->validate($request,[
            'email'=>'required',
        ]);

        $user = User::where('email', '=', $request->email)->first();

        if(!$user){
            return redirect()->back()->with('flashMessageSuccess', 'Login token was sent to your email');
        }

        $sentinelUser = Sentinel::findById($user->id);
        // $usercheck = Sentinel::validateCredentials($sentinelUser, $credentials);

        if($sentinelUser->role == 'admin'){
            // $reminder = Reminder::exists($sentinelUser) ?:  Reminder::create($sentinelUser);
            $randomNumber = random_int(100000, 999999);

            // dd($randomNumber);
            $storeToken = new Token();
            $storeToken->user_id = $user->id;
            $storeToken->token = $randomNumber;
            $storeToken->role = 'admin';
            $storeToken->status = 'N';
            $storeToken->save();

            try {
                $this->sendEmail($user, $randomNumber);
            } catch (\Throwable $th) {
                // throw $th;
                return redirect()->back()->with('flashMessage', 'No internet connection');
            }
            // dd($user, $randomNumber);

            return redirect('/adminlogin')->with('flashMessageSuccess', 'Login token was sent to your email');
        }else{
            return redirect('/')->with('flashMessage', 'UnAuthorized Access. User is not an admin');
        }

    }

    public function adminlogin(){
        return view('authentication.adminlogin');
    }

    
    public function postadminlogin(Request $request){
        // return $request->all();

        try {
            $this->validate($request,[
                'email'=>'required',
                'password'=>'required',
                'token'=>'required',

            ]);

            $credentials = [
                'email'=>request('email'),
                'password'=>request('password')
            ];

            $user = User::where('email', '=', $request->email)->first();

            if(!$user){
                return redirect()->back()->with('flashMessage', "User Doesn't Exist");
            }

            // $checktoken = Token::where('user_id', '=', $user->id)->orderBy('created_at', 'desc')->first();
            // dd($checktoken);
            
            $checktoken = Token::latest()->first();
           
            if($checktoken->token != $request->token){
                $user = User::where('email', '=', $request->email)->first();
                    // dd($user);
                $currentDateTime = date('Y-m-d H:i:s');
                $loginsession = new LoginAudit();
                $loginsession->user_id = $user->id;
                $loginsession->user_type =  $user->role;
                $loginsession->event = 'Login';
                $loginsession->status = 'failed';
                $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->ip =  $request->ip();
                $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->save();
                return redirect()->back()->with('flashMessage', 'Invalid Token');
            }elseif($checktoken->token == $request->token && $checktoken->status == 'Y'){
                    $user = User::where('email', '=', $request->email)->first();
                        // dd($user);
                    $currentDateTime = date('Y-m-d H:i:s');
                    $loginsession = new LoginAudit();
                    $loginsession->user_id = $user->id;
                    $loginsession->user_type =  $user->role;
                    $loginsession->event = 'Login';
                    $loginsession->status = 'failed';
                    $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->ip =  $request->ip();
                    $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->save();
                    return redirect()->back()->with('flashMessage', 'Token already Used');
                
            }

            // $timecheck1 = $checktoken->created_at;
            // $timecheck2 = now();

            // $date1 = Carbon::parse($timecheck1);
            // $date2 = Carbon::parse($timecheck2);

            // $diffInMinutes = $date1->diffInMinutes($date2);

            // if($diffInMinutes > 10){
            //     return redirect('/admin')->with('flashMessage', 'Token Expired');
            // }elseif($checktoken->token != request('token')){
            //     return redirect()->back()->with('flashMessage', 'Invalid Token');
            // }



            $remember = false;

            if(isset($request->remember)){
                $remember = true;
            }

            if(Sentinel::authenticate($credentials, $remember)){
                $slug = Sentinel::getUser()->roles()->first()->slug;

                if($slug == 'admin'){
                    $currentDateTime = date('Y-m-d H:i:s');
                    $loginsession = new LoginAudit();
                    $loginsession->user_id = Sentinel::getUser()->id;
                    $loginsession->user_type = Sentinel::getUser()->roles()->first()->slug;
                    $loginsession->event = 'Login';
                    $loginsession->status = 'Success';
                    $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->ip =  $request->ip();
                    $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                    $loginsession->save();

                    // dd($checktoken->id);
                    $updatetoken = Token::find($checktoken->id);
                    $updatetoken->status = 'Y';
                    $updatetoken->expire_time = date('Y-m-d H:i:s');

                    $updatetoken->save();
                    return redirect('/home');
                }
            }else{
                $user = User::where('email', '=', $request->email)->first();
                    // dd($user);
                $currentDateTime = date('Y-m-d H:i:s');
                $loginsession = new LoginAudit();
                $loginsession->user_id = $user->id;
                $loginsession->user_type =  $user->role;
                $loginsession->event = 'Login';
                $loginsession->status = 'failed';
                $loginsession->eventtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->ip =  $request->ip();
                $loginsession->created_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->updated_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($currentDateTime)));
                $loginsession->save();

                return redirect()->back()->with('flashMessage', 'Wrong Login Credentials. Try again later');
            }
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            return redirect()->back()->with('flashMessage', "You are banned for $delay seconds.");
        } catch(NotActivatedException $e){
            // $delay = $e->getDelay();
            return redirect()->back()->with('flashMessage', "Your account is not activated");
        } 

    }

    private function sendEmail($user, $code){
        Mail::send('email.token', [
            'user'=>$user,
            'code'=>$code,
        ], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject("$user->first_name Login Token");
        });
    }



    
}
