<table class="table" id="table-masterDriver">
	<thead class="thead-light">
		<tr>
			<th width="40" align="right">Rank</th>
			<th width="90">Partnership</th>
			<th width="120">Type</th>
			<th width="140">Car Details</th>
			<th width="90">Schedule</th>
			<th>Driver Details</th>
			<th>Area Priority</th>
			<th width="120">Last Login & Activity</th>
			<th width="80">Status</th>
			<th width="80"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="10" class="text-center">No data found</td>
		</tr>
	</tbody>
</table>
<div class="modal fade" id="editor-modal-masterDriver">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-masterDriver">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-masterDriver">Data Editing</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-5">
					<div class="form-group col-sm-3">
						<label for="optionDriverType" class="control-label">Driver Type</label>
						<select id="optionDriverType" name="optionDriverType" class="form-control"></select>
					</div>
					<div class="form-group col-sm-3">
						<label for="optionPartnershipType" class="control-label">Partnership Type</label>
						<select id="optionPartnershipType" name="optionPartnershipType" class="form-control">
							<option value="1">Partner</option>
							<option value="4">Office</option>
							<option value="2">Freelance</option>
							<option value="3">Team</option>
						</select>
					</div>
					<div class="form-group col-sm-6 required">
						<label for="driverName" class="control-label">Name</label>
						<input type="text" class="form-control" id="driverName" name="driverName" placeholder="Name" maxlength="100">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-lg-4 col-sm-12">
						<label for="driverNameFull" class="control-label">Full Name</label>
						<input type="text" class="form-control" id="driverNameFull" name="driverNameFull" placeholder="Full Name" maxlength="200">
					</div>
					<div class="form-group col-lg-8 col-sm-12">
						<label for="address" class="control-label">Address</label>
						<input type="text" class="form-control" id="address" name="address" placeholder="Address" maxlength="200">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-4">
						<label for="optionScheduleType" class="control-label">Schedule Type</label>
						<select id="optionScheduleType" name="optionScheduleType" class="form-control">
							<option value="1">Auto</option>
							<option value="2">Manual</option>
						</select>
					</div>
					<div class="form-group col-sm-4">
						<label for="optionCarCapacity" class="control-label">Car Capacity</label>
						<select id="optionCarCapacity" name="optionCarCapacity" class="form-control"></select>
					</div>
					<div class="form-group col-sm-4">
						<label for="driverQuota" class="control-label">Driver Quota</label>
						<input type="text" class="form-control text-right" id="driverQuota" name="driverQuota" onkeyup="maskNumberInput(1, 999, 'driverQuota')">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group required col-sm-6">
						<label for="phone" class="control-label">Phone Number</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="prefix-phone">+62</span>
							</div>
							<input type="text" class="form-control maskNumber nocomma" id="phone" name="phone" placeholder="Phone Number" onkeyup="maskNumberInput(8, false, 'phone')" aria-describedby="prefix-phone" maxlength="17">
						</div>
					</div>
					<div class="form-group required col-sm-6">
						<label for="driverEmail" class="control-label">Email</label>
						<input type="text" class="form-control" id="driverEmail" name="driverEmail" placeholder="Email" maxlength="50">
					</div>
				</div>
				<div class="row mb-5" style="border-bottom: 1px solid #e0e0e0;">
					<div class="form-group required col-lg-5 col-sm-12">
						<label for="password" class="control-label">Password</label>
						<input type="text" class="form-control" id="password" name="password" placeholder="" autocomplete="off" maxlength="50">
					</div>
					<div class="form-group col-lg-7 col-sm-12">
						<label class="adomx-checkbox mt-30"><input type="checkbox" name="checkboxReviewBonusPunishment" id="checkboxReviewBonusPunishment" value="1"> <i class="icon"></i> Include review bonus & punishment</label>
					</div>
				</div>
				<div class="row pt-10 mb-5" style="border-bottom: 1px solid #e0e0e0;">
					<div class="form-group required col-lg-3 col-sm-6">
						<label for="carNumberPlate" class="control-label">Car Number Plate</label>
						<input type="text" class="form-control" id="carNumberPlate" name="carNumberPlate" placeholder="Car Number Plate" maxlength="12">
					</div>
					<div class="form-group required col-lg-3 col-sm-6">
						<label for="carBrand" class="control-label">Car Brand</label>
						<input type="text" class="form-control" id="carBrand" name="carBrand" placeholder="Car Brand" maxlength="30">
					</div>
					<div class="form-group required col-lg-6 col-sm-12">
						<label for="carModel" class="control-label">Car Model</label>
						<input type="text" class="form-control" id="carModel" name="carModel" placeholder="Car Model" maxlength="50">
					</div>
				</div>
				<div class="row pt-10">
					<div class="col-sm-12" id="containerSecretPINDriverStatus"></div>
				</div>
				<div class="row pt-10">
					<div class="col-sm-12" id="containerDriverNewFinanceScheme"></div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idData" name="idData" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="editor-modal-driverRank" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="editor-driverRank">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-driverRank">Re-Order Driver Rank</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mt-10 px-3">
					<div class="col-lg-7 col-sm-12">
						<div class="row">
							<div class="col-4 px-2">
								<h4>Tour</h4>
								<div style="height:400px; overflow-y: scroll;">
									<div id="sortableDriverTour" class="list-group"></div>
								</div>
							</div>
							<div class="col-4 px-2">
								<h4>Shuttle</h4>
								<div style="height:400px; overflow-y: scroll;">
									<div id="sortableDriverShuttle" class="list-group"></div>
								</div>
							</div>
							<div class="col-4 px-2">
								<h4>Office</h4>
								<div style="height:400px; overflow-y: scroll;">
									<div id="sortableDriverOffice" class="list-group"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-5 col-sm-12">
						<div class="row">
							<div class="col-6 px-2">
								<h4>Freelance</h4>
								<div style="height:400px; overflow-y: scroll;">
									<div id="sortableDriverFreelance" class="list-group"></div>
								</div>
							</div>
							<div class="col-6 px-2">
								<h4>Team</h4>
								<div style="height:400px; overflow-y: scroll;">
									<div id="sortableDriverTeam" class="list-group"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="editor-modal-driverAreaOrder" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-driverAreaOrder">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-driverAreaOrder">Re-Order Driver Area Priority [<span id="driverNameAreaOrder"></span>]</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mt-10">
					<div class="col-sm-12" style="height:400px; overflow-y: scroll;">
						<div id="sortableDriverAreaOrder" class="list-group"></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idDriverAreaOrder" name="idDriverAreaOrder" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>