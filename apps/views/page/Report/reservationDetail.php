<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto">
		<div class="page-heading">
			<h3 class="title">Reservation Detail <span> / List of reservation details</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-2 col-sm-3">
				<div class="form-group">
					<label for="optionReservationType" class="control-label">Reservation Type</label>
					<select id="optionReservationType" name="optionReservationType" class="form-control" option-all="All Type"></select>
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
			<div class="col-lg-6 col-sm-12">
				<button class="button button-success button-sm mt-25" id="filterDataReservationDetail"><span><i class="fa fa-filter"></i>Search Data</span></button>
			</div>
			<div class="col-12">
				<div class="row mt-5 responsive-table-container">
					<div class="col-sm-12 mb-10 d-flex">
						<span id="tableDataCountReservationDetail" class="align-self-center"></span>
					</div>
					<div class="col-sm-12 mb-10 d-flex">
						<table class="table" id="table-reservationDetail">
							<thead class="thead-light">
								<tr>
									<th width="40" class="text-right">No.</th>
									<th>Reservation Details</th>
									<th width="250">Car Schedule</th>
									<th width="250">Driver Schedule</th>
									<th width="250">Ticket List</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th colspan="5" class="text-center">No data found</th>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-sm-12 mb-10">
						<ul class="pagination" id="tablePaginationReservationDetail"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.order-details-customer-info ul li span:first-child{
		width: 48px;
		margin-right: 8px;
	}
	.table td {
		word-break: break-word;
		white-space: break-spaces;
	}
</style>
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/Report/reservationDetail.js";
	$.getScript(url);
</script>