<div class="row justify-content-between align-items-center">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Currency Exchange <span> / Setting currency exchange rate per period</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-primary button-sm pull-right btn-block" data-toggle="modal" data-target="#modal-editor-currencyExchange" data-action="insert">
				<span><i class="fa fa-plus"></i>New Data</span>
			</button>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-12">
				<div class="row">
					<div class="col-lg-9 col-sm-12 mb-10">
						<h5>Current Exchange : <span id="currentExchange">0</span></h5>
						<span id="tableDataCountCurrencyExchange"></span>
					</div>
					<div class="col-lg-3 col-sm-12 mb-10 text-right">
						<select class="form-control" id="optionCurrency" name="optionCurrency">
							<option value='USD'>U.S Dollar</option>
						</select>
					</div>
				</div>
				<div class="row mt-5 responsive-table-container">
					<table class="table" id="table-currencyExchange">
						<thead class="thead-light">
							<tr>
								<th>Date Start</th>
								<th width="140" class="text-right">Exchange</th>
								<th width="120" class="text-right"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="3" class="text-center">No data found</th>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row mt-20">
					<div class="col-sm-12 mb-10">
						<ul class="pagination" id="tablePaginationCurrencyExchange"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-editor-currencyExchange">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-currencyExchange">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-currencyExchange">Add/Edit Currency Exchange</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group required">
							<label for="optionCurrencyEditor" class="control-label">Currency</label>
							<select class="form-control" id="optionCurrencyEditor" name="optionCurrencyEditor">
								<option value='USD'>U.S Dollar</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group required">
							<label for="dateStart" class="control-label">Date Start</label>
							<input type="text" class="form-control input-date-single text-center" id="dateStart" name="dateStart" value="<?=date('d-m-Y')?>">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group required">
							<label for="exchangeValue" class="control-label">Exchange Value</label>
							<input type="text" class="form-control mb-10 text-right" id="exchangeValue" name="exchangeValue" onkeypress="maskNumberInput(1, 99999, 'exchangeValue')" value="1">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idCurrencyExchange" name="idCurrencyExchange" value="">
				<input type="hidden" id="originDateStart" name="originDateStart" value="">
				<input type="hidden" id="actionType" name="actionType" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url 	=	"<?=BASE_URL_ASSETS?>js/page-module/Finance/currencyExchange.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>