@extends('admin.master.starter')

@section("title","View-Product")

@section('head_extra')

    <link href="{{ asset('back/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <link href="{{ asset('back/assets/libs/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

@endsection

@section('content')

	<div class="row">
	    <div class="col-12">
	        <div class="card">
	            <div class="card-body">
	                <form  method="post">
	                    <h4 class="card-title">View Product
	                         <div class="float-right">
	                            <div class="float-right"><a href="{{ route('add-product') }}" class="btn btn-success">Add</a>
	                            </div>
	                        </div>
	                    </h4>

	                    <div class="table-responsive">
	                        <table id="zero_config" class="table table-striped table-bordered">
	                            <thead>

	                                <tr>
	                                 	<th>S.No</th>
	                                  	<th>Title</th>
	                                  	<th>Name</th>
	                                 	<th>Price</th>
	                                  	<th>Image</th>
	                                 
	                                 {{-- <th>Action</th> --}}

	                                </tr>
	                            </thead>
	                         
	                        </table>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>

@endsection

@section('script_extra')

    <script src="{{ asset('back/assets/extra-libs/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('back/assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script type="text/javascript">

    $(function() {
        $('#zero_config').DataTable({

            "ordering": false,
            "processing": true,
            "serverSide": true,

            "ajax": {

                        "type": "post",


                        "data": function (d) {
                        d.ajax = 1;
                        d._token = "{{ csrf_token() }}";
                    
                    }
            },

            columns: [
            {data: 'id', name: 'id'}, 
            {data: 'prod_title', name: 'prod_title'},        
            {data: 'prod_name', name: 'prod_name'},
            {data: 'price', name: 'price'},
            {data: 'prod_image', name: 'image'},
            ]

        });

    });

    </script>

@endsection

