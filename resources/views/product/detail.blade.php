@extends('layouts.app')
@section('content')
@section('page', $data->title)
<section>
    <ul class="breadcrumb_menu">     
        <li>Product Management</li>      
        <li><a href="{{ route('product.list') }}?{{$getQueryString}}">Product</a> </li>
        <li>{{$data->unique_id}}</li>
    </ul> 
    @if (!empty(Request::get('backtomodule')))
    <ul class="breadcrumb_menu">   
            {{-- {{ Request::get('backtodestination') }}  --}}
        <li>
            <a href="{{Request::get('backtodestination')}}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To {{ str_replace("_"," ",ucwords(Request::get('backtomodule'))) }}
            </a>
        </li>               
    </ul>
    @endif 
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        {{-- <div class="form-group mb-3">
                            <p><span class="text-muted">Title : </span>{{$data->title}} </p>
                        </div>  --}}
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Public Name : </span>{{$data->public_name}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Description : </span>{{$data->description}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Category > Subcategory : </span>{{$data->category->name}} > {{$data->subcategory->name}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">MOP : </span> Rs. {{ number_format((float)$data->mop, 2, '.', '') }} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Warranty Status : </span>
                                @if ($data->warranty_status == 'yes')
                                <span class="badge bg-success">{{ucwords($data->warranty_status)}}</span>                                
                                @else
                                <span class="badge bg-danger">{{ucwords($data->warranty_status)}}</span>
                                @endif                                
                            </p>
                        </div> 
                        @if ($data->warranty_status == 'yes')
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Warranty Period : </span>{{ucwords($data->warranty_period)}} months </p>
                        </div> 
                        @endif
                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Set of Pieces : </span>{{$data->set_of_pcs}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Service Level : </span>{{ucwords(str_replace("_"," ",$data->service_level))}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Installation Applicable : </span>
                                @if (!empty($data->is_installable))
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">AMC Applicable : </span>
                                {{-- {{$data->is_amc_applicable}}  --}}
                                @if (!empty($data->is_amc_applicable))
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </p>
                        </div> 
                    </div>
                </div>  
            </div>                                      
        </div>            
    </div>    
</section>
<script>
    
</script>  
@endsection 