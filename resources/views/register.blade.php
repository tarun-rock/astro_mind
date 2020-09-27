<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>



	<div class="container">

	<form action='register' method="post">

	
        @csrf

		<h1 class="bg-dark text-white text-center">Register Here</h1>

		<div class="col-sm-12">
			<div class="form-group">
				<label>name</label>
				<input type="text" name="name" class="form-control">

			</div>

			 <div class="form-group">
				<label>email</label>
				<input type="text" name="email" class="form-control">

			</div>

			<div class="form-group">
				<label>password</label>
				<input type="password" name="password" class="form-control">

			</div>

			<input type="submit" name="submit" value="Register" class="form-control btn btn-success">
			
		</div>
		
</form>
	</div>
</body>
</html>