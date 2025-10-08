<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Resevartion <span> / List of reservation - compose list of details and cost</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<input type="hidden" id="minHourDateFilterTomorrow" name="minHourDateFilterTomorrow" value="<?=$minHourDateFilterTomorrow?>">
			<input type="hidden" id="maxHourDateFilterToday" name="maxHourDateFilterToday" value="<?=$maxHourDateFilterToday?>">
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseSetDetails"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
			<button class="button button-info button-sm pull-right" id="btnCreateVoucher" data-toggle="modal" data-target="#modal-selectReservationVoucher"><span><i class="fa fa-plus"></i>Voucher</span></button>
			<button class="button button-primary button-sm pull-right" id="btnCreateReservation" data-action="insert" data-toggle="modal" data-target="#modal-editorReservation"><span><i class="fa fa-plus"></i>Create New</span></button>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
	<div class="box" id="reservationFilter">
		<div class="box-body">
			<div class="row">
				<div class="col-lg-2 col-sm-12">
					<div class="form-group">
						<label for="optionReservationType" class="control-label">Reservation Type</label>
						<select id="optionReservationType" name="optionReservationType" class="form-control" option-all="All Reservation Type" option-all-value="0"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionReservationStatus" class="control-label">Reservation Status</label>
						<select id="optionReservationStatus" name="optionReservationStatus" class="form-control">
							<option value="-4">All Status Without Cancel</option>
							<option value="">All Status</option>
							<option value="0">Unprocessed</option>
							<option value="1">Processed By Admin</option>
							<option value="2">Scheduled</option>
							<option value="3">On Process</option>
							<option value="4">Done</option>
							<option value="-1">Cancel</option>
						</select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionYear" class="control-label">Year</label>
						<select id="optionYear" name="optionYear" class="form-control"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-3">
					<div class="form-group">
						<label for="startDate" class="control-label">Reservation Date</label>
						<div class="input-group">
							<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" aria-describedby="iconStartDate">
							<div class="input-group-append mb-10">
								<span class="input-group-text iconInputDate" id="iconStartDate">
									<i class="fa fa-calendar"></i>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-2 col-sm-3">
					<div class="form-group">
						<label for="endDate" class="control-label">.</label>
						<div class="input-group">
							<input type="text" class="form-control input-date-single mb-10 text-center" id="endDate" name="endDate" aria-describedby="iconEndDate">
							<div class="input-group-append mb-10">
								<span class="input-group-text iconInputDate" id="iconEndDate">
									<i class="fa fa-calendar"></i>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionSource" class="control-label">Source</label>
						<select id="optionSource" name="optionSource" class="form-control" option-all="All Source"></select>
					</div>
				</div>
				<div class="col-lg-3 col-sm-6">
					<div class="form-group">
						<label for="bookingCodeFilter" class="control-label">Booking Code</label>
						<input type="text" class="form-control mb-10" id="bookingCodeFilter" name="bookingCodeFilter">
					</div>
				</div>
				<div class="col-lg-3 col-sm-6">
					<div class="form-group">
						<label for="customerNameFilter" class="control-label">Customer Name</label>
						<input type="text" class="form-control mb-10" id="customerNameFilter" name="customerNameFilter">
					</div>
				</div>
				<div class="col-lg-3 col-sm-6">
					<div class="form-group">
						<label for="locationName" class="control-label">Customer Hotel / Pick Up / Drop Off</label>
						<input type="text" class="form-control mb-10" id="locationName" name="locationName">
					</div>
				</div>
				<div class="col-lg-3 col-sm-6">
					<div class="form-group">
						<label for="optionTransportStatus" class="control-label">Transport Status</label>
						<select id="optionTransportStatus" name="optionTransportStatus" class="form-control">
							<option value="">All Transport Status</option>
							<option value="1">With Transport</option>
							<option value="-1">Without Transport</option>
						</select>
					</div>
				</div>
				<div class="col-lg-5 col-sm-12">
					<div class="form-group">
						<label for="reservationTitleFilter" class="control-label">Reservation Title</label>
						<input type="text" class="form-control mb-10" id="reservationTitleFilter" name="reservationTitleFilter">
					</div>
				</div>
				<div class="col-lg-4 col-sm-12">
					<div class="form-group">
						<label for="optionPartner" class="control-label">Partner</label>
						<select id="optionPartner" name="optionPartner" class="form-control" option-all="All Patner"></select>
					</div>
				</div>
				<div class="col-lg-3 col-sm-12">
					<div class="form-group">
						<label for="optionCollectPaymentStatus" class="control-label">Collect Payment Status</label>
						<select id="optionCollectPaymentStatus" name="optionCollectPaymentStatus" class="form-control">
							<option value="">All Status</option>
							<option value="1">Include Collect Payment</option>
							<option value="0">Non Collect Payment</option>
						</select>
					</div>
				</div>
				<div class="col-sm-12">
					<button class="button button-success button-sm" id="filterDataReservation"><span><i class="fa fa-filter"></i>Search Data</span></button>
					<a class="button button-info button-sm pull-right d-none" id="excelDataReservation" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
					<a class="button button-warning button-sm pull-right d-none mr-2" id="excelDataVendorBook" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Booking Vendor</span></a>
				</div>
			</div>
		</div>
	</div>
	<div class="box mt-20">
		<div class="box-body">
			<div class="row">
				<div class="col-12">
					<div class="row">
						<div class="col-lg-8 col-sm-12 mb-10 d-flex">
							<span id="tableDataCountReservation" class="align-self-center"></span>
						</div>
						<div class="col-lg-4 col-sm-12 mb-10">
							<div class="row">
								<div class="col-3 text-right align-self-center px-1">Order By</div>
								<div class="col-6 px-1">
									<select id="optionOrderBy" name="optionOrderBy" class="form-control">
										<option value="1">Reservation Number</option>
										<option value="2">Reservation Date</option>
									</select>
								</div>
								<div class="col-3 px-1">
									<select id="optionOrderType" name="optionOrderType" class="form-control">
										<option value="DESC">Desc</option>
										<option value="ASC">Asc</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-5 responsive-table-container">
						<table class="table" id="table-reservation">
							<thead class="thead-light">
								<tr>
									<th width="70" class="text-right">No.</th>
									<th width="300">Activity</th>
									<th>Customer</th>
									<th width="200">Location</th>
									<th width="140">Booking</th>
									<th width="250">Additional Info</th>
									<th width="40"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th colspan="7" class="text-center">No data is shown, please apply filter first</th>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="row mt-10">
						<div class="col-sm-12 mb-10">
							<ul class="pagination" id="tablePaginationReservation"></ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="box d-none">
		<div class="box-body">
			<div class="row">
				<div class="col-lg-8 col-sm-6" style="border-bottom: 1px solid #e0e0e0;"><h5 id="reservationTitleStr">-</h5></div>
				<div class="col-lg-4 col-sm-6 text-right" style="border-bottom: 1px solid #e0e0e0;"><b id="reservationDateTimeStr">-</b></div>
				<div class="col-lg-4 col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<p id="reservationTypeBadge">-</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Duration</h6>
							<p id="durationOfDayStr">-</p>
						</div>
					</div>
					<h6 class="mb-0">Customer Detail</h6>
					<div class="order-details-customer-info">
						<ul class="ml-5">
							<li> <span>Cust. Name</span> <span id="custNameStr">-</span> </li>
							<li> <span>Contact</span> <span id="custContactStr">-</span> </li>
							<li> <span>Email</span> <span id="custEmailStr">-</span> </li>
						</ul>
					</div>
					<h6 class="mt-15 mb-0">Pax</h6>
					<div class="order-details-customer-info mb-10">
						<ul class="ml-5">
							<li> <span>Adult</span> <span id="paxAdultStr">0</span> </li>
							<li> <span>Child</span> <span id="paxChildStr">0</span> </li>
							<li> <span>Infant</span> <span id="paxInfantStr">0</span> </li>
						</ul>
					</div>
				</div>
				<div class="col-lg-4 col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Area</h6>
							<p id="areaNameStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Hotel</h6>
							<p id="hotelNameStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Pick Up</h6>
							<p id="pickUpStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Drop Off</h6>
							<p id="dropOffStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Tour Plan</h6>
							<p id="tourPlanStr">-</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Booking Code</h6>
							<p id="bookingCodeStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Revenue Nominal</h6>
							<p id="paymentNominalStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Remark</h6>
							<p id="remarkStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Special Request</h6>
							<p id="specialRequestStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Additional Confirmation Info <i class="fa fa-plus pull-right text-info text16px" id="btnAddMailAdditionalInfo" data-toggle="modal" data-target="#modal-additionalConfirmationInfo"></i></h6>
							<div class="order-details-customer-info">
								<ul id="additionalConfirmationInfoList"></ul>
							</div>
							<div class="alert alert-dark d-none mt-10 px-2 py-2" role="alert" id="alertOpenReconfirmationMenu">Reconfirmation scheduled, change additional info in the reconfirmation menu</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row pb-10" style="border-bottom: 1px solid #e0e0e0;">
				<div class="col-sm-12">
					<div class="alert alert-solid-warning" role="alert" id="specialCaseCostAlert"><b>Special case detected!</b> &nbsp; &nbsp; <span id="specialCaseCostText"></span></div>
				</div>
			</div>
			<div class="row pb-10" style="border-bottom: 1px solid #e0e0e0;">
				<div class="col-lg-8 col-sm-6 pt-10 mb-10">
					<div class="row">
						<div class="col-sm-6">
							<h6 class="mb-0">First Input</h6>
							<p id="firstInputStr">-</p>
						</div>
						<div class="col-sm-6">
							<h6 class="mb-0">Last Update</h6>
							<p id="lastUpdateStr">-</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-sm-6 pt-10 mb-10 text-right">
					<button class="button button-primary button-sm" id="btnEditReservationFormDetails"><span><i class="fa fa-pencil"></i>Edit Reservation Data</span></button>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 mt-20"><h5>List Of Details</h5></div>
				<div class="col-sm-6 mt-20 text-right">
					<button class="button button-info button-sm d-none" id="btnVoucherListDetails"><span><i class="fa fa-ticket"></i>Voucher</span></button>
					<button class="button button-warning button-sm" id="btnAutoAddDetails"><span><i class="fa fa-magic"></i>Auto Add Details</span></button>
					<button class="button button-primary button-sm" id="btnAddNewDetails" data-action="insert" data-toggle="modal" data-target="#modal-editorReservationDetails"><span><i class="fa fa-plus"></i>Add New Details</span></button>
				</div>
			</div>
			<div class="row" id="listOfDetails">
				<div class="col-sm-12 text-center mt-20 mb-20" id="noDataListOfDetails"><h6 class="text-warning">No products have been added yet</h6></div>
			</div>
			<div class="row mt-20 pt-15" style="border-top: 1px solid #e0e0e0;">
				<input type="hidden" id="arrIdReservationDetailCosts" name="arrIdReservationDetailCosts" value="">
				<input type="hidden" id="idReservationDetailCosts" name="idReservationDetailCosts" value="0">
				<input type="hidden" id="idAreaPickup" name="idAreaPickup" value="0">
				<input type="hidden" id="idSourceDetailCosts" name="idSourceDetailCosts" value="0">
				<input type="hidden" id="arrDateSchedule" name="arrDateSchedule" value="[]">
				<div class="col-lg-10 col-sm-8"><h6>Total Income [IDR]</h6></div><div class="col-lg-2 col-sm-4"><h6 class="text-right" id="totalIncomeStr">0</h6></div>
				<div class="col-lg-10 col-sm-8"><h6>Total Cost [IDR]</h6></div><div class="col-lg-2 col-sm-4"><h6 class="text-right" id="totalCostStr">0</h6></div>
				<div class="col-lg-10 col-sm-8 mt-15"><h6>Margin [IDR]</h6></div><div class="col-lg-2 col-sm-4" style="border-top: 1px solid #e0e0e0;"><h4 class="text-right" id="totalMarginStr">0</h4></div>
			</div>
		</div>
		<div class="box-body" style="border-top: 1px solid #e0e0e0;">
			<div class="row">
				<div class="col-6">
					<button class="button button-sm button-info" style="width: 120px;" id="btnPreviousReservationDetailCost"><i class="fa fa-arrow-left"></i><span>Previous</span></button>
				</div>
				<div class="col-6 text-right">
					<button class="button button-sm button-info button-icon-right" style="width: 120px;" id="btnNextReservationDetailCost"><i class="fa fa-arrow-right"></i><span>Next</span></button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorReservation" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="content-editorReservation">
			<div class="modal-header">
				<h4 class="modal-title" id="title-editorReservation">Reservation Form</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-lg-4 col-sm-12 border-right">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="optionReservationTypeEditor" class="control-label">Type</label>
									<select id="optionReservationTypeEditor" name="optionReservationTypeEditor" class="form-control"></select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="optionSourceEditor" class="control-label">Source</label>
									<select id="optionSourceEditor" name="optionSourceEditor" class="form-control"></select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="reservationTitle" class="control-label">Title</label>
									<input type="text" class="form-control mb-10" id="reservationTitle" name="reservationTitle" value="" placeholder="Reservation Title">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="productDetailsUrl" class="control-label">Product Details Url</label>
									<input type="text" class="form-control mb-10" id="productDetailsUrl" name="productDetailsUrl" value="" placeholder="Product Details URL">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="customerName" class="control-label">Customer Name</label>
									<input type="text" class="form-control mb-10" id="customerName" name="customerName" placeholder="Customer Name">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="customerContact" class="control-label">Contact</label>
									<input type="text" class="form-control mb-10" id="customerContact" name="customerContact" placeholder="Customer Phone">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="customerEmail" class="control-label">Email</label>
									<input type="text" class="form-control mb-10" id="customerEmail" name="customerEmail" placeholder="Customer Email">
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-12 border-right">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="selfDriveStatus" class="control-label">Self Drive Status</label>
									<select class="form-control" id="selfDriveStatus" name="selfDriveStatus">
										<option value="0">Non Self Drive</option>
										<option value="1">Self Drive</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="durationOfDay" class="control-label">Duration (Day)</label>
									<input type="text" class="form-control mb-10 text-right" id="durationOfDay" name="durationOfDay" value="1" onkeypress="maskNumberInput(1, 99, 'durationOfDay')">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="reservationDate" class="control-label">Date Start</label>
									<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationDate" name="reservationDate" readonly>
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group required">
									<label for="reservationHour" class="control-label">Time</label>
									<select class="form-control" id="reservationHour" name="reservationHour"></select>
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group">
									<label for="reservationMinute" class="control-label">.</label>
									<select class="form-control" id="reservationMinute" name="reservationMinute"></select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="reservationDateEnd" class="control-label">Date End</label>
									<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationDateEnd" name="reservationDateEnd" readonly disabled>
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group required">
									<label for="reservationHourEnd" class="control-label">Time</label>
									<select class="form-control" id="reservationHourEnd" name="reservationHourEnd" disabled></select>
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group">
									<label for="reservationMinuteEnd" class="control-label">.</label>
									<select class="form-control" id="reservationMinuteEnd" name="reservationMinuteEnd" disabled></select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="hotelName" class="control-label">Customer Hotel</label>
									<input type="text" class="form-control mb-10" id="hotelName" name="hotelName" placeholder="Customer Hotel">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="optionPickUpArea" class="control-label">Pick Up Area</label>
									<select class="form-control" id="optionPickUpArea" name="optionPickUpArea" option-all="Without Transfer" option-all-value="-1"></select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="pickUpLocation" class="control-label">Pick Up Location</label>
									<input type="text" class="form-control mb-10" id="pickUpLocation" name="pickUpLocation" placeholder="Pick Up Location">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="pickUpLocationUrl" class="control-label">Pick Up Location Url/Link</label>
									<input type="text" class="form-control mb-10" id="pickUpLocationUrl" name="pickUpLocationUrl" placeholder="Pick Up Location URL/Link">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="dropOffLocation" class="control-label">Drop Off Location</label>
									<input type="text" class="form-control mb-10" id="dropOffLocation" name="dropOffLocation" placeholder="Drop Off Location">
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-12">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group required">
									<label for="numberOfAdult" class="control-label">Adult</label>
									<input type="text" class="form-control mb-10" id="numberOfAdult" name="numberOfAdult" placeholder="Adult" onkeypress="maskNumberInput(0, 99, 'numberOfAdult')">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="numberOfChild" class="control-label">Child</label>
									<input type="text" class="form-control mb-10" id="numberOfChild" name="numberOfChild" placeholder="Child" onkeypress="maskNumberInput(0, 99, 'numberOfChild')">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="numberOfInfant" class="control-label">Infant</label>
									<input type="text" class="form-control mb-10" id="numberOfInfant" name="numberOfInfant" placeholder="Infant" onkeypress="maskNumberInput(0, 99, 'numberOfInfant')">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="bookingCode" class="control-label">Booking Code</label>
									<input type="text" class="form-control mb-10" id="bookingCode" name="bookingCode" placeholder="Booking Code">
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group required">
									<label for="reservationPriceType" class="control-label">Currency</label>
									<select class="form-control" id="reservationPriceType" name="reservationPriceType">
										<option value="IDR">IDR</option>
										<option value="USD">USD</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4 px-1">
								<div class="form-group required">
									<label for="reservationPriceInteger" class="control-label">Integer</label>
									<input type="text" class="form-control mb-10 text-right" id="reservationPriceInteger" name="reservationPriceInteger" onkeyup="calculateReservationPriceIDR()" onkeypress="maskNumberInput(0, 999999999, 'reservationPriceInteger');">
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group required">
									<label for="reservationPriceDecimal" class="control-label">Comma</label>
									<input type="text" class="form-control mb-10 text-right decimalInput" id="reservationPriceDecimal" name="reservationPriceDecimal" onkeyup="calculateReservationPriceIDR()" onkeypress="maskNumberInput(0, 99, 'reservationPriceDecimal');">
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="currencyExchange" class="control-label">Currency Exchange</label>
									<input type="text" class="form-control mb-10 text-right" id="currencyExchange" name="currencyExchange" readonly onkeyup="calculateReservationPriceIDR()" onkeypress="maskNumberInput(1, 999999999, 'currencyExchange')">
								</div>
							</div>
							<div class="col-sm-7" style="padding-left: 6px;">
								<div class="form-group ">
									<label for="reservationPriceIDR" class="control-label">Price (IDR)</label>
									<input type="text" class="form-control mb-10 text-right" id="reservationPriceIDR" name="reservationPriceIDR" readonly="readonly">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-0">
									<label for="tourPlan" class="control-label">Tour Plan</label>
									<textarea class="form-control mb-10 reservationTextArea" placeholder="Tour Plan" id="tourPlan" name="tourPlan"></textarea>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-0">
									<label for="remark" class="control-label">Remark</label>
									<textarea class="form-control mb-10 reservationTextArea" placeholder="Remark" id="remark" name="remark"></textarea>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="specialRequest" class="control-label">Special Request</label>
									<input type="text" class="form-control mb-10" id="specialRequest" name="specialRequest" placeholder="Special Request">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationEditor" name="idReservationEditor" value="0">
				<input type="hidden" id="reservationStatusEditor" name="reservationStatusEditor" value="0">
				<input type="hidden" id="refundTypeEditor" name="refundTypeEditor" value="0">
				<button class="button button-primary" id="saveReservation">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorReservationDetails" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="content-editorReservationDetails">
			<div class="modal-header">
				<h4 class="modal-title" id="title-editorReservationDetails">Reservation Details</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row mb-15 pb-15" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12">
						<h6>Select Schedule Date. Duration : <span id="reservationDetailsDurationOfDayStr"></span></h6>
					</div>
					<div class="col-sm-12 mt-10">
						<div class="adomx-checkbox-radio-group inline" id="containerScheduleDate"></div>
					</div>
				</div>
				<div class="row">
					<ul class="nav nav-tabs mb-0 ml-10" id="tabsPanelReservationDetails">
						<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#transportProductTab">Transport</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#selfDriveProductTab">Self Drive</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#ticketProductTab">Ticket</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade active" id="transportProductTab">
							<div class="row mt-20 pl-10 pr-10">
								<div class="col-sm-8">
									<div class="form-group">
										<label for="optionProductTransportDriver" class="control-label">Activity - Driver Type</label>
										<select id="optionProductTransportDriver" name="optionProductTransportDriver" class="form-control"></select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group required">
										<label for="transportProductNominal" class="control-label">Driver Fee</label>
										<input type="text" class="form-control mb-10 text-right" id="transportProductNominal" name="transportProductNominal" value="0" onkeypress="maskNumberInput(0, 999999999, 'transportProductNominal')" onkeyup="calculateTotalFeeCostTransport()">
									</div>
								</div>
								<div class="col-lg-6 col-sm-12" style="border-right: 1px solid #e0e0e0;">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label for="ticketCostPax" class="control-label">Pax (Ticket)</label>
												<div class="input-group">
													<input type="text" class="form-control mb-10 text-right" id="ticketCostPax" name="ticketCostPax" value="0" aria-describedby="ticketCostPaxSuffix" onkeypress="maskNumberInput(0, 999, 'ticketCostPax')" onkeyup="calculateTicketCost(true)">
													<div class="input-group-append mb-10">
														<span class="input-group-text" id="ticketCostPaxSuffix">Pax</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="costPerPaxTicket" class="control-label">Cost Per Pax</label>
												<div class="input-group">
													<div class="input-group-prepend mb-10">
														<span class="input-group-text" id="costPerPaxTicketPrefix">@</span>
													</div>
													<input type="text" class="form-control mb-10 text-right" id="costPerPaxTicket" name="costPerPaxTicket" value="0" aria-describedby="costPerPaxTicketPrefix" onkeypress="maskNumberInput(0, 999999999, 'costPerPaxTicket')" onkeyup="calculateTicketCost(true)">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="totalTicketCost" class="control-label">Total Ticket Cost</label>
												<input type="text" class="form-control mb-10 text-right" id="totalTicketCost" name="totalTicketCost" value="0" readonly="readonly" onkeypress="maskNumberInput(0, 999999999, 'totalTicketCost')" onkeyup="calculateTicketCost(true)">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="parkingCostPax" class="control-label">Pax (Parking)</label>
												<div class="input-group">
													<input type="text" class="form-control mb-10 text-right" id="parkingCostPax" name="parkingCostPax" value="0" aria-describedby="parkingCostPaxSuffix" onkeypress="maskNumberInput(0, 999, 'parkingCostPax')" onkeyup="calculateParkingCost(true)">
													<div class="input-group-append mb-10">
														<span class="input-group-text" id="parkingCostPaxSuffix">Pax</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="costPerPaxParking" class="control-label">Cost Per Pax</label>
												<div class="input-group">
													<div class="input-group-prepend mb-10">
														<span class="input-group-text" id="costPerPaxParkingPrefix">@</span>
													</div>
													<input type="text" class="form-control mb-10 text-right" id="costPerPaxParking" name="costPerPaxParking" value="0" aria-describedby="costPerPaxParkingPrefix" onkeypress="maskNumberInput(0, 999999999, 'costPerPaxParking')" onkeyup="calculateParkingCost(true)">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="totalParkingCost" class="control-label">Total Parking Cost</label>
												<input type="text" class="form-control mb-10 text-right" id="totalParkingCost" name="totalParkingCost" value="0" readonly="readonly" onkeypress="maskNumberInput(0, 999999999, 'totalParkingCost')" onkeyup="calculateParkingCost(true)">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="mineralWaterCostPax" class="control-label">Pax (Mineral Water)</label>
												<div class="input-group">
													<input type="text" class="form-control mb-10 text-right" id="mineralWaterCostPax" name="mineralWaterCostPax" value="0" aria-describedby="mineralWaterCostPaxSuffix" onkeypress="maskNumberInput(0, 999, 'mineralWaterCostPax')" onkeyup="calculateMineralWaterCost(true)">
													<div class="input-group-append mb-10">
														<span class="input-group-text" id="mineralWaterCostPaxSuffix">Pax</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="costPerPaxMineralWater" class="control-label">Cost Per Pax</label>
												<div class="input-group">
													<div class="input-group-prepend mb-10">
														<span class="input-group-text" id="costPerPaxMineralWaterPrefix">@</span>
													</div>
													<input type="text" class="form-control mb-10 text-right" id="costPerPaxMineralWater" name="costPerPaxMineralWater" value="0" aria-describedby="costPerPaxMineralWaterPrefix" onkeypress="maskNumberInput(0, 999999999, 'costPerPaxMineralWater')" onkeyup="calculateMineralWaterCost(true)">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="totalMineralWaterCost" class="control-label">Total Mineral Water Cost</label>
												<input type="text" class="form-control mb-10 text-right" id="totalMineralWaterCost" name="totalMineralWaterCost" value="0" readonly="readonly" onkeypress="maskNumberInput(0, 999999999, 'totalMineralWaterCost')" onkeyup="calculateMineralWaterCost(true)">
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-6 col-sm-12">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label for="breakfastCostPax" class="control-label">Pax (Breakfast)</label>
												<div class="input-group">
													<input type="text" class="form-control mb-10 text-right" id="breakfastCostPax" name="breakfastCostPax" value="0" aria-describedby="breakfastCostPaxSuffix" onkeypress="maskNumberInput(0, 999, 'breakfastCostPax')" onkeyup="calculateBreakfastCost(true)">
													<div class="input-group-append mb-10">
														<span class="input-group-text" id="breakfastCostPaxSuffix">Pax</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="costPerPaxBreakfast" class="control-label">Cost Per Pax</label>
												<div class="input-group">
													<div class="input-group-prepend mb-10">
														<span class="input-group-text" id="costPerPaxBreakfastPrefix">@</span>
													</div>
													<input type="text" class="form-control mb-10 text-right" id="costPerPaxBreakfast" name="costPerPaxBreakfast" value="0" aria-describedby="costPerPaxBreakfastPrefix" onkeypress="maskNumberInput(0, 999999999, 'costPerPaxBreakfast')" onkeyup="calculateBreakfastCost(true)">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="totalBreakfastCost" class="control-label">Total Breakfast Cost</label>
												<input type="text" class="form-control mb-10 text-right" id="totalBreakfastCost" name="totalBreakfastCost" value="0" readonly="readonly" onkeypress="maskNumberInput(0, 999999999, 'totalBreakfastCost')" onkeyup="calculateBreakfastCost(true)">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="lunchCostPax" class="control-label">Pax (Lunch)</label>
												<div class="input-group">
													<input type="text" class="form-control mb-10 text-right" id="lunchCostPax" name="lunchCostPax" value="0" aria-describedby="lunchCostPaxSuffix" onkeypress="maskNumberInput(0, 999, 'lunchCostPax')" onkeyup="calculateLunchCost(true)">
													<div class="input-group-append mb-10">
														<span class="input-group-text" id="lunchCostPaxSuffix">Pax</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="costPerPaxLunch" class="control-label">Cost Per Pax</label>
												<div class="input-group">
													<div class="input-group-prepend mb-10">
														<span class="input-group-text" id="costPerPaxLunchPrefix">@</span>
													</div>
													<input type="text" class="form-control mb-10 text-right" id="costPerPaxLunch" name="costPerPaxLunch" value="0" aria-describedby="costPerPaxLunchPrefix" onkeypress="maskNumberInput(0, 999999999, 'costPerPaxLunch')" onkeyup="calculateLunchCost(true)">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="totalLunchCost" class="control-label">Total Lunch Cost</label>
												<input type="text" class="form-control mb-10 text-right" id="totalLunchCost" name="totalLunchCost" value="0" readonly="readonly" onkeypress="maskNumberInput(0, 999999999, 'totalLunchCost')" onkeyup="calculateLunchCost(true)">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="bonusPax" class="control-label">Pax (Bonus)</label>
												<div class="input-group">
													<input type="text" class="form-control mb-10 text-right" id="bonusPax" name="bonusPax" value="0" aria-describedby="bonusPaxSuffix" onkeypress="maskNumberInput(0, 999, 'bonusPax')" onkeyup="calculateBonus(true)">
													<div class="input-group-append mb-10">
														<span class="input-group-text" id="bonusPaxSuffix">Pax</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="nominalPerPaxBonus" class="control-label">Bonus Per Pax</label>
												<div class="input-group">
													<div class="input-group-prepend mb-10">
														<span class="input-group-text" id="nominalPerPaxBonusPrefix">@</span>
													</div>
													<input type="text" class="form-control mb-10 text-right" id="nominalPerPaxBonus" name="nominalPerPaxBonus" value="0" aria-describedby="nominalPerPaxBonusPrefix" onkeypress="maskNumberInput(0, 999999999, 'nominalPerPaxBonus')" onkeyup="calculateBonus(true)">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="totalBonus" class="control-label">Total Bonus</label>
												<input type="text" class="form-control mb-10 text-right" id="totalBonus" name="totalBonus" value="0" readonly="readonly" onkeypress="maskNumberInput(0, 999999999, 'totalBonus')" onkeyup="calculateBonus(true)">
											</div>
										</div>
										<div class="col-sm-8 mb-10 pt-10"><h5>Total Fee + Costs</h5></div>
										<div class="col-sm-4 mb-10 pt-10 text-right"><h5 id="totalFeeCostsTransportNominal">0</h5></div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="transportProductNotes" class="control-label">Notes</label>
										<textarea class="form-control mb-10" id="transportProductNotes" name="transportProductNotes"></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="selfDriveProductTab">
							<div class="row mt-20 pl-10 pr-10">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="optionSelfDriveTypeVendor" class="control-label">Car Type & Vendor</label>
										<select id="optionSelfDriveTypeVendor" name="optionSelfDriveTypeVendor" class="form-control mb-10 select2"></select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="optionDurationSelfDrive" class="control-label">Duration</label>
										<select id="optionDurationSelfDrive" name="optionDurationSelfDrive" class="form-control mb-10 select2"></select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group required">
										<label for="selfDriveProductNominal" class="control-label">Nominal Price</label>
										<input type="text" class="form-control mb-10 text-right" id="selfDriveProductNominal" name="selfDriveProductNominal" value="0" onkeypress="maskNumberInput(0, 999999999, 'selfDriveProductNominal')">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="selfDriveProductNotes" class="control-label">Notes</label>
										<textarea class="form-control mb-10" id="selfDriveProductNotes" name="selfDriveProductNotes"></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="ticketProductTab">
							<div class="row mt-20 pl-10 pr-10">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="optionProductTicketVendor" class="control-label">Ticket & Vendor</label>
										<select id="optionProductTicketVendor" name="optionProductTicketVendor" class="form-control"></select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group required">
										<label for="ticketAdultPax" class="control-label">Adult Pax</label>
										<div class="input-group">
											<input type="text" class="form-control mb-10 text-right pl-2" id="ticketAdultPax" name="ticketAdultPax" value="0" aria-describedby="adultPaxSuffix" onkeypress="maskNumberInput(0, 999, 'ticketAdultPax')" onkeyup="calculateTicketProduct()">
											<div class="input-group-append mb-10">
												<span class="input-group-text" id="adultPaxSuffix">Pax</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group required">
										<label for="pricePerPaxAdult" class="control-label">Price Per Pax</label>
										<div class="input-group">
											<div class="input-group-prepend mb-10">
												<span class="input-group-text" id="adultPricePerPaxPrefix">@</span>
											</div>
											<input type="text" class="form-control mb-10 text-right" id="pricePerPaxAdult" name="pricePerPaxAdult" value="0" aria-describedby="adultPricePerPaxPrefix" onkeypress="maskNumberInput(0, 999999999, 'pricePerPaxAdult')" onkeyup="calculateTicketProduct()">
										</div>
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group required">
										<label for="totalPriceAdult" class="control-label">Total Price</label>
										<input type="text" class="form-control mb-10 text-right" id="totalPriceAdult" name="totalPriceAdult" value="0" readonly="readonly">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group required">
										<label for="ticketChildPax" class="control-label">Child Pax</label>
										<div class="input-group">
											<input type="text" class="form-control mb-10 text-right pl-2" id="ticketChildPax" name="ticketChildPax" value="0" aria-describedby="childPaxSuffix" onkeypress="maskNumberInput(0, 999, 'ticketChildPax')" onkeyup="calculateTicketProduct()">
											<div class="input-group-append mb-10">
												<span class="input-group-text" id="childPaxSuffix">Pax</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group required">
										<label for="pricePerPaxChild" class="control-label">Price Per Pax</label>
										<div class="input-group">
											<div class="input-group-prepend mb-10">
												<span class="input-group-text" id="childPricePerPaxPrefix">@</span>
											</div>
											<input type="text" class="form-control mb-10 text-right" id="pricePerPaxChild" name="pricePerPaxChild" value="0" aria-describedby="childPricePerPaxPrefix" onkeypress="maskNumberInput(0, 999999999, 'pricePerPaxChild')" onkeyup="calculateTicketProduct()">
										</div>
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group required">
										<label for="totalPriceChild" class="control-label">Total Price</label>
										<input type="text" class="form-control mb-10 text-right" id="totalPriceChild" name="totalPriceChild" value="0" readonly="readonly">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group required">
										<label for="ticketInfantPax" class="control-label">Infant Pax</label>
										<div class="input-group">
											<input type="text" class="form-control mb-10 text-right pl-2" id="ticketInfantPax" name="ticketInfantPax" value="0" aria-describedby="infantPaxSuffix" onkeypress="maskNumberInput(0, 999, 'ticketInfantPax')" onkeyup="calculateTicketProduct()">
											<div class="input-group-append mb-10">
												<span class="input-group-text" id="infantPaxSuffix">Pax</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group required">
										<label for="pricePerPaxInfant" class="control-label">Price Per Pax</label>
										<div class="input-group">
											<div class="input-group-prepend mb-10">
												<span class="input-group-text" id="infantPricePerPaxPrefix">@</span>
											</div>
											<input type="text" class="form-control mb-10 text-right" id="pricePerPaxInfant" name="pricePerPaxInfant" value="0" aria-describedby="infantPricePerPaxPrefix" onkeypress="maskNumberInput(0, 999999999, 'pricePerPaxInfant')" onkeyup="calculateTicketProduct()">
										</div>
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group required">
										<label for="totalPriceInfant" class="control-label">Total Price</label>
										<input type="text" class="form-control mb-10 text-right" id="totalPriceInfant" name="totalPriceInfant" value="0" readonly="readonly">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group required">
										<label for="ticketProductNominal" class="control-label">Total Ticket Price</label>
										<input type="text" class="form-control mb-10 text-right" id="ticketProductNominal" name="ticketProductNominal" value="0"  readonly="readonly">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="ticketProductNotes" class="control-label">Notes</label>
										<textarea class="form-control mb-10" id="ticketProductNotes" name="ticketProductNotes"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationDetailsEditor" name="idReservationDetailsEditor" value="0">
				<input type="hidden" id="totalPax" name="totalPax" value="0">
				<input type="hidden" id="transportProductNotesHidden" name="transportProductNotesHidden" value="">
				<button class="button button-primary" id="saveReservationDetails">Add Reservation Details</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorReservationDetailsTicket" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-editorReservationDetailsTicket">
			<div class="modal-header">
				<h4 class="modal-title" id="title-editorReservationDetailsTicket">Reservation Details (Ticket)</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row mb-15 pb-10" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12"><h6 id="editorReservationDetailsTicket-reservationTitleStr"></h6></div>
				</div>
				<div class="row mt-20 pl-10 pr-10">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="editorTicket-optionProductTicketVendor" class="control-label">Ticket & Vendor</label>
							<select id="editorTicket-optionProductTicketVendor" name="editorTicket-optionProductTicketVendor" class="form-control"></select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group required">
							<label for="editorTicket-ticketAdultPax" class="control-label">Adult Pax</label>
							<div class="input-group">
								<input type="text" class="form-control mb-10 text-right pl-2" id="editorTicket-ticketAdultPax" name="editorTicket-ticketAdultPax" value="0" aria-describedby="editorTicket-adultPaxSuffix" onkeypress="maskNumberInput(0, 999, 'teditorTicket-icketAdultPax')" onkeyup="calculateTicketProductEditorTicket()">
								<div class="input-group-append mb-10">
									<span class="input-group-text" id="editorTicket-adultPaxSuffix">Pax</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group required">
							<label for="editorTicket-pricePerPaxAdult" class="control-label">Price Per Pax</label>
							<div class="input-group">
								<div class="input-group-prepend mb-10">
									<span class="input-group-text" id="editorTicket-adultPricePerPaxPrefix">@</span>
								</div>
								<input type="text" class="form-control mb-10 text-right" id="editorTicket-pricePerPaxAdult" name="editorTicket-pricePerPaxAdult" value="0" aria-describedby="editorTicket-adultPricePerPaxPrefix" onkeypress="maskNumberInput(0, 999999999, 'editorTicket-pricePerPaxAdult')" onkeyup="calculateTicketProductEditorTicket()">
							</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group required">
							<label for="editorTicket-totalPriceAdult" class="control-label">Total Price</label>
							<input type="text" class="form-control mb-10 text-right" id="editorTicket-totalPriceAdult" name="editorTicket-totalPriceAdult" value="0" readonly="readonly">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group required">
							<label for="editorTicket-ticketChildPax" class="control-label">Child Pax</label>
							<div class="input-group">
								<input type="text" class="form-control mb-10 text-right pl-2" id="editorTicket-ticketChildPax" name="editorTicket-ticketChildPax" value="0" aria-describedby="editorTicket-childPaxSuffix" onkeypress="maskNumberInput(0, 999, 'editorTicket-ticketChildPax')" onkeyup="calculateTicketProductEditorTicket()">
								<div class="input-group-append mb-10">
									<span class="input-group-text" id="editorTicket-childPaxSuffix">Pax</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group required">
							<label for="editorTicket-pricePerPaxChild" class="control-label">Price Per Pax</label>
							<div class="input-group">
								<div class="input-group-prepend mb-10">
									<span class="input-group-text" id="editorTicket-childPricePerPaxPrefix">@</span>
								</div>
								<input type="text" class="form-control mb-10 text-right" id="editorTicket-pricePerPaxChild" name="editorTicket-pricePerPaxChild" value="0" aria-describedby="editorTicket-childPricePerPaxPrefix" onkeypress="maskNumberInput(0, 999999999, 'editorTicket-pricePerPaxChild')" onkeyup="calculateTicketProductEditorTicket()">
							</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group required">
							<label for="editorTicket-totalPriceChild" class="control-label">Total Price</label>
							<input type="text" class="form-control mb-10 text-right" id="editorTicket-totalPriceChild" name="editorTicket-totalPriceChild" value="0" readonly="readonly">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group required">
							<label for="editorTicket-ticketInfantPax" class="control-label">Infant Pax</label>
							<div class="input-group">
								<input type="text" class="form-control mb-10 text-right pl-2" id="editorTicket-ticketInfantPax" name="editorTicket-ticketInfantPax" value="0" aria-describedby="editorTicket-infantPaxSuffix" onkeypress="maskNumberInput(0, 999, 'editorTicket-ticketInfantPax')" onkeyup="calculateTicketProductEditorTicket()">
								<div class="input-group-append mb-10">
									<span class="input-group-text" id="editorTicket-infantPaxSuffix">Pax</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group required">
							<label for="editorTicket-pricePerPaxInfant" class="control-label">Price Per Pax</label>
							<div class="input-group">
								<div class="input-group-prepend mb-10">
									<span class="input-group-text" id="editorTicket-infantPricePerPaxPrefix">@</span>
								</div>
								<input type="text" class="form-control mb-10 text-right" id="editorTicket-pricePerPaxInfant" name="editorTicket-pricePerPaxInfant" value="0" aria-describedby="editorTicket-infantPricePerPaxPrefix" onkeypress="maskNumberInput(0, 999999999, 'editorTicket-pricePerPaxInfant')" onkeyup="calculateTicketProductEditorTicket()">
							</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group required">
							<label for="editorTicket-totalPriceInfant" class="control-label">Total Price</label>
							<input type="text" class="form-control mb-10 text-right" id="editorTicket-totalPriceInfant" name="editorTicket-totalPriceInfant" value="0" readonly="readonly">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="editorTicket-ticketProductNominal" class="control-label">Total Ticket Price</label>
							<input type="text" class="form-control mb-10 text-right" id="editorTicket-ticketProductNominal" name="editorTicket-ticketProductNominal" value="0"  readonly="readonly">
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group">
							<label for="editorTicket-ticketProductNotes" class="control-label">Notes</label>
							<textarea class="form-control mb-10" id="editorTicket-ticketProductNotes" name="editorTicket-ticketProductNotes"></textarea>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group">
							<label for="editorTicket-correctionNotes" class="control-label">Correction Notes</label>
							<textarea class="form-control mb-10" id="editorTicket-correctionNotes" name="editorTicket-correctionNotes"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorTicket-idReservationDetailsEditor" name="editorTicket-idReservationDetailsEditor" value="0">
				<button class="button button-primary" id="editorTicket-saveReservationDetails">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorReservationDetailsTransport" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-editorReservationDetailsTransport">
			<div class="modal-header">
				<h4 class="modal-title" id="title-editorReservationDetailsTransport">Reservation Details (Transport)</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row mb-5">
					<div class="form-group col-sm-12 required">
						<label for="editorTransport-productName" class="control-label">Product Name</label>
						<input type="text" class="form-control" id="editorTransport-productName" name="editorTransport-productName" placeholder="Product/Fee Name" maxlength="200">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12 required">
						<label for="editorTransport-nominalFee" class="control-label">Nominal Fee</label>
						<input type="text" class="form-control mb-10 text-right" id="editorTransport-nominalFee" name="editorTransport-nominalFee" value="0" onkeypress="maskNumberInput(0, 999999999, 'editorTransport-nominalFee')">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12">
						<label for="editorTransport-notesFee" class="control-label">Notes</label>
						<textarea class="form-control mb-10" id="editorTransport-notesFee" name="editorTransport-notesFee"></textarea>
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12">
						<label for="editorTransport-correctionNotes" class="control-label">Correction Notes</label>
						<textarea class="form-control mb-10" id="editorTransport-correctionNotes" name="editorTransport-correctionNotes"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorTransport-idReservationDetailsEditor" name="editorTransport-idReservationDetailsEditor" value="0">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-voucherList" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" id="container-voucherList">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-voucherList">Voucher List</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="order-details-customer-info col-sm-12 mb-15 pb-15" style="border-bottom: 1px solid #dee2e6;">
						<ul>
							<li> <span>Source</span> <span id="textVoucherSource">-</span> </li>
							<li> <span>Title</span> <span id="textVoucherTitle">-</span> </li>
							<li> <span>Guest Name</span> <span id="textVoucherGuestName">-</span> </li>
							<li> <span>Date</span> <span id="textVoucherDate">-</span> </li>
						</ul>
					</div>
					<div class="col-sm-12 text-center mt-10 mb-10" id="containerEmptyListVoucher">
						<h6 class="text-warning">No voucher have been created yet</h6>
					</div>
					<div class="col-sm-12 d-none" id="containerListVoucher"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-primary mr-auto" data-toggle="modal" data-target="#modal-editorCreateVoucher">Create Voucher</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-listReservationPayment" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content form-horizontal" id="container-listReservationPayment">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-listReservationPayment">Reservation Payment List</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-8 col-sm-6 pb-2" style="border-bottom: 1px solid #e0e0e0;"><b id="reservationTitlePaymentStr">-</b></div>
					<div class="col-lg-4 col-sm-6 pb-2 text-right" style="border-bottom: 1px solid #e0e0e0;"><b id="reservationDateTimePaymentStr">-</b></div>
					<div class="col-sm-4 mt-20">
						<h6 class="mb-0">Customer Detail</h6>
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Cust. Name</span> <span id="custNamePaymentStr">-</span> </li>
								<li> <span>Contact</span> <span id="custContactPaymentStr">-</span> </li>
								<li> <span>Email</span> <span id="custEmailPaymentStr">-</span> </li>
							</ul>
						</div>
						<h6 class="mt-15 mb-0">Pax</h6>
						<div class="order-details-customer-info mb-10">
							<ul class="ml-5">
								<li> <span>Adult</span> <span id="paxAdultPaymentStr">0</span> </li>
								<li> <span>Child</span> <span id="paxChildPaymentStr">0</span> </li>
								<li> <span>Infant</span> <span id="paxInfantPaymentStr">0</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-sm-3 mt-20">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<p id="reservationTypePaymentBadge">-</p>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Duration</h6>
								<p id="durationOfDayPaymentStr">-</p>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Booking Code</h6>
								<p id="bookingCodePaymentStr">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Revenue Nominal</h6>
								<p id="paymentNominalPaymentStr">-</p>
							</div>
						</div>
					</div>
					<div class="col-sm-5 mt-20">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Remark</h6>
								<p id="remarkPaymentStr">-</p>
							</div>
						</div>
					</div>
				</div>
				<div class="row border rounded px-2 py-2 mx-1 my-2">
					<div class="col-lg-6 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<h6>Total Revenue (Reservation)</h6>
						<h5 id="totalRevenueReservation"></h5>
					</div>
					<div class="col-lg-6 col-sm-12">
						<h6>Total Payment (Finance)</h6>
						<h5 id="totalPaymentFinance"></h5>
					</div>
				</div>
				<div class="row">
					<table class="table" id="table-reservationPayment">
						<thead class="thead-light">
							<tr>
								<th>Method - Description</th>
								<th width="80">Currency</th>
								<th width="100" class="text-right" align="right">Amount</th>
								<th width="80" class="text-right" align="right">Exchange</th>
								<th width="100" class="text-right" align="right">Amount (IDR)</th>
								<th width="100">Status</th>
								<th width="200">Input Details</th>
								<th width="40"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="8" class="text-center text-bold">No data found</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-primary mr-auto" id="btnCreateNewPayment" data-idReservation="" data-action="insert" data-toggle="modal" data-target="#modal-editorCreatePayment">New Payment</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-editorCreateVoucher" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="container-editorCreateVoucher">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-createVoucher">Create Voucher</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 mt-5 mb-10 pb-10" style="border-bottom: 1px solid #e0e0e0;">
						<div class="adomx-checkbox-radio-group inline" id="containerVoucherDate"></div>
					</div>
					<div class="col-lg-3 col-sm-12">
						<div class="form-group">
							<label for="guestNameVoucher" class="control-label">Guest Name</label>
							<input type="text" class="form-control mb-5" id="guestNameVoucher" name="guestNameVoucher" value="">
						</div>
					</div>
					<div class="col-lg-3 col-sm-7">
						<div class="form-group">
							<label for="optionVendorVoucher" class="control-label">Vendor</label>
							<select id="optionVendorVoucher" name="optionVendorVoucher" class="form-control mb-10"></select>
						</div>
					</div>
					<div class="col-lg-6 col-sm-5">
						<div class="form-group">
							<label for="serviceNameVoucher" class="control-label">Service</label>
							<input type="text" class="form-control mb-5" id="serviceNameVoucher" name="serviceNameVoucher" value="">
						</div>
					</div>
					<div class="col-lg-2 col-sm-8">
						<div class="form-group">
							<label for="paxName1" class="control-label">Pax Type 1</label>
							<input type="text" class="form-control mb-5" id="paxName1" name="paxName1" value="Adult">
						</div>
					</div>
					<div class="col-lg-2 col-sm-4">
						<div class="form-group">
							<label for="paxTotal1" class="control-label">Total Pax 1</label>
							<div class="input-group">
								<input type="text" class="form-control mb-5 text-right pl-2" id="paxTotal1" name="paxTotal1" value="0" aria-describedby="paxTotal1Suffix" onkeypress="maskNumberInput(0, 999, 'paxTotal1')">
								<div class="input-group-append mb-5">
									<span class="input-group-text" id="paxTotal1Suffix">Pax</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-sm-8">
						<div class="form-group">
							<label for="paxName2" class="control-label">Pax Type 2</label>
							<input type="text" class="form-control mb-5" id="paxName2" name="paxName2" value="Child">
						</div>
					</div>
					<div class="col-lg-2 col-sm-4">
						<div class="form-group">
							<label for="paxTotal1" class="control-label">Total Pax 2</label>
							<div class="input-group">
								<input type="text" class="form-control mb-5 text-right pl-2" id="paxTotal2" name="paxTotal2" value="0" aria-describedby="paxTotal2Suffix" onkeypress="maskNumberInput(0, 999, 'paxTotal2')">
								<div class="input-group-append mb-5">
									<span class="input-group-text" id="paxTotal2Suffix">Pax</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-sm-8">
						<div class="form-group">
							<label for="paxName3" class="control-label">Pax Type 3</label>
							<input type="text" class="form-control mb-5" id="paxName3" name="paxName3" value="Infant">
						</div>
					</div>
					<div class="col-lg-2 col-sm-4">
						<div class="form-group">
							<label for="paxTotal3" class="control-label">Total Pax 3</label>
							<div class="input-group">
								<input type="text" class="form-control mb-5 text-right pl-2" id="paxTotal3" name="paxTotal3" value="0" aria-describedby="paxTotal3Suffix" onkeypress="maskNumberInput(0, 999, 'paxTotal3')">
								<div class="input-group-append mb-5">
									<span class="input-group-text" id="paxTotal3Suffix">Pax</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label for="voucherNotes" class="control-label">Notes</label>
							<input type="text" class="form-control mb-5" id="voucherNotes" name="voucherNotes" value="">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationCreateVoucher" name="idReservationCreateVoucher" value="0">
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-editorCreatePayment" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="container-editorCreatePayment">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorCreatePayment">Create New/Edit Payment</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-5">
					<div class="form-group col-sm-7">
						<label for="optionPaymentMethod" class="control-label">Payment Method</label>
						<select id="optionPaymentMethod" name="optionPaymentMethod" class="form-control"></select>
					</div>
					<div class="form-group col-sm-5">
						<label for="optionPaymentStatus" class="control-label">Status</label>
						<select id="optionPaymentStatus" name="optionPaymentStatus" class="form-control">
							<option value="0">Pending</option>
							<option value="1">Paid</option>
							<option value="-1">Cancel</option>
						</select>
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12">
						<label for="descriptionPayment" class="control-label">Description</label>
						<input type="text" class="form-control" id="descriptionPayment" name="descriptionPayment" placeholder="Description" maxlength="150">
					</div>
				</div>
				<div class="row mb-5">
					<div class="col-sm-5">
						<div class="form-group required">
							<label for="paymentCurrency" class="control-label">Currency</label>
							<select class="form-control" id="paymentCurrency" name="paymentCurrency">
								<option value="IDR">IDR</option>
								<option value="USD">USD</option>
							</select>
						</div>
					</div>
					<div class="col-sm-4 px-1">
						<div class="form-group required">
							<label for="paymentPriceInteger" class="control-label">Integer</label>
							<input type="text" class="form-control mb-10 text-right" id="paymentPriceInteger" name="paymentPriceInteger" onkeypress="maskNumberInput(0, 999999999, 'paymentPriceInteger');" value="0">
						</div>
					</div>
					<div class="col-sm-3" style="padding-left: 6px;">
						<div class="form-group required">
							<label for="paymentPriceDecimal" class="control-label">Comma</label>
							<input type="text" class="form-control mb-10 text-right" id="paymentPriceDecimal" name="paymentPriceDecimal" onkeypress="maskNumberInput(0, 99, 'reservationPriceDecimal');" value="0">
						</div>
					</div>
				</div>
				<div class="row mb-5">
					<div class="col-12">
						<div class="form-group">
							<label class="adomx-checkbox">
								<input type="checkbox" id="checkboxUpsellingPayment" name="checkboxUpsellingPayment" value="1"> <i class="icon"></i> <b>Upselling Payment</b>
							</label>
						</div>
					</div>
				</div>
				<div class="row d-none" id="containerCollectData">
					<div class="col-lg-6 col-sm-12">
						<div class="form-group" id="containerOptionDriverCollect">
							<label for="optionDriverCollect" class="control-label">Driver Collect</label>
							<select class="form-control" id="optionDriverCollect" name="optionDriverCollect" option-all-value="0" option-all="Base on Schedule"></select>
						</div>
						<div class="form-group" id="containerOptionVendorCollect">
							<label for="optionVendorCollect" class="control-label">Vendor Collect</label>
							<select class="form-control" id="optionVendorCollect" name="optionVendorCollect" option-all="Base on Schedule"></select>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group" id="contaierOptionDateCollect">
							<label for="optionDateCollect" class="control-label">Date Collect</label>
							<select class="form-control" id="optionDateCollect" name="optionDateCollect"></select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationCreatePayment" name="idReservationCreatePayment" value="0">
				<input type="hidden" id="idReservationPayment" name="idReservationPayment" value="0">
				<input type="hidden" id="actionTypePayment" name="actionTypePayment" value="">
				<input type="hidden" id="editablePayment" name="editablePayment" value="0">
				<input type="hidden" id="isUpsellingCheckedOrigin" name="isUpsellingCheckedOrigin" value="0">
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-selectReservationVoucher" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="container-selectReservationVoucher">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-selectReservationVoucher">Search Reservation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-5">
					<div class="form-group col-sm-12">
						<label for="reservationKeyword" class="control-label">Search reservation by booking code / reservation title / customer name / contact / place / remark / tour plan</label>
						<input type="text" class="form-control" id="reservationKeyword" name="reservationKeyword" placeholder="Type something and press ENTER to search" maxlength="150">
					</div>
				</div>
				<div style="height: 400px;overflow-y: scroll;" class="row mb-5 border mx-1 my-2 rounded" id="containerSelectReservationResult">
					<div class="col-sm-12 text-center mx-auto my-auto">
						<h2><i class="fa fa-list-alt text-warning"></i></h2>
						<b class="text-warning">Results goes here</b>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-selectTemplateAutoCost">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content form-horizontal" id="editor-selectTemplateAutoCost" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-selectTemplateAutoCost">Search template auto cost</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12"><p>Keyword : <b id="textKeywordAutoCost"></b></p></div>
					<div class="col-sm-12" style="border-bottom: 1px solid #e0e0e0;">
						<input type="text" class="form-control mt-10 mb-10" id="searchAutoCost" name="searchAutoCost" placeholder="Type something and press ENTER to search auto cost template">
					</div>
					<div class="col-sm-12 tableFixHead" style="max-height: 300px">
						<table class="table">
							<tbody id="body-listTemplateAutoCost"></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-additionalConfirmationInfo" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-additionalConfirmationInfo">
			<div class="modal-header">
				<h4 class="modal-title" id="title-additionalConfirmationInfo">Additional Reconfirmation Info</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-12 mt-20 px-3" id="additionalConfirmationInfo-formAdditionalInfo">
						<div class="row">
							<div class="col-4 mb-10 px-2">
								<select id="additionalConfirmationInfo-optionTextLink" name="optionTextLink" class="form-control">
									<option value="1">Text</option>
									<option value="2">Link</option>
								</select>
							</div>
							<div class="col-8 mb-10 px-2">
								<input type="text" class="form-control" id="additionalConfirmationInfo-description" name="description" placeholder="Description"/>
								<small class="text-danger d-none" id="warningMessage-description">Please insert description</small>
							</div>
							<div class="col-12 mb-10 px-2">
								<textarea class="form-control" placeholder="Information Content" id="additionalConfirmationInfo-informationContent" name="informationContent"></textarea>
								<small class="text-danger d-none" id="warningMessage-informationContent">Please insert valid information content</small>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary" id="btnSaveAdditionalInfo">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorRefundType" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-editorRefundType">
			<div class="modal-header">
				<h4 class="modal-title" id="title-editorRefundType">Change Refund Type</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-12 px-3">
						<p>Change refund type for reservation ;</p>
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Booking Code</span> <span id="editorRefundType-bookingCode"></span> </li>
								<li> <span>Date</span> <span id="editorRefundType-strDateReservation"></span> </li>
								<li> <span>Customer Name</span> <span id="editorRefundType-customerName"></span> </li>
								<li> <span>Title</span> <span></span> </li>
							</ul>
							<p class="ml-10" id="editorRefundType-reservationTitle"></p>
						</div>
						<div class="form-group mt-20 pt-20 border-top">
							<label for="editorRefundType-optionRefundType" class="control-label">Refund Type</label>
							<select id="editorRefundType-optionRefundType" name="editorRefundType-optionRefundType" class="form-control">
								<option value="0">No Refund</option>
								<option value="-1">Full Refund</option>
								<option value="-2">Partial Refund</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorRefundType-idReservation" name="editorRefundType-idReservation" value="0">
				<input type="hidden" id="editorRefundType-originRefundType" name="editorRefundType-originRefundType" value="0">
				<button type="submit" class="button button-primary" id="editorRefundType-btnSaveRefundType">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<script>
	reservationFunc 				=	null;
	var dateNow						=	"<?=date('d-m-Y')?>";
	var dataIdPaymentMethodUpselling=	JSON.parse("<?=$dataIdPaymentMethodUpselling?>");
	var url 						=	"<?=BASE_URL_ASSETS?>js/page-module/reservation.js?<?=date('YmdHis')?>",
		idReservationTypeAdmin		=	"<?=$idReservationType?>";
	$.getScript(url);
</script>
<style>
.table td {
    word-break: break-word;
    white-space: break-spaces;
}
.reservationTextArea{
	height: 90px !important;
}
</style>