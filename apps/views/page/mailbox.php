<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Mailbox <span> / Reservation data from email inbox</span></h3>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-lg-2 col-sm-12">
					<div class="form-group">
						<label for="reservationTypeFilter" class="control-label">Reservation Type</label>
						<select id="reservationTypeFilter" name="reservationTypeFilter" class="form-control" option-all="Not Set" option-all-value="0"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionMailStatus" class="control-label">Mail Status</label>
						<select id="optionMailStatus" name="optionMailStatus" class="form-control">
							<option value="">All</option>
							<option value="1">Processed</option>
							<option value="0" selected>Unprocessed</option>
						</select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionSource" class="control-label">Source</label>
						<select id="optionSource" name="optionSource" class="form-control" option-all="All Source"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-4">
					<div class="form-group">
						<label for="startDate" class="control-label">Mail Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>" disabled>
					</div>
				</div>
				<div class="col-lg-2 col-sm-4">
					<div class="form-group">
						<label for="endDate" class="control-label">.</label>
						<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('d-m-Y')?>" disabled>
					</div>
				</div>
				<div class="col-lg-2 col-sm-4">
					<div class="form-group">
						<label for="startDate" class="control-label">Reservation Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationDateFilter" name="reservationDateFilter" value="">
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="searchKeyword" class="control-label">Search by booking code/mail subject/reservation title</label>
						<input type="text" class="form-control mb-10" id="searchKeyword" name="searchKeyword" value="" placeholder="Type something and press ENTER to search">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="box mt-10">
		<div class="box-body px-0 py-0 responsive-table-container">
			<div class="row">
				<div class="col-12" style="padding:10px;padding-left: 30px;">
					<span id="tableDataCountMailbox">0 data found</span>
				</div>
				<div class="col-12">
					<table class="table" id="table-dataMailbox">
						<thead class="thead-light">
							<tr>
								<th width="120">Reservation Type</th>
								<th width="80">Source</th>
								<th width="120" class="text-center">Date Time</th>
								<th width="120" class="text-center">Reservation Date</th>
								<th>Subject</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="4" class="text-center">No data found</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-sm-12 mt-10 mb-10" style="padding:14px;padding-left: 30px;padding-right: 30px;">
					<hr/>
					<ul class="pagination" id="tablePaginationMailbox"></ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="box d-none">
		<div class="box-body">
			<div class="row">
				<div class="col-lg-2 col-sm-4">
					<button class="button button-warning button-sm btn-block" type="button" id="btnCloseComposer"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
				</div>
				<div class="col-lg-10 col-sm-8">
					<div class="alert alert-primary py-2 d-none" role="alert" id="validationInfo">
						<i class="fa fa-info"></i> This email has been validated by <b id="userValidator"></b> on <b id="dateTimeValidation"></b>
					</div>
				</div>
				<div class="col-sm-12 mt-30">
					<h5 class="mb-15 text-center" id="mailDetailSubject"></h5>
				</div>
			</div>
			<div class="row pt-15">
				<div class="col-lg-6 col-sm-12" style="border-right: 1px solid #e0e0e0;">
					<h5 class="mb-15 text-center">Mail Preview</h5><hr>
					<div id="mailPreview"></div>
				</div>
				<div class="col-lg-6 col-sm-12" id="reservationComposer">
					<h5 class="mb-15 text-center">Reservation Form</h5><hr>
					<form class="row" id="reservationForm">
						<div class="col-lg-3 col-sm-6">
							<div class="form-group required">
								<label for="reservationType" class="control-label">Reservation Type</label>
								<select class="form-control" id="reservationType" name="reservationType"></select>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6">
							<div class="form-group required">
								<label for="selfDriveStatus" class="control-label">Self Drive Status</label>
								<select class="form-control" id="selfDriveStatus" name="selfDriveStatus">
									<option value="0">Non Self Drive</option>
									<option value="1">Self Drive</option>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6">
							<div class="form-group required">
								<label for="durationOfDay" class="control-label">Duration (Day)</label>
								<input type="text" class="form-control mb-10 text-right" id="durationOfDay" name="durationOfDay" value="1" onkeypress="maskNumberInput(1, 99, 'durationOfDay')">
							</div>
						</div>
						<div class="col-lg-3 col-sm-6">
							<div class="form-group required">
								<label for="duplicateNumber" class="control-label">Duplication (Times)</label>
								<input type="text" class="form-control mb-10 text-right" id="duplicateNumber" name="duplicateNumber" value="1" onkeypress="maskNumberInput(1, 99, 'duplicateNumber')">
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group required">
								<label for="reservationTitle" class="control-label">Reservation Title</label>
								<input type="text" class="form-control mb-10" id="reservationTitle" name="reservationTitle" value="" placeholder="Reservation Title">
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="detailsProductURL" class="control-label">Details Product Url</label>
								<input type="text" class="form-control mb-10" id="detailsProductURL" name="detailsProductURL" value="" placeholder="Details Product Url">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group required">
								<label for="reservationDate" class="control-label">Reservation Date (Start)</label>
								<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationDate" name="reservationDate" readonly>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group required">
								<label for="reservationHour" class="control-label">Hour</label>
								<select class="form-control" id="reservationHour" name="reservationHour"></select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group required">
								<label for="reservationMinute" class="control-label">Minute</label>
								<select class="form-control" id="reservationMinute" name="reservationMinute"></select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group required">
								<label for="reservationDateEnd" class="control-label">Reservation Date (End)</label>
								<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationDateEnd" name="reservationDateEnd" readonly disabled>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group required">
								<label for="reservationHourEnd" class="control-label">Hour</label>
								<select class="form-control" id="reservationHourEnd" name="reservationHourEnd" disabled></select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group required">
								<label for="reservationMinuteEnd" class="control-label">Minute</label>
								<select class="form-control" id="reservationMinuteEnd" name="reservationMinuteEnd" disabled></select>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group required">
								<label for="customerName" class="control-label">Customer Name</label>
								<input type="text" class="form-control mb-10" id="customerName" name="customerName" placeholder="Customer Name">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group required">
								<label for="customerContact" class="control-label">Contact</label>
								<input type="text" class="form-control mb-10" id="customerContact" name="customerContact" placeholder="Customer Phone Number">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="customerEmail" class="control-label">Email</label>
								<input type="text" class="form-control mb-10" id="customerEmail" name="customerEmail" placeholder="Customer Email">
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
								<label for="pickUpLocationUrl" class="control-label">Pick Up Location Link/Url</label>
								<input type="text" class="form-control mb-10" id="pickUpLocationUrl" name="pickUpLocationUrl" placeholder="Pick Up Location Link/Url">
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="dropOffLocation" class="control-label">Drop Off Location</label>
								<input type="text" class="form-control mb-10" id="dropOffLocation" name="dropOffLocation" placeholder="Drop Off Location">
							</div>
						</div>
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
						<div class="col-sm-4">
							<div class="form-group required">
								<label for="bookingCode" class="control-label">Booking Code</label>
								<input type="text" class="form-control mb-10" id="bookingCode" name="bookingCode" placeholder="Booking Code">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group required">
								<label for="reservationPriceType" class="control-label">Reservation Price</label>
								<select class="form-control" id="reservationPriceType" name="reservationPriceType">
									<option value="IDR">IDR</option>
									<option value="USD">USD</option>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="reservationPriceInteger" class="control-label">.</label>
								<input type="text" class="form-control mb-10 text-right" id="reservationPriceInteger" name="reservationPriceInteger" placeholder="Price Integer" onkeypress="maskNumberInput(0, 999999999, 'reservationPriceInteger')">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group ">
								<label for="reservationPriceDecimal" class="control-label">.</label>
								<input type="text" class="form-control mb-10 text-right" id="reservationPriceDecimal" name="reservationPriceDecimal" placeholder="Price Decimal" onkeypress="maskNumberInput(0, 99, 'reservationPriceDecimal')">
							</div>
						</div>
						<div class="col-sm-12">
							<div class="alert alert-solid-warning mb-10 d-none" role="alert" id="specialCasePriceAlert"><b>Special case detected!</b> &nbsp; &nbsp; <span id="specialCasePriceText"></span></div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="tourPlan" class="control-label">Tour Plan</label>
								<textarea class="form-control mb-10" placeholder="Tour Plan" id="tourPlan" name="tourPlan" style="height: 80px"></textarea>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="remark" class="control-label">Remark</label>
								<textarea class="form-control mb-10" placeholder="Remark" id="remark" name="remark" style="height: 80px"></textarea>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="specialRequest" class="control-label">Special Request</label>
								<textarea class="form-control mb-10" placeholder="Special Request" id="specialRequest" name="specialRequest" style="height: 80px" value="-"></textarea>
							</div>
						</div>
						<div class="col-sm-12">
							<input type="hidden" id="idMailbox" name="idMailbox" value="0">
							<input type="hidden" id="idSource" name="idSource" value="0">
							<input type="hidden" id="arrIdMailBox" name="arrIdMailBox" value="">
							<button class="button button-primary button-sm" type="button" id="btnSaveReservation"><span><i class="fa fa-save"></i>Submit Reservation</span></button>
							<button class="button button-info button-sm pull-right" type="button" id="btnAdditionalInfo" data-toggle="modal" data-target="#modal-additionalConfirmationInfo"><span>[<span id="totalAdditionalReconfirmationInfo">0</span>] Additional Confirmation Info</span></button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="box-body" style="border-top: 1px solid #e0e0e0;">
			<div class="row">
				<div class="col-6">
					<button class="button button-sm button-info" style="width: 120px;" id="btnPreviousMail"><i class="fa fa-arrow-left"></i><span>Previous</span></button>
				</div>
				<div class="col-6 text-right">
					<button class="button button-sm button-info button-icon-right" style="width: 120px;" id="btnNextMail"><i class="fa fa-arrow-right"></i><span>Next</span></button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-additionalConfirmationInfo" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-additionalConfirmationInfo">
			<div class="modal-header">
				<h4 class="modal-title" id="title-additionalConfirmationInfo">Additional Reconfirmation Information</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 text-center">
						<button class="button button-primary button-sm mx-auto" type="button" id="btnAddAdditionalInfo"><span><i class="fa fa-plus"></i>New Additional Info</span></button>
					</div>
					<div class="col-sm-12">
						<div class="row" id="additionalConfirmationInfo-additionalInfo">
							<div class="col-12 mt-40 mb-30 text-center" id="noAdditionalInfo">
								<h1><i class="fa fa-search text-body"></i></h1>
								<p>No additional reconfirmation information</p>
							</div>
							<div class="col-12 mt-20 px-2 d-none" id="additionalConfirmationInfo-listAdditionalInfo"></div>
							<div class="col-12 mt-20 px-3 d-none" id="additionalConfirmationInfo-formAdditionalInfo">
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
									<div class="col-6 mb-10 px-2">
										<button type="button" class="button button-warning button-sm btn-block" id="btnCancelAdditionalInfo">Cancel</button>
									</div>
									<div class="col-6 mb-10 px-2">
										<button type="submit" class="button button-primary button-sm btn-block" id="btnSaveAdditionalInfo">Insert</button>
									</div>
								</div>
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
<style>
	.text16px {
	  font-size: 16px !important;
	}
	.elemAdditionalInfo{
		border: 1px solid #e0e0e0;
		background-color: #fff;
	}
</style>
<script>
	var url						=	"<?=BASE_URL_ASSETS?>js/page-module/mailbox.js?<?=date('YmdHis')?>",
		idReservationTypeAdmin	=	'<?=$idReservationType?>';
	$.getScript(url);
</script>