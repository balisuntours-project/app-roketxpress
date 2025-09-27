<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Detail Reservation Payment <span> / Detail payment per reservation</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseUploadExcelPaymentOTA"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
			<button class="button button-primary button-sm pull-right" id="btnUploadExcelPaymentOTA" onclick="openFormUploadExcelPaymentOTA()"><span><i class="fa fa-cloud-upload"></i>Upload OTA Payment</span></button>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
	<div class="box mb-10">
		<div class="box-body">
			<div class="row">
				<div class="col-lg-3 col-sm-6">
					<div class="form-group">
						<label for="optionPaymentMethodFilter" class="control-label">Payment Method</label>
						<select id="optionPaymentMethodFilter" name="optionPaymentMethodFilter" class="form-control" option-all="All Payment Method"></select>
					</div>
				</div>
				<div class="col-lg-3 col-sm-6">
					<div class="form-group">
						<label for="optionPaymentStatusFilter" class="control-label">Payment Status</label>
						<select id="optionPaymentStatusFilter" name="optionPaymentStatusFilter" class="form-control" option-all="All Payment Status">
							<option value="">All Payment Status</option>
							<option value="-2">No Payment Data</option>
							<option value="0">Unpaid/Pending</option>
							<option value="1">Paid</option>
						</select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-4">
					<div class="form-group">
						<label for="optionRefundType" class="control-label">Refund Type</label>
						<select id="optionRefundType" name="optionRefundType" class="form-control">
							<option value="">All Refund Type</option>
							<option value="0">No Refund</option>
							<option value="-1">Full Refund</option>
							<option value="-2">Partial Refund</option>
						</select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-4">
					<div class="form-group">
						<label for="startDate" class="control-label">Reservation Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-2 col-sm-4">
					<div class="form-group">
						<label for="endDate" class="control-label">&nbsp;</label>
						<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
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
						<label for="optionPartner" class="control-label">Partner</label>
						<select id="optionPartner" name="optionPartner" class="form-control" option-all="All Patner"></select>
					</div>
				</div>
				<div class="col-lg-7 col-sm-12">
					<div class="form-group">
						<label for="keywordSearchFilter" class="control-label">Search by Booking Code / Reservation Title / Customer Name</label>
						<input type="text" class="form-control mb-10" id="keywordSearchFilter" name="keywordSearchFilter" placeholder="Type something and press ENTER to search">
					</div>
				</div>
				<div class="col-lg-8 col-sm-12">
					<div class="form-group">
						<label class="adomx-checkbox">
							<input type="checkbox" id="checkboxUnmatchPaymentOnly" name="checkboxUnmatchPaymentOnly" value="1"> <i class="icon"></i> <b>Show unmatch payment reservation vs finance</b>
						</label>
					</div>
				</div>
				<div class="col-lg-4 col-sm-12">
					<a class="button button-primary button-sm pull-right" id="excelReport" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Report</span></a>
				</div>
			</div>
		</div>
	</div>
	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-12">
					<div class="row">
						<div class="col-lg-8 col-sm-12 mb-10 d-flex">
							<span id="tableDataCountReservationPayment" class="align-self-center">Show data from 0 to 0. Total 0 data</span>
						</div>
						<div class="col-lg-4 col-sm-12 mb-10">
							<div class="row">
								<div class="col-3 text-right align-self-center px-1">Order By</div>
								<div class="col-5 px-1">
									<select id="optionOrderBy" name="optionOrderBy" class="form-control form-control-sm">
										<option value="1">Reservation Number</option>
										<option value="2">Reservation Date</option>
									</select>
								</div>
								<div class="col-4">
									<select id="optionOrderType" name="optionOrderType" class="form-control form-control-sm">
										<option value="DESC">Desc</option>
										<option value="ASC">Asc</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-5 responsive-table-container">
						<table class="table" id="table-reservationPayment">
							<thead class="thead-light">
								<tr>
									<th width="40" class="text-right">No.</th>
									<th width="250">Activity</th>
									<th>Customer</th>
									<th width="180">Booking</th>
									<th width="420">Payments</th>
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
							<ul class="pagination" id="tablePaginationReservationPayment"></ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="box d-none" style="border-bottom: 1px solid #e0e0e0;" id="uploadPaymentContainer">
		<div class="box-body">
			<div class="row mb-10">
				<div class="col-lg-8 col-sm-12">
					<div class="alert alert-primary py-2" role="alert">
						<i class="zmdi zmdi-info"></i> The allowed documents are <b>xls and xlsx</b> with a maximum size of <b>800 kolibytes</b>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<select id="optionSourceAutoPayment" name="optionSourceAutoPayment" class="form-control form-control-sm"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<select id="optionFormatType" name="optionFormatType" class="form-control form-control-sm" disabled>
							<option value="0" data-idSource="0">-</option>
							<option value="1" data-idSource="2">BST - WWR</option>
							<option value="2" data-idSource="2">Melancaran</option>
							<option value="3" data-idSource="1">Non Rent Car</option>
							<option value="4" data-idSource="1">Rent Car</option>
						</select>
					</div>
				</div>
				<div class="col-sm-12 text-center mt-20 mb-20">
					<i class="fa fa-cloud-upload display-3" style="font-size: 48px;"></i><br/>
					<a href="#" id="uploaderExcelPaymentOTA">Upload Excel</a>
				</div>
			</div>
		</div>
	</div>
	<div class="box d-none" id="uploadPaymentScanningResult">
		<div class="box-head py-2">
			<div class="row mt-5">
				<div class="col-lg-8 col-sm-12">
					<h5>Scanning Results</h5>
				</div>
				<div class="col-lg-4 col-sm-12">
					<div class="row">
						<div class="col-sm-6">
							<select id="optionMatchStatus" name="optionMatchStatus" class="form-control form-control-sm">
								<option value="">All Match Status</option>
								<option value="Match">Match</option>
								<option value="Not Match">Not Match</option>
							</select>
						</div>
						<div class="col-sm-6">
							<select id="optionPaymentStatusOTA" name="optionPaymentStatusOTA" class="form-control form-control-sm">
								<option value="">All Payment Status</option>
								<option value="Unpaid">Unpaid</option>
								<option value="Paid">Paid</option>
								<option value="Cancel/Refund">Cancel/Refund</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="box-body">
			<div class="row mt-5 responsive-table-container tableFixHead" style="height: 800px">
				<table class="table" id="table-resultUploadExcelPaymentOTA">
					<thead class="thead-light">
						<tr>
							<th width="40" class="text-right" align="right">No.</th>
							<th width="120">Booking Code</th>
							<th>Reservation Details</th>
							<th width="200">Excel Nominal</th>
							<th width="150">Database Nominal</th>
							<th width="80">Match</th>
							<th width="90">Payment</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="7" class="text-center"><b>No Data</b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-reservationPayment">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-reservationPayment">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-title-reservationPayment">Data Reservation Payment Editing</h4>
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
						<label for="description" class="control-label">Description</label>
						<input type="text" class="form-control" id="description" name="description" placeholder="Description" maxlength="150">
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
				<input type="hidden" id="idData" name="idData" value="">
				<input type="hidden" id="idReservation" name="idReservation" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<input type="hidden" id="editablePayment" name="editablePayment" value="0">
				<input type="hidden" id="isUpsellingCheckedOrigin" name="isUpsellingCheckedOrigin" value="0">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-transferDepositPayment">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-transferDepositPayment">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-title-transferDepositPayment">Transfer Deposit Payment</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row pb-0 mb-0">
					<div class="col-sm-6 mb-5" style="border-right: 1px solid #e0e0e0;border-bottom: 1px solid #e0e0e0;"><h5>From</h5></div>
					<div class="col-sm-6 mb-5" style="border-bottom: 1px solid #e0e0e0;">
						<h5>
							Transfer To
							<button class="button button-xs button-primary pull-right" type="button" id="btnModalSearchReservationTransferDepositPayment" data-toggle="modal" data-target="#modal-searchReservationTransferDepositPayment" data-customerName="" data-bookingCode="">
								<span><i class="fa fa-search"></i>Select Reservation</span>
							</button>
						</h5>
					</div>
				</div>
				<div class="row pt-0 mb-5 mt-0">
					<div class="col-sm-6" style="border-right: 1px solid #e0e0e0;">
						<b id="transferDepositPaymentFromCustomerName">-</b><br>
						<b id="transferDepositPaymentFromReservationTitle">-</b><br>
						<b id="transferDepositPaymentFromBookingCode">-</b><br>
						[<span id="transferDepositPaymentFromInputType">-</span>] <span id="transferDepositPaymentFromSourceName">-</span><br><br>
						<b>Revenue (Finance) : </b><br/>
						[IDR] <span id="transferDepositPaymentFromRevenue">-</span>
					</div>
					<div class="col-sm-6">
						<b id="transferDepositPaymentToCustomerName">-</b><br>
						<b id="transferDepositPaymentToReservationTitle">-</b><br>
						<b id="transferDepositPaymentToBookingCode">-</b><br>
						[<span id="transferDepositPaymentToInputType">-</span>] <span id="transferDepositPaymentToSourceName">-</span><br><br>
						<b>Transferred Deposit [Max. <span id="maxTransferredDeposit"></span> IDR] : </b><br/>
						<input type="text" class="form-control text-right" id="nominalTransferredDeposit" name="nominalTransferredDeposit" value="0">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idReservationFrom" name="idReservationFrom" value="">
				<input type="hidden" id="idReservationTo" name="idReservationTo" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-searchReservationTransferDepositPayment">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="editor-searchReservationTransferDepositPayment">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-title-searchReservationTransferDepositPayment">Search Reservation to Transfer Deposit</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 pb-5" style="border-bottom: 1px solid #e0e0e0;">
						<div class="form-group required mb-5">
							<label for="keywordSearchReservationTransferDepositPayment" class="control-label">Search reservation by customer name / booking code / remark / tour plan</label>
							<input type="text" class="form-control" id="keywordSearchReservationTransferDepositPayment" name="keywordSearchReservationTransferDepositPayment" placeholder="Type something and push ENTER to search" maxlength="150">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 tableFixHead" style="max-height:350px">
						<table class="table mt-5" id="table-searchReservationTransferDepositPayment">
							<thead class="thead-light">
								<tr><th colspan="4">Maximum data displayed : 50</th></tr>
							</thead>
							<tbody id="tbody-searchReservationTransferDepositPayment"></tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-editorRevenueReservation">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-editorRevenueReservation">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-title-editorRevenueReservation">Edit Reservation Revenue</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-5">
					<div class="col-sm-12 pb-15 mb-15 border-bottom">
						<div class="order-details-customer-info">
							<ul>
								<li> <span>Source</span>		<span id="editorRevenueReservation-source">-</span> </li>
								<li> <span>Booking Code</span>	<span id="editorRevenueReservation-bookingCode">-</span> </li>
								<li> <span>Cust. Name</span>	<span id="editorRevenueReservation-customerName">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12">
						<div class="form-group required">
							<label for="editorRevenueReservation-currency" class="control-label">Currency</label>
							<select class="form-control" id="editorRevenueReservation-currency" name="editorRevenueReservation-currency">
								<option value="IDR">IDR</option>
								<option value="USD">USD</option>
							</select>
						</div>
					</div>
					<div class="col-lg-4 col-sm-8 px-1">
						<div class="form-group required">
							<label for="editorRevenueReservation-revenueInteger" class="control-label">Integer</label>
							<input type="text" class="form-control mb-10 text-right" id="editorRevenueReservation-revenueInteger" name="editorRevenueReservation-revenueInteger" onkeyup="calculateReservationRevenueIDR()" onkeypress="maskNumberInput(0, 999999999, 'editorRevenueReservation-revenueInteger');">
						</div>
					</div>
					<div class="col-lg-3 col-sm-4" style="padding-left: 6px;">
						<div class="form-group required">
							<label for="editorRevenueReservation-revenueDecimal" class="control-label">Comma</label>
							<input type="text" class="form-control mb-10 text-right decimalInput" id="editorRevenueReservation-revenueDecimal" name="editorRevenueReservation-revenueDecimal" onkeyup="calculateReservationRevenueIDR()" onkeypress="maskNumberInput(0, 99, 'editorRevenueReservation-revenueDecimal');" readonly>
						</div>
					</div>
					<div class="col-lg-5 col-sm-6">
						<div class="form-group">
							<label for="editorRevenueReservation-currencyExchange" class="control-label">Currency Exchange</label>
							<input type="text" class="form-control mb-10 text-right" id="editorRevenueReservation-currencyExchange" name="editorRevenueReservation-currencyExchange" onkeyup="calculateReservationRevenueIDR()" onkeypress="maskNumberInput(1, 999999999, 'editorRevenueReservation-currencyExchange')" readonly>
						</div>
					</div>
					<div class="col-lg-7 col-sm-6">
						<div class="form-group ">
							<label for="editorRevenueReservation-reservationRevenueIDR" class="control-label">Revenue (IDR)</label>
							<input type="text" class="form-control mb-10 text-right" id="editorRevenueReservation-reservationRevenueIDR" name="editorRevenueReservation-reservationRevenueIDR" readonly>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorRevenueReservation-idReservation" name="idReservation" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var dataIdPaymentMethodUpselling=	JSON.parse("<?=$dataIdPaymentMethodUpselling?>");
	var url							=	"<?=BASE_URL_ASSETS?>js/page-module/Finance/detailReservationPayment.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>