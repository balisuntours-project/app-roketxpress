var $confirmDialog= $('#modal-confirm-action');
if (collectPaymentVendorFunc == null){
	var collectPaymentVendorFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionVendor', 'dataVendorTicket');
			$("#optionVendor").select2();

			getDataCollectPayment();
		});	
	}
}

$('#startDate, #endDate, #optionVendor, #optionCollectStatus, #optionSettlementStatus').off('change');
$('#startDate, #endDate, #optionVendor, #optionCollectStatus, #optionSettlementStatus').on('change', function(e) {
	getDataCollectPayment();
});
	
$('#checkboxViewSettlementRequestOnly').off('click');
$("#checkboxViewSettlementRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewSettlementRequestOnly").is(':checked');
	
	if(checked){
		$("#startDate, #endDate, #optionVendor, #optionCollectStatus, #optionSettlementStatus").attr("disabled", true);
		$("#btnShowAllSetttlementRequest").addClass("d-none");
	} else {
		$("#startDate, #endDate, #optionVendor, #optionCollectStatus, #optionSettlementStatus").attr("disabled", false);
		$("#btnShowAllSetttlementRequest").removeClass("d-none");
	}
	
	getDataCollectPayment();
});

function generateDataTable(page){
	getDataCollectPayment(page);
}

function getDataCollectPayment(page = 1){
	
	var $tableBody		=	$('#table-collectPayment > tbody'),
		columnNumber	=	$('#table-collectPayment > thead > tr > th').length,
		idVendor		=	$('#optionVendor').val(),
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		collectStatus	=	$('#optionCollectStatus').val(),
		settlementStatus=	$('#optionSettlementStatus').val(),
		viewRequestOnly	=	$("#checkboxViewSettlementRequestOnly").is(':checked'),
		dataSend		=	{
								page:page,
								idVendor:idVendor,
								startDate:startDate,
								endDate:endDate,
								viewRequestOnly:viewRequestOnly,
								collectStatus:collectStatus,
								settlementStatus:settlementStatus
							};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/collectPayment/getDataCollectPayment",
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
			
			var data					=	response.result.data,
				totalSettlementRequest	=	response.totalSettlementRequest,
				rows					=	"";
			
			if(totalSettlementRequest > 0){
				$("#totalSettlementRequestAlert").removeClass('d-none');
				$("#totalSettlementRequest").html(totalSettlementRequest);
				
				$('#btnShowAllSetttlementRequest').off('click');
				$("#btnShowAllSetttlementRequest").on('click',function(e) {
					$("#checkboxViewSettlementRequestOnly").prop('checked', true);
					$("#startDate, #endDate, #optionVendor, #optionCollectStatus, #optionSettlementStatus").attr("disabled", true);
					$("#btnShowAllSetttlementRequest").addClass("d-none");
					getDataCollectPayment();
				});
			} else {
				$('#btnShowAllSetttlementRequest').off('click');
				$("#totalSettlementRequestAlert").addClass('d-none');
				$("#totalSettlementRequest").html(0);
			}
			
			if(data.length === 0){
				$('#excelCollectPayment').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$('#excelCollectPayment').removeClass('d-none').on("click").attr("href", response.urlExcelCollectPayment);
				$.each(data, function(index, array) {
					
					var	reservationDateEnd	=	"";
					if(array.DURATIONOFDAY > 1){
						reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+"</b><br/>";
					}
						
					var badgeStatus	=	badgeSettlementStatus	=	"";
					switch(array.STATUS){
						case 0		:	
						case "0"	:	badgeStatus	=	'<span class="badge badge-warning mb-5">Pending</span>'; break;
						case 1		:	
						case "1"	:	badgeStatus	=	'<span class="badge badge-warning mb-5">Collected</span>'; break;
						default		:	badgeStatus	=	'<span class="badge badge-info mb-5">-</span>'; break;
					}

					if(array.NEWFINANCESCHEME == "1"){
						switch(array.STATUSSETTLEMENTREQUEST){
							case -1		:	
							case "-1"	:	badgeSettlementStatus	=	'<span class="badge badge-primary mb-5">Settlement Rejected</span>'; break;
							case 1		:	
							case "1"	:	badgeSettlementStatus	=	'<span class="badge badge-primary mb-5">Settlement Requested</span>'; break;
							case 2		:	
							case "2"	:	badgeSettlementStatus	=	'<span class="badge badge-success mb-5">Settlement Success</span>'; break;
							case 0		:	
							case "0"	:	
							default		:	break;
						}
					}
					
					var amountDescription	=	numberFormat(array.AMOUNT)+" "+array.AMOUNTCURRENCY;
					if(array.AMOUNTCURRENCY != "IDR"){
						amountDescription	+=	" x "+numberFormat(array.EXCHANGECURRENCY)+"<br/>"+numberFormat(array.AMOUNTIDR)+" IDR";
					}
					
					var btnMoreDetails	=	'<a class="button button-primary button-xs text-light" onclick="getMoreDetailsCollectPayment('+array.IDCOLLECTPAYMENT+')"><span><i class="fa fa-info"></i>More Details</span></a>';

					rows			+=	"<tr>"+
											"<td>"+
												"<b>Reservation Date :</b><br/>"+
												"<b class='text-primary'>"+array.RESERVATIONDATESTART+"</b><br/>"+
												reservationDateEnd+"<br/>"+
												"<b>Collect Date :</b><br/>"+
												array.DATECOLLECT+
											"</td>"+
											"<td>"+
												"["+array.VENDORTYPE+"] "+array.VENDORNAME+"<br/>"+
												array.SOURCENAME+" ["+array.BOOKINGCODE+"] <br/><br/>"+
												"<b>"+array.CUSTOMERNAME+"</b><br/>"+
												"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
											"</td>"+
											"<td>"+
												"<b>Reservation Remark : </b><br/>"+array.REMARK+"<br/><br/>"+
												"<b>Payment Remark : </b><br/>"+array.DESCRIPTION+
											"</td>"+
											"<td align='right'>"+amountDescription+"</td>"+
											"<td>"+badgeStatus+badgeSettlementStatus+btnMoreDetails+"</td>"+
										"</tr>";
							
				});
				
			}

			generatePagination("tablePaginationCollectPayment", page, response.result.pageTotal);
			generateDataInfo("tableDataCountCollectPayment", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

function getMoreDetailsCollectPayment(idCollectPayment){
	
	var dataSend		=	{idCollectPayment:idCollectPayment};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/collectPayment/getDetailCollectPayment",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$('#settlementReceiptFileName').val('');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				
				var detailCollectPayment	=	response.detailCollectPayment,
					historyCollectPayment	=	response.historyCollectPayment,
					idCollectPaymentHistory	=	response.idCollectPaymentHistory,
					statusSettlementReceipt	=	response.statusSettlementReceipt,
					reservationDateTimeStr	=	detailCollectPayment.RESERVATIONDATESTART+" "+detailCollectPayment.RESERVATIONTIMESTART,
					rowsHistory				=	"";
					
				if(detailCollectPayment.DURATIONOFDAY > 1){
					reservationDateTimeStr	=	reservationDateTimeStr+" - "+detailCollectPayment.RESERVATIONDATEEND+" "+detailCollectPayment.RESERVATIONTIMEEND;
				}

				var amountDescription	=	numberFormat(detailCollectPayment.AMOUNT)+" "+detailCollectPayment.AMOUNTCURRENCY;
				if(detailCollectPayment.AMOUNTCURRENCY != "IDR"){
					amountDescription	+=	" x "+numberFormat(detailCollectPayment.EXCHANGECURRENCY)+"<br/>"+numberFormat(detailCollectPayment.AMOUNTIDR)+" IDR";
				}
					
				$("#sourceStr").html(detailCollectPayment.SOURCENAME);
				$("#bookingCodeStr").html(detailCollectPayment.BOOKINGCODE);
				$("#reservationTitleStr").html(detailCollectPayment.RESERVATIONTITLE);
				$("#reservationDateStr").html(reservationDateTimeStr);
				$("#customerNameStr").html(detailCollectPayment.CUSTOMERNAME);
				$("#cuctomerContactStr").html(detailCollectPayment.CUSTOMERCONTACT);
				$("#customerEmailStr").html(detailCollectPayment.CUSTOMEREMAIL);
				$("#driverNameTypeStr").html("["+detailCollectPayment.VENDORTYPE+"] "+detailCollectPayment.VENDORNAME);
				
				$("#dateCollectStr").html(detailCollectPayment.DATECOLLECT);
				$("#collectPaymentAmountStr").html(amountDescription);
				$("#reservationRemarkStr").html(detailCollectPayment.REMARK);
				$("#paymentRemarkStr").html(detailCollectPayment.DESCRIPTION);
				$("#settlementReceipt").off('click');
				
				if(detailCollectPayment.SETTLEMENTRECEIPT != ""){
					$("#settlementReceipt").removeAttr('src').attr("src", detailCollectPayment.SETTLEMENTRECEIPT).on('click', function(){
						openZoomReceipt(detailCollectPayment.SETTLEMENTRECEIPT);
					});
				}
				
				if(statusSettlementReceipt && detailCollectPayment.STATUSSETTLEMENTREQUEST == 1){
					$('#uploaderSettlementReceipt').removeClass('d-none');
					$('#idCollectPaymentHistoryWithReceipt').val(idCollectPaymentHistory);
					createUploaderSettlementReceipt(idCollectPayment, idCollectPaymentHistory);
				} else {
					$('#idCollectPaymentHistoryWithReceipt').val(0);
					$('#uploaderSettlementReceipt').addClass('d-none');
				}
				
				if(detailCollectPayment.STATUSSETTLEMENTREQUEST == 1){
					$("#btnApproveCollectPayment").removeClass('d-none').on('click', function(){
						confirmApproveRejectCollectPayment(idCollectPayment, 2);
					});
					$("#btnRejectCollectPayment").removeClass('d-none').on('click', function(){
						confirmApproveRejectCollectPayment(idCollectPayment, -1);
					});
				} else {
					$("#btnApproveCollectPayment, #btnRejectCollectPayment").addClass('d-none').off('click');
				}
				
				if(historyCollectPayment.length > 0){
					$.each(historyCollectPayment, function(index, arrayHistory) {
						var btnZoomReceipt	=	arrayHistory.SETTLEMENTRECEIPT != '' ? '<button class="button button-box button-primary button-xs" type="button"><i class="fa fa-list-alt" onclick="openZoomReceipt(\''+arrayHistory.SETTLEMENTRECEIPT+'\')"></i></button>' : '';
						rowsHistory			+=	'<tr>'+
													'<td class="text-center">'+arrayHistory.DATETIMEINPUT+'</td>'+
													'<td>'+arrayHistory.DESCRIPTION+'</td>'+
													'<td>'+arrayHistory.USERINPUT+'</td>'+
													'<td>'+btnZoomReceipt+'</td>'+
												'</tr>';
					});
					$("#noDataCollectPaymentHistory").remove();
				} else {
					rowsHistory	=	'<tr id="noDataCollectPaymentHistory">'+
										'<td colspan="4" class="text-center text-bold">No history found</td>'+
									'</tr>';
				}
				
				$("#table-collectPaymentHistory > tbody").html(rowsHistory);
				$('#modal-detailCollectPayment').modal('show');
				
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
}

function createUploaderSettlementReceipt(idCollectPayment, idCollectPaymentHistory){
	
	idCollectPayment		=	idCollectPayment == "" ? 0 : idCollectPayment;
	idCollectPaymentHistory	=	idCollectPaymentHistory == "" ? 0 : idCollectPaymentHistory;
	$('.ajax-file-upload-container').remove();
	$("#uploaderSettlementReceipt").uploadFile({
		url: baseURL+"financeVendor/collectPayment/uploadSettlementReceipt/"+idCollectPayment+"/"+idCollectPaymentHistory,
		multiple:false,
		dragDrop:false,
		onSuccess:function(files,data,xhr,pd){
			$(".ajax-file-upload-container").html("");
			if(data.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(data.msg);			
				});
				$('#modalWarning').modal('show');
			} else {
				$('.ajax-file-upload-container').html("");
				$("#settlementReceipt").removeAttr('src').attr("src", data.urlSettlementReceipt);
				$("#settlementReceiptFileName").val(data.settlementReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
	
}

function openZoomReceipt(imageSrc){
	$('#modal-zoomReceiptSettlement').on('show.bs.modal', function() {
		$('#zoomImageReceiptSettlement').attr('src', imageSrc);
	});
	$('#modal-zoomReceiptSettlement').modal('show');
}

function confirmApproveRejectCollectPayment(idCollectPayment, status){
	
	var strStatus	=	status == 2 ? "Approve" : "Reject",
		functionUrl	=	"approveRejectCollectPaymentSettlement",
		confirmText	=	'Are you sure you want to <b>'+strStatus+'</b> this collect payment settlement?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idCollectPayment', idCollectPayment).attr('data-status', status).attr('data-function', functionUrl);
	$confirmDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var idCollectPayment			=	$confirmDialog.find('#confirmBtn').attr('data-idCollectPayment'),
		status						=	$confirmDialog.find('#confirmBtn').attr('data-status'),
		functionUrl					=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		settlementReceiptFileName	=	$("#settlementReceiptFileName").val(),
		idCollectPaymentHistory		=	$("#idCollectPaymentHistoryWithReceipt").val(),
		dataSend					=	{idCollectPayment:idCollectPayment, status:status, settlementReceiptFileName:settlementReceiptFileName, idCollectPaymentHistory:idCollectPaymentHistory};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/collectPayment/"+functionUrl,
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
				$('#modal-detailCollectPayment').modal('hide');
				getDataCollectPayment();
			}
			
		}
	});
	
});

collectPaymentVendorFunc();