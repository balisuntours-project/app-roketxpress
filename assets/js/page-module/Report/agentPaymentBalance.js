if (agentPaymentBalanceFunc == null) {
    var agentPaymentBalanceFunc = function () {
        $(document).ready(function () {
            getDataAgentPaymentBalance();
        });
    };
}
function getDataAgentPaymentBalance() {
	$.ajax({
		type: 'POST',
		url: baseURL+"report/agentPaymentBalance/getDataStatsAgentPayment",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			
			var dataIncomePerAgent	=	response.dataIncomePerAgent;
			generateEchartbarIncomePerAgent(dataIncomePerAgent);
		}
	});
}

function generateEchartbarIncomePerAgent(){
	var $echartbarIncomePerAgent = $('.echartbar-incomePerAgent');
	if ($echartbarIncomePerAgent.length) {
		var $echartbarIncomePerAgentId = $echartbarIncomePerAgent.attr('id');
		var $echartbarIncomePerAgentActive = echarts.init(document.getElementById($echartbarIncomePerAgentId));
		var option = {
			legend: {
				textStyle: {
					color: '#aaaaaa'
				}
			},
			tooltip: {},
			dataset: {
				source: [
					['Period', 'Income', 'Expense', 'Margin'],
					['Yesterday', 43.3, 85.8, 93.7],
					['Today', 83.1, 73.4, 55.1],
					['last Month', 86.4, 65.2, 82.5],
					['This Month', 72.4, 53.9, 39.1]
				]
			},
			xAxis: {
				type: 'category',
				axisTick: {
					show: false,
				},
				axisLine: {
					show: false,
				},
				axisLabel: {
					color: '#aaaaaa',
				},
			},
			yAxis: {
				type: 'value',
				axisTick: {
					show: false,
				},
				axisLine: {
					lineStyle: {
						color: 'rgba(136,136,136,0.2)',
					}
				},
				axisLabel: {
					color: '#aaaaaa',
				},
				splitLine: {
					lineStyle: {
						color: 'rgba(136,136,136,0.2)',
					}
				},
			},
			series: [
				{ type: 'bar' },
				{ type: 'bar' },
				{ type: 'bar' }
			]
		};
		$echartbarIncomePerAgentActive.setOption(option);
	}
}

agentPaymentBalanceFunc();