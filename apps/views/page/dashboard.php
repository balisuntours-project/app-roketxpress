<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center mb-10">
   <div class="col-12 col-lg-auto mb-20">
	  <div class="page-heading">
		 <h3>Dashboard<span> / Summary and statistics of reservations for the specified period</span></h3>
	  </div>
   </div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<div class="form-group mr-10">
				<label for="optionMonth" class="control-label">Schedule Period</label>
				<select class="form-control" id="optionMonth" name="optionMonth"></select>
			</div>
			<div class="form-group">
				<label for="optionYear" class="control-label">.</label>
				<select class="form-control" id="optionYear" name="optionYear"></select>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xlg-3 col-md-6 col-12 mb-30">
		<div class="top-report">
			<div class="head">
				<h4>All Time</h4>
				<span class="view"><i class="fa fa-clock-o"></i></span>
			</div>
			<div class="content">
				<h2 id="totalAllTime">0</h2>
			</div>
			<div class="footer">
				<div class="progess">
					<div class="progess-bar" style="width: 100%;"></div>
				</div>
				<p>Total all time reservation</p>
			</div>
		</div>
	</div>
	<div class="col-xlg-3 col-md-6 col-12 mb-30">
		<div class="top-report">
			<div class="head">
				<h4>This Month</h4>
				<span class="view"><i class="fa fa-calendar"></i></span>
			</div>
			<div class="content">
				<h2 id="totalThisMonth">0</h2>
			</div>
			<div class="footer">
				<div class="progess">
					<div class="progess-bar" id="progessbarThisMonth"></div>
				</div>
				<p><span id="percentageThisMonth"></span>% compared to last month</p>
			</div>

		</div>
	</div>
	<div class="col-xlg-3 col-md-6 col-12 mb-30">
		<div class="top-report">
			<div class="head">
				<h4>Today</h4>
				<span class="view"><i class="fa fa-calendar-o"></i></span>
			</div>
			<div class="content">
				<h2 id="totalToday">0</h2>
			</div>
			<div class="footer">
				<div class="progess">
					<div class="progess-bar" id="progessbarToday"></div>
				</div>
				<p><span id="percentageToday"></span>% of reservation this month</p>
			</div>
		</div>
	</div>
	<div class="col-xlg-3 col-md-6 col-12 mb-30">
		<div class="top-report">
			<div class="head">
				<h4>Tomorrow</h4>
				<span class="view"><i class="fa fa-share"></i></span>
			</div>
			<div class="content">
				<h2 id="totalTomorrow">0</h2>
			</div>
			<div class="footer">
				<div class="progess">
					<div class="progess-bar" id="progessbarTomorrow"></div>
				</div>
				<p><span id="percentageTomorrow"></span>% of reservation this month</p>
			</div>
		</div>
	</div>
</div>
<div class="row mbn-30">
	<div class="col-md-8 mb-30">
		<div class="box">
			<div class="box-head">
				<h4 class="title">Reservation Summary</h4>
			</div>
			<div class="box-body">
				<div class="chartjs-revenue-statistics-chart">
					<canvas id="chartjs-statistic"></canvas>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 mb-30">
		<div class="box">
			<div class="box-head">
				<h4 class="title">Top Source Reservation</h4>
			</div>
			<div class="box-body">
				<div class="tableFixHead">
					<table class="table">
						<tbody id="bodyTopSourceReservation"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/_dashboard.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>