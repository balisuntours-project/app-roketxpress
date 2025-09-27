<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Transport Setting <span> / Settings for transport cost / driver fees</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-date-range">
			<button class="button button-warning button-sm pull-right" data-toggle="modal" data-target="#modal-productRank">
				<span><i class="fa fa-list-ol"></i>Product Rank</span>
			</button>
			<button class="button button-primary button-sm pull-right" id="btnAddDriverFee" data-toggle="modal" data-target="#editor-modal-driverFee">
				<span><i class="fa fa-plus"></i>Cost & Fee</span>
			</button>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body pb-1">
		<div class="row">
			<div class="col-lg-3 col-sm-4">
				<div class="form-group">
					<label for="optionDriverType" class="control-label">Driver Type</label>
					<select id="optionDriverType" name="optionDriverType" class="form-control" option-all="All Type"></select>
				</div>
			</div>
			<div class="col-lg-9 col-sm-8">
				<div class="form-group">
					<label for="searchKeyword" class="control-label">Search</label>
					<input type="text" class="form-control" id="searchKeyword" name="searchKeyword" placeholder="Type something and push ENTER to search">
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box mt-10">
	<div class="box-body px-0 py-0 responsive-table-container">
		<div class="row">
			<div class="col-12 tableFixHead" style="height: 700px">
				<table class="table" id="table-dataTransportSetting">
					<thead class="thead-light">
						<tr>
							<th>Product Name | Additional Info</th>
							<th width="350">Details</th>
							<th width="350">Cost & Fee</th>
							<th width="60"></th>
						</tr>
					</thead>
					<tbody>
						<tr><td colspan="4" class="text-center">No data found</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="editor-modal-driverFee">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="editor-driverFee">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-driverFee">Set Driver Fee</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mx-1">
					<div class="col-sm-12 mb-15 border-bottom">
						<div class="row">
							<div class="col-lg-8 col-sm-12">
								<div class="form-group required">
									<label for="optionProductTransport" class="control-label">Transport Product</label>
									<select id="optionProductTransport" name="optionProductTransport" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="form-group required">
									<label for="optionDriverTypeEditor" class="control-label">Driver Type</label>
									<select id="optionDriverTypeEditor" name="optionDriverTypeEditor" class="form-control"></select>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="form-group required">
									<label for="optionSourceEditor" class="control-label">Source</label>
									<select id="optionSourceEditor" name="optionSourceEditor" class="form-control" option-all="Not Set" option-all-value="0"></select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12" style="border-right: 1px solid #e0e0e0;">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="optionJobType" class="control-label">Job Duration Type</label>
									<select id="optionJobType" name="optionJobType" class="form-control">
										<option value="1">Short Job</option>
										<option value="2">Standard Job</option>
										<option value="3">Long Job</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="optionJobRate" class="control-label">Job Rate</label>
									<select id="optionJobRate" name="optionJobRate" class="form-control">
										<option value="3">Top</option>
										<option value="2">Good</option>
										<option value="1">Standard</option>
									</select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="optionArea" class="control-label">Area/Zone Detection</label>
									<select id="optionArea" name="optionArea" class="form-control" option-all="Not Set" option-all-value="0"></select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="optionScheduleType" class="control-label">Schedule Type</label>
									<select id="optionScheduleType" name="optionScheduleType" class="form-control">
										<option value="2">Manual</option>
										<option value="1">Auto</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group required">
									<label for="feeNominal" class="control-label">Fee Nominal</label>
									<input type="text" class="form-control text-right" id="feeNominal" name="feeNominal" onkeypress="maskNumberInput(0, 999999999, 'feeNominal')" value="0">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="notes" class="control-label">Additional Notes</label>
									<textarea class="form-control mb-10" id="notes" name="notes" style="height: 132px;"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="row">
							<div class="col-sm-7">
								<div class="form-group">
									<label for="optionCostTicketType" class="control-label">Ticket Cost</label>
									<select id="optionCostTicketType" name="optionCostTicketType" class="form-control">
										<option value="1">Fixed Cost</option>
										<option value="2" selected>Per Pax</option>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="costTicketNominal" class="control-label">.</label>
									<input type="text" class="form-control text-right" id="costTicketNominal" name="costTicketNominal" onkeypress="maskNumberInput(0, 999999999, 'costTicketNominal')" value="0">
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
									<label for="optionCostParkingType" class="control-label">Parking Cost</label>
									<select id="optionCostParkingType" name="optionCostParkingType" class="form-control">
										<option value="1">Fixed Cost</option>
										<option value="2">Per Pax</option>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="costParkingNominal" class="control-label">.</label>
									<input type="text" class="form-control text-right" id="costParkingNominal" name="costParkingNominal" onkeypress="maskNumberInput(0, 999999999, 'costParkingNominal')" value="0">
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
									<label for="optionCostMineralWaterType" class="control-label">Mineral Water Cost</label>
									<select id="optionCostMineralWaterType" name="optionCostMineralWaterType" class="form-control">
										<option value="1">Fixed Cost</option>
										<option value="2">Per Pax</option>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="costMineralWaterNominal" class="control-label">.</label>
									<input type="text" class="form-control text-right" id="costMineralWaterNominal" name="costMineralWaterNominal" onkeypress="maskNumberInput(0, 999999999, 'costMineralWaterNominal')" value="0">
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
									<label for="optionCostBreakfastType" class="control-label">Breakfast Cost</label>
									<select id="optionCostBreakfastType" name="optionCostBreakfastType" class="form-control">
										<option value="1">Fixed Cost</option>
										<option value="2" selected>Per Pax</option>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="costBreakfastNominal" class="control-label">.</label>
									<input type="text" class="form-control text-right" id="costBreakfastNominal" name="costBreakfastNominal" onkeypress="maskNumberInput(0, 999999999, 'costBreakfastNominal')" value="0">
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
									<label for="optionCostLunchType" class="control-label">Lunch Cost</label>
									<select id="optionCostLunchType" name="optionCostLunchType" class="form-control">
										<option value="1">Fixed Cost</option>
										<option value="2" selected>Per Pax</option>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="costLunchNominal" class="control-label">.</label>
									<input type="text" class="form-control text-right" id="costLunchNominal" name="costLunchNominal" onkeypress="maskNumberInput(0, 999999999, 'costLunchNominal')" value="0">
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
									<label for="optionBonusType" class="control-label">Bonus</label>
									<select id="optionBonusType" name="optionBonusType" class="form-control">
										<option value="1">Fixed Cost</option>
										<option value="2" selected>Per Pax</option>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="bonusNominal" class="control-label">.</label>
									<input type="text" class="form-control text-right" id="bonusNominal" name="bonusNominal" onkeypress="maskNumberInput(0, 999999999, 'bonusNominal')" value="0">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idDriverFee" name="idDriverFee" value="">
				<input type="hidden" id="lastJobrate" name="lastJobrate" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-productRank" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-productRank">
			<div class="modal-header">
				<h4 class="modal-title" id="title-productRank">Re-Order Transport Product Rank</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mt-10">
					<div class="col-12 px-4" style="height:500px; overflow-y: scroll;">
						<div id="sortableTransportProduct" class="list-group"></div>
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
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/ProductSetting/transportSetting.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
