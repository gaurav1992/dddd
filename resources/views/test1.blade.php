@extends('frontend.common')
@section('content')
<br/><br/><br/><br/><br/>
<style type="text/css">
.testing{
	margin-top: 10%;
}
</style>
<div class="row testing">
        <div class="col-md-6 col-md-offset-3">
        	<h1>Hello {{ Auth::user()->first_name }}</h1>
        </div>
    </div>
@endsection