<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Recap Fee per Product <span> / Recap total reservations and fee per product</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-2 col-sm-3">
				<div class="form-group">
					<label for="startDate" class="control-label">Date Period</label>
					<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-2 col-sm-3">
				<div class="form-group">
					<label for="endDate" class="control-label">.</label>
					<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<label for="optionProductType" class="control-label">Product Type</label>
					<select id="optionProductType" name="optionProductType" class="form-control" option-all="All Product Type"></select>
				</div>
			</div>
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<label for="productNameFilter" class="control-label">Product Name</label>
					<input type="text" class="form-control mb-10" id="productNameFilter" name="productNameFilter">
				</div>
			</div>
			<div class="col-12 mt-10">
				<div class="row">
					<div class="col-lg-8 col-sm-12 mb-10">
						<span id="tableDataCountRecapFeePerProduct"></span>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<a class="button button-info button-sm pull-right d-none" id="excelDataRecapFeeProduct" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Recap</span></a>
					</div>
				</div>
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-recapFeePerProduct">
						<thead class="thead-light">
							<tr>
								<th width="180">Product Type</th>
								<th>Product Name</th>
								<th width="140" class="text-right">Total Reservation</th>
								<th width="140" class="text-right">Total Fee</th>
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
						<ul class="pagination" id="tablePaginationRecapFeePerProduct"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/Finance/recapFeePerProduct.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>