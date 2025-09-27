<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Notification<span>/ List of received notifications</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#unreadMessageTab"><i class="fa fa-envelope"></i> Unread Message</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#readMessageTab"><i class="fa fa-envelope-open"></i> All Message</a></li>
		</ul>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-3 col-sm-12 mb-5">
				<select id="optionMessageType" name="optionMessageType" class="form-control" option-all="All Message Type"></select>
			</div>
			<div class="col-lg-9 col-sm-12 mb-5">
				<input type="text" class="form-control" id="keywordSearch" name="keywordSearch" placeholder="Type something and press ENTER to search">
			</div>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body tab-content">
		<div class="tab-pane fade show active" id="unreadMessageTab">
			<div class="row mt-5">
				<div class="col-lg-9 col-sm-8 mb-5">
					<span id="tableDataCountUnreadMessage"></span>
				</div>
				<div class="col-lg-3 col-sm-4 mb-5 text-right">
					<button type="button" class="button button-warning button-sm" id="btnDismissAllMessage" onclick="dismissAllNotification(true)"><span><i class="fa fa-minus-circle"></i>Dismiss All Message</span></button>
				</div>
			</div>
			<div class="row mt-5 tableMessage" id="tableUnreadMessage">
				<div class="col-12 mt-40 mb-30 text-center" id="noDataUnreadMessage">
					<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
					<h5>No Data Found</h5>
					<p>There are no unread message</p>
				</div>
			</div>
			<div class="row mt-15">
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none" style="width: 120px;" id="btnPreviousPageUnreadMessage"><i class="fa fa-arrow-left"></i><span>Previous</span></button>
				</div>
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none button-icon-right pull-right" style="width: 120px;" id="btnNextPageUnreadMessage"><i class="fa fa-arrow-right"></i><span>Next</span></button>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="readMessageTab">
			<div class="row mt-5">
				<div class="col-lg-12 mb-5">
					<span id="tableDataCountReadMessage"></span>
				</div>
			</div>
			<div class="row mt-5 tableMessage" id="tableReadMessage">
				<div class="col-12 mt-40 mb-30 text-center" id="noDataReadMessage">
					<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
					<h5>No Data Found</h5>
					<p>There are no message</p>
				</div>
			</div>
			<div class="row mt-15">
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none" style="width: 120px;" id="btnPreviousPageReadMessage"><i class="fa fa-arrow-left"></i><span>Previous</span></button>
				</div>
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none button-icon-right pull-right" style="width: 120px;" id="btnNextPageReadMessage"><i class="fa fa-arrow-right"></i><span>Next</span></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/notification.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
.tableMessage {
	min-height: 100px;
	max-height: 400px;
	overflow-y: scroll;
}
</style>