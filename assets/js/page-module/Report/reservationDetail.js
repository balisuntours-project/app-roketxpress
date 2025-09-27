if (reservationDetailFunc == null){
	var reservationDetailFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionReservationType', 'dataReservationType');
			getDataReservationDetail();
		});	
	}
}

$('#filterDataReservationDetail').off('click');
$('#filterDataReservationDetail').on('click', function(e) {
	getDataReservationDetail();
});

function generateDataTable(page){
	getDataReservationDetail(page);
}

function getDataReservationDetail (page = 1){
	
	var $tableBody		=	$('#table-reservationDetail > tbody'),
		columnNumber	=	$('#table-reservationDetail > thead > tr > th').length,
		reservationType	=	$('#optionReservationType').val(),
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		dataSend		=	{page:page, reservationType:reservationType, startDate:startDate, endDate:endDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"report/reservationDetail/getDataReservationDetail",
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
			
			var data			=	response.result.data,
				rows			=	"";
			
			if(data.length === 0){
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$.each(data, function(index, array) {
						
					var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>';
					var inputType				=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					
					var	reservationDateEnd	=	"";
					if(array.DURATIONOFDAY > 1){
						reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
					}
					
					var	carSchedule			=	"";
					if(array.CARSCHEDULE != ""){
						$.each(array.CARSCHEDULE, function(indexCarSch, arrayCarSchedule) {
							carSchedule		+=	"<b>"+arrayCarSchedule.SCHEDULEDATE+"</b><br/>"+
												"<b>"+arrayCarSchedule.PRODUCTNAME+"</b><br/>"+
												"Vendor : "+arrayCarSchedule.NAME+"<br/>"+
												arrayCarSchedule.CARDETAILS+"<br/><br/>";
						});
					}
					
					var	driverSchedule			=	"";
					if(array.DRIVERSCHEDULE != ""){
						$.each(array.DRIVERSCHEDULE, function(indexDriverSch, arrayDriverSchedule) {
							driverSchedule	+=	"<b>"+arrayDriverSchedule.SCHEDULEDATE+"</b><br/>"+
												"<b>"+arrayDriverSchedule.PRODUCTNAME+"</b><br/>"+
												"["+arrayDriverSchedule.DRIVERTYPE+"] "+arrayDriverSchedule.NAME+"<br/><br/>";
						});
					}
					
					var	ticketList			=	"";
					if(array.TICKETLIST != ""){
						$.each(array.TICKETLIST, function(indexTicketList, arrayTicketList) {
							ticketList		+=	"<b>"+arrayTicketList.SCHEDULEDATE+"</b><br/>"+
												"<b>"+arrayTicketList.PRODUCTNAME+"</b><br/>"+
												"<b>Vendor : "+arrayTicketList.NAME+"</b><br/>"+
												"<div class='order-details-customer-info mb-10'>"+
													"<ul class='ml-5'>"+
														"<li> <span>Adult</span> <span>"+arrayTicketList.PAXADULT+" x "+numberFormat(arrayTicketList.PRICEPERPAXADULT)+" = "+numberFormat(arrayTicketList.PRICETOTALADULT)+"</span> </li>"+
														"<li> <span>Child</span> <span>"+arrayTicketList.PAXCHILD+" x "+numberFormat(arrayTicketList.PRICEPERPAXCHILD)+" = "+numberFormat(arrayTicketList.PRICETOTALCHILD)+"</span> </li>"+
														"<li> <span>Infant</span> <span>"+arrayTicketList.PAXINFANT+" x "+numberFormat(arrayTicketList.PRICEPERPAXINFANT)+" = "+numberFormat(arrayTicketList.PRICETOTALINFANT)+"</span> </li>"+
													"</ul>"+
												"</div>";
						});
					}
						
					rows	+=	"<tr>"+
									"<td align='right'>"+array.IDRESERVATION+"</td>"+
									"<td>"+
										"<b>Cust : "+array.CUSTOMERNAME+"</b><br/>"+
										"<b>Book Code : "+array.BOOKINGCODE+"</b><br/>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
										reservationDateEnd+
										"Duration : "+array.DURATIONOFDAY+" day(s)<br/><br/>"+
										"["+inputType+"] "+array.SOURCENAME+"<br/>"+
										badgeReservationType+"<br/>"+
									"</td>"+
									"<td>"+carSchedule+"</td>"+
									"<td>"+driverSchedule+"</td>"+
									"<td>"+ticketList+"</td>"+
								"</tr>";
					
				});
				
			}

			generatePagination("tablePaginationReservationDetail", page, response.result.pageTotal);
			generateDataInfo("tableDataCountReservationDetail", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

reservationDetailFunc();