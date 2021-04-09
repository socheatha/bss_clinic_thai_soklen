

<!-- Navbar -->
<nav class="main-header navbar navbar-expand {{ ((Auth::user()->setting()->navbar_color != '')? Auth::user()->setting()->navbar_color : 'navbar-white navbar-light') }}">
	<!-- Left navbar links -->
	<ul class="navbar-nav">
		<li class="nav-item btn">
			<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
		</li>
	</ul>

	<ul class="navbar-nav ml-auto">
		<li class="nav-item dropdown">
			<a class="nav-link pt-3" data-toggle="dropdown" href="#">
				<i class="flag-icon flag-icon-{{ ((session('locale') == 'en')? 'us' : 'kh' ) }}"></i>
			</a>
			<div class="dropdown-menu dropdown-menu-right p-0 mt-0 rounded-0">
				<a href="{{ route('locale', 'kh') }}" class="dropdown-item {{ ((session('locale') == 'kh')? 'active' : '' ) }}">
					<i class="flag-icon flag-icon-kh mr-2"></i> @lang('label.lang.khmer')
				</a>
				<a href="{{ route('locale', 'en') }}" class="dropdown-item {{ ((session('locale') == 'en')? 'active' : '' ) }}">
					<i class="flag-icon flag-icon-us mr-2"></i> @lang('label.lang.english')
				</a>
			</div>
		</li>
		<li class="nav-item dropdown user-menu btn">
			<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
				<img src="/images/users/{{ Auth::user()->image }}" class="user-image img-circle" alt="User Image">
				<span class="d-none d-md-inline">{{ Auth::user()->first_name .' '. Auth::user()->last_name }}</span>
			</a>
			<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
				<!-- User image -->
				<li class="user-header bg-primary">
					<img src="/images/users/{{ Auth::user()->image }}" class="img-circle elevation-2" alt="User Image">

					<p>
						{{ Auth::user()->first_name .' '. Auth::user()->last_name .' - '. Auth::user()->position }}
						<small>Register since : {{ Auth::user()->created_at->format('d M Y') }}</small>
					</p>
				</li>
				<!-- Menu Footer-->
				<li class="user-footer">
					<a href="{{ route('user.profile') }}" class="btn btn-info btn-flat"><i class="fa fa-user"></i> &nbsp;{{ __('label.buttons.profile') }}</a>
					<a class="btn btn-danger btn-flat float-right" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							<i class="fa fa-sign-out-alt"></i> &nbsp;{{ __('label.buttons.sign_out') }}</a>
					</a>

					<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							@csrf
					</form>
				</li>
			</ul>
		</li>
	</ul>

</nav>
<!-- /.navbar -->