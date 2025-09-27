<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Vendor Confirmation <span> / Schedule confirmation and status from vendor</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="scheduleDate" class="control-label">Date Schedule</label>
					<input type="text" class="form-control input-date-single mb-10 text-center" id="scheduleDate" name="scheduleDate" value="<?=date('d-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="optionConfirmationStatus" class="control-label">Confirmation Status</label>
					<select id="optionConfirmationStatus" name="optionConfirmationStatus" class="form-control">
						<option value="">All Confirmation Status</option>
						<option value="0">Unconfirm</option>
						<option value="1">Confirmed</option>
					</select>
				</div>
			</div>
			<div class="col-lg-8 col-sm-12 mb-10">
				<div class="form-group">
					<label for="optionVendor" class="control-label">Vendor</label>
					<select id="optionVendor" name="optionVendor" class="form-control" option-all="All Vendor"></select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body accordion accordion-icon" id="containerListScheduleVendor"></div>
</div>
<div class="modal fade" tabindex="-1" id="modal-bookScheduleSlot" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="content-bookScheduleSlot">
			<div class="modal-header">
				<h4 class="modal-title" id="title-bookScheduleSlot">Book Schedule Slot</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row mb-20" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Customer Hotel</h6>
						<p id="bookScheduleSlot-hotelName">-</p>
					</div>
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Pick Up Location</h6>
						<p id="bookScheduleSlot-pickUpLocation">-</p>
					</div>
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Drop Off Location</h6>
						<p id="bookScheduleSlot-dropOffLocation">-</p>
					</div>
					<div class="col-sm-12 mb-10">
						<h6 class="mb-0">Pick Up Time</h6>
						<p id="bookScheduleSlot-timePickUp">-</p>
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-6 required">
						<label for="bookScheduleSlot-hour" class="control-label">Book Schedule Slot</label>
						<select class="form-control" id="bookScheduleSlot-hour" name="bookScheduleSlot-hour"></select>
					</div>
					<div class="form-group col-sm-6">
						<label for="bookScheduleSlot-minute" class="control-label">.</label>
						<select class="form-control" id="bookScheduleSlot-minute" name="bookScheduleSlot-minute"></select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="bookScheduleSlot-idScheduleVendor" name="idScheduleVendor" value="0">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorReservationDetails" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="content-editorReservationDetails">
			<div class="modal-header">
				<h4 class="modal-title" id="title-editorReservationDetails">Reservation Details</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row mb-15 pb-15" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12 mb-20"><h6 id="editorReservationDetails-reservationTitleStr"></h6></div>
					<div class="col-lg-6 col-sm-12">
						<h6 class="mb-0">Customer Detail</h6>
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Cust. Name</span> <span id="editorReservationDetails-custNameStr">-</span> </li>
								<li> <span>Contact</span> <span id="editorReservationDetails-custContactStr">-</span> </li>
								<li> <span>Email</span> <span id="editorReservationDetails-custEmailStr">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<h6 class="mb-0">Pax</h6>
						<div class="order-details-customer-info mb-10">
							<ul class="ml-5">
								<li> <span>Adult</span> <span id="editorReservationDetails-paxAdultStr">0</span> </li>
								<li> <span>Child</span> <span id="editorReservationDetails-paxChildStr">0</span> </li>
								<li> <span>Infant</span> <span id="editorReservationDetails-paxInfantStr">0</span> </li>
							</ul>
						</div>
					</div>
				</div>
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
					<div class="col-lg-6 col-sm-12">
						<div class="form-group">
							<label for="ticketProductNotes" class="control-label">Notes</label>
							<textarea class="form-control mb-10" id="ticketProductNotes" name="ticketProductNotes"></textarea>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group">
							<label for="correctionNotes" class="control-label">Correction Notes</label>
							<textarea class="form-control mb-10" id="correctionNotes" name="correctionNotes"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationDetailsEditor" name="idReservationDetailsEditor" value="0">
				<button class="button button-primary" id="saveReservationDetails">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-detailReservation" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="content-detailReservation">
			<div class="modal-header">
				<h4 class="modal-title" id="title-detailReservation">Reservation Detail</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-lg-5 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Source</h6>
								<p id="detailReservation-source">-</p>
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
						</div>
					</div>
					<div class="col-lg-7 col-sm-12">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Remark</h6>
								<p id="detailReservation-remark">-</p>
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
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 pt-10" style="border-top: 1px solid #e0e0e0;">
						<h6 class="mb-5">Packages</h6>
						<div id="detailReservation-containerListPackage">
							<table class="table">
								<thead class="thead-light">
									<tr>
										<th>Package Name</th>
										<th class="text-right" width="60">Adult</th>
										<th class="text-right" width="60">Child</th>
										<th class="text-right" width="60">Infant</th>
										<th width="120">Slot Booking</th>
										<th width="120">Slot Schedule</th>
										<th width="140">Confirmation</th>
										<th width="140">Status Process</th>
									</tr>
								</thead>
								<tbody id="detailReservation-tbodyListPackage"></tbody>
							</table>
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
<script>
	var url 		=	"<?=BASE_URL_ASSETS?>js/page-module/Schedule/scheduleVendor.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>