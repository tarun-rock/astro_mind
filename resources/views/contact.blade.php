@extends('layout.app')

@section('title', 'astromind')


@section('content')


<div class="banner" style="background-image: url('images/img.png')">
</div>
<div class="contentinner">

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <label class="d-block text-uppercase text-center solid text-white bg-danger btn-lg m-0">Contact Us</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="bg-white">
                    <br/>
                    <br/>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <address class="mb-5">
                                    <h4><strong>India</strong></h4>
                                    <p>
                                        Astromind Vastu & Solutions.<br/>
                                        Krishna Nagar Joura Fatak, Amritsar , Punjab
                                    </p>
                                </address>
                               {{--  <address class="mb-5">
                                    <h4><strong>India, Mohali</strong></h4>
                                    <p>
                                        Gamapp SportsWizz Tech Pvt. Ltd.<br/>
                                        E-309 4th Floor Industrial Area Phase 8-A (Sector 75), Mohali
                                    </p>
                                </address> --}}


                            </div>
                            {{-- <div class="col-md-6">
                                <address class="mb-5">
                                    <h4><strong>India, Bengaluru</strong></h4>
                                    <p>
                                        Gamapp SportsWizz Tech Pvt. Ltd.<br/>
                                        #2470, V.B Plaza, 2nd Cross, 16th C Main, Hal 2nd Stage IndiraNagar, Bengaluru - 560008
                                    </p>
                                </address>
                                <address class="mb-5">
                                    <h4><strong>Seychelles</strong></h4>
                                    <p>
                                        SportsWizz Ltd.
                                        <br/>
                                        Olivier Maradan Building, Olivier Maradan Street, Victoria, Mahe
                                    </p>
                                </address>
                            </div> --}}
                        </div>
                    </div>
                    <hr/>
                    <form method="post" id="formdata">
                    	@csrf

                          {{-- @if(count($errors) > 0)
                                        <div class="alert alert-danger">

                                            <ul>
                                            @foreach($errors->all() as $error)

                                                <li>{{ $error }}</li>
                                            @endforeach
                                            </ul>
                                            
                                        </div>
                                    @endif 

                                    @if(@session('response'))

                                    <div class="col-md-8 text"></div>

                                    @endif --}}
                    <div class="d-flex justify-content-center">
                        <div class="col-md-6">
                            <h2 class="text-uppercase text-center">Get In Touch</h2>
                            <br/>
                            <div class="form-group">
                                <input type="text" placeholder="Your Name" name="name" id="name" class="form-control" required/>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <input type="email" name="email" id="email" placeholder="Your Email*" class="form-control" required/>
                                </div>
                                <div class="col-md-6 form-group">
                                <input type="number" name="phone" id="phone" placeholder="Your Phone" class="form-control" >


                                </div>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="message" id="message" rows="4" required placeholder="Your Message*"></textarea>
                            </div>
                            <div class="form-group text-center">

                                <input type="hidden" name="hidden_id" name="hidden_id">

                                <input type="submit" name="submit" id="action" class="btn btn-lg btn-outline-primary text-uppercase submit" value="submit">

                                {{-- <input type="submit" name="action" id="action" class="btn btn-lg btn-outline-primary text-uppercase submit" value="Add"> --}}

                                {{-- <button type="button" class="submit">Submit</button> --}}
                            </div>
                        </div>

                    </div>
                    <br/>
                    <br/>

                </div>
            </div>
        </div>
    </div>
</div>

<br/>

</form>

@endsection

 @section('script_extra')

 <script src="sweetalert2/dist/sweetalert2.min.js"></script>
 {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script> --}}


<script type="text/javascript">

    $(function() {

        $("#formdata").submit(function (e){
            //console.log("hello");

            e.preventDefault();

            var form = $(this); 

            $(document).ajaxStart(function(){

                $("#action").attr("disabled", false);

                $(".submit").html(" Processing... <i class='fas fa-spinner fa-spin'></i>");


            });

            $(document).ajaxComplete(function(){
        
                $(".submit").html('Submit');
            });       
           

                    jQuery.ajax({
     
                        url: "{{ route('contact-us') }}",
                        type: "POST",
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        

                        success: function(data){

                                
                            swal({
                                title : "Hurray!",
                                text: "Thank You for contact  with Us",
                                type: "success",
                                confirmButtonColor: '#DD6B55',
                            })
                                .then(() => {

                                    //$("#action").attr("disabled", false);

                                    $("#formdata #name").val("");

                                    $("#formdata #email").val("");

                                    $("#formdata #phone").val("");

                                    $("#formdata #message").val("");
                                });

                        }
                    });
                
        });

    });
    


</script>



@endsection 


