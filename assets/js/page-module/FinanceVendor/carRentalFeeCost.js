if (carRentalFeeCostFunc == null){
	var carRentalFeeCostFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			setOptionHelper('optionVendorCarRecap', 'dataVendorCar');
			setOptionHelper('optionVendorCarDetail', 'dataVendorCar');
			setOptionHelper('optionVendorCarCost', 'dataVendorCar');
			setOptionHelper('editorCarCost-idCostType', 'dataCarCostType');
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
		case '#carRentalCostFeeRecap'	:	getDataRecapCarRentalCostFee(); break;
		case '#carRentalFeeDetail'		:	getDataDetailCarRentalFee(); break;
		case '#carRentalCostDetail'		:	getDataDetailCarRentalCost(); break;
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

carRentalFeeCostFunc();
