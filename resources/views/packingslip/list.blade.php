@extends('layouts.app')
@section('content')
@section('page', 'Packing Slip')
<section>
    <ul class="breadcrumb_menu">    
        <li>Order Management</li>    
        <li>Packing Slips</li>
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
                            <option value="fg" @if($type == 'fg') selected @endif>Finished Goods</option>
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
                <th>SLIP NO</th>
                <th>ORDER NO</th>
                <th>Items</th>
                <th>Item Type</th>
                <th>Goods Out Process</th>
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
                $details = json_decode($item->details);
                // echo $item->sales_order->type;
            @endphp
            <tr>
                <td>{{$i}}</td>
                <td> {{date('d/m/Y', strtotime($item->created_at))}} </td>
                <td>
                    {{$item->slipno}}
                </td>
                <td>
                    <a class="showdetails" href="{{ route('sales-order.show', Crypt::encrypt($item->sales_order_id)) }}?backtomodule=packing_slip&backtodestination={{Request::fullUrl()}}">{{$item->sales_order->order_no}}</a>
                </td>
                <td>
                    <ul class="pincodeclass">
                        @foreach ($details as $detail)
                        <li>
                            <a href="{{ route('product.show', Crypt::encrypt($detail->product_id)) }}?backtomodule=packing_slip&backtodestination={{Request::fullUrl()}}">{{$detail->product_title}}</a>
                            <span> | {{$detail->quantity}} pcs</span>
                        </li> 
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
                    <span class="badge bg-success">{{ ucwords($item->goods_out_type) }}</span>
                </td>
                <td>
                    <a href="{{ route('packingslip.download', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Download</a>
                    @if (empty($item->invoice_no))

                        @if (empty($item->is_goods_out))
                        <a href="{{ route('packingslip.goods-scan-out', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-danger select-md">Goods Out</a>
                        @else
                        <a href="{{ route('packingslip.raise-invoice', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Raise Invoice</a>
                        @endif                        
                    
                    @endif
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