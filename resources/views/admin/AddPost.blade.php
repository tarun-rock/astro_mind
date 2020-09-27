@extends('admin.master.starter')

@section("title","Dashboard")

@section('content')


<div class="row">

  	<div class="col-12">
    	<div class="card">
      		<div class="card-body">
        		<h4 class="card-title">Add Post</h4>

        		<form method="post" enctype="multipart/form-data">

          			@csrf


					<div>

						<div class="form-group">
							<label>Title</label>
							<input type="text" name="title" required  class="form-control">
						</div>
					</div>

					<div class="form-group">

						<label>Slug</label>

						<input type="text" name="slug" required  class="form-control">
						
					</div>

					<div class="form-group">
						<label>Description</label>
						<input type="text" name="description" required  class="form-control">
					</div>

					


					{{-- <div class="form-group">
						<label>Category</label>
						<select name="category" placeholder="select One" class="form-control">
							@foreach($category as $val)
							<option value="{{ $val->value }}">{{ $val->name }}</option>
							@endforeach

						</select>
					</div> --}}


					<div class="form-group">
						<label>Image</label>
						<input type="file" name="image" class="form-control">
					</div>

						<input type="submit" name="submit" value="Submit" class="btn btn-primary">
						<input type="submit" name="submit" value="Cancel" class="btn btn-success">


				</form>
			</div>
		</div>
	</div>
</div>

@endsection