<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class StaffController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            
            if(Auth::user()->id == 8){
                abort(404);
            }            

            return $next($request);
        });
        
    }
    /**
     * Display a listing of the staff.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $total = User::where('type','staff')->count();
        
        $totalActive = User::where('type','staff')->where('status', 1)->count();
        $totlInactive = User::where('type','staff')->where('status', 0)->count();
        $data = User::select('*')->where('type','staff');
        $totalResult = User::where('type','staff');
        if(!empty($status)){
            if($status == 'active'){
                $data = $data->where('status', 1);
                $totalResult = $totalResult->where('status', 1);
            } else if ($status == 'inactive'){
                $data = $data->where('status', 0);
                $totalResult = $totalResult->where('status', 0);
            }
        }
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
        }
        
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'status'=>$status,
            'page'=>$request->page,
            'paginate'=>$paginate
        ]);
        return view('staff.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','paginate'));
    }

    /**
     * Show the form for creating a new staff.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {        
        return view('staff.add');
    }

    /**
     * Store a newly created staff in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:users,email|nullable|required_without:phone',
            'phone' => 'numeric|digits_between:7,10|unique:users,phone|nullable|required_without:email',            
        ]);

        $params = $request->except('_token');
        $params['type'] = 'staff';
        $params['password'] = Hash::make('secret');
        $id = User::insertGetId($params);
        
        if (!empty($id)) {
            Session::flash('message', 'Staff created successfully');
            return redirect()->route('staff.list');
        } else {
            return redirect()->route('staff.add')->withInput($request->all());
        }
    }

    /**
     * Display the specified staff.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = User::find($id);
            return view('staff.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified staff.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = User::find($id);
            return view('staff.edit', compact('data','id','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Update the specified staff in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'unique:users,email,'.$id.'|regex:/(.+)@(.+)\.(.+)/i|nullable|required_without:phone',
                'phone' => 'numeric|unique:users,phone,'.$id.'|nullable|required_without:email'          
            ]);
    
    
            $params = $request->except('_token');
            $data = User::where('id',$id)->update($params);
            // dd($data);
            if (!empty($data)) {
                Session::flash('message', 'Staff updated successfully');
                return redirect('/staff/list?'.$getQueryString);
                // return redirect()->route('staff.list');
            } else {
                return redirect()->route('staff.edit')->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Toggle Status the specified staff from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $customer = User::find($id);
            $message = "";
            if($customer->status == 1){
                User::where('id',$id)->update(['status'=>0]);
                $message = "Staff deactivated successfully";
            } else {
                User::where('id',$id)->update(['status'=>1]);
                $message = "Staff activated successfully";
            }
    
            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/staff/list?'.$getQueryString);
            }
            return redirect()->route('staff.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

}
