var $confirmDialog= $('#modal-confirm-action');
if (charityReportFunc == null){
	var charityReportFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('transferCharity-optionBank', 'dataBank');
			getDataCharityReport();
			getDataCharityProcessTransfer();
		});	
	}
}

$('#startDate, #endDate').off('change');
$('#startDate, #endDate').on('change', function(e) {
	getDataCharityReport();
});
	
$('#checkboxViewUnprocessedOnly').off('click');
$("#checkboxViewUnprocessedOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewUnprocessedOnly").is(':checked');
	
	if(checked){
		$("#startDate, #endDate").attr("disabled", true);
	} else {
		$("#startDate, #endDate").attr("disabled", false);
	}
	
	getDataCharityReport();
});

$('#searchKeyword').off('keypress');
$("#searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
        getDataCharityReport();
    }
});

function generateDataTable(page){
	getDataCharityReport(page);
}

function getDataCharityReport(page = 1){
	var $tableBody		=	$('#table-charityReport > tbody'),
		columnNumber	=	$('#table-charityReport > thead > tr > th').length;
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		searchKeyword	=	$('#searchKeyword').val(),
		viewUnprocessed	=	$("#checkboxViewUnprocessedOnly").is(':checked'),
		dataSend		=	{page:page, startDate:startDate, endDate:endDate, searchKeyword:searchKeyword, viewUnprocessed:viewUnprocessed};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/charityReport/getDataCharityReport",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#btnDisburseCharity').addClass('d-none').off("click");
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var data					=	response.result.data,
				disburseCharity			=	response.disburseCharity,
				dataLastTransferCharity	=	response.dataLastTransferCharity,
				rows					=	"";
			
			if(data.length === 0){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				$.each(data, function(index, array) {
					var idCharity		=	array.IDCHARITY,
						contributorType	=	'-',
						btnEdit 		=	btnDelete	=	'';
						
					switch(array.CONTRIBUTORTYPE){
						case "1"	:	contributorType	=	'Partner'; break;
						case "2"	:	contributorType	=	'Employee'; break;
						case "3"	:	contributorType	=	'Other'; break;
						default		:	contributorType	=	'-'; break;
					}
						
					if(array.INPUTTYPE == '2' && array.IDCHARITYRECAPPROCESS == 0){
						btnEdit		=	'<button class="button button-xs button-box button-primary" data-idCharity="'+idCharity+'" data-action="update" data-toggle="modal" data-target="#modal-manualCharity">'+
											'<i class="fa fa-pencil"></i>'+
										'</button>';
						btnDelete	=	'<button class="button button-xs button-box button-danger" onclick="deleteManualCharity('+idCharity+', \''+array.DATETIMESTR+'\', \''+contributorType+'\', \''+array.NAME+'\', \''+array.NOMINAL+'\', \''+array.DESCRIPTION+'\')">'+
											'<i class="fa fa-trash"></i>'+
										'</button>';
					}
					
					rows	+=	"<tr>"+
									"<td>"+array.DATETIMESTR+"</td>"+
									"<td>"+contributorType+"</td>"+
									"<td>"+array.NAME+"</td>"+
									"<td>"+array.DESCRIPTION+"</td>"+
									"<td align='right'>"+numberFormat(array.NOMINAL)+"</td>"+
									"<td>"+array.PROCESSUSER+"<br/>"+array.PROCESSDATETIME+"</td>"+
									"<td align='center'>"+btnEdit+btnDelete+"</td>"+
								"</tr>";
				});
				
				if(disburseCharity) {
					$('#btnDisburseCharity').removeClass('d-none');
					$('#btnDisburseCharity').off('click');
					$("#btnDisburseCharity").on('click',function(e) {
						$('#modal-transferCharity').off('show.bs.modal');
						$('#modal-transferCharity').on('show.bs.modal', function() {
							$('#transferCharity-optionBank').val(dataLastTransferCharity.IDBANK);			
							$('#transferCharity-accountNumber').val(dataLastTransferCharity.ACCOUNTNUMBER);			
							$('#transferCharity-accountHolderName').val(dataLastTransferCharity.ACCOUNTHOLDERNAME);			
							$('#transferCharity-emailNotification').val(dataLastTransferCharity.EMAILLIST);			
							$('#transferCharity-charityCode').val(dataLastTransferCharity.PARTNERCODE);			
						});
						$('#modal-transferCharity').modal('show');
					});
				}
				
			}

			generatePagination("tablePaginationCharityReport", page, response.result.pageTotal);
			generateDataInfo("tableDataCountCharityReport", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
}

$('#modal-manualCharity').off('show.bs.modal');
$('#modal-manualCharity').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  
	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {

			if($activeElement.attr('data-action') == "insert"){
				$("#contributorName, #charityDescription").val("");
				$("#charityNominal").val(numberFormat(minCharityNominal));
				$("#idCharity").val(0);
			} else {
				var idCharity	=	$activeElement.attr('data-idCharity'),
					dataSend	=	{idCharity:idCharity};
				
				$.ajax({
					type: 'POST',
					url: baseURL+"finance/charityReport/getDetailManualCharity",
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
							var detailData		=	response.detailData;

							$("#charityDate").val(detailData.DATECHARITY);
							$("#optionContributorType").val(detailData.CONTRIBUTORTYPE);
							$("#contributorName").val(detailData.NAME);
							$("#charityDescription").val(detailData.DESCRIPTION);
							$("#charityNominal").val(numberFormat(detailData.NOMINAL));
							$("#idCharity").val(idCharity);
						} else {
							$('#modal-manualCharity').modal('hide');
							$('#modalWarning').on('show.bs.modal', function() {
								$('#modalWarningBody').html(response.msg);			
							});
							$('#modalWarning').modal('show');
						}
					}
				});
			}
		}
	}
});

$('#editor-manualCharity').off('submit');
$('#editor-manualCharity').on('submit', function(e) {
	e.preventDefault();
	var idCharity	=	$("#idCharity").val(),
		actionURL	=	idCharity == 0 ? "addDataManualCharity" : "updateDataManualCharity";
		dataForm	=	$("#editor-manualCharity :input").serializeArray(),
		dataSend	=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/charityReport/"+actionURL,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-manualCharity :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();

			$("#editor-manualCharity :input").attr("disabled", false);
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-manualCharity').modal('hide');
				getDataCharityReport();
			}
		}
	});
});

function deleteManualCharity(idCharity, dateCharity, contributorType, contributorName, charityNominal, description){
	var confirmText	=	'This charity data will be deleted. Details;<br/><br/>'+
						'<div class="order-details-customer-info">'+
							'<ul class="ml-10">'+
								'<li> <span>Date</span> <span>'+dateCharity+'</span> </li>'+
								'<li> <span>Contributor Type</span> <span>'+contributorType+'</span> </li>'+
								'<li> <span>Contributor Name</span> <span>'+contributorName+'</span> </li>'+
								'<li> <span>Nominal</span> <span>'+numberFormat(charityNominal)+'</span> </li>'+
								'<li> <span>Description</span> <span>'+description+'</span> </li>'+
							'</ul>'+
						'</div>'+
						'<br/>Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idCharity).attr('data-function', "deleteDataManualCharity");
	$confirmDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData		=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
		funcName	=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		dataSend	=	{idData:idData};

	$.ajax({
		type: 'POST',
		url: baseURL+"finance/charityReport/"+funcName,
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
			
			toastr["warning"](response.msg);
			if(response.status == 200) {
				getDataCharityReport();
				getDataCharityProcessTransfer();
			}
		}
	});
});

$('#editor-transferCharity').off('submit');
$('#editor-transferCharity').on('submit', function(e) {
	e.preventDefault();
	var idBank				=	$("#transferCharity-optionBank").val(),
		accountNumber		=	$("#transferCharity-accountNumber").val(),
		accountHolderName	=	$("#transferCharity-accountHolderName").val(),
		emailNotification	=	$("#transferCharity-emailNotification").val(),
		charityCode			=	$("#transferCharity-charityCode").val(),
		dataSend			=	{
			idBank:idBank,
			accountNumber:accountNumber,
			accountHolderName:accountHolderName,
			emailNotification:emailNotification,
			charityCode:charityCode
		};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/charityReport/processDisburseCharity",
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
			toastr["success"](response.msg);
			getDataCharityReport();
			getDataCharityProcessTransfer();
			$('#modal-transferCharity').modal('hide');
		}
	});
});

function generateDataTableProcessTransfer(page){
	getDataCharityProcessTransfer(page);
}

function getDataCharityProcessTransfer(page = 1){
	var $tableBody		=	$('#table-charityProcessTransfer > tbody'),
		columnNumber	=	$('#table-charityProcessTransfer > thead > tr > th').length;
		dataSend		=	{page:page};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/charityReport/getDataCharityProcessTransfer",
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
					var badgeStatusTransfer	=	btnCancelTransferProc	=	btnDownloadExcel	=	'';
					switch(array.STATUSTRANSFER){
						case "0"	:
						case 0		:	badgeStatusTransfer		=	'<span class="badge badge-warning">Unprocessed</span>';
										btnCancelTransferProc	=	'<button class="button button-danger button-xs" onclick="confirmCancelCharityTransferProcess(this)" data-idCharityRecapProcess="'+array.IDCHARITYRECAPPROCESS+'" data-datePeriod="'+array.DATEPERIODSTARTSTR+" to "+array.DATEPERIODENDSTR+'">'+
																		'<span><i class="fa fa-times"></i>Cancel</span>'+
																	'</button>';
										break;
						case "1"	:
						case 1		:	badgeStatusTransfer	=	'<span class="badge badge-primary">On Process</span>'; break;
						case "2"	:
						case 2		:	badgeStatusTransfer	=	'<span class="badge badge-warning">Transferred</span>'; break;
					}
					
					if(array.URLEXCELREPORT != ''){
						btnDownloadExcel	=	'<a class="button button-primary button-xs" target="_blank" href="'+array.URLEXCELREPORT+'"><span><i class="fa fa-file-excel-o"></i>Excel Report</span></a>';
					}
					
					rows	+=	"<tr>"+
									"<td>"+array.DATEPERIODSTARTSTR+" to "+array.DATEPERIODENDSTR+"</td>"+
									"<td>"+array.PROCESSDATETIME+"</td>"+
									"<td>"+array.PROCESSUSER+"</td>"+
									"<td>"+array.BANKNAME+" - "+array.ACCOUNTNUMBER+"<br/>Account Holder : "+array.ACCOUNTHOLDERNAME+"<br/>"+array.EMAILLIST+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALCHARITY)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALCHARITYNOMINAL)+"</td>"+
									"<td>"+badgeStatusTransfer+"</td>"+
									"<td>"+btnDownloadExcel+"</td>"+
									"<td>"+btnCancelTransferProc+"</td>"+
								"</tr>";
				});
			}

			generatePagination("tablePaginationCharityProcessTransfer", page, response.result.pageTotal);
			generateDataInfo("tableDataCountCharityProcessTransfer", response.result.dataStart, response.result.dataEnd, response.result.dataTotal, 'generateDataTableProcessTransfer')
			$tableBody.html(rows);
			
		}
	});
}

function confirmCancelCharityTransferProcess(elemBtnCancel){
	var idCharityRecapProcess	=	$(elemBtnCancel).attr('data-idCharityRecapProcess'),
		datePeriod				=	$(elemBtnCancel).attr('data-datePeriod'),
		confirmText				=	'Charity transfer process (period : <b>'+datePeriod+'</b>) will be cancelled.<br/><br/>Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idCharityRecapProcess).attr('data-function', "cancelCharityTransferProcess");
	$confirmDialog.modal('show');
}

charityReportFunc();