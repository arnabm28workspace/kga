@extends('layouts.app')
@section('content')
@section('page', 'Product')
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('product.list')}}">All <span class="count">({{$total}})</span></a></li>
                    <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('product.list',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                    <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('product.list',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{route('product.add')}}" class="btn btn-outline-primary select-md">Add New</a>              
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
                <th width="10%">ID</th>
                <th>Created At</th>
                <th>Item Name</th>  
                <th>Category > Subcategory</th>     
                <th>MOP</th>
                <th>Type</th>        
                <th>Status</th>
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
            <tr>
                <td>{{$i}}</td>
                <td>
                    {{$item->unique_id}}
                </td>
                <td> {{date('d/m/Y', strtotime($item->created_at))}} </td>
                <td>
                    <span>{{$item->title}}</span>                    
                </td>
                <td>
                    <ul class="pincodeclass">
                        <li>
                            <a href="{{ route('category.show', Crypt::encrypt($item->cat_id)) }}">{{$item->category->name}}</a>
                        </li>
                        <li>  >  </li>
                        <li>
                            <a href="{{ route('category.show', Crypt::encrypt($item->subcat_id)) }}">{{$item->subcategory->name}}</a>
                        </li>
                    </ul>
                </td>
                <td>
                    Rs. {{ number_format((float)$item->mop, 2, '.', '') }}
                </td>
                <td>
                    @if ($item->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td>
                    @if(!empty($item->status))
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('product.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('product.copy', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Copy</a>
                    <a href="{{route('product.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">View</a>
                    @if(!empty($item->status))
                    <a href="{{route('product.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('product.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
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