@extends('layouts.app')
@section('content')
@section('page', 'Upload Pincode CSV')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>Upload Pincode CSV</li>
    </ul>
    <div class="row">
        <div class="col">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
        </div>
        <form id="myForm" action="{{ route('service-partner.assign-pincode-csv') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="service_partner_id" value="{{$id}}">
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-12">
                            @if (!empty($service_partner->status))
                            <p><span class="badge bg-success">Active</span></p>
                            @else
                            <p><span class="badge bg-danger">Inactive</span></p>
                            @endif
                            <p><span>Person:- <strong>{{$service_partner->person_name}}</strong> </span></p>
                            <p><span>Company:- <strong>{{$service_partner->company_name}}</strong> </span></p>
                            <p><span>Email:- <strong>{{$service_partner->email}}</strong> </span></p>
                            <p><span>Phone:- <strong>{{$service_partner->phone}}</strong> </span></p>
                            <div class="form-group">
                                <label for="">Upload CSV <span class="text-danger">*</span></label>
                                <input type="file" name="csv" 
                                accept=".csv" 
                                class="form-control" id="">
                                @error('csv') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                  
                    </div>  
                </div>    
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ url('/samplecsv/pincode/sample-pincode.csv') }}" class="btn btn-outline-primary btn-sm">Download Sample CSV</a>
                        <a href="{{route('service-partner.list')}}" class="btn btn-sm btn-danger">Back</a>
                        @php
                            $disabled = "";
                            if(empty($service_partner->status)){
                                $disabled = "disabled";
                            }
                        @endphp
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success" {{$disabled}}>Submit </button>
                    </div>
                </div>                                       
            </div> 
            
        </div>                 
        </form>   
        <div>
            <h6>Assigned Pincodes</h6>
            <ul class="pincodeclass">
                @forelse ($service_partner_pincodes as $pincode)
                <li>{{$pincode->number}}</li>
                @empty
                    
                @endforelse
                
            </ul>
            <a href="{{ route('service-partner.pincodelist', Crypt::encrypt($id)) }}" class="btn btn-outline-danger select-md">Remove Pincodes</a>
        </div>          
    </div>  
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');    
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        return true;
    });
</script>
@endsection