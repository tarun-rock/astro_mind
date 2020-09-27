
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

          <div class="container-fluid">

            <div class="col-md-8">
              <div class="card">
                <div class="card-body">
                    {{-- <h4 class="card-title page-heading">
                        List Of Contact Us
                        <a class="btn btn-info" style="float: right;" href="{{ url('contact-us/create') }}">Add Contact Us</a>
                    </h4> --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered             show-child-rows" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Product Title</th>
                                    <th>Product NAme</th>
                                    <th>Product Image</th>
                                    <th>Price</th>
                                    <th>Name</th>
                                        
                                </tr>
                            </thead>
                            
                        </table>
                    </div>
                </div>
              </div>
            </div>
            
          </div>
      
           <!-- .col-md-8 -->

          

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

      $(function() {
        
        var contactUsTable = $('.show-child-rows').DataTable({

            "ordering": false,
            "processing": true,
            "serverSide": true,

            "ajax": {

                        "data": function (d) { 

                        d._token = "{{ csrf_token() }}";                 
                    
                    }
            },

            columns: [
            {data: 'id', name: 'id'},
            {data: 'prod_title', name: 'prod_title'},
            {data: 'prod_name', name: 'prod_name'},
            {data: 'prod_image', name: 'prod_image'},
            {data: 'price', name: 'price'},
            {data: 'name', name: 'name'}
            
            ],
            /*"order": [
                    [1, 'asc']
                ]*/

        });
      }

      </script>



    @endsection

    
  
				

   