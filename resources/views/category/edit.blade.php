@extends('layouts.app')
@section('content')
@if ($type == 'parent')
    @section('page', 'Category')
@else
    @section('page', 'Sub Category')
@endif
<section>   
    <ul class="breadcrumb_menu">      
        <li>Product Management</li>     
        @if ($type == 'parent')
        <li><a href="{{ route('category.list',['type'=>$type]) }}">Category</a> </li>
        @else
        <li><a href="{{ route('category.list',['type'=>$type]) }}">Sub Category</a> </li>
        @endif  
        <li>Update</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('category.update',[$idStr,$getQueryString]) }}" enctype="multipart/form-data" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-9">            
                <div class="card shadow-sm">
                    <div class="row">
                        @if ($type == 'child')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Parent <span class="text-danger">*</span></label>
                                <select name="parent_id" class="form-control" id="">
                                    <option value="" hidden selected>Select an option</option>
                                    <option value="0">Parent</option>
                                    @forelse ($parents as $p)
                                    <option value="{{$p->id}}" @if($p->id == $data->parent_id) selected @endif>{{$p->name}}</option>
                                    @empty
                                    <option value="">No category found</option>
                                    @endforelse
                                </select>
                                @error('parent_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                         
                        @endif
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" placeholder="Please Enter Name" class="form-control" maxlength="100" value="{{$data->name}}">
                                @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                                                
                    </div>  
                </div>      
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Description </label>
                                <textarea name="description" class="form-control" id="" placeholder="Please Enter Description" cols="1" rows="2">{{$data->description}}</textarea>
                                {{-- @error('about') <p class="small text-danger">{{ $message }}</p> @enderror --}}
                            </div>
                        </div>                                                 
                    </div>
                </div>                                          
            </div> 
            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Image
                    </div>
                    <div class="card-body">
                        <div class="w-100 product__thumb">
                            <label for="thumbnail">
                                @if (!empty($data->image))
                                <img id="output" src="{{asset($data->image) }}">
                                @else
                                <img id="output" src="{{url('assets')}}/images/placeholder-image.jpg">
                                @endif
                                
                            </label>
                        </div>
                        <input type="file" name="photo" id="thumbnail" accept="image/*" onchange="loadFile(event)">
                        <script>
                            var loadFile = function(event) {
                            var output = document.getElementById('output');
                            output.src = URL.createObjectURL(event.target.files[0]);
                            output.onload = function() {
                                URL.revokeObjectURL(output.src) // free memory
                            }
                            };
                        </script>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('category.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Update </button>
                    </div>
                </div> 
            </div>   
        </div>
                 
        </form>             
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