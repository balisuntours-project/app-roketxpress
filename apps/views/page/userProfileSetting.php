<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/summernote-bs4.css" rel="stylesheet" type="text/css">
<div class="row mbn-50">
	<div class="col-12 mb-50">
		<div class="author-top">
			<div class="inner">
				<div class="author-profile">
					<div class="image">
						<h2 id="str-userInitialName">-</h2>
					</div>
					<div class="info">
						<h5 id="str-userFullName">-</h5>
						<span><i class="badge badge-primary" id="str-userLevelName">-</i> &nbsp; <i class="badge badge-info" id="str-userEmailAddress">-</i></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xlg-4 col-12 mb-50">
		<div class="row mbn-30">
			<div class="col-xlg-12 col-lg-6 col-12 mb-30">
				<div class="box">
					<div class="box-head">
						<h3 class="title">User Information</h3>
					</div>
					<div class="box-body">
						<form class="form row" id="userProfileSetting-formUserInformation" action="#">
							<div class="col-12 form-group required">
								<label for="userProfileSetting-name" class="control-label">Name</label>
								<input type="text" class="form-control" id="userProfileSetting-name" name="name" placeholder="Name" disabled/>
							</div>
							<div class="col-12 form-group">
								<label for="userProfileSetting-email" class="control-label">Email</label>
								<input type="text" class="form-control" id="userProfileSetting-email" name="email" placeholder="Email" disabled/>
							</div>
							<div class="col-12 mt-10">
								<input type="submit" class="button button-primary button-sm" value="Update Information" id="userProfileSetting-btnSubmitUserInformation" disabled/>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-xlg-12 col-lg-6 col-12 mb-30">
				<div class="box">
					<div class="box-head">
						<h3 class="title">Login Credentials</h3>
					</div>
					<div class="box-body">
						<form class="form row" id="userProfileSetting-formUserCredential" action="#">
							<div class="col-12 form-group required">
								<label for="userProfileSetting-username" class="control-label">Username</label>
								<input type="text" class="form-control" id="userProfileSetting-username" autocomplete="off" name="username" placeholder="Username" disabled>
							</div>
							<div class="col-12 mt-10 mb-20">
								<div class="alert alert-warning" role="alert">
									<i class="fa fa-info"></i> Fill this form if you want to change your password
								</div>
							</div>
							<div class="col-12 form-group">
								<label for="userProfileSetting-oldPassword" class="control-label">Old Password</label>
								<input type="password" class="form-control" id="userProfileSetting-oldPassword" autocomplete="new-password" name="oldPassword" placeholder="Old Password" disabled>
							</div>
							<div class="col-12 form-group">
								<label for="userProfileSetting-newPassword" class="control-label">New Password</label>
								<input type="password" class="form-control" id="userProfileSetting-newPassword" autocomplete="new-password" name="newPassword" placeholder="New Password" disabled>
							</div>
							<div class="col-12 form-group">
								<label for="userProfileSetting-repeatPassword" class="control-label">Repeat New Password</label>
								<input type="password" class="form-control" id="userProfileSetting-repeatPassword" autocomplete="new-password" name="repeatPassword" placeholder="Repeat New Password" disabled>
							</div>
							<div class="col-12 mt-10">
								<input type="submit" class="button button-primary button-sm" value="Save Login Credentials" id="userProfileSetting-btnSubmitUserCredential" disabled/>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xlg-8 col-12 mb-50">
		<div class="box">
			<div class="box-head">
				<ul class="nav nav-tabs" id="tabsPanel">
					<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#mailTemplateSignatureTab"><i class="fa fa-envelope-o"></i> Mail Template & Signature</a></li>
				</ul>
			</div>
			<div class="box-body tab-content" style="height:860px; overflow-y:scroll;">
				<div class="tab-pane fade show active row" id="mailTemplateSignatureTab">
					<div class="col-12 pr-0">
						<button type="button" class="button button-info button-xs pull-right mb-5" id="btnAddMailTemplate" data-toggle="modal" data-target="#modal-mailTemplate" data-action="insert"><span><i class="fa fa-plus"></i>New Template</span></button>
						<input type="text" class="form-control w-100" id="searchKeyword" name="searchKeyword" placeholder="Type something and press ENTER to search">
					</div>
					<div class="col-12 pr-0 mt-40 mb-30 text-center" id="noDataMailMessageTemplate">
						<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px"/>
						<h5>No data</h5>
						<p>No email template and signature data was found in your account</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-mailTemplate">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-mailTemplate">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-mailTemplate">Template & Signature Editor</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body row">
				<div class="col-sm-12 mb-20">
					<div class="adomx-checkbox-radio-group inline">
						<label class="adomx-radio-2 primary my-1"><input type="radio" value="1" name="templateType" onclick="setTemplateSignature(true)"> <i class="icon"></i> Signature</label>
						<label class="adomx-radio-2 primary my-1"><input type="radio" value="0" name="templateType" onclick="setTemplateSignature(false)"> <i class="icon"></i> Template</label>
					</div>
				</div>
				<div class="col-sm-12 mb-20">
					<input type="text" class="form-control" id="templateName" name="templateName" placeholder="Template Name" maxlength="30">
				</div>
				<div class="col-sm-12 mb-20">
					<textarea id="templateContent" name="templateContent" class="summernote"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idMailMessageTemplate" name="idMailMessageTemplate" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/summernote-bs4.min.js";
	$.getScript(url, function(){
		var urlMainScript = "<?=BASE_URL_ASSETS?>js/page-module/userProfileSetting.js?<?=date('YmdHis')?>";
		$.getScript(urlMainScript);
	});
</script>