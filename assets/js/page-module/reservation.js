var $confirmDialog= $('#modal-confirm-action');

if (reservationFunc == null){
	var reservationFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionReservationType', 'dataReservationType', idReservationTypeAdmin);
			setOptionHelper('optionReservationTypeEditor', 'dataReservationType');
			setOptionHelper('optionYear', 'optionYear', false, false);
			setOptionHelper('optionSource', 'dataSource');
			setOptionHelper('optionSourceEditor', 'dataSource');
			setOptionHelper('optionPickUpArea', 'dataArea');
			setOptionHelper("optionPartner", "dataVendorAndDriverActive");
			setOptionHelper("reservationHour", "dataHours");
			setOptionHelper("reservationHourEnd", "dataHours");
			setOptionHelper("reservationMinute", "dataMinutes");
			setOptionHelper("reservationMinuteEnd", "dataMinutes");
			setOptionHelper('optionPaymentMethod', 'dataPaymentMethod');
			setOptionHelper('optionDriverCollect', 'dataDriver');
			setOptionHelper('optionVendorCollect', 'dataVendorTicket');
			$("#optionPartner").select2();

			var idReservationNotif	=	0;
			if(localStorage.getItem('OSNotificationData') === null || localStorage.getItem('OSNotificationData') === undefined){
			} else {
				var OSNotificationData	=	JSON.parse(localStorage.getItem('OSNotificationData'));
				var OSNotifType			=	OSNotificationData.type;
				if(OSNotifType == "reservation"){
					idReservationNotif	=	OSNotificationData.idReservation;
				}
			}
			
			var minHourDateFilterTomorrow	=	$("#minHourDateFilterTomorrow").val() * 1,
				maxHourDateFilterToday		=	$("#maxHourDateFilterToday").val() * 1,
				d							=	new Date(),
				hour						=	d.getHours(),
				intHour						=	hour * 1;

			if(intHour >= minHourDateFilterTomorrow){
				var dateTomorrow	=	getDateTomorrow(),
					yearDate		=	dateTomorrow.substr(-4, 4);
				$("#optionYear").val(yearDate);
				$("#startDate").val(getDateTomorrow());
				$("#endDate").val("");
				onChangeDateInputFilter($("#startDate"));
			} else if(intHour <= maxHourDateFilterToday) {
				$("#startDate").val(getDateToday());
				$("#endDate").val("");
				onChangeDateInputFilter($("#startDate"));
			} else {
				$("#endDate").val("");
				$("#startDate").val("");				
			}
			
			getDataReservation();
		});	
	}
}

function getOptionHelperReservationProduct(totalPax){
	var dataSend	=	{};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getOptionHelperReservationProduct",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){},
		success:function(response){
			var productSelfDriveVendor		=	response.productSelfDriveVendor;
			var productSelfDriveDuration	=	response.productSelfDriveDuration;
			var productTicketVendor			=	response.productTicketVendor;
			var productTransportVendor		=	response.productTransportVendor;
			localStorage.setItem('optionHelperSelfDriveDuration', JSON.stringify(productSelfDriveDuration));
			
			$('#optionSelfDriveTypeVendor, #optionProductTicketVendor, #optionProductTransportDriver, #optionDurationSelfDrive').empty();
			
			if(productSelfDriveVendor.length > 0){
				$.each(productSelfDriveVendor, function(index, array) {
					$('#optionSelfDriveTypeVendor').append($('<option data-productname="'+array.PRODUCTNAME+'" data-vendorname="'+array.VENDORNAME+'" data-vendor="'+array.IDVENDOR+'" data-idcartype="'+array.IDCARTYPE+'"></option>').val(array.VALUE).html(array.OPTIONTEXT));
					
					if(index == 0){
						var idVendor	=	array.IDVENDOR,
							idCarType	=	array.IDCARTYPE,
							idxMatch	=	0;
						$.each(productSelfDriveDuration, function(indexDuration, arrayDuration) {
							if(idVendor == arrayDuration.IDVENDOR && idCarType == arrayDuration.IDCARTYPE){
								$('#optionDurationSelfDrive').append($('<option data-idvendor="'+idVendor+'" data-duration="'+arrayDuration.DURATION+'" data-price="'+arrayDuration.NOMINALFEE+'" data-notes="'+arrayDuration.NOTES+'"></option>').val(arrayDuration.IDCARSELFDRIVEFEE).html(arrayDuration.OPTIONTEXT));
								if(idxMatch == 0){
									$("#selfDriveProductNominal").val(numberFormat(arrayDuration.NOMINALFEE));
									$("#selfDriveProductNotes").val(arrayDuration.NOTES);
								}
								idxMatch++;
							}
						});
					}
				});
			}
			
			if(productTicketVendor.length > 0){
				$.each(productTicketVendor, function(index, array) {
					$('#optionProductTicketVendor').append($('<option data-voucherStatus="'+array.VOUCHERSTATUS+'" data-productname="'+array.PRODUCTNAME+'" data-vendorname="'+array.VENDORNAME+'" data-priceAdult="'+array.PRICEADULT+'" data-priceChild="'+array.PRICECHILD+'" data-priceInfant="'+array.PRICEINFANT+'" data-notes="'+array.NOTES+'"></option>').val(array.VALUE).html(array.OPTIONTEXT));
					
					if(index == 0){
						$("#pricePerPaxAdult").val(numberFormat(array.PRICEADULT));
						$("#pricePerPaxChild").val(numberFormat(array.PRICECHILD));
						$("#pricePerPaxInfant").val(numberFormat(array.PRICEINFANT));
						$("#ticketProductNotes").val(array.NOTES);
						calculateTicketProduct();
					}
				});
			}
			
			if(productTransportVendor.length > 0){
				$.each(productTransportVendor, function(index, array) {
					$('#optionProductTransportDriver').append($('<option data-iddrivertype="'+array.IDDRIVERTYPE+'" '+
																'data-idproducttype="'+array.IDPRODUCTTYPE+'" '+
																'data-productname="'+array.PRODUCTNAME+'" '+
																'data-vendorname="'+array.VENDORNAME+'" '+
																'data-price="'+array.FEENOMINAL+'" '+
																'data-notes="'+array.ADDITIONALINFO+'" '+
																'data-scheduletype="'+array.SCHEDULETYPE+'" '+
																'data-jobtype="'+array.JOBTYPE+'" '+
																'data-jobrate="'+array.JOBRATE+'" '+
																'data-productrank="'+array.PRODUCTRANK+'" '+
																'data-costtickettype="'+array.COSTTICKETTYPE+'" '+
																'data-costparkingtype="'+array.COSTPARKINGTYPE+'" '+
																'data-costmineralwatertype="'+array.COSTMINERALWATERTYPE+'" '+
																'data-costbreakfasttype="'+array.COSTBREAKFASTTYPE+'" '+
																'data-costlunchtype="'+array.COSTLUNCHTYPE+'" '+
																'data-bonustype="'+array.BONUSTYPE+'" '+
																'data-costticket="'+array.COSTTICKET+'" '+
																'data-costparking="'+array.COSTPARKING+'" '+
																'data-costmineralwater="'+array.COSTMINERALWATER+'" '+
																'data-costbreakfast="'+array.COSTBREAKFAST+'" '+
																'data-costlunch="'+array.COSTLUNCH+'" '+
																'data-bonus="'+array.BONUS+'"></option>').val(array.VALUE).html(array.OPTIONTEXT));
					
					if(index == 0){
						$("#transportProductNominal").val(numberFormat(array.FEENOMINAL));
						$("#transportProductNotes, #transportProductNotesHidden").val(array.ADDITIONALINFO);
						$("#ticketCostPax, #parkingCostPax, #breakfastCostPax, #lunchCostPax, #bonusPax").val(0);
						
						if(array.COSTTICKETTYPE == "1"){
							$("#ticketCostPax, #costPerPaxTicket").attr("disabled", true);
							$("#totalTicketCost").prop('readonly', false);
							$("#costPerPaxTicket").val(0);
							$("#totalTicketCost").val(numberFormat(array.COSTTICKET));
						} else {
							$("#ticketCostPax, #costPerPaxTicket").attr("disabled", false);
							$("#totalTicketCost").prop('readonly', true);
							$("#costPerPaxTicket").val(numberFormat(array.COSTTICKET));
							if((array.COSTTICKET * 1) > 0){
								$("#ticketCostPax").val(numberFormat(totalPax));
							}
						}
						
						if(array.COSTPARKINGTYPE == "1"){
							$("#parkingCostPax, #costPerPaxParking").attr("disabled", true);
							$("#totalParkingCost").prop('readonly', false);
							$("#costPerPaxParking").val(0);
							$("#totalParkingCost").val(numberFormat(array.COSTPARKING));
						} else {
							$("#parkingCostPax, #costPerPaxParking").attr("disabled", false);
							$("#totalParkingCost").prop('readonly', true);
							$("#costPerPaxParking").val(numberFormat(array.COSTPARKING));
							if((array.COSTPARKING * 1) > 0){
								$("#parkingCostPax").val(numberFormat(totalPax));
							}
						}
						
						if(array.COSTMINERALWATERTYPE == "1"){
							$("#mineralWaterCostPax, #costPerPaxMineralWater").attr("disabled", true);
							$("#totalMineralWaterCost").prop('readonly', false);
							$("#costPerPaxMineralWater").val(0);
							$("#totalMineralWaterCost").val(numberFormat(array.COSTMINERALWATER));
						} else {
							$("#mineralWaterCostPax, #costPerPaxMineralWater").attr("disabled", false);
							$("#totalMineralWaterCost").prop('readonly', true);
							$("#costPerPaxMineralWater").val(numberFormat(array.COSTMINERALWATER));
							if((array.COSTMINERALWATER * 1) > 0){
								$("#mineralWaterCostPax").val(numberFormat(totalPax));
							}
						}
						
						if(array.COSTBREAKFASTTYPE == "1"){
							$("#breakfastCostPax, #costPerPaxBreakfast").attr("disabled", true);
							$("#totalBreakfastCost").prop('readonly', false);
							$("#costPerPaxBreakfast").val(0);
							$("#totalBreakfastCost").val(numberFormat(array.COSTBREAKFAST));
						} else {
							$("#breakfastCostPax, #costPerPaxBreakfast").attr("disabled", false);
							$("#totalBreakfastCost").prop('readonly', true);
							$("#costPerPaxBreakfast").val(numberFormat(array.COSTBREAKFAST));
							if((array.COSTBREAKFAST * 1) > 0){
								$("#breakfastCostPax").val(numberFormat(totalPax));
							}
						}
						
						if(array.COSTLUNCHTYPE == "1"){
							$("#lunchCostPax, #costPerPaxLunch").attr("disabled", true);
							$("#totalLunchCost").prop('readonly', false);
							$("#costPerPaxLunch").val(0);
							$("#totalLunchCost").val(numberFormat(array.COSTLUNCH));
						} else {
							$("#lunchCostPax, #costPerPaxLunch").attr("disabled", false);
							$("#totalLunchCost").prop('readonly', true);
							$("#costPerPaxLunch").val(numberFormat(array.COSTLUNCH));
							if((array.COSTLUNCH * 1) > 0){
								$("#lunchCostPax").val(numberFormat(totalPax));
							}
						}
						
						if(array.BONUSTYPE == "1"){
							$("#bonusPax, #nominalPerPaxBonus").attr("disabled", true);
							$("#totalBonus").prop('readonly', false);
							$("#nominalPerPaxBonus").val(0);
							$("#totalBonus").val(numberFormat(array.BONUS));
						} else {
							$("#bonusPax, #nominalPerPaxBonus").attr("disabled", false);
							$("#totalBonus").prop('readonly', true);
							$("#nominalPerPaxBonus").val(numberFormat(array.BONUS));
							if((array.BONUS * 1) > 0){
								$("#bonusPax").val(numberFormat(totalPax));
							}
						}
	
						calculateTicketCost();
						calculateParkingCost();
						calculateMineralWaterCost();
						calculateBreakfastCost();
						calculateLunchCost();
						calculateBonus();
						calculateTotalFeeCostTransport();
					}
				});
			}
			
			$('#optionSelfDriveTypeVendor, #optionProductTicketVendor, #optionProductTransportDriver').select2({
				dropdownParent: $("#modal-editorReservationDetails")
			});	
		}
	});
}

$('#optionOrderBy, #optionOrderType').off('change');
$('#optionOrderBy, #optionOrderType').on('change', function(e) {
	getDataReservation();
});

$('#startDate, #endDate').off('change');
$('#startDate, #endDate').on('change', function(e) {
	onChangeDateInputFilter($(this));
	getDataReservation();
});

$('#optionReservationType, #optionReservationStatus, #optionYear, #optionSource, #optionTransportStatus, #optionPartner, #optionCollectPaymentStatus').off('change');
$('#optionReservationType, #optionReservationStatus, #optionYear, #optionSource, #optionTransportStatus, #optionPartner, #optionCollectPaymentStatus').on('change', function(e) {
	getDataReservation();
});

$('#bookingCodeFilter, #customerNameFilter, #locationName, #reservationTitleFilter').off('keydown');
$('#bookingCodeFilter, #customerNameFilter, #locationName, #reservationTitleFilter').on('keydown', function(e) {
	if(e.which === 13){
		e.preventDefault();
		getDataReservation();
	}
});

$('#filterDataReservation').off('click');
$('#filterDataReservation').on('click', function(e) {
	getDataReservation();
});

function generateDataTable(page){
	getDataReservation(page);
}

function getDataReservation(page = 1, idReservation = 0){
	var $tableBody			=	$('#table-reservation > tbody'),
		columnNumber		=	$('#table-reservation > thead > tr > th').length,
		status				=	$('#optionReservationStatus').val(),
		year				=	$('#optionYear').val(),
		idReservationType	=	$('#optionReservationType').val(),
		idSource			=	$('#optionSource').val(),
		idPartner			=	$('#optionPartner').val(),
		startDate			=	$('#startDate').val(),
		endDate				=	$('#endDate').val(),
		bookingCode			=	$('#bookingCodeFilter').val(),
		customerName		=	$('#customerNameFilter').val(),
		transportStatus		=	$('#optionTransportStatus').val(),
		reservationTitle	=	$('#reservationTitleFilter').val(),
		locationName		=	$('#locationName').val(),
		collectPaymentStatus=	$('#optionCollectPaymentStatus').val(),
		orderBy				=	$('#optionOrderBy').val(),
		orderType			=	$('#optionOrderType').val(),
		dataSend			=	{
			page:page,
			idReservation:idReservation,
			idReservationType:idReservationType,
			status:status,
			year:year,
			idSource:idSource,
			idPartner:idPartner,
			bookingCode:bookingCode,
			customerName:customerName,
			startDate:startDate,
			endDate:endDate,
			transportStatus:transportStatus,
			reservationTitle:reservationTitle,
			locationName:locationName,
			collectPaymentStatus:collectPaymentStatus,
			orderBy:orderBy,
			orderType:orderType
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getDataReservation",
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
			
			localStorage.setItem('bookingCodeManual', response.bookingCodeManual);			
			if(response.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
				$('#excelDataReservation, #excelDataVendorBook').addClass('d-none').off("click").attr("href", "");
			} else {
				var data						=	response.result.data,
					fromNotif					=	response.fromNotif,
					currExchangeData			=	response.dataExchange,
					dateLast90Days				=	response.dateLast90Days,
					rows						=	"",
					arrIdReservationDetailCosts	=	[];
					
				if(fromNotif == true) localStorage.removeItem("OSNotificationData");				
				localStorage.setItem('currExchangeData', JSON.stringify(currExchangeData));
				if(response.urlExcelDetail != "") $('#excelDataReservation').removeClass('d-none').on("click").attr("href", response.urlExcelDetail);
				if(response.urlExcelVendorBook != "") $('#excelDataVendorBook').removeClass('d-none').on("click").attr("href", response.urlExcelVendorBook);

				if(data.length === 0){
					rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
					$('#excelDataReservation, #excelDataVendorBook').addClass('d-none').off("click").attr("href", "");
				} else {
					
					$.each(data, function(index, array) {
						
						var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[array.IDRESERVATIONTYPE]+'">'+array.RESERVATIONTYPE+'</span>';
						var badgeStatus				=	'<span class="badge badge-dark">Unprocessed</span>';
						switch(array.STATUS){
							case "-1"	:	badgeStatus	=	'<span class="badge badge-danger">Cancel</span>'; break;
							case "0"	:	badgeStatus	=	'<span class="badge badge-dark">Unprocessed</span>'; break;
							case "1"	:	badgeStatus	=	'<span class="badge badge-warning">Admin Processed</span>'; break;
							case "2"	:	badgeStatus	=	'<span class="badge badge-success">Scheduled</span>'; break;
							case "3"	:	badgeStatus	=	'<span class="badge badge-primary">On Process</span>'; break;
							case "4"	:	badgeStatus	=	'<span class="badge badge-info">Done</span>'; break;
							default		:	badgeStatus	=	'<span class="badge badge-dark">Unprocessed</span>'; break;
						}

						var badgeStatusDriver			=	array.STATUSDRIVER == 1 ? '<br/><span class="badge badge-outline badge-success text-left">Driver</i></span>' : '';
						var badgeStatusTicket			=	array.STATUSTICKET == 1 ? '<br/><span class="badge badge-outline badge-secondary text-left">Ticket</i></span>' : '';
						var badgeStatusCar				=	array.STATUSCAR == 1 ? '<br/><span class="badge badge-outline badge-primary text-left">Car</i></span>' : '';
						var badgeIncludeCollectPayment	=	array.STATUSINCLUDECOLLECT == 1 ? '<span class="badge badge-pill badge-success text-white p-2">Collect Payment</span><br/><br/>' : '';
						
						var inputType	=	'';
						switch(array.INPUTTYPE){
							case "1"	:	inputType	=	'Mailbox'; break;
							case "2"	:	inputType	=	'Manual'; break;
						}
						
						var idReservation		=	array.IDRESERVATION,
							totalVoucher		=	array.TOTALVOUCHER * 1,
							totalVoucherStatus	=	array.TOTALVOUCHERSTATUS * 1,
							btnEdit				=	btnSetDetails	=	btnDelete	=	btnVoucher	=	btnPayment	=	"";
							arrIdReservationDetailCosts.push(idReservation);
						var btnEdit			=	'<button class="button button-xs button-box button-primary" onclick="getDetailReservation('+idReservation+')">'+
													'<i class="fa fa-pencil"></i>'+
												'</button><br/>';
						var btnSetDetails	=	'<button class="button button-xs button-box button-info" onclick="getSetDetailsReservation('+idReservation+')">'+
													'<i class="fa fa-window-restore"></i>'+
												'</button><br/>';

						if(array.STATUS == 0 || array.STATUS == 1){
							btnDelete	=	'<button class="button button-xs button-box button-danger" onclick="cancelReservation('+idReservation+', \''+array.RESERVATIONTITLE+'\', \''+array.RESERVATIONDATESTART+'\', \''+array.RESERVATIONDATEEND+'\', \''+array.CUSTOMERNAME+'\')">'+
												'<i class="fa fa-times"></i>'+
											'</button><br/>';
						}
						
						if(array.STATUS != -1 || (array.STATUS == -1 && array.REFUNDTYPE == -2)){
							btnPayment	=	'<button class="button button-xs button-box button-javascript" onclick="showPaymentDetails('+idReservation+')">'+
												'<i class="fa fa-money"></i>'+
											'</button><br/>';
						}
						
						if(totalVoucherStatus > 0 || totalVoucher > 0){
							btnVoucher	=	'<button class="button button-xs button-box button-info" onclick="openModalVoucher('+idReservation+')">'+
												'<i class="fa fa-ticket"></i>'+
											'</button><br/>';
						}
						
						var btnCopy		=	'<button class="button button-xs button-box button-warning" onclick="getDetailReservation('+idReservation+', true)">'+
												'<i class="fa fa-files-o"></i>'+
											'</button><br/>';
						
						var	reservationDateEnd	=	"";
						if(array.DURATIONOFDAY > 1 || array.ISSELFDRIVE == 1){
							reservationDateEnd	=	"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>";
						}
						
						var	partnerHandle		=	"-",
							totalDuration		=	parseInt(array.DURATIONOFDAY),
							dataPartnerHandle	=	array.PARTNERHANDLE;
						if(dataPartnerHandle.length > 0){
							partnerHandle		=	"";
							$.each(dataPartnerHandle, function(iHandle, arrayHandle) {
								partnerHandle	+=	totalDuration == 1 ? arrayHandle.PARTNERNAME+"<br/>" : "["+arrayHandle.SCHEDULEDATE+"] "+arrayHandle.PARTNERNAME+"<br/>";
							});
						}
						
						var hotelNameLower	=	array.HOTELNAME.toLowerCase(),
							hotelName		=	hotelNameLower == "without hotel transfer" || hotelNameLower == "without transfer" ? "<b class='text-danger'>"+array.HOTELNAME+"</b>" : array.HOTELNAME,
							areaName		=	array.AREANAME.toLowerCase() == "without transfer" ? '<span class="badge badge-danger">'+array.AREANAME+'</span>' : '<span class="badge badge-info">'+array.AREANAME+'</span>';
						var searchRemark	=	"ori|collect",
							remark			=	array.REMARK,
							regexRemark		=	new RegExp(searchRemark, "gi");
						
						if (remark.search(regexRemark) != -1) {  
						   remark	=	remark.replace(regexRemark, "<b class='text-danger'>$&</b>");
						}
						
						var textSpecialReq			=	array.SPECIALREQUEST,
							strHighlightSpecialReq	=	"follow up",
							regexSearchSpecialReq	=	new RegExp(strHighlightSpecialReq.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), "gi"),
							textSpecialReq			=	textSpecialReq.replace(regexSearchSpecialReq, "<b class='text-danger'>$&</b>");
							
						var refundTypeBadge			=	'';
						switch(array.REFUNDTYPE){
							case "-1"	:	refundTypeBadge	=	'<br/><span class="badge badge-info">Full Refund</span>'; break;
							case "-2"	:	refundTypeBadge	=	'<br/><span class="badge badge-info">Partial Refund</span>'; break;
							case "0"	:	if(array.STATUS == -1) refundTypeBadge	=	'<br/><span class="badge badge-info">No Refund</span>'; break;
							default		:	refundTypeBadge	=	''; break;
						}
						
						var iconEditRefundType	=	'';
						if(array.STATUS == -1){
							let dateReservationStart=	moment(array.RESERVATIONDATESTARTDB, "YYYY-MM-DD");
							dateLast90Days			=	moment(dateLast90Days, "YYYY-MM-DD");
							
							if(dateLast90Days.isBefore(dateReservationStart)) {
								iconEditRefundType	=	'<i class="text-info fa fa-pencil font-size-16 ml-2"  onclick="openEditorRefundStatus('+idReservation+', \''+array.BOOKINGCODE+'\', '+array.REFUNDTYPE+', \''+array.RESERVATIONTITLE+'\', \''+array.RESERVATIONDATESTART+'\', \''+array.RESERVATIONDATEEND+'\', \''+array.CUSTOMERNAME+'\')"></i>';
							}
						}

						rows	+=	"<tr>"+
										"<td align='right'>"+idReservation+"</td>"+
										"<td>"+
											"<b>"+array.RESERVATIONTITLE+"</b><br/><br/>"+
											"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
											reservationDateEnd+
											"Duration : "+array.DURATIONOFDAY+" day(s)<br/><br/>"+
											"["+inputType+"] "+array.SOURCENAME+"<br/>"+
											badgeReservationType+"<br/>"+
											badgeStatus+refundTypeBadge+iconEditRefundType+"<br/>"+
											badgeStatusDriver+badgeStatusTicket+badgeStatusCar+
										"</td>"+
										"<td>"+
											"<b>"+array.CUSTOMERNAME+"</b><br/>"+
											"Contact : "+array.CUSTOMERCONTACT+"<br/>"+
											"Email : "+array.CUSTOMEREMAIL+"<br/><br/>"+
											"Adult : <b>"+array.NUMBEROFADULT+"</b><br/>"+
											"Child : <b>"+array.NUMBEROFCHILD+"</b><br/>"+
											"Infant : <b>"+array.NUMBEROFINFANT+"</b>"+
										"</td>"+
										"<td>"+
											areaName+"<br/><br/>"+
											"<b>Hotel<br/></b>"+hotelName+"<br/><br/>"+
											"<b>Pick Up<br/></b>"+array.PICKUPLOCATION+"<br/><br/>"+
											"<b>Drop Off<br/></b>"+array.DROPOFFLOCATION+
										"</td>"+
										"<td>"+
											"<b>Booking Code :</b><br/>"+array.BOOKINGCODE+"<br/><br/>"+
											"<b>First Input :</b><br/>["+array.DATETIMEINPUT+"] "+array.USERINPUT+"<br/><br/>"+
											"<b>Last Update :</b><br/>["+array.DATETIMELASTUPDATE+"] "+array.USERLASTUPDATE+"<br/><br/>"+
											"<b>Special Request :</b><br/>"+textSpecialReq+
										"</td>"+
										"<td>"+
											badgeIncludeCollectPayment+
											"<b>Tour Plan :</b><br/>"+array.TOURPLAN+"<br/><br/>"+
											"<b>Remark :</b><br/>"+remark+"<br/><br/>"+
											"<b>Handle By :</b><br/>"+partnerHandle+
										"</td>"+
										"<td>"+btnEdit+btnCopy+btnSetDetails+btnVoucher+btnPayment+btnDelete+"</td>"+
									"</tr>";
					});
				}

				arrIdReservationDetailCosts	=	arrIdReservationDetailCosts.length <= 0 ? "" : arrIdReservationDetailCosts.toString();
				$("#arrIdReservationDetailCosts").val(arrIdReservationDetailCosts);
				generatePagination("tablePaginationReservation", page, response.result.pageTotal);
				generateDataInfo("tableDataCountReservation", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
				$tableBody.html(rows);
			}
		}
	});
}

$('#modal-editorReservation').off('show.bs.modal');
$('#modal-editorReservation').on('show.bs.modal', function(event) {
	var $activeElement = $(document.activeElement);

	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {
			if($activeElement.attr('data-action') == "insert"){
				$("#optionReservationTypeEditor, #optionSourceEditor").val("");
				$("#reservationDate").val(dateNow);
				$("#reservationTitle, #productDetailsUrl, #customerName, #customerContact, #customerEmail, #hotelName, #pickUpLocation, #pickUpLocationUrl, #dropOffLocation, #bookingCode, #tourPlan, #remark, #specialRequest").val("");
				$("#numberOfChild, #numberOfInfant, #reservationPriceInteger, #reservationPriceDecimal, #idReservationEditor, #reservationPriceIDR").val(0);
				$("#durationOfDay, #numberOfAdult").val(1);
				$("#optionPickUpArea").val($("#optionPickUpArea option:first").val());
				$("#reservationHour").val($("#reservationHour option:first").val());
				$("#reservationMinute").val($("#reservationMinute option:first").val());
				$("#reservationPriceType").val($("#reservationPriceType option:first").val());
				$("#bookingCode").val(localStorage.getItem("bookingCodeManual"));
				$("#selfDriveStatus").val("");

				var currencyType	=	$("#reservationPriceType option:first").val(),
					currExchangeData=	JSON.parse(localStorage.getItem("currExchangeData"));
				
				$.each(currExchangeData, function(index, array) {
					if(array.CURRENCY == currencyType){
						$("#currencyExchange").val(numberFormat(array.EXCHANGETOIDR));
					}
				});
				setDateTimeEndDisableStatus();
			}
		}
	}
});

$('#selfDriveStatus').off('change');
$('#selfDriveStatus').on('change', function(e) {
	setDateTimeEndDisableStatus();
});

function setDateTimeEndDisableStatus(setDisabledOnly = false){
	var selfDriveStatus	=	$('#selfDriveStatus').val();
	if(selfDriveStatus == 0 || selfDriveStatus == null){
		$('#reservationDateEnd, #reservationHourEnd, #reservationMinuteEnd').prop('disabled', true);
	} else {
		$('#reservationDateEnd, #reservationHourEnd, #reservationMinuteEnd').prop('disabled', false);
	}
	if(!setDisabledOnly) calculateReservationDateTimeEnd();
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

$('#content-editorReservation').off('submit');
$('#content-editorReservation').on('submit', function(e) {
	e.preventDefault();
	var idReservation	=	$("#idReservationEditor").val(),
		selfDriveStatus	=	$("#selfDriveStatus").val(),
		actionURL		=	idReservation == 0 ? "addReservation" : "updateReservation";
		dataForm		=	$("#content-editorReservation :input").serializeArray(),
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
		
		$.ajax({
			type: 'POST',
			url: baseURL+"reservation/"+actionURL,
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#content-editorReservation :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#content-editorReservation :input").attr("disabled", false);
				setDateTimeEndDisableStatus();
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					var currExchangeData=	response.dataExchange;
				
					localStorage.setItem('currExchangeData', JSON.stringify(currExchangeData));
					$('#modal-editorReservation').modal('hide');
					
					if($('#slideContainerRight').hasClass("show")){
						getSetDetailsReservation(idReservation, false);
					} else {
						getDataReservation(1);
					}
				}
			}
		});
	}
});

$('#reservationPriceType').off('change');
$('#reservationPriceType').on('change', function(e) {
	var currencyType	=	$(this).val(),
		currExchangeData=	JSON.parse(localStorage.getItem("currExchangeData"));
	
	if(currencyType != 'IDR'){
		$.each(currExchangeData, function(index, array) {
			if(array.CURRENCY == currencyType){
				$("#currencyExchange").val(numberFormat(array.EXCHANGETOIDR));
			}
		});
	} else {
		$("#currencyExchange").val(1);
	}
	
	calculateReservationPriceIDR();
});

$('#reservationPriceInteger, #reservationPriceDecimal').off('change');
$('#reservationPriceInteger, #reservationPriceDecimal').on('change', function(e) {
	calculateReservationPriceIDR();
});

function calculateReservationPriceIDR(){
	var reservationPriceInt	=	$('#reservationPriceInteger').val().replace(/[^0-9\.]+/g, '');
		reservationPriceDec	=	$('#reservationPriceDecimal').val().replace(/[^0-9\.]+/g, '');
		reservationPrice	=	(reservationPriceInt+"."+reservationPriceDec) * 1,
		currencyExchange	=	$("#currencyExchange").val().replace(/[^0-9\.]+/g, '') * 1,
		reservationPriceIDR	=	reservationPrice * currencyExchange;
	
	$("#reservationPriceIDR").val(numberFormat(reservationPriceIDR));
}

function getDetailReservation(idReservation, setDuplicate=false){
	var dataSend		=	{idReservation:idReservation};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getDetailReservation",
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

				$("#optionReservationTypeEditor").val(detailData.IDRESERVATIONTYPE);
				$("#optionSourceEditor").val(detailData.IDSOURCE);
				$("#optionPickUpArea").val(detailData.IDAREA);
				$("#reservationTitle").val(detailData.RESERVATIONTITLE);
				$("#productDetailsUrl").val(detailData.URLDETAILPRODUCT);
				$("#selfDriveStatus").val(detailData.ISSELFDRIVE);
				$("#durationOfDay").val(detailData.DURATIONOFDAY);
				$("#reservationDate").val(detailData.RESERVATIONDATESTART);
				$("#reservationDateEnd").val(detailData.RESERVATIONDATEEND);
				$("#reservationHour").val(detailData.RESERVATIONHOUR);
				$("#reservationHourEnd").val(detailData.RESERVATIONHOUREND);
				$("#reservationMinute").val(detailData.RESERVATIONMINUTE);
				$("#reservationMinuteEnd").val(detailData.RESERVATIONMINUTEEND);
				$("#customerName").val(detailData.CUSTOMERNAME);
				$("#customerContact").val(detailData.CUSTOMERCONTACT);
				$("#customerEmail").val(detailData.CUSTOMEREMAIL);
				$("#numberOfAdult").val(detailData.NUMBEROFADULT);
				$("#numberOfChild").val(detailData.NUMBEROFCHILD);
				$("#numberOfInfant").val(detailData.NUMBEROFINFANT);
				$("#hotelName").val(detailData.HOTELNAME);
				$("#pickUpLocation").val(detailData.PICKUPLOCATION);
				$("#pickUpLocationUrl").val(detailData.URLPICKUPLOCATION);
				$("#dropOffLocation").val(detailData.DROPOFFLOCATION);
				$("#bookingCode").val(detailData.BOOKINGCODE);
				$("#reservationPriceType").val(detailData.INCOMEAMOUNTCURRENCY);
				$("#reservationPriceInteger").val(numberFormat(detailData.INCOMEAMOUNTINTEGER));
				$("#reservationPriceDecimal").val(detailData.INCOMEAMOUNTDECIMAL);
				$("#currencyExchange").val(numberFormat(detailData.INCOMEEXCHANGECURRENCY));
				$("#reservationPriceIDR").val(numberFormat(detailData.INCOMEAMOUNTIDR));
				$("#tourPlan").val(detailData.TOURPLAN);
				$("#remark").val(detailData.REMARK);
				$("#specialRequest").val(detailData.SPECIALREQUEST);
				$("#reservationStatusEditor").val(detailData.STATUS);
				$("#refundTypeEditor").val(detailData.REFUNDTYPE);
				
				if(!setDuplicate){
					$("#idReservationEditor").val(idReservation);
				} else {
					$("#reservationPriceInteger, #reservationPriceDecimal, #reservationPriceIDR").val(0);
					$("#bookingCode").val("D-"+detailData.BOOKINGCODE);
					$("#idReservationEditor").val(0);
					$("#optionReservationTypeEditor, #optionSourceEditor").val("");
				}
				
				setDateTimeEndDisableStatus(true);
				$('#modal-editorReservation').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

function getSetDetailsReservation(idReservation, toggleSlide=true){
	var dataSend		=	{idReservation:idReservation};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getDetailReservationDetails",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#btnCloseSetDetails, #alertOpenReconfirmationMenu").removeClass("d-none");
			$("#btnCreateReservation, #btnCreateVoucher, #btnPreviousReservationDetailCost, #btnNextReservationDetailCost").addClass("d-none");
			$("#reservationTitleStr, #reservationDateTimeStr, #durationOfDayStr, #custNameStr, #custContactStr, #custEmailStr, #paxAdultStr, #paxChildStr, #paxInfantStr, #areaNameStr, #hotelNameStr, #pickUpStr, #dropOffStr, #bookingCodeStr, #paymentNominalStr, #remarkStr, #specialRequestStr, #tourPlanStr").html("-");
			$("#additionalConfirmationInfoList, #containerScheduleDate").html("");
			$("#idAreaPickup").val(0);
			$('#window-loader').modal('show');
			$('#btnVoucherListDetails, #btnAddMailAdditionalInfo').addClass("d-none");
			$('#btnVoucherListDetails').off('click');
			if(toggleSlide) toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
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
					toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
					$("#btnCreateReservation, #btnCreateVoucher").removeClass("d-none");
					$("#btnCloseSetDetails").addClass("d-none");
				}
			} else {
				var detailData				=	response.detailData,
					listDetails				=	response.reservationDetails,
					arrDateSchedule			=	response.arrDateSchedule,
					differenceHoursOnly		=	response.differenceHoursOnly,
					isAutoCostKeywordExist	=	response.isAutoCostKeywordExist,
					listAutoCostTemplate	=	response.listAutoCostTemplate,
					titleMessage			=	response.titleMessage,
					warningMessage			=	response.warningMessage;
				
				var totalPaxAdult		=	detailData.NUMBEROFADULT * 1,
					totalPaxChild		=	detailData.NUMBEROFCHILD * 1,
					totalPaxInfant		=	detailData.NUMBEROFINFANT * 1,
					totalPax			=	totalPaxAdult+totalPaxChild+totalPaxInfant,
					totalReconfirmation	=	detailData.TOTALRECONFIRMATION,
					statusSelfDrive		=	detailData.ISSELFDRIVE,
					additionalInfoList	=	detailData.ADDITIONALINFOLIST == "" || detailData.ADDITIONALINFOLIST == null ? [] : JSON.parse(detailData.ADDITIONALINFOLIST);
					
				$("#totalPax").val(totalPax);
				$("#idAreaPickup").val(detailData.IDAREA);
				$("#idSourceDetailCosts").val(detailData.IDSOURCE);
				$("#idReservationDetailCosts").val(idReservation);
				getOptionHelperReservationProduct(totalPax);

				var reservationDateTimeStr	=	detailData.RESERVATIONDATESTART+" "+detailData.RESERVATIONTIMESTART;
				if(detailData.DURATIONOFDAY > 1 || statusSelfDrive == 1){
					reservationDateTimeStr	=	reservationDateTimeStr+" - "+detailData.RESERVATIONDATEEND+" "+detailData.RESERVATIONTIMEEND;
				}
					
				var badgeReservationType		=	'<span class="badge badge-'+arrBadgeType[detailData.IDRESERVATIONTYPE]+'">'+detailData.RESERVATIONTYPE+'</span>',
					areaName					=	detailData.AREANAME.toLowerCase() == "without transfer" ? "<b class='text-danger'>"+detailData.AREANAME+"</b>" : detailData.AREANAME,
					reservationDetailDuration	=	detailData.DURATIONOFDAY+" Day(s)";
					
				if(statusSelfDrive == 1 && differenceHoursOnly > 0){
					reservationDetailDuration	+=	' + '+differenceHoursOnly+' Hour(s)';
				}
				
				var btnAddKeywordAutoCost	=	"";
				if(!isAutoCostKeywordExist && listDetails.length <= 0){
					btnAddKeywordAutoCost	=	'<i class="fa fa-plus ml-2" onclick="openFormAddKeywordAutoCost(\''+detailData.RESERVATIONTITLE+'\')"></i>';
					generateListAutoCostTemplate(listAutoCostTemplate, detailData.RESERVATIONTITLE);
				}
						
				$("#idReservationDetailsEditor").val(idReservation);
				$("#reservationTitleStr").html("["+detailData.SOURCENAME+"] <span id='reservationTitleOnly'>"+detailData.RESERVATIONTITLE+"</span>"+btnAddKeywordAutoCost);
				$("#reservationTypeBadge").html(badgeReservationType);
				$("#reservationDateTimeStr").html(reservationDateTimeStr);
				$("#durationOfDayStr, #reservationDetailsDurationOfDayStr").html(reservationDetailDuration);
				$("#custNameStr").html(detailData.CUSTOMERNAME);
				$("#custContactStr").html(detailData.CUSTOMERCONTACT);
				$("#custEmailStr").html(detailData.CUSTOMEREMAIL);
				$("#paxAdultStr").html(detailData.NUMBEROFADULT);
				$("#paxChildStr").html(detailData.NUMBEROFCHILD);
				$("#paxInfantStr").html(detailData.NUMBEROFINFANT);
				$("#ticketAdultPax").val(detailData.NUMBEROFADULT);
				$("#ticketChildPax").val(detailData.NUMBEROFCHILD);
				$("#ticketInfantPax").val(detailData.NUMBEROFINFANT);
				$("#areaNameStr").html(areaName);
				$("#hotelNameStr").html(detailData.HOTELNAME == '' ? '-' : detailData.HOTELNAME);
				$("#pickUpStr").html(detailData.PICKUPLOCATION == '' ? '-' : detailData.PICKUPLOCATION);
				$("#dropOffStr").html(detailData.DROPOFFLOCATION == '' ? '-' : detailData.DROPOFFLOCATION);
				$("#bookingCodeStr").html(detailData.BOOKINGCODE);
				$("#paymentNominalStr").html("["+detailData.INCOMEAMOUNTCURRENCY+"] "+numberFormat(detailData.INCOMEAMOUNT)+" <b>x</b> "+numberFormat(detailData.INCOMEEXCHANGECURRENCY)+"<br/>[IDR] "+numberFormat(detailData.INCOMEAMOUNTIDR));
				$("#totalIncomeStr").html(numberFormat(detailData.INCOMEAMOUNTIDR));
				$("#remarkStr").html(detailData.REMARK == '' ? '-' : detailData.REMARK);
				$("#specialRequestStr").html(detailData.SPECIALREQUEST == '' ? '-' : detailData.SPECIALREQUEST);
				$("#tourPlanStr").html(detailData.TOURPLAN == '' ? '-' : detailData.TOURPLAN);		
				$("#firstInputStr").html("["+detailData.DATETIMEINPUT+"] "+detailData.USERINPUT);
				$("#lastUpdateStr").html("["+detailData.DATETIMELASTUPDATE+"] "+detailData.USERLASTUPDATE);
				
				if(additionalInfoList.length > 0){
					var elemAdditionalInfo	=	'';
					for(var iAdditional=0; iAdditional<additionalInfoList.length; iAdditional++){
						var informationDescription	=	additionalInfoList[iAdditional][0],
							informationContent		=	additionalInfoList[iAdditional][1],
							textLinkType			=	informationContent.includes("</a>"),
							informationContent		=	informationContent.includes("</a>") ? $(additionalInfoList[iAdditional][1]).attr('href') : informationContent,
							deletable				=	totalReconfirmation == 0 ? true : false;
						elemAdditionalInfo			+=	generateElemAdditionalInfo(idReservation, informationDescription, informationContent, textLinkType, deletable);
					}
					$("#additionalConfirmationInfoList").html(elemAdditionalInfo);
				}
				
				if(totalReconfirmation <= 0) {
					$('#btnAddMailAdditionalInfo').removeClass('d-none');
					$('#alertOpenReconfirmationMenu').addClass('d-none');
				}
				
				$('#btnEditReservationFormDetails').off('click');
				$('#btnEditReservationFormDetails').on('click', function(e) {
					getDetailReservation(idReservation);
				});
				
				var cbDateSchedule		=	"",
					strArrDateSchedule	=	[];
				if(arrDateSchedule.length > 0){
					if(arrDateSchedule.length == 1){
						cbDateSchedule	=	'<label class="adomx-checkbox"><input checked disabled name="cbDateSchedule[]" class="cbDateSchedule" type="checkbox" value="'+arrDateSchedule[0][0]+'"> <i class="icon"></i> '+arrDateSchedule[0][1]+'</label>';
						strArrDateSchedule.push(arrDateSchedule[0][0]);
					} else {
						for(var iCB=0; iCB<arrDateSchedule.length; iCB++){
							cbDateSchedule	+=	'<label class="adomx-checkbox"><input name="cbDateSchedule[]" class="cbDateSchedule" type="checkbox" value="'+arrDateSchedule[iCB][0]+'"> <i class="icon"></i> '+arrDateSchedule[iCB][1]+'</label>';
							strArrDateSchedule.push(arrDateSchedule[iCB][0]);
						}
					}
					$("#containerScheduleDate").html(cbDateSchedule);
				}
				$("#arrDateSchedule").val(JSON.stringify(strArrDateSchedule));
				
				$(".detailsChildElement").remove();
				if(listDetails.length > 0){
					var elemListDetails	=	"",
						arrDetailsElem	=	[];
					$.each(listDetails, function(index, array) {
						
						var idReservationDetails	=	array.IDRESERVATIONDETAILS;
							idProductType			=	array.IDPRODUCTTYPE,
							productType				=	array.PRODUCTTYPE,
							productName				=	array.PRODUCTNAME,
							dateSchedule			=	array.DATESCHEDULE,
							nominalCost				=	numberFormat(array.NOMINAL),
							notes					=	array.NOTES,
							userInput				=	array.USERINPUT,
							dateInput				=	array.DATETIMEINPUT,
							voucherStatus			=	array.VOUCHERSTATUS,
							vendorName				=	"";
						switch(idProductType){
							case "2"	:	vendorName	=	array.DRIVERTYPE;
											break;
							case "3"	:	
							case "1"	:	vendorName	=	array.VENDORNAME;
											break;
						}
						
						var arrElem		=	[idReservationDetails, "", dateSchedule, userInput, dateInput, productType, productName, vendorName, nominalCost, idReservation, notes, voucherStatus];
						arrDetailsElem.push(arrElem);
						
					});
					
					var elemListDetails	=	generateDetailsElem(arrDetailsElem);
					$("#noDataListOfDetails").addClass("d-none");
					$("#listOfDetails").append(elemListDetails);
					
				} else {
					$("#noDataListOfDetails").removeClass("d-none");
				}
				
				var arrIdReservationDetailCosts	=	$("#arrIdReservationDetailCosts").val(),
					arrIdReservationDetailCosts	=	arrIdReservationDetailCosts.split(","),
					indexActiveData				=	arrIdReservationDetailCosts.indexOf(idReservation+""),
					nextIndex					=	indexActiveData + 1,
					previousIndex				=	indexActiveData - 1;
					
				if(indexActiveData == -1 || arrIdReservationDetailCosts.length == 1){
					$("#btnPreviousReservationDetailCost, #btnNextReservationDetailCost").addClass("d-none").off('click');
				} else if(indexActiveData == 0) {
					$("#btnPreviousReservationDetailCost").addClass("d-none").off('click');
					$("#btnNextReservationDetailCost").removeClass("d-none").off('click');
					$('#btnNextReservationDetailCost').on('click', function(e) {
						getSetDetailsReservation(arrIdReservationDetailCosts[nextIndex], false);
					});
				} else if(nextIndex == arrIdReservationDetailCosts.length) {
					$("#btnNextReservationDetailCost").addClass("d-none").off('click');
					$("#btnPreviousReservationDetailCost").removeClass("d-none").off('click');
					$('#btnPreviousReservationDetailCost').on('click', function(e) {
						getSetDetailsReservation(arrIdReservationDetailCosts[previousIndex], false);
					});
				} else {
					$("#btnPreviousReservationDetailCost, #btnNextReservationDetailCost").removeClass("d-none").off('click');
					
					$('#btnPreviousReservationDetailCost').on('click', function(e) {
						getSetDetailsReservation(arrIdReservationDetailCosts[previousIndex], false);
					});
					$('#btnNextReservationDetailCost').on('click', function(e) {
						getSetDetailsReservation(arrIdReservationDetailCosts[nextIndex], false);
					});
				}
				
				if(titleMessage != ''){
					$("#specialCaseCostText").html('['+titleMessage+'] — — '+warningMessage);
					$("#specialCaseCostAlert").removeClass('d-none');
				} else {
					$("#specialCaseCostText").html('');
					$("#specialCaseCostAlert").addClass('d-none');
				}
				
				calculateCostMarginReservation();
			}
		}
	});
}

function generateElemAdditionalInfo(idReservation, informationDescription, informationContent, textLinkType, deletable = true){
	var btnDeleteAdditionalInformation	=	deletable ? '<i class="text-info fa fa-trash text16px mr-1" onclick="deleteAdditionalInformation('+idReservation+', \''+informationDescription+'\')"></i>' : '';
	return '<li class="elemAdditionalInfo" data-description="'+informationDescription+'"> <span>'+btnDeleteAdditionalInformation+' '+informationDescription+'</span> <span>'+informationContent+'</span> </li>';
}

$('#modal-additionalConfirmationInfo').off('show.bs.modal');
$('#modal-additionalConfirmationInfo').on('show.bs.modal', function(event) {
	$("#additionalConfirmationInfo-description, #additionalConfirmationInfo-informationContent").val("");
	$("#warningMessage-description, #warningMessage-informationContent").addClass('d-none');
});

$('#content-additionalConfirmationInfo').off('submit');
$('#content-additionalConfirmationInfo').on('submit', function(e) {
	e.preventDefault();
	var idReservation		=	$("#idReservationDetailCosts").val(),
		textLinkType		=	$("#additionalConfirmationInfo-optionTextLink").val(),
		description			=	$("#additionalConfirmationInfo-description").val(),
		informationContent	=	$("#additionalConfirmationInfo-informationContent").val(),
		dataSend			=	{
			idReservation:idReservation,
			textLinkType:textLinkType,
			description:description,
			informationContent:informationContent
		};
	
	if(description == '') {
		$("#warningMessage-description").removeClass('d-none');
		$("#additionalConfirmationInfo-description").focus();
	} else if(informationContent == '') {
		$("#warningMessage-description").addClass('d-none');
		$("#warningMessage-informationContent").removeClass('d-none');
		$("#additionalConfirmationInfo-informationContent").focus();
	} else {
		$("#warningMessage-informationContent").addClass('d-none');
		$.ajax({
			type: 'POST',
			url: baseURL+"reservation/addReconfirmationAdditionalInfo",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#content-additionalConfirmationInfo :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#content-additionalConfirmationInfo :input").attr("disabled", false);
				
				if(response.status == 200){
					$("#additionalConfirmationInfoList").append(generateElemAdditionalInfo(idReservation, description, informationContent, true));
					$('#modal-additionalConfirmationInfo').modal('hide');
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

function deleteAdditionalInformation(idReservation, informationDescription){
	var dataSend	=	{
		idReservation:idReservation,
		informationDescription:informationDescription
	};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/deleteAdditionalInformation",
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
				$(".elemAdditionalInfo[data-description='"+informationDescription+"']").remove();
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

function openFormAddKeywordAutoCost(reservationTitle){
	$("#textKeywordAutoCost").html(reservationTitle);
	$('#modal-selectTemplateAutoCost').modal('show');	
}

function generateListAutoCostTemplate(listAutoCostTemplate, reservationTitle){
	if(listAutoCostTemplate != false){
		$("#body-listTemplateAutoCost").html("");
		var rowListTemplate	=	"";
		$.each(listAutoCostTemplate, function(index, array) {
			rowListTemplate	+=	"<tr>"+
									"<td>"+array.AUTODETAILSTEMPLATENAME+"</td>"+
									"<td width='40'>"+
										"<button class='button button-xs button-box button-info' onclick='addKeywordAutoCost("+array.IDAUTODETAILSTEMPLATE+", \""+reservationTitle+"\")'><i class='fa fa-arrow-right'></i></button>"+
									"</td>"+
								"</tr>";
		});
		$("#body-listTemplateAutoCost").html(rowListTemplate);
	}
}

function addKeywordAutoCost(idAutoDetailsTemplate, reservationTitle){
	var dataSend	=	{
		idAutoDetailsTemplate:idAutoDetailsTemplate,
		reservationTitle:reservationTitle
	};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/addKeywordAutoCost",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#modal-selectTemplateAutoCost').modal('hide');
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();

			if(response.status == 200){
				$('#btnAutoAddDetails').click();
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#searchAutoCost').off('keydown');
$('#searchAutoCost').on('keydown', function(e) {
	if(e.which === 13){
		var keywordSearch	=	$(this).val().toLowerCase();
		$("#body-listTemplateAutoCost tr").each(function(index) {
			$row		=	$(this);
			var rowText	=	$row.find("td:first").text().toLowerCase();
			if(rowText.includes(keywordSearch)) {
				$(this).removeClass("d-none");
			} else {
				$(this).addClass("d-none");
			}
		});
	}
});

$('#btnAutoAddDetails').off('click');
$('#btnAutoAddDetails').on('click', function(e) {
	var idReservation	=	$("#idReservationDetailCosts").val(),
		reservationTitle=	$("#reservationTitleOnly").html(),
		customerName	=	$("#custNameStr").html(),
		arrDateSchedule	=	$("#arrDateSchedule").val(),
		idArea			=	$("#idAreaPickup").val(),
		idSource		=	$("#idSourceDetailCosts").val(),
		areaName		=	$("#areaNameStr").html(),
		totalPaxAdult	=	$("#paxAdultStr").html(),
		totalPaxChild	=	$("#paxChildStr").html(),
		totalPaxInfant	=	$("#paxInfantStr").html(),
		dataSend		=	{
								idReservation:idReservation,
								reservationTitle:reservationTitle,
								customerName:customerName,
								arrDateSchedule:arrDateSchedule,
								idArea:idArea,
								idSource:idSource,
								areaName:areaName,
								totalPaxAdult:totalPaxAdult,
								totalPaxChild:totalPaxChild,
								totalPaxInfant:totalPaxInfant
							};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/autoAddReservationDetails",
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


			if(response.status == 200){
				toastr["info"](response.msg);

				$("#noDataListOfDetails").addClass("d-none");
				var arrResultInsert	=	response.arrResultInsert,
					newDetailsElem	=	generateDetailsElem(arrResultInsert);
				
				$("#listOfDetails").append(newDetailsElem);
				calculateCostMarginReservation();
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}			
		}
	});
});

$('#btnCloseSetDetails').off('click');
$('#btnCloseSetDetails').on('click', function(e) {
	getDataReservation();
	toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
	$("#btnCreateReservation, #btnCreateVoucher").removeClass("d-none");
	$("#btnCloseSetDetails").addClass("d-none");
});

$("#optionSelfDriveTypeVendor").change(function() {
	var selectedItem			=	$(this).val(),
		idVendor				=	$('option:selected', this).data("vendor"),
		idCarType				=	$('option:selected', this).data("idcartype"),
		idxMatch				=	0,
		productSelfDriveDuration=	JSON.parse(localStorage.getItem("optionHelperSelfDriveDuration"));
	
	$('#optionDurationSelfDrive').empty();
	$.each(productSelfDriveDuration, function(indexDuration, arrayDuration) {
		if(idVendor == arrayDuration.IDVENDOR && idCarType == arrayDuration.IDCARTYPE){
			$('#optionDurationSelfDrive').append($('<option data-idvendor="'+idVendor+'" data-duration="'+arrayDuration.DURATION+'" data-price="'+arrayDuration.NOMINALFEE+'" data-notes="'+arrayDuration.NOTES+'"></option>').val(arrayDuration.IDCARSELFDRIVEFEE).html(arrayDuration.OPTIONTEXT));
			if(idxMatch == 0){
				$("#selfDriveProductNominal").val(numberFormat(arrayDuration.NOMINALFEE));
				$("#selfDriveProductNotes").val(arrayDuration.NOTES);
			}
			idxMatch++;
		}
	});
});

$("#optionDurationSelfDrive").change(function() {
	var selectedItem	=	$(this).val();
	var price			=	$('option:selected', this).data("price");
	var notes			=	$('option:selected', this).data("notes");
	
	$("#selfDriveProductNominal").val(numberFormat(price));
	$("#selfDriveProductNotes").val(notes);
});

$("#optionProductTicketVendor").change(function() {
	var selectedItem	=	$(this).val();
	var priceAdult		=	$('option:selected', this).data("priceadult");
	var priceChild		=	$('option:selected', this).data("pricechild");
	var priceInfant		=	$('option:selected', this).data("priceinfant");
	var notes			=	$('option:selected', this).data("notes");
	
	$("#pricePerPaxAdult").val(numberFormat(priceAdult));
	$("#pricePerPaxChild").val(numberFormat(priceChild));
	$("#pricePerPaxInfant").val(numberFormat(priceInfant));
	$("#ticketProductNotes").val(notes);
	calculateTicketProduct();
});

$("#optionProductTransportDriver").change(function() {
	var selectedItem		=	$(this).val(),
		totalPax			=	$("#totalPax").val();
		price				=	$('option:selected', this).data("price"),
		notes				=	$('option:selected', this).data("notes"),
		costTicketType		=	$('option:selected', this).data("costtickettype"),
		costParkingType		=	$('option:selected', this).data("costparkingtype"),
		costMineralWaterType=	$('option:selected', this).data("costmineralwatertype"),
		costBreakfastType	=	$('option:selected', this).data("costbreakfasttype"),
		costLunchType		=	$('option:selected', this).data("costlunchtype"),
		bonusType			=	$('option:selected', this).data("bonustype"),
		costTicket			=	$('option:selected', this).data("costticket"),
		costParking			=	$('option:selected', this).data("costparking"),
		costMineralWater	=	$('option:selected', this).data("costmineralwater"),
		costBreakfast		=	$('option:selected', this).data("costbreakfast"),
		costLunch			=	$('option:selected', this).data("costlunch"),
		bonus				=	$('option:selected', this).data("bonus");
	
	$("#transportProductNominal").val(numberFormat(price));
	$("#transportProductNotes, #transportProductNotesHidden").val(notes);
	$("#ticketCostPax, #parkingCostPax, #breakfastCostPax, #lunchCostPax, #bonusPax").val(0);
						
	if(costTicketType == "1"){
		$("#ticketCostPax, #costPerPaxTicket").attr("disabled", true);
		$("#totalTicketCost").prop('readonly', false);
		$("#costPerPaxTicket").val(0);
		$("#totalTicketCost").val(numberFormat(costTicket));
	} else {
		$("#ticketCostPax, #costPerPaxTicket").attr("disabled", false);
		$("#totalTicketCost").prop('readonly', true);
		$("#costPerPaxTicket").val(numberFormat(costTicket));
		if((costTicket * 1) > 0){
			$("#ticketCostPax").val(numberFormat(totalPax));
		}
	}
	
	if(costParkingType == "1"){
		$("#parkingCostPax, #costPerPaxParking").attr("disabled", true);
		$("#totalParkingCost").prop('readonly', false);
		$("#costPerPaxParking").val(0);
		$("#totalParkingCost").val(numberFormat(costParking));
	} else {
		$("#parkingCostPax, #costPerPaxParking").attr("disabled", false);
		$("#totalParkingCost").prop('readonly', true);
		$("#costPerPaxParking").val(numberFormat(costParking));
		if((costParking * 1) > 0){
			$("#parkingCostPax").val(numberFormat(totalPax));
		}
	}
	
	if(costMineralWaterType == "1"){
		$("#mineralWaterCostPax, #costPerPaxMineralWater").attr("disabled", true);
		$("#totalMineralWaterCost").prop('readonly', false);
		$("#costPerPaxMineralWater").val(0);
		$("#totalMineralWaterCost").val(numberFormat(costMineralWater));
	} else {
		$("#mineralWaterCostPax, #costPerPaxMineralWater").attr("disabled", false);
		$("#totalMineralWaterCost").prop('readonly', true);
		$("#costPerPaxMineralWater").val(numberFormat(costMineralWater));
		if((costMineralWater * 1) > 0){
			$("#mineralWaterCostPax").val(numberFormat(totalPax));
		}
	}
	
	if(costBreakfastType == "1"){
		$("#breakfastCostPax, #costPerPaxBreakfast").attr("disabled", true);
		$("#totalBreakfastCost").prop('readonly', false);
		$("#costPerPaxBreakfast").val(0);
		$("#totalBreakfastCost").val(numberFormat(costBreakfast));
	} else {
		$("#breakfastCostPax, #costPerPaxBreakfast").attr("disabled", false);
		$("#totalBreakfastCost").prop('readonly', true);
		$("#costPerPaxBreakfast").val(numberFormat(costBreakfast));
		if((costBreakfast * 1) > 0){
			$("#breakfastCostPax").val(numberFormat(totalPax));
		}
	}
	
	if(costLunchType == "1"){
		$("#lunchCostPax, #costPerPaxLunch").attr("disabled", true);
		$("#totalLunchCost").prop('readonly', false);
		$("#costPerPaxLunch").val(0);
		$("#totalLunchCost").val(numberFormat(costLunch));
	} else {
		$("#lunchCostPax, #costPerPaxLunch").attr("disabled", false);
		$("#totalLunchCost").prop('readonly', true);
		$("#costPerPaxLunch").val(numberFormat(costLunch));
		if((costLunch * 1) > 0){
			$("#lunchCostPax").val(numberFormat(totalPax));
		}
	}
	
	if(bonusType == "1"){
		$("#bonusPax, #nominalPerPaxBonus").attr("disabled", true);
		$("#totalBonus").prop('readonly', false);
		$("#nominalPerPaxBonus").val(0);
		$("#totalBonus").val(numberFormat(bonus));
	} else {
		$("#bonusPax, #nominalPerPaxBonus").attr("disabled", false);
		$("#totalBonus").prop('readonly', true);
		$("#nominalPerPaxBonus").val(numberFormat(bonus));
		if((bonus * 1) > 0){
			$("#bonusPax").val(numberFormat(totalPax));
		}
	}
	
	calculateTicketCost();
	calculateParkingCost();
	calculateMineralWaterCost();
	calculateBreakfastCost();
	calculateLunchCost();
	calculateBonus();
	calculateTotalFeeCostTransport();
});

function calculateTicketCost(calculateTotalFeeCost = false){
	if(!$('#ticketCostPax').prop('disabled') && !$('#costPerPaxTicket').prop('disabled')){
		var ticketCostPax		=	$("#ticketCostPax").val().replace(/[^0-9\.]+/g, '') * 1,
			costPerPaxTicket	=	$("#costPerPaxTicket").val().replace(/[^0-9\.]+/g, '') * 1,
			totalTicketCost		=	ticketCostPax * costPerPaxTicket;
		$("#totalTicketCost").val(numberFormat(totalTicketCost));	
	}
	if(calculateTotalFeeCost) calculateTotalFeeCostTransport();
}

function calculateParkingCost(calculateTotalFeeCost = false){
	if(!$('#parkingCostPax').prop('disabled') && !$('#costPerPaxParking').prop('disabled')){
		var parkingCostPax		=	$("#parkingCostPax").val().replace(/[^0-9\.]+/g, '') * 1,
			costPerPaxParking	=	$("#costPerPaxParking").val().replace(/[^0-9\.]+/g, '') * 1,
			totalParkingCost	=	parkingCostPax * costPerPaxParking;
		$("#totalParkingCost").val(numberFormat(totalParkingCost));	
	}
	if(calculateTotalFeeCost) calculateTotalFeeCostTransport();
}

function calculateMineralWaterCost(calculateTotalFeeCost = false){
	if(!$('#mineralWaterCostPax').prop('disabled') && !$('#costPerPaxMineralWater').prop('disabled')){
		var mineralWaterCostPax		=	$("#mineralWaterCostPax").val().replace(/[^0-9\.]+/g, '') * 1,
			costPerPaxMineralWater	=	$("#costPerPaxMineralWater").val().replace(/[^0-9\.]+/g, '') * 1,
			totalMineralWaterCost	=	mineralWaterCostPax * costPerPaxMineralWater;
		$("#totalMineralWaterCost").val(numberFormat(totalMineralWaterCost));	
	}
	if(calculateTotalFeeCost) calculateTotalFeeCostTransport();
}

function calculateBreakfastCost(calculateTotalFeeCost = false){
	if(!$('#breakfastCostPax').prop('disabled') && !$('#costPerPaxBreakfast').prop('disabled')){
		var breakfastCostPax	=	$("#breakfastCostPax").val().replace(/[^0-9\.]+/g, '') * 1,
			costPerPaxBreakfast	=	$("#costPerPaxBreakfast").val().replace(/[^0-9\.]+/g, '') * 1,
			totalBreakfastCost	=	breakfastCostPax * costPerPaxBreakfast;
		$("#totalBreakfastCost").val(numberFormat(totalBreakfastCost));	
	}
	if(calculateTotalFeeCost) calculateTotalFeeCostTransport();
}

function calculateLunchCost(calculateTotalFeeCost = false){
	if(!$('#lunchCostPax').prop('disabled') && !$('#costPerPaxLunch').prop('disabled')){
		var lunchCostPax		=	$("#lunchCostPax").val().replace(/[^0-9\.]+/g, '') * 1,
			costPerPaxLunch		=	$("#costPerPaxLunch").val().replace(/[^0-9\.]+/g, '') * 1,
			totalLunchCost		=	lunchCostPax * costPerPaxLunch;
		$("#totalLunchCost").val(numberFormat(totalLunchCost));	
	}
	if(calculateTotalFeeCost) calculateTotalFeeCostTransport();
}

function calculateBonus(calculateTotalFeeCost = false){
	if(!$('#bonusPax').prop('disabled') && !$('#nominalPerPaxBonus').prop('disabled')){
		var bonusPax			=	$("#bonusPax").val().replace(/[^0-9\.]+/g, '') * 1,
			nominalPerPaxBonus	=	$("#nominalPerPaxBonus").val().replace(/[^0-9\.]+/g, '') * 1,
			totalBonus			=	bonusPax * nominalPerPaxBonus;
		$("#totalBonus").val(numberFormat(totalBonus));	
	}
	if(calculateTotalFeeCost) calculateTotalFeeCostTransport();
}

function calculateTotalFeeCostTransport(){
	var totalDriverFee			=	$("#transportProductNominal").val().replace(/[^0-9\.]+/g, '') * 1,
		totalTicketCost			=	$("#totalTicketCost").val().replace(/[^0-9\.]+/g, '') * 1,
		totalParkingCost		=	$("#totalParkingCost").val().replace(/[^0-9\.]+/g, '') * 1,
		totalMineralWaterCost	=	$("#totalMineralWaterCost").val().replace(/[^0-9\.]+/g, '') * 1,
		totalBreakfastCost		=	$("#totalBreakfastCost").val().replace(/[^0-9\.]+/g, '') * 1,
		totalLunchCost			=	$("#totalLunchCost").val().replace(/[^0-9\.]+/g, '') * 1,
		totalBonus				=	$("#totalBonus").val().replace(/[^0-9\.]+/g, '') * 1,
		totalFeeCostTransport	=	totalDriverFee + totalTicketCost + totalParkingCost + totalMineralWaterCost + totalBreakfastCost + totalLunchCost + totalBonus,
		
		costPerPaxTicket		=	$("#costPerPaxTicket").val().replace(/[^0-9\.]+/g, '') * 1,
		costPerPaxParking		=	$("#costPerPaxParking").val().replace(/[^0-9\.]+/g, '') * 1,
		costPerPaxMineralWater	=	$("#costPerPaxMineralWater").val().replace(/[^0-9\.]+/g, '') * 1,
		costPerPaxBreakfast		=	$("#costPerPaxBreakfast").val().replace(/[^0-9\.]+/g, '') * 1,
		costPerPaxLunch			=	$("#costPerPaxLunch").val().replace(/[^0-9\.]+/g, '') * 1,
		nominalPerPaxBonus		=	$("#nominalPerPaxBonus").val().replace(/[^0-9\.]+/g, '') * 1,
		transportProductNotes	=	$("#transportProductNotesHidden").val(),
		additionalNotes			=	"";
	
	$("#totalFeeCostsTransportNominal").html(numberFormat(totalFeeCostTransport));
	
	if(totalTicketCost > 0 || totalParkingCost > 0 || totalMineralWaterCost > 0 || totalBreakfastCost > 0 || totalLunchCost > 0 || totalBonus > 0){
		additionalNotes			+=	"- Driver Fee : "+numberFormat(totalDriverFee)+" \n";
	}
	
	if(totalTicketCost > 0){
		if(!$('#ticketCostPax').prop('disabled') && !$('#costPerPaxTicket').prop('disabled')){
			additionalNotes		+=	"- Ticket : "+numberFormat(costPerPaxTicket)+" (Per Pax) \n";
		} else {
			additionalNotes		+=	"- Ticket : "+numberFormat(totalTicketCost)+" (Fixed) \n";		
		}
	}

	if(totalParkingCost > 0){
		if(!$('#parkingCostPax').prop('disabled') && !$('#costPerPaxParking').prop('disabled')){
			additionalNotes		+=	"- Parking : "+numberFormat(costPerPaxParking)+" (Per Pax) \n";
		} else {
			additionalNotes		+=	"- Parking : "+numberFormat(totalParkingCost)+" (Fixed) \n";		
		}
	}

	if(totalMineralWaterCost > 0){
		if(!$('#mineralWaterCostPax').prop('disabled') && !$('#costPerPaxMineralWater').prop('disabled')){
			additionalNotes		+=	"- Mineral Water : "+numberFormat(costPerPaxMineralWater)+" (Per Pax) \n";
		} else {
			additionalNotes		+=	"- Mineral Water : "+numberFormat(totalMineralWaterCost)+" (Fixed) \n";		
		}
	}

	if(totalBreakfastCost > 0){
		if(!$('#breakfastCostPax').prop('disabled') && !$('#costPerPaxBreakfast').prop('disabled')){
			additionalNotes		+=	"- Breakfast : "+numberFormat(costPerPaxBreakfast)+" (Per Pax) \n";
		} else {
			additionalNotes		+=	"- Breakfast : "+numberFormat(totalBreakfastCost)+" (Fixed) \n";		
		}
	}

	if(totalLunchCost > 0){
		if(!$('#lunchCostPax').prop('disabled') && !$('#costPerPaxLunch').prop('disabled')){
			additionalNotes		+=	"- Lunch : "+numberFormat(costPerPaxLunch)+" (Per Pax) \n";
		} else {
			additionalNotes		+=	"- Lunch : "+numberFormat(totalLunchCost)+" (Fixed) \n";		
		}
	}

	if(totalBonus > 0){
		if(!$('#bonusPax').prop('disabled') && !$('#nominalPerPaxBonus').prop('disabled')){
			additionalNotes		+=	"- Bonus : "+numberFormat(nominalPerPaxBonus)+" (Per Pax) \n";
		} else {
			additionalNotes		+=	"- Bonus : "+numberFormat(totalBonus)+" (Fixed) \n";		
		}
	}
	
	if(additionalNotes != ""){
		var doubleNewLine	=	transportProductNotes == "" ? "" : " \n\n";
		$("#transportProductNotes").val(transportProductNotes+doubleNewLine+additionalNotes);
	}
}

$('#modal-editorReservationDetails').off('show.bs.modal');
$('#modal-editorReservationDetails').on('show.bs.modal', function(event) {
	if($('.cbDateSchedule').length > 1){
		$("input:checkbox.cbDateSchedule").prop('checked', false);
	}
});

$('#saveReservationDetails').off('click');
$('#saveReservationDetails').on('click', function(e) {
	e.preventDefault();
	var idProductType	=	idVendor	=	idDriverType	=	idCarType	=	durationSelfDrive	=	voucherStatus	=	0;
	var nominalCost		=	0;
	var scheduleType	=	1;
	var jobType			=	0;
	var jobRate			=	1;
	var productRank		=	1;
	var ticketAdultPax	=	pricePerPaxAdult	=	totalPriceAdult	=	0;
	var ticketChildPax	=	pricePerPaxChild	=	totalPriceChild	=	0;
	var ticketInfantPax	=	pricePerPaxInfant	=	totalPriceInfant	=	0;
	var notes			=	vendorName	=	productName	=	productType	=	"";
	var activeTab		=	$("ul#tabsPanelReservationDetails li a.active").attr("href");
	var customerName	=	$("#custNameStr").html();
	var arrDateSchedule	=	[];
	
	switch(activeTab){
		case "#selfDriveProductTab"		:	idProductType		=	3;
											idVendor			=	$('#optionSelfDriveTypeVendor option:selected').attr('data-vendor');
											idCarType			=	$('#optionSelfDriveTypeVendor option:selected').attr('data-idcartype');
											durationSelfDrive	=	$("#optionDurationSelfDrive option:selected").attr('data-duration');
											nominalCost			=	$("#selfDriveProductNominal").val();
											notes				=	$("#selfDriveProductNotes").val();
											vendorName			=	$('#optionSelfDriveTypeVendor option:selected').attr('data-vendorname');
											productName			=	$('#optionSelfDriveTypeVendor option:selected').attr('data-productname')+" - "+$('#optionDurationSelfDrive option:selected').text();
											productType			=	"Self Drive";
											break;
		case "#ticketProductTab"		:	idProductType		=	1;
											idVendor			=	$("#optionProductTicketVendor").val();
											nominalCost			=	$("#ticketProductNominal").val();
											ticketAdultPax		=	$("#ticketAdultPax").val();
											ticketChildPax		=	$("#ticketChildPax").val();
											ticketInfantPax		=	$("#ticketInfantPax").val();
											pricePerPaxAdult	=	$("#pricePerPaxAdult").val();
											pricePerPaxChild	=	$("#pricePerPaxChild").val();
											pricePerPaxInfant	=	$("#pricePerPaxInfant").val();
											totalPriceAdult		=	$("#totalPriceAdult").val();
											totalPriceChild		=	$("#totalPriceChild").val();
											totalPriceInfant	=	$("#totalPriceInfant").val();
											notes				=	$("#ticketProductNotes").val();
											vendorName			=	$('#optionProductTicketVendor option:selected').attr('data-vendorname');
											productName			=	$('#optionProductTicketVendor option:selected').attr('data-productname');
											voucherStatus		=	$('#optionProductTicketVendor option:selected').attr('data-voucherStatus');
											productType			=	"Ticket";
											break;
		case "#transportProductTab"	:		idProductType		=	2;
											idDriverType		=	$('#optionProductTransportDriver option:selected').attr('data-iddrivertype');
											nominalCost			=	$("#totalFeeCostsTransportNominal").html();
											notes				=	$("#transportProductNotes").val();
											vendorName			=	$('#optionProductTransportDriver option:selected').attr('data-vendorname');
											productName			=	$('#optionProductTransportDriver option:selected').attr('data-productname');
											scheduleType		=	$('#optionProductTransportDriver option:selected').attr('data-scheduletype');
											jobType				=	$('#optionProductTransportDriver option:selected').attr('data-jobtype');
											jobRate				=	$('#optionProductTransportDriver option:selected').attr('data-jobrate');
											productRank			=	$('#optionProductTransportDriver option:selected').attr('data-productrank');
											productType			=	"Transport";
											break;
	}
	
	$('input:checkbox.cbDateSchedule').each(function () {
		var checkboxVal	=	(this.checked ? $(this).val() : false);
		
		if(checkboxVal) arrDateSchedule.push(checkboxVal);
	});

	var idReservation	=	$("#idReservationDetailsEditor").val();
	var dataSend		=	{
								idReservation:idReservation,
								arrDateSchedule:arrDateSchedule,
								scheduleType:scheduleType,
								jobType:jobType,
								jobRate:jobRate,
								productRank:productRank,
								idProductType:idProductType,
								idVendor:idVendor,
								idDriverType:idDriverType,
								idCarType:idCarType,
								voucherStatus:voucherStatus,
								durationSelfDrive:durationSelfDrive,
								productName:productName,
								vendorName:vendorName,
								nominalCost:nominalCost,
								ticketAdultPax:ticketAdultPax,
								ticketChildPax:ticketChildPax,
								ticketInfantPax:ticketInfantPax,
								pricePerPaxAdult:pricePerPaxAdult,
								pricePerPaxChild:pricePerPaxChild,
								pricePerPaxInfant:pricePerPaxInfant,
								totalPriceAdult:totalPriceAdult,
								totalPriceChild:totalPriceChild,
								totalPriceInfant:totalPriceInfant,
								notes:notes,
								customerName:customerName
							};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/saveReservationDetails",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-editorReservationDetails :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-editorReservationDetails :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$("#noDataListOfDetails").addClass("d-none");
				var arrResultInsert	=	response.arrResultInsert,
					newDetailsElem	=	generateDetailsElem(arrResultInsert);
				
				$("#listOfDetails").append(newDetailsElem);
				$("#modal-editorReservationDetails").modal("hide");
				calculateCostMarginReservation();
			}
		}
	});
});

function generateDetailsElem(arrResultInsert){
	var newDetailsElem			=	"",
		totalVoucherDetails		=	0;
	for(var iResult=0; iResult<arrResultInsert.length; iResult++){
		var idReservationDetails=	arrResultInsert[iResult][0],
			dateSchedule		=	arrResultInsert[iResult][1],
			strDateSchedule		=	arrResultInsert[iResult][2],
			detailInput			=	arrResultInsert[iResult][3]+" ["+arrResultInsert[iResult][4]+"]",
			productType			=	arrResultInsert[iResult][5],
			productName			=	arrResultInsert[iResult][6],
			vendorName			=	arrResultInsert[iResult][7],
			nominalCost			=	arrResultInsert[iResult][8],
			idReservation		=	arrResultInsert[iResult][9],
			notes				=	arrResultInsert[iResult][10],
			voucherStatus		=	arrResultInsert[iResult][11] * 1,
			functionEditDetails	=	productType == 'Ticket' ? 'editReservationDetailsTicket' : 'editReservationDetailsTransport';
			totalVoucherDetails+=	voucherStatus;
		newDetailsElem			+=	'<div class="col-sm-12 mt-20 mb-5 rounded-lg detailsChildElement" style="border: 1px solid #e0e0e0;" id="reservationDetails'+idReservationDetails+'">'+
										'<div class="row pt-10 pb-1">'+
											'<div class="col-lg-2 col-sm-12 text-center">'+
												'<span class="badge badge-primary">'+strDateSchedule+'</span><br/>'+
												'<button class="button button-xs button-box button-primary" onclick="'+functionEditDetails+'('+idReservationDetails+')"><i class="fa fa-pencil"></i></button>'+
												'<button class="button button-xs button-box button-danger" onclick="deleteDetailsReservation('+idReservation+', '+idReservationDetails+', \''+productType+'\', \''+productName+'\', \''+vendorName+'\', \''+strDateSchedule+'\', \''+dateSchedule+'\')"><i class="fa fa-trash"></i></button>'+
											'</div>'+
											'<div class="col-lg-4 col-sm-12">'+
												'<p class="font-weight-bold">'+
													'['+productType+'] '+productName+'<br/>'+
													'Vendor : '+vendorName+
												'</p>'+
											'</div>'+
											'<div class="col-lg-5 col-sm-12">'+
												'<small>Input By : '+detailInput+'</small><br/>'+
												'<small>Notes : '+notes+'</small>'+
											'</div>'+
											'<div class="col-lg-1 col-sm-12 text-right">'+
												'<p class="font-weight-bold text-right costPerDetails">'+nominalCost+'</p>'+
											'</div>'+
										'</div>'+
									'</div>';
	}
	
	if(totalVoucherDetails > 0){
		$('#btnVoucherListDetails').removeClass("d-none");
		$('#btnVoucherListDetails').on('click', function(e) {
			openModalVoucher(idReservation);
		});
	}
	
	return newDetailsElem;
}

function calculateTicketProduct(){
	var ticketAdultPax		=	$("#ticketAdultPax").val().replace(/[^0-9\.]+/g, ''),
		ticketChildPax		=	$("#ticketChildPax").val().replace(/[^0-9\.]+/g, ''),
		ticketInfantPax		=	$("#ticketInfantPax").val().replace(/[^0-9\.]+/g, ''),
		pricePerPaxAdult	=	$("#pricePerPaxAdult").val().replace(/[^0-9\.]+/g, ''),
		pricePerPaxChild	=	$("#pricePerPaxChild").val().replace(/[^0-9\.]+/g, ''),
		pricePerPaxInfant	=	$("#pricePerPaxInfant").val().replace(/[^0-9\.]+/g, '');
	
	var totalPriceAdult		=	ticketAdultPax * pricePerPaxAdult,
		totalPriceChild		=	ticketChildPax * pricePerPaxChild,
		totalPriceInfant	=	ticketInfantPax * pricePerPaxInfant,
		totalPriceTicket	=	totalPriceAdult + totalPriceChild + totalPriceInfant;

	$("#totalPriceAdult").val(numberFormat(totalPriceAdult));	
	$("#totalPriceChild").val(numberFormat(totalPriceChild));	
	$("#totalPriceInfant").val(numberFormat(totalPriceInfant));	
	$("#ticketProductNominal").val(numberFormat(totalPriceTicket));	
}

function calculateCostMarginReservation(){
	var totalIncome		=	$("#totalIncomeStr").html().replace(/[^0-9\.]+/g, '');
	var totalCost		=	marginReservation	=	0;
		
	$(".costPerDetails").each(function() {
		totalCost		=	totalCost + parseFloat($(this).html().replace(/[^0-9\.]+/g, ''), 10);
	});

	marginReservation	=	(totalIncome * 1) - totalCost;
	$("#totalCostStr").html("- "+numberFormat(totalCost));
	$("#totalMarginStr").html(numberFormat(marginReservation));	
}

function editReservationDetailsTicket(idReservationDetails){
	var dataSend		=	{idReservationDetails:idReservationDetails};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getReservationDetailsTicket",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#editorReservationDetailsTicket-reservationTitleStr").html('-');
			$("#editorTicket-ticketAdultPax, #editorTicket-pricePerPaxAdult, #editorTicket-totalPriceAdult, #editorTicket-ticketChildPax, #editorTicket-pricePerPaxChild, #editorTicket-totalPriceChild").val(0);
			$("#editorTicket-ticketInfantPax, #editorTicket-pricePerPaxInfant, #editorTicket-totalPriceInfant, #editorTicket-ticketProductNominal").val(0);
			$("#editorTicket-ticketProductNotes").val('-');
			$("#editorTicket-correctionNotes").val('');
			$('#editorTicket-optionProductTicketVendor').empty();
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailData			=	response.detailData,
					productTicketVendor	=	response.productTicketVendor,
					ticketDetail		=	response.ticketDetail;

				$("#editorReservationDetailsTicket-reservationTitleStr").html('['+detailData.SOURCENAME+' - '+detailData.BOOKINGCODE+'] '+detailData.RESERVATIONTITLE+' <span class="badge badge-primary pull-right">'+detailData.SCHEDULEDATE+'</span>');
				$("#editorTicket-ticketAdultPax").val(numberFormat(ticketDetail.PAXADULT));
				$("#editorTicket-pricePerPaxAdult").val(numberFormat(ticketDetail.PRICEPERPAXADULT));
				$("#editorTicket-totalPriceAdult").val(numberFormat(ticketDetail.PRICETOTALADULT));
				
				$("#editorTicket-ticketChildPax").val(numberFormat(ticketDetail.PAXCHILD));
				$("#editorTicket-pricePerPaxChild").val(numberFormat(ticketDetail.PRICEPERPAXCHILD));
				$("#editorTicket-totalPriceChild").val(numberFormat(ticketDetail.PRICETOTALCHILD));
				
				$("#editorTicket-ticketInfantPax").val(numberFormat(ticketDetail.PAXINFANT));
				$("#editorTicket-pricePerPaxInfant").val(numberFormat(ticketDetail.PRICEPERPAXINFANT));
				$("#editorTicket-totalPriceInfant").val(numberFormat(ticketDetail.PRICETOTALINFANT));
				
				$("#editorTicket-ticketProductNominal").val(numberFormat(detailData.NOMINAL));
				$("#editorTicket-ticketProductNotes").val(detailData.NOTES);
				$("#editorTicket-correctionNotes").val(detailData.CORRECTIONNOTES);
				$("#editorTicket-idReservationDetailsEditor").val(idReservationDetails);
				
				generateOptionHelperVendorProduct(productTicketVendor, detailData.PRODUCTNAME);
				$('#modal-editorReservationDetailsTicket').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

function generateOptionHelperVendorProduct(productTicketVendor, productName){
	if(productTicketVendor.length > 0){
		$.each(productTicketVendor, function(index, array) {
			var selected	=	productName == array.PRODUCTNAME ? 'selected' : '';
			$('#editorTicket-optionProductTicketVendor').append($('<option '+selected+' data-voucherStatus="'+array.VOUCHERSTATUS+'" data-productname="'+array.PRODUCTNAME+'" data-vendorname="'+array.VENDORNAME+'" data-priceAdult="'+array.PRICEADULT+'" data-priceChild="'+array.PRICECHILD+'" data-priceInfant="'+array.PRICEINFANT+'" data-notes="'+array.NOTES+'"></option>').val(array.VALUE).html(array.OPTIONTEXT));
		});
			
		$('#editorTicket-optionProductTicketVendor').select2({
			dropdownParent: $("#modal-editorReservationDetailsTicket")
		});
	}

	$("#editorTicket-optionProductTicketVendor").change(function() {		
		var selectedItem	=	$(this).val();
		var priceAdult		=	$('option:selected', this).data("priceadult");
		var priceChild		=	$('option:selected', this).data("pricechild");
		var priceInfant		=	$('option:selected', this).data("priceinfant");
		var notes			=	$('option:selected', this).data("notes");
		
		$("#editorTicket-pricePerPaxAdult").val(numberFormat(priceAdult));
		$("#editorTicket-pricePerPaxChild").val(numberFormat(priceChild));
		$("#editorTicket-pricePerPaxInfant").val(numberFormat(priceInfant));
		$("#editorTicket-ticketProductNotes").val(notes);
		calculateTicketProductEditorTicket();
	});
}

function calculateTicketProductEditorTicket(){
	var ticketAdultPax		=	$("#editorTicket-ticketAdultPax").val().replace(/[^0-9\.]+/g, ''),
		ticketChildPax		=	$("#editorTicket-ticketChildPax").val().replace(/[^0-9\.]+/g, ''),
		ticketInfantPax		=	$("#editorTicket-ticketInfantPax").val().replace(/[^0-9\.]+/g, ''),
		pricePerPaxAdult	=	$("#editorTicket-pricePerPaxAdult").val().replace(/[^0-9\.]+/g, ''),
		pricePerPaxChild	=	$("#editorTicket-pricePerPaxChild").val().replace(/[^0-9\.]+/g, ''),
		pricePerPaxInfant	=	$("#editorTicket-pricePerPaxInfant").val().replace(/[^0-9\.]+/g, '');
	
	var totalPriceAdult		=	ticketAdultPax * pricePerPaxAdult,
		totalPriceChild		=	ticketChildPax * pricePerPaxChild,
		totalPriceInfant	=	ticketInfantPax * pricePerPaxInfant,
		totalPriceTicket	=	totalPriceAdult + totalPriceChild + totalPriceInfant;

	$("#editorTicket-totalPriceAdult").val(numberFormat(totalPriceAdult));
	$("#editorTicket-totalPriceChild").val(numberFormat(totalPriceChild));
	$("#editorTicket-totalPriceInfant").val(numberFormat(totalPriceInfant));
	$("#editorTicket-ticketProductNominal").val(numberFormat(totalPriceTicket));
}

$('#content-editorReservationDetailsTicket').off('submit');
$('#content-editorReservationDetailsTicket').on('submit', function(e) {
	e.preventDefault();
	var idVendor			=	$("#editorTicket-optionProductTicketVendor").val(),
		nominalCost			=	$("#editorTicket-ticketProductNominal").val(),
		ticketAdultPax		=	$("#editorTicket-ticketAdultPax").val(),
		ticketChildPax		=	$("#editorTicket-ticketChildPax").val(),
		ticketInfantPax		=	$("#editorTicket-ticketInfantPax").val(),
		pricePerPaxAdult	=	$("#editorTicket-pricePerPaxAdult").val(),
		pricePerPaxChild	=	$("#editorTicket-pricePerPaxChild").val(),
		pricePerPaxInfant	=	$("#editorTicket-pricePerPaxInfant").val(),
		totalPriceAdult		=	$("#editorTicket-totalPriceAdult").val(),
		totalPriceChild		=	$("#editorTicket-totalPriceChild").val(),
		totalPriceInfant	=	$("#editorTicket-totalPriceInfant").val(),
		notes				=	$("#editorTicket-ticketProductNotes").val(),
		correctionNotes		=	$("#editorTicket-correctionNotes").val(),
		vendorName			=	$('#editorTicket-optionProductTicketVendor option:selected').attr('data-vendorname'),
		productName			=	$('#editorTicket-optionProductTicketVendor option:selected').attr('data-productname'),
		voucherStatus		=	$('#editorTicket-optionProductTicketVendor option:selected').attr('data-voucherStatus'),
		idReservationDetails=	$("#editorTicket-idReservationDetailsEditor").val();
	
	var dataSend		=	{
								idReservationDetails:idReservationDetails,
								idVendor:idVendor,
								productName:productName,
								vendorName:vendorName,
								nominalCost:nominalCost,
								ticketAdultPax:ticketAdultPax,
								ticketChildPax:ticketChildPax,
								ticketInfantPax:ticketInfantPax,
								pricePerPaxAdult:pricePerPaxAdult,
								pricePerPaxChild:pricePerPaxChild,
								pricePerPaxInfant:pricePerPaxInfant,
								totalPriceAdult:totalPriceAdult,
								totalPriceChild:totalPriceChild,
								totalPriceInfant:totalPriceInfant,
								notes:notes,
								correctionNotes:correctionNotes,
								voucherStatus:voucherStatus
							};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/updateReservationDetailsTicket",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-editorReservationDetailsTicket :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-editorReservationDetailsTicket :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');
					
			if(response.status == 200){
				getSetDetailsReservation($("#idReservationDetailCosts").val(), false);
				$('#modal-editorReservationDetailsTicket').modal('hide');
			}			
		}
	});
	
});

function editReservationDetailsTransport(idReservationDetails){
	var dataSend		=	{idReservationDetails:idReservationDetails};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getReservationDetailsTransport",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#editorTransport-nominalFee, #editorTransport-idReservationDetailsEditor").val(0);
			$("#editorTransport-productName, #editorTransport-notesFee, #editorTransport-correctionNotes").val('');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailData			=	response.detailData;

				$("#editorTransport-productName").val(detailData.PRODUCTNAME);
				$("#editorTransport-nominalFee").val(numberFormat(detailData.NOMINAL));
				$("#editorTransport-notesFee").val(detailData.NOTES);
				$("#editorTransport-correctionNotes").val(detailData.CORRECTIONNOTES);
				$("#editorTransport-idReservationDetailsEditor").val(idReservationDetails);
				
				$('#modal-editorReservationDetailsTransport').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#content-editorReservationDetailsTransport').off('submit');
$('#content-editorReservationDetailsTransport').on('submit', function(e) {
	e.preventDefault();
	var productName				=	$("#editorTransport-productName").val(),
		nominal					=	$("#editorTransport-nominalFee").val(),
		notes					=	$("#editorTransport-notesFee").val(),
		correctionNotes			=	$("#editorTransport-correctionNotes").val(),
		correctionNotesClear	=	correctionNotes.replace(/\s+/g, ''),
		correctionNotesLength	=	correctionNotes.length,
		idReservationDetails	=	$("#editorTransport-idReservationDetailsEditor").val();
	
	var dataSend		=	{
								idReservationDetails:idReservationDetails,
								productName:productName,
								nominal:nominal,
								notes:notes,
								correctionNotes:correctionNotes,
								correctionNotesLength:correctionNotesLength
							};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/updateReservationDetailsTransport",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-editorReservationDetailsTransport :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-editorReservationDetailsTransport :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');
					
			if(response.status == 200){
				getSetDetailsReservation($("#idReservationDetailCosts").val(), false);
				$('#modal-editorReservationDetailsTransport').modal('hide');
			}			
		}
	});
});

function deleteDetailsReservation(idReservation, idReservationDetails, productType, productName, vendorName, strDateSchedule, dateSchedule){
	var confirmText	=	'This details will be deleted from reservation. Details ;<br/><br/>'+
						'<div class="order-details-customer-info">'+
							'<ul class="ml-5">'+
								'<li> <span>Date</span> <span>'+strDateSchedule+'</span> </li>'+
								'<li> <span>Product Type</span> <span>'+productType+'</span> </li>'+
								'<li> <span>Product Name</span> <span>'+productName+' hours</span> </li>'+
								'<li> <span>Vendor</span> <span>'+vendorName+'</span> </li>'+
							'</ul>'+
						'</div>'+
						'Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idReservation', idReservation).attr('data-idData', idReservationDetails).attr('data-dateSchedule', dateSchedule).attr('data-function', "deactivateReservationDetails");
	$confirmDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData			=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
		funcName		=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		dataSend		=	{idData:idData},
		idReservation	=	funcName == "deactivateReservationDetails" ? $confirmDialog.find('#confirmBtn').attr('data-idReservation') : "";
		dateSchedule	=	funcName == "deactivateReservationDetails" ? $confirmDialog.find('#confirmBtn').attr('data-dateSchedule') : "";
	
	if(funcName == "printPdf"){
		var urlPdfFile	=	$confirmDialog.find('#confirmBtn').attr('data-urlpdf');
		
		if(urlPdfFile !== null && urlPdfFile !== undefined && urlPdfFile != ""){
			$confirmDialog.modal('hide');
			window.open(urlPdfFile, '_blank');			
		}
	} else {
		if(funcName == "deactivateReservationDetails") Object.assign(dataSend, {idReservation:idReservation, dateSchedule:dateSchedule});
		if(funcName == "cancelReservation") {
			var refundType	=	$("#cancelDialog-optionRefundType").val();
			dataSend['refundType'] = refundType;
		}

		$.ajax({
			type: 'POST',
			url: baseURL+"reservation/"+funcName,
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

				if(response.status == 200){
					if(funcName == "deactivateReservationDetails"){
						$("#reservationDetails"+idData).remove();
						calculateCostMarginReservation();
						if($(".detailsChildElement").length <= 0){
							$("#noDataListOfDetails").removeClass("d-none");
						}
					} else if(funcName == "deleteReservationVoucher") {
						$("#elemVoucherItem"+idData).remove();
						if($(".elemVoucherItem").length <= 0){
							$("#containerEmptyListVoucher").removeClass("d-none");
							$("#containerListVoucher").addClass("d-none");						
						}
					} else if(funcName == "deleteReservationPayment") {
						$("#trReservationPayment"+idData).remove();
						calculateTotalPaymentFinance();
						if($(".elemReservationPayment").length <= 0){
							var rowsPayment	=	'<tr id="noDataReservationPayment"><td colspan="8" class="text-center text-bold">No data found</td></tr>';
							$('#table-reservationPayment > tbody').html(rowsPayment);	
						}
					} else {
						getDataReservation();
					}
				}
			}
		});
	}
});

function cancelReservation(idReservation, reservationTitle, dateStart, dateEnd, customerName){
	var strDateReservation	=	dateStart == dateEnd ? dateStart : dateStart+" - "+dateEnd;
	var confirmText			=	'This reservation will be deleted. Details ;<br/><br/>'+
								'<div class="order-details-customer-info">'+
									'<ul class="ml-5">'+
										'<li> <span>Date</span> <span>'+strDateReservation+'</span> </li>'+
										'<li> <span>Customer Name</span> <span>'+customerName+'</span> </li>'+
										'<li> <span>Reservation Title</span> <span></span> </li>'+
									'</ul>'+
									'<p class="ml-10">'+reservationTitle+'</p>'+
								'</div><br/>'+
								'Are you sure? Please select the cancellation refund type.<br/>'+
								'<div class="form-group mt-20 pt-20 border-top">'+
									'<label for="cancelDialog-optionRefundType" class="control-label">Refund Type</label>'+
									'<select id="cancelDialog-optionRefundType" name="cancelDialog-optionRefundType" class="form-control">'+
										'<option value="0">No Refund</option>'+
										'<option value="-1">Full Refund</option>'+
										'<option value="-2">Partial Refund</option>'+
									'</select>'+
								'</div>';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idReservation).attr('data-function', "cancelReservation");
	$confirmDialog.modal('show');
}

function openEditorRefundStatus(idReservation, bookingCode, refundType, reservationTitle, reservationDateStart, reservationDateEnd, customerName){
	var strDateReservation	=	reservationDateStart == reservationDateEnd ? reservationDateStart : reservationDateStart+" - "+reservationDateEnd;
	$("#editorRefundType-bookingCode").html("<b>"+bookingCode+"</b>");
	$("#editorRefundType-reservationTitle").html(reservationTitle);
	$("#editorRefundType-strDateReservation").html(strDateReservation);
	$("#editorRefundType-customerName").html(customerName);
	$("#editorRefundType-optionRefundType, #editorRefundType-originRefundType").val(refundType);
	$("#editorRefundType-idReservation").val(idReservation);
	
	$("#modal-editorRefundType").modal('show');
}

$('#content-editorRefundType').off('submit');
$('#content-editorRefundType').on('submit', function(e) {
	e.preventDefault();
	var idReservation	=	$("#editorRefundType-idReservation").val(),
		refundType		=	$("#editorRefundType-optionRefundType").val(),
		refundTypeOrigin=	$("#editorRefundType-originRefundType").val(),
		dataSend		=	{idReservation:idReservation, refundType:refundType};
	
	if(refundType == refundTypeOrigin){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Please select a refund type that is different from the origin type");
		});
		$('#modalWarning').modal('show');
	} else {
		$.ajax({
			type: 'POST',
			url: baseURL+"reservation/updateRefundTypeReservation",
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
					getDataReservation();
					$('#modal-editorRefundType').modal('hide');
				}
			}
		});
	}
});

$('#modal-selectReservationVoucher').off('show.bs.modal');
$('#modal-selectReservationVoucher').on('show.bs.modal', function(event) {
	$("#reservationKeyword").val("");
	$("#containerSelectReservationResult").html(
		'<div class="col-sm-12 text-center mx-auto my-auto">'+
			'<h2><i class="fa fa-list-alt text-warning"></i></h2>'+
			'<b class="text-warning">Results goes here</b>'+
		'</div>'
	);
});

$('#container-selectReservationVoucher').off('submit');
$('#container-selectReservationVoucher').on('submit', function(e) {
	e.preventDefault();
	var reservationKeyword	=	$("#reservationKeyword").val(),
		dataSend			=	{reservationKeyword:reservationKeyword};
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/searchListReservationForVoucher",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#container-selectReservationVoucher :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#container-selectReservationVoucher :input").attr("disabled", false);

			if(response.status == 200){
				var reservationList	=	response.reservationList,
					reservationRows	=	"";
					
				$.each(reservationList, function(index, array) {

					var	reservationDateEnd	=	'',
						idReservation		=	array.IDRESERVATION;
					if(array.DURATIONOFDAY > 1){
						reservationDateEnd	=	'<br/><b class="text-secondary">'+array.RESERVATIONDATEEND+'</b>';
					}					
					reservationRows	+=	'<div class="col-sm-12 pb-1 mb-5 border-bottom">'+
											'<div class="row pt-10 pb-1">'+
												'<div class="col-lg-2 col-sm-12">'+
													'<span class="badge badge-outline badge-primary">'+array.SOURCENAME+'</span><br/><br/>'+
													'<b>'+array.BOOKINGCODE+'</b><br/>'+
													'<b class="text-primary">'+array.RESERVATIONDATESTART+'</b>'+
													reservationDateEnd+
												'</div>'+
												'<div class="col-lg-8 col-sm-8">'+
													'<b>'+array.RESERVATIONTITLE+'</b><br/>'+
													'<p>'+
														'Customer : <span id="voucherSpanCustomerName'+idReservation+'">'+array.CUSTOMERNAME+'</span> ('+array.CUSTOMERCONTACT+' / '+array.CUSTOMEREMAIL+')<br/>'+
														'Pax : '+
															'<span id="voucherSpanPaxAdult'+idReservation+'">'+array.NUMBEROFADULT+'</span> + '+
															'<span id="voucherSpanPaxChild'+idReservation+'">'+array.NUMBEROFCHILD+'</span> + '+
															'<span id="voucherSpanPaxInfant'+idReservation+'">'+array.NUMBEROFINFANT+'</span><br/>'+
														'Hotel / Pick Up : <br/>'+array.HOTELNAME+' / '+array.PICKUPLOCATION+
													'</p>'+
												'</div>'+
												'<div class="col-lg-2 col-sm-4 text-center">'+
													'<button type="button" class="button button-sm" onclick="generateVoucherDataForm('+idReservation+')"><span><i class="fa fa-pencil"></i>Choose</span></button>'+
												'</div>'+
											'</div>'+
											'<input type="hidden" id="arrDateSchedule'+idReservation+'" name="arrDateSchedule'+idReservation+'" value="'+array.ARRDATESCHEDULE+'">'+
										'</div>';
					
				});
				
				$("#containerSelectReservationResult").html(reservationRows);
			} else {
				$("#containerSelectReservationResult").html(
					'<div class="col-sm-12 text-center mx-auto my-auto">'+
						'<h2><i class="fa fa-search-minus text-danger"></i></h2>'+
						'<b class="text-danger">No active reservations found based on the keywords you entered</b>'+
					'</div>'
				);
			}
		}
	});
});

function generateVoucherDataForm(idReservation){
	var customerName		=	$("#voucherSpanCustomerName"+idReservation).html(),
		paxAdult			=	$("#voucherSpanPaxAdult"+idReservation).html(),
		paxChild			=	$("#voucherSpanPaxChild"+idReservation).html(),
		paxInfant			=	$("#voucherSpanPaxInfant"+idReservation).html(),
		detailData			=	{
									CUSTOMERNAME:customerName,
									NUMBEROFADULT:paxAdult,
									NUMBEROFCHILD:paxChild,
									NUMBEROFINFANT:paxInfant,
								},
		dataOpt				=	JSON.parse(localStorage.getItem('optionHelper')),
		ticketVendor		=	dataOpt['dataVendorTicket'],
		arrDateScheduleSplit=	$("#arrDateSchedule"+idReservation).val().split(','),
		arrVendorVoucher	=	[],
		arrDateSchedule		=	[];
	
	if(ticketVendor.length > 0){
		$.each(ticketVendor, function(index, array) {
			arrVendorVoucher.push([array.ID, array.VALUE]);
		});
	}
	
	if(arrDateScheduleSplit.length > 0){
		for(var i=0; i<arrDateScheduleSplit.length; i++){
			var convertDate		=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("YYYY-MM-DD"),
				convertDateStr	=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("DD MMM YYYY");
			arrDateSchedule.push([convertDate, convertDateStr]);
		}
	}
	
	generateFormCreateVoucher(idReservation, detailData, "", arrVendorVoucher, arrDateSchedule);
	$('#modal-selectReservationVoucher').modal('hide');
	$('#modal-editorCreateVoucher').modal('show');
}

function openModalVoucher(idReservation){
	var dataSend		=	{idReservation:idReservation};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getReservationVoucherList",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$('#optionVendorVoucher').empty();
			$("#containerListVoucher").html("");
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailData				=	response.detailData,
					listVoucher				=	response.listVoucher,
					serviceNameVoucher		=	response.serviceNameVoucher,
					arrVendorVoucher		=	response.arrVendorVoucher,
					arrDateSchedule			=	response.arrDateSchedule,
					reservationDateTimeStr	=	detailData.RESERVATIONDATESTART;
					
				if(detailData.DURATIONOFDAY > 1){
					reservationDateTimeStr	=	reservationDateTimeStr+" - "+detailData.RESERVATIONDATEEND;
				}
				
				$("#textVoucherSource").html(detailData.SOURCENAME);
				$("#textVoucherTitle").html(detailData.RESERVATIONTITLE);
				$("#textVoucherGuestName").html(detailData.CUSTOMERNAME);
				$("#textVoucherDate").html(reservationDateTimeStr);
				
				if(listVoucher.length > 0){
					var elemVoucherItem	=	generateVoucherElem(listVoucher);
					$("#containerEmptyListVoucher").addClass("d-none");
					$("#containerListVoucher").html(elemVoucherItem).removeClass("d-none");
				} else {
					$("#containerEmptyListVoucher").removeClass("d-none");
					$("#containerListVoucher").addClass("d-none");
				}
				
				generateFormCreateVoucher(idReservation, detailData, serviceNameVoucher, arrVendorVoucher, arrDateSchedule);			
				$('#modal-voucherList').modal('show');
				
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

function generateFormCreateVoucher(idReservation, detailData, serviceNameVoucher, arrVendorVoucher, arrDateSchedule){
	$('#idReservationCreateVoucher').val(idReservation);
	$("#guestNameVoucher").val(detailData.CUSTOMERNAME);
	$("#serviceNameVoucher").val(serviceNameVoucher);
	$("#paxName1").val("Adult");
	$("#paxName2").val("Child");
	$("#paxName3").val("Infant");
	$("#paxTotal1").val(detailData.NUMBEROFADULT);
	$("#paxTotal2").val(detailData.NUMBEROFCHILD);
	$("#paxTotal3").val(detailData.NUMBEROFINFANT);
	$("#voucherNotes").val("");
	
	if(arrVendorVoucher.length > 0){
		for(var i=0; i<arrVendorVoucher.length; i++){
			$('#optionVendorVoucher').append($('<option></option>').val(arrVendorVoucher[i][0]).html(arrVendorVoucher[i][1]));
		}
	}
	
	var cbDateSchedule		=	"";
	if(arrDateSchedule.length > 0){
		if(arrDateSchedule.length == 1){
			cbDateSchedule	=	'<label class="adomx-checkbox"><input checked disabled name="cbDateVoucher[]" class="cbDateVoucher" type="checkbox" value="'+arrDateSchedule[0][0]+'"> <i class="icon"></i> '+arrDateSchedule[0][1]+'</label>';
		} else {
			for(var iCB=0; iCB<arrDateSchedule.length; iCB++){
				cbDateSchedule	+=	'<label class="adomx-checkbox"><input name="cbDateVoucher[]" class="cbDateVoucher" type="checkbox" value="'+arrDateSchedule[iCB][0]+'"> <i class="icon"></i> '+arrDateSchedule[iCB][1]+'</label>';
			}
		}
		$("#containerVoucherDate").html(cbDateSchedule);
	}
}

$('#container-editorCreateVoucher').off('submit');
$('#container-editorCreateVoucher').on('submit', function(e) {
	e.preventDefault();
	var idReservation	=	$("#idReservationCreateVoucher").val(),
		dataForm		=	$("#container-editorCreateVoucher :input").serializeArray(),
		vendorName		=	$("#optionVendorVoucher option:selected").text(),
		arrDateVoucher	=	[];
	
	$('input:checkbox.cbDateVoucher').each(function () {
		var checkboxVal	=	(this.checked ? $(this).val() : false);
		
		if(checkboxVal){
			arrDateVoucher.push(checkboxVal);
		}
	});
	
	var dataSend		=	{vendorName:vendorName, arrDateVoucher:arrDateVoucher};	
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/saveReservationVoucher",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#container-editorCreateVoucher :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#container-editorCreateVoucher :input").attr("disabled", false);
			
			if(response.status == 200){
				var newVoucherDetail	=	response.newVoucherDetail,
					newVoucherElem		=	generateVoucherElem(newVoucherDetail);

				$("#containerEmptyListVoucher").addClass("d-none");
				$("#containerListVoucher").append(newVoucherElem).removeClass("d-none");
				$('#modal-editorCreateVoucher').modal('hide');
				
				var newVoucherDetail	=	response.newVoucherDetail,
					urlPdfFile			=	newVoucherDetail[0]['URLPDFFILEVOUCHER'],
					confirmText			=	response.msg+'.<br/>Do you want to show voucher file?';
					
				$confirmDialog.find('#modal-confirm-body').html(confirmText);
				$confirmDialog.find('#confirmBtn').attr('data-urlpdf', urlPdfFile).attr('data-function', "printPdf");
				$confirmDialog.modal('show');
				
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}
		}
	});
});

function generateVoucherElem(listVoucher){
	var elemVoucherItem	=	"";
	$.each(listVoucher, function(index, array) {
		elemVoucherItem	+=	'<div class="col-sm-12 mt-20 mb-5 rounded-lg elemVoucherItem" style="border: 1px solid #e0e0e0;" id="elemVoucherItem'+array.IDRESERVATIONVOUCHER+'">'+
								'<div class="row pt-10 pb-1">'+
									'<div class="col-lg-4 col-sm-12">'+
										'<p class="font-weight-bold mb-0">#'+array.VOUCHERCODE+'</p>'+
										'<p>'+array.SERVICEDATE+'</p>'+
									'</div>'+
									'<div class="col-lg-7 col-sm-9">'+
										'<p class="font-weight-bold mb-0">'+array.VENDORNAME+'</p>'+
										'<p>Service : '+array.SERVICENAME+'</p>'+
									'</div>'+
									'<div class="col-lg-1 col-sm-3">'+
										'<a class="button button-xs button-box button-info" href="'+array.URLPDFFILEVOUCHER+'" target="_blank"><i class="fa fa-file-pdf-o"></i></a>'+
										'<button class="button button-xs button-box button-danger" onclick="confirmDeleteReservationVoucher('+array.IDRESERVATIONVOUCHER+', \''+array.VOUCHERCODE+'\', \''+array.GUESTNAME+'\', \''+array.SERVICEDATE+'\', \''+array.SERVICENAME+'\')"><i class="fa fa-trash"></i></button>'+
									'</div>'+
								'</div>'+
							'</div>';
	});
	return elemVoucherItem;
}

function confirmDeleteReservationVoucher(idReservationVoucher, voucherCode, guestName, serviceDate, serviceName){
	var confirmText	=	'This voucher will be deleted. Details ;'+
								'<div class="order-details-customer-info">'+
									'<ul class="ml-5">'+
										'<li> <span>Voucher Code</span> <span>'+voucherCode+'</span> </li>'+
										'<li> <span>Customer Name</span> <span>'+guestName+'</span> </li>'+
										'<li> <span>Date</span> <span>'+serviceDate+'</span> </li>'+
										'<li> <span>Service</span> <span>'+serviceName+'</span> </li>'+
									'</ul>'+
								'</div><br/>'+
								'<br/><br/>Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idReservation', 0).attr('data-idData', idReservationVoucher).attr('data-function', "deleteReservationVoucher");
	$confirmDialog.modal('show');
}

function showPaymentDetails(idReservation){
	var dataSend		=	{idReservation:idReservation};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/getDetailPayment",
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
				
				var detailReservation		=	response.detailReservation,
					arrDateSchedule			=	response.arrDateSchedule,
					arrDateScheduleSplit	=	arrDateSchedule.split(","),
					reservationPaymentList	=	response.reservationPaymentList,
					totalPaxAdult			=	detailReservation.NUMBEROFADULT * 1,
					totalPaxChild			=	detailReservation.NUMBEROFCHILD * 1,
					totalPaxInfant			=	detailReservation.NUMBEROFINFANT * 1,
					totalPax				=	totalPaxAdult+totalPaxChild+totalPaxInfant,
					reservationDateTimeStr	=	detailReservation.RESERVATIONDATESTART+" "+detailReservation.RESERVATIONTIMESTART;
					
				if(detailReservation.DURATIONOFDAY > 1){
					reservationDateTimeStr	=	reservationDateTimeStr+" - "+detailReservation.RESERVATIONDATEEND+" "+detailReservation.RESERVATIONTIMEEND;
				}
					
				var badgeReservationType	=	'<span class="badge badge-'+arrBadgeType[detailReservation.IDRESERVATIONTYPE]+'">'+detailReservation.RESERVATIONTYPE+'</span>',
					areaName				=	detailReservation.AREANAME.toLowerCase() == "without transfer" ? "<b class='text-danger'>"+detailReservation.AREANAME+"</b>" : detailReservation.AREANAME,
					rowsPayment				=	"",
					totalPaymentFinance		=	0;

				$("#reservationTitlePaymentStr").html("["+detailReservation.SOURCENAME+"] "+detailReservation.RESERVATIONTITLE);
				$("#reservationDateTimePaymentStr").html(reservationDateTimeStr);
				$("#reservationTypePaymentBadge").html(badgeReservationType);
				$("#durationOfDayPaymentStr").html(detailReservation.DURATIONOFDAY+" Day(s)");
				$("#custNamePaymentStr").html(detailReservation.CUSTOMERNAME);
				$("#custContactPaymentStr").html(detailReservation.CUSTOMERCONTACT);
				$("#custEmailPaymentStr").html(detailReservation.CUSTOMEREMAIL);
				$("#paxAdultPaymentStr").html(detailReservation.NUMBEROFADULT);
				$("#paxChildPaymentStr").html(detailReservation.NUMBEROFCHILD);
				$("#paxInfantPaymentStr").html(detailReservation.NUMBEROFINFANT);
				$("#bookingCodePaymentStr").html(detailReservation.BOOKINGCODE);
				$("#paymentNominalPaymentStr").html("["+detailReservation.INCOMEAMOUNTCURRENCY+"] "+numberFormat(detailReservation.INCOMEAMOUNT)+" <b>x</b> "+numberFormat(detailReservation.INCOMEEXCHANGECURRENCY));
				$("#remarkPaymentStr").html(detailReservation.REMARK);
				
				if(reservationPaymentList.length > 0){
					$.each(reservationPaymentList, function(index, arrayPayment) {
						rowsPayment			+=	generatePaymentElem(arrayPayment);
						totalPaymentFinance	+=	arrayPayment.AMOUNTIDR * 1;
					});
				} else {
					rowsPayment	=	'<tr id="noDataReservationPayment">'+
										'<td colspan="8" class="text-center text-bold">No data found</td>'+
									'</tr>';
				}
				
				$('#optionDateCollect').empty();
				if(arrDateScheduleSplit.length > 0){
					for(var i=0; i<arrDateScheduleSplit.length; i++){
						var convertDate		=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("YYYY-MM-DD"),
							convertDateStr	=	moment(arrDateScheduleSplit[i],"YYYYMMDD").format("DD MMM YYYY");
						$('#optionDateCollect').append($('<option></option>').val(convertDate).html(convertDateStr));
					}
				}
				
				$("#totalRevenueReservation").html("[IDR] "+numberFormat(detailReservation.INCOMEAMOUNTIDR));
				$("#totalPaymentFinance").html("[IDR] "+numberFormat(totalPaymentFinance));
				$('#table-reservationPayment > tbody').html(rowsPayment);
				$('#btnCreateNewPayment').attr("data-idReservation", idReservation);
				$('#modal-listReservationPayment').modal('show');
				
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#modal-editorCreatePayment').off('show.bs.modal');
$('#modal-editorCreatePayment').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);  
	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {
			var actionType	=	$activeElement.attr('data-action');
			if($activeElement.attr('data-action') == "insert"){
				$("#container-editorCreatePayment :input").attr("disabled", false);
				$("#optionPaymentMethod").val($("#optionPaymentMethod option:first").val()).trigger('change');
				$("#idReservationPayment, #optionPaymentStatus, #paymentPriceInteger, #paymentPriceDecimal").val(0);
				$("#descriptionPayment").val("");
				$("#actionTypePayment").val(actionType);
				$("#checkboxUpsellingPayment").prop('checked', false);
				$("#isUpsellingCheckedOrigin").val("0");
				$("#idReservationCreatePayment").val($activeElement.attr('data-idReservation'));
				$("#containerCollectData, #containerOptionDriverCollect, #containerOptionVendorCollect, #contaierOptionDateCollect").addClass("d-none");
				$("#editablePayment").val(1);
				
				if($('#contaierOptionDateCollect option').length == 1){
					$("#optionDateCollect").val($("#optionDateCollect option:first").val());
				}
			}
		}
	}
});

$('#optionPaymentMethod').off('change');
$('#optionPaymentMethod').on('change', function(e) {
	var paymentMethod	=	$(this).val(),
		totalOptionDate	=	$('#contaierOptionDateCollect option').length;
	
	if(paymentMethod == 2){
		$("#containerOptionVendorCollect").addClass("d-none");
		$("#containerCollectData, #containerOptionDriverCollect").removeClass("d-none");
		$("#optionDriverCollect").val($("#optionDriverCollect option:first").val());
	} else if(paymentMethod == 7) {
		$("#containerOptionDriverCollect").addClass("d-none");
		$("#containerCollectData, #containerOptionVendorCollect").removeClass("d-none");
		$("#optionVendorCollect").val($("#optionVendorCollect option:first").val());
	} else {
		$("#containerCollectData, #containerOptionDriverCollect, #containerOptionVendorCollect, #contaierOptionDateCollect").addClass("d-none");
	}
	
	if((paymentMethod == 2 || paymentMethod == 7) && totalOptionDate > 1){
		$("#contaierOptionDateCollect").removeClass("d-none");
	} else {
		$("#contaierOptionDateCollect").addClass("d-none");
	}

	if(!dataIdPaymentMethodUpselling.includes(parseInt(paymentMethod))){
		$("#checkboxUpsellingPayment").prop('checked', false).prop('disabled', true);
	} else {
		var isUpsellingCheckedOrigin	=	$("#isUpsellingCheckedOrigin").val();
		$("#checkboxUpsellingPayment").prop('checked', false).prop('disabled', false);
		if(isUpsellingCheckedOrigin == '1') $("#checkboxUpsellingPayment").prop('checked', true);
	}
});

$('#container-editorCreatePayment').off('submit');
$('#container-editorCreatePayment').on('submit', function(e) {
	e.preventDefault();
	var idData				=	$("#idReservationPayment").val(),
		actionURL			=	idData == 0 ? "addReservationPayment" : "updateReservationPayment";
		dataForm			=	$("#container-editorCreatePayment :input").serializeArray(),
		paymentMethodName	=	$("#optionPaymentMethod option:selected").text(),
		optionPaymentMethod	=	$("#optionPaymentMethod").val(),
		optionPaymentStatus	=	$("#optionPaymentStatus").val(),
		descriptionPayment	=	$("#descriptionPayment").val(),
		editablePayment		=	$("#editablePayment").val(),
		dataSend			=	{paymentMethodName:paymentMethodName, optionPaymentMethod:optionPaymentMethod, optionPaymentStatus:optionPaymentStatus, descriptionPayment:descriptionPayment};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	dataSend['checkboxUpsellingPayment']	=	$("#checkboxUpsellingPayment").is(':checked') ? 1 : 0;
	
	$.ajax({
		type: 'POST',
		url: baseURL+"reservation/"+actionURL,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#container-editorCreatePayment :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#container-editorCreatePayment :input").attr("disabled", false);
			
			if(editablePayment != 1){
				$("#optionPaymentMethod, #optionPaymentStatus, #descriptionPayment").attr("disabled", true);
			}
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				var insertUpdateData	=	response.insertUpdateData,
					elemInsertUpdate	=	generatePaymentElem(insertUpdateData);
				
				if(actionURL == "addReservationPayment"){
					$('#table-reservationPayment > tbody').append(elemInsertUpdate);
				} else {
					$('#trReservationPayment'+insertUpdateData.IDRESERVATIONPAYMENT).replaceWith(elemInsertUpdate);
				}
				
				calculateTotalPaymentFinance();
				$("#noDataReservationPayment").remove();
				$('#modal-editorCreatePayment').modal('hide');
			}
		}
	});
});

function editReservationPayment(idReservationPayment, idPaymentMethod, status, description, amountCurrency, amount, editable, idVendorCollect, idDriverCollect, dateCollect, deletable, isUpselling){
	var expAmount		=	amount.split("."),
		amountInteger	=	expAmount[0],
		amountComma		=	expAmount[1];
	
	$("#isUpsellingCheckedOrigin").val(isUpselling);
	$("#optionPaymentMethod").val(idPaymentMethod).trigger('change');
	$("#optionPaymentStatus").val(status);
	$("#descriptionPayment").val(description);
	$("#paymentCurrency").val(amountCurrency);
	$("#paymentPriceInteger").val(numberFormat(amountInteger));
	$("#paymentPriceDecimal").val(numberFormat(amountComma));
	$("#optionDriverCollect").val(idDriverCollect);
	$("#optionVendorCollect").val(idVendorCollect);
	$("#actionTypePayment").val("update");
	$("#idReservationPayment").val(idReservationPayment);
	$("#idReservationCreatePayment").val(0);
	
	if(parseInt(isUpselling) == 1) $("#checkboxUpsellingPayment").prop('checked', true).prop('disabled', false);
	if(dateCollect == null || dateCollect == "" || dateCollect == undefined){
		$("#optionDateCollect").val($("#optionDateCollect option:first").val());
	} else {
		$("#optionDateCollect").val(dateCollect);
	}
	
	if(deletable != 1){
		$("#optionPaymentMethod, #optionPaymentStatus, #descriptionPayment").attr("disabled", true);
	} else {
		$("#optionPaymentMethod, #optionPaymentStatus, #descriptionPayment").attr("disabled", false);
	}
	
	$("#editablePayment").val(editable);
	$('#modal-editorCreatePayment').modal('show');
}

function deleteReservationPayment(idReservationPayment, paymentMethodName, description, amountCurrency, amount){
	var confirmText	=	'This payment details will be deleted from reservation. Details ;'+
							'<div class="order-details-customer-info mb-10 mt-10">'+
								'<ul class="ml-10">'+
									'<li> <span>Payment Method</span> <span><b>'+paymentMethodName+'</b></span> </li>'+
									'<li> <span>Description</span> <span><b>'+description+'</b></span> </li>'+
									'<li> <span>Amount</span> <span><b>['+amountCurrency+'] '+numberFormat(amount)+'</b></span> </li>'+
								'</ul>'+
							'</div>'+
						'Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idReservationPayment).attr('data-function', "deleteReservationPayment");
	$confirmDialog.modal('show');
}

function generatePaymentElem(dataPayment){
	var badgeUpselling	=	parseInt(dataPayment.ISUPSELLING) == 1 ? '<br/><span class="badge badge-primary">Upselling</span>' : '';
		badgeStatus		=	btnDeletePayment	=	"";
	switch(dataPayment.STATUS){
		case "0"	:	badgeStatus	=	'<span class="badge badge-warning">Pending</span>'; break;
		case "1"	:	badgeStatus	=	'<span class="badge badge-success">Paid</span>'; break;
		case "-1"	:	badgeStatus	=	'<span class="badge badge-danger">Cancel/Refund</span>'; break;
		default		:	badgeStatus	=	'<span class="badge badge-info">-</span>'; break;
	}
	
	btnEditPayment	=	'<button class="button button-xs button-box button-info" '+
								 'onclick="editReservationPayment('+dataPayment.IDRESERVATIONPAYMENT+', \''+
																	dataPayment.IDPAYMENTMETHOD+'\', \''+
																	dataPayment.STATUS+'\', \''+
																	dataPayment.DESCRIPTION+'\', \''+
																	dataPayment.AMOUNTCURRENCY+'\', \''+
																	dataPayment.AMOUNT+'\', \''+
																	dataPayment.EDITABLE+'\', \''+
																	dataPayment.IDVENDORCOLLECT+'\', \''+
																	dataPayment.IDDRIVERCOLLECT+'\', \''+
																	dataPayment.DATECOLLECT+'\', \''+
																	dataPayment.DELETABLE+'\', \''+
																	dataPayment.ISUPSELLING+'\')">'+
							'<i class="fa fa-pencil"></i>'+
						'</button><br/>';
	
	if(dataPayment.EDITABLE == 1){
		btnDeletePayment	=	'<button class="button button-xs button-box button-danger" '+
										 'onclick="deleteReservationPayment('+dataPayment.IDRESERVATIONPAYMENT+', \''+dataPayment.PAYMENTMETHODNAME+'\', \''+dataPayment.DESCRIPTION+'\', \''+dataPayment.AMOUNTCURRENCY+'\', \''+dataPayment.AMOUNT+'\')">'+
									'<i class="fa fa-times"></i>'+
								'</button><br/>';
	}
	
	rowsPayment	=	'<tr id="trReservationPayment'+dataPayment.IDRESERVATIONPAYMENT+'" class="elemReservationPayment">'+
						'<td>'+dataPayment.PAYMENTMETHODNAME+'<br/><small>'+dataPayment.DESCRIPTION+'</small>'+badgeUpselling+'</td>'+
						'<td>'+dataPayment.AMOUNTCURRENCY+'</td>'+
						'<td align="right">'+numberFormat(dataPayment.AMOUNT)+'</td>'+
						'<td align="right">'+numberFormat(dataPayment.EXCHANGECURRENCY)+'</td>'+
						'<td align="right" class="nominalPaymentFinance">'+numberFormat(dataPayment.AMOUNTIDR)+'</td>'+
						'<td>'+badgeStatus+'</td>'+
						'<td>'+dataPayment.USERINPUT+'<br/>'+dataPayment.DATETIMEINPUT+'</td>'+
						'<td>'+btnEditPayment+btnDeletePayment+'</td>'+
					'</tr>';
	
	return rowsPayment;
}

function calculateTotalPaymentFinance(){
	var totalPaymentFinance	=	0;
	$(".nominalPaymentFinance").each(function() {
		var nominalPaymentFinance	=	$(this).html(),
			nominalPaymentFinance	=	nominalPaymentFinance.replace(/[^0-9.]/g,"") * 1;
		totalPaymentFinance			+=	nominalPaymentFinance;
	});
	$("#totalPaymentFinance").html("[IDR] "+numberFormat(totalPaymentFinance));
}

reservationFunc();
