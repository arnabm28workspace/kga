<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseOrderBarcode;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Models\StockBarcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PurchaseOrderController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $po_type = !empty($request->po_type)?$request->po_type:'po';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $total = PurchaseOrder::count();
        
        $data = PurchaseOrder::select('*');
        $totalResult = PurchaseOrder::select('id');
        if($po_type == 'po'){
            $data = $data->whereIn('status', [1,3]);
            $totalResult = $totalResult->whereIn('status', [1,3]);
        } else {
            $data = $data->where('status', 2);
            $totalResult = $totalResult->where('status', 2);
        }
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('supplier', function ($supplier) use ($search) {
                    $supplier->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('order_no', 'LIKE','%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%')->orWhereHas('supplier', function ($supplier) use ($search) {
                    $supplier->where('name', 'LIKE','%'.$search.'%')->orWhere('phone', 'LIKE','%'.$search.'%');
                });
            });
        }

        if(!empty($type)){
            $data = $data->where('type', $type);
            $totalResult = $totalResult->where('type', $type);
        }
        
        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'type' => $type,
            'po_type' => $po_type,
            'page'=>$request->page,
            'paginate'=>$paginate
        ]);

        // dd($data);
        return view('purchaseorder.list', compact('data','totalResult','total','search','type','po_type','paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $supplier_id = !empty($request->supplier_id)?$request->supplier_id:'';
        $type = !empty($request->type)?$request->type:'';
        $supplier = Supplier::where('status', 1)->orderBy('name','asc')->get();

        // if(isset($request->supplier_id) && isset($request->type)){
        //     $request->validate([
        //         'supplier_id' => 'required',
        //         'type' => 'required'
        //     ]);
        // }
        return view('purchaseorder.add',compact('supplier','supplier_id','type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:sp,fg',
            'details.*.product_id' => 'required',
            'details.*.quantity' => 'required',
            'details.*.pack_of' => 'required_if:type,sp',
            'details.*.quantity_in_pack' => 'required_if:type,sp',
            'details.*.cost_price' => 'required',
            'details.*.hsn_code' => 'required',
            'details.*.mrp' => 'required',
            'details.*.tax' => 'required'
        ],[
            'details.*.product_id.required' => 'Please add product',
            'details.*.quantity.required' => 'Please add quantity',
            'details.*.cost_price.required' => 'Please add cost price',
            'details.*.mrp.required' => 'Please add MRP',
            'details.*.tax.required' => 'Please add Tax',
            'details.*.hsn_code.required' => 'Please add HSN'
        ]);

        $params = $request->except('_token');
        $params['order_no'] = 'KGAPO'.genAutoIncreNo(10,'purchase_orders');

        // echo '<pre>'; print_r($params['details']);
        

        $order_no = "KGAPO".genAutoIncreNo(10,'purchase_orders');
        $purchaseOrderData = array(
            'created_by' => Auth::user()->id,
            'order_no' => $order_no,
            'supplier_id' => $params['supplier_id'],
            'type' => $params['type'],
            'details' => json_encode($params['details'])
        );
        $id = PurchaseOrder::insertGetId($purchaseOrderData);

        $details = $params['details'];
        $total_amount = 0;
        foreach($details as $detail){
            
            $pack_of = 1;
            $quantity_in_pack = $detail['quantity'];
            if($params['type'] == 'sp'){
                $pack_of = $detail['pack_of'];
                $quantity_in_pack = $detail['quantity_in_pack'];
            }

            $purchaseOrderProductData = array(
                'purchase_order_id' => $id,
                'product_id' => $detail['product_id'],
                'pack_of' => $pack_of,
                'quantity_in_pack' => $quantity_in_pack,
                'quantity' => $detail['quantity'],
                'cost_price' => $detail['cost_price'],
                'total_price' => $detail['total_price'],
                'mrp' => $detail['mrp'],
                'tax' => $detail['tax'],
                'hsn_code' => $detail['hsn_code']
            );
            $total_amount += $detail['total_price'];
            PurchaseOrderProduct::insert($purchaseOrderProductData);

            $quantity = $detail['quantity'];
            if($params['type'] == 'sp'){
                $quantity = $detail['pack_of'];
            }

            for($i=0; $i<$quantity;$i++){
                // $barcodeGenerator = barcodeGenerator();
                $barcodeGenerator = genAutoIncreNoBarcode($detail['product_id'],$detail['product_unique_id'],date('Y'));
                $barcode_no = $barcodeGenerator['barcode_no'];
                $code_html = $barcodeGenerator['code_html'];
                $code_base64_img = $barcodeGenerator['code_base64_img'];
                $purchaseOrderBarcodeData = array(
                    'purchase_order_id' => $id,
                    'product_id' => $detail['product_id'],
                    'barcode_no' => $barcode_no,
                    'code_html' => $code_html,
                    'code_base64_img' => $code_base64_img,
                );
                PurchaseOrderBarcode::insert($purchaseOrderBarcodeData);
            }

        }

        PurchaseOrder::where('id',$id)->update(['amount'=>$total_amount]);

        Session::flash('message', 'Purchase Order Created Successfully');
        return redirect()->route('purchase-order.list');
    }

    /*
    ** Cancel Purchase Order
    */    
    public function cancel($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            PurchaseOrder::where('id',$id)->update(['status'=>3]);
            Session::flash('message', 'Purchase Order Cancelled Successfully');
            return redirect('/purchase-order/list?'.$getQueryString);
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** View Barcodes
    */

    public function make_grn(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $search = !empty($request->search)?$request->search:'';
            $goods_in_type = !empty($request->goods_in_type)?$request->goods_in_type:'';
            $purchaseorder = PurchaseOrder::find($id);

            $order_no = $purchaseorder->order_no;
            $data = PurchaseOrderBarcode::with('product')->where('purchase_order_id',$id);        
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            $data = $data->get()->sortBy('product_id')->groupBy('product.id');
            // dd($data);
            if(!empty($goods_in_type)){
                PurchaseOrder::where('id',$id)->update([
                    'goods_in_type' => $goods_in_type
                ]);
            }
            
            return view('purchaseorder.makegrn', compact('id','idStr','order_no','getQueryString','data','search','goods_in_type'));
        } catch ( DecryptException $e) {
            return abort(404);
        }        
    }

    /*
    ** Generate GRN
    */

    public function generategrn(Request $request)
    {
        # generate GRN...       

        $params = $request->except('_token');
        $purchase_order_id = $params['id'];
        $grn_no = genAutoIncreNo(10,'stock');
        $params['grn_no'] = "GRN".$grn_no;
        // dd($params);
        

        
        $id = Stock::insert([
            'purchase_order_id'=>$purchase_order_id,
            'grn_no' => "GRN".$grn_no
        ]);
        for($i=0;$i<count($params['product_id']);$i++){
            StockProduct::insert([
                'stock_id' => $id,
                'product_id' => $params['product_id'][$i],
                'count' => $params['count'][$i],
            ]);
            updateStockInvetory($params['product_id'][$i],$params['count'][$i],'in',$purchase_order_id);
        }
        

        
        $barcodeArr = array();
        for($i=0;$i<count($params['barcode_no']);$i++){
            $barcodeArr[] = array(
                'stock_id' => $id,
                'barcode_no' => $params['barcode_no'][$i],
                'product_id' => getSingleAttributeTable('purchase_order_barcodes','barcode_no',$params['barcode_no'][$i],'product_id'),
                'code_html' => getSingleAttributeTable('purchase_order_barcodes','barcode_no',$params['barcode_no'][$i],'code_html'),                
                'code_base64_img' => getSingleAttributeTable('purchase_order_barcodes','barcode_no',$params['barcode_no'][$i],'code_base64_img'),
            );
        }
        StockBarcode::insert($barcodeArr);
        
        // dd($barcodeArr);

        $purchaseorder = PurchaseOrder::find($purchase_order_id);
        if($purchaseorder->goods_in_type == 'scan'){
            PurchaseOrder::where('id',$purchase_order_id)->update([
                'status'=>2,
                'is_goods_in'=>1,
                'grn_no'=>$params['grn_no']
            ]);
        } else {
            PurchaseOrder::where('id',$purchase_order_id)->update([
                'status'=>2,
                'is_goods_in'=>0,
                'grn_no'=>$params['grn_no']
            ]);
        }
        
        
        Session::flash('message', 'Goods Received Note Created Successfully');
        return redirect()->route('purchase-order.list', ['po_type'=>'grn']);
    }

    /*
    ** View GRN
    */

    public function viewgrn(Request $request,$idStr)
    {
        # view GRN...
        try{
            $id = Crypt::decrypt($idStr);
            $data = PurchaseOrderProduct::where('purchase_order_id',$id)->get();
            foreach($data as $product){
                $barcodes = PurchaseOrderBarcode::where('product_id',$product->product_id)->get();
                $product->barcodes = $barcodes;
            }
            return view('purchaseorder.viewgrn', compact('data','id'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    public function download($idStr)
    {
        # code...
        try{
            $id = Crypt::decrypt($idStr);
            $data = PurchaseOrderBarcode::where('purchase_order_id',$id)->get();
            $po = PurchaseOrder::find($id);
            $status = $po->status;

            return view('purchaseorder.download', compact('data','id','status'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    public function show($idStr,$getQueryString='')
    {
        # code...
        try{
            $id = Crypt::decrypt($idStr);
            $order = PurchaseOrder::find($id);
            $data = PurchaseOrderProduct::with('product')->where('purchase_order_id',$id)->get();
            return view('purchaseorder.detail', compact('id','order','data'));
        } catch (DecryptException $e) {
            return abort(404);
        }
        
    }

    
    
}
