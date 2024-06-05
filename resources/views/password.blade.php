@extends('layouts.app')
@section('content')
@section('page', 'Change Password')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('home') }}">Dashboard</a> </li>
        <li>Change Password</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
            <form id="myForm" action="{{ route('savepassword') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="password" name="password" placeholder="New Password" class="form-control" maxlength="100" value="">
                                @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control" maxlength="100"  value="">
                                @error('password_confirmation') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>  
                </div>     
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('home')}}" class="btn btn-sm btn-danger">Back To Dasboard</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Save </button>
                    </div>
                </div>  
            </form>                              
        </div>            
    </div>    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    });  
</script>
@endsection