<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Data Master <span> / Main data related to reservation</span></h3>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<ul class="nav nav-tabs mb-15" id="tabsPanel">
			<li class="nav-item"><a class="tabMaster nav-link active" data-toggle="tab" href="#sourceTab" data-table="masterSource"><i class="fa fa-tag"></i> Source</a></li>
			<li class="nav-item"><a class="tabMaster nav-link" data-toggle="tab" href="#productTab" data-table="masterProduct"><i class="fa fa-product-hunt"></i> Product</a></li>
			<li class="nav-item"><a class="tabMaster nav-link" data-toggle="tab" href="#driverTab" data-table="masterDriver"><i class="fa fa-id-card-o"></i> Driver</a></li>
			<li class="nav-item"><a class="tabMaster nav-link" data-toggle="tab" href="#vendorTab" data-table="masterVendor"><i class="fa fa-address-card"></i> Vendor</a></li>
			<li class="nav-item"><a class="tabMaster nav-link" data-toggle="tab" href="#carTypeTab" data-table="masterCarType"><i class="fa fa-car"></i> Car Type</a></li>
		</ul>
		<div class="row mt-20">
			<div class="col-sm-12 mb-20">
				<input type="text" class="form-control" id="keywordSearch" name="keywordSearch" placeholder="Type something and press ENTER to search">
			</div>
			<div class="col-lg-8 col-sm-12 mb-10">
				<span id="tableDataCount"></span>
			</div>
			<div class="col-lg-4 col-sm-12 mb-10 text-right">
				<button class="hidden button button-primary button-sm" id="rankDriverOrder"><span><i class="fa fa-bars"></i>Driver Rank Order</span></button>
				<button class="button button-success button-sm" id="addData"><span><i class="fa fa-plus"></i>Add New</span></button>
			</div>
		</div>
		<div class="tab-content">
			<div class="responsive-table-container tab-pane fade show active" id="sourceTab"><?=$viewMasterSource?></div>
			<div class="responsive-table-container tab-pane fade" id="productTab"><?=$viewMasterProduct?></div>
			<div class="responsive-table-container tab-pane fade" id="driverTab"><?=$viewMasterDriver?></div>
			<div class="responsive-table-container tab-pane fade" id="vendorTab"><?=$viewMasterVendor?></div>
			<div class="responsive-table-container tab-pane fade" id="carTypeTab"><?=$viewMasterCarType?></div>
		</div>
		<div class="row mt-50">
			<div class="col-sm-12 mb-10">
				<ul class="pagination" id="tablePagination"></ul>
			</div>
		</div>
	</div>
</div>
<script>
	var dateToday	=	'<?=date("d-m-Y")?>',
		levelUser	=	'<?=$levelUser?>',
		url			=	"<?=BASE_URL_ASSETS?>js/page-module/dataMaster.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>