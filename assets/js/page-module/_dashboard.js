var DashboarfdChartjs, ChartElem;
if (dashboardFunc == null){
	var dashboardFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			getDataDashboard();
		});
	}
}

$('#optionMonth, #optionYear').off('change');
$('#optionMonth, #optionYear').on('change', function(e) {
	$("#chartjs-statistic").remove();
	getDataDashboard();
});

function getDataDashboard(){
	
	var strmonth	=	$('#optionMonth option:selected').text(),
		month		=	$('#optionMonth').val(),
		year		=	$('#optionYear').val(),
		dataSend	=	{month:month, year:year};

	$.ajax({
		type: 'POST',
		url: baseURL+"dashboard/getDataDashboard",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#totalAllTime, #totalThisMonth, #totalToday, #totalTomorrow").html("0");
			$("#bodyTopSourceReservation").html("<tr><td colspan='2'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
			DashboarfdChartjs	=	null;
			$(".chartjs-revenue-statistics-chart").html('<canvas id="chartjs-statistic"></canvas>');
		},
		success:function(response){

			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			var dataReservation	=	response.dataReservation,
				dataSource		=	response.dataSource;
			
			if(dataReservation != undefined || dataReservation.length != 0){
				$("#totalAllTime").html(numberFormat(dataReservation.TOTALRESERVATIONALLTIME));
				$("#totalThisMonth").html(numberFormat(dataReservation.TOTALRESERVATIONTHISMONTH));
				$("#totalToday").html(numberFormat(dataReservation.TOTALRESERVATIONTODAY));
				$("#totalTomorrow").html(numberFormat(dataReservation.TOTALRESERVATIONTOMORROW));
				$("#percentageThisMonth").html(dataReservation.PERCENTAGETHISMONTH);
				$("#percentageToday").html(dataReservation.PERCENTAGETODAY);
				$("#percentageTomorrow").html(dataReservation.PERCENTAGETOMORROW);
				
				$("#progessbarThisMonth").css("width", dataReservation.PERCENTAGETHISMONTHSTYLE+"%");
				$("#progessbarToday").css("width", dataReservation.PERCENTAGETODAY+"%");
				$("#progessbarTomorrow").css("width", dataReservation.PERCENTAGETOMORROW+"%");
				
			}
			
			if(dataSource !== false && dataSource.length > 0){
				
				var trTopSource	=	"";
				$.each(dataSource, function(index, array) {
					
					styleNoBorder	=	index == 0 ? "border-top:none !important" : "";
					trTopSource		+=	'<tr>'+
											'<td valign="middle" style="'+styleNoBorder+'">'+
												'<div class="image"><img width="80px" src="'+array.LOGOURL+'" alt=""></div>'+
											'</td>'+
											'<td style="'+styleNoBorder+'">'+
												'<h4 class="topSourceContent">'+array.SOURCENAME+'</h4>'+
												'<p class="topSourceContent mt-5">'+numberFormat(array.AVERAGERESERVATIONPERMONTH)+' Avg Reservation / month</p>'+
												'<small class="topSourceContent">'+numberFormat(array.TOTALRESERVATIONOFMONTH)+' Reservation at selected period</small>'+
											'</td>'+
										'</tr>';
					
				});
				
				$("#bodyTopSourceReservation").html(trTopSource);
			} else {
				$("#bodyTopSourceReservation").html("<tr><td class='text-center'>No data found</td></tr>");
			}
			
			if(response.dataStatistic !== false){
				if( $('#chartjs-statistic').length ) {
					var canvas 		=	document.getElementById('chartjs-statistic');
					ChartElem 		=	canvas.getContext('2d');
					ChartElem.clearRect(0, 0, ChartElem.canvas.width, ChartElem.canvas.height);
					
					var Chartconfig		= {
						type: 'line',
						data: {
							labels: response.dataStatistic.arrDates,
							datasets: response.dataStatistic.arrDetailData
						},
						options: {
							maintainAspectRatio: false,
							legend: {
								display: true,
								labels: {
									fontColor: '#aaaaaa',
								}
							},
							tooltips: {
								mode: 'index',
								intersect: false,
								xPadding: 10,
								yPadding: 10,
								caretPadding: 10,
								cornerRadius: 4,
								titleMarginBottom: 4,
								displayColors: false,
								callbacks: {
									title: function(tooltipItems, data) {
										return tooltipItems[0].xLabel+' '+strmonth+' '+year;
									}
								}
							},
							scales: {
								xAxes: [{
									display: true,
									gridLines: {
										display: false
									},
									ticks: {
										fontColor: '#aaaaaa',
									},
								}],
								yAxes: [{
									display: true,
									labelString: 'probability',
									gridLines: {
										color: 'rgba(136,136,136,0.1)',
										lineWidth: 3,
										drawBorder: false,
										zeroLineWidth: 3,
										zeroLineColor: 'rgba(136,136,136,0.1)',
									},
									ticks: {
										padding: 15,
										stepSize: 10,
										fontColor: '#aaaaaa',
									},
								}]
							}
						}
					};
					DashboarfdChartjs = new Chart(ChartElem, Chartconfig);
				}
			}
		}
	});
	
}

dashboardFunc();