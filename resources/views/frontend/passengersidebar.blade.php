<div class="col-md-4 padleftzero"> 
  <div class="left-sec">
    <div class="edit-user">
      <?php if($myData['profile_pic'] !=''){ ?>
          <img src="{!! asset($myData['profile_pic']) !!}" class="img-responsive center-block thumbnail" alt="#"/>
      <?php }else{ ?>
          <img src="{!! asset('public/images/passanger.png') !!}" class="img-responsive center-block thumbnail" alt="#"/>
      <?php } ?> 
      {{$myData['first_name'] }}    {{$myData['last_name'] }}<br> {{ $myData['email']}}    
    </div>
    <ul class="date-meta mtop-30">
      <li> Joining Date <span>{!! $newjoinind_date; !!}</span></li>  
      <li> Last Ride <span>@if(!empty($myData['last_id'])) {{@$myData['last_id'] }} @else - - @endif</span></li> </ul>
        <ul class="profile-meta">
          <li class="{!! Request::is('editpassenger') || Request::is('editpassenger')  ? 'active' : '' !!}"><a href="{!! asset('/editpassenger') !!}">Profile</a></li>
          <?php if($myData['profile_status'] == '1'){ ?>
            <li class="{!! Request::is('passenger/favoriteplaces') ? 'active' : '' !!}"><a href="{!! asset('/passenger/favoriteplaces') !!}">Favorite Places</a></li>
            <li class="{!! Request::is('passenger/yourcars') ? 'active' : '' !!}"><a href="{!! asset('/passenger/yourcars') !!}">Your Cars</a></li>
            <li class="{!! Request::is('passenger/triphistory') ? 'active' : '' !!}"><a href="{!! asset('/passenger/triphistory') !!}">Trip History</a></li>
            <li class="{!! Request::is('passenger/paymentpassenger') ? 'active' : '' !!}"><a href="{!! asset('/passenger/paymentpassenger') !!}">Payments</a></li>
            <li class="{!! Request::is('passenger/referhistorypassenger') ? 'active' : '' !!}"><a href="{!! asset('/passenger/referhistorypassenger') !!}">Referral History</a></li>
            <?php
              $id = Auth::id();
              $user_detail = $users = DB::table('role_user')
              ->select(array('dn_users.*'))
              ->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')  
              ->where('dn_users.id' ,$id) 
              ->where('role_id' ,'4')
              ->first();
             
              
              if(empty($user_detail)) {
            ?>
             <li class="{!! Request::is('becomedriver') ? 'active' : '' !!}"><a href="{!! asset('/becomedriver') !!}">Become a Driver </a></li>
               
            <?php } else { ?> 
            <li class="{!! Request::is('becomedriver') ? 'active' : '' !!}"><a href="{!! asset('/editdriver') !!}">Switch to Driver </a></li> 
             
            <?php } ?>
          <?php } ?>  
        </ul>
  </div>
</div>