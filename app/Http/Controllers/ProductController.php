<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ProductController extends Controller
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
     * Display a listing of the product.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'';
        $type = !empty($request->type)?$request->type:'';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $total = Product::count();
        
        $totalActive = Product::where('status', 1)->count();
        $totlInactive = Product::where('status', 0)->count();
        $data = Product::select('*');
        $totalResult = Product::select('id');
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%')
                ->orWhere('public_name','LIKE','%'.$search.'%')
                ->orWhereHas('category', function ($category) use ($search) {
                    $category->where('name', 'LIKE','%'.$search.'%');
                })
                ->orWhereHas('subcategory', function ($subcategory) use ($search) {
                    $subcategory->where('name', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('title', 'LIKE','%'.$search.'%')
                ->orWhere('public_name','LIKE','%'.$search.'%')
                ->orWhereHas('category', function ($category) use ($search) {
                    $category->where('name', 'LIKE','%'.$search.'%');
                })
                ->orWhereHas('subcategory', function ($subcategory) use ($search) {
                    $subcategory->where('name', 'LIKE','%'.$search.'%');
                });
            });
        }
        if(!empty($type)){
            $data = $data->where('type', $type);
            $totalResult = $totalResult->where('type', $type);
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

        // dd($data);

        $data = $data->appends([
            'search'=>$search,
            'type'=>$type,
            'status'=>$status,
            'page'=>$request->page,
            'paginate'=>$paginate
        ]);
        return view('product.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','type','paginate'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $category = Category::where('status', 1)->where('parent_id','=',0)->orderBy('name','asc')->get();
        return view('product.add', compact('category'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|max:100|unique:products,title',
            'public_name' => 'required|max:100|unique:products,title',
            'cat_id' => 'required|exists:categories,id',
            'subcat_id' => 'required|exists:categories,id',
            'set_of_pcs' => 'required|numeric',            
            'service_level' => 'required|in:customer_level,dealer_level',
            'type' => 'required|in:fg,sp',
            'warranty_status' => 'required',
            'warranty_period' => 'nullable|required_if:warranty_status,yes',
            'mop' => 'required'
        ],[
            'cat_id.required' => 'The category field is required. ',
            'subcat_id.required' => 'The subcategory field is required. ',
            'set_of_pcs.required' => 'The set of pieces field is required. ',
        ]);

        $params = $request->except('_token');
        $params['unique_id'] = genAutoIncreNo();
        unset($params['subcat_name']);
        
        // dd($params);

        $id = Product::insertGetId($params);
        if (!empty($id)) {
            Session::flash('message', 'Product created successfully');
            return redirect()->route('product.list');
        } else {
            return redirect()->route('product.add')->withInput($request->all());
        }
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Product::with('category','subcategory')->find($id);
            return view('product.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Product::with('category','subcategory')->find($id);
            $category = Category::where('status', 1)->where('parent_id','=',0)->orderBy('name','asc')->get();
            $subcategory = Category::where('status', 1)->where('parent_id','=',$data->cat_id)->orderBy('name','asc')->get();
            return view('product.edit', compact('data','idStr','category','subcategory','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
    
    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idStr,$getQueryString='')
    {
        // dd($request->all());
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                'title' => 'required|max:100|unique:products,title,'.$id,
                'public_name' => 'required|max:100|unique:products,public_name,'.$id,
                'cat_id' => 'required|exists:categories,id',
                'subcat_id' => 'required|exists:categories,id',
                'set_of_pcs' => 'required|numeric',
                'service_level' => 'required|in:customer_level,dealer_level',
                'type' => 'required|in:fg,sp',
                'warranty_status' => 'required',
                'warranty_period' => 'nullable|required_if:warranty_status,yes',
                'mop' => 'required'
            ]);
            $params = $request->except('_token');
            unset($params['subcat_name']);
            $params['is_installable'] = isset($params['is_installable'])?$params['is_installable']:0;
            $params['is_amc_applicable'] = isset($params['is_amc_applicable'])?$params['is_amc_applicable']:0;
            $params['is_title_public_name_same'] = isset($params['is_title_public_name_same'])?$params['is_title_public_name_same']:0;
            // dd($params);
            $data = Product::where('id',$id)->update($params);
            if (!empty($data)) {
                Session::flash('message', 'Product updated successfully');
                return redirect('/product/list?'.$getQueryString);
                // return redirect()->route('product.list');
            } else {
                return redirect()->route('product.edit',$id)->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Change Status the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $product = Product::find($id);
            $message = "";
            if($product->status == 1){
                Product::where('id',$id)->update(['status'=>0]);
                $message = "Product deactivated successfully";
            } else {
                Product::where('id',$id)->update(['status'=>1]);
                $message = "Product activated successfully";
            }
            Session::flash('message', $message);        
            // if(!empty($getQueryString)){            
            return redirect('/product/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function copy($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = Product::with('category','subcategory')->find($id);
            $category = Category::where('status', 1)->where('parent_id','=',0)->orderBy('name','asc')->get();
            $subcategory = Category::where('status', 1)->where('parent_id','=',$data->cat_id)->orderBy('name','asc')->get();
            return view('product.copy', compact('data','id','category','subcategory','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    
    
}
