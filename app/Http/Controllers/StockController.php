<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\StockInventory;
use App\Models\StockLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $ordering = 'desc';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $data = StockInventory::with('product');
        $data = $data->where(function($q) use ($search){
            $q->orWhereHas('product', function ($q) use ($search) {
                $q->where('title', 'LIKE','%'.$search.'%')->orWhere('unique_id', 'LIKE', '%'.$search.'%');
            });
        });

        if(!empty($type)){
            $data = $data->where(function($q_type) use ($type) {
                $q_type->orWhereHas('product', function($q_type) use ($type){
                    $q_type->where('type', '=', $type);
                });
            });
        }
        
        $data = $data->paginate($paginate);

        $data = $data->appends([
            'page' => $request->page,
            'search' => $search,
            'type' => $type,
            'paginate'=>$paginate
        ]);
        return view('stock.list', compact('data','search','type','paginate'));
    }

    public function logs($product_idStr,$getQueryString='')
    {
        # product wise stock logs...
        try {
            $product_id = Crypt::decrypt($product_idStr);
            $product = Product::find($product_id);
            $stock_inventory = StockInventory::where('product_id',$product_id)->first();
            $count = $stock_inventory->quantity;
            $data = StockLog::where('product_id',$product_id)->orderBy('id','desc')->paginate(10);
            return view('stock.logs', compact('data','product_id','product_idStr','product','getQueryString','count'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
}
