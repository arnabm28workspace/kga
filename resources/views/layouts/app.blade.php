<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{url('assets')}}/css/bootstrap.min.css" rel="stylesheet">
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-bold-rounded/css/uicons-bold-rounded.css'>
        <link href="{{url('assets')}}/css/style.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{url('assets')}}/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>
        <script type="text/javascript" src="{{url('assets')}}/js/custom.js"></script>
        
        <title>KGA | @yield('page')</title>
    </head>
    <body>
        @php
            $is_service_partner_only = false;
            if(Auth::user()->id == 8){
                $is_service_partner_only = true;
                // die("Hi");
            }
        @endphp
        <aside class="side__bar shadow-sm">
            <div class="admin__logo">
                <div class="logo">
                <img src="{{url('assets')}}/images/kga_logo.png">
                </div>
                <div class="admin__info">
                <h1>{{Auth::user()->name}}</h1>
                <h4>{{Auth::user()->email}}</h4>
                </div>
            </div>
            <nav class="main__nav">
                <ul>
                    <li class="@if(request()->is('home*')) {{'active'}} @endif">
                        <a href="{{route('home')}}"><i class="fi fi-br-home"></i> <span>Home</span></a>
                    </li>
                    @if (Auth::user()->type == 'admin')                    
                    <li>
                        <a href="#"><i class="fi fi-br-user"></i> <span>User Management</span></a>
                        <ul>
                            <li @if(request()->is('manager/*')) class="active" @endif>
                                <a href="{{ route('manager.list') }}"><i class="fi fi-br-user-time"></i> <span>Manager</span> </a> 
                            </li>
                            <li @if(request()->is('staff/*')) class="active" @endif>
                                <a href="{{route('staff.list')}}"><i class="fi fi-br-user-add"></i> <span>Staff</span> </a>
                            </li>
                        </ul>
                    </li>
                    @endif                    
                    <li>
                        <a href="#"><i class="fi fi-br-user"></i> <span>Service Partner Management</span></a>
                        <ul>
                            <li 
                            @if(request()->is('service-partner/upload-csv-order')) class="active" @endif
                            >
                                <a href="{{ route('service-partner.upload-csv-order') }}"><i class="fi fi-br-location-alt"></i> <span>Upload Order CSV</span> </a> 
                            </li>
                            <li 
                            @if(request()->is('service-partner/list')) class="active" @endif
                            >
                                <a href="{{route('service-partner.list')}}"><i class="fi fi-br-users"></i> <span>Partner List</span> </a>
                            </li>
                        </ul>
                    </li>
                    @if(!$is_service_partner_only)  
                    <li class="@if(request()->is('supplier*')) {{'active'}} @endif">
                        <a href="{{route('supplier.list')}}"><i class="fi fi-br-users-alt"></i> <span>Supplier</span></a>
                    </li>
                    <li class="@if(request()->is('customer*')) {{'active'}}  @endif">
                        <a href="{{route('customer.list')}}"><i class="fi fi-br-users"></i> <span>Customer</span></a>
                    </li>                   
                    <li>
                        <a href="#"><i class="fi fi-br-money-check-edit-alt"></i> <span>Product Management</span></a>
                        <ul>
                            <li @if(request()->is('category/*') && (Request::get('type') == '' || Request::get('type') == 'parent')) class="active" @endif>
                                <a href="{{route('category.list',['type'=>'parent'])}}"><i class="fi fi-br-chart-tree"></i> <span>Category</span></a>
                            </li>
                            <li @if(request()->is('category/*') && (Request::get('type') == 'child')) class="active" @endif>
                                <a href="{{route('category.list',['type'=>'child'])}}"><i class="fi fi-br-chart-tree"></i> <span>Sub Category</span></a>
                            </li>
                            <li class="@if(request()->is('product*')) {{'active'}} @endif">
                                <a href="{{route('product.list')}}"><i class="fi fi-br-cube"></i> <span>Product</span></a>
                            </li>
                        </ul>
                    </li>                   
                    <li>                        
                        <a href="#"><i class="fi fi-br-money-check-edit-alt"></i> <span>Purchase Order</span></a>
                        <ul>
                            <li @if(request()->is('purchase-order/list') && (Request::get('po_type') == '' || Request::get('po_type') == 'po')) class="active" @endif>
                                <a href="{{route('purchase-order.list', ['po_type'=>'po'])}}">
                                    <i class="fi fi-br-truck-container"></i>
                                    <span>PO</span> 
                                </a>
                            </li>
                            <li @if(request()->is('purchase-order/list') && (Request::get('po_type') == 'grn')) class="active" @endif>
                                <a href="{{route('purchase-order.list', ['po_type'=>'grn'])}}">
                                    <i class="fi fi-br-truck-couch"></i>
                                    <span>GRN</span> 
                                </a>
                            </li>
                        </ul>
                    </li>  
                    <li @if(request()->is('stock/*')) class="active" @endif>
                        <a href="{{ route('stock.list') }}"><i class="fi fi-br-cube"></i> <span>Stock Inventory</span></a>
                    </li>
                    <li>
                        <a href="#"><i class="fi fi-br-money-check-edit"></i> <span>Order Management</span></a>
                        <ul>
                            <li @if(request()->is('sales-order/*')) class="active" @endif>
                                <a href="{{ route('sales-order.list') }}">
                                    <i class="fi fi-br-file-invoice"></i>
                                    <span>Orders</span>
                                </a>
                            </li>
                            <li @if(request()->is('packingslip/*')) class="active" @endif>
                                <a href="{{ route('packingslip.list') }}">
                                    <i class="fi fi-br-file-invoice"></i>
                                    <span>Packing Slips</span>
                                </a>
                            </li>
                            <li @if(request()->is('invoice/*')) class="active" @endif>
                                <a href="{{ route('invoice.list') }}">
                                    <i class="fi fi-br-file-invoice"></i>
                                    <span>Invoices</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </nav>
            <div class="nav__footer">
                <a class="dropdown-item" href=""
                 onclick="if (confirm('Are You Sure?')){ event.preventDefault();  document.getElementById('logout-form').submit(); }  else { return false; } "
                 >
                    <i class="fi fi-br-sign-out"></i> <span>{{ __('Logout') }}</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </aside>
        <main class="admin">
            <header>
                <div class="row align-items-center">                    
                    <div class="col-auto ms-auto">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                {{Auth::user()->name}}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                @if(!$is_service_partner_only)
                                <li>
                                    <a class="dropdown-item" href="{{ route('myprofile') }}">My Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('changepassword') }}">Change Password</a>
                                </li>                                
                                @endif                                
                                <li>
                                    <a class="dropdown-item" href="{{ route('settings') }}">Settings</a>
                                </li>
                                <li>
                                    <a id="logout" class="dropdown-item" href="" 
                                    onclick="if (confirm('Are You Sure?')){ event.preventDefault();  document.getElementById('logout-form').submit(); }  else { return false; } "
                                    >
                                        <i class="fi fi-br-sign-out"></i> <span>{{ __('Logout') }}</span>
                                    </a>                    
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <section class="admin__title">
                <h1>@yield('page')</h1>
            </section>
            @yield('content')
            <footer>
                <div class="row">
                    <div class="col-12 text-end">KGA 2021-{{date('Y')}}</div>
                </div>
            </footer>
        </main>
        
    </body> 
    <script>
        
    </script>   
</html>