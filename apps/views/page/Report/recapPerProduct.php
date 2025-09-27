<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Recap per Product <span> / Recap total reservations per product</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<label for="productNameFilter" class="control-label">Product Name</label>
					<input type="text" class="form-control mb-10" id="productNameFilter" name="productNameFilter">
				</div>
			</div>
			<div class="col-lg-4 col-sm-6">
				<div class="form-group">
					<label for="optionSource" class="control-label">Source</label>
					<select id="optionSource" name="optionSource" class="form-control" option-all="All Source"></select>
				</div>
			</div>
			<div class="col-lg-2 col-sm-3">
				<div class="form-group">
					<label for="startDate" class="control-label">Reservation Date</label>
					<input type="text" class="form-control input-date-single mb-10 text-center" id="startDate" name="startDate" value="<?=date('01-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-2 col-sm-3">
				<div class="form-group">
					<label for="endDate" class="control-label">.</label>
					<input type="text" class="form-control input-date-single text-center" id="endDate" name="endDate" value="<?=date('t-m-Y')?>">
				</div>
			</div>
			<div class="col-sm-12">
				<button class="button button-success button-sm" id="filterDataRecapPerProduct"><span><i class="fa fa-filter"></i>Search Data</span></button>
			</div>
			<div class="col-12 mt-20">
				<div class="row mt-10">
					<div class="col-lg-8 col-sm-12 mb-10">
						<span id="tableDataCountRecapPerProduct"></span>
					</div>
				</div>
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-recapPerProduct">
						<thead class="thead-light">
							<tr>
								<th width="180">Product Type</th>
								<th>Vendor/Driver</th>
								<th>Product Name</th>
								<th width="140">Total Reservation</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="3" class="text-center">No data found</th>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row mt-20">
					<div class="col-sm-12 mb-10">
						<ul class="pagination" id="tablePaginationRecapPerProduct"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var dateNow	=	"<?=date('d-m-Y')?>";
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/Report/recapPerProduct.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>