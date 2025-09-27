<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Driver Loan & Prepaid Capital<span>/ List of driver loan and prepaid capital</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-info button-sm pull-right" id="btnAddLoanRecord" data-toggle="modal" data-target="#modal-addLoanRecord"><span><i class="fa fa-plus"></i>Loan Record</span></button>
		</div>
	</div>
</div>
<ul class="nav nav-tabs mb-15" id="tabsPanel">
	<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#recapPerDriverTab"><i class="fa fa-id-card-o"></i> Recap per Driver</a></li>
	<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#detailPerDriverTab"><i class="fa fa-check-square-o"></i> Detail per Driver</a></li>
	<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#installmentRequestTab"><i class="fa fa-check-square-o"></i> Installment Request</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane fade show active" id="recapPerDriverTab">
		<div class="box mb-10">
			<div class="box-body">
				<div class="row mt-10" id="formDriverSearch">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="searchKeyword" class="control-label">Type something to search driver data</label>
							<input type="text" class="form-control mb-10" id="searchKeyword" name="searchKeyword" value="">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label class="adomx-checkbox">
								<input type="checkbox" id="checkboxViewRequestOnly" name="checkboxViewRequestOnly" value="1"> <i class="icon"></i> <b>Show all loan and prepaid capital request only</b>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="alert alert-danger d-none pr-15" role="alert" id="totalLoanPrepaidCapitalRequestAlert">
			<i class="fa fa-info"></i> You have <b id="totalLoanPrepaidCapitalRequest">4</b> unprocessed loan and/or prepaid capital request
			<button class="button button-xs button-primary pull-right" id="btnShowAllLoanPrepaidCapitalRequest"><span>Show All</span></button>
		</div>
		<div class="box">
			<div class="box-body">
				<div class="row mt-5">
					<div class="col-lg-8 col-sm-12 mb-10">
						<span id="tableDataCount"></span>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<a class="button button-primary button-sm pull-right" id="excelLoanRecap" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Loan Recap</span></a>
					</div>
				</div>
				<div class="row mt-5">
					<table class="table" id="table-loanPrepaidCapital">
						<tbody></tbody>
					</table>
				</div>
				<div class="row mt-5">
					<div class="col-sm-12 mb-5">
						<ul class="pagination" id="tablePagination"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="detailPerDriverTab">
		<div class="box mb-10">
			<div class="box-body pb-10">
				<div class="row mt-10">
					<div class="col-lg-4 col-sm-12 mb-10">
						<div class="form-group">
							<label for="optionDetailDriver" class="control-label">Driver</label>
							<select id="optionDetailDriver" name="optionDetailDriver" class="form-control select2"></select>
						</div>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<div class="form-group">
							<label for="optionLoanTypeDetail" class="control-label">Loan Type</label>
							<select id="optionLoanTypeDetail" name="optionLoanTypeDetail" class="form-control" option-all="All Loan Type"></select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<ul class="nav nav-tabs mb-10" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailPerDriverTab-history"><i class="fa fa-history"></i> Detail Loan History</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#detailPerDriverTab-activeLoan"><i class="fa fa-outdent"></i> Recap Active Loan</a></li>
		</ul>
		<div class="box">
			<div class="box-body tab-content">
				<div class="tab-pane fade show active" id="detailPerDriverTab-history">
					<div class="row">
						<div class="col-lg-2 col-sm-6 pl-0">
							<input type="text" class="form-control input-date-single text-center" id="startDateDetail" name="startDateDetail" value="<?=date('01-m-Y')?>">
						</div>
						<div class="col-lg-2 col-sm-6 px-0">
							<input type="text" class="form-control input-date-single text-center" id="endDateDetail" name="endDateDetail" value="<?=date('t-m-Y')?>">
						</div>
						<div class="col-lg-8 col-sm-12 px-0">
							<button class="button button-info button-sm pull-right mt-3" id="btnAddInstallmentRecord" data-toggle="modal" data-target="#modal-addInstallmentRecord"><span><i class="fa fa-plus"></i>Installment Record</span></button>
							<a class="button button-info button-sm pull-right mt-3 mr-2 d-none" id="excelDataPerDriver" target="_blank" href="#"><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
						</div>
						<div class="col-12 tableFixHead mt-10 px-0" style="min-height:250px; max-height:650px">
							<table class="table" id="table-detailLoanPerDriver">
								<thead class="thead-light">
									<tr>
										<th width="120">Date Time</th>
										<th width="160">Loan Type</th>
										<th>Description</th>
										<th width="80">DB/CR</th>
										<th class="text-right" width="120">Amount</th>
									</tr>
								</thead>
								<tbody id="bodyDetailLoanPerDriver">
									<tr><td colspan="5" align="center">No Data Found</td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="detailPerDriverTab-activeLoan">
					<div class="row border-allround">
						<div class="col-4 px-0 border-right">
							<div class="top-report bg-white py-3">
								<div class="head">
									<h5>Car Loan</h5>
									<span class="view"><i class="fa fa-credit-card"></i></span>
								</div>
								<div class="content mb-0">
									<h4>Rp. <span id="totalLoanCar">0</span></h4>
								</div>
							</div>
						</div>
						<div class="col-4 px-0 border-right">
							<div class="top-report bg-white py-3">
								<div class="head">
									<h5>Personal Loan</h5>
									<span class="view"><i class="fa fa-credit-card-alt"></i></span>
								</div>
								<div class="content mb-0">
									<h4>Rp. <span id="totalLoanPersonal">0</span></h4>
								</div>
							</div>
						</div>
						<div class="col-4 px-0">
							<div class="top-report bg-white py-3">
								<div class="head">
									<h5>Prepaid Capital</h5>
									<span class="view"><i class="fa fa-money"></i></span>
								</div>
								<div class="content mb-0">
									<h4>Rp. <span id="totalPrepaidCapital">0</span></h4>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="detailPerDriverTab-activeLoan-containerDetailPerActiveLoan"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="installmentRequestTab">
		<div class="box mb-10">
			<div class="box-body">
				<div class="row mt-10">
					<div class="col-lg-4 col-sm-6 mb-10">
						<div class="form-group">
							<label for="optionInstallmentDriverType" class="control-label">Driver Type</label>
							<select id="optionInstallmentDriverType" name="optionInstallmentDriverType" class="form-control" option-all="All Driver Type"></select>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 mb-10">
						<div class="form-group">
							<label for="optionInstallmentDriver" class="control-label">Driver</label>
							<select id="optionInstallmentDriver" name="optionInstallmentDriver" class="form-control" option-all="All Driver"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-3 mb-10">
						<div class="form-group">
							<label for="startDateRequest" class="control-label">Date Request</label>
							<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateRequest" name="startDateRequest" value="<?=date('01-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-2 col-sm-3 mb-10">
						<div class="form-group">
							<label for="endDateRequest" class="control-label">.</label>
							<input type="text" class="form-control input-date-single text-center" id="endDateRequest" name="endDateRequest" value="<?=date('t-m-Y')?>">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label class="adomx-checkbox">
								<input type="checkbox" id="checkboxViewInstallmentRequestOnly" name="checkboxViewInstallmentRequestOnly" value="1"> <i class="icon"></i> <b>Show all loan and prepaid capital installment request only</b>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="box mb-10">
			<div class="box-body px-0">
				<div class="tableFixHead" style="max-height:450px">
					<table class="table" id="table-installmentRequest">
						<thead class="thead-light">
							<tr>
								<th width="120">Date Time</th>
								<th width="150">Driver</th>
								<th width="160">Loan Type</th>
								<th>Notes</th>
								<th class="text-right" width="100">Amount</th>
								<th width="120">Status</th>
								<th width="60"></th>
							</tr>
						</thead>
						<tbody id="bodyInstallmentRequest">
							<tr><td colspan="7" align="center">No Data Found</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-loanPrepaidCapitalRequest">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-loanPrepaidCapitalRequest">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-loanPrepaidCapitalRequest">Loan / Prepaid Capital Request</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row border-bottom" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-lg-4 col-sm-12 mb-10">
						<h6 class="mb-0">Driver</h6>
						<p id="loanPrepaidCapitalRequest-driverName"></p>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<h6 class="mb-0">Request Type</h6>
						<p id="loanPrepaidCapitalRequest-loanType"></p>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<h6 class="mb-0">Date Time Request</h6>
						<p id="loanPrepaidCapitalRequest-dateTimeRequest"></p>
					</div>
					<div class="col-lg-8 col-sm-12 pb-10">
						<h6 class="mb-0">Notes</h6>
						<p id="loanPrepaidCapitalRequest-notes"></p>
					</div>
					<div class="col-lg-4 col-sm-12 pb-10">
						<h6 class="mb-0">Amount</h6>
						<h5 id="loanPrepaidCapitalRequest-amount"></h5>
					</div>
				</div>
				<div class="row border-bottom">
					<div class="col-lg-6 col-sm-12 border-right pt-20 pb-0">
						<div class="row">
							<div class="col-lg-8 col-sm-8">
								<div class="form-group">
									<label for="loanPrepaidCapitalRequest-interestPerAnnumInteger" class="control-label">Interest Per Annum (%)</label>
									<input type="text" class="form-control mb-10 text-right" id="loanPrepaidCapitalRequest-interestPerAnnumInteger" name="loanPrepaidCapitalRequest-interestPerAnnumInteger" onkeyup="calculateDetailLoanRequest()" value="0" onkeypress="maskNumberInput(0, 99, 'loanPrepaidCapitalRequest-interestPerAnnumInteger');">
								</div>
							</div>
							<div class="col-lg-4 col-sm-4" style="padding-left: 3px;">
								<div class="form-group">
									<label for="loanPrepaidCapitalRequest-interestPerAnnumDecimal" class="control-label">&nbsp;</label>
									<input type="text" class="form-control mb-10 text-right decimalInput" id="loanPrepaidCapitalRequest-interestPerAnnumDecimal" name="loanPrepaidCapitalRequest-interestPerAnnumDecimal" onkeyup="calculateDetailLoanRequest()" value="0" onkeypress="maskNumberInput(0, 99, 'loanPrepaidCapitalRequest-interestPerAnnumDecimal');">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="loanPrepaidCapitalRequest-totalPeriodMonth" class="control-label">Total Loan Period (Month)</label>
									<div class="input-group">
										<span class="input-group-prepend">
											<button type="button" class="btn btn-outline-secondary btn-number" data-type="minus" data-field="loanPrepaidCapitalRequest-totalPeriodMonth">
												<span class="fa fa-minus"></span>
											</button>
										</span>
										<input type="text" name="loanPrepaidCapitalRequest-totalPeriodMonth" id="loanPrepaidCapitalRequest-totalPeriodMonth" class="form-control input-number text-right" value="12" onkeyup="calculateDetailLoanRequest()" min="1" max="120">
										<span class="input-group-append">
											<button type="button" class="btn btn-outline-secondary btn-number" data-type="plus" data-field="loanPrepaidCapitalRequest-totalPeriodMonth">
												<span class="fa fa-plus"></span>
											</button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12 pt-20 pb-0">
						<h6 class="mb-10">Loan Nominal Summary</h6>
						<div class="order-details-customer-info mb-10">
							<ul class="ml-10">
								<li> <span>Principal</span> <span id="loanPrepaidCapitalRequest-summary-nominalPrincipal">0</span> </li>
								<li> <span>Interest</span> <span id="loanPrepaidCapitalRequest-summary-nominalInterest">0</span> </li>
								<li> <span>Total</span> <span id="loanPrepaidCapitalRequest-summary-nominalTotal">0</span> </li>
								<li> <span><b>Monthly Installment</b></span> <span><b id="loanPrepaidCapitalRequest-summary-installmentPerMonth">0</b></span> </li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row pt-15">
					<div class="col-lg-12 mb-10">
						<p>Driver asks for funds to be transferred to : </p>
					</div>
					<div class="col-lg-2 col-sm-4 mb-10 text-center">
						<img src="<?=URL_BANK_LOGO?>default.png" class="my-auto" width="90" id="loanPrepaidCapitalRequest-imgBankLogo">
					</div>
					<div class="col-lg-10 col-sm-8 mb-10">
						<h6 class="mb-0" id="loanPrepaidCapitalRequest-bankName"></h6>
						<h5 class="mb-0" id="loanPrepaidCapitalRequest-accountNumber"></h5>
						<h6 class="mb-0" id="loanPrepaidCapitalRequest-accountHolderName"></h6>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idLoanDriverRequest" name="idLoanDriverRequest" value="">
				<button type="button" class="button button-warning mr-auto" id="btnRejectLoanPrepaidCapitalRequest">Reject</button>
				<button type="submit" class="button button-primary">Accept</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-loanPrepaidCapitalHistory">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="editor-loanPrepaidCapitalHistory">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-loanPrepaidCapitalHistory"><span id="spanLoanPrepaidCapitalHistoryType"></span> Transaction History</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row px-3 mb-10">
					<div class="col-sm-12">
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Name</span> <span id="loanPrepaidCapitalHistory-name"></span> </li>
								<li> <span>Address</span> <span id="loanPrepaidCapitalHistory-address"></span> </li>
								<li> <span>Contact</span> <span id="loanPrepaidCapitalHistory-contact"></span> </li>
								<li> <span class="h6 fw-600">Total Amount</span> <span class="h6 fw-600 text-success" id="loanPrepaidCapitalHistory-total"></span> </li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row tableFixHead px-3" style="height:400px">
					<table class="table" id="tableLoanPrepaidCapitalHistory">
						<thead class="thead-light">
							<tr>
								<th width="100" class="text-center">Date Time</th>
								<th width="150">Type</th>
								<th>Description</th>
								<th width="40">D/K</th>
								<th width="120" class="text-right">Amount</th>
								<th width="120" class="text-right">Saldo</th>
								<th width="40"></th>
							</tr>
						</thead>
						<tbody id="tbodyRatingPointHistory">
							<tr>
								<td colspan="7" id="noDataLoanPrepaidCapitalHistory" align="center"><img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="60px"><h5>No data</h5><p>This driver has no loan / prepaid capital history</p></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="loanPrepaidCapitalHistory-page" name="loanPrepaidCapitalHistory-page" value="1">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-loanPrepaidCapitalTransferReceipt">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content" id="viewer-loanPrepaidCapitalTransferReceipt">
			<div class="modal-body">
				<div class="row px-3 mb-10">
					<div class="col-sm-12" id="transferReceiptPreview"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-addLoanRecord">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="editor-addLoanRecord">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-addLoanRecord">Add new Loan / Prepaid Capital record</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row border-bottom">
					<div class="col-lg-6 col-sm-12 border-right">
						<div class="row mb-10">
							<div class="col-lg-6 col-md-12">
								<div class="form-group required">
									<label for="optionDriver" class="control-label">Driver</label>
									<select id="optionDriver" name="optionDriver" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-6 col-md-12">
								<div class="form-group required">
									<label for="optionLoanType" class="control-label">Loan Type</label>
									<select id="optionLoanType" name="optionLoanType" class="form-control"></select>
								</div>
							</div>
							<div class="col-12">
								<div class="form-group">
									<label for="driverNote" class="control-label">Driver Note</label>
									<input type="text" class="form-control" id="driverNote" name="driverNote" value="">
								</div>
							</div>
							<div class="col-12">
								<div class="form-group required">
									<label for="loanDescription" class="control-label">Description</label>
									<textarea class="form-control" placeholder="Description" id="loanDescription" name="loanDescription" style="height: 90px !important;"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="row mb-10">
							<div class="col-12 pb-20 mb-20 border-bottom">
								<h6 class="mb-15">Bank Account</h6>
								<div class="adomx-checkbox-radio-group" id="containerRadioBankAccount">
									<label class="adomx-radio-2"><input type="radio" name="radioBankAccount" checked="" value="0" class="radioBankAccountElem"> <i class="icon"></i> Create New</label>
								</div>
							</div>
							<div class="col-lg-6 col-md-12">
								<div class="form-group">
									<label for="optionBank" class="control-label">Bank</label>
									<select id="optionBank" name="optionBank" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-6 col-md-12">
								<div class="form-group">
									<label for="accountNumber" class="control-label">Account Number</label>
									<input type="text" class="form-control mb-10" id="accountNumber" name="accountNumber">
								</div>
							</div>
							<div class="col-12">
								<div class="form-group">
									<label for="accountHolderName" class="control-label">Account Holder Name</label>
									<input type="text" class="form-control" id="accountHolderName" name="accountHolderName">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-sm-12 border-right pt-20 pb-0">
						<div class="row">
							<div class="col-lg-6 col-sm-12">
								<div class="form-group required">
									<label for="dateRecord" class="control-label">Date Record</label>
									<input type="text" class="form-control input-date-single mb-10 text-center" id="dateRecord" name="dateRecord" value="<?=date('d-m-Y')?>">
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="form-group required">
									<label for="loanPrincipalNominal" class="control-label">Principal Nominal</label>
									<input type="text" class="form-control mb-10 text-right" id="loanPrincipalNominal" name="loanPrincipalNominal" onkeyup="calculateDetailLoan()" value="0" onkeypress="maskNumberInput(1, 999999999, 'loanPrincipalNominal')">
								</div>
							</div>
							<div class="col-lg-4 col-sm-8">
								<div class="form-group">
									<label for="interestPerAnnumInteger" class="control-label">Interest Per Annum (%)</label>
									<input type="text" class="form-control mb-10 text-right" id="interestPerAnnumInteger" name="interestPerAnnumInteger" onkeyup="calculateDetailLoan()" value="0" onkeypress="maskNumberInput(0, 99, 'interestPerAnnumInteger');">
								</div>
							</div>
							<div class="col-lg-2 col-sm-4" style="padding-left: 3px;">
								<div class="form-group">
									<label for="interestPerAnnumDecimal" class="control-label">&nbsp;</label>
									<input type="text" class="form-control mb-10 text-right decimalInput" id="interestPerAnnumDecimal" name="interestPerAnnumDecimal" onkeyup="calculateDetailLoan()" value="0" onkeypress="maskNumberInput(0, 99, 'interestPerAnnumDecimal');">
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="form-group">
									<label for="totalPeriodMonth" class="control-label">Total Loan Period (Month)</label>
									<div class="input-group">
										<span class="input-group-prepend">
											<button type="button" class="btn btn-outline-secondary btn-number" data-type="minus" data-field="totalPeriodMonth">
												<span class="fa fa-minus"></span>
											</button>
										</span>
										<input type="text" name="totalPeriodMonth" id="totalPeriodMonth" class="form-control input-number text-right" value="12" onkeyup="calculateDetailLoan()" min="1" max="120">
										<span class="input-group-append">
											<button type="button" class="btn btn-outline-secondary btn-number" data-type="plus" data-field="totalPeriodMonth">
												<span class="fa fa-plus"></span>
											</button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12 pt-20 pb-0">
						<h6 class="mb-10">Loan Nominal Summary</h6>
						<div class="order-details-customer-info mb-10">
							<ul class="ml-10">
								<li> <span>Principal</span> <span id="addLoanRecord-summary-nominalPrincipal">0</span> </li>
								<li> <span>Interest</span> <span id="addLoanRecord-summary-nominalInterest">0</span> </li>
								<li> <span>Total</span> <span id="addLoanRecord-summary-nominalTotal">0</span> </li>
								<li> <span><b>Monthly Installment</b></span> <span><b id="addLoanRecord-summary-installmentPerMonth">0</b></span> </li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-info" data-dismiss="modal">Cancel</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-addInstallmentRecord" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-addInstallmentRecord">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-addInstallmentRecord">Add new installment record</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="optionDriverInstallment" class="control-label">Driver</label>
									<select id="optionDriverInstallment" name="optionDriverInstallment" class="form-control"></select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="optionInstallmentLoanType" class="control-label">Loan Type</label>
									<select id="optionInstallmentLoanType" name="optionInstallmentLoanType" class="form-control"></select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="dateRecordInstallment" class="control-label">Date Record</label>
									<input type="text" class="form-control input-date-single mb-10 text-center" id="dateRecordInstallment" name="dateRecordInstallment" value="<?=date('d-m-Y')?>">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="loanInstallmentNominal" class="control-label">Nominal</label>
									<input type="text" class="form-control mb-10 text-right" id="loanInstallmentNominal" name="loanInstallmentNominal" value="0" onkeypress="maskNumberInput(1, 999999999, 'loanInstallmentNominal')">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="loanInstallmentDescription" class="control-label">Description</label>
									<input type="text" class="form-control" id="loanInstallmentDescription" name="loanInstallmentDescription" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12 text-center">
						<h6 class="mb-20">Transfer Receipt</h6>
						<small>Allowed file extension is : <b>.jpg or .png</b>.</small><br/>
						<img id="imageTransferReceiptInstallment" src="" style="max-height:200px; max-width:290px;"/><br/>
						<div id="uploaderTransferReceiptInstallment" class="mt-20">Upload Transfer Receipt</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="transferReceiptInstallmentFileName" name="transferReceiptInstallmentFileName" value="">
				<button type="button" class="button button-info" data-dismiss="modal">Cancel</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-loanInstallmentRequest">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-loanInstallmentRequest">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-loanInstallmentRequest">Installment Request</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-sm-12 mb-10" style="border-right: 1px solid #e0e0e0;">
						<h6 class="mb-0">Driver</h6>
						<p id="loanInstallmentRequest-driverName" class="mb-10"></p>
						<h6 class="mb-0">Loan Type</h6>
						<p id="loanInstallmentRequest-loanType" class="mb-10"></p>
						<h6 class="mb-0">Notes</h6>
						<p id="loanInstallmentRequest-notes" class="mb-10"></p>
						<h6 class="mb-0">Amount</h6>
						<b id="loanInstallmentRequest-amount" class="mb-10"></b>
					</div>
					<div class="col-lg-6 col-sm-12 mb-10">
						<p>Transfer Receipt : </p>
						<a href="#" target="_blank" id="loanInstallmentRequest-zoomTransferReceipt">
							<img src="<?=URL_TRANSFER_RECEIPT?>no-image.jpg" class="my-auto" style="max-width:150px;max-height:250px;" id="loanInstallmentRequest-transferReceipt">
						</a>
					</div>
					<div class="col-sm-12 mb-10 pt-10" style="border-top: 1px solid #e0e0e0;">
						<div id="badgeInstallmentRequestStatus"></div>
						<div class="order-details-customer-info">
							<ul class="ml-5">
								<li> <span>Request Time</span> <span id="loanInstallmentRequest-dateTimeRequest"></span> </li>
								<li> <span>Approval Time</span> <span id="loanInstallmentRequest-dateTimeApproval"></span> </li>
								<li> <span>User Approval</span> <span id="loanInstallmentRequest-userApproval"></span> </li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idLoanInstallmentRequest" name="idLoanInstallmentRequest" value="">
				<button type="button" class="button button-warning mr-auto" id="btnRejectLoanInstallmentRequest">Reject</button>
				<button type="submit" class="button button-primary" id="btnAcceptLoanInstallmentRequest">Accept</button>
			</div>
		</form>
	</div>
</div>
<script>
	var dateToday	=	"<?=date('d-m-Y')?>";
	var url 		=	"<?=BASE_URL_ASSETS?>js/page-module/FinanceDriver/loanPrepaidCapital.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
.table td, .table th {
	border : none !important;
}
.rowDriver{
	border: 1px solid #e0e0e0;
	min-height: 120px;
	max-height: 250px;
	overflow: hidden;
}
</style>