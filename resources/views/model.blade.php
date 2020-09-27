@extends('layout.app')

@section('title', 'astromind')

@section('content')

<input type="submit" name="button" value="rock" id="rock">



@endsection


@section('script_extra')

    <script type="text/javascript">
      
      	$(document).ready(function(){
      
      $("#rock").on('click',function(){
      
        alert('done');
      });
    
    });

    </script>


@endsection