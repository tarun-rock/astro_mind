
@extends('layout.app')

@section('title', 'astromind')

@section('content')


<div class="container">
  
  <div class="row">

    @csrf

    @auth

        @foreach($first as $value)

        <div class="block-21 mb-4 d-flex">

            <div class="col-md-6">
                <div class="dec-info-outer">
                    <a href="{{ url('single-product/'. $value->id) }}">
                
                    <img src="{{ $value->prod_image }}" class="img"/>
                    <div class="dec-info">
                        <label class="bg-danger text-uppercase text-white pt-1 pb-1 pr-2 pl-2">{{ $value->prod_title }}</label>
                        <h3>
                            
                            <strong>
                                {{ $value->prod_name }}
                            </strong>

                        </h3>
                    </div>
                    </a>


                </div>
                <br/>

                    <input type="submit" value="Add To Cart" class="btn btn-primary addToCart" data-id="{{ $value->id }}">
                   
                    <input type="submit" value="buy_now" class="btn btn-primary buy_now  ml-4">
                    
                    <div class="right pb-4 pl-6">
                          
                            <strong>
                                Rs.{{ $value->price }}
                            </strong>
                    </div>
                    {{-- <i class="fa" style="font-size:24px">&#xf07a;</i>
                        <span class='badge badge-warning' id='lblCartCount'> 5 </span> --}}
            </div>  
        </div>

        @endforeach
    @endauth
    </div>

</div>
	
@endsection  

@section('script_extra')

<script src="{{ asset('back/assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script type="text/javascript">
    
$(function(){

    $(".addToCart").on('click',function (){

        var id = $(this).data('id');    

        if(id != ""){

            $.ajax({

                url: "{{ url('cart') }}/" + id,
                type: "GET",
                headers: 
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

            success: function(response){
            
                if(response)
                {

                    swal(
                            "Done!",
                            "It was succesfully Added in your Cart!", 
                            "success"
                        );

                } 
                else
                {
                    swal("Error", "Already Added in your Cart", "warning");
                }

            }

          });
        }

    });

    $(".buy_now").on('click' , function (e){

        var totalAmount = $(this).attr("data-amount");
        var product_id =  $(this).attr("data-id");
        var options = {
           "key": "rzp_test_8W7uuQ3lmOxd6b",
           "amount": (100), // 2000 paise = INR 20
           "name": "Tutsmake",
           "description": "Payment",
           "image": "https://www.tutsmake.com/wp-content/uploads/2018/12/cropped-favicon-1024-1-180x180.png",
           "handler": function (response){
                 $.ajax({
                   url: SITEURL + 'paysuccess',
                   type: 'post',
                   data: {
                    razorpay_payment_id: response.razorpay_payment_id , 
                     totalAmount : totalAmount ,
                     product_id : product_id,
                   }, 
                   success: function (msg) {
          
                       window.location.href = SITEURL + 'razor-thank-you';
                   }
               });
             
           },
          "prefill": {
               "contact": '9517232258',
               "email":   'increadibletarun07@gmail.com',
           },
           "theme": {
               "color": "red"
           }
         };
         var rzp1 = new Razorpay(options);
         rzp1.open();
         e.preventDefault();

    });

});







</script>



@endsection
  

 