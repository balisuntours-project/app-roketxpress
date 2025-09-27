<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto">
		<div class="page-heading">
			<h3 class="title">Detail fee per vendor <span> / Detail fee car and ticket vendor base on period</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto">
		<div class="page-date-range">
			<div class="form-group mr-10">
				<label for="optionMonth" class="control-label">Period</label>
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
		<ul class="nav nav-tabs mb-15" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#ticketVendorTab"><i class="fa fa-ticket"></i> Ticket Vendor</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#carVendorTab"><i class="fa fa-car"></i> Car Vendor</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade show active" id="ticketVendorTab">
				<div class="row mt-20">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="optionVendorTicket" class="control-label">Ticket Vendor</label>
							<select id="optionVendorTicket" name="optionVendorTicket" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-12">
						<div class="row mt-10">
							<div class="col-lg-8 col-sm-12 mb-10">
								<span id="tableDataCountDetailFeeVendorTicket"></span>
							</div>
							<div class="col-lg-4 col-sm-12 mb-10">
								<a class="button button-info button-sm pull-right d-none" id="excelDataDetailFeeVendorTicket" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
							</div>
						</div>
						<div class="row mt-5 responsive-table-container">
							<table class="table" id="table-detailFeeVendorTicket">
								<thead class="thead-light">
									<tr>
										<th width="120">Vendor</th>
										<th width="120">Date</th>
										<th >Reservation Detail</th>
										<th >Schedule Detail</th>
										<th >Pax</th>
										<th width="120" class="text-right">Fee</th>
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
								<ul class="pagination" id="tablePaginationDetailFeeVendorTicket"></ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="carVendorTab">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="optionVendorCar" class="control-label">Car Vendor</label>
							<select id="optionVendorCar" name="optionVendorCar" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-12">
						<div class="row mt-10">
							<div class="col-lg-8 col-sm-12 mb-10">
								<span id="tableDataCountDetailFeeVendorCar"></span>
							</div>
						</div>
						<div class="row mt-5 responsive-table-container">
							<table class="table" id="table-detailFeeVendorCar">
								<thead class="thead-light">
									<tr>
										<th width="120">Vendor</th>
										<th width="120">Date</th>
										<th >Car</th>
										<th >Reservation Detail</th>
										<th >Schedule Detail</th>
										<th width="120" class="text-right">Fee</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th colspan="5" class="text-center">No data found</th>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="row mt-20">
							<div class="col-sm-12 mb-10">
								<ul class="pagination" id="tablePaginationDetailFeeVendorCar"></ul>
							</div>
						</div>
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
</style>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/FinanceVendor/detailFeeVendor.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>