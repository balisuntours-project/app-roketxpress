<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Self Drive Setting <span> / Settings rent fee by car type and vendor for self drive product</span></h3>
		</div>
	</div>
</div>
<ul class="nav nav-tabs mb-15" id="tabsPanel">
	<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#vendorCarTab"><i class="fa fa-list-alt"></i> Vendor Car List</a></li>
	<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#carFeeTab"><i class="fa fa-file-text"></i> Car Fee</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane fade show active" id="vendorCarTab">
		<div class="box">
			<div class="box-body pb-0">
				<div class="row">
					<div class="col-lg-3 col-sm-12 mb-10">
						<div class="form-group">
							<select id="optionVendorCarFilter" name="optionVendorCarFilter" class="form-control" option-all="All Vendor"></select>
						</div>
					</div>
					<div class="col-lg-7 col-sm-12 mb-10">
						<div class="form-group">
							<input type="text" class="form-control mb-10" id="keywordSearchCarList" name="keywordSearchCarList" placeholder="Type something and press ENTER to search">
						</div>
					</div>
					<div class="col-lg-2 col-sm-12 mb-10">
						<button class="button button-primary button-sm pull-right btn-block pt-2 pb-2" data-action="insert" data-toggle="modal" data-target="#modal-editor-vendorCar">
							<span><i class="fa fa-plus"></i>New Data</span>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="box mt-5">
			<div class="box-body pt-2">
				<div class="row">
					<div class="col-sm-12 responsive-table-container">
						<table class="table" id="table-vendorCar">
							<thead class="thead-light">
								<tr>
									<th width="60" class="text-right">#</th>
									<th width="120">Vendor Name</th>
									<th width="120">Default Driver</th>
									<th width="180">Car Detail</th>
									<th width="100">Plat Number</th>
									<th width="120">Transmission</th>
									<th width="300">Cost/Fee List</th>
									<th>Description</th>
									<th width="80">Status</th>
									<th width="40"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="9" class="text-center">No data found</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="carFeeTab">
		<div class="box">
			<div class="box-body pb-0">
				<div class="row">
					<div class="col-lg-10 col-sm-7 mb-10">
						<div class="form-group">
							<select id="optionCarTypeFeeFilter" name="optionCarTypeFeeFilter" class="form-control" option-all="All Car Type"></select>
						</div>
					</div>
					<div class="col-lg-2 col-sm-5 mb-10">
						<button class="button button-primary button-sm pull-right btn-block pt-2 pb-1" data-action="insert" data-toggle="modal" data-target="#modal-editor-selfDriveFee">
							<span><i class="fa fa-plus"></i>New Data</span>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="box mt-5">
			<div class="box-body pt-2">
				<div class="row">
					<div class="col-sm-12 responsive-table-container">
						<table class="table" id="table-selfDriveFee">
							<thead class="thead-light">
								<tr>
									<th width="60" class="text-right">#</th>
									<th width="220">Vendor Name</th>
									<th width="180">Car Type</th>
									<th width="100" class="text-right">Duration (Hours)</th>
									<th width="100" class="text-right">Nominal Fee</th>
									<th>Notes</th>
									<th width="40"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="7" class="text-center">No data found</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-editor-selfDriveFee">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-selfDriveFee">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-selfDriveFee">Add/Edit Rent Car Fee</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="optionVendorFeeEditor" class="control-label">Vendor</label>
							<select id="optionVendorFeeEditor" name="optionVendorFeeEditor" class="form-control"></select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="optionCarTypeFeeEditor" class="control-label">Car Type</label>
							<select id="optionCarTypeFeeEditor" name="optionCarTypeFeeEditor" class="form-control"></select>
						</div>
					</div>
					<div class="col-lg-4 col-sm-5">
						<div class="form-group">
							<label for="optionDurationFeeEditor" class="control-label">Duration (Hours)</label>
							<select id="optionDurationFeeEditor" name="optionDurationFeeEditor" class="form-control">
								<option value="3">3 Hours</option>
								<option value="6">6 Hours</option>
								<option value="8">8 Hours</option>
								<option value="10">10 Hours</option>
								<option value="12">12 Hours</option>
								<option value="18">18 Hours</option>
								<option value="24">24 Hours</option>
							</select>
						</div>
					</div>
					<div class="col-lg-8 col-sm-7">
						<div class="form-group required">
							<label for="nominalFee" class="control-label">Nominal Fee</label>
							<input type="text" class="form-control mb-10 text-right" id="nominalFee" name="nominalFee" onkeypress="maskNumberInput(0, 99999999, 'nominalFee')" value="0">
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
				<input type="hidden" id="idCarSelfDriveFee" name="idCarSelfDriveFee" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-editor-vendorCar">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-vendorCar">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-vendorCar">Add/Edit Vendor Car</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-6 col-xs-12">
						<div class="form-group">
							<label for="optionVendorCarEditor" class="control-label">Vendor</label>
							<select id="optionVendorCarEditor" name="optionVendorCarEditor" class="form-control"></select>
						</div>
					</div>
					<div class="col-lg-6 col-xs-12">
						<div class="form-group">
							<label for="optionCarTypeEditor" class="control-label">Car Type</label>
							<select id="optionCarTypeEditor" name="optionCarTypeEditor" class="form-control"></select>
						</div>
					</div>
					<div class="col-lg-4 col-xs-5">
						<div class="form-group required">
							<label for="brandVendorCar" class="control-label">Brand</label>
							<input type="text" class="form-control mb-10" id="brandVendorCar" name="brandVendorCar">
						</div>
					</div>
					<div class="col-lg-8 col-xs-7">
						<div class="form-group required">
							<label for="modelVendorCar" class="control-label">Model</label>
							<input type="text" class="form-control mb-10" id="modelVendorCar" name="modelVendorCar">
						</div>
					</div>
					<div class="col-lg-4 col-xs-6">
						<div class="form-group">
							<label for="optionTransmissionVendorCar" class="control-label">Transmission</label>
							<select id="optionTransmissionVendorCar" name="optionTransmissionVendorCar" class="form-control">
								<option value="1">Manual</option>
								<option value="2">Automatic</option>
							</select>
						</div>
					</div>
					<div class="col-lg-4 col-xs-6">
						<div class="form-group required">
							<label for="yearVendorCar" class="control-label">Year</label>
							<input type="text" class="form-control mb-10 nocomma" id="yearVendorCar" name="yearVendorCar" onkeypress="maskNumberInput(20, <?=date('Y')?>, 'yearVendorCar')">
						</div>
					</div>
					<div class="col-lg-4 col-xs-6">
						<div class="form-group required">
							<label for="platNumberVendorCar" class="control-label">Plat Number</label>
							<input type="text" class="form-control mb-10" id="platNumberVendorCar" name="platNumberVendorCar">
						</div>
					</div>
					<div class="col-lg-6 col-xs-12">
						<div class="form-group">
							<label for="optionDriverCarEditor" class="control-label">Default Driver</label>
							<select id="optionDriverCarEditor" name="optionDriverCarEditor" class="form-control" option-all="Not Set"></select>
						</div>
					</div>
					<div class="col-lg-6 col-xs-12">
						<div class="form-group">
							<label for="colorVendorCar" class="control-label">Color</label>
							<input type="text" class="form-control mb-10" id="colorVendorCar" name="colorVendorCar">
						</div>
					</div>
					<div class="col-12">
						<div class="form-group">
							<label for="description" class="control-label">Description</label>
							<textarea class="form-control mb-10" id="description" name="description"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idCarVendor" name="idCarVendor" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/ProductSetting/selfDriveSetting.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>