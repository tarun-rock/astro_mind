<!DOCTYPE html>
<html lang="en">

    @include('layout.head')

    @include('layout.script')

    @include('layout.starter')

    <body>
		@include('layout.header')
	 

		 @yield('content')

		{{--  @yield('sidebar') --}} 

		{{-- @include('layout.login-sign-up-model') --}}

		@include('layout.footer')


		{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script> --}}

    	{{-- <script>

	        $(function () {


	            $(document).on("click", ".create-account-btn", function () {

	                $("#login").modal("hide");

	                setTimeout(function(){

	                    $("#signin").modal("show");

	                }, 500);
	            });

	            $(".user-login-btn").click(function(){
				    
				    $("#signin").hide();

				    $("#login").show();
				  });
				  

	            $(document).on("click", ".user-login-btn", function () {

	            	console.log("hello");

	                $("#signin").modal("hide");

	                setTimeout(function(){

	                    $("#login").modal("show");

	                }, 500);
	            });

	            $("#user-register-form").validate({

	                rules: {
	                    name : {
	                        required : true,
	                        maxlength : 255,
	                    },
	                    email : {
	                        required : true,
	                        email : true,
	                        maxlength : 255,
	                    },
	                    password : {
	                        required : true,
	                        minlength : 8,
	                        maxlength : 20,
	                    },
	                    
	                },
	                submitHandler: function () {

	                    $(".user-register-btn").attr("disabled", true);

	                    $(".user-register-btn").html("processing... <i class='fas fa-spinner fa-spin'></i>");

	                    $.ajax({

	                        url: "{{ route('user-register') }}",
	                        type: "POST",
	                        data: $("#user-register-form").serialize(),
	                        headers: {
	                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	                        },
	                        success: function(resp) {

	                            console.log(resp);

	                            if(resp.status == 2)
	                            {
	                                Swal.fire({
	                                    title: resp.data.title,
	                                    type: 'error',
	                                    text : resp.message,
	                                    allowOutsideClick: false,
	                                }).then((result) => {
	                                    if (result.value) {

	                                        $(".user-register-btn").attr("disabled", false);

	                                        $(".user-register-btn").html("register");
	                                    }
	                                });
	                                
	                            }
	                            else
	                            {
	                                Swal.fire({
	                                    title: resp.data.title,
	                                    type: resp.data.type,
	                                    text : resp.message,
	                                }).then((result) => {
	                                    if (result.value) {

	                                        $("#signin").modal("hide");

	                                        window.location.href = "{{ route('product') }}";
	                                        
	                                    }
	                                });
	                            }
	                        }
	                    });

	                    $(".user-register-btn").attr("disabled", true);
	                }
	            });

	            $("#user-login-form").validate({

	                rules: {
	                    email : {
	                        required : true,
	                        email : true,
	                    },
	                    password : {
	                        required : true,
	                        minlength : 6,
	                        maxlength : 20,
	                    },
	                },
	                submitHandler: function () {

	                    $(".login-btn").attr("disabled", true);

	                    $(".login-btn").html("Logging In... <i class='fas fa-spinner fa-spin'></i>");

	                    $.ajax({

	                        url: "{{ route('user-login') }}",
	                        type: "POST",
	                        data: $("#user-login-form").serialize(),
	                        headers: {
	                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	                        },
	                        success: function(resp){

	                            if(resp.status == 1)
	                            {
	                                $('.success-block ul li').text(resp.message);
	                                $(".all-error-block").hide();
	                                $(".success-block").show();

	                                setTimeout(function () {

	                                    window.location.href = "{{ route('product') }}";

	                                }, 1000 );

	                            }
	                            else
	                            {
	                                $(".success-block").hide();
	                                $(".all-error-block ul li").text(resp.message);
	                                $(".all-error-block").show();
	                                $(".login-btn").attr("disabled", false);

	                                $(".login-btn").html("Login");
	                            }
	                        },
	                    });

	                    $(".login-btn").attr("disabled", true);
	                }
	            });

	    </script> --}}

	</body>
</html>