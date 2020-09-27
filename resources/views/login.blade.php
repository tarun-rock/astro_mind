@extends('layout.app')

@section('title', 'astromind')

@section('content')

	<div class="container">


		@if(isset(Auth::user()->email))

		<script>window.location = "main/successlogin";</script>

		@endif



		@if($message = Session::get('error'))

		<div class="alert alert-danger alert-block">
			
			<button type="button" class="close" data-dismiss="alert">x</button>
			
			<strong>{{ $message }}</strong>

		</div>


		@endif


		@if(count($errors) > 0)
			<div class="alert alert-danger">

				<ul>
				@foreach($errors->all() as $error)

					<li>{{ $error }}</li>
				@endforeach
				</ul>
				
			</div>
			@endif

			<form method = "post" action="{{ url('main/checklogin') }}" >
				
			
		        @csrf
		        <div class="container-box">

					 <div class="form-group">
						<label>email</label>
						<input type="text" name="email" class="form-control">

					</div>

					<div class="form-group">
						<label>password</label>
						<input type="password" name="password" class="form-control">

					</div>

					<input type="submit" name="submit" value="Login" class="form-control">
					
				</div>
				
		</form>
	</div>
	<br>
	<br/>

@endsection



