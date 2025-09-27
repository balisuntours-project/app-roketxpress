var $confirmDialog= $('#modal-confirm-action');
if (reimbursementFunc == null){
	var reimbursementFunc	=	function(){
		$(document).ready(function () {
			localStorage.setItem('viewConfirmDialogValidateReimbursement', '1');
			getDataReimbursement();
		});	
	}
}

$('#startDateReimbursement, #endDateReimbursement').off('change');
$('#startDateReimbursement, #endDateReimbursement').on('change', function(e) {
	getDataReimbursement();
});

$('#keywordSearch').off('keypress');
$("#keywordSearch").on('keypress', function(e) {
    if(e.which == 13) {
        getDataReimbursement();
    }
});

$('#checkboxViewRequestOnly').off('click');
$("#checkboxViewRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewRequestOnly").is(':checked');
	
	if(checked){
		$("#startDateReimbursement, #endDateReimbursement, #keywordSearch").attr("disabled", true);
	} else {
		$("#startDateReimbursement, #endDateReimbursement, #keywordSearch").attr("disabled", false);
	}
	
	getDataReimbursement();
});
	
function generateDataTable(page){
	getDataReimbursement(page);
}

function getDataReimbursement(page = 1){
	var $tableBody		=	$('#table-reimbursement > tbody'),
		columnNumber	=	$('#table-reimbursement > thead > tr > th').length,
		startDate		=	$('#startDateReimbursement').val(),
		endDate			=	$('#endDateReimbursement').val(),
		keywordSearch	=	$('#keywordSearch').val(),
		viewRequestOnly	=	$("#checkboxViewRequestOnly").is(':checked'),
		dataSend		=	{page:page, startDate:startDate, endDate:endDate, keywordSearch:keywordSearch, viewRequestOnly:viewRequestOnly};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/reimbursement/getDataReimbursement",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
			$('#btnExcelDetail').addClass('d-none').off("click").attr("href", "");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var data			=	response.result.data,
				rows			=	"";
			
			if(data.length === 0){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				if(response.urlExcelDetail != "") $('#btnExcelDetail').removeClass('d-none').on("click").attr("href", response.urlExcelDetail);
				$.each(data, function(index, array) {
					var idReimbursement	=	array.IDREIMBURSEMENT,
						badgeStatus		=	badgeRequestBy	=	"",
						btnEdit			=	btnDelete	=	btnValidate	=	btnIgnore	=	"";
						
					switch(array.STATUS){
						case "0"	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Requested</span>';
										break;
						case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-success">Approved</span>';
										break;
						case "-1"	:	badgeStatus	=	'<span class="badge badge-pill badge-warning">Rejected</span>';
										break;
						case "-2"	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Cancelled</span>';
										break;
						default		:	badgeStatus	=	'<span class="badge badge-pill badge-info">-</span>';
										break;
					}
					
					switch(array.REQUESTBY){
						case "1"	:	badgeRequestBy	=	'<span class="badge badge-pill badge-info">'+array.REQUESTBYTYPE+'</span>';
										break;
						case "2"	:	badgeRequestBy	=	'<span class="badge badge-pill badge-primary">'+array.REQUESTBYTYPE+'</span>';
										break;
						default		:	badgeRequestBy	=	'<span class="badge badge-pill badge-warning">'+array.REQUESTBYTYPE+'</span>';
										break;
					}
					
					if(array.IDWITHDRAWALRECAP == "0" && array.INPUTMETHOD == 2 && array.STATUS == 1){
						btnEdit		=	'<button class="button button-xs button-box button-primary btnEditReimbursement" data-toggle="modal" data-target="#modal-formReimbursement" data-idReimbursement="'+idReimbursement+'">'+
											'<i class="fa fa-pencil"></i>'+
										'</button><br/>';
						btnDelete	=	'<button class="button button-xs button-box button-danger" onclick="cancelReimbursement('+idReimbursement+', \''+array.DATERECEIPT+'\', \''+array.REQUESTBYTYPE+'\', \''+array.REQUESTBYNAME+'\', \''+array.DESCRIPTION+'\', \''+array.NOMINAL+'\')">'+
											'<i class="fa fa-trash"></i>'+
										'</button>';
					}
					
					if(array.INPUTMETHOD == 1 && array.STATUS == 0){
						btnValidate	=	'<button class="button button-box button-primary button-sm" onclick="setValidateReimbursement('+idReimbursement+', 1)" >'+
											'<i class="fa fa-check"></i>'+
										'</button><br/>';
						btnIgnore	=	'<button class="button button-box button-warning button-sm" onclick="setValidateReimbursement('+idReimbursement+', -1)" >'+
											'<i class="fa fa-times"></i>'+
										'</button>';						
					}
					
					rows	+=	"<tr class='trDataReimbursement' id='trDataReimbursement"+idReimbursement+"' data-idReimbursement='"+idReimbursement+"'>"+
									"<td>"+badgeStatus+"<br/><span id='rowReimbursement-date"+idReimbursement+"'>"+array.DATERECEIPT+"</span></td>"+
									"<td>"+badgeRequestBy+"<br/><span id='rowReimbursement-requestBy"+idReimbursement+"'>"+array.REQUESTBYNAME+"</span></td>"+
									"<td id='rowReimbursement-description"+idReimbursement+"'>"+array.DESCRIPTION+"</td>"+
									"<td><b>"+array.INPUTBYNAME+"</b><br/>"+array.INPUTDATETIME+"</td>"+
									"<td id='rowReimbursement-approvalDetail"+idReimbursement+"'><b>"+array.APPROVALBYNAME+"</b><br/>"+array.APPROVALDATETIME+"</td>"+
									"<td>"+array.NOTES+"</td>"+
									"<td align='right' id='rowReimbursement-nominal"+idReimbursement+"'>"+numberFormat(array.NOMINAL)+"</td>"+
									"<td>"+
										"<a href='#' data-imgsrc='"+array.RECEIPTIMAGE+"' class='zoomImage'>"+
											"<img src='"+array.RECEIPTIMAGE+"' id='rowReimbursement-imageReceipt"+idReimbursement+"' width='150px'>"+
										"</a>"+
									"</td>"+
									"<td id='containerBtn"+idReimbursement+"'>"+btnEdit+btnDelete+btnValidate+btnIgnore+"</td>"+
								"</tr>";
				});
				
			}

			generatePagination("tablePaginationReimbursement", page, response.result.pageTotal);
			generateDataInfo("tableDataCountReimbursement", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
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

function setValidateReimbursement(idReimbursement, status){
	var viewConfirmDialog	=	localStorage.getItem('viewConfirmDialogValidateReimbursement'),
		txtValidateStatus	=	status == 1 ? "Approved" : "Rejected";
	
	$("#confirmationReimbursement-dateReceipt, #confirmationReimbursement-requestBy, #confirmationReimbursement-description, #confirmationReimbursement-nominal").html("-");
	if(viewConfirmDialog == '1'){
		$('.trDataReimbursement').each(function (index, elemObject) {
			var idReimbursementElem	=	$(this).attr('data-idReimbursement');
			
			if(idReimbursementElem == idReimbursement){
				$("#confirmationReimbursement-dateReceipt").html($("#rowReimbursement-date"+idReimbursement).html());
				$("#confirmationReimbursement-requestBy").html($("#rowReimbursement-requestBy"+idReimbursement).html());
				$("#confirmationReimbursement-description").html($("#rowReimbursement-description"+idReimbursement).html());
				$("#confirmationReimbursement-nominal").html($("#rowReimbursement-nominal"+idReimbursement).html());
				$('#confirmationReimbursement-imageReceipt').attr('src', $('#rowReimbursement-imageReceipt'+idReimbursement).attr('src'));				
			}
		});
		
		$("#confirmationReimbursement-textValidateStatus").html(txtValidateStatus);
		$("#btnConfirmValidateReimbursement").attr('data-idReimbursement', idReimbursement).attr('data-status', status);
		$('#modal-confirmValidateReimbursement').modal('show');
	} else {
		submitValidateReimbursement(idReimbursement, status);
	}	
}

$('#btnConfirmValidateReimbursement').off('click');
$('#btnConfirmValidateReimbursement').on('click', function(e) {
	e.preventDefault();
	if($('#confirmationReimbursement-disableConfirm').is(":checked")){
		localStorage.setItem('viewConfirmDialogValidateReimbursement', '0');
	}
	submitValidateReimbursement($("#btnConfirmValidateReimbursement").attr('data-idReimbursement'), $("#btnConfirmValidateReimbursement").attr('data-status'));
});

function submitValidateReimbursement(idReimbursement, status){
	
	var dataSend	=	{idReimbursement:idReimbursement, status:status};
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/reimbursement/submitValidateReimbursement",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$('#window-loader').modal('show');
			$('#modal-confirmValidateReimbursement').modal('hide');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');

			if(response.status == 200){
					
				var badgeStatus		=	"",
					status			=	response.statusApproval,
					idReimbursement	=	response.idReimbursement;
					
				switch(status){
					case 1		:	
					case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-success">Approved</span>'; break;
					case -1		:	
					case "-1"	:	badgeStatus	=	'<span class="badge badge-pill badge-warning">Rejected</span>'; break;
					case -2		:	
					case "-2"	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Cancelled</span>'; break;
					case "0"	:
					default		:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Requested</span>'; break;
				}
				
				var trReimbursement		=	$("#trDataReimbursement"+idReimbursement);
				trReimbursement.find('td:first').find('span.badge').remove();
				trReimbursement.find('td:first').prepend(badgeStatus);				
				trReimbursement.find('td:last').find('button.button-box').remove();
				
				$("#containerBtn"+idReimbursement).html("");
				$("#rowReimbursement-approvalDetail"+idReimbursement).html("<b>"+response.userValidate+"</b><br/>"+response.strDateTime);
				
				var toastType	=	status == 1 ? "success" : "warning";
				toastr[toastType](response.msg);
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
	
}

$('#modal-formReimbursement').off('show.bs.modal');
$('#modal-formReimbursement').on('show.bs.modal', function(event) {
	var triggerElem		=	$(event.relatedTarget),
		triggerElemId	=	triggerElem.attr("id");
	
	$("#reimbursementDate").val(dateNow);
	$("#requesterName, #reimbursementDescription").val("");
	$("#reimbursementNominal").val(0);
	
	setOptionDriverVendor();
	$('#optionRequestByType').off('change');
	$('#optionRequestByType').on('change', function(e) {
		setOptionDriverVendor();
	});
	
	$('#optionDriverVendor').off('change');
	$('#optionDriverVendor').on('change', function(e) {
		setRequesterName();
	});
	
	createUploaderReimbursementReceipt();
	$("#imageReimbursementReceipt").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "200px");
	
	if(triggerElemId != "btnAddNewReimbursement"){
		var idReimbursement	=	triggerElem.attr("data-idReimbursement"),
			dataSend		=	{idReimbursement:idReimbursement};
	
		$.ajax({
			type: 'POST',
			url: baseURL+"finance/reimbursement/getDetailReimbursement",
			contentType: 'application/json',
			dataType: 'json',
			cache: false,
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				$('#window-loader').modal('hide');
				NProgress.done();
				setUserToken(response);
				
				if(response.status == 200){
					var detailData		=	response.detailData,
						requestByType	=	detailData.REQUESTBY,
						idVendorDriver	=	0;
						
					switch(requestByType){
						case "1"	:	idVendorDriver	=	detailData.IDVENDOR; break;
						case "2"	:	idVendorDriver	=	detailData.IDDRIVER; break;
						default		:	idVendorDriver	=	0; break;
					}

					$("#reimbursementDate").val(detailData.DATERECEIPT);
					$("#optionRequestByType").val(requestByType);
					$("#optionDriverVendor").val(idVendorDriver);
					$("#requesterName").val(detailData.REQUESTBYNAME);
					$("#reimbursementNominal").val(numberFormat(detailData.NOMINAL));
					$("#reimbursementDescription").val(detailData.DESCRIPTION);
					$("#imageReimbursementReceipt").attr("src", detailData.RECEIPTIMAGEURL).attr("height", "200px");
					$("#idReimbursement").val(idReimbursement);
					$("#reimbursementReceiptFileName").val(detailData.RECEIPTIMAGE);
					setOptionDriverVendor();
				}
			}
		});
	}
});

function setOptionDriverVendor(){
	var requestByType	=	$("#optionRequestByType").val();
	
	switch(requestByType){
		case "1"	:	setOptionHelper('optionDriverVendor', 'dataVendor');
						$('#requesterName').prop('readonly', true);
						break;
		case "2"	:	setOptionHelper('optionDriverVendor', 'dataDriver');
						$('#requesterName').prop('readonly', true);
						break;
		default		:	$('#optionDriverVendor').empty();
						$('#optionDriverVendor').append($('<option></option>').val(0).html("Other"));
						$('#requesterName').prop('readonly', false);
						break;
	}
	setRequesterName();
}

function setRequesterName(){
	var requestByType	=	$("#optionRequestByType").val(),
		requesterName	=	requestByType == 3 ? "" : $("#optionDriverVendor option:selected").text();
	
	$("#requesterName").val(requesterName);
}

function createUploaderReimbursementReceipt(){
	$('.ajax-file-upload-container').remove();
	$("#uploaderReimbursementReceipt").uploadFile({
		url: baseURL+"finance/reimbursement/uploadReimbursementReceipt/",
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
				$("#imageReimbursementReceipt").removeAttr('src').attr("src", data.urlReimbursementReceipt);
				$("#reimbursementReceiptFileName").val(data.reimbursementReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#editor-formReimbursement').off('submit');
$('#editor-formReimbursement').on('submit', function(e) {	
	e.preventDefault();
	var reimbursementReceipt	=	$("#reimbursementReceiptFileName").val(),
		reimbursementDescription=	$('#reimbursementDescription').val(),
		descriptionCheck		=	reimbursementDescription.replaceAll(' ', ''),
		descriptionCheckLen		=	descriptionCheck.length;
		
	if(reimbursementReceipt == ""){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please upload reimbursement receipt first");
		});
		$('#modalWarning').modal('show');
	} else if(descriptionCheckLen <= 8){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please enter a valid reimbursement description");
		});
		$('#modalWarning').modal('show');
	} else {
		var reimbursementDate		=	$('#reimbursementDate').val(),
			requestByType			=	$('#optionRequestByType').val(),
			idDriverVendor			=	$('#optionDriverVendor').val(),
			requesterName			=	$('#requesterName').val(),
			reimbursementNominal	=	$('#reimbursementNominal').val(),
			idReimbursement			=	$('#idReimbursement').val(),
			dataSend				=	{
				reimbursementDate:reimbursementDate,
				requestByType:requestByType,
				idDriverVendor:idDriverVendor,
				requesterName:requesterName,
				reimbursementNominal:reimbursementNominal,
				reimbursementDescription:reimbursementDescription,
				reimbursementReceipt:reimbursementReceipt,
				idReimbursement:idReimbursement
			};
		$.ajax({
			type: 'POST',
			url: baseURL+"finance/reimbursement/insertUpdateReimbursement",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				NProgress.set(0.4);
				$("#editor-formReimbursement :input").attr("disabled", true);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				$("#editor-formReimbursement :input").attr("disabled", false);
				NProgress.done();
				
				if(response.status == 200){
					toastr["success"](response.msg);
					$("#modal-formReimbursement").modal('hide');
					getDataReimbursement();
				} else {
					$('#modalWarning').on('show.bs.modal', function() {
						$('#modalWarningBody').html(response.msg);
					});
					$('#modalWarning').modal('show');
				}
			}
		});
	}
});

function cancelReimbursement(idReimbursement, dateReceipt, requestByType, requestByName, description, nominal){
	var confirmText	=	'This reimbursement will be cancelled. Details ;<br/><br/>'+
						'<div class="order-details-customer-info">'+
							'<ul class="ml-5">'+
								'<li> <span>Date Receipt</span> <span>'+dateReceipt+'</span> </li>'+
								'<li> <span>Request By</span> <span>['+requestByType+'] '+requestByName+'</span> </li>'+
								'<li> <span>Description</span> <span>'+description+'</span> </li>'+
								'<li> <span>Nominal</span> <span>'+numberFormat(nominal)+'</span> </li>'+
							'</ul>'+
						'</div><br/>'+
						'Are you sure? Please enter cancellation reason.'+
						'<div class="alert alert-warning mt-5 d-none" role="alert" id="container-warningCancellationReason">'+
							'<i class="fa fa-exclamation-triangle"></i> <span id="warningCancellationReason"></span>'+
						'</div>'+
						'<div class="form-group mt-5">'+
							'<textarea class="form-control" placeholder="Cancellation reason" id="cancellationReason" name="cancellationReason"></textarea>'+
						'</div>';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idReimbursement).attr('data-function', "cancelReimbursement");
	$confirmDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData				=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
		funcName			=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		cancellationReason	=	$("#cancellationReason").val(),
		reasonCheck			=	cancellationReason.replaceAll(' ', ''),
		reasonCheckLen		=	reasonCheck.length,
		dataSend			=	{idData:idData, cancellationReason:cancellationReason};
	
	if(funcName == "cancelReimbursement"){
		if(reasonCheckLen <= 8){
			$('#warningCancellationReason').html("Please enter a valid cancellation note");
			$('#container-warningCancellationReason').removeClass('d-none');
		} else {
			$.ajax({
				type: 'POST',
				url: baseURL+"finance/reimbursement/"+funcName,
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

					if(response.status == 200){
						toastr["warning"](response.msg);
						getDataReimbursement();
					} else {
						$('#modalWarning').on('show.bs.modal', function() {
							$('#modalWarningBody').html(response.msg);
						});
						$('#modalWarning').modal('show');
					}
				}
			});
		}
	}
});

reimbursementFunc();