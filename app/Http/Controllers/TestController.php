<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceNotificationCSV;
use App\Models\Packingslip;
use App\Models\StockInventory;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderBarcode;
use App\Models\PurchaseOrderProduct;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    //

    public function index(Request $request)
    {
        $product = DB::table('products')->select('id','title','unique_id')->get();

        foreach($product as $item){
            $u =  str_pad($item->id,5,"0",STR_PAD_LEFT);
            // DB::table('products')->where('id',$item->id)->update([
            //     'unique_id' => $u
            // ]);
        }
        
        echo '<pre>'; print_r($product);
        
    }

    public function mail_send(Request $request)
    {
        # mail send...
        $to = !empty($request->to)?$request->to:'';


        if(!empty($to)){
            $data['email'] = $to;
            $data['name'] = "Arnab M";
            $data['subject'] = "Test Email KGA";
            // $mailBody = "<h1>Hi, Arnab!</h1>";
            $mailBody = "";
            
            $mailBody .= "<h1>Hi, Arnab!</h1> <br/>";
            $mailBody .= "<p>You have a new notification for servicing new goods.<p>";
            $mailBody .= "Please find the details below , <br/>";
            $mailBody .= "
            <table cellspacing='0' cellpadding='0'>
                <thead>
                    <tr>
                        <th style='padding:5px; border: 1px solid #ddd;'>Order Detail</th>
                        <th style='padding:5px; border: 1px solid #ddd;'>Product Detail</th>
                        <th style='padding:5px; border: 1px solid #ddd;'>Customer Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>Bill No: <strong>K066242223/09866</strong> </td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Product Sl No: <strong>23520220003006</strong> </td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Customer Name: <strong>SK MUKUL</strong> </td>
                    </tr>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>Delivery Date:<strong>24/11/2022</strong></td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Product Name: <strong>KGA Ultra HD LED TV Pro Series- NS (Black)</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Address: <strong>BALARAM PUR SAMAJ UNNOION SAMITY CLUB </strong></td>
                    </tr>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>Branch: <strong>BAGUIATI SHOWROOM</strong></td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Brand: <strong>ABC</strong> </td>
                        <td style='padding:5px; border: 1px solid #ddd;'>District: <strong>SOUTH 24 PARGANAS<strong></strong></td>                
                    </tr>
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Class: <strong>PANEL_LED</strong></td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Customer PIN Code: <strong>700114</strong></td>
                    </tr>            
                    <tr>
                        <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                        <td style='padding:5px; border: 1px solid #ddd;'>Contact Number: <strong>9876543210</strong></td>
                    </tr>
                </tbody>
            </table>
            ";
            $data['body'] = $mailBody;
            // $data['blade_file'] = "mailview/test";
            // dd($data);
            $mail = sendMail($data);
            // print_r($mail);
            if($mail) {
                echo "Sent";                
            }else {
                $errors = 'Failed to send password reset email, please try again.';
                echo $errors;
            }

        } else {
            echo "Please send <strong>to</strong> as query param";
        }
    }

    public function cookie(Request $request)
    {
        
    }

}
