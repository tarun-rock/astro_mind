
@extends('layout.app')

@section('title', 'astromind')

@section('content')

  @csrf

        {{-- facebook plugin --}}

  <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v6.0&appId=2874207425996262&autoLogAppEvents=1"></script>

          {{-- ////////////////// --}}

		<section class="ftco-section ftco-degree-bg">

      <div class="container">
      
        <div class="row">
      
          <div class="col-md-8 ftco-animate">

              @foreach($post->Category as $cat)
              <small>
                
                <h2 class="mb-3">{{ $cat->name }}</h2>
              </small>

              @endforeach

             <h2 class="mb-3">{{ $post->title }}</h2>

              <h3 class="right">Views: {{ $post->count }}</h3>

              <br/>

              <p>{{ $post->body }}</p>
              
              <p>
                <img src="{{ asset("$post->image") }}" alt="" class="img-fluid">
              </p>
            
            <div class="fb-comments" data-href="{{ url('post/{id}') }}" data-numposts="5" data-width=""></div>
    
            <div class="tag-widget post-tag-container mb-5 mt-5">
              
            </div>  

          </div> <!-- .col-md-8 -->

          <div class="col-md-4 pl-md-5 sidebar ftco-animate">
            <div class="sidebar-box">

              <form method="post" action="{{ route('search') }}" class="search-form" id="search-form">
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
                  <a href="{{ url('post/'. $value->id) }}" class="blog-img mr-4"><img src="{{ asset("$value->image") }}" class="back"></a>
                  <div class="text">
                    <h3 class="heading"><a href="{{ url('post/'. $value->id) }}">{{ $value->body }}</a></h3>
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
  
				

   