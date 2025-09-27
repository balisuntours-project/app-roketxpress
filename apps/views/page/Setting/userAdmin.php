<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">User Admin <span> / List of user web admin</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<table	class="table data-table-footable-userAdmin"
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
<div class="modal fade" id="footable-editor-modal-userAdmin">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="footable-editor-userAdmin">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title-userAdmin">Add New Data</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body row">
				<div class="col-lg-5 col-sm-12 form-group">
					<label for="optionUserLevel" class="control-label">User Level</label>
					<select id="optionUserLevel" name="optionUserLevel" class="form-control"></select>
				</div>
				<div class="col-lg-7 col-sm-12 form-group required">
					<label for="nameUser" class="control-label">Name</label>
					<input type="text" class="form-control" id="nameUser" name="nameUser" placeholder="Name">
				</div>
				<div class="col-lg-5 col-sm-12 form-group">
					<label for="optionReservationType" class="control-label">Reservation Type</label>
					<select id="optionReservationType" name="optionReservationType" class="form-control" option-all="All Reservation Type" option-all-value="0"></select>
				</div>
				<div class="col-lg-7 col-sm-12 form-group">
					<label for="userEmail" class="control-label">Email</label>
					<input type="text" class="form-control" id="userEmail" name="userEmail" placeholder="Email">
				</div>
				<div class="col-sm-12 pb-20 form-group border-bottom form-group required">
					<label for="username" class="control-label">Username</label>
					<input type="text" class="form-control" id="username" autocomplete="off" name="username" placeholder="Username">
				</div>
				<div class="col-sm-12 mb-20">
					<label class="adomx-checkbox"><input type="checkbox" name="checkboxPartnerContact" id="checkboxPartnerContact" value="1"> <i class="icon"></i> Available for partner contact</label>
				</div>
				<div class="col-sm-12 pb-10 form-group border-bottom">
					<div class="form-group">
						<label for="whatsappNumber" class="control-label">WhatsApp Number</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="prefix-whatsappNumber">+62</span>
							</div>
							<input type="text" class="form-control maskNumber nocomma" id="whatsappNumber" name="whatsappNumber" placeholder="WhatsApp Number" onkeyup="maskNumberInput(8, false, 'whatsappNumber')" aria-describedby="prefix-whatsappNumber" maxlength="17">
						</div>
					</div>
				</div>
				<div class="col-sm-12 mb-10 pb-10">
					<div class="alert alert-primary" role="alert">
						<i class="zmdi zmdi-info"></i> <span>Please fill in password form if you want to change password</span>
					</div>
				</div>
				<div class="col-sm-12 form-group" id="oldPasswordContainer">
					<label for="oldUserPassword" class="control-label">Old Password</label>
					<input type="password" class="form-control" id="oldUserPassword" autocomplete="new-password" name="oldUserPassword" placeholder="Old Password">
				</div>
				<div class="col-lg-6 col-sm-12 form-group">
					<label for="newUserPassword" class="control-label">New Password</label>
					<input type="password" class="form-control" id="newUserPassword" autocomplete="new-password" name="newUserPassword" placeholder="Password">
				</div>
				<div class="col-lg-6 col-sm-12 form-group">
					<label for="repeatUserPassword" class="control-label">Repeat Password</label>
					<input type="password" class="form-control" id="repeatUserPassword" autocomplete="new-password" name="repeatUserPassword" placeholder="Repeat Password">
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idUser" name="idUser" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/Setting/userAdmin.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>