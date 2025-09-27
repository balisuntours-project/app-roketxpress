<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Recap Per Vendor <span> / Recap fee per vendor</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseDetails"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseDetailsManualWithdraw"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
		</div>
	</div>
</div>
<div class="alert alert-warning" role="alert">
	<i class="zmdi zmdi-info"></i> <span>This menu only displays vendors that are already using the new financial scheme</span>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
	<div class="box mb-10">
		<div class="box-body">
			<ul class="nav nav-tabs" id="tabsPanel">
				<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#allVendorReportTab"><i class="fa fa-file-text"></i> All Vendor Report</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#recapFeePerPeriodTab"><i class="fa fa-calendar"></i> Recap Fee Per Period</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#recapPerVendorTab"><i class="fa fa-file"></i> Recap Per Vendor</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#withdrawalTab"><i class="fa fa-credit-card-alt"></i> Withdrawal</a></li>
			</ul>
		</div>
	</div>
	<div class="box mb-10">
		<div class="box-body">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="allVendorReportTab">
					<div class="row">
						<div class="col-lg-6 col-sm-12">
							<div class="form-group">
								<label for="optionVendorTypeReport" class="control-label">Vendor Type</label>
								<select id="optionVendorTypeReport" name="optionVendorTypeReport" class="form-control" option-all="All Vendor Type"></select>
							</div>
						</div>
						<div class="col-lg-6 col-sm-12">
							<div class="form-group">
								<label for="optionVendorReport" class="control-label">Vendor</label>
								<select id="optionVendorReport" name="optionVendorReport" class="form-control" option-all="All Vendor"></select>
							</div>
						</div>
						<div class="col-12 mt-10">
							<div class="row">
								<div class="col-lg-8 col-sm-12 mb-10">
									<span id="tableDataCountReportPerVendor"></span>
								</div>
								<div class="col-lg-4 col-sm-12 mb-10">
									<a class="button button-primary button-sm pull-right" id="excelAllVendorReport" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Report Per Vendor</span></a>
								</div>
							</div>
							<div class="row mt-5 responsive-table-container">
								<table class="table" id="table-reportPerVendor">
									<thead class="thead-light">
										<tr>
											<th >Vendor Type</th>
											<th >Vendor Name</th>
											<th width="120" class="text-right">Schedule</th>
											<th width="120" class="text-right">Fee</th>
											<th width="120" class="text-right">Collect Payment</th>
											<th width="120" class="text-right">Balance</th>
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
									<ul class="pagination" id="tablePaginationReportPerVendor"></ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="recapFeePerPeriodTab">
					<div class="row">
						<div class="col-lg-2 col-sm-3">
							<div class="form-group">
								<label for="startDate" class="control-label">Date Schedule</label>
								<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
							</div>
						</div>
						<div class="col-lg-2 col-sm-3">
							<div class="form-group">
								<label for="endDate" class="control-label">.</label>
								<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
							</div>
						</div>
						<div class="col-lg-4 col-sm-6">
							<div class="form-group">
								<label for="optionVendorTypeRecap" class="control-label">Vendor Type</label>
								<select id="optionVendorTypeRecap" name="optionVendorTypeRecap" class="form-control" option-all="All Vendor Type"></select>
							</div>
						</div>
						<div class="col-lg-4 col-sm-6">
							<div class="form-group">
								<label for="optionVendorRecap" class="control-label">Vendor</label>
								<select id="optionVendorRecap" name="optionVendorRecap" class="form-control" option-all="All Vendor"></select>
							</div>
						</div>
						<div class="col-12 mt-20">
							<div class="row mt-10">
								<div class="col-lg-8 col-sm-12 mb-10">
									<span id="tableDataCountRecapPerVendor"></span>
								</div>
								<div class="col-lg-4 col-sm-12 mb-10">
									<a class="button button-primary button-sm pull-right" id="excelRecapPerVendor" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Recap Per Vendor</span></a>
								</div>
							</div>
							<div class="row mt-5 responsive-table-container">
								<table class="table" id="table-recapPerVendor">
									<thead class="thead-light">
										<tr>
											<th width="120">Vendor Type</th>
											<th >Vendor Name</th>
											<th width="120" class="text-right">Total Schedule</th>
											<th width="120" class="text-right">Fee</th>
											<th width="60"></th>
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
									<ul class="pagination" id="tablePaginationRecapPerVendor"></ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="recapPerVendorTab">
					<div class="row">
						<div class="col-lg-4 col-sm-6">
							<select id="optionVendorTypePerRecap" name="optionVendorTypePerRecap" class="form-control"></select>
						</div>
						<div class="col-lg-8 col-sm-6">
							<select id="optionVendorPerRecap" name="optionVendorPerRecap" class="form-control"></select>
						</div>
						<div class="col-sm-12">
							<div class="rowVendor rounded-lg row px-3 py-3 mx-0 mb-1 mt-10 bg-white">
								<div class="col-lg-2 col-sm-4 text-center">
									<div class="author-profile">
										<div class="image">
											<h1 id="vendorDetailInitial">-</h1>
										</div>
									</div>
								</div>
								<div class="col-lg-10 col-sm-8">
									<div class="row px-0 py-0">
										<div class="col-lg-8 col-sm-12" id="containerDetailVendor">
											<p class="mb-0"><b id="vendorDetailName">-</b></p>
											<p class="mb-0" id="vendorDetailAddress">-</p>
											<p class="mb-0" id="vendorDetailPhoneEmail">-</p><br>
											<span class="badge badge-pill mr-2 badge-primary"><p class="m-0 p-1 text-white">Vendor Type : <span id="vendorDetailVendorType">-</span></p></span>
										</div>
										<div class="col-lg-4 col-sm-12">
											<img src="<?=URL_BANK_LOGO?>default.png" style="max-height:30px; max-width:90px" class="mb-10" id="vendorBankLogo"><br/>
											<ul class="list-icon"><li class="pl-0"><i class="fa fa-university mr-1"></i><span id="vendorBankName">-</span></li></ul>
											<ul class="list-icon"><li class="pl-0"><i class="fa fa-credit-card mr-1"></i><span id="vendorBankAccountNumber">-</span></li></ul>
											<ul class="list-icon"><li class="pl-0"><i class="fa fa-user-circle mr-1"></i><span id="vendorBankAccountHolder">-</span></li></ul>
											<button class="button button-warning button-xs mt-5 d-none" id="btnManualWithdraw">
												<span><i class="fa fa-plus"></i>Manual Withdraw</span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="rowVendor rounded-lg row px-2 py-2 mx-0 mb-1 mt-10 bg-white">
								<div class="col-xlg-3 col-sm-12 px-0 border-right">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Total Fee</h4>
											<span class="view"><i class="fa fa-money"></i></span>
										</div>
										<div class="content">
											<h2 id="totalNominalFee">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Total Schedule : <span id="totalSchedule">0</span></p>
										</div>
									</div>
								</div>
								<div class="col-xlg-3 col-sm-12 px-0 border-right">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Collect Payment</h4>
											<span class="view"><i class="fa fa-list-alt"></i></span>
										</div>
										<div class="content">
											<h2 id="totalNominalCollectPayment">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Total Schedule With Collect : <span id="totalCollectPayment">0</span></p>
										</div>

									</div>
								</div>
								<div class="col-xlg-3 col-sm-12 px-0 border-right">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Withdraw Balance</h4>
											<span class="view"><i class="fa fa-cc-mastercard"></i></span>
										</div>
										<div class="content">
											<h2 id="totalWithdrawBalance">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Last Withdrawal : <span id="lastWitdrawalDate">-</span></p>
										</div>
									</div>
								</div>
								<div class="col-xlg-3 col-sm-12 px-0">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Deposit Balance</h4>
											<span class="view"><i class="fa fa-cc-amex"></i></span>
										</div>
										<div class="content">
											<h2 id="totalDepositBalance">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Last Transaction : <span id="lastDepositTransactionDate">-</span></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 mt-20">
							<ul class="nav nav-tabs" id="tabsPanelDetail">
								<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#listFeeTab"><i class="fa fa-money"></i> List Fee</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#listCollectPaymentTab"><i class="fa fa-list-alt"></i> List Collect Payment</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#listDepositTransactionTab"><i class="fa fa-list-alt"></i> List Deposit Transaction</a></li>
							</ul>
							<div class="tab-content mt-30">
								<div class="tab-pane fade show active" id="listFeeTab">
									<div class="tableFixHead">
										<table class="table" id="table-listFee">
											<thead class="thead-light">
												<tr>
													<th width="120">Date</th>
													<th width="250">Source & Booking Code</th>
													<th width="250">Customer Name</th>
													<th>Reservation Title | Schedule Title</th>
													<th class="text-right" width="120">Total Fee</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="listCollectPaymentTab">
									<div class="tableFixHead">
										<table class="table" id="table-listCollectPayment">
											<thead class="thead-light">
												<tr>
													<th width="120">Date</th>
													<th>Reservation Details</th>
													<th>Remarks</th>
													<th>Payment Description</th>
													<th class="text-right" width="120">Amount</th>
													<th class="text-right" width="120">Amount (IDR)</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="listDepositTransactionTab">
									<div class="row">
										<div class="col-lg-2 col-sm-6">
											<div class="form-group">
												<label for="startDateDepositTransaction" class="control-label">Date Transaction</label>
												<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateDepositTransaction" name="startDateDepositTransaction" value="<?=date('01-m-Y')?>">
											</div>
										</div>
										<div class="col-lg-2 col-sm-6">
											<div class="form-group">
												<label for="endDateDepositTransaction" class="control-label">.</label>
												<input type="text" class="form-control input-date-single text-center" id="endDateDepositTransaction" name="endDateDepositTransaction" value="<?=date('t-m-Y')?>">
											</div>
										</div>
										<div class="col-lg-8 col-sm-12 text-right">
											<button class="button button-info button-sm pull-right mt-30" id="btnAddNewDepositRecord" data-toggle="modal" data-target="#modal-addNewDepositRecord"><span><i class="fa fa-plus"></i>Add New Deposit Record</span></button>
										</div>
									</div>
									<div class="tableFixHead">
										<table class="table" id="table-listDepositTransaction">
											<thead class="thead-light">
												<tr>
													<th width="140">Input Details</th>
													<th>Description</th>
													<th>Reservation Details</th>
													<th>Collect Payment Details</th>
													<th class="text-right" width="120">Amount</th>
													<th width="60"></th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="withdrawalTab">
					<div class="row" style="border-bottom: 1px solid #dee2e6;">
						<div class="col-lg-2 col-sm-6">
							<div class="form-group">
								<label for="startDateWithdrawalRequest" class="control-label">Date Request</label>
								<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateWithdrawalRequest" name="startDateWithdrawalRequest" value="<?=date('01-m-Y')?>">
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="form-group">
								<label for="endDateWithdrawalRequest" class="control-label">.</label>
								<input type="text" class="form-control input-date-single text-center" id="endDateWithdrawalRequest" name="endDateWithdrawalRequest" value="<?=date('t-m-Y')?>">
							</div>
						</div>
						<div class="col-lg-4 col-sm-6">
							<div class="form-group">
								<label for="optionVendorWithdrawal" class="control-label">Vendor</label>
								<select id="optionVendorWithdrawal" name="optionVendorWithdrawal" class="form-control" option-all="All Vendor"></select>
							</div>
						</div>
						<div class="col-lg-4 col-sm-6">
							<div class="form-group">
								<label for="optionStatusWithdrawal" class="control-label">Withdrawal Status</label>
								<select id="optionStatusWithdrawal" name="optionStatusWithdrawal" class="form-control">
									<option value="">All Status</option>
									<option value="0">Requested</option>
									<option value="1">Approved</option>
									<option value="2">Transfered</option>
									<option value="-1">Rejected</option>
								</select>
							</div>
						</div>
						<div class="col-sm-12 mb-10">
							<div class="form-group">
								<label class="adomx-checkbox">
									<input type="checkbox" id="checkboxViewWithdrawalRequestOnly" name="checkboxViewWithdrawalRequestOnly" value="1"> <i class="icon"></i> <b>Show withdrawal requests only</b>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 mt-40 mb-30 text-center" id="noDataWithdrawalRequest">
							<img src="<?=BASE_URL_ASSETS?>img/no-data.png" style="max-height: 150px"/>
							<h5>No Data Found</h5>
							<p id="msgNoDataWithdrawalRequest">There are no withdrawal requests at this time</p>
						</div>
						<div class="col-12 d-none responsive-table-container" id="withdrawalRequestListContainer">
							<div class="row py-2" id="withdrawalRequestList"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRightManualWithdraw">
	<div class="row d-none">
		<div class="col-lg-4 col-sm-12">
			<div class="box">
				<div class="box-body">
					<div class="author-profile mb-20">
						<div class="image">
							<h1 id="manualWithdraw-vendorDetailInitial">R8</h1>
						</div>
					</div>
					<p class="mb-0"><b id="manualWithdraw-vendorDetailName">-</b></p>
					<p class="mb-0" id="manualWithdraw-vendorDetailAddress">-</p>
					<p class="mb-20" id="manualWithdraw-vendorDetailPhoneEmail">-</p>
					<div class="alert alert-danger" role="alert"  id="manualWithdraw-autoReduceCollectPaymentWarning">
						<i class="zmdi zmdi-info"></i> <span>Vendor use <b>Auto Reduce Collect Payment</b>. The uploaded excel file only contains the <b>final fee amount</b> (after deducting the collect payment)</span>
					</div>
					<h6 class="border-top pt-4 mb-4">Active Bank Account Detail</h6>
					<img style="max-height:30px; max-width:90px" class="mb-10" id="manualWithdraw-imgBankLogo"><br>
					<ul class="list-icon"><li class="pl-0"><i class="fa fa-university mr-1"></i><span id="manualWithdraw-bankNameStr"></span></li></ul>
					<ul class="list-icon"><li class="pl-0"><i class="fa fa-credit-card mr-1"></i><span id="manualWithdraw-accountNumberStr"></span></li></ul>
					<ul class="list-icon mb-10"><li class="pl-0"><i class="fa fa-user-circle mr-1"></i><span id="manualWithdraw-accountHolderNameStr"></span></li></ul>
					<button class="button button-info button-sm btn-block mt-10 mb-20" id="manualWithdraw-btnBankAccountPartnerModal" data-toggle="modal" data-target="#modal-manualWithdrawBankAccountList"><span><i class="fa fa-external-link-square"></i>Bank Account List</span></button>
					<p class="mt-10 mb-30 border-top"></p>
					<div class="alert alert-primary mb-20 py-2" role="alert">
						<i class="zmdi zmdi-info"></i> Please upload the invoice document provided by vendor.<br/>The allowed documents are <b>xls, xlsx, doc, docx, jpg/jpeg, png, and pdf</b> with a maximum size of <b>800 kolibytes</b>
					</div>
					<div class="row border-bottom mb-20">
						<div class="col-sm-12 text-center">
							<i class="fa fa-cloud-upload display-3 mb-20" style="font-size: 48px;" id="manualWithdraw-iconDocumentManualWithdraw"></i><br/>
							<a href="#" id="manualWithdraw-withdrawDocument">Upload Document</a>
						</div>
						<div class="col-sm-12 text-center">
							<p id="manualWithdraw-documentNameManualWithdraw" class="mb-20"></p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 text-center">
							<input type="hidden" id="manualWithdraw-idVendor" name="manualWithdraw-idVendor" value="0">
							<input type="hidden" id="manualWithdraw-idBankAccountVendor" name="manualWithdraw-idBankAccountVendor" value="0">
							<input type="hidden" id="manualWithdraw-fileWithdrawDocument" name="manualWithdraw-fileWithdrawDocument" value="">
							<input type="hidden" id="manualWithdraw-autoReduceCollectPayment" name="manualWithdraw-autoReduceCollectPayment" value="0">
							<button class="button button-primary button-sm mb-0" id="manualWithdraw-btnSaveManualWithdraw" type="button"><span><i class="fa fa-save"></i>Save Manual Withdraw</span></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-8 col-sm-12">
			<div class="box">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 border-bottom pb-10 mb-10">
							<button class="button button-primary button-sm" id="manualWithdraw-btnModalUploadInvoice" type="button" data-toggle="modal" data-target="#modal-manualWithdrawUploadInvoice"><span><i class="fa fa-cloud-upload"></i>Upload Invoice</span></button>
							<button class="button button-info button-sm pull-right" id="manualWithdraw-btnAddAdditionalCostDeduction" type="button" data-toggle="modal" data-target="#modal-manualWithdrawAdditionalCostDeduction"><span><i class="fa fa-plus"></i>Additional Cost / Deduction</span></button>
						</div>
						<div class="col-lg-2 col-sm-6">
							<input type="text" class="form-control input-date-single mb-10 text-center" id="manualWithdraw-startDate" name="manualWithdraw-startDate">
						</div>
						<div class="col-lg-2 col-sm-6">
							<input type="text" class="form-control input-date-single mb-10 text-center" id="manualWithdraw-endDate" name="manualWithdraw-endDate">
						</div>
						<div class="col-lg-8 col-sm-12 pt-15">
							<div class='adomx-checkbox-radio-group inline pull-right'>
								<label class="adomx-checkbox"><input type="checkbox" name="manualWithdraw-withdrawType[]" id="manualWithdraw-withdrawType1" value="1" class="manualWithdraw-withdrawType"> <i class="icon"></i> Fee</label>
								<label class="adomx-checkbox"><input type="checkbox" name="manualWithdraw-withdrawType[]" id="manualWithdraw-withdrawType2" value="2" class="manualWithdraw-withdrawType"> <i class="icon"></i> Additional Cost</label>
								<label class="adomx-checkbox"><input type="checkbox" name="manualWithdraw-withdrawType[]" id="manualWithdraw-withdrawType3" value="3" class="manualWithdraw-withdrawType"> <i class="icon"></i> Collect Payment</label>
								<label class="adomx-checkbox"><input type="checkbox" name="manualWithdraw-withdrawType[]" id="manualWithdraw-withdrawType4" value="4" class="manualWithdraw-withdrawType"> <i class="icon"></i> Deduction</label>
							</div>
						</div>
						<div class="col-sm-12 tableFixHead" style="height: 520px">
							<table class="table" id="manualWithdraw-dataListWithdrawalDetail">
								<thead class="thead-light">
									<tr>
										<th width="60" class="text-center" align="center"><label class="adomx-checkbox mx-auto"><input type="checkbox" id="manualWithdraw-checkAllWithdrawItem"><i class="icon"></i></label></th>
										<th width="150">Type</th>
										<th width="120">Date</th>
										<th>Description</th>
										<th width="120" class="text-right">Amount</th>
									</tr>
								</thead>
								<tbody><tr><td colspan="5" class="text-center">No data found</td></tr></tbody>
							</table>
						</div>
						<div class="col-sm-12 ">
							<table class="table">
								<tbody>
									<tr>
										<td colspan="2" class="font-weight-bold py-2">Fee</td>
										<td class="text-right font-weight-bold py-2" id="manualWithdraw-totalFeeStr">0</td>
									</tr>
									<tr>
										<td colspan="2" class="font-weight-bold py-2">Additional Cost</td>
										<td class="text-right font-weight-bold py-2" id="manualWithdraw-additionalCostStr">0</td>
									</tr>
									<tr>
										<td colspan="2" class="font-weight-bold py-2">Collect Payment</td>
										<td class="text-right font-weight-bold py-2" id="manualWithdraw-totalCollectPaymentStr">0</td>
									</tr>
									<tr>
										<td colspan="2" class="font-weight-bold">Deduction</td>
										<td class="text-right font-weight-bold py-2" id="manualWithdraw-totalDeductionStr">0</td>
									</tr>
								</tbody>
								<tfoot class="thead-light">
									<tr>
										<th colspan="2" class="py-2">Total Withdraw Nominal</th>
										<th width="120" class="text-right py-2" id="manualWithdraw-totalWithdrawalNominalStr">0</th>
									</tr>
								</tfoot>
							</table>
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
			<div class="row" style="border-bottom: 1px solid #e0e0e0;">
				<div class="col-lg-8 col-sm-6" style="border-bottom: 1px solid #e0e0e0;"><h5 id="vendorNameStr">-</h5></div>
				<div class="col-lg-4 col-sm-6 text-right" style="border-bottom: 1px solid #e0e0e0;">Date Time Request : <b id="requestDateTimeStr">-</b></div>
				<div class="col-lg-3 col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<p id="badgeStatusWithdrawal">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-3">Bank Account Detail</h6>
							<img style="max-height:30px; max-width:90px" class="mb-10" id="imgBankLogo"><br>
							<ul class="list-icon"><li class="pl-0"><i class="fa fa-university mr-1"></i><span id="bankNameStr"></span></li></ul>
							<ul class="list-icon"><li class="pl-0"><i class="fa fa-credit-card mr-1"></i><span id="accountNumberStr"></span></li></ul>
							<ul class="list-icon"><li class="pl-0"><i class="fa fa-user-circle mr-1"></i><span id="accountHolderNameStr"></span></li></ul>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Message</h6>
							<p id="messageStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<div class="order-details-customer-info pb-1" style="border-bottom: 1px solid #dee2e6;">
								<ul>
									<li> <span>Fee</span> <span><b id="totalFeeStr"></b></span> </li>
									<li> <span>Additional Cost</span> <span><b id="totalAdditionalCostStr"></b></span> </li>
									<li> <span>Collect Payment</span> <span><b id="totalCollectPaymentStr"></b></span> </li>
									<li> <span>Deduction</span> <span><b id="totalDeductionStr"></b></span> </li>
								</ul>
							</div>
							<div class="order-details-customer-info pt-1">
								<ul>
									<li> <span><b>Total Withdrawal</b></span> <span><b id="totalWithdrawalStr"></b></span> </li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-5 col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Transfer Receipt</h6>
						</div>
						<div class="col-sm-12 mb-10 bg-white" id="transferReceiptPreview"></div>
					</div>
				</div>
				<div class="col-lg-8 col-sm-6 pt-10 mb-10" style="border-top: 1px solid #e0e0e0;">
					<div class="row">
						<div class="col-sm-6">
							<h6 class="mb-0">Approval User</h6>
							<p id="approvalUserStr">-</p>
						</div>
						<div class="col-sm-6">
							<h6 class="mb-0">Date Time Approval</h6>
							<p id="dateTimeApprovalStr">-</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-sm-6 pt-10 mb-10 text-right" style="border-top: 1px solid #e0e0e0;">
					<button class="button button-warning button-sm" id="btnRejectWithdrawal"><span><i class="fa fa-times"></i>Reject</span></button>
					<button class="button button-primary button-sm" id="btnApproveWithdrawal"><span><i class="fa fa-check"></i>Approve</span></button>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 mt-20"><h5>List Of Withdrawal Details</h5></div>
				<div class="col-sm-12 mt-10 tableFixHead" style="max-height: 400px">
					<table class="table" id="table-dataListWithdrawalDetail">
						<thead class="thead-light">
							<tr>
								<th width="150">Type</th>
								<th width="100">Date</th>
								<th>Description</th>
								<th width="120" class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody><td colspan="4" class="text-center">No data found</td></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-manualWithdrawBankAccountList">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-manualWithdrawBankAccountList">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-manualWithdrawBankAccountList">Bank Account List</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<table class="table" id="manualWithdrawBankAccountList-tableBankAccount"><tbody></tbody></table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-sm button-primary mr-auto" id="manualWithdraw-btnAddBankAccountPartner" data-toggle="modal" data-target="#modal-manualWithdrawAddBankAccountVendor">New Bank Account</button>
				<button type="button" class="button button-sm button-info" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-manualWithdrawAddBankAccountVendor">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-manualWithdrawAddBankAccountVendor">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-manualWithdrawAddBankAccountVendor">Add New Bank Account Partner</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-7 col-sm-12">
						<div class="form-group required">
							<label for="manualWithdrawAddBankAccountVendor-optionBank" class="control-label">Bank</label>
							<select id="manualWithdrawAddBankAccountVendor-optionBank" name="manualWithdrawAddBankAccountVendor-optionBank" class="form-control"></select>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12">
						<div class="form-group required">
							<label for="manualWithdrawAddBankAccountVendor-accountNumber" class="control-label">Account Number</label>
							<input type="text" class="form-control nocomma padzeroleft" id="manualWithdrawAddBankAccountVendor-accountNumber" name="manualWithdrawAddBankAccountVendor-accountNumber" onkeypress="maskNumberInput(0, 999999999999999999, 'manualWithdrawAddBankAccountVendor-accountNumber')" value="0">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="manualWithdrawAddBankAccountVendor-accountHolderName" class="control-label">Account Holder Name</label>
							<input type="text" class="form-control" id="manualWithdrawAddBankAccountVendor-accountHolderName" name="manualWithdrawAddBankAccountVendor-accountHolderName">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button button-sm button-primary" id="manualWithdrawAddBankAccountVendor-btnSaveBankAccountPartner">Save</button>
				<button type="button" class="button button-sm button-info" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-manualWithdrawAdditionalCostDeduction">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-manualWithdrawAdditionalCostDeduction">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-manualWithdrawAdditionalCostDeduction">Add New Additional Cost / Deduction</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-sm-12">
						<div class="form-group required">
							<label for="manualWithdrawAdditionalCostDeduction-optionType" class="control-label">Type</label>
							<select id="manualWithdrawAdditionalCostDeduction-optionType" name="manualWithdrawAdditionalCostDeduction-optionType" class="form-control">
								<option value="2">Additional Cost</option>
								<option value="4">Deduction</option>
							</select>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group required">
							<label for="manualWithdrawAdditionalCostDeduction-date" class="control-label">Date</label>
							<input type="text" class="form-control input-date-single text-center" id="manualWithdrawAdditionalCostDeduction-date" name="manualWithdrawAdditionalCostDeduction-date" value="<?=date('d-m-Y')?>">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="manualWithdrawAdditionalCostDeduction-nominal" class="control-label">Nominal</label>
							<input type="text" class="form-control text-right" id="manualWithdrawAdditionalCostDeduction-nominal" name="manualWithdrawAdditionalCostDeduction-nominal" onkeypress="maskNumberInput(1, 999999999999999999, 'manualWithdrawAdditionalCostDeduction-nominal')">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="manualWithdrawAdditionalCostDeduction-description" class="control-label">Description</label>
							<input type="text" class="form-control" id="manualWithdrawAdditionalCostDeduction-description" name="manualWithdrawAdditionalCostDeduction-description">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button button-sm button-primary" id="manualWithdrawAdditionalCostDeduction-btnSaveAdditionalCostDeduction">Add Record</button>
				<button type="button" class="button button-sm button-info" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-manualWithdrawUploadInvoice">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-manualWithdrawUploadInvoice">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-manualWithdrawUploadInvoice">Upload Excel Invoice Data</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-primary py-2" role="alert">
							<i class="zmdi zmdi-info"></i> The allowed documents are <b>xls and xlsx</b> with a maximum size of <b>800 kolibytes.</b> Please follow the excel file format as below
							<ul>
                                <li>Column <b>A</b> : Booking Code</li>
                                <li>Column <b>B</b> : Customer Name</li>
                                <li>Column <b>C</b> : Nominal</li>
                                <li><b>Data start from row number #2</b></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-12 text-center mt-20 mb-20">
						<i class="fa fa-cloud-upload display-3" style="font-size: 48px;"></i><br/>
						<a href="#" id="manualWithdrawUploadInvoice-uploaderExcelInvoice">Upload Excel</a>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-sm button-info" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-manualWithdrawUploadInvoiceInvalidList">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content form-horizontal">
			<div class="modal-header">
				<h4 class="modal-title">Invalid Invoice Data List</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 tableFixHead" style="height: 520px">
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th width="250">Detail Excel</th>
									<th width="250">Detail Booking (System)</th>
									<th>Product</th>
									<th width="100">Schedule</th>
									<th width="110">Status WD</th>
									<th width="160">Invalid Message</th>
								</tr>
							</thead>
							<tbody id="manualWithdrawUploadInvoiceInvalidList-tbodyDataInvalid"><tr><td align="center" colspan="6">No data displayed</td></tr></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-sm button-info" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-zoomReceiptTransfer">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" style="background-color: transparent; border: none;">
			<div class="modal-header" style="border:none">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 mx-auto text-center">
						<img src="" width="600px" id="zoomImageReceiptTransfer">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-addNewDepositRecord">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-addNewDepositRecord">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-addNewDepositRecord">Add new deposit record</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-7 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-lg-6 col-sm-12">
								<div class="form-group required">
									<label for="optionVendorDepositRecord" class="control-label">Vendor</label>
									<select id="optionVendorDepositRecord" name="optionVendorDepositRecord" class="form-control" disabled></select>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="form-group required">
									<label for="dateDepositRecord" class="control-label">Date Record</label>
									<input type="text" class="form-control input-date-single mb-10 text-center" id="dateDepositRecord" name="dateDepositRecord" value="<?=date('d-m-Y')?>">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="depositNominal" class="control-label">Nominal</label>
									<input type="text" class="form-control mb-10 text-right" id="depositNominal" name="depositNominal" value="0" onkeypress="maskNumberInput(1, 999999999, 'depositNominal')">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="depositDescription" class="control-label">Description</label>
									<input type="text" class="form-control" id="depositDescription" name="depositDescription" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12 text-center">
						<h6 class="mb-20">Transfer Receipt</h6>
						<small>Allowed file extension is : <b>.jpg or .png</b>.</small><br/>
						<img id="imageTransferReceiptDeposit" src="" style="max-height:200px; max-width:290px;"/><br/>
						<div id="uploaderTransferReceiptDeposit" class="mt-20">Upload Transfer Receipt</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="transferReceiptDepositFileName" name="transferReceiptDepositFileName" value="">
				<button type="button" class="button button-info" data-dismiss="modal">Cancel</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<input type="hidden" id="urlImageBankLogo" name="urlImageBankLogo" value="<?=URL_BANK_LOGO?>">
<script>
	var dateToday		=	"<?=date('d-m-Y')?>",
		levelUser		=	'<?=$levelUser?>',
		baseURLAssets	=	'<?=BASE_URL_ASSETS?>',
		url 			=	"<?=BASE_URL_ASSETS?>js/page-module/FinanceVendor/recapPerVendor.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
.rowVendor{
	border: 1px solid #e0e0e0;
	min-height: 120px;
	max-height: 250px;
	overflow: hidden;
}
.author-profile .image {
  width: 130px;
  height: 130px;
  overflow: hidden;
  position: relative;
  border-radius: 50%;
  margin: auto;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
      -ms-flex-pack: center;
          justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
  background-color: #f1f1f1;
}

.author-profile .image h1 {
  font-size: 50px;
  margin: 0;
  font-weight: 700;
}
@media (max-width: 767px) {
	.withdrawalTableElement{
		overflow: scroll !important;
	}
}
.withdrawalTableElement{
	border: 1px solid #e0e0e0;
	min-height: 120px;
	background-color: #fff;
}
</style>