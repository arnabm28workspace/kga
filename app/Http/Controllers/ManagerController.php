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

class ManagerController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {            
            $this->type = Auth::user()->type;            
            // dd($this->type);
            if($this->type != 'admin'){                
                abort(401);                
            }

            return $next($request);
        });
    }
    /**
     * Display a listing of the manager.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'all';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $total = User::where('type','manager')->count();
        
        $totalActive = User::where('type','manager')->where('status', 1)->count();
        $totlInactive = User::where('type','manager')->where('status', 0)->count();
        $data = User::select('*')->where('type','manager');
        $totalResult = User::where('type','manager');
        // if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('name', 'LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%');
            });
        // }
        if($status == 'active'){
            $data = $data->where('status', 1);
            $totalResult = $totalResult->where('status', 1);
        } else if ($status == 'inactive'){
            $data = $data->where('status', 0);
            $totalResult = $totalResult->where('status', 0);
        }
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'status'=>$status,
            'page'=>$request->page,
            'paginate'=>$paginate
        ]);
        return view('manager.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','paginate'));
    }

    /**
     * Show the form for creating a new manager.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {        
        return view('manager.add');
    }

    /**
     * Store a newly created manager in storage.
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
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

        $params = $request->except('_token');
        unset($params['password_confirmation']);
        $params['password'] = Hash::make($params['password']);
        $id = User::insertGetId($params);
        
        if (!empty($id)) {
            Session::flash('message', 'Manager created successfully');
            return redirect()->route('manager.list');
        } else {
            return redirect()->route('manager.add')->withInput($request->all());
        }
    }

    /**
     * Display the specified manager.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = User::find($id);
            return view('manager.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified manager.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = User::find($id);
            return view('manager.edit', compact('data','id','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Update the specified manager in storage.
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
                'phone' => 'numeric|unique:users,phone,'.$id.'|nullable|required_without:email' ,
                'password' => 'nullable|min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'nullable|min:6'           
            ]);
    
            $params = $request->except('_token');
            
            if(!empty($params['password'])){
                $params['password'] = Hash::make($params['password']);            
            } else {
                unset($params['password']);
            }
            
            unset($params['password_confirmation']);
            // dd($params);
            $data = User::where('id',$id)->update($params);
            // dd($data);
            if (!empty($data)) {
                Session::flash('message', 'Manager updated successfully');
                return redirect('/manager/list?'.$getQueryString);
                // return redirect()->route('manager.list');
            } else {
                return redirect()->route('manager.edit',$id)->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Toggle Status the specified manager from storage.
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
                $message = "Manager deactivated successfully";
            } else {
                User::where('id',$id)->update(['status'=>1]);
                $message = "Manager activated successfully";
            }
    
            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/manager/list?'.$getQueryString);
            }
            return redirect()->route('manager.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
}
