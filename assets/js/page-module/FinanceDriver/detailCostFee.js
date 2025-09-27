if (detailCostFeeFunc == null){
	var detailCostFeeFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionDriverType', 'dataDriverType');
			setOptionHelper('optionDriver', 'dataDriver');

			$('#optionDriverType').off('change');
			$('#optionDriverType').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionDriver', 'dataDriver', false, false, this.value);
				} else {
					setOptionHelper('optionDriver', 'dataDriver');
				}

				getDataDetailCostFee();
			});

			getDataDetailCostFee();
		});	
	}
}

$('#optionDriver, #startDate, #endDate').off('change');
$('#optionDriver, #startDate, #endDate').on('change', function(e) {
	getDataDetailCostFee();
});

function generateDataTable(page){
	getDataDetailCostFee(page);
}

function getDataDetailCostFee(page = 1){
	
	var $tableBody		=	$('#table-detailCostFee > tbody'),
		columnNumber	=	$('#table-detailCostFee > thead > tr > th').length,
		idDriverType	=	$('#optionDriverType').val(),
		idDriver		=	$('#optionDriver').val(),
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		dataSend		=	{page:page, idDriverType:idDriverType, idDriver:idDriver, startDate:startDate, endDate:endDate}
		rowNoDataFound	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/detailCostFee/getDataDetailCostFee",
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
			
			if(response.status != 200 || response.status != "200"){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
				$('#excelDetailCostFee').addClass('d-none').off("click").attr("href", "");
				$tableBody.html(rowNoDataFound);
			} else {
				
				var data			=	response.result.data,
					rows			=	"";
				
				if(data.length === 0){
					$('#excelDetailCostFee').addClass('d-none').off("click").attr("href", "");
					rows	=	rowNoDataFound;
				} else {
					
					$('#excelDetailCostFee').removeClass('d-none').on("click").attr("href", response.urlExcelDetailFee);
					$.each(data, function(index, array) {
						
						var badgeStatus				=	'<span class="badge badge-warning">Unprocessed</span>';
						switch(array.STATUS){
							case "-1"	:	badgeStatus	=	'<span class="badge badge-danger">Cancel</span>'; break;
							case "0"	:	badgeStatus	=	'<span class="badge badge-dark">Unprocessed</span>'; break;
							case "1"	:	badgeStatus	=	'<span class="badge badge-warning">Admin Processed</span>'; break;
							case "2"	:	badgeStatus	=	'<span class="badge badge-success">Scheduled</span>'; break;
							case "3"	:	badgeStatus	=	'<span class="badge badge-primary">On Process</span>'; break;
							case "4"	:	badgeStatus	=	'<span class="badge badge-info">Done</span>'; break;
							default		:	badgeStatus	=	'<span class="badge badge-warning">Unprocessed</span>'; break;
						}
						
						var	reservationDateEnd	=	"";
						if(array.DURATIONOFDAY > 1){
							reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
						}
							
						var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>';
						var inputType				=	'';
						switch(array.INPUTTYPE){
							case "1"	:	inputType	=	'Mailbox'; break;
							case "2"	:	inputType	=	'Manual'; break;
						}
						
						var elemCostDetail	=	"-",
							totalCosts		=	array.TOTALCOST * 1,
							totalFee		=	array.TOTALFEE * 1,
							totalCostFee	=	totalCosts + totalFee;
							
						if(array.COSTDETAIL != '' && array.TOTALCOST > 0 && array.TOTALCOST != "0"){
							elemCostDetail	=	"";
							$.each(array.COSTDETAIL, function(idxCost, arrayCost) {
								elemCostDetail	+=	arrayCost.DATETIMEINPUT+"-"+arrayCost.ADDITIONALCOSTTYPE+"<br/>"+arrayCost.DESCRIPTION+"<br/>Rp. "+numberFormat(arrayCost.NOMINAL)+"<br/><br/>";
							});
							elemCostDetail	+=	"<b>Total Cost : "+numberFormat(totalCosts)+"</b>";
						}

						var elemScheduleList=	"",
							jobDetailsArray	=	array.JOBDETAILS;
							
						if(jobDetailsArray && jobDetailsArray.length > 0){
							for(var i=0; i<jobDetailsArray.length; i++){
								var withdrawStatus		=	jobDetailsArray[i][3],
									badgeWithdrawStatus	=	"";
								
								switch(withdrawStatus){
									case "0"	:	
									case 0		:	
													badgeWithdrawStatus	=	"<span class='badge badge-info'>Balanced</span>"; break;
									case "1"	:	
									case 1		:	
													badgeWithdrawStatus	=	"<span class='badge badge-success'>Withdrawn</span>"; break;
								}
								
								elemScheduleList		+=	jobDetailsArray[i][0]+" "+badgeWithdrawStatus+"<br/><b>"+jobDetailsArray[i][1]+"</b><br/>Fee : "+jobDetailsArray[i][2]+"<br/><br/>";
							}
						}
						
						rows			+=	"<tr>"+
												"<td>"+badgeStatus+"<br/>["+array.DRIVERTYPE+"] "+array.DRIVERNAME+"<br/><br/>"+
													"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
													reservationDateEnd+
													"Duration : "+array.DURATIONOFDAY+" day(s)"+
												"</td>"+
												"<td>"+
													"<b>Cust : "+array.CUSTOMERNAME+"</b><br/>"+
													"<b>Book Code : "+array.BOOKINGCODE+"</b><br/>"+
													"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
													"["+inputType+"] "+array.SOURCENAME+"<br/>"+
													badgeReservationType+"<br/>"+
												"</td>"+
												"<td>"+elemScheduleList+"<b>Total Fee : "+numberFormat(array.TOTALFEE)+"</b></td>"+
												"<td>"+elemCostDetail+"</td>"+
												"<td align='right'><b>"+numberFormat(totalCostFee)+"</b></td>"+
											"</tr>";
								
					});
					
				}

				generatePagination("tablePaginationDetailCostFee", page, response.result.pageTotal);
				generateDataInfo("tableDataCountDetailCostFee", response.result.dataStart, response.result.dataEnd, response.result.dataTotal);
				$tableBody.html(rows);
			
			}
			
		}
	});
	
}

detailCostFeeFunc();