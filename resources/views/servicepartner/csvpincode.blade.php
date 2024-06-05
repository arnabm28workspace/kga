@extends('layouts.app')
@section('content')
@section('page', 'CSV for Mail Send')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>CSV for Mail Send</li>
    </ul>
    <div class="row">
        <div class="col">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
        </div>
        <form id="myForm" action="{{ route('service-partner.send-mail-pincode') }}" enctype="multipart/form-data" method="POST">
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
                        <a href="{{route('service-partner.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Submit </button>
                    </div>
                </div>                                       
            </div> 
            
        </div>                 
        </form>             
    </div>  
    <div class="row">
        <table class="table">
            <thead>
                <tr>
                    <th>CSV File</th>
                    <th>Date</th>
                    <th>Service Partner</th>   
                    <th>Pincode</th>  
                    <th>Order Detail</th>
                    <th>Product Detail</th>    
                    <th>Customer Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                <tr>
                    <td>{{$item->csv_file_name}}</td>
                    <td>{{$item->entry_date}}</td>
                    <td>{{$item->service_partner->name}} ({{$item->service_partner->email}})</td>
                    <td>{{$item->pincode}}</td>
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
                            <span>Product Value: <strong>{{$item->product_value}}</strong></span> <br/>
                            <span>Brand: <strong>{{$item->brand}}</strong></span> <br/>
                            <span>Class: <strong>{{$item->class}}</strong></span> <br/>
                        </p>
                    </td>
                    <td>
                        <p class="small text-muted mb-1">
                            <span>Customer Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                            <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                            <span>District: <strong>{{$item->district}}</strong></span> <br/>
                        </p>
                    </td>
                </tr>
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
    })
</script>
@endsection