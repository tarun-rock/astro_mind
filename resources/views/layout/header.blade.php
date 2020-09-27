<header>

    <div class="topbar pt-2 pb-2">
        <div class="container d-flex justify-content-end">
            <div class="container">
                <h5 class="text-white">&copy; increadibletarun07@gmail.com</h5>
                {{-- <p class="text-white">8283818157</p> --}}
            </div>
            <a href="https://www.facebook.com/Astromind/" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com/Astromind" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://www.linkedin.com/Astromind/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark justify-content-center d-flex">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <h3>Increadible Posts</h3>
                {{-- <img src="#"/> --}}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse text-u ppercase" id="collapsibleNavbar">
                <ul class="navbar-nav ml-auto headernav">
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('home') }}">Home</a>
                    </li>
                   <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact-us') }}">Contact us</a>
                    </li>

                    @auth

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('product') }}">Products</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="">My Cart</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('reset') }}">Change password</a>
                    </li>

                    @else

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    @endauth


                </ul>

                <div class="form-inline my-2 my-lg-0">

                    <form method="get" action="{{ route('search') }}" class="search-form" id="search-form"> 

                        <div class="input-group">
                            <input type="text" name="term" class="form-control" id="validationTooltipUsername" placeholder=""
                                   required>
                            <div class="input-group-append">
                                <input type="submit"  class="btn btn-primary icon icon-search">
                                 {{-- <span class="input-group-text" id="validationTooltipUsernamePrepend"><i
                                        class="fas fa-search"></i></span> --}}

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    


    <div class="bg-light secondary-nav">
        <div class="container">
            <ul class="">
                <li ><a href="{{ route('home') }}" >Home</a></li>
                <li><a href="{{ route('contact-us') }}" >Contact Us</a></li>
                @auth
                <li><a href="{{ url('product') }}" >Products</a></li> 
                    <li><a href="{{ route('view-cart') }}">My Cart</a><i class="fa" style="font-size:24px">&#xf07a;</i>
                    <span class='badge badge-warning' id='lblCartCount'>$COUNT</span></li>
                @endauth
            </ul>
        </div>
    </div>

</header>

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> --}}

{{-- <script src="{{ asset("js/jquery.min.js") }}"></script> --}}

 {{-- <script src="sweetalert2/dist/sweetalert2.min.js"></script> --}}
 {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script> --}}

<script type="text/javascript">


    
    /*$(function () {


                $(document).on("click", ".create-account-btn", function () {

                    $("#loginbtn").modal("hide");

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
                        password_confirmation : {
                            required : true,
                            equalTo : "#password",
                        },
                        terms : {
                            required: true
                        }
                    },
                    submitHandler: function () {

                        alert("hello");

                        $(".user-register-btn").attr("disabled", true);

                        $(".user-register-btn").html("processing... <i class='fas fa-spinner fa-spin'></i>");

                        $.ajax({

                            url: " route('user-register') ",
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
                                    $(".user-register-btn").attr("disabled", false);
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

                                            window.location.href = " route('product') ";
                                           
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

                            url: " route('user-login') ",
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


                                        window.location.href = " route('product') ";

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
                });*/




</script>





                {{--- end --}}




{{-- <script type="text/javascript">

    $(function() {

        $(".register-data").submit(function (e){
            //console.log("hello");

            e.preventDefault();

            var form = $(this); 
            
            /*console.log(form);

            return;*/

            /*$(document).ajaxStart(function(){

                $("#action").attr("disabled", false);

                $(".submit").html(" Processing... <i class='fas fa-spinner fa-spin'></i>");


            });

            $(document).ajaxComplete(function(){
        
                $(".submit").html('Submit');
            }); */      
           

                    $.ajax({
     
                        url: "{{ route('user-register') }}",
                        type: "POST",
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        

                        success: function(data){

                                
                            swal({
                                title : "Hurray!",
                                text: "Thank You for Register  with Us",
                                type: "success",
                                confirmButtonColor: '#DD6B55',
                            })
                                .then(() => {

                                    //$("#action").attr("disabled", false);

                                    $(".register-data #name").val("");

                                    $(".register-data #email").val("");

                                    $(".register-data #type").val("");

                                    $(".register-data #password").val("");
                                });

                        }
                    });
                
        });

    });
    


</script>
 --}}

