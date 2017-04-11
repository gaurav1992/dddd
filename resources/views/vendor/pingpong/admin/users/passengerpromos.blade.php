
@extends($layout)
@section('title', 'passenger Promos')

@section('content')
          {!! Form::open(array('method'=>'POST',id' => 'addUserForm','name'=>'addUserForm','files' => true)) !!}
          <input type="text" value="" name="reffreal_amount" id="reffreal_amount">
          <input type="text" value="" name="reffreal_amount" id="birthday_amount">
          <input type="text" value="" name="reffreal_amount" id="birthday_amount">
          <input type="text" value="">
          <input type="submit" value="Submit">
         {!! Form::close() !!}

@stop
@stop
