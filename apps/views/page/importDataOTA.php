<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Import Data OTA <span> / Import data excel reservation from online travel agent</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range"></div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<div class="row mb-10">
			<div class="col-lg-9 col-sm-12">
				<div class="alert alert-primary py-2" role="alert">
					<i class="zmdi zmdi-info"></i> The allowed documents are <b>xls and xlsx</b> with a maximum size of <b>800 kolibytes</b>
				</div>
			</div>
			<div class="col-lg-3 col-sm-12">
				<div class="form-group">
					<select id="optionSourceImportDataOTA" name="optionSourceImportDataOTA" class="form-control form-control-sm"></select>
				</div>
			</div>
			<div class="col-sm-12 text-center mt-20 mb-20">
				<i class="fa fa-cloud-upload display-3" style="font-size: 48px;"></i><br/>
				<a href="#" id="uploaderExcelReservationOTA">Upload Excel</a>
			</div>
		</div>
	</div>
</div>
<div class="box d-none" id="uploadReservationOTAScanningResult">
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
			<table class="table" id="table-resultUploadExcelReservationOTA">
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
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/importDataOTA.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>