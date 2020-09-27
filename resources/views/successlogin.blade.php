
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>
<body>

	<div class="container">
		
        @csrf

		<h3 align="center">simple login system</h3>


    	@if(isset(Auth::user()->email))

    	<div class="alert alert-success">

    		<strong>welcome {{ auth::user()->name }}</strong>
    		
    	</div>
    		<a href="{{ url('main/logout') }}">Logout</a>

    	@else
    	<script>window.location = "main";</script>

    	@endif 

	</div>
</body>