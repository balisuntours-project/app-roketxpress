<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Driver Schedule Monitor <span> / Driver schedule monitor per date</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<div class="form-group mr-10">
				<label for="optionMonth" class="control-label">Report Period</label>
				<select class="form-control" id="optionMonth" name="optionMonth"></select>
			</div>
			<div class="form-group">
				<label for="optionYear" class="control-label">.</label>
				<select class="form-control" id="optionYear" name="optionYear"></select>
			</div>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-12">
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-scheduleDriverMonitor">
						<thead class="thead-light">
							<tr>
								<th class="text-center">Date</th>
								<th>Status</th>
								<th width="140" class="text-right">Total Schedule</th>
								<th width="140" class="text-right">Total Active Driver</th>
								<th width="140" class="text-right">Total Off Driver</th>
								<th width="140" class="text-right">Day Off Quota</th>
								<th width="60" class="text-center"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="6" class="text-center">No data found</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="editor-modal-inputDayOffQuota" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-sm" role="document">
		<form class="modal-content form-horizontal" id="editor-inputDayOffQuota">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-inputDayOffQuota">Set Day Off Quota</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 mb-10 pb-10" style="border-bottom: 1px solid #dee2e6;">
						<div class="order-details-customer-info">
							<ul>
								<li> <span>Date</span> <span id="inputDayOffQuota-date">0</span> </li>
								<li> <span>Total Schedule</span> <span id="inputDayOffQuota-totalSchedule">0</span> </li>
								<li> <span>Total Active Driver</span> <span id="inputDayOffQuota-totalActiveDriver">0</span> </li>
								<li> <span>Total Off Driver</span> <span id="inputDayOffQuota-totalOffDriver">0</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-sm-12 mb-10 form-group required">
						<label for="dayOffQuota" class="control-label">Day Off Quota</label>
						<input type="text" class="form-control mb-10 text-right" id="dayOffQuota" name="dayOffQuota" onkeypress="maskNumberInput(0, 999, 'dayOffQuota')">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idScheduleDriverMonitor" name="idScheduleDriverMonitor" value="0">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var dateNow		=	"<?=date('d-m-Y')?>";
	var thisMonth	=	"<?=$thisMonth?>";
	var url 		=	"<?=BASE_URL_ASSETS?>js/page-module/Schedule/scheduleDriverMonitor.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>