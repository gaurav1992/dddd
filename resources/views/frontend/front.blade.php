<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DeziNow</title>

        <!-- CSS And JavaScript -->
        {!! HTML::style('public/css/bootstrap.min.css') !!}
        {!! HTML::style('public/css/frontend.css') !!}
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2">
                    </div>
                    <div class="col-sm-8">
                        @yield('form')
                    </div>
                    <div class="col-sm-2">
                    </div>
                </div>
            </div>
        </div>

        @yield('content')
        @yield('sidebar')
        
    {!! HTML::script('public/js/jquery-2.1.4.min.js'); !!}
    {!! HTML::script('public/js/bootstrap.min.js'); !!}
    {!! HTML::script('public/js/frontend.js'); !!} 
    </body>
</html>