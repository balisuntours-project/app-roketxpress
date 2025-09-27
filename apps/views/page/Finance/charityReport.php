<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Finance <span> / Charity report detail</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-primary button-sm pull-right" id="btnAddDataCharityManual" data-action="insert" data-toggle="modal" data-target="#modal-manualCharity"><span><i class="fa fa-plus"></i>New Charity</span></button>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detailReportTab"><i class="fa fa-list"></i> Detail Report</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#transferReportTab"><i class="fa fa-download"></i> Transfer Report</a></li>
		</ul>
	</div>
</div>
<div class="box mt-10">
	<div class="box-body tab-content">
		<div class="tab-pane fade show active" id="detailReportTab">
			<div class="row pb-20 mb-20 border-bottom">
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-5">
						<label for="startDate" class="control-label">Record Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-5">
						<label for="endDate" class="control-label">.</label>
						<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-8 col-sm-12">
					<div class="form-group">
						<label for="searchKeyword" class="control-label">Search by Contributor Name / Description</label>
						<input type="text" class="form-control" id="searchKeyword" name="searchKeyword" value="" placeholder="Type something and press ENTER to search">
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label class="adomx-checkbox">
							<input type="checkbox" id="checkboxViewUnprocessedOnly" name="checkboxViewUnprocessedOnly" value="1"> <i class="icon"></i> <b>Display only the data that has not been processed</b>
						</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-8 col-sm-12 mb-10">
					<span id="tableDataCountCharityReport"></span>
				</div>
				<div class="col-lg-4 col-sm-12 mb-10">
					<button class="button button-primary button-sm pull-right" id="btnDisburseCharity"><span><i class="fa fa-database"></i>Disburse Charity</span></button>
				</div>
			</div>
			<div class="row mt-5 responsive-table-container">
				<table class="table" id="table-charityReport">
					<thead class="thead-light">
						<tr>
							<th width="160">Record Date Time</th>
							<th width="120">Contributor Type</th>
							<th width="180">Name</th>
							<th>Description</th>
							<th width="120" class="text-right">Nominal</th>
							<th width="200">Report Details</th>
							<th width="120"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th colspan="7" class="text-center">No data found</th>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="row mt-20">
				<div class="col-sm-12 mb-10">
					<ul class="pagination" id="tablePaginationCharityReport"></ul>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="transferReportTab">
			<div class="row">
				<div class="col-sm-12 mb-10">
					<span id="tableDataCountCharityProcessTransfer"></span>
				</div>
			</div>
			<div class="row mt-5 responsive-table-container">
				<table class="table" id="table-charityProcessTransfer">
					<thead class="thead-light">
						<tr>
							<th width="200">Date Period</th>
							<th width="160">Process Date Time</th>
							<th width="160">Process User</th>
							<th>Transfer Details</th>
							<th width="140" class="text-right">Total Charity</th>
							<th width="140" class="text-right">Total Nominal</th>
							<th width="130">Status Transfer</th>
							<th width="150">Download</th>
							<th width="120"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th colspan="9" class="text-center">No data found</th>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="row mt-20">
				<div class="col-sm-12 mb-10">
					<ul class="pagination" id="tablePaginationCharityProcessTransfer"></ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-manualCharity">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-manualCharity">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-manualCharity">Add/Edit Manual Charity</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-3 col-sm-6">
						<div class="form-group required">
							<label for="charityDate" class="control-label">Charity Date</label>
							<input type="text" class="form-control input-date-single text-center" id="charityDate" name="charityDate" value="<?=date('d-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">
						<div class="form-group required">
							<label for="optionContributorType" class="control-label">Contributor Type</label>
							<select class="form-control" id="optionContributorType" name="optionContributorType">
								<option value='2'>Employee</option>
								<option value='3'>Other</option>
							</select>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group required">
							<label for="contributorName" class="control-label">Contributor Name</label>
							<input type="text" class="form-control" id="contributorName" name="contributorName">
						</div>
					</div>
					<div class="col-lg-3 col-sm-4">
						<div class="form-group required">
							<label for="charityNominal" class="control-label">Nominal</label>
							<input type="text" class="form-control mb-10 text-right" id="charityNominal" name="charityNominal" onkeypress="maskNumberInput(1, 99999999, 'charityNominal')" value="<?=number_format($minCharityNominal, 0, '.', ',')?>">
						</div>
					</div>
					<div class="col-lg-9 col-sm-6">
						<div class="form-group required">
							<label for="charityDescription" class="control-label">Notes</label>
							<input type="text" class="form-control mb-10" id="charityDescription" name="charityDescription">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idCharity" name="idCharity" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-transferCharity">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-transferCharity">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-transferCharity">Transfer Process Charity</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-sm-12">
						<div class="form-group required">
							<label for="transferCharity-optionBank" class="control-label">Bank</label>
							<select class="form-control" id="transferCharity-optionBank" name="transferCharity-optionBank"></select>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group required">
							<label for="transferCharity-accountNumber" class="control-label">Account Number</label>
							<input type="text" class="form-control nocomma padzeroleft" id="transferCharity-accountNumber" name="transferCharity-accountNumber" onkeypress="maskNumberInput(0, 999999999999999999, 'transferCharity-accountNumber')" value="0">
						</div>
					</div>
					<div class="col-sm-12 required">
						<div class="form-group required">
							<label for="transferCharity-accountHolderName" class="control-label">Account Holder Name</label>
							<input type="text" class="form-control" id="transferCharity-accountHolderName" name="transferCharity-accountHolderName">
						</div>
					</div>
					<div class="col-sm-12 required">
						<div class="form-group required">
							<label for="transferCharity-emailNotification" class="control-label">Email Transfer Notification</label>
							<input type="text" class="form-control" id="transferCharity-emailNotification" name="transferCharity-emailNotification">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="transferCharity-charityCode" name="transferCharity-charityCode" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var minCharityNominal	=	"<?=$minCharityNominal?>",
		url 				=	"<?=BASE_URL_ASSETS?>js/page-module/Finance/charityReport.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>