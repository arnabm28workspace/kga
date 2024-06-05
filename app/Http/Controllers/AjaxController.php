<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PurchaseOrderBarcode;
use App\Models\Category;
use App\Models\StockBarcode;

class AjaxController extends Controller
{
    public function __construct(Request $request)
    {
        # code...
    }

    public function subcategory_by_category(Request $request)
    {
        # code...

        $cat_id = !empty($request->cat_id)?$request->cat_id:'';

        $data = Category::where('status', 1)->where('parent_id',$cat_id)->orderBy('name','asc')->get();

        return $data;
    }

    public function search_product_by_type(Request $request)
    {
        # for add product to PO...
        $search = !empty($request->search)?$request->search:'';
        $type = !empty($request->type)?$request->type:'';
        $idnotin = !empty($request->idnotin)?$request->idnotin:array();
        
        $data = Product::where('status',1)->where('type',$type);
        if(!empty($idnotin)){
            $data = $data->whereNotIn('id', $idnotin);
        }        
        
        $data = $data->where(function($query) use ($search){
            $query->where('title', 'LIKE','%'.$search.'%');
        })->get();

        return $data;
        
    }

    public function get_single_product(Request $request)
    {
        # single product...
        $id = !empty($request->id)?$request->id:'';
        if(!empty($id)){
            $data = Product::find($id);
            return $data;
        } else {
            return (object) array();
        }
    }

    public function pobulkscan(Request $request)
    {
        # PO Bulk Scan...

        $purchase_order_id = !empty($request->purchase_order_id)?$request->purchase_order_id:'';
        $product_id = !empty($request->product_id)?$request->product_id:'';
        $is_bulk_scanned = $request->is_bulk_scanned;
        $is_scanned = $request->is_scanned;
        $data = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->where('is_scanned', 0)->get();

        // dd($data);
        
        if(!empty($data)){
            foreach($data as $item){
                PurchaseOrderBarcode::where('id',$item->id)->update(['is_bulk_scanned'=>$is_bulk_scanned,'is_stock_in'=>$is_scanned]);
            }
        }

        return true;
        

        // if(!empty($data)){
        //     return true;
        // } else {
        //     return false;
        // }
    }

    public function checkPOScannedboxes(Request $request)
    {
        # Ajax ... 
        $data = array();
        $purchase_order_id = $request->purchase_order_id;
        $data = PurchaseOrderBarcode::where('purchase_order_id',$purchase_order_id)->where('is_scanned', 1)->get();

        return $data;
    }

    public function checkPSScannedboxes(Request $request)
    {
        # code...
        $data = array();
        $packingslip_id = $request->packingslip_id;
        $data = StockBarcode::where('packingslip_id',$packingslip_id)->where('is_scanned', 1)->get();

        return $data;
    }
}
