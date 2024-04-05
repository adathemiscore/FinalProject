<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Http\Controllers\Helper;
use App\Models\Product;
use App\Models\User;
use App\Models\Purchase;
use App\Models\CartItem;
use App\Models\Feedbackmessage;
use App\Models\Feedback;
use App\Models\Seqencecounter;




class BuyerController extends Controller
{
    //
    public function index(){      
        // $itempicture = DB::table('products')->where('id', '=', $purchase->product_id)->get();

        
        // dd($itempicture);
        return view('buyer.index');
    }


    public function viewproductall(){
        return view('buyer.viewallproduct');
    }

    public function purchaseHistory(){
        $getpurchase = Purchase::where('buyer_id', '=', Sentinel::getUser()->id)->paginate(5);

        return view('buyer.history', compact('getpurchase'));
    }
 

    public function addtocart($user, $seller, $product){

        try {
            $product = Product::findorFail($product);
                
        } catch (\Exception $e) {
            return redirect()->back()->with('flashMessage', 'Product does not exist');
        }
        
        try {
            $user = User::findorFail($user) ;
            
        } catch (\Exception $e) {
            return redirect()->back()->with('flashMessage', 'User does not exist');
        }

        try {
            $seller = User::findorFail($seller);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('flashMessage', 'Seller does not exist');
        }

        // dd($product, $user, $seller);
        return $this->cartstore($user, $seller, $product); 
            
    }  

    public function cartstore($user, $seller, $product){    
       
        $cartCheck = CartItem::where('product_id', '=', $product->id)
            ->where('purchase_id', '=', null)
            ->where('buyer_id', '=', Sentinel::getUser()->id)
            ->get();
 
        if(count($cartCheck) == 0){
            $cart = new CartItem();
            $cart->product_id = $product->id;
            $cart->buyer_id = $user->id;
            $cart->seller_id = $seller->id;
            $cart->price = $product->price;
            $cart->quantity = 1;
            $cart->sub_total_amount = $cart->quantity*$product->price ;
            $cart->save();
 
            return redirect()->back()->with('flashMessageSuccess', 'Item added to cart successfully');
        }else{
            $cartupdate = CartItem::where('product_id', $product->id)->where('purchase_id', null)->get();
            // dd($cartupdate[0]['quantity']);
            $cartid = CartItem::where('product_id', $product->id)
                ->update([
                    'buyer_id' => $user->id,
                    'seller_id' => $seller->id,
                    'sub_total_amount' => ($cartupdate[0]['quantity']+1)*$product->price,
                    'quantity' => $cartupdate[0]['quantity']+1,
                    'price' => $product->price
                ]);
 
            return redirect()->back()->with('flashMessageSuccess', 'Item added to cart successfully');
        }
     
    }
 
    public function deletecart($user, $id){
        try {
            $user = User::findorFail($user) ;
            
        } catch (\Exception $e) {
            return redirect()->back()->with('flashMessage', 'User does not exist');
        }

       $cartitem = CartItem::find($id); 

       if(!$cartitem){
            return redirect()->back()->with('flashMessage', 'Could not remove item from cart');
       }

       $cartitem->delete();
       return redirect()->back()->with('flashMessageSuccess', 'Item removed from cart successfully');
    }



    public function checkout($id){
        // dd($id);

        $usercheck = User::findOrFail($id);
        // dd($usercheck);
        if(!$usercheck){
            return redirect()->back()->with('flashMessage', 'User does not exist');
        }

        return view('buyer.checkout');
    }

    public function checkoutstore(Request $request){
        // return $request->all();

        // $sumcart = CartItem::where('buyer_id', '=', Sentinel::getUser()->id)
        //     ->where('purchase_id', '=', null)
        //     ->sum('sub_total_amount');
        // // dd($sumcart);

        $this->validate($request,[
            'card'=>'required|numeric',
        ]);

        $purchaseid=$this->getTransactionID('purchase_id');
                
        $purchase = new Purchase();

        $purchase->address = preg_replace('/[^a-zA-Z0-9\s]/', '', request('address'));
        $purchase->name = preg_replace('/[^a-zA-Z0-9\s]/', '',request('name'));
        $purchase->buyer_id = Sentinel::getUser()->id;
        $purchase->phone = request('phone');
        $purchase->email = request('email');
        $purchase->total_amount = $this->getcarttotalamount(Sentinel::getUser()->id);
        $purchase->status = 'N';
        $purchase->purchase_id  = $purchaseid;
        $purchase->save();

        $cartitem = CartItem::where('buyer_id', '=', Sentinel::getUser()->id)
            ->where('purchase_id', '=', null);

        $cartitem->purchase_id = $purchaseid;
        $cartitem->update(['purchase_id' => $purchaseid]);
        return redirect('/buyer')->with('flashMessageSuccess', 'Checkout Successful');
    }

    public function getcarttotalamount($id)
    {
        $sumcart = CartItem::where('buyer_id', '=', $id)
            ->where('purchase_id', '=', null)
            ->sum('sub_total_amount');
        return $sumcart;
    }

    public function getTransactionID($sequencename)
    {
    	try {
            $transactionid = Seqencecounter::where('sequence_name','=',$sequencename)->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['message'=>'Sequence Not Found'],404);
        }
        
        $currentNum=$transactionid->current_num;
        $currentNumret='PUR'.str_pad($currentNum, 9, "0", STR_PAD_LEFT);
        $transactionid->current_num=$currentNum+1;          
        $transactionid->save();
       
        return $currentNumret;
    }
    

    public function cart(){
        $purchases = CartItem::where('buyer_id', '=', Sentinel::getUser()->id)
            ->where('purchase_id', '=', null)
            ->paginate(5);
        // dd($purchases);
        return view('buyer.cart', compact('purchases'));
    }

    
    public function feedback(){
        $feedback = Feedback::where('user_id', '=', Sentinel::getUser()->id)->paginate(5);
        // dd($feedback);
        return view('buyer.feedback', compact('feedback'));
    }

    public function feedbackstore(Request $request){
        // return $request->all();
        $this->validate($request,[
            'title'=>'required|max:50',
            'message'=>'required|max:200',
        ]);

        $feedback = new Feedback();
        $feedback->user_id = Sentinel::getUser()->id;
        $feedback->title = preg_replace('/[^a-zA-Z0-9\s]/', '', request('title'));
        $feedback->message = preg_replace('/[^a-zA-Z0-9\s]/', '', request('message'));
        $feedback->to_user_type = 'admin';

        $feedback->save();
        return redirect('/feedback')->with('flashMessageSuccess', 'Message Created Sucessfully');

    }

    public function passwordresetbuyer(){
        return view('buyer.passwordreset');
    }

    
    public function passwordresetbuyerstore(Request $request){
        
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

    // feedbackstore
}
