if (detailReservationIncomeFunc == null){
	var detailReservationIncomeFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('detailIncomeTab-optionReservationType', 'dataReservationType');
			setOptionHelper('detailIncomeTab-optionSource', 'dataSource');
			setOptionHelper('recapPerSource-optionSource', 'dataSource');
			setOptionHelper('recapPerYear-optionSource', 'dataSource');
			setOptionHelper('recapPerYear-optionYear', 'optionYear', false, false);
			getDataDetailReservationIncome();
			
			$('a.tabReservationIncome[data-toggle="tab"]').off('shown.bs.tab');
			$('a.tabReservationIncome[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				var tabHref	= $(e.target).attr("href");
				
				switch(tabHref){
					case '#detailIncomeTab'	:	getDataDetailReservationIncome(); break;
					case '#recapPerSource'	:	getDataRecapReservationIncome(); break;
					case '#recapPerYear'		:	getDataRecapPerYear(); break;
				}
			});
		});	
	}
}

$('#detailIncomeTab-optionReservationType, #detailIncomeTab-optionSource, #detailIncomeTab-startDate, #detailIncomeTab-endDate').off('change');
$('#detailIncomeTab-optionReservationType, #detailIncomeTab-optionSource, #detailIncomeTab-startDate, #detailIncomeTab-endDate').on('change', function(e) {
	getDataDetailReservationIncome();
});

$('#detailIncomeTab-keywordSearch').off('keypress');
$("#detailIncomeTab-keywordSearch").on('keypress',function(e) {
    if(e.which == 13) {
        getDataDetailReservationIncome();
    }
});

$('#detailIncomeTab-checkboxIncludeCollectPayment, #detailIncomeTab-checkboxIncludeAdditionalCost').off('click');
$("#detailIncomeTab-checkboxIncludeCollectPayment, #detailIncomeTab-checkboxIncludeAdditionalCost").on('click',function(e) {
	getDataDetailReservationIncome();
});
	
function generateDataTableDetail(page){
	getDataDetailReservationIncome(page);
}

function getDataDetailReservationIncome(page = 1){
	var $tableBody				=	$('#detailIncomeTab-tableDetail > tbody'),
		columnNumber			=	$('#detailIncomeTab-tableDetail > thead > tr > th').length,
		idReservationType		=	$('#detailIncomeTab-optionReservationType').val(),
		idSource				=	$('#detailIncomeTab-optionSource').val(),
		startDate				=	$('#detailIncomeTab-startDate').val(),
		endDate					=	$('#detailIncomeTab-endDate').val(),
		keywordSearch			=	$('#detailIncomeTab-keywordSearch').val(),
		includeCollectPayment	=	$("#detailIncomeTab-checkboxIncludeCollectPayment").is(':checked'),
		includeAdditionalCost	=	$("#detailIncomeTab-checkboxIncludeAdditionalCost").is(':checked'),
		dataSend				=	{
			page:page,
			idReservationType:idReservationType,
			idSource:idSource,
			startDate:startDate,
			endDate:endDate,
			keywordSearch:keywordSearch,
			includeCollectPayment:includeCollectPayment,
			includeAdditionalCost:includeAdditionalCost
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationIncome/getDataDetailReservationIncome",
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
			
			if(response.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
				$tableBody.html("<tr><td colspan='"+columnNumber+"'><center>"+response.msg+"</center></td></tr>");
				$('#detailIncomeTab-excelDetail').addClass('d-none').off("click").attr("href", "");
				generatePagination("detailIncomeTab-tablePagination", page, 1);
				generateDataInfo("detailIncomeTab-tableDataCount", 0, 0, 0);
			} else {
				var data			=	response.result.data,
					rows			=	"";
				
				if(data.length === 0){
					rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
					$('#detailIncomeTab-excelDetail').addClass('d-none').off("click").attr("href", "");
				} else {
					
					if(response.urlExcelDetail != "") $('#detailIncomeTab-excelDetail').removeClass('d-none').on("click").attr("href", response.urlExcelDetail);
					$.each(data, function(index, array) {
						var badgeReservationType=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+' mr-1">'+array.RESERVATIONTYPE+'</span>';
						var badgeStatus			=	'<span class="badge badge-warning">Unprocessed</span>',
							tdPaymentFinance	=	'';
						switch(array.STATUS){
							case "-1"	:	badgeStatus	=	'<span class="badge badge-danger">Cancel</span>'; break;
							case "0"	:	badgeStatus	=	'<span class="badge badge-secondary">Unprocessed</span>'; break;
							case "1"	:	badgeStatus	=	'<span class="badge badge-warning">Admin Processed</span>'; break;
							case "2"	:	badgeStatus	=	'<span class="badge badge-info">Scheduled</span>'; break;
							case "3"	:	badgeStatus	=	'<span class="badge badge-primary">On Process</span>'; break;
							case "4"	:	badgeStatus	=	'<span class="badge badge-success">Done</span>'; break;
							default		:	badgeStatus	=	'<span class="badge badge-warning">Unprocessed</span>'; break;
						}
						
						var inputType	=	'';
						switch(array.INPUTTYPE){
							case "1"	:	inputType	=	'Mailbox'; break;
							case "2"	:	inputType	=	'Manual'; break;
						}
						
						var firstRowDetails	=	nextRowDetails	=	"";
						
						if(array.DETAILSPRODUCTTYPE != "" && array.DETAILSPRODUCTTYPE != null){
							var splitProductType			=	array.DETAILSPRODUCTTYPE.split('|'),
								splitProductVendorDriver	=	array.DETAILSPRODUCTVENDORDRIVER.split('|'),
								splitProductName			=	array.DETAILSPRODUCTNAME.split('|'),
								splitProductDate			=	array.DETAILSPRODUCTDATE.split('|'),
								splitProductCost			=	array.DETAILSPRODUCTCOST.split('|'),
								rowSpanNumber				=	splitProductName.length,
								incomeMargin				=	array.INCOMEAMOUNTIDR * 1,
								totalCost					=	0;
							
							for(var iDetail = 0; iDetail<rowSpanNumber; iDetail++){
								var styleHeight			=	(iDetail-1) == rowSpanNumber ? "" : "style='height:40px'",
									productType			=	splitProductType[iDetail].split('=')[0],
									productVendorDriver	=	splitProductVendorDriver[iDetail].split('=')[0],
									productName			=	splitProductName[iDetail].split('=')[0],
									productDate			=	splitProductDate[iDetail].split('=')[0],
									productCost			=	splitProductCost[iDetail].split('=')[0],
									tdDetails			=	"<td "+styleHeight+">["+productType+"] "+productVendorDriver+"<br/>"+productName+"</td>"+
															"<td "+styleHeight+" align='center'>"+productDate+"</td>"+
															"<td "+styleHeight+" align='right'>"+numberFormat(productCost)+"</td>";
								if(iDetail == 0){
									firstRowDetails	+=	tdDetails;
								} else {
									nextRowDetails	+=	"<tr>"+tdDetails+"</tr>";
								}
								
								totalCost			+=	productCost * 1;
								incomeMargin		-=	productCost * 1;
							}
							
							if(rowSpanNumber > 1){
								nextRowDetails	+=	"<tr><td colspan='2'><b>Total Cost</b></td><td align='right'><b>"+numberFormat(totalCost)+"</b></td></tr>";
								rowSpanNumber++;
							}
						} else {
							firstRowDetails	=	"<td colspan='3'>No details</td>";
						}
						
						if(array.DETAILSPAYMENTFINANCE != "" && array.DETAILSPAYMENTFINANCE != null){
							var splitPaymentFinance	=	array.DETAILSPAYMENTFINANCE.split('|'),
								countPaymentFinance	=	splitPaymentFinance.length,
								totalPaymentFinance	=	0,
								badgeStatusPayment	=	'<span class="badge badge-info">Not Set</span>';
								
							for(var iDetail = 0; iDetail<countPaymentFinance; iDetail++){
								var splitPayment	=	splitPaymentFinance[iDetail].split(']');
								switch(splitPayment[0]){
									case "-1"	:	badgeStatusPayment	=	'<span class="badge badge-danger">Cancel</span>	'; break;
									case "0"	:	badgeStatusPayment	=	'<span class="badge badge-secondary">Unpaid</span>	'; break;
									case "1"	:	badgeStatusPayment	=	'<span class="badge badge-primary mr-4">Paid </span>	'; break;
									default		:	badgeStatusPayment	=	'<span class="badge badge-info">Not Set</span>	'; break;
								}
								
								tdPaymentFinance	+=	badgeStatusPayment+splitPayment[1]+"<span class='pull-right'>"+numberFormat(splitPayment[2])+"</span><br/>";
								totalPaymentFinance	+=	splitPayment[2] * 1;
							}
							
							if(countPaymentFinance > 1) tdPaymentFinance+=	"<br/><b>Total</b><b class='pull-right'>"+numberFormat(totalPaymentFinance)+"</b><br/>";
						} else {
							tdPaymentFinance	=	"No details";
						}
							
						var	reservationDateEnd	=	"";
						if(array.DURATIONOFDAY > 1){
							reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
						}

						rows	+=	"<tr>"+
										"<td rowspan='"+rowSpanNumber+"'>"+
											badgeReservationType+badgeStatus+"<br/> ["+inputType+"] "+array.SOURCENAME+" - "+array.BOOKINGCODE+"<br/><br/>"+
											"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
											array.DURATIONOFDAY+" day(s) | <b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/><br/>"+
											reservationDateEnd+
											array.CUSTOMERNAME+"<br/>"+
											"Contact : "+array.CUSTOMERCONTACT+" | "+array.CUSTOMEREMAIL+
										"</td>"+
										"<td rowspan='"+rowSpanNumber+"' align='right'>"+numberFormat(array.INCOMEAMOUNTIDR)+"</td>"+
										"<td rowspan='"+rowSpanNumber+"'>"+tdPaymentFinance+"</td>"+
										firstRowDetails+
										"<td rowspan='"+rowSpanNumber+"' align='right'>"+numberFormat(incomeMargin)+"</td>"+
									"</tr>"+
									nextRowDetails;
					});
				}

				generatePagination("detailIncomeTab-tablePagination", page, response.result.pageTotal, 'generateDataTableDetail');
				generateDataInfo("detailIncomeTab-tableDataCount", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
				$tableBody.html(rows);
			}
		}
	});
}

$('#recapPerSource-optionReportType, #recapPerSource-optionSource, #recapPerSource-startDate, #recapPerSource-endDate').off('change');
$('#recapPerSource-optionReportType, #recapPerSource-optionSource, #recapPerSource-startDate, #recapPerSource-endDate').on('change', function(e) {
	getDataRecapReservationIncome();
});

function generateDataTableRecap(page){
	getDataRecapReservationIncome(page);
}

function getDataRecapReservationIncome(page = 1){
	var $tableBody		=	$('#recapPerSource-tableRecap > tbody'),
		columnNumber	=	$('#recapPerSource-tableRecap > thead > tr > th').length,
		reportType		=	$('#recapPerSource-optionReportType').val(),
		idSource		=	$('#recapPerSource-optionSource').val(),
		startDate		=	$('#recapPerSource-startDate').val(),
		endDate			=	$('#recapPerSource-endDate').val(),
		dataSend		=	{
			page:page,
			reportType:reportType,
			idSource:idSource,
			startDate:startDate,
			endDate:endDate
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationIncome/getDataRecapReservationIncome",
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
			
			if(response.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
				$tableBody.html("<tr><td colspan='"+columnNumber+"'><center>"+response.msg+"</center></td></tr>");
				$('#recapPerSource-excelRecap').addClass('d-none').off("click").attr("href", "");
				generatePagination("recapPerSource-tablePagination", page, 1);
				generateDataInfo("recapPerSource-tableDataCount", 0, 0, 0);
			} else {
				var data			=	response.result.data,
					rows			=	"";
				
				if(data.length === 0){
					rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
					$('#recapPerSource-excelRecap').addClass('d-none').off("click").attr("href", "");
				} else {
					
					if(response.urlExcelRecap != "") $('#recapPerSource-excelRecap').removeClass('d-none').on("click").attr("href", response.urlExcelRecap);
					$.each(data, function(index, array) {
						rows	+=	"<tr>"+
										"<td>"+array.PERIOD+"</td>"+
										"<td>"+array.SOURCENAME+"</td>"+
										"<td align='right'>"+numberFormat(array.TOTALRESERVATION)+"</td>"+
										"<td align='right'>"+numberFormat(array.TOTALINCOME)+"</td>"+
									"</tr>";
					});
				}

				generatePagination("recapPerSource-tablePagination", page, response.result.pageTotal, 'generateDataTableRecap');
				generateDataInfo("recapPerSource-tableDataCount", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
				$tableBody.html(rows);
			}
		}
	});
}

$('#recapPerYear-optionYear, #recapPerYear-optionSource').off('change');
$('#recapPerYear-optionYear, #recapPerYear-optionSource').on('change', function(e) {
	getDataRecapPerYear();
});

function getDataRecapPerYear(){
	var $tableBody	=	$('#recapPerYear-tableRecap > tbody'),
		columnNumber=	$('#recapPerYear-tableRecap > thead > tr > th').length,
		year		=	$('#recapPerYear-optionYear').val(),
		idSource	=	$('#recapPerYear-optionSource').val(),
		dataSend	=	{
			year:year,
			idSource:idSource
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationIncome/getDataRecapPerYear",
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
			
			if(response.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
				$tableBody.html("<tr><td colspan='"+columnNumber+"'><center>"+response.msg+"</center></td></tr>");
				$('#recapPerYear-excelRecap').addClass('d-none').off("click").attr("href", "");
			} else {
				var data	=	response.result,
					rows	=	"";
				
				if(data.length === 0){
					rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
					$('#recapPerYear-excelRecap').addClass('d-none').off("click").attr("href", "");
				} else {
					
					if(response.urlExcelRecap != "") $('#recapPerYear-excelRecap').removeClass('d-none').on("click").attr("href", response.urlExcelRecap);
					var totalPeriod	=	0;
					var grandTotalActiveReservation = grandTotalIncomeReservation =	grandTotalIncomeFinance =	grandTotalCost =	grandTotalMargin =	0;
					$.each(data, function(index, array) {
						var totalMargin					=	parseInt(array.TOTALINCOMEFINANCE) - parseInt(array.TOTALCOST);
							grandTotalActiveReservation	+=	parseInt(array.TOTALRESERVATION);
							grandTotalIncomeReservation	+=	parseInt(array.TOTALINCOMERESERVATION);
							grandTotalIncomeFinance		+=	parseInt(array.TOTALINCOMEFINANCE);
							grandTotalCost				+=	parseInt(array.TOTALCOST);
							grandTotalMargin			+=	totalMargin;
							
						rows	+=	"<tr>"+
										"<td>"+array.PERIOD+"</td>"+
										"<td align='right'>"+numberFormat(array.TOTALRESERVATION)+"</td>"+
										"<td align='right'>"+numberFormat(array.TOTALINCOMERESERVATION)+"</td>"+
										"<td align='right'>"+numberFormat(array.TOTALINCOMEFINANCE)+"</td>"+
										"<td align='right'>"+numberFormat(array.TOTALCOST)+"</td>"+
										"<td align='right'>"+numberFormat(totalMargin)+"</td>"+
									"</tr>";
						totalPeriod++;
					});
							
					rows	+=	"<tr>"+
									"<td class='font-weight-bold'>TOTAL</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(grandTotalActiveReservation)+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(grandTotalIncomeReservation)+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(grandTotalIncomeFinance)+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(grandTotalCost)+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(grandTotalMargin)+"</td>"+
								"</tr>";
							
					rows	+=	"<tr>"+
									"<td class='font-weight-bold'>AVERAGE</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(Math.trunc(grandTotalActiveReservation / totalPeriod))+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(Math.trunc(grandTotalIncomeReservation / totalPeriod))+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(Math.trunc(grandTotalIncomeFinance / totalPeriod))+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(Math.trunc(grandTotalCost / totalPeriod))+"</td>"+
									"<td class='font-weight-bold' align='right'>"+numberFormat(Math.trunc(grandTotalMargin / totalPeriod))+"</td>"+
								"</tr>";
				}
				
				$tableBody.html(rows);
			}
		}
	});
}

detailReservationIncomeFunc();