@extends('layouts.app')
@section('content')
@section('page', 'Details')
<section>
    <ul class="breadcrumb_menu"> 
        <li><a href="{{ route('sales-order.list') }}">Sales Order</a> </li>
        <li>
            {{$order->order_no}}
            @if($order->status == 'cancelled')
            <span class="badge bg-danger">{{ ucwords($order->status) }}</span>            
            @elseif($order->status == 'pending')
            <span class="badge bg-warning">{{ ucwords($order->status) }}</span>            
            @elseif($order->status == 'completed')
            <span class="badge bg-success">{{ ucwords($order->status) }}</span>            
            @endif
        </li>
    </ul>   
    @if (!empty(Request::get('backtomodule')))
    <ul class="breadcrumb_menu">   
            {{-- {{ Request::get('backtodestination') }}  --}}
        <li><a href="{{Request::get('backtodestination')}}">
            <i class="fi fi-br-arrow-alt-circle-left"></i>
            Back To {{ str_replace("_"," ",ucwords(Request::get('backtomodule'))) }}
        </a></li>               
    </ul>
    @endif  
    <div class="row">
        <div class="col-sm-6">
            <h5>Order Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order No : {{$order->order_no}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Date : {{ date('d/m/Y h:i a', strtotime($order->created_at)) }} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Order Amount : Rs. {{ number_format((float)$order->order_amount, 2, '.', '') }} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>  
        <div class="col-sm-6">
            <h5>Customer Details</h5>
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Person Name : {{$order->customer->name}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Email : {{$order->customer->email}} </span> </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Phone : {{$order->customer->phone}} </span> </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>   
    </div>
    <div class="row">
        <div class="col-md-12">
            <h5>Item Details</h5>
            <table class="table" id="timePriceTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Tax(%)</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i=1;
                    @endphp
                    @forelse ($data as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td>
                            <a href="{{ route('product.show', Crypt::encrypt($item->product_id)) }}" class="showdetails">
                                {{ $item->product->unique_id }} | {{ $item->product->title }}
                            </a>
                        </td>
                        <td>
                            @if ($item->product->type == 'fg')
                                <span class="badge bg-dark">Finished Goods</span>
                            @else
                                <span class="badge bg-dark">Spare Parts</span>
                            @endif
                        </td>
                        <td>{{$item->quantity}} pcs</td>
                        <td>Rs. {{ number_format((float)$item->product_price, 2, '.', '') }}</td>
                        <td>{{$item->tax}}</td>
                        <td>Rs. {{ number_format((float)$item->product_total_price, 2, '.', '') }}</td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                    @empty
                        
                    @endforelse
                   
                </tbody>
            </table>
        </div>
    </div>

</section>
@endsection