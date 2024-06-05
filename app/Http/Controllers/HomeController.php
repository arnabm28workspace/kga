<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ServicePartner;
use App\Models\Settings;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Invoice;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $countCustomers = Customer::count();
        $countSuppliers = Supplier::count();
        $countServicePartners = ServicePartner::where('is_default', 0)->count();
        $countProducts = Product::count();
        $countPO = PurchaseOrder::where('status', 1)->count();
        $countGRN = PurchaseOrder::where('status', 2)->count();
        $countSales = SalesOrder::count();
        $countInvoice = Invoice::count();
        
        return view('home', compact('countCustomers','countSuppliers','countServicePartners','countProducts','countPO','countGRN','countSales','countInvoice'));
    }

    public function myprofile(Request $request)
    {
        return view('profile');
    }

    public function saveprofile(Request $request)
    {
        # code...
        $request->validate([
            'name' => 'required|max:100'
        ]);

        $params = $request->except('_token');
        User::where('id', Auth::user()->id)->update($params);

        Session::flash('message', 'Profile updated successfully');
        return redirect()->route('myprofile'); 
    }

    public function changepassword(Request $request)
    {
        return view('password');
    }

    public function savepassword(Request $request)
    {
        # code...

        $request->validate([
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        $params = $request->except('_token');
        
        if(!empty($params['password'])){
            $params['password'] = Hash::make($params['password']);            
        } else {
            unset($params['password']);
        }
        
        unset($params['password_confirmation']);
        // dd($params);
        $data = User::where('id', Auth::user()->id)->update($params);
        Session::flash('message', 'Password changed successfully');
        return redirect()->route('changepassword'); 
    }

    public function settings(Request $request)
    {
        # view settings...
        $settings = Settings::find(1);
        return view('settings', compact('settings'));
    }

    public function savesettings(Request $request)
    {
        # save settings...
        $params = $request->except('_token');
        
        Settings::where('id',1)->update($params);

        ServicePartner::where('id', 1)->update([
            'email' => $params['csv_to_email']
        ]);

        Session::flash('message', 'Settings saved successfully');
        return redirect()->route('settings'); 
    }
}
