@extends('layout.app')

@section('title', 'astromind')

@section('content')


	<div class="container">
		<form  action="{{ url('payment-request-initiate') }}" method="post">

        @csrf
		<h1 class="bg-dark text-white text-center">payment Here

		</h1>

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
				<label>Contact No</label>
				<input type="text" name="contact" class="form-control">

			</div>

			<div class="form-group">
				<label>address</label>
				<input type="text" name="address" class="form-control">

			</div>

			<div class="form-group">
				<label>Amount</label>
				<input type="text" name="amount" class="form-control">

			</div>



			<input type="submit" name="submit" value="Register" class="form-control">

			
		</div>
		
		</form>

	</div>			

<br>

@endsection