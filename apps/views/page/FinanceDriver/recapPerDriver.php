<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Recap Per Driver <span> / Recap report fee, collect payment, loan and cost per driver</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseDetails"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
		</div>
	</div>
</div>
<div class="alert alert-warning" role="alert">
	<i class="zmdi zmdi-info"></i> <span>This menu only displays drivers that are already using the new financial scheme</span>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
	<div class="box mb-10">
		<div class="box-body">
			<ul class="nav nav-tabs" id="tabsPanel">
				<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#allDriverReportTab"><i class="fa fa-file-text"></i> All Driver Report</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#recapFeePerPeriodTab"><i class="fa fa-calendar"></i> Recap Fee Per Period</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#recapPerDriverTab"><i class="fa fa-file"></i> Recap Per Driver</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#withdrawalTab"><i class="fa fa-credit-card-alt"></i> Withdrawal</a></li>
			</ul>
		</div>
	</div>
	<div class="box mb-10">
		<div class="box-body">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="allDriverReportTab">
					<div class="row">
						<div class="col-lg-6 col-sm-12">
							<div class="form-group">
								<label for="optionDriverType" class="control-label">Driver Type</label>
								<select id="optionDriverType" name="optionDriverType" class="form-control" option-all="All Driver Type"></select>
							</div>
						</div>
						<div class="col-lg-6 col-sm-12">
							<div class="form-group">
								<label for="optionDriver" class="control-label">Driver</label>
								<select id="optionDriver" name="optionDriver" class="form-control" option-all="All Driver"></select>
							</div>
						</div>
						<div class="col-12 mt-10">
							<div class="row">
								<div class="col-lg-8 col-sm-12 mb-10">
									<span id="tableDataCountRecapPerDriver"></span>
								</div>
								<div class="col-lg-4 col-sm-12 mb-10">
									<a class="button button-primary button-sm pull-right" id="excelAllDriverRecap" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Recap Per Driver</span></a>
								</div>
							</div>
							<div class="row mt-5 responsive-table-container">
								<table class="table" id="table-recapPerDriver">
									<thead class="thead-light">
										<tr>
											<th width="120">Driver Type</th>
											<th >Driver Name</th>
											<th width="120" class="text-right">Schedule</th>
											<th width="120" class="text-right">Fee</th>
											<th width="120" class="text-right">Additional Cost</th>
											<th width="120" class="text-right">Reimbursement</th>
											<th width="120" class="text-right">Review Bonus</th>
											<th width="120" class="text-right">Collect Payment</th>
											<th width="120" class="text-right">Prepaid Capital</th>
											<th width="120" class="text-right">Balance</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<th colspan="10" class="text-center">No data found</th>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="row mt-20">
								<div class="col-sm-12 mb-10">
									<ul class="pagination" id="tablePaginationRecapPerDriver"></ul>
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
						<div class="col-12 mt-10">
							<div class="row">
								<div class="col-lg-8 col-sm-12 mb-10">
									<span id="tableDataCountFeePerPeriod"></span>
								</div>
								<div class="col-lg-4 col-sm-12 mb-10">
									<a class="button button-primary button-sm pull-right" id="excelFeePerPeriod" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Fee Per Period</span></a>
								</div>
							</div>
							<div class="row mt-5 responsive-table-container">
								<table class="table" id="table-feePerPeriod">
									<thead class="thead-light">
										<tr>
											<th width="120">Driver Type</th>
											<th >Driver Name</th>
											<th width="120" class="text-right">Total Schedule</th>
											<th width="120" class="text-right">Total Fee</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<th colspan="4" class="text-center">No data found</th>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="row mt-20">
								<div class="col-sm-12 mb-10">
									<ul class="pagination" id="tablePaginationFeePerPeriod"></ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="recapPerDriverTab">
					<div class="row">
						<div class="col-lg-4 col-sm-6">
							<select id="optionDriverTypeRecap" name="optionDriverTypeRecap" class="form-control"></select>
						</div>
						<div class="col-lg-8 col-sm-6">
							<select id="optionDriverRecap" name="optionDriverRecap" class="form-control"></select>
						</div>
						<div class="col-sm-12">
							<div class="rowDriver rounded-lg row px-3 py-3 mx-0 mb-1 mt-10 bg-white">
								<div class="col-lg-2 col-sm-4 text-center">
									<div class="author-profile">
										<div class="image" style="width: 80px;height: 80px;">
											<h2 id="driverDetailInitial">-</h2>
										</div>
									</div>
								</div>
								<div class="col-lg-10 col-sm-8">
									<div class="row px-0 py-0">
										<div class="col-lg-8 col-sm-12">
											<p class="mb-0"><b id="driverDetailName">-</b></p>
											<p class="mb-0" id="driverDetailAddress">-</p>
											<p class="mb-0" id="driverDetailPhoneEmail">-</p><br>
											<span class="badge badge-pill mr-2 badge-primary"><p class="m-0 p-1 text-white">Driver Type : <span id="driverDetailDriverType">-</span></p></span>
											<span class="badge badge-pill mr-2 badge-info mt-5"><p class="m-0 p-1 text-white">Car Type : <span id="driverDetailCarTypeCapacity">-</span></p></span>
										</div>
										<div class="col-lg-4 col-sm-12">
											<img src="<?=URL_BANK_LOGO?>default.png" style="max-height:30px; max-width:90px" class="mb-10" id="driverBankLogo"><br/>
											<ul class="list-icon"><li class="pl-0"><i class="fa fa-university mr-1"></i><span id="driverBankName">-</span></li></ul>
											<ul class="list-icon"><li class="pl-0"><i class="fa fa-credit-card mr-1"></i><span id="driverBankAccountNumber">-</span></li></ul>
											<ul class="list-icon"><li class="pl-0"><i class="fa fa-user-circle mr-1"></i><span id="driverBankAccountHolder">-</span></li></ul>
											<button class="button button-warning button-xs mt-5 d-none" id="btnManualWithdraw">
												<span><i class="fa fa-plus"></i>Manual Withdraw</span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="rowDriver rounded-lg row px-2 py-2 mx-0 mb-1 mt-10 bg-white">
								<div class="col-xlg-4 col-md-6 col-12 px-0 border-right">
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
								<div class="col-xlg-4 col-md-6 col-12 px-0 border-right">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Additional Cost</h4>
											<span class="view"><i class="fa fa-cc-amex"></i></span>
										</div>
										<div class="content">
											<h2 id="totalNominalAdditionalCost">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Total Schedule With Cost : <span id="totalScheduleWithAdditioalCost">0</span></p>
										</div>
									</div>
								</div>
								<div class="col-xlg-4 col-md-12 col-12 px-0 border-right">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Reimbursement</h4>
											<span class="view"><i class="fa fa-share-square"></i></span>
										</div>
										<div class="content">
											<h2 id="totalNominalReimbursement">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Total Data Reimbursement : <span id="totalDataReimbursement">0</span></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="rowDriver rounded-lg row px-2 py-2 mx-0 mb-1 mt-10 bg-white">
								<div class="col-xlg-4 col-md-6 col-12 px-0 border-right">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Review Bonus Punishment</h4>
											<span class="view"><i class="fa fa-pencil-square-o"></i></span>
										</div>
										<div class="content">
											<h2 id="totalNominalReviewBonusPunishment">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Total Bonus Period : <span id="totalReviewBonusPeriod">0</span></p>
										</div>
									</div>
								</div>
								<div class="col-xlg-4 col-md-6 col-12 px-0 border-right">
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
								<div class="col-xlg-4 col-md-12 col-12 px-0">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Prepaid Capital</h4>
											<span class="view"><i class="fa fa-credit-card"></i></span>
										</div>
										<div class="content">
											<h2 id="totalNominalPrepaidCapital">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Last Transaction : <span id="lastTransactionPrepaidCapital">-</span></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="rowDriver rounded-lg row px-2 py-2 mx-0 mb-1 mt-10 bg-white">
								<div class="col-6 px-0 border-right">
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
								<div class="col-6 px-0">
									<div class="top-report bg-white">
										<div class="head">
											<h4>Loan Balance</h4>
											<span class="view"><i class="fa fa-cc-mastercard"></i></span>
										</div>
										<div class="content">
											<h2 id="totalNominalLoan">0</h2>
										</div>
										<div class="footer">
											<div class="progess">
												<div class="progess-bar" style="width: 100%;"></div>
											</div>
											<p>Car : <span id="totalNominalLoanCar">0</span> | Personal : <span id="totalNominalLoanPersonal">0</span></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 mt-20">
							<ul class="nav nav-tabs" id="tabsPanelDetail">
								<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#listFeeTab"><i class="fa fa-money"></i> List Fee</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#listAdditionalCostTab"><i class="fa fa-cc-amex"></i> List Additional Cost</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#listReimbursementTab"><i class="fa fa-share-square"></i> List Reimbursement</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#listReviewBonusPunishmentTab"><i class="fa fa-pencil-square-o"></i> List Review Bonus Punishment</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#listCollectPaymentTab"><i class="fa fa-list-alt"></i> List Collect Payment</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#historyPrepaidCapitalTab"><i class="fa fa-credit-card"></i> Prepaid Capital History</a></li>
								<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#historyLoanTab"><i class="fa fa-cc-mastercard"></i> Loan History</a></li>
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
								<div class="tab-pane fade" id="listAdditionalCostTab">
									<div class="tableFixHead">
										<table class="table" id="table-listAdditionalCost">
											<thead class="thead-light">
												<tr>
													<th width="120">Date Time</th>
													<th>Reservation Details</th>
													<th width="250">Customer Name</th>
													<th>Reservation Title</th>
													<th>Cost Type</th>
													<th>Description</th>
													<th class="text-right" width="120">Amount</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="listReimbursementTab">
									<div class="tableFixHead">
										<table class="table" id="table-listReimbursement">
											<thead class="thead-light">
												<tr>
													<th width="120">Date Receipt</th>
													<th width="120">Request By</th>
													<th width="400">Description</th>
													<th>Notes</th>
													<th class="text-right" width="120">Amount</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="listReviewBonusPunishmentTab">
									<div class="tableFixHead">
										<table class="table" id="table-listReviewBonusPunishment">
											<thead class="thead-light">
												<tr>
													<th>Period</th>
													<th width="120">Period Start</th>
													<th width="120">Period End</th>
													<th class="text-right" width="130">Target Point</th>
													<th class="text-right" width="130">Review Point</th>
													<th class="text-right" width="130">Rate</th>
													<th class="text-right" width="130">Bonus</th>
													<th class="text-right" width="130">Punishment</th>
													<th class="text-right" width="130">Result</th>
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
								<div class="tab-pane fade" id="historyPrepaidCapitalTab">
									<div class="tableFixHead">
										<table class="table" id="table-historyPrepaidCapital">
											<thead class="thead-light">
												<tr>
													<th class="text-center" width="100">Date Time</th>
													<th>Description</th>
													<th width="40">D/K</th>
													<th class="text-right" width="100">Amount</th>
													<th class="text-right" width="100">Saldo</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="historyLoanTab">
									<div class="tableFixHead">
										<table class="table" id="table-historyLoan">
											<thead class="thead-light">
												<tr>
													<th class="text-center" width="100">Date Time</th>
													<th width="100">Type</th>
													<th>Description</th>
													<th width="40">D/K</th>
													<th class="text-right" width="100">Amount</th>
													<th class="text-right" width="100">Saldo</th>
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
								<label for="optionDriverWithdrawal" class="control-label">Driver</label>
								<select id="optionDriverWithdrawal" name="optionDriverWithdrawal" class="form-control" option-all="All Driver"></select>
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
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="box d-none">
		<div class="box-body">
			<div class="row" style="border-bottom: 1px solid #e0e0e0;">
				<div class="col-lg-8 col-sm-6" style="border-bottom: 1px solid #e0e0e0;"><h5 id="driverNameStr">-</h5></div>
				<div class="col-lg-4 col-sm-6 text-right" style="border-bottom: 1px solid #e0e0e0;">Date Time Request : <b id="requestDateTimeStr">-</b></div>
				<div class="col-lg-3 col-sm-12 mt-20">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Status</h6>
							<p id="badgeStatusWithdrawal">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Last Date Period</h6>
							<p id="lastDatePeriodStr">-</p>
						</div>
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Message</h6>
							<p id="messageStr">-</p>
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
							<div class="order-details-customer-info pb-1" style="border-bottom: 1px solid #dee2e6;">
								<ul>
									<li> <span>Fee</span> <span><b id="totalFeeStr"></b></span> </li>
									<li> <span>Additional Cost</span> <span><b id="totalAdditioalCostStr"></b></span> </li>
									<li> <span>Reimbursement</span> <span><b id="totalReimbursementStr"></b></span> </li>
									<li> <span>Review Bonus</span> <span><b id="totalReviewBonusStr"></b></span> </li>
									<li> <span>Collect Payment</span> <span><b id="totalCollectPaymentStr"></b></span> </li>
									<li> <span>Additional Income</span> <span><b id="totalAdditioalIncomeStr"></b></span> </li>
									<li> <span>Prepaid Capital</span> <span><b id="totalPrepaidCapitalStr"></b></span> </li>
									<li> <span>Loan - Car</span> <span><b id="totalCarInstallmentStr"></b></span> </li>
									<li> <span>Loan - Personal</span> <span><b id="totalPersonalInstallmentStr"></b></span> </li>
									<li> <span>Charity</span> <span><b id="totalCharityStr"></b></span> </li>
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
						<div class="col-sm-5">
							<h6 class="mb-0">Approval User</h6>
							<p id="approvalUserStr">-</p>
						</div>
						<div class="col-sm-5">
							<h6 class="mb-0">Date Time Approval</h6>
							<p id="dateTimeApprovalStr">-</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-sm-6 pt-10 mb-10 text-right" style="border-top: 1px solid #e0e0e0;">
					<button class="button button-warning button-sm" id="btnRejectWithdrawal"><span><i class="fa fa-times"></i>Reject</span></button>
					<button class="button button-primary button-sm" id="btnApproveWithdrawal"><span><i class="fa fa-check"></i>Approve</span></button>
					<button class="button button-danger button-sm pull-right btn-block d-none" id="btnCancelWithdrawal"><span><i class="fa fa-times"></i>Cancel Withdrawal</span></button>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 mt-20"><h5>List Of Withdrawal Details</h5></div>
				<div class="col-sm-12 mt-10 tableFixHead" style="max-height: 400px">
					<table class="table" id="table-dataListWithdrawalDetail">
						<thead class="thead-light">
							<tr>
								<th width="220">Type</th>
								<th width="100">Date</th>
								<th width="130">Booking Code</th>
								<th>Description</th>
								<th width="120" class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody><td colspan="5" class="text-center">No data found</td></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorManualWithdraw" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="container-editorManualWithdraw">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorManualWithdraw">Manual Withdraw</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 pb-10 border-bottom">
						<p class="mb-0"><b id="editorManualWithdraw-driverName">-</b></p>
						<p class="mb-0" id="editorManualWithdraw-driverAddress">-</p>
						<p class="mb-0" id="editorManualWithdraw-driverPhoneEmail">-</p>
						<span class="badge badge-pill mr-2 badge-primary"><p class="m-0 p-1 text-white">Driver Type : <span id="editorManualWithdraw-driverType">-</span></p></span>
						<span class="badge badge-pill mr-2 badge-info mt-5"><p class="m-0 p-1 text-white">Car Type : <span id="editorManualWithdraw-carTypeCapacity">-</span></p></span>
					</div>
					<div class="col-lg-6 col-sm-12 pt-10 pb-10 border-bottom border-right">
						<img src="<?=URL_BANK_LOGO?>default.png" style="max-height:30px; max-width:90px" class="mb-10" id="editorManualWithdraw-bankLogo"><br>
						<ul class="list-icon"><li class="pl-0"><i class="fa fa-university mr-1"></i><span id="editorManualWithdraw-bankName">-</span></li></ul>
						<ul class="list-icon"><li class="pl-0"><i class="fa fa-credit-card mr-1"></i><span id="editorManualWithdraw-bankAccountNumber">-</span></li></ul>
						<ul class="list-icon"><li class="pl-0"><i class="fa fa-user-circle mr-1"></i><span id="editorManualWithdraw-bankAccountHolder">-</span></li></ul>
					</div>
					<div class="col-lg-6 col-sm-12 pt-10 pb-10 border-bottom">
						<h6 class="mb-0">Saldo Loan - Car</h6>
						<p id="editorManualWithdraw-saldoLoanCar">-</p>
						<h6 class="mb-0">Saldo Loan - Personal</h6>
						<p id="editorManualWithdraw-saldoLoanPersonal">-</p>
					</div>
					<div class="col-lg-7 col-sm-6 mb-5 pt-10">Total Fee</div>
					<div class="col-lg-5 col-sm-6 mb-5 pt-10"><b id="editorManualWithdraw-totalFee">-</b></div>
					<div class="col-lg-7 col-sm-6 mb-5">Additional Cost</div>
					<div class="col-lg-5 col-sm-6 mb-5"><b id="editorManualWithdraw-additionalCost">-</b></div>
					<div class="col-lg-7 col-sm-6 mb-5">Reimbursement</div>
					<div class="col-lg-5 col-sm-6 mb-5"><b id="editorManualWithdraw-reimbursement">-</b></div>
					<div class="col-lg-7 col-sm-6 mb-5">Review Bonus/Punishment <b class="pull-right">(+/-)</b></div>
					<div class="col-lg-5 col-sm-6 mb-5"><b id="editorManualWithdraw-reviewBonusPunishment">-</b></div>
					<div class="col-lg-7 col-sm-6 mb-5">Collect Payment <b class="pull-right">(-)</b></div>
					<div class="col-lg-5 col-sm-6 mb-5"><b id="editorManualWithdraw-collectPayment">-</b></div>
					<div class="col-lg-7 col-sm-6 mb-5">Prepaid Capital <b class="pull-right">(-)</b></div>
					<div class="col-lg-5 col-sm-6 mb-5"><b id="editorManualWithdraw-prepaidCapital">-</b></div>
					<div class="col-lg-7 col-sm-6 mb-10">Additional Income <b class="pull-right">(-)</b></div>
					<div class="col-lg-5 col-sm-6 mb-10">
						<input type="text" class="form-control form-control-sm pt-0 pb-0 maskNumber" id="editorManualWithdraw-additionalIncome" value="0" onkeyup="calculateManualWithdrawGrandTotal()" onkeypress="maskNumberInput(0, 999999999, 'editorManualWithdraw-additionalIncome');" style="width: 120px;">
					</div>
					<div class="col-lg-7 col-sm-6 mb-5">Loan - Car <b class="pull-right">(-)</b></div>
					<div class="col-lg-5 col-sm-6 mb-5">
						<input type="text" class="form-control form-control-sm pt-0 pb-0 maskNumber" id="editorManualWithdraw-loanCar" value="0" onkeyup="calculateManualWithdrawGrandTotal()" onkeypress="maskNumberInput(0, 999999999, 'editorManualWithdraw-loanCar');" style="width: 120px;">
					</div>
					<div class="col-lg-7 col-sm-6 mb-10">Loan - Personal <b class="pull-right">(-)</b></div>
					<div class="col-lg-5 col-sm-6 mb-10">
						<input type="text" class="form-control form-control-sm pt-0 pb-0 maskNumber" id="editorManualWithdraw-loanPersonal" value="0" onkeyup="calculateManualWithdrawGrandTotal()" onkeypress="maskNumberInput(0, 999999999, 'editorManualWithdraw-loanPersonal');" style="width: 120px;">
					</div>
					<div class="col-lg-7 col-sm-6 mb-10">Charity <b class="pull-right">(-)</b></div>
					<div class="col-lg-5 col-sm-6 mb-10">
						<input type="text" class="form-control form-control-sm pt-0 pb-0 maskNumber" id="editorManualWithdraw-charity" value="0" onkeyup="calculateManualWithdrawGrandTotal()" onkeypress="maskNumberInput(0, 999999999, 'editorManualWithdraw-charity');" style="width: 120px;">
					</div>
					<div class="col-lg-7 col-sm-6 pt-10 pb-10 border-top border-bottom"><b>Grand Total</b></div>
					<div class="col-lg-5 col-sm-6 pt-10 pb-10 border-top border-bottom"><b id="editorManualWithdraw-grandTotal">-</b></div>					
					<div class="col-sm-12 pt-10 mb-5"><p>Withdrawal Notes (Required)</p></div>
					<div class="col-sm-12">
						<textarea class="form-control" placeholder="Withdrawal Notes" id="editorManualWithdraw-notes" name="editorManualWithdraw-notes"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorManualWithdraw-idDriver" name="editorManualWithdraw-idDriver" value="">
				<button class="button button-primary">Save Manual Withdraw</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<input type="hidden" id="urlImageBankLogo" name="urlImageBankLogo" value="<?=URL_BANK_LOGO?>">
<script>
	var levelUser	=	'<?=$levelUser?>',
		url 		=	"<?=BASE_URL_ASSETS?>js/page-module/FinanceDriver/recapPerDriver.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
.rowDriver{
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

.author-profile .image h2 {
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