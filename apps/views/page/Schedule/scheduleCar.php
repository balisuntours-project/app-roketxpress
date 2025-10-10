<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto">
		<div class="page-heading">
			<h3 class="title">Car Schedule <span> / List of car schedule per month</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto">
		<div class="page-date-range">
			<div class="form-group mr-10">
				<label for="optionMonth" class="control-label">Schedule Period</label>
				<select class="form-control" id="optionMonth" name="optionMonth"></select>
			</div>
			<div class="form-group">
				<label for="optionYear" class="control-label">.</label>
				<select class="form-control" id="optionYear" name="optionYear"></select>
			</div>
		</div>
	</div>
</div>
<div class="row justify-content-between align-items-center">
	<div class="col-12 mb-10">
		<div class="alert alert-primary" role="alert">
			<i class="zmdi zmdi-info"></i> <span id="totalScheduleInfo"></span>
		</div>
	</div>
</div>
<div class="box mb-5">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#calendarTab"><i class="fa fa-calendar"></i> Schedule Calendar</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reservationTab"><i class="fa fa-list"></i> Reservation List</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#dropOffPickUpTab"><i class="fa fa-calendar-plus-o"></i> Drop Off & Pick Up</a></li>
		</ul>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="tab-content">
			<div class="tab-pane fade show active" id="calendarTab">
				<div class="row px-0">
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="calendarTab-optionVendorCar" class="control-label">Car Vendor</label>
							<select id="calendarTab-optionVendorCar" name="calendarTab-optionVendorCar" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="calendarTab-optionCarType" class="control-label">Car Type</label>
							<select id="calendarTab-optionCarType" name="calendarTab-optionCarType" class="form-control" option-all="All Car Type"></select>
						</div>
					</div>
					<div class="col-lg-8 col-sm-12">
						<div class="form-group">
							<label for="calendarTab-searchKeyword" class="control-label">Search by Brand / Model / Plat Number / Color</label>
							<input type="text" class="form-control" id="calendarTab-searchKeyword" name="calendarTab-searchKeyword" value="" placeholder="Type something and press ENTER to search">
						</div>
					</div>
					<div class="col-12 mt-5 tableFixHead px-0" style="height: 600px">
						<table class="table tableFix" id="table-carScheduleList">
							<thead class="thead-light">
								<tr id="headerDates">
									<th style="width:400px !important">Car Details &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="reservationTab">
				<div class="row border-bottom pb-5 mb-20 px-0">
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="reservationTab-optionSource" class="control-label">Source</label>
							<select id="reservationTab-optionSource" name="calendarTab-optionSource" class="form-control" option-all="All Source"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="reservationTab-dateSchedule" class="control-label">Schedule Date</label>
							<div class="input-group">
								<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationTab-dateSchedule" name="reservationTab-dateSchedule" aria-describedby="iconDateSchedule">
								<div class="input-group-append mb-10">
									<span class="input-group-text iconInputDate" id="iconDateSchedule">
										<i class="fa fa-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-sm-8">
						<div class="form-group">
							<label for="reservationTab-bookingCode" class="control-label">Booking Code</label>
							<input type="text" class="form-control mb-10" id="reservationTab-bookingCode" name="reservationTab-bookingCode">
						</div>
					</div>
					<div class="col-lg-6 col-sm-8">
						<div class="form-group">
							<label for="reservationTab-searchKeyword" class="control-label">Search by Customer Name / Product Details / Notes</label>
							<input type="text" class="form-control" id="reservationTab-searchKeyword" name="reservationTab-searchKeyword" value="" placeholder="Type something and press ENTER to search">
						</div>
					</div>
				</div>
				<div class="row px-3" id="tableReservationSchedule">
					<div class="col-12 mt-40 mb-30 text-center" id="noDataTableReservationSchedule">
						<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
						<h5>No Data Found</h5>
						<p>There are no unscheduled reservations <b>on the date</b> you have selected</p>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="dropOffPickUpTab">
				<div class="row border-bottom mb-20 pb-10">
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="dropOffPickUpTab-optionVendor" class="control-label">Car Vendor</label>
							<select id="dropOffPickUpTab-optionVendor" name="dropOffPickUpTab-optionVendor" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6 mb-10">
						<div class="form-group">
							<label for="dropOffPickUpTab-optionDriver" class="control-label">Driver</label>
							<select id="dropOffPickUpTab-optionDriver" name="dropOffPickUpTab-optionDriver" class="form-control" option-all="All Driver"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6 mb-10">
						<div class="form-group">
							<label for="dropOffPickUpTab-startDate" class="control-label">Date Schedule</label>
							<input type="text" class="form-control input-date-single mb-10 text-center" id="dropOffPickUpTab-startDate" name="dropOffPickUpTab-startDate" value="<?=date('d-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-2 col-sm-6 mb-10">
						<div class="form-group">
							<label for="dropOffPickUpTab-endDate" class="control-label">.</label>
							<input type="text" class="form-control input-date-single text-center" id="dropOffPickUpTab-endDate" name="dropOffPickUpTab-endDate" value="<?=$next7DayDate?>">
						</div>
					</div>
					<div class="col-lg-4 col-sm-12">
						<div class="form-group">
							<label for="dropOffPickUpTab-searchKeyword" class="control-label">Type something to search data</label>
							<input type="text" class="form-control mb-10" id="dropOffPickUpTab-searchKeyword" name="dropOffPickUpTab-searchKeyword" placeholde="Type something and press ENTER to search">
						</div>
					</div>
				</div>
				<div class="row">
					<table class="table" id="dropOffPickUpTab-tableData">
						<thead class="thead-light">
							<tr>
								<th>Reservation Details</th>
								<th width="300">Customer Details</th>
								<th width="350">Car Details</th>
								<th width="300">Drop Off</th>
								<th width="300">Pick Up</th>
							</tr>
						</thead>
						<tbody>
							<tr><th colspan="5" class="text-center">No data found</th></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-unscheduledList" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="content-unscheduledList">
			<div class="modal-header">
				<h5 class="modal-title" id="title-unscheduledList">Choose Schedule or Add Day Off</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-10" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-lg-4 col-sm-12 mb-10">
						<h6 class="mb-0">Vendor</h6>
						<p id="unscheduledListVendorName"></p>
					</div>
					<div class="col-lg-8 col-sm-12 mb-10">
						<h6 class="mb-0">Car Details</h6>
						<p id="unscheduledListCarDetail"></p>
					</div>
				</div>
				<div class="row" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-lg-2 col-sm-6">
						<label for="scheduleDateStartFilter" class="control-label">Date Range</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="scheduleDateStartFilter" name="scheduleDateStartFilter">
					</div>
					<div class="col-lg-2 col-sm-6">
						<label for="scheduleDateEndFilter" class="control-label">.</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="scheduleDateEndFilter" name="scheduleDateEndFilter">
					</div>
					<div class="col-lg-3 col-sm-4">
						<label for="optionScheduleDurationFilter" class="control-label">Duration (Hours)</label>
						<select id="optionScheduleDurationFilter" name="optionScheduleDurationFilter" class="form-control">
							<option value="">All Duration</option>
							<option value="6">6 Hours</option>
							<option value="12">12 Hours</option>
							<option value="18">18 Hours</option>
							<option value="24">24 Hours</option>
						</select>
					</div>
					<div class="col-lg-5 col-sm-8">
						<label for="scheduleKeywordFilter" class="control-label">Search by Booking Code / Customer Name / Etc</label>
						<input type="text" class="form-control mb-10" id="scheduleKeywordFilter" name="scheduleKeywordFilter" placeholder="Type something and press ENTER to search">
					</div>
				</div>
				<div class="row mb-5 border mx-1 my-2 rounded" id="containerUnscheduledList" style="height: 400px;overflow-y: scroll;">
					<div class="col-12 mt-40 mb-30 text-center" id="noDataUnscheduledList">
						<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
						<h5>No Data Found</h5>
						<p>There are no unscheduled reservations for the <b>type of car, duration and on the date</b> you have selected</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="unscheduledListIdVendorCar" name="unscheduledListIdVendorCar" value="0">
				<button type="button" class="button button-warning mr-auto" data-toggle="modal" data-target="#modal-addCarDayOff">Add Day Off</button>
				<button class="button button-primary" id="saveScheduleToCar">Save Schedule</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-carList" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-carList">
			<div class="modal-header">
				<h5 class="modal-title" id="title-carList">Choose Car For Reservation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 mb-10 order-details-customer-info">
						<ul id="containerReservationDetail"></ul>
					</div>
					<div class="col-sm-12 mb-10"><p class="pl-30" id="containerReservationDetail-customerProduct"></p></div>
				</div>
				<div class="row scrollList">
					<table class="table">
						<tbody id="containerCarList"></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationDetails" name="idReservationDetails" value="0">
				<button class="button button-primary" id="saveCarToReservation">Set Reservation To Car</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-addCarDayOff" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-addCarDayOff">
			<div class="modal-header">
				<h4 class="modal-title">Input Car Day Off</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-10 border-bottom">
					<div class="col-sm-12 mb-10">
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span><b>Vendor</b></span> <span id="addCarDayOff-vendorNameStr">-</span> </li>
								<li> <span><b>Car</b></span> <span id="addCarDayOff-carDetailsStr">-</span> </li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row border-bottom pb-10 mb-10">
					<div class="col-sm-12 pt-10 form-group required">
						<label for="addCarDayOff-optionType" class="control-label">Day Off Type</label>
						<select id="addCarDayOff-optionType" name="addCarDayOff-optionType" class="form-control"></select>
					</div>
					<div class="col-lg-5 col-sm-6 form-group required">
						<label for="addCarDayOff-dateStart" class="control-label">Date Time Start</label>
						<input type="text" class="form-control input-date-single text-center" id="addCarDayOff-dateStart" name="addCarDayOff-dateStart">
					</div>
					<div class="col-lg-2 col-sm-3 form-group">
						<label for="addCarDayOff-hourStart" class="control-label">&nbsp;</label>
						<select id="addCarDayOff-hourStart" name="addCarDayOff-hourStart" class="form-control"></select>
					</div>
					<div class="col-lg-2 col-sm-3 form-group">
						<label for="addCarDayOff-minuteStart" class="control-label">&nbsp;</label>
						<select id="addCarDayOff-minuteStart" name="addCarDayOff-minuteStart" class="form-control">
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
						</select>
					</div>
					<div class="col-lg-3 col-sm-12 form-group required">
						<label for="addCarDayOff-durationHour" class="control-label">Duration (Hour)</label>
						<input type="text" class="form-control text-right" id="addCarDayOff-durationHour" name="addCarDayOff-durationHour" value="0" onkeypress="maskNumberInput(1, 72, 'addCarDayOff-durationHour')">
					</div>
					<div class="col-sm-12 mb-10 form-group required">
						<label for="addCarDayOff-description" class="control-label">Description</label>
						<textarea class="form-control" placeholder="Day off description" id="addCarDayOff-description" name="addCarDayOff-description"></textarea>
					</div>
				</div>
				<div class="row pt-10">
					<div class="col-lg-8 col-sm-6 mb-10 form-group">
						<label for="addCarDayOff-optionCarCostType" class="control-label">Cost Type</label>
						<select id="addCarDayOff-optionCarCostType" name="addCarDayOff-optionCarCostType" class="form-control" disabled></select>
					</div>
					<div class="col-lg-4 col-sm-6 mb-10 form-group">
						<label for="addCarDayOff-costNominal" class="control-label">Cost Nominal</label>
						<input type="text" class="form-control mb-10 text-right" id="addCarDayOff-costNominal" name="addCarDayOff-costNominal" value="1000" onkeypress="maskNumberInput(1000, 999999999, 'addCarDayOff-costNominal')" disabled>
					</div>
					<div class="col-sm-12 mb-10 form-group">
						<label for="addCarDayOff-costDescription" class="control-label">Cost Description</label>
						<input type="text" class="form-control" id="addCarDayOff-costDescription" name="addCarDayOff-costDescription" value="" placeholder="Cost description" disabled>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="addCarDayOff-idCarVendor" name="addCarDayOff-idCarVendor" value="0">
				<input type="hidden" id="addCarDayOff-isNeedCost" name="addCarDayOff-isNeedCost" value="0">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-detailSchedule" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-detailSchedule">
			<div class="modal-header">
				<h4 class="modal-title" id="title-detailSchedule">Reservation Detail</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 pb-10" style="border-bottom: 1px solid #e0e0e0;">
						<div class="order-details-customer-info">
							<ul>
								<li> <span><strong>Product</strong></span> <span id="detailSchedule-productName">-</span> </li>
								<li> <span><strong>Duration</strong></span> <span id="detailSchedule-carDuration">-</span> </li>
								<li> <span><strong>Notes</strong></span> <span id="detailSchedule-notes">-</span> </li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row pt-10">
					<div class="col-lg-6 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Source</h6>
								<p id="detailSchedule-source">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Title</h6>
								<p id="detailSchedule-title">-</p>
							</div>
							<div class="col-lg-6 col-sm-12 mb-10">
								<h6 class="mb-0">Date Time Start</h6>
								<p id="detailSchedule-dateTimeStart">-</p>
							</div>
							<div class="col-lg-6 col-sm-12 mb-10">
								<h6 class="mb-0">Date Time End</h6>
								<p id="detailSchedule-dateTimeEnd">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Customer Name</h6>
								<p id="detailSchedule-customerName">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Contact</h6>
								<p id="detailSchedule-customerContact">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Email</h6>
								<p id="detailSchedule-customerEmail">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Car Details</h6>
								<p id="detailSchedule-carDetails">-</p>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Pax</h6>
								<div class="order-details-customer-info">
									<ul class="ml-5">
										<li> <span>Adult</span> <span id="detailSchedule-numberOfAdult">0</span> </li>
										<li> <span>Child</span> <span id="detailSchedule-numberOfChild">0</span> </li>
										<li> <span>Infant</span> <span id="detailSchedule-numberOfInfant">0</span> </li>
									</ul>
								</div>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Customer Hotel</h6>
								<p id="detailSchedule-hotelName">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Pick Up Location</h6>
								<p id="detailSchedule-pickUpLocation">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Drop Off Location</h6>
								<p id="detailSchedule-dropOffLocation">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Tour Plan</h6>
								<p id="detailSchedule-tourPlan">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Remark</h6>
								<p id="detailSchedule-remark">-</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-danger" id="removeScheduleCar" data-idScheduleCar="0" data-detailCarVendor="" data-detailReservation="">Delete Schedule</button>
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
					<div class="col-lg-4 col-sm-12 mb-10">
						<h6 class="mb-0">Vendor</h6>
						<p id="detailDayOff-vendorName">-</p>
					</div>
					<div class="col-lg-8 col-sm-12 mb-10">
						<h6 class="mb-0">Car Details</h6>
						<p id="detailDayOff-carDetails">-</p>
					</div>
					<div class="col-lg-4 col-sm-6 mb-10">
						<h6 class="mb-0">Date Time Start</h6>
						<p id="detailDayOff-dateTimeStart">-</p>
					</div>
					<div class="col-lg-4 col-sm-6 mb-10">
						<h6 class="mb-0">Date Time End</h6>
						<p id="detailDayOff-dateTimeEnd">-</p>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<h6 class="mb-0">Duration</h6>
						<p id="detailDayOff-durationHour">-</p>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<h6 class="mb-0">Date Time Input</h6>
						<p id="detailDayOff-datetimeinput">-</p>
					</div>
					<div class="col-lg-8 col-sm-12 mb-10">
						<h6 class="mb-0">Detail Approval</h6>
						<p id="detailDayOff-detailApproval">-</p>
					</div>
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Reason</h6>
						<p id="detailDayOff-reason">-</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="detailDayOff-idDayoff" name="unscheduledListIdVendorCar" value="0">
				<button type="button" id="detailDayOff-btnDeleteDayOff" class="button button-danger mr-auto">Delete Day Off</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-detailDropOffPickUp" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-detailDropOffPickUp">
			<div class="modal-header">
				<h4 class="modal-title">Detail Drop Off / Pick Up Schedule</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row pt-10">
					<div class="col-lg-6 col-sm-12 border-right">
						<div class="row">
							<div class="col-sm-12 mb-10" id="detailDropOffPickUp-containerJobType"></div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Title</h6>
								<p id="detailDropOffPickUp-title">-</p>
							</div>
							<div class="col-lg-6 col-sm-12 mb-10">
								<h6 class="mb-0">Date Time Start</h6>
								<p id="detailDropOffPickUp-dateTimeStart">-</p>
							</div>
							<div class="col-lg-6 col-sm-12 mb-10">
								<h6 class="mb-0">Date Time End</h6>
								<p id="detailDropOffPickUp-dateTimeEnd">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Customer Name</h6>
								<p id="detailDropOffPickUp-customerName">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Contact</h6>
								<p id="detailDropOffPickUp-customerContact">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Email</h6>
								<p id="detailDropOffPickUp-customerEmail">-</p>
							</div>
							<div class="col-sm-12 mb-10 pb-10 border-bottom">
								<h6 class="mb-0">Remark</h6>
								<p id="detailDropOffPickUp-remark">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Vendor</h6>
								<p id="detailDropOffPickUp-vendor">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Car Details</h6>
								<p id="detailDropOffPickUp-carDetails">-</p>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Status</h6>
								<p id="detailDropOffPickUp-status">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-2">Driver Handle *</h6>
								<select id="detailDropOffPickUp-driverHandle" name="detailDropOffPickUp-driverHandle" class="form-control" option-all="Not Set"></select>
							</div>
							<div class="col-sm-12 mb-10 d-none" id="detailDropOffPickUp-containerDropOffLocation">
								<h6 class="mb-2">Drop Off Location *</h6>
								<textarea class="form-control mb-10 dropOffPickUpTextArea" placeholder="Drop Off Location" id="detailDropOffPickUp-dropOffLocation" name="detailDropOffPickUp-dropOffLocation"></textarea>
							</div>
							<div class="col-sm-12 mb-10 d-none" id="detailDropOffPickUp-containerPickUpLocation">
								<h6 class="mb-2">Pick Up Location *</h6>
								<textarea class="form-control mb-10 dropOffPickUpTextArea" placeholder="Pick Up Location" id="detailDropOffPickUp-pickUpLocation" name="detailDropOffPickUp-pickUpLocation"></textarea>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-2">Notes</h6>
								<textarea class="form-control mb-10 dropOffPickUpTextArea" placeholder="Notes" id="detailDropOffPickUp-notes" name="detailDropOffPickUp-notes"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="row pt-10 d-none" id="detailDropOffPickUp-containerNoScheduleWarning">
					<div class="col-sm-12">
						<div class="alert alert-warning" role="alert">
							<i class="zmdi zmdi-info"></i> <span>The car schedule is incomplete. Please complete the car schedule first.</span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="detailDropOffPickUp-jobType" name="detailDropOffPickUp-jobType" value="1">
				<input type="hidden" id="detailDropOffPickUp-idScheduleCar" name="detailDropOffPickUp-idScheduleCar" value="0">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<style>
@media (max-width: 767px) {
	.reservationTableElement{
		overflow: scroll !important;
	}
}
.order-details-customer-info ul li span:first-child {
    width: 70px !important;
}

.reservationListElement, .reservationTableElement{
	border: 1px solid #e0e0e0;
	max-height: 120px;
	overflow: hidden;
	background: #fff;
}
.scrollList{
	height: 450px;
	width: 104%;
	overflow-y: scroll;
}
.unscheduledListElement {
	border: 1px solid #e0e0e0;
	max-height: 120px;
	overflow: hidden;
}
#table-carScheduleList tbody tr td {
	padding: 2px;
	padding-top: 4px;
}
.bgGrey{
	background-color: #eee !important;
}
.btnAddCarSchedule{
	height: 20px !important;
}

.btnAddCarSchedule i {
	line-height: 18px !important;
}

.btnSchedule{
	font-weight: bold;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}

:root {
  --schedule-width-per-hour: 10px;
}
.tdSpacer{width: calc(1 * var(--schedule-width-per-hour))}
.sch-1-hours{width: calc(1 * var(--schedule-width-per-hour))}
.sch-2-hours{width: calc(2 * var(--schedule-width-per-hour))}
.sch-3-hours{width: calc(3 * var(--schedule-width-per-hour))}
.sch-4-hours{width: calc(4 * var(--schedule-width-per-hour))}
.sch-5-hours{width: calc(5 * var(--schedule-width-per-hour))}
.sch-6-hours{width: calc(6 * var(--schedule-width-per-hour))}
.sch-7-hours{width: calc(7 * var(--schedule-width-per-hour))}
.sch-8-hours{width: calc(8 * var(--schedule-width-per-hour))}
.sch-9-hours{width: calc(9 * var(--schedule-width-per-hour))}
.sch-10-hours{width: calc(10 * var(--schedule-width-per-hour))}
.sch-11-hours{width: calc(11 * var(--schedule-width-per-hour))}
.sch-12-hours{width: calc(12 * var(--schedule-width-per-hour))}
.sch-13-hours{width: calc(13 * var(--schedule-width-per-hour))}
.sch-14-hours{width: calc(14 * var(--schedule-width-per-hour))}
.sch-15-hours{width: calc(15 * var(--schedule-width-per-hour))}
.sch-16-hours{width: calc(16 * var(--schedule-width-per-hour))}
.sch-17-hours{width: calc(17 * var(--schedule-width-per-hour))}
.sch-18-hours{width: calc(18 * var(--schedule-width-per-hour))}
.sch-19-hours{width: calc(19 * var(--schedule-width-per-hour))}
.sch-20-hours{width: calc(20 * var(--schedule-width-per-hour))}
.sch-21-hours{width: calc(21 * var(--schedule-width-per-hour))}
.sch-22-hours{width: calc(22 * var(--schedule-width-per-hour))}
.sch-23-hours{width: calc(23 * var(--schedule-width-per-hour))}
.sch-24-hours{width: calc(24 * var(--schedule-width-per-hour))}

.dropOffPickUpTextArea {
  height: 90px !important;
}
</style>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/Schedule/scheduleCar.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>