<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PurchaseOrderBarcode;
use App\Models\StockBarcode;
use Illuminate\Support\Facades\Response;


class ScanController extends Controller
{
    public function __construct()
    {
        
    }

    public function stockin(Request $request)
    {
        # scan for stock in ...
        $validator = Validator::make($request->all(),[
            'barcode_no' => 'required|numeric|exists:purchase_order_barcodes,barcode_no'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $barcode_no = $params['barcode_no'];
            $exist = PurchaseOrderBarcode::where('barcode_no', $barcode_no)->first();

            if(!empty($exist)){
                if(empty($exist->is_scanned)){
                    PurchaseOrderBarcode::where('barcode_no',$barcode_no)->update(['is_scanned' => 1]);
                    return Response::json(['status' => true, 'message' => "Scanned successfully", 'data' => array() ],200);
                } else {
                    return Response::json(['status' => false, 'message' => "Already scanned", 'data' => array() ],200);
                }
            } else {
                return Response::json(['status' => false, 'message' => "No barcode found", 'data' => array() ],200);
            } 
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }   
    }

    public function stockout(Request $request)
    {
        # scan for stock out ...
        $validator = Validator::make($request->all(),[
            'barcode_no' => 'required|numeric|exists:stock_barcodes,barcode_no'
        ]);

        if(!$validator->fails()){
            $params = $request->except('_token');
            $barcode_no = $params['barcode_no'];
            $exist = StockBarcode::where('barcode_no', $barcode_no)->first();

            if(!empty($exist)){
                if(empty($exist->is_scanned)){
                    StockBarcode::where('barcode_no',$barcode_no)->update(['is_scanned' => 1]);
                    return Response::json(['status' => true, 'message' => "Scanned successfully", 'data' => array() ],200);
                } else {
                    return Response::json(['status' => false, 'message' => "Already scanned", 'data' => array() ],200);
                }
            } else {
                return Response::json(['status' => false, 'message' => "No barcode found", 'data' => array() ],200);
            } 
        } else {
            return Response::json(['status' => false, 'message' => $validator->errors()->first() , 'data' => array( $validator->errors() ) ],400);
        }
    }
}
