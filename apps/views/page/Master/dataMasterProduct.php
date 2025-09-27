<table class="table" id="table-masterProduct">
	<thead class="thead-light">
		<tr>
			<th width="30">#</th>
			<th >Product Name</th>
			<th >Product Type Details</th>
			<th width="100" class="text-right">Duration (Hour)</th>
			<th >Description</th>
			<th width="30"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5" class="text-center">No data found</td>
		</tr>
	</tbody>
</table>
<div class="modal fade" id="editor-modal-masterProduct">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-masterProduct">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-masterProduct">Data Editing</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row form-group required">
					<div class="col-sm-12 mb-10">
						<label for="productName" class="control-label">Product Name</label>
						<input type="text" class="form-control" id="productName" name="productName" placeholder="Product Name">
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-12 mb-10">
						<label for="durationHour" class="control-label">Duration (Hour)</label>
						<input type="text" class="form-control text-right" id="durationHour" name="durationHour" value="0" onkeypress="maskNumberInput(0, 99, 'durationHour')" maxlength="2">
					</div>
				</div>
				<div class="row form-group" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12 mb-10">
						<label for="description" class="control-label">Description</label>
						<input type="text" class="form-control" id="description" name="description" placeholder="Description">
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 mb-15 ml-15">
						<label for="productTypeDetail" class="control-label mb-10">Product Type Details</label>
						<div class="adomx-checkbox-radio-group" id="containerProductTypeDetail"></div>
					</div>
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