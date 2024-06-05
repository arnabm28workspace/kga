<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class InvoiceController extends Controller
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
        $data = Invoice::select('*');
        $totalResult = Invoice::select('*');
        if(!empty($search)){
            $data = $data->where(function($q) use ($search){
                $q->where('invoice_no', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($q) use ($search){
                $q->where('invoice_no', 'LIKE', '%'.$search.'%');
            });
        }

        if(!empty($type)){
            $data = $data->where(function($q_type) use ($type) {
                $q_type->orWhereHas('sales_order', function($q_type) use ($type){
                    $q_type->where('type', '=', $type);
                });
            });
        }

        $data = $data->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'page'=>$request->page,
            'type'=>$type,
            'paginate'=>$paginate
        ]);
        return view('invoice.list', compact('data','totalResult','search','type','paginate'));
    }

    public function download($idStr)
    {
        # code...
        try {
            $id = Crypt::decrypt($idStr);
            $invoice = Invoice::find($id);
            $invoice_items = InvoiceItem::where('invoice_id',$id)->get();
            $invoice->invoice_items = $invoice_items;

            // dd($invoice);
            return view('invoice.download', compact('invoice','id'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
    }
}
