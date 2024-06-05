@extends('layouts.app')
@section('content')
@section('page', 'Upload Order CSV')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>Upload Order CSV</li>
    </ul>
    <div class="row">
        <div class="col">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
        </div>
        <form id="myForm" action="{{ route('service-partner.assign-order-csv') }}" enctype="multipart/form-data" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-12">
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
                        <a href="{{ url('/samplecsv/order/sample-order.csv') }}" class="btn btn-outline-primary btn-sm">Download Sample CSV</a>
                        <a href="{{route('service-partner.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Submit </button>
                    </div>
                </div>                                       
            </div> 
            
        </div>                 
        </form>             
    </div>  
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <h5>List Notification</h5>
            </div>
            <div class="col-auto">
                <div class="row g-3 align-items-center">
                    <form action="" id="searchForm">
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control" placeholder="Search ..">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <div class="row">        
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>CSV File</th>
                    <th>Service Partner</th>   
                    <th>Pincode</th>  
                    <th>Order Detail</th>
                    <th>Product Detail</th>    
                    <th>Customer Detail</th>
                </tr>
            </thead>
            <tbody>
                @php
                    if(empty(Request::get('page')) || Request::get('page') == 1){
                        $i=1;
                    } else {
                        $i = (((Request::get('page')-1)*10)+1);
                    } 
                @endphp
                @forelse ($data as $item)
                <tr>
                    <td>{{$i}}</td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>File Name: <strong>{{$item->csv_file_name}} </strong></span> <br/>
                            <span>Uploaded At: <strong>{{ date('d/m/Y', strtotime($item->created_at)) }} </strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        @if ($item->service_partner_id == 1)
                            <span class="badge bg-secondary">{{$item->service_partner_email}}</span> 
                            <span class="badge bg-info">Default</span> <br/>
                            <span class="badge bg-danger">No pincode assigned</span>
                        @else
                        <p class="small text-muted mb-1">
                            @if (!empty($item->service_partner->company_name))
                            <span>Company Name: <strong>{{$item->service_partner->company_name}} </strong></span> <br/>
                            @endif
                            @if (!empty($item->service_partner->person_name))
                            <span>Person Name: <strong>{{$item->service_partner->person_name}} </strong></span> <br/>
                            @endif
                            @if (!empty($item->service_partner->email))
                            <span>Email: <strong>{{$item->service_partner_email}}</strong></span> <br/>
                            @endif                            
                        </p>   
                        @endif
                                             
                    </td>
                    <td>
                        {{$item->pincode}}
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                            <span>Delivery Date: <strong>{{$item->delivery_date}}</strong></span> <br/>
                            <span>Salesman: <strong>{{$item->delivery_date}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Product Sl No: <strong>{{$item->product_sl_no}}</strong></span> <br/>
                            <span>Product Name: <strong>{{$item->product_name}}</strong></span> <br/>
                            <span>Brand: <strong>{{$item->brand}}</strong></span> <br/>
                            <span>Class: <strong>{{$item->class}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Customer Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                            <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                            <span>District: <strong>{{$item->district}}</strong></span> <br/>
                            <span>Mobile No: <strong>{{$item->mobile_no}}</strong></span> <br/>
                            <span>Phone No: <strong>{{$item->phone_no}}</strong></span>
                        </p>
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
                @empty
                <tr>
                    <td colspan="">No record found</td>
                </tr>  
                @endforelse
                
            
            </tbody>
        </table>
        {{$data->links()}}
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
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
</script>
@endsection