if (mailboxFunc == null){
	var mailboxFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('reservationTypeFilter', 'dataReservationType', idReservationTypeAdmin);
			setOptionHelper('optionSource', 'dataSource');
			setOptionHelper('optionPickUpArea', 'dataArea');
			$("#reservationDateFilter").val("");
			if(localStorage.getItem('OSNotificationData') === null || localStorage.getItem('OSNotificationData') === undefined){
				getDataMailbox();
			} else {
				var OSNotificationData	=	JSON.parse(localStorage.getItem('OSNotificationData'));
				var OSNotifType			=	OSNotificationData.type;
				if(OSNotifType == "mailbox"){
					idMailbox			=	OSNotificationData.idMailbox;
					getDetailMail(idMailbox);
					localStorage.removeItem("OSNotificationData");
				} else {
					getDataMailbox();			
				}
			}		
		});	
	}
}

$('#reservationTypeFilter, #optionSource, #startDate, #endDate, #reservationDateFilter').off('change');
$('#reservationTypeFilter, #optionSource, #startDate, #endDate, #reservationDateFilter').on('change', function(e) {
	getDataMailbox();
});

$('#optionMailStatus').off('change');
$('#optionMailStatus').on('change', function(e) {
	var mailStatus	=	$(this).val();
	if(mailStatus == 0 && mailStatus !=  ""){
		$('#startDate, #endDate').prop('disabled', true);
	} else {
		$('#startDate, #endDate').prop('disabled', false);
	}
	getDataMailbox();
});

$('#searchKeyword').off('keypress');
$("#searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
        getDataMailbox();
    }
});

function generateDataTable(page){
	getDataMailbox(page);
}

function getDataMailbox(page = 1){
	var $tableBody			=	$('#table-dataMailbox > tbody'),
		columnNumber		=	$('#table-dataMailbox > thead > tr > th').length,
		status				=	$('#optionMailStatus').val(),
		idReservationType	=	$('#reservationTypeFilter').val(),
		idSource			=	$('#optionSource').val(),
		startDate			=	$('#startDate').val(),
		endDate				=	$('#endDate').val(),
		reservationDate		=	$('#reservationDateFilter').val(),
		searchKeyword		=	$('#searchKeyword').val(),
		dataSend			=	{page:page, status:status, idSource:idSource, idReservationType:idReservationType, startDate:startDate, endDate:endDate, reservationDate:reservationDate, searchKeyword:searchKeyword};
	$.ajax({
		type: 'POST',
		url: baseURL+"mailbox/getDataMailbox",
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
				rows			=	"",
				arrIdMailBox	=	[];
			
			if(data.length === 0){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				$.each(data, function(index, array) {
					var badgeStatus	=	'<span class="badge badge-warning">Unprocessed</span>';
					switch(array.STATUS){
						case "0"	:	badgeStatus	=	'<span class="badge badge-warning">Unprocessed</span>'; break;
						case "1"	:	badgeStatus	=	'<span class="badge badge-success">Processed</span>'; break;
						default		:	badgeStatus	=	'<span class="badge badge-warning">Unprocessed</span>'; break;
					}
					arrIdMailBox.push(array.IDMAILBOX);
					
					rows	+=	"<tr onclick='getDetailMail("+array.IDMAILBOX+")'>"+
									"<td>"+array.RESERVATIONTYPE+"</td>"+
									"<td>"+array.SOURCE+"</td>"+
									"<td align='center'>"+array.DATETIMEMAIL+"</td>"+
									"<td align='center'>"+array.RESERVATIONDATE+"</td>"+
									"<td>"+array.MAILSUBJECT+"</td>"+
									"<td>"+badgeStatus+"</td>"+
								"</tr>";
				});
			}

			arrIdMailBox	=	arrIdMailBox.length <= 0 ? "" : arrIdMailBox.toString();
			$("#arrIdMailBox").val(arrIdMailBox);
			generatePagination("tablePaginationMailbox", page, response.result.pageTotal);
			generateDataInfo("tableDataCountMailbox", response.result.dataStart, response.result.dataEnd, response.result.dataTotal);
			$tableBody.html(rows);
		}
	});
}

$('#selfDriveStatus').off('change');
$('#selfDriveStatus').on('change', function(e) {
	setDateTimeEndDisableStatus();
});

function setDateTimeEndDisableStatus(){
	var selfDriveStatus	=	$('#selfDriveStatus').val();
	if(selfDriveStatus == 0 || selfDriveStatus == null){
		$('#reservationDateEnd, #reservationHourEnd, #reservationMinuteEnd').prop('disabled', true);
	} else {
		$('#reservationDateEnd, #reservationHourEnd, #reservationMinuteEnd').prop('disabled', false);
	}
	calculateReservationDateTimeEnd();
}

$('#durationOfDay, #reservationDate, #reservationHour, #reservationMinute').off('change');
$('#durationOfDay, #reservationDate, #reservationHour, #reservationMinute').on('change', function(e) {
	calculateReservationDateTimeEnd();
});

function calculateReservationDateTimeEnd(){
	var selfDriveStatus			=	parseInt($('#selfDriveStatus').val()),
		durationOfDay			=	parseInt($('#durationOfDay').val()),
		reservationDate			=	$('#reservationDate').val(),
		reservationHour			=	$('#reservationHour').val(),
		reservationMinute		=	$('#reservationMinute').val(),
		reservationDateTimeStr	=	reservationDate+' '+reservationHour+':'+reservationMinute,
		dayAdditiionNumber		=	durationOfDay == 0 ? 0 : durationOfDay - 1;
		
	if(selfDriveStatus == 1) dayAdditiionNumber	= durationOfDay == 0 ? 1 : durationOfDay;

	var reservationDateTimeMmnt	=	moment(reservationDateTimeStr, "DD-MM-YYYY HH:mm");
	reservationDateTimeMmnt.add(dayAdditiionNumber, 'days');
	var reservationDateEnd		=	reservationDateTimeMmnt.format("DD-MM-YYYY"),
		reservationHourEnd		=	reservationDateTimeMmnt.format("HH"),
		reservationMinuteEnd	=	reservationDateTimeMmnt.format("mm");
	
	$('#reservationDateEnd').val(reservationDateEnd);
	$('#reservationHourEnd').val(reservationHourEnd);
	$('#reservationMinuteEnd').val(reservationMinuteEnd);
}

function getDetailMail(idMailbox, toggleSlide=true){
	var dataSend		=	{idMailbox:idMailbox};
	$.ajax({
		type: 'POST',
		url: baseURL+"mailbox/getDetailMailbox",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#mailPreview").html("<center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
			$("#mailDetailSubject").html('-');
			$("#specialRequest").val('-');
			$('#window-loader').modal('show');
			if(toggleSlide) toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$('#window-loader').modal('hide');
			
			if(response.status != 200){
				$("#mailPreview").html("<center>Mail preview not available</center>");
				if(toggleSlide) toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
			} else {
				var detailData		=	response.detailData,
					specialCasePrice=	response.specialCasePrice;
				
				setOptionHelper('reservationType', 'dataReservationType');
				setOptionHelper("reservationHour", "dataHours", detailData.RESERVATIONHOUR, false, false);
				setOptionHelper("reservationMinute", "dataMinutes", detailData.RESERVATIONMINUTE, false, false);
				setOptionHelper("reservationHourEnd", "dataHours", detailData.RESERVATIONHOUREND, false, false);
				setOptionHelper("reservationMinuteEnd", "dataMinutes", detailData.RESERVATIONMINUTEEND, false, false);
				$("#mailPreview").html('<iframe id="iFrameMailPreview" width="100%" height="1480" padding="8px" src="'+detailData.URLPREVIEW+'" frameborder="0"></iframe>');
				$("#mailDetailSubject").html(detailData.MAILSUBJECT+" ["+detailData.DATETIMEMAIL+"]");

				$("#reservationType").val(detailData.IDRESERVATIONTYPE);
				$("#reservationTitle").val(detailData.RESERVATIONTITLE);
				$("#detailsProductURL").val(detailData.URLDETAILPRODUCT);
				$("#durationOfDay").val(detailData.DURATIONOFDAY);
				$("#duplicateNumber").val(detailData.DUPLICATENUMBER);
				$("#reservationDate").val(detailData.RESERVATIONDATE);
				$("#reservationDateEnd").val(detailData.RESERVATIONDATEEND);
				$("#customerName").val(detailData.CUSTOMERNAME);
				$("#customerContact").val(detailData.CUSTOMERCONTACT);
				$("#customerEmail").val(detailData.CUSTOMEREMAIL);
				$("#hotelName").val(detailData.HOTELNAME);
				$("#optionPickUpArea").val(detailData.IDAREA);
				$("#pickUpLocation").val(detailData.PICKUPLOCATION);
				$("#pickUpLocationUrl").val(detailData.URLPICKUPLOCATION);
				$("#dropOffLocation").val(detailData.DROPOFFLOCATION);
				$("#numberOfAdult").val(detailData.NUMBEROFADULT);
				$("#numberOfChild").val(detailData.NUMBEROFCHILD);
				$("#numberOfInfant").val(detailData.NUMBEROFINFANT);
				$("#bookingCode").val(detailData.BOOKINGCODE);
				$("#reservationPriceType").val(detailData.INCOMEAMOUNTCURRENCY);
				$("#reservationPriceInteger").val(detailData.INCOMEAMOUNTINTEGER);
				$("#reservationPriceDecimal").val(detailData.INCOMEAMOUNTDECIMAL);
				$("#tourPlan").val(detailData.TOURPLAN);
				$("#remark").val(detailData.REMARK);
				
				$("#totalAdditionalReconfirmationInfo").html('0');
				$("#additionalConfirmationInfo-listAdditionalInfo").html("").addClass('d-none');
				$("#noAdditionalInfo").removeClass('d-none');
				if(detailData.ADDITIONALINFOLIST != "" && detailData.ADDITIONALINFOLIST != null){
					var additionalInfoList	=	JSON.parse(detailData.ADDITIONALINFOLIST),
						elemAdditionalInfo	=	'';
						
					if(additionalInfoList.length > 0){
						for(var iAdditional=0; iAdditional<additionalInfoList.length; iAdditional++){
							var informationDescription	=	additionalInfoList[iAdditional][0],
								informationContent		=	additionalInfoList[iAdditional][1],
								textLinkType			=	informationContent.includes("</a>"),
								informationContent		=	informationContent.includes("</a>") ? $(additionalInfoList[iAdditional][1]).attr('href') : informationContent;
							elemAdditionalInfo			+=	generateElemAdditionalInfo(informationDescription, informationContent, textLinkType, false);
						}
							
						$("#totalAdditionalReconfirmationInfo").html(additionalInfoList.length);
						$("#additionalConfirmationInfo-listAdditionalInfo").html(elemAdditionalInfo).removeClass('d-none');
						$("#noAdditionalInfo").addClass('d-none');
					} else {
						$("#additionalConfirmationInfo-listAdditionalInfo").html("").addClass('d-none');
						$("#noAdditionalInfo").removeClass('d-none');
					}
				}
				
				if(specialCasePrice == ""){
					$("#specialCasePriceAlert").addClass('d-none');
					$("#specialCasePriceText").html('');
				} else {
					$("#specialCasePriceAlert").removeClass('d-none');
					$("#specialCasePriceText").html(specialCasePrice);					
				}
				
				if(detailData.STATUS != 0){
					$("#reservationForm :input").prop("disabled", true);
					$("#selfDriveStatus").val(detailData.ISSELFDRIVE);
					$("#btnAdditionalInfo").prop("disabled", false);
					$("#btnSaveReservation").addClass("d-none");
					$("#userValidator").html(detailData.USEREDITOR);
					$("#dateTimeValidation").html(detailData.DATETIMEVALIDATION);
					$("#validationInfo").removeClass("d-none");
					$("#btnAddAdditionalInfo").addClass('d-none');
					$("#idMailbox, #idSource").val(0);
				} else {
					$("#reservationForm :input").prop("disabled", false);
					$("#selfDriveStatus").val("");
					$("#btnSaveReservation").removeClass("d-none");
					$("#userValidator, #dateTimeValidation").html("-");
					$("#validationInfo").addClass("d-none");
					$("#btnAddAdditionalInfo").removeClass('d-none');
					$("#idMailbox").val(detailData.IDMAILBOX);
					$("#idSource").val(detailData.IDSOURCE);
					setDateTimeEndDisableStatus();
				}
				
				$('#iFrameMailPreview').off('load');
				$('#iFrameMailPreview').on('load', function(e) {
					$("#iFrameMailPreview").contents().find('a').off('click');
					$("#iFrameMailPreview").contents().find('a').on('click', function(e) {
						e.preventDefault();
					});
				});
				
				var arrIdMailBox	=	$("#arrIdMailBox").val(),
					arrIdMailBox	=	arrIdMailBox.split(","),
					indexActiveData	=	arrIdMailBox.indexOf(idMailbox+""),
					nextIndex		=	indexActiveData + 1,
					previousIndex	=	indexActiveData - 1;
					
				if(indexActiveData == -1 || arrIdMailBox.length == 1){
					$("#btnPreviousMail, #btnNextMail").addClass("d-none").off('click');
				} else if(indexActiveData == 0) {
					$("#btnPreviousMail").addClass("d-none").off('click');
					$("#btnNextMail").removeClass("d-none").off('click');
					$('#btnNextMail').on('click', function(e) {
						getDetailMail(arrIdMailBox[nextIndex], false);
					});
				} else if(nextIndex == arrIdMailBox.length) {
					$("#btnNextMail").addClass("d-none").off('click');
					$("#btnPreviousMail").removeClass("d-none").off('click');
					$('#btnPreviousMail').on('click', function(e) {
						getDetailMail(arrIdMailBox[previousIndex], false);
					});
				} else {
					$("#btnPreviousMail, #btnNextMail").removeClass("d-none").off('click');
					
					$('#btnPreviousMail').on('click', function(e) {
						getDetailMail(arrIdMailBox[previousIndex], false);
					});
					$('#btnNextMail').on('click', function(e) {
						getDetailMail(arrIdMailBox[nextIndex], false);
					});
				}
			}
		}
	});
}

$('#modal-additionalConfirmationInfo').off('show.bs.modal');
$('#modal-additionalConfirmationInfo').on('show.bs.modal', function(event) {
	$("#btnCancelAdditionalInfo").click();
});

$('#btnCloseComposer').off('click');
$('#btnCloseComposer').on('click', function(e) {
	getDataMailbox();
	toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
});

$('#btnSaveReservation').off('click');
$('#btnSaveReservation').on('click', function(e) {
	var selfDriveStatus	=	$("#selfDriveStatus").val(),
		dataForm		=	$("#reservationForm :input").serializeArray(),
		additionalInfo	=	[],
		dataSend		=	{};
		
	if(selfDriveStatus == "" || selfDriveStatus == null){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please select <b>self drive status</b> before save");
		});
		$('#modalWarning').modal('show');
	} else {
		$.each(dataForm, function() {
			dataSend[this.name] = this.value;
		});
		
		$('.elemAdditionalInfo').each(function(i){
			var description	=	$(this).attr('data-description'),
				content		=	$(this).attr('data-content'),
				textLinkType=	$(this).attr('data-textLinkType');
			additionalInfo.push([textLinkType, description, content]);
		});
		dataSend['additionalInfo'] = additionalInfo;
		
		$.ajax({
			type: 'POST',
			url: baseURL+"mailbox/saveReservation",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#reservationForm :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#reservationForm :input").attr("disabled", false);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
					getDataMailbox(1);
				}
			}
		});
	}
});

$("#btnAddAdditionalInfo").off('click');
$('#btnAddAdditionalInfo').on('click', function(e) {
	e.preventDefault();
	$("#btnAddAdditionalInfo, #additionalConfirmationInfo-listAdditionalInfo, #warningMessage-description, #warningMessage-informationContent").addClass('d-none');
	if($("#noAdditionalInfo").length > 0) $("#noAdditionalInfo").addClass('d-none');
	$("#additionalConfirmationInfo-formAdditionalInfo").removeClass('d-none');
	$("#additionalConfirmationInfo-description, #additionalConfirmationInfo-informationContent").val('');													
});
											
$("#btnCancelAdditionalInfo").off('click');
$('#btnCancelAdditionalInfo').on('click', function(e) {
	e.preventDefault();
	$("#btnAddAdditionalInfo, #additionalConfirmationInfo-listAdditionalInfo").removeClass('d-none');
	if($("#additionalConfirmationInfo-listAdditionalInfo").html() == '') $("#noAdditionalInfo").removeClass('d-none');
	$("#additionalConfirmationInfo-formAdditionalInfo").addClass('d-none');													
});

$('#btnSaveAdditionalInfo').off('click');
$('#btnSaveAdditionalInfo').on('click', function(e) {
	e.preventDefault();
	var textLinkType		=	$("#additionalConfirmationInfo-optionTextLink").val(),
		description			=	$("#additionalConfirmationInfo-description").val(),
		informationContent	=	$("#additionalConfirmationInfo-informationContent").val();
	
	if(description == '') {
		$("#warningMessage-description").removeClass('d-none');
		$("#additionalConfirmationInfo-description").focus();
	} else if(informationContent == '' || (textLinkType == '2' && !validateUrl(informationContent))) {
		// $("#warningMessage-description").addClass('d-none');
		// $("#warningMessage-informationContent").removeClass('d-none');
		// $("#additionalConfirmationInfo-informationContent").focus();
	} else {
		$("#warningMessage-informationContent").addClass('d-none');
		$("#additionalConfirmationInfo-listAdditionalInfo").append(generateElemAdditionalInfo(description, informationContent, textLinkType));
		$("#additionalConfirmationInfo-description, #additionalConfirmationInfo-informationContent").val('');
		$("#btnCancelAdditionalInfo").click();
	}
	countAdditionalInfo();
});

function countAdditionalInfo(){
	var totalAdditionalInfo	=	$('.elemAdditionalInfo').length;
	$("#totalAdditionalReconfirmationInfo").html(totalAdditionalInfo);
}

function generateElemAdditionalInfo(informationDescription, informationContent, textLinkType, deletable = true){
	var btnDeleteAdditionalInformation	=	deletable ? '<i class="text-info fa fa-trash text16px mt-2 pull-right" onclick="deleteAdditionalInformation(\''+informationDescription+'\')"></i>' : '';
	return '<div class="row elemAdditionalInfo rounded-lg mx-1 px-2 py-1 mb-10" data-description="'+informationDescription+'" data-content="'+informationContent+'" data-textLinkType="'+textLinkType+'">'+
				'<div class="col-sm-12 px-2">'+
					'<b>'+informationDescription+'</b>'+btnDeleteAdditionalInformation+
				'</div>'+
				'<div class="col-sm-12 mt-2 px-2">'+informationContent+'</div>'+
			'</div>';
}

function deleteAdditionalInformation(informationDescription){
	$(".elemAdditionalInfo[data-description='"+informationDescription+"']").remove();
	if($("#additionalConfirmationInfo-listAdditionalInfo").html() == '') {
		$("#additionalConfirmationInfo-listAdditionalInfo").addClass('d-none');
		$("#noAdditionalInfo").removeClass('d-none');
	}
	countAdditionalInfo();
}

mailboxFunc();