<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Reservation Income <span> / Detail & recap income and cost per reservation</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link tabReservationIncome active" data-toggle="tab" href="#detailIncomeTab"><i class="fa fa-list"></i> Detail Income</a></li>
			<li class="nav-item"><a class="nav-link tabReservationIncome" data-toggle="tab" href="#recapPerSource"><i class="fa fa-list-ul"></i> Recap Per Source</a></li>
			<li class="nav-item"><a class="nav-link tabReservationIncome" data-toggle="tab" href="#recapPerYear"><i class="fa fa-calendar"></i> Recap Per Year</a></li>
		</ul>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body tab-content">
		<div class="tab-pane fade show active" id="detailIncomeTab">
			<div class="row border-bottom pb-20 mb-20">
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-10">
						<label for="detailIncomeTab-optionReservationType" class="control-label">Reservation Type</label>
						<select id="detailIncomeTab-optionReservationType" name="detailIncomeTab-optionReservationType" class="form-control" option-all="All Reservation Type"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-10">
						<label for="detailIncomeTab-optionSource" class="control-label">Source</label>
						<select id="detailIncomeTab-optionSource" name="detailIncomeTab-optionSource" class="form-control" option-all="All Source"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-10">
						<label for="detailIncomeTab-startDate" class="control-label">Date Reservation</label>
						<input type="text" class="form-control input-date-single text-center" id="detailIncomeTab-startDate" name="detailIncomeTab-startDate" value="<?=date('01-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-10">
						<label for="detailIncomeTab-endDate" class="control-label">&nbsp;</label>
						<input type="text" class="form-control input-date-single text-center" id="detailIncomeTab-endDate" name="detailIncomeTab-endDate" value="<?=date('t-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-4 col-sm-12">
					<div class="form-group mb-10">
						<label for="detailIncomeTab-keywordSearch" class="control-label">Search by Booking Code/Title/Name/Address</label>
						<input type="text" class="form-control" id="detailIncomeTab-keywordSearch" name="detailIncomeTab-keywordSearch" placeholder="Type something and press ENTER to search">
					</div>
				</div>
				<div class="col-sm-12">
					<div class="adomx-checkbox-radio-group inline">
						<label class="adomx-checkbox">
							<input type="checkbox" id="detailIncomeTab-checkboxIncludeCollectPayment" name="detailIncomeTab-checkboxIncludeCollectPayment" value="1"> <i class="icon"></i> <b>Include Collect Payment</b>
						</label>
						<label class="adomx-checkbox">
							<input type="checkbox" id="detailIncomeTab-checkboxIncludeAdditionalCost" name="detailIncomeTab-checkboxIncludeAdditionalCost" value="1"> <i class="icon"></i> <b>Include Additional Cost</b>
						</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="row">
						<div class="col-lg-8 col-sm-12 mb-10">
							<span id="detailIncomeTab-tableDataCount"></span>
						</div>
						<div class="col-lg-4 col-sm-12 mb-10">
							<a class="button button-info button-sm pull-right d-none" id="detailIncomeTab-excelDetail" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
						</div>
					</div>
					<div class="row mt-5 responsive-table-container">
						<table class="table" id="detailIncomeTab-tableDetail">
							<thead class="thead-light">
								<tr>
									<th>Reservation Detail</th>
									<th width="160" class="text-right">Income Rsv</th>
									<th width="300">Income Finance</th>
									<th width="300">Product Details</th>
									<th width="140" class="text-center">Date</th>
									<th width="90" class="text-right">Cost</th>
									<th width="90" class="text-right">Margin</th>
								</tr>
							</thead>
							<tbody>
								<tr><th colspan="7" class="text-center">No data found</th></tr>
							</tbody>
						</table>
					</div>
					<div class="row mt-20">
						<div class="col-sm-12 mb-10">
							<ul class="pagination" id="detailIncomeTab-tablePagination"></ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="recapPerSource">
			<div class="row border-bottom pb-20 mb-20">
				<div class="col-lg-3 col-sm-12">
					<div class="form-group">
						<label for="recapPerSource-optionReportType" class="control-label">Report Type</label>
						<select id="recapPerSource-optionReportType" name="recapPerSource-optionReportType" class="form-control">
							<option value="1">Recap Per Source</option>
							<option value="2">Recap Per Month & Source</option>
						</select>
					</div>
				</div>
				<div class="col-lg-5 col-sm-12">
					<div class="form-group">
						<label for="recapPerSource-optionSource" class="control-label">Source</label>
						<select id="recapPerSource-optionSource" name="recapPerSource-optionSource" class="form-control" option-all="All Source"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-0">
						<label for="recapPerSource-startDate" class="control-label">Date Reservation</label>
						<input type="text" class="form-control input-date-single text-center" id="recapPerSource-startDate" name="recapPerSource-startDate" value="<?=date('01-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-0">
						<label for="recapPerSource-endDate" class="control-label">.</label>
						<input type="text" class="form-control input-date-single text-center" id="recapPerSource-endDate" name="recapPerSource-endDate" value="<?=date('t-m-Y')?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="row">
						<div class="col-lg-8 col-sm-12 mb-10"><span id="recapPerSource-tableDataCount"></span></div>
						<div class="col-lg-4 col-sm-12 mb-10">
							<a class="button button-info button-sm pull-right d-none" id="recapPerSource-excelRecap" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Recap</span></a>
						</div>
					</div>
					<div class="row mt-5 responsive-table-container">
						<table class="table" id="recapPerSource-tableRecap">
							<thead class="thead-light">
								<tr>
									<th width="300">Period</th>
									<th>Source</th>
									<th width="180" class="text-right">Total Reservation</th>
									<th width="180" class="text-right">Total Income</th>
								</tr>
							</thead>
							<tbody>
								<tr><td colspan="4" class="text-center">No data found</td></tr>
							</tbody>
						</table>
					</div>
					<div class="row mt-20">
						<div class="col-sm-12 mb-10">
							<ul class="pagination" id="recapPerSource-tablePagination"></ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="recapPerYear">
			<div class="row border-bottom pb-20 mb-20">
				<div class="col-lg-3 col-sm-4">
					<div class="form-group">
						<label for="recapPerYear-optionYear" class="control-label">Report Type</label>
						<select id="recapPerYear-optionYear" name="recapPerYear-optionYear" class="form-control"></select>
					</div>
				</div>
				<div class="col-lg-5 col-sm-8">
					<div class="form-group">
						<label for="recapPerYear-optionSource" class="control-label">Source</label>
						<select id="recapPerYear-optionSource" name="recapPerYear-optionSource" class="form-control" option-all="All Source"></select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 text-right">
					<a class="button button-info button-sm pull-right d-none" id="recapPerYear-excelRecap" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Recap</span></a>
				</div>
				<div class="col-12">
					<div class="row mt-5 responsive-table-container">
						<table class="table" id="recapPerYear-tableRecap">
							<thead class="thead-light">
								<tr>
									<th>Month Year</th>
									<th width="180" class="text-right">Total Active Reservation</th>
									<th width="180" class="text-right">Total Income (Reservation)</th>
									<th width="180" class="text-right">Total Income (Finance)</th>
									<th width="180" class="text-right">Total Cost</th>
									<th width="180" class="text-right">Total Margin</th>
								</tr>
							</thead>
							<tbody>
								<tr><td colspan="6" class="text-center">No data found</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/Finance/detailReservationIncome.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>