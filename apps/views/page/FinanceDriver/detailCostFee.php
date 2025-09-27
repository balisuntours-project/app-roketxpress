<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Details Driver Fee <span> / Detail fee per driver based on date range</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<label for="optionDriverType" class="control-label">Driver Type</label>
					<select id="optionDriverType" name="optionDriverType" class="form-control" option-all="All Driver Type"></select>
				</div>
			</div>
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<label for="optionDriver" class="control-label">Driver</label>
					<select id="optionDriver" name="optionDriver" class="form-control" option-all="All Driver"></select>
				</div>
			</div>
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
						<span id="tableDataCountDetailCostFee"></span>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<a class="button button-primary button-sm pull-right d-none" id="excelDetailCostFee" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail Fee</span></a>
					</div>
				</div>
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-detailCostFee">
						<thead class="thead-light">
							<tr>
								<th width="180">Schedule Details</th>
								<th >Reservation Detail</th>
								<th width="250">Job Details</th>
								<th width="250">Cost Detail</th>
								<th width="120" class="text-right">Cost + Fee</th>
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
						<ul class="pagination" id="tablePaginationDetailCostFee"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.table td {
		word-break: break-word;
		white-space: break-spaces;
	}
</style>
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/FinanceDriver/detailCostFee.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>