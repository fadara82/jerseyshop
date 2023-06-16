<?php

namespace App\Http\Controllers;
use App\Models\UserModel;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{

    public function login()
    {
        //Session::put('product_id',$id);
        return view('login');
    }
    public function Wishlogin($id)
    {
        Session::put('product_id',$id);
        return view('login');
    }

    public function loginAction(Request $request){

        $this->validate($request,[
            'username'=> 'required',
            'password'=> 'required',
        ]
        );
        $mail = $request['username'];
        $password = $request['password'];

        $user = DB::table('users')
                ->where('email',$mail)
                ->orWhere('name',$mail)
                ->first();

        if(!empty($user) && \Hash::check($password, $user->password)){
            $request->session()->put('userid', $user->id);
            $request->session()->put('username', $user->name);

            $is_admin = $user->is_admin; // Get the is_admin value from the user object
            //dd($is_admin);
            //die();

            $request->session()->put('is_admin', $is_admin);

            $request->session()->flash('status', "You have successfully logged in");

            if(session('product_id')){

                $prod['product_id'] = session('product_id');
                $prod['user_id'] = session('userid');

                $resultWish = DB::table('wishlist')->insert($prod);
                if($resultWish){
                    $request->session()->flash('status', "Added successfully!");
                    return redirect('products');
                    Session::flush('product_id');
                } else {
                    $request->session()->flash('status', "Error!");
                    return redirect('products');
                }

            } else {
                return redirect('products');
            }
        } else {
            $request->session()->flash('status', "Unknown user");
            return redirect('/login');
        }
    }
    public function ajaxLogin(Request $request){
        $mail = $request['email'];
        $password = $request['password'];
        $user = DB::table('users')
                ->where('email',$mail)
                ->orWhere('name',$mail)
                ->first();

        if(!empty($user) && \Hash::check($password, $user->password)){
            $request->session()->put('userid', $user->id);
            $request->session()->put('username', $user->name);
            return "success";
        } else {
            return "failed";
        }
    }

    public function logout(){
        // $request->session()->flush();
        \Session::flush();
        return redirect('/products');
    }
}
