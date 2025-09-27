<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto">
		<div class="page-heading">
			<h3 class="title">Transfer List <span> / Transfer list of withdrawal and loan request</span></h3>
		</div>
	</div>
</div>
<div class="box mt-20">
	<div class="box-body">
		<ul class="nav nav-tabs mb-15" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#unprocessedTab"><i class="fa fa-hourglass-start"></i> Unprocessed</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#onGoingTab"><i class="fa fa-hourglass-half"></i> On Going</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#finishedTab"><i class="fa fa-check-square-o"></i> Finished</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade show active" id="unprocessedTab">
				<div class="row mt-10">
					<div class="col-lg-9 col-sm-6 mb-10">
						<div class="alert alert-primary py-2" role="alert" id="unprocessedDescription"></div>
					</div>
					<div class="col-lg-3 col-sm-6 mb-10">
						<button type="button" class="button button-info button-md btn-block" id="createExcelPayroll"><span><i class="fa fa-file-excel-o"></i>Create Excel Payroll</span></button>
					</div>
					<div class="col-sm-12 mb-10 tableFixHead" style="max-height:450px">
						<table class="table" id="table-unprocessedTransferList">
							<thead class="thead-light">
								<tr>
									<th width="100"></th>
									<th width="120">Type</th>
									<th width="160">Partner Detail</th>
									<th>Bank Account</th>
									<th class="text-right" width="100">Amount</th>
									<th>Remark</th>
									<th width="60" class="text-center" align="center"><label class="adomx-checkbox mx-auto"><input type="checkbox" id="checkAllTransferList"><i class="icon"></i></label></th>
								</tr>
							</thead>
							<tbody id="bodyUnprocessedTransferList">
								<tr><td colspan="6" align="center">No Data Found</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="onGoingTab">
				<div class="row mt-10">
					<div class="col-lg-2 col-sm-3">
						<div class="form-group">
							<label for="startDateProcess" class="control-label">Date Process</label>
							<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateProcess" name="startDateProcess" value="<?=date('01-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-2 col-sm-3">
						<div class="form-group">
							<label for="endDateProcess" class="control-label">.</label>
							<input type="text" class="form-control input-date-single text-center" id="endDateProcess" name="endDateProcess" value="<?=date('d-m-Y')?>">
						</div>
					</div>
				</div>
				<div class="row mt-10">
					<div class="col-sm-12 mb-10 accordion accordion-icon" style="max-height:600px; overflow-y:scroll" id="bodyProcessedTransferList"></div>
				</div>
			</div>
			<div class="tab-pane fade" id="finishedTab">
				<div class="row mt-10">
					<div class="col-lg-2 col-sm-3">
						<div class="form-group">
							<label for="startDateFinish" class="control-label">Date Finish</label>
							<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateFinish" name="startDateFinish" value="<?=date('01-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-2 col-sm-3">
						<div class="form-group">
							<label for="endDateFinish" class="control-label">.</label>
							<input type="text" class="form-control input-date-single text-center" id="endDateFinish" name="endDateFinish" value="<?=date('d-m-Y')?>">
						</div>
					</div>
				</div>
				<div class="row mt-10">
					<div class="col-sm-12 mb-10 accordion accordion-icon" style="max-height:600px; overflow-y:scroll" id="bodyFinishedTransferList"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-htmlFilePreview">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" id="editor-htmlFilePreview">
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 text-center" id="iframeHtmlFilePreviewContainer"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalManualTransfer">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-manualTransfer">
			<div class="modal-header">
				<h4 class="modal-title" id="manualTransfer-title">Manual Transfer</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row pb-20" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12 order-details-customer-info mb-5 pb-5">
						<ul>
							<li> <span>Transfer Type</span> <span id="manualTransfer-transferType">-</span> </li>
							<li> <span>Partner Type</span> <span id="manualTransfer-partnerType">-</span> </li>
							<li> <span>Partner Name</span> <span id="manualTransfer-partnerName">-</span> </li>
							<li> <span>Bank Account</span> <span id="manualTransfer-bankAccount">-</span> </li>
							<li> <span>Amount</span> <span id="manualTransfer-amount">-</span> </li>
							<li> <span>Remark</span> <span id="manualTransfer-remark">-</span> </li>
						</ul>
					</div>
				</div>
				<div class="row pt-20">
					<div class="col-sm-12">
						<div class="row mbn-10">
							<div class="col-sm-3 col-12 mb-10"><label for="transferDate" class="control-label">Transfer Date</label></div>
							<div class="col-sm-9 col-12 mb-10"><input type="text" class="form-control form-control-sm input-date-single mb-10 text-center" id="transferDate" name="transferDate" value="<?=date('d-m-Y')?>"></div>
						</div>
					</div>
					<div class="col-sm-12 text-center">
						<small>Allowed file extension is : <b>.jpg or .png</b>.</small><br/>
						<img id="imageTransferReceipt" src=""/><br/>
						<div id="uploaderTransferReceipt" class="mt-20">Upload Transfer Receipt</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idTransferList" name="idTransferList" value="0">
				<input type="hidden" id="transferReceiptFileName" name="transferReceiptFileName" value="">
				<button class="button button-primary" id="saveManualTransfer">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/Finance/transferList.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
