<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Reservation Invoice <span> / Create reservation invoice & invoice history</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#createInvoiceTab"><i class="fa fa-pencil"></i> Create Invoice</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#invoiceHistoryTab"><i class="fa fa-files-o"></i> Invoice History</a></li>
		</ul>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body tab-content">
		<div class="tab-pane fade show active" id="createInvoiceTab">
			<div class="row">
				<div class="col-12">
					<h5 class="title">
						<span id="chooseReservationTitle">Choose reservation to create new invoice</span>
						<button class="button button-warning button-sm pull-right d-none" id="cancelInvoice"><span><i class="fa fa-times"></i>Cancel</span></button>
						<button class="button button-primary button-sm pull-right d-none" id="saveInvoice"><span><i class="fa fa-save"></i>Save</span></button>
						<button class="button button-primary button-sm pull-right mr-5" id="chooseReservation" data-toggle="modal" data-target="#modal-chooseReservation"><span><i class="fa fa-external-link"></i>Choose Reservation</span></button>
						<input type="hidden" id="idReservationValueHidden" name="idReservationValueHidden" value="0">
					</h5>
				</div>
				<div class="col-9 mt-20">
					<table class="table" id="table-detailReservation">
						<thead class="thead-light">
							<tr>
								<th>Reservation Details</th>
								<th>Customer Details</th>
								<th>Location</th>
							</tr>
						</thead>
						<tbody id="bodyDetailReservation">
							<tr>
								<td colspan="3" align="center">Please choose reservation</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-3 mt-20">
					<table class="table" id="table-listInvoice">
						<thead class="thead-light">
							<tr>
								<th>List Of Invoice</th>
							</tr>
						</thead>
						<tbody id="bodyListInvoice">
							<tr>
								<td align="center">No Data</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-12 mt-20 d-none" id="invoicePreview">
					<div class="row">
						<div class="col-12 mb-15 text-center" style="border-top: 2px solid #e0e0e0;border-bottom: 2px solid #e0e0e0;">
							<h5 class="mt-15 mb-15 text-center">Invoice Preview</h5>
							<div class="adomx-checkbox-radio-group inline mb-15 text-center">
								<label class="adomx-radio-2 mx-auto"><input type="radio" name="invoiceType" value="1" checked> <i class="icon"></i> Reservation Invoice</label>
								<label class="adomx-radio-2 mx-auto"><input type="radio" name="invoiceType" value="2"> <i class="icon"></i> Additional Cost Invoice</label>
							</div>
						</div>
						<div class="col-1 text-right">
							<img src="<?=BASE_URL_ASSETS?>img/logo-color-2025.png" alt="" height="80px">
						</div>
						<div class="col-2">
							<h4><?=COMPANY_NAME?></h4>
							<small><?=COMPANY_ADDRESS?><br/><?=COMPANY_PHONE?><br/><?=COMPANY_EMAIL?></small>
						</div>
						<div class="col-6"></div>
						<div class="col-3 text-right">
							<b class="previewFont">INVOICE NO.</b><br/>
							<span class="previewFont previewValue" id="invoiceNumberComposerContainer">
								<input type="text" class="form-control form-control-sm text-right" id="invoiceNumberComposer" name="invoiceNumberComposer" disabled>
							</span>
							<b class="previewFont">DATE</b><br/>
							<span class="previewFont previewValue" id="invoiceDateComposerContainer">
								<input type="text" class="form-control form-control-sm input-date-single mb-10 text-right" id="invoiceDateComposer" name="invoiceDateComposer" value="<?=date('d-m-Y')?>">
							</span>
							<b class="previewFont">DUE</b><br/>
							<span class="previewFont previewValue">On Receipt</span><br/>
							<b class="previewFont">BALANCE DUE</b><br/>
							<span class="previewFont" id="balanceDuePreview">IDR <span id="totalBalanceDueSmall">0</span>,-</span>
						</div>
						<div class="col-12 mt-15 mx-1" style="border-top: 2px solid #e0e0e0;"></div>
						<div class="col-3 mt-15">
							<b class="previewFont">BILL TO</b><br/>
							<h5>
								<input type="text" class="form-control form-control-sm" id="customerNameComposer" name="customerNameComposer">
							</h5>
							<span class="previewFont previewValue" id="customerPhoneNumberComposerContainer">
								<input type="text" class="form-control form-control-sm" id="customerPhoneNumberComposer" name="customerPhoneNumberComposer">
							</span><br/>
						</div>
						<div class="col-9 mt-15"></div>
						<div class="col-12 mx-1" style="border-top: 2px solid #e0e0e0;"></div>
						<div class="col-12 mt-5 mx-1">
							<table border="0" width="100%" cellspacing="0">
								<thead>
									<tr style="border-bottom: 2px solid #e0e0e0;">
										<th class="previewFont">DESCRIPTION</th>
										<th class="previewFont text-right" align="right" width="160">RATE</th>
										<th class="previewFont text-right" align="right" width="80">QTY</th>
										<th class="previewFont text-right" align="right" width="160">AMOUNT</th>
									</tr>
								</thead>
								<tbody id="invoiceItemBody">
									<tr style="border-bottom: 1px solid #e0e0e0;">
										<td colspan="4" class="text-center">
											<button class="button button-primary button-sm mt-5" id="btnAddNewItem" data-toggle="modal" data-target="#modal-editorInvoiceItem">
												<span><i class="fa fa-plus"></i>Add New Item</span>
											</button>
										</td>
									</tr>
									<tr class="previewFont">
										<td></td>
										<td style="border-bottom: 1px solid #e0e0e0;" colspan="2"><b>TOTAL</b></td>
										<td style="border-bottom: 1px solid #e0e0e0;" align="right"><b>Rp. <span id="totalInvoiceItem">0</span>,-</b></td>
									</tr>
									<tr class="previewFont">
										<td></td>
										<td style="border-bottom: 1px solid #e0e0e0;"><b>BALANCE</b></td>
										<td style="border-bottom: 1px solid #e0e0e0;" class="text-right"><h5>Rp.</h5></td>
										<td style="border-bottom: 1px solid #e0e0e0;" align="right">
											<input type="text" class="form-control form-control-sm text-right" id="customerBalanceComposer" name="customerBalanceComposer" value="0" onkeyup="calculateTotalAmountItemAndDue()" onkeypress="maskNumberInput(0, 9999999, 'customerBalanceComposer')">
										</td>
									</tr>
									<tr class="previewFont">
										<td></td>
										<td style="border-bottom: 2px solid #e0e0e0;"><b>DUE</b></td>
										<td style="border-bottom: 2px solid #e0e0e0;" colspan="2" align="right"><h5>Rp. <span id="totalBalanceDueInvoice">0</span>,-</h5></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-12 mt-10 mx-1">
							<h5 class="mb-5">Payment Instructions</h5>
							<b class="previewFont">BANK TRANSFER</b><br/>
							<?=COMPANY_BANK_NAME?><br/>
							Bank : <?=COMPANY_BANK_PROVIDER?><br/>
							Account Number : <?=COMPANY_BANK_ACCOUNT_NUMBER?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="invoiceHistoryTab">
			<div class="row border-bottom pb-10 mb-20">
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-10">
						<label for="startDate" class="control-label">Invoice Date Between</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-10">
						<label for="endDate" class="control-label">&nbsp;</label>
						<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-8 col-sm-12">
					<div class="form-group mb-10">
						<label for="keyword" class="control-label">Keyword</label>
						<input type="text" class="form-control mb-10" id="keyword" name="keyword" placeholder="Search by customer name or booking code and press ENTER to search">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<span id="tableDataCountInvoiceHistory"></span>
				</div>
				<div class="col-12 mt-10 responsive-table-container">
					<table class="table" id="table-invoiceHistory">
						<thead class="thead-light">
							<tr>
								<th>Reservation Details</th>
								<th width="200">Customer Details</th>
								<th width="200">Location</th>
								<th width="300">Invoice Details</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="4" class="text-center">No data found</th>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-sm-12 border-top pt-10 mt-10">
					<ul class="pagination" id="tablePaginationInvoiceHistory"></ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-chooseReservation" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="editor-chooseReservation">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="footable-editor-title">Search & Choose Reservation</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<div class="row">
								<div class="col-lg-8 col-sm-12">
									<div class="form-group">
										<label for="nameBookingCodeChooseReservation" class="control-label">Search by customer name / booking code</label>
										<input type="text" class="form-control" id="nameBookingCodeChooseReservation" name="nameBookingCodeChooseReservation" placeholder="Type customer name or booking code and press ENTER to search">
									</div>
								</div>
								<div class="col-lg-2 col-sm-6">
									<div class="form-group">
										<label for="optionMonthChooseReservation" class="control-label">Period</label>
										<select class="form-control" id="optionMonthChooseReservation" name="optionMonthChooseReservation" option-all="All Month"></select>
									</div>
								</div>
								<div class="col-lg-2 col-sm-6">
									<div class="form-group">
										<label for="optionYearChooseReservation" class="control-label">.</label>
										<select class="form-control" id="optionYearChooseReservation" name="optionYearChooseReservation" option-all="All Year"></select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 mb-10 tableFixHead">
							<table class="table" id="table-chooseReservation">
								<thead class="thead-light">
									<tr>
										<th>Reservation Details</th>
										<th>Customer Details</th>
										<th>Location</th>
										<th width="100"></th>
									</tr>
								</thead>
								<tbody id="bodyChooseReservation">
									<tr>
										<td colspan="4" align="center">Please search data by entering keywords or select month and year</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="button button-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
    </div>
</div>
<div class="modal fade" id="modal-editorInvoiceItem" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-editorInvoiceItem">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="footable-editor-title">New Invoice Item</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="descriptionInvoiceItem" class="control-label">Description</label>
										<input type="text" class="form-control" id="descriptionInvoiceItem" name="descriptionInvoiceItem" placeholder="Type Description">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="subDescriptionInvoiceItem" class="control-label">Sub Description</label>
										<input type="text" class="form-control" id="subDescriptionInvoiceItem" name="subDescriptionInvoiceItem" placeholder="Type Sub Description">
									</div>
								</div>
								<div class="col-sm-8">
									<div class="form-group">
										<label for="rateInvoiceItem" class="control-label">Rate</label>
										<input type="text" class="form-control text-right" id="rateInvoiceItem" name="rateInvoiceItem" placeholder="Rate" value="0" onkeyup="calculateInvoiceItem()" onkeypress="maskNumberInput(0, 9999999, 'rateInvoiceItem')">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="quantityInvoiceItem" class="control-label">Quantity</label>
										<input type="text" class="form-control text-right" id="quantityInvoiceItem" name="quantityInvoiceItem" placeholder="QTY" value="1" onkeyup="calculateInvoiceItem()" onkeypress="maskNumberInput(1, 99, 'quantityInvoiceItem', calculateInvoiceItem)">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="totalAmountInvoiceItem" class="control-label">Total Amount</label>
										<input type="text" class="form-control text-right" id="totalAmountInvoiceItem" name="totalAmountInvoiceItem" placeholder="Total Amount" value="0" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
					<button class="button button-primary" id="btnAddNewItemInvoice">Add</button>
				</div>
			</div>
		</form>
    </div>
</div>
<div class="modal fade" id="modal-inputEmail" aria-hidden="true">
    <div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-inputEmail">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="footable-editor-title">Confirm & input customer email</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<p>Please enter the customer's email and make sure all the fields are filled correctly before making an invoice. Invoice will be sent automatically to customer's email</p>
						</div>
						<div class="col-sm-12 mb-10">
							<label for="customerEmail" class="control-label">Customer Email</label>
							<input type="text" class="form-control" id="customerEmail" name="customerEmail">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="button button-primary" id="btnSubmitReservationInvoice">Submit</button>
				</div>
			</div>
		</form>
    </div>
</div>
<style>
	.order-details-customer-info ul li span:first-child{
		width: 48px;
		margin-right: 8px;
	}
	.table td {
		word-break: break-word;
		white-space: break-spaces;
	}
	.previewFont{
		font-size: 11px;
	}
	.previewValue{
		line-height: 14px;
		vertical-align: top;
	}
</style>
<script>
	var dateToday	=	"<?=date('d-m-Y')?>";
	var url 		=	"<?=BASE_URL_ASSETS?>js/page-module/Finance/reservationInvoice.js";
	$.getScript(url);
</script>