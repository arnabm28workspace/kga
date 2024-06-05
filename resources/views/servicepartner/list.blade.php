@extends('layouts.app')
@section('content')
@section('page', 'Service Partner')
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('service-partner.list')}}">All <span class="count">({{$total}})</span></a></li>
                    <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('service-partner.list',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                    <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('service-partner.list',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{ route('service-partner.view-duplicate-pincode-assignee') }}" class="btn btn-outline-danger select-md">Dupliate PINCODE</a>              
            </div>
            <div class="col-auto">
                <a href="{{route('service-partner.add')}}" class="btn btn-outline-primary select-md">Add New</a>              
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <input type="hidden" name="status" value="{{$status}}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <select name="type" class="form-control select-md" id="type">
                            <option value="">All Types</option>
                            <option value="1" @if($type == 1) selected @endif>24*7</option>
                            <option value="2" @if($type == 2) selected @endif>Inhouse Technician</option>
                            <option value="3" @if($type == 3) selected @endif>Local Vendors</option>
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
                <th>Contact Details</th>
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
                <td>
                    {{$i}}
                </td>
                <td>
                    {{date('d/m/Y', strtotime($item->created_at))}}                    
                </td>
                <td>
                    <p class="small text-muted mb-1">
                        @if (!empty($item->company_name))
                        <span>Company Name: <strong>{{$item->company_name}}</strong></span> <br/>
                        @endif
                        @if (!empty($item->person_name))
                        <span>Person Name: <strong>{{$item->person_name}}</strong></span> <br/>
                        @endif                        
                    </p>
                </td>
                <td>
                    <p class="small text-muted mb-1">
                        @if (!empty($item->email))
                        <span>Email: <strong>{{$item->email}}</strong></span> <br/>
                        @endif
                        @if (!empty($item->phone))
                        <span>Phone: <strong>{{$item->phone}}</strong></span> <br/>
                        @endif                        
                    </p>                    
                </td> 
                <td>
                    @if($item->type == 1)
                    <strong >24 * 7</strong>
                    @elseif ($item->type == 2)
                    <strong >Inhouse Technician</strong>
                    @elseif ($item->type == 3)
                    <strong >Local Vendors</strong>
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
                    <a href="{{route('service-partner.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('service-partner.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">View</a>
                    <a href="{{ route('service-partner.upload-pincode-csv', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Assign PIN Code</a>
                    <a href="{{ route('service-partner.pincodelist', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">PIN Code List</a>
                    @if(!empty($item->status))
                    <a href="{{route('service-partner.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                    <a href="{{route('service-partner.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
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