<table class="table" id="table-masterCarType">
	<thead class="thead-light">
		<tr>
			<th width="60">#</th>
			<th>Car Type</th>
			<th>Description</th>
			<th width="40"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="4" class="text-center">No data found</td>
		</tr>
	</tbody>
</table>
<div class="modal fade" id="editor-modal-masterCarType">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-masterCarType">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-masterCarType">Data Editing</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="carTypeName" class="col-sm-12 control-label">Car Type</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="carTypeName" name="carTypeName" placeholder="Car Type">
					</div>
				</div>
				<div class="form-group">
					<label for="carTypeDescription" class="col-sm-12 control-label">Description</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="carTypeDescription" name="carTypeDescription" placeholder="Description">
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