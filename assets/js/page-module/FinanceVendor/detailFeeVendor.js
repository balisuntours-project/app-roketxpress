if (detailFeeVendorFunc == null){
	var detailFeeVendorFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			setOptionHelper('optionVendorCar', 'dataVendorCar');
			setOptionHelper('optionVendorTicket', 'dataVendorTicket');
			$("#optionVendorTicket").select2();
			$("#optionVendorCar").select2();
			getDetailFeeVendorCar();
			getDetailFeeVendorTicket();
		});	
	}
}

$('#optionMonth, #optionYear, #filterDetailFeeVendorCar, #optionVendorTicket').off('change');
$('#optionMonth, #optionYear, #filterDetailFeeVendorCar, #optionVendorTicket').on('change', function(e) {
	getDetailFeeVendorCar();
	getDetailFeeVendorTicket();
});

function generateDataTableCar(page){
	getDetailFeeVendorCar(page);
}

function generateDataTableTicket(page){
	getDetailFeeVendorTicket(page);
}

function getDetailFeeVendorCar(page = 1){
	
	var $tableBody		=	$('#table-detailFeeVendorCar > tbody'),
		columnNumber	=	$('#table-detailFeeVendorCar > thead > tr > th').length,
		idVendorCar		=	$('#optionVendorCar').val(),
		month			=	$('#optionMonth').val(),
		year			=	$('#optionYear').val(),
		dataSend		=	{page:page, idVendorCar:idVendorCar, month:month, year:year};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/detailFeeVendor/getDetailFeeVendorCar",
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
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				
				$.each(data, function(index, array) {

					var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>';
					var inputType	=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					rows			+=	"<tr>"+
											"<td>"+array.NAME+"</td>"+
											"<td>"+array.SCHEDULEDATE+"</td>"+
											"<td>"+array.CARDETAILS+"</td>"+
											"<td>"+
												badgeReservationType+"<br/>"+
												"["+inputType+"] "+array.SOURCENAME+"<br/><br/>"+
												"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
												array.CUSTOMERNAME+
											"</td>"+
											"<td>"+array.PRODUCTNAME+"<br/>Schedule by:"+array.USERINPUT+"<br/><br/>Notes:<br/>"+array.NOTES+"</td>"+
											"<td align='right'>"+numberFormat(array.NOMINAL)+"</td>"+
										"</tr>";
							
				});
				
			}

			generatePagination("tablePaginationDetailFeeVendorCar", page, response.result.pageTotal, "generateDataTableCar");
			generateDataInfo("tableDataCountDetailFeeVendorCar", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

function getDetailFeeVendorTicket(page = 1){
	
	var $tableBody		=	$('#table-detailFeeVendorTicket > tbody'),
		columnNumber	=	$('#table-detailFeeVendorTicket > thead > tr > th').length,
		idVendorTicket	=	$('#optionVendorTicket').val(),
		month			=	$('#optionMonth').val(),
		year			=	$('#optionYear').val(),
		dataSend		=	{page:page, idVendorTicket:idVendorTicket, month:month, year:year};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/detailFeeVendor/getDetailFeeVendorTicket",
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
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
				$('#excelDataDetailFeeVendorTicket').addClass('d-none').off("click").attr("href", "");
			} else {
				
				if(response.urlExcelDetailFeeVendorTicket != "") $('#excelDataDetailFeeVendorTicket').removeClass('d-none').on("click").attr("href", response.urlExcelDetailFeeVendorTicket);
				$.each(data, function(index, array) {

					var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>';
					var inputType	=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					rows			+=	"<tr>"+
											"<td>"+array.NAME+"</td>"+
											"<td>"+array.SCHEDULEDATE+"</td>"+
											"<td>"+
												badgeReservationType+"<br/>"+
												"["+inputType+"] "+array.SOURCENAME+"<br/><br/>"+
												"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
												array.CUSTOMERNAME+
											"</td>"+
											"<td>"+array.PRODUCTNAME+"<br/>Input by:"+array.USERINPUT+"<br/><br/>Notes:<br/>"+array.NOTES+"</td>"+
											"<td>"+
												"<div class='order-details-customer-info mb-10'>"+
													"<ul class='ml-5'>"+
														"<li> <span>Adult</span> <span>"+array.PAXADULT+" x "+numberFormat(array.PRICEPERPAXADULT)+" = "+numberFormat(array.PRICETOTALADULT)+"</span> </li>"+
														"<li> <span>Child</span> <span>"+array.PAXCHILD+" x "+numberFormat(array.PRICEPERPAXCHILD)+" = "+numberFormat(array.PRICETOTALCHILD)+"</span> </li>"+
														"<li> <span>Infant</span> <span>"+array.PAXINFANT+" x "+numberFormat(array.PRICEPERPAXINFANT)+" = "+numberFormat(array.PRICETOTALINFANT)+"</span> </li>"+
													"</ul>"+
												"</div>"+
											"</td>"+
											"<td align='right'><b>"+numberFormat(array.NOMINAL)+"</b></td>"+
										"</tr>";
							
				});
				
			}

			generatePagination("tablePaginationDetailFeeVendorTicket", page, response.result.pageTotal, "generateDataTableTicket");
			generateDataInfo("tableDataCountDetailFeeVendorTicket", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

detailFeeVendorFunc();