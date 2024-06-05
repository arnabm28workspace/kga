@extends('layouts.app')
@section('content')
@section('page', 'Manager')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('manager.list') }}">Manager</a> </li>
        <li>Create</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('manager.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span> </label>
                                <input type="text" name="name" placeholder="Name" class="form-control" maxlength="100" value="{{old('name')}}">
                                @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Email <span class="text-danger">*</span> </label>
                                <input type="text" name="email" placeholder="Email" class="form-control" maxlength="100" value="{{old('email')}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Phone <span class="text-danger">*</span> </label>
                                <input type="text" name="phone" placeholder="Phone" class="form-control" maxlength="10" value="{{old('phone')}}">
                                @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>  
                </div> 
                <div class="card shadow-sm">
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Password <span class="text-danger">*</span> </label>
                                <input type="password" name="password" placeholder="Password" class="form-control" maxlength="100" value="">
                                @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Confirm Password <span class="text-danger">*</span> </label>
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control" maxlength="10" value="">
                                @error('password_confirmation') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>  
                </div>     
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('manager.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Create </button>
                    </div>
                </div>  
            </form>                              
        </div>            
    </div>    
</section>
<script>
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    }); 
</script>
@endsection