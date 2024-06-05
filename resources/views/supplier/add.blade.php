@extends('layouts.app')
@section('content')
@section('page', 'Supplier')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('supplier.list') }}">Supplier</a> </li>
        <li>Create</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('supplier.store') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" placeholder="Please Enter Name" class="form-control" maxlength="100" value="{{old('name')}}">
                                @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Public Name <span class="text-danger">*</span></label>
                                <input type="text" name="public_name" placeholder="Please Enter Public Name" class="form-control" maxlength="100" value="{{old('public_name')}}">
                                @error('public_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                        
                    </div>  
                </div>    
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Email <span class="text-danger">*</span></label>
                                <input type="text" name="email" placeholder="Please Enter Email" class="form-control" maxlength="100" value="{{old('email')}}">
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" placeholder="Please Enter Phone" class="form-control" maxlength="10" value="{{old('phone')}}">
                                @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" name="is_inside" type="checkbox" value="false" id="is_inside">
                                    <label class="form-check-label" for="is_inside">Inside India</label>
                                </div>
                                @error('is_inside') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="">Address <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" id="" placeholder="Please Enter Address" cols="1" rows="1">{{old('address')}}</textarea>
                                @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                                                 
                    </div>
                </div> 
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" placeholder="Please Enter State" class="form-control" maxlength="100" value="{{old('state')}}">
                                @error('state') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" placeholder="Please Enter City" class="form-control" maxlength="100" value="{{old('city')}}">
                                @error('city') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-4">  
                            <div class="form-group">
                                <label for="">PIN <span class="text-danger">*</span></label>
                                <input type="text" name="pin" placeholder="Please Enter PIN Code" class="form-control" maxlength="10" value="{{old('pin')}}">
                                @error('pin') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('supplier.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Create </button>
                    </div>
                </div>  
            </form>                              
        </div>            
    </div>    
</section>
<script>    
    $('#is_inside').change(function(){
        cb = $(this);
        cb.val(cb.prop('checked'));
        if($('checkbox#is_inside').is(':checked')){
            $("checkbox#is_inside").val(1);  // checked
        }else{
            $("checkbox#is_inside").val(0);  // unchecked
        }            
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...'); 
        return true;
    });    
</script>
@endsection