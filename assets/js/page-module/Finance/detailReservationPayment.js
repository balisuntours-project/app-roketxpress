var $confirmDialog= $('#modal-confirm-action');

if (detailReservationPaymentFunc == null){
	var detailReservationPaymentFunc	=	function(){
		$(document).ready(function () {			
			setOptionHelper('optionPaymentMethodFilter', 'dataPaymentMethod');
			setOptionHelper('optionSource', 'dataSource');
			setOptionHelper('optionPaymentMethod', 'dataPaymentMethod');
			setOptionHelper('optionSourceAutoPayment', 'dataSourceAutoPayment');
			setOptionHelper("optionPartner", "dataVendorAndDriver");
			setOptionHelper('optionDriverCollect', 'dataDriver');
			setOptionHelper('optionVendorCollect', 'dataVendorTicket');
			$("#optionPartner").select2();
			
			getDataReservationPayment(1);
		});	
	}
}

$('#optionPaymentMethodFilter, #optionPaymentStatusFilter, #optionRefundType, #optionSource, #optionPartner, #startDate, #endDate, #optionOrderBy, #optionOrderType').off('change');
$('#optionPaymentMethodFilter, #optionPaymentStatusFilter, #optionRefundType, #optionSource, #optionPartner, #startDate, #endDate, #optionOrderBy, #optionOrderType').on('change', function(e) {
	getDataReservationPayment();
});

$('#keywordSearchFilter').off('keypress');
$("#keywordSearchFilter").on('keypress',function(e) {
    if(e.which == 13) {
        getDataReservationPayment();
    }
});
	
$('#checkboxUnmatchPaymentOnly').off('click');
$("#checkboxUnmatchPaymentOnly").on('click',function(e) {
	getDataReservationPayment();
});

function generateDataTable(page){
	getDataReservationPayment(page);
}

function getDataReservationPayment(page = 1){
	var $tableBody				=	$('#table-reservationPayment > tbody'),
		columnNumber			=	$('#table-reservationPayment > thead > tr > th').length,
		idPaymentMethod			=	$('#optionPaymentMethodFilter').val(),
		paymentStatus			=	$('#optionPaymentStatusFilter').val(),
		refundType				=	$('#optionRefundType').val(),
		idSource				=	$('#optionSource').val(),
		idPartner				=	$('#optionPartner').val(),
		startDate				=	$('#startDate').val(),
		endDate					=	$('#endDate').val(),
		keywordSearch			=	$('#keywordSearchFilter').val(),
		orderBy					=	$('#optionOrderBy').val(),
		orderType				=	$('#optionOrderType').val(),
		viewUnmatchPaymentOnly	=	$("#checkboxUnmatchPaymentOnly").is(':checked'),
		dataSend				=	{
			page:page,
			idPaymentMethod:idPaymentMethod,
			paymentStatus:paymentStatus,
			refundType:refundType,
			idSource:idSource,
			idPartner:idPartner,
			keywordSearch:keywordSearch,
			startDate:startDate,
			endDate:endDate,
			orderBy:orderBy,
			orderType:orderType,
			viewUnmatchPaymentOnly:viewUnmatchPaymentOnly
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationPayment/getDataReservationPayment",
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

			var rows	=	"";				
			if(response.status != 200){
				$('#excelReport').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td></tr>";
				generatePagination("tablePaginationReservationPayment", 1, 1);
				generateDataInfo("tableDataCountReservationPayment", 0, 0, 0);
			} else {
				var data=	response.result.data;

				if(data.length === 0){
					$('#excelReport').addClass('d-none').off("click").attr("href", "");
					rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
				}
				
				$('#excelReport').removeClass('d-none').on("click").attr("href", response.urlExcelReport);
				$.each(data, function(index, array) {
					var inputType	=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					
					var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>',
						badgeStatus				=	'<span class="badge badge-dark">Unprocessed</span>',
						revenueNominal			=	array.INCOMEAMOUNT,
						revenueNominalSplit		=	revenueNominal.split('.'),
						revenueInteger			=	revenueNominalSplit[0],
						revenueDecimal			=	revenueNominalSplit[1].length > 0 ? revenueNominalSplit[1] : 0,
						btnEditRevenueRsvp		=	array.INCOMEAMOUNTIDR == array.INCOMEAMOUNTFINANCE ? '' :
													'<i class="text-info fa fa-pencil mt-1 pull-right" style="font-size: 18px;" '+
														'data-idReservation='+array.IDRESERVATION+' '+
														'data-source="['+inputType+'] '+array.SOURCENAME+'" '+
														'data-bookingCode="'+array.BOOKINGCODE+'" '+
														'data-customerName="'+array.CUSTOMERNAME+'" '+
														'data-currency="'+array.INCOMEAMOUNTCURRENCY+'" '+
														'data-revenueInteger="'+revenueInteger+'" '+
														'data-revenueDecimal="'+revenueDecimal+'" '+
														'data-currencyExchange="'+array.INCOMEEXCHANGECURRENCY+'" '+
														'data-reservationRevenueIDR="'+array.INCOMEAMOUNTIDR+'" '+
														'data-toggle="modal" data-target="#modal-editorRevenueReservation"></i>';
					switch(array.STATUS){
						case "-1"	:	badgeStatus	=	'<span class="badge badge-danger">Cancel</span>'; break;
						case "0"	:	badgeStatus	=	'<span class="badge badge-dark">Unprocessed</span>'; break;
						case "1"	:	badgeStatus	=	'<span class="badge badge-warning">Admin Processed</span>'; break;
						case "2"	:	badgeStatus	=	'<span class="badge badge-info">Scheduled</span>'; break;
						case "3"	:	badgeStatus	=	'<span class="badge badge-primary">On Process</span>'; break;
						case "4"	:	badgeStatus	=	'<span class="badge badge-success">Done</span>'; break;
						default		:	badgeStatus	=	'<span class="badge badge-dark">Unprocessed</span>'; break;
					}
					
					var refundTypeBadge			=	'';
					switch(array.REFUNDTYPE){
						case "-1"	:	refundTypeBadge	=	'<br/><span class="badge badge-info">Full Refund</span>'; break;
						case "-2"	:	refundTypeBadge	=	'<br/><span class="badge badge-info">Partial Refund</span>'; break;
						case "0"	:	if(array.STATUS == -1) refundTypeBadge	=	'<br/><span class="badge badge-info">No Refund</span>'; break;
						default		:	refundTypeBadge	=	''; break;
					}
					
					var	reservationDateEnd	=	"";
					if(array.DURATIONOFDAY > 1) reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
					
					var paymentData			=	array.PAYMENTDATA,
						elemPaymentData		=	"";
					if(paymentData.length > 0){
						elemPaymentData	+=	"<ul class='todo-list'>";
						$.each(paymentData, function(idxPayment, arrayPayment) {
							
							var badgePayment	=	btnDeletePayment	=	"",
								btnEditPayment	=	arrayPayment.EDITABLE == "1" ? "<button class='remove mr-2' type='button' onclick='detailUpdateReservationPayment("+arrayPayment.IDRESERVATIONPAYMENT+")'><i class='fa fa-pencil'></i></button>" : "";
							switch(arrayPayment.STATUS){
								case "0"	:	badgePayment	=	'<span class="badge badge-warning">Pending</span>'; break;
								case "1"	:	badgePayment	=	'<span class="badge badge-success">Paid</span>'; break;
								case "-1"	:	badgePayment	=	'<span class="badge badge-danger">Cancel/Refund</span>'; break;
								default		:	badgePayment	=	'<span class="badge badge-info">-</span>'; break;
							}
							
							switch(arrayPayment.DELETABLE){
								case "1"	:	btnDeletePayment	=	"<button class='remove' type='button' onclick='deleteReservationPayment("+arrayPayment.IDRESERVATIONPAYMENT+", \""+arrayPayment.PAYMENTMETHODNAME+"\", "+arrayPayment.STATUS+", \""+arrayPayment.DESCRIPTION+"\", \""+arrayPayment.AMOUNTCURRENCY+"\", \""+arrayPayment.AMOUNT+"\")'><i class='fa fa-trash'></i></button>";
												break;
								default		:	break;
							}
							
							var expAmount		=	arrayPayment.AMOUNT.split("."),
								amountInteger	=	expAmount[0],
								amountComma		=	expAmount[1],
								badgeUpselling	=	parseInt(arrayPayment.ISUPSELLING) == 1 ? '<br/><span class="badge badge-primary">Upselling</span>' : '';
							elemPaymentData		+=	"<li id='liPayment"+arrayPayment.IDRESERVATIONPAYMENT+"' "+
														"data-paymentmethod='"+arrayPayment.IDPAYMENTMETHOD+"' data-paymentstatus='"+arrayPayment.STATUS+"' "+
														"data-description='"+arrayPayment.DESCRIPTION+"' data-currency='"+arrayPayment.AMOUNTCURRENCY+"' "+
														"data-priceinteger='"+amountInteger+"' data-pricecomma='"+amountComma+"' data-editable='"+arrayPayment.EDITABLE+"' "+
														"data-idDriverCollect='"+arrayPayment.IDDRIVERCOLLECT+"' data-idVendorCollect='"+arrayPayment.IDVENDORCOLLECT+"' "+
														"data-dateCollect='"+arrayPayment.DATECOLLECT+"' data-arrDateSchedule='"+array.ARRDATESCHEDULE+"' data-isUpselling='"+arrayPayment.ISUPSELLING+"'>"+
														"<div class='list-action py-2 px-0 d-inline text-left' style='width:100px'>"+badgePayment+badgeUpselling+"</div>"+
														"<div class='list-content py-2 text-left'>"+
															arrayPayment.PAYMENTMETHODNAME+"<span class='pull-right'>"+numberFormat(arrayPayment.AMOUNT)+" ["+arrayPayment.AMOUNTCURRENCY+"]</span><br/>"+
															"<small>"+arrayPayment.DESCRIPTION+"</small>"+
														"</div>"+
														"<div class='list-action right py-2 px-2'>"+
															btnEditPayment+btnDeletePayment+
														"</div>"+
													"</li>";
						});
						elemPaymentData	+=	"</ul>";
					}
					
					
					elemPaymentData	+=	"<button class='button button-xs button-primary mb-2 mt-5 mr-1' type='button' data-toggle='modal' "+
										"data-idReservation='"+array.IDRESERVATION+"' data-action='insert' data-target='#modal-reservationPayment' "+
										"data-arrDateSchedule='"+array.ARRDATESCHEDULE+"'><span><i class='fa fa-plus'></i>New Payment</span></button>"+
										"<button class='button button-xs button-warning mb-2 mt-5' type='button' data-toggle='modal' "+
										"data-idReservation='"+array.IDRESERVATION+"' data-reservationTitle='"+array.RESERVATIONTITLE+"' "+
										"data-inputType='"+inputType+"' data-sourceName='"+array.SOURCENAME+"' data-target='#modal-transferDepositPayment' "+
										"data-bookingCode='"+array.BOOKINGCODE+"' data-customerName='"+array.CUSTOMERNAME+"' data-totalIncomeFinance='"+array.INCOMEAMOUNTFINANCE+"' data-isUpselling='"+array.ISUPSELLING+"'>"+
										"<span><i class='fa fa-plus'></i>Transfer Deposit</span></button>";
					var additionalRevenueIDR	=	array.INCOMEAMOUNTCURRENCY != 'IDR' ? " x "+numberFormat(array.INCOMEEXCHANGECURRENCY)+"<br/>[IDR] "+numberFormat(array.INCOMEAMOUNTIDR) : "";
					rows	+=	"<tr>"+
									"<td align='right'>"+array.IDRESERVATION+"</td>"+
									"<td>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
										reservationDateEnd+
										"Duration : "+array.DURATIONOFDAY+" day(s)<br/><br/>"+
										"["+inputType+"] "+array.SOURCENAME+"<br/>"+
										badgeStatus+refundTypeBadge+
									"</td>"+
									"<td>"+
										"<b>"+array.CUSTOMERNAME+"</b><br/>"+
										"Contact : "+array.CUSTOMERCONTACT+"<br/>"+
										"Email : "+array.CUSTOMEREMAIL+"<br/><br/>"+
										"Adult : <b>"+array.NUMBEROFADULT+"</b><br/>"+
										"Child : <b>"+array.NUMBEROFCHILD+"</b><br/>"+
										"Infant : <b>"+array.NUMBEROFINFANT+"</b>"+
									"</td>"+
									"<td>"+
										"<b>Booking Code :</b><br/>"+array.BOOKINGCODE+"<br/><br/>"+
										"<b>Revenue (Rsvp) : </b><br/>["+array.INCOMEAMOUNTCURRENCY+"] "+numberFormat(array.INCOMEAMOUNT)+additionalRevenueIDR+btnEditRevenueRsvp+"<br/><br/>"+
										"<b>Revenue (Finance) : </b><br/>[IDR] "+numberFormat(array.INCOMEAMOUNTFINANCE)+
									"</td>"+
									"<td>"+elemPaymentData+"</td>"+
								"</tr>";
					
				});
				
				generatePagination("tablePaginationReservationPayment", page, response.result.pageTotal);
				generateDataInfo("tableDataCountReservationPayment", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			}
			$tableBody.html(rows);		
		}
	});	
}

$('#modal-editorRevenueReservation').off('shown.bs.modal');
$('#modal-editorRevenueReservation').on('shown.bs.modal', function (e) {
    var idReservation			=	$(e.relatedTarget).attr('data-idReservation'),
		source					=	$(e.relatedTarget).attr('data-source'),
		bookingCode				=	$(e.relatedTarget).attr('data-bookingCode'),
		customerName			=	$(e.relatedTarget).attr('data-customerName'),
		currency				=	$(e.relatedTarget).attr('data-currency'),
		revenueInteger			=	$(e.relatedTarget).attr('data-revenueInteger'),
		revenueDecimal			=	$(e.relatedTarget).attr('data-revenueDecimal'),
		currencyExchange		=	$(e.relatedTarget).attr('data-currencyExchange'),
		reservationRevenueIDR	=	$(e.relatedTarget).attr('data-reservationRevenueIDR');
		
	$("#editorRevenueReservation-source").html(source);
	$("#editorRevenueReservation-bookingCode").html(bookingCode);
	$("#editorRevenueReservation-customerName").html(customerName);
	
	$("#editorRevenueReservation-currency").val(currency);
	$("#editorRevenueReservation-revenueInteger").val(numberFormat(revenueInteger));
	$("#editorRevenueReservation-revenueDecimal").val(revenueDecimal);
	$("#editorRevenueReservation-currencyExchange").val(numberFormat(currencyExchange));
	$("#editorRevenueReservation-reservationRevenueIDR").val(numberFormat(reservationRevenueIDR));
	$("#editorRevenueReservation-idReservation").val(idReservation);
	
	if(currency != 'IDR'){
		$("#editorRevenueReservation-currencyExchange, #editorRevenueReservation-revenueDecimal").prop('readonly', false);
	} else {
		$("#editorRevenueReservation-currencyExchange, #editorRevenueReservation-revenueDecimal").prop('readonly', true);
	}
});

$('#editorRevenueReservation-currency').off('change');
$('#editorRevenueReservation-currency').on('change', function(e) {
	var currencyType	=	$(this).val(),
		currExchangeData=	JSON.parse(localStorage.getItem("currExchangeData"));
	
	if(currencyType != 'IDR'){
		$.each(currExchangeData, function(index, array) {
			if(array.CURRENCY == currencyType){
				$("#editorRevenueReservation-currencyExchange").val(numberFormat(array.EXCHANGETOIDR)).prop('readonly', false);
			}
		});
		$("#editorRevenueReservation-revenueDecimal").val(0).prop('readonly', false);
	} else {
		$("#editorRevenueReservation-currencyExchange").val(1).prop('readonly', true);
		$("#editorRevenueReservation-revenueDecimal").val(0).prop('readonly', true);
	}
	
	calculateReservationRevenueIDR();
});

$('#editorRevenueReservation-revenueInteger, #editorRevenueReservation-revenueDecimal').off('change');
$('#editorRevenueReservation-revenueInteger, #editorRevenueReservation-revenueDecimal').on('change', function(e) {
	calculateReservationRevenueIDR();
});

function calculateReservationRevenueIDR(){
	var revenueInteger		=	$('#editorRevenueReservation-revenueInteger').val().replace(/[^0-9\.]+/g, '');
		revenueDecimal		=	$('#editorRevenueReservation-revenueDecimal').val().replace(/[^0-9\.]+/g, '');
		revenueNominal		=	(revenueInteger+"."+revenueDecimal) * 1,
		currencyExchange	=	$("#editorRevenueReservation-currencyExchange").val().replace(/[^0-9\.]+/g, '') * 1,
		revenueNominalIDR	=	revenueNominal * currencyExchange;
	
	$("#editorRevenueReservation-reservationRevenueIDR").val(numberFormat(revenueNominalIDR));
}

$('#editor-editorRevenueReservation').off('submit');
$('#editor-editorRevenueReservation').on('submit', function(e) {
	e.preventDefault();
	var idReservation			=	$("#editorRevenueReservation-idReservation").val(),
		currency				=	$("#editorRevenueReservation-currency").val(),
		revenueInteger			=	$("#editorRevenueReservation-revenueInteger").val(),
		revenueDecimal			=	$("#editorRevenueReservation-revenueDecimal").val(),
		currencyExchange		=	$("#editorRevenueReservation-currencyExchange").val(),
		reservationRevenueIDR	=	$("#editorRevenueReservation-reservationRevenueIDR").val(),
		dataSend				=	{
			idReservation:idReservation,
			currency:currency,
			revenueInteger:revenueInteger,
			revenueDecimal:revenueDecimal,
			currencyExchange:currencyExchange,
			reservationRevenueIDR:reservationRevenueIDR
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationPayment/updateRevenueReservation",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-editorRevenueReservation :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-editorRevenueReservation :input").attr("disabled", false);
			
			if(currency != 'IDR'){
				$("#editorRevenueReservation-currencyExchange, #editorRevenueReservation-revenueDecimal").prop('readonly', false);
			} else {
				$("#editorRevenueReservation-currencyExchange, #editorRevenueReservation-revenueDecimal").prop('readonly', true);
			}

			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-editorRevenueReservation').modal('hide');
				getDataReservationPayment(1);
			}
		}
	});
});

$('#modal-reservationPayment').off('show.bs.modal');
$('#modal-reservationPayment').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);  
	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {
			var actionType	=	$activeElement.attr('data-action');
			if($activeElement.attr('data-action') == "insert"){
				$("#editor-reservationPayment :input").attr("disabled", false);
				$("#optionPaymentMethod").val($("#optionPaymentMethod option:first").val()).trigger('change');
				$("#idData, #optionPaymentStatus, #paymentPriceInteger, #paymentPriceDecimal").val(0);
				$("#description").val("");
				$("#actionType").val(actionType);
				$("#checkboxUpsellingPayment").prop('checked', false);
				$("#isUpsellingCheckedOrigin").val("0");
				$("#idReservation").val($activeElement.attr('data-idReservation'));
				$("#containerCollectData, #containerOptionDriverCollect, #containerOptionVendorCollect, #contaierOptionDateCollect").addClass("d-none");
				$("#editablePayment").val(1);
				
				var arrDateScheduleSplit	=	$activeElement.attr('data-arrDateSchedule').split(",");
				$('#optionDateCollect').empty();
				if(arrDateScheduleSplit.length > 0){
					for(var i=0; i<arrDateScheduleSplit.length; i++){
						var convertDate		=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("YYYY-MM-DD"),
							convertDateStr	=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("DD MMM YYYY");
						$('#optionDateCollect').append($('<option></option>').val(convertDate).html(convertDateStr));
					}
				}
				
				if($('#contaierOptionDateCollect option').length == 1){
					$("#optionDateCollect").val($("#optionDateCollect option:first").val());
				}
			}
		}
	}
});

$('#optionPaymentMethod').off('change');
$('#optionPaymentMethod').on('change', function(e) {
	var paymentMethod	=	$(this).val(),
		totalOptionDate	=	$('#contaierOptionDateCollect option').length;
	
	if(paymentMethod == 2){
		$("#containerOptionVendorCollect").addClass("d-none");
		$("#containerCollectData, #containerOptionDriverCollect").removeClass("d-none");
		$("#optionDriverCollect").val($("#optionDriverCollect option:first").val());
	} else if(paymentMethod == 7) {
		$("#containerOptionDriverCollect").addClass("d-none");
		$("#containerCollectData, #containerOptionVendorCollect").removeClass("d-none");
		$("#optionVendorCollect").val($("#optionVendorCollect option:first").val());
	} else {
		$("#containerCollectData, #containerOptionDriverCollect, #containerOptionVendorCollect, #contaierOptionDateCollect").addClass("d-none");
	}
	
	if((paymentMethod == 2 || paymentMethod == 7) && totalOptionDate > 1){
		$("#contaierOptionDateCollect").removeClass("d-none");
	} else {
		$("#contaierOptionDateCollect").addClass("d-none");
	}

	if(!dataIdPaymentMethodUpselling.includes(parseInt(paymentMethod))){
		$("#checkboxUpsellingPayment").prop('checked', false).prop('disabled', true);
	} else {
		var isUpsellingCheckedOrigin	=	$("#isUpsellingCheckedOrigin").val();
		$("#checkboxUpsellingPayment").prop('checked', false).prop('disabled', false);
		if(isUpsellingCheckedOrigin == '1') $("#checkboxUpsellingPayment").prop('checked', true);
	}
});

$('#editor-reservationPayment').off('submit');
$('#editor-reservationPayment').on('submit', function(e) {
	e.preventDefault();
	var idData				=	$("#idData").val(),
		actionURL			=	idData == 0 ? "addReservationPayment" : "updateReservationPayment";
		dataForm			=	$("#editor-reservationPayment :input").serializeArray(),
		paymentMethodName	=	$("#optionPaymentMethod option:selected").text(),
		optionPaymentMethod	=	$("#optionPaymentMethod").val(),
		optionPaymentStatus	=	$("#optionPaymentStatus").val(),
		description			=	$("#description").val(),
		editablePayment		=	$("#editablePayment").val(),
		dataSend			=	{paymentMethodName:paymentMethodName, optionPaymentMethod:optionPaymentMethod, optionPaymentStatus:optionPaymentStatus, description:description};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	dataSend['checkboxUpsellingPayment']	=	$("#checkboxUpsellingPayment").is(':checked') ? 1 : 0;
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationPayment/"+actionURL,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-reservationPayment :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-reservationPayment :input").attr("disabled", false);
			
			if(editablePayment != 1){
				$("#optionPaymentMethod, #optionPaymentStatus, #description").attr("disabled", true);
			}

			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-reservationPayment').modal('hide');
				getDataReservationPayment(1);
			}
		}
	});
});

function detailUpdateReservationPayment(idReservationPayment){
	var $liElement	=	$("#liPayment"+idReservationPayment);
	
	if($("#liPayment"+idReservationPayment).length > 0){
		var arrDateScheduleSplit	=	$liElement.attr('data-arrDateSchedule').split(",");
		$('#optionDateCollect').empty();
		if(arrDateScheduleSplit.length > 0){
			for(var i=0; i<arrDateScheduleSplit.length; i++){
				var convertDate		=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("YYYY-MM-DD"),
					convertDateStr	=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("DD MMM YYYY");
				$('#optionDateCollect').append($('<option></option>').val(convertDate).html(convertDateStr));
			}
		}
		
		if($('#contaierOptionDateCollect option').length == 1){
			$("#optionDateCollect").val($("#optionDateCollect option:first").val());
		} else {
			$("#optionDateCollect").val($liElement.attr('data-dateCollect'));
		}

		var paymentMethod	=	$liElement.attr('data-paymentmethod');

		$("#isUpsellingCheckedOrigin").val($liElement.attr('data-isUpselling'));
		$("#optionPaymentMethod").val(paymentMethod).trigger('change');
		$("#optionPaymentStatus").val($liElement.attr('data-paymentstatus'));
		$("#description").val($liElement.attr('data-description'));
		$("#paymentCurrency").val($liElement.attr('data-currency'));
		$("#paymentPriceInteger").val(numberFormat($liElement.attr('data-priceinteger')));
		$("#paymentPriceDecimal").val($liElement.attr('data-pricecomma'));
		$("#optionDriverCollect").val($liElement.attr('data-idDriverCollect'));
		$("#optionVendorCollect").val($liElement.attr('data-idVendorCollect'));
		$("#idData").val(idReservationPayment);
		$("#idReservation").val(0);
		
		if(parseInt($liElement.attr('data-isUpselling')) == 1) $("#checkboxUpsellingPayment").prop('checked', true).prop('disabled', false);
		if(paymentMethod == 1){
			$("#optionPaymentMethod, #optionPaymentStatus, #description").attr("disabled", true);
			$("#editablePayment").val(0);
		} else {
			$("#optionPaymentMethod, #optionPaymentStatus, #description").attr("disabled", false);
			$("#editablePayment").val(1);
		}
		
		$('#modal-reservationPayment').modal('show');
	} else {
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Reservation payment is no longer available, please refresh your data");			
		});
		$('#modalWarning').modal('show');
	}
}

function deleteReservationPayment(idReservationPayment, paymentMethod, paymentStatus, description, currency, paymentAmount){
	var badgePayment	=	"";
	switch(paymentStatus){
		case 0		:	
		case "0"	:	
						badgePayment	=	'<div class="badge badge-warning">Pending</div>'; break;
		case 1		:	
		case "1"	:	
						badgePayment	=	'<div class="badge badge-success">Paid</div>'; break;
		case -1		:	
		case "-1"	:	
						badgePayment	=	'<div class="badge badge-danger">Cancel</div>'; break;
		default		:	badgePayment	=	'<div class="badge badge-info">-</div>'; break;
	}
	var confirmText	=	'Payment data will be deleted. Details ;'+
						'<div class="order-details-customer-info mb-10 mt-10">'+
							'<ul class="ml-10">'+
								'<li> <span>Payment Method</span> <span>'+paymentMethod+'</span> </li>'+
								'<li> <span>Status</span> <span>'+badgePayment+'</span> </li>'+
								'<li> <span>Description</span> <span>'+description+'</span> </li>'+
								'<li> <span>Currency</span> <span>'+currency+'</span> </li>'+
								'<li> <span>Amount</span> <span>'+numberFormat(paymentAmount)+'</span> </li>'+
							'</ul>'+
						'</div>'+
						'Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idReservationPayment).attr('data-function', "deleteReservationPayment");
	$confirmDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData	=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
		funcName=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		dataSend=	{idReservationPayment:idData};

	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationPayment/"+funcName,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$confirmDialog.modal('hide');
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
				getDataReservationPayment(1);
			}
		}
	});
});

function openFormUploadExcelPaymentOTA(){
	var $tableBody			=	$('#table-resultUploadExcelPaymentOTA > tbody'),
		idSourceAutoPayment	=	$("#optionSourceAutoPayment").val();
	
	$tableBody.html('<tr><td colspan="7" class="text-center"><b>No Data</b></td></tr>');
	$("#btnCloseUploadExcelPaymentOTA").removeClass("d-none");
	$("#btnUploadExcelPaymentOTA").addClass("d-none");
	
	if(idSourceAutoPayment == 2) $("#optionFormatType").attr('disabled', false);
	if(idSourceAutoPayment != 2) $("#optionFormatType").attr('disabled', true);
	
	toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
	createUploaderPaymentOTA();
}

$('#optionSourceAutoPayment').off('change');
$('#optionSourceAutoPayment').on('change', function(e) {
	var idSourceAutoPayment	=	$(this).val(),
	allowSourceEnableFormat	=	[1,2];

	if(allowSourceEnableFormat.includes(parseInt(idSourceAutoPayment))){
		$('#optionFormatType option[data-idSource!="'+idSourceAutoPayment+'"]').prop('disabled', true);
		$('#optionFormatType option[data-idSource="'+idSourceAutoPayment+'"]').prop('disabled', false);
		$("#optionFormatType").prop('disabled', false).val($('#optionFormatType option:not([disabled])').first().val());
	} else {
		$("#optionFormatType").prop('disabled', true).val(0);
	}
});

function createUploaderPaymentOTA(){
	$('.ajax-file-upload-container').remove();
	$("#uploadPaymentScanningResult").addClass("d-none");
	$("#uploaderExcelPaymentOTA").uploadFile({
		url: baseURL+"finance/detailReservationPayment/uploadExcelPaymentOTA",
		multiple:false,
		dragDrop:false,
		allowedTypes: "xls, xlsx",
		onSubmit:function(files){
			$('#window-loader').modal('show');
			$(".ajax-file-upload-container").addClass("text-center");
		},
		onSuccess:function(files,data,xhr,pd){
			$('#window-loader').modal('hide');
			$(".ajax-file-upload-statusbar").remove();
			if(data.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(data.msg);			
				});
				$('#modalWarning').modal('show');
			} else {
				$("#uploadPaymentContainer").addClass("d-none");
				$("#uploadPaymentScanningResult").removeClass("d-none");
				scanExcelPaymentOTA(data.fileName, data.extension);
			}
		}
	});
}

$('#btnCloseUploadExcelPaymentOTA').off('click');
$('#btnCloseUploadExcelPaymentOTA').on('click', function(e) {
	toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
	$("#btnUploadExcelPaymentOTA").removeClass("d-none");
	$("#btnCloseUploadExcelPaymentOTA").addClass("d-none");
});

function scanExcelPaymentOTA(fileName, extension){
	var idSource			=	$("#optionSourceAutoPayment").val(),
		formatAutoPayment	=	$("#optionFormatType").val(),
		dataSend			=	{fileName:fileName, extension:extension, idSource:idSource, formatAutoPayment:formatAutoPayment},
		$tableBody			=	$('#table-resultUploadExcelPaymentOTA > tbody'),
		columnNumber		=	$('#table-resultUploadExcelPaymentOTA > thead > tr > th').length;
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationPayment/scanExcelPaymentOTA",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
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
				var resScan	=	response.resScan,
					number	=	1,
					rows	=	"";
				$.each(resScan, function(index, array) {
					
					var excelAmount		=	array.EXCELAMOUNT,
						rowExcelAmount	=	"";
					
					if(excelAmount.length > 0){
						$.each(excelAmount, function(idxExcelAmount, arrayExcelAmount) {
							rowExcelAmount	+=	"["+arrayExcelAmount.CURRENCY+"] "+arrayExcelAmount.SETTLEMENTSTATUS+" <span class='pull-right'>"+numberFormat(arrayExcelAmount.AMOUNT)+"</span><br/>";
							
							if (arrayExcelAmount.AMOUNTDISCOUNT	 !== undefined && 
								arrayExcelAmount.AMOUNTDISCOUNT	 !== null && 
								arrayExcelAmount.AMOUNTDISCOUNT	 !== '' && 
								arrayExcelAmount.AMOUNTDISCOUNT	 !== 0) {
								rowExcelAmount	+=	"["+arrayExcelAmount.CURRENCY+"] Disc <span class='pull-right'>"+numberFormat(arrayExcelAmount.AMOUNTDISCOUNT)+"</span><br/>";
							}
						});
					}
					
					rows	+=	"<tr class='trResultScanPaymentOTA' id='trResultScanPaymentOTA"+array.IDRESERVATION+"'>"+
									"<td align='right'>"+number+"</td>"+
									"<td>"+array.BOOKINGCODE+"</td>"+
									"<td>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
										"<b>"+array.CUSTOMERNAME+"</b><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATE+"</b>"+
									"</td>"+
									"<td>"+rowExcelAmount+"</td>"+
									"<td>["+array.AMOUNTCURRENCY+"] <span class='pull-right'>"+numberFormat(array.AMOUNTDB)+"</span></td>"+
									"<td class='tdMatchStatus' data-idReservation='"+array.IDRESERVATION+"'>"+array.MATCHSTATUS+"</td>"+
									"<td class='tdPaymentStatus' data-idReservation='"+array.IDRESERVATION+"'>"+array.PAYMENTSTATUS+"</td>"+
								"</tr>";
					number++;
				});
				

			} else {
				rows	=	"<tr><td colspan='"+columnNumber+"'><center><b>No data match found</b></center></td></tr>";
				$("#uploadPaymentContainer").removeClass("d-none");
				$("#uploadPaymentScanningResult").addClass("d-none");
			}
			
			$tableBody.html(rows);
		}
	});
}

$('#optionMatchStatus, #optionPaymentStatusOTA').off('change');
$('#optionMatchStatus, #optionPaymentStatusOTA').on('change', function(e) {
	var matchStatus		=	$('#optionMatchStatus').val(),
		paymentStatus	=	$('#optionPaymentStatusOTA').val();
		
	$('table').each(function(ind) {
		$(this).find('tr').each(function() {
			var matchStatusTable	=	$(this).find('td.tdMatchStatus').html(),
				paymentStatusTable	=	$(this).find('td.tdPaymentStatus').html(),
				idReservationTable	=	$(this).find('td.tdPaymentStatus').attr('data-idReservation');
			if((matchStatus == matchStatusTable || matchStatus == "") && (paymentStatus == paymentStatusTable || paymentStatus == "")){
				$("#trResultScanPaymentOTA"+idReservationTable).removeClass("d-none");
			} else {
				$("#trResultScanPaymentOTA"+idReservationTable).addClass("d-none");
			}
		});
	});
});

$('#modal-transferDepositPayment').off('show.bs.modal');
$('#modal-transferDepositPayment').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);  
	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {
			var idReservation		=	$activeElement.attr('data-idReservation'),
				customerName		=	$activeElement.attr('data-customerName'),
				bookingCode			=	$activeElement.attr('data-bookingCode')
				totalIncomeFinance	=	$activeElement.attr('data-totalIncomeFinance');
			$("#transferDepositPaymentFromCustomerName").html(customerName);
			$("#transferDepositPaymentFromReservationTitle").html($activeElement.attr('data-reservationTitle'));
			$("#transferDepositPaymentFromInputType").html($activeElement.attr('data-inputType'));
			$("#transferDepositPaymentFromSourceName").html($activeElement.attr('data-sourceName'));
			$("#transferDepositPaymentFromBookingCode").html(bookingCode);
			$("#transferDepositPaymentFromRevenue, #maxTransferredDeposit").html(numberFormat(totalIncomeFinance));
			
			$('#nominalTransferredDeposit').off('keypress');
			$('#nominalTransferredDeposit').on('keypress', function(e) {
				maskNumberInput(0, totalIncomeFinance, 'nominalTransferredDeposit');
			});
			
			$('#btnModalSearchReservationTransferDepositPayment').attr('data-customerName', customerName);
			$('#btnModalSearchReservationTransferDepositPayment').attr('data-bookingCode', bookingCode);
			$('#btnModalSearchReservationTransferDepositPayment').attr('data-idReservation', idReservation);
			$('#idReservationFrom').val(idReservation);
			
			$("#transferDepositPaymentToCustomerName, #transferDepositPaymentToReservationTitle, #transferDepositPaymentToInputType, #transferDepositPaymentToSourceName, #transferDepositPaymentToBookingCode").html("-");
			$('#idReservationTo').val(0);
		}
	}
});

$('#modal-searchReservationTransferDepositPayment').off('show.bs.modal');
$('#modal-searchReservationTransferDepositPayment').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);  
	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {
			var idReservation	=	$activeElement.attr('data-idReservation'),
				customerName	=	$activeElement.attr('data-customerName'),
				bookingCode		=	$activeElement.attr('data-bookingCode'),
				searchKeyword	=	customerName+" | "+bookingCode;

			$("#keywordSearchReservationTransferDepositPayment").val(searchKeyword);
			searchReservationByKeyword(idReservation, searchKeyword);
			
			$('#keywordSearchReservationTransferDepositPayment').off('keydown');
			$('#keywordSearchReservationTransferDepositPayment').on('keydown', function(e) {
				if(e.which === 13){
					searchReservationByKeyword(idReservation, $('#keywordSearchReservationTransferDepositPayment').val());
					e.preventDefault();
				}
			});
		}
	}
});

function searchReservationByKeyword(idReservation, searchKeyword){
	var dataSend		=	{idReservation:idReservation, searchKeyword:searchKeyword},
		$tableBody		=	$('#tbody-searchReservationTransferDepositPayment');
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/detailReservationPayment/searchReservationByKeyword",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$tableBody.html("<tr><td><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			var rows	=	"";

			if(response.status == 200){
				var result	=	response.result;
				
				$.each(result, function(index, array) {
					var inputType	=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					
					var	reservationDateEnd	=	"";
					if(array.DURATIONOFDAY > 1){
						reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
					}
					
					var btnSelectReservation=	'<br/><br/><button class="button button-xs button-primary" type="button" '+
														'onclick="setReservationTransferDepositPayment(\''+array.IDRESERVATION+'\', \''+array.CUSTOMERNAME+'\', \''+array.RESERVATIONTITLE+'\', \''+array.BOOKINGCODE+'\', \''+inputType+'\', \''+array.SOURCENAME+'\')">'+
													'<span><i class="fa fa-check"></i>Select This Reservation</span>'+
												'</button>';
					
					rows	+=	"<tr>"+
									"<td width='250'>"+
										"<b>"+array.CUSTOMERNAME+"</b><br/>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
										reservationDateEnd+
										btnSelectReservation+
									"</td>"+
									"<td width='100'>"+
										"<b>"+array.BOOKINGCODE+"</b><br/>"+
										"["+inputType+"] "+array.SOURCENAME+"<br/>"+
									"</td>"+
									"<td>"+
										"<b>Remark :</b><br/>"+array.REMARK+"<br/><br/>"+
									"</td>"+
									"<td>"+
										"<b>Tour Plan :</b><br/>"+array.TOURPLAN+"<br/><br/>"+
									"</td>"+
								"</tr>";
				});
				$('#modalWarning').modal('hide');

			} else {
				rows	=	"<tr><td colspan='4'><center><b>"+response.msg+"</b></center></td></tr>";
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}
			
			$tableBody.html(rows);
		}
	});
}

function setReservationTransferDepositPayment(idReservation, customerName, reservationTitle, bookingCode, inputType, sourceName){
	$("#transferDepositPaymentToCustomerName").html(customerName);
	$("#transferDepositPaymentToReservationTitle").html(reservationTitle);
	$("#transferDepositPaymentToInputType").html(inputType);
	$("#transferDepositPaymentToSourceName").html(sourceName);
	$("#transferDepositPaymentToBookingCode").html(bookingCode);
	$('#idReservationTo').val(idReservation);
	
	$('#modal-searchReservationTransferDepositPayment').modal('hide');
}

$('#editor-transferDepositPayment').off('submit');
$('#editor-transferDepositPayment').on('submit', function(e) {
	e.preventDefault();
	var idReservationFrom		=	$("#idReservationFrom").val(),
		idReservationTo			=	$("#idReservationTo").val(),
		nominalTransfer			=	$("#nominalTransferredDeposit").val().replace(/[^0-9\.]+/g, '') * 1,
		maxTransferredDeposit	=	$("#maxTransferredDeposit").html().replace(/[^0-9\.]+/g, '') * 1,
		bookingCodeFrom			=	$("#transferDepositPaymentFromBookingCode").html(),
		bookingCodeTo			=	$("#transferDepositPaymentToBookingCode").html(),
		dataSend				=	{
										idReservationFrom:idReservationFrom,
										idReservationTo:idReservationTo,
										nominalTransfer:nominalTransfer,
										maxTransferredDeposit:maxTransferredDeposit,
										bookingCodeFrom:bookingCodeFrom,
										bookingCodeTo:bookingCodeTo
									},
		errorMsg				=	"";

	if(idReservationFrom == 0 || idReservationFrom == "" || typeof idReservationFrom === 'undefined'){
		errorMsg			=	"Invalid submission data. Please refresh the page and try this action again";
	} else if(idReservationTo == 0 || idReservationTo == "" || typeof idReservationTo === 'undefined'){
		errorMsg			=	"Please select the reservation to which the deposit will be transferred first";
	} else if(nominalTransfer <= 0) {
		errorMsg			=	"Deposit amount transferred cannot be zero (0)";
	} else if(nominalTransfer > maxTransferredDeposit) {
		errorMsg			=	"Deposit amount transferred cannot be more than <b>"+$("#maxTransferredDeposit").html()+" IDR</b>";
	}
	
	if(errorMsg != ""){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html(errorMsg);
		});
		$('#modalWarning').modal('show');
	} else {
		$.ajax({
			type: 'POST',
			url: baseURL+"finance/detailReservationPayment/saveTransferDepositPayment",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#editor-transferDepositPayment :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#editor-transferDepositPayment :input").attr("disabled", false);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					$('#modal-transferDepositPayment').modal('hide');
					getDataReservationPayment(1);
				}
			}
		});
	}
});

detailReservationPaymentFunc();