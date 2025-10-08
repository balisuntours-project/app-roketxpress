var $confirmDeleteDialog= $('#modal-confirm-action');
$.fn.raty.defaults.path = 'assets/img/rating/';

if (driverRatingPointFunc == null){
	var driverRatingPointFunc	=	function(){
		$(document).ready(function () {
			getDataTable();
			setOptionHelper('optionSource', 'dataSourceAutoRating');
			setOptionHelper('optionSourceInputAuto', 'dataSourceAutoRating');
			setOptionHelper('optionSourceRatingCalendar', 'dataSourceAutoRating');

			setOptionHelper('optionDriverType', 'dataDriverType');
			setOptionHelper('optionDriver', 'dataDriver');
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			getDataRatingCalendar();
		});
	}
}

$('#formDriverSearch').off('submit');
$('#formDriverSearch').on('submit', function(e) {
	e.preventDefault();
});

$('#searchKeyword').off('keypress');
$("#searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
        getDataTable();
    }
});

$('#btnCloseInputRating').off('click');
$('#btnCloseInputRating').on('click', function(e) {
	$("#btnOpenInputRating, #btnSettingRating").removeClass('d-none');
	$("#btnCloseInputRating, #btnInputAuto").addClass('d-none');
	$("#searchKeywordManual").val("");
	toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
});

function generateDataTable(page){
	getDataTable(page);
}
	
function getDataTable(page = 1){
	
	var $tableBody		=	$('#table-dataDriver > tbody'),
		columnNumber	=	$('#table-dataDriver > thead > tr > th').length,
		keyword			=	$('#searchKeyword').val(),
		dataSend		=	{page:page, keyword:keyword};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/getDataDriverRatingPoint",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Memuat data...</center></td></tr>");
		},
		success:function(response){
			
			NProgress.done();
			setUserToken(response);
			var rows		=	"",
				noDataElem	=	'<tr>'+
									'<td align="center">'+
										'<div class="row"><div class="col-12 text-center"><img src="'+ASSET_IMG_URL+'no-data.png" width="120px"><h5>No data</h5><p>'+response.msg+'</p></div></div>'+
									'</td>'+
								'</tr>';
			var data	=	response.result.data;
			
			if(data.length === 0){
				rows	=	noDataElem;
			} else {
				var arrPartnershipType		=	[],
					arrPartnershipTypeName	=	[],
					arrTypeDriver			=	[];
				$.each(data, function(index, array) {
					
					var	scheduleType	=	"",
						partnershipType	=	array.PARTNERSHIPTYPE,
						driverType		=	array.DRIVERTYPE,
						last30DaysPoint	=	(array.TOTALPOINT * 1) - (array.BASICPOINT * 1);
					switch(array.SCHEDULETYPE){
						case "1"	:	scheduleType	=	"Auto"; break;
						case "2"	:	scheduleType	=	"Manual"; break;
						default		:	scheduleType	=	"-"; break;
					}
					
					if(!arrPartnershipType.includes(partnershipType) || !arrTypeDriver.includes(driverType)){
						var partnershipTypeName	=	'-';
						
						switch(partnershipType){
							case "1"	:	
							case 1		:	partnershipTypeName	=	"Partner - "+driverType; break;
							case "2"	:	
							case 2		:	partnershipTypeName	=	"Freelance"; break;
							case "3"	:	
							case 3		:	partnershipTypeName	=	"Team"; break;
							case "4"	:	
							case 4		:	partnershipTypeName	=	"Office"; break;
						}
						
						if(!arrPartnershipTypeName.includes(partnershipTypeName)){
							rows	+=	'<tr>'+
											'<td><h4>'+partnershipTypeName+'</h4></td>'+
										'<tr>';
						}
						
						arrPartnershipType.push(partnershipType);
						arrPartnershipTypeName.push(partnershipTypeName);
						arrTypeDriver.push(driverType);
					}
					
					rows	+=	'<tr>'+
									'<td>'+
										'<div class="rowDriver rounded-lg row px-3 py-3 ml-2 mr-2 mb-1 bg-white">'+
											'<div class="col-lg-2 col-sm-4 text-center">'+
												'<div class="author-profile">'+
													'<div class="image" style="width: 80px;height: 80px;">'+
														'<h2>#'+array.RANKNUMBER+'</h2>'+
													'</div>'+
												'</div>'+
											'</div>'+
											'<div class="col-lg-10 col-sm-8">'+
												'<div class="row px-0 py-0">'+
													'<div class="col-lg-6 col-sm-12">'+
														'<p class="mb-0"><b>'+array.NAME+'</b></p>'+
														'<p class="mb-0">'+array.ADDRESS+'</p>'+
														'<p class="mb-0">'+array.PHONE+' | '+array.EMAIL+'</p><br/>'+
														'<span class="badge badge-pill mr-2 badge-primary"><p class="m-0 p-1 text-white">Driver Type : '+array.DRIVERTYPE+'</p></span>'+
														'<span class="badge badge-pill mr-2 badge-info"><p class="m-0 p-1 text-white">Schedule : '+scheduleType+'</p></span>'+
													'</div>'+
													'<div class="col-lg-3 col-sm-12">'+
														'<b>Last Input :</b><br/>'+
														'<i class="fa fa-star text-success"></i> '+array.RATING+' ('+array.POINT+' Points)<br/>'+
														'<i class="fa fa-calendar text-info"></i> '+array.DATERATINGPOINT+' ('+array.SOURCENAME+')<br/>'+
														'<small> '+array.DATETIMEINPUT+' by '+array.USERINPUT+'</small><br/>'+
														'<button type="button" class="button button-info button-xs py-0 mt-5" onclick="showPointHistory('+array.IDDRIVER+', \''+array.NAME+'\')"><span><i class="fa fa-history"></i> Point History</span></button>'+
													'</div>'+
													'<div class="col-lg-3 col-sm-12 text-right">'+
														'Basic Point <i class="fa fa-pencil" onclick="openFormBasicPoint('+array.IDDRIVER+', \''+array.NAME+'\', '+array.BASICPOINT+')"></i><br/>'+
														'<h4>'+numberFormat(array.BASICPOINT)+'</h4>'+
														'Total Point (last 30 days)<br/>'+
														'<h4>'+numberFormat(last30DaysPoint)+'</h4>'+
													'</div>'+
												'</div>'+
											'</div>'+
										'</div>'+
									'</td>'+
								'</tr>';
					
				});
			
			}

			generatePagination("tablePagination", page, response.result.pageTotal);
			generateDataInfo("tableDataCount", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
		
	});
	
}

$('#dateRating').off('change');
$("#dateRating").on('change',function(e) {
	getDataInputRating();
	$("#dateRatingInputAuto").val($(this).val());
});

$('#searchKeywordManual').off('keypress');
$("#searchKeywordManual").on('keypress',function(e) {
    if(e.which == 13) {
        getDataInputRating();
    }
});

function showInputRating(){
	
	toggleSlideContainer('slideContainerLeft', 'slideContainerRight');
	$("#btnOpenInputRating, #btnSettingRating").addClass('d-none');
	$("#btnCloseInputRating, #btnInputAuto").removeClass('d-none');
	getDataInputRating();

}

function getDataInputRating(){
	
	var dateRating		=	$("#dateRating").val(),
		searchKeyword	=	$("#searchKeywordManual").val(),
		dataSend		=	{dateRating:dateRating, searchKeyword:searchKeyword};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/getDataDriverRatingByDate",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$(".generatedResultElem").remove();
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			if(response.status != 200){
				$('#noDataDriverRatingPoint').removeClass('d-none');
			} else {
				$('#noDataDriverRatingPoint').addClass('d-none');
				
				var dataRatingPoint			=	response.result,
					dateRatingStr			=	response.dateRatingStr,
					arrDriverType			=	[],
					arrPartnershipType		=	[],
					arrPartnershipTypeName	=	[],
					rows					=	"";
				
				$.each(dataRatingPoint, function(index, array) {
					
					var partnershipType		=	array.PARTNERSHIPTYPE,
						driverType			=	array.DRIVERTYPE;
						
					if(!arrPartnershipType.includes(partnershipType) || !arrDriverType.includes(driverType) || !arrPartnershipTypeName.includes(partnershipTypeName)){
						var partnershipTypeName	=	'-';
						
						switch(partnershipType){
							case "1"	:	
							case 1		:	partnershipTypeName	=	"Partner - "+driverType; break;
							case "2"	:	
							case 2		:	partnershipTypeName	=	"Freelance"; break;
							case "3"	:	
							case 3		:	partnershipTypeName	=	"Team"; break;
							case "4"	:	
							case 4		:	partnershipTypeName	=	"Office"; break;
						}
						
						if(!arrPartnershipTypeName.includes(partnershipTypeName)){
							if(arrDriverType.length > 0){
								rows	+=			'</tbody>'+
												'</table>'+
											'</div>';
							}
							rows	+=	'<div class="col-sm-12 px-0 generatedResultElem"><h5>'+partnershipTypeName+'</h5></div>'+
										'<div class="col-sm-12 px-0 mb-20 generatedResultElem">'+
											'<table class="table" id="table-inputManual">'+
												'<thead class="thead-light">'+
													'<tr>'+
														'<th>Driver Name</th>'+
														'<th width="300">Source Name</th>'+
														'<th width="140">Booking Code</th>'+
														'<th width="100">Rating</th>'+
														'<th width="100">Point</th>'+
														'<th width="200">Input By</th>'+
														'<th width="40"></th>'+
													'</tr>'+
												'</thead>'+
												'<tbody>';
							arrPartnershipType.push(partnershipType);
							arrPartnershipTypeName.push(partnershipTypeName);
							arrDriverType.push(driverType);
						}
						
					}
					
					var idDriver		=	array.IDDRIVER,
						arrRatingPoint	=	array.DRIVERRATINGPOINT == "" ? [] : array.DRIVERRATINGPOINT.split("&&"),
						lenRatingPoint	=	arrRatingPoint.length,
						rowspan			=	lenRatingPoint <= 0 ? 1 : lenRatingPoint,
						tdRatingPoint	=	"";
					
					if(lenRatingPoint <= 0){
						tdRatingPoint	+=		'<td id="emptyTdSource'+idDriver+'">-</td>'+
												'<td id="emptyTdBookingCode'+idDriver+'">-</td>'+
												'<td id="emptyTdRating'+idDriver+'">-</td>'+
												'<td id="emptyTdPoint'+idDriver+'">-</td>'+
												'<td id="emptyTdInputBy'+idDriver+'">-</td>'+
												'<td id="emptyTdBtnDelete'+idDriver+'">-</td>'+
											'</tr>';
					} else {
						for(var i=0; i<lenRatingPoint; i++){
							var dataRatingPoint			=	arrRatingPoint[i].split("|"),
								bookingCode				=	dataRatingPoint[7],
								inputType				=	dataRatingPoint[8] == "2" || dataRatingPoint[8] == 2 ? "Auto" : "Manual",
								reviewTitle				=	dataRatingPoint[9],
								reviewContent			=	dataRatingPoint[10],
								dataAttrReviewSource	=	reviewTitle == "" ? "" : "data-reviewSource='"+dataRatingPoint[1]+"'";
								dataAttrReviewTitle		=	reviewTitle == "" ? "" : "data-reviewTitle='"+reviewTitle+"'";
								dataAttrReviewDriverName=	reviewTitle == "" ? "" : "data-reviewDriverName='"+array.NAME+"'";
								dataAttrReviewRsvTitle	=	reviewTitle == "" ? "" : "data-reviewRsvTitle='"+dataRatingPoint[11]+"'";
								dataAttrReviewRating	=	reviewTitle == "" ? "" : "data-reviewRating='"+dataRatingPoint[2]+"'";
								dataAttrReviewPoint		=	reviewTitle == "" ? "" : "data-reviewPoint='"+dataRatingPoint[3]+"'";
								dataAttrReviewContent	=	reviewTitle == "" ? "" : "data-reviewContent='"+reviewContent+"'";
								stylePointer			=	reviewTitle == "" ? "" : "style='cursor: pointer'";
								idTdReview				=	reviewTitle == "" ? "" : "id='tdReviewContent"+bookingCode+"'";
								onclickReview			=	reviewTitle == "" ? "" : "onclick='showDetailReviewContent(\""+bookingCode+"\", \"tdReviewContent\")'";
								iconShowReview			=	reviewTitle == "" ? "" : "<i class='fa fa-external-link-square ml-1 text-primary'></i>";
								tdRatingPoint			+=	'<td>'+dataRatingPoint[1]+'</td>'+
															'<td '+idTdReview+' '+
																  stylePointer+' '+
																  onclickReview+' '+
																  dataAttrReviewSource+' '+
																  dataAttrReviewTitle+' '+
																  dataAttrReviewDriverName+' '+
																  dataAttrReviewRsvTitle+' '+
																  dataAttrReviewRating+' '+
																  dataAttrReviewPoint+' '+
																  dataAttrReviewContent+'>'+bookingCode+iconShowReview+'</td>'+
															'<td><i class="fa fa-star text-success"></i> <b>'+dataRatingPoint[2]+'</b></td>'+
															'<td><b>'+dataRatingPoint[3]+'</b></td>'+
															'<td>['+inputType+'] '+dataRatingPoint[4]+'<br/>'+dataRatingPoint[5]+'</td>'+
															'<td>'+
																'<button class="button button-xs button-box button-danger mb-0" onclick="deleteDriverRatingPoint(\''+dataRatingPoint[0]+'\', \''+array.DRIVERTYPE+'\', \''+array.NAME+'\', \''+dateRatingStr+'\', \''+dataRatingPoint[1]+'\', '+dataRatingPoint[2]+', \''+dataRatingPoint[6]+'\')">'+
																	'<i class="fa fa-times"></i>'+
																'</button>'+
															'</td>';
							if(lenRatingPoint == 1){
								tdRatingPoint	+=	'</tr>';
							} else {
								if(lenRatingPoint == (i+1)){
									tdRatingPoint	+=	'</tr>';
								} else {
									tdRatingPoint	+=	'</tr><tr>';
								}
							}
						}
					}
					
					rows	+=	'<tr>'+
									'<td rowspan="'+rowspan+'">'+
										'<button class="button button-xs button-box button-primary mb-0" onclick="addNewDriverRatingPoint(\''+idDriver+'\', \''+array.DRIVERTYPE+'\', \''+array.NAME+'\', \''+dateRatingStr+'\', \''+array.LASTINPUTDATA+'\')">'+
											'<i class="fa fa-plus"></i>'+
										'</button>'+
										' '+array.NAME+
									'</td>'+
									tdRatingPoint;
					
				});
				console.log(arrDriverType);
				console.log(arrPartnershipType);
				console.log(arrPartnershipTypeName);
				
				rows	+=			'</tbody>'+
								'</table>'+
							'</div>';
				
				$("#generatedResultContainer").append(rows);
				
			}
			
		}
	});
	
}

function addNewDriverRatingPoint(idDriver, driverType, driverName, dateRatingStr, lastInputData){
	var splitLastInputData	=	lastInputData.split('|'),
		detailLastInput		=	splitLastInputData[1]+' ['+splitLastInputData[0]+']<br/>'+'<i class="fa fa-star text-success"></i> '+splitLastInputData[2]+' - '+splitLastInputData[3]+' Points';
	
	$("#inputDriverName").html(driverName+" ["+driverType+"]");
	$("#inputDateRating").html(dateRatingStr);
	$("#lastRatingPoint").html(detailLastInput);
	$("#pointInput").html("0");
	$("#ratingInputHidden").val("0");
	$("#reviewTitle, #reviewContent").val("");
	$("#idDriver").val(idDriver);
	
	if($('#ratingInput').length) {
		$('#ratingInput').raty({
			click: function(score, evt) {
				var lsOptionHelper	=	JSON.parse(localStorage.getItem('optionHelper')),
					dataRatingPoint	=	lsOptionHelper['dataRatingPoint'];
				$("#ratingInputHidden").val(score);
				$.each(dataRatingPoint, function(index, array) {
					var rating		=	array.RATING * 1,
						point		=	array.POINT * 1;
					if(rating == score){
						$("#pointInput").html(point);
					}
				});
			}
		});
	}
	
	$("#showPointHistoryModalBtn").off('click');
	$("#showPointHistoryModalBtn").on('click',function(e) {
		showPointHistory(idDriver, driverName);
	});
	
	$("#editor-modal-driverRatingPoint").modal("show");
}

$('#editor-driverRatingPoint').off('submit');
$('#editor-driverRatingPoint').on('submit', function(e) {
	e.preventDefault();
	var idDriver		=	$("#idDriver").val(),
		idSource		=	$("#optionSource").val(),
		dateRating		=	$("#dateRating").val(),
		rating			=	$("#ratingInputHidden").val(),
		point			=	$("#pointInput").html(),
		reviewTitle		=	$("#reviewTitle").val(),
		reviewContent	=	$("#reviewContent").val(),
		dataSend		=	{
			idDriver:idDriver,
			idSource:idSource,
			dateRating:dateRating,
			rating:rating,
			point:point,
			reviewTitle:reviewTitle,
			reviewContent:reviewContent
		};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/saveDataDriverRatingPoint",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-driverRatingPoint :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-driverRatingPoint :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				getDataInputRating();
				generateDataTable();
				$("#editor-modal-driverRatingPoint").modal("hide");
			}
		}
	});
});

function deleteDriverRatingPoint(idDriverRatingPoint, driverType, driverName, dateRating, sourceName, rating, date){
	
	var detailsConfirm	=	'<div class="order-details-customer-info">'+
								'<ul class="ml-10 px-1">'+
									'<li> <span>Driver</span> <span>'+driverName+' ['+driverType+']</span> </li>'+
									'<li> <span>Date</span> <span>'+dateRating+'</span> </li>'+
									'<li> <span>Source</span> <span>'+sourceName+'</span> </li>'+
									'<li> <span>Rating</span> <span><i class="fa fa-star text-success"></i> '+rating+'</span> </li>'+
								'</ul>'+
							'</div>';
	var confirmText		=	'Driver rating & point will be deleted. Details ;<br/><br/>'+detailsConfirm+'<br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idDriverRatingPoint+'|'+date).attr('data-function', "deleteDriverRatingPoint");
	$confirmDeleteDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var strData				=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData'),
		expData				=	strData.split('|'),
		idDriverRatingPoint	=	expData[0],
		dateRating			=	expData[1],
		dataSend			=	{idDriverRatingPoint:idDriverRatingPoint, dateRating:dateRating};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/deleteDriverRatingPoint",
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
				getDataInputRating();
				generateDataTable();
			}
		}
	});
});

$('#editor-modal-settingRating').off('show.bs.modal');
$('#editor-modal-settingRating').on('show.bs.modal', function() {
	var lsOptionHelper	=	JSON.parse(localStorage.getItem('optionHelper')),
		dataRatingPoint	=	lsOptionHelper['dataRatingPoint'],
		rows			=	"";
	
	$.each(dataRatingPoint, function(index, array) {
		var pointValue		=	array.POINT < 0 ? array.POINT * -1 : array.POINT,
			plusMinus		=	array.POINT <= 0 ? '-' : '+',
			selectedPlus	=	plusMinus == '+' ? "selected" : "";
			selectedMinus	=	plusMinus == '-' ? "selected" : "";
		rows				+=	'<tr id="trRatingPointInput'+array.RATING+'">'+
									'<td><i class="fa fa-star text-success"></i> '+array.RATING+'</td>'+
									'<td>'+
										'<select id="optionPlusMinus'+array.RATING+'" name="optionPlusMinus'+array.RATING+'" class="form-control form-control-sm">'+
											'<option value="+" '+selectedPlus+'>+</option>'+
											'<option value="-" '+selectedMinus+'>-</option>'+
										'</select>'+
									'</td>'+
									'<td><input type="text" data-rating="'+array.RATING+'" class="form-control form-control-sm pointInput" id="pointInput'+array.RATING+'" name="pointInput'+array.RATING+'" value="'+pointValue+'"/></td>'+
								'</tr>';
	});
	
	$("#tbodySettingRatingPoint").html(rows);
});

$('#editor-modal-settingRating').off('submit');
$('#editor-modal-settingRating').on('submit', function(e) {
	
	e.preventDefault();
	var arrRatingPoint	=	[];
	
	$('input:text.pointInput').each(function () {
		var rating		=	$(this).attr("data-rating"),
			plusMinus	=	$("#optionPlusMinus"+rating).val();
			point		=	$(this).val(),
			point		=	plusMinus == '+' ? point * 1 : point * -1;
		
		arrRatingPoint.push([rating, point]);
	});
	
	var dataSend	=	{arrRatingPoint:arrRatingPoint};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/saveDataSettingRatingPoint",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-settingRating :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-settingRating :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				updateDataOptionHelper("dataRatingPoint", response.optionHelper);
				$("#editor-modal-settingRating").modal("hide");
			}
			
		}
	});
	
});

function showPointHistory(idDriver, driverName){
	
	var $tableBody		=	$('#tableRatingPointHistory > tbody'),
		columnNumber	=	$('#tableRatingPointHistory > thead > tr > th').length,
		dataSend	=	{idDriver:idDriver};
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/getDataHistoryRatingPoint",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#modal-ratingPointHistory").modal('show');
			$("#ratingPointHistoryDriverName").html(driverName);
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			if(response.status != 200){
				$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>"+response.msg+"</center></td></tr>");
			} else {
	
				var dataHistory	=	response.result,
					rows		=	"";
				
				$.each(dataHistory, function(index, array) {
					
					var bookingCode				=	array.BOOKINGCODE,
						reviewTitle				=	array.REVIEWTITLE,
						reviewContent			=	array.REVIEWCONTENT,
						dataAttrReviewSource	=	reviewTitle == "" ? "" : "data-reviewSource='"+array.SOURCENAME+"'";
						dataAttrReviewTitle		=	reviewTitle == "" ? "" : "data-reviewTitle='"+reviewTitle+"'";
						dataAttrReviewDriverName=	reviewTitle == "" ? "" : "data-reviewDriverName='"+driverName+"'";
						dataAttrReviewRsvTitle	=	reviewTitle == "" ? "" : "data-reviewRsvTitle='"+array.RESERVATIONTITLE+"'";
						dataAttrReviewRating	=	reviewTitle == "" ? "" : "data-reviewRating='"+array.RATING+"'";
						dataAttrReviewPoint		=	reviewTitle == "" ? "" : "data-reviewPoint='"+array.POINT+"'";
						dataAttrReviewContent	=	reviewTitle == "" ? "" : "data-reviewContent='"+reviewContent+"'";
						stylePointer			=	reviewTitle == "" ? "" : "style='cursor: pointer'";
						idTdReview				=	reviewTitle == "" ? "" : "id='tdHistoryReviewContent"+bookingCode+"'";
						onclickReview			=	reviewTitle == "" ? "" : "onclick='showDetailReviewContent(\""+bookingCode+"\", \"tdHistoryReviewContent\")'";
						iconShowReview			=	reviewTitle == "" ? "" : "<i class='fa fa-external-link-square ml-1 text-primary'></i>";
					rows						+=	'<tr>'+
														'<td>'+array.SOURCENAME+'</td>'+
														'<td '+idTdReview+' '+
															  stylePointer+' '+
															  onclickReview+' '+
															  dataAttrReviewSource+' '+
															  dataAttrReviewTitle+' '+
															  dataAttrReviewDriverName+' '+
															  dataAttrReviewRsvTitle+' '+
															  dataAttrReviewRating+' '+
															  dataAttrReviewPoint+' '+
															  dataAttrReviewContent+'>'+bookingCode+iconShowReview+'</td>'+
														'<td align="center">'+array.DATERATINGPOINT+'</td>'+
														'<td align="right"><i class="fa fa-star text-success"></i> '+array.RATING+'</td>'+
														'<td align="right">'+array.POINT+'</td>'+
														'<td>'+array.USERINPUT+' @'+array.DATETIMEINPUT+'</td>'+
													'</tr>';
				});
				
				$tableBody.html(rows);
			}
			
		}
		
	});
	
}

function openFormBasicPoint(idDriver, driverName, basicPoint){
	
	$("#driverNameBasicPoint").html(driverName);
	$("#basicPointInput").val(numberFormat(basicPoint));
	$("#idDriverBasicPoint").val(idDriver);
	$("#editor-modal-driverBasicPoint").modal('show');
	
}

$('#scanAutoInputButton').off('click');
$('#scanAutoInputButton').on('click', function(e) {
	
	var idSource	=	$('#optionSourceInputAuto').val(),
		sourceName	=	$('#optionSourceInputAuto option:selected').text(),
		jsonData	=	$('#jsonDataInputAuto').val(),
		dataSend	=	{idSource:idSource, jsonData:jsonData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/scanInputRatingPointAuto",
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
			
			if(response.status == 200){
				
				var arrRatingPointTable	=	response.arrRatingPointTable,
					rowScanResult		=	"",
					lsOptionHelper		=	JSON.parse(localStorage.getItem('optionHelper')),
					dataRatingPoint		=	lsOptionHelper['dataRatingPoint'];
					
				for(var i=0; i<arrRatingPointTable.length; i++){
					
					var ratingPointTable	=	arrRatingPointTable[i],
						driverId			=	JSON.stringify([ratingPointTable.driverId * 1]),
						bookingCode			=	ratingPointTable.bookingCode,
						reservationTitle	=	ratingPointTable.reservationTitle,
						reviewTitle			=	ratingPointTable.reviewTitle,
						driverName			=	ratingPointTable.driverName,
						reviewContentLength	=	ratingPointTable.reviewContentLength,
						reviewContent		=	ratingPointTable.reviewContent.replace(/['"]+/g, ''),
						reviewContentStr	=	reviewContentLength > 200 ? reviewContent.substring(0, 180)+"..." : reviewContent,
						ratingTable			=	ratingPointTable.rating * 1,
						pointTable			=	0
						arrBookingCode		=	[];
						
					$.each(dataRatingPoint, function(index, array) {
						var rating		=	array.RATING * 1,
							point		=	array.POINT * 1;
						if(rating == ratingTable){
							pointTable	=	point;
						}
					});
					
					if(idSource == 2){
						driverId		=	"[9]";
						driverName		=	generateOptionDriverInputAuto(bookingCode);
						arrBookingCode.push(bookingCode);
					}
					
					rowScanResult		+=	"<tr id='trInputAuto"+bookingCode+"' "+
												"data-dateRating='"+ratingPointTable.dateRating+"' "+
												"data-driverId='"+driverId+"' "+
												"data-bookingCode='"+ratingPointTable.bookingCode+"' "+
												"data-reviewSource='"+sourceName+"' "+
												"data-reviewTitle='"+reviewTitle+"' "+
												"data-reviewDriverName='-' "+
												"data-reviewRsvTitle='"+reservationTitle+"' "+
												"data-reviewRating='"+ratingPointTable.rating+"' "+
												"data-reviewPoint='"+pointTable+"' "+
												"data-reviewContent='"+reviewContent+"'"+
												">"+
												"<td>"+ratingPointTable.dateRatingStr+"</td>"+
												"<td>"+driverName+"</td>"+
												"<td onclick='showDetailReviewContent(\""+ratingPointTable.bookingCode+"\")'><b>"+reviewTitle+"</b><br/>"+reviewContentStr+"</td>"+
												"<td>"+ratingPointTable.bookingCode+"</td>"+
												"<td align='right'><i class='fa fa-star text-success'></i> "+ratingPointTable.rating+"</td>"+
												"<td align='right'>"+pointTable+"</td>"+
											"</tr>";
				}
				
				$("#tbodyInputAuto").html(rowScanResult);
				
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}

		}

	});

});

$('#editor-inputAuto').off('submit');
$('#editor-inputAuto').on('submit', function(e) {
	
	e.preventDefault();
	var arrRatingPointInput	=	[],
		idSource			=	$("#optionSourceInputAuto").val();
	
	$('#tbodyInputAuto > tr').each(function () {
		var dateRating		=	$(this).attr("data-dateRating"),
			driverId		=	$(this).attr("data-driverId"),
			bookingCode		=	$(this).attr("data-bookingCode"),
			rating			=	$(this).attr("data-reviewRating"),
			point			=	$(this).attr("data-reviewPoint"),
			reviewTitle		=	$(this).attr("data-reviewTitle"),
			reviewContent	=	$(this).attr("data-reviewContent").replace(/(?:\r\n|\r|\n)/g, '<br>');
		
		arrRatingPointInput.push([dateRating, driverId, rating, point, bookingCode, reviewTitle, reviewContent]);
	});
	
	var dataSend	=	{idSource:idSource, arrRatingPointInput:arrRatingPointInput};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/saveInputRatingPointAuto",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-inputAuto :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-inputAuto :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$("#editor-modal-inputAuto").modal("hide");
				$("#jsonDataInputAuto").val("");
				$("#tbodyInputAuto").html("");
				getDataInputRating();
				generateDataTable();
			}
			
		}
	});
	
});

$('#editor-driverBasicPoint').off('submit');
$('#editor-driverBasicPoint').on('submit', function(e) {
	
	e.preventDefault();
	var idDriver	=	$("#idDriverBasicPoint").val(),
		basicPoint	=	$("#basicPointInput").val(),
		dataSend	=	{idDriver:idDriver, basicPoint:basicPoint};
		
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/saveDriverBasicPoint",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-driverBasicPoint :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-driverBasicPoint :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$("#editor-modal-driverBasicPoint").modal("hide");
				getDataTable();
			}
			
		}
	});
	
});

function refreshDriverPoint(){
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/refreshDriverPoint",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			generateDataTable();
			
		}
	});
	
}

function showDetailReviewContent(bookingCode, elemPrefix = 'trInputAuto'){
	
	if(elemPrefix != false){
		var $elemReviewContent	=	$("#"+elemPrefix+bookingCode),
			reviewTitle			=	$elemReviewContent.attr('data-reviewTitle'),
			sourceName			=	$elemReviewContent.attr('data-reviewSource'),
			reservationTitle	=	$elemReviewContent.attr('data-reviewRsvTitle'),
			driverName			=	$elemReviewContent.attr('data-reviewDriverName'),
			rating				=	$elemReviewContent.attr('data-reviewRating'),
			point				=	$elemReviewContent.attr('data-reviewPoint'),
			reviewContent		=	$elemReviewContent.attr('data-reviewContent');		
		
		$("#detailReviewContent-title").html(reviewTitle);
		$("#detailReview-sourceBookingCode").html(sourceName+' - '+bookingCode);
		$("#detailReview-reservationTitle").html(reservationTitle);
		$("#detailReview-driverName").html(driverName);
		$("#detailReview-ratingPoint").html('<i class="fa fa-star text-success"></i> <b>'+rating+'</b> ('+point+')');
		$("#detailReviewContent-text").html(reviewContent.replace(/(?:\r\n|\r|\n)/g, '<br>'));
		$('#modalDetailReviewContent').modal('show');	
	} else {
		var dataSend	=	{bookingCode:bookingCode};
		$.ajax({
			type: 'POST',
			url: baseURL+"schedule/driverRatingPoint/getDetailReviewContent",
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
					var detailData	=	response.detailData;
					$("#detailReviewContent-title").html(detailData.REVIEWTITLE);
					$("#detailReview-sourceBookingCode").html(detailData.SOURCENAME+' - '+detailData.BOOKINGCODE);
					$("#detailReview-reservationTitle").html(detailData.RESERVATIONTITLE);
					$("#detailReview-driverName").html(detailData.DRIVERNAME);
					$("#detailReview-ratingPoint").html('<i class="fa fa-star text-success"></i> <b>'+detailData.RATING+'</b> ('+detailData.POINT+')');
					$("#detailReviewContent-text").html(detailData.REVIEWCONTENT.replace(/(?:\r\n|\r|\n)/g, '<br>'));
					$('#modalDetailReviewContent').modal('show');	
				} else {
					$('#modalWarning').on('show.bs.modal', function() {
						$('#modalWarningBody').html(response.msg);
					});
					$('#modalWarning').modal('show');
				}				
			}
		});
	}
	
}

function generateOptionDriverInputAuto(bookingCode, index = 0){
	
	var optionElemText	=	'<select id="optionDriverInputAuto'+bookingCode+'-'+index+'" '+
									'name="optionDriverInputAuto'+bookingCode+'-'+index+'" '+
									'class="form-control form-control-sm mb-5 optionDriverInputAuto'+bookingCode+'" '+
									'onchange="setIdDriverInputAuto(\''+bookingCode+'\', this.value, '+index+')">'+
										'<option value="9">Not Set</option>',
		dataOpt			=	JSON.parse(localStorage.getItem('optionHelper')),
		options     	=   dataOpt['dataDriver'];
	
	$.each(options, function(index, array) {
		optionElemText	+=	'<option value="'+array.ID+'">'+array.VALUE+'</option>';
	});
	
	optionElemText	+=	'</select>';
	
	if(index == 0){
		var newIndex	=	index + 1;
		optionElemText	+=	'<button type="button" id="addOtherDriverInputAuto'+bookingCode+'" class="button button-primary button-xs" onclick="addOtherDriverInputAuto(\''+bookingCode+'\', '+newIndex+')"><span><i class="fa fa-plus"></i>Add Other Driver</span></button>';
	}
	
	return optionElemText;
	
}

function addOtherDriverInputAuto(bookingCode, newIndex){
	
	var newElemOptionDriver	=	generateOptionDriverInputAuto(bookingCode, newIndex),
		oldIndex			=	newIndex - 1,
		newIndexClick		=	newIndex + 1,
		trArrIdDriver		=	JSON.parse($("#trInputAuto"+bookingCode).attr('data-driverId'));
	
	trArrIdDriver.push(9);
	$('#optionDriverInputAuto'+bookingCode+'-'+oldIndex).after(newElemOptionDriver);
	
	$('#addOtherDriverInputAuto'+bookingCode).attr('onclick', 'addOtherDriverInputAuto(\''+bookingCode+'\', '+newIndexClick+')');	
	$("#trInputAuto"+bookingCode).attr('data-driverId', JSON.stringify(trArrIdDriver));
	
}

function setIdDriverInputAuto(bookingCode, idDriver, index){
	var trArrIdDriver	=	JSON.parse($("#trInputAuto"+bookingCode).attr('data-driverId'));
	trArrIdDriver[index]=	idDriver * 1;
	$("#trInputAuto"+bookingCode).attr('data-driverId', JSON.stringify(trArrIdDriver));
}

$('#optionMonth, #optionYear, #optionDriverType, #optionDriver, #optionSourceRatingCalendar, #optionOrderBy, #optionOrderType').off('change');
$("#optionMonth, #optionYear, #optionDriverType, #optionDriver, #optionSourceRatingCalendar, #optionOrderBy, #optionOrderType").on('change',function(e) {
	getDataRatingCalendar();
});

$('#checkboxShowRank').off('click');
$("#checkboxShowRank").on('click',function(e) {
	getDataRatingCalendar();
});

function getDataRatingCalendar(){
	
	var $tableBody		=	$('#table-calendarRating > tbody'),
		columnNumber	=	1,
		month			=	$('#optionMonth').val(),
		year			=	$('#optionYear').val(),
		idDriverType	=	$('#optionDriverType').val(),
		idDriver		=	$('#optionDriver').val(),
		idSource		=	$('#optionSourceRatingCalendar').val(),
		orderField		=	$('#optionOrderBy').val(),
		orderType		=	$('#optionOrderType').val(),
		dataSend		=	{month:month, year:year, idDriverType:idDriverType, idDriver:idDriver, idSource:idSource, orderField:orderField, orderType:orderType};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"schedule/driverRatingPoint/getDataRatingCalendar",
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
			
			var arrDates		=	response.arrDates,
				arrTotalPerDate	=	[],
				defaultArr5Star	=	[],
				checkedShowRank	=	$("#checkboxShowRank").is(':checked'),
				rows			=	thHeaderDates	=	"";
			columnNumber		=	columnNumber + arrDates.length + 1;
			
			for(var iDates=0; iDates<arrDates.length; iDates++){
				thHeaderDates		+=	'<th width="80" class="thHeaderDates text-center" onclick="getClipboard5StarDataPerDate('+iDates+')">'+arrDates[iDates]+'</th>';
				arrTotalPerDate.push(0);
				defaultArr5Star.push(0);
			}
			thHeaderDates	+=	'<th width="75" class="thHeaderDates text-center" onclick="getClipboard5StarAllDriverDate()">Total <i class="fa fa-star mr-1">5</th>';
			
			$("#headerDates").append(thHeaderDates);
			
			if(response.status != 200){
				rows		=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				
				var data					=	response.dataDriver,
					totalFiveStarMonth		=	0,
					arrDriverNameCalendar	=	[],
					arrRatingPoint5Star		=	[],
					arrPartnershipType		=	[],
					arrPartnershipTypeName	=	[],
					arrIdTypeDriver			=	[];
					
				$.each(data, function(indexDriver, array) {
					
					var classStrong			=	"",
						totalFiveStar		=	0,
						arrTotal5StarDate	=	JSON.parse(JSON.stringify(defaultArr5Star)),
						partnershipType		=	array.PARTNERSHIPTYPE,
						idDriverType		=	array.IDDRIVERTYPE,
						driverType			=	array.DRIVERTYPE;
						
					switch(partnershipType){
						case "1"	:	switch(idDriverType){
											case "1"	:	classStrong	=	"text-warning"; break;
											case "2"	:	classStrong	=	"text-primary"; break;
											case "3"	:	
											default		:	classStrong	=	"text-info"; break;
										}
										break;
						case "2"	:	classStrong	=	"text-info";
										break;
						case "3"	:	classStrong	=	"text-success";
										break;
						case "4"	:	classStrong	=	"text-dark";
										break;
					}
					
					if(!arrPartnershipType.includes(partnershipType) || !arrIdTypeDriver.includes(idDriverType) || !arrPartnershipTypeName.includes(partnershipTypeName)){
						var partnershipTypeName	=	'-';
						switch(partnershipType){
							case "1"	:	
							case 1		:	partnershipTypeName	=	"Partner - "+driverType; break;
							case "2"	:	
							case 2		:	partnershipTypeName	=	"Freelance"; break;
							case "3"	:	
							case 3		:	partnershipTypeName	=	"Team"; break;
							case "4"	:	
							case 4		:	partnershipTypeName	=	"Office"; break;
						}
						
						if(!arrPartnershipTypeName.includes(partnershipTypeName)){
							rows	+=	'<tr><td colspan="'+columnNumber+'"><h6>'+partnershipTypeName+'</h6></td><tr>';
							arrPartnershipType.push(partnershipType);
							arrPartnershipTypeName.push(partnershipTypeName);
							arrIdTypeDriver.push(idDriverType);
						}
					}
					
					var rankText	=	checkedShowRank ? "<strong class='"+classStrong+"'>["+array.RANKNUMBER+"]</strong>" : "";
					rows			+=	"<tr>"+
											"<td style='white-space: nowrap;'>"+
												rankText+" "+array.DRIVERNAME
											"</td>";
					arrDriverNameCalendar.push(array.DRIVERNAME);
					
					$.each(array.DATARATING, function(iRating, arrRating) {
						var elemTD				=	"",
							totalFiveStarDate	=	0;
						if(arrRating.length > 0){
							for(var iChildRating=0; iChildRating<arrRating.length; iChildRating++){
							    var starValue       =   arrRating[iChildRating][1],
							        bookingCode		=   arrRating[iChildRating][2],
									onclickReview	=	bookingCode != "" ? 'onclick="showDetailReviewContent(\''+bookingCode+'\', false)"' : '',
							        classBtnRating  =   "button-success";
							    
							    switch(starValue){
							        case "1"  :   classBtnRating  =   "button-danger"; break;
							        case "2"  :   classBtnRating  =   "button-warning"; break;
							        case "3"  :   classBtnRating  =   "button-info"; break;
							        case "4"  :   classBtnRating  =   "button-primary"; break;
							        case "5"  :   classBtnRating  =   "button-success"; break;
							    }
							    
								elemTD	+=	'<button class="button '+classBtnRating+' button-xs" '+onclickReview+'><span></i><b>'+starValue+'</b></span></button><br/>';
								if(starValue == 5){
									arrTotalPerDate[iRating]++;
									totalFiveStar++;
									totalFiveStarDate++;
									totalFiveStarMonth++;
								}
							}
						}
						
						rows	+=	'<td style="padding: .7rem .3rem;">'+elemTD+'</td>';
						if(totalFiveStarDate > 0) arrTotal5StarDate[iRating]	=	totalFiveStarDate;
					});
					
					arrRatingPoint5Star.push(arrTotal5StarDate);
					rows	+=	"<td align='right'><b>"+totalFiveStar+"</b></td></tr>";
					
				});
				
				console.log(arrPartnershipType);
				console.log(arrPartnershipTypeName);
				console.log(arrIdTypeDriver);
				
				rows	+=	"<tr><td><b>Total Per Date</b></td>";
				for(var iTotalPerDate=0; iTotalPerDate<arrTotalPerDate.length; iTotalPerDate++){
					rows+=	"<td align='right' onclick='getClipboard5StarDataPerDate("+iTotalPerDate+")'><b>"+arrTotalPerDate[iTotalPerDate]+"</b></td>";
				}
				rows	+=	"<td align='right'><b>"+totalFiveStarMonth+"</b></td>";
				rows	+=	"</tr>";
				
				localStorage.setItem('arrRatingPoint5Star', JSON.stringify(arrRatingPoint5Star));		
				localStorage.setItem('arrDriverNameCalendar', JSON.stringify(arrDriverNameCalendar));		
			}
			
			$tableBody.html(rows);

		}
	});	
}

function getClipboard5StarDataPerDate(idxArray){
	var	arrRatingPoint5Star	=	JSON.parse(localStorage.getItem('arrRatingPoint5Star')),
		textClipboard		=	"";
	$.each(arrRatingPoint5Star, function(idxDriver, arrTotal5StarDate) {
		$.each(arrTotal5StarDate, function(idx5Star, total5Star) {
			if(idx5Star == idxArray){
				textClipboard	+=	total5Star+"\n";
			}
		});
	});

	navigator.clipboard.writeText(textClipboard);
	toastr["info"]("Data copied to clipboard");
}

function getClipboardRatingCalendarDriverName(){
	var	arrDriverNameCalendar	=	JSON.parse(localStorage.getItem('arrDriverNameCalendar')),
		textClipboard			=	"";
	$.each(arrDriverNameCalendar, function(idxDriver, driverName) {
		textClipboard	+=	driverName+"\n";
	});

	navigator.clipboard.writeText(textClipboard);
	toastr["info"]("Data copied to clipboard");
}

function getClipboard5StarAllDriverDate(){
	var	arrDriverNameCalendar	=	JSON.parse(localStorage.getItem('arrDriverNameCalendar')),
		arrRatingPoint5Star		=	JSON.parse(localStorage.getItem('arrRatingPoint5Star')),
		textClipboard			=	"";
		
	$.each(arrDriverNameCalendar, function(idxDriver, driverName) {
		textClipboard	+=	driverName;
		$.each(arrRatingPoint5Star, function(idxRatingPoint, arrTotal5StarDate) {
			if(idxRatingPoint == idxDriver){
				$.each(arrTotal5StarDate, function(idx5Star, total5Star) {
					textClipboard	+=	"\t"+total5Star;
				});
			}
		});
		textClipboard	+=	"\n";
	});

	navigator.clipboard.writeText(textClipboard);
	toastr["info"]("Data copied to clipboard");
}
driverRatingPointFunc();