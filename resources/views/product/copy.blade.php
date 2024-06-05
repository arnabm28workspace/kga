@extends('layouts.app')
@section('content')
@section('page', 'Product')
<section>   
    <ul class="breadcrumb_menu">    
        <li>Product Management</li>       
        <li><a href="{{ route('product.list') }}?{{$getQueryString}}">Product</a> </li>
        <li>Copy</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('product.store') }}" enctype="multipart/form-data" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{genAutoIncreNo()}}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" id="">
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="fg" @if($data->type == 'fg') selected @endif>Finished Goods</option>
                                    <option value="sp" @if($data->type == 'sp') selected @endif>Spare Parts</option>
                                </select>
                                @error('type') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Category <span class="text-danger">*</span></label>
                                <select name="cat_id" class="form-control" id="cat_id">
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($category as $item)
                                    <option value="{{$item->id}}" @if($data->cat_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @empty                                       
                                    <option value="" disabled>No category available ... </option> 
                                    @endforelse
                                </select>
                                @error('cat_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Subcategory <span class="text-danger">*</span></label>
                                <input type="hidden" name="subcat_name" value="{{old('subcat_name')}}" id="subcat_name">
                                <select name="subcat_id" class="form-control" id="subcat_id">
                                    <option value="" hidden selected>Select an option</option>
                                    @forelse ($subcategory as $item)
                                    <option value="{{$item->id}}" @if($data->subcat_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @empty                                       
                                    <option value="" disabled>No subcategory available ... </option> 
                                    @endforelse
                                </select>
                                @error('subcat_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                                                
                    </div>                      
                </div>
                <div class="card shadow-sm">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Title <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="title" id="name" placeholder="Enter title" value="{{ $data->title }}" maxlength="100">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 37px;">
                                            <input type="checkbox" id="flexCheckDefault" title="Check same as product title and public name" @if(!empty($data->is_title_public_name_same)) checked @endif >
                                        </div>
                                    </div>
                                </div>
                                @error('title') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                         
                    </div>                      
                </div>
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Public Name <span class="text-danger">*</span></label>
                                <input type="text" name="public_name" id="public_name" placeholder="Please Enter Public Name" class="form-control" maxlength="100" value="{{$data->public_name}}">
                                @error('public_name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Description </label>
                                <textarea name="description" class="form-control" id="" cols="3" rows="3">{{$data->description}}</textarea>
                                
                            </div>
                        </div>                                        
                    </div>                      
                </div>
                <div class="card shadow-sm">
                    <div class="row">     
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Service Level <span class="text-danger">*</span></label>
                                <select name="service_level" class="form-control" id="">
                                    <option value="" selected hidden>Select an option</option>
                                    <option value="customer_level" @if($data->service_level == 'customer_level') selected @endif>Customer</option>
                                    <option value="dealer_level" @if($data->service_level == 'dealer_level') selected @endif>Dealer</option>
                                </select>
                                
                                @error('service_level') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Set of Pieces <span class="text-danger">*</span></label>
                                <input type="number" value="{{$data->set_of_pcs}}" name="set_of_pcs" class="form-control" min="1" id="">
                                @error('set_of_pcs') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_installable" name="is_installable" @if($data->is_installable == 1) checked @endif>
                                <label class="form-check-label" for="is_installable">
                                    Installation Applicable
                                </label>
                            </div>
                            @error('is_installable') <p class="small text-danger">{{ $message }}</p> @enderror                            
                        </div> 
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_amc_applicable" name="is_amc_applicable" @if($data->is_amc_applicable == 1) checked @endif>
                                <label class="form-check-label" for="is_amc_applicable">
                                    AMC Applicable
                                </label>
                            </div>
                            @error('is_amc_applicable') <p class="small text-danger">{{ $message }}</p> @enderror                            
                        </div>                                  
                    </div>                      
                </div>
                <div class="card shadow-sm">
                    <div class="row">  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Warranty Status <span class="text-danger">*</span></label>
                                <select name="warranty_status" class="form-control" id="warranty_status">
                                    <option value="" selected hidden>Select an option</option>
                                    <option value="yes" @if($data->warranty_status == 'yes') selected @endif>Yes</option>
                                    <option value="no" @if($data->warranty_status == 'no') selected @endif>No</option>
                                </select>
                                @error('warranty_status') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>  
                        <div class="col-md-3" id="warranty_div">
                            <div class="form-group">
                                <label for="">Warranty Period (In month)  <span class="text-danger">*</span></label>
                                <input type="text" name="warranty_period" class="form-control" value="{{ $data->warranty_period }}" id="">                                
                                @error('warranty_period') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">MOP <span class="text-danger">*</span></label>
                                {{-- <input type="text" name="mop" class="form-control" value="{{ $data->mop }}" placeholder="Enter Market Operating Price" id="" onkeypress="validateNum(event)" maxlength="8"> --}}
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 34px;">
                                            Rs.
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="mop" id="mop" placeholder="Enter Market Operating Price" value="{{ $data->mop }}" onkeypress="validateNum(event)" maxlength="8">
                                </div>
                                @error('mop') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">                        
                        <a href="{{route('product.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Save </button>
                    </div>
                </div>                                            
            </div>              
        </div>
                 
        </form>             
    </div>    
</section>
<script>

    $(document).ready(function(){
        var is_title_public_name_same = "{{ $data->is_title_public_name_same }}";
        console.log(is_title_public_name_same);
        if(is_title_public_name_same == 1){
            console.log("Checked");
            $('#public_name').prop('readonly', true);
        } else {
            console.log("Not Checked");
            $('#public_name').prop('readonly', false);
        }

        var old_warranty_status = "{{ $data->warranty_status  }}";
        if(old_warranty_status != ''){
            if(old_warranty_status == 'yes'){
                $('#warranty_div').show();
            }else{
                $('#warranty_div').hide();
            }
        }else {
            $('#warranty_div').hide();
        }
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    });
    $('#cat_id').on('change', function(){        
        $.ajax({
            url: "{{ route('ajax.subcategory-by-category') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "cat_id": this.value
            },
            success: function(data){
                console.log(data);
                var subcatHTML = ``;
                if(data.length == 0){
                    subcatHTML += `<option value="" disabled>No subcategory available ... </option>`;
                }
                subcatHTML += `<option value="" hidden selected>Select an option</option>`;
                for(var i=0; i < data.length; i++){
                    subcatHTML += `<option value="`+data[i].id+`" data-name="`+data[i].name+`">`+data[i].name+`</option>`;
                }

                $('#subcat_id').html(subcatHTML);
            }
        });
    });
    $('#subcat_id').on('change', function(){
        var subcat_name = $('option:selected', this).attr('data-name');
        // alert(subcat_name);
        $('#subcat_name').val(subcat_name);
    });
    $('#warranty_status').on('change', function(){
        var warranty_status = this.value;
        if(warranty_status == 'yes'){
            $('#warranty_div').show();
        }else{
            $('#warranty_div').hide();
        }
    });
    $("input:checkbox#flexCheckDefault").change(function() {
        var ischecked= $(this).is(':checked');
        var name = $('#name').val();
        var public_name = $('#public_name').val();  
        if(ischecked){
            $('#public_name').val(name);  
            $('#public_name').prop('readonly', true);  
            
        }else{
            $('#public_name').val('');  
            $('#public_name').prop('readonly', false);   
           
        }       
    });
    function validateNum(evt) {
        var theEvent = evt || window.event;

        // Handle paste
        if (theEvent.type === 'paste') {
            key = event.clipboardData.getData('text/plain');
        } else {
        // Handle key press
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
        }
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }
</script>
@endsection