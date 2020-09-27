@extends('layout.app')

@section('title', 'astromind')

@section('content')


	<div class="container">
		<form   method="post">

        @csrf
		<h1 class="bg-dark text-white text-center">Register Here

		</h1>
			<a href="{{ url('main') }}" class="btn btn-success right">Login</a>
			<br>
			<br>

		<div class="col-sm-12">
			<div class="form-group">
				<label>Firstname</label>
				<input type="text" name="name" class="form-control">

			</div>

			 <div class="form-group">
				<label>Email</label>
				<input type="text" name="email" class="form-control">

			</div>

			<div class="form-group">
				<label>Password</label>
				<input type="password" name="password" class="form-control">

			</div>

			<div class="form-group">
				<label>Type</label>
				<input type="text" name="type" class="form-control">

			</div>

			<input type="submit" name="submit" value="Register" class="form-control">

			
		</div>
		
		</form>

	</div>			

<br>

@endsection