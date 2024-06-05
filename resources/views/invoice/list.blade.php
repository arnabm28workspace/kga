@extends('layouts.app')
@section('content')
@section('page', 'Invoice')
<section>
    <ul class="breadcrumb_menu">    
        <li>Order Management</li>
        <li>Invoices</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                             
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">  
                    <div class="col-auto">
                        <select name="type" class="form-control select-md" id="type">
                            <option value="">All Types</option>
                            <option value="fg" @if($type == 'fg') selected @endif>Finshed Goods</option>
                            <option value="sp" @if($type == 'sp') selected @endif>Spare Parts</option>
                        </select>
                    </div>                  
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                    {{ Session::forget('message') }}
                </div>
                @endif
            </div>
            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Created At</th>
                <th>Invoice No</th>
                <th>Slip No</th>
                <th>Order No</th>
                <th>Amount</th>
                <th>Items Details</th>
                <th>Item Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @php
            if(empty(Request::get('page')) || Request::get('page') == 1){
                $i=1;
            } else {
                $i = (((Request::get('page')-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)
            @php
                $item_details = json_decode($item->item_details);
            @endphp
            <tr>
                <td>{{$i}}</td>
                <td>{{date('d/m/Y', strtotime($item->created_at))}}</td>
                <td>{{$item->invoice_no}}</td>
                <td>{{$item->packingslip->slipno}}</td>
                <td>
                    <a class="showdetails" href="{{ route('sales-order.show', Crypt::encrypt($item->sales_order_id)) }}?backtomodule=invoice&backtodestination={{Request::fullUrl()}}">{{$item->sales_order->order_no}}</a>
                </td>
                <td>Rs. {{ number_format((float)$item->total_amount, 2, '.', '') }}</td>
                <td>
                    <ul class="pincodeclass">
                        @foreach ($item_details as $items)
                        <li><a href="{{ route('product.show', Crypt::encrypt($items->product_id)) }}?backtomodule=invoice&backtodestination={{Request::fullUrl()}}">{{ $items->product_title }}</a></li>
                        @endforeach
                    </ul>                    
                </td>
                <td>
                    @if ($item->sales_order->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('invoice.download', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Download</a>
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td>
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
    {{$data->links()}}
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 