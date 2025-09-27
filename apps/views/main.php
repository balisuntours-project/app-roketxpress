<!doctype html>
<html lang="en">
  <head>
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?=GOOGLE_TAG_MANAGER_ID?>"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'G-GGS1C83JS9');
	</script>
    <meta http-equiv="Content-Type" content="text/html;">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<meta name="googlebot" content="noindex">
    <title><?=APP_NAME?></title>

	<link rel="icon" href="<?=BASE_URL_ASSETS?>img/logo-single-2025.ico" type="image/x-icon"/>
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/main-loader.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/helper.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/nprogress.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/plugins.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/uploadfile.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/material-design-iconic-font.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/toastr.min.css" rel="stylesheet" type="text/css">

	<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
	<script>
	  window.OneSignal = window.OneSignal || [];
	  OneSignal.push(function() {
		OneSignal.init({
		  appId: "6f5c2da3-a213-4870-82f2-41e072aef838",
		  notificationClickHandlerAction: "focus"
		});
		OneSignal.on('notificationDismiss', function(event) {
			localStorage.setItem('OSNotificationData', JSON.stringify(event.data));
		});
	  });
	</script>
	
	</head>
	<body id="mainbody">
		<div class="main-wrapper">
			<div class="content-body m-0 p-0">
				<div class="login_wrapper" style="margin-top:0">
					<div class="animate form login_form">
					  <section class="login_content" id="center_content">
						  <h3><center><?=APP_NAME?></center></h3>
						  <center>
							<img src="<?=BASE_URL_ASSETS?>img/loader.gif"/>
							<p id="loadtext">Checking session...</p>
						  </center>
					  </section>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" id="lastUpdateElemLocalStorageChange" name="lastUpdateElemLocalStorageChange" value="">
	</body>
	<script src="<?=BASE_URL_ASSETS?>js/define.js?<?=date('YmdHis')?>"></script>
	<script src="<?=BASE_URL_ASSETS?>js/modernizr.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/jquery.min.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/popper.min.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/bootstrap.min.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/tippy4.min.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/moment.min.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/nprogress.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/perfect-scrollbar.min.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/chart.min.js"></script>
	<script src="<?=BASE_URL_ASSETS?>js/session-controller.js?<?=date('YmdHis')?>"></script>
</html>
