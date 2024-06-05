@extends('layouts.app')
@section('content')
@section('page', 'Dashboard')
    <section>
        @php
            $is_service_partner_only = false;
            if(Auth::user()->id == 8){
                $is_service_partner_only = true;
                // die("Hi");
            }
        @endphp
        @if(!$is_service_partner_only)
        <div class="row">            
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-success" onclick="location.href='{{route('customer.list')}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Customers <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countCustomers}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('supplier.list')}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Suppliers <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countSuppliers}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-info" onclick="location.href='{{route('service-partner.list')}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Service Partners <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countServicePartners}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-secondary" onclick="location.href='{{route('product.list')}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Products <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countProducts}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-secondary" onclick="location.href='{{route('purchase-order.list',['po_type'=>'po'])}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>PO <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countPO}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-info" onclick="location.href='{{route('purchase-order.list',['po_type'=>'grn'])}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>GRN <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countGRN}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('sales-order.list')}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Sales Orders <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countSales}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-success" onclick="location.href='{{route('invoice.list')}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Invoices <i class="fi fi-br-user"></i></h4>
                        <h2> {{$countInvoice}}</h2>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>  
@endsection     