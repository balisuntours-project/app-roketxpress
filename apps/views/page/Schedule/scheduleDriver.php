<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto">
		<div class="page-heading">
			<h3 class="title">Driver Schedule <span> / Set driver schedule for tour or transport</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto">
		<div class="page-date-range">
			<input type="hidden" id="minHourDateFilterTomorrow" name="minHourDateFilterTomorrow" value="<?=$minHourDateFilterTomorrow?>">
			<input type="text" class="form-control input-date-single mb-10 text-center" id="scheduleDate" name="scheduleDate">
		</div>
	</div>
</div>
<div class="row mb-10 justify-content-between align-items-center">
	<div class="col-12">
		<div class="alert alert-primary" role="alert">
			<i class="zmdi zmdi-info"></i> <span id="totalReservationInfo"></span>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#driverListTab"><i class="fa fa-users"></i> Driver List</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reservationTab"><i class="fa fa-list"></i> Reservation List</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#calendarTab"><i class="fa fa-calendar"></i> Schedule Calendar</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#dayOffRequestTab"><i class="fa fa-calendar-minus-o"></i> Day Off Request</a></li>
		</ul>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="tab-content">
			<div class="tab-pane fade show active" id="driverListTab">
				<div class="row mt-5 responsive-table-container">
					<div class="col-12 pl-0 pr-0">
						<div class="row">
							<div class="col-lg-3 col-md-8 col-sm-12">
								<div class="form-group">
									<label for="driverListTab-confirmationStatus" class="control-label">Confirmation Status</label>
									<select id="driverListTab-confirmationStatus" name="driverListTab-confirmationStatus" class="form-control">
										<option value="">All Confirmation Status</option>
										<option value="0">Unconfirmed Schedule</option>
										<option value="1">Confirmed Schedule</option>
									</select>
								</div>
							</div>
							<div class="col-lg-9 col-md-9 col-sm-12">
								<div class="form-group">
									<label for="driverListTab-searchKeyword" class="control-label">Search by Driver Name / Reservation Title / Customer Name / Booking Code</label>
									<input type="text" class="form-control" id="driverListTab-searchKeyword" name="driverListTab-searchKeyword" value="" placeholder="Type something and press ENTER to search">
								</div>
							</div>
						</div>
					</div>
					<table class="col-12 table" id="table-driverScheduleList">
						<thead class="thead-light">
							<tr>
								<th width="40"></th>
								<th width="40" align="right">#Rank</th>
								<th width="220">Driver Name</th>
								<th>Reservation List</th>
								<th width="90"></th>
								<th width="40"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="5" class="text-center">No data found</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane fade" id="reservationTab">
				<div class="row border-bottom">
					<div class="col-lg-4 col-sm-12">
						<div class="form-group">
							<label for="optionDriverFilter" class="control-label">Driver</label>
							<select id="optionDriverFilter" name="optionDriverFilter" class="form-control" option-all="All Driver"></select>
						</div>
					</div>
					<div class="col-lg-8 col-sm-12">
						<div class="form-group">
							<label for="searchKeyword" class="control-label">Search by Reservation Title / Customer Name / Booking Code</label>
							<input type="text" class="form-control" id="searchKeyword" name="searchKeyword" value="" placeholder="Type something and press ENTER to search">
						</div>
					</div>
				</div>
				<div class="row mt-20 px-20" id="tableReservationSchedule">
					<div class="col-12 mt-40 mb-30 text-center" id="noDataTableReservationSchedule">
						<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
						<h5>No Data Found</h5>
						<p>There are no unscheduled reservations <b>on the date</b> you have selected</p>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="calendarTab">
				<div class="row">
					<div class="col-sm-12 pl-0 pr-0">
						<button class="button button-primary button-sm pull-right" type="button" id="btnInputDayOff" data-toggle="modal" data-target="#editor-modal-inputDayOff">
							<span><i class="fa fa-edit"></i>Input Day Off</span>
						</button>
						<input type="text" class="form-control" id="calendarTab-searchKeyword" name="calendarTab-searchKeyword" value="" placeholder="Type something and press ENTER to search">
					</div>
				</div>
				<div class="row mt-5 tableFixHead" style="height: 600px">
					<table class="table tableFix" id="table-driverScheduleCalendar">
						<thead class="thead-light">
							<tr id="headerDates">
								<th width="240">Driver</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane fade" id="dayOffRequestTab">
				<div class="row px-0 mb-10 border-bottom">
					<div class="col-lg-2 col-sm-6">
						<div class="form-group mb-0">
							<label for="dayOffRequestTab-dayOffDate" class="control-label">Date</label>
							<div class="input-group">
								<input type="text" class="form-control input-date-single mb-10 text-center" id="dayOffRequestTab-dayOffDate" name="dayOffRequestTab-dayOffDate" aria-describedby="iconDayOffDate">
								<div class="input-group-append mb-10">
									<span class="input-group-text iconInputDate" id="iconDayOffDate">
										<i class="fa fa-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group mb-0">
							<label for="dayOffRequestTab-optionStatus" class="control-label">Status</label>
							<div class="input-group">
								<select id="dayOffRequestTab-optionStatus" name="dayOffRequestTab-optionStatus" class="form-control">
									<option value="">All Status</option>
									<option value="0">Requested</option>
									<option value="1">Approved</option>
									<option value="-1">Rejected</option>
									<option value="-2">Cancelled</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-8 col-sm-12">
						<div class="form-group mb-0">
							<label for="dayOffRequestTab-searchKeyword" class="control-label">Search</label>
							<input type="text" class="form-control" id="dayOffRequestTab-searchKeyword" name="dayOffRequestTab-searchKeyword" value="" placeholder="Type something and press ENTER to search">
						</div>
					</div>
				</div>
				<div class="row" id="tableDayOffRequest">
					<div class="col-12 mt-40 mb-30 text-center" id="noDataTableDayOffRequest">
						<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
						<h5>No Data Found</h5>
						<p>There are no day off request <b>on the date/month</b> you have selected</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-reservationList" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-reservationList">
			<div class="modal-header">
				<h5 class="modal-title" id="title-reservationList">Choose Reservation For : <span id="listReservationScheduleDriverName"></span><br/><span id="listReservationScheduleDate"></span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-12 pl-0 pr-0">
						<div class="form-group">
							<label for="modalReservationList-searchKeyword" class="control-label">Search by Reservation Title / Customer Name / Booking Code</label>
							<input type="text" class="form-control" id="modalReservationList-searchKeyword" name="modalReservationList-searchKeyword" value="" placeholder="Type something and press ENTER to search">
						</div>
					</div>
				</div>
				<div class="row" id="containerReservationList">
					<div class="col-12 mt-40 mb-30 text-center" id="noDataReservationList">
						<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
						<h5>No Data Found</h5>
						<p>There are no unscheduled reservations for the <b>type of driver and on the date</b> you have selected</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="reservationListIdDriver" name="reservationListIdDriver" value="0">
				<button class="button button-primary" id="saveReservationToDriver">Add Schedule To Driver</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
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
					<div class="col-sm-12 mb-10 pb-10" id="containerReservationDetail" style="border-bottom: 1px solid #e0e0e0;"></div>
					<div class="col-sm-12  pl-2 pr-2 mb-10" style="border-bottom: 1px solid #e0e0e0;">
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
<div class="modal fade" tabindex="-1" id="modal-detailReservation" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-detailReservation">
			<div class="modal-header">
				<h4 class="modal-title" id="title-detailReservation">Reservation Detail</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-6" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-6 mb-10">
								<h6 class="mb-0">Source</h6>
								<p id="detailReservation-source">-</p>
							</div>
							<div class="col-sm-6 mb-10">
								<h6 class="mb-0">Booking Code</h6>
								<p id="detailReservation-bookingCode">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Title</h6>
								<p id="detailReservation-title">-</p>
							</div>
							<div class="col-sm-8 mb-10">
								<h6 class="mb-0">Date</h6>
								<p id="detailReservation-date">-</p>
							</div>
							<div class="col-sm-4 mb-10">
								<h6 class="mb-0">Pick Up Time</h6>
								<p id="detailReservation-time">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Customer Name</h6>
								<p id="detailReservation-customerName">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Contact</h6>
								<p id="detailReservation-customerContact">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Email</h6>
								<p id="detailReservation-customerEmail">-</p>
							</div>
							<div class="col-sm-12 border-bottom pb-20 mb-10">
								<h6 class="mb-0">Schedule Activity</h6>
								<div class="order-details-customer-info">
									<ul id="detailReservation-scheduleActivity"></ul>
								</div>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Detail Driver</h6>
								<p id="detailReservation-detailDriver">-</p>
							</div>
							<div class="col-sm-12">
								<h6 class="mb-0">Detail Car</h6>
								<p id="detailReservation-detailCar">-</p>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Pax</h6>
								<div class="order-details-customer-info">
									<ul class="ml-5">
										<li> <span>Adult</span> <span id="detailReservation-numberOfAdult">0</span> </li>
										<li> <span>Child</span> <span id="detailReservation-numberOfChild">0</span> </li>
										<li> <span>Infant</span> <span id="detailReservation-numberOfInfant">0</span> </li>
									</ul>
								</div>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Customer Hotel</h6>
								<p id="detailReservation-hotelName">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Pick Up Location</h6>
								<p id="detailReservation-pickUpLocation">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Drop Off Location</h6>
								<p id="detailReservation-dropOffLocation">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Tour Plan</h6>
								<p id="detailReservation-tourPlan">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Remark</h6>
								<p id="detailReservation-remark">-</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-detailDayOff" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-detailDayOff">
			<div class="modal-header">
				<h4 class="modal-title" id="title-detailDayOff">Day Off Detail</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Driver Type</h6>
						<p id="detailDayOff-drivertype">-</p>
					</div>
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Name</h6>
						<p id="detailDayOff-name">-</p>
					</div>
					<div class="col-sm-8 mb-10">
						<h6 class="mb-0">Date</h6>
						<p id="detailDayOff-date">-</p>
					</div>
					<div class="col-sm-4 mb-10">
						<h6 class="mb-0">Date Time Input</h6>
						<p id="detailDayOff-datetimeinput">-</p>
					</div>
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Reason</h6>
						<p id="detailDayOff-reason">-</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-reservationDetails" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-reservationDetails">
			<div class="modal-header">
				<h4 class="modal-title" id="title-reservationDetails">Reservation Cost/Fee Details</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row mb-5">
					<div class="form-group col-sm-12 required">
						<label for="productName" class="control-label">Product Name</label>
						<input type="text" class="form-control" id="productName" name="productName" placeholder="Product/Fee Name" maxlength="200">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12 required">
						<label for="nominalFee" class="control-label">Nominal Fee</label>
						<input type="text" class="form-control mb-10 text-right" id="nominalFee" name="nominalFee" value="0" onkeypress="maskNumberInput(0, 999999999, 'nominalFee')">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12">
						<label for="notesFee" class="control-label">Notes</label>
						<textarea class="form-control mb-10" id="notesFee" name="notesFee"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationDetailsFee" name="idReservationDetailsFee" value="0">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="editor-modal-inputDayOff" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-inputDayOff">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-inputDayOff">Input Day Off</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-4 mb-10 form-group required">
						<label for="dayOffDate" class="control-label">Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="dayOffDate" name="dayOffDate">
					</div>
					<div class="col-sm-8 mb-10 form-group required">
						<label for="optionDayOffDriver" class="control-label">Driver</label>
						<select id="optionDayOffDriver" name="optionDayOffDriver" class="form-control"></select>
					</div>
					<div class="col-sm-12 mb-10 form-group required">
						<label for="dayOffReason" class="control-label">Reason</label>
						<textarea class="form-control" placeholder="Day off reason from driver" id="dayOffReason" name="dayOffReason"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var dateToday				=	'<?=$dateToday?>',
		dateTomorrow			=	'<?=$dateTomorrow?>',
		pmsAddDriverSchedule	=	'<?=$pmsAddDriverSchedule?>',
		pmsDeleteDriverSchedule	=	'<?=$pmsDeleteDriverSchedule?>',
		url 					=	"<?=BASE_URL_ASSETS?>js/page-module/Schedule/scheduleDriver.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
@media (max-width: 767px) {
	.reservationTableElement, .dayOffTableElement{
		overflow: scroll !important;
	}
}
.scrollList{
	height: 500px;
	width: 104%;
	overflow-y: scroll;
}
.scrollListTable{
	height: 600px;
	width: 103%;
	overflow-y: scroll;
}
.scrollList tr:first-of-type td {
  border-color: transparent;
}
.reservationListElement, .reservationTableElement, .dayOffTableElement{
	border: 1px solid #e0e0e0;
	min-height: 120px;
	background-color: #fff;
}
#table-driverScheduleCalendar tbody tr td {
	padding: 2px;
	padding-top: 4px;
}
</style>