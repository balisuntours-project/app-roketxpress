<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Additional Income<span>/ List of driver additional income (SS)</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<button class="button button-primary button-sm pull-right" type="button" id="btnEditorAdditionalIncome" data-idAdditionalIncome="0" data-toggle="modal" data-target="#modal-additionalIncome">
			<span><i class="fa fa-plus"></i>Additional Income</span>
		</button>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#recapPerDriverTab"><i class="fa fa-list"></i> Reccap Per Driver</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#detailReportTab"><i class="fa fa-list"></i> Detail Report</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#settingPointRateTab"><i class="fa fa-cog"></i> Setting Point Rate</a></li>
		</ul>
	</div>
</div>
<div class="box mt-10">
	<div class="box-body tab-content">
		<div class="tab-pane fade show active" id="recapPerDriverTab">
			<div class="row">
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionMonth" class="control-label">Month</label>
						<select class="form-control" id="optionMonth" name="optionMonth"></select>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group">
						<label for="optionYear" class="control-label">Year</label>
						<select class="form-control" id="optionYear" name="optionYear"></select>
					</div>
				</div>
				<div class="col-lg-8 col-sm-12">
					<div class="form-group">
						<label for="searchKeywordRecap" class="control-label">Search by Driver Name</label>
						<input type="text" class="form-control" id="searchKeywordRecap" name="searchKeywordRecap" value="" placeholder="Type something and press ENTER to search">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 mb-10">
					<span id="tableDataCountAdditionalIncomeRecap"></span>
				</div>
			</div>
			<div class="row responsive-table-container">
				<table class="table" id="table-additionalIncomeRecap">
					<thead class="thead-light">
						<tr>
							<th width="200">Driver Name</th>
							<th>Exception Reason</th>
							<th width="200">Last Paid</th>
							<th width="140" class="text-right">Number of Payment</th>
							<th width="140" class="text-right">Total Nominal</th>
							<th width="100" class="text-right">Review Point</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th colspan="6" class="text-center">No data found</th>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="row mt-20">
				<div class="col-sm-12 mb-10">
					<ul class="pagination" id="tablePaginationAdditionalIncomeRecap"></ul>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="detailReportTab">
			<div class="row mb-20 border-bottom">
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-5">
						<label for="startDate" class="control-label">Date Period</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="form-group mb-5">
						<label for="endDate" class="control-label">&nbsp;</label>
						<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
					</div>
				</div>
				<div class="col-lg-8 col-sm-12">
					<div class="form-group">
						<label for="searchKeyword" class="control-label">Search by Driver Name / Description</label>
						<input type="text" class="form-control" id="searchKeyword" name="searchKeyword" value="" placeholder="Type something and press ENTER to search">
					</div>
				</div>
				<div class="col-sm-12 mb-10">
					<div class="form-group">
						<label class="adomx-checkbox">
							<input type="checkbox" id="checkboxViewRequestOnly" name="checkboxViewRequestOnly" value="1"> <i class="icon"></i> <b>Show additional income requests that need approval only</b>
						</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-8 col-sm-12 mb-10">
					<span id="tableDataCountAdditionalIncome"></span>
				</div>
				<div class="col-lg-4 col-sm-12 mb-10">
					<a class="button button-info button-sm pull-right d-none" id="excelDataAdditionalIncome" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
				</div>
			</div>
			<div class="row responsive-table-container">
				<table class="table" id="table-additionalIncome">
					<thead class="thead-light">
						<tr>
							<th width="120">Date</th>
							<th width="180">Driver Name</th>
							<th>Description</th>
							<th width="120" class="text-right">Income Nominal</th>
							<th width="200">Receipt</th>
							<th width="200">Input Detail</th>
							<th width="200">Approval Detail</th>
							<th width="120">Approval Status</th>
							<th width="100"></th>
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
					<ul class="pagination" id="tablePaginationAdditionalIncome"></ul>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="settingPointRateTab">
			<div class="row">
				<div class="col-sm-12">
					<button type="button" class="button button-primary button-sm pull-right" data-toggle="modal" data-target="#modal-settingPointRate" data-idAdditionalIncomeRate="0"><span><i class="fa fa-plus"></i>New Rate</span></button>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<table class="table" id="table-additionalIncomesettingPointRate">
						<thead class="thead-light">
							<tr>
								<th class="text-right" width="130">Min. Nominal</th>
								<th class="text-right" width="130">Max. Nominal</th>
								<th class="text-right" width="120">Point</th>
								<th></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-confirmApproveAdditionalIncome" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-confirm-title">Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body" id="modal-confirm-body">
				<div class="row">
					<div class="col-sm-12 mb-20 text-center">
						<img id="confirmApproveAdditionalIncome-imageReceipt" style="max-width: 300px; max-height:300px" src=""/>
					</div>
					<div class="col-sm-12 mb-5" style="border-bottom: 1px solid #e0e0e0;">
						<div class="order-details-customer-info mb-10">
							<ul class="ml-5">
								<li> <span><b>Driver Name</b></span> <span id="confirmApproveAdditionalIncome-driverName">-</span> </li>
								<li> <span><b>Date Receipt</b></span> <span id="confirmApproveAdditionalIncome-dateReceipt">-</span> </li>
								<li> <span><b>Nominal</b></span> <span id="confirmApproveAdditionalIncome-nominal">-</span> </li>
								<li> <span><b>Description</b></span> <span id="confirmApproveAdditionalIncome-description">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-sm-12 mt-10 mb-15">
						<p>This additional income will be <b id="confirmApproveAdditionalIncome-txtApprovalStatus"></b><br/>Are you sure?</p>
					</div>
					<div class="col-sm-12">
						<div class="adomx-checkbox-radio-group">
							<label class="adomx-checkbox"><input type="checkbox" id="confirmApproveAdditionalIncome-disableConfirm"> <i class="icon"></i> Don't show confirmation again</label>
						</div>
					</div>
				</div>
			</div>
           <div class="modal-footer">
				<input type="hidden" value="" name="confirmApproveAdditionalIncome-idDriver" id="confirmApproveAdditionalIncome-idDriver"/>
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-primary" id="confirmApproveAdditionalIncome-btnSubmit" data-idAdditionalIncome="" data-status="">Confirm</button>
           </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-additionalIncome">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-additionalIncome">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-additionalIncome">Add/Edit Additional Income</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 text-center border-bottom pb-20 mb-20">
						<small>Allowed file extension is : <b>.jpg or .png</b>.</small><br/>
						<img id="additionalIncome-imageTransferReceipt" src=""/><br/>
						<div id="uploaderTransferReceipt" class="mt-20">Upload Transfer Receipt</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="additionalIncome-optionDriver" class="control-label">Driver Name</label>
							<select class="form-control" id="additionalIncome-optionDriver" name="additionalIncome-optionDriver"></select>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group required">
							<label for="additionalIncome-date" class="control-label">Date</label>
							<input type="text" class="form-control input-date-single text-center" id="additionalIncome-date" name="additionalIncome-date" value="<?=date('d-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="form-group required">
							<label for="additionalIncome-nominal" class="control-label">Nominal</label>
							<input type="text" class="form-control mb-10 text-right" id="additionalIncome-nominal" name="additionalIncome-nominal" onkeypress="maskNumberInput(1, 99999999, 'additionalIncome-nominal')" value="0">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="additionalIncome-description" class="control-label">Description</label>
							<input type="text" class="form-control mb-10" id="additionalIncome-description" name="additionalIncome-description">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="additionalIncome-idAdditionalIncome" name="additionalIncome-idAdditionalIncome" value="">
				<input type="hidden" id="additionalIncome-transferReceiptFileName" name="additionalIncome-transferReceiptFileName" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-settingPointRate">
	<div class="modal-dialog modal-sm" role="document">
		<form class="modal-content form-horizontal" id="editor-settingPointRate">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-settingPointRate">Add/Edit Setting Point Rate</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 mb-10">
						<div class="form-group required">
							<label for="settingPointRate-minNominal" class="control-label text-right">Min. Nominal</label>
							<h6 class="text-right" id="settingPointRate-minNominal"></h6>
						</div>
					</div>
					<div class="col-sm-12 mb-10">
						<div class="form-group required">
							<label for="settingPointRate-maxNominal" class="control-label text-right">Max. Nominal</label>
							<input type="text" class="form-control text-right" id="settingPointRate-maxNominal" name="settingPointRate-maxNominal" onkeypress="maskNumberInput(0, 99999999, 'settingPointRate-maxNominal')">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="settingPointRate-point" class="control-label text-right">Point Review</label>
							<input type="text" class="form-control text-right" id="settingPointRate-point" name="settingPointRate-point" onkeypress="maskNumberInput(0, 99, 'settingPointRate-point')">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="settingPointRate-idAdditionalIncomeRate" name="settingPointRate-idAdditionalIncomeRate" value="0">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default button-sm" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/FinanceDriver/additionalIncome.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>