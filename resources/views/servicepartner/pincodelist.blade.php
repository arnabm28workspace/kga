@extends('layouts.app')
@section('content')
@section('page', 'PIN Code List')
<section>
    <ul class="breadcrumb_menu">
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a></li>
        <li>
            <a href="{{ route('service-partner.show', Crypt::encrypt($service_partner_id)) }}">
                {{$service_partner->person_name}} | {{$service_partner->company_name}}
            </a>
        </li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                            
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
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control" placeholder="Search PIN Code ">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <form action="{{ route('service-partner.removepincdoebulk',$service_partner_id) }}" method="POST">
        @csrf
        <div class="filter">
            <div class="row align-items-center justify-content-between">
                <div class="col">
                    @if (Session::has('message'))
                    <div class="alert alert-success" role="alert">
                        {{ Session::get('message') }}
                    </div>
                    @endif
                    <input type="submit" value="Remove" class="btn btn-outline-danger select-md" id="btnSuspend" onclick="return confirm('Are you sure?');">
                </div>   
                <div class="col-auto">
                    <p>{{$totalResult}} Items</p>
                </div>         
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="check-column" width="10%">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAll">
                            <label class="form-check-label" for=""></label>
                        </div>
                    </th>
                    <th width="70%">PIN Code</th>                
                    <th width="20%">Action</th>
                </tr>
            </thead>
            <tbody>       
            @php
                
            @endphp 
            @forelse ($data as $item)
                <tr>
                    <td class="check-column">
                        <div class="form-check">
                            <input name="ids[]" class="data-check" type="checkbox" value="{{$item->id}}">
                            <label class="form-check-label" for=""></label>
                        </div>
                    </td>
                    <td>
                        {{$item->number}}
                    </td>
                    <td>
                        <a href="{{ route('service-partner.removepincdoesingle',[Crypt::encrypt($item->id),Crypt::encrypt($service_partner_id),Request::getQueryString()]) }}" onclick="return confirm('Are You Sure?')" class="btn btn-outline-danger select-md">Remove</a>                        
                    </td>
                </tr>
                @php
                    
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
    </form>
       
    {{$data->links()}}
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
        $('#btnSuspend').prop('disabled', true);        
        $("#checkAll").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
            var checkAllStatus = $("#checkAll:checked").length;
            // console.log(checkAllStatus)
            if(checkAllStatus == 1){
                $('#btnSuspend').prop('disabled', false);
            }else{
                $('#btnSuspend').prop('disabled', true);
            }
        });
        
        $('.data-check').change(function () {
            $('#btnSuspend').prop('disabled', false);
            var total_checkbox = $('input:checkbox.data-check').length;
            var total_checked = $('input:checkbox.data-check:checked').length;
            
            if(total_checked == 0){
                $('#btnSuspend').prop('disabled', true);
            }
          
            if(total_checkbox == total_checked){
                console.log('All checked')
                $('#checkAll').prop('checked', true);
            }else{
                console.log('Not All checked')
                $('#checkAll').prop('checked', false);
            }
        })
    })

    
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    
</script>  
@endsection 