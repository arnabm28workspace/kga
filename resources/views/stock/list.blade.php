@extends('layouts.app')
@section('content')
@section('page', 'Stock')
<section>
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

            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>                
                <th>Product</th>    
                <th>Type</th>    
                <th>Quantity</th>
                <th>View Logs</th>
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
            <tr>
                <td>{{$i}}</td>
                <td>
                    {{$item->product->unique_id}} | {{$item->product->title }}                    
                </td>
                <td>
                    @if ($item->product->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td> 
                    <span class="showdetails">{{ $item->quantity }}</span>
                </td>
                <td>
                    <a href="{{ route('stock.logs', [Crypt::encrypt($item->product_id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">View</a>
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