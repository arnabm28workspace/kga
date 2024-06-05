@extends('layouts.app')
@section('content')
@section('page', 'Customer')
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('customer.list')}}">All <span class="count">({{$total}})</span></a></li>
                    <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('customer.list',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                    <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('customer.list',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{route('customer.add')}}" class="btn btn-outline-primary select-md">Add New</a>              
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <input type="hidden" name="status" value="{{$status}}">
                <div class="row g-3 align-items-center">
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
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>PAN</th>
                <th>GST No</th>
                <th>License No</th>
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
                <td>{{date('d/m/Y', strtotime($item->created_at))}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->email}}</td>
                <td>{{$item->phone}}</td>
                <td>{{$item->address}}</td>
                <td>{{$item->pan_no}}</td>
                <td>{{$item->gst_no}}</td>
                <td>{{$item->license_no}}</td>
                <td>
                    @if(!empty($item->status))
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('customer.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('customer.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">View</a>
                    @if(!empty($item->status))
                    <a href="{{route('customer.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('customer.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
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
</script>  
@endsection 