<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\Customer;
use App\Models\Packingslip;
use App\Models\PackingslipBarcode;
use App\Models\PackingslipProduct;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SalesOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        # sales order...
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $status = !empty($request->status)?$request->status:'';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $total = SalesOrder::count();
        
        $data = SalesOrder::select('*');
        $totalResult = SalesOrder::select('id');

        $countAll = SalesOrder::count();
        $countPending = SalesOrder::where('status','pending')->count();
        $countCancelled = SalesOrder::where('status','cancelled')->count();
        $countCompleted = SalesOrder::where('status','completed')->count();
        
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('customer', function ($customer) use ($search) {
                    $customer->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('customer', function ($customer) use ($search) {
                    $customer->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%')->orWhere('email', 'LIKE','%'.$search.'%');
                });
            });
        }

        if(!empty($type)){
            $data = $data->where('type', $type);
            $totalResult = $totalResult->where('type', $type);
        }
        if(!empty($status)){
            $data = $data->where('status', $status);
            $totalResult = $totalResult->where('status', $status);
        }
        
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'type' => $type,
            'status' => $status,
            'page'=>$request->page,
            'paginate'=>$paginate
        ]);

        return view('salesorder.list', compact('data','totalResult','total','search','type','paginate','status','countAll','countPending','countCancelled','countCompleted'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $customer_id = !empty($request->customer_id)?$request->customer_id:'';
        $type = !empty($request->type)?$request->type:'';
        $customer = Customer::where('status', 1)->orderBy('name','asc')->get();

        return view('salesorder.add',compact('customer','customer_id','type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:sp,fg',
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.product_price' => 'required',
            'details.*.tax' => 'required',
            'details.*.hsn_code' => 'required',
        ],[
            'details.*.product_id.required' => 'Please add product',
            'details.*.quantity.required' => 'Please add quantity',
            'details.*.product_price.required' => 'Please add rate',
            'details.*.tax.required' => 'Please add tax',
            'details.*.hsn_code.required' => 'Please add HSN',
        ]);

        $params = $request->except('_token');
        $params['order_no'] = 'KGAPO'.genAutoIncreNo(10,'sales_orders');

        // echo '<pre>'; print_r($params['details']);
        // echo '<pre>'; print_r($params); die;

        $order_no = "ORDER".genAutoIncreNo(10,'sales_orders');
        $salesOrderData = array(
            'order_no' => $order_no,
            'customer_id' => $params['customer_id'],
            'user_id' => Auth::user()->id,
            'type' => $params['type'],
            'details' => json_encode($params['details'])
        );
        $id = SalesOrder::insertGetId($salesOrderData);

        $details = $params['details'];
        $total_amount = 0;
        foreach($details as $detail){
            
            // $mop = getSingleAttributeTable('products','id',$detail['product_id'],'mop');
            // $product_total_price = ($detail['quantity'] * $mop);
            $product_total_price = ($detail['quantity'] * $detail['product_price']);
            $salesOrderProductData = array(
                'sales_orders_id' => $id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'product_price' => $detail['product_price'],
                'product_total_price' => $product_total_price,
                'tax' => $detail['tax'],
                'hsn_code' => $detail['hsn_code']
            );
            $total_amount += $product_total_price;
            SalesOrderProduct::insert($salesOrderProductData);

            

        }

        SalesOrder::where('id',$id)->update(['order_amount'=>$total_amount]);

        Session::flash('message', 'Sales Order Created Successfully');
        return redirect()->route('sales-order.list');
    }

    /*
    ** Cancel Purchase Order
    */    
    public function cancel($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            SalesOrder::where('id',$id)->update(['status'=>3]);
            Session::flash('message', 'Sales Order Cancelled Successfully');
            return redirect('/sales-order/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function show($idStr,$getQueryString='')
    {
        # order details...
        try{
            $id = Crypt::decrypt($idStr);
            $order = SalesOrder::find($id);
            $data = SalesOrderProduct::with('product')->where('sales_orders_id',$id)->get();
            return view('salesorder.detail', compact('id','order','data'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    public function generate_packing_slip(Request $request,$idStr,$getQueryString='')
    {
        # view goods out order...
        try{
            $id = Crypt::decrypt($idStr);
            $data = SalesOrderProduct::with('product')->with('order')->where('sales_orders_id',$id)->get();
            // dd($data);
        return view('salesorder.generateps', compact('idStr','data','getQueryString'));
        } catch (DecryptException $e){
            return abort(404);
        }
        
    }

    public function save_packing_slip(Request $request)
    {
        # save packing slip...
        // dd($request->all());
        $request->validate([
            'goods_out_type' => 'required',
            'details.*.quantity' => 'required',
        ],[
            'goods_out_type.required' => 'Please mention goods out type',
            'details.quantity.required' => 'Please add quantity',
        ]);

        $params = $request->except('_token');
        if(empty($params['details'])){
            return  redirect()->back()->withErrors(['goods_out_type'=> " Not enough quantity to genrate packing slip "])->withInput(); 
        }
        // dd($params);
        // $is_goods_out = 0;
        // if($params['goods_out_type'] == 'bulk'){
        //     $is_goods_out = 1;
        // }

        $packingslip_id = Packingslip::insertGetId([
            'sales_order_id' => $params['sales_order_id'],
            'goods_out_type' => $params['goods_out_type'],
            // 'is_goods_out' => $is_goods_out,
            'slipno' => $params['slipno'],
            'details' => json_encode($params['details'])
        ]);

        $packingslip_products = $params['details'];
        foreach($packingslip_products as $product){
            PackingslipProduct::insert([
                'packingslip_id' => $packingslip_id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity']
            ]);

            /* Update quantity of sales product */
            SalesOrderProduct::where('sales_orders_id',$params['sales_order_id'])->where('product_id', $product['product_id'])->update([
                'delivered_quantity'=>$product['quantity']
            ]);

            /* Update quantity of stock inventory */
            updateStockInvetory($product['product_id'],$product['quantity'],'out',$packingslip_id);       
        }
        updateSalesOrderStatusPS($params['sales_order_id']);
                        
        Session::flash('message', 'Packing Slip Generated Successfully');
        return redirect()->route('packingslip.list');
        die("Submitted");
    }
}
