@extends('layouts.app')
@section('content')
@if ($type == 'parent')
    @section('page', 'Category')
@else
    @section('page', 'Sub Category')
@endif

<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                <a href="{{route('category.add',['type'=>$type])}}" class="btn btn-outline-primary select-md">Add New</a>              
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <input type="hidden" name="status" value="{{$status}}">
                <div class="row g-3 align-items-center">
                    
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
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
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th class="text-center"><i class="fi fi-br-picture"></i></th>
                <th>Created At</th>
                <th>Name</th>                
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
                <td class="text-center column-thumb">
                    @if (!empty($item->image))                        
                    <img src="{{ asset($item->image) }}">
                    @else                        
                    <img src="{{url('assets')}}/images/placeholder-image.jpg">
                    @endif
                </td>
                <td> {{date('d/m/Y', strtotime($item->created_at))}} </td>
                <td>
                    <ul class="pincodeclass">
                        @if (!empty($item->parent_id))
                        <li><a href="{{ route('category.show', Crypt::encrypt($item->parent_id)) }}?type=parent">{{$item->child->name}}</a></li>
                        <li> > </li>
                        <li><a href="{{ route('category.show', Crypt::encrypt($item->id)) }}?type=child">{{$item->name}}</a></li>
                        @else
                        <li><a href="{{ route('category.show', Crypt::encrypt($item->id)) }}?type=child">{{$item->name}}</a></li>
                        @endif
                        
                    </ul>
                    
                </td>
                <td>
                    @if(!empty($item->status))
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('category.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}?type={{$type}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('category.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}?type={{$type}}" class="btn btn-outline-primary select-md">View</a>
                    @if(!empty($item->status))
                    <a href="{{route('category.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('category.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
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