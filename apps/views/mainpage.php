<script>
const interval_id = window.setInterval(function(){}, Number.MAX_SAFE_INTEGER);
for (let i = 1; i < interval_id; i++) {
  window.clearInterval(i);
}
if(!window.jQuery){
    window.location = window.location.origin;
}
</script>
<div class="main-wrapper">
<div class="header-section">
   <div class="container-fluid">
      <div class="row justify-content-between align-items-center">
         <div class="header-logo col-auto">
            <a href="<?=base_url()?>">
				<img src="<?=BASE_URL_ASSETS?>img/logo-update-text-2025.png" alt="" height="50px">
				<img src="<?=BASE_URL_ASSETS?>img/logo-update-text-2025.png" class="logo-light" alt="" height="50px">
            </a>
         </div>
         <div class="header-right flex-grow-1 col-auto">
            <div class="row justify-content-between align-items-center">
               <div class="col-auto">
                  <div class="row align-items-center">
                     <div class="col-auto"><button class="side-header-toggle"><i class="fa fa-align-justify"></i></button></div>
                  </div>
               </div>
               <div class="col-auto">
                  <ul class="header-notification-area">
					 <li class="adomx-dropdown col-auto" id="containerNotificationButton">
						<a class="toggle" href="#" id="containerNotificationIcon"><i class="zmdi zmdi-notifications"></i></a>
						<div class="adomx-dropdown-menu dropdown-menu-notifications" id="containerNotificationIconBodyList">
							<div class="head">
								<h5 class="title"><span id="containerNotificationCounter" class="text-bold"></span> Unread Notification</h5>
							</div>
							<div class="body custom-scroll ps ps--active-y">
								<ul id="containerNotificationList"></ul>
							<div class="ps__rail-x" style="left: 0px; bottom: 3px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 275px; right: 3px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 133px;"></div></div></div>
							<div class="footer">
								<span class="view-all">
									<a href="#" onclick="dismissAllNotification()">Dismiss All</a> | <a href="#" onclick="openListNotification()">See All</a>
								</span>
							</div>
						</div>
					 </li>
                     <li class="adomx-dropdown col-auto">
                        <a class="toggle" href="#">
							<span class="user">
								<span class="avatar">
									<i class="fa fa-user-circle-o"></i>
								</span>
								<span class="name" id="spanNameUser"><?=$nameUser?></span>
							</span>
                        </a>
                        <div class="adomx-dropdown-menu dropdown-menu-user">
                           <div class="head">
                              <h5 class="name"><a href="#" id="linkNameUser"><?=$nameUser?></a></h5>
                              <a class="mail" href="#" id="linkLevelUser"><span class="badge badge-primary"><?=$levelName?></span></a>
                              <a class="mail" href="#" id="linkEmailUser"><?=$email?></a>
                           </div>
                           <div class="body">
                              <ul>
                                 <li><a href="#" id="linkSetting" onclick="openUserProfileSetting()"><i class="fa fa-cogs"></i>Settings</a></li>
                                 <li><a href="#" id="linkClearAppData" onclick="clearAppData()"><i class="fa fa-trash-o"></i>Clear App Data</a></li>
                                 <li><a href="#" id="linkLogout"><i class="fa fa-sign-out"></i>Sign Out</a></li>
                              </ul>
                           </div>
                        </div>
                     </li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="side-header show">
   <button class="side-header-close"><i class="fa fa-close"></i></button>
   <div class="side-header-inner custom-scroll">
      <nav class="side-header-menu" id="side-header-menu">
         <ul>
            <li class="menu-item active" data-alias="DASH" data-url="dashboard" id="dashboard-menu">
               <a href="#"><i class="fa fa-home"></i> <span>Main Page</span></a>
            </li>
            <?=$menuElement?>
         </ul>
      </nav>
   </div>
</div>
<div class="content-body" id="main-content"></div>
<div class="modal fade" id="modal-pengaturan">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="form-pengaturan">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-pengaturan">Account Setting</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="name" class="col-sm-12 control-label">Name</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="name" name="name" placeholder="Name">
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-sm-12 control-label">Email</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="email" name="email" placeholder="Email">
					</div>
				</div>
				<div class="form-group required">
					<label for="username" class="col-sm-12 control-label">Username</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="username" autocomplete="off" name="username" placeholder="Username">
					</div>
				</div><br/>
				<p>Fill this form if you want to change your password</p>
				<div class="form-group">
					<label for="password" class="col-sm-12 control-label">Old Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="oldPassword" autocomplete="new-password" name="oldPassword" placeholder="Old Password">
					</div>
				</div>
				<div class="form-group">
					<label for="password" class="col-sm-12 control-label">New Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="newPassword" autocomplete="new-password" name="newPassword" placeholder="New Password">
					</div>
				</div>
				<div class="form-group">
					<label for="repeatPassword" class="col-sm-12 control-label">Retype New Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="repeatPassword" autocomplete="new-password" name="repeatPassword" placeholder="Retype New Password">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary" id="saveSetting">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modalWarning">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalWarningTitle">Warning</h5>
				<button class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body" id="modalWarningBody">-</div>
			<div class="modal-footer">
				<button class="button button-danger" id="modalWarningBtnOK" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="footable-confirm-delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title">Confirm Action</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				Are you sure want to delete this data?
			</div>
            <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-danger" id="deleteBtn" data-idData="" data-table="">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-confirm-action" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-confirm-title">Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body" id="modal-confirm-body"></div>
           <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-primary" id="confirmBtn" data-idData="" data-function="">Yes</button>
           </div>
        </div>
    </div>
</div>
<div class="modal loader-modal" id="window-loader" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-body">
				<div class="d-flex justify-content-center">
					<div class="spinner-border text-success">
						<span class="sr-only">Loading...</span>
					</div>
				</div><br/>
				<div class="row">
					<div class="col-12 text-center">
						<span>Loading, please wait..</span>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-zoomReceiptImage">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" id="editor-zoomReceiptImage">
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 text-center">
						<img id="zoomReceiptImage" style="max-width: 700px; max-height:700px" src=""/>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="lastMenuAlias" name="lastMenuAlias" value="">
<script>
	localStorage.setItem('lastApplicationLoadTime', '<?=gmdate("YmdHis")?>');
	localStorage.setItem('allowNotifList', '<?=$allowNotifList?>');
	var baseURL				=	'<?=base_url()?>',
		loaderElem			=	"<center class='mt-5'>"+
								"	<img src='<?=BASE_URL_ASSETS?>img/loader_content.gif'/><br/><br/>"+
								"	Loading Content..."+
								"</center>",
		arrBadgeType		=	['dark', 'primary', 'warning', 'info', 'success', 'secondary', 'danger'],
		notificationSound	=	new Audio(ASSET_AUDIO_URL+'notification.mp3'),
		notificationSoundSS	=	new Audio(ASSET_AUDIO_URL+'sixth_sense.mp3');
		notificationSoundIp	=	new Audio(ASSET_AUDIO_URL+'iphone.mp3');
		
	$.ajaxSetup({ cache: true });
	OneSignal.push(function() {
		OneSignal.on('notificationPermissionChange', function(permissionChange) {
			var currentPermission = permissionChange.to;
			if(currentPermission != "granted"){
				var urlLogout	=	$('#linkLogout').attr('href');
				window.location.replace(urlLogout);
			}
		});
		OneSignal.on('notificationDisplay', function(event) {
			toastr["info"](event.heading+"<br/>"+event.content);
			if(!document.hidden){
				getUnreadNotificationList();
			}
			notificationSoundSS.play();
		});
	});
	
	function clearAppData(modalWarning = true){
		$('.adomx-dropdown.col-auto.show').removeClass('show');
		$('.adomx-dropdown-menu.dropdown-menu-user.show').removeClass('show');
		var localStorageKeys	=	Object.keys(localStorage),
			localStorageIdx		=	localStorageKeys.length;
		for(var i=0; i<localStorageIdx; i++){
			var keyName			=	localStorageKeys[i];
			if(keyName.substring(0, 5) == "form_"){
				localStorage.removeItem(keyName);
			}
		}
		
		if(modalWarning){
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html("App data has been cleared");
			});
			$('#modalWarning').modal('show');
		}
	}
</script>
<script>
	var optionMonth	=	localStorage.setItem('optionMonth','<?=$optionMonth?>'),
		optionYear	=	localStorage.setItem('optionYear','<?=$optionYear?>');
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/select2.full.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/footable.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/bootstrap-select.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/daterangepicker.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/jquery.uploadfile.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/jquery.raty.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/sortable.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/jquery.scrollTo.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/toastr.min.js";
	$.getScript(url, function(){
		toastr.options = {
		  "closeButton": false,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "rtl": false,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": 300,
		  "hideDuration": 300,
		  "timeOut": 6000,
		  "extendedTimeOut": 0,
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
	});
</script>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/main.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<script type="module">
	import { initializeApp } from "https://www.gstatic.com/firebasejs/9.13.0/firebase-app.js";
	import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/9.13.0/firebase-database.js";

	const firebaseConfig = {
		apiKey: "<?=FIREBASE_RTDB_API_KEY?>",
		authDomain: "<?=FIREBASE_RTDB_AUTH_DOMAIN?>",
		databaseURL: "<?=FIREBASE_RTDB_URI?>",
		projectId: "<?=FIREBASE_RTDB_PROJECT_ID?>",
		storageBucket: "<?=FIREBASE_RTDB_STORAGE_BUCKET?>",
		messagingSenderId: "<?=FIREBASE_RTDB_MESSAGING_SENDER_ID?>",
		appId: "<?=FIREBASE_RTDB_APPLICATION_ID?>",
		measurementId: "<?=FIREBASE_RTDB_MEASUREMENT_ID?>"
	};

	const app						=	initializeApp(firebaseConfig),
		  database	 				= 	getDatabase(app),
		  allowNotifList			=	JSON.parse(localStorage.getItem('allowNotifList')),
		  allowNotifMail			=	allowNotifList.NOTIFMAIL * 1,
		  allowNotifReservation		=	allowNotifList.NOTIFRESERVATION * 1,
		  allowNotifScheduleDriver	=	allowNotifList.NOTIFSCHEDULEDRIVER * 1,
		  allowNotifScheduleCar		=	allowNotifList.NOTIFSCHEDULEVENDOR * 1,
		  allowNotifAdditionalCost	=	allowNotifList.NOTIFADDITIONALCOST * 1,
		  allowNotifAdditionalIncome=	allowNotifList.NOTIFADDITIONALINCOME * 1,
		  allowNotifFinance			=	allowNotifList.NOTIFFINANCE * 1;
  
	const unprocessedReservationMail=	ref(database, '<?=FIREBASE_RTDB_MAILREF_NAME?>');
	const unprocessedReservation	=	ref(database, '<?=FIREBASE_RTDB_MAINREF_NAME?>unprocessedReservation');
	const unreadThreadReconfirmation=	ref(database, '<?=FIREBASE_RTDB_MAINREF_NAME?>unreadThreadReconfirmation');
	const undeterminedSchedule		=	ref(database, '<?=FIREBASE_RTDB_MAINREF_NAME?>undeterminedSchedule');
	const unprocessedFinance		=	ref(database, '<?=FIREBASE_RTDB_MAINREF_NAME?>unprocessedFinance');
	const unprocessedFinanceDriver	=	ref(database, '<?=FIREBASE_RTDB_MAINREF_NAME?>unprocessedFinanceDriver');
	const unprocessedFinanceVendor	=	ref(database, '<?=FIREBASE_RTDB_MAINREF_NAME?>unprocessedFinanceVendor');
	
	onValue(unprocessedReservationMail, (snapshot) => {
		const dataMail					=	snapshot.val(),
			  newMailStatus				=	dataMail.newMailStatus,
			  timestampUpdate			=	dataMail.timestampUpdate * 1,
			  lastApplicationLoadTime	=	localStorage.getItem('lastApplicationLoadTime') * 1,
			  lastMenuAlias				=	$("#lastMenuAlias").val();
			  
		if(newMailStatus && timestampUpdate > lastApplicationLoadTime && allowNotifMail == 1){
			notificationSound.play();
			toastr["success"]("New mail from : "+dataMail.newMailSourceName+"<br/>Subject : "+dataMail.newMailSubject)
		}
		
		generateTotalUnreadMailElem(dataMail.totalUnprocessedMail);
		getUnreadNotificationList();
		
		if(lastMenuAlias == 'MB'){
			// getDataMailbox();
		}
	});
	
	onValue(unprocessedReservation, (snapshot) => {
		const totalUnprocessedReservation	=	snapshot.val(),
			  lastMenuAlias					=	$("#lastMenuAlias").val();
		
		generateTotalUnprocessReservarionElem(totalUnprocessedReservation);
		generateTotalReservationElem(totalUnprocessedReservation);
		getUnreadNotificationList();
		
		if(lastMenuAlias == 'RV'){
			// getDataReservation();
		}
	});
	
	onValue(unreadThreadReconfirmation, (snapshot) => {
		const dataThreadReconfirmation	=	snapshot.val(),
			  newMailThreadStatus		=	dataThreadReconfirmation.newMailThreadStatus,
			  timestampUpdate			=	dataThreadReconfirmation.timestampUpdate * 1,
			  lastApplicationLoadTime	=	localStorage.getItem('lastApplicationLoadTime') * 1,
			  lastMenuAlias				=	$("#lastMenuAlias").val();
			  
		if(newMailThreadStatus && timestampUpdate > lastApplicationLoadTime && allowNotifMail == 1){
			notificationSoundIp.play();
			toastr["info"]("New reconfirmation mail from : "+dataThreadReconfirmation.newMailThreadName+"<br/>Address : "+dataThreadReconfirmation.newMailThreadAddress)
		}
		
		generateTotalUnreadThreadReconfirmation(dataThreadReconfirmation.unreadThreadReconfirmation);
		getUnreadNotificationList();
		
		if(lastMenuAlias == 'RRC'){
			getDataReconfirmation();
		}
	});
	
	onValue(undeterminedSchedule, (snapshot) => {
		const totalUndeterminedSchedule	=	snapshot.val(),
			  lastMenuAlias				=	$("#lastMenuAlias").val();
		
		generateTotalUndeterminedScheduleElem(totalUndeterminedSchedule);
		getUnreadNotificationList();
		
		if(lastMenuAlias == 'SCDRA'){
			// getDataAutoScheduleSetting();
			// getDataScheduleAuto();
			// getDataDriverList();
			// getDataScheduleManual();
		}
		
		if(lastMenuAlias == 'SCDR'){
			// getDataDriverSchedule();
			// getDataReservationSchedule();
			// getDataDriverCalendar();
			// getDataDayOffRequest();
		}
	});
	
	onValue(unprocessedFinance, (snapshot) => {
		const dataFinance					=	snapshot.val(),
			  dataReimbursement				=	dataFinance.reimbursement,
			  newReimbursementStatus		=	dataReimbursement.newReimbursementStatus,
			  reimbursementTimestampUpdate	=	dataReimbursement.timestampUpdate,
			  lastApplicationLoadTime		=	localStorage.getItem('lastApplicationLoadTime') * 1;

		if(newReimbursementStatus && reimbursementTimestampUpdate > lastApplicationLoadTime && allowNotifFinance == 1){
			notificationSound.play();
			toastr["info"](dataReimbursement.newReimbursementMessage)
		}
		
		generateTotalReimbursementElem(dataReimbursement.newReimbursementTotal);
		generateTotalFinanceElem(dataReimbursement.newReimbursementTotal);
	});
	
	onValue(unprocessedFinanceDriver, (snapshot) => {
		const dataFinanceDriver					=	snapshot.val(),
			  dataWithdrawalRequest				=	dataFinanceDriver.withdrawalRequest,
			  newWithdrawalRequestStatus		=	dataWithdrawalRequest.newWithdrawalRequestStatus,
			  withdrawalRequestTimestampUpdate	=	dataWithdrawalRequest.timestampUpdate,
			  dataAdditionalCost				=	dataFinanceDriver.additionalCost,
			  newAdditionalCostStatus			=	dataAdditionalCost.newAdditionalCostStatus,
			  additionalCostTimestampUpdate		=	dataAdditionalCost.timestampUpdate,
			  dataAdditionalIncome				=	dataFinanceDriver.additionalIncome,
			  newAdditionalIncomeStatus			=	dataAdditionalIncome.newAdditionalIncomeStatus,
			  additionalIncomeTimestampUpdate	=	dataAdditionalIncome.timestampUpdate,
			  dataCollectPayment				=	dataFinanceDriver.collectPayment,
			  newCollectPaymentStatus			=	dataCollectPayment.newCollectPaymentStatus,
			  collectPaymentTimestampUpdate		=	dataCollectPayment.timestampUpdate,
			  dataLoanPrepaidCapital			=	dataFinanceDriver.loanPrepaidCapital,
			  newLoanPrepaidCapitalStatus		=	dataLoanPrepaidCapital.newLoanPrepaidCapitalStatus,
			  loanPrepaidCapitalTimestampUpdate	=	dataLoanPrepaidCapital.timestampUpdate,
			  lastApplicationLoadTime			=	localStorage.getItem('lastApplicationLoadTime') * 1;

		if(newWithdrawalRequestStatus && withdrawalRequestTimestampUpdate > lastApplicationLoadTime && allowNotifFinance == 1){
			notificationSound.play();
			toastr["info"](dataWithdrawalRequest.newWithdrawalRequestMessage)
		}
		
		if(newAdditionalCostStatus && additionalCostTimestampUpdate > lastApplicationLoadTime && allowNotifAdditionalCost == 1){
			notificationSound.play();
			toastr["info"](dataAdditionalCost.newAdditionalCostMessage)
		}
		
		if(newAdditionalIncomeStatus && additionalIncomeTimestampUpdate > lastApplicationLoadTime && allowNotifAdditionalIncome == 1){
			notificationSound.play();
			toastr["info"](dataAdditionalIncome.newAdditionalIncomeMessage)
		}
		
		if(newCollectPaymentStatus && collectPaymentTimestampUpdate > lastApplicationLoadTime && allowNotifFinance == 1){
			notificationSound.play();
			toastr["info"](dataCollectPayment.newCollectPaymentMessage)
		}
		
		if(newLoanPrepaidCapitalStatus && loanPrepaidCapitalTimestampUpdate > lastApplicationLoadTime && allowNotifFinance == 1){
			notificationSound.play();
			toastr["info"](dataLoanPrepaidCapital.newLoanPrepaidCapitalMessage)
		}
		
		generateTotalWithdrawalDriverRequestElem(dataWithdrawalRequest.newWithdrawalRequestTotal);
		generateTotalAdditionalCostDriverElem(dataAdditionalCost.newAdditionalCostTotal);
		generateTotalAdditionalIncomeDriverElem(dataAdditionalIncome.newAdditionalIncomeTotal);
		generateTotalCollectPaymentDriverElem(dataCollectPayment.newCollectPaymentTotal);
		generateTotalLoanPrepaidCapitalDriverElem(dataLoanPrepaidCapital.newLoanPrepaidCapitalTotal);
		generateTotalFinanceDriverElem(dataWithdrawalRequest.newWithdrawalRequestTotal, dataAdditionalCost.newAdditionalCostTotal, dataCollectPayment.newCollectPaymentTotal, dataLoanPrepaidCapital.newLoanPrepaidCapitalTotal, dataAdditionalIncome.newAdditionalIncomeTotal);
	});
	
	onValue(unprocessedFinanceVendor, (snapshot) => {
		const dataFinanceVendor					=	snapshot.val(),
			  dataWithdrawalRequest				=	dataFinanceVendor.withdrawalRequest,
			  newWithdrawalRequestStatus		=	dataWithdrawalRequest.newWithdrawalRequestStatus,
			  withdrawalRequestTimestampUpdate	=	dataWithdrawalRequest.timestampUpdate,
			  dataCollectPayment				=	dataFinanceVendor.collectPayment,
			  newCollectPaymentStatus			=	dataCollectPayment.newCollectPaymentStatus,
			  collectPaymentTimestampUpdate		=	dataCollectPayment.timestampUpdate,
			  lastApplicationLoadTime			=	localStorage.getItem('lastApplicationLoadTime') * 1;
			  
		if(newWithdrawalRequestStatus && withdrawalRequestTimestampUpdate > lastApplicationLoadTime && allowNotifFinance == 1){
			notificationSound.play();
			toastr["info"](dataWithdrawalRequest.newWithdrawalRequestMessage)
		}
		
		if(newCollectPaymentStatus && collectPaymentTimestampUpdate > lastApplicationLoadTime && allowNotifFinance == 1){
			notificationSound.play();
			toastr["info"](dataCollectPayment.newCollectPaymentMessage)
		}
		
		generateTotalWithdrawalVendorRequestElem(dataWithdrawalRequest.newWithdrawalRequestTotal);
		generateTotalCollectPaymentVendorElem(dataCollectPayment.newCollectPaymentTotal);
		generateTotalFinanceVendorElem(dataWithdrawalRequest.newWithdrawalRequestTotal, dataCollectPayment.newCollectPaymentTotal);
	});

</script>