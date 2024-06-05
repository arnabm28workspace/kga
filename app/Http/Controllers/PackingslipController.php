<?php

namespace App\Http\Controllers;

use App\Models\Packingslip;
use App\Models\PackingslipBarcode;
use App\Models\PackingslipProduct;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\StockBarcode;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PackingslipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $data = Packingslip::select('*');
        $totalResult = Packingslip::select('*');

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('slipno', 'LIKE', '%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('slipno', 'LIKE', '%'.$search.'%')->orWhere('details', 'LIKE', '%'.$search.'%');
            });
        }

        if(!empty($type)){
            $data = $data->whereHas('sales_order', function($querytype) use ($type){
                $querytype->where('type', $type);
            });
            $totalResult = $totalResult->whereHas('sales_order', function($querytype) use ($type){
                $querytype->where('type', $type);
            });
        }

        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'page'=>$request->page,
            'type' => $type,
            'paginate'=>$paginate
        ]);
        return view('packingslip.list', compact('data','totalResult','search','type','paginate'));
    }

    public function download($idStr)
    {
        # download...
        try {
            $id = Crypt::decrypt($idStr);
            $packingslips = Packingslip::find($id); 
            // dd($packingslips);
            $sales_orders = SalesOrder::find($packingslips->sales_order_id);
            $customer = Customer::find($sales_orders->customer_id);
            return view('packingslip.download', compact('id','idStr','packingslips','customer'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** Raise Invoice Preview
    */
    public function raise_invoice(Request $request,$packingslip_idStr,$getQueryString='')
    {
        try {
            $packingslip_id = Crypt::decrypt($packingslip_idStr);
            $packingslip = Packingslip::find($packingslip_id);
            if(empty($packingslip->invoice_no)){
                $data = PackingslipProduct::where('packingslip_id',$packingslip_id)->get();
                $customer = Customer::find($packingslip->sales_order->customer_id);
                return view('packingslip.raise-invoice', compact('packingslip','packingslip_id','packingslip_idStr','data','customer'));
            } else {
                Session::flash('message', 'Invoice exists already');
                return redirect()->route('packingslip.list');
            }            
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    /*
    ** Save Invoice 
    */
    public function save_invoice(Request $request)
    {
        # save invoice...
        
        $params = $request->except('_token');
        // dd($params);
        $invoice_no = genAutoIncreNo(10,'invoices');
        $items = $params['items'];
        $invoiceData = array(
            'invoice_no' => $invoice_no,
            'sales_order_id' => $params['sales_order_id'],
            'customer_id' => $params['customer_id'],
            'packingslip_id' => $params['packingslip_id'],
            'total_amount' => $params['total_amount'],
            'customer_details' => json_encode($params['customer_details']),
            'item_details' => json_encode($items)
        );
        $invoice_id = Invoice::insertGetId($invoiceData);
        
        foreach($items as $item){
            $invoiceitemData = array(
                'invoice_id' => $invoice_id,
                'product_id' => $item['product_id'],
                'product_title' => $item['product_title'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total_price' => $item['total_price'],
                'price_exc_tax' => $item['price_exc_tax'],
                'total_price_exc_tax' => $item['total_price_exc_tax'],
                'tax' => $item['tax'],
                'hsn_code' => $item['hsn_code']
            );
            InvoiceItem::insert($invoiceitemData);
        }

        Packingslip::where('id', $params['packingslip_id'])->update(['invoice_no'=>$invoice_no]);

        Session::flash('message', 'Invoice Generated Successfully');
        return redirect()->route('invoice.list');
    }

    /* 
    ** View Goods Scan Out  
    */
    public function goods_scan_out(Request $request,$packingslip_idStr,$getQueryString='')
    {
        // return abort(404);
        # view goods scan out...
        try {
            $search = !empty($request->search)?$request->search:'';
            $packingslip_id = Crypt::decrypt($packingslip_idStr);
            $packingslip = Packingslip::find($packingslip_id);
            $proIds = array();
            $packingslip_products = PackingslipProduct::where('packingslip_id', $packingslip_id)->get();
            foreach($packingslip_products as $product){
                $proIds[] = $product->product_id;
            }
            $data = StockBarcode::with('product')->where('is_stock_out', 0)->whereIn('product_id', $proIds);        
            if(!empty($search)){
                $data = $data->where(function($q) use ($search){
                    $q->where('barcode_no','LIKE', '%'.$search.'%')->orWhereHas('product', function ($product) use ($search) {
                        $product->where('title', 'LIKE','%'.$search.'%');
                    });
                });
            }
            $data = $data->get()->sortBy('product_id')->groupBy('product.id');
            $total_products = PackingslipProduct::where('packingslip_id', $packingslip_id)->sum('quantity');
            // dd($total_products);
            return view('packingslip.goods-out', compact('packingslip_id','packingslip_idStr','packingslip','getQueryString','search','data','total_products'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }

    public function save_scan_out(Request $request,$id)
    {
        # save scan out...
        // return abort(503);
        // dd($request->all());
        $params = $request->except('_token');
        $barcodes = $request->barcodes;
        foreach($barcodes as $barcode_no){    
            
            $stock_barcodes = StockBarcode::where('barcode_no',$barcode_no)->first();
            $product_id = $stock_barcodes->product_id;
            $code_html = $stock_barcodes->code_html;
            $code_base64_img = $stock_barcodes->code_base64_img;

            $packingslip_barcode_arr = array(
                'packingslip_id' => $id,
                'product_id' => $product_id,
                'barcode_no' => $barcode_no,
                'code_html' => $code_html,
                'code_base64_img' => $code_base64_img
            );
            // dd($packingslip_barcode_arr);            
            PackingslipBarcode::insert($packingslip_barcode_arr);
        }
        PackingSlip::where('id',$id)->update(['is_goods_out' => 1]);
        Session::flash('message', 'All scanned goods out successfully');
        return redirect()->route('packingslip.list');
        // dd($barcodes);
        
    }
    
}
