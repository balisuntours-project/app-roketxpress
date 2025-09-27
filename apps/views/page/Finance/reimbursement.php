<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Reimbursement <span> / List of reimbursements from driver</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-primary button-sm pull-right" id="btnAddNewReimbursement" data-toggle="modal" data-target="#modal-formReimbursement"><span><i class="fa fa-plus"></i>Add New</span></button>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body pb-5">
		<div class="row">
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="startDateReimbursement" class="control-label">Date</label>
					<input type="text" class="form-control input-date-single mb-10 text-center" id="startDateReimbursement" name="startDateReimbursement" value="<?=date('01-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-2 col-sm-6 mb-10">
				<div class="form-group">
					<label for="endDateReimbursement" class="control-label">.</label>
					<input type="text" class="form-control input-date-single text-center" id="endDateReimbursement" name="endDateReimbursement" value="<?=date('t-m-Y')?>">
				</div>
			</div>
			<div class="col-lg-8 col-sm-12 mb-10">
				<div class="form-group">
					<label for="keywordSearch" class="control-label">Search</label>
					<input type="text" class="form-control" id="keywordSearch" name="keywordSearch" placeholder="Type something and press ENTER to search...">
				</div>
			</div>
			<div class="col-sm-12 mb-10">
				<div class="form-group">
					<label class="adomx-checkbox">
						<input type="checkbox" id="checkboxViewRequestOnly" name="checkboxViewRequestOnly" value="1"> <i class="icon"></i> <b>Show reimbursement requests required approval only</b>
					</label>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-8 col-sm-12 mb-10">
				<span id="tableDataCountReimbursement"></span>
			</div>
			<div class="col-lg-4 col-sm-12 mb-10">
				<a class="button button-info button-sm pull-right d-none" id="btnExcelDetail" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Detail</span></a>
			</div>
			<div class="col-sm-12 mb-10 tableFixHead" style="max-height:750px">
				<table class="table" id="table-reimbursement">
					<thead class="thead-light">
						<tr>
							<th width="100">Date</th>
							<th width="120">Request By</th>
							<th>Description</th>
							<th width="140">Input Detail</th>
							<th width="140">Approval Detail</th>
							<th width="180">Notes</th>
							<th class="text-right" width="100">Nominal</th>
							<th width="150">Receipt Image</th>
							<th width="60"></th>
						</tr>
					</thead>
					<tbody id="bodyReimbursement">
						<tr><td colspan="9" align="center">No Data Found</td></tr>
					</tbody>
				</table>
			</div>
			<div class="col-sm-12 mb-10">
				<ul class="pagination" id="tablePaginationReimbursement"></ul>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-confirmValidateReimbursement" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-confirm-title">Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body" id="modal-confirm-body">
				<div class="row">
					<div class="col-sm-12 mb-20 text-center">
						<img id="confirmationReimbursement-imageReceipt" style="max-width: 300px; max-height:300px" src=""/>
					</div>
					<div class="col-sm-12 mb-5" style="border-bottom: 1px solid #e0e0e0;">
						<div class="order-details-customer-info mb-10">
							<ul class="ml-5">
								<li> <span><b>Date Receipt</b></span> <span id="confirmationReimbursement-dateReceipt">-</span> </li>
								<li> <span><b>Request By</b></span> <span id="confirmationReimbursement-requestBy">-</span> </li>
								<li> <span><b>Description</b></span> <span id="confirmationReimbursement-description">-</span> </li>
								<li> <span><b>Nominal</b></span> <span id="confirmationReimbursement-nominal">-</span> </li>
							</ul>
						</div>
					</div>
					<div class="col-sm-12 mt-10 mb-15">
						<p>This additional cost will be <b id="confirmationReimbursement-textValidateStatus"></b>. Are you sure?</p>
					</div>
					<div class="col-sm-12">
						<div class="adomx-checkbox-radio-group">
							<label class="adomx-checkbox"><input type="checkbox" id="confirmationReimbursement-disableConfirm"> <i class="icon"></i> Don't show confirmation again</label>
						</div>
					</div>
				</div>
			</div>
           <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-primary" id="btnConfirmValidateReimbursement" data-idReimbursement="" data-status="">Confirm</button>
           </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-zoomReceiptImage">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" id="editor-zoomReceiptImage">
			<div class="modal-body mr-10 ml-10">
				<div class="row">
					<div class="col-sm-12 text-center">
						<img id="zoomReceiptImage" style="max-width: 700px; max-height:700px" src=""/>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-formReimbursement">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-formReimbursement">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-formReimbursement">Add reimbursement</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-7 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="reimbursementDate" class="control-label">Date</label>
									<input type="text" class="form-control input-date-single mb-10 text-center" id="reimbursementDate" name="reimbursementDate">
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="form-group required">
									<label for="optionRequestByType" class="control-label">Request By</label>
									<select id="optionRequestByType" name="optionRequestByType" class="form-control">
										<option value="2">Driver</option>
										<option value="1">Vendor</option>
										<option value="3">Other</option>
									</select>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="form-group required">
									<label for="optionDriverVendor" class="control-label">Driver/Vendor</label>
									<select id="optionDriverVendor" name="optionDriverVendor" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-7 col-sm-12">
								<div class="form-group required">
									<label for="requesterName" class="control-label">Requester Name</label>
									<input type="text" class="form-control" id="requesterName" name="requesterName" value="" readonly>
								</div>
							</div>
							<div class="col-lg-5 col-sm-12">
								<div class="form-group required">
									<label for="reimbursementNominal" class="control-label">Nominal</label>
									<input type="text" class="form-control mb-10 text-right" id="reimbursementNominal" name="reimbursementNominal" value="0" onkeypress="maskNumberInput(1, 999999999, 'reimbursementNominal')">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group required">
									<label for="reimbursementDescription" class="control-label">Description</label>
									<input type="text" class="form-control" id="reimbursementDescription" name="reimbursementDescription" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12 text-center">
						<h6 class="mb-20">Reimbursement Receipt</h6>
						<small>Allowed file extension is : <b>.jpg or .png</b>.</small><br/>
						<img id="imageReimbursementReceipt" src="" style="max-height:200px; max-width:290px;"/><br/>
						<div id="uploaderReimbursementReceipt" class="mt-20">Upload Transfer Receipt</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" value="" name="idReimbursement" id="idReimbursement"/>
				<input type="hidden" id="reimbursementReceiptFileName" name="reimbursementReceiptFileName" value="">
				<button type="button" class="button button-info" data-dismiss="modal">Cancel</button>
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<script>
	var dateNow	=	'<?=date('d-m-Y')?>';
		url 	=	"<?=BASE_URL_ASSETS?>js/page-module/Finance/reimbursement.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
