<div class="main-wrapper">
	<div class="content-body m-0 p-0">
		<div class="login-register-wrap">
			<div class="row">
				<div class="d-flex align-self-center justify-content-center order-2 order-lg-1 col-lg-4 col-12">
					<div class="login-register-form-wrap">
						<div class="content">
							<center>
								<img src="<?=BASE_URL_ASSETS?>img/logo-single-2025.png" width="100px" />
							</center><br/>
							<h2><?=APP_NAME?></h2>
							<p>Please enter your username and password</p>
							<div class="alert alert-solid-warning d-none" role="alert" id="notifAllowAlert">
								Please allow notification before login!
							</div>
						</div>
						<div class="login-register-form">
							<form id="login-form" method="POST">
								<div class="row">
									<div class="col-12 mb-20">
										<div class="alert alert-dark d-none" role="alert" id="warning-element">
											<strong></strong>
											<span class="close"><i class="fa fa-close"></i></span>
										</div>
									</div>
									<div class="col-12 mb-20">
										<input id="username" name="username" class="form-control" type="text" placeholder="Username">
									</div>
									<div class="col-12 mb-20">
										<input id="password" name="password" class="form-control" type="password" placeholder="Password">
										<div class="show-password">
											<i class="fa fa-eye"></i>
										</div>
									</div>
									<div class="col-12 mt-10">
										<button id="loginSubmitBtn" type="submit" class="button button-primary button-outline">Sign In</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="login-register-bg order-1 order-lg-2 col-lg-8 col-12">
					<div class="content">
						<h1>Sign In</h1>
						<p>Please enter your username and password</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?=BASE_URL_ASSETS?>js/login.js?<?=date('YmdHis')?>"></script>