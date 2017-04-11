<div class="col-md-4 padleftzero"> 
       <div class="left-sec">
        <div class="edit-user">
        <?php
         $id = Auth::id();
         $driver_profile_data = DB::table('dn_users_data')->select('*')->where('user_id', $id)->first();
         $driver_profile_img = $driver_profile_data->driver_profile_pic;
        ?>
          <?php if($driver_profile_img !=''){ ?>
              <img src="{!! asset($driver_profile_img) !!}" class="img-responsive center-block thumbnail" alt="#"/>
          <?php }else{ ?>
              <img src="{!! asset('public/images/passanger.png') !!}" class="img-responsive center-block thumbnail" alt="#"/>
          <?php } ?> 
          {{$myData['first_name'] }} <br> {{ $myData['email']}}    
        </div>
        <ul class="date-meta mtop-30">
          <li> Joining Date <span>{!! $newjoinind_date; !!}</span></li>  
          <li> Last Ride <span>@if(!empty($myData['last_id'])) {{@$myData['last_id'] }} @else - - @endif</span></li> </ul>
          <!-- <li><a href="{!! asset('user-driver/viewdocument') !!}" >view driver application</a></li> -->
          </ul>
            <ul class="profile-meta">
              <li class="{!! Request::is('editdriver') ? 'active' : '' !!}"><a href="{!! asset('/editdriver') !!}">Profile</a></li>
              <li class="{!! Request::is('user-driver/earning') ? 'active' : '' !!}"><a href="{!! asset('user-driver/earning') !!}">Earning</a></li>
              <li class="{!! Request::is('user-driver/payments') ? 'active' : '' !!}"><a href="{!! asset('user-driver/payments') !!}">Payments</a></li>
              <li class="{!! Request::is('user-driver/referhistory') ? 'active' : '' !!}"><a href="{!! asset('user-driver/referhistory') !!}">Referral History</a></li>
              <li class="{!! Request::is('user-driver/faqd') ? 'active' : '' !!}"><a href="{!! asset('user-driver/faqd') !!}">FAQs</a></li>
              <li class="{!! Request::is('user-driver/tierlevel') ? 'active' : '' !!}"><a href="{!! asset('user-driver/tierlevel') !!}">Rewards</a></li>
			       <li class="{!! Request::is('editpassenger') ? 'active' : '' !!}"><a href="{!! asset('/editpassenger') !!}">Switch to passenger mode </a></li>
            </ul>
      </div>
    </div>