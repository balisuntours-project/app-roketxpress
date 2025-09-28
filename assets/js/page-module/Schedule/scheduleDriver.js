var $confirmationDialog = $('#modal-confirm-action');

if (scheduleDriverFunc == null) {
	var scheduleDriverFunc = function () {
		$(document).ready(function () {
			var callbackFunc = null,
				idDayoffRequest = false,
				idReservationDetails = false;
			if (localStorage.getItem('OSNotificationData') === null || localStorage.getItem('OSNotificationData') === undefined) {
				var minHourDateFilterTomorrow = $("#minHourDateFilterTomorrow").val() * 1,
					d = new Date(),
					hour = d.getHours(),
					intHour = hour * 1;

				if (intHour >= minHourDateFilterTomorrow) {
					$("#scheduleDate").val(dateTomorrow);
				} else {
					$("#scheduleDate").val(dateToday);
				}
			} else {

				var OSNotificationData = JSON.parse(localStorage.getItem('OSNotificationData')),
					OSNotifType = OSNotificationData.type,
					OSNotifDate = OSNotificationData.date,
					OSNotiftabMenuView = OSNotificationData.tabMenuView;

				if (OSNotifType == "driverschedule" || OSNotifType == "schedule-driver") {

					if (typeof OSNotiftabMenuView !== 'undefined' && OSNotiftabMenuView !== null && OSNotiftabMenuView !== "") {
						$('.nav-tabs a[href="#' + OSNotiftabMenuView + '"]').tab('show');
						if (OSNotiftabMenuView == "dayOffRequestTab") {
							idDayoffRequest = OSNotificationData.idDayOffRequest;
						} else if (OSNotiftabMenuView == "driverListTab") {
							idReservationDetails = OSNotificationData.idReservationDetails;
							$("#scheduleDate").val(OSNotificationData.dateSchedule);
						}
					} else {
						idReservationDetails = OSNotificationData.idReservationDetails;
						$("#scheduleDate").val(OSNotifDate);
						$('.nav-tabs a[href="#reservationTab"]').tab('show');
						callbackFunc = function () {
							$('.addDriverBtn[data-idreservationdetails=' + idReservationDetails + ']').trigger("click");
						}
					}

					localStorage.removeItem("OSNotificationData");

				}

			}

			setOptionHelper('optionDayOffDriver', 'dataDriver');
			setOptionHelper('optionDriverFilter', 'dataDriver');
			$("#optionDriverFilter").select2();
			$("#dayOffRequestTab-dayOffDate").val("");

			getDataDriverSchedule(idReservationDetails);
			getDataReservationSchedule(callbackFunc);
			getDataDriverCalendar();
			getDataDayOffRequest(idDayoffRequest);

			$('#optionDayOffDriver').select2({
				dropdownParent: $("#editor-modal-inputDayOff")
			});
		});
	}
}

$('#scheduleDate').off('change');
$('#scheduleDate').on('change', function (e) {
	getDataDriverSchedule();
	getDataReservationSchedule();
	getDataDriverCalendar();
	getDataDayOffRequest();
});

$('#optionDriverFilter').off('change');
$('#optionDriverFilter').on('change', function (e) {
	getDataReservationSchedule();
});

$('#searchKeyword').off('keypress');
$("#searchKeyword").on('keypress', function (e) {
	if (e.which == 13) {
		getDataReservationSchedule();
	}
});

function getDataDriverSchedule(idReservationDetails = false) {
	var $tableBody = $('#table-driverScheduleList > tbody'),
		columnNumber = $('#table-driverScheduleList > thead > tr > th').length,
		scheduleDate = $('#scheduleDate').val(),
		dataSend = { scheduleDate: scheduleDate };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDataDriverSchedule",
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

			var data = response.result,
				totalReservation = response.dataTotalReservation,
				rows = strInfoTotalReservation = "";

			if (totalReservation['TOTALRESERVATION'] <= 0) {
				strInfoTotalReservation = "There are no unscheduled reservations for the selected date (<b>" + response.scheduleDateStr + "</b>)";
			} else {
				strInfoTotalReservation = "Total unscheduled reservations : <b>" + totalReservation['TOTALRESERVATION'] + "</b>. Shuttle : <b>" + totalReservation['TOTALRESERVATIONSHUTTLE'] + "</b>, Charter : <b>" + totalReservation['TOTALRESERVATIONCHARTER'] + "</b>, Tour : <b>" + totalReservation['TOTALRESERVATIONTOUR'] + "</b>";
			}

			$("#totalReservationInfo").html(strInfoTotalReservation);

			if (response.status != 200 || data.length === 0) {
				rows = "<tr><td colspan='" + columnNumber + "' align='center'><center>No data found</center></td></tr>";
			} else {
				var arrDriverType = [],
					arrPartnershipType = [],
					arrPartnershipTypeName = [];

				$.each(data, function (index, array) {

					var partnershipType = array.PARTNERSHIPTYPE,
						driverType = array.DRIVERTYPE,
						partnershipTypeName = '-';

					switch (partnershipType) {
						case "1":
						case 1: partnershipTypeName = "Partner - " + driverType; break;
						case "2":
						case 2: partnershipTypeName = "Freelance"; break;
						case "3":
						case 3: partnershipTypeName = "Team"; break;
						case "4":
						case 4: partnershipTypeName = "Office"; break;
					}

					if ((!arrPartnershipType.includes(partnershipType) || !arrDriverType.includes(driverType)) && !arrPartnershipTypeName.includes(partnershipTypeName)) {

						if (!arrPartnershipTypeName.includes(partnershipTypeName)) {
							rows += '<tr>' +
								'<td colspan="' + columnNumber + '"><h5>' + partnershipTypeName + '</h5></td>' +
								'<tr>';
						}

						arrPartnershipType.push(partnershipType);
						arrPartnershipTypeName.push(partnershipTypeName);
						arrDriverType.push(driverType);
					}

					var rowSpan = array.TOTALSCHEDULE == 0 || array.TOTALSCHEDULE == "" ? 1 : array.TOTALSCHEDULE;
					var btnEdit = array.IDDAYOFF == 0 && pmsAddDriverSchedule == '1' ?
						'<button class="button button-sm button-box button-primary mb-0" onclick="showModalListReservationList(' + array.IDDRIVERTYPE + ', \'' + array.DRIVERTYPE + '\', \'' + array.NAME + '\', ' + array.IDDRIVER + ')">' +
						'<i class="fa fa-plus"></i>' +
						'</button>'
						: "";
					var dataDetailDriver = array.NAME + " " + array.CARCAPACITYNAME;
					firstRsv = reservationRow = btnDeleteDayOff = "",
						statusConfirmFirst = -1;

					if (array.IDDAYOFF != 0 || array.IDDAYOFF != "0") {
						btnDeleteDayOff = response.allowDeleteDayOff == true ? '<i class="fa fa-times pull-right mt-1" style="font-size: 16px;" onclick="confirmDeleteDayOff(' + array.IDDAYOFF + ', \'' + array.NAME + '\', \'' + response.scheduleDateStr + '\', \'' + escape(array.REASON.replace(/['"]+/g, '')) + '\')"></i>' : '';
						firstRsv = '<td colspan="3">' +
							'<div class="alert alert-danger py-2 px-2" role="alert">' +
							'<i class="fa fa-exclamation-triangle"></i> Day Off : ' + array.REASON + btnDeleteDayOff +
							'</div>' +
							'</td>';
						if (array.TOTALSCHEDULE != 0 && array.TOTALSCHEDULE != "") {
							rowSpan++;
						}
					}

					if (array.ARRRESERVATION != "") {
						var arrReservationSplit = array.ARRRESERVATION.split("|"),
							arrDetailCarDriverPickup = array.ARRDETAILCARDRIVERPICKUP.split("|"),
							arrIdStatusProcessSplit = array.STATUSPROCESS.split("|"),
							arrStatusProcessSplit = array.STATUSPROCESSNAME.split("|"),
							arrIdScheduleSplit = array.ARRIDSCHEDULEDRIVER.split("|"),
							arrStatusConfirm = array.STATUSCONFIRM.split("|"),
							arrDateTimeConfirm = array.DATETIMECONFIRM.split("|"),
							arrIdReservationDetails = array.IDRESERVATIONDETAILS.split("|"),
							idxReservation = 0;

						arrReservationSplit.forEach((reservationText) => {
							var detailCarDriverPickup = arrDetailCarDriverPickup[idxReservation].length < 15 ? "" : arrDetailCarDriverPickup[idxReservation];
							badgeStatusProcess = '<span class="mt-2 pull-right badge badge-' + arrBadgeType[arrIdStatusProcessSplit[idxReservation]] + '">' + arrStatusProcessSplit[idxReservation] + '</span><br/>';
							badgeConfirmStatus = arrStatusConfirm[idxReservation] == 0 || arrStatusConfirm[idxReservation] == "0" ?
								'<i class="fa fa-clock-o text-danger pull-right ml-1" style="padding:3px;font-size: 25px;" data-toggle="tooltip" data-placement="top" data-original-title="Scheduled, Unconfirmed"></i>' :
								'<i class="fa fa-check text-success pull-right ml-1" style="padding:3px;font-size: 25px;" data-toggle="tooltip" data-placement="top" data-original-title="Scheduled, Confirmed at ' + arrDateTimeConfirm[idxReservation] + '"></i>',
								btnResendNotif = arrStatusConfirm[idxReservation] == 0 || arrStatusConfirm[idxReservation] == "0" ?
									'<span class="badge badge-warning pull-right" style="padding:7px;font-size: 16px;" data-toggle="tooltip" data-placement="top" data-original-title="Resend schedule notitication to driver" onclick="confirmResendNotification(' + arrIdScheduleSplit[idxReservation] + ', \'' + array.NAME + '\', \'' + reservationText + '\')"><i class="fa fa-bullhorn"></i></span>' :
									'',
								btnReservation = '<button class="button button-sm btn-block button-warning text-left mb-0" id="btnShowDetailReservation' + arrIdReservationDetails[idxReservation] + '" onclick="showModalDetailReservation(' + arrIdScheduleSplit[idxReservation] + ', 0)">' +
								reservationText.replace("<badgeStatus/>", badgeStatusProcess).replace("<detailCarDriverPickup/>", "<br/>" + detailCarDriverPickup) +
								'</button>',
								btnDeleteSchedule = '';

							//if(response.allowDeleteSchedule == true && arrIdStatusProcessSplit[idxReservation] < 3){
							btnDeleteSchedule = pmsDeleteDriverSchedule == '1' ?
								'<button class="button button-sm button-box button-secondary mb-0" onclick="confirmDeleteDriverSchedule(' + arrIdScheduleSplit[idxReservation] + ', \'' + reservationText + '\', \'' + array.NAME + '\')">' +
								'<i class="fa fa-trash"></i>' +
								'</button>'
								: '';
							//}

							if (idxReservation == 0 && firstRsv == "") {
								statusConfirmFirst = arrStatusConfirm[idxReservation];
								firstRsv += "<td>" + btnReservation + "</td>" +
									"<td>" + btnResendNotif + badgeConfirmStatus + "</td>" +
									"<td>" + btnDeleteSchedule + "</td>";
							} else {
								reservationRow += "<tr class='trDriverListTab' data-detailDriver='" + dataDetailDriver + "' data-arrReservation='" + array.ARRRESERVATION + "' data-statusConfirm='" + arrStatusConfirm[idxReservation] + "'>" +
									"<td>" + btnReservation + "</td>" +
									"<td>" + btnResendNotif + badgeConfirmStatus + "</td>" +
									"<td>" + btnDeleteSchedule + "</td>" +
									"</tr>";
							}
							idxReservation++;
						});

					} else {
						if (firstRsv == "") {
							firstRsv = "<td></td><td></td><td></td>";
						}
					}

					rows += "<tr class='trDriverListTab' data-detailDriver='" + dataDetailDriver + "' data-arrReservation='" + array.ARRRESERVATION + "' data-statusConfirm='" + statusConfirmFirst + "'>" +
						"<td rowspan='" + rowSpan + "'>" + btnEdit + "</td>" +
						"<td rowspan='" + rowSpan + "' align='right'><b>" + array.RANKNUMBER + "</b></td>" +
						"<td rowspan='" + rowSpan + "'><b>" + array.NAME + "</b><br/><small>[" + array.CARCAPACITYNAME + "]</small></td>" +
						firstRsv +
						"</tr>" + reservationRow;

				});
			}

			$tableBody.html(rows);
			$('[data-toggle="tooltip"]').tooltip();
			if ($('#driverListTab-searchKeyword').val() !== '' || $('#driverListTab-confirmationStatus').val() !== '') filterDataDriverListTab();

			$('#driverListTab-confirmationStatus').off('change');
			$('#driverListTab-confirmationStatus').on('change', function (e) {
				filterDataDriverListTab();
			});

			$('#driverListTab-searchKeyword').off('keydown');
			$('#driverListTab-searchKeyword').on('keydown', function (e) {
				if (e.which === 13) {
					e.preventDefault();
					filterDataDriverListTab();
				}
			});

			if (idReservationDetails != false) {
				var elementID = "btnShowDetailReservation" + idReservationDetails;
				if ($("#" + elementID).length > 0) {
					jumpFocusToElement(elementID);
					showModalDetailReservation(0, idReservationDetails);
				}
			}
		}
	});
}

function filterDataDriverListTab() {
	var confirmationStatus = $('#driverListTab-confirmationStatus').val(),
		keyword = $('#driverListTab-searchKeyword').val().toLowerCase();
	$('.trDriverListTab').each(function () {
		var detailDriver = $(this).attr('data-detailDriver').toLowerCase(),
			arrReservation = $(this).attr('data-arrReservation').toLowerCase(),
			statusConfirmRowSchedule = $(this).attr('data-statusConfirm').toLowerCase(),
			conConfirmationStatus = confirmationStatus == '' ? true : statusConfirmRowSchedule == confirmationStatus;

		if ((detailDriver.includes(keyword) || arrReservation.includes(keyword)) && conConfirmationStatus) {
			$(this).removeClass('d-none');
		} else {
			$(this).addClass('d-none');
		}
	});
}

function getDataReservationSchedule(callbackFunc = null) {

	var scheduleDate = $('#scheduleDate').val(),
		idDriver = $('#optionDriverFilter').val(),
		searchKeyword = $('#searchKeyword').val(),
		dataSend = { scheduleDate: scheduleDate, idDriver: idDriver, searchKeyword: searchKeyword };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDataReservationSchedule",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$(".reservationTableElement").remove();
			$("#noDataTableReservationSchedule").addClass("d-none");
			$("#tableReservationSchedule").append("<center id='spinnerLoadData'><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success: function (response) {
			NProgress.done();
			setUserToken(response);
			$("#spinnerLoadData").remove();

			var data = response.result,
				rows = "";

			if (response.status != 200) {
				$("#noDataTableReservationSchedule").removeClass("d-none");
			} else {

				var elemTableReservation = "";
				$.each(response.result, function (index, array) {

					var badgeProductType = '<span class="badge badge-primary" style="padding-top:6px; padding-bottom:6px;">' + array.PRODUCTTYPE + '</span>';
					var badgeDriverType = '<span class="badge badge-outline badge-primary">' + array.DRIVERTYPE + '</span>';
					var badgeStatusProcess = '<span class="badge badge-' + arrBadgeType[array.STATUSPROCESS] + '">' + array.STATUSPROCESSNAME + '</span>';
					var btnAddDriver = badgeConfirmStatus = btnRemoveDriver = btnResendNotif = strDriverName = "";
					var queryString = array.CUSTOMERNAME + " " + array.RESERVATIONTITLE + " " + array.PRODUCTNAME + " " + array.PICKUPLOCATION + " " + array.REMARK + " " + array.NOTES + " " + array.SPECIALREQUEST;

					switch (array.IDPRODUCTTYPE) {
						case "1": badgeProductType = '<span class="badge badge-primary" style="padding-top:6px; padding-bottom:6px;">' + array.PRODUCTTYPE + '</span>'; break;
						case "2": badgeProductType = '<span class="badge badge-success" style="padding-top:6px; padding-bottom:6px;">' + array.PRODUCTTYPE + '</span>'; break;
						default: badgeProductType = '<span class="badge badge-primary" style="padding-top:6px; padding-bottom:6px;">' + array.PRODUCTTYPE + '</span>'; break;
					}

					switch (array.STATUSCONFIRM) {
						case "0": badgeConfirmStatus = '<span class="badge badge-primary mr-5" style="padding:5px;font-size: 14px;" data-toggle="tooltip" data-placement="top" data-original-title="Scheduled, Unconfirmed"><i class="fa fa-clock-o"></i></span>'; break;
						case "1": badgeConfirmStatus = '<span class="badge badge-success mr-5" style="padding:5px;font-size: 14px;" data-toggle="tooltip" data-placement="top" data-original-title="Scheduled, Confirmed at ' + array.DATETIMECONFIRM + '"><i class="fa fa-check"></i></span>'; break;
						default: badgeConfirmStatus = '<span class="badge badge-warning mr-5" style="padding:5px;font-size: 14px;" data-toggle="tooltip" data-placement="top" data-original-title="Unscheduled, Unconfirmed"><i class="fa fa-circle-o-notch"></i></span>'; break;
					}

					var rsvText = '[' + array.RESERVATIONTIMESTART + '] ' + array.PRODUCTNAME + ' - ' + array.CUSTOMERNAME,
						btnInfo = "<button class='button button-xs button-box button-info mb-0 mr-5' onclick='showModalDetailReservation(0, " + array.IDRESERVATIONDETAILS + ")'><i class='fa fa-info'></i></button>",
						detailDriver = array.DRIVERNAMEDETAIL == '' && array.DRIVERPHONENUMBER == '' ? '-' : array.DRIVERNAMEDETAIL + ' (' + array.DRIVERPHONENUMBER + ')',
						detailCar = array.CARBRANDMODEL == '' && array.CARNUMBERPLATE == '' ? '-' : array.CARBRANDMODEL + ' [' + array.CARNUMBERPLATE + ']';

					if (array.IDDRIVER == 0) {
						btnAddDriver = pmsAddDriverSchedule == '1' ?
							'<button class="button button-xs button-box button-primary mb-0 pull-right addDriverBtn" data-idreservationdetails="' + array.IDRESERVATIONDETAILS + '" onclick="showModalDriverPicker(' + array.IDDRIVERTYPE + ', ' + array.IDRESERVATIONDETAILS + ', \'' + rsvText + '\', \'' + array.RESERVATIONTITLE + '\', \'' + array.DRIVERTYPE + '\')">' +
							'<i class="fa fa-user-plus"></i>' +
							'</button>'
							: '';
					} else {
						strDriverName = '<b class="pull-right">[' + array.DRIVERNAME + ']</b>';
						btnRemoveDriver = pmsDeleteDriverSchedule == '1' ?
							'<button class="button button-xs button-box button-warning mb-0 pull-right ml-5" onclick="confirmDeleteDriverSchedule(' + array.IDSCHEDULEDRIVER + ', \'' + rsvText + '\', \'' + array.DRIVERNAME + '\')">' +
							'<i class="fa fa-trash"></i>' +
							'</button>'
							: '';

						if (response.allowDeleteSchedule == true && array.STATUSPROCESS < 3) {
							if (array.STATUSCONFIRM == 0) {
								var reservationText = '[' + array.RESERVATIONTIMESTART + '] ' + array.RESERVATIONTITLE + ' - ' + array.CUSTOMERNAME;
								btnResendNotif = '<button class="button button-xs button-box button-warning mb-0 pull-right ml-5" data-toggle="tooltip" data-placement="top" data-original-title="Resend schedule notitication to driver" onclick="confirmResendNotification(' + array.IDSCHEDULEDRIVER + ', \'' + array.DRIVERNAME + '\', \'' + reservationText + '\')"><i class="fa fa-bullhorn"></i></button>';
							}
						}
					}

					elemTableReservation += '<div class="col-sm-12 pb-1 mb-5 rounded-lg reservationTableElement" data-queryString="' + queryString.replace(/\n/g, ' ') + '">' +
						'<div class="row pt-10 pb-1">' +
						'<div class="col-lg-2 col-sm-4">' +
						'[' + array.RESERVATIONTIMESTART + ']' + ' ' + badgeDriverType +
						'</div>' +
						'<div class="col-lg-10 col-sm-8">' +
						'<p class="font-weight-bold">' +
						badgeConfirmStatus + btnInfo + badgeProductType + ' ' + array.CUSTOMERNAME + '<br/>' +
						array.RESERVATIONTITLE + ' (' + array.PRODUCTNAME + ')' +
						btnAddDriver +
						btnRemoveDriver +
						btnResendNotif +
						strDriverName +
						'</p>' +
						'</div>' +
						'<div class="col-lg-2 col-sm-4">' +
						badgeStatusProcess + '<br/><br/>' +
						detailDriver + '<br/>' +
						detailCar +
						'</div>' +
						'<div class="col-lg-10 col-sm-8">' +
						'<small>Hotel/Pick Up : ' + array.HOTELNAME + ' / ' + array.PICKUPLOCATION + '</small><br/>' +
						'<small>Pax [Adult | Child | Infant] : ' + array.NUMBEROFADULT + ' | ' + array.NUMBEROFCHILD + ' | ' + array.NUMBEROFINFANT + '</small><br/>' +
						'<small>Notes | Remark : ' + array.NOTES + ' | ' + array.REMARK + '</small><br/>' +
						'<small>Special Request : ' + array.SPECIALREQUEST + '</small><br/>' +
						'<small>Fee/Cost : Rp. ' + numberFormat(array.NOMINAL) + ' <i class="fa fa-pencil" onclick="editReservationDetails(' + array.IDRESERVATIONDETAILS + ', ' + array.NOMINAL + ', \'' + array.PRODUCTNAME + '\', \'' + escape(array.NOTES.replace(/['"]+/g, '')) + '\')"></i></small>' +
						'</div>' +
						'</div>' +
						'</div>';
				});

				$("#tableReservationSchedule").append(elemTableReservation);
				$('[data-toggle="tooltip"]').tooltip();
				if (typeof callbackFunc == "function") callbackFunc();

			}

		}

	});

}

function getDataDriverCalendar() {

	var $tableBody = $('#table-driverScheduleCalendar > tbody'),
		columnNumber = 1,
		scheduleDate = $('#scheduleDate').val(),
		dataSend = { scheduleDate: scheduleDate };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDataDriverCalendar",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$(".thHeaderDates").remove();
			$tableBody.html("<tr><td colspan='" + columnNumber + "'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success: function (response) {
			NProgress.done();
			setUserToken(response);

			var arrDates = response.arrDates,
				rows = thHeaderDates = "";
			columnNumber = columnNumber + arrDates.length;

			for (var iDates = 0; iDates < arrDates.length; iDates++) {
				thHeaderDates += '<th width="75" class="thHeaderDates text-center">' + arrDates[iDates] + '</th>';
			}

			$("#headerDates").append(thHeaderDates);

			if (response.status != 200) {
				rows = "<tr>" +
					"<td colspan='" + columnNumber + "' align='center'><center>No data found</center></td>" +
					"</tr>";
			} else {

				var data = response.dataDriver;
				$.each(data, function (index, array) {

					var driverDetail = array.DRIVERNAME + " [" + array.DRIVERTYPE + "]";
					var btnAdd = pmsAddDriverSchedule == '1' ?
						'<button class="button button-xs button-box button-primary mb-0" onclick="showModalListReservationList(' + array.IDDRIVERTYPE + ', \'' + array.DRIVERTYPE + '\', \'' + array.DRIVERNAME + '\', ' + array.IDDRIVER + ', ' + true + ')">' +
						'<i class="fa fa-plus"></i>' +
						'</button>'
						: '';
					var classStrong = "";
					switch (array.PARTNERSHIPTYPE) {
						case "1": switch (array.IDDRIVERTYPE) {
							case "1": classStrong = "text-warning"; break;
							case "2": classStrong = "text-primary"; break;
							case "3":
							default: classStrong = "text-info"; break;
						}
							break;
						case "2": classStrong = "text-info";
							break;
						case "3": classStrong = "text-success";
							break;
						case "4": classStrong = "text-dark";
							break;
					}

					rows += "<tr class='trCalendarDriverList' data-driverName='" + array.DRIVERNAME + "'>" +
						"<td style='white-space: nowrap;'>" +
						btnAdd + " <strong class='" + classStrong + "'>[" + array.RANKNUMBER + "]</strong> <b>" + array.DRIVERNAME + "</b>" +
						"</td>";

					var currentIdReservation = 0,
						idxBadge = 1,
						badgeName = arrBadgeType[idxBadge];

					$.each(array.DATASCHEDULE, function (iSchedule, arrSchedule) {
						var btnSchedule = "";
						if (arrSchedule.length > 0) {
							for (var iChildSch = 0; iChildSch < arrSchedule.length; iChildSch++) {
								if (arrSchedule[iChildSch][0] != 0) {
									if (currentIdReservation != 0 && currentIdReservation !== arrSchedule[iChildSch][2]) {
										if (idxBadge == 3) {
											idxBadge = 1;
										} else {
											idxBadge++;
										}
										badgeName = arrBadgeType[idxBadge];
									}
									btnSchedule += '<button class="button button-xs btn-block button-' + badgeName + ' px-1" onclick="showModalDetailReservation(' + arrSchedule[iChildSch][0] + ', 0)">' + arrSchedule[iChildSch][1] + '</button><br/>';
									currentIdReservation = arrSchedule[iChildSch][2] * 1;
								} else {
									btnSchedule += '<button class="button button-xs btn-block button-danger px-1" onclick="showModalDetailDayOff(' + arrSchedule[iChildSch][2] + ')">' + arrSchedule[iChildSch][1] + '</button><br/>';
									currentIdReservation = 0;
								}
							}
						}

						rows += '<td>' + btnSchedule + '</td>';
					});

					rows += "</tr>";

				});
			}
			$tableBody.html(rows);

			$('#calendarTab-searchKeyword').off('keyup');
			$('#calendarTab-searchKeyword').on('keyup', function (e) {
				if (e.which === 13) {
					e.preventDefault();
					var keyword = $(this).val().toLowerCase();
					$('.trCalendarDriverList').each(function () {
						var driverName = $(this).attr('data-driverName').toLowerCase();

						if (driverName.includes(keyword)) {
							$(this).removeClass('d-none');
						} else {
							$(this).addClass('d-none');
						}
					});
				}
			});
		}
	});
}

function getDataDayOffRequest(idDayOffRequest = false) {
	var scheduleDate = $('#scheduleDate').val(),
		dayOffDate = $('#dayOffRequestTab-dayOffDate').val(),
		dayOffStatus = $('#dayOffRequestTab-optionStatus').val(),
		searchKeyword = $('#dayOffRequestTab-searchKeyword').val(),
		dataSend = { scheduleDate: scheduleDate, dayOffDate: dayOffDate, dayOffStatus: dayOffStatus, searchKeyword: searchKeyword, idDayOffRequest: idDayOffRequest };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDataDayOffRequest",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$(".dayOffTableElement").remove();
			$("#noDataTableDayOffRequest").addClass("d-none");
			$("#tableDayOffRequest").append("<div id='spinnerLoadData' class='col-sm-12 text-center py-10'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></div>");
		},
		success: function (response) {
			NProgress.done();
			setUserToken(response);
			$("#spinnerLoadData").remove();

			var data = response.result,
				rows = "";

			if (response.status != 200) {
				$("#noDataTableDayOffRequest").removeClass("d-none");
			} else {

				var elemTableDayOffRequest = "";
				$.each(response.result, function (index, array) {

					var badgeDriverType = '<span class="badge badge-outline badge-primary">' + array.DRIVERTYPE + '</span>';
					var btnApprove = btnReject = badgeApproveStatus = "",
						dayOffReason = escape(array.REASON.replace(/['"]+/g, '')),
						queryString = array.DRIVERNAME + " " + array.REASON + " " + array.USERAPPROVAL,
						badgeDayOffQuotaExceed = array.DAYOFFQUOTAEXCEED == 1 ? '<span class="badge badge-warning">Day off quota on the date exceeded</span>' : '',
						badgeDriverQuotaExceed = array.DRIVERLIMITEXCEED == 1 ? '<span class="badge badge-warning">Driver day off quota exceeded</span>' : '';

					if (array.STATUS == 0) {
						btnApprove = '<button class="button button-xs button-box button-primary mb-0 pull-right ml-5" onclick="confirmApprovalDayOff(' + array.IDDAYOFFREQUEST + ', 1, \'' + array.DRIVERNAME + '\', \'' + array.DATEDAYOFF + '\', \'' + dayOffReason + '\')">' +
							'<i class="fa fa-check"></i>' +
							'</button>';
						btnReject = '<button class="button button-xs button-box button-warning mb-0 pull-right" onclick="confirmApprovalDayOff(' + array.IDDAYOFFREQUEST + ', -1, \'' + array.DRIVERNAME + '\', \'' + array.DATEDAYOFF + '\', \'' + dayOffReason + '\')">' +
							'<i class="fa fa-times"></i>' +
							'</button>';
					}

					switch (array.STATUS) {
						case "0": badgeApproveStatus = '<span class="badge badge-primary mr-5">Waiting Approval</span>'; break;
						case "1": badgeApproveStatus = '<span class="badge badge-success mr-5">Approved</span>'; break;
						case "-1": badgeApproveStatus = '<span class="badge badge-warning mr-5">Rejected</span>'; break;
						case "-2": badgeApproveStatus = '<span class="badge badge-danger mr-5">Deleted</span>'; break;
						default: badgeApproveStatus = '<span class="badge badge-info mr-5">-</span>'; break;
					}

					elemTableDayOffRequest += '<div class="col-sm-12 pb-1 mb-5 rounded-lg dayOffTableElement" data-queryString="' + queryString + '">\
						<div class="row pt-10 pb-1">\
						<div class="col-lg-2 col-sm-4">' +
						badgeDriverType +
						' <b>' + array.DRIVERNAME + '</b> ' +
						'</div>' +
						'<div class="col-lg-10 col-sm-8">' +
						'<p class="font-weight-bold">' +
						badgeApproveStatus + '<br/>' +
						'Day off reason : ' + array.REASON +
						btnApprove +
						btnReject +
						'</p>' +
						'</div>' +
						'<div class="col-lg-2 col-sm-4">' +
						array.DATEDAYOFF +
						'</div>' +
						'<div class="col-lg-10 col-sm-8">' +
						'<small>Requested At : ' + array.DATETIMEINPUT + '</small><br/>' +
						'<small>Aprrove By : ' + array.USERAPPROVAL + ' [' + array.DATETIMEAPPROVAL + ']</small><br/>' +
						badgeDayOffQuotaExceed + ' ' + badgeDriverQuotaExceed +
						'</div>' +
						'</div>' +
						'</div>';
				});

				$("#tableDayOffRequest").append(elemTableDayOffRequest);
				$('#dayOffRequestTab-searchKeyword').off('keyup');
				$('#dayOffRequestTab-searchKeyword').on('keyup', function (e) {
					if (e.which === 13) {
						e.preventDefault();
						var keyword = $(this).val().toLowerCase();
						$('.dayOffTableElement').each(function () {
							var queryString = $(this).attr('data-queryString').toLowerCase();

							if (queryString.includes(keyword)) {
								$(this).removeClass('d-none');
							} else {
								$(this).addClass('d-none');
							}
						});
					}
				});
			}
		}
	});
}

$('#dayOffRequestTab-dayOffDate').off('change');
$('#dayOffRequestTab-dayOffDate').on('change', function (e) {
	onChangeDateInputFilter($(this));
	getDataDayOffRequest();
});

$('#dayOffRequestTab-optionStatus').off('change');
$('#dayOffRequestTab-optionStatus').on('change', function (e) {
	getDataDayOffRequest();
});

function showModalListReservationList(idDriverType, driverType, driverName, idDriver, monthly = false) {
	var scheduleDate = $('#scheduleDate').val();
	var dataSend = { monthly: monthly, scheduleDate: scheduleDate, idDriver: idDriver, idDriverType: idDriverType };
	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDataReservationList",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$(".reservationListElement").remove();
		},
		success: function (response) {

			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);

			if (response.status != 200) {
				$("#saveReservationToDriver").addClass("d-none");
				$("#noDataReservationList").removeClass("d-none");
				$("#containerReservationList").removeClass("scrollList");
			} else {

				var periodStr = "Date : " + response.scheduleDateStr;
				if (monthly == true) {
					periodStr = "Period : " + response.scheduleMonthStr;
				}

				var elemListReservation = "";
				$.each(response.result, function (index, array) {

					var disabledCheckbox = array.IDDAYOFF == 0 ? "" : "disabled";
					var dayOffElem = array.IDDAYOFF == 0 ? "" : '<div class="alert alert-danger py-1 px-2 mb-0" role="alert"><i class="fa fa-exclamation-triangle"></i> Day Off : ' + array.REASON + '</div>';
					var badgeProductType = '<span class="badge badge-primary">' + array.PRODUCTTYPE + '</span>';
					var queryString = array.CUSTOMERNAME + " " + array.DRIVERTYPE + " " + array.PRODUCTNAME + " " + array.HOTELNAME + " " + array.PICKUPLOCATION + " " + array.REMARK + " " + array.NOTES;

					switch (array.IDPRODUCTTYPE) {
						case "1": badgeProductType = '<span class="badge badge-primary">' + array.PRODUCTTYPE + '</span>'; break;
						case "2": badgeProductType = '<span class="badge badge-success">' + array.PRODUCTTYPE + '</span>'; break;
						default: badgeProductType = '<span class="badge badge-primary">' + array.PRODUCTTYPE + '</span>'; break;
					}

					elemListReservation += '<div class="col-sm-12 pb-1 mb-5 rounded-lg reservationListElement" data-queryString="' + queryString.replace(/\n/g, ' ') + '">' +
						'<div class="row pt-10 pb-1">' +
						'<div class="col-lg-2 col-sm-4 text-center">' +
						'<div class="adomx-checkbox-radio-group mx-auto mb-5">' +
						'<label class="adomx-checkbox mx-auto">' +
						'<input type="checkbox" id="idReservationDetails" class="cbReservationProduct" value="' + array.IDRESERVATIONDETAILS + '" ' + disabledCheckbox + '> <i class="icon"></i>' +
						'</label>' +
						'</div>' +
						array.SCHEDULEDATE + "<br/>" +
						'[' + array.RESERVATIONTIMESTART + ']' +
						'</div>' +
						'<div class="col-lg-10 col-sm-8">' +
						dayOffElem +
						'<p class="font-weight-bold">' +
						array.BOOKINGCODE + ' - ' + array.CUSTOMERNAME + '<br/>' +
						'[' + array.DRIVERTYPE + '] ' + array.PRODUCTNAME + '<br/>' +
						'<small>Hotel/Pick Up : ' + array.HOTELNAME + ' / ' + array.PICKUPLOCATION + '</small><br/>' +
						'<small>Pax [Adult | Child | Infant] : ' + array.NUMBEROFADULT + ' | ' + array.NUMBEROFCHILD + ' | ' + array.NUMBEROFINFANT + '</small><br/>' +
						'<small>Notes | Remark : ' + array.NOTES + ' | ' + array.REMARK + '</small>' +
						'</p>' +
						'</div>' +
						'</div>' +
						'</div>';
				});

				$("#containerReservationList").addClass("scrollList").append(elemListReservation);
				$("#saveReservationToDriver").removeClass("d-none");
				$("#noDataReservationList").addClass("d-none");
			}

			$("#listReservationScheduleDriverName").html(driverName + " [" + driverType + "]");
			$("#listReservationScheduleDate").html(periodStr);
			$("#reservationListIdDriver").val(idDriver);
			$('#modalReservationList-searchKeyword').off('keydown');
			$('#modalReservationList-searchKeyword').on('keydown', function (e) {
				if (e.which === 13) {
					e.preventDefault();
					var keyword = $(this).val().toLowerCase();
					$('.reservationListElement').each(function () {
						var queryString = $(this).attr('data-queryString').toLowerCase();

						if (queryString.includes(keyword)) {
							$(this).removeClass('d-none');
						} else {
							$(this).addClass('d-none');
						}
					});
				}
			});
			$('#modal-reservationList').modal('show');
		}
	});
}

function showModalDriverPicker(idDriverType, idReservationDetails, rsvText, rsvTitle, strDriverType) {

	var scheduleDate = $('#scheduleDate').val();
	var dataSend = { scheduleDate: scheduleDate, idDriverType: idDriverType, idReservationDetails: idReservationDetails };
	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDataDriverList",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$(".driverListElement").remove();
		},
		success: function (response) {

			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);

			$("#containerReservationDetail").html('<div class="order-details-customer-info">' +
				'<ul class="ml-5">' +
				'<li> <span>Driver Type</span> <span>' + strDriverType + '</span> </li>' +
				'<li> <span>Date</span> <span>' + response.scheduleDateStr + '</span> </li>' +
				'<li> <span>Details</span> <span>' + rsvTitle + '</span> </li>' +
				'</ul>' +
				'</div>');

			if (response.status != 200) {
				$("#saveDriverToReservation").addClass("d-none");
				$("#containerDriverList").removeClass("scrollList").html("<tr><td colspan='2'>No driver data found</td><tr>");
			} else {
				var elemListDriver = "";
				$.each(response.result, function (index, array) {

					var radioElem = array.IDDAYOFF == 0 ?
						"<label class='adomx-radio-2 pull-right'>" +
						"<input type='radio' name='radioDriverList' id='radioDriverList" + array.IDDRIVER + "' value='" + array.IDDRIVER + "'> <i class='icon'></i>" +
						"</label>"
						: "";
					var dayOffElem = array.IDDAYOFF == 0 ? "" :
						'<div class="alert alert-danger py-2 px-2" role="alert">' +
						'<i class="fa fa-exclamation-triangle"></i> Day Off : ' + array.REASON +
						'</div>';

					var badgePartnershipType = "";
					switch (array.PARTNERSHIPTYPE) {
						case "1": badgePartnershipType = '<span class="badge badge-primary pull-right">Internal</span>'; break;
						case "2": badgePartnershipType = '<span class="badge badge-info pull-right">Freelance</span>'; break;
						case "3": badgePartnershipType = '<span class="badge badge-warning pull-right">Team</span>'; break;
						case "4": badgePartnershipType = '<span class="badge badge-dark pull-right">Office</span>'; break;
						default: badgePartnershipType = ''; break;
					}

					elemListDriver += "<tr class='driverListElement' data-driverName='" + array.DRIVERNAME + "'>" +
						"<td>[#" + array.RANKNUMBER + "] [" + array.DRIVERTYPE + "] <b>" + array.DRIVERNAME + " " + badgePartnershipType + "</b><br/><small class='mt-1'>Total schedule on the selected date : " + array.TOTALSCHEDULE + "</small><br/>" + dayOffElem + "</td>" +
						"<td width='10' align='center'>" + radioElem + "</td>" +
						"</tr>";
				});

				$("#containerDriverList").addClass("scrollList").html(elemListDriver);
				$("#saveReservationToDriver").removeClass("d-none");

				$('#searchKeywordDriver').off('keyup');
				$('#searchKeywordDriver').on('keyup', function (e) {
					e.preventDefault();
					var keyword = $(this).val().toLowerCase();
					$(".driverListElement").each(function (index) {
						var driverNameTr = $(this).attr("data-driverName").toLowerCase();
						$(this).removeClass("d-none");
						if (keyword != "" && driverNameTr.indexOf(keyword) < 0) $(this).addClass("d-none");
					});
				});
			}

			$("#idReservationDetails").val(idReservationDetails);
			$('#modal-driverList').modal('show');

		}

	});

}

$('#content-reservationList').off('submit');
$('#content-reservationList').on('submit', function (e) {

	e.preventDefault();
	var arrIDReservationDetails = [];
	$('input:checkbox.cbReservationProduct').each(function () {
		var checkboxVal = (this.checked ? $(this).val() : false);

		if (checkboxVal) {
			arrIDReservationDetails.push(checkboxVal);
		}
	});
	var idDriver = $("#reservationListIdDriver").val(),
		dataSend = { idDriver: idDriver, arrIDReservationDetails: arrIDReservationDetails };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/saveDriverSchedule",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			$("#content-reservationList :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success: function (response) {

			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-reservationList :input").attr("disabled", false);

			$('#modalWarning').on('show.bs.modal', function () {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if (response.status == 200) {
				$('#modal-reservationList').modal('hide');
				getDataDriverSchedule();
				getDataReservationSchedule();
				getDataDriverCalendar();
			}

		}
	});
});

$('#content-driverList').off('submit');
$('#content-driverList').on('submit', function (e) {

	e.preventDefault();
	var arrIDReservationDetails = [$("#idReservationDetails").val()];
	var idDriver = $("input[name=radioDriverList]:checked").val(),
		dataSend = { idDriver: idDriver, arrIDReservationDetails: arrIDReservationDetails };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/saveDriverSchedule",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			$("#content-driverList :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success: function (response) {

			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-driverList :input").attr("disabled", false);

			$('#modalWarning').on('show.bs.modal', function () {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if (response.status == 200) {
				$('#modal-driverList').modal('hide');
				getDataDriverSchedule();
				getDataReservationSchedule();
				getDataDriverCalendar();
			}

		}
	});
});

function confirmDeleteDriverSchedule(idDriverSchedule, reservationText, driverName) {

	var confirmText = 'Driver reservation schedule will be deleted. Details ;<br/><br/>Driver : <br/><b>' + driverName + '</b><br/>Reservation : <br/><b>' + reservationText + '</b>.<br/><br/>Are you sure?';

	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', idDriverSchedule).attr('data-function', "deleteDriverSchedule");
	$confirmationDialog.modal('show');

}

function confirmApprovalDayOff(idDayoffRequest, status, driverName, dateDayOff, reason) {

	var statusApproval = status == 1 ? "approved" : "rejected";
	var functionUrl = status == 1 ? "approveDayOffRequest" : "rejectDayOffRequest";
	var confirmText = 'Driver day off request will be <b>' + statusApproval + '</b>. Details ;<br/><br/>Driver : <br/><b>' + driverName + '</b><br/>Date : <br/><b>' + dateDayOff + '</b><br/>Reason : <br/><b>' + unescape(reason) + '</b>.<br/><br/>Are you sure?';

	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', idDayoffRequest).attr('data-function', functionUrl);
	$confirmationDialog.modal('show');

}

function confirmDeleteDayOff(idDayoff, driverName, dateDayOff, reason) {

	$('#modal-detailDayOff').modal('hide');
	var confirmText = 'Driver day off data will be <b>deleted</b>. Details ;<br/><br/>Driver : <br/><b>' + driverName + '</b><br/>Date : <br/><b>' + dateDayOff + '</b><br/>Reason : <br/><b>' + unescape(reason) + '</b>.<br/><br/>Are you sure?';

	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', idDayoff).attr('data-function', 'deleteDayOff');
	$confirmationDialog.modal('show');

}

function confirmResendNotification(idDriverSchedule, driverName, reservationText) {

	var confirmText = 'You will resend notification to driver : <b>' + driverName + '</b><br/>Schedule : <b>' + reservationText + '</b>.<br/><br/>Are you sure?';

	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', idDriverSchedule).attr('data-function', 'resendScheduleNotification');
	$confirmationDialog.modal('show');

}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function (e) {

	var idData = $confirmationDialog.find('#confirmBtn').attr('data-idData'),
		params = $confirmationDialog.find('#confirmBtn').attr('data-params'),
		funcName = $confirmationDialog.find('#confirmBtn').attr('data-function'),
		dataSend = { idData: idData, params: params };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/" + funcName,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$confirmationDialog.modal('hide');
			$('#window-loader').modal('show');
		},
		success: function (response) {
			$('#window-loader').modal('hide');
			setUserToken(response);
			NProgress.done();

			$('#modalWarning').on('show.bs.modal', function () {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if (response.status == 200) {
				getDataDriverSchedule();
				getDataReservationSchedule();
				getDataDriverCalendar();
				getDataDayOffRequest();

				if (funcName == "saveDriverDayOff") {
					$("#dayOffReason").val("");
				}
			} else {
				if (funcName == "saveDriverDayOff") {
					$("#editor-modal-inputDayOff").modal("show");
				}
			}
		}
	});
});

function showModalDetailReservation(idDriverSchedule = 0, idReservationDetails = 0) {
	var dataSend = { idDriverSchedule: idDriverSchedule, idReservationDetails: idReservationDetails };
	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDetailReservation",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$("#detailReservation-source, #detailReservation-title, #detailReservation-date, #detailReservation-time, #detailReservation-customerName").html("-");
			$("#detailReservation-customerContact, #detailReservation-customerEmail, #detailReservation-hotelName, #detailReservation-pickUpLocation").html("-");
			$("#detailReservation-dropOffLocation, #detailReservation-tourPlan, #detailReservation-remark").html("-");
			$("#detailReservation-numberOfAdult, #detailReservation-numberOfChild, #detailReservation-numberOfInfant").html("0");
			$("#detailReservation-scheduleActivity").html("");
		},
		success: function (response) {
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);

			if (response.status == 200) {
				var detailData = response.detailData,
					listScheduleActivity = response.listScheduleActivity,
					detailDriver = detailData.DRIVERNAME == '' && detailData.DRIVERPHONENUMBER == '' ? '-' : detailData.DRIVERNAME + ' (' + detailData.DRIVERPHONENUMBER + ')',
					detailCar = detailData.CARBRANDMODEL == '' && detailData.CARNUMBERPLATE == '' ? '-' : detailData.CARBRANDMODEL + ' [' + detailData.CARNUMBERPLATE + ']';

				$("#detailReservation-source").html(detailData.SOURCENAME);
				$("#detailReservation-bookingCode").html(detailData.BOOKINGCODE);
				$("#detailReservation-title").html(detailData.RESERVATIONTITLE);
				$("#detailReservation-date").html(detailData.SCHEDULEDATE);
				$("#detailReservation-time").html(detailData.RESERVATIONTIMESTART);
				$("#detailReservation-customerName").html(detailData.CUSTOMERNAME);
				$("#detailReservation-customerContact").html(detailData.CUSTOMERCONTACT);
				$("#detailReservation-customerEmail").html(detailData.CUSTOMEREMAIL);
				$("#detailReservation-detailDriver").html(detailDriver);
				$("#detailReservation-detailCar").html(detailCar);
				$("#detailReservation-numberOfAdult").html(detailData.NUMBEROFADULT);
				$("#detailReservation-numberOfChild").html(detailData.NUMBEROFCHILD);
				$("#detailReservation-numberOfInfant").html(detailData.NUMBEROFINFANT);
				$("#detailReservation-hotelName").html(detailData.HOTELNAME == "" ? "-" : detailData.HOTELNAME);
				$("#detailReservation-pickUpLocation").html(detailData.PICKUPLOCATION == "" ? "-" : detailData.PICKUPLOCATION);
				$("#detailReservation-dropOffLocation").html(detailData.DROPOFFLOCATION == "" ? "-" : detailData.DROPOFFLOCATION);
				$("#detailReservation-tourPlan").html(detailData.TOURPLAN == "" ? "-" : detailData.TOURPLAN);
				$("#detailReservation-remark").html(detailData.REMARK == "" ? "-" : detailData.REMARK);

				if (listScheduleActivity.length > 0) {
					var liScheduleActivity = '';
					$.each(listScheduleActivity, function (index, array) {
						var badgeConfirmation = '';
						switch (array.STATUSCONFIRM) {
							case 0:
							case "0": badgeConfirmation = '<i class="badge badge-info" style="width: 80px;">Unconfirmed</i>'; break;
							case 1:
							case "1": badgeConfirmation = '<i class="badge badge-success" style="width: 80px;">Confirmed</i>'; break;
							default: badgeConfirmation = '<i class="badge badge-dark" style="width: 80px;">Unavailable</i>'; break;
						}
						liScheduleActivity += '<li> <span>' + array.VENDORNAME + '</span> <span>' + badgeConfirmation + ' ' + array.TIMESCHEDULE + '</span> </li>';
					});
					$("#detailReservation-scheduleActivity").html(liScheduleActivity);
				} else {
					liScheduleActivity = "-";
				}

				$('#modal-detailReservation').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function () {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

function showModalDetailDayOff(idDayoff = 0) {

	var dataSend = { idDayoff: idDayoff };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/getDetailDayOff",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			NProgress.set(0.4);
			$('#window-loader').modal('show');
			$('.btnDeleteDayOffModal').remove();
			$("#detailDayOff-drivertype, #detailDayOff-name, #detailDayOff-date, #detailDayOff-datetimeinput, #detailDayOff-reason").html("-");
		},
		success: function (response) {
			$('#window-loader').modal('hide');
			NProgress.done();
			setUserToken(response);

			if (response.status == 200) {
				var detailData = response.detailData;

				$("#detailDayOff-drivertype").html(detailData.DRIVERTYPE);
				$("#detailDayOff-name").html(detailData.NAME);
				$("#detailDayOff-date").html(detailData.DATEDAYOFF);
				$("#detailDayOff-datetimeinput").html(detailData.DATETIMEINPUT);
				$("#detailDayOff-reason").html(detailData.REASON);

				if (response.allowDeleteDayOff == true) {
					$("#content-detailDayOff > div.modal-footer").prepend('<button type="button" class="button button-danger pull-left btnDeleteDayOffModal" onclick="confirmDeleteDayOff(' + idDayoff + ', \'' + detailData.NAME + '\', \'' + detailData.DATEDAYOFF + '\', \'' + escape(detailData.REASON.replace(/['"]+/g, '')) + '\')">Delete Day Off</button>')
				}

				$('#modal-detailDayOff').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function () {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}

		}

	});

}

function editReservationDetails(idReservationDetails, nominal, productName, notes) {

	var notes = unescape(notes);
	$('#modal-reservationDetails').on('show.bs.modal', function () {
		$('#modal-reservationDetails #productName').val(productName);
		$('#modal-reservationDetails #nominalFee').val(numberFormat(nominal));
		$('#modal-reservationDetails #notesFee').val(notes);
		$('#modal-reservationDetails #idReservationDetailsFee').val(idReservationDetails);
	});
	$('#modal-reservationDetails').modal('show');

}

$('#content-reservationDetails').off('submit');
$('#content-reservationDetails').on('submit', function (e) {

	e.preventDefault();
	var productName = $('#modal-reservationDetails #productName').val();
	nominal = $('#modal-reservationDetails #nominalFee').val();
	notes = $('#modal-reservationDetails #notesFee').val();
	idReservationDetails = $('#modal-reservationDetails #idReservationDetailsFee').val();
	dataSend = { idReservationDetails: idReservationDetails, productName: productName, nominal: nominal, notes: notes };

	$.ajax({
		type: 'POST',
		url: baseURL + "schedule/driverSchedule/saveReservationDetailsFee",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend: function () {
			$("#content-reservationDetails :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success: function (response) {

			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#content-reservationDetails :input").attr("disabled", false);

			$('#modalWarning').on('show.bs.modal', function () {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if (response.status == 200) {
				$('#modal-reservationDetails').modal('hide');
				getDataReservationSchedule();
			}

		}
	});
});

$('#editor-inputDayOff').off('submit');
$('#editor-inputDayOff').on('submit', function (e) {

	e.preventDefault();
	var dateDayOff = $("#dayOffDate").val(),
		driverDayOff = $("#optionDayOffDriver").val(),
		driverDayOffStr = $("#optionDayOffDriver option:selected").text(),
		reasonDayOff = $("#dayOffReason").val();

	if (reasonDayOff == "" || reasonDayOff == null || reasonDayOff === undefined) {
		$('#modalWarning').on('show.bs.modal', function () {
			$('#modalWarningBody').html("Please enter the reason for the day off");
		});
		$('#modalWarning').modal('show');
	} else {

		$("#editor-modal-inputDayOff").modal("hide");
		var confirmText = 'Day off data will set to<br/><br/><div class="order-details-customer-info"><ul class="ml-5"><li> <span><b>Driver</b></span> <span>' + driverDayOffStr + '</span> </li><li> <span><b>Date</b></span> <span>' + dateDayOff + '</span> </li><li> <span><b>Reason</b></span> <span>' + reasonDayOff + '</span> </li></ul><br/>Are you sure?';

		$confirmationDialog.find('#modal-confirm-body').html(confirmText);
		$confirmationDialog.find('#confirmBtn').attr('data-idData', driverDayOff).attr('data-params', dateDayOff + "|" + escape(reasonDayOff.replace(/['"]+/g, ''))).attr('data-function', 'saveDriverDayOff');
		$confirmationDialog.modal('show');

	}

});

scheduleDriverFunc();