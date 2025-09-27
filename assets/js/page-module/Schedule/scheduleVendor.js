var $confirmationDialog= $('#modal-confirm-action');

if (scheduleVendorFunc == null){
	var scheduleVendorFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionVendor', 'dataVendorTicket');
			$("#optionVendor").select2();

			getDataSchduleVendor();
		});	
	}
}

$('#scheduleDate, #optionConfirmationStatus, #optionVendor').off('change');
$('#scheduleDate, #optionConfirmationStatus, #optionVendor').on('change', function(e) {
	getDataSchduleVendor();
});

function getDataSchduleVendor(){
	
	var scheduleDate		=	$('#scheduleDate').val(),
		confirmationStatus	=	$('#optionConfirmationStatus').val(),
		idVendor			=	$('#optionVendor').val(),
		dataSend			=	{scheduleDate:scheduleDate, confirmationStatus:confirmationStatus, idVendor:idVendor};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/vendorSchedule/getDataVendorSchedule",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#containerListScheduleVendor").html("<center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var rows			=	"";
			if(response.status != 200){
				rows	=	'<center>'+
								'<img src="'+ASSET_IMG_URL+'no-data.png" width="120px"/>'+
								'<h5>No Data Found</h5>'+
								'<p>There are no vendor schedule <b>on the filter</b> you have selected</p>'+
							'</center>';
			} else {
				var data	=	response.dataActiveVendor;
				$.each(data, function(index, array) {
					var idVendor		=	array.IDVENDOR,
						arrReservation	=	array.ARRRESERVATION,
						rowsReservation	=	'';
					
					$.each(arrReservation, function(index, arrayReservation) {
						var firstRowPackage	=	nextRowPackage	=	'',
							totalRowSpan	=	0,
							hotelName		=	unescape(arrayReservation.HOTELNAME),
							pickUpLocation	=	unescape(arrayReservation.PICKUPLOCATION),
							dropOffLocation	=	unescape(arrayReservation.DROPOFFLOCATION),
							arrPackageList	=	arrayReservation.PACKAGELIST,
							btnShowDetails	=	'<button type="button" class="button button-xs button-box button-primary" onclick="showReservationDetails(\''+arrayReservation.IDRESERVATION+'\')"><i class="fa fa-info"></i></button>';
						
						$.each(arrPackageList, function(index, arrayPackageList) {
							var reservationText		=	'['+arrayReservation.RESERVATIONTIMESTART+'] '+arrayReservation.RESERVATIONTITLE+' ('+arrayPackageList.PACKAGENAME+') - '+arrayReservation.CUSTOMERNAME,
								badgeStatusConfirm	=	createBadgeStatusConfirm(arrayPackageList.STATUSCONFIRM),
								badgeStatusProccess	=	createBadgeStatusProcess(arrayPackageList.STATUSPROCESS, arrayPackageList.STATUSPROCESSNAME),
								btnResendNotif		=	arrayPackageList.STATUSCONFIRM == 0 || arrayPackageList.STATUSCONFIRM == "0" ?
														'<button type="button" class="button button-xs button-box button-warning" data-toggle="tooltip" data-placement="top" data-original-title="Resend schedule notitication to vendor" onclick="confirmResendNotification('+arrayPackageList.IDRESERVATIONDETAILS+', \''+array.NAME+'\', \''+reservationText+'\')"><i class="fa fa-bullhorn"></i></button>' :
														'',
								btnEditCostDetails	=	'<button type="button" class="button button-xs button-box button-success" onclick="editReservationDetails(\''+arrayPackageList.IDRESERVATIONDETAILS+'\')"><i class="fa fa-pencil"></i></button>',
								badgeScheduleDiff	=	arrayPackageList.STATUSCONFIRM == 1 && arrayPackageList.TIMEBOOKING != arrayPackageList.TIMESCHEDULE ? "<span class='badge badge-warning' id='badgeScheduleDiff"+arrayPackageList.IDSCHEDULEVENDOR+"'><i class='fa fa-warning' aria-hidden='true'></i> Schedule Difference</span>" : "";
								newRowPackage		=	"<td>"+arrayPackageList.PACKAGENAME+"</td>"+
														"<td align='right'>"+arrayPackageList.PAXADULT+"</td>"+
														"<td align='right'>"+arrayPackageList.PAXCHILD+"</td>"+
														"<td align='right'>"+arrayPackageList.PAXINFANT+"</td>"+
														"<td id='tdScheduleVendor"+arrayPackageList.IDSCHEDULEVENDOR+"'>"+
															"<div class='order-details-customer-info'>"+
																"<ul>"+
																	"<li> <span>Pick Up</span> <span>"+arrayPackageList.TIMEPICKUP+"</span> </li>"+
																	"<li> <span>Slot Booking</span> <span id='spanSlotBooking"+arrayPackageList.IDSCHEDULEVENDOR+"' data-statusConfirm='"+arrayPackageList.STATUSCONFIRM+"'>"+arrayPackageList.TIMEBOOKING+" <i class='fa fa-pencil pull-right' style='font-size: 18px;' onclick='editSlotBookingTime(\""+arrayPackageList.TIMEBOOKING+"\", \""+arrayPackageList.TIMEPICKUP+"\", "+arrayPackageList.IDSCHEDULEVENDOR+", \""+hotelName+"\", \""+pickUpLocation+"\", \""+dropOffLocation+"\")'></i></span> </li>"+
																	"<li> <span>Slot Schedule</span> <span id='spanSlotSchedule"+arrayPackageList.IDSCHEDULEVENDOR+"'>"+arrayPackageList.TIMESCHEDULE+"</span> </li>"+
																"</ul>"+
															"</div>"+
															badgeScheduleDiff+
														"</td>"+
														"<td>"+badgeStatusConfirm+"</td>"+
														"<td>"+badgeStatusProccess+"</td>"+
														"<td align='center'>"+btnResendNotif+" "+btnEditCostDetails+"</td>";
							if(totalRowSpan == 0) firstRowPackage	=	newRowPackage;
							if(totalRowSpan != 0) nextRowPackage	+=	"<tr>"+newRowPackage+"</tr>";
							totalRowSpan++;
						});
						
						rowsReservation		+=	"<tr>"+
													"<td rowspan='"+totalRowSpan+"'>"+
														"<b>"+arrayReservation.SOURCENAME+" - "+arrayReservation.BOOKINGCODE+"</b><br/>"+
														arrayReservation.RESERVATIONTITLE+"<br/>"+
														arrayReservation.CUSTOMERNAME+
													"</td>"+
													firstRowPackage+
													"<td align='center'>"+btnShowDetails+"</td>"+
												"</tr>"+
												nextRowPackage;
					});

					rows	+=	'<div class="card">'+
									'<div class="card-header">'+
										'<h2>'+
											'<button data-toggle="collapse" data-target="#collapseVendor'+idVendor+'" aria-expanded="true">'+
												'['+array.TOTALRESERVATION+' Rsv] '+array.NAME+'<br/>'+
											'</button>'+
										'</h2>'+
									'</div>'+
									'<div id="collapseVendor'+idVendor+'" class="collapse show">'+
										'<div class="card-body">'+
											'<table class="table">'+
												'<thead class="thead-light">'+
													'<tr>'+
														'<th>Reservation Details</th>'+
														'<th>Packages</th>'+
														'<th class="text-right" width="80">Adult</th>'+
														'<th class="text-right" width="80">Child</th>'+
														'<th class="text-right" width="80">Infant</th>'+
														'<th width="250">Time</th>'+
														'<th width="120">Confirmation</th>'+
														'<th width="120">Status</th>'+
														'<th width="90"></th>'+
														'<th width="50"></th>'+
													'</tr>'+
												'</thead>'+
												'<tbody>'+
													rowsReservation+
												'</tbody>'+
											'</table>'+
										'</div>'+
									'</div>'+
								'</div>';
				});
			}

			$("#containerListScheduleVendor").html(rows);
		}

	});
	
}

function editSlotBookingTime(timeBooking, timePickup, idScheduleVendor, hotelName, pickUpLocation, dropOffLocation){
	var splitTimeBooking	=	timeBooking.split(':'),
		hourBooking			=	splitTimeBooking[0],
		minuteBooking		=	splitTimeBooking[1];
	setOptionHelper("bookScheduleSlot-hour", "dataHours", hourBooking);
	setOptionHelper("bookScheduleSlot-minute", "dataMinutes", minuteBooking);
	$("#bookScheduleSlot-idScheduleVendor").val(idScheduleVendor);
	$("#bookScheduleSlot-hotelName").html(hotelName);
	$("#bookScheduleSlot-pickUpLocation").html(pickUpLocation);
	$("#bookScheduleSlot-dropOffLocation").html(dropOffLocation);
	$("#bookScheduleSlot-timePickUp").html(timePickup);
	if($('#modal-detailReservation').hasClass('show')) $('#modal-detailReservation').modal('hide');
	$('#modal-bookScheduleSlot').modal('show');
}

$('#content-bookScheduleSlot').off('submit');
$('#content-bookScheduleSlot').on('submit', function(e) {
	e.preventDefault();
	var idScheduleVendor=	$("#bookScheduleSlot-idScheduleVendor").val(),
		slotHour		=	$("#bookScheduleSlot-hour").val(),
		slotMinute		=	$("#bookScheduleSlot-minute").val(),
		slotTime		=	slotHour+":"+slotMinute,
		dataSend		=	{
								idScheduleVendor:idScheduleVendor,
								slotTime:slotTime
							};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/vendorSchedule/updateSlotTimeBooking",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#content-bookScheduleSlot :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-bookScheduleSlot :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');
					
			if(response.status == 200){
				var statusConfirm	=	$('#spanSlotBooking'+idScheduleVendor).attr('data-statusConfirm'),
					timeSchedule	=	$('#spanSlotSchedule'+idScheduleVendor).html();
				if(statusConfirm == 1 && timeSchedule == slotTime){
					$('#badgeScheduleDiff'+idScheduleVendor).remove();
				} else {
					if($('#badgeScheduleDiff'+idScheduleVendor).length <= 0) $("#tdScheduleVendor"+idScheduleVendor).append("<span class='badge badge-warning' id='badgeScheduleDiff"+idScheduleVendor+"'><i class='fa fa-warning' aria-hidden='true'></i> Schedule Difference</span>");
				}
				var hotelName		=	$("#bookScheduleSlot-hotelName").html(),
					pickUpLocation	=	$("#bookScheduleSlot-pickUpLocation").html(),
					dropOffLocation	=	$("#bookScheduleSlot-dropOffLocation").html(),
					timePickUp		=	$("#bookScheduleSlot-timePickUp").html();
				$('#spanSlotBooking'+idScheduleVendor).html(slotTime+" <i class='fa fa-pencil pull-right' style='font-size: 18px;' onclick='editSlotBookingTime(\""+slotTime+"\", \""+timePickUp+"\", "+idScheduleVendor+", \""+hotelName+"\", \""+pickUpLocation+"\", \""+dropOffLocation+"\")'></i>");
				$('#modal-bookScheduleSlot').modal('hide');
			}			
		}
	});
	
});

function editReservationDetails(idReservationDetails){
	
	var dataSend		=	{idReservationDetails:idReservationDetails};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/vendorSchedule/getDetailReservationDetailsProduct",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#editorReservationDetails-reservationTitleStr, #editorReservationDetails-custNameStr, #editorReservationDetails-custContactStr, #editorReservationDetails-custEmailStr").html('-');
			$("#editorReservationDetails-paxAdultStr, #editorReservationDetails-paxChildStr, #editorReservationDetails-paxInfantStr").html('-');
			$("#ticketAdultPax, #pricePerPaxAdult, #totalPriceAdult, #ticketChildPax, #pricePerPaxChild, #totalPriceChild, #ticketInfantPax, #pricePerPaxInfant, #totalPriceInfant, #ticketProductNominal").val(0);
			$("#ticketProductNotes").val('-');
			$("#correctionNotes").val('');
			$('#optionProductTicketVendor').empty();
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailData			=	response.detailData,
					productTicketVendor	=	response.productTicketVendor,
					ticketDetail		=	response.ticketDetail;

				$("#editorReservationDetails-reservationTitleStr").html('['+detailData.SOURCENAME+' - '+detailData.BOOKINGCODE+'] '+detailData.RESERVATIONTITLE+' <span class="badge badge-primary pull-right">'+detailData.SCHEDULEDATE+'</span>');
				$("#editorReservationDetails-custNameStr").html(detailData.CUSTOMERNAME);
				$("#editorReservationDetails-custContactStr").html(detailData.CUSTOMERCONTACT);
				$("#editorReservationDetails-custEmailStr").html(detailData.CUSTOMEREMAIL);
				$("#editorReservationDetails-paxAdultStr").html(detailData.NUMBEROFADULT);
				$("#editorReservationDetails-paxChildStr").html(detailData.NUMBEROFCHILD);
				$("#editorReservationDetails-paxInfantStr").html(detailData.NUMBEROFINFANT);
				
				$("#ticketAdultPax").val(numberFormat(ticketDetail.PAXADULT));
				$("#pricePerPaxAdult").val(numberFormat(ticketDetail.PRICEPERPAXADULT));
				$("#totalPriceAdult").val(numberFormat(ticketDetail.PRICETOTALADULT));
				
				$("#ticketChildPax").val(numberFormat(ticketDetail.PAXCHILD));
				$("#pricePerPaxChild").val(numberFormat(ticketDetail.PRICEPERPAXCHILD));
				$("#totalPriceChild").val(numberFormat(ticketDetail.PRICETOTALCHILD));
				
				$("#ticketInfantPax").val(numberFormat(ticketDetail.PAXINFANT));
				$("#pricePerPaxInfant").val(numberFormat(ticketDetail.PRICEPERPAXINFANT));
				$("#totalPriceInfant").val(numberFormat(ticketDetail.PRICETOTALINFANT));
				
				$("#ticketProductNominal").val(numberFormat(detailData.NOMINAL));
				$("#ticketProductNotes").val(detailData.NOTES);
				$("#correctionNotes").val(detailData.CORRECTIONNOTES);
				$("#idReservationDetailsEditor").val(idReservationDetails);
				
				generateOptionHelperVendorProduct(productTicketVendor, detailData.PRODUCTNAME);
				$('#modal-editorReservationDetails').modal('show');
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
			$('#optionProductTicketVendor').append($('<option '+selected+' data-voucherStatus="'+array.VOUCHERSTATUS+'" data-productname="'+array.PRODUCTNAME+'" data-vendorname="'+array.VENDORNAME+'" data-priceAdult="'+array.PRICEADULT+'" data-priceChild="'+array.PRICECHILD+'" data-priceInfant="'+array.PRICEINFANT+'" data-notes="'+array.NOTES+'"></option>').val(array.VALUE).html(array.OPTIONTEXT));
		});
			
		$('#optionProductTicketVendor').select2({
			dropdownParent: $("#modal-editorReservationDetails")
		});
	}

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

$('#content-editorReservationDetails').off('submit');
$('#content-editorReservationDetails').on('submit', function(e) {
	e.preventDefault();
	var idVendor			=	$("#optionProductTicketVendor").val(),
		nominalCost			=	$("#ticketProductNominal").val(),
		ticketAdultPax		=	$("#ticketAdultPax").val(),
		ticketChildPax		=	$("#ticketChildPax").val(),
		ticketInfantPax		=	$("#ticketInfantPax").val(),
		pricePerPaxAdult	=	$("#pricePerPaxAdult").val(),
		pricePerPaxChild	=	$("#pricePerPaxChild").val(),
		pricePerPaxInfant	=	$("#pricePerPaxInfant").val(),
		totalPriceAdult		=	$("#totalPriceAdult").val(),
		totalPriceChild		=	$("#totalPriceChild").val(),
		totalPriceInfant	=	$("#totalPriceInfant").val(),
		notes				=	$("#ticketProductNotes").val(),
		correctionNotes		=	$("#correctionNotes").val(),
		vendorName			=	$('#optionProductTicketVendor option:selected').attr('data-vendorname'),
		productName			=	$('#optionProductTicketVendor option:selected').attr('data-productname'),
		voucherStatus		=	$('#optionProductTicketVendor option:selected').attr('data-voucherStatus'),
		idReservationDetails=	$("#idReservationDetailsEditor").val();
	
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
		url: baseURL+"schedule/vendorSchedule/saveReservationDetails",
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
				getDataSchduleVendor();
				$('#modal-editorReservationDetails').modal('hide');
			}			
		}
	});
	
});

function showReservationDetails(idReservation){
	
	var dataSend		=	{idReservation:idReservation};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/vendorSchedule/getDetailReservation",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#detailReservation-source, #detailReservation-title, #detailReservation-date, #detailReservation-time, #detailReservation-customerName").html("-");
			$("#detailReservation-customerContact, #detailReservation-customerEmail, #detailReservation-hotelName, #detailReservation-pickUpLocation").html("-");
			$("#detailReservation-dropOffLocation, #detailReservation-remark").html("-");
			$('.rowPackage').remove();
		},
		success:function(response){
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);
			
			if(response.status == 200){
				var detailData		=	response.detailData,
					pickupTime		=	unescape(detailData.RESERVATIONTIMESTART),
					hotelName		=	unescape(detailData.HOTELNAME),
					pickUpLocation	=	unescape(detailData.PICKUPLOCATION),
					dropOffLocation	=	unescape(detailData.DROPOFFLOCATION),
					packageList		=	detailData.PACKAGELIST,
					arrPackageList	=	packageList.split('~'),
					rowPackage		=	'';

				$("#detailReservation-source").html(detailData.SOURCENAME);
				$("#detailReservation-title").html(detailData.RESERVATIONTITLE);
				$("#detailReservation-date").html(detailData.SCHEDULEDATE);
				$("#detailReservation-time").html(detailData.RESERVATIONTIMESTART);
				$("#detailReservation-customerName").html(detailData.CUSTOMERNAME);
				$("#detailReservation-customerContact").html(detailData.CUSTOMERCONTACT);
				$("#detailReservation-customerEmail").html(detailData.CUSTOMEREMAIL);
				$("#detailReservation-hotelName").html(hotelName);
				$("#detailReservation-pickUpLocation").html(pickUpLocation);
				$("#detailReservation-dropOffLocation").html(dropOffLocation);
				$("#detailReservation-remark").html(detailData.REMARK);
				
				$.each(arrPackageList, function(index, arrayPackageList) {
					var arrDetailPackage	=	arrayPackageList.split('|'),
						badgeStatusConfirm	=	createBadgeStatusConfirm(arrDetailPackage[4]),
						badgeStatusProccess	=	createBadgeStatusProcess(arrDetailPackage[6], arrDetailPackage[5]),
						btnEditBookSlot		=	"<i class='fa fa-pencil pull-right' style='font-size: 18px;' onclick='editSlotBookingTime(\""+arrDetailPackage[9]+"\", \""+pickupTime+"\", "+arrDetailPackage[10]+", \""+hotelName+"\", \""+pickUpLocation+"\", \""+dropOffLocation+"\")'></i>";
					rowPackage				+=	"<tr class='rowPackage'>"+
													"<td>"+arrDetailPackage[0]+"</td>"+
													"<td align='right'>"+arrDetailPackage[1]+"</td>"+
													"<td align='right'>"+arrDetailPackage[2]+"</td>"+
													"<td align='right'>"+arrDetailPackage[3]+"</td>"+
													"<td>"+arrDetailPackage[9]+btnEditBookSlot+"</td>"+
													"<td>"+arrDetailPackage[7]+"</td>"+
													"<td>"+badgeStatusConfirm+"</b></td>"+
													"<td>"+badgeStatusProccess+"</td>"+
												"</tr>";
				});				
				
				$("#detailReservation-tbodyListPackage").html(rowPackage);
				$('#modal-detailReservation').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
		
	});
	
}

function createBadgeStatusConfirm(STATUSCONFIRM){
	var badgeStatusConfirm	=	'-';
					
	switch(STATUSCONFIRM){
		case "0"	:	
		case 0		:	badgeStatusConfirm	=	'<span class="badge badge-danger">Unconfirm</span>';
						break;
		case "1"	:	
		case 1		:	badgeStatusConfirm	=	'<span class="badge badge-success">Confirmed</span>';
						break;
	}
	
	return badgeStatusConfirm;
}

function createBadgeStatusProcess(STATUSPROCESS, STATUSPROCESSNAME){
	var badgeStatusProccess	=	'-';
					
	switch(STATUSPROCESS){
		case "1"	:	
		case 1		:	badgeStatusProccess	=	'<span class="badge badge-info">'+STATUSPROCESSNAME+'</span>';
						break;
		case "2"	:	
		case 2		:	badgeStatusProccess	=	'<span class="badge badge-primary">'+STATUSPROCESSNAME+'</span>';
						break;
		case "3"	:	
		case 3		:	badgeStatusProccess	=	'<span class="badge badge-success">'+STATUSPROCESSNAME+'</span>';
						break;
	}
	
	return badgeStatusProccess;
}

function confirmResendNotification(idReservationDetails, vendorName, reservationText){
	
	var confirmText		=	'You will resend notification to vendor : <b>'+vendorName+'</b><br/>Schedule : <b>'+reservationText+'</b>.<br/><br/>Are you sure?';
		
	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', idReservationDetails).attr('data-function', 'resendScheduleNotification');
	$confirmationDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var idData	=	$confirmationDialog.find('#confirmBtn').attr('data-idData'),
		funcName=	$confirmationDialog.find('#confirmBtn').attr('data-function'),
		dataSend=	{idData:idData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/vendorSchedule/"+funcName,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$confirmationDialog.modal('hide');
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
				getDataSchduleVendor();
			}
		}
	});
});

scheduleVendorFunc();