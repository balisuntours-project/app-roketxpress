var $confirmDialog= $('#modal-confirm-action');
if (carRentalFeeCostFunc == null){
	var carRentalFeeCostFunc	=	function(){
		$(document).ready(function () {
			localStorage.setItem('viewConfirmDialogValidateCarRentalCost', '1');
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			setOptionHelper('optionVendorCarRecap', 'dataVendorCar');
			setOptionHelper('optionVendorCarDetail', 'dataVendorCar');
			setOptionHelper('optionVendorCarCost', 'dataVendorCar');
			setOptionHelper('editorCarCost-idCostType', 'dataCarCostType');
			setOptionHelper('optionVendorAdditionalCost', 'dataVendorCar');
			setOptionHelper('optionDriverAdditionalCost', 'dataDriverCarRental');
			setOptionHelper('scheduleAdditionalCost-driver', 'dataDriverCarRental');
			setOptionHelper('editorAdditionalCost-optionAdditionalCostType', 'dataAdditionalCostType');
			getDataAllCarVendor();
			getDataRecapCarRentalCostFee();
			$("#recognitionDate").val('');
			onChangeDateInputFilter($("#recognitionDate"));
		});	
	}
}

function getDataAllCarVendor(){
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/carRentalFeeCost/getDataAllCarVendor",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(),
		beforeSend:function(){},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			$('#editorCarCost-idCarVendor').empty();
			if(response.status == 200){
				$.each(response.dataAllCarVendor, function(index, array) {
					$('#editorCarCost-idCarVendor').append($('<option></option>').val(array.IDCARVENDOR).html(array.CARDETAIL));
				});
			} else {
				$('#editorCarCost-idCarVendor').append($('<option></option>').val(0).html("Not Set"));
			}
		}
	});
}

$('a[data-toggle="tab"]').off('shown.bs.tab');
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
	var activeTabId	=	$(e.target).attr('href');
	
	switch(activeTabId){
		case '#carRentalCostFeeRecap'			:	getDataRecapCarRentalCostFee(); break;
		case '#carRentalFeeDetail'				:	getDataDetailCarRentalFee(); break;
		case '#carRentalCostDetail'				:	getDataDetailCarRentalCost(); break;
		case '#carRentalAdditionalCostDetail'	:	getDataCarRentalAdditionalCost(); break;
	}
});

$('#optionMonth, #optionYear, #optionVendorCarRecap').off('change');
$('#optionMonth, #optionYear, #optionVendorCarRecap').on('change', function(e) {
	getDataRecapCarRentalCostFee();
});

$('#searchKeywordRecap').off('keypress');
$("#searchKeywordRecap").on('keypress',function(e) {
    if(e.which == 13) {
        getDataRecapCarRentalCostFee();
    }
});

function generateDataTableRecap(page){
	getDataRecapCarRentalCostFee(page);
}

function getDataRecapCarRentalCostFee(page = 1){
	var $tableBody		=	$('#table-dataCarRentalCostFeeRecap > tbody'),
		columnNumber	=	$('#table-dataCarRentalCostFeeRecap > thead > tr > th').length,
		month			=	$('#optionMonth').val(),
		year			=	$('#optionYear').val(),
		idVendorCar		=	$('#optionVendorCarRecap').val(),
		searchKeyword	=	$('#searchKeywordRecap').val(),
		dataSend		=	{page:page, idVendorCar:idVendorCar, month:month, year:year, searchKeyword:searchKeyword};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/carRentalFeeCost/getRecapCarRentalCostFee",
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
				$('#excelDataCarRentalCostFeeRecap').addClass('d-none').off("click").attr("href", "");
			} else {
				if(response.urlExcelRecap != "") $('#excelDataCarRentalCostFeeRecap').removeClass('d-none').on("click").attr("href", response.urlExcelRecap);
				$.each(data, function(index, array) {
					let totalNominalFee		=	parseInt(array.TOTALNOMINALFEE),
						totalNominalCost	=	parseInt(array.TOTALNOMINALCOST),
						totalNominalCostFee	=	totalNominalFee - totalNominalCost;
					rows	+=	"<tr>"+
									"<td>"+array.VENDORNAME+"</td>"+
									"<td>"+array.DRIVERNAME+"</td>"+
									"<td>"+array.PLATNUMBER+"<br/>"+array.CARDETAIL+"<br/><small>"+array.DESCRIPTION+"</small></td>"+
									"<td align='right'>"+numberFormat(array.TOTALCARSCHEDULE)+"</td>"+
									"<td align='right'>"+numberFormat(totalNominalFee)+"</td>"+
									"<td align='right'>"+numberFormat(totalNominalCost)+"</td>"+
									"<td align='right'>"+numberFormat(totalNominalCostFee)+"</td>"+
								"</tr>";
							
				});	
			}
			generatePagination("tablePaginationDataCarRentalCostFeeRecap", page, response.result.pageTotal, "generateDataTableRecap");
			generateDataInfo("tableDataCountCarRentalCostFeeRecap", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);	
		}
	});
}

$('#optionVendorCarDetail, #scheduleStartDate, #scheduleEndDate').off('change');
$('#optionVendorCarDetail, #scheduleStartDate, #scheduleEndDate').on('change', function(e) {
	getDataDetailCarRentalFee();
});

$('#searchKeywordDetail').off('keypress');
$("#searchKeywordDetail").on('keypress',function(e) {
    if(e.which == 13) {
        getDataDetailCarRentalFee();
    }
});

function generateDataTableDetailFee(page){
	getDataDetailCarRentalFee(page);
}

function getDataDetailCarRentalFee(page = 1){
	var $tableBody		=	$('#table-dataCarRentalFeeDetail > tbody'),
		columnNumber	=	$('#table-dataCarRentalFeeDetail > thead > tr > th').length,
		idVendorCar		=	$('#optionVendorCarDetail').val(),
		startDate		=	$('#scheduleStartDate').val(),
		endDate			=	$('#scheduleEndDate').val(),
		searchKeyword	=	$('#searchKeywordDetail').val(),
		dataSend		=	{page:page, idVendorCar:idVendorCar, startDate:startDate, endDate:endDate, searchKeyword:searchKeyword};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/carRentalFeeCost/getDetailCarRentalFee",
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
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
				$('#excelDataCarRentalFeeDetail').addClass('d-none').off("click").attr("href", "");
			} else {
				if(response.urlExcelDetail != "") $('#excelDataCarRentalFeeDetail').removeClass('d-none').on("click").attr("href", response.urlExcelDetail);
				$.each(data, function(index, array) {
					var inputType	=	'';
					switch(array.INPUTTYPE){
						case "1"	:	inputType	=	'Mailbox'; break;
						case "2"	:	inputType	=	'Manual'; break;
					}
					rows			+=	"<tr>"+
											"<td>"+array.VENDORNAME+"</td>"+
											"<td>"+array.DRIVERNAME+"</td>"+
											"<td>"+array.SCHEDULEDATE+"</td>"+
											"<td>"+array.CARDETAILS+"</td>"+
											"<td>"+
												"["+inputType+"] "+array.SOURCENAME+"<br/>"+
												"<b>["+array.BOOKINGCODE+"] "+array.RESERVATIONTITLE+"</b><br/>"+
												array.CUSTOMERNAME+
											"</td>"+
											"<td>"+array.PRODUCTNAME+"<br/>Schedule by : "+array.USERINPUT+"<br/><br/>Notes :<br/>"+array.NOTES+"</td>"+
											"<td align='right'>"+numberFormat(array.NOMINAL)+"</td>"+
										"</tr>";
							
				});
				
			}
			generatePagination("tablePaginationDataCarRentalFeeDetail", page, response.result.pageTotal, "generateDataTableDetailFee");
			generateDataInfo("tableDataCountCarRentalFeeDetail", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);			
		}
	});
	
}

$('#optionVendorCarCost').off('change');
$('#optionVendorCarCost').on('change', function(e) {
	getDataDetailCarRentalCost();
});

$('#recognitionDate').off('change');
$('#recognitionDate').on('change', function(e) {
	onChangeDateInputFilter($(this));
	getDataDetailCarRentalCost();
});

$('#searchKeywordDetailCost').off('keypress');
$("#searchKeywordDetailCost").on('keypress',function(e) {
    if(e.which == 13) {
        getDataDetailCarRentalCost();
    }
});

function generateDataTableDetailCost(page){
	getDataDetailCarRentalCost(page);
}

function getDataDetailCarRentalCost(page = 1){
	var $tableBody		=	$('#table-dataCarRentalCostDetail > tbody'),
		columnNumber	=	$('#table-dataCarRentalCostDetail > thead > tr > th').length,
		idVendorCar		=	$('#optionVendorCarCost').val(),
		recognitionDate	=	$('#recognitionDate').val(),
		searchKeyword	=	$('#searchKeywordDetailCost').val(),
		dataSend		=	{page:page, idVendorCar:idVendorCar, recognitionDate:recognitionDate, searchKeyword:searchKeyword};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/carRentalFeeCost/getDetailCarRentalCost",
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
				$('#excelDataCarRentalCostDetail').addClass('d-none').off("click").attr("href", "");
			} else {
				if(response.urlExcelDetail != "") $('#excelDataCarRentalCostDetail').removeClass('d-none').on("click").attr("href", response.urlExcelDetail);
				$.each(data, function(index, array) {
					var durationHourOff	=	parseInt(array.DURATIONHOUROFF),
						badgeApproval	=	dayOffDetails	=	btnEditDetails	=	'';
					
					switch(parseInt(array.STATUSAPPROVAL)){
						case -1	:	badgeApproval	=	'<br/><br/><span class="badge badge-danger">Rejected</span>'; break;
						case 0	:	badgeApproval	=	'<br/><br/><span class="badge badge-warning">Waiting</span>';
									btnEditDetails	=	'<button class="button button-xs button-box button-primary" onclick="openEditorCarCost('+array.IDCARCOST+')"><i class="fa fa-pencil"></i></button>';
									break;
						case 1	:	badgeApproval	=	'<br/><br/><span class="badge badge-success">Approved</span>'; break;
					}
					
					if(durationHourOff > 0){
						dayOffDetails	=	durationHourOff+' Hours<br/>[Start] '+array.DATETIMEOFFSTART+'<br/>[End] '+array.DATETIMEOFFEND;
					}
					
					rows	+=	"<tr>"+
									"<td>"+array.CARDETAILS+"</td>"+
									"<td>"+
										"<span class='badge badge-outline badge-primary'>"+array.CARCOSTTYPE+"</span><br/>"+
										"<div class='order-details-customer-info'>"+
											"<ul>"+
												"<li> <span>Recognition</span> <span>"+array.DATECOSTRECOGNITION+"</span> </li>"+
												"<li> <span>Description</span> <span>"+array.DESCRIPTION+"</span> </li>"+
											"</ul>"+
										"</div>"+
									"</td>"+
									"<td>"+array.USERINPUT+"<br/>"+array.DATETIMEINPUT+"</td>"+
									"<td>"+array.USERAPPROVAL+"<br/>"+array.DATETIMEAPPROVAL+badgeApproval+"</td>"+
									"<td>"+
										"<a href='#' data-imgsrc='"+array.IMAGERECEIPT+"' class='zoomImage'>"+
											"<img src='"+array.IMAGERECEIPT+"' id='imageReceipt"+array.IDCARCOST+"' width='150px'>"+
										"</a>"+
									"</td>"+
									"<td>"+dayOffDetails+"</td>"+
									"<td align='right'>"+numberFormat(array.NOMINAL)+"</td>"+
									"<td align='right'>"+btnEditDetails+"</td>"+
								"</tr>";
							
				});
				
			}
			
			generatePagination("tablePaginationDataCarRentalCostDetail", page, response.result.pageTotal, "generateDataTableDetailCost");
			generateDataInfo("tableDataCountCarRentalCostDetail", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
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

function openEditorCarCost(idCarCost){
	var dataSend	=	{idCarCost:idCarCost};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/carRentalFeeCost/getDetailCarRentalCostById",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
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
			} else {
				let detailCarCost	=	response.detailCarCost;
				$("#editorCarCost-idCarVendor").val(detailCarCost.IDCARVENDOR).prop('disabled', true);
				$("#editorCarCost-recognitionDate").val(detailCarCost.DATECOSTRECOGNITION);
				$("#editorCarCost-idCostType").val(detailCarCost.IDCARCOSTTYPE);
				$("#editorCarCost-nominal").val(numberFormat(detailCarCost.NOMINAL));
				$("#editorCarCost-description").val(detailCarCost.DESCRIPTION);
				$('input[name="editorCarCost-approvalStatus"][value="'+detailCarCost.STATUSAPPROVAL+'"]').prop('checked', true);
				$("#editorCarCost-imageCostReceipt").attr("src", detailCarCost.IMAGERECEIPTURL).attr("height", "200px");
				$("#editorCarCost-costReceiptFileName").val(detailCarCost.IMAGERECEIPT);
				$("#editorCarCost-idCarCost").val(idCarCost);
				
				$("#editorCarCost-dayOffDurationStr").html(numberFormat(detailCarCost.DAYOFFDURATIONHOUR));
				$("#editorCarCost-dayOffDateTimeStart").html(detailCarCost.DAYOFFDATETIMESTART);
				$("#editorCarCost-dayOffDateTimeEnd").html(detailCarCost.DAYOFFDATETIMESTART);
				$('#modal-editorCarCost').modal('show');
			}
		}
	});
}

$('#modal-editorCarCost').off('show.bs.modal');
$('#modal-editorCarCost').on('show.bs.modal', function(e) {
	let triggerElement	=	$(e.relatedTarget),
		triggerId		=	triggerElement.attr('id');
		
	if(triggerId == "btnAddNewCarCost"){
		$("#editorCarCost-idCarVendor, #editorCarCost-idCostType").val("").prop('disabled', false);
		$("#editorCarCost-nominal, #editorCarCost-idCarCost").val(0);
		$("#editorCarCost-description, #editorCarCost-costReceiptFileName").val("");
		$('input[name="editorCarCost-approvalStatus"][value="0"]').prop('checked', true);
		$("#editorCarCost-imageCostReceipt").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "200px");
	}

	createUploaderCostReceipt();
});

function createUploaderCostReceipt(){
	$('.ajax-file-upload-container').remove();
	$("#editorCarCost-uploaderCostReceipt").uploadFile({
		url: baseURL+"financeVendor/carRentalFeeCost/uploadCostReceipt",
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
				$("#editorCarCost-imageCostReceipt").removeAttr('src').attr("src", data.urlCostReceipt);
				$("#editorCarCost-costReceiptFileName").val(data.costReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#content-editorCarCost').off('submit');
$('#content-editorCarCost').on('submit', function(e) {
	e.preventDefault();
	let idCarVendor			=	$("#editorCarCost-idCarVendor").val(),
		recognitionDate		=	$("#editorCarCost-recognitionDate").val(),
		idCostType			=	$("#editorCarCost-idCostType").val(),
		nominal				=	$("#editorCarCost-nominal").val(),
		description			=	$("#editorCarCost-description").val(),
		approvalStatus		=	$('input[name="editorCarCost-approvalStatus"]').val(),
		costReceiptFileName	=	$("#editorCarCost-costReceiptFileName").val(),
		idCarCost			=	$("#editorCarCost-idCarCost").val(),
		msgWarning			=	'';
	
	if(idCarVendor == '' || idCarVendor == 0 || idCarVendor == undefined || idCarVendor == null) msgWarning	=	'Please select a valid car';
	if(recognitionDate == '' || recognitionDate == '00-00-0000' || recognitionDate == undefined) msgWarning	=	'Please select a valid recognition date';
	if(idCostType == '' || idCostType == 0 || idCostType == undefined) msgWarning	=	'Please select a valid cost type';
	if(parseInt(nominal) <= 0 || nominal == undefined) msgWarning	=	'Please input a valid nominal';
	if(description == '' || description == undefined) msgWarning	=	'Please input a valid description';
	if(approvalStatus == '' || approvalStatus == undefined) msgWarning	=	'Please choose a valid approval status';
	if(costReceiptFileName == '' || costReceiptFileName == undefined) msgWarning	=	'Please upload a valid receipt document';
	
	if(msgWarning != ''){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html(msgWarning);
		});
		$('#modalWarning').modal('show');
	} else {
		var dataSend	=	{
				idCarVendor:idCarVendor,
				recognitionDate:recognitionDate,
				idCostType:idCostType,
				nominal:nominal,
				description:description,
				approvalStatus:approvalStatus,
				costReceiptFileName:costReceiptFileName,
				idCarCost:idCarCost
			};
			
		$.ajax({
			type: 'POST',
			url: baseURL+"financeVendor/carRentalFeeCost/saveCarCost",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#content-editorCarCost :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#content-editorCarCost :input").attr("disabled", false);
				if(idCarCost != 0) $("#editorCarCost-idCarVendor").prop("disabled", false);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					$('#modal-editorCarCost').modal('hide');
					getDataDetailCarRentalCost();
				}
			}
		});
	}
});

$('#optionVendorAdditionalCost, #optionDriverAdditionalCost, #startDateAdditionalCost, #endDateAdditionalCost').off('change');
$('#optionVendorAdditionalCost, #optionDriverAdditionalCost, #startDateAdditionalCost, #endDateAdditionalCost').on('change', function (e) {
	getDataCarRentalAdditionalCost();
});

$('#cbRequestOnlyAdditionalCost').off('click');
$("#cbRequestOnlyAdditionalCost").on('click', function (e) {
	var checked = $("#cbRequestOnlyAdditionalCost").is(':checked');

	if (checked) {
		$("#optionVendorAdditionalCost, #optionDriverAdditionalCost, #startDateAdditionalCost, #endDateAdditionalCost").attr("disabled", true);
	} else {
		$("#optionVendorAdditionalCost, #optionDriverAdditionalCost, #startDateAdditionalCost, #endDateAdditionalCost").attr("disabled", false);
	}

	getDataCarRentalAdditionalCost();
});

$('#searchKeywordAdditionalCost').off('keypress');
$("#searchKeywordAdditionalCost").on('keypress', function (e) {
	if (e.which == 13) {
		getDataCarRentalAdditionalCost();
	}
});

function generateDataDetailCarRentalAdditionalCost(page) {
	getDataCarRentalAdditionalCost(page);
}

function getDataCarRentalAdditionalCost(page = 1) {
	var $tableBody		=	$('#table-dataCarRentalAdditionalCost > tbody'),
		columnNumber	=	$('#table-dataCarRentalAdditionalCost > thead > tr > th').length,
		idVendorCar		=	$('#optionVendorAdditionalCost').val(),
		idDriver		=	$('#optionDriverAdditionalCost').val(),
		startDate		=	$('#startDateAdditionalCost').val(),
		endDate			=	$('#endDateAdditionalCost').val(),
		searchKeyword	=	$('#searchKeywordAdditionalCost').val(),
		viewRequestOnly	=	$("#cbRequestOnlyAdditionalCost").is(':checked'),
		dataSend		=	{
			page: page,
			idVendorCar: idVendorCar,
			idDriver: idDriver,
			startDate: startDate,
			endDate: endDate,
			searchKeyword: searchKeyword,
			viewRequestOnly: viewRequestOnly
		};

	$.ajax({
		type: 'POST',
		url: baseURL + "financeVendor/carRentalFeeCost/getDataCarRentalAdditionalCost",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$tableBody.html("<tr><td colspan='" + columnNumber + "'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success: function (response) {
			NProgress.done();
			setUserToken(response);

			var data	= response.result.data,
				rows 	= "";

			if (data.length === 0) {
				rows = "<tr><td colspan='" + columnNumber + "' align='center'><center>No data found</center></td></tr>";
			} else {
				$.each(data, function (index, array) {
					var statusApproval	=	parseInt(array.STATUSAPPROVAL),
						badgeStatus		=	"";
					switch(statusApproval){
						case 1	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>'; break;
						case -1	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>'; break;
						case 0	:
						default	:	badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>'; break;
					}

					var driverName		=	array.DRIVERNAME != null && array.DRIVERNAME != "" ? array.DRIVERNAME : "-",
						vehicleDetail	=	array.BRAND+" "+array.MODEL+" ["+array.PLATNUMBER+"]",
						costType		=	array.ADDITIONALCOSTTYPE != null && array.ADDITIONALCOSTTYPE != "" ? array.ADDITIONALCOSTTYPE : "-",
						costNominal		=	array.NOMINAL != null && array.NOMINAL != "" ? numberFormat(array.NOMINAL) : "-",
						costDescription	=	array.DESCRIPTION != null && array.DESCRIPTION != "" ? array.DESCRIPTION : "-",
						imageReceiptUrl	=	array.IMAGERECEIPT,
						btnValidate		=	statusApproval != 0 ? "" :
											'<button class="button button-box button-primary button-sm" onclick="setValidateAdditionalCost('+array.IDRESERVATIONADDITIONALCOST+', 1)" >'+
												'<i class="fa fa-check"></i>'+
											'</button>',
						btnIgnore		=	statusApproval != 0 ? "" :
											'<button class="button button-box button-warning button-sm" onclick="setValidateAdditionalCost('+array.IDRESERVATIONADDITIONALCOST+', -1)" >'+
												'<i class="fa fa-times"></i>'+
											'</button>';
					rows	+=	"<tr class='trAdditionalCost' "+
									"data-id='"+array.IDRESERVATIONADDITIONALCOST+"' "+
									"data-status='"+statusApproval+"' "+
									"data-driverName='"+driverName+"' "+
									"data-vehicleDetail='"+vehicleDetail+"' "+
									"data-costType='"+costType+"' "+
									"data-costNominal='"+costNominal+"' "+
									"data-costDescription='"+costDescription+"' "+
									"data-imageReceiptUrl='"+imageReceiptUrl+"' "+
								">"+
									"<td class='containerTextValidate'>"+badgeStatus+"<br/>"+array.DATETIMEINPUT+"</td>"+
									"<td>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
										"<b>"+array.PRODUCTNAME+"</b><br/><br/>"+
										"Cust : "+array.CUSTOMERNAME+
									"</td>"+
									"<td>"+
										"<b>"+array.VENDORNAME+"</b><br/>"+
										"<b>"+array.CARTYPE+"</b><br/><br/>"+
										"Car : "+vehicleDetail+
									"</td>"+
									"<td>"+
										"<b>Driver : "+driverName+"</b><br/>"+
										"<b>"+costType+"</b><br/><br/>"+
										"Description : "+costDescription+
									"</td>"+
									"<td align='right'>"+costNominal+"</td>"+
									"<td>"+
										"<a href='#' data-imgsrc='"+imageReceiptUrl+"' class='zoomImage'>"+
											"<img src='"+imageReceiptUrl+"' width='150px'>"+
										"</a>"+
									"</td>"+
									"<td align='center'>"+
										btnValidate+"<br>"+btnIgnore+
									"</td>"+
								"</tr>";
				});

			}

			generatePagination("tablePaginationCarRentalAdditionalCost", page, response.result.pageTotal, "generateDataDetailCarRentalAdditionalCost");
			generateDataInfo("tableDataCountCarRentalAdditionalCost", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);

			$('.zoomImage').off('click');
			$(".zoomImage").on("click", function () {
				var imgSrc = $(this).attr('data-imgSrc');
				$('#zoomReceiptImage').attr('src', imgSrc);
				$('#modal-zoomReceiptImage').modal('show');
			});
		}
	});
}

function setValidateAdditionalCost(idAdditionalCost, status){
	var $trAdditionalCost	=	$(".trAdditionalCost[data-id='"+idAdditionalCost+"']"),
		driverName			=	$trAdditionalCost.attr('data-driverName'),
		vehicleDetail		=	$trAdditionalCost.attr('data-vehicleDetail'),
		costType			=	$trAdditionalCost.attr('data-costType'),
		costNominal			=	$trAdditionalCost.attr('data-costNominal'),
		costDescription		=	$trAdditionalCost.attr('data-costDescription'),
		imageReceiptUrl		=	$trAdditionalCost.attr('data-imageReceiptUrl'),
		viewConfirmDialog	=	localStorage.getItem('viewConfirmDialogValidateCarRentalCost'),
		txtValidateStatus	=	status == 1 ? "Approved" : "Rejected";
	
	if(viewConfirmDialog == '1'){
		$("#confirmValidateAdditionalCost-driverName").html(driverName);
		$("#confirmValidateAdditionalCost-vehicleDetail").html(vehicleDetail);
		$("#confirmValidateAdditionalCost-costType").html(costType);
		$("#confirmValidateAdditionalCost-description").html(costDescription);
		$("#confirmValidateAdditionalCost-nominal").html(costNominal);

		$("#confirmValidateAdditionalCost-textValidateStatus").html(txtValidateStatus);
		$('#confirmValidateAdditionalCost-imageReceipt').attr('src', imageReceiptUrl);
		$("#confirmValidateAdditionalCost-confirmBtn").attr('data-idAdditionalCost', idAdditionalCost).attr('data-status', status);
		$('#modal-confirmValidateAdditionalCost').modal('show');
	} else {
		submitValidateAdditionalCost(idAdditionalCost, status);
	}
}

$('#confirmValidateAdditionalCost-confirmBtn').off('click');
$('#confirmValidateAdditionalCost-confirmBtn').on('click', function(e) {
	e.preventDefault();
	if($('#disableConfirm').is(":checked")) localStorage.setItem('viewConfirmDialogValidateCarRentalCost', '0');
	submitValidateAdditionalCost($("#confirmValidateAdditionalCost-confirmBtn").attr('data-idAdditionalCost'), $("#confirmValidateAdditionalCost-confirmBtn").attr('data-status'));
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
				var $trAdditionalCost	=	$(".trAdditionalCost[data-id='" + idAdditionalCost + "']"),
					badgeStatus			=	"",
					status				=	parseInt(response.statusApproval),
					idAdditionalCost	=	response.idAdditionalCost;

				switch(status){
					case 1	:	badgeStatus	=	'<span class="badge badge-pill badge-primary">Approved</span>'; break;
					case -1	:	badgeStatus	=	'<span class="badge badge-pill badge-danger">Rejected</span>'; break;
					case 0	:
					default	:	badgeStatus	=	'<span class="badge badge-pill badge-warning">Waiting</span>'; break;
				}

				$trAdditionalCost.find('td:first').find('span.badge').remove().prepend(badgeStatus);
				$trAdditionalCost.find('td:last').find('button.button-box').remove();
				
				var toastType	=	status == 1 ? "success" : "warning";
				toastr[toastType](response.msg);
				getDataCarRentalAdditionalCost();
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#modal-scheduleAdditionalCost').off('show.bs.modal');
$('#modal-scheduleAdditionalCost').on('show.bs.modal', function(event) {
	$("#scheduleAdditionalCost-keyword").val("");
	$("#containerSelectReservationResult").html(
		'<div class="col-sm-12 text-center mx-auto my-auto">'+
			'<h2><i class="fa fa-list-alt text-warning"></i></h2>'+
			'<b class="text-warning">Results goes here</b>'+
		'</div>'
	);
	searchListScheduleAdditionalCost();

	$('#scheduleAdditionalCost-form').off('submit');
	$('#scheduleAdditionalCost-form').on('submit', function(e) {
		e.preventDefault();
		searchListScheduleAdditionalCost();
	});

	$('#scheduleAdditionalCost-keyword').off('keydown');
	$('#scheduleAdditionalCost-keyword').on('keydown', function(e) {
		if(e.which === 13){
			searchListScheduleAdditionalCost();
		}
	});

	$('#scheduleAdditionalCost-date, #scheduleAdditionalCost-type, #scheduleAdditionalCost-driver').off('change');
	$('#scheduleAdditionalCost-date, #scheduleAdditionalCost-type, #scheduleAdditionalCost-driver').on('change', function(e) {
		e.preventDefault();
		searchListScheduleAdditionalCost();
	});
});

function searchListScheduleAdditionalCost(){
	var idDriver	= $("#scheduleAdditionalCost-driver").val(),
		idJobType	= $("#scheduleAdditionalCost-jobType").val(),
		scheduleDate= $("#scheduleAdditionalCost-date").val(),
		keyword		= $("#scheduleAdditionalCost-keyword").val(),
		dataSend	= {
			idDriver:idDriver,
			idJobType:idJobType,
			scheduleDate:scheduleDate,
			keyword:keyword
		};
	$.ajax({
		type: 'POST',
		url: baseURL+"financeVendor/carRentalFeeCost/getDataScheduleAdditionalCost",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#scheduleAdditionalCost-form :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#scheduleAdditionalCost-form :input").attr("disabled", false);

			if(response.status == 200){
				var scheduleList	=	response.scheduleList,
					scheduleRows	=	"";
					
				$("#scheduleAdditionalCost-driver").select2({
					dropdownParent: $('#modal-scheduleAdditionalCost')
				});

				$.each(scheduleList, function(index, array) {
					var idReservationDetails= array.IDRESERVATIONDETAILS,
						badgeJobType		=	parseInt(array.JOBTYPE) == 1 ? '<span class="badge badge-primary">Drop Off</span>' : '<span class="badge badge-success">Pick Up</span>';
					scheduleRows			+=	'<div class="col-sm-12 pb-1 mb-5 border-bottom rowScheduleDetail"'+
													' data-idReservationDetails="'+idReservationDetails+'"'+
													' data-idDriver="'+array.IDDRIVER+'"'+
													' data-bookingCode="'+array.BOOKINGCODE+'"'+
													' data-reservationTitle="'+array.RESERVATIONTITLE+'"'+
													' data-scheduleDateTime="'+array.SCHEDULEDATETIME+'"'+
													' data-customerName="'+array.CUSTOMERNAME+'"'+
													' data-driverName="'+array.DRIVERNAME+'"'+
													' data-carDetail="'+array.CARDETAIL+'"'+
													'">'+
														'<div class="row pt-10 pb-1">'+
															'<div class="col-sm-12">'+
																badgeJobType+' <span class="badge badge-outline badge-primary"><b>'+array.BOOKINGCODE+'</b> - <b class="text-primary">'+array.SCHEDULEDATETIME+'</b></span>'+
																'<button type="button" class="button button-sm pull-right" onclick="generateAdditionalCostDataForm('+idReservationDetails+')"><span><i class="fa fa-pencil"></i>Choose</span></button>'+
															'</div>'+
															'<div class="col-sm-12">'+
																'<b>'+array.RESERVATIONTITLE+'</b><br/>'+
																'<div class="order-details-customer-info">'+
																	'<ul>'+
																		'<li> <span>Customer</span> <span>'+array.CUSTOMERNAME+'</span> </li>'+
																		'<li> <span>Driver</span> <span>'+array.DRIVERNAME+'</span> </li>'+
																		'<li> <span>Car Detail</span> <span>'+array.CARDETAIL+'</span> </li>'+
																		'<li> <span>Location</span> <span>'+array.LOCATION+'</span> </li>'+
																	'</ul>'+
																'</div>'+
															'</div>'+
														'</div>'+
													'</div>';
							
				});
				
				$("#scheduleAdditionalCost-containerResult").html(scheduleRows);
			} else {
				$("#scheduleAdditionalCost-containerResult").html(
					'<div class="col-sm-12 text-center mx-auto my-auto">'+
						'<h2><i class="fa fa-search-minus text-danger"></i></h2>'+
						'<b class="text-danger">No active schedules found based on the filter you choose</b>'+
					'</div>'
				);
			}
		}
	});
}

function generateAdditionalCostDataForm(idReservationDetails){
	var elemRowReservationDetail	=	$(".rowScheduleDetail[data-idReservationDetails='"+idReservationDetails+"']"),
		idDriver					=	elemRowReservationDetail.attr('data-idDriver');
	
	$('#editorAdditionalCost-idReservationDetail').val(idReservationDetails);
	$('#editorAdditionalCost-idDriver').val(idDriver);
	$('#editorAdditionalCost-bookingCode').html(elemRowReservationDetail.attr('data-bookingCode'));
	$('#editorAdditionalCost-reservationTitle').html(elemRowReservationDetail.attr('data-reservationTitle'));
	$('#editorAdditionalCost-scheduleDateTime').html(elemRowReservationDetail.attr('data-scheduleDateTime'));
	$('#editorAdditionalCost-customerName').html(elemRowReservationDetail.attr('data-customerName'));
	$('#editorAdditionalCost-driverName').html(elemRowReservationDetail.attr('data-driverName'));
	$('#editorAdditionalCost-carDetail').html(elemRowReservationDetail.attr('data-carDetail'));
	$("#editorAdditionalCost-imageAdditionalCostReceipt").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "200px");
	$("#editorAdditionalCost-additionalCostReceiptFileName").val("");
	$('#editorAdditionalCost-nominal').val(0);
	$('#editorAdditionalCost-description').val("");
	createUploaderAdditionalCostReceipt(idDriver);
	
	$('#modal-scheduleAdditionalCost').modal('hide');
	$('#modal-editorAdditionalCost').modal('show');
}

function createUploaderAdditionalCostReceipt(idDriver) {
	idDriver = idDriver == "" ? 0 : idDriver;
	$('.ajax-file-upload-container').remove();
	$("#editorAdditionalCost-uploaderAdditionalCostReceipt").uploadFile({
		url: baseURL + "financeDriver/additionalCost/uploadTransferReceipt/" + idDriver,
		multiple: false,
		dragDrop: false,
		onSuccess: function (files, data, xhr, pd) {
			if (data.status != 200) {
				$('#modalWarning').on('show.bs.modal', function () {
					$('#modalWarningBody').html(data.msg);
				});
				$('#modalWarning').modal('show');
			} else {
				$('.ajax-file-upload-container').html("");
				$("#editorAdditionalCost-imageAdditionalCostReceipt").removeAttr('src').attr("src", data.urlTransferReceipt);
				$("#editorAdditionalCost-additionalCostReceiptFileName").val(data.transferReceiptFileName);
			}
		}
	});
	$(".ajax-file-upload-container").remove();
}

$('#form-editorAdditionalCost').off('submit');
$('#form-editorAdditionalCost').on('submit', function(e) {
	e.preventDefault();
	var additionalCostReceiptFileName = $("#editorAdditionalCost-additionalCostReceiptFileName").val();
	if(additionalCostReceiptFileName == ""){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please upload receipt first");
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
		var idDriver			=	$('#editorAdditionalCost-idDriver').val(),
			idReservationDetail	=	$('#editorAdditionalCost-idReservationDetail').val(),
			idCostType			=	$('#editorAdditionalCost-optionAdditionalCostType').val(),
			nominal				=	$('#editorAdditionalCost-nominal').val(),
			description			=	$('#editorAdditionalCost-description').val(),
			receiptFileName		=	$('#editorAdditionalCost-additionalCostReceiptFileName').val(),
			dataSend			=	{
				idDriver:idDriver,
				idReservationDetail:idReservationDetail,
				idCostType:idCostType,
				nominal:nominal,
				description:description,
				receiptFileName:receiptFileName
			};
		$("#form-editorAdditionalCost :input").attr("disabled", true);
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
			$("#form-editorAdditionalCost :input").attr("disabled", false);

			if(response.status == 200){
				$("#modal-editorAdditionalCost").modal('hide');
				getDataCarRentalAdditionalCost();
			}
		}
	});
});

carRentalFeeCostFunc();