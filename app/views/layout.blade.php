<!DOCTYPE html>
<html lang="en" >
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/x-icon" href="{{Config::get('kblis.favicon') }}">
        <link rel="stylesheet" type="text/css" href="{{URL::asset('css/ui-lightness/jquery-ui-min.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap.min.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/layout.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{URL::asset('css/select2.min.css')}}" > 
        <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap-theme.min.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{URL::asset('css/dataTables.bootstrap.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{URL::asset('css/flatpickr.min.css')}}" >
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/select2-bootstrap.min.css') }}" >       

        <script src="{{URL::asset('js/jquery.js')}}"></script> 
        <script src="{{URL::asset('js/jquery-ui-min.js')}}"></script>
        <script src="{{URL::asset('js/bootstrap.min.js')}}"></script>
        <script src="{{URL::asset('js/flatpickr.min.js')}}"></script>
        <script src="{{URL::asset('js/jquery.dataTables.js')}}"></script>
        <script src="{{URL::asset('js/dataTables.bootstrap.js')}}"></script>
        <script src="{{URL::asset('js/jQuery.print.js')}}"></script>
        <script src="{{URL::asset('js/html.sortable.min.js')}}"></script>       
        <script src="{{URL::asset('js/select2.min.js')}}"></script>
        <script src="{{URL::asset('js/script.js')}}"></script>

        


        <title>{{ Config::get('kblis.name') }} {{ Config::get('kblis.version') }}</title>
    </head>
    <body  class="">
        <div id="wrap">
            @include("header")
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2 sidebar">
                        @include("sidebar")
                    </div>
                    <div class="col-md-10 col-md-offset-2 main" id="the-one-main">
                        @yield("content")
                    </div>
                </div>
            </div>
        </div>
        @include("footer")
<!-- <script>
    $('.select-single').select2();</script> -->
    </body>
</html>
