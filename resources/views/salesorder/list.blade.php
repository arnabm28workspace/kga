@extends('layouts.app')
@section('content')
@section('page', 'Order')
<section>
    <ul class="breadcrumb_menu">    
        <li>Order Management</li>    
        <li>Orders</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(empty($status)) class="active" @endif>
                        <a href="{{ route('sales-order.list') }}">All <span class="count">({{$countAll}})</span> </a>
                    </li>
                    <li @if($status == 'pending') class="active" @endif>
                        <a href="{{ route('sales-order.list', ['status'=>'pending']) }}">Pending <span class="count">({{$countPending}})</span> </a>
                    </li>
                    <li @if($status == 'completed') class="active" @endif>
                        <a href="{{ route('sales-order.list', ['status'=>'completed']) }}">Completed <span class="count">({{$countCompleted}})</span> </a>
                    </li>
                    <li @if($status == 'cancelled') class="active" @endif>
                        <a href="{{ route('sales-order.list', ['status'=>'cancelled']) }}">Cancelled <span class="count">({{$countCancelled}})</span> </a>
                    </li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{route('sales-order.add')}}" class="btn btn-outline-primary select-md">Add New</a>  
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                    <input type="hidden" name="status" value="{{$status}}">
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
                <th>ORDER NO <br/> / Created At</th>    
                <th>Customer</th>  
                <th>Type</th>
                <th>Items</th>
                <th>Amount</th>       
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            {{-- {{Request::getRequestUri()}} --}}
            {{-- {{Request::fullUrl()}} --}}
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
        @endphp
            <tr>
                <td>{{$i}}</td>
                <td>
                    <strong>{{$item->order_no}}</strong>  <br/>
                    {{date('d/m/Y', strtotime($item->created_at))}}                   
                    
                </td>
                <td>
                    <p class="small text-muted mb-1">
                        <span> Name: {{$item->customer->name}} 
                        </span> <br>
                        <span>
                            @if(!empty($item->customer->phone)) ({{$item->customer->phone}}) @endif
                        </span> <br> 
                        <span>
                            @if(!empty($item->customer->email)) ({{$item->customer->email}}) @endif
                        </span> <br> 
                        
                    </p>
                    
                </td>
                <td>
                    @if ($item->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td>
                    <ul class="pincodeclass">
                        @foreach ($details as $detail)
                        <li><a href="{{ route('product.show',Crypt::encrypt($detail->product_id)) }}?backtomodule=order&backtodestination={{Request::fullUrl()}}">{{$detail->product}}</a>  <span> | {{$detail->quantity}} pcs</span></li>  
                        @endforeach                        
                    </ul>
                </td>
                <td>
                    Rs. {{ number_format((float)$item->order_amount, 2, '.', '') }}
                </td>
                <td>
                    @if($item->status == 'cancelled')
                    <span class="badge bg-danger">{{ ucwords($item->status) }}</span>            
                    @elseif($item->status == 'pending')
                    <span class="badge bg-warning">{{ ucwords($item->status) }}</span>            
                    @elseif($item->status == 'completed')
                    <span class="badge bg-success">{{ ucwords($item->status) }}</span>            
                    @endif
                            
                </td>
                <td>
                    @if ($item->status == 'pending')
                    <a href="{{ route('sales-order.generate-packing-slip', [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-success select-md">Generate Packing Slip</a>
                    <a href="{{route('sales-order.cancel', [Crypt::encrypt($item->id),Request::getQueryString()] )}}" onclick="return confirm('Are you sure want to cancel the order?');" class="btn btn-outline-danger select-md">Cancel Order</a>
                    @endif    
                    <a href="{{ route('sales-order.show',  [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Details</a> 
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
    $('#status').on('change', function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 