var urlImageBankLogo	=	$("#urlImageBankLogo").val(),
	$confirmDialog		=	$('#modal-confirm-action');
	
if (recapPerDriverFunc == null){
	var recapPerDriverFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionDriverType', 'dataDriverType');
			setOptionHelper('optionDriver', 'dataDriverNewFinance');
			$('#optionDriverType').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionDriver', 'dataDriverNewFinance', false, false, this.value);
				} else {
					setOptionHelper('optionDriver', 'dataDriverNewFinance');
				}
				getDataAllDriverRecap();
			});

			setOptionHelper('optionDriverTypeRecap', 'dataDriverType');
			setOptionHelper('optionDriverRecap', 'dataDriverNewFinance', false, false, $('#optionDriverTypeRecap').val());
			$('#optionDriverTypeRecap').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionDriverRecap', 'dataDriverNewFinance', false, function(){
						getDataPerDriverRecap()
					}, this.value);
				} else {
					setOptionHelper('optionDriverRecap', 'dataDriverNewFinance', false, function(){
						getDataPerDriverRecap()
					});
				}
			});

			$('#optionDriverRecap').change(function() { 
				getDataPerDriverRecap();
			});
			setOptionHelper('optionDriverWithdrawal', 'dataDriver');
			$("#optionDriverWithdrawal").select2();

			getDataAllDriverRecap();
			getDataFeePerPeriod();
			getDataPerDriverRecap();
			getDataWithdrawalRequest();
		});	
	}
}

$('#optionDriver').off('change');
$('#optionDriver').on('change', function(e) {
	getDataAllDriverRecap();
});

$('#startDate, #endDate').off('change');
$('#startDate, #endDate').on('change', function(e) {
	getDataFeePerPeriod();
});

function generateDataTable(page){
	getDataAllDriverRecap(page);
}

function generateDataTableFeePerPeriod(page){
	getDataFeePerPeriod(page);
}

function getDataAllDriverRecap(page = 1){
	
	var $tableBody		=	$('#table-recapPerDriver > tbody'),
		columnNumber	=	$('#table-recapPerDriver > thead > tr > th').length,
		idDriverType	=	$('#optionDriverType').val(),
		idDriver		=	$('#optionDriver').val(),
		dataSend		=	{page:page, idDriverType:idDriverType, idDriver:idDriver};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/recapPerDriver/getDataAllDriverRecap",
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
				$('#excelAllDriverRecap').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$('#excelAllDriverRecap').removeClass('d-none').on("click").attr("href", response.urlexcelAllDriverRecap);
				$.each(data, function(index, array) {
					
					var totalBalance=	(array.TOTALADDITIONALCOST * 1) + (array.TOTALFEE * 1) + (array.TOTALREIMBURSEMENT * 1) + (array.TOTALREVIEWBONUSPUNISHMENT * 1) - (array.TOTALCOLLECTPAYMENT * 1) - (array.TOTALPREPAIDCAPITAL * 1);
					rows			+=	"<tr>"+
											"<td>"+array.DRIVERTYPE+"</td>"+
											"<td>"+array.DRIVERNAME+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALSCHEDULE)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALFEE)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALADDITIONALCOST)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALREIMBURSEMENT)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALREVIEWBONUSPUNISHMENT)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALCOLLECTPAYMENT)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALPREPAIDCAPITAL)+"</td>"+
											"<td align='right'>"+numberFormat(totalBalance)+"</td>"+
										"</tr>";
							
				});
				
			}

			generatePagination("tablePaginationRecapPerDriver", page, response.result.pageTotal);
			generateDataInfo("tableDataCountRecapPerDriver", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

function getDataFeePerPeriod(page = 1){
	
	var $tableBody		=	$('#table-feePerPeriod > tbody'),
		columnNumber	=	$('#table-feePerPeriod > thead > tr > th').length,
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		dataSend		=	{page:page, startDate:startDate, endDate:endDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/recapPerDriver/getDataFeePerPeriod",
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
				$('#excelFeePerPeriod').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$('#excelFeePerPeriod').removeClass('d-none').on("click").attr("href", response.urlExcelFeePerPeriod);
				$.each(data, function(index, array) {
					rows			+=	"<tr>"+
											"<td>"+array.DRIVERTYPE+"</td>"+
											"<td>"+array.DRIVERNAME+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALSCHEDULE)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALFEE)+"</td>"+
										"</tr>";							
				});
				
			}

			generatePagination("tablePaginationFeePerPeriod", page, response.result.pageTotal, "generateDataTableFeePerPeriod");
			generateDataInfo("tableDataCountFeePerPeriod", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

function getDataPerDriverRecap(){
	
	var idDriver	=	$("#optionDriverRecap").val(),
		dataSend	=	{idDriver:idDriver};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/recapPerDriver/getDataPerDriverRecap",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#driverDetailInitial, #driverDetailName, #driverDetailAddress, #driverDetailPhoneEmail, #driverDetailDriverType, #driverDetailCarTypeCapacity").html("-");
			$("#editorManualWithdraw-driverName, #editorManualWithdraw-driverAddress, #editorManualWithdraw-driverPhoneEmail, #editorManualWithdraw-driverType, #editorManualWithdraw-carTypeCapacity").html("-");
			$("#driverBankName, #driverBankAccountNumber, #driverBankAccountHolder").html("-");
			$("#editorManualWithdraw-bankName, #editorManualWithdraw-bankAccountNumber, #editorManualWithdraw-bankAccountHolder").html("-");
			$("#driverBankLogo, #editorManualWithdraw-bankLogo").attr("src", urlImageBankLogo+"default.png");
			$("#btnManualWithdraw").addClass('d-none').off('change');
			$("#totalNominalFee, #totalNominalCollectPayment, #totalNominalPrepaidCapital, #totalNominalLoan").html(0);
			$("#totalSchedule, #totalCollectPayment, #totalNominalLoanCar, #totalNominalLoanPersonal").html(0);
			$("#editorManualWithdraw-saldoLoanCar, #editorManualWithdraw-saldoLoanPersonal, #editorManualWithdraw-totalFee, #editorManualWithdraw-additionalCost").html(0);
			$("#editorManualWithdraw-collectPayment, #editorManualWithdraw-prepaidCapital, #editorManualWithdraw-grandTotal").html(0);
			$("#editorManualWithdraw-loanCar, #editorManualWithdraw-loanPersonal").val(0);
			$("#editorManualWithdraw-notes").val("");
			$("#lastTransactionPrepaidCapital").html("-");
			$("#table-listFee > tbody, #table-listReimbursement > tbody, #table-historyPrepaidCapital > tbody").html('<tr><td colspan="5" align="center">No data</td></tr>');
			$("#table-listFee > tbody, #table-historyPrepaidCapital > tbody").html('<tr><td colspan="5" align="center">No data</td></tr>');
			$("#table-listCollectPayment > tbody, #table-historyLoan > tbody").html('<tr><td colspan="6" align="center">No data</td></tr>');
			$("#table-listAdditionalCost > tbody").html('<tr><td colspan="7" align="center">No data</td></tr>');
			$("#table-listReviewBonusPunishment > tbody").html('<tr><td colspan="9" align="center">No data</td></tr>');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailDriver					=	response.detailDriver,
					dataBankAccount					=	response.dataBankAccount,
					dataRecapPerDriver				=	response.dataRecapPerDriver,
					dataListFee						=	response.dataListFee,
					dataListAdditionalCost			=	response.dataListAdditionalCost,
					dataListReimbursement			=	response.dataListReimbursement,
					dataListReviewBonusPunishment	=	response.dataListReviewBonusPunishment,
					dataListCollectPayment			=	response.dataListCollectPayment,
					dataLoanHistory					=	response.dataLoanHistory,
					dataPrepaidCapitalHistory		=	response.dataPrepaidCapitalHistory,
					rowListFee						=	rowListAdditionalCost	=	rowListReimbursement	=	rowListReviewBonusPunishment	=	rowListCollectPayment	=	rowHistoryLoan	=	rowHistoryPrepaidCapital	=	"",
					totalFeeVal						=	parseInt(dataRecapPerDriver.TOTALFEE),
					totalAdditioalCostVal			=	parseInt(dataRecapPerDriver.TOTALADDITIONALCOST),
					totalReimbursementVal			=	parseInt(dataRecapPerDriver.TOTALREIMBURSEMENT),
					totalReviewBonusPunishmentVal	=	parseInt(dataRecapPerDriver.TOTALREVIEWBONUSPUNISHMENT),
					totalCollectPaymentVal			=	parseInt(dataRecapPerDriver.TOTALCOLLECTPAYMENT),
					totalPrepaidCapitalVal			=	parseInt(dataRecapPerDriver.TOTALPREPAIDCAPITAL),
					withdrawalBalance				=	totalFeeVal + totalAdditioalCostVal + totalReimbursementVal + totalReviewBonusPunishmentVal - totalCollectPaymentVal - totalPrepaidCapitalVal;

				$("#driverDetailInitial").html(detailDriver.INITIALNAME);
				$("#driverDetailName").html(detailDriver.NAME);
				$("#driverDetailAddress").html(detailDriver.ADDRESS);
				$("#driverDetailPhoneEmail").html(detailDriver.PHONE+" | "+detailDriver.EMAIL);
				$("#driverDetailDriverType").html(detailDriver.DRIVERTYPE);
				$("#driverDetailCarTypeCapacity").html(detailDriver.CARCAPACITYNAME+" ("+detailDriver.CARCAPACITYDETAIL+")");
				$("#driverBankName").html(dataBankAccount.BANKNAME);
				$("#driverBankAccountNumber").html(dataBankAccount.ACCOUNTNUMBER);
				$("#driverBankAccountHolder").html(dataBankAccount.ACCOUNTHOLDERNAME);
				$("#driverBankLogo").attr("src", dataBankAccount.BANKLOGO);
				
				if(withdrawalBalance > 0 && dataBankAccount.ACCOUNTNUMBER != '-' && levelUser == '1'){
					$("#btnManualWithdraw").removeClass('d-none');
					$('#btnManualWithdraw').on('click', function(e) {
						$("#editorManualWithdraw-driverName").html(detailDriver.NAME);
						$("#editorManualWithdraw-driverAddress").html(detailDriver.ADDRESS);
						$("#editorManualWithdraw-driverPhoneEmail").html(detailDriver.PHONE+" | "+detailDriver.EMAIL);
						$("#editorManualWithdraw-driverType").html(detailDriver.DRIVERTYPE);
						$("#editorManualWithdraw-carTypeCapacity").html(detailDriver.CARCAPACITYNAME+" ("+detailDriver.CARCAPACITYDETAIL+")");

						$("#editorManualWithdraw-bankName").html(dataBankAccount.BANKNAME);
						$("#editorManualWithdraw-bankAccountNumber").html(dataBankAccount.ACCOUNTNUMBER);
						$("#editorManualWithdraw-bankAccountHolder").html(dataBankAccount.ACCOUNTHOLDERNAME);
						$("#editorManualWithdraw-bankLogo").attr("src", dataBankAccount.BANKLOGO);
						
						$("#editorManualWithdraw-saldoLoanCar").html(numberFormat(dataRecapPerDriver.TOTALLOANCAR));
						$("#editorManualWithdraw-saldoLoanPersonal").html(numberFormat(dataRecapPerDriver.TOTALLOANPERSONAL));
						$("#editorManualWithdraw-totalFee").html(numberFormat(totalFeeVal));
						$("#editorManualWithdraw-additionalCost").html(numberFormat(totalAdditioalCostVal));
						$("#editorManualWithdraw-reimbursement").html(numberFormat(totalReimbursementVal));
						$("#editorManualWithdraw-reviewBonusPunishment").html(numberFormat(totalReviewBonusPunishmentVal));
						$("#editorManualWithdraw-collectPayment").html(numberFormat(totalCollectPaymentVal));
						$("#editorManualWithdraw-prepaidCapital").html(numberFormat(totalPrepaidCapitalVal));
						$("#editorManualWithdraw-grandTotal").html(numberFormat(withdrawalBalance));
						$("#editorManualWithdraw-idDriver").val(idDriver);
						
						if(dataRecapPerDriver.TOTALLOANCAR > 0){
							$("#editorManualWithdraw-loanCar").prop('disabled', false)
						} else {
							$("#editorManualWithdraw-loanCar").prop('disabled', true)
						}
						
						if(dataRecapPerDriver.TOTALLOANPERSONAL > 0){
							$("#editorManualWithdraw-loanPersonal").prop('disabled', false)
						} else {
							$("#editorManualWithdraw-loanPersonal").prop('disabled', true)
						}
						
						$('#modal-editorManualWithdraw').modal('show');
					});
				}

				$("#totalNominalFee").html(numberFormat(totalFeeVal));
				$("#totalNominalAdditionalCost").html(numberFormat(totalAdditioalCostVal));
				$("#totalNominalReimbursement").html(numberFormat(totalReimbursementVal));
				$("#totalNominalReviewBonusPunishment").html(numberFormat(totalReviewBonusPunishmentVal));
				$("#totalNominalCollectPayment").html(numberFormat(totalCollectPaymentVal));
				$("#totalNominalPrepaidCapital").html(numberFormat(totalPrepaidCapitalVal));
				$("#totalWithdrawBalance").html(numberFormat(withdrawalBalance));
				$("#totalNominalLoan").html(numberFormat(dataRecapPerDriver.TOTALLOAN));
				$("#totalSchedule").html(numberFormat(dataRecapPerDriver.TOTALSCHEDULE));
				$("#totalScheduleWithAdditioalCost").html(numberFormat(dataRecapPerDriver.TOTALSCHEDULEWITHCOST));
				$("#totalDataReimbursement").html(numberFormat(dataRecapPerDriver.TOTALDATAREIMBURSEMENT));
				$("#totalReviewBonusPeriod").html(numberFormat(dataRecapPerDriver.TOTALREVIEWBONUSPERIOD));
				$("#totalCollectPayment").html(numberFormat(dataRecapPerDriver.TOTALSCHEDULEWITHCOLLECTPAYMENT));
				$("#totalNominalLoanCar").html(numberFormat(dataRecapPerDriver.TOTALLOANCAR));
				$("#totalNominalLoanPersonal").html(numberFormat(dataRecapPerDriver.TOTALLOANPERSONAL));
				$("#lastWitdrawalDate").html(dataRecapPerDriver.LASTWITHDRAWALDATE);
				$("#lastTransactionPrepaidCapital").html(dataRecapPerDriver.MAXDATEPREPAIDCAPITAL);
				
				if(dataListFee != false){
					$.each(dataListFee, function(indexListFee, arrayListFee) {
						
						var inputType	=	'';
						switch(arrayListFee.INPUTTYPE){
							case "1"	:	inputType	=	'Mailbox'; break;
							case "2"	:	inputType	=	'Manual'; break;
						}
						
						rowListFee	+=	'<tr>'+
											'<td><b class="text-primary">'+arrayListFee.SCHEDULEDATE+'<br/>'+arrayListFee.RESERVATIONTIMESTART+'</b></td>'+
											'<td>['+inputType+'] '+arrayListFee.SOURCENAME+'<br>Book Code : '+arrayListFee.BOOKINGCODE+'</td>'+
											'<td>'+arrayListFee.CUSTOMERNAME+'</td>'+
											'<td><b>'+arrayListFee.RESERVATIONTITLE+'</b><br/>'+arrayListFee.PRODUCTNAME+'</td>'+
											'<td align="right"><b>'+numberFormat(arrayListFee.NOMINAL)+'</b></td>'+
										'</tr>';
							
					});
					$("#table-listFee > tbody").html(rowListFee);
				}
				
				if(dataListAdditionalCost != false){
					$.each(dataListAdditionalCost, function(indexListAdditionalCost, arrayListAdditionalCost) {
						
						var inputType	=	'';
						switch(arrayListAdditionalCost.INPUTTYPE){
							case "1"	:	inputType	=	'Mailbox'; break;
							case "2"	:	inputType	=	'Manual'; break;
						}
						
						rowListAdditionalCost	+=	'<tr>'+
														'<td>'+arrayListAdditionalCost.DATETIMEINPUTSTR+'</td>'+
														'<td>['+inputType+'] '+arrayListAdditionalCost.SOURCENAME+'<br>Book Code : '+arrayListAdditionalCost.BOOKINGCODE+'</td>'+
														'<td>'+arrayListAdditionalCost.CUSTOMERNAME+'</td>'+
														'<td><b>'+arrayListAdditionalCost.RESERVATIONTITLE+'</b><br/>'+arrayListAdditionalCost.PRODUCTNAME+'</td>'+
														'<td>'+arrayListAdditionalCost.ADDITIONALCOSTTYPE+'</td>'+
														'<td>'+arrayListAdditionalCost.DESCRIPTION+'</td>'+
														'<td align="right"><b>'+numberFormat(arrayListAdditionalCost.NOMINAL)+'</b></td>'+
													'</tr>';
							
					});
					$("#table-listAdditionalCost > tbody").html(rowListAdditionalCost);
				}
				
				if(dataListReimbursement != false){
					$.each(dataListReimbursement, function(indexListReimbursement, arrayListReimbursement) {
						rowListReimbursement	+=	'<tr>'+
														'<td>'+arrayListReimbursement.DATERECEIPT+'</td>'+
														'<td>'+arrayListReimbursement.REQUESTBYTYPE+'</td>'+
														'<td>'+arrayListReimbursement.DESCRIPTION+'</td>'+
														'<td>'+arrayListReimbursement.NOTES+'</td>'+
														'<td align="right"><b>'+numberFormat(arrayListReimbursement.NOMINAL)+'</b></td>'+
													'</tr>';
							
					});
					$("#table-listReimbursement > tbody").html(rowListReimbursement);
				}
				
				if(dataListReviewBonusPunishment != false){
					$.each(dataListReviewBonusPunishment, function(indexListReviewBonusPunishment, arrayListReviewBonusPunishment) {
						rowListReviewBonusPunishment+=	'<tr>'+
															'<td>'+arrayListReviewBonusPunishment.PERIODNAME+'</td>'+
															'<td>'+arrayListReviewBonusPunishment.PERIODDATESTART+'</td>'+
															'<td>'+arrayListReviewBonusPunishment.PERIODDATEEND+'</td>'+
															'<td align="right">'+numberFormat(arrayListReviewBonusPunishment.TOTALTARGET)+'</td>'+
															'<td align="right">'+numberFormat(arrayListReviewBonusPunishment.TOTALREVIEWPOINT)+'</td>'+
															'<td align="right">'+numberFormat(arrayListReviewBonusPunishment.BONUSRATE)+'</td>'+
															'<td align="right">'+numberFormat(arrayListReviewBonusPunishment.NOMINALBONUS)+'</td>'+
															'<td align="right">'+numberFormat(arrayListReviewBonusPunishment.NOMINALPUNISHMENT)+'</td>'+
															'<td align="right"><b>'+numberFormat(arrayListReviewBonusPunishment.NOMINALRESULT)+'</b></td>'+
														'</tr>';
							
					});
					$("#table-listReviewBonusPunishment > tbody").html(rowListReviewBonusPunishment);
				}
				
				if(dataListCollectPayment != false){
					$.each(dataListCollectPayment, function(indexListCollectPayment, arrayListCollectPayment) {
						
						var inputType	=	'';
						switch(arrayListCollectPayment.INPUTTYPE){
							case "1"	:	inputType	=	'Mailbox'; break;
							case "2"	:	inputType	=	'Manual'; break;
						}
						
						rowListCollectPayment	+=	'<tr>'+
														'<td><b class="text-primary">'+arrayListCollectPayment.DATECOLLECT+'</b></td>'+
														'<td>'+
															'['+inputType+'] '+arrayListCollectPayment.SOURCENAME+' | '+arrayListCollectPayment.BOOKINGCODE+'<br/><br/>'+
															'<b>'+arrayListCollectPayment.CUSTOMERNAME+'</b><br/>'+
															'<b>'+arrayListCollectPayment.RESERVATIONTITLE+'</b>'+
														'</td>'+
														'<td>'+arrayListCollectPayment.REMARK+'</td>'+
														'<td>'+arrayListCollectPayment.DESCRIPTION+'</td>'+
														'<td align="right"><b>'+numberFormat(arrayListCollectPayment.AMOUNT)+' '+arrayListCollectPayment.AMOUNTCURRENCY+'</b></td>'+
														'<td align="right"><b>'+numberFormat(arrayListCollectPayment.AMOUNTIDR)+'</b></td>'+
													'</tr>';
							
					});
					$("#table-listCollectPayment > tbody").html(rowListCollectPayment);
				}
				
				if(dataPrepaidCapitalHistory != false){
					$.each(dataPrepaidCapitalHistory, function(indexPrepaidCapitalHistory, arrayPrepaidCapitalHistory) {
						
						rowHistoryPrepaidCapital	+=	'<tr>'+
															'<td><b class="text-primary">'+arrayPrepaidCapitalHistory.DATETIMEINPUT+'</b></td>'+
															'<td>'+arrayPrepaidCapitalHistory.DESCRIPTION+'</td>'+
															'<td>'+arrayPrepaidCapitalHistory.TYPE+'</td>'+
															'<td align="right"><b>'+numberFormat(arrayPrepaidCapitalHistory.AMOUNT)+'</b></td>'+
															'<td align="right"><b>'+numberFormat(arrayPrepaidCapitalHistory.SALDO)+'</b></td>'+
														'</tr>';
							
					});
					$("#table-historyPrepaidCapital > tbody").html(rowHistoryPrepaidCapital);
				}
				
				if(dataLoanHistory != false){
					$.each(dataLoanHistory, function(indexLoanHistory, arrayLoanHistory) {
						
						rowHistoryLoan	+=	'<tr>'+
												'<td><b class="text-primary">'+arrayLoanHistory.DATETIMEINPUT+'</b></td>'+
												'<td>'+arrayLoanHistory.LOANTYPE+'</td>'+
												'<td>'+arrayLoanHistory.DESCRIPTION+'</td>'+
												'<td>'+arrayLoanHistory.TYPE+'</td>'+
												'<td align="right"><b>'+numberFormat(arrayLoanHistory.AMOUNT)+'</b></td>'+
												'<td align="right"><b>'+numberFormat(arrayLoanHistory.SALDO)+'</b></td>'+
											'</tr>';
							
					});
					$("#table-historyLoan > tbody").html(rowHistoryLoan);
				}
				
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
	
}

function calculateManualWithdrawGrandTotal(){
	var totalFee				=	$("#editorManualWithdraw-totalFee").html().replace(/[^0-9\.]+/g, '') * 1,
		additionalCost			=	$("#editorManualWithdraw-additionalCost").html().replace(/[^0-9\.]+/g, '') * 1,
		reimbursement			=	$("#editorManualWithdraw-reimbursement").html().replace(/[^0-9\.]+/g, '') * 1,
		reviewBonusPunishment	=	$("#editorManualWithdraw-reviewBonusPunishment").html().replace(/[^0-9\.\-]+/g, '') * 1,
		collectPayment			=	$("#editorManualWithdraw-collectPayment").html().replace(/[^0-9\.]+/g, '') * 1,
		prepaidCapital			=	$("#editorManualWithdraw-prepaidCapital").html().replace(/[^0-9\.]+/g, '') * 1,
		additionalIncome		=	$("#editorManualWithdraw-additionalIncome").val().replace(/[^0-9\.]+/g, '') * 1,
		loanCar					=	$("#editorManualWithdraw-loanCar").val().replace(/[^0-9\.]+/g, '') * 1,
		loanPersonal			=	$("#editorManualWithdraw-loanPersonal").val().replace(/[^0-9\.]+/g, '') * 1,
		charity					=	$("#editorManualWithdraw-charity").val().replace(/[^0-9\.]+/g, '') * 1,
		grandTotal				=	totalFee + additionalCost + reimbursement + reviewBonusPunishment - collectPayment - prepaidCapital - additionalIncome - loanCar - loanPersonal - charity;
	$("#editorManualWithdraw-grandTotal").html(numberFormat(grandTotal));
}

$('#container-editorManualWithdraw').off('submit');
$("#container-editorManualWithdraw").on('submit',function(e) {
	e.preventDefault();
	var functionUrl				=	"submitManualWithdrawal",
		driverName				=	$("#editorManualWithdraw-driverName").html(),
		grandTotalWD			=	$("#editorManualWithdraw-grandTotal").html(),
		additionalIncomeNominal	=	$("#editorManualWithdraw-additionalIncome").val().replace(/[^0-9\.]+/g, '') * 1,
		loanCarSettlement		=	$("#editorManualWithdraw-loanCar").val().replace(/[^0-9\.]+/g, '') * 1,
		loanPersonalSettlement	=	$("#editorManualWithdraw-loanPersonal").val().replace(/[^0-9\.]+/g, '') * 1,
		charityNominal			=	$("#editorManualWithdraw-charity").val().replace(/[^0-9\.]+/g, '') * 1,
		idDriver				=	$("#editorManualWithdraw-idDriver").val(),
		withdrawalNotes			=	$('#editorManualWithdraw-notes').val(),
		withdrawalNotesCheck	=	withdrawalNotes.replaceAll(' ', ''),
		withdrawalNotesCheckLen	=	withdrawalNotesCheck.length,
		confirmText				=	'Are you sure you want to submit Manual Withdrawal for driver <b>'+driverName+'</b> in the amount of <b>Rp.'+grandTotalWD+'</b>?';
	
	if(withdrawalNotesCheckLen <= 8){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please enter a valid withdrawal note!");
		});
		$('#modalWarning').modal('show');
	} else if(charityNominal < 1000){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please enter the nominal charity");
		});
		$('#modalWarning').modal('show');
	} else {
		$confirmDialog.find('#modal-confirm-body').html(confirmText);
		$confirmDialog.find('#confirmBtn').attr('data-additionalIncome', additionalIncomeNominal).attr('data-loanCar', loanCarSettlement).attr('data-loanPersonal', loanPersonalSettlement).attr('data-charity', charityNominal).attr('data-idDriver', idDriver).attr('data-function', functionUrl);
		$confirmDialog.modal('show');
	}
});

$('#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionDriverWithdrawal, #optionStatusWithdrawal').off('change');
$('#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionDriverWithdrawal, #optionStatusWithdrawal').on('change', function(e) {
	getDataWithdrawalRequest();
});
	
$('#checkboxViewWithdrawalRequestOnly').off('click');
$("#checkboxViewWithdrawalRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewWithdrawalRequestOnly").is(':checked');
	
	if(checked){
		$("#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionDriverWithdrawal, #optionStatusWithdrawal").attr("disabled", true);
	} else {
		$("#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionDriverWithdrawal, #optionStatusWithdrawal").attr("disabled", false);
	}
	
	getDataWithdrawalRequest();
});

function getDataWithdrawalRequest(){
	
	var startDate			=	$('#startDateWithdrawalRequest').val(),
		endDate				=	$('#endDateWithdrawalRequest').val(),
		idDriver			=	$("#optionDriverWithdrawal").val(),
		statusWithdrawal	=	$("#optionStatusWithdrawal").val(),
		viewRequestOnly		=	$("#checkboxViewWithdrawalRequestOnly").is(':checked'),
		dataSend			=	{startDate:startDate, endDate:endDate, idDriver:idDriver, statusWithdrawal:statusWithdrawal, viewRequestOnly:viewRequestOnly};

	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/recapPerDriver/getDataWithdrawalRequest",
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
				
				var dataWithdrawalRequest	=	response.dataWithdrawalRequest,
					rowWithdrawalRequest	=	"";

				$.each(dataWithdrawalRequest, function(index, array) {
					var badgeStatusWithdrawal	=	'';
					
					switch(array.STATUSWITHDRAWAL){
						case "-2"	:	badgeStatusWithdrawal	=	'<span class="badge badge-danger pull-right">Cancelled</span>'; break;
						case "-1"	:	badgeStatusWithdrawal	=	'<span class="badge badge-danger pull-right">Rejected</span>'; break;
						case "0"	:	badgeStatusWithdrawal	=	'<span class="badge badge-info pull-right">Requested</span>'; break;
						case "1"	:	badgeStatusWithdrawal	=	'<span class="badge badge-warning pull-right">Approved</span>'; break;
						case "2"	:	badgeStatusWithdrawal	=	'<span class="badge badge-success pull-right">Transfered</span>'; break;
						default		:	break;
					}
					
					rowWithdrawalRequest		+=	'<div class="col-sm-12 pb-1 mb-5 rounded-lg withdrawalTableElement">'+
														'<div class="row pt-10 pb-1">'+
															'<div class="col-lg-8 col-sm-12" style="border-right: 1px solid #dee2e6;">'+
																'<b>'+array.DRIVERNAME+badgeStatusWithdrawal+'</b>'+
																'<br/><small>Date Time Request : '+array.DATETIMEREQUEST+'</small>'+
																'<br/><small>Message : '+array.MESSAGE+'</small>'+
																'<div class="row py-3 mt-3" style="border-top: 1px solid #dee2e6;">'+
																	'<div class="col-12">'+
																		'<ul class="list-icon mb-20"><li class="pl-0"><i class="fa fa-calendar-check-o mr-1"></i><span>Last Date Period : '+array.DATELASTPERIOD+'</span></li></ul>'+
																		'<img src="'+array.BANKLOGO+'" style="max-height:30px; max-width:90px" class="mb-10"><br>'+
																		'<ul class="list-icon"><li class="pl-0"><i class="fa fa-university mr-1"></i><span>'+array.BANKNAME+'</span></li></ul>'+
																		'<ul class="list-icon"><li class="pl-0"><i class="fa fa-credit-card mr-1"></i><span>'+array.ACCOUNTNUMBER+'</span></li></ul>'+
																		'<ul class="list-icon"><li class="pl-0"><i class="fa fa-user-circle mr-1"></i><span>'+array.ACCOUNTHOLDERNAME+'</span></li></ul>'+
																	'</div>'+
																'</div>'+
															'</div>'+
															'<div class="col-lg-4 col-sm-12">'+
																'<div class="order-details-customer-info pb-1" style="border-bottom: 1px solid #dee2e6;">'+
																	'<ul>'+
																		'<li> <span>Fee</span> <span><b>'+numberFormat(array.TOTALFEE)+'</b></span> </li>'+
																		'<li> <span>Additional Cost</span> <span><b>'+numberFormat(array.TOTALADDITIONALCOST)+'</b></span> </li>'+
																		'<li> <span>Reimbursement</span> <span><b>'+numberFormat(array.TOTALREIMBURSEMENT)+'</b></span> </li>'+
																		'<li> <span>Review Bonus</span> <span><b>'+numberFormat(array.TOTALREVIEWBONUSPUNISHMENT)+'</b></span> </li>'+
																		'<li> <span>Collect Payment</span> <span><b>-'+numberFormat(array.TOTALCOLLECTPAYMENT)+'</b></span> </li>'+
																		'<li> <span>Additional Income</span> <span><b>-'+numberFormat(array.TOTALADDITIONALINCOME)+'</b></span> </li>'+
																		'<li> <span>Prepaid Capital</span> <span><b>-'+numberFormat(array.TOTALPREPAIDCAPITAL)+'</b></span> </li>'+
																		'<li> <span>Loan - Car</span> <span><b>-'+numberFormat(array.TOTALLOANCARINSTALLMENT)+'</b></span> </li>'+
																		'<li> <span>Loan - Personal</span> <span><b>-'+numberFormat(array.TOTALLOANPERSONALINSTALLMENT)+'</b></span> </li>'+
																		'<li> <span>Charity</span> <span><b>-'+numberFormat(array.TOTALCHARITY)+'</b></span> </li>'+
																	'</ul>'+
																'</div>'+
																'<div class="order-details-customer-info pt-1">'+
																	'<ul>'+
																		'<li> <span><b>Total Withdrawal</b></span> <span><b>'+numberFormat(array.TOTALWITHDRAWAL)+'</b></span> </li>'+
																	'</ul>'+
																'</div>'+
																'<a class="button button-primary button-xs text-light btn-block text-center" onclick="getWithdrawalDetail('+array.IDWITHDRAWALRECAP+')"><span><i class="fa fa-info"></i> More Details</span></a>'+
															'</div>'+
														'</div>'+
													'</div>';
				});
				
				$("#noDataWithdrawalRequest").addClass('d-none');
				$("#withdrawalRequestListContainer").removeClass('d-none');
				$("#withdrawalRequestList").html(rowWithdrawalRequest);
				
			} else {
				$("#noDataWithdrawalRequest").removeClass('d-none');
				$("#withdrawalRequestListContainer").addClass('d-none');
				$("#withdrawalRequestList").html("");
				$("#msgNoDataWithdrawalRequest").html(response.msg);
			}
			
		}
	});
	
}

function getWithdrawalDetail(idWithdrawalRecap, toggleSlide=true){
	
	var dataSend		=	{idWithdrawalRecap:idWithdrawalRecap};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/recapPerDriver/getDetailWithdrawalRequest",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#btnCloseDetails").removeClass("d-none");
			$('#btnRejectWithdrawal, #btnApproveWithdrawal, #btnCancelWithdrawal').addClass('d-none').off('click');
			if(toggleSlide) toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$('#window-loader').modal('hide');
			
			if(response.status != 200){
				$("#btnCloseDetails").addClass("d-none");
				if(toggleSlide) toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
			} else {
				
				var dataDetail				=	response.detailWithdrawalRequest,
					listDetailWithdrawal	=	response.listDetailWithdrawal,
					badgeStatusWithdrawal	=	'';
					
				switch(dataDetail.STATUSWITHDRAWAL){
					case "-2"	:	badgeStatusWithdrawal	=	'<span class="badge badge-danger">Cancelled</span>'; break;
					case "-1"	:	badgeStatusWithdrawal	=	'<span class="badge badge-danger">Rejected</span>'; break;
					case "0"	:	badgeStatusWithdrawal	=	'<span class="badge badge-info">Requested</span>'; break;
					case "1"	:	badgeStatusWithdrawal	=	'<span class="badge badge-warning">Approved</span>'; break;
					case "2"	:	badgeStatusWithdrawal	=	'<span class="badge badge-success">Transfered</span>'; break;
					default		:	break;
				}
				
				$("#badgeStatusWithdrawal").html(badgeStatusWithdrawal);
				$("#driverNameStr").html(dataDetail.DRIVERNAME);
				$("#requestDateTimeStr").html(dataDetail.DATETIMEREQUEST);
				$("#lastDatePeriodStr").html(dataDetail.DATELASTPERIOD);
				$("#messageStr").html(dataDetail.MESSAGE);
				$("#imgBankLogo").attr("src", dataDetail.BANKLOGO);
				$("#bankNameStr").html(dataDetail.BANKNAME);
				$("#accountNumberStr").html(dataDetail.ACCOUNTNUMBER);
				$("#accountHolderNameStr").html(dataDetail.ACCOUNTHOLDERNAME);
				
				$("#totalFeeStr").html(numberFormat(dataDetail.TOTALFEE));
				$("#totalAdditioalCostStr").html(numberFormat(dataDetail.TOTALADDITIONALCOST));
				$("#totalReimbursementStr").html(numberFormat(dataDetail.TOTALREIMBURSEMENT));
				$("#totalReviewBonusStr").html(numberFormat(dataDetail.TOTALREVIEWBONUSPUNISHMENT));
				$("#totalCollectPaymentStr").html("-"+numberFormat(dataDetail.TOTALCOLLECTPAYMENT));
				$("#totalAdditioalIncomeStr").html("-"+numberFormat(dataDetail.TOTALADDITIONALINCOME));
				$("#totalPrepaidCapitalStr").html("-"+numberFormat(dataDetail.TOTALPREPAIDCAPITAL));
				$("#totalCarInstallmentStr").html("-"+numberFormat(dataDetail.TOTALLOANCARINSTALLMENT));
				$("#totalPersonalInstallmentStr").html("-"+numberFormat(dataDetail.TOTALLOANPERSONALINSTALLMENT));
				$("#totalCharityStr").html("-"+numberFormat(dataDetail.TOTALCHARITY));
				$("#totalWithdrawalStr").html(numberFormat(dataDetail.TOTALWITHDRAWAL));

				if(dataDetail.RECEIPTFILE != ''){
					$("#transferReceiptPreview").html('<iframe id="iFrameTransferReceiptPreview" width="100%" height="250" padding="8px" src="'+dataDetail.RECEIPTFILE+'" frameborder="0"></iframe>');
				} else {
					$("#transferReceiptPreview").html('<span>Receipt is not available</span>');
				}

				$("#approvalUserStr").html(dataDetail.USERAPPROVAL);
				$("#dateTimeApprovalStr").html(dataDetail.DATETIMEAPPROVAL);
				
				if(dataDetail.STATUSWITHDRAWAL == "0"){
					$("#btnApproveWithdrawal").removeClass('d-none').on('click', function(){
						confirmApproveRejectWithdrawal(idWithdrawalRecap, 1);
					});
					$("#btnRejectWithdrawal").removeClass('d-none').on('click', function(){
						confirmApproveRejectWithdrawal(idWithdrawalRecap, -1);
					});
				} else if(dataDetail.STATUSWITHDRAWAL == "1") {
					$('#btnCancelWithdrawal').removeClass('d-none').on('click', function(){
						confirmCancelWithdrawal(idWithdrawalRecap);
					});
				}
				
				var rowListDetailWithdrawal	=	'';
				if(listDetailWithdrawal.length > 0){
					$.each(listDetailWithdrawal, function(index, array) {
						rowListDetailWithdrawal	+=	'<tr>'+
														'<td>'+array.TYPESTR+'</td>'+
														'<td>'+array.DATESTR+'</td>'+
														'<td>'+array.BOOKINGCODE+'</td>'+
														'<td>'+array.DESCRIPTION+'</td>'+
														'<td align="right">'+numberFormat(array.NOMINAL)+'</td>'+
													'</tr>';
					});
				} else {
					rowListDetailWithdrawal	=	'<td colspan="5" class="text-center">No data found</td>';
				}
				
				$("#table-dataListWithdrawalDetail > tbody").html(rowListDetailWithdrawal);
				
			}
		}
	});
}

$('#btnCloseDetails').off('click');
$('#btnCloseDetails').on('click', function(e) {
	toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
	$("#btnCloseDetails").addClass("d-none");
});

function confirmApproveRejectWithdrawal(idWithdrawalRecap, status){
	
	var strStatus	=	status == 1 ? "Approve" : "Reject",
		functionUrl	=	"approveRejectWithdrawal",
		confirmText	=	'Are you sure you want to <b>'+strStatus+'</b> this withdrawal request?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idWithdrawalRecap', idWithdrawalRecap).attr('data-status', status).attr('data-function', functionUrl);
	$confirmDialog.modal('show');
	
}

function confirmCancelWithdrawal(idWithdrawalRecap){
	
	var functionUrl	=	"cancelWithdrawal",
		confirmText	=	'Are you sure you want to <b>Cancel</b> this withdrawal request?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idWithdrawalRecap', idWithdrawalRecap).attr('data-function', functionUrl);
	$confirmDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var functionUrl	=	$confirmDialog.find('#confirmBtn').attr('data-function');

	if(functionUrl == "submitManualWithdrawal"){
		var idDriver		=	$confirmDialog.find('#confirmBtn').attr('data-idDriver'),
			additionalIncome=	$confirmDialog.find('#confirmBtn').attr('data-additionalIncome'),
			loanCar			=	$confirmDialog.find('#confirmBtn').attr('data-loanCar'),
			loanPersonal	=	$confirmDialog.find('#confirmBtn').attr('data-loanPersonal'),
			charity			=	$confirmDialog.find('#confirmBtn').attr('data-charity'),
			withdrawalNotes	=	$('#editorManualWithdraw-notes').val(),
			dataSend		=	{idDriver:idDriver, additionalIncome:additionalIncome, loanCar:loanCar, loanPersonal:loanPersonal, charity:charity, withdrawalNotes:withdrawalNotes};
	} else {
		var idWithdrawalRecap	=	$confirmDialog.find('#confirmBtn').attr('data-idWithdrawalRecap'),
			status				=	$confirmDialog.find('#confirmBtn').attr('data-status'),
			dataSend			=	{idWithdrawalRecap:idWithdrawalRecap, status:status};
	}
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/recapPerDriver/"+functionUrl,
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
				if(functionUrl == "submitManualWithdrawal"){
					getDataPerDriverRecap();
					$('#modal-editorManualWithdraw').modal('hide');
				} else {
					getWithdrawalDetail(idWithdrawalRecap, false);
				}
				getDataWithdrawalRequest();
			}
		}
	});
});

recapPerDriverFunc();