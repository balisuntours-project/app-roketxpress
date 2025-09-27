<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">User Level Menu <span> / List menu by user level</span></h3>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="box">
			<div class="box-body" id="formLevelMenu">
				<div class="row">
					<div class="col-sm-8 mb-10">
						<label for="cari" class="control-label">User Level</label>
						<select class="form-control" name="userLevel" id="optionUserLevel" onchange="generateTableLevelUserMenu(this.value)"></select>
					</div>
					<div class="col-sm-4 mt-25">
						<button class="button button-primary btn-block" id="btnSaveLevelMenu"><span>Save</span></button>
					</div>
				</div>
				<div>
					<table class="table">
						<thead class="thead-light">
							<tr>
								<th>Grup Menu</th>
								<th>Menu</th>
								<th width="200">Access Permission</th>
							</tr>
						</thead>
						<tbody id="bodyLevelMenu">
							<tr>
								<th colspan="3">Tidak ada data</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="levelmenu-confirm-save" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title">Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				Save menu changes on level <b id="textUserLevel"></b>?
			</div>
            <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-success" id="btnConfirmSaveLevelMenu">Yes, Save</button>
            </div>
        </div>
    </div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/Setting/userLevelMenu.js";
	$.getScript(url);
</script>