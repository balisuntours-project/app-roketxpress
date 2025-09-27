<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/summernote-bs4.css" rel="stylesheet" type="text/css">
<div class="row justify-content-between align-items-center mb-20">
	<div class="col-12 col-lg-auto">
		<div class="page-heading">
			<h3 class="title">Re-Confirmation <span> / Reconfirmation of the reservation by customer based on date</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto">
		<div class="page-date-range">
			<input type="hidden" id="arrIdReservation" name="arrIdReservation" value="">
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseDetailReconfirmation"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
	<div class="box mb-10">
		<div class="box-body">
			<div class="row">
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="scheduleDate" class="control-label">Schedule/Activity Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="scheduleDate" name="scheduleDate" value="<?=$defaultDateFilter?>">
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionSource" class="control-label">Source</label>
						<select id="optionSource" name="optionSource" class="form-control" option-all="All Source"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionSendingMethod" class="control-label">Sending Method</label>
						<select id="optionSendingMethod" name="optionSendingMethod" class="form-control">
							<option value="">All Method</option>
							<option value="1">Auto System</option>
							<option value="2">Manual</option>
						</select>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="form-group">
						<label for="customerName" class="control-label">Customer Name</label>
						<input type="text" class="form-control mb-10" id="customerName" name="customerName">
					</div>
				</div>
				<div class="col-lg-4 col-sm-12">
					<div class="form-group">
						<label for="bookingCode" class="control-label">Booking Code</label>
						<input type="text" class="form-control mb-10" id="bookingCode" name="bookingCode">
					</div>
				</div>
				<div class="col-lg-8 col-sm-12">
					<div class="form-group">
						<label for="reservationTitle" class="control-label">Reservation Title</label>
						<input type="text" class="form-control mb-10" id="reservationTitle" name="reservationTitle">
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label class="adomx-checkbox">
							<input type="checkbox" id="checkboxViewUnreadThreadOnly" name="checkboxViewUnreadThreadOnly" value="1"> <i class="icon"></i> <b>Show only recently received conversations</b>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="box">
		<div class="box-body px-0 py-0 responsive-table-container">
			<div class="row">
				<div class="col-lg-6 col-sm-12 pl-20">
					<ul class="nav nav-tabs ml-15 mt-15 mb-15 nav-tabs-date">
						<li class="nav-item"><a class="nav-link daySelectorTab active" data-toggle="pill" data-additionalDay="0">Today</a></li>
						<li class="nav-item"><a class="nav-link daySelectorTab" data-toggle="pill" data-additionalDay="1">Tomorrow</a></li>
						<li class="nav-item"><a class="nav-link daySelectorTab" data-toggle="pill" data-additionalDay="2">Next 2 Day</a></li>
					</ul>
				</div>
				<div class="col-lg-6 col-sm-12 pr-40">
					<ul class="nav nav-tabs ml-15 mt-15 mb-15 nav-tabs-status pull-right">
						<li class="nav-item"><a class="nav-link active" data-toggle="pill" data-status=""><i class="fa fa-bars"></i> All Status [<span id="totalStatusAll">0</span>]</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="pill" data-status="0"><i class="fa fa-clock-o"></i> Scheduled [<span id="totalStatusScheduled">0</span>]</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="pill" data-status="1"><i class="fa fa-envelope-o"></i> Sent [<span id="totalStatusSent">0</span>]</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="pill" data-status="2"><i class="fa fa-check"></i> Confirmed [<span id="totalStatusConfirmed">0</span>]</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="pill" data-status="3"><i class="fa fa-check-circle"></i> Confirmed With Update [<span id="totalStatusUpdate">0</span>]</a></li>
					</ul>
				</div>
				<div class="col-sm-12">
					<table class="table" id="table-reconfirmation">
						<thead class="thead-light">
							<tr>
								<th width="320">Booking Details</th>
								<th width="250">Customer</th>
								<th>Other Details</th>
								<th width="200">Email</th>
								<th width="200">Whatsapp</th>
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
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="row d-none">
		<div class="col-lg-5 col-sm-12 box mb-10 d-none pl-20 pr-20 pt-20 pb-20" style="border-right: 1px solid #e0e0e0;">
			<div class="row">
				<div class="col-sm-12 pb-10" style="border-bottom: 1px solid #e0e0e0;"><h6><span id="reservationTitleStr">-</span> <span id="reservationDateTimeStr" class="pull-right" >-</span></h6></div>
				<div class="col-sm-12 mt-20">
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
				<div class="col-sm-12 mt-20">
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
					</div>
				</div>
				<div class="col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Tour Plan</h6>
							<p id="tourPlanStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Remark</h6>
							<p id="remarkStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Special Request</h6>
							<p id="specialRequestStr">-</p>
						</div>
					</div>
				</div>
				<div class="col-sm-12 mt-10 pt-10 border-top" id="container-driverHandle">
					<h6 class="mb-0">Driver Handle</h6>
				</div>
			</div>
		</div>
		<div class="col-lg-7 col-sm-12 box mb-10 d-none pl-20 pr-20 pt-20 pb-20">
			<div class="row">
				<div class="col-lg-6 col-sm-12">
					<ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#detailReconfirmation-tabEmail"><i class="fa fa-envelope-o"></i> Email</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="pill" href="#detailReconfirmation-tabWhatsapp"><i class="fa fa-whatsapp"></i> Whatsapp</a></li>
					</ul>
				</div>
				<div class="col-lg-6 col-sm-12">
					<button class="button button-primary button-sm pull-right" id="btnEditReservationFormDetails"><span><i class="fa fa-pencil"></i>Edit Reservation</span></button>
				</div>
				<div class="col-sm-12">
					<div class="tab-content pt-20" style="border-top: 1px solid #e0e0e0;">
						<div class="tab-pane fade show active" id="detailReconfirmation-tabEmail">
							<div class="row" id="detailReconfirmation-email">
								<div class="col-12 mt-40 mb-30 text-center" id="noEmailMessage">
									<h1><i class="fa fa-search text-body"></i></h1>
									<p>No email message for this reservation</p>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="detailReconfirmation-tabWhatsapp">
							<div class="row" id="detailReconfirmation-whatsapp">
								<div class="col-12 mt-40 mb-30 text-center" id="noWhatsappMessage">
									<h1><i class="fa fa-search text-body"></i></h1>
									<p>No whatsapp conversation for this reservation</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-12 box pl-20 pr-20 pt-20 pb-10 mb-10 d-none">
			<div class="row">
				<div class="col-6">
					<button class="button button-sm button-info" style="width: 120px;" id="btnPreviousReconfirmationDetail"><i class="fa fa-arrow-left"></i><span>Previous</span></button>
				</div>
				<div class="col-6 text-right">
					<button class="button button-sm button-info button-icon-right" style="width: 120px;" id="btnNextReconfirmationDetail"><i class="fa fa-arrow-right"></i><span>Next</span></button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-mailReconfirmationPreview" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="content-mailReconfirmationPreview">
			<div class="modal-header">
				<h4 class="modal-title" id="title-mailReconfirmationPreview">[<span id="mailPreviewStatus"></span>] Reconfirmation Mail</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-lg-4 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<h6>Additional Information <i class="fa fa-plus pull-right text-info d-none text16px" id="btnAddMailAdditionalInfo"></i></h6>
						<div class="row" id="mailReconfirmationPreview-additionalInfo">
							<div class="col-12 mt-40 mb-30 text-center" id="noAdditionalInfo">
								<h1><i class="fa fa-search text-body"></i></h1>
								<p>No additional information for this confirmation email</p>
							</div>
							<div class="col-12 mt-20 px-2 d-none" id="mailReconfirmationPreview-listAdditionalInfo"></div>
							<div class="col-12 mt-20 px-3 d-none" id="mailReconfirmationPreview-formAdditionalInfo">
								<div class="row">
									<div class="col-4 mb-10 px-2">
										<select id="mailReconfirmationPreview-optionTextLink" name="optionTextLink" class="form-control">
											<option value="1">Text</option>
											<option value="2">Link</option>
										</select>
									</div>
									<div class="col-8 mb-10 px-2">
										<input type="text" class="form-control" id="mailReconfirmationPreview-description" name="description" placeholder="Description"/>
										<small class="text-danger d-none" id="warningMessage-description">Please insert description</small>
									</div>
									<div class="col-12 mb-10 px-2">
										<textarea class="form-control" placeholder="Information Content" id="mailReconfirmationPreview-informationContent" name="informationContent"></textarea>
										<small class="text-danger d-none" id="warningMessage-informationContent">Please insert information content</small>
									</div>
									<div class="col-12 mb-10 px-2">
										<button type="button" class="button button-warning button-sm btn-block" id="btnCancelAdditionalInfo">Cancel</button>
										<button type="submit" class="button button-primary button-sm btn-block" id="btnSaveAdditionalInfo">Insert Additional Info</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-8 col-sm-12">
						<h6>Mail Message Preview</h6>
						<iframe id="iFrameMailPreview" width="100%" height="500" padding="8px" src="nodata" frameborder="0"></iframe>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-primary mr-auto d-none" id="btnSendManualMailReconfirmation">Send Reconfirmation</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorReservation" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-reservation modal-lg" role="document">
		<form class="modal-content modal-content-reservation form-horizontal" id="content-editorReservation">
			<div class="modal-header">
				<h4 class="modal-title" id="title-editorReservation">Reservation Form</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-lg-6 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group mb-10">
									<label for="optionReservationTypeEditor" class="control-label">Type</label>
									<select id="optionReservationTypeEditor" name="optionReservationTypeEditor" class="form-control"></select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group mb-10">
									<label for="optionSourceEditor" class="control-label">Source</label>
									<select id="optionSourceEditor" name="optionSourceEditor" class="form-control"></select>
								</div>
							</div>
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
								<div class="form-group mb-10">
									<label for="bookingCodeEditor" class="control-label">Booking Code</label>
									<input type="text" class="form-control mb-10" id="bookingCodeEditor" name="bookingCode" placeholder="Booking Code">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10 required">
									<label for="reservationTitleEditor" class="control-label">Title</label>
									<input type="text" class="form-control mb-10" id="reservationTitleEditor" name="reservationTitle" value="" placeholder="Reservation Title">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="productDetailsUrl" class="control-label">Product Details Url</label>
									<input type="text" class="form-control mb-10" id="productDetailsUrl" name="productDetailsUrl" value="" placeholder="Product Details URL">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group mb-10 required">
									<label for="reservationDate" class="control-label">Date Start</label>
									<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationDate" name="reservationDate">
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group mb-10 required">
									<label for="reservationHour" class="control-label">Time Start</label>
									<select class="form-control" id="reservationHour" name="reservationHour"></select>
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group mb-10">
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
									<label for="reservationHourEnd" class="control-label">Time End</label>
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
								<div class="form-group mb-10 required">
									<label for="customerNameEditor" class="control-label">Customer Name</label>
									<input type="text" class="form-control mb-10" id="customerNameEditor" name="customerName" placeholder="Customer Name">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10 required">
									<label for="customerContact" class="control-label">Contact</label>
									<input type="text" class="form-control mb-10" id="customerContact" name="customerContact" placeholder="Customer Phone">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="customerEmail" class="control-label">Email</label>
									<input type="text" class="form-control mb-10" id="customerEmail" name="customerEmail" placeholder="Customer Email">
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group mb-10 required">
									<label for="reservationPriceType" class="control-label">Price</label>
									<select class="form-control" id="reservationPriceType" name="reservationPriceType">
										<option value="IDR">IDR</option>
										<option value="USD">USD</option>
									</select>
								</div>
							</div>
							<div class="col-sm-4 px-1">
								<div class="form-group mb-10 required">
									<label for="reservationPriceInteger" class="control-label">Integer</label>
									<input type="text" class="form-control mb-10 text-right" id="reservationPriceInteger" name="reservationPriceInteger" onkeyup="calculateReservationPriceIDR()" onkeypress="maskNumberInput(0, 999999999, 'reservationPriceInteger');">
								</div>
							</div>
							<div class="col-sm-3" style="padding-left: 6px;">
								<div class="form-group mb-10 required">
									<label for="reservationPriceDecimal" class="control-label">Comma</label>
									<input type="text" class="form-control mb-10 text-right decimalInput" id="reservationPriceDecimal" name="reservationPriceDecimal" onkeyup="calculateReservationPriceIDR()" onkeypress="maskNumberInput(0, 99, 'reservationPriceDecimal');">
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group mb-10">
									<label for="currencyExchange" class="control-label">Currency Exchange</label>
									<input type="text" class="form-control mb-10 text-right" id="currencyExchange" name="currencyExchange" readonly onkeyup="calculateReservationPriceIDR()" onkeypress="maskNumberInput(1, 999999999, 'currencyExchange')">
								</div>
							</div>
							<div class="col-sm-7" style="padding-left: 6px;">
								<div class="form-group mb-10 ">
									<label for="reservationPriceIDR" class="control-label">Price (IDR)</label>
									<input type="text" class="form-control mb-10 text-right" id="reservationPriceIDR" name="reservationPriceIDR" readonly="readonly">
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group mb-10 required">
									<label for="numberOfAdult" class="control-label">Adult</label>
									<input type="text" class="form-control mb-10" id="numberOfAdult" name="numberOfAdult" placeholder="Adult" onkeypress="maskNumberInput(0, 99, 'numberOfAdult')">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group mb-10">
									<label for="numberOfChild" class="control-label">Child</label>
									<input type="text" class="form-control mb-10" id="numberOfChild" name="numberOfChild" placeholder="Child" onkeypress="maskNumberInput(0, 99, 'numberOfChild')">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group mb-10">
									<label for="numberOfInfant" class="control-label">Infant</label>
									<input type="text" class="form-control mb-10" id="numberOfInfant" name="numberOfInfant" placeholder="Infant" onkeypress="maskNumberInput(0, 99, 'numberOfInfant')">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10 required">
									<label for="durationOfDay" class="control-label">Duration (Day)</label>
									<input type="text" class="form-control mb-10 text-right" id="durationOfDay" name="durationOfDay" value="1" onkeypress="maskNumberInput(1, 99, 'durationOfDay')">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="hotelName" class="control-label">Customer Hotel</label>
									<input type="text" class="form-control mb-10" id="hotelName" name="hotelName" placeholder="Customer Hotel">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="optionPickUpArea" class="control-label">Pick Up Area</label>
									<select class="form-control" id="optionPickUpArea" name="optionPickUpArea" option-all="Without Transfer" option-all-value="-1"></select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="pickUpLocation" class="control-label">Pick Up Location</label>
									<input type="text" class="form-control mb-10" id="pickUpLocation" name="pickUpLocation" placeholder="Pick Up Location">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="pickUpLocationUrl" class="control-label">Pick Up Location Url/Link</label>
									<input type="text" class="form-control mb-10" id="pickUpLocationUrl" name="pickUpLocationUrl" placeholder="Pick Up Location URL/Link">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="dropOffLocation" class="control-label">Drop Off Location</label>
									<input type="text" class="form-control mb-10" id="dropOffLocation" name="dropOffLocation" placeholder="Drop Off Location">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="tourPlan" class="control-label">Tour Plan</label>
									<textarea class="form-control mb-10 reservationTextArea" placeholder="Tour Plan" id="tourPlan" name="tourPlan" style="height:90px"></textarea>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
									<label for="remark" class="control-label">Remark</label>
									<textarea class="form-control mb-10 reservationTextArea" placeholder="Remark" id="remark" name="remark" style="height:90px"></textarea>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group mb-10">
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
				<button class="button button-primary" id="saveReservation">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<style>
	.bubleThread{
		padding: 10px 15px;
		background-color: #ffffff;
		border-radius: 4px;
		-webkit-box-shadow: 0 0 5px rgba(24, 24, 24, 0.05);
		box-shadow: 0 0 5px rgba(24, 24, 24, 0.05);
	}
	.elemAdditionalInfo{
		border: 1px solid #e0e0e0;
		background-color: #fff;
	}
	.text13px {
	  font-size: 13px !important;
	}
	.text16px {
	  font-size: 16px !important;
	}
	.text18px {
	  font-size: 18px !important;
	}
	.modal-dialog-reservation {
		position: fixed;
		margin: auto;
		height: 100%;
		left: 0px;
	}
	.modal-content-reservation {
		height: 100%;
		overflow-y: scroll;
	}
</style>
<script>
	var dateToday	=	'<?=date('d-m-Y')?>',
		url			=	"<?=BASE_URL_ASSETS?>js/summernote-bs4.min.js";
	$.getScript(url, function(){
		var urlMainScript = "<?=BASE_URL_ASSETS?>js/page-module/reConfirmation.js?<?=date('YmdHis')?>";
		$.getScript(urlMainScript);
	});
</script>