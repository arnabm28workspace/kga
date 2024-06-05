@extends('layouts.app')
@section('content')
@section('page', 'Barcodes')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('sales-order.list') }}?{{$getQueryString}}">PO</a> </li>
        <li>{{$order_no}}</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                             
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <a href="" class="btn btn-outline-primary btn-sm">Download Barcodes </a>
                    </div>
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control" placeholder="Search items..">
                    </div>
                    <div class="col-auto">
                        
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div> 
    <div class="row">
        <form action="{{ route('purchase-order.generate-grn') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$id}}">
        <div class="col-sm-12">
            @php
                $i=1;
            @endphp
            @foreach ($data as $product_id => $barcodes)
            <input type="hidden" name="product_id[]" value="{{$product_id}}">
            <input type="hidden" name="count[]" value="{{ count($data[$product_id]) }}">
            @php
                $product_title = getSingleAttributeTable('products','id',$product_id,'title');
                $isBulkScanned = isBulkScanned($id,$product_id);
                // dd($data[$product_id]);
                
            @endphp
            <div class="card shadow-sm">
                <div class="row">
                    <p>#{{$i}}  
                        <strong>{{$product_title}}</strong>
                        <input class="form-check-input data-check-{{$product_id}}" name=""  type="checkbox" value="1" id="bulk_scan_{{$product_id}}" onchange="bulkScan({{$product_id}});" @if($isBulkScanned) checked @endif>
                        <label class="form-check-label" for="bulk_scan_{{$product_id}}">Bulk Scan</label>
                    </p>
                    @foreach ($barcodes as $barcode)
                    <div class="col-sm-4">                        
                        {{-- <span title="{{$barcode->barcode_no}}">
                            {!! $barcode->code_html !!}
                            <strong>{{$barcode->barcode_no}}</strong>
                        </span>  --}}
                        <img class="barcode_image" alt="Barcoded value {{$barcode->barcode_no}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&amp;text={{$barcode->barcode_no}}&amp;includetext&height=6">
                        <div class="form-group">
                            <input class="form-check-input data-barcode data-check-{{$product_id}}" name="barcode_no[]" value="{{$barcode->barcode_no}}" type="checkbox" value="1" id="barcode_no_{{$barcode->barcode_no}}" onclick="return false" @if($barcode->is_scanned == 1) checked @endif >
                            <label class="form-check-label" for="barcode_no_{{$barcode->barcode_no}}"> SCANNED</label>
                        </div>
                        
                    </div>
                    @endforeach                   
                    
                </div>
            </div>   
            @php
                $i++;
            @endphp   
            @endforeach                                             
        </div>  
        <div class="card shadow-sm">
            <div class="card-body text-end">
                <a href="{{route('purchase-order.list')}}" class="btn btn-sm btn-danger">Back</a>
                <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Generate </button>
            </div>
        </div> 
    </form>           
    </div>  
</section>
<script>
    var id = "{{ $id }}";
    $(document).ready(function(){
        
        var total_barcodes = $('input:checkbox.data-barcode').length;
        var total_scanned = $('input:checkbox.data-barcode:checked').length;
        // alert(total_barcodes+'  '+total_scanned);
        if(total_scanned < total_barcodes){
            $('#submitBtn').prop('disabled', true);
            const interval = setInterval(() => {        
                getScannedImages(id);
            }, 10000);
        }
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });

    function bulkScan(product_id){
        var is_scanned = 1;
        var is_bulk_scanned = 1;
        if (document.getElementById('bulk_scan_'+product_id).checked == true) {
            
            var box = confirm("Are you sure ?");
            if (box == true)  {
                $('input:checkbox.data-check-'+product_id).prop('checked', true);
                $('input:checkbox#bulk_scan_'+product_id).prop('checked', true);
                is_scanned = 1;
                is_bulk_scanned = 1;
            }  else  {
                $('input:checkbox.data-check-'+product_id).prop('checked', false);
                document.getElementById('bulk_scan_'+product_id).checked = false;                
            }
       
        } else {
            $('input:checkbox.data-check-'+product_id).prop('checked', false);
            is_scanned = 0;
            is_bulk_scanned = 0;            
        }
        $.ajax({
            url: "{{ route('ajax.po-bulk-scan') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "purchase_order_id": id,
                "product_id": product_id,
                "is_bulk_scanned": is_bulk_scanned,
                "is_scanned": is_scanned
            },
            success: function(data){
                
                var sucessData = data;
                console.log(sucessData)                
            }
        });
        var total_barcodes = $('input:checkbox.data-barcode').length;
        var total_scanned = $('input:checkbox.data-barcode:checked').length;
        if(total_scanned == total_barcodes) {
            console.log("Hi");
            $('#submitBtn').prop('disabled', false);
        } else if (total_scanned < total_barcodes) {
            $('#submitBtn').prop('disabled', true);
        }
    }

    function getScannedImages(id){
        $.ajax({
            url: "{{ route('ajax.check-po-scanned-boxes') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "purchase_order_id": id
            },
            success: function(data){
                
                var sucessData = data;
                console.log(sucessData)
                
                // console.log(sucessData);
                for(var i = 0; i < sucessData.length; i++) {
                    // alert(sucessData[i].barcode_no)
                    // $('#scanstatus'+sucessData[i].barcode_no).text('Scanned');
                    // $('#scannedweight'+sucessData[i].barcode_no).text(scanned_weight_val+' kg');
                    // $('#scanstatus'+sucessData[i].barcode_no).removeClass('badge bg-secondary');
                    // $('#scanstatus'+sucessData[i].barcode_no).addClass('badge bg-primary');
                    // $('#archivebtn'+sucessData[i].barcode_no).hide();
                    $('#barcode_no_'+sucessData[i].barcode_no).attr('checked', 'checked');

                    var total_barcodes = $('input:checkbox.data-barcode').length;
                    var total_scanned = $('input:checkbox.data-barcode:checked').length;

                    if(total_barcodes == total_scanned){
                        $('#submitBtn').prop('disabled', false);
                    }

                }
               
            }
        });
    }
</script>  
@endsection 