<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Sentinel as Sentinels;
use Illuminate\Database\UniqueConstraintViolationException ;
use App\Models\Feedback;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Feedbackmessage;
use App\Models\User;
use App\Models\DeletedUser;
use Illuminate\Support\Facades\Hash;
use Cartalyst\Sentinel\Users\UserInterface;


class AdminController extends Controller
{
    //

    public function index(){
        return view('admin.home');
    }

    public function earnings(){
        return 'Total earning: 10000';
    }

    public function register(){
        return view('admin.register');
    }

    public function postregister(Request $request){

        
        $this->validate($request,[
            'email'=>'required',
            'password'=>'required|confirmed|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
            'password_confirmation'=>'required|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
            'first_name'=>'required|string|max:20',
            'last_name'=>'required|string|max:30',
            'address'=>'required|string|max:50',
            'phone'=>'required|string|max:15',
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
            $user = Sentinel::registerAndActivate($credentials);
            $slug = request('role');
            $role = Sentinel::findRoleBySlug($slug);
            $role->users()->attach($user);
            return redirect()->back()->with('flashMessageSuccess', 'Account Created Successfully');
        } catch (UniqueConstraintViolationException $e) {
            return redirect()->back()->with('flashMessage', 'Email Already Exist');
        }
    }

    public function viewsellers(){
        // $seller = User::where('role', '=', 'seller')->get();
        $seller = User::where('role', '=', 'seller')->paginate(5);
        // dd($seller);
        return view('admin.viewseller', compact('seller'));
    }

    public function viewbuyers(){
        $buyer = User::where('role', '=', 'buyer')->paginate(5);
        return view('admin.viewbuyer', compact('buyer'));
    }


    public function feedbackadmin(){
        $feedback = Feedback::where('to_user_type', '=', 'admin')->orderBy('created_at', 'desc')->paginate(6);
        return view('admin.feedbackadmin', compact('feedback'));
    }

    public function feedbackview(){
        return view('admin.feedbackview');
    }

    public function feedbackmessage(){
        return view('admin.createfeedbackmessage');
    }

    public function storefeedbackmessage(Request $request){

        
    //    return $request->all();

        $this->validate($request,[
            'slug'=>'required',
            'name'=>'required',
        ]);

        $feedbackmessage = new Feedbackmessage();
        $feedbackmessage->user_id = Sentinel::getUser()->id;
        $feedbackmessage->slug = request('slug');
        $feedbackmessage->name = request('name');

        $feedbackmessage->save();
        return redirect('/feedbackadmin')->with('flashMessageSuccess', 'Message Created Sucessfully');
        // return view('admin.storefeedbackmessage');
    }

    public function passwordreset(){
        // dd(session('last_activity'));
        return view('admin.passwordreset');
    }


    public function passwordresetstore(Request $request){

        $this->validate($request,[
            'password'=>'required',
            'newpassword'=>'required|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
        ]);

        if(request('password') == request('newpassword')){
            return redirect()->back()->with('flashMessage', 'New password should be different from old password');
        }

        $user = User::where('email', '=', Sentinel::getUser()->email);

        try {
            //code...
            if($user){
                $credentials = [
                    'email' => request('email'),
                    'password'=> request('password'),
                ];
  
                $user = Sentinel::findUserById(Sentinel::getUser()->id);
                $user = Sentinel::validateCredentials($user, $credentials);
                
                // dd($user);
                // $validate =  Sentinel::login($request->all());

                if($user){
                    $check = User::where('email','=',request('email'))->firstOrFail();

                    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./';

                    $max = strlen($pool) - 1;

                    $salt = '';

                    $saltLength = 22;

                    $strength = 8;

                    for ($i = 0; $i < $saltLength; $i++) {
                        $salt .= $pool[random_int(0, $max)];
                    }

                    $strength = str_pad($strength, 2, '0', STR_PAD_LEFT);
            
                    $prefix = '$2y$';
            
                    $newpassword = crypt(request('newpassword'), $prefix.$strength.'$'.$salt.'$');

                    $check->password = $newpassword;
                    $check->save(); 

                    // dd('Yes');
                    return redirect()->back()->with('flashMessageSuccess', 'Password reset successfully');
                }else{
                    return redirect()->back()->with('flashMessage', "Invalid Password");
                }
            }else{
                return redirect()->back()->with('flashMessage', "User dosen't exist");
            }
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->with('flashMessage', "An error occured");
            // dd($th);
            
        }
    }

    public function purchase(){
        $purchases = Purchase::where('purchase_id', '!=', null)->paginate(5);
        // dd($purchases);
        return view('admin.purchases', compact('purchases'));
    }

    public function deleteUser($id){
        $user = User::findOrFail($id);

        // dd($user->id);

        if(!$user){
            return redirect()->back()->with('flashMessage', "User doesn't exist");
        }

        $deletetable = new DeletedUser();
        $deletetable->user_id = $user->id;
        $deletetable->email = $user->email;
        $deletetable->first_name = $user->first_name;
        $deletetable->last_name = $user->last_name;
        $deletetable->date_deleted = date('Y-m-d H:i:s');
        $deletetable->role = $user->role;

        $findproduct = Product::where('seller_id', '=', $id);
        // User::whereIn('id', $idsToDelete)->delete();
        // dd($findproduct);

        if($deletetable->save()){
            $user->delete();
            $findproduct->delete();
            return redirect()->back()->with('flashMessageSuccess', "User Deleted Successfully");
        }else{
            return redirect()->back()->with('flashMessage', "Error Deleteing User");
        }
    }

    public function deleteProduct($id){

        $product = Product::findOrFail($id);

        if(!$product){
            return redirect()->back()->with('flashMessage', "Product doesn't exist");
        }

        if( $product->delete()){
            return redirect()->back()->with('flashMessageSuccess', "Product Deleted Successfully");
        }else{
            return redirect()->back()->with('flashMessage', "Error Deleteing Product");
        }
    }

    public function productsall(){
        $product = Product::paginate(5);

        // dd($product);
        return view('admin.viewproduct', compact('product'));
    }

    
    public function deletefeedback($id){
        $product = Feedback::findOrFail($id);
        
        if(!$product){
            return redirect()->back()->with('flashMessage', "Feedback mesage doesn't exist");
        }

        $product->delete();
        return redirect()->back()->with('flashMessageSuccess', 'Feedback message deleted successfully');
    }  

}

