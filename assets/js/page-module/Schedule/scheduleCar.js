var $confirmDeleteDialog= $('#modal-confirm-action');

if (scheduleCarFunc == null){
	var scheduleCarFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			setOptionHelper('calendarTab-optionVendorCar', 'dataVendorCar');
			setOptionHelper('calendarTab-optionCarType', 'dataCarType');
			setOptionHelper('reservationTab-optionSource', 'dataSource');
			setOptionHelper('addCarDayOff-optionType', 'dataCarDayOffType');
			setOptionHelper('addCarDayOff-hourStart', 'dataHours');
			setOptionHelper('addCarDayOff-optionCarCostType', 'dataCarCostType');
			setOptionHelper('dropOffPickUpTab-optionVendor', 'dataVendorCar');
			setOptionHelper('dropOffPickUpTab-optionDriver', 'dataDriverCarRental');
			setOptionHelper('detailDropOffPickUp-driverHandle', 'dataDriverCarRental');

			$("#reservationTab-dateSchedule").val("");
			onChangeDateInputFilter($("#reservationTab-dateSchedule"));
			getCarSchedule();
		});	
	}
}

$('a[data-toggle="tab"]').off('shown.bs.tab');
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	var activeTabId = $(e.target).attr('href');

	switch (activeTabId) {
		case '#calendarTab'		: getCarSchedule(); break;
		case '#reservationTab'	: 
			var callbackFunc = null;
			if(localStorage.getItem('OSNotificationData') === null || localStorage.getItem('OSNotificationData') === undefined){
				setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
				setOptionHelper('optionYear', 'optionYear', false, false);
			} else {
				var OSNotificationData	=	JSON.parse(localStorage.getItem('OSNotificationData'));
				var OSNotifType			=	OSNotificationData.type;
				var OSNotifMonth		=	OSNotificationData.month;
				var OSNotifYear			=	OSNotificationData.year;

				setOptionHelper('optionMonth', 'optionMonth', OSNotifMonth, false);
				setOptionHelper('optionYear', 'optionYear', OSNotifYear, false);

				if(OSNotifType == "carschedule"){
					idReservationDetails=	OSNotificationData.idReservationDetails;
					$('.nav-tabs a[href="#reservationTab"]').tab('show');
					callbackFunc		=	function(){
						$('.addCarBtn[data-idreservationdetails='+idReservationDetails+']').trigger("click");
					}
					localStorage.removeItem("OSNotificationData");
				}	
			}
			getDataReservationSchedule(callbackFunc);
			break;
		case '#dropOffPickUpTab': getDataDropOffPickUpSchedule(); break;
	}
});

$('#optionMonth, #optionYear').off('change');
$('#optionMonth, #optionYear').on('change', function(e) {
	getCarSchedule();
	getDataReservationSchedule();
});

$('#calendarTab-optionVendorCar, #calendarTab-optionCarType').off('change');
$('#calendarTab-optionVendorCar, #calendarTab-optionCarType').on('change', function(e) {
	getCarSchedule();
});

$('#calendarTab-searchKeyword').off('keypress');
$("#calendarTab-searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
        getCarSchedule();
    }
});

function getCarSchedule(){
	var $tableBody		=	$('#table-carScheduleList > tbody'),
		columnNumber	=	1,
		month			=	$('#optionMonth').val(),
		year			=	$('#optionYear').val(),
		idVendorCar		=	$('#calendarTab-optionVendorCar').val(),
		idCarType		=	$('#calendarTab-optionCarType').val(),
		searchKeyword	=	$('#calendarTab-searchKeyword').val(),
		dataSend		=	{
			idVendorCar:idVendorCar,
			idCarType:idCarType,
			searchKeyword:searchKeyword,
			month:month,
			year:year
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/getDataCarSchedule",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$(".thHeaderDates").remove();
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var dataStatistic	=	response.dataStatistic,
				arrDates		=	response.arrDates,
				firstDateStr	=	response.firstDateStr,
				lastDateStr		=	response.lastDateStr,
				rows			=	thHeaderDates	=	strInfoTotalReservation	=	"",
				arrVendor		=	[],
				colspanPerDate	=	24;
			columnNumber		=	columnNumber + arrDates.length;
			
			$('#scheduleDateStartFilter').val(firstDateStr);
			$('#scheduleDateEndFilter').val(lastDateStr);
			
			if(dataStatistic['TOTALRESERVATION'] <= 0){
				strInfoTotalReservation	=	"There are no unscheduled reservations for the selected period ("+response.strYearMonth+")";
			} else {
				strInfoTotalReservation	=	"Total reservations : <b>"+dataStatistic['TOTALRESERVATION']+"</b>. Unscheduled reservations : <b>"+dataStatistic['TOTALRESERVATIONUNSCHEDULED']+"</b>";
			}
			
			$("#totalScheduleInfo").html(strInfoTotalReservation);
				
			for(var iDates=0; iDates<arrDates.length; iDates++){
				thHeaderDates	+=	'<th colspan="'+colspanPerDate+'" class="thHeaderDates text-center px-0">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; '+arrDates[iDates]+' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</th>';
			}
			
			$("#headerDates").append(thHeaderDates);
			
			if(response.status != 200){
				rows		=	"<tr><td colspan='"+(columnNumber * colspanPerDate)+"'><center>No data found</center></td></tr>";
			} else {
				
				var data			=	response.dataCar,
					firstDateTimeStr=	response.firstDateTimeStr;
				$.each(data, function(index, array) {
					var firstTR	=	"";
						
					if(!arrVendor.includes(array.VENDORNAME)){
						let totalColSpan	=	arrDates.length * colspanPerDate,
							tdSpacerFirstTR	=	getTdSpacerFirstTR(totalColSpan);
						firstTR	=	"<tr class='bgGrey'><td class='bgGrey'><b>"+array.VENDORNAME+"</b></td>"+tdSpacerFirstTR+"</tr>";
						arrVendor.push(array.VENDORNAME);
					}
					
					var carDetail			=	array.BRAND+' '+array.MODEL+' ['+array.PLATNUMBER+']';
					var badgeDetailDriver	=	array.DRIVERNAME == "" || array.DRIVERNAME == "-" ? "" : "<i class='fa fa-user-circle-o' style='font-size: 18px;'></i> <b>"+array.DRIVERNAME+"</b><br/>";
					var btnAdd				=	'<button class="button button-xs button-box button-primary btnAddCarSchedule mb-0" onclick="showModalListUnScheduleCar(\''+month+'\', '+year+', '+array.IDCARTYPE+', '+array.IDCARVENDOR+', \''+carDetail+'\', \''+array.VENDORNAME+'\', \''+array.IDVENDOR+'\')">'+
													'<i class="fa fa-plus"></i>'+
												'</button>';
					
					rows	+=	firstTR+
								"<tr>"+
									"<td class='px-0'>"+
									array.MODEL+" ["+array.TRANSMISSION+"]<br/>"+badgeDetailDriver+btnAdd+" "+array.PLATNUMBER
									"</td>";
					
					var currentIdReservation=	0,
						idxBadge			=	1,
						badgeName			=	arrBadgeType[idxBadge]
						dataSchedule		=	array.DATASCHEDULE,
						currentDateTimeCheck=	firstDateTimeStr;
						
					$.each(dataSchedule, function(iSchedule, arrSchedule) {
						let btnDefaultClass		=	"button button-xs btn-block btnSchedule text-left px-1 mr-0";
							
						if(arrSchedule.length > 0){
							for(var iChildSch=0; iChildSch<arrSchedule.length; iChildSch++){
								let btnSchedule		=	"",
									colspanTd		=	6,
									classRadiusLeft	=	classRadiusRight	=	'',
									onclickFunction	=	'';
								
								let idCarSchedule			=	arrSchedule[iChildSch][0],
									timeStart				=	arrSchedule[iChildSch][1],
									idReservationDayOff		=	arrSchedule[iChildSch][2],
									bookingCode				=	arrSchedule[iChildSch][3],
									duration				=	arrSchedule[iChildSch][4],
									dateTimeStart			=	arrSchedule[iChildSch][5],
									dateTimeEnd				=	arrSchedule[iChildSch][6],
									customerName			=	arrSchedule[iChildSch][7],
									hourStart				=	parseInt(timeStart.substring(0, 2)),
									diffHourWithCurrDTCheck	=	getDiffDateTime(currentDateTimeCheck, dateTimeStart);

								// console.log(iSchedule);
								// console.log(iChildSch);
								// console.log(dataSchedule);

								let idReservationCurrent	=	idReservationDayOff,
									idCarSchedulePrevious	=	idCarScheduleNext	=	idReservationPrevious	=	idReservationNext	=	-1;
								
								//previous idCarSchedule
								if(iChildSch != 0){
									idCarSchedulePrevious	=	dataSchedule[iSchedule][(iChildSch - 1)][0];
								} else {
									if(dataSchedule[(iSchedule - 1)] !== undefined && dataSchedule[(iSchedule - 1)].length != 0) {
										let lenChildPrevious	=	dataSchedule[(iSchedule - 1)].length;
										idCarSchedulePrevious	=	dataSchedule[(iSchedule - 1)][(lenChildPrevious -1)][0];
									}
								}
								
								//next idCarSchedule
								if(dataSchedule[iSchedule][(iChildSch + 1)] !== undefined && dataSchedule[iSchedule][(iChildSch + 1)].length != 0){
									idCarScheduleNext	=	dataSchedule[iSchedule][(iChildSch + 1)][0];
								} else {
									if(dataSchedule[(iSchedule + 1)] !== undefined && dataSchedule[(iSchedule + 1)].length != 0) {
										idCarScheduleNext	=	dataSchedule[(iSchedule + 1)][0][0];
									}
								}
								
								//previous idReservation
								if(iChildSch != 0){
									idReservationPrevious	=	dataSchedule[iSchedule][(iChildSch - 1)][2];
								} else {
									if(dataSchedule[(iSchedule - 1)] !== undefined && dataSchedule[(iSchedule - 1)].length != 0) {
										let lenChildPrevious	=	dataSchedule[(iSchedule - 1)].length;
										idReservationPrevious	=	dataSchedule[(iSchedule - 1)][(lenChildPrevious -1)][2];
									}
								}
								
								//next idReservation
								if(dataSchedule[iSchedule][(iChildSch + 1)] !== undefined && dataSchedule[iSchedule][(iChildSch + 1)].length != 0){
									idReservationNext	=	dataSchedule[iSchedule][(iChildSch + 1)][2];
								} else {
									if(dataSchedule[(iSchedule + 1)] !== undefined && dataSchedule[(iSchedule + 1)].length != 0) {
										idReservationNext	=	dataSchedule[(iSchedule + 1)][0][2];
									}
								}
									
								if(idCarSchedule != 0){
									if(currentIdReservation != 0 && currentIdReservation !== arrSchedule[iChildSch][2]){
										if(idxBadge == 3){
											idxBadge	=	1;
										} else {
											idxBadge++;
										}
										badgeName		=	arrBadgeType[idxBadge];
									}
									
									currentIdReservation=	arrSchedule[iChildSch][2] * 1;
									onclickFunction		=	'onclick="showModalDetailSchedule('+idCarSchedule+', '+idReservationDayOff+')"';
								} else {
									badgeName			=	'danger';
									currentIdReservation= 	0;
									onclickFunction		=	'onclick="showModalDetailDayOff('+idReservationDayOff+')"';
								}
								
								// console.log(idReservationCurrent+" == "+idReservationPrevious);
								// console.log(idReservationCurrent+" == "+idReservationNext);
								
								if(idReservationCurrent == idReservationPrevious || idCarSchedule == idCarSchedulePrevious) classRadiusLeft	=	'border-top-left-0 border-bottom-left-0';
								if(idReservationCurrent == idReservationNext || idCarSchedule == idCarScheduleNext) classRadiusRight	=	'border-top-right-0 border-bottom-right-0';

								if(diffHourWithCurrDTCheck > 1){
									let widthEmptyHoursSpan	=	'sch-'+diffHourWithCurrDTCheck+'-hours';
									rows					+=	'<td class="px-0" colspan="'+diffHourWithCurrDTCheck+'"><span class="d-block '+widthEmptyHoursSpan+'"></span></td>';
									currentDateTimeCheck	=	addHoursToDateTime(currentDateTimeCheck, diffHourWithCurrDTCheck);
								}
								
								let contentBtnSchedule	=	'['+duration+'H] '+timeStart+' - '+bookingCode+' - '+customerName;
								switch(true){
									case duration <= 3	:	contentBtnSchedule	=	'['+duration+'H]'; break
									case duration <= 6	:	contentBtnSchedule	=	'['+duration+'H] '+timeStart; break
									case duration <= 12	:	contentBtnSchedule	=	'['+duration+'H] '+timeStart+' - '+bookingCode; break
									default				:	break
								}
								
								colspanTd			=	duration;
								btnSchedule			=	'<button class="'+btnDefaultClass+' sch-'+duration+'-hours button-'+badgeName+' '+classRadiusLeft+' '+classRadiusRight+'" '+onclickFunction+'>'+contentBtnSchedule+'</button>';
								currentDateTimeCheck=	addHoursToDateTime(currentDateTimeCheck, duration);
								rows	+=	'<td class="px-0 align-middle" colspan="'+colspanTd+'">'+btnSchedule+'</td>';
							}
						} else {
							let totalHoursByIndexDate	=	(iSchedule + 1) * 24,
								datetimeCurrentByIndex	=	addHoursToDateTime(firstDateTimeStr, totalHoursByIndexDate),
								diffHourWithCurrDTCheck	=	parseInt(getDiffDateTime(currentDateTimeCheck, datetimeCurrentByIndex));
							
							if(diffHourWithCurrDTCheck > 1){
								let durationEmptyHours	=	diffHourWithCurrDTCheck > 24 ? diffHourWithCurrDTCheck - 24 : diffHourWithCurrDTCheck;
								
								let widthEmptyHoursSpan	=	'sch-'+durationEmptyHours+'-hours';
								rows					+=	'<td class="px-0" colspan="'+durationEmptyHours+'"><span class="d-block '+widthEmptyHoursSpan+'"></span></td>';
								currentDateTimeCheck	=	addHoursToDateTime(currentDateTimeCheck, durationEmptyHours);
								
								if(diffHourWithCurrDTCheck > 24){
									durationEmptyHours	=	diffHourWithCurrDTCheck - 24;
									currentDateTimeCheck=	addHoursToDateTime(currentDateTimeCheck, 24);
									rows				+=	'<td class="px-0" colspan="24"><span class="d-block sch-24-hours"></span></td>';
								}
							}
						}
					});
					rows	+=	"</tr>";
				});
			}
			$tableBody.html(rows);
		}
	});
}

function getTdSpacerFirstTR(totalColSpan){
	let tdSpacer	=	'';
	for(let i=0; i<totalColSpan; i++){
		tdSpacer	+=	'<td class="px-0 tdSpacer"><span class="d-block sch-1-hours"></span></td>';
	}
	return tdSpacer;
}

function getDiffDateTime(dateTimeStart, dateTimeEnd){
	dateTimeStart	=	moment(dateTimeStart);
	dateTimeEnd		=	moment(dateTimeEnd);
	let diffInHours	=	dateTimeEnd.diff(dateTimeStart, 'hours');
	
	return diffInHours;
}

function addHoursToDateTime(dateTime, nHour){
	dateTime	=	moment(dateTime);
	const newDatetime = dateTime.add(nHour, 'hours');
	
	return newDatetime.format('YYYY-MM-DD HH:mm:ss');
}

$('#reservationTab-optionSource').off('change');
$('#reservationTab-optionSource').on('change', function(e) {
	getDataReservationSchedule();
});

$('#reservationTab-dateSchedule').off('change');
$('#reservationTab-dateSchedule').on('change', function(e) {
	onChangeDateInputFilter($(this));
	getDataReservationSchedule();
});

$('#reservationTab-bookingCode, #reservationTab-searchKeyword').off('keypress');
$("#reservationTab-bookingCode, #reservationTab-searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
        getDataReservationSchedule();
    }
});

function getDataReservationSchedule(callbackFunc = null){
	var month			=	$('#optionMonth').val(),
		year			=	$('#optionYear').val(),
		idSource		=	$('#reservationTab-optionSource').val(),
		dateSchedule	=	$('#reservationTab-dateSchedule').val(),
		bookingCode		=	$('#reservationTab-bookingCode').val(),
		searchKeyword	=	$('#reservationTab-searchKeyword').val(),
		dataSend		=	{
			month:month,
			year:year,
			idSource:idSource,
			dateSchedule:dateSchedule,
			bookingCode:bookingCode,
			searchKeyword:searchKeyword
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/getDataReservationSchedule",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$(".reservationTableElement").remove();
			$("#noDataTableReservationSchedule").addClass("d-none");
			$("#tableReservationSchedule").append("<center id='spinnerLoadData' class='mx-auto'><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$("#spinnerLoadData").remove();
			
			var data			=	response.result,
				rows			=	"";
				
			if(response.status != 200){
				$("#noDataTableReservationSchedule").removeClass("d-none");
			} else {
				
				var elemTableReservation=	"";
				$.each(response.result, function(index, array) {
					var badgeCarDuration	=	'<span class="badge badge-primary pull-right">'+array.DURATION+' hours</span>',
						btnAddCar			=	btnRemoveCar	=	strCarName	=	strCarNameDelete	=	"",
						rsvText				=	array.PRODUCTNAME+'<br/>'+array.CUSTOMERNAME,
						dateTimeInfoTop		=	array.DATETIMESTARTSTR == '-' ? '['+array.RESERVATIONTIMESTART+']' : array.DATETIMESTARTSTR,
						dateTimeInfoBottom	=	array.DATETIMEENDSTR == '-' ? array.SCHEDULEDATE : array.DATETIMEENDSTR,
						dateScheduleRange	=	array.DATETIMESTARTSTR+' to '+array.DATETIMEENDSTR;

					if(array.IDCARVENDOR == 0){
						let dateTimeSchedule=	array.SCHEDULEDATE+' '+array.RESERVATIONTIMESTART;
						btnAddCar			=	'<button class="button button-xs button-box button-primary mb-0 pull-right addCarBtn" '+
													'data-idreservationdetails="'+array.IDRESERVATIONDETAILS+'" '+
													'onclick="showModalCarPicker('+array.IDCARTYPE+', '+array.IDVENDOR+', '+array.IDRESERVATIONDETAILS+', \''+rsvText+'\', \''+array.RESERVATIONTITLE+'\', \''+array.CARTYPE+'\', \''+array.SCHEDULEDATESTR+'\', \''+dateTimeSchedule+'\')">'+
													'<i class="fa fa-user-plus"></i>'+
												'</button>';
					} else {
						strCarName		=	'<b class="pull-right">'+array.CARNAME+'</b>';
						strCarNameDelete=	'['+array.VENDORNAME+'] '+array.BRAND+' '+array.MODEL+' - '+array.PLATNUMBER;
						btnRemoveCar	=	'<button class="button button-xs button-box button-warning mb-0 pull-right ml-5 btnRemoveScheduleCar" '+
												'data-idschedulecar="'+array.IDSCHEDULECAR+'" '+
												'data-dateScheduleRange="'+dateScheduleRange+'" '+
												'data-detailcarvendor="'+strCarNameDelete+'" '+
												'data-detailreservation="'+rsvText+'">'+
												'<i class="fa fa-trash"></i>'+
											'</button>';
					}

					var btnInfo			=	"<button class='button button-xs button-box button-info mb-0 ml-1 pull-right' onclick='showModalDetailSchedule("+array.IDSCHEDULECAR+", "+array.IDRESERVATIONDETAILS+")'><i class='fa fa-info'></i></button>";
					elemTableReservation+=	'<div class="col-sm-12 pb-1 mb-5 rounded-lg reservationTableElement">'+
												'<div class="row pt-10 pb-1">'+
													'<div class="col-lg-2 col-sm-4 text-center">'+dateTimeInfoTop+'</div>'+
													'<div class="col-lg-10 col-sm-8">'+
														'<p class="font-weight-bold">'+
															'<b>['+array.SOURCENAME+' - '+array.BOOKINGCODE+'] </b>'+array.CUSTOMERNAME+badgeCarDuration+' <br/>'+
															array.PRODUCTNAME+
															btnInfo+
															btnAddCar+
															btnRemoveCar+
															strCarName+
														'</p>'+
													'</div>'+
													'<div class="col-lg-2 col-sm-4 text-center">'+dateTimeInfoBottom+'</div>'+
													'<div class="col-lg-10 col-sm-8">'+
														'<small>Notes : '+array.NOTES+'</small>'+
													'</div>'+
												'</div>'+
											'</div>';
				});
				
				$("#tableReservationSchedule").append(elemTableReservation);
				$('.btnRemoveScheduleCar').off('click');
				$('.btnRemoveScheduleCar').on('click', function(e) {
					var carDetail			=	$(this).attr("data-detailCarVendor");
					var dateScheduleRange	=	$(this).attr("data-dateScheduleRange");
					var detailReservation	=	$(this).attr("data-detailReservation");
					var idScheduleCar		=	$(this).attr("data-idScheduleCar");
					var confirmText			=	'Rent car schedule will be deleted. Details ;<br/><br/>Schedule Details : <br/><b>'+dateScheduleRange+'</b><br/><br/>Car Details : <br/><b>'+carDetail+'</b><br/><br/>Reservation : <br/><b>'+detailReservation+'</b>.<br/><br/>Are you sure?';
						
					$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
					$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idScheduleCar).attr('data-function', "deleteCarSchedule");
					$confirmDeleteDialog.modal('show');
				});
				
				if (typeof callbackFunc == "function") callbackFunc();
			}
		}
	});
}

function showModalListUnScheduleCar(month, year, idCarType, idCarVendor, carDetail, vendorName, idVendor){
	
	var dataSend		=	{month:month, year:year, idCarType:idCarType, idCarVendor:idCarVendor, idVendor:idVendor};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/getDataUnScheduleCar",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$(".unscheduledListElement").remove();
			$("#optionScheduleDurationFilter").val($("#optionScheduleDurationFilter option:first").val());
		},
		success:function(response){
			
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status != 200){
				$("#saveScheduleToCar").addClass("d-none");
				$("#noDataUnscheduledList").removeClass("d-none");
				$('#scheduleKeywordFilter').val('');
			} else {
				
				var elemListUnscheduled	=	""
					totalDisplay		=	0;
				$.each(response.result, function(index, array) {

					var dateStartSelected	=	moment($('#scheduleDateStartFilter').val(), 'DD-MM-YYYY'),
						dateStartSelected	=	dateStartSelected.format('YYYY-MM-DD'),
						dateEndSelected		=	moment($('#scheduleDateEndFilter').val(), 'DD-MM-YYYY'),
						dateEndSelected		=	dateEndSelected.format('YYYY-MM-DD'),
						scheduleDateStr		=	moment(array.SCHEDULEDATESTR, 'DD-MM-YYYY'),
						scheduleDateStr		=	scheduleDateStr.format('YYYY-MM-DD'),
						badgeCarDuration	=	'<span class="badge badge-primary pull-right">'+array.DURATION+' hours</span>',
						classDisplay		=	moment(scheduleDateStr).isBetween(dateStartSelected, dateEndSelected, 'day', '[]') ? "" : "d-none";
					
					if(classDisplay == "") totalDisplay++;
					elemListUnscheduled			+=	'<div class="col-sm-12 pb-1 mb-5 rounded-lg unscheduledListElement '+classDisplay+'" '+
														'data-reservationTitle="'+array.RESERVATIONTITLE+'" '+
														'data-productName="'+array.PRODUCTNAME+'" '+
														'data-customerName="'+array.CUSTOMERNAME+'" '+
														'data-bookingCode="'+array.BOOKINGCODE+'" '+
														'data-date="'+array.SCHEDULEDATESTR+'" '+
														'data-duration="'+array.DURATION+'">'+
														'<div class="row pt-10 pb-1">'+
															'<div class="col-lg-2 col-sm-4 text-center">'+
																'<div class="adomx-checkbox-radio-group" style="margin: 2px auto">'+
																	'<label class="adomx-checkbox" style="margin: 0px auto">'+
																		'<input type="checkbox" id="idReservationDetails" class="cbReservationDetails" value="'+array.IDRESERVATIONDETAILS+'"> <i class="icon"></i>'+
																	'</label>'+
																'</div>'+
																array.SCHEDULEDATESTR+'<br/>'+
																'['+array.RESERVATIONTIMESTART+']'+
															'</div>'+
															'<div class="col-lg-10 col-sm-8">'+
																'<p class="font-weight-bold">'+
																	'<b>['+array.SOURCENAME+' - '+array.BOOKINGCODE+'] </b>'+array.CUSTOMERNAME+badgeCarDuration+' <br/>'+
																	array.PRODUCTNAME+
																	'<br/><small>Notes : '+array.NOTES+'</small>'+
																'</p>'+
															'</div>'+
														'</div>'+
													'</div>';
				});
				
				$("#containerUnscheduledList").append(elemListUnscheduled);
				
				if(totalDisplay > 0){
					$("#saveScheduleToCar").removeClass("d-none");
					$("#noDataUnscheduledList").addClass("d-none");
				} else {
					$("#saveScheduleToCar").addClass("d-none");
					$("#noDataUnscheduledList").removeClass("d-none");
				}
			
			}

			$("#unscheduledListCarDetail").html(carDetail);
			$("#unscheduledListVendorName").html(vendorName);
			$("#unscheduledListIdVendorCar").val(idCarVendor);
			$('#modal-unscheduledList').modal('show');
			
			$('#scheduleDateStartFilter, #scheduleDateEndFilter, #optionScheduleDurationFilter').off('change');
			$('#scheduleDateStartFilter, #scheduleDateEndFilter, #optionScheduleDurationFilter').on('change', function(e) {
				searchUnscheduledList();
			});
			
			$('#scheduleKeywordFilter').off('keydown');
			$('#scheduleKeywordFilter').on('keydown', function(e) {
				if(e.which === 13){
					e.preventDefault();
					searchUnscheduledList();
				}
			});
		}
	});
}

function searchUnscheduledList(){
	var dateStartSelected	=	moment($('#scheduleDateStartFilter').val(), 'DD-MM-YYYY'),
		dateStartSelected	=	dateStartSelected.format('YYYY-MM-DD'),
		dateEndSelected		=	moment($('#scheduleDateEndFilter').val(), 'DD-MM-YYYY'),
		dateEndSelected		=	dateEndSelected.format('YYYY-MM-DD'),
		durationSelected	=	$('#optionScheduleDurationFilter').val(),
		scheduleKeyword		=	$('#scheduleKeywordFilter').val().toLowerCase(),
		totalFound			=	0;

	$(".unscheduledListElement").each(function (index, elem) {
		var reservationTitle=	$(this).attr("data-reservationTitle").toLowerCase(),
			productName		=	$(this).attr("data-productName").toLowerCase(),
			customerName	=	$(this).attr("data-customerName").toLowerCase(),
			bookingCode		=	$(this).attr("data-bookingCode").toLowerCase(),
			dateElem		=	moment($(this).attr("data-date"), 'DD-MM-YYYY'),
			dateElem		=	dateElem.format('YYYY-MM-DD'),
			durationElem	=	$(this).attr("data-duration"),
			condDuration	=	durationSelected == "" ? durationElem : durationSelected;

		if(moment(dateElem).isBetween(dateStartSelected, dateEndSelected, 'day', '[]') && durationElem == condDuration &&
		   (reservationTitle.includes(scheduleKeyword) || productName.includes(scheduleKeyword) || customerName.includes(scheduleKeyword) || bookingCode.includes(scheduleKeyword))
		){
			$(this).removeClass("d-none");
			totalFound++;
		} else {
			$(this).addClass("d-none");
		}
		
	});
	
	if(totalFound > 0){
		$("#saveScheduleToCar").removeClass("d-none");
		$("#noDataUnscheduledList").addClass("d-none");
	} else {
		$("#saveScheduleToCar").addClass("d-none");
		$("#noDataUnscheduledList").removeClass("d-none");
	}
}

$('#modal-addCarDayOff').off('show.bs.modal');
$('#modal-addCarDayOff').on('show.bs.modal', function() {
	$('#addCarDayOff-vendorNameStr').html($('#unscheduledListVendorName').html());
	$('#addCarDayOff-carDetailsStr').html($('#unscheduledListCarDetail').html());
	$('#addCarDayOff-hourStart, #addCarDayOff-minuteStart').prop('selectedIndex', 0);
	$('#addCarDayOff-durationHour').val(0);
	$('#addCarDayOff-description').val("");
	$('#addCarDayOff-idCarVendor').val($('#unscheduledListIdVendorCar').val());
	$('#addCarDayOff-optionCarCostType, #addCarDayOff-costDescription').val("");
	$('#addCarDayOff-costNominal').val("1,000");
	$('#modal-unscheduledList').modal('hide');
	
	onChangeDayOffType();
	$('#addCarDayOff-optionType').off('change');
	$('#addCarDayOff-optionType').on('change', function() {
		onChangeDayOffType();
	});
});

function onChangeDayOffType(){
	let idDayOffType	=	$("#addCarDayOff-optionType").val(),
		dataOptionHelper=	JSON.parse(localStorage.getItem('optionHelper')),
		listDayOffType	=   dataOptionHelper['dataCarDayOffType'],
		isNeedCostValue	=	0;

	for (let i = 0; i < listDayOffType.length; i++) {
		if (listDayOffType[i].ID === idDayOffType) {
			isNeedCostValue	=	parseInt(listDayOffType[i].ISNEEDCOST);
			break;
		}
	}
	
	$("#addCarDayOff-isNeedCost").val(isNeedCostValue);
	if(isNeedCostValue == 0) $('#addCarDayOff-optionCarCostType, #addCarDayOff-costDescription, #addCarDayOff-costNominal').prop("disabled", true);
	if(isNeedCostValue == 1) $('#addCarDayOff-optionCarCostType, #addCarDayOff-costDescription, #addCarDayOff-costNominal').prop("disabled", false);
}

$('#editor-addCarDayOff').off('submit');
$('#editor-addCarDayOff').on('submit', function(e) {
	e.preventDefault();
	var dataForm		=	$(this).serializeArray(),
		offDurationHour	=	$("#addCarDayOff-durationHour").val(),
		isNeedCost		=	$("#addCarDayOff-isNeedCost").val(),
		warningMessage	=	'',
		dataSend		=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name.replace('addCarDayOff-', '')] = this.value;
	});
	
	if(parseInt(offDurationHour) <= 0){
		warningMessage	=	'Please input a valid <b>duration</b> for car day off';
	} else if(parseInt(isNeedCost) == 1) {
		let idCarCostType		=	$("#addCarDayOff-optionCarCostType").val(),
			costDescription		=	$("#addCarDayOff-costDescription").val(),
			costDescriptionClean=	costDescription.replace(/ {2,}/g, ' '),
			costDescriptionLen	=	costDescriptionClean.length;
		
		if(idCarCostType == "" || idCarCostType == null) warningMessage	=	'Please select a valid <b>cost type</b>';
		if(costDescription == "" || costDescriptionLen <= 8) warningMessage	=	'Please input a valid <b>cost description</b>';
	} 
	
	if(warningMessage != ''){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html(warningMessage);
		});
		$('#modalWarning').modal('show');
	} else {
		$.ajax({
			type: 'POST',
			url: baseURL+"schedule/carSchedule/addCarDayOff",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#editor-addCarDayOff :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#editor-addCarDayOff :input").attr("disabled", false);
				onChangeDayOffType();
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					$('#modal-addCarDayOff').modal('hide');
					getCarSchedule();
				}
			}
		});
	}
});

function showModalCarPicker(idCarType, idVendor, idReservationDetails, rsvText, rsvTitle, strCarType, scheduleDate, dateTimeSchedule){
	var dataSend	=	{scheduleDate:scheduleDate, idCarType:idCarType, idVendor:idVendor, idReservationDetails:idReservationDetails};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/getDataCarList",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$(".carListElement").remove();
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			$("#containerReservationDetail").html(
				'<li> <span>Car Type</span> <span><b>'+strCarType+'</b></span> </li>'+
				'<li> <span>Date</span> <span><b>'+dateTimeSchedule+'</b></span> </li>'+
				'<li> <span>Vendor</span> <span><b>'+response.vendorName+'</b></span> </li>'+
				'<li> <span>Detail</span> <span></span> </li>'
			);
			$("#containerReservationDetail-customerProduct").html('<b>'+rsvText+'<br/>'+rsvTitle+'</b>');
			
			if(response.status != 200){
				$("#saveCarToReservation").addClass("d-none");
				$("#containerCarList").removeClass("scrollList").html("<tr><td colspan='2'>No car data found</td><tr>");
			} else {
				
				var elemListCar	=	"";
				$.each(response.result, function(index, array) {
					var radioElem	=	"<label class='adomx-radio-2 pull-right mt-10'>"+
											"<input type='radio' name='radioCarList' id='radioCarList"+array.IDCARVENDOR+"' value='"+array.IDCARVENDOR+"'> <i class='icon'></i>"+
										"</label>";
					
					elemListCar		+=	"<tr class='carListElement'>"+
											"<td>"+array.CARNAME+"<br/><small class='mt-1'>Total schedule on the selected date : "+array.TOTALSCHEDULE+"</small></td>"+
											"<td width='10'>"+radioElem+"</td>"+
										"</tr>";
				});
				
				$("#containerCarList").addClass("scrollList").html(elemListCar);
				$("#saveReservationToCar").removeClass("d-none");
			}

			$("#idReservationDetails").val(idReservationDetails);
			$('#modal-carList').modal('show');
		}
	});
}

$('#content-carList').off('submit');
$('#content-carList').on('submit', function(e) {
	e.preventDefault();
	var arrIDReservationDetails	=	[$("#idReservationDetails").val()];
	var idCarVendor				=	$("input[name=radioCarList]:checked").val(),
		dataSend				=	{idCarVendor:idCarVendor, arrIDReservationDetails:arrIDReservationDetails};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/saveCarSchedule",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-carList :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-carList :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-carList').modal('hide');
				getCarSchedule();
				getDataReservationSchedule();
			}
		}
	});
});

$('#content-unscheduledList').off('submit');
$('#content-unscheduledList').on('submit', function(e) {
	e.preventDefault();
	var arrIDReservationDetails	=	[];
	$('input:checkbox.cbReservationDetails').each(function () {
		var checkboxVal	=	(this.checked ? $(this).val() : false);
		
		if(checkboxVal){
			arrIDReservationDetails.push(checkboxVal);
		}
	});
	
	var idCarVendor		=	$("#unscheduledListIdVendorCar").val(),
		dataSend		=	{idCarVendor:idCarVendor, arrIDReservationDetails:arrIDReservationDetails};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/saveCarSchedule",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-unscheduledList :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-unscheduledList :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-unscheduledList').modal('hide');
				getCarSchedule();
				getDataReservationSchedule();
			}
		}
	});
});

function showModalDetailSchedule(idCarSchedule, idReservationDetails){
	var dataSend	=	{idCarSchedule:idCarSchedule, idReservationDetails:idReservationDetails};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/getDetailSchedule",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#detailSchedule-source, #detailSchedule-title, #detailSchedule-date, #detailSchedule-time, #detailSchedule-customerName").html("-");
			$("#detailSchedule-customerContact, #detailSchedule-customerEmail, #detailSchedule-hotelName, #detailSchedule-pickUpLocation").html("-");
			$("#detailSchedule-dropOffLocation, #detailSchedule-tourPlan, #detailSchedule-remark").html("-");
			$("#detailSchedule-numberOfAdult, #detailSchedule-numberOfChild, #detailSchedule-numberOfInfant").html("0");
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailData	=	response.detailData,
					carDetails	=	detailData.IDSCHEDULECAR == '' || detailData.IDSCHEDULECAR == null ? 'Not Set' : detailData.CARDETAIL;

				$("#detailSchedule-productName").html(detailData.PRODUCTNAME);
				$("#detailSchedule-carDuration").html(detailData.DURATION+" Hours");
				$("#detailSchedule-notes").html(detailData.NOTES);
				
				$("#detailSchedule-source").html(detailData.SOURCENAME);
				$("#detailSchedule-title").html(detailData.RESERVATIONTITLE);
				$("#detailSchedule-dateTimeStart").html(detailData.DATETIMESTART);
				$("#detailSchedule-dateTimeEnd").html(detailData.DATETIMEEND);
				$("#detailSchedule-customerName").html(detailData.CUSTOMERNAME);
				$("#detailSchedule-customerContact").html(detailData.CUSTOMERCONTACT);
				$("#detailSchedule-customerEmail").html(detailData.CUSTOMEREMAIL);
				$("#detailSchedule-carDetails").html(carDetails);
				$("#detailSchedule-numberOfAdult").html(detailData.NUMBEROFADULT);
				$("#detailSchedule-numberOfChild").html(detailData.NUMBEROFCHILD);
				$("#detailSchedule-numberOfInfant").html(detailData.NUMBEROFINFANT);
				$("#detailSchedule-hotelName").html(detailData.HOTELNAME);
				$("#detailSchedule-pickUpLocation").html(detailData.PICKUPLOCATION);
				$("#detailSchedule-dropOffLocation").html(detailData.DROPOFFLOCATION);
				$("#detailSchedule-tourPlan").html(detailData.TOURPLAN);
				$("#detailSchedule-remark").html(detailData.REMARK);
				
				if(idCarSchedule > 0){
					$("#removeScheduleCar").attr("data-idScheduleCar", detailData.IDSCHEDULECAR);
					$("#removeScheduleCar").attr("data-detailCarVendor", detailData.CARDETAIL);
					$("#removeScheduleCar").attr("data-detailReservation", "["+detailData.RESERVATIONTIMESTART+"] "+detailData.PRODUCTNAME+". Customer : "+detailData.CUSTOMERNAME);
					$("#removeScheduleCar").removeClass('d-none');
				} else {
					$("#removeScheduleCar").attr("data-idScheduleCar", 0).attr("data-detailCarVendor", "").attr("data-detailReservation", "");
					$("#removeScheduleCar").addClass('d-none');
				}
				
				$('#modal-detailSchedule').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#removeScheduleCar').off('click');
$('#removeScheduleCar').on('click', function(e) {
	var carDetail			=	$(this).attr("data-detailCarVendor");
	var detailReservation	=	$(this).attr("data-detailReservation");
	var idScheduleCar		=	$(this).attr("data-idScheduleCar");
	var confirmText			=	'Rent car schedule will be deleted. Details ;<br/><br/>Car Details : <br/><b>'+carDetail+'</b><br/>Reservation : <br/><b>'+detailReservation+'</b>.<br/><br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idScheduleCar).attr('data-function', "deleteCarSchedule");
	$confirmDeleteDialog.modal('show');
});

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData	=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData'),
		funcName=	$confirmDeleteDialog.find('#confirmBtn').attr('data-function'),
		dataSend=	{idData:idData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/"+funcName,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$confirmDeleteDialog.modal('hide');
			$('#window-loader').modal('show');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			setUserToken(response);
			NProgress.done();
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);			
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				getCarSchedule();
				getDataReservationSchedule();
				$('#modal-detailSchedule').modal('hide');
			}
		}
	});
});

function showModalDetailDayOff(idDayoff = 0){
	var dataSend		=	{idDayoff:idDayoff};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/getDetailDayOff",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#detailDayOff-vendorName, #detailDayOff-carDetails, #detailDayOff-date, #detailDayOff-datetimeinput, #detailDayOff-reason").html("-");
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailData		=	response.detailData;

				$("#detailDayOff-vendorName").html(detailData.VENDORNAME);
				$("#detailDayOff-carDetails").html(detailData.CARDETAIL);
				$("#detailDayOff-dateTimeStart").html(detailData.DATETIMESTART);
				$("#detailDayOff-dateTimeEnd").html(detailData.DATETIMEEND);
				$("#detailDayOff-durationHour").html(detailData.DURATIONHOUR + ' Hours');
				$("#detailDayOff-datetimeinput").html(detailData.DATETIMEINPUTINPUT);
				$("#detailDayOff-detailApproval").html(detailData.DATETIMEAPPROVAL+' By '+detailData.USERAPPROVAL);
				$("#detailDayOff-reason").html('('+detailData.DAYOFFTYPE+') '+detailData.REASON);
				$("#detailDayOff-idDayoff").html(idDayoff);
				
				$('#detailDayOff-btnDeleteDayOff').off('click');
				$('#detailDayOff-btnDeleteDayOff').on('click', function(e) {
					var carDetail			=	$(this).attr("data-detailCarVendor");
					var detailReservation	=	$(this).attr("data-detailReservation");
					var idScheduleCar		=	$(this).attr("data-idScheduleCar");
					var confirmText			=	'Car day off data will be deleted. Details ;<br/><br/>'+
												'<div class="order-details-customer-info">'+
													'<ul class="ml-5">'+
														'<li> <span>Car Details</span> <span>'+detailData.CARDETAIL+'</span> </li>'+
														'<li> <span>Date Time</span> <span>'+detailData.DATETIMESTART+' to '+detailData.DATETIMEEND+'</span> </li>'+
														'<li> <span>Reason</span> <span>('+detailData.DAYOFFTYPE+') '+detailData.REASON+'</span> </li>'+
													'</ul>'+
												'</div><br/>'+
												'Are you sure?';
						
					$('#modal-detailDayOff').modal('hide');
					$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
					$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idDayoff).attr('data-function', "deleteCarDayOff");
					$confirmDeleteDialog.modal('show');
				});
				
				$('#modal-detailDayOff').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#dropOffPickUpTab-optionVendor, #dropOffPickUpTab-optionDriver, #dropOffPickUpTab-startDate, #dropOffPickUpTab-endDate').off('change');
$('#dropOffPickUpTab-optionVendor, #dropOffPickUpTab-optionDriver, #dropOffPickUpTab-startDate, #dropOffPickUpTab-endDate').on('change', function (e) {
	getDataDropOffPickUpSchedule();
});

$('#dropOffPickUpTab-searchKeyword').off('keypress');
$("#dropOffPickUpTab-searchKeyword").on('keypress', function (e) {
	if (e.which == 13) {
		getDataDropOffPickUpSchedule();
	}
});

function getDataDropOffPickUpSchedule() {
	var $tableBody		=	$('#dropOffPickUpTab-tableData > tbody'),
		columnNumber	=	$('#dropOffPickUpTab-tableData > thead > tr > th').length,
		idVendorCar		=	$('#dropOffPickUpTab-optionVendor').val(),
		idDriver		=	$('#dropOffPickUpTab-optionDriver').val(),
		startDate		=	$('#dropOffPickUpTab-startDate').val(),
		endDate			=	$('#dropOffPickUpTab-endDate').val(),
		searchKeyword 	=	$('#dropOffPickUpTab-searchKeyword').val(),
		dataSend		=	{
			idVendorCar:idVendorCar,
			idDriver: idDriver,
			startDate:startDate,
			endDate:endDate,
			searchKeyword:searchKeyword
		};
	
	$.ajax({
		type: 'POST',
		url: baseURL +"schedule/carSchedule/getDataDropOffPickUpSchedule",
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

			var data = response.dataTable,
				rows = "";
			
			if (response.status != 200 || data.length === 0) {
				rows = "<tr><td colspan='" + columnNumber + "' align='center'><b>" + response.msg +"</b></td></tr>";
			} else {
				$.each(data, function (index, array) {
					let idScheduleCar		=	array.IDSCHEDULECAR == null ? 0 : array.IDSCHEDULECAR,
						transmissionName	=	array.PLATNUMBER == '-' ? '-' : array.TRANSMISSION,
						dropOffBadgeStatus	=	generateBadgeStatusDropOffPickUp(array.DROPOFFSTATUSPROCCESSID, array.DROPOFFSTATUSPROCCESS, true),
						pickUpBadgeStatus	=	generateBadgeStatusDropOffPickUp(array.PICKUPSTATUSPROCCESSID, array.PICKUPSTATUSPROCCESS, true),
						btnDropOffSchedule	=	btnPickUpSchedule	=	'<div class="alert alert-warning position-absolute p-2 mb-10" role="alert" style="bottom: 0; width:96%"><i class="zmdi zmdi-info"></i> <span>No car schedule</span></div>';

					if(idScheduleCar > 0){
						btnDropOffSchedule	=	"<button class='button button-sm btn-block button-info text-left mb-10 position-absolute p-2' style='bottom: 0; width:96%' onclick='showModalDetailDropOffPickUpSchedule("+array.IDRESERVATION+", 1)'>"+
													"<b>"+array.DROPOFFDRIVERNAME+"</b>"+dropOffBadgeStatus+"<br/>"+
													"Note : "+array.DROPOFFNOTES+
												"</button>";
						btnPickUpSchedule	=	"<button class='button button-sm btn-block button-info text-left mb-10 position-absolute p-2' style='bottom: 0; width:96%' onclick='showModalDetailDropOffPickUpSchedule("+array.IDRESERVATION+", 2)'>"+
													"<b>"+array.PICKUPDRIVERNAME+"</b>"+pickUpBadgeStatus+"<br/>"+
													"Note : "+array.PICKUPNOTES+
												"</button>";
					}

					rows	+=	"<tr>"+
									"<td>"+
										"<b>"+array.BOOKINGCODE+"</b><br/>"+
										"<b>["+array.DURATIONOFDAY+" Days] "+array.RESERVATIONTITLE+"</b><br/><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
										"<b class='text-secondary'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/><br/>"+
										"Remark : "+array.REMARK+"<br/>"+
									"</td>"+
									"<td>"+
										"<div class='order-details-customer-info'>"+
											"<ul>"+
												"<li> <span>Name</span> <span><b>"+array.CUSTOMERNAME+"</b></span> </li>"+
												"<li> <span>Contact</span> <span>"+array.CUSTOMERCONTACT+"</span> </li>"+
												"<li> <span>Email</span> <span>"+array.CUSTOMEREMAIL+"</span> </li>"+
											"</ul>"+
										"</div>"+
									"</td>"+
									"<td>"+
										"<div class='order-details-customer-info'>"+
											"<ul>"+
												"<li> <span>Vendor</span> <span><b>"+array.VENDORNAME+"</b></span> </li>"+
												"<li> <span>Car</span> <span>"+array.BRAND+" "+array.MODEL+" ["+transmissionName+"]</span> </li>"+
												"<li> <span>Plate</span> <span>"+array.PLATNUMBER+"</span> </li>"+
												"<li> <span>Type</span> <span>"+array.CARTYPE+"</span> </li>"+
											"</ul>"+
										"</div>"+
									"</td>"+
									"<td class='position-relative'>"+
										"<b class='text-primary'>"+array.RESERVATIONDATESTART+" "+array.RESERVATIONTIMESTART+"</b><br/>"+
										array.DROPOFFLOCATION+"<br/>"+
										btnDropOffSchedule+
									"</td>"+
									"<td class='position-relative'>"+
										"<b class='text-success'>"+array.RESERVATIONDATEEND+" "+array.RESERVATIONTIMEEND+"</b><br/>"+
										array.PICKUPLOCATION+"<br/><br/>"+
										btnPickUpSchedule+
									"</td>"+
								"</tr>";
				});
			}
			$tableBody.html(rows);
		}
	});
}

function showModalDetailDropOffPickUpSchedule(idReservation, jobType) {
	var dataSend = { idReservation: idReservation, jobType: jobType };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/carSchedule/getDetailDropOffPickUpSchedule",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#detailDropOffPickUp-title, #detailDropOffPickUp-dateTimeStart, #detailDropOffPickUp-dateTimeEnd, #detailDropOffPickUp-customerName").html("-");
			$("#detailDropOffPickUp-customerContact, #detailDropOffPickUp-customerEmail, #detailDropOffPickUp-remark, #detailDropOffPickUp-carDetails, #detailDropOffPickUp-status").html("-");
			$("#detailDropOffPickUp-driverHandle, #detailDropOffPickUp-pickUpLocation, #detailDropOffPickUp-dropOffLocation, #detailDropOffPickUp-notes").val("");
			$("#detailDropOffPickUp-jobType, #detailDropOffPickUp-idScheduleCar").val(0);
		},
		success: function (response) {
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);

			if (response.status == 200) {
				var detailReservation		=	response.detailReservation
					dropOffPickUpSchedule	=	response.dropOffPickUpSchedule
					badgeJobType			=	jobType == 1 ? '<span class="badge badge-primary">Drop Off</span>' : '<span class="badge badge-success">Pick Up</span>';

				$("#detailDropOffPickUp-title").html("[" + detailReservation.BOOKINGCODE + "] [" + detailReservation.DURATIONOFDAY + " Days] " + detailReservation.RESERVATIONTITLE);
				$("#detailDropOffPickUp-dateTimeStart").html(detailReservation.RESERVATIONDATESTART + " " + detailReservation.RESERVATIONTIMESTART);
				$("#detailDropOffPickUp-dateTimeEnd").html(detailReservation.RESERVATIONDATEEND + " " + detailReservation.RESERVATIONTIMEEND);
				$("#detailDropOffPickUp-customerName").html(detailReservation.CUSTOMERNAME);
				$("#detailDropOffPickUp-customerContact").html(detailReservation.CUSTOMERCONTACT);
				$("#detailDropOffPickUp-customerEmail").html(detailReservation.CUSTOMEREMAIL);
				$("#detailDropOffPickUp-remark").html(detailReservation.REMARK);
				$("#detailDropOffPickUp-containerJobType").html(badgeJobType);

				if(jobType == 1){
					$("#detailDropOffPickUp-containerDropOffLocation").removeClass('d-none');
					$("#detailDropOffPickUp-containerPickUpLocation").addClass('d-none');
				} else {
					$("#detailDropOffPickUp-containerDropOffLocation").addClass('d-none');
					$("#detailDropOffPickUp-containerPickUpLocation").removeClass('d-none');
				}

				if (dropOffPickUpSchedule == false){
					$("#detailDropOffPickUp-vendor, #detailDropOffPickUp-carDetails").html('-');
					$("#detailDropOffPickUp-driverHandle, #detailDropOffPickUp-dropOffLocation, #detailDropOffPickUp-pickUpLocation, #detailDropOffPickUp-notes").prop('disabled', true);
					$("#detailDropOffPickUp-containerNoScheduleWarning").removeClass('d-none');
				} else {
					let badgeStatus		=	generateBadgeStatusDropOffPickUp(dropOffPickUpSchedule.IDSTATUSPROCESSCARDROPOFFPICKUP, dropOffPickUpSchedule.STATUSPROCESSNAME),
						locationDropOff	=	dropOffPickUpSchedule.LOCATIONDROPOFF == '' || dropOffPickUpSchedule.LOCATIONDROPOFF == null ? detailReservation.DROPOFFLOCATION : dropOffPickUpSchedule.LOCATIONDROPOFF,
						locationPickUp	=	dropOffPickUpSchedule.LOCATIONPICKUP == '' || dropOffPickUpSchedule.LOCATIONPICKUP == null ? detailReservation.PICKUPLOCATION : dropOffPickUpSchedule.LOCATIONPICKUP;
					
					$("#detailDropOffPickUp-vendor").html(dropOffPickUpSchedule.VENDORNAME + ' - ' + dropOffPickUpSchedule.CARTYPE);
					$("#detailDropOffPickUp-carDetails").html(dropOffPickUpSchedule.BRAND+' '+dropOffPickUpSchedule.MODEL+' ['+dropOffPickUpSchedule.TRANSMISSION+'] - '+dropOffPickUpSchedule.PLATNUMBER);
					$("#detailDropOffPickUp-status").html(badgeStatus);
					$("#detailDropOffPickUp-driverHandle").val(dropOffPickUpSchedule.IDDRIVER);
					$("#detailDropOffPickUp-dropOffLocation").val(locationDropOff);
					$("#detailDropOffPickUp-pickUpLocation").val(locationPickUp);
					$("#detailDropOffPickUp-notes").val(dropOffPickUpSchedule.NOTES);
					$("#detailDropOffPickUp-driverHandle, #detailDropOffPickUp-dropOffLocation, #detailDropOffPickUp-pickUpLocation, #detailDropOffPickUp-notes").prop('disabled', false);
					$("#detailDropOffPickUp-containerNoScheduleWarning").addClass('d-none');
					$("#detailDropOffPickUp-jobType").val(jobType);
					$("#detailDropOffPickUp-idScheduleCar").val(dropOffPickUpSchedule.IDSCHEDULECAR);
				}

				$('#modal-detailDropOffPickUp').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function () {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#content-detailDropOffPickUp').off('submit');
$('#content-detailDropOffPickUp').on('submit', function(e) {
	e.preventDefault();	
	var idScheduleCar	=	$("#detailDropOffPickUp-idScheduleCar").val(),
		jobType			=	$("#detailDropOffPickUp-jobType").val(),
		driverHandle	=	$("#detailDropOffPickUp-driverHandle").val(),
		dropOffLocation	=	$("#detailDropOffPickUp-dropOffLocation").val(),
		pickUpLocation	=	$("#detailDropOffPickUp-pickUpLocation").val(),
		notes			=	$("#detailDropOffPickUp-notes").val(),
		dataSend		=	{
			idScheduleCar:idScheduleCar,
			jobType: jobType,
			driverHandle: driverHandle,
			dropOffLocation: dropOffLocation,
			pickUpLocation: pickUpLocation,
			notes: notes
		};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/carSchedule/saveCarDropOffPickUpSchedule",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-unscheduledList :input").attr("disabled", true);
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
				$('#modal-detailDropOffPickUp').modal('hide');
				getDataDropOffPickUpSchedule();
			}
		}
	});
});

function generateBadgeStatusDropOffPickUp(idStatusProcess, statusProcessName, pullRight = false){
	let classPull	=	pullRight ? ' pull-right' : '',
		badgeStatus = '<span class="badge badge-dark' + classPull + '">Unscheduled</span>';
	switch (idStatusProcess) {
		case "1": badgeStatus = '<span class="badge badge-warning' + classPull + '">' + statusProcessName + '</span>'; break;
		case "2": badgeStatus = '<span class="badge badge-primary' + classPull + '">' + statusProcessName + '</span>'; break;
		case "3": badgeStatus = '<span class="badge badge-success' + classPull + '">' + statusProcessName + '</span>'; break;
		default	: badgeStatus = '<span class="badge badge-dark' + classPull + '">Unscheduled</span>'; break;
	}

	return badgeStatus;
}

scheduleCarFunc();