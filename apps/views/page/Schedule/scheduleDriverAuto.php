<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto">
		<div class="page-heading">
			<h3 class="title">Driver Schedule (Auto/Manual) <span> / Set driver schedule for tour or transport automatically or manually</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto">
		<div class="page-date-range">
			<input type="hidden" id="minHourDateFilterTomorrow" name="minHourDateFilterTomorrow" value="<?=$minHourDateFilterTomorrow?>">
			<input type="text" class="form-control input-date-single mb-10 text-center" id="scheduleDate" name="scheduleDate">
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#manualScheduleTab"><i class="fa fa-list"></i> Manual Schedule</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#autoScheduleTab"><i class="fa fa-users"></i> Auto Schedule</a></li>
		</ul>
	</div>
</div>
<div class="box mt-10">
	<div class="box-body tab-content">
		<div class="tab-pane fade show active" id="manualScheduleTab">
			<h6 class="text-center">Unscheduled Reservation List (Manual)</h6>
			<div style="border-top: 1px solid #e0e0e0;height:800px; overflow-y:scroll;">
				<div class="col-12 mt-40 mb-30 text-center" id="noDataUnscheduledReservationManual">
					<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
					<h5>No Data Found</h5>
					<p>There are no unscheduled reservations (manual) <b>on the date</b> you have selected</p>
				</div>
				<div id="manualReservationList" class="list-group py-2 px-1"></div>
			</div>
		</div>
		<div class="tab-pane fade" id="autoScheduleTab">
			<div class="row">
				<div class="col-sm-12 text-right pb-3">
					<button type="button" class="d-none button button-primary button-sm" id="btnSnapshotAutoSchedule"><span><i class="fa fa-random"></i>Snapshot Schedule</span></button>
					<button type="button" class="d-none button button-success button-sm" id="btnSaveAutoSchedule"><span><i class="fa fa-save"></i>Save</span></button>
					<button type="button" class="d-none button button-warning button-sm" id="btnCancelAutoSchedule"><span><i class="fa fa-times"></i>Reset</span></button>
					<input type="hidden" id="totalDriverTour" name="totalDriverTour" value="0">
					<input type="hidden" id="totalDriverCharter" name="totalDriverCharter" value="0">
					<input type="hidden" id="totalDriverShuttle" name="totalDriverShuttle" value="0">
					<input type="hidden" id="totalArea" name="totalArea" value="0">
					<input type="hidden" id="arrMaxCapacity" name="arrMaxCapacity" value="">
					<input type="hidden" id="maxScoreArea" name="maxScoreArea" value="0">
					<input type="hidden" id="maxScoreDriverRank" name="maxScoreDriverRank" value="0">
					<input type="hidden" id="maxScoreNoJob" name="maxScoreNoJob" value="0">
					<input type="hidden" id="maxScoreJobYesterday" name="maxScoreJobYesterday" value="0">
					<input type="hidden" id="maxScoreJobTopRatePriority" name="maxScoreJobTopRatePriority" value="0">
					<input type="hidden" id="maxScoreJobGoodRatePriority" name="maxScoreJobTopRatePriority" value="0">
				</div>
				<div class="col-lg-2 col-sm-4 pb-3">
					<select id="optionJobType" name="optionJobType" class="form-control">
						<option value="">All Job Type</option>
						<option value="2">Tour</option>
						<option value="3">Charter</option>
						<option value="1">Shuttle</option>
					</select>
				</div>
				<div class="col-lg-6 col-sm-8 pb-3">
					<select id="optionArea" name="optionArea" class="form-control" option-all="All Zone"></select>
				</div>
				<div class="col-lg-4 col-sm-12 pb-3">
					<select id="optionDriverType" name="optionDriverType" class="form-control pull-left">
						<option value="">All Driver Type</option>
						<option value="2">Tour</option>
						<option value="3">Charter</option>
						<option value="1">Shuttle</option>
					</select>
				</div>
				<div class="col-sm-12 text-center pb-3" style="border-bottom: 1px solid #e0e0e0;">
					<div class="alert alert-primary d-none" role="alert" id="autoScheduleProcessWarning">
						<i class="fa fa-warning"></i> The automatic scheduling process is running, please wait a moment <i class="fa fa-spinner fa-pulse"></i>
					</div>
				</div>
				<div class="col-lg-8 col-sm-12 pl-0 py-3" style="border-right: 1px solid #e0e0e0;">
					<h6>Unscheduled Reservation List</h6>
					<div style="border-top: 1px solid #e0e0e0;height:800px; overflow-y:scroll;">
						<div class="col-12 mt-40 mb-30 text-center" id="noDataUnscheduledReservation">
							<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
							<h5>No Data Found</h5>
							<p>There are no unscheduled reservations <b>on the date</b> you have selected</p>
						</div>
						<div id="sortableReservationList" class="list-group py-2 px-1"></div>
					</div>
				</div>
				<div class="col-lg-4 col-sm-12 pr-0 py-3">
					<h6>Available Driver List</h6>
					<div style="height:800px; overflow-y:scroll;border-top: 1px solid #e0e0e0;" id="availableDriverList">
						<div class="col-12 mt-40 mb-30 text-center" id="noDataAvailableDriver">
							<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
							<h5>No Data Found</h5>
							<p>There are no driver available <b>on the date</b> you have selected</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-driverList" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-driverList">
			<div class="modal-header">
				<h5 class="modal-title" id="title-driverList">Choose Driver For Reservation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 mb-10 pb-10" style="border-bottom: 1px solid #e0e0e0;">
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Driver Type</span> <span id="driverTypeStr">-</span> </li>
								<li> <span>Date & Time</span> <span id="dateTimeStr">-</span> </li>
								<li> <span>Reservation Detail</span> <span id="reservationDetailStr">-</span> </li>
								<li> <span>Job Detail</span> <span id="jobDetailStr">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-sm-12 mb-10" style="border-bottom: 1px solid #e0e0e0;">
						<div class="form-group mb-10">
							<input type="text" class="form-control" id="searchKeywordDriver" name="searchKeywordDriver" value="" placeholder="Search driver by name">
						</div>
					</div>
				</div>
				<div class="row scrollList">
					<table class="table">
						<tbody id="containerDriverList"></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationDetails" name="idReservationDetails" value="0">
				<button class="button button-primary" id="saveDriverToReservation">Set Reservation To Driver</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-driverLastJobList" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-driverLastJobList">
			<div class="modal-header">
				<h5 class="modal-title" id="title-driverLastJobList">Driver Job History</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 mb-10 pb-10" style="border-bottom: 1px solid #e0e0e0;">
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Driver Name</span> <span id="driverLastJobListDriverName">-</span> </li>
								<li> <span>Type</span> <span id="driverLastJobListDriverType">-</span> </li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row scrollList">
					<table class="table">
						<thead class="thead-light">
							<tr>
								<th width="120" class="text-center">Date</th>
								<th>Reservation Details</th>
								<th width="80">Rate</th>
							</tr>
						</thead>
						<tbody id="bodyDriverLastJobList"></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal" id="loader-scheduleSave" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-body px-4">
				<div class="row">
					<div class="col-sm-12 mb-10 text-center">
						<div class="spinner-border text-success">
							<span class="sr-only">Loading...</span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 mb-10">Processing data [ <span id="dataProcessNumber">1/25</span> ]</div>
				</div>
				<div class="row">
					<div class="col-sm-2 mb-1"><b>Driver</b></div>
					<div class="col-sm-10 mb-1" id="driverNameStr"></div>
				</div>
				<div class="row">
					<div class="col-sm-2 mb-1"><b>Job</b></div>
					<div class="col-sm-10 mb-1" id="jobDetailDialogStr"></div>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal-snapshotHistory" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content form-horizontal" id="content-snapshotHistory">
			<div class="modal-header">
				<h4 class="modal-title" id="title-snapshotHistory">Reservation Snapshot History</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-4" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h5 class="mb-0" id="snapshotHistory-reservationTitle">-</h5>
							</div>
							<div class="col-sm-12 mb-5">
								<div class="row">
									<div class="col-sm-6">
										<h6 class="mb-0">Job Type</h6>
									</div>
									<div class="col-sm-6">
										<h6 class="mb-0 text-right" id="snapshotHistory-jobType">-</h6>
									</div>
									<div class="col-sm-12 mb-5">
										<small>Allowed Driver Type : <b id="snapshotHistory-allowedDriverType"></b></small>
									</div>
								</div>
							</div>
							<div class="col-sm-12 mb-5">
								<div class="row">
									<div class="col-sm-6">
										<h6 class="mb-0">Total Pax</h6>
									</div>
									<div class="col-sm-6">
										<h6 class="mb-0 text-right" id="snapshotHistory-totalPax">-</h6>
									</div>
									<div class="col-sm-12 mb-5">
										<small>Allowed Car Type : <b id="snapshotHistory-allowedCarType"></b></small>
									</div>
								</div>
							</div>
							<div class="col-sm-12 mb-5">
								<div class="row">
									<div class="col-sm-6">
										<h6 class="mb-0">Area</h6>
									</div>
									<div class="col-sm-6">
										<h6 class="mb-0 text-right" id="snapshotHistory-areaName">-</h6>
									</div>
								</div>
							</div>
							<div class="col-sm-12 mb-5">
								<div class="row">
									<div class="col-sm-6">
										<h6 class="mb-0">Job Rate</h6>
									</div>
									<div class="col-sm-6">
										<h6 class="mb-0 text-right" id="snapshotHistory-jobRate">-</h6>
									</div>
								</div>
							</div>
							<div class="col-sm-12 mb-5">
								<div class="row">
									<div class="col-sm-6">
										<h6 class="mb-0">Job Duration</h6>
									</div>
									<div class="col-sm-6">
										<h6 class="mb-0 text-right" id="snapshotHistory-jobDuration">-</h6>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="row">
							<div class="col-sm-12 mb-10" id="containerListDriverSnapshotHistory" style="height:500px; overflow-y:scroll"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var dateToday	=	'<?=$dateToday?>',
		dateTomorrow=	'<?=$dateTomorrow?>',
		url 		=	"<?=BASE_URL_ASSETS?>js/page-module/Schedule/scheduleDriverAuto.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
.availableDriverItem, .manualReservationListItem, .listDriverSnapshotHistory{
	border: 1px solid #e0e0e0;
	background-color: #fff;
}
.scrollList{
	height: 500px;
	width: 104%;
	overflow-y: scroll;
}
.scrollList tr:first-of-type td {
  border-color: transparent;
}
</style>