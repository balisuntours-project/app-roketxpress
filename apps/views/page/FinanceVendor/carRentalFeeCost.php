<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Car Rental Fee & Cost <span> / Recap and detail car rental fee and cost per period/date range</span></h3>
		</div>
	</div>
</div>
<div class="box mt-10 mb-10">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#carRentalCostFeeRecap"><i class="fa fa-list-alt"></i> Recap per Car</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#carRentalFeeDetail"><i class="fa fa-list"></i> Details Fee</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#carRentalCostDetail"><i class="fa fa-list-ul"></i> Details Cost</a></li>
		</ul>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="tab-content">
			<div class="tab-pane fade show active" id="carRentalCostFeeRecap">
				<div class="row">
					<div class="col-lg-3 col-sm-12">
						<div class="form-group">
							<label for="optionVendorCarRecap" class="control-label">Car Vendor</label>
							<select id="optionVendorCarRecap" name="optionVendorCarRecap" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="optionMonth" class="control-label">Period</label>
							<select class="form-control" id="optionMonth" name="optionMonth"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="optionYear" class="control-label">.</label>
							<select class="form-control" id="optionYear" name="optionYear"></select>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12">
						<div class="form-group">
							<label for="searchKeywordRecap" class="control-label">Type something to search data</label>
							<input type="text" class="form-control mb-10" id="searchKeywordRecap" name="searchKeywordRecap" placeholde="Type something and press ENTER to search">
						</div>
					</div>
					<div class="col-12">
						<div class="row mt-10">
							<div class="col-lg-8 col-sm-12 mb-10">
								<span id="tableDataCountCarRentalCostFeeRecap"></span>
							</div>
							<div class="col-lg-4 col-sm-12 mb-10">
								<a class="button button-info button-sm pull-right d-none" id="excelDataCarRentalCostFeeRecap" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Recap</span></a>
							</div>
						</div>
						<div class="row mt-5 responsive-table-container">
							<table class="table" id="table-dataCarRentalCostFeeRecap">
								<thead class="thead-light">
									<tr>
										<th width="200">Vendor</th>
										<th width="180">Default Driver</th>
										<th >Car Detail</th>
										<th width="140" class="text-right">Total Schedule</th>
										<th width="140" class="text-right">Total Fee</th>
										<th width="140" class="text-right">Total Cost</th>
										<th width="140" class="text-right">Grand Total</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th colspan="7" class="text-center">No data found</th>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="row mt-20">
							<div class="col-sm-12 mb-10">
								<ul class="pagination" id="tablePaginationDataCarRentalCostFeeRecap"></ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="carRentalFeeDetail">
				<div class="row">
					<div class="col-lg-3 col-sm-12">
						<div class="form-group">
							<label for="optionVendorCarDetail" class="control-label">Car Vendor</label>
							<select id="optionVendorCarDetail" name="optionVendorCarDetail" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="scheduleStartDate" class="control-label">Schedule Date</label>
							<input type="text" class="form-control input-date-single mb-10 text-center" id="scheduleStartDate" name="scheduleStartDate" value="<?=date('01-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="scheduleEndDate" class="control-label">.</label>
							<input type="text" class="form-control input-date-single text-center" id="scheduleEndDate" name="scheduleEndDate" value="<?=date('t-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-5 col-sm-12">
						<div class="form-group">
							<label for="searchKeywordDetail" class="control-label">Type something to search data</label>
							<input type="text" class="form-control mb-10" id="searchKeywordDetail" name="searchKeywordDetail" placeholde="Type something and press ENTER to search">
						</div>
					</div>
					<div class="col-12">
						<div class="row mt-10">
							<div class="col-lg-8 col-sm-12 mb-10">
								<span id="tableDataCountCarRentalFeeDetail"></span>
							</div>
							<div class="col-lg-4 col-sm-12 mb-10">
								<a class="button button-info button-sm pull-right d-none" id="excelDataCarRentalFeeDetail" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
							</div>
						</div>
						<div class="row mt-5 responsive-table-container">
							<table class="table" id="table-dataCarRentalFeeDetail">
								<thead class="thead-light">
									<tr>
										<th width="120">Vendor</th>
										<th width="180">Default Driver</th>
										<th width="120">Date</th>
										<th width="200">Car</th>
										<th >Reservation Detail</th>
										<th >Schedule Detail</th>
										<th width="120" class="text-right">Fee</th>
									</tr>
								</thead>
								<tbody>
									<tr><th colspan="7" class="text-center">No data found</th></tr>
								</tbody>
							</table>
						</div>
						<div class="row mt-20">
							<div class="col-sm-12 mb-10">
								<ul class="pagination" id="tablePaginationDataCarRentalFeeDetail"></ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="carRentalCostDetail">
				<div class="row">
					<div class="col-lg-3 col-sm-6">
						<div class="form-group">
							<label for="optionVendorCarCost" class="control-label">Car Vendor</label>
							<select id="optionVendorCarCost" name="optionVendorCarCost" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-6">
						<div class="form-group">
							<label for="recognitionDate" class="control-label">Recognition Date</label>
							<div class="input-group">
								<input type="text" class="form-control input-date-single mb-10 text-center" id="recognitionDate" name="recognitionDate" aria-describedby="iconRecognitionDate">
								<div class="input-group-append mb-10">
									<span class="input-group-text iconInputDate" id="iconRecognitionDate">
										<i class="fa fa-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-7 col-sm-12">
						<div class="form-group">
							<label for="searchKeywordDetailCost" class="control-label">Type something to search data</label>
							<input type="text" class="form-control mb-10" id="searchKeywordDetailCost" name="searchKeywordDetailCost" placeholde="Type something and press ENTER to search">
						</div>
					</div>
					<div class="col-12">
						<div class="row mt-10">
							<div class="col-lg-8 col-sm-12 mb-10">
								<span id="tableDataCountCarRentalCostDetail"></span>
							</div>
							<div class="col-lg-4 col-sm-12 mb-10">
								<a class="button button-info button-sm pull-right d-none" id="excelDataCarRentalCostDetail" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
								<button class="button button-primary button-sm pull-right mr-2" type="button" id="btnAddNewCarCost" data-toggle="modal" data-target="#modal-editorCarCost">
									<span><i class="fa fa-edit"></i>New Car Cost</span>
								</button>
							</div>
						</div>
						<div class="row mt-5 responsive-table-container">
							<table class="table" id="table-dataCarRentalCostDetail">
								<thead class="thead-light">
									<tr>
										<th width="200">Car Details</th>
										<th>Cost Details</th>
										<th width="160">Input</th>
										<th width="160">Approval</th>
										<th width="140">Image Receipt</th>
										<th width="280">Day Off Details</th>
										<th width="120" class="text-right">Cost Nominal</th>
										<th width="60"></th>
									</tr>
								</thead>
								<tbody>
									<tr><th colspan="8" class="text-center">No data found</th></tr>
								</tbody>
							</table>
						</div>
						<div class="row mt-20">
							<div class="col-sm-12 mb-10">
								<ul class="pagination" id="tablePaginationDataCarRentalCostDetail"></ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorCarCost" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="content-editorCarCost">
			<div class="modal-header">
				<h4 class="modal-title">Editor Car Cost</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-lg-7 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-lg-8 col-sm-12">
								<div class="form-group required">
									<label for="editorCarCost-idCarVendor" class="control-label">Car Vendor</label>
									<select id="editorCarCost-idCarVendor" name="editorCarCost-idCarVendor" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-4 col-sm-12">
								<div class="form-group required">
									<label for="editorCarCost-recognitionDate" class="control-label">Recognition Date</label>
									<input type="text" class="form-control input-date-single text-center" id="editorCarCost-recognitionDate" name="editorCarCost-recognitionDate" value="<?=date('d-m-Y')?>">
								</div>
							</div>
							<div class="col-lg-7 col-sm-12">
								<div class="form-group required">
									<label for="editorCarCost-idCostType" class="control-label">Cost Type</label>
									<select id="editorCarCost-idCostType" name="editorCarCost-idCostType" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-5 col-sm-12">
								<div class="form-group required">
									<label for="editorCarCost-nominal" class="control-label">Nominal</label>
									<input type="text" class="form-control text-right" id="editorCarCost-nominal" name="editorCarCost-nominal" value="0" onkeypress="maskNumberInput(1, 999999999, 'editorCarCost-nominal')">
								</div>
							</div>
							<div class="col-sm-12 border-bottom pb-10 mb-10">
								<div class="form-group required">
									<label for="editorCarCost-description" class="control-label">Description</label>
									<textarea class="form-control" placeholder="Cost description" id="editorCarCost-description" name="editorCarCost-description"></textarea>
								</div>
							</div>
							<div class="col-sm-12 mt-10">
								<div class="form-group required mb-10">
									<label for="editorCarCost-approvalStatus" class="control-label">Approval Status</label>
									<div class="adomx-checkbox-radio-group inline">
										<label class="adomx-radio-2"><input type="radio" name="editorCarCost-approvalStatus" value="0"> <i class="icon"></i> Waiting</label>
										<label class="adomx-radio-2"><input type="radio" name="editorCarCost-approvalStatus" value="1"> <i class="icon"></i> Approved</label>
										<label class="adomx-radio-2"><input type="radio" name="editorCarCost-approvalStatus" value="-1"> <i class="icon"></i> Rejected</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12">
						<div class="border-bottom pb-10 mb-10">
							<h6 class="mb-0">Day Off Detail</h6>
							<div class="order-details-customer-info">
								<ul class="ml-5">
									<li> <span>Duration</span> <span id="editorCarCost-dayOffDurationStr">-</span> </li>
									<li> <span>Start</span> <span id="editorCarCost-dayOffDateTimeStart">-</span> </li>
									<li> <span>End</span> <span id="editorCarCost-dayOffDateTimeEnd">-</span> </li>
							</div>
						</div>
						<div class="text-center">
							<h6 class="mb-20">Cost Receipt</h6>
							<small>Allowed file extension is : <b>.jpg or .png</b>.</small><br/>
							<img id="editorCarCost-imageCostReceipt" src="" style="max-height:200px; max-width:290px;"/><br/>
							<div id="editorCarCost-uploaderCostReceipt" class="mt-20">Upload Cost Receipt</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorCarCost-idCarCost" name="editorCarCost-idCarCost" value="0">
				<input type="hidden" id="editorCarCost-costReceiptFileName" name="editorCarCost-costReceiptFileName" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/FinanceVendor/carRentalFeeCost.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>