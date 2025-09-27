if (scheduleDriverMonitorFunc == null){
	var scheduleDriverMonitorFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			getDataScheduleDriverMonitor();
		});	
	}
}

$('#optionMonth, #optionYear').off('change');
$('#optionMonth, #optionYear').on('change', function(e) {
	getDataScheduleDriverMonitor();
});

function getDataScheduleDriverMonitor(){
	
	var $tableBody		=	$('#table-scheduleDriverMonitor > tbody'),
		columnNumber	=	$('#table-scheduleDriverMonitor > thead > tr > th').length;
		optionMonth		=	$('#optionMonth').val(),
		optionYear		=	$('#optionYear').val(),
		dataSend		=	{month:optionMonth, year:optionYear};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/scheduleDriverMonitor/getDataScheduleDriverMonitor",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var data			=	response.result,
				rows			=	"";
			
			if(data.length === 0){
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				$.each(data, function(index, array) {
					var statusStr	=	'-';
					switch(array.STATUS){
						case "-1"	:	statusStr	=	'<span class="badge badge-danger" style="padding-top:6px; padding-bottom:6px;">Unsufficient</span>'; break;
						case "0"	:	statusStr	=	'<span class="badge badge-primary" style="padding-top:6px; padding-bottom:6px;">No Schedule Yet</span>'; break;
						case "1"	:	statusStr	=	'<span class="badge badge-success" style="padding-top:6px; padding-bottom:6px;">Sufficient</span>'; break;
						default		:	break;
					}
					
					var btnEdit		=	'<button class="button button-xs button-box button-primary" onclick="getDetailEditDayOffQuota('+
											array.IDSCHEDULEDRIVERMONITOR+
											', \''+array.DATESCHEDULESTR+'\''+
											', '+array.TOTALSCHEDULE+
											', '+array.TOTALACTIVEDRIVER+
											', '+array.TOTALOFFDRIVER+
											', '+array.TOTALDAYOFFQUOTA+
										')">'+
											'<i class="fa fa-pencil"></i>'+
										'</button><br/>';
					if(array.STATUSDATE == 0 || array.STATUS == 0) btnEdit = '';
					rows			+=	"<tr>"+
											"<td align='center'>"+array.DATESCHEDULESTR+"</td>"+
											"<td>"+statusStr+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALSCHEDULE)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALACTIVEDRIVER)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALOFFDRIVER)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALDAYOFFQUOTA)+"</td>"+
											"<td align='center'>"+btnEdit+"</td>"+
										"</tr>";
				});
			}

			$tableBody.html(rows);
			
		}
	});
	
}

function getDetailEditDayOffQuota(idScheduleDriverMonitor, dateScheduleStr, totalSchedule, totalActiveDriver, totalOffDriver, totalDayOffQuota){
	$("#inputDayOffQuota-date").html(dateScheduleStr);
	$("#inputDayOffQuota-totalSchedule").html(numberFormat(totalSchedule));
	$("#inputDayOffQuota-totalActiveDriver").html(numberFormat(totalActiveDriver));
	$("#inputDayOffQuota-totalOffDriver").html(numberFormat(totalOffDriver));
	$("#dayOffQuota").val(numberFormat(totalDayOffQuota));
	$("#idScheduleDriverMonitor").val(idScheduleDriverMonitor);

	$('#editor-modal-inputDayOffQuota').modal('show');
}

$('#editor-inputDayOffQuota').off('submit');
$('#editor-inputDayOffQuota').on('submit', function(e) {
	
	e.preventDefault();
	var idScheduleDriverMonitor	=	$('#editor-modal-inputDayOffQuota #idScheduleDriverMonitor').val();
		dayOffQuota				=	$('#editor-modal-inputDayOffQuota #dayOffQuota').val();
		dataSend				=	{idScheduleDriverMonitor:idScheduleDriverMonitor, dayOffQuota:dayOffQuota};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/scheduleDriverMonitor/setDayOffQuotaPerDate",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-inputDayOffQuota :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-inputDayOffQuota :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#editor-modal-inputDayOffQuota').modal('hide');
				getDataScheduleDriverMonitor();
			}
			
		}
	});
});

scheduleDriverMonitorFunc();