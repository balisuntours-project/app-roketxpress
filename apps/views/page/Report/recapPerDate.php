<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Recap per Date <span> / Recap total reservations per date</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-2 col-sm-6">
				<div class="form-group">
					<label for="optionMonth" class="control-label">Report Period</label>
					<select class="form-control" id="optionMonth" name="optionMonth"></select>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6">
				<div class="form-group">
					<label for="optionYear" class="control-label">.</label>
					<select class="form-control" id="optionYear" name="optionYear"></select>
				</div>
			</div>
			<div class="col-12 mt-20">
				<div class="row mt-10">
					<div class="col-lg-8 col-sm-12 mb-10">
						<span id="tableDataCountRecapPerDate"></span>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<a class="button button-primary button-sm pull-right" id="excelReport" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Report</span></a>
					</div>
				</div>
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-recapPerDate">
						<thead class="thead-light">
							<tr>
								<th class="text-center">Date</th>
								<th width="140" class="text-right">Total Reservation</th>
								<th width="140" class="text-right">Active/Done</th>
								<th width="140" class="text-right">Cancel</th>
								<th width="140" class="text-right">Handle By Driver</th>
								<th width="140" class="text-right">Handle By Vendor</th>
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
						<ul class="pagination" id="tablePaginationRecapPerDate"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var dateNow		=	"<?=date('d-m-Y')?>";
	var thisMonth	=	"<?=$thisMonth?>";
	var url 		=	"<?=BASE_URL_ASSETS?>js/page-module/Report/recapPerDate.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>