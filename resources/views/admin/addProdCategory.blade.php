@extends('admin.master.starter')

@section("title","Dashboard")

@section('head_extra')

    <link href="{{ asset('back/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <link href="{{ asset('back/assets/libs/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

@endsection

@section('content')


<div class="row">

  	<div class="col-12">
    	<div class="card">
      		<div class="card-body">
        		<h4 class="card-title">Add Product Category</h4>

        		<form method="post">

          		@csrf

	           		<div class="form-group">

	            		<label>Prouct Category Name</label>
	            
	            		<input type="text" name="category" required class="form-control">
	          
	          		</div>

                <div class="form-group">
                  
                <label>Slug</label>
              
                  <input type="text" name="slug" required class="form-control">
            
                </div>

				<input type="submit" name="submit" value="Submit" class="btn btn-success">

				<input type="submit" name="submit" value="Cancel" class="btn btn-danger">
      			</form>
        	</div>
      	</div>


    </div>
	

</div>




@endsection






