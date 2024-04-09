<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Purchase;
use App\Models\Feedback;
use App\Models\User;


class SellerController extends Controller
{
    //

    public function index(){
        return view('seller.index');
    }

    public function viewproduct(){
        // $prod = Product::where('seller_id','=',Sentinel::getUser()->id)->get();
        // dd(Product::all());
        $product = Product::where('seller_id','=',Sentinel::getUser()->id)->paginate(5);
        return view('seller.viewproduct', compact('product'));
    }

    public function viewproductid(){

    }

    public function createproduct(){
        return view('seller.createproduct');
    }

    public function storeproduct(Request $request){
        // return $request->all();

        $this->validate($request,[
            'name'=>'required|string|max:120',
            'description'=>'required|string|max:120',
            'price'=>'required|numeric|max:9999999999|gt:0',
            // 'available'=>'required',
            'picture'=>'required|mimes:,jpg,png,jpeg',
        ]);

        // $validator = Validator::make($request->all(),$rules);
        // if($validator->fails()){
        //     return response()->json($validator->errors()->toArray(),404);
        // }

        $available = 'N';

        if($request->status == true){
            $available = 'Y';
        }

        // $product->name = htmlspecialchars(trim(request('name')));
        // $product->description = htmlspecialchars(trim(request('description')));
        // $product->seller_id = htmlspecialchars(trim(request('seller_id')));
        // $product->price = htmlspecialchars(trim(request('price')));
        
        $product = new Product();
        $product->name =  preg_replace('/[^a-zA-Z0-9\s]/', '', request('name'));
        $product->description = preg_replace('/[^a-zA-Z0-9\s]/', '', request('description'));
        $product->seller_id = request('seller_id');
        $product->price = preg_replace('/[^a-zA-Z0-9\s]/', '', (request('price')));

        // preg_replace('/[^a-zA-Z0-9\s]/', '',
        // $product->status = $available;
        
        $file = $request->file('picture');
        $ext = $file->getClientOriginalExtension();
        $filename = time().'.'.$ext;
        $file->move('products', $filename);

        
        $product->picture = $filename;
        $product->save();
        
        return redirect('/viewproduct')->with('flashMessageSuccess', 'Product Created Successfully');
    }

    public function feedbackseller(){
        $feedback = Feedback::where('user_id', '=', Sentinel::getUser()->id)->paginate(5);
        return view('seller.feedback', compact('feedback'));
    }

    public function feedbacksellerstore(Request $request){
        // return $request->all();
        $this->validate($request,[
            'title'=>'required|string|max:50',
            'message'=>'required|max:200',
        ]);

        $feedback = new Feedback();
        $feedback->user_id = Sentinel::getUser()->id;
        $feedback->title = preg_replace('/[^a-zA-Z0-9\s]/', '', request('title'));
        $feedback->message = preg_replace('/[^a-zA-Z0-9\s]/', '', request('message'));
        $feedback->to_user_type = 'admin';
        
        $feedback->save();
        return redirect('/feedbackseller')->with('flashMessageSuccess', 'Message Created Sucessfully');
        // return view('seller.feedback');
    }
    

    public function passwordresetseller(){
        return view('seller.passwordreset');
    }


    public function passwordresetsellerstore(Request $request){
        $this->validate($request,[
            'password'=>'required|string|min:8',
            'newpassword'=>'required|string|min:8|max:15|regex:/[!@#$%^&*()\-_=+{};:,<.>]/',
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
        $purchases = CartItem::where('purchase_id', '!=', null)->paginate(5);
        // dd($purchases);
        return view('seller.purchases', compact('purchases'));
    }

    public function deliver(){
        $purchases = Purchase::where('purchase_id', '!=', null)->paginate(5);
        // dd($purchases);
        return view('seller.purchases', compact('purchases'));
    }


    public function deleteproduct($id){
        $product = Product::findOrFail($id);

        if(!$product){
            return redirect()->back()->with('flashMessage', "Product doesn't exist");
        }

        $product->delete();
        return redirect()->back()->with('flashMessageSuccess', 'Product deleted successfully');
    }   


}
