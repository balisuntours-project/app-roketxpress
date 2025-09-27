<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Review bonus & punishment <span> / Bonus & punishment from review collected by driver</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#allDriverReportTab"><i class="fa fa-file-text"></i> All Driver Report</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#periodTargetRateTab"><i class="fa fa-calendar"></i> Period - Target - Rate Settings</a></li>
		</ul>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<div class="tab-content">
			<div class="tab-pane fade show active" id="allDriverReportTab">
				<div class="row">
					<div class="col-lg-2 col-sm-5">
						<div class="form-group">
							<label for="allDriverReport-optionDriverType" class="control-label">Driver Type</label>
							<select id="allDriverReport-optionDriverType" name="allDriverReport-optionDriverType" class="form-control" option-all="All Driver Type"></select>
						</div>
					</div>
					<div class="col-lg-4 col-sm-7">
						<div class="form-group">
							<label for="allDriverReport-optionDriver" class="control-label">Driver</label>
							<select id="allDriverReport-optionDriver" name="allDriverReport-optionDriver" class="form-control" option-all="All Driver"></select>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">
						<div class="form-group">
							<label for="allDriverReport-optionMonth" class="control-label">Period</label>
							<select class="form-control" id="allDriverReport-optionMonth" name="allDriverReport-optionMonth"></select>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">
						<div class="form-group">
							<label for="allDriverReport-optionYear" class="control-label">.</label>
							<select class="form-control" id="allDriverReport-optionYear" name="allDriverReport-optionYear"></select>
						</div>
					</div>
					<div class="col-12 mt-10">
						<div class="row">
							<div class="col-lg-8 col-sm-12 mb-5">
								<span id="tableDataCountAllDriverReport"></span>
							</div>
							<div class="col-lg-4 col-sm-12 mb-5">
								<a class="button button-primary button-sm pull-right" id="excelAllDriverReport" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel All Driver Report</span></a>
							</div>
						</div>
						<div class="row mt-5 responsive-table-container">
							<table class="table" id="table-allDriverReport">
								<thead class="thead-light">
									<tr>
										<th width="120">Driver Type</th>
										<th >Driver Name</th>
										<th width="160" >Period Start</th>
										<th width="160" >Period End</th>
										<th width="90" class="text-right">Target Point</th>
										<th width="100" class="text-right">Review Point</th>
										<th width="120" class="text-right">Rate</th>
										<th width="120" class="text-right">Bonus</th>
										<th width="120" class="text-right">Punishment</th>
										<th width="120" class="text-right">Result</th>
										<th width="100">Status WD</th>
										<th width="60"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th colspan="12" class="text-center">No data found</th>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="row mt-20">
							<div class="col-sm-12 mb-10">
								<ul class="pagination" id="tablePaginationAllDriverReport"></ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="periodTargetRateTab">
				<div class="row">
					<div class="col-lg-10 col-sm-12 mb-10">
						<span id="tableDataCountPeriodTargetRate"></span>
					</div>
					<div class="col-lg-2 col-sm-12 mb-10">
						<select class="form-control" id="periodTargetRate-optionYear" name="periodTargetRate-optionYear"></select>
					</div>
				</div>
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-periodTargetRate">
						<thead class="thead-light">
							<tr>
								<th>Month Year</th>
								<th width="130">Date Start</th>
								<th width="130">Date End</th>
								<th width="100" class="text-right">Target Point</th>
								<th width="100" class="text-right">Bonus Rate</th>
								<th width="150" class="text-right">Count Driver</th>
								<th width="150" class="text-right">Withdrawn</th>
								<th width="150" class="text-right">Total Bonus</th>
								<th width="150" class="text-right">Total Punishment</th>
								<th width="150" class="text-right">Total Result</th>
								<th width="80"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="12" class="text-center">No data found</th>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row mt-20">
					<div class="col-sm-12 mb-10">
						<ul class="pagination" id="tablePaginationPeriodTargetRate"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-formPeriodTargetRate" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="container-formPeriodTargetRate">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-formPeriodTargetRate">Edit Review Period - Target - Rate</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-4 col-sm-6 pt-10 pb-10 border-bottom"><b>Month Year Period</b></div>
					<div class="col-lg-8 col-sm-6 pt-10 pb-10 border-bottom"><b id="formPeriodTargetRate-periodMonthYearStr">-</b></div>
					<div class="col-sm-6 pt-10">
						<div class="form-group">
							<label for="formPeriodTargetRate-datePeriodStart" class="control-label">Date Period</label>
							<input type="text" class="form-control form-control-sm mb-10 text-center" id="formPeriodTargetRate-datePeriodStart" name="formPeriodTargetRate-datePeriodStart" disabled>
						</div>
					</div>
					<div class="col-sm-6 pt-10">
						<div class="form-group">
							<label for="formPeriodTargetRate-datePeriodEnd" class="control-label">.</label>
							<input type="text" class="form-control form-control-sm input-date-single mb-10 text-center" id="formPeriodTargetRate-datePeriodEnd" name="formPeriodTargetRate-datePeriodEnd">
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 pt-10">
						<div class="form-group">
							<label for="formPeriodTargetRate-totalTarget" class="control-label">Total Target</label>
							<div class="input-group">
								<span class="input-group-prepend">
									<button type="button" class="btn btn-outline-secondary btn-number" data-type="minus" data-field="formPeriodTargetRate-totalTarget">
										<span class="fa fa-minus"></span>
									</button>
								</span>
								<input type="text" name="formPeriodTargetRate-totalTarget" id="formPeriodTargetRate-totalTarget" class="form-control form-control-sm input-number text-right" value="1" min="1" max="28">
								<span class="input-group-append">
									<button type="button" class="btn btn-outline-secondary btn-number" data-type="plus" data-field="formPeriodTargetRate-totalTarget">
										<span class="fa fa-plus"></span>
									</button>
								</span>
							</div>
						</div>
					</div>
					<div class="col-lg-8 col-sm-6 pt-10">
						<div class="form-group">
							<label for="formPeriodTargetRate-rateBonusPunishment" class="control-label">Rate Bonus/Punishment</label>
							<input type="text" class="form-control form-control-sm mb-10 text-right" id="formPeriodTargetRate-rateBonusPunishment" name="formPeriodTargetRate-rateBonusPunishment" value="0" onkeypress="maskNumberInput(1000, 100000, 'formPeriodTargetRate-rateBonusPunishment');">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="formPeriodTargetRate-idDriverReviewBonusPeriod" name="formPeriodTargetRate-idDriverReviewBonusPeriod" value="">
				<input type="hidden" id="formPeriodTargetRate-periodMonthYear" name="formPeriodTargetRate-periodMonthYear" value="">
				<input type="hidden" id="formPeriodTargetRate-isLastPeriod" name="formPeriodTargetRate-isLastPeriod" value="">
				<input type="hidden" id="formPeriodTargetRate-originDatePeriodEnd" name="formPeriodTargetRate-originDatePeriodEnd" value="">
				<button class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorTargetReviewPoint" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="container-editorTargetReviewPoint">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorTargetReviewPoint">Edit Review Target For Driver</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12 pt-10 pb-20 border-bottom">
						<div class="order-details-customer-info">
							<ul>
								<li> <span>Driver Details</span> <span id="editorTargetReviewPoint-driverDetailsStr">-</span> </li>
								<li> <span>Target Period</span> <span id="editorTargetReviewPoint-targetPeriodStr">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-sm-6 pt-20">
						<div class="form-group">
							<label for="editorTargetReviewPoint-targetStatus" class="control-label">Target Status</label>
							<select id="editorTargetReviewPoint-targetStatus" name="editorTargetReviewPoint-targetStatus" class="form-control">
								<option value="-1">Active</option>
								<option value="1">Inactive</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6 pt-20">
						<div class="form-group">
							<label for="editorTargetReviewPoint-totalTarget" class="control-label">Total Target</label>
							<div class="input-group">
								<span class="input-group-prepend">
									<button type="button" class="btn btn-outline-secondary btn-number" data-type="minus" data-field="editorTargetReviewPoint-totalTarget">
										<span class="fa fa-minus"></span>
									</button>
								</span>
								<input type="text" name="editorTargetReviewPoint-totalTarget" id="editorTargetReviewPoint-totalTarget" class="form-control form-control-sm input-number text-right" value="1" min="1" max="28">
								<span class="input-group-append">
									<button type="button" class="btn btn-outline-secondary btn-number" data-type="plus" data-field="editorTargetReviewPoint-totalTarget">
										<span class="fa fa-plus"></span>
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorTargetReviewPoint-idDriverReviewBonus" name="editorTargetReviewPoint-idDriverReviewBonus" value="0">
				<button class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url 		=	"<?=BASE_URL_ASSETS?>js/page-module/FinanceDriver/reviewBonusPunishment.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>