<table class="table" id="table-masterSource">
	<thead class="thead-light">
		<tr>
			<th width="60">#</th>
			<th width="150">Logo</th>
			<th>Source Name</th>
			<th width="120">Upselling</th>
			<th width="140" class="text-right">Review 5 Star Point</th>
			<th width="160">Review Bonus & Punishment</th>
			<th width="80">Default Currency</th>
			<th width="40"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="8" class="text-center">No data found</td>
		</tr>
	</tbody>
</table>
<div class="modal fade" id="editor-modal-masterSource">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-masterSource">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-masterSource">Data Editing</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row border-bottom px-3 mb-10">
					<div class="col-sm-6 mb-20">
						<label for="upsellingType" class="control-label">Upselling</label>
					</div>
					<div class="col-sm-6 mb-20 text-right">
						<div class='adomx-checkbox-radio-group inline'>
							<label class='adomx-radio-2'><input type='radio' id='upsellingType' value='1' name='upsellingType'><i class='icon'></i> Yes</label>
							<label class='adomx-radio-2'><input type='radio' id='upsellingType' value='0' name='upsellingType'><i class='icon'></i> No</label>
						</div>
					</div>
					<div class="col-sm-6 mb-20">
						<label for="reviewBonusPunishment" class="control-label">Review Bonus & Punishment</label>
					</div>
					<div class="col-sm-6 mb-20 text-right">
						<div class='adomx-checkbox-radio-group inline'>
							<label class='adomx-radio-2'><input type='radio' id='reviewBonusPunishment' value='1' name='reviewBonusPunishment'><i class='icon'></i> Yes</label>
							<label class='adomx-radio-2'><input type='radio' id='reviewBonusPunishment' value='0' name='reviewBonusPunishment'><i class='icon'></i> No</label>
						</div>
					</div>
					<div class="col-lg-8 col-sm-12 mb-10">
						<div class="form-group required">
							<label for="sourceName" class="control-label">Source Name</label>
							<input type="text" class="form-control" id="sourceName" name="sourceName" placeholder="Source Name">
						</div>
					</div>
					<div class="col-lg-4 col-sm-12 mb-10">
						<div class="form-group required">
							<label for="defaultCurrency" class="control-label">Default Currency</label>
							<select class="form-control" id="defaultCurrency" name="defaultCurrency">
								<option value="IDR">IDR</option>
								<option value="USD">USD</option>
							</select>
						</div>
					</div>
					<div class="col-sm-12 mb-10">
						<div class="form-group required">
							<label for="review5StarPoint" class="control-label">Review 5 Star Point</label>
							<input type="text" class="form-control" id="review5StarPoint" name="review5StarPoint" placeholder="Review 5 Star Point" value="0" onkeypress="maskNumberInput(1, 9, 'review5StarPoint')">
						</div>
					</div>
				</div>
				<div class="row mbn-30">
					<div class="col-12 mb-30 text-center">
						<small>Allowed file extension is : <b>.jpg or .png</b>.<br/>Please make sure the image has a square shape.</small><br/>
						<img id="imageLogoSourceEditor" src=""/><br/>
						<div id="uploaderLogoSource" class="mt-20">Upload Logo</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idData" name="idData" value="">
				<input type="hidden" id="logoSourceName" name="logoSourceName" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>