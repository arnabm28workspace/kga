<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

function test(){
    return "Test";
}

function barcodeGenerator(){
    $length = 12;    
    $min = str_repeat(0, $length-1) . 1;
    $max = str_repeat(9, $length);
    $barcode_no =  mt_rand($min, $max);   

    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    $generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG(); // Vector based SVG
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG(); // Pixel based PNG
    $generatorJPG = new Picqer\Barcode\BarcodeGeneratorJPG(); // Pixel based JPG
    $generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML(); // Pixel based HTML
    $generatorDynamicHTML = new Picqer\Barcode\BarcodeGeneratorDynamicHTML(); // Vector based HTML

    $code_html = $generator->getBarcode($barcode_no, $generator::TYPE_CODE_128);
    $code_base64_img = base64_encode($generatorPNG->getBarcode($barcode_no, $generatorPNG::TYPE_CODE_128));

    return array('barcode_no'=>$barcode_no,'code_html'=>$code_html,'code_base64_img'=>$code_base64_img);
}

function barcodeGeneratorTest($no){
    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    $generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG(); // Vector based SVG
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG(); // Pixel based PNG
    $generatorJPG = new Picqer\Barcode\BarcodeGeneratorJPG(); // Pixel based JPG
    $generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML(); // Pixel based HTML
    $generatorDynamicHTML = new Picqer\Barcode\BarcodeGeneratorDynamicHTML(); // Vector based HTML

    $code_html = $generator->getBarcode($no, $generator::TYPE_CODE_128);
    $code_base64_img = base64_encode($generatorPNG->getBarcode($no, $generatorPNG::TYPE_CODE_128));

    return array('barcode_no'=>$no,'code_html'=>$code_html,'code_base64_img'=>$code_base64_img);
}

function genAutoIncreNo($length=5,$table='products'){
    $val = 1;    
    $data = DB::table($table)->select('id')->orderBy('id','desc')->first();
    if(empty($data)){
        $val = 1;
    } else {
        $val = $data->id + 1;
    }    
    $number = str_pad($val,$length,"0",STR_PAD_LEFT);
    return $number;
}

function getSingleAttributeTable($tableName,$idColumn,$idValue,$attribute){    
    $data = DB::table($tableName)->select($attribute)->where($idColumn,$idValue)->first();
    return $data->$attribute;
}

function isBulkScanned($purchase_order_id,$product_id){
    $data = DB::table('purchase_order_barcodes')->where('purchase_order_id',$purchase_order_id)->where('product_id',$product_id)->where('is_bulk_scanned', 1)->first();

    if(!empty($data)){
        return true;
    } else {
        return false;
    }
}

function sendMail($data){
    // $mail = Mail::send(['text'=>'mailview'] , $data, function ($message) use ($data) {
    //     $message->to($data['email'], $data['name'])->subject($data['subject']);
    // });
    try{        
        $mail = Mail::send([], [], function ($message) use ($data)  {
            $message->to($data['email'],$data['name'])
              ->subject($data['subject'])
              // here comes what you want
            //   ->setBody('Hi, welcome user!'); // assuming text/plain
              // or:
              ->setBody($data['body'], 'text/html'); // for HTML rich messages
          });
    
        return true;
    } catch(Exception $e){
        return $e;
    }
    
}

function getDateValue($excelDateTime){
    // die($excelDateTime);
    $date_format = floor($excelDateTime);
    $time_format = $excelDateTime - $date_format;
    $mysql_strdate = ($date_format > 0) ? ( $date_format - 25569 ) * 86400 + $time_format * 86400 :    $time_format * 86400;
    $mysql_date_format = date("Y-m-d", $mysql_strdate);
    return $mysql_date_format;
}

function getStockProduct($product_id){
    $data = DB::table('stock_barcodes')->where('product_id',$product_id)->where('is_scanned', 0)->count();
    return $data;
}

function getStockInventoryProduct($product_id){
    $data = DB::table('stock_inventory')->where('product_id',$product_id)->first();
    if(!empty($data)){
        return $data->quantity;
    } else {
        return 0;
    }
}

function updateStockInvetory($product_id,$quantity,$type='in',$data_id){
    # $type is 'in/out'
    $data = DB::table('stock_inventory')->where('product_id',$product_id)->first();
    $stock_quantity = 0;
    if(!empty($data)){
        $stock_quantity = $data->quantity;
        if($type == 'in'){
            $net_quantity = ($stock_quantity + $quantity);
            DB::table('stock_inventory')->where('product_id',$product_id)->update(['quantity'=>$net_quantity]);
        } else if ($type == 'out'){
            $net_quantity = ($stock_quantity - $quantity);
            DB::table('stock_inventory')->where('product_id',$product_id)->update(['quantity'=>$net_quantity]);
        }
        
    } else {
        DB::table('stock_inventory')->insert([
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);
    }

    if($type == 'in'){
        $entry_type = 'grn';
    } else if ($type == 'out'){
        $entry_type = 'ps';
    }
    DB::table('stock_logs')->insert([
        'product_id' =>$product_id,
        'quantity' => $quantity,
        'type' => $type,
        'data_id' => $data_id,
        'entry_type' => $entry_type,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
}

function getSalesOrderProduct($sales_orders_id,$product_id){
    $data = DB::table('sales_order_products')->where('sales_orders_id',$sales_orders_id)->where('product_id',$product_id)->first();

    return $data;
}

function updateSalesOrderStatusPS($sales_order_id){
    $order_status = 'pending';
    $sales_order_products = DB::table('sales_order_products')->where('sales_orders_id', $sales_order_id)->get();
    $isAllCompleted = 0;
    $isCompleteArr = array();
    foreach($sales_order_products as $pro){
        if($pro->quantity == $pro->delivered_quantity){
            $isAllCompleted = 1;            
        } else {
            $isAllCompleted = 0;
        }
        $pro->is_all_completed = $isAllCompleted;
        $isCompleteArr[] = $isAllCompleted;
    }

    if(in_array(0,$isCompleteArr)){
        $order_status = 'pending';
    } else {
        $order_status = 'completed';
    }
    DB::table('sales_orders')->where('id',$sales_order_id)->update([
        'status' => $order_status
    ]);
}

function getAmountAlphabetically($amount)
{
    $number = $amount;
    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ? "" . $words[$point / 10] . " " . $words[$point = $point % 10] : 'Zero';
    return  ucwords($result) . " Rupees  and " . $points . " Paise";
}

function getPercentageVal($percent,$number)
{
    return ($percent / 100) * $number;
}

function getGSTAmount($price,$gst_val){
    $gst_amount = $price - ( $price * ( 100 / ( 100 + (getPercentageVal($gst_val,100)) ) ) );
    $net_price = ($price - $gst_amount);

    return array('gst_amount' => $gst_amount , 'net_price' => $net_price);
}

function getPSProductQuantity($packingslip_id,$product_id){
    $data = DB::table('packingslip_products')->where('packingslip_id',$packingslip_id)->where('product_id',$product_id)->first();
    return $data->quantity;
}

function checkStockProductScanned($barcode_no){
    $data = DB::table('stock_barcodes')->select('id','stock_id','product_id','barcode_no','is_scanned','is_stock_out','packingslip_id')->where('barcode_no',$barcode_no)->first();
    // dd($data);
    if(!empty($data)){
        if(!empty($data->is_scanned) && !empty($data->packingslip_id)){
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

function genAutoIncreNoBarcode($product_id,$product_unique_id,$year){
    $val = 1;    
    $data = DB::table('purchase_order_barcodes')->where('product_id',$product_id)->whereRaw("DATE_FORMAT(created_at, '%Y') = '".$year."'")->count();

    if(!empty($data)){
        $val = ($data + 1);
    }

    // dd($data);
    $prefix = $product_unique_id.''.$year.'';
    $suffix = str_pad($val,5,"0",STR_PAD_LEFT);
    $number = $prefix.''.$suffix;
    $barcode_no = $number;
    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

    $code_html = $generator->getBarcode($barcode_no, $generator::TYPE_CODE_128);

    $code_base64_img = base64_encode($generatorPNG->getBarcode($barcode_no, $generatorPNG::TYPE_CODE_128));

    return array('barcode_no'=>$barcode_no,'code_html'=>$code_html,'code_base64_img'=>$code_base64_img);
    
}