<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use File; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CategoryController extends Controller
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
     * Display a listing of the category or any child category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'parent';
        $status = !empty($request->status)?$request->status:'all';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $total = Category::count();
        
        $totalActive = Category::where('status', 1)->count();
        $totlInactive = Category::where('status', 0)->count();
        $data = Category::select('*')->with('child');
        $totalResult = Category::select('*')->with('child');
        
        $data = $data->where(function($query) use ($search){
            $query->where('name', 'LIKE','%'.$search.'%')->orWhereHas('child', function ($q) use ($search) {
                $q->where('name', 'LIKE','%'.$search.'%');
            });
        });
        $totalResult = $totalResult->where(function($query) use ($search){
            $query->where('name', 'LIKE','%'.$search.'%')->orWhereHas('child', function ($q) use ($search) {
                $q->where('name', 'LIKE','%'.$search.'%');
            });
        });

        if(!empty($type)){
            if($type == 'parent'){
                $data = $data->where('parent_id','=',0);
                $totalResult = $totalResult->where('parent_id','=',0);
            } else if ($type == 'child'){
                $data = $data->where('parent_id','!=',0);
                $totalResult = $totalResult->where('parent_id','!=',0);
            }
        }
        
        
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
            'type'=>$type,
            'status'=>$status,
            'page'=>$request->page,
            'paginate'=>$paginate
        ]);
        return view('category.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','type','paginate'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $type = !empty($request->type)?$request->type:'parent';
        $parents = Category::where('parent_id',0)->where('status',1)->get();
        return view('category.add', compact('parents','type'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            // 'parent_id' => 'exists:categories,id|nullable',
            'parent_id' => 'nullable|required_if:type,child',
            'name' => 'required|unique:categories,name'
        ],[
            'parent_id.required_if' => 'Please choose a category',
            'name.required' => 'Please enter name'
        ]);

        $params = $request->except('_token');

        if(!empty($params['photo'])){
            $upload_path = "uploads/category/";
            $image = $params['photo'];
            $imageName = time() . "." . $image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $params['image'] = $upload_path . $uploadedImage;
            unset($params['photo']);
        } else {
            $params['image'] = '';
        }

        // $params['parent_id'] = !empty($params['parent_id'])?$params['parent_id']:0;
        // dd($params);
        unset($params['type']);

        $id = Category::insertGetId($params);
        $message = "";
        if(empty($params['parent_id'])){
            $message = "Category Created Successfully";
            Session::flash('message', $message);
            return redirect()->route('category.list',['type'=>'parent']);
        } else {
            $message = "Sub Category Created Successfully";
            Session::flash('message', $message);
            return redirect()->route('category.list',['type'=>'child']);
        }

        
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$idStr,$getQueryString='')
    {
        try {
            $type = !empty($request->type)?$request->type:'parent';
            $id = Crypt::decrypt($idStr);
            $data = Category::find($id);
            return view('category.detail', compact('data','id','getQueryString','type'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$idStr,$getQueryString='')
    {
        try {
            $type = !empty($request->type)?$request->type:'parent';
            $id = Crypt::decrypt($idStr);
            $data = Category::find($id);
            $parents = Category::where('parent_id',0)->where('status',1)->get();
            return view('category.edit', compact('type','data','idStr','parents','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'parent_id' => 'exists:categories,id|nullable',
                'name' => 'unique:categories,name,'.$id
            ]);
    
            $params = $request->except('_token');
    
            $category = Category::find($id);
    
            if(!empty($params['photo'])){            
                File::delete($category->image);
    
                $upload_path = "uploads/category/";
                $image = $params['photo'];
                $imageName = time() . "." . $image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $params['image'] = $upload_path . $uploadedImage;
                unset($params['photo']);            
    
            } else {
                $params['image'] = $category->image;
            }
    
            // dd($params); die;
            $params['parent_id'] = !empty($params['parent_id'])?$params['parent_id']:0;
            $data = Category::where('id',$id)->update($params);
    
            $message = "";
            if(empty($params['parent_id'])){
                $message = "Parent Category Updated Successfully";
            } else {
                $message = "Child Category Updated Successfully";
            }
    
            Session::flash('message', $message);
            return redirect('/category/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /**
     * Change Status the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $category = Category::find($id);
            $message = "";
            if($category->status == 1){
                Category::where('id',$id)->update(['status'=>0]);
                $message = "Category deactivated successfully";
            } else {
                Category::where('id',$id)->update(['status'=>1]);
                $message = "Category activated successfully";
            }
            Session::flash('message', $message);        
            if(!empty($getQueryString)){            
                return redirect('/category/list?'.$getQueryString);
            }
            return redirect()->route('category.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
}
