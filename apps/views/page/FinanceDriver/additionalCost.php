<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Additional Cost <span> / List approval & history of driver additional cost</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-primary button-sm pull-right" id="btnAddNewAdditionalCost" data-toggle="modal" data-target="#modal-selectReservationAdditionalCost"><span><i class="fa fa-plus"></i>Add New</span></button>
		</div>
	</div>
</div>
<ul class="nav nav-tabs mb-15" id="tabsPanel">
	<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#approvalTab"><i class="fa fa-check-square-o"></i> Approval</a></li>
	<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#historyTab"><i class="fa fa-files-o"></i> History</a></li>
</ul>
<div class="box">
	<div class="box-body">
		<div class="tab-content">
			<div class="tab-pane fade show active" id="approvalTab">
				<div class="row">
					<div class="col-sm-12 mb-10">
						<div class="alert alert-primary" role="alert" id="approvalDescription"></div>
					</div>
					<div class="col-lg-4 col-sm-6 mb-10">
						<div class="form-group">
							<label for="optionApprovalDriverType" class="control-label">Driver Type</label>
							<select id="optionApprovalDriverType" name="optionApprovalDriverType" class="form-control" option-all="All Driver Type"></select>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 mb-10">
						<div class="form-group">
							<label for="optionApprovalDriver" class="control-label">Driver</label>
							<select id="optionApprovalDriver" name="optionApprovalDriver" class="form-control" option-all="All Driver"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-3 mb-10">
						<div class="form-group">
							<label for="startDateApproval" class="control-label">Date Schedule</label>
							<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateApproval" name="startDateApproval" value="<?=date('01-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-2 col-sm-3 mb-10">
						<div class="form-group">
							<label for="endDateApproval" class="control-label">.</label>
							<input type="text" class="form-control input-date-single text-center" id="endDateApproval" name="endDateApproval" value="<?=date('t-m-Y')?>">
						</div>
					</div>
					<div class="col-sm-12 mb-10">
						<div class="form-group">
							<label class="adomx-checkbox">
								<input type="checkbox" id="checkboxViewRequestOnly" name="checkboxViewRequestOnly" value="1"> <i class="icon"></i> <b>Show additional cost requests that need approval only</b>
							</label>
						</div>
					</div>
				</div>
				<div class="row mt-10">
					<div class="col-sm-12 mb-10 tableFixHead" style="max-height:450px">
						<table class="table" id="table-additionalCostApproval">
							<thead class="thead-light">
								<tr>
									<th>Date Time</th>
									<th>Reservation Detail</th>
									<th>Cost Detail</th>
									<th class="text-right" width="100">Nominal</th>
									<th>Receipt Image</th>
									<th width="60"></th>
								</tr>
							</thead>
							<tbody id="bodyApprovalAdditionalCost">
								<tr><td colspan="6" align="center">No Data Found</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="historyTab">
				<div class="row">
					<div class="col-lg-4 col-sm-6">
						<div class="form-group">
							<label for="optionHistoryDriverType" class="control-label">Driver Type</label>
							<select id="optionHistoryDriverType" name="optionHistoryDriverType" class="form-control" option-all="All Driver Type"></select>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6">
						<div class="form-group">
							<label for="optionHistoryDriver" class="control-label">Driver</label>
							<select id="optionHistoryDriver" name="optionHistoryDriver" class="form-control" option-all="All Driver"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-3">
						<div class="form-group">
							<label for="startDateHistory" class="control-label">Date Schedule</label>
							<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateHistory" name="startDateHistory" value="<?=date('01-m-Y')?>">
						</div>
					</div>
					<div class="col-lg-2 col-sm-3">
						<div class="form-group">
							<label for="endDateHistory" class="control-label">.</label>
							<input type="text" class="form-control input-date-single text-center" id="endDateHistory" name="endDateHistory" value="<?=date('t-m-Y')?>">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 mb-10">
						<div class="row mt-10">
							<div class="col-sm-12 mb-10">
								<span id="tableDataCountHistoryAdditionalCost"></span>
							</div>
						</div>
						<div class="row mt-5 responsive-table-container">
							<table class="table ml-15 mr-15" id="table-additionalCostHistory">
								<thead class="thead-light">
									<tr>
										<th>Date Time</th>
										<th>Reservation Detail</th>
										<th>Cost Detail</th>
										<th>Approval Detail</th>
										<th class="text-right" width="100">Nominal</th>
										<th>Receipt Image</th>
									</tr>
								</thead>
								<tbody id="bodyHistoryAdditionalCost">
									<tr><td align="center"></td></tr>
								</tbody>
							</table>
						</div>
						<div class="row mt-20">
							<div class="col-sm-12 mb-10">
								<ul class="pagination" id="tablePaginationHistoryAdditionalCost"></ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-confirmValidateAdditionalCost" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-confirm-title">Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body" id="modal-confirm-body">
				<div class="row">
					<div class="col-sm-12 mb-20 text-center">
						<img id="imageReceipt-confirmation" style="max-width: 300px; max-height:300px" src=""/>
					</div>
					<div class="col-sm-12 mb-5" style="border-bottom: 1px solid #e0e0e0;">
						<div class="order-details-customer-info mb-10">
							<ul class="ml-5">
								<li> <span><b>Cost Type</b></span> <span id="costType-confirmation">-</span> </li>
								<li> <span><b>Description</b></span> <span id="description-confirmation">-</span> </li>
								<li> <span><b>Nominal</b></span> <span id="nominal-confirmation">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-sm-12 mt-10 mb-15">
						<p>This additional cost will be <b id="textValidateStatus"></b><br/>Are you sure?</p>
					</div>
					<div class="col-sm-12">
						<div class="adomx-checkbox-radio-group">
							<label class="adomx-checkbox"><input type="checkbox" id="disableConfirm"> <i class="icon"></i> Don't show confirmation again</label>
						</div>
					</div>
				</div>
			</div>
           <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-primary" id="confirmBtnValidateAdditionalCost" data-idAdditionalCost="" data-status="">Confirm</button>
           </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-selectReservationAdditionalCost" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="container-selectReservationAdditionalCost">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-selectReservationAdditionalCost">Search Reservation for Additional Cost (Last <?=MAX_DAY_ADDITIONAL_COST_INPUT?> days)</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-5">
					<div class="form-group col-lg-2 col-sm-12">
						<label for="reservationDate" class="control-label">Reservation Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="reservationDate" name="reservationDate" value="<?=date('d-m-Y')?>">
					</div>
					<div class="form-group col-lg-3 col-sm-12">
						<label for="optionDriverSearch" class="control-label">Driver</label>
						<select id="optionDriverSearch" name="optionDriverSearch" class="form-control select2" option-all="All Driver"></select>
					</div>
					<div class="form-group col-lg-7 col-sm-12">
						<label for="reservationKeyword" class="control-label">Search reservation by driver name / booking code / reservation title / customer name / contact</label>
						<input type="text" class="form-control" id="reservationKeyword" name="reservationKeyword" placeholder="Type something and press ENTER to search" maxlength="150">
					</div>
				</div>
				<div style="height: 400px;overflow-y: scroll;" class="row mb-5 border mx-1 my-2 rounded" id="containerSelectReservationResult">
					<div class="col-sm-12 text-center mx-auto my-auto">
						<h2><i class="fa fa-list-alt text-warning"></i></h2>
						<b class="text-warning">Results goes here</b>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-editorNewAdditionalCost">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-editorNewAdditionalCost">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorNewAdditionalCost">Add new additional cost</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-7 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-12 mb-15 pb-15" style="border-bottom: 1px solid #e0e0e0;">
								<h6 class="mb-0">Reservation Detail</h6>
								<div class="order-details-customer-info">
									<ul class="ml-5">
										<li> <span>Source</span> <span id="sourceNewAdditionalCost">-</span> </li>
										<li> <span>Booking Code</span> <span id="bookingCodeNewAdditionalCost">-</span> </li>
										<li> <span>Reservation Title</span> <span id="reservationTitleNewAdditionalCost">-</span> </li>
										<li> <span>Reservation Date</span> <span id="reservationDateNewAdditionalCost">-</span> </li>
										<li> <span>Cust. Name</span> <span id="customerNameNewAdditionalCost">-</span> </li>
										<li> <span>Driver</span> <span id="driverNameTypeNewAdditionalCost">-</span> </li>
									</ul>
								</div>
							</div>
							<div class="col-lg-7 col-sm-12">
								<div class="form-group required">
									<label for="optionAdditionalCostType" class="control-label">Additional Cost Type</label>
									<select id="optionAdditionalCostType" name="optionAdditionalCostType" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-5 col-sm-12">
								<div class="form-group required">
									<label for="additionalCostNominal" class="control-label">Nominal</label>
									<input type="text" class="form-control mb-10 text-right" id="additionalCostNominal" name="additionalCostNominal" value="0" onkeypress="maskNumberInput(1, 999999999, 'additionalCostNominal')">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="additionalCostDescription" class="control-label">Description</label>
									<input type="text" class="form-control" id="additionalCostDescription" name="additionalCostDescription" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12 text-center">
						<h6 class="mb-20">Transfer Receipt</h6>
						<small>Allowed file extension is : <b>.jpg or .png</b>.</small><br/>
						<img id="imageTransferReceipt" src="" style="max-height:200px; max-width:290px;"/><br/>
						<div id="uploaderTransferReceipt" class="mt-20">Upload Transfer Receipt</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" value="" name="idReservationDetail" id="idReservationDetail"/>
				<input type="hidden" value="" name="idDriver" id="idDriver"/>
				<input type="hidden" id="transferReceiptFileName" name="transferReceiptFileName" value="">
				<button type="button" class="button button-info" data-dismiss="modal">Cancel</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/FinanceDriver/additionalCost.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
