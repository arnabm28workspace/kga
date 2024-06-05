@extends('layouts.app')
@section('content')
@section('page', strtoupper($po_type))
<section>
    <ul class="breadcrumb_menu">    
        <li>Purchase Order</li>    
        <li><a href="{{ route('purchase-order.list', ['po_type'=>$po_type]) }}">{{strtoupper($po_type)}}</a> </li>        
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                @if ($po_type == 'po')
                <a href="{{route('purchase-order.add')}}" class="btn btn-outline-primary select-md">Add New</a>        
                @endif
                      
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                    <input type="hidden" name="po_type" value="{{$po_type}}">
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
                @if ($po_type == 'po')
                <th>ORDER NO <br/> / Created At </th>
                @else
                <th>GRN NO <br/> / Created At </th>
                @endif                
                <th>Supplier</th>  
                <th>Items</th>
                <th>Type</th>  
                <th>Amount</th>       
                <th>Status</th>
                @if ($po_type == 'grn')
                <th>Stock In Type</th>
                @endif
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
            // dd($details);
        @endphp
            <tr>
                <td>{{$i}}</td>
                <td>
                    @if ($po_type == 'po')
                        <strong>{{$item->order_no}}</strong> <br/>
                        {{date('d/m/Y h:i A', strtotime($item->created_at))}} 
                    @else
                        <strong>{{$item->stock->grn_no}}</strong> <br/>
                        {{date('d/m/Y h:i A', strtotime($item->updated_at))}} 
                    @endif
                </td>                
                <td>
                    <span>{{$item->supplier->name}} ({{$item->supplier->phone}})  </span>
                </td>
                <td>
                    <ul class="pincodeclass">
                        @foreach ($details as $detail)
                        @php
                            
                            if($item->type == 'sp'){
                                $pack_of = $detail->pack_of;
                                $quantity_in_pack = $detail->quantity_in_pack;
                                $quantity_span = " | ".$quantity_in_pack." pcs in ".$pack_of." pack  ";
                            } else {
                                $quantity_span = " | ".$detail->quantity." pcs ";
                            }
                        @endphp
                        <li>
                            @if ($po_type == 'po')
                            <a href="{{ route('product.show',Crypt::encrypt($detail->product_id)) }}?backtomodule=PO&backtodestination={{route('purchase-order.list',['po_type'=>'po'])}}">{{$detail->product}}</a>  {{$quantity_span}}
                            @else
                            <a href="{{ route('product.show',Crypt::encrypt($detail->product_id)) }}?backtomodule=GRN&backtodestination={{ route('purchase-order.list',['po_type'=>'grn']) }}">{{$detail->product}}</a>  {{$quantity_span}}
                            @endif
                            
                        </li>  
                        @endforeach                        
                    </ul>
                </td>
                <td>
                    @if ($item->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td>
                    Rs. {{ number_format((float)$item->amount, 2, '.', '') }}
                </td>
                <td>
                    @if($item->status == 1)
                        <span class="badge bg-warning">Pending</span>
                    @elseif ($item->status == 2)
                        <span class="badge bg-success">Received</span>
                    @elseif ($item->status == 3)
                        <span class="badge bg-danger">Cancelled</span>
                    @endif
                </td>
                @if ($po_type == 'grn')
                <td>
                    <span class="badge bg-success">{{ ucwords($item->goods_in_type) }}</span>
                </td>
                @endif
                <td>                    
                    @if ($item->status == 1)
                        <a href="{{ route('purchase-order.make-grn', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Generate GRN</a>
                        <a href="{{route('purchase-order.cancel', [Crypt::encrypt($item->id),Request::getQueryString()] )}}" onclick="return confirm('Are you sure want to cancel the order?');" class="btn btn-outline-danger select-md">Cancel</a>
                    @endif
                    <a href="{{ route('purchase-order.show', [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Details</a>
                    @if ($item->status != 3)
                        <a href="{{ route('purchase-order.download', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Barcodes</a>
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