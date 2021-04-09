<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>Login | Clinic</title>

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom-style.css') }}" rel="stylesheet">
	<link href="{{ asset('css/login-style.css') }}" rel="stylesheet">
	<style type="text/css">
		.wrap-input100 .input100.is-invalid{
			box-shadow: 0 0 2px #E53E3E;
			border: 1px solid rgba(229, 62, 62, 0.8);
		}
		small.has-error{
			color: red;
			font-size: 11px;
			padding: 0;
			padding-left: 15px;
			margin-top: -5px;
			display: block;
		}

	</style>
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="/images/log.png" alt="IMG">
				</div>

				<form method="POST" action="{{ route('login') }}" class="login100-form validate-form">
					<span class="login100-form-title">
						Member Login
					</span>

					@error('email')
						<small class="has-error">
							<strong>{{ $message }}</strong>
						</small>
					@enderror
					<div class="wrap-input100 validate-input @error('email') alert-validate @enderror" data-validate = "Valid email is required: ex@abc.xyz">
						<input id="email" class="input100 @error('email') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email" type="text" name="email" placeholder="Email" required>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					@error('password')
						<small class="has-error">
							<strong>{{ $message }}</strong>
						</small>
					@enderror
					<div class="wrap-input100 validate-input @error('password') alert-validate @enderror" data-validate = "Password is required">
						<input id="password" class="input100 @error('password') is-invalid @enderror" autocomplete="current-password" type="password" name="password" placeholder="Password" required>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					<input class="sr-only" type="checkbox" id="remember" checked>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="#">
							Username / Password?
						</a>
					</div>

					<div class="text-center p-t-136">
						<a class="txt2" href="#">
							Create your Account
							<i class="fa fa-long-arrow-alt-right m-l-5" aria-hidden="true"></i>
						</a>
						@if(!empty(env('GIT_USR')) && !empty(env('GIT_PWD')) && !empty(env('GIT_PATH')) && \AppHelper::IsInternetConnected())
							<br><a class="txt2" href="/login?sync=1">
								@if(\AppHelper::IsProjectHosted())
									Synchronize local project<i class="fa fa-spin fa-sync m-l-5" aria-hidden="true"></i><br><mark>Status : {{ shell_exec('git pull') }}</mark>
								@else
									Synchronize local project<i class="fa fa-spin fa-sync m-l-5" aria-hidden="true"></i><br><mark>Status : {{ shell_exec('git pull https://' . env('GIT_USR') . ':' . env('GIT_PWD') . '@' . env('GIT_PATH')) }}</mark>
								@endif
							</a>
						@endif
					</div>
					@csrf
				</form>
			</div>
		</div>
	</div>
	
	{{-- Javascript --}}
	<script src="{{ asset('js/app.js') }}"></script>
	<script src="{{ asset('js/tilt.jquery.min.js') }}"></script>
	<script type="text/javascript">

		(function ($) {
				"use strict";

				
				/*==================================================================
				[ Validate ]*/
				var input = $('.validate-input .input100');

				$('.validate-form').on('submit',function(){
					var check = true;
					for(var i=0; i<input.length; i++) {
						if(validate(input[i]) == false){
							showValidate(input[i]);
							check=false;
						}
					}
					return check;
				});

				$('.validate-form .input100').each(function(){
					$(this).focus(function(){
						hideValidate(this);
					});
				});

				function validate (input) {
					if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
						if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
							return false;
						}
					}
					else {
						if($(input).val().trim() == ''){
							return false;
						}
					}
				}

				function showValidate(input) {
					var thisAlert = $(input).parent();
					$(thisAlert).addClass('alert-validate');
				}

				function hideValidate(input) {
					var thisAlert = $(input).parent();

					$(thisAlert).removeClass('alert-validate');
				}
				
				

		})(jQuery);
	</script>

</body>
</html>