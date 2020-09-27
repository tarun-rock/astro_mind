
@extends('layout.app')

@section('title', 'astromind')

@section('content')


<div class="container">
  
  <div class="row">

    {{-- @php
      print_r($getData);
      die;
    @endphp --}}
    

        @foreach($getData as $value)

        <div class="block-21 mb-4 d-flex">

            <div class="col-md-6">
                <div class="dec-info-outer">
                    <a href="{{ url('post/'. $value->id) }}">
                        {{-- <hr/> --}}
                    <img src="{{ $value->image }}" class="img"/>
                    <div class="dec-info">
                        <label class="bg-danger text-uppercase text-white pt-1 pb-1 pr-2 pl-2">{{ $value->title }}</label>
                        <h3 style="color: red"  >
                            <strong>
                                {{ $value->body }}
                            </strong>

                        </h3>
                    </div>
                    </a>
                </div>
            </div>

        </div>

        @endforeach

    </div>

</div>

		
@endsection  
  

 