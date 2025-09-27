<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3>Agent Payment Balance<span> / Payment balance per agent statistic</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10"></div>
</div>
<div class="row">
	<div class="col-xlg-9 col-12">
		<div class="row">
			<div class="col-xlg-8 col-12 mb-30">
				<div class="box">
					<div class="box-head py-3">
						<h4 class="title">Income per Agent - Top 5 - Last 3 Months</h4>
					</div>
					<div class="box-body">
						<div id="echartbar-incomePerAgent" class="example-echart-bar-dataset example-echarts echartbar-incomePerAgent"></div>
					</div>
				</div>
			</div>
			<div class="col-xlg-4 col-12 mb-30">
				<div class="box">
					<div class="box-head">
						<h4 class="title">Balance Portion</h4>
					</div>
					<div class="box-body">
						<div id="example-echart-doughnut-chart" class="example-echart-doughnut-chart example-echarts" style="height: 300px;"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="box">
					<div class="box-head">
						<h4 class="title">Payment History - Last 3 Month Statistics</h4>
					</div>
					<div class="box-body">
						<div class="example-chartjs" style="height: 300px;">
							<canvas id="example-chartjs-line"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xlg-3 col-12">
		<div class="box">
			<div class="box-head py-3">
				<h4 class="title">Revenue</h4>
			</div>
			<div class="box-body">
				<div class="row py-3 border-bottom">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ACCOUNTING_APP?>img/calendar/number-12.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">Yesterday</h6><span>12 August 2024</span></div>
					<div class="col-5 text-right"><h6>12,000,000</h6></div>
				</div>
				<div class="row py-3 border-bottom">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ACCOUNTING_APP?>img/calendar/number-13.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">Today</h6><span>13 August 2024</span></div>
					<div class="col-5 text-right"><h6>17,700,000</h6></div>
				</div>
				<div class="row py-3 border-bottom">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ACCOUNTING_APP?>img/calendar/july.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">Last Month</h6><span>July 2024</span></div>
					<div class="col-5 text-right"><h6>369,635,000</h6></div>
				</div>
				<div class="row py-3">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ACCOUNTING_APP?>img/calendar/august.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">This Month</h6><span>August 2024</span></div>
					<div class="col-5 text-right"><h6>411,876,000</h6></div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/Report/agentPaymentBalance.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>