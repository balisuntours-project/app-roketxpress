var $confirmDialog	=	$('#modal-confirm-action');
	
if (reviewBonusPunishmentFunc == null){
	var reviewBonusPunishmentFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('allDriverReport-optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('allDriverReport-optionYear', 'optionYear', false, false);
			setOptionHelper('allDriverReport-optionDriverType', 'dataDriverType');
			setOptionHelper('allDriverReport-optionDriver', 'dataDriverReview');
			
			$('#allDriverReport-optionDriverType').change(function() { 
				if(this.value != ""){
					setOptionHelper('allDriverReport-optionDriver', 'dataDriverReview', false, false, this.value);
				} else {
					setOptionHelper('allDriverReport-optionDriver', 'dataDriverReview');
				}
				getDataAllDriverReport();
			});
			getDataAllDriverReport();

			setOptionHelper('periodTargetRate-optionYear', 'optionYear', false, false);
			getDataPeriodTargetRate();
		});	
	}
}

$('#allDriverReport-optionDriverType, #allDriverReport-optionDriver, #allDriverReport-optionMonth, #allDriverReport-optionYear').off('change');
$('#allDriverReport-optionDriverType, #allDriverReport-optionDriver, #allDriverReport-optionMonth, #allDriverReport-optionYear').on('change', function(e) {
	getDataAllDriverReport();
});

$('#periodTargetRate-optionYear').off('change');
$('#periodTargetRate-optionYear').on('change', function(e) {
	getDataPeriodTargetRate();
});

function generateDataTable(page){
	getDataAllDriverReport(page);
}

function getDataAllDriverReport(page = 1){
	var $tableBody	=	$('#table-allDriverReport > tbody'),
		columnNumber=	$('#table-allDriverReport > thead > tr > th').length,
		idDriverType=	$('#allDriverReport-optionDriverType').val(),
		idDriver	=	$('#allDriverReport-optionDriver').val(),
		month		=	$('#allDriverReport-optionMonth').val(),
		year		=	$('#allDriverReport-optionYear').val(),
		dataSend	=	{page:page, idDriverType:idDriverType, idDriver:idDriver, month:month, year:year};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/reviewBonusPunishment/getDataAllDriverReport",
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
				urlExcelReport	=	response.urlExcelReport,
				rows			=	"";
			
			if(data.length === 0){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
				$('#excelAllDriverReport').addClass('d-none').off("click").attr("href", "");
			} else {
				$('#excelAllDriverReport').removeClass('d-none').on("click").attr("href", urlExcelReport);
				$.each(data, function(index, array) {
					var idDriverReviewBonus		=	array.IDDRIVERREVIEWBONUS,
						badgeStatusWithdrawal	=	btnEdit	=	'';
					switch(array.STATUSWITHDRAWAL){
						case "0"	:	badgeStatusWithdrawal	=	'<span class="badge badge-pill badge-primary">Hold</span>';
										btnEdit					=	'<button class="button button-xs button-box button-primary" onclick="getEditorTargetReviewPoint('+idDriverReviewBonus+')">'+
																		'<i class="fa fa-pencil"></i>'+
																	'</button>';
										break;
						case "1"	:	badgeStatusWithdrawal	=	'<span class="badge badge-pill badge-success">Withdrawn</span>';
										break;
						default		:	badgeStatusWithdrawal	=	'<span class="badge badge-pill badge-info">-</span>';
										break;
					}
					
					rows	+=	"<tr class='trDataDriverReviewBonus' data-idDriverReviewBonus='"+idDriverReviewBonus+"' data-targetException='"+array.TARGETEXCEPTION+"'>"+
									"<td>"+array.DRIVERTYPE+"</td>"+
									"<td>"+array.DRIVERNAME+"</td>"+
									"<td>"+array.PERIODDATESTART+"</td>"+
									"<td>"+array.PERIODDATEEND+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALTARGET)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALREVIEWPOINT)+"</td>"+
									"<td align='right'>"+numberFormat(array.BONUSRATE)+"</td>"+
									"<td align='right'>"+numberFormat(array.NOMINALBONUS)+"</td>"+
									"<td align='right'>"+numberFormat(array.NOMINALPUNISHMENT)+"</td>"+
									"<td align='right'>"+numberFormat(array.NOMINALRESULT)+"</td>"+
									"<td>"+badgeStatusWithdrawal+"</td>"+
									"<td align='center'>"+btnEdit+"</td>"+
								"</tr>";
				});
			}

			generatePagination("tablePaginationAllDriverReport", page, response.result.pageTotal);
			generateDataInfo("tableDataCountAllDriverReport", response.result.dataStart, response.result.dataEnd, response.result.dataTotal);
			$tableBody.html(rows);
		}
	});
}

function getEditorTargetReviewPoint(idDriverReviewBonus){
	$('#container-editorTargetReviewPoint').off('submit');
	var trDataDriverReviewBonus	=	$(".trDataDriverReviewBonus[data-idDriverReviewBonus='"+idDriverReviewBonus+"']");
	
	if(trDataDriverReviewBonus.length > 0){
		let columnDriverReviewBonus	=	trDataDriverReviewBonus.find('td'),
			targetException			=	parseInt(trDataDriverReviewBonus.attr('data-targetException')),
			driverTypeStr			=	columnDriverReviewBonus[0].innerHTML,
			driverNameStr			=	columnDriverReviewBonus[1].innerHTML,
			periodStartStr			=	columnDriverReviewBonus[2].innerHTML,
			periodEndStr			=	columnDriverReviewBonus[3].innerHTML,
			targetPoint				=	parseInt(columnDriverReviewBonus[4].innerHTML);
						
		$("#editorTargetReviewPoint-driverDetailsStr").html(driverNameStr+ " ("+driverTypeStr+")");
		$("#editorTargetReviewPoint-targetPeriodStr").html(periodStartStr+ " - "+periodEndStr);
		
			
		if(targetException != -1 && targetPoint == 0){
			$("#editorTargetReviewPoint-targetStatus").val(1);
			$("#editorTargetReviewPoint-totalTarget").val(0).attr('recentTotalTarget', 0).prop('disabled', true);
			$(".btn-number[data-field=editorTargetReviewPoint-totalTarget]").prop('disabled', true);
		} else {
			$("#editorTargetReviewPoint-targetStatus").val(-1);
			$("#editorTargetReviewPoint-totalTarget").val(targetPoint).attr('recentTotalTarget', targetPoint).prop('disabled', false);
			$(".btn-number[data-field=editorTargetReviewPoint-totalTarget]").prop('disabled', false);
		}
		
		$("#editorTargetReviewPoint-idDriverReviewBonus").val(idDriverReviewBonus);
		activateOnChangeTargetStatus();
		activateCounterFieldEvent();
		
		$('#modal-editorTargetReviewPoint').modal('show');
		$('#container-editorTargetReviewPoint').on('submit', function(e) {
			e.preventDefault();
			var idDriverReviewBonus	=	$("#editorTargetReviewPoint-idDriverReviewBonus").val(),
				totalTarget			=	$("#editorTargetReviewPoint-totalTarget").val(),
				dataSend			=	{idDriverReviewBonus:idDriverReviewBonus, totalTarget:totalTarget};
			
			$.ajax({
				type: 'POST',
				url: baseURL+"financeDriver/reviewBonusPunishment/updateTargetReviewPointDriver",
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
						$('#modal-editorTargetReviewPoint').modal('hide');
						getDataAllDriverReport();
						getDataPeriodTargetRate();
					}
				}
			});
		});
	} else {
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html("Could not find the details for the data you selected");
		});
		$('#modalWarning').modal('show');
	}
}

function activateOnChangeTargetStatus(){
	$('#editorTargetReviewPoint-targetStatus').off('change');
	$('#editorTargetReviewPoint-targetStatus').on('change', function(e) {
		let targetStatus		=	$(this).val(),
			recentTotalTarget	=	$("#editorTargetReviewPoint-totalTarget").attr('recentTotalTarget');
		
		if(targetStatus == -1){
			recentTotalTarget	=	recentTotalTarget == 0 ? 1 : recentTotalTarget;
			$("#editorTargetReviewPoint-totalTarget").val(recentTotalTarget).prop('disabled', false);
			$(".btn-number[data-field=editorTargetReviewPoint-totalTarget]").prop('disabled', false);
		} else {
			$("#editorTargetReviewPoint-totalTarget").val(0).prop('disabled', true);
			$(".btn-number[data-field=editorTargetReviewPoint-totalTarget]").prop('disabled', true);
		}
	});
}

function getDataPeriodTargetRate(){
	var $tableBody	=	$('#table-periodTargetRate > tbody'),
		columnNumber=	$('#table-periodTargetRate > thead > tr > th').length,
		year		=	$('#periodTargetRate-optionYear').val(),
		dataSend	=	{year:year};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"financeDriver/reviewBonusPunishment/getDataPeriodTargetRate",
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
			
			var data		=	response.result.data,
				totalData	=	response.totalData,
				rows		=	"";
			
			if(data.length === 0){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>No data found</center></td></tr>";
			} else {
				
				$.each(data, function(index, array) {
					var idDriverReviewBonusPeriod	=	array.IDDRIVERREVIEWBONUSPERIOD,
						totalReviewBonusWithdrawn	=	array.TOTALREVIEWBONUSWITHDRAWN * 1,
						btnEdit						=	"";
						
					if(((index + 1) == totalData || index == (totalData - 2)) && totalReviewBonusWithdrawn <= 0){
						var isLastPeriod=	index + 1 == totalData ? true : false;
						btnEdit			=	'<button class="button button-xs button-box button-primary" data-toggle="modal" data-target="#modal-formPeriodTargetRate" data-idDriverReviewBonusPeriod="'+idDriverReviewBonusPeriod+'" data-isLastPeriod="'+isLastPeriod+'">'+
												'<i class="fa fa-pencil"></i>'+
											'</button><br/>';
					}				
					
					rows	+=	"<tr id='rowReviewBonusPeriod"+idDriverReviewBonusPeriod+"' "+
									"data-periodMonthYear = '"+array.PERIODMONTHYEAR+"' "+
									"data-periodMonthYearStr = '"+array.PERIODMONTHYEARSTR+"' "+
									"data-periodDateStart = '"+array.PERIODDATESTARTVAL+"' "+
									"data-periodDateEnd = '"+array.PERIODDATEENDVAL+"' "+
									"data-periodTarget = '"+array.TOTALTARGET+"' "+
									"data-periodRate = '"+numberFormat(array.BONUSRATE)+"' >"+
									"<td><b>"+array.PERIODMONTHYEARSTR+"</b></td>"+
									"<td>"+array.PERIODDATESTART+"</td>"+
									"<td>"+array.PERIODDATEEND+"</td>"+
									"<td align='right'>"+array.TOTALTARGET+"</td>"+
									"<td align='right'>"+numberFormat(array.BONUSRATE)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALDRIVERBONUSPUNISHMENT)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALREVIEWBONUSWITHDRAWN)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALBONUS)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALPUNISHMENT)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALRESULT)+"</td>"+
									"<td align='center'>"+btnEdit+"</td>"+
								"</tr>";
				});
				
			}

			generatePagination("tablePaginationPeriodTargetRate", 1, response.result.pageTotal);
			generateDataInfo("tableDataCountPeriodTargetRate", response.result.dataStart, response.result.dataEnd, response.result.dataTotal);
			$tableBody.html(rows);
		}
	});
}

$('#modal-formPeriodTargetRate').off('show.bs.modal');
$('#modal-formPeriodTargetRate').on('show.bs.modal', function(e) {
	var idDriverReviewBonusPeriod	=	$(e.relatedTarget).attr('data-idDriverReviewBonusPeriod'),
		isLastPeriod				=	$(e.relatedTarget).attr('data-isLastPeriod'),
		elemRowReviewBonusPeriod	=	$("#rowReviewBonusPeriod"+idDriverReviewBonusPeriod),
		periodMonthYear				=	elemRowReviewBonusPeriod.attr('data-periodMonthYear'),
		periodMonthYearStr			=	elemRowReviewBonusPeriod.attr('data-periodMonthYearStr'),
		periodDateStart				=	elemRowReviewBonusPeriod.attr('data-periodDateStart'),
		periodDateEnd				=	elemRowReviewBonusPeriod.attr('data-periodDateEnd'),
		periodTarget				=	elemRowReviewBonusPeriod.attr('data-periodTarget'),
		periodRate					=	elemRowReviewBonusPeriod.attr('data-periodRate');

	$("#formPeriodTargetRate-periodMonthYearStr").html(periodMonthYearStr);
	$("#formPeriodTargetRate-datePeriodStart").val(periodDateStart);
	$("#formPeriodTargetRate-datePeriodEnd").val(periodDateEnd);
	$("#formPeriodTargetRate-totalTarget").val(periodTarget);
	$("#formPeriodTargetRate-rateBonusPunishment").val(periodRate);
	$("#formPeriodTargetRate-idDriverReviewBonusPeriod").val(idDriverReviewBonusPeriod);
	$("#formPeriodTargetRate-periodMonthYear").val(periodMonthYear);
	$("#formPeriodTargetRate-isLastPeriod").val(isLastPeriod);
	$("#formPeriodTargetRate-originDatePeriodEnd").val(periodDateEnd);
	activateCounterFieldEvent();
	
	$('#container-formPeriodTargetRate').off('submit');
	$('#container-formPeriodTargetRate').on('submit', function(e) {
		e.preventDefault();
		var dataForm	=	$("#container-formPeriodTargetRate :input").serializeArray(),
			dataSend	=	{};
			
		$.each(dataForm, function() {
			dataSend[this.name] = this.value;
		});
		
		$.ajax({
			type: 'POST',
			url: baseURL+"financeDriver/reviewBonusPunishment/savePeriodTargetRate",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#container-formPeriodTargetRate :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#container-formPeriodTargetRate :input").attr("disabled", false);
				$("#formPeriodTargetRate-datePeriodStart").attr("disabled", true);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					$('#modal-formPeriodTargetRate').modal('hide');
					getDataPeriodTargetRate();
					getDataAllDriverReport();
				}
			}
		});
	});
});

reviewBonusPunishmentFunc();