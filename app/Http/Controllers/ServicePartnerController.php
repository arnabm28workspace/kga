<?php

namespace App\Http\Controllers;

use App\Models\ServicePartner;
use App\Models\Pincode;
use App\Models\ServicePartnerPincode;
use App\Models\ServiceNotificationCSV;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\File;
use File; 
use Illuminate\Support\Facades\Storage;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\FromArray;
// use Excel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ServicePartnerController extends Controller
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
        $status = !empty($request->status)?$request->status:'';
        $type = !empty($request->type)?$request->type:'';
        $paginate = !empty($request->paginate)?$request->paginate:10;
        $total = ServicePartner::where('is_default', 0)->count();
        
        $totalActive = ServicePartner::where('is_default', 0)->where('status', 1)->count();
        $totlInactive = ServicePartner::where('is_default', 0)->where('status', 0)->count();
        $data = ServicePartner::select('*')->with('pincodes');
        $totalResult = ServicePartner::select('id');
        if(!empty($status)){
            if($status == 'active'){
                $data = $data->where('status', 1);
                $totalResult = $totalResult->where('status', 1);
            } else if ($status == 'inactive'){
                $data = $data->where('status', 0);
                $totalResult = $totalResult->where('status', 0);
            }
        }
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('person_name', 'LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhereHas('pincodes', function ($q) use ($search) {
                    $q->where('number', 'LIKE','%'.$search.'%');
                });
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('person_name', 'LIKE','%'.$search.'%')->orWhere('company_name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhereHas('pincodes', function ($q) use ($search) {
                    $q->where('number', 'LIKE','%'.$search.'%');
                });
            });
        }

        if(!empty($type)){
            $data = $data->where('type', $type);
            $totalResult = $totalResult->where('type', $type);
        }
        
        $data = $data->where('is_default', 0)->orderBy('id','desc')->paginate($paginate);
        $totalResult = $totalResult->where('is_default', 0)->count();

        $data = $data->appends([
            'search'=>$search,
            'type' => $type,
            'status'=>$status,
            'page'=>$request->page,
            'paginate'=>$paginate
        ]);

        // dd($data);
        return view('servicepartner.list', compact('data','totalResult','total','totalActive','totlInactive','status','search','type','paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('servicepartner.add');
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
            'type' => 'required',
            'company_name' => 'required|max:100',
            'person_name' => 'required|max:100',
            'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:service_partners,email|nullable|required_without:phone',
            'phone' => 'numeric|digits_between:7,10|unique:service_partners,phone|nullable|required_without:email',
            // 'pan_no' => 'required',
            // 'aadhaar_no' => 'required',
            // 'gst_no' => 'required',
            // 'license_no' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            // 'salary' => 'nullable|required_unless:type,1',
            // 'repair_charge' => 'nullable|required_unless:type,1',
            // 'travelling_allowance' => 'nullable|required_unless:type,1',
        ],[
            // 'salary.required_unless' => 'The salary field is required',
            // 'repair_charge.required_unless' => 'The repair charge is required',
            // 'travelling_allowance.required_unless' => 'The travelling allowance field is required',
        ]);

        $params = $request->except('_token');

        if(!empty($params['image'])){
            $upload_path = "uploads/service-partner/";
            $image = $params['image'];
            $imageName = time() . "." . $image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $params['photo'] = $upload_path . $uploadedImage;
            unset($params['image']);
        } else {
            $params['photo'] = '';
        }


        
        $id = ServicePartner::insertGetId($params);
        if (!empty($id)) {
            Session::flash('message', 'Service Partner Created Successfully');
            return redirect()->route('service-partner.list');
        } else {
            return redirect()->route('service-partner.add')->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServicePartner  $servicePartner
     * @return \Illuminate\Http\Response
     */
    public function show($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = ServicePartner::findOrFail($id);
            return view('servicepartner.detail', compact('data','id','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServicePartner  $servicePartner
     * @return \Illuminate\Http\Response
     */
    public function edit($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $data = ServicePartner::findOrFail($id);
            return view('servicepartner.edit', compact('data','idStr','getQueryString'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServicePartner  $servicePartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $request->validate([
                // 'type' => 'required',
                'company_name' => 'required|max:100',
                'person_name' => 'required|max:100',
                'email' => 'regex:/(.+)@(.+)\.(.+)/i|max:100|unique:service_partners,email,'.$id.'|nullable|required_without:phone',
                'phone' => 'numeric|digits_between:7,10|unique:service_partners,phone,'.$id.'|nullable|required_without:email',
                // 'pan_no' => 'required',
                // 'aadhaar_no' => 'required',
                // 'gst_no' => 'required',
                // 'license_no' => 'required',
                'address' => 'required',
                'state' => 'required',
                'city' => 'required',
                // 'salary' => 'nullable|required_unless:type,1',
                // 'repair_charge' => 'nullable|required_unless:type,1',
                // 'travelling_allowance' => 'nullable|required_unless:type,1',
            ],[
                // 'salary.required_unless' => 'The salary field is required',
                // 'repair_charge.required_unless' => 'The repair charge is required',
                // 'travelling_allowance.required_unless' => 'The travelling allowance field is required',
            ]);
    
            $params = $request->except('_token');
    
            $service_partner = ServicePartner::find($id);
            
            // echo asset($service_partner->photo); die;
    
            if(!empty($params['image'])){
                // if (Storage::exists($service_partner->photo)) {
                //     die($service_partner->photo);
                //     unlink($service_partner->photo);
                // }
    
                File::delete($service_partner->photo);
    
                $upload_path = "uploads/service-partner/";
                $image = $params['image'];
                $imageName = time() . "." . $image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $params['photo'] = $upload_path . $uploadedImage;
                unset($params['image']);
                
                
    
            } else {
                $params['photo'] = $service_partner->photo;
            }
            
            $data = ServicePartner::where('id',$id)->update($params);
            if (!empty($data)) {
                Session::flash('message', 'Service Partner Updated Successfully');
                return redirect('/service-partner/list?'.$getQueryString);
                // return redirect()->route('service-partner.list');
            } else {
                return redirect()->route('service-partner.edit',$id)->withInput($request->all());
            }
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    /**
     * Change Status the specified resource from storage.
     *
     * @param  \App\Models\ServicePartner  $servicePartner
     * @return \Illuminate\Http\Response
     */
    public function toggle_status($idStr,$getQueryString='')
    {
        try {
            $id = Crypt::decrypt($idStr);
            $customer = ServicePartner::find($id);
            $message = "";
            if($customer->status == 1){
                ServicePartner::where('id',$id)->update(['status'=>0]);
                $message = "Service Partner deactivated successfully";
            } else {
                ServicePartner::where('id',$id)->update(['status'=>1]);
                $message = "Service Partner activated successfully";
            }

            Session::flash('message', $message);
            if(!empty($getQueryString)){            
                return redirect('/service-partner/list?'.$getQueryString);
            }
            return redirect()->route('service-partner.list');
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }
    /* PIN Code Master Setup */
    /*public function pincodes(Request $request)
    {
        # code...
        $search = !empty($request->search)?$request->search:'';
        $status = !empty($request->status)?$request->status:'';

        $total = Pincode::count();
        
        $totalActive = Pincode::where('status', 1)->count();
        $totlInactive = Pincode::where('status', 0)->count();
        $data = Pincode::select('*');
        $totalResult = Pincode::select('id');
        if(!empty($status)){
            if($status == 'active'){
                $data = $data->where('status', 1);
                $totalResult = $totalResult->where('status', 1);
            } else if ($status == 'inactive'){
                $data = $data->where('status', 0);
                $totalResult = $totalResult->where('status', 0);
            }
        }
        
        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('number', 'LIKE','%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('number', 'LIKE','%'.$search.'%');
            });
        }

        $data = $data->orderBy('id','desc')->paginate(10);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'status'=>$status,
            'page'=>$request->page
        ]);

        // $data = Pincode::paginate(10);
        // dd($data);
        return view('pincodes.list', compact('data','totalResult','total','totalActive','totlInactive','status','search'));
    }*/

    /*public function save_pincode(Request $request)
    {
        # add pincode...
        $request->validate([
            'number' => 'required|unique:pincodes,number'
        ]);

        $params = $request->except('_token');
        $id = Pincode::insertGetId($params);

        Session::flash('message', 'PIN Code Created Successfully');
        return redirect('/service-partner/list-pincode');
    }*/

    /*public function toggle_status_pincode($id,$getQueryString='')
    {
        $customer = Pincode::find($id);
        $message = "";
        if($customer->status == 1){
            Pincode::where('id',$id)->update(['status'=>0]);
            $message = "Pin Code deactivated successfully";
        } else {
            Pincode::where('id',$id)->update(['status'=>1]);
            $message = "Pin Code activated successfully";
        }

        Session::flash('message', $message);
        return redirect('/service-partner/list-pincode?'.$getQueryString);
    }*/

    /* Assign PIN Codes */
    /*public function view_pincodes($id,$getQueryString='')
    {
        # code...
        $pincodeArr = $mypincodeArr = array();
        $others_availed_pincodes = ServicePartnerPincode::where('service_partner_id', '!=',$id)->distinct()->get('pincode_id');
        // dd($others_availed_pincodes);
        foreach($others_availed_pincodes as $pin){
            $pincodeArr[] = $pin->pincode_id;
        }
        // dd($pincodeArr);
        $data = Pincode::where('status', 1);
        
        
        $data = $data->orderBy('number','asc')->get();

        $my_availed_pincodes = ServicePartnerPincode::where('service_partner_id',$id)->get();
        foreach($my_availed_pincodes as $pin){
            $mypincodeArr[] = $pin->pincode_id;
        }

        return view('servicepartner.pincodes', compact('id','data','getQueryString','pincodeArr','mypincodeArr'));
    }*/

    public function upload_pincode_csv($idStr,Request $request)
    {
        try {
            $id = Crypt::decrypt($idStr);
            $service_partner = ServicePartner::find($id);
            $service_partner_pincodes =ServicePartnerPincode::where('service_partner_id',$id)->get();
            return view('servicepartner.csvpin', compact('id','service_partner','service_partner_pincodes'));
        } catch ( DecryptException $e) {
            return abort(404);
        }
        
    }

    public function assign_pincode_csv(Request $request)
    {
        # csv for pincodes...
        $request->validate([
            'csv' => 'required'
        ]);
        $params = $request->except('_token');
        $csv = $params['csv'];
        $service_partner_id = $params['service_partner_id'];

        // dd($params);
        
        $rows = Excel::toArray([],$request->file('csv'));
        $data = $rows[0];        
        // $columns = $rows[0][0];       
        

        foreach($data as $item){
            $pincode = $item[0];
            $pincode_id = 0;
            // dd($pincode);
            $exist_pincode = Pincode::where('number',$pincode)->first();
            if(empty($exist_pincode)){
                $pincode_id = Pincode::insertGetId(['number'=>$pincode,'is_csv_uploaded'=>1]);
            }else{
                $pincode_id = $exist_pincode->id;                
            }
            // dd($pincode_id);
            $exist_service_partner_pincodes = ServicePartnerPincode::where('service_partner_id',$service_partner_id)->where('pincode_id',$pincode_id)->first();
            if(empty($exist_service_partner_pincodes)){
                ServicePartnerPincode::insert([
                    'service_partner_id' => $service_partner_id,
                    'pincode_id' => $pincode_id,
                    'number' => $pincode,
                    'is_from_csv' => 1
                ]);
            }else{
                ServicePartnerPincode::insert([
                    'service_partner_id' => $service_partner_id,
                    'pincode_id' => $pincode_id,
                    'number' => $pincode,
                    'is_from_csv' => 1
                ]);
            }
            
        }
        
        
        Session::flash('message', "Pin codes has been assigned to service partner successfully"); 
        return redirect()->route('service-partner.upload-pincode-csv',Crypt::encrypt($service_partner_id));        
    }

    public function view_duplicate_pincode_assignee(Request $request)
    {
        # view duplicate pincdoe assignee...
        $data = DB::select("SELECT number, COUNT(number) AS total_pincode, GROUP_CONCAT(service_partner_id) AS partn_ids, GROUP_CONCAT(id) AS service_partner_pincode_ids FROM service_partner_pincodes GROUP BY number HAVING COUNT(number) > 1");

        return view('servicepartner.duplicatecsvassignee', compact('data'));
    }

    public function remove_duplicate_pincode_assignee(Request $request)
    {
        # code...

        $request->validate([
            'service_partner_pincode_id' => 'required'
        ],[
            'service_partner_pincode_id.required' => 'Please choose at least one'
        ]);

        $service_partner_pincode_id_arr = !empty($request->service_partner_pincode_id)?$request->service_partner_pincode_id:array();
        if(!empty($service_partner_pincode_id_arr)){
            foreach($service_partner_pincode_id_arr as $id){
                ServicePartnerPincode::where('id',$id)->delete();
            }
        }

        Session::flash('message', 'Duplicate pincodes removed successfully');
        return redirect()->route('service-partner.list');        
    }

    public function pincodelist($service_partner_idStr,Request $request)
    {
        # code...
        try {
            $search = !empty($request->search)?$request->search:'';
            $paginate = 20;
            $service_partner_id = Crypt::decrypt($service_partner_idStr);
            $data = ServicePartnerPincode::where('service_partner_id',$service_partner_id);
            $totalResult = ServicePartnerPincode::where('service_partner_id',$service_partner_id);

            if(!empty($search)){
                $data = $data->where('number', 'LIKE', '%'.$search.'%');
                $totalResult = $totalResult->where('number', 'LIKE', '%'.$search.'%');
            }

            $data = $data->paginate($paginate);
            $totalResult = $totalResult->count();

            $data = $data->appends([
                'page' => $request->page,
                'search' => $search
            ]);
            $service_partner = ServicePartner::find($service_partner_id);
            return view('servicepartner.pincodelist', compact('data','totalResult','service_partner_id','service_partner','search','paginate'));
        } catch ( DecryptException $e) {
            return abort(404);
        }        
    }

    public function removepincdoebulk($service_partner_id,Request $request)
    {
        # remove pin codes...

        // dd($request->all());
        // dd($service_partner_id);

        $ids = !empty($request->ids)?$request->ids:array();

        if(!empty($ids)){
            foreach($ids as $id){
                ServicePartnerPincode::where('id',$id)->delete();
            }
        }

        Session::flash('message', "PIN Codes removed successfully");
        return redirect()->route('service-partner.pincodelist',Crypt::encrypt($service_partner_id));

    }

    public function removepincdoesingle($idStr,$service_partner_idStr,$getQueryString='')
    {
        # code...
        try {
            $id = Crypt::decrypt($idStr);
            $service_partner_id = Crypt::decrypt($service_partner_idStr);
            // echo $getQueryString; die;
            // echo 'id:- '.$id.'<br/>';
            // echo 'service_partner_id:- '.$service_partner_id.'<br/>'; 
            // die;
            ServicePartnerPincode::where('id',$id)->delete();
            Session::flash('message', "PIN Code removed successfully");
            // return redirect()->route('service-partner.pincodelist',$service_partner_idStr,$getQueryString);
            return redirect('/service-partner/pincodelist/'.$service_partner_idStr.'?'.$getQueryString);
        } catch ( DecryptException $e) {
            
        }
    }

    /* ORDER CSV NOTIFICATION */

    public function upload_csv_order(Request $request)
    {
        # code...
        $search = !empty($request->search)?$request->search:'';

        $data = ServiceNotificationCSV::with('service_partner');
        $totalResult = ServiceNotificationCSV::select('id');

        if(!empty($search)){
            $data = $data->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('csv_file_name', 'LIKE','%'.$search.'%')->orWhere(DB::raw("(DATE_FORMAT(created_at,'%d/%m/%Y'))"), "=", $search)->orWhere('mobile_no', 'LIKE', '%'.$search.'%')->orWhere('phone_no', 'LIKE', '%'.$search.'%');
            });
            $totalResult = $totalResult->where(function($query) use ($search){
                $query->where('pincode', 'LIKE','%'.$search.'%')->orWhere('bill_no', 'LIKE','%'.$search.'%')->orWhere('csv_file_name', 'LIKE','%'.$search.'%')->orWhere(DB::raw("(DATE_FORMAT(created_at,'%d/%m/%Y'))"), "=", $search)->orWhere('mobile_no', 'LIKE', '%'.$search.'%')->orWhere('phone_no', 'LIKE', '%'.$search.'%');
            });
        }

        $data = $data->orderBy('id','desc')->paginate(10);
        $totalResult = $totalResult->count();

        $data = $data->appends([
            'search'=>$search,
            'page'=>$request->page
        ]);
        // dd($data);
        return view('servicepartner.csvorder', compact('data','totalResult', 'search'));
    }

    public function assign_order_csv(Request $request)
    {
        # upload order csv for sending mail as per pincode...

        $request->validate([
            'csv' => 'required'
        ]);
        $params = $request->except('_token');
        $csv = $params['csv'];
        $csvFileName = $csv->getClientOriginalName();
        $csvFileExt = $csv->getClientOriginalExtension();
        
        $rows = Excel::toArray([],$request->file('csv'));
        // $settings = Settings::find(1);
        // $csv_to_email = $settings->csv_to_email;  /* CSV Master To Email */
        $default_service_partner = ServicePartner::find(1);
        $csv_to_email = $default_service_partner->email;
        
        // die;
        // dd($rows); 
        // echo $csv_to_email; 
        $data = $rows[0];
        /* Column validaton */
        $columns = $rows[0][0];        
        $myReqColumns = ['Sl.','Branch','Date','Bill Number','Product Name','Customer Name','Address','Pincode','District','Mobile No','Phone','Delivery Dt','Remarks','Brand','Class','Salesman','Salesman MobNo','Product Value','Product Srl No.'];
        $reqColumnErr = false;
        foreach($columns as $col){
            if(!in_array($col,$myReqColumns)){
                $reqColumnErr = true;
            }
        }
        if($reqColumnErr){
            return  redirect()->back()->withErrors(['csv'=> "Missing column in file"])->withInput();
        }

        // dd($data);
               
        foreach($data as $key => $item){            
            if($key != 0){
                $pincode = $item[7];
                $pincodes = Pincode::where('number',$pincode)->first();
                $mail_send = 0;
                $service_partner_id = 0;

                $exist_service_partner_csv = ServiceNotificationCSV::where('bill_no', $item[3])->first();

                if(empty($exist_service_partner_csv)){
                    if(!empty($pincodes)){
                        $pincode_id = $pincodes->id;
                        $getpartnerpincode = ServicePartnerPincode::with('service_partner')->where('pincode_id',$pincode_id)->orderBy('id','desc')->first();
                        // echo '<pre>'; print_r($getpartnerpincode);
                        if(!empty($getpartnerpincode)){
                            $email = $getpartnerpincode->service_partner->email; 
                            $name = $getpartnerpincode->service_partner->name; 
                            if(!empty($email)){
                                $mail_send = 1;
                                $service_partner_id = $getpartnerpincode->service_partner_id;
                                $notificationData = array(
                                    'csv_file_name' => $csvFileName,
                                    'service_partner_id' => $service_partner_id,
                                    'service_partner_email' => $email,
                                    'pincode' => $pincode,
                                    'mail_send' => $mail_send,
                                    'branch' => $item[1],
                                    'entry_date' => $item[2],
                                    'bill_no' => $item[3],
                                    'customer_name' => $item[5],
                                    'address' => $item[6],
                                    'district' => $item[8],
                                    'mobile_no' => $item[9],
                                    'phone_no' => $item[10],
                                    'delivery_date' => $item[11],
                                    'brand' => $item[13],
                                    'class' => $item[14],
                                    'salesman' => $item[15],
                                    'product_value' => $item[17],
                                    'product_sl_no' => $item[18],
                                    'product_name' => $item[4],
                                    'remarks' => $item[16],
                                );
                                ServiceNotificationCSV::insert($notificationData);
                                /* Mail Send Service Partner */
                                $mailData['email'] = $email;
                                $mailData['name'] = $name;
                                $mailData['subject'] = "KGA SERVICE NOTIFICATION";
                                $mailData['bill_no'] = $item[3];
                                $mailData['customer_name'] = $item[5];
                                $mailData['branch'] = $item[1];
                                $mailData['address'] = $item[6];
                                $mailData['district'] = $item[8];
                                $mailData['mobile_no'] = $item[9];
                                $mailData['phone_no'] = $item[10];
                                $mailData['delivery_date'] = $item[11];
                                $mailData['brand'] = $item[13];
                                $mailData['class'] = $item[14];
                                $mailData['salesman'] = $item[15];
                                $mailData['salesman_mobile_no'] = $item[16];
                                $mailData['product_value'] = $item[17];
                                $mailData['product_sl_no'] = $item[18];
                                $mailData['product_name'] = $item[4];
                                $mailData['pincode'] = $item[7];
                                $this->mailSendData($mailData);  
                            }                            
                        } else {
                            /* Mail Send Master */
                            $mailAdminData['email'] = $csv_to_email;
                            $mailAdminData['name'] = "KGA Admin";
                            $mailAdminData['subject'] = "KGA SERVICE NOTIFICATION";
                            $mailAdminData['bill_no'] = $item[3];
                            $mailAdminData['customer_name'] = $item[5];
                            $mailAdminData['branch'] = $item[1];
                            $mailAdminData['address'] = $item[6];
                            $mailAdminData['district'] = $item[8];
                            $mailAdminData['mobile_no'] = $item[9];
                            $mailAdminData['phone_no'] = $item[10];
                            $mailAdminData['delivery_date'] = $item[11];
                            $mailAdminData['brand'] = $item[13];
                            $mailAdminData['class'] = $item[14];
                            $mailAdminData['salesman'] = $item[15];
                            $mailAdminData['salesman_mobile_no'] = $item[16];
                            $mailAdminData['product_value'] = $item[17];
                            $mailAdminData['product_sl_no'] = $item[18];
                            $mailAdminData['product_name'] = $item[4];
                            $mailAdminData['pincode'] = $item[7];
                            $this->mailSendData($mailAdminData);
                            $notificationData = array(
                                'csv_file_name' => $csvFileName,
                                'service_partner_id' => 1,
                                'service_partner_email' => $csv_to_email,
                                'pincode' => $pincode,
                                'mail_send' => $mail_send,
                                'branch' => $item[1],
                                'entry_date' => $item[2],
                                'bill_no' => $item[3],
                                'customer_name' => $item[5],
                                'address' => $item[6],
                                'district' => $item[8],
                                'mobile_no' => $item[9],
                                'phone_no' => $item[10],
                                'delivery_date' => $item[11],
                                'brand' => $item[13],
                                'class' => $item[14],
                                'salesman' => $item[15],
                                'product_value' => $item[17],
                                'product_sl_no' => $item[18],
                                'product_name' => $item[4],
                                'remarks' => $item[16],
                            );
                            ServiceNotificationCSV::insert($notificationData);
                        }     
                    } else {
                        /* Mail Send Master */
                        $mailAdminData['email'] = $csv_to_email;
                        $mailAdminData['name'] = "KGA Admin";
                        $mailAdminData['subject'] = "KGA SERVICE NOTIFICATION";
                        $mailAdminData['bill_no'] = $item[3];
                        $mailAdminData['customer_name'] = $item[5];
                        $mailAdminData['branch'] = $item[1];
                        $mailAdminData['address'] = $item[6];
                        $mailAdminData['district'] = $item[8];
                        $mailAdminData['mobile_no'] = $item[9];
                        $mailAdminData['phone_no'] = $item[10];
                        $mailAdminData['delivery_date'] = $item[11];
                        $mailAdminData['brand'] = $item[13];
                        $mailAdminData['class'] = $item[14];
                        $mailAdminData['salesman'] = $item[15];
                        $mailAdminData['salesman_mobile_no'] = $item[16];
                        $mailAdminData['product_value'] = $item[17];
                        $mailAdminData['product_sl_no'] = $item[18];
                        $mailAdminData['product_name'] = $item[4];
                        $mailAdminData['pincode'] = $item[7];
                        $this->mailSendData($mailAdminData);
                        $notificationData = array(
                            'csv_file_name' => $csvFileName,
                            'service_partner_id' => 1,
                            'service_partner_email' => $csv_to_email,
                            'pincode' => $pincode,
                            'mail_send' => $mail_send,
                            'branch' => $item[1],
                            'entry_date' => $item[2],
                            'bill_no' => $item[3],
                            'customer_name' => $item[5],
                            'address' => $item[6],
                            'district' => $item[8],
                            'mobile_no' => $item[9],
                            'phone_no' => $item[10],
                            'delivery_date' => $item[11],
                            'brand' => $item[13],
                            'class' => $item[14],
                            'salesman' => $item[15],                            
                            'product_value' => $item[17],
                            'product_sl_no' => $item[18],
                            'product_name' => $item[4],
                            'remarks' => $item[16],
                        );
                        ServiceNotificationCSV::insert($notificationData);
    
                    }
                }
            }                        
        }
        // dd($data);
        // echo '<pre>'; print_r($data); die;
        Session::flash('message', "Service Partner notified successfully from csv");
        return redirect()->route('service-partner.upload-csv-order');
        
    }

    private function mailSendData($data)
    {
        # mail send data...
        $mailData['email'] = $data['email'];
        $mailData['name'] = $data['name'];
        $mailData['subject'] = $data['subject'];
        $mailBody = "";
        
        $mailBody .= "<h1>Hi, ".$data['name']."!</h1> <br/>";
        $mailBody .= "<p>You have a new notification for servicing new goods.<p>";
        $mailBody .= "Please find the details below , <br/>";
        
        
        $mailBody .= "
        <table cellspacing='0' cellpadding='0' style='border: 1px solid #ddd;'>
            <thead>
                <tr>
                    <th style='padding:5px; border: 1px solid #ddd;'>Order Detail</th>
                    <th style='padding:5px; border: 1px solid #ddd;'>Product Detail</th>
                    <th style='padding:5px; border: 1px solid #ddd;'>Customer Detail</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Bill No: <strong>".$data['bill_no']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Product Sl No: <strong>".$data['product_sl_no']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer Name: <strong>".$data['customer_name']."</strong> </td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Delivery Date:<strong>".$data['delivery_date']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Product Name: <strong>".$data['product_name']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Address: <strong>".$data['address']." </strong></td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>Branch: <strong>".$data['branch']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Brand: <strong>".$data['brand']."</strong> </td>
                    <td style='padding:5px; border: 1px solid #ddd;'>District: <strong>".$data['district']."<strong></strong></td>
                </tr>
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Class: <strong>".$data['class']."</strong></td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Customer PIN Code: <strong>".$data['pincode']."</strong></td>
                </tr>            
                <tr>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>&nbsp;</td>
                    <td style='padding:5px; border: 1px solid #ddd;'>Contact Number: <strong>".$data['mobile_no']." / ".$data['phone_no']."</strong></td>
                </tr>
            </tbody>
        </table>
        ";


        $mailData['body'] = $mailBody;
        $mail = sendMail($mailData);
        if($mail) {
            $details = json_encode($data);
            DB::table('mail_send')->insert(['email' => $data['email'] ,'bill_no' =>  $data['bill_no'] , 'details' => $details ]);        
        }

    }


    
}
