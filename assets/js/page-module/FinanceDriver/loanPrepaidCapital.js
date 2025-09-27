var $confirmDialog= $('#modal-confirm-action');
if (loanPrepaidCapitalFunc == null){
	var loanPrepaidCapitalFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionDriver', 'dataDriver');
			setOptionHelper('optionLoanType', 'dataLoanType');
			setOptionHelper('optionBank', 'dataBank');
			$("#optionDriver").select2();
			
			setOptionHelper('optionDriverInstallment', 'dataDriver');
			setOptionHelper('optionInstallmentLoanType', 'dataLoanType');

			setOptionHelper('optionInstallmentDriverType', 'dataDriverType');
			setOptionHelper('optionInstallmentDriver', 'dataDriver');
			$('#optionInstallmentDriverType').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionInstallmentDriver', 'dataDriver', false, false, this.value);
				} else {
					setOptionHelper('optionInstallmentDriver', 'dataDriver');
				}
			});
			
			setOptionHelper('optionDetailDriver', 'dataDriver');
			setOptionHelper('optionLoanTypeDetail', 'dataLoanType');
			$("#optionDetailDriver").select2();

			getDataTable();
			getDetailLoanPerDriver();
			getLoanInstallmentRequest();
		});
	}
}

$('#searchKeyword').off('keypress');
$("#searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
        getDataTable();
    }
});

function generateDataTable(page){
	getDataTable(page);
}
	
$('#checkboxViewRequestOnly').off('click');
$("#checkboxViewRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewRequestOnly").is(':checked');
	if(checked){
		$("#btnShowAllLoanPrepaidCapitalRequest").addClass("d-none");
	} else {
		$("#btnShowAllLoanPrepaidCapitalRequest").removeClass("d-none");
	}
	getDataTable();
});

function getDataTable(page = 1){
	
	var $tableBody		=	$('#table-loanPrepaidCapital > tbody'),
		columnNumber	=	$('#table-loanPrepaidCapital > thead > tr > th').length,
		keyword			=	$('#searchKeyword').val(),
		viewRequestOnly	=	$("#checkboxViewRequestOnly").is(':checked'),
		dataSend		=	{page:page, keyword:keyword, viewRequestOnly:viewRequestOnly};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/getDataLoanPrepaidCapital",
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
			var rows		=	"",
				noDataElem	=	'<tr>'+
									'<td align="center">'+
										'<div class="row"><div class="col-12 text-center"><img src="'+ASSET_IMG_URL+'no-data.png" width="120px"><h5>No data</h5><p>'+response.msg+'</p></div></div>'+
									'</td>'+
								'</tr>',
				data		=	response.result.data,
				totalRequest=	response.totalRequest;
			
			if(totalRequest > 0){
				$("#totalLoanPrepaidCapitalRequestAlert").removeClass('d-none');
				$("#totalLoanPrepaidCapitalRequest").html(totalRequest);
				
				$('#btnShowAllLoanPrepaidCapitalRequest').off('click');
				$("#btnShowAllLoanPrepaidCapitalRequest").on('click',function(e) {
					$("#checkboxViewRequestOnly").prop('checked', true);
					$("#btnShowAllLoanPrepaidCapitalRequest").addClass("d-none");
					getDataTable();
				});
			} else {
				$('#btnShowAllLoanPrepaidCapitalRequest').off('click');
				$("#totalLoanPrepaidCapitalRequestAlert").addClass('d-none');
				$("#totalLoanPrepaidCapitalRequest").html(0);
			}
			
			if(data.length === 0){
				$('#excelLoanRecap').addClass('d-none').off("click").attr("href", "");
				rows	=	noDataElem;
			} else {
				$('#excelLoanRecap').removeClass('d-none').on("click").attr("href", response.urlExcelLoanRecap);
				$.each(data, function(index, array) {
					
					var	scheduleType				=	loanRequestAlert	=	prepaidCapitalRequestAlert	=	"",
						loanRequestData				=	array.LOANREQUESTDATA,
						prepaidCapitalRequestData	=	array.PREPAIDCAPITALREQUESTDATA;
					switch(array.SCHEDULETYPE){
						case "1"	:	scheduleType	=	"Auto"; break;
						case "2"	:	scheduleType	=	"Manual"; break;
						default		:	scheduleType	=	"-"; break;
					}
					
					if(loanRequestData){
						loanRequestAlert			=	'<div class="col-sm-12 mt-10">'+
															'<div class="alert alert-success p-3">'+
																'<strong>New Loan Request ['+loanRequestData.LOANTYPE+'] | '+numberFormat(loanRequestData.AMOUNT)+' | </strong> Notes : '+loanRequestData.NOTES+
																'<button class="button button-xs button-primary button-outline pull-right" onclick="showDetailsLoanPrepaidCapitalRequest('+loanRequestData.IDLOANDRIVERREQUEST+')">'+
																	'<span>Details</span>'+
																'</button>'+
															'</div>'+
														'</div>';
					}
					
					if(prepaidCapitalRequestData){
						prepaidCapitalRequestAlert	=	'<div class="col-sm-12 mt-10">'+
															'<div class="alert alert-warning p-3">'+
																'<strong>New Loan Request | '+numberFormat(prepaidCapitalRequestData.AMOUNT)+' | </strong> Notes : '+prepaidCapitalRequestData.NOTES+
																'<button class="button button-xs button-primary button-outline pull-right" onclick="showDetailsLoanPrepaidCapitalRequest('+prepaidCapitalRequestData.IDLOANDRIVERREQUEST+')">'+
																	'<span>Details</span>'+
																'</button>'+
															'</div>'+
														'</div>';
					}
					
					rows	+=	'<tr>'+
									'<td class="py-1">'+
										'<div class="rowDriver rounded-lg row px-3 py-3 ml-2 mr-2 mb-1 bg-white">'+
											'<div class="col-lg-6 col-sm-12">'+
												'<p class="mb-0">'+
													'<span class="badge badge-pill badge-primary text-white">'+array.DRIVERTYPE+'</span>'+
													'<b> '+array.NAME+'</b>'+
												'</p>'+
												'<p class="mb-0">'+array.ADDRESS+'</p>'+
												'<p class="mb-0">'+array.PHONE+' | '+array.EMAIL+'</p>'+
												'<span class="badge badge-outline badge-success h5 py-0">Total : '+numberFormat(array.TOTALLOANPREPAIDCAPITAL)+'</span>'+
											'</div>'+
											'<div class="col-lg-3 col-sm-12">'+
												'<b>Loan :</b><br/>'+
												'<i class="fa fa-money text-success"></i> <b>IDR '+numberFormat(array.TOTALLOAN)+'</b><br/>'+
												'<i class="fa fa-calendar text-warning"></i> Last Transaction : '+array.MAXDATELOAN+'<br/>'+
												'<span class="badge badge-pill badge-info w-100 text-white" onclick="showLoanPrepaidCapitalHistory('+array.IDDRIVER+', 1)" style="cursor: pointer;">Show History</span>'+
											'</div>'+
											'<div class="col-lg-3 col-sm-12">'+
												'<b>Prepaid Capital :</b><br/>'+
												'<i class="fa fa-money text-success"></i> <b>IDR '+numberFormat(array.TOTALPREPAIDCAPITAL)+'</b><br/>'+
												'<i class="fa fa-calendar text-warning"></i> Last Transaction : '+array.MAXDATEPREPAIDCAPITAL+'<br/>'+
												'<span class="badge badge-pill badge-info w-100 text-white" onclick="showLoanPrepaidCapitalHistory('+array.IDDRIVER+', 2)" style="cursor: pointer;">Show History</span>'+
											'</div>'+
											loanRequestAlert+
											prepaidCapitalRequestAlert+
										'</div>'+
									'</td>'+
								'</tr>';
					
				});
			
			}

			generatePagination("tablePagination", page, response.result.pageTotal);
			generateDataInfo("tableDataCount", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
		
	});
	
}

$('#modal-addLoanRecord').off('shown.bs.modal');
$('#modal-addLoanRecord').on('shown.bs.modal', function (e) {
	$("#optionDriver").val($("#optionDriver option:first").val());
	$("#optionDriver").select2();
	$("#optionLoanType").val($("#optionLoanType option:first").val());
	$("#optionBank").val($("#optionBank option:first").val());
	$("#loanPrincipalNominal, #interestPerAnnumInteger, #interestPerAnnumDecimal, #totalPeriodMonth").val(0);
	$("#loanDescription, #driverNote, #accountNumber").val("");
	$("#totalPeriodMonth").val(1);
	$("#addLoanRecord-summary-nominalPrincipal, #addLoanRecord-summary-nominalInterest, #addLoanRecord-summary-nominalTotal, #addLoanRecord-summary-installmentPerMonth").html('0');
	$("input[name=radioBankAccount][value=0]").prop('checked', true);
	$("#optionBank, #accountNumber, #accountHolderName").attr("disabled", false);
	$("#imageTransferReceipt").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "100px");
	getListBankAccountDriver();
	activateCounterFieldEvent();
});

$('#optionDriver').off('change');
$('#optionDriver').on('change', function() {
	getListBankAccountDriver();
});

function getListBankAccountDriver(){
	var idDriver	=	$("#optionDriver").val(),
		dataSend	=	{idDriver:idDriver};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/getListBankAccountDriver",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$(".radioBankAccount-child").remove();
			$("input[name=radioBankAccount][value=0]").prop('checked', true);
			$("#optionBank, #accountNumber, #accountHolderName").attr("disabled", false);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var data	=	response.dataBankAccount;
				$.each(data, function(index, array) {
					$('#containerRadioBankAccount').prepend('<label class="adomx-radio-2 radioBankAccount-child"><input type="radio" name="radioBankAccount" value="'+array.IDBANKACCOUNTPARTNER+'" class="radioBankAccountElem"> <i class="icon"></i> '+array.DETAILACCOUNT+'</label>');
				});

				$('.radioBankAccountElem').off('change');
				$('.radioBankAccountElem').on('change', function() {
				  if($(this).val() == '0'){
					$("#optionBank, #accountNumber, #accountHolderName").attr("disabled", false);
				  } else {
					$("#optionBank, #accountNumber, #accountHolderName").attr("disabled", true);
				  }
				}).filter(':checked').trigger('change');
			}
		}
	});
}

$('#totalPeriodMonth').off('change');
$('#totalPeriodMonth').on('change', function() {
	calculateDetailLoan();
});

function calculateDetailLoan(){
	var nominalPrincipal		=	$("#loanPrincipalNominal").val().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnumInteger	=	$("#interestPerAnnumInteger").val().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnumDecimal	=	$("#interestPerAnnumDecimal").val().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnumDecimal	=	interestPerAnnumDecimal > 9 ? interestPerAnnumDecimal / 100 : interestPerAnnumDecimal / 10,
		totalPeriodMonth		=	$("#totalPeriodMonth").val().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnum		=	interestPerAnnumInteger + interestPerAnnumDecimal,
		interestPerMonth		=	interestPerAnnum / 12,
		interestTotal			=	interestPerMonth * totalPeriodMonth,
		interestNominalTotal	=	nominalPrincipal * interestTotal / 100,
		interestNominalTotal	=	Math.floor(interestNominalTotal),
		loanTotalNominal		=	nominalPrincipal + interestNominalTotal,
		monthlyInstallment		=	loanTotalNominal / totalPeriodMonth,
		monthlyInstallment		=	Math.floor(monthlyInstallment);
	
	$("#addLoanRecord-summary-nominalPrincipal").html(numberFormat(nominalPrincipal));
	$("#addLoanRecord-summary-nominalInterest").html(numberFormat(interestNominalTotal));
	$("#addLoanRecord-summary-nominalTotal").html(numberFormat(loanTotalNominal));
	$("#addLoanRecord-summary-installmentPerMonth").html(numberFormat(monthlyInstallment));
}

$('#editor-addLoanRecord').off('submit');
$('#editor-addLoanRecord').on('submit', function(e) {
	e.preventDefault();
	var dataForm	=	$("#editor-addLoanRecord :input").serializeArray(),
		loanTypeStr	=	$("#optionLoanType option:selected").text(),
		dataSend	=	{loanTypeStr:loanTypeStr};
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/saveNewLoanRecord",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#editor-addLoanRecord :input").attr("disabled", true);
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			$("#editor-addLoanRecord :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);			
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-addLoanRecord').modal('hide');
				getDataTable();
			}
		}
	});
});

function showDetailsLoanPrepaidCapitalRequest(idLoanDriverRequest){
	var dataSend		=	{idLoanDriverRequest:idLoanDriverRequest};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/getDetailLoanPrepaidCapitalRequest",
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
				var detailLoanRequest	=	response.detailLoanRequest,
					statusLoanCapital	=	detailLoanRequest.STATUSLOANCAPITAL,
					loanType			=	statusLoanCapital == "1" || statusLoanCapital == 1 ? "Loan - "+detailLoanRequest.LOANTYPE : "Prepaid Capital";

				$("#loanPrepaidCapitalRequest-driverName").html(detailLoanRequest.DRIVERNAME);
				$("#loanPrepaidCapitalRequest-loanType").html(loanType);
				$("#loanPrepaidCapitalRequest-notes").html(detailLoanRequest.NOTES);
				$("#loanPrepaidCapitalRequest-dateTimeRequest").html(detailLoanRequest.DATETIMEINPUT);
				$("#loanPrepaidCapitalRequest-amount").html(numberFormat(detailLoanRequest.AMOUNT));
				$("#loanPrepaidCapitalRequest-imgBankLogo").attr('src', detailLoanRequest.BANKLOGO);
				$("#loanPrepaidCapitalRequest-bankName").html(detailLoanRequest.BANKNAME);
				$("#loanPrepaidCapitalRequest-accountNumber").html(detailLoanRequest.ACCOUNTNUMBER);
				$("#loanPrepaidCapitalRequest-accountHolderName").html(detailLoanRequest.ACCOUNTHOLDERNAME);
				$("#idLoanDriverRequest").val(idLoanDriverRequest);
				activateCounterFieldEvent();
				calculateDetailLoanRequest();
				
				$('#loanPrepaidCapitalRequest-totalPeriodMonth').off('change');
				$('#loanPrepaidCapitalRequest-totalPeriodMonth').on('change', function() {
					calculateDetailLoanRequest();
				});
				
				$('#modal-loanPrepaidCapitalRequest').modal('show');
			} else {
				$("#idLoanDriverRequest").val(0);
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
}

function calculateDetailLoanRequest(){
	var nominalPrincipal		=	$("#loanPrepaidCapitalRequest-amount").html().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnumInteger	=	$("#loanPrepaidCapitalRequest-interestPerAnnumInteger").val().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnumDecimal	=	$("#loanPrepaidCapitalRequest-interestPerAnnumDecimal").val().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnumDecimal	=	interestPerAnnumDecimal > 9 ? interestPerAnnumDecimal / 100 : interestPerAnnumDecimal / 10,
		totalPeriodMonth		=	$("#loanPrepaidCapitalRequest-totalPeriodMonth").val().replace(/[^0-9\.]+/g, '') * 1,
		interestPerAnnum		=	interestPerAnnumInteger + interestPerAnnumDecimal,
		interestPerMonth		=	interestPerAnnum / 12,
		interestTotal			=	interestPerMonth * totalPeriodMonth,
		interestNominalTotal	=	nominalPrincipal * interestTotal / 100,
		interestNominalTotal	=	Math.floor(interestNominalTotal),
		loanTotalNominal		=	nominalPrincipal + interestNominalTotal,
		monthlyInstallment		=	loanTotalNominal / totalPeriodMonth,
		monthlyInstallment		=	Math.floor(monthlyInstallment);
	
	$("#loanPrepaidCapitalRequest-summary-nominalPrincipal").html(numberFormat(nominalPrincipal));
	$("#loanPrepaidCapitalRequest-summary-nominalInterest").html(numberFormat(interestNominalTotal));
	$("#loanPrepaidCapitalRequest-summary-nominalTotal").html(numberFormat(loanTotalNominal));
	$("#loanPrepaidCapitalRequest-summary-installmentPerMonth").html(numberFormat(monthlyInstallment));
}

$('#editor-loanPrepaidCapitalRequest').off('submit');
$('#editor-loanPrepaidCapitalRequest').on('submit', function(e) {
	e.preventDefault();
	var idLoanDriverRequest	=	$("#idLoanDriverRequest").val();
	confirmApproveRejectLoanPrepaidCapitalRequest(idLoanDriverRequest, 1)
});

$('#btnRejectLoanPrepaidCapitalRequest').off('click');
$('#btnRejectLoanPrepaidCapitalRequest').on('click', function(e) {
	e.preventDefault();
	var idLoanDriverRequest	=	$("#idLoanDriverRequest").val();
	confirmApproveRejectLoanPrepaidCapitalRequest(idLoanDriverRequest, -1)
});

function confirmApproveRejectLoanPrepaidCapitalRequest(idLoanDriverRequest, status){
	
	var strStatus	=	status == 1 ? "Approve" : "Reject",
		functionUrl	=	"approveRejectLoanPrepaidCapitalRequest",
		confirmText	=	'Are you sure you want to <b>'+strStatus+'</b> this loan/prepaid capital request?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idLoanDriverRequest', idLoanDriverRequest).attr('data-status', status).attr('data-function', functionUrl);
	$confirmDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	e.preventDefault();
	var functionUrl		=	$confirmDialog.find('#confirmBtn').attr('data-function');
	switch(functionUrl){
		case "approveRejectLoanPrepaidCapitalRequest"	:
			var idLoanDriverRequest		=	$confirmDialog.find('#confirmBtn').attr('data-idLoanDriverRequest'),
				status					=	$confirmDialog.find('#confirmBtn').attr('data-status'),
				interestPerAnnumInteger	=	$('#loanPrepaidCapitalRequest-interestPerAnnumInteger').val(),
				interestPerAnnumDecimal	=	$('#loanPrepaidCapitalRequest-interestPerAnnumDecimal').val(),
				totalPeriodMonth		=	$('#loanPrepaidCapitalRequest-totalPeriodMonth').val(),
				dataSend				=	{
					idLoanDriverRequest:idLoanDriverRequest,
					status:status,
					interestPerAnnumInteger:interestPerAnnumInteger,
					interestPerAnnumDecimal:interestPerAnnumDecimal,
					totalPeriodMonth:totalPeriodMonth
				};
			break;
		case "approveRejectInstallmentRequest"	:
			var idLoanInstallmentRequest=	$confirmDialog.find('#confirmBtn').attr('data-idLoanInstallmentRequest'),
				status					=	$confirmDialog.find('#confirmBtn').attr('data-status'),
				dataSend				=	{idLoanInstallmentRequest:idLoanInstallmentRequest, status:status};
			break;
		default		:
			var dataSend			=	{};
			break;
	}

	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/"+functionUrl,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			if(functionUrl == "approveRejectLoanPrepaidCapitalRequest") $("#editor-loanPrepaidCapitalRequest :input").attr("disabled", true);
			if(functionUrl == "approveRejectInstallmentRequest") $("#editor-loanInstallmentRequest :input").attr("disabled", true);
			
			$confirmDialog.modal('hide');
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();

			if(functionUrl == "approveRejectLoanPrepaidCapitalRequest") $("#editor-loanPrepaidCapitalRequest :input").attr("disabled", false);
			if(functionUrl == "approveRejectInstallmentRequest") $("#editor-loanInstallmentRequest :input").attr("disabled", false);

			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);			
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				if(functionUrl == "approveRejectLoanPrepaidCapitalRequest"){
					$('#modal-loanPrepaidCapitalRequest').modal('hide');
					getDataTable();
				}

				if(functionUrl == "approveRejectInstallmentRequest"){
					$('#modal-loanInstallmentRequest').modal('hide');
					getLoanInstallmentRequest();
				}
			}
			
		}
		
	});
	
});

function showLoanPrepaidCapitalHistory(idDriver, typeLoanCapital){
	
	var dataSend		=	{idDriver:idDriver, typeLoanCapital:typeLoanCapital};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/getDetailHistoryLoanPrepaidCapital",
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
				var detailDriver		=	response.detailDriver,
					dataHistory			=	response.dataHistory,
					loanPrepaidBalance	=	response.loanPrepaidBalance,
					loanType			=	typeLoanCapital == "1" ? "Loan" : "Prepaid Capital",
					rowsHistory			=	"";

				$("#spanLoanPrepaidCapitalHistoryType").html(loanType);
				$("#loanPrepaidCapitalHistory-name").html('<i class="badge badge-pill badge-primary text-white">'+detailDriver.DRIVERTYPE+'</i> <b>'+detailDriver.NAME+'</b>');
				$("#loanPrepaidCapitalHistory-address").html(detailDriver.ADDRESS);
				$("#loanPrepaidCapitalHistory-contact").html(detailDriver.PHONE+" | "+detailDriver.EMAIL);
				$("#loanPrepaidCapitalHistory-total").html(numberFormat(loanPrepaidBalance));
				$("#loanPrepaidCapitalHistory-page").val(1);
				
				if(dataHistory && dataHistory.length > 0){
					$.each(dataHistory, function(index, array) {
						
						var btnShowTransferReceipt	=	'';
						if(array.RECEIPTFILE != ''){
							btnShowTransferReceipt	=	'<button type="button" class="button button-box button-xs button-primary" onclick="showTransferReceiptFile(\''+array.RECEIPTFILE+'\')">'+
															'<i class="fa fa-list-alt"></i>'+
														'</button>';
						}
						
						rowsHistory	+=	"<tr>"+
											"<td>"+array.DATETIMEINPUT+"</td>"+
											"<td>"+array.LOANTYPE+"</td>"+
											"<td>"+array.DESCRIPTION+"</td>"+
											"<td>"+array.TYPE+"</td>"+
											"<td align='right'>"+numberFormat(array.AMOUNT)+"</td>"+
											"<td align='right'>"+numberFormat(array.SALDO)+"</td>"+
											"<td align='center'>"+btnShowTransferReceipt+"</td>"+
										"</tr>";
					});
				} else {
					rowsHistory	=	'<tr>'+
										'<td colspan="7" id="noDataLoanPrepaidCapitalHistory" align="center">'+
											'<img src="'+ASSET_IMG_URL+'no-data.png" width="60px">'+
											'<h5>No data</h5>'+
											'<p>This driver has no loan / prepaid capital history</p>'+
										'</td>'+
									'</tr>';
				}
				
				$('#tbodyRatingPointHistory').html(rowsHistory);
				$('#modal-loanPrepaidCapitalHistory').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
}

function showTransferReceiptFile(urlFile){
	$("#transferReceiptPreview").html('');
	$("#transferReceiptPreview").html('<iframe id="iFrameTransferReceiptPreview" width="100%" height="400" padding="8px" src="'+urlFile+'" frameborder="0"></iframe>');
	$('#modal-loanPrepaidCapitalTransferReceipt').modal('show');
}

$('#optionDetailDriver, #optionLoanTypeDetail, #startDateDetail, #endDateDetail').off('change');
$('#optionDetailDriver, #optionLoanTypeDetail, #startDateDetail, #endDateDetail').on('change', function(e) {
	getDetailLoanPerDriver();
});

function getDetailLoanPerDriver(){
	var $tableBody		=	$('#table-detailLoanPerDriver > tbody'),
		columnNumber	=	$('#table-detailLoanPerDriver > thead > tr > th').length,
		idDriver		=	$('#optionDetailDriver').val(),
		idLoanType		=	$('#optionLoanTypeDetail').val(),
		startDate		=	$('#startDateDetail').val(),
		endDate			=	$('#endDateDetail').val(),
		dataSend		=	{
								idDriver:idDriver,
								idLoanType:idLoanType,
								startDate:startDate,
								endDate:endDate
							};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/getDataLoanPerDriver",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#totalLoanCar, #totalLoanPersonal, #totalPrepaidCapital").html("0");
			$("#detailPerDriverTab-activeLoan-containerDetailPerActiveLoan").html("");
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var data				=	response.dataTable,
				dataActiveLoanRecap	=	response.dataActiveLoanRecap,
				rows				=	"";
				
			if(response.status != 200){
				$('#excelDataPerDriver').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td></tr>";
			} else {
				$("#totalLoanCar").html(numberFormat(response.saldoLoanCar));
				$("#totalLoanPersonal").html(numberFormat(response.saldoLoanPersonal));
				$("#totalPrepaidCapital").html(numberFormat(response.saldoLoanPrepaidCapital));

				if(response.urlExcelPerDriver != "") $('#excelDataPerDriver').removeClass('d-none').on("click").attr("href", response.urlExcelPerDriver);
				
				if(data == false){
					rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No loan record found on selected date range</center></td></tr>";
				} else {
					$.each(data, function(index, array) {
						var crdbType	=	array.TYPE == 'K' ? "CR" : "DB",
							amountType	=	array.TYPE == 'K' ? '-' : '';
							
						rows			+=	"<tr>"+
												"<td>"+array.DATETIMEINPUTSTR+"</td>"+
												"<td>"+array.LOANTYPE+"</td>"+
												"<td>"+array.DESCRIPTION+"</td>"+
												"<td>"+crdbType+"</td>"+
												"<td align='right'>"+amountType+" "+numberFormat(array.AMOUNT)+"</td>"+
											"</tr>";
					});
				}
				
				if(dataActiveLoanRecap.length > 0){
					var elemActiveLoanRecap	=	'';
					$.each(dataActiveLoanRecap, function(index, arrayActiveLoanRecap) {
						var dataHistoryInstallment	=	arrayActiveLoanRecap.DATAHISTORYLOANINSTALLMENT,
							rowsInstallmentHistory	=	'';

						if(dataHistoryInstallment.length > 0){
							$.each(dataHistoryInstallment, function(index, arrayHistoryInstallment) {
								rowsInstallmentHistory	+=	'<tr>'+
																'<td>'+arrayHistoryInstallment.TRANSACTIONDATE+'</td>'+
																'<td>'+arrayHistoryInstallment.INSTALLMENTPERIOD+'</td>'+
																'<td>'+arrayHistoryInstallment.DESCRIPTION+'</td>'+
																'<td align="right">'+numberFormat(arrayHistoryInstallment.NOMINALINSTALLMENT)+'</td>'+
																'<td align="right">'+numberFormat(arrayHistoryInstallment.NOMINALSALDO)+'</td>'+
																'<td>'+arrayHistoryInstallment.INPUTUSER+'</td>'+
																'<td>'+arrayHistoryInstallment.INPUTDATETIME+'</td>'+
															'</tr>';
							});
						} else {
							rowsInstallmentHistory	=	'<tr><td colspan="7" align="center">No installment history found</td></tr>';
						}
						
						elemActiveLoanRecap			+=	'<div class="col-12 mt-10 bg-white border-allround">'+
															'<div class="row px-10 py-10">'+
																'<div class="col-12 mb-10 mt-10">'+
																	'<h6 class="mb-0">Loan Detail</h6>'+
																'</div>'+
																'<div class="col-lg-4 col-md-12 mb-10 border-right">'+
																	'<div class="order-details-customer-info">'+
																		'<ul>'+
																			'<li> <span>Loan Type</span> <span>'+arrayActiveLoanRecap.LOANTYPE+'</span> </li>'+
																			'<li> <span>Loan Period</span> <span>'+arrayActiveLoanRecap.LOANDURATIONMONTH+' Month(s)</span> </li>'+
																			'<li> <span>Disbursement Date</span> <span>'+arrayActiveLoanRecap.LOANDATEDISBURSEMENTSTR+'</span> </li>'+
																			'<li> <span>Interest Percentage</span> <span>'+arrayActiveLoanRecap.LOANINTERESTPERANNUM+'% Per Annum</span> </li>'+
																		'</ul>'+
																	'</div>'+
																'</div>'+
																'<div class="col-lg-4 col-md-12 mb-10 border-right">'+
																	'<h6 class="mb-0">&nbsp;</h6>'+
																	'<div class="order-details-customer-info">'+
																		'<ul>'+
																			'<li> <span>Principal Nominal</span> <span>'+numberFormat(arrayActiveLoanRecap.LOANNOMINALPRINCIPAL)+'</span> </li>'+
																			'<li> <span>Interest Nominal</span> <span>'+numberFormat(arrayActiveLoanRecap.LOANNOMINALINTEREST)+'</span> </li>'+
																			'<li> <span>Total Nominal</span> <span>'+numberFormat(arrayActiveLoanRecap.LOANNOMINALTOTAL)+'</span> </li>'+
																		'</ul>'+
																	'</div>'+
																'</div>'+
																'<div class="col-lg-4 col-md-12 mb-10">'+
																	'<h6 class="mb-0">&nbsp;</h6>'+
																	'<div class="order-details-customer-info">'+
																		'<ul>'+
																			'<li> <span>Monthly Installment</span> <span>'+numberFormat(arrayActiveLoanRecap.LOANINSTALLMENTPERMONTH)+'</span> </li>'+
																			'<li> <span>Last Installment</span> <span>'+arrayActiveLoanRecap.LOANINSTALLMENTLASTPERIOD+'</span> </li>'+
																			'<li> <span>Current Saldo</span> <span>'+numberFormat(arrayActiveLoanRecap.LOANNOMINALSALDO)+'</span> </li>'+
																		'</ul>'+
																	'</div>'+
																'</div>'+
																'<div class="col-12 pt-10 mb-10 mt-10 border-top">'+
																	'<h6 class="mb-0">Installment History</h6>'+
																'</div>'+
																'<div class="col-12 responsive-table-container">'+
																	'<table class="table" id="table-reservation">'+
																		'<thead class="thead-light">'+
																			'<tr>'+
																				'<th width="120">Installment Date</th>'+
																				'<th width="120">Period</th>'+
																				'<th>Description</th>'+
																				'<th width="160" class="text-right">Nominal Installment</th>'+
																				'<th width="160" class="text-right">Nominal Saldo</th>'+
																				'<th width="120">User Input</th>'+
																				'<th width="150">Date Time Input</th>'+
																			'</tr>'+
																		'</thead>'+
																		'<tbody>'+rowsInstallmentHistory+'<tbody>'+
																	'</table>'+
																'</div>'+
															'</div>'+
														'</div>';
					});
					$("#detailPerDriverTab-activeLoan-containerDetailPerActiveLoan").html(elemActiveLoanRecap);
				}
			}
			$tableBody.html(rows);
		}
	});
}

$('#modal-addInstallmentRecord').off('shown.bs.modal');
$('#modal-addInstallmentRecord').on('shown.bs.modal', function (e) {
	var idDriver	=	$("#optionDetailDriver").val();
	$("#optionDriverInstallment").val(idDriver);
	$("#dateRecordInstallment").val(dateToday);
	$("#loanInstallmentNominal").val(0);
	$("#loanInstallmentDescription").val("");
	$("#imageTransferReceiptInstallment").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "200px");
	createUploaderTransferReceiptInstallment(idDriver);
});

function createUploaderTransferReceiptInstallment(idDriver){
	
	idDriver	=	idDriver == "" ? 0 : idDriver;
	$('.ajax-file-upload-container').remove();
	$("#uploaderTransferReceiptInstallment").uploadFile({
		url: baseURL+"financeDriver/loanPrepaidCapital/uploadTransferReceiptInstallment/"+idDriver,
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
				$("#imageTransferReceiptInstallment").removeAttr('src').attr("src", data.urlTransferReceipt);
				$("#transferReceiptInstallmentFileName").val(data.transferReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#editor-addInstallmentRecord').off('submit');
$('#editor-addInstallmentRecord').on('submit', function(e) {
	e.preventDefault();
	var dataForm	=	$("#editor-addInstallmentRecord :input").serializeArray(),
		idDriver	=	$("#optionDriverInstallment option:selected").val(),
		driverName	=	$("#optionDriverInstallment option:selected").text(),
		loanTypeStr	=	$("#optionLoanType option:selected").text(),
		dataSend	=	{idDriver:idDriver, driverName:driverName, loanTypeStr:loanTypeStr};
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/saveNewInstallmentRecord",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#editor-addInstallmentRecord :input").attr("disabled", true);
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			$("#editor-addInstallmentRecord :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);			
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-addInstallmentRecord').modal('hide');
				getDetailLoanPerDriver();
			}
		}
	});
});

$('#optionInstallmentDriverType, #optionInstallmentDriver, #startDateRequest, #endDateRequest').off('change');
$('#optionInstallmentDriverType, #optionInstallmentDriver, #startDateRequest, #endDateRequest').on('change', function(e) {
	getLoanInstallmentRequest();
});
	
$('#checkboxViewInstallmentRequestOnly').off('click');
$("#checkboxViewInstallmentRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewInstallmentRequestOnly").is(':checked');
	
	if(checked){
		$("#optionInstallmentDriverType, #optionInstallmentDriver, #startDateRequest, #endDateRequest").attr("disabled", true);
	} else {
		$("#optionInstallmentDriverType, #optionInstallmentDriver, #startDateRequest, #endDateRequest").attr("disabled", false);
	}
	
	getLoanInstallmentRequest();
});

function getLoanInstallmentRequest(){
	
	var $tableBody		=	$('#table-installmentRequest > tbody'),
		columnNumber	=	$('#table-installmentRequest > thead > tr > th').length,
		idDriverType	=	$('#optionInstallmentDriverType').val(),
		idDriver		=	$('#optionInstallmentDriver').val(),
		startDate		=	$('#startDateRequest').val(),
		endDate			=	$('#endDateRequest').val(),
		viewRequestOnly	=	$("#checkboxViewInstallmentRequestOnly").is(':checked'),
		dataSend		=	{
								idDriverType:idDriverType,
								idDriver:idDriver,
								startDate:startDate,
								endDate:endDate,
								viewRequestOnly:viewRequestOnly
							};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/getDataLoanInstallmentRequest",
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
			
			var data			=	response.dataTable,
				rows			=	"";
				
			if(response.status != 200){
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td>"+
							"</tr>";
			} else {
				
				$.each(data, function(index, array) {
					
					var badgeStatus	=	"";
					switch(array.STATUS){
						case "1"	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>';
										break;
						case "-1"	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>';
										break;
						case "0"	:
						default		:
										badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>';
										break;
					}

					var btnShowDetails	=	'<button class="button button-box button-primary button-xs" '+
													'onclick="showDetailLoanInstallmentRequest('+array.IDLOANDRIVERINSTALLMENTREQUEST+', '+array.STATUS+')">'+
												'<i class="fa fa-info"></i>'+
											'</button>';
					rows				+=	"<tr>"+
												"<td>"+array.DATETIMEINPUT+"</td>"+
												"<td>["+array.DRIVERTYPE+"] "+array.DRIVERNAME+"</td>"+
												"<td>"+array.LOANTYPE+"</td>"+
												"<td>"+array.NOTES+"</td>"+
												"<td>"+numberFormat(array.AMOUNT)+"</td>"+
												"<td>"+badgeStatus+"</td>"+
												"<td align='center'>"+btnShowDetails+"</td>"+
											"</tr>";
				});
				
			}
			$tableBody.html(rows);
		}
	});
	
}

function showDetailLoanInstallmentRequest(idLoanInstallmentRequest, status){
	
	var dataSend		=	{idLoanInstallmentRequest:idLoanInstallmentRequest};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/loanPrepaidCapital/getDetailLoanInstallmentRequest",
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
				var detailInstallmentRequest	=	response.detailInstallmentRequest;
				var badgeStatus	=	"";
				switch(status){
					case 1		:	
					case "1"	:	
									badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>';
									break;
					case -1		:	
					case "-1"	:	
									badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>';
									break;
					case 0		:
					case "0"	:
					default		:
									badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>';
									break;
				}

				$("#loanInstallmentRequest-driverName").html(detailInstallmentRequest.DRIVERNAME);
				$("#loanInstallmentRequest-loanType").html(detailInstallmentRequest.LOANTYPE);
				$("#loanInstallmentRequest-notes").html(detailInstallmentRequest.NOTES);
				$("#loanInstallmentRequest-amount").html(numberFormat(detailInstallmentRequest.AMOUNT));
				$("#loanInstallmentRequest-dateTimeRequest").html(detailInstallmentRequest.DATETIMEINPUT);
				$("#loanInstallmentRequest-dateTimeApproval").html(detailInstallmentRequest.DATETIMEAPPROVE);
				$("#loanInstallmentRequest-userApproval").html(detailInstallmentRequest.USERAPPROVE);
				$("#loanInstallmentRequest-transferReceipt").attr("src", detailInstallmentRequest.FILETRANSFERRECEIPT);
				$("#loanInstallmentRequest-zoomTransferReceipt").attr("href", detailInstallmentRequest.FILETRANSFERRECEIPT);
				$("#badgeInstallmentRequestStatus").html(badgeStatus);
				$("#idLoanInstallmentRequest").val(idLoanInstallmentRequest);
				
				$('#editor-loanInstallmentRequest').off('submit');
				$('#btnRejectLoanInstallmentRequest').off('click');

				if(status == 0){
					$("#btnAcceptLoanInstallmentRequest, #btnRejectLoanInstallmentRequest").removeClass('d-none');
					$('#editor-loanInstallmentRequest').on('submit', function(e) {
						e.preventDefault();
						var idLoanInstallmentRequest	=	$("#idLoanInstallmentRequest").val();
						confirmApproveRejectLoanInstallmentRequest(idLoanInstallmentRequest, 1)
					});

					$('#btnRejectLoanInstallmentRequest').on('click', function(e) {
						e.preventDefault();
						var idLoanInstallmentRequest	=	$("#idLoanInstallmentRequest").val();
						confirmApproveRejectLoanInstallmentRequest(idLoanInstallmentRequest, -1)
					});
				} else {
					$("#btnAcceptLoanInstallmentRequest, #btnRejectLoanInstallmentRequest").addClass('d-none');
				}
				
				$('#modal-loanInstallmentRequest').modal('show');
			} else {
				$("#idLoanInstallmentRequest").val(0);
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
	
}

function confirmApproveRejectLoanInstallmentRequest(idLoanInstallmentRequest, status){
	
	var strStatus	=	status == 1 ? "Approve" : "Reject",
		functionUrl	=	"approveRejectInstallmentRequest",
		confirmText	=	'Are you sure you want to <b>'+strStatus+'</b> this installment request?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idLoanInstallmentRequest', idLoanInstallmentRequest).attr('data-status', status).attr('data-function', functionUrl);
	$confirmDialog.modal('show');
	
}

loanPrepaidCapitalFunc();