<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Ticket Setting <span> / Settings for ticket vendor and price per vendor</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-primary button-sm pull-right btn-block" data-action="insert" data-toggle="modal" data-target="#modal-editor-ticketVendorPrice">
				<span><i class="fa fa-plus"></i>New Data</span>
			</button>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body pb-1">
		<div class="row">
			<div class="col-lg-8 col-sm-12">
				<div class="form-group">
					<label for="productName" class="control-label">Product Name</label>
					<input type="text" class="form-control" id="productName" name="productName" placeholder="Type something and push ENTER to search">
				</div>
			</div>
			<div class="col-lg-4 col-sm-12">
				<div class="form-group">
					<label for="optionVendorFilter" class="control-label">Vendor</label>
					<select id="optionVendorFilter" name="optionVendorFilter" class="form-control select2" option-all="All Vendor"></select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box mt-2">
	<div class="box-body pt-1">
		<div class="row mt-25">
			<div class="col-sm-12 responsive-table-container">
				<table class="table" id="table-ticketVendorPrice">
					<thead class="thead-light">
						<tr>
							<th width="60" class="text-right">#</th>
							<th width="300">Product Name</th>
							<th width="120">Vendor Name</th>
							<th width="80">Voucher</th>
							<th width="80" class="text-right">Pax Range</th>
							<th width="80" class="text-right">Adult</th>
							<th width="80" class="text-right">Child</th>
							<th width="80" class="text-right">Infant</th>
							<th>Notes</th>
							<th width="40"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="10" class="text-center">No data found</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-editor-ticketVendorPrice">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-ticketVendorPrice">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-ticketVendorPrice">Add/Edit Ticket Price per Vendor</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="optionProductEditor" class="control-label">Product</label>
							<select id="optionProductEditor" name="optionProductEditor" class="form-control"></select>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-group">
							<label for="optionVendorEditor" class="control-label">Vendor</label>
							<select id="optionVendorEditor" name="optionVendorEditor" class="form-control"></select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="optionVoucherStatus" class="control-label">Generate Voucher</label>
							<select id="optionVoucherStatus" name="optionVoucherStatus" class="form-control">
								<option value="0">No</option>
								<option value="1">Yes</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group required">
							<label for="paxRangeMin" class="control-label">Pax Range</label>
							<input type="text" class="form-control mb-10 text-right" id="paxRangeMin" name="paxRangeMin" onkeypress="maskNumberInput(1, 999, 'paxRangeMin')" value="1">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="paxRangeMax" class="control-label">.</label>
							<input type="text" class="form-control mb-10 text-right" id="paxRangeMax" name="paxRangeMax" onkeypress="maskNumberInput(1, 999, 'paxRangeMax')" value="1">
						</div>
					</div>
					<div class="col-lg-4 col-sm-12">
						<div class="form-group required">
							<label for="priceAdult" class="control-label">Adult Price</label>
							<input type="text" class="form-control mb-10 text-right" id="priceAdult" name="priceAdult" onkeypress="maskNumberInput(0, 99999999, 'priceAdult')" value="0">
						</div>
					</div>
					<div class="col-lg-4 col-sm-12">
						<div class="form-group required">
							<label for="priceChild" class="control-label">Child Price</label>
							<input type="text" class="form-control mb-10 text-right" id="priceChild" name="priceChild" onkeypress="maskNumberInput(0, 99999999, 'priceChild')" value="0">
						</div>
					</div>
					<div class="col-lg-4 col-sm-12">
						<div class="form-group required">
							<label for="priceInfant" class="control-label">Infant Price</label>
							<input type="text" class="form-control mb-10 text-right" id="priceInfant" name="priceInfant" onkeypress="maskNumberInput(0, 99999999, 'priceInfant')" value="0">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label for="notes" class="control-label">Notes</label>
							<textarea class="form-control mb-10" id="notes" name="notes"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idVendorTicketPrice" name="idVendorTicketPrice" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/ProductSetting/ticketSetting.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
