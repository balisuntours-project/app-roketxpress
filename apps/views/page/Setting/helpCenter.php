<link rel="stylesheet" href="<?=BASE_URL_ASSETS?>css/summernote-bs4.css" rel="stylesheet" type="text/css">
<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Help Center <span> / Help center content settings for driver and vendor</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button type="button" class="button button-success button-sm pull-right" id="btnAddNewCategory" data-toggle="modal" data-target="#modal-helpCenterCategory" data-action="insert"><span><i class="fa fa-plus"></i>New Category</span></button>
		</div>
	</div>
</div>
<div class="box mt-20">
	<div class="box-body">
		<div class="row mt-10">
			<div class="col-lg-4 col-sm-6 mb-10">
				<div class="form-group">
					<label for="optionPartnerType" class="control-label">Partner Type</label>
					<select id="optionPartnerType" name="optionPartnerType" class="form-control">
						<option value="1">Vendor</option>
						<option value="2">Driver</option>
					</select>
				</div>
			</div>
			<div class="col-lg-8 col-sm-6 mb-10">
				<div class="form-group">
					<label for="searchKeyword" class="control-label">Search by category / article title / article content</label>
					<input type="text" class="form-control" id="searchKeyword" name="searchKeyword" placeholder="Type something and press ENTER to search">
				</div>
			</div>
			<div class="col-sm-12 mb-10 accordion accordion-icon" style="max-height:600px; overflow-y:scroll" id="bodyHelpCenterContentList"></div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-helpCenterCategory">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-helpCenterCategory">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-helpCenterCategory">Data Category Editing</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-5">
					<div class="form-group col-sm-7 required">
						<label for="optionPartnerTypeEditor" class="control-label">Vendor Type</label>
						<select id="optionPartnerTypeEditor" name="optionPartnerTypeEditor" class="form-control">
							<option value="1">Vendor</option>
							<option value="2">Driver</option>
						</select>
					</div>
					<div class="form-group col-sm-5 required">
						<label for="iconCode" class="control-label">Icon Code <a class="pull-right text-warning" href="https://fontawesome.com/v4/cheatsheet/" target="_blank">Code List</a></label>
						<input type="text" class="form-control" id="iconCode" name="iconCode" placeholder="Icon Code" maxlength="50">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12 required">
						<label for="categoryName" class="control-label">Category Name</label>
						<input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Category Name" maxlength="100">
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12">
						<label for="description" class="control-label">Description</label>
						<input type="text" class="form-control" id="description" name="description" placeholder="Description" maxlength="255">
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
<div class="modal fade" id="modal-helpCenterArticle">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-helpCenterArticle">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-helpCenterArticle">Data Article Editing</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row mb-10">
					<div class="col-sm-12">
						<h6>Partner Type : <span id="helpCenterPartnerTypeStr"></span></h6>
						<h6>Category : <span id="helpCenterCategoryStr"></span></h6>
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12 col-lg-9 required">
						<label for="articleTitle" class="control-label">Article Title</label>
						<input type="text" class="form-control" id="articleTitle" name="articleTitle" placeholder="Article Title" maxlength="100">
					</div>
					<div class="form-group col-sm-12 col-lg-3 required">
						<label for="articleTitle" class="control-label">View Status</label>
						<div class="adomx-checkbox-radio-group">
							<label class="adomx-radio-2 primary my-1 mt-2"><input type="radio" value="1" name="radioStatusView"> <i class="icon"></i> Seen</label>
							<label class="adomx-radio-2 primary my-1"><input type="radio" value="0" name="radioStatusView"> <i class="icon"></i> Hidden</label>
						</div>
					</div>
				</div>
				<div class="row mb-5">
					<div class="form-group col-sm-12 required">
						<label for="articleContent" class="control-label">Article Content</label>
						<textarea id="articleContent" name="articleContent" class="summernote"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idHelpCenterCategory" name="idHelpCenterCategory" value="">
				<input type="hidden" id="idHelpCenterArticle" name="idHelpCenterArticle" value="">
				<input type="hidden" id="actionTypeArticle" name="actionTypeArticle" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<style>
	.accordion.accordion-icon .card .card-header h2 button::before, .accordion.accordion-icon .card .card-header h2 button::after{
		top: 65% !important;
		right: 2.2%;
	}
</style>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/summernote-bs4.min.js";
	$.getScript(url, function(){
		var urlMainScript = "<?=BASE_URL_ASSETS?>js/page-module/Setting/helpCenter.js?<?=date('YmdHis')?>";
		$.getScript(urlMainScript);
	});
</script>