<header class="main-header">
        <!-- Logo -->
       

           <a href="{!! url('/') !!}" target="_blank" class="logo">
             <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>Dezi</b></span>
             <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Dezi</b>Now</span>
       
            </a>
      
      
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
             
             
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                 <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-user"></i>
                    <span>{!! Auth::user()->first_name.' '.Auth::user()->last_name !!} <small>({!! Auth::user()->email !!})</small><i class="caret"></i></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    
                    <li class="user-header bg-light-blue">

                        <!-- <img src="{!! Auth::user()->gravatar(100) !!}" class="img-circle" alt="User Image"/> -->
                        <img src="{!! asset('') !!}/{!! Auth::user()->profile_pic !!}" class="img-circle" alt="User Image"/>
                        <p>
                            {!! Auth::user()->name !!}
                            <small>Member since {!! Auth::user()->created_at->format('M, Y') !!}</small>
                        </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body hidden">
                        <div class="col-xs-4 text-center">
                            <a href="#">Followers</a>
                        </div>
                        <div class="col-xs-4 text-center">
                            <a href="#">Sales</a>
                        </div>
                        <div class="col-xs-4 text-center">
                            <a href="#">Friends</a>
                        </div>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                     
                        <div class="pull-right">
                            <a href="{!! route('admin.logout') !!}" class="btn btn-default btn-flat">Sign out</a>
                        </div>
                    </li>
                </ul>

                </li>
                 <!-- Control Sidebar Toggle Button -->
              <!--li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
              </li-->
            </ul>
          </div>
        </nav>
</header>
