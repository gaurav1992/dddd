@extends($layout)

@section('content-header')
<h1 style="text-align:center;"> {!! $title or 'Driver Application' !!} </h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop


@section('content')
<div class="single-driver content-part">
  <div class="col-sm-12 pad seaction1-driver">
    <div class="col-sm-3 pad new-image"> <img alt="a picture" id="profile_pic" class="img-thumbnail" src="http://www.mobilytedev.com/deziNow/public/img/memberImages/4b31504ab172931b912d44cbe3e976de6a35c54b.png">
      <p><span>Full Name:</span>Pallav Kaker</p>
      <p><span>Email:</span>p@gmail.com</p>
      <p><span>Phone:</span>123456</p>
      <p><span>DOB:</span>15-03-1992</p>
      <p><span>Aniversary:</span>15-03-1992</p>
      <p><span>Male:</span>27</p>
    </div>
    <div class="col-sm-4 pad">
      <div class="col-sm-6 pad">
        <p><span>User Type:</span>Passanger</p>
        <p><span>User ID:</span>123456</p>
        <div class="address-line">
          <p><span>Address:</span>Line1</p>
          <p><span>Address:</span>Line2</p>
          <p><span>City,State:</span>Zip Code</p>
        </div>
      </div>
      <div class="col-sm-6 pad"> <a href="">View Passanger Proffile</a> </div>
    </div>
    <div class="col-sm-5 pad">
      <div class="col-sm-7 suspend-button">
        <button type="submit" class="btn btn-default">Suspend User Account</button>
      </div>
      <div class="col-sm-5 dateofjoining suspend-button">
        <button type="submit" class="btn btn-default">Approve Driver</button>
        <button type="submit" class="btn btn-default">Disapprove Driver</button>
        <p><span>Join Date:</span>10-10-2015</p>
        <p><span>Date Applied:</span>20-10-2015</p>
      </div>
    </div>
  </div>
  <hr>
  </hr>
  <div class="col-sm-12 seaction1-driver licence-seaction">
    <div class="col-sm-4">
      <p><span>Licence Number:</span>10-10-2015</p>
      <p><span>Licence Number:</span>10-10-2015</p>
    </div>
    <div class="col-sm-4">
      <p><span>Licence Number:</span>10-10-2015</p>
      <p><span>Licence Number:</span>10-10-2015</p>
    </div>
    <div class="col-sm-4"> </div>
  </div>
  <div class="col-sm-12 pad seaction1-driver">
    <div class="pull-left">
      <p><span>More than one accident in the last 3 years?</span>NO</p>
      <p><span>More than one accident in the last 3 years?</span>NO</p>
      <p><span>More than one accident in the last 3 years?</span>NO</p>
      <p><span>More than one accident in the last 3 years?</span>NO</p>
    </div>
    <div class="pull-right">
      <p><span>More than one accident in the last 3 years?</span>NO</p>
      <p><span>More than one accident in the last 3 years?</span>NO</p>
      <p><span>More than one accident in the last 3 years?</span>NO</p>
      <p><span>More than one accident in the last 3 years?</span>NO</p>
    </div>
  </div>
  <div class="col-sm-12 pad">
    <div class="col-sm-4 applicant-box seaction1-driver">
      <p>Lorem Ipsum is simply dummy text of the printing 
        and typesetting industry. Lorem Ipsum has been the industry's standard 
        dummy text ever since the 1500s, when an unknown printer took a galley </p>
    </div>
    <div class="col-sm-8 seaction1-driver">
      <ul class="glyp">
        <span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
        <span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
		<span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
      </ul>
      <ul class="glyp">
        <span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
        <span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
		<span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
      </ul>
    </div>
  </div>
</div>
@stop 