var $confirmDialog= $('#modal-confirm-action');
if (additionalIncomeFunc == null){
	var additionalIncomeFunc	=	function(){
		$(document).ready(function () {
			localStorage.setItem('viewConfirmDialogApprovalAdditionalIncome', '1');
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			resetOptionDriverEditor();
			getDataAdditionalIncomeRecap();
			getDataAdditionalIncomeAndPointRateSetting();
		});	
	}
}

$('#optionMonth, #optionYear').off('change');
$('#optionMonth, #optionYear').on('change', function(e) {
	getDataAdditionalIncomeRecap();
});

$('#searchKeywordRecap').off('keypress');
$("#searchKeywordRecap").on('keypress',function(e) {
    if(e.which == 13) {
		getDataAdditionalIncomeRecap();
    }
});

function generateDataTableRecap(page){
	getDataAdditionalIncomeRecap(page);
}

function getDataAdditionalIncomeRecap(page = 1){
	var $tableBody		=	$('#table-additionalIncomeRecap > tbody'),
		columnNumber	=	$('#table-additionalIncomeRecap > thead > tr > th').length,
		month			=	$('#optionMonth').val(),
		year			=	$('#optionYear').val(),
		searchKeyword	=	$('#searchKeywordRecap').val(),
		dataSend		=	{page:page, month:month, year:year, searchKeyword:searchKeyword};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalIncome/getDataAdditionalIncomeRecap",
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
					rows	+=	"<tr>"+
									"<td>"+array.DRIVERNAME+"</td>"+
									"<td>"+array.EXCEPTIONREASON+"</td>"+
									"<td>"+array.DATELASTPAYMENT+"</td>"+
									"<td align='right'>"+numberFormat(array.NUMBEROFPAYMENT)+"</td>"+
									"<td align='right'>"+numberFormat(array.NOMINAL)+"</td>"+
									"<td align='right'>"+numberFormat(array.REVIEWPOINT)+"</td>"+
								"</tr>";
				});
			}
			
			generatePagination("tablePaginationAdditionalIncomeRecap", page, response.result.pageTotal, 'generateDataTableRecap');
			generateDataInfo("tableDataCountAdditionalIncomeRecap", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
		}
	});
}

function resetOptionDriverEditor(idDriver){
	setOptionHelper('additionalIncome-optionDriver', 'dataDriver', idDriver);
	$("#additionalIncome-optionDriver").select2();
	$('#additionalIncome-optionDriver').select2({
		dropdownParent: $("#modal-additionalIncome")
	});
}

$('#startDate, #endDate').off('change');
$('#startDate, #endDate').on('change', function(e) {
	getDataAdditionalIncomeAndPointRateSetting();
});

$('#searchKeyword').off('keypress');
$("#searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
		getDataAdditionalIncomeAndPointRateSetting();
    }
});
	
$('#checkboxViewRequestOnly').off('click');
$("#checkboxViewRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewRequestOnly").is(':checked');
	
	if(checked){
		$("#startDate, #endDate, #searchKeyword").attr("disabled", true);
	} else {
		$("#startDate, #endDate, #searchKeyword").attr("disabled", false);
	}
	
	getDataAdditionalIncomeAndPointRateSetting();
});

function generateDataTable(page){
	getDataAdditionalIncomeAndPointRateSetting(page);
}

function getDataAdditionalIncomeAndPointRateSetting(page = 1){
	var $tableBody		=	$('#table-additionalIncome > tbody'),
		columnNumber	=	$('#table-additionalIncome > thead > tr > th').length,
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		searchKeyword	=	$('#searchKeyword').val(),
		viewRequestOnly	=	$("#checkboxViewRequestOnly").is(':checked'),
		dataSend		=	{page:page, startDate:startDate, endDate:endDate, searchKeyword:searchKeyword, viewRequestOnly:viewRequestOnly};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalIncome/getDataAdditionalIncomeAndPointRateSetting",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
			$('#excelDataAdditionalIncome').addClass('d-none').off("click").attr("href", "");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var data			=	response.result.data,
				dataPointRate	=	response.dataPointRate,
				rows			=	"";
			
			if(data.length === 0){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				$.each(data, function(index, array) {
					var inputType		=	array.INPUTTYPE,
						approvalStatus	=	array.APPROVALSTATUS,
						badgeStatus		=	btnEdit	=	btnDelete	=	btnApproveRequest	=	btnRejectRequest	=	'';
					
					if(inputType == 1){
						btnEdit		=	'<button class="button button-box button-primary button-xs" data-idAdditionalIncome="'+array.IDADDITIONALINCOME+'" data-toggle="modal" data-target="#modal-additionalIncome">'+
											'<i class="fa fa-pencil"></i>'+
										'</button>',
						btnDelete	=	'<button class="button button-box button-danger button-xs" onclick="confirmDeleteAdditionalIncome('+array.IDADDITIONALINCOME+')">'+
											'<i class="fa fa-trash"></i>'+
										'</button>';
					} else if(inputType == 2) {
						if(approvalStatus == 0 && viewRequestOnly){
							btnApproveRequest	=	'<button class="button button-box button-info button-xs" onclick="setApprovalAdditionalIncome('+array.IDADDITIONALINCOME+', '+array.IDDRIVER+', 1)">'+
														'<i class="fa fa-check"></i>'+
													'</button>';
							btnRejectRequest	=	'<button class="button button-box button-warning button-xs" onclick="setApprovalAdditionalIncome('+array.IDADDITIONALINCOME+', '+array.IDDRIVER+', -1)">'+
														'<i class="fa fa-times"></i>'+
													'</button>';
						}
					}

					switch(array.APPROVALSTATUS){
						case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>';
										break;
						case "-1"	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>';
										break;
						case "0"	:
						default		:	badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>';
										break;
					}

					
					rows	+=	"<tr class='trDataAdditionalIncome' id='trDataAdditionalIncome"+array.IDADDITIONALINCOME+"' "+
								"data-idAdditionalIncome='"+array.IDADDITIONALINCOME+"' "+
								"data-idDriver='"+array.IDDRIVER+"' "+
								"data-driverName='"+array.DRIVERNAME+"' "+
								"data-date='"+array.INCOMEDATE+"' "+
								"data-dateStr='"+array.INCOMEDATESTR+"' "+
								"data-nominal='"+array.IDADDITIONALINCOME+"' "+
								"data-description='"+array.DESCRIPTION+"' "+
								"data-imageReceipt='"+array.IMAGERECEIPT+"' "+
								"data-imageReceiptURL='"+array.IMAGERECEIPTURL+"'>"+
									"<td>"+array.INCOMEDATESTR+"</td>"+
									"<td>"+array.DRIVERNAME+"</td>"+
									"<td>"+array.DESCRIPTION+"</td>"+
									"<td align='right'>"+numberFormat(array.INCOMENOMINAL)+"</td>"+
									"<td>"+
										"<a href='#' data-imgsrc='"+array.IMAGERECEIPTURL+"' class='zoomImage'>"+
											"<img src='"+array.IMAGERECEIPTURL+"' id='imageReceipt"+array.IDADDITIONALINCOME+"' width='150px'>"+
										"</a>"+
									"</td>"+
									"<td>"+array.INPUTUSER+"<br/>"+array.INPUTDATETIME+"</td>"+
									"<td>"+array.APPROVALUSER+"<br/>"+array.APPROVALDATETIME+"</td>"+
									"<td>"+badgeStatus+"</td>"+
									"<td align='right' id='containerBtn"+array.IDADDITIONALINCOME+"'>"+btnEdit+" "+btnDelete+" "+btnApproveRequest+" "+btnRejectRequest+"</td>"+
								"</tr>";
				});
				if(response.urlExcelAdditonalIncome != "") $('#excelDataAdditionalIncome').removeClass('d-none').on("click").attr("href", response.urlExcelAdditonalIncome);
			}
			
			generatePagination("tablePaginationAdditionalIncome", page, response.result.pageTotal);
			generateDataInfo("tableDataCountAdditionalIncome", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
			$('.zoomImage').off('click');
			$(".zoomImage").on("click", function() {
				var imgSrc	=	$(this).attr('data-imgSrc');
				$('#zoomReceiptImage').attr('src', imgSrc);
				$('#modal-zoomReceiptImage').modal('show');
			});
				
			if(dataPointRate){
				var rowsPointRate	=	'';
				$.each(dataPointRate, function(indexPointRate, arrayPointRate) {
					var btnEdit		=	indexPointRate == dataPointRate.length - 1 ?
										'<button class="button button-box button-primary button-xs" data-idAdditionalIncomeRate="'+arrayPointRate.IDADDITIONALINCOMERATE+'" data-toggle="modal" data-target="#modal-settingPointRate">'+
											'<i class="fa fa-pencil"></i>'+
										'</button>' : '',
						btnDelete	=	indexPointRate == dataPointRate.length - 1 ?
										'<button class="button button-box button-danger button-xs" onclick="confirmDeleteSettingPointRate('+arrayPointRate.IDADDITIONALINCOMERATE+')">'+
											'<i class="fa fa-trash"></i>'+
										'</button>' : '';
					rowsPointRate	+=	"<tr class='trDataAdditionalIncomePointRate' data-idAdditionalIncomeRate='"+arrayPointRate.IDADDITIONALINCOMERATE+"'>"+
											"<td align='right'>"+numberFormat(arrayPointRate.NOMINALMIN)+"</td>"+
											"<td align='right'>"+numberFormat(arrayPointRate.NOMINALMAX)+"</td>"+
											"<td align='right'>"+numberFormat(arrayPointRate.REVIEWPOINT)+"</td>"+
											"<td align='right'>"+btnEdit+" "+btnDelete+"</td>"+
										"</tr>";
				});

				if(rowsPointRate == '' || dataPointRate.length <= 0) rowsPointRate	=	"<tr><td colspan='4' align='center'><center>No data found</center></td></tr>";
				$("#table-additionalIncomesettingPointRate > tbody").html(rowsPointRate);
			}
		}
	});
}

$('#modal-additionalIncome').off('show.bs.modal');
$('#modal-additionalIncome').on('show.bs.modal', function(event) {
	var idAdditionalIncome	=	$(event.relatedTarget).attr('data-idAdditionalIncome'),
		imageReceiptURL		=	ASSET_IMG_URL+"noimage.jpg";
		
	if(idAdditionalIncome == 0){
		resetOptionDriverEditor();
		$("#additionalIncome-nominal").val(0);
		$("#additionalIncome-description").val("");
		$("#additionalIncome-transferReceiptFileName").val("");
	} else {
		var trElemAdditionalIncome	=	$(".trDataAdditionalIncome[data-idAdditionalIncome='"+idAdditionalIncome+"']"),
			idDriver				=	trElemAdditionalIncome.attr('data-idDriver'),
			date					=	trElemAdditionalIncome.attr('data-date'),
			imageReceipt			=	trElemAdditionalIncome.attr('data-imageReceipt'),
			imageReceiptURL			=	trElemAdditionalIncome.attr('data-imageReceiptURL'),
			description				=	trElemAdditionalIncome.find('td').eq(2).html(),
			nominal					=	trElemAdditionalIncome.find('td').eq(3).html();
		resetOptionDriverEditor(idDriver);
		$("#additionalIncome-date").val(date);
		$("#additionalIncome-nominal").val(numberFormat(nominal));
		$("#additionalIncome-description").val(description);
		$("#additionalIncome-transferReceiptFileName").val(imageReceipt);
	}
	
	createUploaderTransferReceipt(idAdditionalIncome);
	$("#additionalIncome-imageTransferReceipt").attr("src", imageReceiptURL).attr("height", "100px");
	$("#additionalIncome-idAdditionalIncome").val(idAdditionalIncome);
});

function createUploaderTransferReceipt(idAdditionalIncome){
	idAdditionalIncome	=	idAdditionalIncome == "" ? 0 : idAdditionalIncome;
	$('.ajax-file-upload-container').remove();
	$("#uploaderTransferReceipt").uploadFile({
		url: baseURL+"financeDriver/additionalIncome/uploadTransferReceipt/"+idAdditionalIncome,
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
				$("#additionalIncome-imageTransferReceipt").removeAttr('src').attr("src", data.urlTransferReceipt).attr("height", data.defaultHeight);
				$("#additionalIncome-transferReceiptFileName").val(data.transferReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#editor-additionalIncome').off('submit');
$('#editor-additionalIncome').on('submit', function(e) {
	e.preventDefault();
	var idDriver				=	$('#additionalIncome-optionDriver').val(),
		date					=	$('#additionalIncome-date').val(),
		nominal					=	$('#additionalIncome-nominal').val().replace(/[^0-9\.]+/g, '') * 1,
		description				=	$('#additionalIncome-description').val(),
		idAdditionalIncome		=	$('#additionalIncome-idAdditionalIncome').val(),
		transferReceiptFileName	=	$('#additionalIncome-transferReceiptFileName').val(),
		dataSend				=	{
			idDriver:idDriver,
			date:date,
			nominal:nominal,
			description:description,
			idAdditionalIncome:idAdditionalIncome,
			transferReceiptFileName:transferReceiptFileName
		};
	
	if(nominal <= 0 || description == '' || description.replace(/\s+/g, '').length <= 6){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please complete all fields in the form with valid data");
		});
		$('#modalWarning').modal('show');
	} else {
		$.ajax({
			type: 'POST',
			url: baseURL+"financeDriver/additionalIncome/insertUpdateAdditionalIncome",
			contentType: 'application/json',
			dataType: 'json',
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
					$('#modal-additionalIncome').modal('hide');
					getDataAdditionalIncomeAndPointRateSetting();
					getDataAdditionalIncomeRecap();
				}
			}
		});
	}
});

function confirmDeleteAdditionalIncome(idAdditionalIncome){
	var trElemAdditionalIncome	=	$(".trDataAdditionalIncome[data-idAdditionalIncome='"+idAdditionalIncome+"']"),
		idDriver				=	trElemAdditionalIncome.attr('data-idDriver'),
		date					=	trElemAdditionalIncome.find('td').eq(0).html(),
		driverName				=	trElemAdditionalIncome.find('td').eq(1).html(),
		description				=	trElemAdditionalIncome.find('td').eq(2).html();
		nominal					=	trElemAdditionalIncome.find('td').eq(3).html();
		confirmText				=	'Additional income data will be deleted. Details;<br/><br/>'+
									'<div class="order-details-customer-info">'+
										'<ul class="ml-5">'+
											'<li> <span>Driver</span> <span>'+driverName+'</span> </li>'+
											'<li> <span>Date</span> <span>'+date+'</span> </li>'+
											'<li> <span>Description</span> <span>'+description+'</span> </li>'+
											'<li> <span>Nominal</span> <span>'+numberFormat(nominal)+'</span> </li>'+
										'</ul>'+
									'</div>'+
									'<br/>Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idAdditionalIncome).attr('data-idDriver', idDriver).attr('data-function', "deleteAdditionalIncome");
	$confirmDialog.modal('show');
}

function setApprovalAdditionalIncome(idAdditionalIncome, idDriver, status){
	var viewConfirmDialog		=	localStorage.getItem('viewConfirmDialogApprovalAdditionalIncome'),
		txtApprovalStatus		=	status == 1 ? "Approved" : "Rejected",
		trDataAdditionalIncome	=	$("#trDataAdditionalIncome"+idAdditionalIncome);
	
	if(viewConfirmDialog == '1'){
		$("#confirmApproveAdditionalIncome-idDriver").html(trDataAdditionalIncome.attr('data-idDriver'));
		$("#confirmApproveAdditionalIncome-driverName").html(trDataAdditionalIncome.attr('data-driverName'));
		$("#confirmApproveAdditionalIncome-dateReceipt").html(trDataAdditionalIncome.attr('data-dateStr'));
		$("#confirmApproveAdditionalIncome-nominal").html(numberFormat(trDataAdditionalIncome.attr('data-nominal')));
		$("#confirmApproveAdditionalIncome-description").html(trDataAdditionalIncome.attr('data-description'));
		
		$("#confirmApproveAdditionalIncome-txtApprovalStatus").html(txtApprovalStatus);
		$('#confirmApproveAdditionalIncome-imageReceipt').attr('src', trDataAdditionalIncome.attr('data-imageReceiptURL'));
		$("#confirmApproveAdditionalIncome-btnSubmit").attr('data-idAdditionalIncome', idAdditionalIncome).attr('data-idDriver', idDriver).attr('data-status', status);
		$('#modal-confirmApproveAdditionalIncome').modal('show');
	} else {
		submitApprovalAdditionalIncome(idAdditionalIncome, idDriver, status);
	}
}

$('#confirmApproveAdditionalIncome-btnSubmit').off('click');
$('#confirmApproveAdditionalIncome-btnSubmit').on('click', function(e) {
	e.preventDefault();
	if($('#confirmApproveAdditionalIncome-disableConfirm').is(":checked")){
		localStorage.setItem('viewConfirmDialogApprovalAdditionalIncome', '0');
	}
	submitApprovalAdditionalIncome($("#confirmApproveAdditionalIncome-btnSubmit").attr('data-idAdditionalIncome'), $("#confirmApproveAdditionalIncome-btnSubmit").attr('data-idDriver'), $("#confirmApproveAdditionalIncome-btnSubmit").attr('data-status'));
});

function submitApprovalAdditionalIncome(idAdditionalIncome, idDriver, status){
	var dataSend	=	{
		idAdditionalIncome:idAdditionalIncome,
		idDriver:idDriver,
		status:status
	};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalIncome/submitApprovalAdditionalIncome",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$('#window-loader').modal('show');
			$('#modal-confirmApproveAdditionalIncome').modal('hide');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');

			if(response.status == 200){
				var badgeStatus			=	"",
					status				=	response.statusApproval,
					idAdditionalIncome	=	response.idAdditionalIncome;
				switch(status){
					case -1		:	
					case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>';
									break;
					case -1		:	
					case "-1"	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>';
									break;
					case 0		:
					case "0"	:
					default		:	badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>';
									break;
				}
				
				var trDataAdditionalIncome	=	$("#trDataAdditionalIncome"+idAdditionalIncome);
				trDataAdditionalIncome.find('td').eq(7).find('span.badge').remove();
				trDataAdditionalIncome.find('td').eq(7).prepend(badgeStatus);
				trDataAdditionalIncome.find('td').eq(6).html(response.approvalUser+"<br/>"+response.approvalDateTime);
				
				trDataAdditionalIncome.find('td:last').find('button.button-box').remove();
				$("#containerBtn"+idAdditionalIncome).html("");
				
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

$('#modal-settingPointRate').off('show.bs.modal');
$('#modal-settingPointRate').on('show.bs.modal', function(event) {
	var minNominal				=	0,
		idAdditionalIncomeRate	=	$(event.relatedTarget).attr('data-idAdditionalIncomeRate');
		
	if(idAdditionalIncomeRate == 0){
		if($(".trDataAdditionalIncomePointRate").length > 0){
			minNominal	=	$('#table-additionalIncomesettingPointRate tbody tr.trDataAdditionalIncomePointRate:last td:eq(1)').html().replace(/[^0-9\.]+/g, '') * 1;
			minNominal	=	minNominal + 1;
		}
		$("#settingPointRate-maxNominal").val(numberFormat(minNominal));
		$("#settingPointRate-point").val(0);
	} else {
		var trElemSettingPointRate	=	$(".trDataAdditionalIncomePointRate[data-idAdditionalIncomeRate='"+idAdditionalIncomeRate+"']"),
			minNominal				=	trElemSettingPointRate.find('td').eq(0).html(),
			maxNominal				=	trElemSettingPointRate.find('td').eq(1).html(),
			point					=	trElemSettingPointRate.find('td').eq(2).html();
		$("#settingPointRate-maxNominal").val(maxNominal);
		$("#settingPointRate-point").val(point);
	}
	
	$("#settingPointRate-minNominal").html(numberFormat(minNominal));
	$("#settingPointRate-idAdditionalIncomeRate").val(idAdditionalIncomeRate);
});

$('#editor-settingPointRate').off('submit');
$('#editor-settingPointRate').on('submit', function(e) {
	e.preventDefault();
	var nominalMin				=	$('#settingPointRate-minNominal').html().replace(/[^0-9\.]+/g, '') * 1,
		nominalMax				=	$('#settingPointRate-maxNominal').val().replace(/[^0-9\.]+/g, '') * 1,
		point					=	$('#settingPointRate-point').val().replace(/[^0-9\.]+/g, '') * 1,
		idAdditionalIncomeRate	=	$('#settingPointRate-idAdditionalIncomeRate').val(),
		dataSend				=	{
			idAdditionalIncomeRate:idAdditionalIncomeRate,
			nominalMin:nominalMin,
			nominalMax:nominalMax,
			point:point,
		};
	
	if(nominalMax <= nominalMin){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("The minimum nominal <b>must not be greater than or equal to</b> the maximum nominal");
		});
		$('#modalWarning').modal('show');
	} else {
		$.ajax({
			type: 'POST',
			url: baseURL+"financeDriver/additionalIncome/insertUpdatePointRate",
			contentType: 'application/json',
			dataType: 'json',
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
					$('#modal-settingPointRate').modal('hide');
					getDataAdditionalIncomeAndPointRateSetting();
				}
			}
		});
	}
});

function confirmDeleteSettingPointRate(idAdditionalIncomeRate){
	var trElemSettingPointRate	=	$(".trDataAdditionalIncomePointRate[data-idAdditionalIncomeRate='"+idAdditionalIncomeRate+"']"),
		minNominal				=	trElemSettingPointRate.find('td').eq(0).html(),
		maxNominal				=	trElemSettingPointRate.find('td').eq(1).html(),
		point					=	trElemSettingPointRate.find('td').eq(2).html();
		confirmText				=	'Additional income setting point rate will be deleted. Details;<br/><br/>'+
									'<div class="order-details-customer-info">'+
										'<ul class="ml-5">'+
											'<li> <span>Min Nominal</span> <span>'+numberFormat(minNominal)+'</span> </li>'+
											'<li> <span>Max Nominal</span> <span>'+numberFormat(maxNominal)+'</span> </li>'+
											'<li> <span>Point</span> <span>'+numberFormat(point)+'</span> </li>'+
										'</ul>'+
									'</div>'+
									'<br/>Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idAdditionalIncomeRate).attr('data-function', "deleteAdditionalIncomeSettingPointRate");
	$confirmDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData		=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
		funcName	=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		dataSend	=	{idData:idData};
	
	if(funcName == 'deleteAdditionalIncome'){
		let idDriver		=	$confirmDialog.find('#confirmBtn').attr('data-idDriver');
		dataSend.idDriver	=	idDriver;
	}

	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/additionalIncome/"+funcName,
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
				getDataAdditionalIncomeRecap();
				getDataAdditionalIncomeAndPointRateSetting();
				refreshDriverPoint();
			}
		}
	});
});

function refreshDriverPoint(){
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/refreshDriverPoint",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
		}
	});
}

additionalIncomeFunc();