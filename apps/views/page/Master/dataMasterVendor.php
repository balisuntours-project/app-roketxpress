<table class="table" id="table-masterVendor">
	<thead class="thead-light">
		<tr>
			<th width="140">Vendor Type</th>
			<th>Name</th>
			<th>Address</th>
			<th width="120">Phone</th>
			<th width="160">Email</th>
			<th width="150">Finance Scheme</th>
			<th width="100">Status</th>
			<th width="120">Transport</th>
			<th width="90"></th>
		</tr>
	</thead>
	<tbody>
		<tr><td colspan="8" class="text-center">No data found</td></tr>
	</tbody>
</table>
<div class="modal fade" id="editor-modal-masterVendor">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-masterVendor">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-masterVendor">Data Editing</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-5">
					<div class="form-group col-lg-3 col-sm-6">
						<label for="optionVendorType" class="control-label">Vendor Type</label>
						<select id="optionVendorType" name="optionVendorType" class="form-control"></select>
					</div>
					<div class="form-group col-lg-3 col-sm-6">
						<label for="optionTransportService" class="control-label">Transport Service</label>
						<select id="optionTransportService" name="optionTransportService" class="form-control">
							<option value="0">Not Included</option>
							<option value="1">Included</option>
						</select>
					</div>
					<div class="form-group col-lg-6 col-sm-12 required">
						<label for="vendorName" class="control-label">Name</label>
						<input type="text" class="form-control" id="vendorName" name="vendorName" placeholder="Name" maxlength="100">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12">
						<label for="address" class="control-label">Address</label>
						<input type="text" class="form-control" id="address" name="address" placeholder="Address" maxlength="200">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group required col-sm-6">
						<label for="vendorPhone" class="control-label">Phone Number</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="prefix-vendorPhone">+62</span>
							</div>
							<input type="text" class="form-control maskNumber nocomma" id="vendorPhone" name="vendorPhone" placeholder="Phone Number" onkeyup="maskNumberInput(8, false, 'vendorPhone')" aria-describedby="prefix-vendorPhone" maxlength="17">
						</div>
					</div>
					<div class="form-group required col-sm-6">
						<label for="vendorEmail" class="control-label">Email</label>
						<input type="text" class="form-control" id="vendorEmail" name="vendorEmail" placeholder="Email" maxlength="50">
					</div>
				</div>
				<div class="row mb-5 border-bottom">
					<div class="col-sm-8 mb-20">
						<label for="autoReduceCollectPayment" class="control-label">Auto Reduce Collect Payment (Invoice Upload)</label>
					</div>
					<div class="col-sm-4 mb-20 text-right">
						<div class='adomx-checkbox-radio-group inline'>
							<label class='adomx-radio-2'><input type='radio' id='autoReduceCollectPayment' value='1' name='autoReduceCollectPayment'><i class='icon'></i> Yes</label>
							<label class='adomx-radio-2'><input type='radio' id='autoReduceCollectPayment' value='0' name='autoReduceCollectPayment' checked><i class='icon'></i> No</label>
						</div>
					</div>
				</div>
				<div class="row pt-10">
					<div class="col-sm-12" id="containerSecretPINVendorStatus"></div>
				</div>
				<div class="row pt-10">
					<div class="col-sm-12" id="containerVendorNewFinanceScheme"></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idData" name="idData" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="button" class="button button-warning mr-auto d-none" id="btnUpdateLastWithdraw">Update Last Withdraw</button>
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>