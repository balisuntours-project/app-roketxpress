<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Knowledge <span> / List of guide and knowledge content</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<ul class="nav nav-tabs border-bottom pb-10 mb-10" id="knowledge-tabsPanel"></ul>
		<div class="tab-content" id="knowledge-tabsContent"></div>
	</div>
</div>
<div class="box mt-10">
	<div class="box-body px-20 py-10">
		<div class="text-right mb-10 px-4">
			<a id="knowledge-btnNewTab" target="_blank" class="btn btn-sm btn-info ml-auto text-white">
				Open In New Tab <i class="fa fa-external-link"></i>
			</a>
		</div>
		<div class="text-center mb-10 px-4">
			<button id="knowledge-zoom-in" class="btn btn-sm btn-primary">
				<i class="fa fa-search-plus"></i> Zoom In
			</button>
			<button id="knowledge-zoom-out" class="btn btn-sm btn-danger">
				Zoom Out <i class="fa fa-search-minus"></i>
			</button>
		</div>
		<div id="pdf-viewer-container" style="width: 100%; height: 900px; overflow: auto;" data-urlFilePdf=""></div>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/knowledge.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>