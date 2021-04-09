
<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 {{ ((Auth::user()->setting()->legacy == '1')? '' : 'sidebar-light-indigo') }} {{ ((Auth::user()->setting()->sidebar_color != '')? Auth::user()->setting()->sidebar_color : 'sidebar-dark-primary') }}">
	<!-- Brand Logo -->
	<a href="index3.html" class="brand-link">
		<img src="/images/setting/logo.png" alt="{{ Auth::user()->setting()->clinic_name }}" class="brand-image img-circle elevation-3">
		<span class="brand-text font-weight-light">{{ Auth::user()->setting()->clinic_name_kh }}</span>
	</a>

	<!-- Sidebar -->
	<div class="sidebar">
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column {{ ((Auth::user()->setting()->legacy == '1')? 'nav-legacy nav-flat' : 'sidebar-light-indigo') }} nav-child-indent text-sm" data-widget="treeview" role="menu" data-accordion="false">

				<!-- Add icons to the links using the .nav-icon class
						with font-awesome or any other icon font library -->
				<li class="nav-item">
					<a href="{{ route('home') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'home' )? 'active':'') }}">
						<i class="fa fa-home nav-icon"></i>
						<p>{{ __('sidebar.home.main') }}</p>
					</a>
				</li>



				{{-- User Management --}}
				@canany(['Patient Index', 'Patient Create', 'Patient Edit', 'Patient Delete', 'Medicine Index', 'Medicine Create', 'Medicine Edit', 'Medicine Delete'])
				
					<li class="nav-header">{{ __('sidebar.header.main_data') }}</li>

					@can('Invoice Index', 'Invoice Create', 'Invoice Edit', 'Invoice Delete')
					<li class="nav-item">
						<a href="{{ route('invoice.create') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'invoice' )? 'active':'') }}">
							<i class="fa fa-file-invoice nav-icon"></i>
							<p>{{ __('sidebar.invoice.main') }}</p>
						</a>
					</li>
					@endcan

					@can('Prescription Index', 'Prescription Create', 'Prescription Edit', 'Prescription Delete')
					<li class="nav-item">
						<a href="{{ route('prescription.create') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'prescription' )? 'active':'') }}">
							<i class="fa fa-file-medical-alt nav-icon"></i>
							<p>{{ __('sidebar.prescription.main') }}</p>
						</a>
					</li>
					@endcan

					@can('Labor Index', 'Labor Create', 'Labor Edit', 'Labor Delete')
					<li class="nav-item">
						<a href="{{ route('labor.create') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'labor' )? 'active':'') }}">
							<i class="las la-flask nav-icon"></i>
							<p>{{ __('sidebar.labor.main') }}</p>
						</a>
					</li>
					@endcan

					@canany(['Labor Service Index','Labor Service Create', 'Labor Service Edit', 'Labor Service Delete', 'Labor Category Index','Labor Category Create', 'Labor Category Edit', 'Labor Category Delete'])
				
					<li class="nav-item has-treeview {{ ((in_array(Auth::user()->sidebarActive(), [ 'labor_service', 'labor_category' ]))? 'menu-open':'') }}">
						<a href="#" class="nav-link {{ ((in_array(Auth::user()->sidebarActive(), [ 'labor_service', 'labor_category' ]))? 'active':'') }}">
							<i class="nav-icon fas fa-vial"></i>
							<p>
								{{ __('sidebar.labor_service.main') }}
								<i class="right fas fa-angle-left"></i>
							</p>
						</a>
						<ul class="nav nav-treeview">
							@can('Labor Service Index', 'Labor Service Create', 'Labor Service Edit', 'Labor Service Delete')
							<li class="nav-item">
								<a href="{{ route('labor_service.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'labor_service' )? 'active':'') }}">
									<i class="far fa-circle nav-icon"></i>
									<p>{{ __('sidebar.labor_service.sub.labor_service') }}</p>
								</a>
							</li>
							@endcan
							@can('Labor Category Index', 'Labor Category Create', 'Labor Category Edit', 'Labor Category Delete')
							<li class="nav-item">
								<a href="{{ route('labor_category.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'labor_category' )? 'active':'') }}">
									<i class="far fa-circle nav-icon"></i>
									<p>{{ __('sidebar.labor_service.sub.labor_category') }}</p>
								</a>
							</li>
							@endcan
	
						</ul>
					</li>
					
					@endcan

					@if (count(Auth::user()->echoDefaultDescriptions()->get()))
						@canany(['Echo Index','Echo Create', 'Echo Edit', 'Echo Delete','Echo Print'])
					
						<li class="nav-item has-treeview {{ ((in_array(Auth::user()->sidebarActive(), [ 'echoes' ]))? 'menu-open':'') }}">
							<a href="#" class="nav-link {{ ((strpos('echoes', Auth::user()->sidebarActive()) !== false)? 'active':'') }}">
								<i class="nav-icon fas fa-file-video"></i>
								<p>
									{{ __('sidebar.echo.main') }}
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">

							@foreach (Auth::user()->echoDefaultDescriptions()->get() as $kkey => $echo_default )
								@can('Echo Index', 'Echo Create', 'Echo Edit', 'Echo Delete')
								<li class="nav-item">
									<a href="{{ route('echoes.create', $echo_default->slug) }}" class="nav-link {{ (($echo_default->slug == @$type )? 'active':'') }}">
										<i class="far fa-circle nav-icon"></i>
										<p>{{ $echo_default->name }}</p>
									</a>
								</li>
								@endcan
									
							@endforeach
		
							</ul>
						</li>
						
						@endcan
					@endif

					@can('Patient Index', 'Patient Create', 'Patient Edit', 'Patient Delete')
					<li class="nav-item">
						<a href="{{ route('patient.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'patient' )? 'active':'') }}">
							<i class="fa fa-user-injured nav-icon"></i>
							<p>{{ __('sidebar.patient.main') }}</p>
						</a>
					</li>
					@endcan

					@can('Medicine Index', 'Medicine Create', 'Medicine Edit', 'Medicine Delete')
					<li class="nav-item">
						<a href="{{ route('medicine.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'medicine' )? 'active':'') }}">
							<i class="fa fa-pills nav-icon"></i>
							<p>{{ __('sidebar.medicine.main') }}</p>
						</a>
					</li>
					@endcan

					@can('Service Index', 'Service Create', 'Service Edit', 'Service Delete')
					<li class="nav-item">
						<a href="{{ route('service.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'service' )? 'active':'') }}">
							<i class="fa fa-concierge-bell nav-icon"></i>
							<p>{{ __('sidebar.service.main') }}</p>
						</a>
					</li>
					@endcan

					@can('Doctor Index', 'Doctor Create', 'Doctor Edit', 'Doctor Delete')
					<li class="nav-item">
						<a href="{{ route('doctor.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'doctor' )? 'active':'') }}">
							<i class="fa fa-user-md nav-icon"></i>
							<p>{{ __('sidebar.doctor.main') }}</p>
						</a>
					</li>
					@endcan

					@can('Usage Index', 'Usage Create', 'Usage Edit', 'Usage Delete')
					<li class="nav-item">
						<a href="{{ route('usage.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'usage' )? 'active':'') }}">
							<i class="fa fa-hand-holding-water nav-icon"></i>
							<p>{{ __('sidebar.usage.main') }}</p>
						</a>
					</li>
					@endcan

				@endcan


				{{-- User Management --}}
				@canany(['Echo Default Description Index', 'Echo Default Description Create', 'Echo Default Description Edit', 'Echo Default Description Delete', 'User Index', 'User Create', 'User Edit', 'User Delete', 'User Assign Role', 'User Assign Permission', 'Role Index', 'Role Create', 'Role Edit', 'Role Delete', 'Role User Assign', 'Permission Index', 'Permission Create', 'Permission Edit', 'Permission Delete', 'Permission Role Assign', 'Permission User Assign', 'Province Index', 'Province Create', 'Province Edit', 'Province Delete', 'District Index', 'District Create', 'District Edit', 'District Delete','Usage Index', 'Usage Create', 'Usage Edit', 'Usage Delete'])
				
					<li class="nav-header">{{ __('sidebar.header.other_management') }}</li>

					@can('Echo Default Description Index', 'Echo Default Description Create', 'Echo Default Description Edit', 'Echo Default Description Delete')
					<li class="nav-item">
						<a href="{{ route('echo_default_description.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'echo_default_description' )? 'active':'') }}">
							<i class="far fa-file-video nav-icon"></i>
							<p>{{ __('sidebar.echo_default_description.main') }}</p>
						</a>
					</li>
					@endcan

					@can('User Index', 'User Create', 'User Edit', 'User Delete', 'User Assign Role', 'User Assign Permission')
					<li class="nav-item">
						<a href="{{ route('user.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'user' )? 'active':'') }}">
							<i class="fa fa-user nav-icon"></i>
							<p>{{ __('sidebar.user.sub.user') }}</p>
						</a>
					</li>
					@endcan

					@can('Role Index', 'Role Create', 'Role Edit', 'Role Delete', 'Role User Assign')
					<li class="nav-item">
						<a href="{{ route('role.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'role' )? 'active':'') }}">
							<i class="fa fa-user-graduate nav-icon"></i>
							<p>{{ __('sidebar.user.sub.role') }}</p>
						</a>
					</li>
					@endcan

					@can('Permission Index', 'Permission Create', 'Permission Edit', 'Permission Delete', 'Permission Role Assign', 'Permission User Assign', 'Usage Index', 'Usage Create', 'Usage Edit', 'Usage Delete')
					<li class="nav-item">
						<a href="{{ route('permission.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'permission' )? 'active':'') }}">
							<i class="fa fa-shield-alt nav-icon"></i>
							<p>{{ __('sidebar.user.sub.permission.sub.permission') }}</p>
						</a>
					</li>
					@endcan

					@can('Setting Index')
					<li class="nav-item">
						<a href="{{ route('setting.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'setting' )? 'active':'') }}">
							<i class="fa fa-cogs nav-icon"></i>
							<p>{{ __('sidebar.setting.main') }}</p>
						</a>
					</li>
					@endcan

					@can('Province Index', 'Province Create', 'Province Edit', 'Province Delete')
					<li class="nav-item">
						<a href="{{ route('province.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'province' )? 'active':'') }}">
							<i class="fa fa-map nav-icon"></i>
							<p>{{ __('sidebar.province.main') }}</p>
						</a>
					</li>
					@endcan

					@can('District Index', 'District Create', 'District Edit', 'District Delete')
					<li class="nav-item">
						<a href="{{ route('district.index') }}" class="nav-link {{ ((Auth::user()->sidebarActive() == 'district' )? 'active':'') }}">
							<i class="fa fa-map-marker-alt nav-icon"></i>
							<p>{{ __('sidebar.district.main') }}</p>
						</a>
					</li>
					@endcan

				@endcan

			</ul>
			<br/>
			<br/>
			<br/>
			<br/>
			<br/>
		</nav>
		<!-- /.sidebar-menu -->
	</div>
	<!-- /.sidebar -->
</aside>