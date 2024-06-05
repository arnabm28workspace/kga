@extends('layouts.app')
@section('content')
@section('page', 'Stock Logs')
<section>
    <ul class="breadcrumb_menu"> 
        <li><a href="{{ route('stock.list') }}">Stock</a></li>
        <li><a href="{{ route('product.show', Crypt::encrypt($product_id)) }}?backtomodule=stock_logs&backtodestination={{Request::fullUrl()}}" title="{{$product->title}}">{{$product->unique_id}}</a> </li>
    </ul>
    <ul class="pincodeclass">
        <li>
            <a href="{{ route('product.show', Crypt::encrypt($product_id)) }}?backtomodule=stock_logs&backtodestination={{Request::fullUrl()}}">{{ $product->title }}</a>
        </li>
        <li>
            >
        </li>
        <li>
            <span>{{$count}}</span>
        </li>
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
                        
                    </div>
                    <div class="col-auto">
                        
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

            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>     
                <th>Date</th>    
                <th>In / Out</th>
                <th>Quantity</th>
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
                <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                <td>
                    @if ($item->type == 'in')
                        <span class="badge bg-success">{{ ucwords($item->type) }}</span>
                    @else
                        <span class="badge bg-danger">{{ ucwords($item->type) }}</span>
                    @endif
                </td>
                <td>
                    @if ($item->type == 'in')
                        <span class="">{{ $item->quantity }} pcs</span>
                    @else
                        <span class="">{{ $item->quantity }} pcs</span>
                    @endif
                    {{-- {{$item->type}} --}}
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