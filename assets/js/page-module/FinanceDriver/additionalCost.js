var $confirmDialog= $('#modal-confirm-action');
if (additionalCostFunc == null){
	var additionalCostFunc	=	function(){
		$(document).ready(function () {
			localStorage.setItem('viewConfirmDialogValidateAdditionalCost', '1');
			setOptionHelper('optionApprovalDriverType', 'dataDriverType');
			setOptionHelper('optionApprovalDriver', 'dataDriver');
			$('#optionApprovalDriverType').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionApprovalDriver', 'dataDriver', false, false, this.value);
				} else {
					setOptionHelper('optionApprovalDriver', 'dataDriver');
				}
			});
			
			setOptionHelper('optionHistoryDriverType', 'dataDriverType');
			setOptionHelper('optionHistoryDriver', 'dataDriver');
			$('#optionHistoryDriverType').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionHistoryDriver', 'dataDriver', false, false, this.value);
				} else {
					setOptionHelper('optionHistoryDriver', 'dataDriver');
				}
			});
			
			setOptionHelper('optionAdditionalCostType', 'dataAdditionalCostType');
			setOptionHelper('optionDriverSearch', 'dataDriver');
			$("#optionDriverSearch").select2({
				dropdownParent: $('#modal-selectReservationAdditionalCost')
			});
			
			getAdditionalCostApproval();
			getAdditionalCostHistory();
		});	
	}
}

$('#optionApprovalDriverType, #optionApprovalDriver, #startDateApproval, #endDateApproval').off('change');
$('#optionApprovalDriverType, #optionApprovalDriver, #startDateApproval, #endDateApproval').on('change', function(e) {
	getAdditionalCostApproval();
});
	
$('#checkboxViewRequestOnly').off('click');
$("#checkboxViewRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewRequestOnly").is(':checked');
	
	if(checked){
		$("#optionApprovalDriverType, #optionApprovalDriver, #startDateApproval, #endDateApproval").attr("disabled", true);
	} else {
		$("#optionApprovalDriverType, #optionApprovalDriver, #startDateApproval, #endDateApproval").attr("disabled", false);
	}
	
	getAdditionalCostApproval();
});

$('#optionHistoryDriverType, #optionHistoryDriver, #startDateHistory, #endDateHistory').off('change');
$('#optionHistoryDriverType, #optionHistoryDriver, #startDateHistory, #endDateHistory').on('change', function(e) {
	getAdditionalCostHistory();
});

function generateDataTable(page){
	getAdditionalCostHistory(page);
}

function getAdditionalCostApproval(){
	var $tableBody		=	$('#table-additionalCostApproval > tbody'),
		columnNumber	=	$('#table-additionalCostApproval > thead > tr > th').length,
		idDriverType	=	$('#optionApprovalDriverType').val(),
		idDriver		=	$('#optionApprovalDriver').val(),
		startDate		=	$('#startDateApproval').val(),
		endDate			=	$('#endDateApproval').val(),
		viewRequestOnly	=	$("#checkboxViewRequestOnly").is(':checked'),
		dataSend		=	{idDriverType:idDriverType, idDriver:idDriver, startDate:startDate, endDate:endDate, viewRequestOnly:viewRequestOnly};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalCost/getDataAdditionalCostApproval",
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
				totalData		=	iMutasi	=	0,
				descTotalData	=	"No data to validate",
				rows			=	"";
			
			if(response.status != 200){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td></tr>";
			} else {
				$.each(data, function(index, array) {
					var badgeStatus	=	"";
					switch(array.STATUSAPPROVAL){
						case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>';
										break;
						case "-1"	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>';
										break;
						case "0"	:
						default		:	badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>';
										break;
					}

					var hideClass	=	iMutasi == 0 ? "" : "d-none";
					var btnValidate	=	'<button class="button button-box button-primary button-sm '+hideClass+'" '+
												'onclick="setValidateAdditionalCost('+array.IDRESERVATIONADDITIONALCOST+', 1)" >'+
											'<i class="fa fa-check"></i>'+
										'</button>';
					var btnIgnore	=	'<button class="button button-box button-warning button-sm '+hideClass+'" '+
												'onclick="setValidateAdditionalCost('+array.IDRESERVATIONADDITIONALCOST+', -1)" >'+
											'<i class="fa fa-times"></i>'+
										'</button>';
					rows			+=	"<tr id='trAdditionalCost"+array.IDRESERVATIONADDITIONALCOST+"'>"+
											"<td id='containerTextValidate"+array.IDRESERVATIONADDITIONALCOST+"'>"+badgeStatus+"<br/>"+array.DATETIMEINPUT+"</td>"+
											"<td>"+
												"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
												"<b>"+array.PRODUCTNAME+"</b><br/><br/>"+
												"Cust : "+array.CUSTOMERNAME+
											"</td>"+
											"<td>"+
												"<b>Driver : "+array.DRIVERNAME+"</b><br/>"+
												"<b id='additionalCostType"+array.IDRESERVATIONADDITIONALCOST+"'>"+array.ADDITIONALCOSTTYPE+"</b><br/>"+
												"Description : <span id='additionalCostDescription"+array.IDRESERVATIONADDITIONALCOST+"'>"+array.DESCRIPTION+"</span>"+
											"</td>"+
											"<td align='right' id='additionalCostNominal"+array.IDRESERVATIONADDITIONALCOST+"'>"+numberFormat(array.NOMINAL)+"</td>"+
											"<td>"+
												"<a href='#' data-imgsrc='"+array.IMAGERECEIPT+"' class='zoomImage'>"+
													"<img src='"+array.IMAGERECEIPT+"' id='imageReceipt"+array.IDRESERVATIONADDITIONALCOST+"' width='150px'>"+
												"</a>"+
											"</td>"+
											"<td id='containerBtn"+array.IDRESERVATIONADDITIONALCOST+"' align='center'>"+
												btnValidate+"<br>"+btnIgnore+
											"</td>"+
										"</tr>";
					iMutasi++;
					totalData++;
				});
			}

			descTotalData	=	totalData == 0 ? descTotalData : "<b>"+totalData+"</b> data need to validate";
			$("#approvalDescription").html("<i class='fa fa-info'></i> "+descTotalData);
			$tableBody.html(rows);
			$('.zoomImage').off('click');
			$(".zoomImage").on("click", function() {
				var imgSrc	=	$(this).attr('data-imgSrc');
				$('#zoomReceiptImage').attr('src', imgSrc);
				$('#modal-zoomReceiptImage').modal('show');
			});
		}
	});
}

function setValidateAdditionalCost(idAdditionalCost, status){
	
	var viewConfirmDialog	=	localStorage.getItem('viewConfirmDialogValidateAdditionalCost'),
		txtValidateStatus	=	status == 1 ? "Approved" : "Rejected";
	
	if(viewConfirmDialog == '1'){
		$("#costType-confirmation").html($("#additionalCostType"+idAdditionalCost).html());
		$("#description-confirmation").html($("#additionalCostDescription"+idAdditionalCost).html());
		$("#nominal-confirmation").html($("#additionalCostNominal"+idAdditionalCost).html());
		
		$("#textValidateStatus").html(txtValidateStatus);
		$('#imageReceipt-confirmation').attr('src', $('#imageReceipt'+idAdditionalCost).attr('src'));
		$("#confirmBtnValidateAdditionalCost").attr('data-idAdditionalCost', idAdditionalCost).attr('data-status', status);
		$('#modal-confirmValidateAdditionalCost').modal('show');
	} else {
		submitValidateAdditionalCost(idAdditionalCost, status);
	}
	
}

$('#confirmBtnValidateAdditionalCost').off('click');
$('#confirmBtnValidateAdditionalCost').on('click', function(e) {
	e.preventDefault();
	if($('#disableConfirm').is(":checked")){
		localStorage.setItem('viewConfirmDialogValidateAdditionalCost', '0');
	}
	submitValidateAdditionalCost($("#confirmBtnValidateAdditionalCost").attr('data-idAdditionalCost'), $("#confirmBtnValidateAdditionalCost").attr('data-status'));
});

function submitValidateAdditionalCost(idAdditionalCost, status){
	var dataSend	=	{idAdditionalCost:idAdditionalCost, status:status};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalCost/submitValidateAdditionalCost",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$('#window-loader').modal('show');
			$('#modal-confirmValidateAdditionalCost').modal('hide');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');

			if(response.status == 200){
					
				var badgeStatus			=	"",
					status				=	response.statusApproval,
					idAdditionalCost	=	response.idAdditionalCost;
				switch(status){
					case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>';
									break;
					case -1		:	
					case "-1"	:	
									badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>';
									break;
					case "0"	:
					default		:
									badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>';
									break;
				}
				
				var trAdditionalCost		=	$("#trAdditionalCost"+idAdditionalCost);
				trAdditionalCost.find('td:first').find('span.badge').remove();
				trAdditionalCost.find('td:first').prepend(badgeStatus);
				
				trAdditionalCost.find('td:last').find('button.button-box').remove();
				trAdditionalCost.next().find('td:last').find('button.button-box').removeClass('d-none');
				
				$("#containerBtn"+idAdditionalCost).html("");
				$("#containerTextValidate"+idAdditionalCost).append("<br/><br/>"+response.strStatus+" by : <br/>"+response.userValidate);
				
				var toastType	=	status == 1 ? "success" : "warning";
				toastr[toastType](response.msg);
				getAdditionalCostHistory();
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

function getAdditionalCostHistory(page = 1){
	
	var $tableBody		=	$('#table-additionalCostHistory > tbody'),
		columnNumber	=	$('#table-additionalCostHistory > thead > tr > th').length,
		idDriverType	=	$('#optionHistoryDriverType').val(),
		idDriver		=	$('#optionHistoryDriver').val(),
		startDate		=	$('#startDateHistory').val(),
		endDate			=	$('#endDateHistory').val(),
		dataSend		=	{page:page, idDriverType:idDriverType, idDriver:idDriver, startDate:startDate, endDate:endDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalCost/getDataAdditionalCostHistory",
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
					
					var badgeStatus	=	"";
					switch(array.STATUSAPPROVAL){
						case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>';
										break;
						case "-1"	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>';
										break;
						case "0"	:
						default		:
										badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>';
										break;
					}
					
					rows	+=	"<tr>"+
									"<td>"+badgeStatus+"<br/>"+array.DATETIMEINPUT+"</td>"+
									"<td>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
										"<b>"+array.PRODUCTNAME+"</b><br/><br/>"+
										"Cust : "+array.CUSTOMERNAME+
									"</td>"+
									"<td>"+
										"<b>Driver : "+array.DRIVERNAME+"</b><br/>"+
										"<b>"+array.ADDITIONALCOSTTYPE+"</b><br/>"+
										"Description : <span>"+array.DESCRIPTION+"</span>"+
									"</td>"+
									"<td>"+
										"<b>User : "+array.USERAPPROVAL+"</b><br/>"+
										"Date Time : <span>"+array.DATETIMEAPPROVAL+"</span>"+
									"</td>"+
									"<td align='right'>"+numberFormat(array.NOMINAL)+"</td>"+
									"<td>"+
										"<a href='#' data-imgsrc='"+array.IMAGERECEIPT+"' class='zoomImage'>"+
											"<img src='"+array.IMAGERECEIPT+"' id='imageReceipt"+array.IDRESERVATIONADDITIONALCOST+"' width='150px'>"+
										"</a>"+
									"</td>"+
								"</tr>";
				});
				
			}

			generatePagination("tablePaginationHistoryAdditionalCost", page, response.result.pageTotal);
			generateDataInfo("tableDataCountHistoryAdditionalCost", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
			$('.zoomImage').off('click');
			$(".zoomImage").on("click", function() {
				var imgSrc	=	$(this).attr('data-imgSrc');
				$('#zoomReceiptImage').attr('src', imgSrc);
				$('#modal-zoomReceiptImage').modal('show');
			});
			
		}
	});
	
}

$('#modal-selectReservationAdditionalCost').off('show.bs.modal');
$('#modal-selectReservationAdditionalCost').on('show.bs.modal', function(event) {
	$("#reservationKeyword").val("");
	$("#containerSelectReservationResult").html('<div class="col-sm-12 text-center mx-auto my-auto">'+
													'<h2><i class="fa fa-list-alt text-warning"></i></h2>'+
													'<b class="text-warning">Results goes here</b>'+
												'</div>');
	searchListReservationForAdditionalCost();

	$('#container-selectReservationAdditionalCost').off('submit');
	$('#container-selectReservationAdditionalCost').on('submit', function(e) {
		e.preventDefault();
		searchListReservationForAdditionalCost();
	});

	$('#reservationKeyword').off('keydown');
	$('#reservationKeyword').on('keydown', function(e) {
		if(e.which === 13){
			searchListReservationForAdditionalCost();
		}
	});

	$('#reservationDate, #optionDriverSearch').off('change');
	$('#reservationDate, #optionDriverSearch').on('change', function(e) {
		e.preventDefault();
		searchListReservationForAdditionalCost();
	});
});

function searchListReservationForAdditionalCost(){
	var idDriver			=	$("#optionDriverSearch").val(),
		reservationDate		=	$("#reservationDate").val(),
		reservationKeyword	=	$("#reservationKeyword").val(),
		dataSend			=	{
									idDriver:idDriver,
									reservationDate:reservationDate,
									reservationKeyword:reservationKeyword
								};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalCost/searchListReservationForAdditionalCost",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#container-selectReservationAdditionalCost :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#container-selectReservationAdditionalCost :input").attr("disabled", false);

			if(response.status == 200){
				var reservationList	=	response.reservationList,
					reservationRows	=	"";
					
				$("#optionDriverSearch").select2({
					dropdownParent: $('#modal-selectReservationAdditionalCost')
				});
				$.each(reservationList, function(index, array) {

					var	reservationDateEnd		=	'',
						reservationDateEndStr	=	'',
						idReservationDetails	=	array.IDRESERVATIONDETAILS;
					if(array.DURATIONOFDAY > 1){
						reservationDateEnd		=	' - <b class="text-secondary">'+array.RESERVATIONDATEEND+'</b>';
						reservationDateEndStr	=	array.RESERVATIONDATEEND;
					}
					var reservationDate		=	array.RESERVATIONDATESTART+'</b>'+reservationDateEnd;
					reservationRows			+=	'<div class="col-sm-12 pb-1 mb-5 border-bottom" id="rowReservationDetail'+idReservationDetails+'"'+
												' data-idReservationDetails="'+idReservationDetails+'"'+
												' data-idDriver="'+array.IDDRIVER+'"'+
												' data-source="'+array.SOURCENAME+'"'+
												' data-bookingCode="'+array.BOOKINGCODE+'"'+
												' data-reservationTitle="'+array.RESERVATIONTITLE+'"'+
												' data-reservationDate="'+array.RESERVATIONDATESTART+' - '+reservationDateEndStr+'"'+
												' data-customerName="'+array.CUSTOMERNAME+'"'+
												' data-driverName="'+array.DRIVERNAME+'"'+
												'">'+
													'<div class="row pt-10 pb-1">'+
														'<div class="col-sm-12">'+
															'<span class="badge badge-outline badge-primary">'+array.SOURCENAME+'</span> '+
															'<b>'+array.BOOKINGCODE+'</b><br/>'+
															'<b class="text-primary">'+reservationDate+
															'<button type="button" class="button button-sm pull-right" onclick="generateAdditionalCostDataForm('+idReservationDetails+')"><span><i class="fa fa-pencil"></i>Choose</span></button>'+
														'</div>'+
														'<div class="col-sm-12">'+
															'<b>'+array.RESERVATIONTITLE+'</b><br/>'+
															'<p>'+
																'Customer : '+array.CUSTOMERNAME+' ('+array.CUSTOMERCONTACT+' / '+array.CUSTOMEREMAIL+')<br/>'+
																'Driver : '+array.DRIVERNAME+
															'</p>'+
														'</div>'+
													'</div>'+
												'</div>';
					
				});
				
				$("#containerSelectReservationResult").html(reservationRows);
			} else {
				$("#containerSelectReservationResult").html('<div class="col-sm-12 text-center mx-auto my-auto">'+
																'<h2><i class="fa fa-search-minus text-danger"></i></h2>'+
																'<b class="text-danger">No active reservations found based on the date and driver you choose</b>'+
															'</div>');
			}
			
		}
	});
}

function generateAdditionalCostDataForm(idReservationDetails){
	
	var elemRowReservationDetail	=	$("#rowReservationDetail"+idReservationDetails),
		idDriver					=	elemRowReservationDetail.attr('data-idDriver');
	
	$('#idReservationDetail').val(idReservationDetails);
	$('#idDriver').val(idDriver);
	$('#sourceNewAdditionalCost').html(elemRowReservationDetail.attr('data-source'));
	$('#bookingCodeNewAdditionalCost').html(elemRowReservationDetail.attr('data-bookingCode'));
	$('#reservationTitleNewAdditionalCost').html(elemRowReservationDetail.attr('data-reservationTitle'));
	$('#reservationDateNewAdditionalCost').html(elemRowReservationDetail.attr('data-reservationDate'));
	$('#customerNameNewAdditionalCost').html(elemRowReservationDetail.attr('data-customerName'));
	$('#driverNameTypeNewAdditionalCost').html(elemRowReservationDetail.attr('data-driverName'));
	$("#imageTransferReceipt").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "200px");
	$('#additionalCostNominal').val(0);
	$('#additionalCostDescription').val("");
	createUploaderTransferReceipt(idDriver);
	
	$('#modal-selectReservationAdditionalCost').modal('hide');
	$('#modal-editorNewAdditionalCost').modal('show');

}

function createUploaderTransferReceipt(idDriver){
	
	idDriver	=	idDriver == "" ? 0 : idDriver;
	$('.ajax-file-upload-container').remove();
	$("#uploaderTransferReceipt").uploadFile({
		url: baseURL+"financeDriver/additionalCost/uploadTransferReceipt/"+idDriver,
		multiple:false,
		dragDrop:false,
		onSuccess:function(files,data,xhr,pd){
			if(data.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(data.msg);			
				});
				$('#modalWarning').modal('show');
			} else {
				$('.ajax-file-upload-container').html("");
				$("#imageTransferReceipt").removeAttr('src').attr("src", data.urlTransferReceipt);
				$("#transferReceiptFileName").val(data.transferReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#editor-editorNewAdditionalCost').off('submit');
$('#editor-editorNewAdditionalCost').on('submit', function(e) {	
	e.preventDefault();
	var transferReceiptFileName	=	$("#transferReceiptFileName").val();
	if(transferReceiptFileName == ""){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please upload transfer receipt first");
		});
		$('#modalWarning').modal('show');
	} else {
		var confirmText				=	'Are you sure you want to add new additional cost data?.<br/>Once these data have been saved they <b>cannot be undone</b>.';
			
		$confirmDialog.find('#modal-confirm-body').html(confirmText);
		$confirmDialog.find('#confirmBtn').attr('data-function', "saveNewAdditionalCost");
		$confirmDialog.modal('show');
	}
});

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var functionUrl	=	$confirmDialog.find('#confirmBtn').attr('data-function');
	
	if(functionUrl == 'saveNewAdditionalCost'){
		var idDriver			=	$('#idDriver').val(),
			idReservationDetail	=	$('#idReservationDetail').val(),
			idCostType			=	$('#optionAdditionalCostType').val(),
			nominal				=	$('#additionalCostNominal').val(),
			description			=	$('#additionalCostDescription').val(),
			receiptFileName		=	$('#transferReceiptFileName').val(),
			dataSend			=	{
										idDriver:idDriver,
										idReservationDetail:idReservationDetail,
										idCostType:idCostType,
										nominal:nominal,
										description:description,
										receiptFileName:receiptFileName
									};
		$("#editor-editorNewAdditionalCost :input").attr("disabled", true);
	}
		
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalCost/"+functionUrl,
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
			$("#editor-editorNewAdditionalCost :input").attr("disabled", false);

			if(response.status == 200){
				if(functionUrl == 'saveNewAdditionalCost'){
					$("#modal-editorNewAdditionalCost").modal('hide');
					getAdditionalCostApproval();
					getAdditionalCostHistory();	
				}
			}
			
		}
	});
	
});

additionalCostFunc();