
@extends('layout.app')

@section('title', 'astromind')

@section('content')


<div class="container">

    <div class="row">

     {{--     @php
      print_r($first);
      die;
    @endphp --}}
    

        @foreach($first as $value)

        <div class="block-21 mb-4 d-flex">

            <div class="col-md-6">
                <div class="dec-info-outer">
                    <a href="{{ url('post/'. $value->id) }}">
                        {{-- <hr/> --}}
                        <img src="{{ $value->image }}" class="img"/>
                        <div class="dec-info">

                            {{-- @foreach($first->Category as $cat)

                                <label class="bg-danger text-uppercase text-white pt-1 pb-1 pr-2 pl-2">{{ $cat->name }}</label>
                            
                            @endforeach --}}

                            <h3 style="color: red">
                                <strong>
                                    {{ $value->title }}
                                </strong>

                            </h3>

                        </div>
                    </a>
                </div>
            </div>

        </div>

        @endforeach

    </div>
    
    <br/>
            <ul class="pager">
                <li class="next">
                    {{ $first->links() }}
                </li>
            </ul>

    {{-- <div class="d-lg-flex sportscard-outer">
    </div> --}}

    {{-- <div class="d-lg-flex sportscard-outer">

        <div class="row">

            @foreach($first as $val)

            <div class="col-md-6  mb-3">
                <div class="dec-info-outer">
                    <a href="{{ url('post/'. $val->slug) }}">
                <img src="{{ $val->image }}" class="img-fluid"/>
                <div class="dec-info">
                    <label class="bg-danger text-uppercase text-white pt-1 pb-1 pr-2 pl-2">{{ $val->name }}</label>
                    <h3 class="text-white">
                        <strong>
                            {{ $val->title }}
                        </strong>
                    </h3>
                </div>
                    </a>
                </div>
            </div>

            @endforeach
            
        </div>
    </div> --}}


   {{--  <div class="d-lg-flex sportscard-outer">

        <div class="row">

            @foreach($first as $v)

            <div class="col-md-12">
                <div class="dec-info-outer">
                    <a href="{{ url('post/'. $v->slug) }}">
                <img src="{{ $v->image }}" class="img-fluid"/>
                <div class="dec-info">
                    <label class="bg-danger text-uppercase text-white pt-1 pb-1 pr-2 pl-2">{{ $v->name }}</label>
                    <h3 class="text-white">
                        <strong>
                            {{ $v->title }}
                        </strong>
                    </h3>
                </div>
                    </a>
                </div>
            </div>

            @endforeach
            
        </div>

    </div> --}}
    
</div>

<br/>

@endsection