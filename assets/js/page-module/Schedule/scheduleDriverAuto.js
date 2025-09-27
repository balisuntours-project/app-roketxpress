var $confirmSaveDialog	= $('#modal-confirm-action');
if (scheduleDriverAutoFunc == null){
	var scheduleDriverAutoFunc	=	function(){
		$(document).ready(function () {
			var idReservationDetails	=	false;
			setOptionHelper('optionArea', 'dataAreaNonTBA');
			if(localStorage.getItem('OSNotificationData') === null || localStorage.getItem('OSNotificationData') === undefined){
				var minHourDateFilterTomorrow	=	$("#minHourDateFilterTomorrow").val() * 1,
					d							=	new Date(),
					hour						=	d.getHours(),
					intHour						=	hour * 1;
				
				if(intHour >= minHourDateFilterTomorrow){
					$("#scheduleDate").val(dateTomorrow);
				} else {
					$("#scheduleDate").val(dateToday);
				}
			} else {

				var OSNotificationData	=	JSON.parse(localStorage.getItem('OSNotificationData')),
					OSNotifType			=	OSNotificationData.type;

				if(OSNotifType == "schedule-driver-auto"){
					var idReservationDetails	=	OSNotificationData.idReservationDetails,
						dateSchedule			=	OSNotificationData.dateSchedule;

					$("#scheduleDate").val(dateSchedule);
					localStorage.removeItem("OSNotificationData");						
				}
				
			}
			
			getDataAutoScheduleSetting();
			getDataScheduleAuto(idReservationDetails);
			getDataDriverList();
			getDataScheduleManual(idReservationDetails);
		});	
	}
}

$('#scheduleDate, #optionJobType, #optionArea, #optionDriverType').off('change');
$('#scheduleDate, #optionJobType, #optionArea, #optionDriverType').on('change', function(e) {
	$('#btnSnapshotAutoSchedule').removeClass("d-none");
	$('#btnSaveAutoSchedule, #btnCancelAutoSchedule, #autoScheduleProcessWarning').addClass("d-none");

	getDataScheduleAuto();
	getDataDriverList();
	getDataScheduleManual();
});

function getDataAutoScheduleSetting(){
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/getDataAutoScheduleSetting",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(),
		beforeSend:function(){},
		success:function(response){
			setUserToken(response);
			
			if(response.status == 200){
				
				var dataSetting	=	response.dataSetting;
				$.each(dataSetting, function(index, array) {
					
					var idVariable	=	array.IDSYSTEMSETTINGVARIABLE;
					switch(idVariable){
						case "1"	:	$("#maxScoreArea").val(array.VALUE); break;
						case "2"	:	$("#maxScoreDriverRank").val(array.VALUE); break;
						case "3"	:	$("#maxScoreNoJob").val(array.VALUE); break;
						case "4"	:	$("#maxScoreJobYesterday").val(array.VALUE); break;
						case "8"	:	$("#maxScoreJobTopRatePriority").val(array.VALUE); break;
						case "9"	:	$("#maxScoreJobGoodRatePriority").val(array.VALUE); break;
						default		:	break;
					}
				});
			}
		}
	});
}

function getDataScheduleAuto(idReservationDetails = false){
	var jobType			=	$('#optionJobType').val(),
		idArea			=	$('#optionArea').val(),
		scheduleDate	=	$('#scheduleDate').val(),
		dataSend		=	{jobType:jobType, idArea:idArea, scheduleDate:scheduleDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/getDataScheduleAuto",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$(".unscheduledReservationItem").remove();
			$("#noDataUnscheduledReservation").addClass("d-none");
			$("#sortableReservationList").append("<center id='spinnerLoadData'><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$("#spinnerLoadData").remove();
			
			var data			=	response.result,
				rows			=	"";
				
			if(response.status != 200){
				$("#noDataUnscheduledReservation").removeClass("d-none");
				$("#btnSnapshotAutoSchedule, #btnSaveAutoSchedule, #btnCancelAutoSchedule").addClass("d-none");
			} else {
				
				var elemTableReservation=	"",
					orderNumber			=	1;
				$("#btnSnapshotAutoSchedule").removeClass("d-none");
				$.each(response.result, function(index, array) {

					var idReservationDetails=	array.IDRESERVATIONDETAILS,
						badgeDriverType		=	'<span class="badge badge-outline badge-primary mr-1" id="reservationDriverType'+idReservationDetails+'">'+array.DRIVERTYPE+'</span>',
						jobType				=	jobRate	=	"",
						dataAttr			=	"data-idReservationDetails="+idReservationDetails+
												" data-idDriver=0"+
												" data-totalPax="+array.TOTALPAX+
												" data-idAreaPickUp="+array.IDAREA+
												" data-areaName="+array.AREANAME+
												" data-driverType="+array.IDDRIVERTYPE+
												" data-driverTypeStr="+array.DRIVERTYPE+
												" data-jobType="+array.JOBTYPE+
												" data-jobRate="+array.JOBRATE+
												" data-jobTitle=\""+array.RESERVATIONTITLE+"\""+
												" data-orderNumber="+orderNumber;
						
					switch(array.JOBTYPE){
						case "1"	:	jobType	=	"Short"; break;
						case "2"	:	jobType	=	"Standard"; break;
						case "3"	:	jobType	=	"Long"; break;
						default		:	jobType	=	"-"; break;
					}
						
					switch(array.JOBRATE){
						case "1"	:	jobRate	=	'<span class="badge badge-warning ml-1 pull-right" id="badgeJobRate'+idReservationDetails+'">Rate : S</span>'; break;
						case "2"	:	jobRate	=	'<span class="badge badge-primary ml-1 pull-right" id="badgeJobRate'+idReservationDetails+'">Rate : G</span>'; break;
						case "3"	:	jobRate	=	'<span class="badge badge-success ml-1 pull-right" id="badgeJobRate'+idReservationDetails+'">Rate : T</span>'; break;
						default		:	jobRate	=	'<span class="badge badge-primary ml-1 pull-right" id="badgeJobRate'+idReservationDetails+'">-</span>'; break;
					}
					
					var btnMoveToManual		=	"",
						userLevel			=	getLevelUser();
					if(userLevel == 1){
						btnMoveToManual		=	'<div class="col-sm-12 px-2 pt-3" id="containerBtnMoveManual'+idReservationDetails+'">'+
													'<button type="button" class="button button-warning button-sm pull-right px-1 py-0" onclick="moveScheduleToManual('+idReservationDetails+')"><span>Move To Manual <i class="fa fa-arrow-circle-right"></i></span></button>'+
												'</div>';
					}
					
					elemTableReservation	+=	'<div class="list-group-item px-0 py-0 unscheduledReservationItem" id="unscheduledReservationItem'+idReservationDetails+'" '+dataAttr+'>'+
													'<div class="row px-0 mx-0">'+
														'<div class="col-sm-8 px-0" style="border-right: 1px solid #e0e0e0;">'+
															'<div class="row px-1 py-3 pb-0 mx-1 my-2" id="rowAutoScheduleDetail'+idReservationDetails+'">'+
																'<div class="col-sm-12 px-2 mb-2">'+
																	'<span class="badge badge-success py-0 mr-2">'+
																		'<h6 class="mb-0 text-light">#'+orderNumber+'</h6>'+
																	'</span>'+
																	badgeDriverType+
																	jobRate+
																	// '<span class="badge badge-primary pull-right" id="reservationJobType'+idReservationDetails+'">'+jobType+'</span>'+
																'</div>'+
																'<div class="col-sm-12 px-2 mb-2">'+
																	'<h6 class="mb-0" id="reservationDetails'+idReservationDetails+'">['+array.RESERVATIONTIMESTART+'] '+array.RESERVATIONTITLE+'</h6>'+
																'</div>'+
																'<div class="col-sm-12 mb-2 px-2">'+
																	'<h6 class="mb-0">'+array.CUSTOMERNAME+'</h6>'+
																	'<h6 class="mb-0">Pax : '+array.NUMBEROFADULT+' | '+array.NUMBEROFCHILD+' | '+array.NUMBEROFINFANT+'</h6>'+
																'</div>'+
																'<div class="col-sm-12 mb-2 px-2">'+
																	'<h6 class="mb-0">'+
																		'Hotel/Pick Up'+
																		'<span class="badge badge-primary pull-right" id="badgeAreaName'+idReservationDetails+'">'+array.AREANAME+'</span>'+
																	'</h6>'+
																	'<span>'+array.HOTELNAME+' / '+array.PICKUPLOCATION+'</span>'+
																'</div>'+
																'<div class="col-sm-12 mb-2 px-2">'+
																	'<h6 class="mb-0">Tour Plan</h6>'+
																	'<span>'+array.TOURPLAN+'</span><br/>'+
																'</div>'+
																'<div class="col-sm-12 px-2">'+
																	'<h6 class="mb-0">Remark</h6>'+
																	'<span>'+array.REMARK+'</span><br/>'+
																'</div>'+
																btnMoveToManual+
															'</div>'+
														'</div>'+
														'<div class="col-sm-4 px-0 text-center" id="driverSelected'+idReservationDetails+'">'+
															'<h1 class="mt-30 mb-5" style="color: #666666;"><i class="fa fa-user-secret"></i></h1>'+
															'<b>No Driver</b>'+
														'</div>'+
													'</div>'+
												'</div>';
						orderNumber++;
				});
				
				$("#sortableReservationList").html(elemTableReservation);
				if($(window).width() >= 720){
					setSortableListSchedule();					
				}
			
				if(idReservationDetails != false){
					var elementID	=	"unscheduledReservationItem"+idReservationDetails;
					if($("#"+elementID).length != 0){
						jumpFocusToElement(elementID);
					} else {
						$('.nav-tabs a[href="#manualScheduleTab"]').tab('show');
					}
				}
			}
		}
	});
}

function setSortableListSchedule(){
	var sortableReservationListEl=	document.getElementById('sortableReservationList');
	var sortableReservationList	 =	Sortable.create(sortableReservationListEl, {
		animation: 150
	});
}

function getDataDriverList(){
	var scheduleDate	=	$('#scheduleDate').val(),
		idArea			=	$('#optionArea').val(),
		driverType		=	$('#optionDriverType').val(),
		dataSend		=	{scheduleDate:scheduleDate, idArea:idArea, driverType:driverType};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/getDataDriverList",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$(".availableDriverItem, .availableDriverTitle").remove();
			$("#noDataAvailableDriver").addClass("d-none");
			$("#availableDriverList").append("<center id='spinnerLoadData'><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success:function(response){
			
			NProgress.done();
			setUserToken(response);
			$("#spinnerLoadData").remove();
						
			if(response.status != 200){
				$("#noDataAvailableDriver").removeClass("d-none");
			} else {
				
				var elemListDriver	=	"",
					arrDriverType	=	[];
				$.each(response.result, function(index, array) {

					if(!arrDriverType.includes(array.DRIVERTYPE)){
						elemListDriver	+=	'<div class="row availableDriverTitle px-1 mx-1 mt-3 ml-0">'+
												'<div class="col-sm-12 px-3">'+
													'<h5 class="mb-1">'+array.DRIVERTYPE+'</h5>'+
												'</div>'+
											'</div>';
						arrDriverType.push(array.DRIVERTYPE);
					}
					
					var totalConsecutiveNoJob	=	array.TOTALCONSECUTIVENOJOB,
						jobTypeYesterday		=	"";
					switch(array.JOBTYPEYESTERDAY){
						case "-1"	:	jobTypeYesterday		=	'<span class="badge badge-warning pull-right">Day Off</span>'; break;
						case "0"	:	totalConsecutiveNoJobStr=	totalConsecutiveNoJob == 1 ? "" : " x "+totalConsecutiveNoJob;
										jobTypeYesterday		=	'<span class="badge badge-danger pull-right">No Job'+totalConsecutiveNoJobStr+'</span>'; break;
						case "1"	:	jobTypeYesterday		=	'<span class="badge badge-primary pull-right">Short</span>'; break;
						case "2"	:	jobTypeYesterday		=	'<span class="badge badge-primary pull-right">Standard</span>'; break;
						case "3"	:	jobTypeYesterday		=	'<span class="badge badge-primary pull-right">Long</span>'; break;
					}
					
					var badgeJobRate	=	'',
						lastJobRate		=	array.LASTJOBRATE,
						nextJobPriority	=	0;
						
					if(lastJobRate.length > 0){
						for(var i=0; i<lastJobRate.length; i++){
							badgeJobRate	+=	generateBadgeJobRate(lastJobRate[i], array.IDDRIVER, array.DRIVERNAME, array.DRIVERTYPE);
						}
					} else {
						badgeJobRate		=	'-';
					}
					
					if(array.IDDRIVERTYPE == 2){
						if(typeof lastJobRate[4] !== 'undefined' && lastJobRate[4] == "1" &&
						   typeof lastJobRate[3] !== 'undefined' && lastJobRate[3] == "1"){
							nextJobPriority	=	2;
						}

						// if(typeof lastJobRate[4] !== 'undefined' && lastJobRate[4] == "1" &&
						   // typeof lastJobRate[3] !== 'undefined' && lastJobRate[3] == "2"){
							// nextJobPriority	=	3;
						// }
					}
					
					var badgePartnershipType	=	"";
					switch(array.PARTNERSHIPTYPE){
						case "1"	:	badgePartnershipType	=	'<span class="badge badge-primary">Partner</span>'; break;
						case "2"	:	badgePartnershipType	=	'<span class="badge badge-info">Freelance</span>'; break;
						case "3"	:	badgePartnershipType	=	'<span class="badge badge-warning">Team</span>'; break;
						case "4"	:	badgePartnershipType	=	'<span class="badge badge-warning">Office</span>'; break;
						default		:	badgePartnershipType	=	''; break;
					}
					
					var dataAttr	=	"data-idDriver="+array.IDDRIVER+
										" data-driverRank="+array.RANKNUMBER+
										" data-driverType="+array.IDDRIVERTYPE+
										" data-carCapacity="+array.IDCARCAPACITY+
										" data-carCapacityMax="+array.MAXCAPACITY+
										" data-pickupArea="+array.ARRAREA+
										" data-lastTypeDrive="+array.IDDRIVETYPE+
										" data-lastTypeJob="+array.JOBTYPEYESTERDAY+
										" data-totalConsecutiveNoJob="+totalConsecutiveNoJob+
										" data-lastRateJob="+lastJobRate[lastJobRate.length - 1]+
										" data-nextJobPriority="+nextJobPriority+
										" data-driverName="+array.DRIVERNAME,
						idDriver	=	array.IDDRIVER;
					elemListDriver	+=	'<div class="row availableDriverItem rounded-lg px-1 py-3 mx-1 my-2 listDriverScore" '+dataAttr+' id="availableDriverItem'+idDriver+'" data-idDriver="'+idDriver+'">'+
											'<div class="col-lg-9 col-sm-8 px-2"><span id="driverName'+idDriver+'"><b>['+array.DRIVERTYPE+'] '+array.DRIVERNAME+'</b> | '+array.CARCAPACITY+'</span></div>'+
											'<div class="col-lg-3 col-sm-4 px-2 text-right">'+
												badgePartnershipType+
												'<span class="badge badge-warning ml-1" id="driverRankNumber'+idDriver+'">#'+array.RANKNUMBER+'</span>'+
											'</div>'+
											'<hr/>'+
											'<div class="col-sm-12 mb-3 px-2">'+
												'<h6 class="mb-0">Area Priority</h6>'+
												'<span>'+array.AREAPRIORITY+'</span>'+
											'</div>'+
											'<div class="col-sm-12 mb-3 px-2">'+
												'<h6 class="mb-0">Last 5 Job Rate (Oldest - Newest)</h6>'+
												badgeJobRate+
											'</div>'+
											'<div class="col-sm-12 mb-3 px-2">'+
												'<h6 class="mb-0">'+
													'Last Job'+
													jobTypeYesterday+
												'</h6>'+
												'<span>'+array.JOBYESTERDAY+'</span><br/>'+
											'</div>'+
										'</div>';
				});
				
				$("#totalDriverTour").val(response.totalDriverTour);
				$("#totalDriverCharter").val(response.totalDriverCharter);
				$("#totalDriverShuttle").val(response.totalDriverShuttle);
				$("#totalArea").val(response.totalArea);
				$("#arrMaxCapacity").val(response.arrMaxCapacity);
				$("#availableDriverList").append(elemListDriver);
			}
		}
	});
}

function generateBadgeJobRate(jobRate, idDriver, driverName, driverType){
	var badgeJobRate=	'',
		onclickEvent=	"openDriverJobHistoryList("+idDriver+", '"+driverName+"', '"+driverType+"')";
	switch(jobRate){
		case "1"	:	badgeJobRate	=	'<span class="badge badge-warning mr-1" onclick="'+onclickEvent+'">S</span>'; break;
		case "2"	:	badgeJobRate	=	'<span class="badge badge-primary mr-1" onclick="'+onclickEvent+'">G</span>'; break;
		case "3"	:	badgeJobRate	=	'<span class="badge badge-success mr-1" onclick="'+onclickEvent+'">T</span>'; break;
		default		:	badgeJobRate	=	'<span class="badge badge-primary mr-1" onclick="'+onclickEvent+'">-</span>'; break;
	}
	return badgeJobRate;
}

function openDriverJobHistoryList(idDriver, driverName, driverType){
	
	var dataSend	=	{idDriver:idDriver};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/getDataDriverJobHistoryList",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$('#bodyDriverLastJobList').html("");
		},
		success:function(response){
			$('#window-loader').modal('hide');
			setUserToken(response);
			NProgress.done();
			
			if(response.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			} else {
				
				var dataHistory		=	response.dataHistory,
					rowsJobHistory	=	"";
				
				$.each(dataHistory, function(index, array) {
					
					var badgeJobRate=	'';
					switch(array.JOBRATE){
						case "1"	:	badgeJobRate	=	'<span class="badge badge-warning mr-1">Standard</span>'; break;
						case "2"	:	badgeJobRate	=	'<span class="badge badge-primary mr-1">Good</span>'; break;
						case "3"	:	badgeJobRate	=	'<span class="badge badge-success mr-1">Top</span>'; break;
						default		:	badgeJobRate	=	'<span class="badge badge-primary mr-1">-</span>'; break;
					}
					rowsJobHistory	+=	"<tr>"+
											"<td align='center'>"+array.SCHEDULEDATE+"<br/>"+array.RESERVATIONTIMESTART+"</td>"+
											"<td><b>"+array.CUSTOMERNAME+"</b><br/>"+array.RESERVATIONTITLE+"<br/>"+array.PRODUCTNAME+"</td>"+
											"<td >"+badgeJobRate+"</td>"+
										"</tr>";
					
				});
				
				$('#bodyDriverLastJobList').html(rowsJobHistory);
				$('#driverLastJobListDriverName').html(driverName);
				$('#driverLastJobListDriverType').html(driverType);
				$('#modal-driverLastJobList').modal('show');
			}
		}
	});
	
}

function getDataScheduleManual(idReservationDetails = false){
	
	var scheduleDate	=	$('#scheduleDate').val(),
		dataSend		=	{scheduleDate:scheduleDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/getDataScheduleManual",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$(".manualReservationListItem").remove();
			$("#noDataUnscheduledReservationManual").addClass("d-none");
			$("#manualReservationList").append("<center id='spinnerLoadData'><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$("#spinnerLoadData").remove();
			
			var data			=	response.result,
				rows			=	"";
				
			if(response.status != 200){
				$("#noDataUnscheduledReservationManual").removeClass("d-none");
			} else {
				
				var elemTableReservation=	"",
					orderNumber			=	1;
				$.each(response.result, function(index, array) {

					var badgeDriverType		=	'<span class="badge badge-outline badge-primary mr-1">'+array.DRIVERTYPE+'</span>',
						badgeSourceName		=	'<span class="badge badge-outline badge-warning mr-1">'+array.SOURCENAME+'</span>',
						jobType				=	"";
						
					switch(array.JOBTYPE){
						case "1"	:	jobType	=	"Short Job"; break;
						case "2"	:	jobType	=	"Standard Job"; break;
						case "3"	:	jobType	=	"Long Job"; break;
						default		:	jobType	=	"-"; break;
					}
					
					var btnMoveToAutomatic	=	"",
						userLevel			=	getLevelUser();
					if(userLevel == 1){
						btnMoveToAutomatic	=	'<div class="col-sm-12 px-2">'+
													'<button type="button" class="button button-warning button-sm px-1 py-0" onclick="moveScheduleToAutomatic('+array.IDRESERVATIONDETAILS+')"><span>Move To Automatic <i class="fa fa-arrow-circle-left"></i></span></button>'+
												'</div>';
					}

					elemTableReservation	+=	'<div class="row px-0 mx-0 manualReservationListItem" id="manualReservationListItem'+array.IDRESERVATIONDETAILS+'">'+
													'<div class="col-sm-8 px-0" style="border-right: 1px solid #e0e0e0;">'+
														'<div class="row px-1 py-3 mx-1 my-2">'+
															'<div class="col-sm-12 px-2 mb-3">'+
																'<span class="badge badge-success py-0 mr-2">'+
																	'<h6 class="mb-0 text-light">#'+orderNumber+'</h6>'+
																'</span>'+
																badgeDriverType+
																badgeSourceName+
																// '<span class="badge badge-primary pull-right">'+jobType+'</span>'+
																'<span class="badge badge-info pull-right mr-1">'+array.DURATIONOFDAY+' Day(s)</span>'+
															'</div>'+
															'<div class="col-sm-12 px-2 mb-2">'+
																'<h6 class="mb-0">['+array.RESERVATIONTIMESTART+'] '+array.RESERVATIONTITLE+'</h6>'+
															'</div>'+
															'<div class="col-sm-12 mb-2 px-2">'+
																'<h6 class="mb-0">'+array.CUSTOMERNAME+'</h6>'+
																'<h6 class="mb-0">Pax : '+array.NUMBEROFADULT+' | '+array.NUMBEROFCHILD+' | '+array.NUMBEROFINFANT+'</h6>'+
															'</div>'+
															'<div class="col-sm-12 mb-2 px-2">'+
																'<h6 class="mb-0">'+
																	'Hotel/Pick Up'+
																	'<span class="badge badge-info pull-right">'+array.AREANAME+'</span>'+
																'</h6>'+
																'<span>'+array.HOTELNAME+' / '+array.PICKUPLOCATION+'</span>'+
															'</div>'+
															'<div class="col-sm-12 mb-2 px-2">'+
																'<h6 class="mb-0">Tour Plan</h6>'+
																'<span>'+array.TOURPLAN+'</span><br/>'+
															'</div>'+
															'<div class="col-sm-12 px-2 mb-2">'+
																'<h6 class="mb-0">Remark</h6>'+
																'<span>'+array.REMARK+'</span><br/>'+
															'</div>'+
															'<div class="col-sm-12 mb-2 px-2">'+
																'<h6 class="mb-0">Special Request</h6>'+
																'<span>'+array.SPECIALREQUEST+'</span><br/>'+
															'</div>'+
															btnMoveToAutomatic+
														'</div>'+
													'</div>'+
													'<div class="col-sm-4 px-0 text-center">'+
														'<h1 class="mt-30 mb-5" style="color: #666666;"><i class="fa fa-user-secret"></i></h1>'+
														'<b>No Driver</b><br/>'+
														'<button type="button" class="button button-primary button-sm px-1 py-0" onclick="showListDriverModal('+array.IDDRIVERTYPE+', '+array.IDRESERVATIONDETAILS+')"><span>Choose Driver <i class="fa fa-user-plus"></i></span></button>'+
													'</div>'+
												'</div>';
						orderNumber++;
				});
				
				$("#manualReservationList").html(elemTableReservation);
			
				if(idReservationDetails != false){
					jumpFocusToElement("manualReservationListItem"+idReservationDetails);
				}
			}
			
		}

	});
	
}

$('#btnSnapshotAutoSchedule').off('click');
$('#btnSnapshotAutoSchedule').on('click', function(e) {
	
	e.preventDefault();
	$('#btnSnapshotAutoSchedule').addClass("d-none");
	$('#autoScheduleProcessWarning').removeClass("d-none");
	
	var maxScoreArea				=	$("#maxScoreArea").val() * 1,
		maxScoreDriverRank			=	$("#maxScoreDriverRank").val() * 1,
		maxScoreNoJob				=	$("#maxScoreNoJob").val() * 1,
		maxScoreJobYesterday		=	$("#maxScoreJobYesterday").val() * 1,
		maxScoreJobTopRatePriority	=	$("#maxScoreJobTopRatePriority").val() * 1,
		maxScoreJobGoodRatePriority	=	$("#maxScoreJobGoodRatePriority").val() * 1,
		totalArea					=	$("#totalArea").val() * 1,
		totalDriverTour				=	$("#totalDriverTour").val() * 1,
		totalDriverCharter			=	$("#totalDriverCharter").val() * 1,
		totalDriverShuttle			=	$("#totalDriverShuttle").val() * 1,
		arrMaxCapacity				=	$("#arrMaxCapacity").val().split(","),
		arrDataSnapshotHistory		=	[],
		totalSchedule				=	totalSuccess	=	totalFailed	=	0;
		
	$('.unscheduledReservationItem').each(function() {
		
		var driverList				=	getDriverList(),
			totalCurrDriverTour		=	driverList.totalDriverTour,
			totalCurrDriverCharter	=	driverList.totalDriverCharter,
			totalCurrDriverShuttle	=	driverList.totalDriverShuttle,
			totalDriverSmall		=	driverList.totalDriverSmall,
			totalDriverStandard		=	driverList.totalDriverStandard,
			totalDriverMinivan		=	driverList.totalDriverMinivan,
			totalDriverInnova		=	driverList.totalDriverInnova,
			totalDriverJobTop		=	driverList.totalDriverJobTop,
			totalDriverJobGood		=	driverList.totalDriverJobGood,
			totalDriverJobStandard	=	driverList.totalDriverJobStandard,
			totalLastRateTop		=	driverList.totalLastRateTop,
			totalLastRateGood		=	driverList.totalLastRateGood,
			totalLastRateStandard	=	driverList.totalLastRateStandard,
			idReservationDetails	=	$(this).attr("data-idReservationDetails"),
			totalPax				=	$(this).attr("data-totalPax") * 1,
			idAreaPickUp			=	$(this).attr("data-idAreaPickUp"),
			areaName				=	$(this).attr("data-areaName"),
			driverType				=	$(this).attr("data-driverType"),
			driverTypeStr			=	$(this).attr("data-driverTypeStr"),
			jobTitle				=	$(this).attr("data-jobTitle").toLowerCase(),
			jobType					=	$(this).attr("data-jobType"),
			jobRate					=	$(this).attr("data-jobRate"),
			orderNumber				=	$(this).attr("data-orderNumber"),
			availDriverType			=	availDriverCapacity	=	totalDriverCompare	=	0,
			reasonException			=	carCapacityStr	=	"",
			arrDetailReservation	=	{jobTitle:jobTitle, totalPax:totalPax, totalPax:totalPax, areaName:areaName, jobRate:jobRate, driverType:driverType, driverTypeStr:driverTypeStr, jobType:jobType};
			
		console.log("==== #"+orderNumber+" ==== jobRate : "+jobRate+" ==== totalDriverJobTop : "+totalDriverJobTop+" ==== totalDriverJobGood : "+totalDriverJobGood+" ==== totalDriverJobStandard : "+totalDriverJobStandard+" ====");	
		
		if(totalPax <= 3){
			availDriverCapacity	=	totalDriverSmall + totalDriverStandard + totalDriverMinivan + totalDriverInnova;
			carCapacityStr		=	"Small";
			arrDetailReservation['allowedCarType']	=	"Small - Standard - Minivan - Innova";
		} else if(totalPax <= 5){
			availDriverCapacity	=	totalDriverStandard + totalDriverMinivan + totalDriverInnova;
			carCapacityStr		=	"Standard";
			arrDetailReservation['allowedCarType']	=	"Standard - Minivan - Innova";
		} else {
			availDriverCapacity	=	totalDriverMinivan + totalDriverInnova;
			carCapacityStr		=	"Minivan/Innova";
			arrDetailReservation['allowedCarType']	=	"Minivan - Innova";
		}
		
		if(driverType == 1){
			availDriverType		=	totalDriverTour + totalDriverCharter + totalDriverShuttle;
			
			if(totalDriverTour > 0){
				availDriverType		=	totalDriverTour;
			} else if(totalDriverTour == 0 && totalDriverCharter > 0){
				availDriverType		=	totalDriverCharter;
			}

			arrDetailReservation['allowedDriverType']	=	"Tour | Charter | Shuttle";
		} else if(driverType == 3){
			availDriverType		=	totalDriverTour > 0 ? totalDriverTour : totalDriverCharter;
			arrDetailReservation['allowedDriverType']	=	"Tour | Charter";
		} else {
			availDriverType		=	totalDriverTour;
			arrDetailReservation['allowedDriverType']	=	"Tour";
		}

		console.log("==== availDriverCapacity : "+availDriverCapacity+" ==== totalDriverSmall : "+totalDriverSmall+" ==== totalDriverStandard : "+totalDriverStandard+" ===="+" ==== totalDriverMinivan : "+totalDriverMinivan+" ===="+" ==== totalDriverInnova : "+totalDriverInnova+" ====");	
		if((availDriverType > 0 || totalDriverTour > 0) && availDriverCapacity > 0){
			
			var arrDriverScore		=	[],
				arrAllDriverScore	=	[];
				
			$.each(driverList.arrDriverList, function (index, arrayDriver) {
				var idDriver				=	arrayDriver.idDriver,
					arrData					=	arrayDriver.arrData,
					driverRankData			=	arrData[0],
					driverTypeData			=	arrData[1] * 1,
					driverTypeDataStr		=	"Shuttle",
					carCapacityData			=	arrData[2],
					carCapacityMaxData		=	arrData[3] * 1,
					pickupAreaData			=	arrData[4],
					pickupAreaData			=	typeof pickupAreaData === 'undefined' || pickupAreaData == "" ? [] : pickupAreaData.split(","),
					lastTypeJobData			=	arrData[5],
					driverName				=	arrData[6],
					lastTypeDriveData		=	arrData[7],
					nextJobPriorityData		=	arrData[8],
					lastRateJobData			=	arrData[9],
					totalConsecutiveNoJob	=	arrData[10],
					totalScore				=	scoreCarCapacity	=	scoreArea	=	scoreDriverRank	=	scoreNoJob	=	scoreJobType	=	scoreDriveType	=	scoreJobRatePriority	=	0,
					allowDriverJob			=	allowDriverJobRate	=	isAllowedCarType	=	true,
					charterJob				=	isAllowedDriverType	=	false;
					
				switch(driverTypeData){
					case 2	:	driverTypeDataStr	=	"Tour"; break;
					case 3	:	driverTypeDataStr	=	"Charter"; break;
					case 1	:	
					default	:	driverTypeDataStr	=	"Shuttle"; break;
				}
		
				if(driverTypeData == 1 || driverTypeData == "1"){
					totalDriverCompare	=	totalDriverShuttle;
				} else if(driverTypeData == 3 || driverTypeData == "3"){
					totalDriverCompare	=	totalDriverCharter;
				} else {
					totalDriverCompare	=	totalDriverTour;
				}
				
				if(driverTypeData == 2 || driverType == driverTypeData){
					isAllowedDriverType	=	true;
				} else if (driverTypeData == 3 && (driverType == driverTypeData || driverType == 1)){
					isAllowedDriverType	=	true;
				}
				
				if(carCapacityMaxData < totalPax){
					isAllowedCarType	=	false;
				}

				if(jobTitle.includes("standard")){
					if(carCapacityMaxData == 3){
						allowDriverJob	=	false;
						isAllowedCarType=	false;
					}
					arrDetailReservation['allowedCarType']	=	"Standard - Minivan - Innova";
					charterJob			=	true;
				}

				if(jobTitle.includes("minivan")){
					if(carCapacityMaxData == 3 || carCapacityMaxData == 5){
						allowDriverJob	=	false;
						isAllowedCarType=	false;
					}
					arrDetailReservation['allowedCarType']	=	"Minivan - Innova";
					charterJob			=	true;
				}
				
				if(jobTitle.includes("innova") || jobTitle.includes("comfort")){
					if(carCapacityData != 4){
						allowDriverJob	=	false;
						isAllowedCarType=	false;
					}
					arrDetailReservation['allowedCarType']	=	"Innova";
					charterJob			=	true;
				}
				arrDetailReservation['charterJob']	=	charterJob;
				
				if(!charterJob){
					if(jobRate == 3 && totalDriverJobTop > 0 && allowDriverJob != false){
						if(nextJobPriorityData == 3){
							scoreJobRatePriority	=	maxScoreJobTopRatePriority;
						}
					}
					
					if(jobRate == 2 && (totalDriverJobGood > 0 || totalDriverJobTop > 0) && allowDriverJob != false){
						if(totalDriverJobTop > 0) {
							if(nextJobPriorityData == 3){
								scoreJobRatePriority=	maxScoreJobTopRatePriority;
							}
						} else if(nextJobPriorityData == 2){
							scoreJobRatePriority	=	maxScoreJobGoodRatePriority;
						}
					}
				}

				if((driverType == driverTypeData || parseInt(driverTypeData) === 2 || (parseInt(driverTypeData) === 3 && [1, 3].includes(parseInt(driverType)))) && carCapacityMaxData >= totalPax && allowDriverJob == true){
					//SCORE CAR CAPACITY
					var maxScoreCarCapacity		=	arrMaxCapacity.length,
						constMaxScoreCapacity	=	4;
					
					//update 22-10-2022 == non aktifkan score capacity / nol score
					scoreCarCapacity			=	0;
					
					//SCORE AREA PICK UP
					if(pickupAreaData.length > 0){
						var indexArea	=	pickupAreaData.indexOf(idAreaPickUp);
						if(indexArea != -1){
							scoreArea	=	(totalArea - indexArea) / totalArea * maxScoreArea;
						}
					}

					//DRIVER SKILL & RANK
					if(driverTypeData == driverType || driverTypeData == 2 || driverTypeData == "2"){
						scoreDriverRank	=	(totalDriverCompare - (driverRankData - 1)) / totalDriverCompare * maxScoreDriverRank;
					}
					
					//NO JOB YESTERDAY PRIORITY
					switch(lastTypeJobData){
						case "0"	:	scoreNoJob	=	maxScoreNoJob; break;
						case "-1"	:	
						default		:	break;
					}
					
					//JOB TYPE YESTERDAY PRIORITY
					switch(lastTypeJobData){
						case 0	:	switch(jobType){
										case 1	:	scoreJobType	=	3; break;
										case 2	:	scoreJobType	=	2; break;
										case 3	:	scoreJobType	=	1; break;
									}
									break;
						case 1	:	switch(jobType){
										case 1	:	scoreJobType	=	0; break;
										case 2	:	scoreJobType	=	3; break;
										case 3	:	scoreJobType	=	2; break;
									}
									break;
						case 2	:	switch(jobType){
										case 1	:	scoreJobType	=	2; break;
										case 2	:	scoreJobType	=	0; break;
										case 3	:	scoreJobType	=	3; break;
									}
									break;
						case 3	:	switch(jobType){
										case 1	:	scoreJobType	=	3; break;
										case 2	:	scoreJobType	=	2; break;
										case 3	:	scoreJobType	=	0; break;
									}
									break;
					}
					
					//DRIVER TOUR :: IF LAST DRIVE != LAST TYPE DRIVE (+1)
					if(driverTypeData == 2 || driverTypeData == "2"){
						if(lastTypeDriveData != driverType && lastTypeJobData != -1){
							scoreDriveType	=	maxScoreJobYesterday;
						}
					}
					
					//DRIVER CHARTER :: IF LAST JOB DRIVE != CHARTER (+1)
					if(driverTypeData == 3 || driverTypeData == "3"){
						if(lastTypeDriveData == 1 && lastTypeJobData != -1){
							scoreDriveType	=	maxScoreJobYesterday;
						}
					}
					
					if(([1, 3].includes(driverType)) && ([1, 3].includes(driverTypeData)) && totalCurrDriverTour > 0){
						scoreCarCapacity	=	scoreArea	=	scoreDriverRank	=	scoreNoJob	=	scoreJobType	=	scoreDriveType	=	scoreJobRatePriority	=	0;
					} else if(driverType == 1 && driverTypeData == 1 && totalCurrDriverCharter > 0) {
						scoreCarCapacity	=	scoreArea	=	scoreDriverRank	=	scoreNoJob	=	scoreJobType	=	scoreDriveType	=	scoreJobRatePriority	=	0;
					}
					
					totalScore	=	scoreCarCapacity + scoreArea + scoreDriverRank + scoreNoJob + scoreJobType + scoreDriveType + scoreJobRatePriority;
					
				}
				
				if(totalScore > 0) arrDriverScore.push({driverType:driverType, allowDriverJob:allowDriverJob, driverName:driverName, idDriver:idDriver, totalScore:totalScore, scoreCarCapacity:scoreCarCapacity, scoreArea:scoreArea, scoreDriverRank:scoreDriverRank, scoreNoJob:scoreNoJob, scoreJobType:scoreJobType, scoreDriveType:scoreDriveType, scoreJobRatePriority:scoreJobRatePriority, driverTypeData:driverTypeData, totalDriverCompare:totalDriverCompare});
				arrAllDriverScore.push({idDriver:idDriver, driverName:driverName, isAllowedDriverType:isAllowedDriverType, isAllowedCarType:isAllowedCarType, allowDriverJob:allowDriverJob, nextJobPriorityData:nextJobPriorityData, driverName:driverName, totalScore:totalScore, scoreArea:scoreArea, scoreDriverRank:scoreDriverRank, scoreNoJob:scoreNoJob, scoreJobType:scoreJobType, scoreDriveType:scoreDriveType, scoreJobRatePriority:scoreJobRatePriority});
				
			});
			
			if(arrDriverScore.length > 0){
				arrDriverScore.sort((a, b) => parseFloat(b.totalScore) - parseFloat(a.totalScore));				
				arrAllDriverScore.sort((a, b) => parseFloat(b.totalScore) - parseFloat(a.totalScore));				
				console.log(arrDriverScore);
			} else {
				reasonException	=	"No driver match for this job";
				console.log(arrAllDriverScore);
			}
			
		} else {
			if(availDriverType <= 0){
				reasonException	=	"No driver available for this job ("+driverTypeStr+")";
			} else {
				reasonException	=	"No car available for this job ("+carCapacityStr+")";
			}
		}
		
		if(reasonException != ""){
			$("#driverSelected"+idReservationDetails).html('<h1 class="mt-30 mb-5" style="color: #666666;"><i class="fa fa-times"></i></h1><p>'+reasonException+'</p>');
			totalFailed++;
		} else {
			var idDriverSelected	=	arrDriverScore[0].idDriver;
			$("#unscheduledReservationItem"+idReservationDetails).attr("data-idDriver", idDriverSelected)
			$("#driverSelected"+idReservationDetails).html('<div class="row rounded-lg px-1 py-2 mx-1 text-left listDriverScore" data-idDriver="'+idDriverSelected+'">'+$("#availableDriverItem"+idDriverSelected).html()+'</div>');
			$("#availableDriverItem"+idDriverSelected+", #containerBtnMoveManual"+idReservationDetails).remove();
			totalSuccess++;
		}
		
		arrDataSnapshotHistory.push([idReservationDetails, arrDetailReservation, arrAllDriverScore]);
		$("#rowAutoScheduleDetail"+idReservationDetails).append('<div class="col-sm-12 px-2 pt-3 text-center"><button type="button" class="button button-info button-xs" onclick="showDetailSnapshotHistory('+idReservationDetails+')"><span>Snapshot History <i class="fa fa-history"></i></span></button></div>');
		
		totalSchedule++;
		
	});
	
	localStorage.setItem('arrDataSnapshotHistory', JSON.stringify(arrDataSnapshotHistory));
	$('#autoScheduleProcessWarning').addClass("d-none");
	$('#modalWarning').on('show.bs.modal', function() {
		$('#modalWarningBody').html(
									'Process complete. <b>'+totalSchedule+'</b> data has been processed.<br/>'+
									'<div class="order-details-customer-info">'+
										'<ul class="ml-5">'+
											'<li> <span>Success</span> <span>'+totalSuccess+'</span> </li>'+
											'<li> <span>Failed</span> <span>'+totalFailed+'</span> </li>'+
										'</ul>'+
									'</div>'
								);
	});
	$('#modalWarning').modal('show');

	if(totalSuccess > 0){
		$('#btnSaveAutoSchedule, #btnCancelAutoSchedule').removeClass("d-none");
	} else {
		$('#btnSnapshotAutoSchedule').removeClass("d-none");
	}
	
});

function showDetailSnapshotHistory(idReservationDetails){
	
	var arrDataSnapshotHistory	=	JSON.parse(localStorage.getItem('arrDataSnapshotHistory'));
	
	for(var i=0; i<arrDataSnapshotHistory.length; i++){
		
		var arrReservationSnapshot	=	arrDataSnapshotHistory[i],
			idReservationDetailsArr	=	arrReservationSnapshot[0],
			arrDetailReservation	=	arrReservationSnapshot[1],
			arrAllDriverScore		=	arrReservationSnapshot[2],
			jobDurationStr			=	jobRateStr	=	"";
		
		if(idReservationDetails == idReservationDetailsArr){
			
			var charterJobStr		=	arrDetailReservation.charterJob ? " [Car Charter]" : "";
			$("#containerListDriverSnapshotHistory").html("");
			switch(arrDetailReservation.jobType){
				case "1"	:	jobDurationStr	=	"Short"; break;
				case "2"	:	jobDurationStr	=	"Standard"; break;
				case "3"	:	jobDurationStr	=	"Long"; break;
				default		:	jobDurationStr	=	"-"; break;
			}
				
			switch(arrDetailReservation.jobRate){
				case "1"	:	jobRateStr	=	'Standard'; break;
				case "2"	:	jobRateStr	=	'Good'; break;
				case "3"	:	jobRateStr	=	'Top'; break;
				default		:	jobRateStr	=	'-'; break;
			}
			
			$("#snapshotHistory-reservationTitle").html($("#reservationDetails"+idReservationDetails).html());
			$("#snapshotHistory-jobType").html($("#reservationDriverType"+idReservationDetails).clone());
			$("#snapshotHistory-allowedDriverType").html(arrDetailReservation.allowedDriverType);
			$("#snapshotHistory-totalPax").html(arrDetailReservation.totalPax+charterJobStr);
			$("#snapshotHistory-allowedCarType").html(arrDetailReservation.allowedCarType);
			$("#snapshotHistory-areaName").html($("#badgeAreaName"+idReservationDetails).clone());
			$("#snapshotHistory-jobDuration").html(jobDurationStr);
			$("#snapshotHistory-jobRate").html($("#badgeJobRate"+idReservationDetails).clone());
			
			for(var j=0; j<arrAllDriverScore.length; j++){
				var idDriverScore			=	arrAllDriverScore[j].idDriver,
					isAllowedCarType		=	arrAllDriverScore[j].isAllowedCarType,
					isAllowedDriverType		=	arrAllDriverScore[j].isAllowedDriverType,
					nextJobPriority			=	arrAllDriverScore[j].nextJobPriorityData,
					scoreDriverRank			=	arrAllDriverScore[j].scoreDriverRank,
					scoreArea				=	arrAllDriverScore[j].scoreArea,
					scoreNoJobYesterday		=	arrAllDriverScore[j].scoreNoJob,
					scoreDurationYesterday	=	arrAllDriverScore[j].scoreJobType,
					scoreLastDriveType		=	arrAllDriverScore[j].scoreDriveType,
					scoreJobRatePriority	=	arrAllDriverScore[j].scoreJobRatePriority,
					scoreTotal				=	arrAllDriverScore[j].totalScore,
					elemDriverScore			=	$(".listDriverScore[data-idDriver='"+idDriverScore+"']").html(),
					allowCarTypeBadge		=	isAllowedCarType ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-danger" aria-hidden="true"></i>',
					allowDriverTypeBadge	=	isAllowedDriverType ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-danger" aria-hidden="true"></i>',
					nextJobPriorityStr		=	"-";
				
				switch(nextJobPriority){
					case "1"	:	nextJobPriorityStr	=	'Standard'; break;
					case "2"	:	nextJobPriorityStr	=	'Good'; break;
					case "3"	:	nextJobPriorityStr	=	'Top'; break;
					default		:	nextJobPriorityStr	=	'-'; break;
				}
				
				var rowListDriver	=	'<div class="row rounded-lg px-1 py-2 mx-1 text-left listDriverSnapshotHistory mb-5">'+
											'<div class="col-sm-7" style="border-right: 1px solid #e0e0e0;"><div class="row">'+elemDriverScore+'</div></div>'+
											'<div class="col-sm-5">'+
												'<div class="row mb-10" style="border-bottom: 1px solid #e0e0e0;">'+
													'<div class="col-sm-7 mb-5">'+
														'<h6 class="mb-0">Allow Driver Type</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-5">'+
														'<h6 class="mb-0 text-right">'+allowDriverTypeBadge+'</h6>'+
													'</div>'+
													'<div class="col-sm-7 mb-5">'+
														'<h6 class="mb-0">Allow Car Type</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-5">'+
														'<h6 class="mb-0 text-right">'+allowCarTypeBadge+'</h6>'+
													'</div>'+
													'<div class="col-sm-7 mb-10">'+
														'<h6 class="mb-0">Next Job Priority</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-10">'+
														'<h6 class="mb-0 text-right">'+nextJobPriorityStr+'</h6>'+
													'</div>'+
												'</div>'+
												'<div class="row mb-10" style="border-bottom: 1px solid #e0e0e0;">'+
													'<div class="col-sm-7 mb-2">'+
														'<h6 class="mb-0">Rank</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-2">'+
														'<h6 class="mb-0 text-right">'+numberFormat(scoreDriverRank)+'</h6>'+
													'</div>'+
													'<div class="col-sm-7 mb-2">'+
														'<h6 class="mb-0">Area</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-2">'+
														'<h6 class="mb-0 text-right">'+numberFormat(scoreArea)+'</h6>'+
													'</div>'+
													'<div class="col-sm-7 mb-2">'+
														'<h6 class="mb-0">No Job Yesterday</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-2">'+
														'<h6 class="mb-0 text-right">'+numberFormat(scoreNoJobYesterday)+'</h6>'+
													'</div>'+
													'<div class="col-sm-7 mb-2">'+
														'<h6 class="mb-0">Duration Yesterday</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-2">'+
														'<h6 class="mb-0 text-right">'+numberFormat(scoreDurationYesterday)+'</h6>'+
													'</div>'+
													'<div class="col-sm-7 mb-2">'+
														'<h6 class="mb-0">Drive Type</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-2">'+
														'<h6 class="mb-0 text-right">'+numberFormat(scoreLastDriveType)+'</h6>'+
													'</div>'+
													'<div class="col-sm-7 mb-2">'+
														'<h6 class="mb-0">Next Job Rate</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-2">'+
														'<h6 class="mb-0 text-right">'+numberFormat(scoreJobRatePriority)+'</h6>'+
													'</div>'+
												'</div>'+
												'<div class="row mb-10">'+
													'<div class="col-sm-7 mb-5">'+
														'<h6 class="mb-0">Score Total</h6>'+
													'</div>'+
													'<div class="col-sm-5 mb-5">'+
														'<h6 class="mb-0 text-right">'+numberFormat(scoreTotal)+'</h6>'+
													'</div>'+
												'</div>'+
											'</div>'+
										'</div>';
				if(elemDriverScore !== undefined) $("#containerListDriverSnapshotHistory").append(rowListDriver);
			}
			
		}
		
	}
	
	$('#modal-snapshotHistory').modal('show');
	
}

function getDriverList(){
	
	var arrDriverList		=	[],
		totalDriverTour		=	totalDriverCharter	=	totalDriverShuttle	=	totalDriverSmall	=	totalDriverStandard	=	totalDriverMinivan	=	totalDriverInnova	=	0,
		totalDriverJobTop	=	totalDriverJobGood	=	totalDriverJobStandard	=	0,
		totalLastRateTop	=	totalLastRateGood	=	totalLastRateStandard	=	0;
	
	$('.availableDriverItem').each(function() {
		var idDriver				=	$(this).attr("data-idDriver"),
			driverRank				=	$(this).attr("data-driverRank"),
			driverType				=	$(this).attr("data-driverType"),
			carCapacity				=	$(this).attr("data-carCapacity"),
			carCapacityMax			=	$(this).attr("data-carCapacityMax"),
			pickupArea				=	$(this).attr("data-pickupArea"),
			lastTypeJob				=	$(this).attr("data-lastTypeJob"),
			totalConsecutiveNoJob	=	$(this).attr("data-totalConsecutiveNoJob"),
			lastRateJob				=	$(this).attr("data-lastRateJob"),
			lastTypeDrive			=	$(this).attr("data-lastTypeDrive"),
			nextJobPriority			=	$(this).attr("data-nextJobPriority"),
			driverName				=	$(this).attr("data-driverName");
		arrDriverList.push({idDriver:idDriver, arrData:[driverRank, driverType, carCapacity, carCapacityMax, pickupArea, lastTypeJob, driverName, lastTypeDrive, nextJobPriority, lastRateJob, totalConsecutiveNoJob]});
		
		if(driverType == 2) totalDriverTour++;
		if(driverType == 3) totalDriverCharter++;
		if(driverType == 1) totalDriverShuttle++;
		if(carCapacity == 1) totalDriverSmall++;
		if(carCapacity == 2) totalDriverStandard++;
		if(carCapacity == 3) totalDriverMinivan++;
		if(carCapacity == 4) totalDriverInnova++;
		if(nextJobPriority == 3) totalDriverJobTop++;
		if(nextJobPriority == 2) totalDriverJobGood++;
		if(nextJobPriority == 1) totalDriverJobStandard++;
		if(lastRateJob == 3) totalLastRateTop++;
		if(lastRateJob == 2) totalLastRateGood++;
		if(lastRateJob == 1) totalLastRateStandard++;
	});
	
	return {
			arrDriverList:arrDriverList,
			totalDriverTour:totalDriverTour,
			totalDriverCharter:totalDriverCharter,
			totalDriverShuttle:totalDriverShuttle,
			totalDriverSmall:totalDriverSmall,
			totalDriverStandard:totalDriverStandard,
			totalDriverMinivan:totalDriverMinivan,
			totalDriverInnova:totalDriverInnova,
			totalDriverJobTop:totalDriverJobTop,
			totalDriverJobGood:totalDriverJobGood,
			totalDriverJobStandard:totalDriverJobStandard,
			totalLastRateTop:totalLastRateTop,
			totalLastRateGood:totalLastRateGood,
			totalLastRateStandard:totalLastRateStandard
		};
	
}

$('#btnCancelAutoSchedule').off('click');
$('#btnCancelAutoSchedule').on('click', function(e) {
	e.preventDefault();
	$('#btnSnapshotAutoSchedule').removeClass("d-none");
	$('#btnSaveAutoSchedule, #btnCancelAutoSchedule, #autoScheduleProcessWarning').addClass("d-none");

	getDataScheduleAuto();
	getDataDriverList();
	getDataScheduleManual();
});

$('#btnSaveAutoSchedule').off('click');
$('#btnSaveAutoSchedule').on('click', function(e) {
	e.preventDefault();
	$confirmSaveDialog.find('#modal-confirm-body').html("Make sure the auto-scheduling results contain no errors.<br/><br/>Continue saving process?");
	$confirmSaveDialog.find('#confirmBtn').attr('data-action', "saveAutoSchedule");
	$confirmSaveDialog.modal('show');
});

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	e.preventDefault();
	var actionType	=	$('#confirmBtn').attr('data-action');
	$('#modal-confirm-action').modal('hide');
	
	if(actionType = "saveAutoSchedule"){
		
		var procPromise	=	$.when(),
			totalData	=	0;
		
		$('.unscheduledReservationItem').each(function() {
			var idReservationDetails	=	$(this).attr("data-idReservationDetails") * 1,
				idDriver				=	$(this).attr("data-idDriver") * 1;
			if(idDriver != 0 && idReservationDetails != 0) totalData++;
		});

		localStorage.setItem('numberProcessAutoSch', 0);
		localStorage.setItem('totalSuccessAutoSch', 0);
		localStorage.setItem('totalFailedAutoSch', 0);
		
		$('.unscheduledReservationItem').each(function() {
			var idReservationDetails	=	$(this).attr("data-idReservationDetails") * 1,
				idDriver				=	$(this).attr("data-idDriver") * 1,
				driverName				=	$("#driverName"+idDriver).html(),
				driverRank				=	$("#driverRankNumber"+idDriver).html(),
				driverType				=	$("#reservationDriverType"+idReservationDetails).html(),
				jobType					=	$("#reservationJobType"+idReservationDetails).html(),
				reservationDetails		=	$("#reservationDetails"+idReservationDetails).html();
			procPromise = procPromise.then(function(response) { 
				return sendPromiseSchedule(idReservationDetails, idDriver, totalData, driverRank, driverName, driverType, jobType, reservationDetails);
			});
		});
		
	}
	
});

function moveScheduleToManual(idReservationDetails){
	
	var dataSend	=	{idReservationDetails:idReservationDetails};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/moveScheduleToManual",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			setUserToken(response);
			NProgress.done();
			
			if(response.status == 200){
				getDataScheduleManual();
				$("#unscheduledReservationItem"+idReservationDetails).remove();
				setSortableListSchedule();
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
	
}

function moveScheduleToAutomatic(idReservationDetails){
	
	var dataSend	=	{idReservationDetails:idReservationDetails};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/moveScheduleToAutomatic",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			setUserToken(response);
			NProgress.done();
			
			if(response.status == 200){
				getDataScheduleAuto();
				$("#manualReservationListItem"+idReservationDetails).remove();
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
		}
	});
	
}

function showListDriverModal(idDriverType, idReservationDetails){
	
	var scheduleDate=	$('#scheduleDate').val();
	var dataSend	=	{scheduleDate:scheduleDate, idDriverType:idDriverType, idReservationDetails:idReservationDetails};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverScheduleAuto/getDataDriverListManual",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$('#searchKeywordDriver').val("");
		},
		success:function(response){
			$('#window-loader').modal('hide');
			setUserToken(response);
			NProgress.done();
			
			var detailReservation	=	response.detailReservation;
			$("#driverTypeStr").html(detailReservation.DRIVERTYPE);
			$("#dateTimeStr").html(detailReservation.SCHEDULEDATE+' '+detailReservation.RESERVATIONTIMESTART);
			$("#reservationDetailStr").html(detailReservation.CUSTOMERNAME+' - '+detailReservation.RESERVATIONTITLE);
			$("#jobDetailStr").html(detailReservation.PRODUCTNAME);
			
			if(response.status != 200){
				$("#saveDriverToReservation").addClass("d-none");
				$("#containerDriverList").removeClass("scrollList").html("<tr><td colspan='2'>No driver data found</td><tr>");
			} else {
				
				var elemListDriver	=	"";
				$.each(response.result, function(index, array) {

					var radioElem	=	array.IDDAYOFF == 0 ?
										"<label class='adomx-radio-2 pull-right mt-10'>"+
											"<input type='radio' name='radioDriverList' id='radioDriverList"+array.IDDRIVER+"' value='"+array.IDDRIVER+"'> <i class='icon'></i>"+
										"</label>"
										: "";
					var dayOffElem	=	array.IDDAYOFF == 0 ? "" :
										'<div class="alert alert-danger py-2 px-2" role="alert">'+
											'<i class="fa fa-exclamation-triangle"></i> Day Off : '+array.REASON+
										'</div>';
					
					var badgePartnershipType	=	"";
					switch(array.PARTNERSHIPTYPE){
						case "1"	:	badgePartnershipType	=	'<span class="badge badge-primary pull-right">Internal</span>'; break;
						case "2"	:	badgePartnershipType	=	'<span class="badge badge-info pull-right">Freelance</span>'; break;
						case "3"	:	badgePartnershipType	=	'<span class="badge badge-warning pull-right">Team</span>'; break;
						case "4"	:	badgePartnershipType	=	'<span class="badge badge-dark pull-right">Office</span>'; break;
						default		:	badgePartnershipType	=	''; break;
					}
					
					elemListDriver	+=	"<tr class='driverListElement' data-driverName='"+array.DRIVERNAME+"'>"+
											"<td>[#"+array.RANKNUMBER+"] ["+array.DRIVERTYPE+"] <b>"+array.DRIVERNAME+"</b>"+badgePartnershipType+"<br/><small class='mt-1'>Total schedule on the selected date : "+array.TOTALSCHEDULE+"</small><br/>"+dayOffElem+"</td>"+
											"<td width='10' align='center'>"+radioElem+"</td>"+
										"</tr>";
				});
				
				$("#containerDriverList").addClass("scrollList").html(elemListDriver);
				$("#saveReservationToDriver").removeClass("d-none");
			}

			$("#idReservationDetails").val(idReservationDetails);
			$('#modal-driverList').modal('show');

			$('#searchKeywordDriver').off('keyup');
			$('#searchKeywordDriver').on('keyup', function(e) {
				e.preventDefault();
				var keyword	=	$(this).val().toLowerCase();
				$(".driverListElement").each(function(index) {
					var driverNameTr	=	$(this).attr("data-driverName").toLowerCase();
					$(this).removeClass("d-none");
					if(keyword != "" && driverNameTr.indexOf(keyword) < 0) $(this).addClass("d-none");
				});
			});
		}
	});
}

$('#content-driverList').off('submit');
$('#content-driverList').on('submit', function(e) {
	
	e.preventDefault();
	var arrIDReservationDetails	=	[$("#idReservationDetails").val()];
	var idDriver				=	$("input[name=radioDriverList]:checked").val(),
		dataSend				=	{idDriver:idDriver, arrIDReservationDetails:arrIDReservationDetails};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverSchedule/saveDriverSchedule",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-driverList :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-driverList :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-driverList').modal('hide');
				getDataDriverList();
				getDataScheduleManual();
			}
			
		}
	});
});

function sendPromiseSchedule(idReservationDetails, idDriver, totalData, driverRank, driverName, driverType, jobType, reservationDetails) {
	
	var numberProcess	=	(localStorage.getItem('numberProcessAutoSch') * 1) + 1;
	$('#dataProcessNumber').html(numberProcess+"/"+totalData);
	$('#jobDetailDialogStr').html("["+driverType+"] ["+jobType+"]<br/>"+reservationDetails);
	$('#driverNameStr').html("["+driverRank+"] "+driverName);
	localStorage.setItem('numberProcessAutoSch', numberProcess);
	
	if(numberProcess < (totalData * 1)){
		$('#loader-scheduleSave').modal('show');
	}
	
	return $.ajax({
		type: 'POST',
			url: baseURL+"schedule/driverSchedule/saveDriverSchedule",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend({arrIDReservationDetails:[idReservationDetails], idDriver:idDriver}),
			success:function(response){

				switch(response.status){
					case 200	:	localStorage.setItem('totalSuccessAutoSch', (localStorage.getItem('totalSuccessAutoSch') * 1) + 1); break
					default		:	localStorage.setItem('totalFailedAutoSch', (localStorage.getItem('totalFailedAutoSch') * 1) + 1); break;
				}
				
				if(numberProcess >= (totalData * 1)){
					$('#loader-scheduleSave').modal('hide');
					$('#modalWarning').on('show.bs.modal', function() {
						$('#modalWarningBody').html("Process complete. Total data : "+totalData+"<br/>Success : "+localStorage.getItem('totalSuccessAutoSch')+"<br/>Failed : "+localStorage.getItem('totalFailedAutoSch'));
					});
					$('#modalWarning').modal('show');
					$('#btnSaveAutoSchedule, #btnCancelAutoSchedule').addClass("d-none");
					$('#btnSnapshotAutoSchedule').removeClass("d-none");
					getDataScheduleAuto();
					getDataDriverList();
					getDataScheduleManual();
				}
				
				return response;
			}
	});
}

scheduleDriverAutoFunc();