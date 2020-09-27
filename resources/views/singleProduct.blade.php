
@extends('layout.app')

@section('title', 'astromind')

@section('content')

  @csrf

        {{-- facebook plugin --}}

  {{-- <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v6.0&appId=2874207425996262&autoLogAppEvents=1"></script> --}}

          {{-- ////////////////// --}}

		<section class="ftco-section ftco-degree-bg">

      <div class="container">
      
        <div class="row" >
      
          <div class="col-md-8 ftco-animate">

             

             <h2 class="mb-3">{{ $single->prod_title }}</h2>

              <p>{{ $single->prod_name }}</p>

              <h1>views:{{ $single->getViewsCountUpto($offset = 0, $limit = 0) }}</h1>
              
              <p>
                <img src="{{ asset("$single->prod_image") }}" alt="" class="img-fluid">
              </p>

              {{-- <a href="">Add tO Cart</a> --}}

              <button type ="button" class="fa fa-cart">Add To Cart</button>
            
            {{-- <div class="fb-comments" data-href="{{ url('/{id}') }}" data-numposts="5" data-width=""></div> --}}
    
            <div class="tag-widget post-tag-container mb-5 mt-5">
              
            </div>  

          </div> <!-- .col-md-8 -->

          <div class="col-md-4 pl-md-5 sidebar ftco-animate">
            <div class="sidebar-box">

              <form method="get" action="{{ route('search') }}" class="search-form" id="search-form">
                <div class="form-group">
                  {{-- <span class="icon icon-search"></span> --}}
                  <input type="text" name="term" class="form-control" placeholder="Type a keyword and hit enter">
                  <input type="submit" id="submit" class="btn btn-success icon icon-search"/>
                </div>
              </form>

            </div>
            
            <div class="sidebar-box ftco-animate">


                  
              <h3 class="heading-3">Recent Blog</h3>
               
                @foreach($first as $value)
                
                <div class="block-21 mb-4 d-flex">
                  <a href="{{ url('single-product/'. $value->id) }}" class="blog-img mr-4"><img src="{{ asset("$value->prod_image") }}" class="back"></a>
                  <div class="text">
                    <h3 class="heading"><a href="{{ url('single-product/'. $value->id) }}">{{ $value->body }}</a></h3>
                    <div class="meta">

                      <div><a href="#"><span class="icon-calendar"></span> {{ \Carbon\Carbon::parse($value->created_at)->diffForHumans() }}</a></div>

                      <div><a href="#"><span class="icon-person"></span> Admin</a></div>
                      <div><a href="#"><span class="icon-chat"></span> 19</a></div>
                    </div>
                  </div>


                </div>

                @endforeach

            </div>

          </div>
        </div>


      </div>

    </section> 

    @endsection  

    @section('script_extra')

    <script type="text/javascript">
      
      $(document).ready(function() {

        $("#search-form").submit(function(){

          //e.preventDefault();

         // console.log("hello");

          var form = $(this);

          //console.log(form);


          $.ajax({

            url:"{{ url('search') }}",
            type:"get",
            data: form.serialize(),
            headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },

              success: function(data){


                //console.log('hello');


              }

          });

        });

      });




    </script>

    @endsection
  
				

   