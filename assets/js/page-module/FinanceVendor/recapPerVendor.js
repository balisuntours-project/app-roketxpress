var urlImageBankLogo	=	$("#urlImageBankLogo").val(),
	$confirmDialog		=	$('#modal-confirm-action');
	
if (recapPerVendorFunc == null){
	var recapPerVendorFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionVendorTypeReport', 'dataVendorType');
			setOptionHelper('optionVendorReport', 'dataVendorNewFinance');
			$('#optionVendorTypeReport').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionVendorReport', 'dataVendorNewFinance', false, false, this.value);
				} else {
					setOptionHelper('optionVendorReport', 'dataVendorNewFinance');
				}
				getDataAllVendorReport();
			});

			setOptionHelper('optionVendorRecapTypeRecap', 'dataVendorType');
			setOptionHelper('optionVendorRecap', 'dataVendor');
			$('#optionVendorRecapTypeRecap').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionVendorRecap', 'dataVendor', false, false, this.value);
				} else {
					setOptionHelper('optionVendorRecap', 'dataVendor');
					getDataRecapPerVendor();
				}
			});

			setOptionHelper('optionVendorTypeRecap', 'dataVendorType');
			setOptionHelper('optionVendorTypePerRecap', 'dataVendorType');
			setOptionHelper('optionVendorPerRecap', 'dataVendorNewFinance', false, false, $('#optionVendorTypePerRecap').val());
			setOptionHelper('optionVendorDepositRecord', 'dataVendorNewFinance', false, false);
			$('#optionVendorTypePerRecap').change(function() { 
				if(this.value != ""){
					setOptionHelper('optionVendorPerRecap', 'dataVendorNewFinance', false, function(){
						getDataPerVendorRecap()
					}, this.value);
				} else {
					setOptionHelper('optionVendorPerRecap', 'dataVendorNewFinance', false, function(){
						getDataPerVendorRecap()
					});
				}
			});

			$('#optionVendorPerRecap').change(function() { 
				getDataPerVendorRecap();
			});
			
			setOptionHelper('optionVendorWithdrawal', 'dataVendor');
			$("#optionVendorWithdrawal").select2();
			
			setOptionHelper('manualWithdrawAddBankAccountVendor-optionBank', 'dataBank');
			getDataAllVendorReport();
			getDataRecapPerVendor();
			getDataPerVendorRecap();
			getDataWithdrawalRequest();
		});	
	}
}

$('#optionVendorReport').off('change');
$('#optionVendorReport').on('change', function(e) {
	getDataAllVendorReport();
});

$('#startDate, #endDate, #optionVendorRecapTypeRecap, #optionVendorRecap').off('change');
$('#startDate, #endDate, #optionVendorRecapTypeRecap, #optionVendorRecap').on('change', function(e) {
	getDataRecapPerVendor();
});

function generateDataTable(page){
	getDataAllVendorReport(page);
}

function generateDataTableRecapPerVendor(page){
	getDataRecapPerVendor(page);
}

function getDataAllVendorReport(page = 1){
	
	var $tableBody		=	$('#table-reportPerVendor > tbody'),
		columnNumber	=	$('#table-reportPerVendor > thead > tr > th').length,
		idVendorType	=	$('#optionVendorTypeReport').val(),
		idVendor		=	$('#optionVendorReport').val(),
		dataSend		=	{page:page, idVendorType:idVendorType, idVendor:idVendor};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/getDataAllVendorReport",
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
				$('#excelAllVendorReport').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$('#excelAllVendorReport').removeClass('d-none').on("click").attr("href", response.urlexcelAllVendorReport);
				$.each(data, function(index, array) {
					
					var totalBalance=	(array.TOTALFEE * 1) - (array.TOTALCOLLECTPAYMENT * 1);
					rows			+=	"<tr>"+
											"<td>"+array.VENDORTYPE+"</td>"+
											"<td>"+array.VENDORNAME+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALSCHEDULE)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALFEE)+"</td>"+
											"<td align='right'>"+numberFormat(array.TOTALCOLLECTPAYMENT)+"</td>"+
											"<td align='right'>"+numberFormat(totalBalance)+"</td>"+
										"</tr>";
							
				});
				
			}

			generatePagination("tablePaginationReportPerVendor", page, response.result.pageTotal);
			generateDataInfo("tableDataCountReportPerVendor", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

function getDataRecapPerVendor(page = 1){
	
	var $tableBody		=	$('#table-recapPerVendor > tbody'),
		columnNumber	=	$('#table-recapPerVendor > thead > tr > th').length,
		idVendorType	=	$('#optionVendorRecapTypeRecap').val(),
		idVendor		=	$('#optionVendorRecap').val(),
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		dataSend		=	{page:page, idVendorType:idVendorType, idVendor:idVendor, startDate:startDate, endDate:endDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/getDataRecapPerVendor",
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
				$('#excelRecapPerVendor').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$('#excelRecapPerVendor').removeClass('d-none').on("click").attr("href", response.urlExcelRecapPerVendor);
				$.each(data, function(index, array) {
					var btnExcelDetail	=	"";
					
					if(array.URLEXCELDETAILFEE != ""){
						btnExcelDetail	=	'<a class="button button-box button-xs button-info" target="_blank" href="'+array.URLEXCELDETAILFEE+'"><i class="fa fa-file-excel-o"></i></a>';
					}
					
					rows				+=	"<tr>"+
												"<td>"+array.VENDORTYPE+"</td>"+
												"<td>"+array.VENDORNAME+"</td>"+
												"<td align='right'>"+numberFormat(array.TOTALSCHEDULE)+"</td>"+
												"<td align='right'>"+numberFormat(array.TOTALFEE)+"</td>"+
												"<td align='center'>"+btnExcelDetail+"</td>"+
											"</tr>";
							
				});
				
			}

			generatePagination("tablePaginationRecapPerVendor", page, response.result.pageTotal, "generateDataTableRecapPerVendor");
			generateDataInfo("tableDataCountRecapPerVendor", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

function getDataPerVendorRecap(){
	var idVendor		=	$("#optionVendorPerRecap").val(),
		startDateDeposit=	$("#startDateDepositTransaction").val(),
		endDateDeposit	=	$("#endDateDepositTransaction").val(),
		dataSend		=	{
								idVendor:idVendor,
								startDateDeposit:startDateDeposit,
								endDateDeposit:endDateDeposit
							};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/getDataPerVendorRecap",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#vendorDetailInitial, #vendorDetailName, #vendorDetailAddress, #vendorDetailPhoneEmail, #vendorDetailVendorType").html("-");
			$("#vendorBankName, #vendorBankAccountNumber, #vendorBankAccountHolder, #lastWitdrawalDate, #lastDepositTransactionDate").html("-");
			$("#vendorBankLogo").attr("src", urlImageBankLogo+"default.png");
			$("#badgeFinanceSchemeTypeElem").remove();
			$("#btnManualWithdraw").addClass('d-none').off('click');
			$("#totalNominalFee, #totalNominalCollectPayment").html(0);
			$("#totalSchedule, #totalCollectPayment").html(0);
			$("#table-listFee > tbody").html('<tr><td colspan="5" align="center">No data</td></tr>');
			$("#table-listCollectPayment > tbody, #table-listDepositTransaction > tbody").html('<tr><td colspan="6" align="center">No data</td></tr>');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailVendor				=	response.detailVendor,
					dataBankAccount				=	response.dataBankAccount,
					dataRecapPerVendor			=	response.dataRecapPerVendor,
					dataListFee					=	response.dataListFee,
					dataListCollectPayment		=	response.dataListCollectPayment,
					dataListDepositHistory		=	response.dataListDepositHistory,
					financeSchemeType			=	detailVendor.FINANCESCHEMETYPE,
					rowListFee					=	rowListCollectPayment	=	badgeFinanceSchemeType	=	"",
					totalFeeVal					=	parseInt(dataRecapPerVendor.TOTALFEE),
					totalFeeSchedule			=	parseInt(dataRecapPerVendor.TOTALFEESCHEDULE),
					totalCollectPaymentVal		=	parseInt(dataRecapPerVendor.TOTALCOLLECTPAYMENT),
					withdrawalBalance			=	totalFeeVal - totalCollectPaymentVal,
					feeScheduleBalance			=	totalFeeSchedule - totalCollectPaymentVal;
				
				switch(financeSchemeType){
					case "1"	:	badgeFinanceSchemeType	=	'<span class="badge badge-pill mr-2 badge-primary" id="badgeFinanceSchemeTypeElem"><p class="m-0 p-1 text-white">Withdrawal Scheme</p></span>';
									$("#btnAddNewDepositRecord").addClass("d-none");
									break;
					case "2"	:	badgeFinanceSchemeType	=	'<span class="badge badge-pill mr-2 badge-warning" id="badgeFinanceSchemeTypeElem"><p class="m-0 p-1 text-white">Deposit Scheme</p></span>';
									$("#btnAddNewDepositRecord").removeClass("d-none");
									break;
				}

				$("#vendorDetailInitial").html(detailVendor.INITIALNAME);
				$("#vendorDetailName").html(detailVendor.NAME);
				$("#vendorDetailAddress").html(detailVendor.ADDRESS);
				$("#vendorDetailPhoneEmail").html(detailVendor.PHONE+" | "+detailVendor.EMAIL);
				$("#vendorDetailVendorType").html(detailVendor.VENDORTYPE);
				$("#vendorBankName").html(dataBankAccount.BANKNAME);
				$("#vendorBankAccountNumber").html(dataBankAccount.ACCOUNTNUMBER);
				$("#vendorBankAccountHolder").html(dataBankAccount.ACCOUNTHOLDERNAME);
				$("#vendorBankLogo").attr("src", dataBankAccount.BANKLOGO);
				$("#containerDetailVendor").append(badgeFinanceSchemeType);

				$("#totalNominalFee").html(numberFormat(totalFeeVal));
				$("#totalNominalCollectPayment").html(numberFormat(totalCollectPaymentVal));
				$("#totalCollectPayment").html(numberFormat(dataRecapPerVendor.TOTALSCHEDULEWITHCOLLECTPAYMENT));
				$("#totalWithdrawBalance").html(numberFormat(withdrawalBalance));
				$("#totalSchedule").html(numberFormat(dataRecapPerVendor.TOTALSCHEDULE));
				$("#totalDepositBalance").html(numberFormat(dataRecapPerVendor.DEPOSITBALANCE));
				$("#lastDepositTransactionDate").html(dataRecapPerVendor.LASTDEPOSITTRANSACTIONDATE);
				
				if(feeScheduleBalance > 0 && financeSchemeType == '1'){
					$("#btnManualWithdraw").removeClass('d-none');
					$('#btnManualWithdraw').on('click', function(e) {
						getDetailManualWithdraw(idVendor);
					});
				}
				
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
				
				if(dataListDepositHistory != false){
					var rowListDepositHistory	=	generateTableDepositHistory(dataListDepositHistory);
					$("#table-listDepositTransaction > tbody").html(rowListDepositHistory);
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

function getDetailManualWithdraw(idVendor){
	var dataSend		=	{idVendor:idVendor};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/getDetailManualWithdraw",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#manualWithdraw-vendorDetailInitial, #manualWithdraw-vendorDetailName, #manualWithdraw-vendorDetailAddress, #manualWithdraw-vendorDetailPhoneEmail, #manualWithdraw-documentNameManualWithdraw").html("-");
			$("#manualWithdraw-imgBankLogo").attr("src", urlImageBankLogo+"default.png");
			$("#manualWithdraw-bankNameStr, #manualWithdraw-accountNumberStr, #manualWithdraw-accountHolderNameStr").html("-");
			$("#manualWithdraw-totalFeeStr, #manualWithdraw-totalCollectPaymentStr, #manualWithdraw-totalWithdrawalStr, #manualWithdraw-totalDeductionStr, #manualWithdraw-totalAdditionalCostStr").html("0").attr('data-nominal', 0);
			$('#manualWithdraw-dataListWithdrawalDetail > tbody').html('<tr><td colspan="5" class="text-center">No data found</td></tr>');
			$('input[name="manualWithdraw-withdrawType[]"]').prop('checked', true);
			$("#manualWithdraw-idBankAccountVendor").val(0);
			$("#manualWithdraw-fileWithdrawDocument").val("");
			$('#manualWithdraw-startDate, #manualWithdraw-endDate, .manualWithdraw-withdrawType, #manualWithdraw-checkAllWithdrawItem').prop('disabled', false);
			
			$("#btnCloseDetailsManualWithdraw").removeClass("d-none");
			toggleSlideContainer('slideContainerLeft', 'slideContainerRightManualWithdraw');
			$('#manualWithdraw-btnModalUploadInvoice').removeClass('d-none');
			$('#manualWithdrawUploadInvoiceInvalidList-tbodyDataInvalid').html('<tr><td align="center" colspan="6">No data displayed</td></tr>');
			$('#window-loader').modal('show');
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$('#window-loader').modal('hide');
			
			if(response.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(toggleSlide){
					toggleSlideContainer('slideContainerLeft', 'slideContainerRightManualWithdraw');
					$("#btnCloseDetailsManualWithdraw").addClass("d-none");
				}
			} else {
				var detailVendor				=	response.detailVendor,
					dataBankAccount				=	response.dataBankAccount,
					listBankAccountVendor		=	response.listBankAccountVendor,
					dataFeeCollectPayment		=	response.dataFeeCollectPayment,
					firstDateFeeCollectPayment	=	response.firstDateFeeCollectPayment,
					autoReduceCollectPayment	=	detailVendor.AUTOREDUCECOLLECTPAYMENT,
					rowFeeCollectPayment		=	'';
					
				$("#manualWithdraw-vendorDetailInitial").html(detailVendor.INITIALNAME);
				$("#manualWithdraw-vendorDetailName").html(detailVendor.NAME);
				$("#manualWithdraw-vendorDetailAddress").html(detailVendor.ADDRESS);
				$("#manualWithdraw-vendorDetailPhoneEmail").html(detailVendor.PHONE+" | "+detailVendor.EMAIL);
				$("#manualWithdraw-autoReduceCollectPayment").val(autoReduceCollectPayment);
				$("#manualWithdraw-bankNameStr").html(dataBankAccount.BANKNAME);
				$("#manualWithdraw-accountNumberStr").html(dataBankAccount.ACCOUNTNUMBER);
				$("#manualWithdraw-accountHolderNameStr").html(dataBankAccount.ACCOUNTHOLDERNAME);
				$("#manualWithdraw-imgBankLogo").attr("src", dataBankAccount.BANKLOGO);
				$("#manualWithdraw-idVendor").val(idVendor);
				$("#manualWithdraw-idBankAccountVendor").val(dataBankAccount.IDBANKACCOUNTPARTNER);
				if(autoReduceCollectPayment == '1') $("#manualWithdraw-autoReduceCollectPaymentWarning").removeClass('d-none');
				if(autoReduceCollectPayment != '1') $("#manualWithdraw-autoReduceCollectPaymentWarning").addClass('d-none');
				createUploaderWithdrawDocument(idVendor);
				createUploaderExcelInvoice(idVendor);
				
				if(listBankAccountVendor.length > 0){
					var rowBankAccountVendor	=	'';
					$.each(listBankAccountVendor, function(indexBankAccountVendor, arrayBankAccountVendor) {
						var dnoneClass			=	arrayBankAccountVendor.STATUS == 1 ? "d-none" : "",
							borderNoneClass		=	indexBankAccountVendor == 0 ? "border-none" : "";
						rowBankAccountVendor	+=	'<tr>'+
														'<td class="'+borderNoneClass+'">'+
															'<img src="'+arrayBankAccountVendor.BANKLOGO+'" style="max-height:30px; max-width:90px"><br/>'+
															'<h6 class="mt-15 mb-0">'+arrayBankAccountVendor.BANKNAME+' - '+arrayBankAccountVendor.ACCOUNTNUMBER+'</h6>'+
															'<p>'+arrayBankAccountVendor.ACCOUNTHOLDERNAME+'</p>'+
														'</td>'+
														'<td width="140" class="'+borderNoneClass+'">'+
															'<button type="button" '+
																'class="btnChooseBankAccountVendor button button-info button-xs pull-right '+dnoneClass+'" '+
																'data-idBankAccountPartner="'+arrayBankAccountVendor.IDBANKACCOUNTPARTNER+'" '+
																'data-bankName="'+arrayBankAccountVendor.BANKNAME+'" '+
																'data-accountNumber="'+arrayBankAccountVendor.ACCOUNTNUMBER+'" '+
																'data-accountHolderName="'+arrayBankAccountVendor.ACCOUNTHOLDERNAME+'" '+
																'data-imgBankLogo="'+arrayBankAccountVendor.BANKLOGO+'" >'+
																'<span><i class="fa fa-external-link-square"></i>Choose</span>'+
															'</button>'+
														'</td>'+
													'</tr>';
					});
					$("#manualWithdrawBankAccountList-tableBankAccount > tbody").html(rowBankAccountVendor);
				} else {
					$("#manualWithdrawBankAccountList-tableBankAccount > tbody").html('<tr id="tableBankAccount-noData"><td align="center" class="border-none"><img src="'+baseURLAssets+'img/no-data.png" style="max-height: 100px"/><h6>No Data Found</h6></td></tr>');
				}
				
				$("#manualWithdraw-startDate").val(firstDateFeeCollectPayment);
				if(dataFeeCollectPayment.length > 0){
					$.each(dataFeeCollectPayment, function(indexFeeCollectPayment, arrayFeeCollectPayment) {
						rowFeeCollectPayment	+=	'<tr class="manualWithdrawItem" data-date="'+arrayFeeCollectPayment.DATEORDER+'" data-type="'+arrayFeeCollectPayment.TYPE+'">'+
														'<td align="center">'+
															'<label class="adomx-checkbox">'+
																'<input type="checkbox" class="checkboxWithdrawItem" value="'+arrayFeeCollectPayment.IDDATA+'" data-type="'+arrayFeeCollectPayment.TYPE+'" data-nominal="'+arrayFeeCollectPayment.NOMINAL+'"> '+
																'<i class="icon"></i>'+
															'</label>'+
														'</td>'+
														'<td>'+arrayFeeCollectPayment.TYPESTR+'</td>'+
														'<td>'+arrayFeeCollectPayment.DATESTR+'</td>'+
														'<td>'+arrayFeeCollectPayment.DESCRIPTION+'</td>'+
														'<td align="right"><b>'+numberFormat(arrayFeeCollectPayment.NOMINAL)+'</b></td>'+
													'</tr>';
							
					});
					$("#manualWithdraw-dataListWithdrawalDetail > tbody").html(rowFeeCollectPayment);
					$('#manualWithdraw-checkAllWithdrawItem').prop('checked', false);

					calculateNominalManualWithdraw();
				}
				
				enableOnclickChangeInputManualWithdraw();
			}
		}
	});
}

$("#manualWithdraw-btnAddBankAccountPartner").off("click");
$("#manualWithdraw-btnAddBankAccountPartner").on("click", function(e){
	e.preventDefault();
	$('#modal-manualWithdrawBankAccountList').modal('hide');
});

$('#modal-manualWithdrawAddBankAccountVendor').off('show.bs.modal');
$('#modal-manualWithdrawAddBankAccountVendor').on('show.bs.modal', function() {
	$('#manualWithdrawAddBankAccountVendor-optionBank option:first').prop('selected', true);
	$('#manualWithdrawAddBankAccountVendor-accountNumber').val("0");
	$('#manualWithdrawAddBankAccountVendor-accountHolderName').val("");
});

$('#editor-manualWithdrawAddBankAccountVendor').off('submit');
$('#editor-manualWithdrawAddBankAccountVendor').on('submit', function(e) {
	e.preventDefault();
	var idVendor			=	$("#manualWithdraw-idVendor").val(),
		idBank				=	$("#manualWithdrawAddBankAccountVendor-optionBank option:selected").val(),
		bankName			=	$("#manualWithdrawAddBankAccountVendor-optionBank option:selected").text(),
		accountNumber		=	$("#manualWithdrawAddBankAccountVendor-accountNumber").val(),
		accountHolderName	=	$("#manualWithdrawAddBankAccountVendor-accountHolderName").val(),
		msgWarning			=	'',
		dataSend			=	{
			idVendor:idVendor,
			idBank:idBank,
			bankName:bankName,
			accountNumber:accountNumber,
			accountHolderName:accountHolderName
		};
	
	if(idBank == 0 || idBank == '') msgWarning	=	'The type of bank you have selected is not valid.';
	if(bankName == 0 || bankName == '') msgWarning	=	'The type of bank you have selected is not valid.';
	if(accountNumber == 0 || accountNumber == '' || accountNumber.length <= 6) msgWarning	=	'Please enter a valid bank account number.';
	if(accountHolderName == 0 || accountHolderName == '' || accountHolderName.length <= 6) msgWarning	=	'Please enter a valid account holder name.';
	
	if(msgWarning != ''){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html(msgWarning);			
		});
		$('#modalWarning').modal('show');
	} else {
		$.ajax({
			type: 'POST',
			url: baseURL+"financeVendor/recapPerVendor/saveNewBankAccountVendor",
			contentType: 'application/json',
			dataType: 'json',
			cache: false,
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				NProgress.set(0.4);
				$('#window-loader').modal('show');
				$("#editor-manualWithdrawAddBankAccountVendor :input").attr("disabled", true);
			},
			success:function(response){
				$('#window-loader').modal('hide');
				NProgress.done();
				setUserToken(response);
				$("#editor-manualWithdrawAddBankAccountVendor :input").attr("disabled", false);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					var idBankAccountPartner=	response.idBankAccountPartner,
						detailBank			=	response.detailBank,
						borderNoneClass		=	$("#tableBankAccount-noData").length == 0 ? "" : "border-none";
						
					$('#modal-manualWithdrawAddBankAccountVendor').modal('hide');
					$("#manualWithdraw-bankNameStr").html(bankName);
					$("#manualWithdraw-accountNumberStr").html(accountNumber);
					$("#manualWithdraw-accountHolderNameStr").html(accountHolderName);
					$("#manualWithdraw-imgBankLogo").attr("src", detailBank.BANKLOGO);
					$("#manualWithdraw-idBankAccountVendor").val(idBankAccountPartner);

					$("#tableBankAccount-noData").remove();
					$(".btnChooseBankAccountVendor").removeClass('d-none');
					$("#manualWithdrawBankAccountList-tableBankAccount > tbody")
					.append('<tr>'+
								'<td class="'+borderNoneClass+'">'+
									'<img src="'+detailBank.BANKLOGO+'" style="max-height:30px; max-width:90px"><br/>'+
									'<h6 class="mt-15 mb-0">'+bankName+' - '+accountNumber+'</h6>'+
									'<p>'+accountHolderName+'</p>'+
								'</td>'+
								'<td width="140" class="'+borderNoneClass+'">'+
									'<button type="button" '+
										'class="btnChooseBankAccountVendor button button-info button-xs pull-right d-none" '+
										'data-idBankAccountPartner="'+idBankAccountPartner+'" '+
										'data-bankName="'+bankNameStr+'" '+
										'data-accountNumber="'+accountNumberStr+'" '+
										'data-accountHolderName="'+accountHolderNameStr+'" '+
										'data-imgBankLogo="'+detailBank.BANKLOGO+'" >'+
										'<span><i class="fa fa-external-link-square"></i>Choose</span>'+
									'</button>'+
								'</td>'+
							'</tr>');
					enableOnclickChangeInputManualWithdraw();
				}
			}
		});
	}
});

function createUploaderWithdrawDocument(idVendor){
	$('.ajax-file-upload-container').remove();
	$("#manualWithdraw-iconDocumentManualWithdraw").removeClass('fa-file-image-o fa-file-excel-o fa-file-word-o fa-file-pdf-o').addClass('fa-cloud-upload');
	$("#manualWithdraw-withdrawDocument").uploadFile({
		url: baseURL+"financeVendor/recapPerVendor/uploadDocumentInvoiceManualWithdraw/"+idVendor,
		multiple:false,
		dragDrop:false,
		allowedTypes: "xls, xlsx, doc, docx, pdf, jpg, jpeg, png",
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
				var fileNameOrigin	=	data.fileNameOrigin,
					extension		=	data.extension,
					iconUploadClass	=	'fa-cloud-upload';
				$("#manualWithdraw-fileWithdrawDocument").val(data.fileName);
				switch(extension){
					case 'jpg'	:
					case 'jpeg'	:	
					case 'png'	:	iconUploadClass	=	'fa-file-image-o'; break;
					case 'xls'	:	
					case 'xlsx'	:	iconUploadClass	=	'fa-file-excel-o'; break;
					case 'doc'	:	
					case 'docx'	:	iconUploadClass	=	'fa-file-word-o'; break;
					case 'pdf'	:	iconUploadClass	=	'fa-file-pdf-o'; break;
					default		:	break;
				}
				
				$("#manualWithdraw-iconDocumentManualWithdraw").removeClass('fa-cloud-upload').addClass(iconUploadClass);
				$("#manualWithdraw-documentNameManualWithdraw").html(fileNameOrigin);
			}
		}
	});
}

function createUploaderExcelInvoice(idVendor){
	$('.ajax-file-upload-container').remove();
	$("#manualWithdrawUploadInvoice-uploaderExcelInvoice").uploadFile({
		url: baseURL+"financeVendor/recapPerVendor/uploadExcelInvoice/"+idVendor,
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
				let autoReduceCollectPayment	=	$("#manualWithdraw-autoReduceCollectPayment").val();
				$('#modal-manualWithdrawUploadInvoice').modal('hide');
				readExcelInvoiceVendor(idVendor, data.fileName, autoReduceCollectPayment);
			}
		}
	});
}

function readExcelInvoiceVendor(idVendor, fileExcelName, autoReduceCollectPayment){
	var dataSend	=	{
			idVendor:idVendor,
			fileExcelName:fileExcelName,
			autoReduceCollectPayment:autoReduceCollectPayment
		};

	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/readExcelInvoiceVendor",
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
				var validData			=	response.validData,
					collectPaymentData	=	response.collectPaymentData;
					
				if(validData.length <= 0){
					showErrorWarning("Unknown error");
				} else {
					$('#manualWithdraw-startDate, #manualWithdraw-endDate, .manualWithdraw-withdrawType, #manualWithdraw-checkAllWithdrawItem').prop('disabled', true);
					$('.checkboxWithdrawItem').prop('checked', false).prop('disabled', true);
					$('#manualWithdraw-btnModalUploadInvoice, .manualWithdrawItem').addClass('d-none');

					if(autoReduceCollectPayment != '1'){
						$('input[type="checkbox"][data-type="3"].checkboxWithdrawItem').each(function() {
							$(this).prop('checked', true);
							$(this).closest('tr').removeClass('d-none');
						});
					} else {
						for(var i=0; i<collectPaymentData.length; i++){
							var idCollectPayment	=	collectPaymentData[i];
							$('input[type="checkbox"][data-type="3"][value='+idCollectPayment+'].checkboxWithdrawItem').each(function() {
								$(this).prop('checked', true);
								$(this).closest('tr').removeClass('d-none');
							});
						}
					}
					
					for(var i=0; i<validData.length; i++){
						var idReservationDetails	=	validData[i];
						$('input[type="checkbox"][data-type="1"][value='+idReservationDetails+'].checkboxWithdrawItem').each(function() {
							$(this).prop('checked', true);
							$(this).closest('tr').removeClass('d-none');
						});
					}
					calculateNominalManualWithdraw();
				}
			} else {
				var msgError	=	response.msg,
					invalidData	=	response.invalidData;
				
				if(invalidData.length <= 0){
					if(msgError !== null && msgError !== undefined && msgError !== ''){
						showErrorWarning(msgError);
					} else {
						showErrorWarning("Unknown error");
					}
				} else {
					var rowsDataInvalid	=	'';
					$.each(invalidData, function(index, array) {
						var arrDataExcel	=	array.arrDataExcel,
							arrDataSchedule	=	array.arrDataSchedule,
							errCode			=	array.errCode,
							errMessage		=	array.errMessage,
							elemDetailSystem=	productName	=	dateSchedule	=	badgeStatusWD	=	'-';
						
						if(arrDataSchedule !== null && arrDataSchedule !== undefined && arrDataSchedule !== ''){
							var bookingCodeDB			=	arrDataSchedule.bookingCodeDB,
								nominalCollectPayment	=	arrDataSchedule.nominalCollectPayment,
								nominalFinal			=	arrDataSchedule.nominalFinal,
								elemAddsCollectPayment	=	nominalCollectPayment == 0 ? '' : ' - '+numberFormat(nominalCollectPayment)+'<br/><b>'+numberFormat(nominalFinal)+'</b>';
								
							if(bookingCodeDB !== null && bookingCodeDB !== undefined && bookingCodeDB !== ''){
								elemDetailSystem=	arrDataSchedule.bookingCodeDB+'<br/>'+arrDataSchedule.customerNameDB+'<br/>'+numberFormat(arrDataSchedule.nominalDB)+elemAddsCollectPayment;
								productName		=	arrDataSchedule.productName;
								dateSchedule	=	arrDataSchedule.dateScheduleStr;
								badgeStatusWD	=	arrDataSchedule.withdrawStatus == 1 ? '<span class="badge badge-primary">Withdrawn</span>' : '';
							}
						}
						
						rowsDataInvalid		+=	'<tr>'+
													'<td>'+arrDataExcel.bookingCode+'<br/>'+arrDataExcel.customerName+'<br/>'+numberFormat(arrDataExcel.nominal)+'</td>'+
													'<td>'+elemDetailSystem+'</td>'+
													'<td>'+productName+'</td>'+
													'<td>'+dateSchedule+'</td>'+
													'<td>'+badgeStatusWD+'</td>'+
													'<td>'+errMessage+'</td>'+
												'</tr>'
					});
					$("#manualWithdrawUploadInvoiceInvalidList-tbodyDataInvalid").html(rowsDataInvalid);
					$('#modal-manualWithdrawUploadInvoiceInvalidList').modal('show');
				}
			}
		}
	});
}

function showErrorWarning(errMessage){
	$('#modalWarning').on('show.bs.modal', function() {
		$('#modalWarningBody').html(errMessage);			
	});
	$('#modalWarning').modal('show');
}

$('#modal-manualWithdrawAdditionalCostDeduction').off('show.bs.modal');
$('#modal-manualWithdrawAdditionalCostDeduction').on('show.bs.modal', function() {
	$('#manualWithdrawAdditionalCostDeduction-optionType option:first').prop('selected', true);
	$('#manualWithdrawAdditionalCostDeduction-nominal').val(0);
	$('#manualWithdrawAdditionalCostDeduction-description').val("");
});

$('#editor-manualWithdrawAdditionalCostDeduction').off('submit');
$('#editor-manualWithdrawAdditionalCostDeduction').on('submit', function(e) {
	e.preventDefault();
	var type			=	$("#manualWithdrawAdditionalCostDeduction-optionType option:selected").val(),
		typeStr			=	$("#manualWithdrawAdditionalCostDeduction-optionType option:selected").text(),
		date			=	$("#manualWithdrawAdditionalCostDeduction-date").val(),
		isValidDate		=	moment(date, "DD-MM-YYYY", true).isValid(),
		nominal			=	$("#manualWithdrawAdditionalCostDeduction-nominal").val(),
		description		=	$("#manualWithdrawAdditionalCostDeduction-description").val(),
		msgWarning		=	'';
	
	if(type == 0 || type == '') msgWarning	=	'The type you have selected is not valid.';
	if(typeStr == 0 || typeStr == '') msgWarning	=	'The type you have selected is not valid.';
	if(date == 0 || date == '' || date.length != 10 || !isValidDate) msgWarning	=	'Please enter a valid date.';
	if(description == 0 || description == '' || description.length <= 6) msgWarning	=	'Please enter a valid description.';
	
	if(msgWarning != ''){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html(msgWarning);			
		});
		$('#modalWarning').modal('show');
	} else {
		var dateFormatted				=	moment(date, "DD-MM-YYYY").format("YYYY-MM-DD"),
			dateStr						=	moment(date, "DD-MM-YYYY").format("DD MMM YYYY"),
			nominal						=	nominal.replace(/\D/g, ''),
			nominal						=	type == 2 ? nominal : nominal * -1,
			rowAdditionalCostDeduction	=	'<tr class="manualWithdrawItem" data-type="'+type+'">'+
												'<td align="center">'+
													'<label class="adomx-checkbox">'+
														'<input type="checkbox" class="checkboxWithdrawItem" value="0" data-type="'+type+'" data-nominal="'+nominal+'" data-date="'+dateFormatted+'" checked disabled> '+
														'<i class="icon"></i>'+
													'</label>'+
												'</td>'+
												'<td>'+typeStr+'</td>'+
												'<td>'+dateStr+'</td>'+
												'<td>'+description+'</td>'+
												'<td align="right">'+
													'<b>'+numberFormat(nominal)+'</b><br/>'+
													'<button type="button" class="btnRemoveAdditionalCostDeduction button button-warning button-xs pull-right>'+
														'<span><i class="fa fa-trash"></i>Delete</span>'+
													'</button>'+
												'</td>'+
											'</tr>';
		$("#manualWithdraw-dataListWithdrawalDetail > tbody").append(rowAdditionalCostDeduction);
		enableOnclickBtnRemoveAdditionalCostDeduction();
		enableOnclickChangeInputManualWithdraw();
		calculateNominalManualWithdraw();
		$('input.manualWithdraw-withdrawType[value="'+type+'"]').prop('checked', true);
		$('#modal-manualWithdrawAdditionalCostDeduction').modal('hide');
	}
});

function enableOnclickBtnRemoveAdditionalCostDeduction(){
	$(".btnRemoveAdditionalCostDeduction").off("click");
	$(".btnRemoveAdditionalCostDeduction").on("click", function(e){
		e.preventDefault();
		$(this).closest('tr').remove();
		enableOnclickChangeInputManualWithdraw();
		calculateNominalManualWithdraw();
	});
}

function enableOnclickChangeInputManualWithdraw(){
	$(".btnChooseBankAccountVendor").off("click");
	$(".btnChooseBankAccountVendor").on("click", function(e){
		e.preventDefault();
		var idBankAccountPartner=	$(this).attr('data-idBankAccountPartner'),
			bankName			=	$(this).attr('data-bankName'),
			accountNumber		=	$(this).attr('data-accountNumber'),
			accountHolderName	=	$(this).attr('data-accountHolderName'),
			imgBankLogo			=	$(this).attr('data-imgBankLogo');
			
		$("#manualWithdraw-bankNameStr").html(bankName);
		$("#manualWithdraw-accountNumberStr").html(accountNumber);
		$("#manualWithdraw-accountHolderNameStr").html(accountHolderName);
		$("#manualWithdraw-imgBankLogo").attr("src", imgBankLogo);
		$("#manualWithdraw-idBankAccountVendor").val(idBankAccountPartner);
		$(".btnChooseBankAccountVendor").removeClass('d-none');
		$(this).addClass('d-none');
		$('#modal-manualWithdrawBankAccountList').modal('hide');
	});
	
	$("#manualWithdraw-startDate, #manualWithdraw-endDate").off("change");
	$("#manualWithdraw-startDate, #manualWithdraw-endDate").on("change", function(){
		filterManualWithdrawItem();
	});
	
	$(".manualWithdraw-withdrawType").off("click");
	$(".manualWithdraw-withdrawType").on("click", function(e){
		filterManualWithdrawItem();
	});
	
	$("#manualWithdraw-checkAllWithdrawItem").off("click");
	$("#manualWithdraw-checkAllWithdrawItem").on("click", function(){
		var checkedStatus	=	$("#manualWithdraw-checkAllWithdrawItem").is(':checked');
		$(".checkboxWithdrawItem").prop('checked', false);
		
		if(checkedStatus){
			$('.checkboxWithdrawItem').each(function() {
				if (!$(this).closest('tr').hasClass('d-none')) {
					$(this).prop('checked', true);	
				}
			});
		}
		calculateNominalManualWithdraw();
	});
	
	$(".checkboxWithdrawItem").off("click");
	$(".checkboxWithdrawItem").on("click", function(){
		var totalCheckbox	=	$(".checkboxWithdrawItem").length,
			totalChecked	=	$("[class='checkboxWithdrawItem']:checked").length;
		if(totalCheckbox == totalChecked){
			$("#manualWithdraw-checkAllWithdrawItem").prop('checked', true);
		} else {
			$("#manualWithdraw-checkAllWithdrawItem").prop('checked', false);
		}
		calculateNominalManualWithdraw();
	});
}

function filterManualWithdrawItem(){
	var startDate			=	$("#manualWithdraw-startDate").val(),
		startDate			=	moment(startDate, "DD-MM-YYYY"),
		endDate				=	$("#manualWithdraw-endDate").val(),
		endDate				=	moment(endDate, "DD-MM-YYYY"),
		checkedWithdrawType	=	[];
	
	$('input[name="manualWithdraw-withdrawType[]"]:checked').each(function() {
		checkedWithdrawType.push($(this).val());
	});
		
	$('.manualWithdrawItem').each(function(index, element) {
		var rowDate	=	$(this).attr('data-date'),
			rowDate	=	moment(rowDate, "YYYY-MM-DD"),
			rowType	=	$(this).attr('data-type');
		if(rowDate.isBetween(startDate, endDate, undefined, '[]') && checkedWithdrawType.indexOf(rowType) !== -1){
			$(this).removeClass('d-none');
		} else {
			$(this).addClass('d-none');
		}
	});
	
	$('#manualWithdraw-checkAllWithdrawItem').prop('checked', false);
	$(".checkboxWithdrawItem").prop('checked', false);
	calculateNominalManualWithdraw();
}

function calculateNominalManualWithdraw(){
	var totalFee=	totalCollectPayment	= totalAdditionalCost = totalDeduction = 0;
	$('.checkboxWithdrawItem').each(function(index, element) {
		var type	=	$(this).attr('data-type'),
			nominal	=	parseInt($(this).attr('data-nominal')) * 1,
			checked	=	$(this).is(':checked');
		if(checked){
			switch(type){
				case "1"	:	totalFee			+=	nominal; break;
				case "2"	:	totalAdditionalCost	+=	nominal; break;
				case "3"	:	totalCollectPayment	+=	nominal; break;
				case "4"	:	totalDeduction		+=	nominal; break;
			}
		}
	});
	
	var totalManualWithdraw	=	totalFee + totalAdditionalCost + totalCollectPayment + totalDeduction;
	$("#manualWithdraw-totalFeeStr").html(numberFormat(totalFee)).attr('data-nominal', totalFee);
	$("#manualWithdraw-additionalCostStr").html(numberFormat(totalAdditionalCost)).attr('data-nominal', totalAdditionalCost);
	$("#manualWithdraw-totalCollectPaymentStr").html(numberFormat(totalCollectPayment)).attr('data-nominal', totalCollectPayment);
	$("#manualWithdraw-totalDeductionStr").html(numberFormat(totalDeduction)).attr('data-nominal', totalDeduction);
	$("#manualWithdraw-totalWithdrawalNominalStr").html(numberFormat(totalManualWithdraw)).attr('data-nominal', totalManualWithdraw);
}

$('#manualWithdraw-btnSaveManualWithdraw').off('click');
$('#manualWithdraw-btnSaveManualWithdraw').on('click', function(e) {
	e.preventDefault();

	var idVendor				=	$("#manualWithdraw-idVendor").val(),
		idBankAccountVendor		=	$("#manualWithdraw-idBankAccountVendor").val(),
		fileWithdrawDocument	=	$("#manualWithdraw-fileWithdrawDocument").val(),
		totalWithdrawalNominal	=	$("#manualWithdraw-totalWithdrawalNominalStr").attr('data-nominal'),
		msgWarning				=	'';
	
	if(idVendor == 0 || idVendor == '') msgWarning	=	'The submitted data is invalid. Please try again later';
	if(idBankAccountVendor == 0 || idBankAccountVendor == '') msgWarning	=	'Please select vendor bank account data or create a new bank account to proceed';
	if(fileWithdrawDocument == 0 || fileWithdrawDocument == '') msgWarning	=	'Please upload a valid invoice document';
	if(totalWithdrawalNominal <= 0 || totalWithdrawalNominal == '') msgWarning	=	'The vendor`s withdrawal balance is insufficient (less than or equal to zero)';
	
	if(msgWarning != ''){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html(msgWarning);			
		});
		$('#modalWarning').modal('show');
	} else {
		var vendorName				=	$("#manualWithdraw-vendorDetailName").html(),
			periodDateStart			=	$("#manualWithdraw-startDate").val(),
			periodDateStart			=	moment(periodDateStart, "DD-MM-YYYY").format("D MMM YYYY"),
			periodDateEnd			=	$("#manualWithdraw-endDate").val(),
			periodDateEnd			=	moment(periodDateEnd, "DD-MM-YYYY").format("D MMM YYYY"),
			nominalFee				=	$("#manualWithdraw-totalFeeStr").attr('data-nominal'),
			nominalAdditionalCost	=	$("#manualWithdraw-additionalCostStr").attr('data-nominal'),
			nominalCollectPayment	=	$("#manualWithdraw-totalCollectPaymentStr").attr('data-nominal'),
			nominalDeduction		=	$("#manualWithdraw-totalDeductionStr").attr('data-nominal'),
			confirmText				=	'Manual withdraw data will be saved. Details ;<br/><br/>'+
										'<div class="order-details-customer-info border-bottom pb-10 mb-10">'+
											'<ul class="ml-5">'+
												'<li> <span>Vendor</span> <span class="font-weight-bold">'+vendorName+'</span> </li>'+
												'<li> <span>Period</span> <span>'+periodDateStart+' to '+periodDateEnd+'</span> </li>'+
											'</ul>'+
										'</div>'+
										'<div class="order-details-customer-info">'+
											'<ul class="ml-5">'+
												'<li> <span>Fee</span> <span>'+numberFormat(nominalFee)+'</span> </li>'+
												'<li> <span>Additional Cost</span> <span>'+numberFormat(nominalAdditionalCost)+'</span> </li>'+
												'<li> <span>Collect Payment</span> <span>'+numberFormat(nominalCollectPayment)+'</span> </li>'+
												'<li> <span>Deduction</span> <span>'+numberFormat(nominalDeduction)+'</span> </li>'+
												'<li> <span>Saldo</span> <span class="font-weight-bold">'+numberFormat(totalWithdrawalNominal)+'</span> </li>'+
											'</ul>'+
										'</div><br/>'+
										'Are you sure? Please add description/notes to continue.'+
										'<div class="form-group mt-5">'+
											'<textarea class="form-control" placeholder="Description/notes" id="manualWithdraw-descriptionNotes" name="manualWithdraw-descriptionNotes"></textarea>'+
										'</div>';
			
		$confirmDialog.find('#modal-confirm-body').html(confirmText);
		$confirmDialog.find('#confirmBtn').attr('data-function', "saveManualWithdrawVendor");
		$confirmDialog.modal('show');
	}
});

$('#btnCloseDetailsManualWithdraw').off('click');
$('#btnCloseDetailsManualWithdraw').on('click', function(e) {
	toggleSlideContainer('slideContainerLeft', 'slideContainerRightManualWithdraw');
	$("#btnCloseDetailsManualWithdraw").addClass("d-none");
});

function openZoomTransferReceipt(imageSrc){
	$('#modal-zoomReceiptTransfer').on('show.bs.modal', function() {
		$('#zoomImageReceiptTransfer').attr('src', imageSrc);
	});
	$('#modal-zoomReceiptTransfer').modal('show');
}

$('#startDateDepositTransaction, #endDateDepositTransaction').off('change');
$('#startDateDepositTransaction, #endDateDepositTransaction').on('change', function(e) {
	var $tableBody		=	$('#table-listDepositTransaction > tbody'),
		columnNumber	=	$('#table-listDepositTransaction > thead > tr > th').length,
		idVendor		=	$("#optionVendorPerRecap").val(),
		startDateDeposit=	$("#startDateDepositTransaction").val(),
		endDateDeposit	=	$("#endDateDepositTransaction").val(),
		dataSend		=	{
								idVendor:idVendor,
								startDateDeposit:startDateDeposit,
								endDateDeposit:endDateDeposit
							};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/getDataListDepositHistory",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#optionVendorPerRecap, #startDateDepositTransaction, #endDateDepositTransaction").attr("disabled", true);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$("#optionVendorPerRecap, #startDateDepositTransaction, #endDateDepositTransaction").attr("disabled", false);
			
			var dataListDepositHistory	=	response.dataListDepositHistory,
				rows					=	"";
			
			if(response.status == 200){
				rows	=	generateTableDepositHistory(dataListDepositHistory);
			} else {
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td>"+
							"</tr>";
			}

			$tableBody.html(rows);
			
		}
	});
});

function generateTableDepositHistory(dataListDepositHistory){
	var rowListDepositHistory	=	'';
	$.each(dataListDepositHistory, function(indexListDepositHistory, arrayListDepositHistory) {
						
		var inputType			=	'',
			reservationDetails	=	collectPaymentDetails	=	'-',
			btnZoomReceipt		=	arrayListDepositHistory.TRANSFERRECEIPT != '' ? '<button class="button button-box button-primary button-xs" type="button"><i class="fa fa-list-alt" onclick="openZoomTransferReceipt(\''+arrayListDepositHistory.TRANSFERRECEIPT+'\')"></i></button>' : '';
		
		switch(arrayListDepositHistory.INPUTTYPE){
			case "1"	:	inputType	=	'Mailbox'; break;
			case "2"	:	inputType	=	'Manual'; break;
		}
		
		if(arrayListDepositHistory.IDRESERVATIONDETAILS != 0 && arrayListDepositHistory.IDRESERVATIONDETAILS != "0"){
			reservationDetails	=	'['+inputType+'] '+arrayListDepositHistory.SOURCENAME+' | '+arrayListDepositHistory.BOOKINGCODE+'<br/><br/>'+
									'<b>'+arrayListDepositHistory.CUSTOMERNAME+'</b><br/>'+
									'<b>'+arrayListDepositHistory.RESERVATIONTITLE+'</b>';
		}
		
		if(arrayListDepositHistory.IDCOLLECTPAYMENT != 0 && arrayListDepositHistory.IDCOLLECTPAYMENT != "0"){
			collectPaymentDetails	=	'['+arrayListDepositHistory.PAYMENTAMOUNTCURRENCY+'] '+arrayListDepositHistory.PAYMENTAMOUNT+' x '+numberFormat(arrayListDepositHistory.PAYMENTEXCHANGECURRENCY)+
										'<br/><b>'+numberFormat(arrayListDepositHistory.PAYMENTAMOUNTIDR)+' IDR</b>'+
										'<br/>'+arrayListDepositHistory.PAYMENTDESCRIPTION+
										'<br/><br/>Approval :<br/>'+
										arrayListDepositHistory.COLLECTDATETIMESTATUS+
										'<br/>By '+arrayListDepositHistory.COLLECTUSERAPPROVE;
		}
		
		rowListDepositHistory	+=	'<tr>'+
										'<td>'+arrayListDepositHistory.USERINPUT+'<br/><b class="text-primary">'+arrayListDepositHistory.DATETIMEINPUTSTR+'</b></td>'+
										'<td>'+arrayListDepositHistory.DESCRIPTION+'</td>'+
										'<td>'+reservationDetails+'</td>'+
										'<td>'+collectPaymentDetails+'</td>'+
										'<td align="right"><b>'+numberFormat(arrayListDepositHistory.AMOUNT)+'</b></td>'+
										'<td>'+btnZoomReceipt+'</td>'+
									'</tr>';
			
	});
	
	return rowListDepositHistory;
}

$('#modal-addNewDepositRecord').off('shown.bs.modal');
$('#modal-addNewDepositRecord').on('shown.bs.modal', function (e) {
	var idVendor	=	$("#optionVendorPerRecap").val();
	$("#optionVendorDepositRecord").val(idVendor).attr("disabled", true);
	$("#dateDepositRecord").val(dateToday);
	$("#depositNominal").val(0);
	$("#depositDescription").val("");
	$("#imageTransferReceiptDeposit").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "200px");
	createUploaderTransferReceiptDeposit(idVendor);
});

function createUploaderTransferReceiptDeposit(idVendor){
	
	idVendor	=	idVendor == "" ? 0 : idVendor;
	$('.ajax-file-upload-container').remove();
	$("#uploaderTransferReceiptDeposit").uploadFile({
		url: baseURL+"financeVendor/recapPerVendor/uploadTransferReceiptDeposit/"+idVendor,
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
				$("#imageTransferReceiptDeposit").removeAttr('src').attr("src", data.urlTransferReceipt);
				$("#transferReceiptDepositFileName").val(data.transferReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#editor-addNewDepositRecord').off('submit');
$('#editor-addNewDepositRecord').on('submit', function(e) {
	e.preventDefault();
	var dataForm	=	$("#editor-addNewDepositRecord :input").serializeArray(),
		idVendor	=	$("#optionVendorDepositRecord option:selected").val(),
		vendorName	=	$("#optionVendorDepositRecord option:selected").text(),
		dataSend	=	{idVendor:idVendor, vendorName:vendorName};
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/saveNewDepositRecord",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#editor-addNewDepositRecord :input").attr("disabled", true);
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			$("#editor-addNewDepositRecord :input").attr("disabled", false);
			$("#optionVendorDepositRecord").attr("disabled", true);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);			
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-addNewDepositRecord').modal('hide');
				getDataPerVendorRecap();
			}
		}
	});
});

$('#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionVendorWithdrawal, #optionStatusWithdrawal').off('change');
$('#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionVendorWithdrawal, #optionStatusWithdrawal').on('change', function(e) {
	getDataWithdrawalRequest();
});
	
$('#checkboxViewWithdrawalRequestOnly').off('click');
$("#checkboxViewWithdrawalRequestOnly").on('click',function(e) {
	var checked	=	$("#checkboxViewWithdrawalRequestOnly").is(':checked');
	
	if(checked){
		$("#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionVendorWithdrawal, #optionStatusWithdrawal").attr("disabled", true);
	} else {
		$("#startDateWithdrawalRequest, #endDateWithdrawalRequest, #optionVendorWithdrawal, #optionStatusWithdrawal").attr("disabled", false);
	}
	
	getDataWithdrawalRequest();
});

function getDataWithdrawalRequest(){
	
	var startDate			=	$('#startDateWithdrawalRequest').val(),
		endDate				=	$('#endDateWithdrawalRequest').val(),
		idVendor			=	$("#optionVendorWithdrawal").val(),
		statusWithdrawal	=	$("#optionStatusWithdrawal").val(),
		viewRequestOnly		=	$("#checkboxViewWithdrawalRequestOnly").is(':checked'),
		dataSend			=	{startDate:startDate, endDate:endDate, idVendor:idVendor, statusWithdrawal:statusWithdrawal, viewRequestOnly:viewRequestOnly};

	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/getDataWithdrawalRequest",
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
						case "-2"	:	badgeStatusWithdrawal	=	'<span class="badge badge-danger">Cancelled</span>'; break;
						case "-1"	:	badgeStatusWithdrawal	=	'<span class="badge badge-danger">Rejected</span>'; break;
						case "0"	:	badgeStatusWithdrawal	=	'<span class="badge badge-info">Requested</span>'; break;
						case "1"	:	badgeStatusWithdrawal	=	'<span class="badge badge-warning">Approved</span>'; break;
						case "2"	:	badgeStatusWithdrawal	=	'<span class="badge badge-success">Transfered</span>'; break;
						default		:	break;
					}
					
					rowWithdrawalRequest		+=	'<div class="col-sm-12 pb-1 mb-5 rounded-lg withdrawalTableElement">'+
														'<div class="row pt-10 pb-1">'+
															'<div class="col-lg-4 col-sm-12" style="border-right: 1px solid #dee2e6;">'+
																badgeStatusWithdrawal+"<br/>"+
																'<b>'+array.VENDORNAME+'</b>'+
																'<br/><small>Date Time Request : '+array.DATETIMEREQUEST+'</small>'+
																'<br/><small>Message : '+array.MESSAGE+'</small>'+
															'</div>'+
															'<div class="col-lg-4 col-sm-12" style="border-right: 1px solid #dee2e6;">'+
																'<div class="row">'+
																	'<div class="col-12">'+
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
																		'<li> <span>Collect Payment</span> <span><b>-'+numberFormat(array.TOTALCOLLECTPAYMENT)+'</b></span> </li>'+
																		'<li> <span>Deduction</span> <span><b>- '+numberFormat(array.TOTALDEDUCTION)+'</b></span> </li>'+
																	'</ul>'+
																'</div>'+
																'<div class="order-details-customer-info pt-1">'+
																	'<ul>'+
																		'<li> <span><b>Total Withdrawal</b></span> <span><b>'+numberFormat(array.TOTALWITHDRAWAL)+'</b></span> </li>'+
																	'</ul>'+
																'</div>'+
																'<a class="button button-primary button-xs text-light btn-block text-center mt-auto" onclick="getWithdrawalDetail('+array.IDWITHDRAWALRECAP+')"><span><i class="fa fa-info"></i> More Details</span></a>'+
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
		url: baseURL+"financeVendor/recapPerVendor/getDetailWithdrawalRequest",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#btnCloseDetails").removeClass("d-none");
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
				$("#vendorNameStr").html(dataDetail.VENDORNAME);
				$("#requestDateTimeStr").html(dataDetail.DATETIMEREQUEST);
				$("#messageStr").html(dataDetail.MESSAGE);
				$("#imgBankLogo").attr("src", dataDetail.BANKLOGO);
				$("#bankNameStr").html(dataDetail.BANKNAME);
				$("#accountNumberStr").html(dataDetail.ACCOUNTNUMBER);
				$("#accountHolderNameStr").html(dataDetail.ACCOUNTHOLDERNAME);
				
				$("#totalFeeStr").html(numberFormat(dataDetail.TOTALFEE));
				$("#totalAdditionalCostStr").html(numberFormat(dataDetail.TOTALADDITIONALCOST));
				$("#totalCollectPaymentStr").html("-"+numberFormat(dataDetail.TOTALCOLLECTPAYMENT));
				$("#totalDeductionStr").html("-"+numberFormat(dataDetail.TOTALDEDUCTION));
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
				} else {
					$('#btnRejectWithdrawal, #btnApproveWithdrawal').addClass('d-none').off('click');
				}
				
				var rowListDetailWithdrawal	=	'';
				if(listDetailWithdrawal.length > 0){
					$.each(listDetailWithdrawal, function(index, array) {
						rowListDetailWithdrawal	+=	'<tr>'+
														'<td>'+array.TYPESTR+'</td>'+
														'<td>'+array.DATESTR+'</td>'+
														'<td>'+array.DESCRIPTION+'</td>'+
														'<td align="right">'+numberFormat(array.NOMINAL)+'</td>'+
													'</tr>';
					});
				} else {
					rowListDetailWithdrawal	=	'<td colspan="4" class="text-center">No data found</td>';
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

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idWithdrawalRecap	=	$confirmDialog.find('#confirmBtn').attr('data-idWithdrawalRecap'),
		status				=	$confirmDialog.find('#confirmBtn').attr('data-status'),
		functionUrl			=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		dataSend			=	{idWithdrawalRecap:idWithdrawalRecap, status:status};
		
	if(functionUrl == 'saveManualWithdrawVendor'){
		var idVendor				=	$("#manualWithdraw-idVendor").val(),
			idBankAccountVendor		=	$("#manualWithdraw-idBankAccountVendor").val(),
			fileWithdrawDocument	=	$("#manualWithdraw-fileWithdrawDocument").val(),
			descriptionNotes		=	$("#manualWithdraw-descriptionNotes").val(),
			descriptionNotesTrim	=	descriptionNotes.replace(/\s+/g, ''),
			totalFee				=	$("#manualWithdraw-totalFeeStr").attr('data-nominal'),
			totalAdditionalCost		=	$("#manualWithdraw-additionalCostStr").attr('data-nominal'),
			totalCollectPayment		=	$("#manualWithdraw-totalCollectPaymentStr").attr('data-nominal'),
			totalDeduction			=	$("#manualWithdraw-totalDeductionStr").attr('data-nominal'),
			totalWithdrawalNominal	=	$("#manualWithdraw-totalWithdrawalNominalStr").attr('data-nominal'),
			arrayWithdrawItem		=	[],
			dataSend				=	{
				idVendor:idVendor,
				idBankAccountVendor:idBankAccountVendor,
				fileWithdrawDocument:fileWithdrawDocument,
				descriptionNotes:descriptionNotes,
				totalFee:totalFee,
				totalAdditionalCost:totalAdditionalCost,
				totalCollectPayment:totalCollectPayment,
				totalDeduction:totalDeduction,
				totalWithdrawalNominal:totalWithdrawalNominal
			};
			
		$('input.checkboxWithdrawItem:checked').each(function() {
			var idData	=	$(this).val(),
				type	=	$(this).attr('data-type'),
				nominal	=	$(this).attr('data-nominal');
				
			switch(type){
				case "1"	:	
				case "3"	:	arrayWithdrawItem.push([type, idData, nominal]);
								break;
				case "2"	:	
				case "4"	:	var additionalCostDeductionDate			=	$(this).attr('data-date'),
									additionalCostDeductionDescription	=	$(this).closest('tr').find('td').eq(3).html();
								arrayWithdrawItem.push([type, idData, nominal, additionalCostDeductionDate, additionalCostDeductionDescription]);
								break;
			}
		});
		dataSend['arrayWithdrawItem']	=	arrayWithdrawItem;
		
		if(descriptionNotes == '' || descriptionNotesTrim.length <= 8) {
			$confirmDialog.modal('hide');
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html('Please enter a valid description/notes');			
			});
			$('#modalWarning').modal('show');
			return;
		}
	}
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/recapPerVendor/"+functionUrl,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			if(functionUrl != 'saveManualWithdrawVendor') $confirmDialog.modal('hide');
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
				if(functionUrl != 'saveManualWithdrawVendor'){
					getWithdrawalDetail(idWithdrawalRecap, false);
				} else {
					toggleSlideContainer('slideContainerLeft', 'slideContainerRightManualWithdraw');
					$("#btnCloseDetailsManualWithdraw").addClass("d-none");
					$confirmDialog.modal('hide');
				}
				getDataWithdrawalRequest();
			}
		}
	});
});

recapPerVendorFunc();