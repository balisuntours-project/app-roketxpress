<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Collect Payment <span> / List of payment collect by driver</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="startDate" class="control-label">Date Schedule</label>
					<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="endDate" class="control-label">.</label>
					<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-4 col-sm-12 mb-10">
				<div class="form-group">
					<label for="optionDriver" class="control-label">Driver</label>
					<select id="optionDriver" name="optionDriver" class="form-control" option-all="All Driver"></select>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="optionCollectStatus" class="control-label">Collect Status</label>
					<select id="optionCollectStatus" name="optionCollectStatus" class="form-control">
						<option value="">All Collect Status</option>
						<option value="0">Uncollected</option>
						<option value="1">Collected</option>
					</select>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="optionSettlementStatus" class="control-label">Settlement Status</label>
					<select id="optionSettlementStatus" name="optionSettlementStatus" class="form-control">
						<option value="">All Settlement Status</option>
						<option value="0">Unrequested</option>
						<option value="1">Requested</option>
						<option value="2">Approved</option>
						<option value="-1">Rejected</option>
					</select>
				</div>
			</div>
			<div class="col-sm-12 mb-10">
				<div class="form-group">
					<label class="adomx-checkbox">
						<input type="checkbox" id="checkboxViewSettlementRequestOnly" name="checkboxViewSettlementRequestOnly" value="1"> <i class="icon"></i> <b>Show settlement requests that need approval only</b>
					</label>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="alert alert-danger d-none pr-15" role="alert" id="totalSettlementRequestAlert">
	<i class="fa fa-info"></i> You have <b id="totalSettlementRequest">4</b> unprocessed settlement request
	<button class="button button-xs button-primary pull-right" id="btnShowAllSetttlementRequest"><span>Show All</span></button>
</div>
<div class="box">
	<div class="box-body">
		<div class="row mt-5">
			<div class="col-12">
				<div class="row">
					<div class="col-lg-8 col-sm-12 mb-10">
						<span id="tableDataCountCollectPayment"></span>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<a class="button button-primary button-sm pull-right" id="excelCollectPayment" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Details</span></a>
					</div>
				</div>
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-collectPayment">
						<thead class="thead-light">
							<tr>
								<th width="150" align="center">Date</th>
								<th >Details</th>
								<th width="200">Remark & Description</th>
								<th width="150" class="text-right">Amount</th>
								<th width="150">Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="5" class="text-center">No data found</th>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row mt-20">
					<div class="col-sm-12 mb-10">
						<ul class="pagination" id="tablePaginationCollectPayment"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-detailCollectPayment" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content form-horizontal" id="container-detailCollectPayment">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-title-detailCollectPayment">Detail Collect Payment</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-5 col-sm-12">
						<h6 class="mb-0">Customer Detail</h6>
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Source</span> <span id="sourceStr">-</span> </li>
								<li> <span>Booking Code</span> <span id="bookingCodeStr">-</span> </li>
								<li> <span>Reservation Title</span> <span id="reservationTitleStr">-</span> </li>
								<li> <span>Reservation Date</span> <span id="reservationDateStr">-</span> </li>
								<li> <span>Cust. Name</span> <span id="customerNameStr">-</span> </li>
								<li> <span>Contact</span> <span id="cuctomerContactStr">-</span> </li>
								<li> <span>Email</span> <span id="customerEmailStr">-</span> </li>
								<li> <span>Driver</span> <span id="driverNameTypeStr">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-lg-4 col-sm-12">
						<div class="row">
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Date Collect</h6>
								<p id="dateCollectStr">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Collect Payment Amount</h6>
								<p id="collectPaymentAmountStr">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Reservation Remark</h6>
								<p id="reservationRemarkStr">-</p>
							</div>
							<div class="col-sm-12 mb-10">
								<h6 class="mb-0">Payment Remark</h6>
								<p id="paymentRemarkStr">-</p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-sm-12">
						<div class="row">
							<div class="col-sm-12">
								<h6 class="mb-0">Settlement Receipt</h6>
								<img src="<?=$defaultImage?>" id="settlementReceipt" style="max-height:300px; max-width:250px"/><br/>
								<div id="uploaderSettlementReceipt" class="mt-20">Upload Transfer Receipt</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 mt-5">
						<h5>History</h5>
					</div>
					<div class="col-12">
						<table class="table" id="table-collectPaymentHistory">
							<thead class="thead-light">
								<tr>
									<th width="140" class="text-center">Date Time</th>
									<th>Description</th>
									<th width="160">User Input</th>
									<th width="40"></th>
								</tr>
							</thead>
							<tbody>
								<tr id="noDataCollectPaymentHistory">
									<td colspan="4" class="text-center text-bold">No history found</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<span class="mr-auto">
					<input type="hidden" id="settlementReceiptFileName" name="settlementReceiptFileName" value="">
					<input type="hidden" id="idCollectPaymentHistoryWithReceipt" name="idCollectPaymentHistoryWithReceipt" value="">
					<button type="button" class="button button-primary button-sm" id="btnApproveCollectPayment" data-idCollectPayment="" data-action="approveCollectPaymentSettlement">Approve Settlement</button>
					<button type="button" class="button button-danger button-sm" id="btnRejectCollectPayment" data-idCollectPayment="" data-action="rejectCollectPaymentSettlement">Reject Settlement</button>
				</span>
				<button type="button" class="button button-default button-sm" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-zoomReceiptSettlement">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" style="background-color: transparent; border: none;">
			<div class="modal-header" style="border:none">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 mx-auto text-center">
						<img src="" width="600px" id="zoomImageReceiptSettlement">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/FinanceDriver/collectPayment.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>