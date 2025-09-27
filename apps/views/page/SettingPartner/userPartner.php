<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">User Partner <span> / List of user web partner</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<table	class="table data-table-footable-userPartner"
				data-paging="true"
				data-filtering="true"
				data-sorting="true"
				style="table-layout: fixed; overflow: hidden;"
				data-breakpoints='{ "xs": 480, "sm": 768, "md": 992, "lg": 1200, "xl": 1400 }'
				data-editing-add-text="New Data"
				data-editing-always-show="true"
				data-editing-show-text="<span class='fooicon fooicon-pencil' aria-hidden='true'></span> Data Editing"
				data-editing-hide-text="Cancel"
				>
		</table>
	</div>
</div>
<div class="modal fade" id="footable-editor-modal-userPartner">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="footable-editor-userPartner">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title-userPartner">Add New Data</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="optionPartner" class="col-sm-12 control-label">Partner</label>
					<div class="col-sm-12 mb-20">
						<select id="optionPartner" name="optionPartner" class="form-control"></select>
					</div>
				</div>
				<div class="form-group required">
					<label for="nameUser" class="col-sm-12 control-label">Name</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="nameUser" name="nameUser" placeholder="Name">
					</div>
				</div>
				<div class="form-group required">
					<label for="userEmail" class="col-sm-12 control-label">Email</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="userEmail" name="userEmail" placeholder="Email">
					</div>
				</div>
				<div class="form-group required">
					<label for="optionUserLevel" class="col-sm-12 control-label">User Level</label>
					<div class="col-sm-12 mb-20">
						<select id="optionUserLevel" name="optionUserLevel" class="form-control"></select>
					</div>
				</div>
				<div class="form-group required">
					<label for="username" class="col-sm-12 control-label">Username</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="username" autocomplete="new-password" name="username" placeholder="Username">
					</div>
				</div><br/>
				<p>Please fill in the password form if you want to change the password</p>
				<div class="form-group" id="oldPasswordContainer">
					<label for="oldUserPassword" class="col-sm-12 control-label">Old Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="oldUserPassword" autocomplete="new-password" name="oldUserPassword" placeholder="Old Password">
					</div>
				</div>
				<div class="form-group">
					<label for="newUserPassword" class="col-sm-12 control-label">New Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="newUserPassword" autocomplete="new-password" name="newUserPassword" placeholder="Password">
					</div>
				</div>
				<div class="form-group">
					<label for="repeatUserPassword" class="col-sm-12 control-label">Repeat Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="repeatUserPassword" autocomplete="new-password" name="repeatUserPassword" placeholder="Repeat Password">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idUserPartner" name="idUserPartner" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/SettingPartner/userPartner.js";
	$.getScript(url);
</script>