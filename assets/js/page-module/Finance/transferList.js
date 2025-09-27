var $confirmDialog= $('#modal-confirm-action');
if (transferListFunc == null){
	var transferListFunc	=	function(){
		$(document).ready(function () {
			getDataUnprocessedTransferList();
			getDataOnGoingTransferList();
			getDataFinishedTransferList();
		});	
	}
}

function getDataUnprocessedTransferList(){
	
	var $tableBody		=	$('#table-unprocessedTransferList > tbody'),
		columnNumber	=	$('#table-unprocessedTransferList > thead > tr > th').length,
		dataSend		=	{};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/transferList/getDataUnprocessed",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
			$("#checkAllTransferList").off("click").prop('checked', false).attr('disable', true);
			$("#createExcelPayroll").off("click").attr('disable', true);
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var rows			=	"";
			
			if(response.status != 200){
				$("#unprocessedDescription").html("No unprocessed data");
				$("#checkAllTransferList").attr("disable", true);
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td>"+
							"</tr>";
			} else {
				
				var data		=	response.result,
					totalData	=	0
				$.each(data, function(index, array) {
					
					var partnerDetail	=	array.PARTNERTYPE+" - "+array.SUBPARTNERTYPE+"<br/>"+array.PARTNERNAME,
						transferType	=	array.TRANSFERTYPE,
						transferAmount	=	numberFormat(array.AMOUNT);
					rows				+=	"<tr>"+
												"<td aling='center'>"+
													"<button class='button button-danger button-xs mx-auto' "+
													" onclick='confirmCancelTransferList("+array.IDTRANSFERLIST+", \""+partnerDetail+"\", \""+transferType+"\", \""+transferAmount+"\")'>"+
														"<span><i class='fa fa-times'></i>Cancel</span>"+
													"</button>"+
												"</td>"+
												"<td>"+transferType+"</td>"+
												"<td>"+partnerDetail+"</td>"+
												"<td>"+array.BANKNAME+" - "+array.ACCOUNTNUMBER+"<br/>"+array.ACCOUNTHOLDERNAME+"</td>"+
												"<td align='right'>"+transferAmount+"</td>"+
												"<td>"+array.REMARK+"</td>"+
												"<td align='center'><label class='adomx-checkbox'><input type='checkbox' class='checkboxTransferList' value='"+array.IDTRANSFERLIST+"'> <i class='icon'></i></label></td>"+
											"</tr>";
					totalData++;
					
				});
				
				$("#unprocessedDescription").html(totalData+" data waiting for the transfer process");
				$("#createExcelPayroll").attr('disable', false).on("click", function(){
					createExcelPayrollTransferList();
				});
				
				$("#checkAllTransferList").attr("disable", false).on("click", function(){
					var checkedStatus	=	$("#checkAllTransferList").is(':checked');
					if(checkedStatus){
						$(".checkboxTransferList").prop('checked', true);
					} else {
						$(".checkboxTransferList").prop('checked', false);
					}
				});
				
			}

			$tableBody.html(rows);

			$(".checkboxTransferList").off("click");
			$(".checkboxTransferList").on("click", function(){
				var totalCheckbox	=	$(".checkboxTransferList").length,
					totalChecked	=	$("[class='checkboxTransferList']:checked").length;
				if(totalCheckbox == totalChecked){
					$("#checkAllTransferList").prop('checked', true);
				} else {
					$("#checkAllTransferList").prop('checked', false);
				}
			});
			
		}
	});
	
}

function confirmCancelTransferList(idTransferList, partnerDetail, transferType, transferAmount){
	var confirmText	=	'This transfer list will be deleted. Details ;<br/><br/>'+
						'<div class="order-details-customer-info">'+
							'<ul class="ml-5">'+
								'<li> <span>Partner</span> <span>'+partnerDetail.replace("<br></button>", " - ")+'</span> </li>'+
								'<li> <span>Transfer Type</span> <span>'+transferType+'</span> </li>'+
								'<li> <span>Amount</span> <span>'+transferAmount+'</span> </li>'+
							'</ul>'+
						'</div><br/>Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idTransferList).attr('data-function', "cancelTransferList");
	$confirmDialog.modal('show');
}

function createExcelPayrollTransferList(){
	var arrIdTransferList	=	[];
	
	$('input:checkbox.checkboxTransferList').each(function () {
		var checkboxVal	=	(this.checked ? $(this).val() : false);
		
		if(checkboxVal){
			arrIdTransferList.push(checkboxVal);
		}
	});
	
	if(arrIdTransferList.length <= 0){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please select at least 1 data from list by check the right checkboxes");
		});
		$('#modalWarning').modal('show');
	} else {
		confirmCreateExcelPayrollTransferList(arrIdTransferList);
	}
}

function confirmCreateExcelPayrollTransferList(arrIdTransferList){
	var confirmText			=	'Are you sure you want to create excel payroll file?<br/>After the payroll file is created, <b>data transfer status will change to On Going and cannot be canceled</b>.',
		strArrIdTransferList=	JSON.stringify(arrIdTransferList);
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-arrIdTransferList', arrIdTransferList).attr('data-function', "createExcelPayrollTransferList");
	$confirmDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var functionUrl	=	$confirmDialog.find('#confirmBtn').attr('data-function');
	
	if(functionUrl == 'cancelTransferList'){
		var idData	=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
			dataSend=	{idData:idData};
			
		$.ajax({
			type: 'POST',
			url: baseURL+"finance/transferList/"+functionUrl,
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				NProgress.set(0.4);
				$confirmDialog.modal('hide');
				$('#window-loader').modal('show');
			},
			success:function(response){

				if(response.status == 200){
					var detailTransferList		=	response.detailTransferList,
						idPartnerType			=	detailTransferList.IDPARTNERTYPE,
						idWithdrawal			=	detailTransferList.IDWITHDRAWAL,
						idLoanDriverRequest		=	detailTransferList.IDLOANDRIVERREQUEST,
						idCharityRecapProcess	=	detailTransferList.IDCHARITYRECAPPROCESS,
						urlFunction				=	'',
						dataSend				=	{};
					
					if(idPartnerType == 2){
						if(idWithdrawal != 0){
							urlFunction	=	baseURL+"financeDriver/recapPerDriver/approveRejectWithdrawal";
							dataSend	=	{idWithdrawalRecap:idWithdrawal, status:-1};
						}
						
						if(idLoanDriverRequest != 0){
							urlFunction	=	baseURL+"financeDriver/loanPrepaidCapital/approveRejectLoanPrepaidCapitalRequest";
							dataSend	=	{idLoanDriverRequest:idLoanDriverRequest, status:-2}
						}
					} else if(idPartnerType == 1) {
						urlFunction	=	baseURL+"financeVendor/recapPerVendor/approveRejectWithdrawal";
						dataSend	=	{idWithdrawalRecap:idWithdrawal, status:-1};
					} else if(idCharityRecapProcess != 0) {
						urlFunction	=	baseURL+"finance/charityReport/cancelCharityTransferProcess";
						dataSend	=	{idData:idCharityRecapProcess};
					}
					
					if(urlFunction != ''){
						$.ajax({
							type: 'POST',
							url: urlFunction,
							contentType: 'application/json',
							dataType: 'json',
							data: mergeDataSend(dataSend),
							beforeSend:function(){
								NProgress.set(0.4);
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
									getDataUnprocessedTransferList();
									getDataOnGoingTransferList();
								}
								
							}
						});
					}
				}			
			}
		});
	} else {
		if(functionUrl == 'saveManualTransfer'){
			var idTransferList			=	$confirmDialog.find('#confirmBtn').attr('data-idTransferList'),
				dateTransfer			=	$confirmDialog.find('#confirmBtn').attr('data-dateTransfer'),
				transferReceiptFileName	=	$confirmDialog.find('#confirmBtn').attr('data-transferReceiptFileName'),
				dataSend				=	{idTransferList:idTransferList, dateTransfer:dateTransfer, transferReceiptFileName:transferReceiptFileName};
		} else {		
			var arrIdTransferList		=	$confirmDialog.find('#confirmBtn').attr('data-arrIdTransferList'),
				functionUrl				=	$confirmDialog.find('#confirmBtn').attr('data-function'),
				dataSend				=	{arrIdTransferList:arrIdTransferList};
		}
			
		$.ajax({
			type: 'POST',
			url: baseURL+"finance/transferList/"+functionUrl,
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
					if(functionUrl == 'saveManualTransfer'){
						$("#modalManualTransfer").modal('hide');
						getDataOnGoingTransferList();
						getDataFinishedTransferList();					
					} else {					
						getDataUnprocessedTransferList();
						getDataOnGoingTransferList();
					}
				}
				
			}
		});
	}
});

$('#startDateProcess, #endDateProcess').off('change');
$('#startDateProcess, #endDateProcess').on('change', function(e) {
	getDataOnGoingTransferList();
});

function getDataOnGoingTransferList(){
	
	var $tableBody	=	$("#bodyProcessedTransferList"),
		dateStart	=	$('#startDateProcess').val(),
		dateEnd		=	$('#endDateProcess').val(),
		dataSend	=	{dateStart:dateStart, dateEnd:dateEnd};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/transferList/getDataOngoing",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
			$('#window-loader').modal('show');
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$('#window-loader').modal('hide');
			
			var rows			=	"";
			if(response.status != 200){
				$tableBody.addClass("text-center");
				rows	=	'<img src="'+ASSET_IMG_URL+'no-data.png" width="120px"/>'+
							'<h5>No Data Found</h5>'+
							'<p>There are no transfer list data <b>on the date</b> you have selected</p>';
			} else {
				
				$tableBody.removeClass("text-center");
				var data	=	response.result,
					number	=	1;
				$.each(data, function(index, array) {
					
					var btnDownloadExcel	=	'<a class="button button-primary button-sm pull-right" target="_blank" href="'+array.URLFILEDOWNLOAD+'"><span><i class="fa fa-file-excel-o"></i>Download Excel Payroll</span></a>',
						dataTransferList	=	array.TRANSFERLIST,
						rowsTransferList	=	'';
					
					if(dataTransferList == false || dataTransferList.length <= 0){
						rowsTransferList	=	'<tr><td colspan="5" align="center">No Data Found</td></tr>';
					} else {
						$.each(dataTransferList, function(index, arrayTransferList) {
							rowsTransferList+=	"<tr id='transferListOnGoing"+arrayTransferList.IDTRANSFERLIST+"' "+
													"data-transferType='"+arrayTransferList.TRANSFERTYPE+"' "+
													"data-partnerType='"+arrayTransferList.PARTNERTYPE+" - "+arrayTransferList.SUBPARTNERTYPE+"' "+
													"data-partnerName='"+arrayTransferList.PARTNERNAME+"' "+
													"data-bankAccount='"+arrayTransferList.BANKNAME+" - "+arrayTransferList.ACCOUNTNUMBER+"<br/>"+arrayTransferList.ACCOUNTHOLDERNAME+"' "+
													"data-amount='"+arrayTransferList.AMOUNT+"' "+
													"data-remark='"+arrayTransferList.REMARK+"' "+
												">"+
													"<td>"+arrayTransferList.TRANSFERTYPE+"</td>"+
													"<td>"+arrayTransferList.PARTNERTYPE+" - "+arrayTransferList.SUBPARTNERTYPE+"<br/>"+arrayTransferList.PARTNERNAME+"</td>"+
													"<td>"+arrayTransferList.BANKNAME+" - "+arrayTransferList.ACCOUNTNUMBER+"<br/>"+arrayTransferList.ACCOUNTHOLDERNAME+"</td>"+
													"<td align='right'>"+numberFormat(arrayTransferList.AMOUNT)+"</td>"+
													"<td>"+arrayTransferList.REMARK+"</td>"+
													"<td aling='center'>"+
														"<button class='button button-primary button-xs mx-auto' onclick='showManualTransferModal("+arrayTransferList.IDTRANSFERLIST+")'>"+
															"<span><i class='fa fa-upload'></i>Manual Transfer</span>"+
														"</button>"+
													"</td>"+
												"</tr>";
						});
					}
					
					rows	+=	'<div class="card">'+
                                    '<div class="card-header">'+
										'<h2>'+
											'<button class="collapsed" data-toggle="collapse" data-target="#collapseProcessed'+number+'" aria-expanded="false">'+
												'['+array.TOTALDATA+' Data] '+array.PAYROLLFILE+'<br/>'+
												'<small>Processed on : '+array.DOWNLOADDATETIME+'</small><br/>'+
												'<small>By : '+array.DOWNLOADUSER+'</small>'+
											'</button>'+
										'</h2>'+
                                    '</div>'+
                                    '<div id="collapseProcessed'+number+'" class="collapse" data-parent="#bodyProcessedTransferList" style="">'+
                                        '<div class="card-body">'+
											btnDownloadExcel+
                                            '<table class="table">'+
												'<thead class="thead-light">'+
													'<tr>'+
														'<th width="120">Type</th>'+
														'<th width="160">Partner Detail</th>'+
														'<th>Bank Account</th>'+
														'<th class="text-right" width="100">Amount</th>'+
														'<th>Remark</th>'+
														'<th width="150"></th>'+
													'</tr>'+
												'</thead>'+
												'<tbody>'+
													rowsTransferList+
												'</tbody>'+
											'</table>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>';
					number++;
					
				});
				
			}

			$tableBody.html(rows);
			
		}
	});
	
}

function showManualTransferModal(idTransferList){
	var $elemTrTransferList	=	$("#transferListOnGoing"+idTransferList),
		transferType		=	$elemTrTransferList.attr('data-transferType'),
		partnerType			=	$elemTrTransferList.attr('data-partnerType'),
		partnerName			=	$elemTrTransferList.attr('data-partnerName'),
		bankAccount			=	$elemTrTransferList.attr('data-bankAccount'),
		amount				=	$elemTrTransferList.attr('data-amount'),
		remark				=	$elemTrTransferList.attr('data-remark');
	
	createUploaderTransferReceipt(idTransferList);
	$("#manualTransfer-transferType").html(transferType);
	$("#manualTransfer-partnerType").html(partnerType);
	$("#manualTransfer-partnerName").html(partnerName);
	$("#manualTransfer-bankAccount").html(bankAccount);
	$("#manualTransfer-amount").html(numberFormat(amount));
	$("#manualTransfer-remark").html(remark);
	$("#imageTransferReceipt").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "100px");
	$("#transferReceiptFileName").val("");
	$("#idTransferList").val(idTransferList);
	$('#modalManualTransfer').modal('show');
}

function createUploaderTransferReceipt(idTransferList){
	
	idTransferList	=	idTransferList == "" ? 0 : idTransferList;
	$('.ajax-file-upload-container').remove();
	$("#uploaderTransferReceipt").uploadFile({
		url: baseURL+"finance/transferList/uploadTransferReceipt/"+idTransferList,
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
				$("#imageTransferReceipt").removeAttr('src').attr("src", data.urlTransferReceipt).attr("height", data.defaultHeight);
				$("#transferReceiptFileName").val(data.transferReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#editor-manualTransfer').off('submit');
$('#editor-manualTransfer').on('submit', function(e) {	
	e.preventDefault();
	var transferReceiptFileName	=	$("#transferReceiptFileName").val();
	if(transferReceiptFileName == ""){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please upload transfer receipt first");
		});
		$('#modalWarning').modal('show');
	} else {
		var confirmText				=	'Transfer status will be recognized as successful. Please make sure the <b>transfer date and receipt are valid</b>.<br/>Once these changes have been saved they <b>cannot be undone</b>.',
			dateTransfer			=	$("#transferDate").val(),
			transferReceiptFileName	=	$("#transferReceiptFileName").val(),
			idTransferList			=	$("#idTransferList").val();
			
		$confirmDialog.find('#modal-confirm-body').html(confirmText);
		$confirmDialog.find('#confirmBtn').attr('data-idTransferList', idTransferList).attr('data-dateTransfer', dateTransfer).attr('data-transferReceiptFileName', transferReceiptFileName).attr('data-function', "saveManualTransfer");
		$confirmDialog.modal('show');
	}
});

$('#startDateFinish, #endDateFinish').off('change');
$('#startDateFinish, #endDateFinish').on('change', function(e) {
	getDataFinishedTransferList();
});

function getDataFinishedTransferList(){
	
	var $tableBody	=	$("#bodyFinishedTransferList"),
		dateStart	=	$('#startDateFinish').val(),
		dateEnd		=	$('#endDateFinish').val(),
		dataSend	=	{dateStart:dateStart, dateEnd:dateEnd};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/transferList/getDataFinished",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
			$('#window-loader').modal('show');
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$('#window-loader').modal('hide');
			
			var rows			=	"";
			if(response.status != 200){
				$tableBody.addClass("text-center");
				rows	=	'<img src="'+ASSET_IMG_URL+'no-data.png" width="120px"/>'+
							'<h5>No Data Found</h5>'+
							'<p>There are no transfer list data <b>on the date</b> you have selected</p>';
			} else {
				
				$tableBody.removeClass("text-center");
				var data	=	response.result,
					number	=	1;
				$.each(data, function(index, array) {
					
					var dataTransferList	=	array.TRANSFERLIST,
						rowsTransferList	=	'';
					
					if(dataTransferList == false || dataTransferList.length <= 0){
						rowsTransferList	=	'<tr><td colspan="7" align="center">No Data Found</td></tr>';
					} else {
						$.each(dataTransferList, function(index, arrayTransferList) {
							var btnShowReceipt	=	'<button type="button" class="button button-xs button-box button-primary" onclick="showTransferReceipt(\''+arrayTransferList.URLRECEIPTFILE+'\')"><i class="fa fa-file-text-o"></i></button>';
							rowsTransferList	+=	"<tr>"+
														"<td>"+arrayTransferList.TRANSFERTYPE+"</td>"+
														"<td>"+arrayTransferList.PARTNERTYPE+" - "+arrayTransferList.SUBPARTNERTYPE+"<br/>"+arrayTransferList.PARTNERNAME+"</td>"+
														"<td>"+arrayTransferList.BANKNAME+" - "+arrayTransferList.ACCOUNTNUMBER+"<br/>"+arrayTransferList.ACCOUNTHOLDERNAME+"</td>"+
														"<td align='right'>"+numberFormat(arrayTransferList.AMOUNT)+"</td>"+
														"<td>"+arrayTransferList.REMARK+"</td>"+
														"<td align='center'>"+arrayTransferList.STATUSDATETIME+"</td>"+
														"<td align='center'>"+btnShowReceipt+"</td>"+
													"</tr>";
						});
					}
					
					rows	+=	'<div class="card">'+
                                    '<div class="card-header">'+
										'<h2>'+
											'<button class="collapsed" data-toggle="collapse" data-target="#collapseFinished'+number+'" aria-expanded="false">'+
												'['+array.TOTALDATA+' Data] '+array.PAYROLLFILE+'<br/>'+
												'<small>By : '+array.DOWNLOADUSER+'</small>'+
											'</button>'+
										'</h2>'+
                                    '</div>'+
                                    '<div id="collapseFinished'+number+'" class="collapse" data-parent="#bodyFinishedTransferList" style="">'+
                                        '<div class="card-body">'+
                                            '<table class="table">'+
												'<thead class="thead-light">'+
													'<tr>'+
														'<th width="120">Type</th>'+
														'<th width="160">Partner Detail</th>'+
														'<th>Bank Account</th>'+
														'<th class="text-right" width="100">Amount</th>'+
														'<th>Remark</th>'+
														'<th class="text-center" width="100">Date Time</th>'+
														'<th class="text-center" width="80">Receipt</th>'+
													'</tr>'+
												'</thead>'+
												'<tbody>'+
													rowsTransferList+
												'</tbody>'+
											'</table>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>';
					number++;
					
				});
				
			}

			$tableBody.html(rows);
			
		}
	});
	
}

function showTransferReceipt(urlHtmlFileReceipt){
	$("#iframeHtmlFilePreview").remove();
	$("#iframeHtmlFilePreviewContainer").html('<iframe id="iframeHtmlFilePreview" width="100%" height="600" padding="8px" src="'+urlHtmlFileReceipt+'" frameborder="0"></iframe>');
	$('#modal-htmlFilePreview').modal('show');
}

transferListFunc();