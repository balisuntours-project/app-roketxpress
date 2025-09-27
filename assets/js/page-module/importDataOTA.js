if (importDataOTAFunc == null){
	var importDataOTAFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionSourceImportDataOTA', 'dataSourceImportOTA');
			createUploaderDataReservationOTA();
		});	
	}
}

function createUploaderDataReservationOTA(){
	$('.ajax-file-upload-container').remove();
	$("#uploadReservationOTAScanningResult").addClass("d-none");
	$("#uploaderExcelReservationOTA").uploadFile({
		url: baseURL+"importDataOTA/uploadExcelReservationOTA",
		multiple:false,
		dragDrop:false,
		allowedTypes: "xls, xlsx",
		onSubmit:function(files){
			$('#window-loader').modal('show');
			$(".ajax-file-upload-container").addClass("text-center");
		},
		onSuccess:function(files,data,xhr,pd){
			$('#window-loader').modal('hide');
			$(".ajax-file-upload-statusbar").remove();
			if(data.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(data.msg);			
				});
				$('#modalWarning').modal('show');
			} else {
				$("#uploadReservationOTAScanningResult").removeClass("d-none");
				scanExcelReservationOTA(data.fileName, data.extension);
			}
		}
	});	
}

function scanExcelReservationOTA(fileName, extension){
	var idSource			=	$("#optionSourceImportDataOTA").val()
		dataSend			=	{fileName:fileName, extension:extension, idSource:idSource},
		$tableBody			=	$('#table-resultUploadExcelReservationOTA > tbody'),
		columnNumber		=	$('#table-resultUploadExcelReservationOTA > thead > tr > th').length;
		
	$.ajax({
		type: 'POST',
		url: baseURL+"importDataOTA//scanExcelReservationOTA",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$tableBody.html("<tr><td colspan='"+columnNumber+"'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
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
				var resScan	=	response.resScan,
					number	=	1,
					rows	=	"";
				$.each(resScan, function(index, array) {
					
					var excelAmount		=	array.EXCELAMOUNT,
						rowExcelAmount	=	"";
					
					if(excelAmount.length > 0){
						$.each(excelAmount, function(idxExcelAmount, arrayExcelAmount) {
							rowExcelAmount	+=	"["+arrayExcelAmount.CURRENCY+"] "+arrayExcelAmount.SETTLEMENTSTATUS+" <span class='pull-right'>"+numberFormat(arrayExcelAmount.AMOUNT)+"</span><br/>";
						});
					}
					
					rows	+=	"<tr class='trResultScanPaymentOTA' id='trResultScanPaymentOTA"+array.IDRESERVATION+"'>"+
									"<td align='right'>"+number+"</td>"+
									"<td>"+array.BOOKINGCODE+"</td>"+
									"<td>"+
										"<b>"+array.RESERVATIONTITLE+"</b><br/>"+
										"<b>"+array.CUSTOMERNAME+"</b><br/>"+
										"<b class='text-primary'>"+array.RESERVATIONDATE+"</b>"+
									"</td>"+
									"<td>"+rowExcelAmount+"</td>"+
									"<td>["+array.AMOUNTCURRENCY+"] <span class='pull-right'>"+numberFormat(array.AMOUNTDB)+"</span></td>"+
									"<td class='tdMatchStatus' data-idReservation='"+array.IDRESERVATION+"'>"+array.MATCHSTATUS+"</td>"+
									"<td class='tdPaymentStatus' data-idReservation='"+array.IDRESERVATION+"'>"+array.PAYMENTSTATUS+"</td>"+
								"</tr>";
					number++;
				});
				

			} else {
				rows	=	"<tr><td colspan='"+columnNumber+"'><center><b>No data match found</b></center></td></tr>";
				$("#uploadPaymentContainer").removeClass("d-none");
				$("#uploadPaymentScanningResult").addClass("d-none");
			}
			
			$tableBody.html(rows);
			
		}
	});
	
}

importDataOTAFunc();