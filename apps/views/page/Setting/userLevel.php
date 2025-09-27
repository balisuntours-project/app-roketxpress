<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">User Level <span> / List of user level admin</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<table	class="table data-table-footable-userLevel"
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
<div class="modal fade" id="footable-editor-modal-userLevel">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="footable-editor-userLevel">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title-userLevel">Add/Edit Data</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="levelName" class="col-sm-12 control-label">Level Name</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="levelName" name="levelName" placeholder="Level Name">
					</div>
				</div>
				<div class="form-group border-bottom pb-10">
					<label for="notes" class="col-sm-12 control-label">Notes</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="notes" name="notes" placeholder="Notes">
					</div>
				</div>
				<ul class="nav nav-tabs" id="tabsPanel">
					<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#notificationListTab"><i class="fa fa-bell"></i> Notification</a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#actionPermissionTab"><i class="fa fa-list-ul"></i> Action Permission</a></li>
				</ul>
				<div class="tab-content pt-20">
					<div class="tab-pane fade active show" id="notificationListTab">
						<div class="adomx-checkbox-radio-group pl-15" id="containerNotificationList">
							<label class="adomx-checkbox">
								<input type="checkbox" name="notificationType[]" value="1" class="cbNotificationType"> <i class="icon"></i> Mail
							</label>
							<label class="adomx-checkbox">
								<input type="checkbox" name="notificationType[]" value="2" class="cbNotificationType"> <i class="icon"></i> Reservation
							</label>
							<label class="adomx-checkbox">
								<input type="checkbox" name="notificationType[]" value="3" class="cbNotificationType"> <i class="icon"></i> Driver Schedule
							</label>
							<label class="adomx-checkbox">
								<input type="checkbox" name="notificationType[]" value="4" class="cbNotificationType"> <i class="icon"></i> Car Schedule
							</label>
							<label class="adomx-checkbox">
								<input type="checkbox" name="notificationType[]" value="5" class="cbNotificationType"> <i class="icon"></i> Additional Cost
							</label>
							<label class="adomx-checkbox">
								<input type="checkbox" name="notificationType[]" value="7" class="cbNotificationType"> <i class="icon"></i> Additional Income
							</label>
							<label class="adomx-checkbox">
								<input type="checkbox" name="notificationType[]" value="6" class="cbNotificationType"> <i class="icon"></i> Finance
							</label>
						</div>
					</div>
					<div class="tab-pane fade" id="actionPermissionTab">
						<div class="adomx-checkbox-radio-group pl-15" id="containerActionPermission">
							<label class="adomx-checkbox">
								<input type="checkbox" name="actionPermission[]" value="PMSADDDRIVERSCHEDULE" class="cbActionPermission"> <i class="icon"></i> Add Driver Schedule
							</label>
							<label class="adomx-checkbox">
								<input type="checkbox" name="actionPermission[]" value="PMSDELETEDRIVERSCHEDULE" class="cbActionPermission"> <i class="icon"></i> Delete Driver Schedule
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idUserLevel" name="idUserLevel" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/Setting/userLevel.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>