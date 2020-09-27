@extends('admin.master.starter')

@section("title","Dashboard")

@section('content')

<!-- ============================================================== -->
<!-- Info box -->
<!-- ============================================================== -->
<div class="card-group">
    <!-- Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="m-r-10">
                                    <span class="btn btn-circle btn-lg bg-danger">
                                        <i class="ti-clipboard text-white"></i>
                                    </span>
                </div>
                <div>
                    New projects
                </div>
                <div class="ml-auto">
                    <h2 class="m-b-0 font-light">1</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Card -->
    <!-- Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="m-r-10">
                                    <span class="btn btn-circle btn-lg btn-info">
                                        <i class="ti-wallet text-white"></i>
                                    </span>
                </div>
                <div>
                    Total Earnings

                </div>
                <div class="ml-auto">
                    <h2 class="m-b-0 font-light">113</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Card -->
    <!-- Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="m-r-10">
                                    <span class="btn btn-circle btn-lg bg-success">
                                        <i class="ti-shopping-cart text-white"></i>
                                    </span>
                </div>
                <div>
                    Total Sales

                </div>
                <div class="ml-auto">
                    <h2 class="m-b-0 font-light">0</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Card -->
    <!-- Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="m-r-10">
                                    <span class="btn btn-circle btn-lg bg-warning">
                                        <i class="mdi mdi-currency-usd text-white"></i>
                                    </span>
                </div>
                <div>
                    Profit

                </div>
                <div class="ml-auto">
                    <h2 class="m-b-0 font-light">0</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Card -->
    <!-- Column -->


</div>

@endsection
