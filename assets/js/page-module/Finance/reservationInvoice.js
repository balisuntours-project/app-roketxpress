if (reservationInvoiceFunc == null){
	var reservationInvoiceFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMonthChooseReservation', 'optionMonth', false, false);
			setOptionHelper('optionYearChooseReservation', 'optionYear', false, false);
			getDataInvoiceHistory();
		});	
	}
}

$('#filterDataInvoiceHistory').off('click');
$('#filterDataInvoiceHistory').on('click', function(e) {
	getDataInvoiceHistory();
});

function generateDataTable(page){
	getDataInvoiceHistory(page);
}

$('#optionMonthChooseReservation, #optionYearChooseReservation').off('change');
$('#optionMonthChooseReservation, #optionYearChooseReservation').on('change', function(e) {
	searchDataReservation();
});

$('#nameBookingCodeChooseReservation').off('keypress');
$("#nameBookingCodeChooseReservation").on('keypress',function(e) {
    if(e.which == 13) {
        searchDataReservation();
    }
});

$('#editor-chooseReservation').off('submit');
$("#editor-chooseReservation").on('submit',function(e) {
	e.preventDefault();
});

function searchDataReservation() {
	var $tableBody		=	$('#table-chooseReservation > tbody'),
		columnNumber	=	$('#table-chooseReservation > thead > tr > th').length,
		nameBookingCode	=	$('#nameBookingCodeChooseReservation').val(),
		month			=	$('#optionMonthChooseReservation').val(),
		year			=	$('#optionYearChooseReservation').val(),
		dataSend		=	{nameBookingCode:nameBookingCode, month:month, year:year};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/reservationInvoice/searchDataReservation",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Searching data...</center></td></tr>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var rows			=	"";
			
			if(response.status != 200){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td></tr>";
			} else {
				var data	=	response.data;
				$.each(data, function(index, array) {
						
					var	reservationDateEnd	=	"";
					if(array.DURATIONOFDAY > 1){
						reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
					}
						
					var inputType	=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					
					var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>';
					var btnChoose				=	'<button type="button" class="button button-xs button-primary" onclick="setReservationDetails('+array.IDRESERVATION+')"><i class="fa fa-check"></i> Select</button>';
					rows	+=	"<tr id='trReservation"+array.IDRESERVATION+"'>"+
									"<td>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
										reservationDateEnd+
										"Duration : "+array.DURATIONOFDAY+" day(s)<br/><br/>"+
										"["+inputType+"] "+array.SOURCENAME+"<br/>"+
										badgeReservationType+"<br/><br/>"+
										"<b>Booking Code :</b><br/>"+array.BOOKINGCODE+
									"</td>"+
									"<td>"+
										"<b id='customerName"+array.IDRESERVATION+"'>"+array.CUSTOMERNAME+"</b><br/>"+
										"Contact : <span id='customerContact"+array.IDRESERVATION+"'>"+array.CUSTOMERCONTACT+"</span><br/>"+
										"Email : <span id='customerEmail"+array.IDRESERVATION+"'>"+array.CUSTOMEREMAIL+"</span><br/><br/>"+
										"Adult : <b>"+array.NUMBEROFADULT+"</b><br/>"+
										"Child : <b>"+array.NUMBEROFCHILD+"</b><br/>"+
										"Infant : <b>"+array.NUMBEROFINFANT+"</b>"+
									"</td>"+
									"<td>"+
										"<b>Hotel<br/></b>"+array.HOTELNAME+"<br/><br/>"+
										"<b>Pick Up<br/></b>"+array.PICKUPLOCATION+"<br/><br/>"+
										"<b>Drop Off<br/></b>"+array.DROPOFFLOCATION+
									"</td>"+
									"<td>"+btnChoose+"</td>"+
								"</tr>";
				});
			}
			$tableBody.html(rows);
		}
	});
}

function setReservationDetails(idReservation){
	
	var dataSend	=	{idReservation:idReservation};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/reservationInvoice/getInvoiceNumberDetailCost",
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

			$("#idReservationValueHidden").val(idReservation);
			$("#invoiceNumberComposer").val(response.invoiceNumber);
			$("#customerNameComposer").val($("#customerName"+idReservation).html());
			$("#customerPhoneNumberComposer").val($("#customerContact"+idReservation).html());
			var elemDetailReservation	=	"<tr>";
			$("#trReservation"+idReservation).find('td').each(function(index) {
				if(index != 3){
					elemDetailReservation	+=	"<td>"+$(this).html()+"</td>";
				}
			});
			elemDetailReservation		+=	"</tr>";
			
			$("#chooseReservationTitle").html("Compose invoice for this reservation");
			$("#chooseReservation").addClass("d-none")
			$("#saveInvoice, #cancelInvoice, #invoicePreview").removeClass("d-none");
			$("#bodyDetailReservation").html(elemDetailReservation);
			$("#customerEmail").val($("#customerEmail"+idReservation).html());
			
			var listInvoice		=	response.listInvoice,
				trListInvoice	=	"";
			
			if(listInvoice.length > 0){
				$.each(listInvoice, function(idxInvoice, arrayInvoice) {
					trListInvoice	+=	"<tr>"+
											"<td>"+
												arrayInvoice.INVOICENUMBER+"<br/>"+
												"<small>"+arrayInvoice.INVOICEDATE+"</small>"+
												"<a class='badge badge-info pull-right' target='_blank' href='"+arrayInvoice.URLINVOICE+"'>Show Invoice</a>"+
											"</td>"+
										"</tr>";
				});
			}
			
			$("#bodyListInvoice").html(trListInvoice);
			
			$('#modal-chooseReservation').modal('hide');
			
		}
	});

}

$('#editor-editorInvoiceItem').on('show.bs.modal', function() {
	$("#descriptionInvoiceItem, #subDescriptionInvoiceItem").val("");
	$("#rateInvoiceItem, #quantityInvoiceItem, #totalAmountInvoiceItem").val(0);

	$('#rateInvoiceItem, #quantityInvoiceItem').off('change');
	$('#rateInvoiceItem, #quantityInvoiceItem').on('change', function(e) {
		calculateInvoiceItem();
	});
});

$('#editor-editorInvoiceItem').off('submit');
$('#editor-editorInvoiceItem').on('submit', function(e) {
	e.preventDefault();
	var description			=	$("#descriptionInvoiceItem").val(),
		subDescription		=	$("#subDescriptionInvoiceItem").val(),
		rate				=	$("#rateInvoiceItem").val(),
		quantity			=	$("#quantityInvoiceItem").val(),
		totalAmount			=	$("#totalAmountInvoiceItem").val(),
		totalAmountCheck	=	$("#totalAmountInvoiceItem").val().replace(/[^0-9\.]+/g, '') * 1;
	
	if(description == ""){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please input a valid value for description");
		});
		$('#modalWarning').modal('show');
	} else if(totalAmountCheck <= 0) {		
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please input a valid amount");			
		});
		$('#modalWarning').modal('show');
	} else {
		var newElemInvoiceItem	=	"<tr class='py-2 trItemInvoice' style='border-bottom: 1px solid #e0e0e0;'>"+
										"<td>"+description+"<br/><small>"+subDescription+"</small></td>"+
										"<td align='right'>"+rate+"</td>"+
										"<td align='right'>"+quantity+"</td>"+
										"<td align='right' class='totalAmountItem'>"+totalAmount+"</td>"+
									"</tr>";
		$("#invoiceItemBody").prepend(newElemInvoiceItem);
		$("#descriptionInvoiceItem, #subDescriptionInvoiceItem").val("");
		$("#rateInvoiceItem, #quantityInvoiceItem, #totalAmountInvoiceItem").val(0);
		$('#modal-editorInvoiceItem').modal('hide');	
		calculateTotalAmountItemAndDue();
	}
	
});

function calculateInvoiceItem(){
	var rate		=	$("#rateInvoiceItem").val().replace(/[^0-9\.]+/g, ''),
		quantity	=	$("#quantityInvoiceItem").val().replace(/[^0-9\.]+/g, ''),
		totalAmount	=	rate * quantity;
	
	$("#totalAmountInvoiceItem").val(numberFormat(totalAmount));
}

function calculateTotalAmountItemAndDue(){
	var totalAmountInvoice	=	0,
		totalBalanceInvoice	=	$("#customerBalanceComposer").val().replace(/[^0-9\.]+/g, '') * 1;
	$('td.totalAmountItem').each(function () {
		totalAmountInvoice	+=	$(this).html().replace(/[^0-9\.]+/g, '') * 1;
	});
	$("#totalInvoiceItem").html(numberFormat(totalAmountInvoice));
	
	var totalDueInvoice		=	totalAmountInvoice - totalBalanceInvoice;
	
	$("#totalBalanceDueInvoice, #totalBalanceDueSmall").html(numberFormat(totalDueInvoice));
}

$('#cancelInvoice').off('click');
$('#cancelInvoice').on('click', function(e) {
	resetInvoiceComposer();
});

function resetInvoiceComposer(){
	$("#chooseReservationTitle").html("Choose reservation to create new invoice");
	$("#chooseReservation").removeClass("d-none")
	$("#saveInvoice, #cancelInvoice, #invoicePreview").addClass("d-none");
	$("#bodyDetailReservation").html('<tr><td colspan="3" align="center">Please choose reservation</td></tr>');	
	$("#bodyListInvoice").html('<tr><td align="center">No Data</td></tr>');
	$("#invoiceNumberComposer, #customerNameComposer, #customerPhoneNumberComposer").val("");
	$("#invoiceDateComposer").val(dateToday);
	$(".trItemInvoice").remove();
	$("#totalBalanceDueSmall, #totalInvoiceItem, #totalBalanceDueInvoice").html(0);
	$("#customerBalanceComposer, #idReservationValueHidden").val(0);
}

$('#saveInvoice').off('click');
$('#saveInvoice').on('click', function(e) {
	$('#modal-inputEmail').modal('show');
});

$('#editor-inputEmail').off('submit');
$('#editor-inputEmail').on('submit', function(e) {
	e.preventDefault();
});

$('#btnSubmitReservationInvoice').off('click');
$('#btnSubmitReservationInvoice').on('click', function(e) {

	var detailItemInvoice	=	[];
	$('tr.trItemInvoice').each(function(){
		var description	=	subDescription	=	"";
		var rate	=	quantity	=	totalAmount	=	0;
		
		$(this).find('td').each (function(index) {
			var tdValue	=	$(this).html();
			if(index == 0){
				var expTdValue	=	tdValue.split("<br>");
				description		=	expTdValue[0];
				subDescription	=	$(expTdValue[1]).text();
			}
			if(index == 1){
				rate		=	tdValue.replace(/[^0-9\.]+/g, '') * 1;
			}
			if(index == 2){
				quantity	=	tdValue.replace(/[^0-9\.]+/g, '') * 1;
			}
			if(index == 3){
				totalAmount	=	tdValue.replace(/[^0-9\.]+/g, '') * 1;
			}
		}); 
		detailItemInvoice.push([description, subDescription, rate, quantity, totalAmount]);
	});
	
	if(detailItemInvoice.length <= 0){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please input invoice item first (at least one)");
		});
		$('#modalWarning').modal('show');
	} else {
	
		var idReservation	=	$("#idReservationValueHidden").val(),
			invoiceType		=	$('input[name="invoiceType"]:checked').val();
			invoiceDate		=	$("#invoiceDateComposer").val(),
			customerName	=	$("#customerNameComposer").val(),
			customerContact	=	$("#customerPhoneNumberComposer").val(),
			customerEmail	=	$("#customerEmail").val(),
			totalBalance	=	$("#customerBalanceComposer").val().replace(/[^0-9\.]+/g, ''),
			dataSend		=	{
									idReservation:idReservation,
									invoiceType:invoiceType,
									invoiceDate:invoiceDate,
									customerName:customerName,
									customerContact:customerContact,
									customerEmail:customerEmail,
									detailItemInvoice:detailItemInvoice,
									totalBalance:totalBalance
								};
		
		$.ajax({
			type: 'POST',
			url: baseURL+"finance/reservationInvoice/submitReservationInvoice",
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

				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
				
				if(response.status == 200){
					resetInvoiceComposer();
					$('#modal-inputEmail').modal('hide');
				}
			}
		});
	
	}
	
});

$('#startDate, #endDate').off('change');
$('#startDate, #endDate').on('change', function(e) {
	getDataInvoiceHistory();
});

$('#keyword').off('keypress');
$("#keyword").on('keypress',function(e) {
    if(e.which == 13) {
        getDataInvoiceHistory();
    }
});
	
function generateDataTable(page){
	getDataInvoiceHistory(page);
}

function getDataInvoiceHistory(page = 1){
	var $tableBody			=	$('#table-invoiceHistory > tbody'),
		columnNumber		=	$('#table-invoiceHistory > thead > tr > th').length,
		startDate			=	$('#startDate').val(),
		endDate				=	$('#endDate').val(),
		keyword				=	$('#keyword').val(),
		dataSend			=	{page:page, keyword:keyword, startDate:startDate, endDate:endDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/reservationInvoice/getDataInvoiceHistory",
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
			
			var data	=	response.result.data,
				rows	=	"";
			
			if(data.length === 0){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				$.each(data, function(index, array) {
					var	reservationDateEnd	=	"";
					if(array.DURATIONOFDAY > 1){
						reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
					}
						
					var inputType	=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					
					var badgeReservationType=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>';
					
					rows	+=	"<tr id='trReservation"+array.IDRESERVATION+"'>"+
									"<td>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
										reservationDateEnd+
										"Duration : "+array.DURATIONOFDAY+" day(s)<br/><br/>"+
										"["+inputType+"] "+array.SOURCENAME+"<br/>"+
										badgeReservationType+"<br/><br/>"+
										"<b>Booking Code :</b><br/>"+array.BOOKINGCODE+
									"</td>"+
									"<td>"+
										"<b id='customerName"+array.IDRESERVATION+"'>"+array.CUSTOMERNAME+"</b><br/>"+
										"Contact : <span id='customerContact"+array.IDRESERVATION+"'>"+array.CUSTOMERCONTACT+"</span><br/>"+
										"Email : <span id='customerEmail"+array.IDRESERVATION+"'>"+array.CUSTOMEREMAIL+"</span><br/><br/>"+
										"Adult : <b>"+array.NUMBEROFADULT+"</b><br/>"+
										"Child : <b>"+array.NUMBEROFCHILD+"</b><br/>"+
										"Infant : <b>"+array.NUMBEROFINFANT+"</b>"+
									"</td>"+
									"<td>"+
										"<b>Hotel<br/></b>"+array.HOTELNAME+"<br/><br/>"+
										"<b>Pick Up<br/></b>"+array.PICKUPLOCATION+"<br/><br/>"+
										"<b>Drop Off<br/></b>"+array.DROPOFFLOCATION+
									"</td>"+
									"<td>"+
										"<b>Invoice No.<br/></b>"+array.INVOICENUMBER+"<br/><br/>"+
										"<b>Invoice Date<br/></b>"+array.INVOICEDATE+"<br/><br/>"+
										"<b>Total Amount<br/></b>"+numberFormat(array.TOTALINVOICEAMOUNT)+"<br/><br/>"+
										"<a class='badge badge-info' target='_blank' href='"+array.URLINVOICE+"'>Show Invoice</a>"+
									"</td>"+
								"</tr>";
					
				});
				$tableBody.html(rows);
			}

			generatePagination("tablePaginationInvoiceHistory", page, response.result.pageTotal);
			generateDataInfo("tableDataCountInvoiceHistory", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
		}
	});
}

reservationInvoiceFunc();