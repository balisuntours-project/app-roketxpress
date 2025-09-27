<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Partner User Level <span> / List of user level partner admin</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<table	class="table data-table-footable-partnerUserLevel"
				data-paging="true"
				data-filtering="true"
				data-sorting="true"
				style="table-layout: fixed; overflow: hidden;"
				data-breakpoints='{ "xs": 480, "sm": 768, "md": 992, "lg": 1200, "xl": 1400 }'
				data-editing-add-text="Add New"
				data-editing-always-show="true"
				data-editing-show-text="<span class='fooicon fooicon-pencil' aria-hidden='true'></span> Data Editing"
				data-editing-hide-text="Cancel"
				>
		</table>
	</div>
</div>
<div class="modal fade" id="footable-editor-modal-partnerUserLevel">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="footable-editor-partnerUserLevel">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title-partnerUserLevel">Add/Edit Data</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="levelName" class="col-sm-12 control-label">Level Name</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="levelName" name="levelName" placeholder="Level Name">
					</div>
				</div>
				<div class="form-group">
					<label for="productTypeDetail" class="col-sm-12 control-label mb-10">Notification List</label>
					<div class="adomx-checkbox-radio-group pl-15" id="containerNotificationList">
						<label class="adomx-checkbox">
							<input type="checkbox" name="notificationType[]" value="1" class="cbNotificationType"> <i class="icon"></i> Schedule
						</label>
						<label class="adomx-checkbox">
							<input type="checkbox" name="notificationType[]" value="2" class="cbNotificationType"> <i class="icon"></i> Finance
						</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idUserLevelPartner" name="idUserLevelPartner" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/SettingPartner/partnerUserLevel.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>