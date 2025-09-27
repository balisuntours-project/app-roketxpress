<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Template Auto Cost <span> / List of templates to detect reservation cost details automatically</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-primary button-sm pull-right btn-block data-action="insert" data-toggle="modal" data-target="#modal-editor-templateAutoCost">
				<span><i class="fa fa-plus"></i>New Data</span>
			</button>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body pb-1">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<input type="text" class="form-control" id="keywordSearch" name="keywordSearch" autocomplete="off" placeholder="Type something and push ENTER to search">
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box mt-2">
	<div class="box-body pt-1">
		<div class="row mt-25">
			<div class="col-sm-12 responsive-table-container">
				<table class="table" id="table-templateAutoCost">
					<thead class="thead-light">
						<tr>
							<th width="180">Template Name</th>
							<th>Cost Details</th>
							<th>Reservation Title Keywords</th>
							<th width="40"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4" class="text-center">No data found</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-editor-templateAutoCost">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="editor-templateAutoCost" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-templateAutoCost">Add New Template Auto Cost</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 pb-10 mb-10" style="border-bottom: 1px solid #e0e0e0;">
						<div class="form-group">
							<label for="optionVendorEditor" class="control-label">Template Name</label>
							<input type="text" class="form-control" id="templateAutoCostNameEditor" name="templateAutoCostNameEditor" placeholder="Type for template name">
						</div>
					</div>
					<div class="col-sm-6" style="border-right: 1px solid #e0e0e0;">
						<h6 class="mb-10">Detail Cost</h6>
						<ul class="list-group mb-10" id="containerListProduct"></ul>
						<button type="button" class="button button-primary button-xs" 
						 data-actionUrl="" 
						 data-triggerFrom="dialog" 
						 data-arrExceptionTicket="" 
						 data-arrExceptionTransport="" 
						 data-idAutoDetailsTemplateidAutoDetailsTemplateidAutoDetailsTemplate="0" 
						 data-autoDetailsTemplateName="" 
						 data-toggle="modal" 
						 data-target="#modal-editor-templateAutoCostDetail">
							<span><i class="fa fa-plus"></i>Add Cost Detail</span>
						</button>
					</div>
					<div class="col-sm-6">
						<h6 class="mb-10">Keywords</h6>
						<ul class="list-group mb-10" id="containerListKeyword"></ul>
						<button type="button" class="button button-primary button-xs" 
						 data-actionUrl="" 
						 data-triggerFrom="dialog" 
						 data-idAutoDetailsTemplate="0" 
						 data-autoDetailsTemplateName="" 
						 data-toggle="modal" 
						 data-target="#modal-editor-templateAutoCostKeyword">
							<span><i class="fa fa-plus"></i>Add Keyword</span>
						</button>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-editor-templateAutoCostDetail">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-templateAutoCostDetail" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-templateAutoCostDetail">Add new cost detail</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 pb-10 mb-10" style="border-bottom: 1px solid #e0e0e0;"><p>Template Name : <b id="textTemplateNameCostDetail"></b></p></div>
					<div class="col-sm-12">
						<div class="form-group">
							<label for="optionCostType" class="control-label">Cost Type</label>
							<select id="optionCostType" name="optionCostType" class="form-control">
								<option value="2">Transport</option>
								<option value="1">Ticket</option>
							</select>
						</div>
					</div>
					<div class="col-sm-12 d-none" id="containerOptionTicketProduct">
						<div class="form-group">
							<label for="optionTicketProduct" class="control-label">Ticket Product</label>
							<select id="optionTicketProduct" name="optionTicketProduct" class="form-control"></select>
						</div>
					</div>
					<div class="col-sm-12" id="containerOptionTransportProduct">
						<div class="form-group">
							<label for="optionTransportProduct" class="control-label">Transport Product</label>
							<select id="optionTransportProduct" name="optionTransportProduct" class="form-control"></select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idAutoDetailsTemplateCostDetail" name="idAutoDetailsTemplateCostDetail" value="">
				<button type="submit" class="button button-primary" id="btnSaveAutoDetailsTemplateDetail">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-editor-templateAutoCostKeyword">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-templateAutoCostKeyword" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-templateAutoCostKeyword">Add new template keyword</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12"><p>Template Name : <b id="textTemplateNameKeyword"></b></p></div>
					<div class="col-sm-12">
						<div class="form-group">
							<input type="text" class="form-control mt-10 mb-10" id="templateAutoCostKeyword" name="templateAutoCostKeyword" placeholder="Type keyword for template">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idAutoDetailsTemplateKeyword" name="idAutoDetailsTemplateKeyword" value="">
				<button type="submit" class="button button-primary" id="btnSaveAutoDetailsTemplateKeyword">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-editor-templateAutoCostName">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-templateAutoCostName" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-templateAutoCostName">Edit template name</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<input type="text" class="form-control mt-10 mb-10" id="templateAutoCostName" name="templateAutoCostName" placeholder="Type for template name">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idAutoDetailsTemplateEditName" name="idAutoDetailsTemplateEditName" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/ProductSetting/templateAutoCost.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
