var $confirmDeleteDialog= $('#modal-confirm-action');
var $updateStatusDialog	= $('#modal-confirm-action');

if (selfDriveSettingFunc == null){
	var selfDriveSettingFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionCarTypeFeeFilter', 'dataCarType');
			setOptionHelper('optionCarTypeFeeEditor', 'dataCarType');
			setOptionHelper('optionCarTypeEditor', 'dataCarType');
			setOptionHelper('optionVendorFeeEditor', 'dataVendorCar');
			setOptionHelper('optionVendorCarFilter', 'dataVendorCar');
			setOptionHelper('optionVendorCarEditor', 'dataVendorCar');
			getDataSelfDriveFees();
			getDataVendorCar();
		});	
	}
}

$('#optionCarTypeFeeFilter').off('change');
$('#optionCarTypeFeeFilter').on('change', function(e) {
	getDataSelfDriveFees();
});

$('#optionVendorCarFilter').off('change');
$('#optionVendorCarFilter').on('change', function(e) {
	getDataVendorCar();
});

$('#keywordSearchCarList').off('keydown');
$('#keywordSearchCarList').on('keydown', function(e) {
	if(e.which === 13){
		getDataVendorCar();
	}
});

function getDataSelfDriveFees(){
	
	var $tableBody	=	$('#table-selfDriveFee > tbody'),
		columnNumber=	$('#table-selfDriveFee > thead > tr > th').length,
		carType		=	$('#optionCarTypeFeeFilter').val(),
		dataSend	=	{carType:carType};
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/selfDrive/getDataSelfDriveFees",
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
			
			if(response.status != 200){
				$tableBody.html("<tr><td colspan='"+columnNumber+"'><center>No data found</center></td></tr>");
			} else {

				var data	=	response.result,
					rows	=	"",
					number	=	1;
				
				$.each(data, function(index, array) {
					
					var btnEdit		=	'<button class="button button-xs button-box button-primary" onclick="getDetailSelfDriveFee('+array.IDCARSELFDRIVEFEE+')">'+
											'<i class="fa fa-pencil"></i>'+
										'</button>';
					var btnDelete	=	'<button class="button button-xs button-box button-danger" onclick="confirmDeleteDataSelfDriveFee('+array.IDCARSELFDRIVEFEE+', \''+array.VENDORNAME+'\', \''+array.CARTYPE+'\', '+array.DURATION+')">'+
											'<i class="fa fa-trash"></i>'+
										'</button>';
					rows			+=	"<tr>"+
											"<td align='right'>"+number+"</td>"+
											"<td>"+array.VENDORNAME+"</td>"+
											"<td>"+array.CARTYPE+"</td>"+
											"<td align='right'>"+array.DURATION+"</td>"+
											"<td align='right'>"+numberFormat(array.NOMINALFEE)+"</td>"+
											"<td>"+array.NOTES+"</td>"+
											"<td align='center'>"+btnEdit+" "+btnDelete+"</td>"+
										"</tr>";
					number++;
					
				});
				
				$tableBody.html(rows);
			
			}
			
		}
		
	});
	
}

$('#modal-editor-selfDriveFee').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  
  if ($activeElement.is('[data-toggle]')) {
    if (event.type === 'show') {
      if($activeElement.attr('data-action') == "insert"){
		  $("#notes").val("");
		  $("#nominalFee, #idCarSelfDriveFee").val(0);
		  $("#optionVendorFeeEditor").val($("#optionVendorFeeEditor option:first").val());
		  $("#optionCarTypeFeeEditor").val($("#optionCarTypeFeeEditor option:first").val());
		  $("#optionDurationFeeEditor").val($("#optionDurationFeeEditor option:first").val());
	  }
    }
  }

});

$('#editor-selfDriveFee').off('submit');
$('#editor-selfDriveFee').on('submit', function(e) {
	
	e.preventDefault();
	var idCarSelfDriveFee	=	$("#idCarSelfDriveFee").val(),
		actionURL			=	idCarSelfDriveFee == 0 ? "addSelfDriveFee" : "updateSelfDriveFee";
		dataForm			=	$("#editor-selfDriveFee :input").serializeArray(),
		dataSend			=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/selfDrive/"+actionURL,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-selfDriveFee :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-selfDriveFee :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-editor-selfDriveFee').modal('hide');
				getDataSelfDriveFees();
			}
			
		}
	});
});

function getDetailSelfDriveFee(idCarSelfDriveFee){
	
	var dataSend		=	{idCarSelfDriveFee:idCarSelfDriveFee};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/selfDrive/detailSelfDriveFee",
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
				var detailData		=	response.dataDetail;

				$("#optionVendorFeeEditor").val(detailData.IDVENDOR);
				$("#optionCarTypeFeeEditor").val(detailData.IDCARTYPE);
				$("#optionDurationFeeEditor").val(detailData.DURATION);
				$("#nominalFee").val(numberFormat(detailData.NOMINALFEE));
				$("#notes").val(detailData.NOTES);
				$("#idCarSelfDriveFee").val(idCarSelfDriveFee);
				$('#modal-editor-selfDriveFee').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
	
}

function confirmDeleteDataSelfDriveFee(idCarSelfDriveFee, vendorName, carType, duration){
	
	var confirmText	=	'The car fee data will be deleted. Details ;<br/><br/>Vendor : <b>'+vendorName+'</b><br/>Car Type : <b>'+carType+'</b><br/>Duration : <b>'+duration+' hours</b>.<br/><br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idCarSelfDriveFee).attr('data-function', "deleteSelfDriveFee");
	$confirmDeleteDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var idData	=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData'),
		funcName=	$confirmDeleteDialog.find('#confirmBtn').attr('data-function'),
		dataSend=	{idData:idData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/selfDrive/"+funcName,
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
				if(funcName == "updateStatusVendorCar"){
					getDataVendorCar();
				} else {
					getDataSelfDriveFees();
				}
			}
		}
	});
});

function getDataVendorCar(){
	
	var $tableBody		=	$('#table-vendorCar > tbody'),
		columnNumber	=	$('#table-vendorCar > thead > tr > th').length,
		idVendor		=	$('#optionVendorCarFilter').val(),
		keywordSearch	=	$('#keywordSearchCarList').val(),
		dataSend		=	{idVendor:idVendor, keywordSearch:keywordSearch};
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/selfDrive/getDataVendorCar",
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
			var dataDriverNoCar	=	response.dataDriverNoCar;
			updateDataOptionHelper('dataDriverNoCar', dataDriverNoCar);
			
			if(response.status != 200){
				$tableBody.html("<tr><td colspan='"+columnNumber+"'><center>No data found</center></td></tr>");
			} else {

				var data	=	response.result,
					rows	=	"",
					number	=	1;
				
				$.each(data, function(index, array) {
					
					var status			=	btnStatusUpdate = transmission	=	"",
						costFeeListElem	=	"-",
						costFeeList		=	array.CARCOSTFEELIST,
						costFeeList		=	costFeeList == "" || costFeeList == null || costFeeList == "null" ? "" : costFeeList,
						arrCostFeeList	=	costFeeList.split('|');
						
					switch(array.TRANSMISSION){
						case "1"	:	transmission	=	'<span class="badge badge-info">Manual</span>'; break;
						case "2"	:	transmission	=	'<span class="badge badge-primary">Automatic</span>'; break;
					}

					switch(array.STATUS){
						case "1"	:	status			=	'<span class="badge badge-success">Active</span>';
										btnStatusUpdate	=	'<button class="button button-xs button-box button-warning" onclick="updateStatusVendorCar('+array.IDCARVENDOR+', -1, \''+array.VENDORNAME+'\', \''+array.BRAND+'\', \''+array.MODEL+'\', \''+array.PLATNUMBER+'\')">'+
																'<i class="fa fa-eye-slash"></i>'+
															'</button>';
										break;
						case "-1"	:	
						default		:	status			=	'<span class="badge badge-danger">Inactive</span>';
										btnStatusUpdate	=	'<button class="button button-xs button-box button-warning" onclick="updateStatusVendorCar('+array.IDCARVENDOR+', 1, \''+array.VENDORNAME+'\', \''+array.BRAND+'\', \''+array.MODEL+'\', \''+array.PLATNUMBER+'\')">'+
																'<i class="fa fa-eye"></i>'+
															'</button>';
										break;
					}
					
					var btnEdit		=	'<button class="button button-xs button-box button-primary" onclick="getDetailVendorCar('+array.IDCARVENDOR+')">'+
											'<i class="fa fa-pencil"></i>'+
										'</button>';

					if(arrCostFeeList.length > 0 && costFeeList != ""){
						costFeeListElem	=	"";
						for(var i=0; i<arrCostFeeList.length; i++){
							var splitCostFee	=	arrCostFeeList[i].split('=');
							costFeeListElem		+=	splitCostFee[0]+numberFormat(splitCostFee[1])+"<br/>";
						}
					}
										
					rows			+=	"<tr>"+
											"<td align='right'>"+number+"</td>"+
											"<td>"+array.VENDORNAME+"</td>"+
											"<td>"+array.DRIVERNAME+"</td>"+
											"<td>"+array.CARTYPE+"<br/>Brand : "+array.BRAND+"<br/>Model : "+array.MODEL+"<br/>Year : "+array.YEAR+"<br/>Color : "+array.COLOR+"</td>"+
											"<td>"+array.PLATNUMBER+"</td>"+
											"<td>"+transmission+"</td>"+
											"<td>"+costFeeListElem+"</td>"+
											"<td>"+array.DESCRIPTION+"</td>"+
											"<td>"+status+"</td>"+
											"<td align='center'>"+btnEdit+btnStatusUpdate+"</td>"+
										"</tr>";
					number++;
					
				});
				
				$tableBody.html(rows);
			
			}
			
		}
		
	});
	
}

$('#modal-editor-vendorCar').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  
	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {
			if($activeElement.attr('data-action') == "insert"){
				setOptionHelper('optionDriverCarEditor', 'dataDriverNoCar');
				$("#brandVendorCar, #modelVendorCar, #platNumberVendorCar, #colorVendorCar, #description").val("");
				$("#yearVendorCar").val(2000);
				$("#optionVendorCarEditor").val($("#optionVendorCarEditor option:first").val());
				$("#optionCarTypeEditor").val($("#optionCarTypeEditor option:first").val());
				$("#optionTransmissionVendorCar").val($("#optionTransmissionVendorCar option:first").val());
				$("#actionType").val("addVendorCar");
				$("#idCarVendor").val("0");
			}
		}
	}
});

$('#editor-vendorCar').off('submit');
$('#editor-vendorCar').on('submit', function(e) {
	
	e.preventDefault();
	var idCarVendor	=	$("#idCarVendor").val(),
		actionURL	=	idCarVendor == 0 ? "addVendorCar" : "updateVendorCar";
		dataForm	=	$("#editor-vendorCar :input").serializeArray(),
		dataSend	=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/selfDrive/"+actionURL,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-vendorCar :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-vendorCar :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-editor-vendorCar').modal('hide');
				getDataVendorCar();
			}
			
		}
	});
});

function getDetailVendorCar(idCarVendor){
	
	var dataSend		=	{idCarVendor:idCarVendor};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/selfDrive/detailVendorCar",
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
				var detailData		=	response.dataDetail,
					dataDriverNoCar	=	response.dataDriverNoCar;

				updateDataOptionHelper('dataDriverNoCar', dataDriverNoCar);
				setOptionHelper('optionDriverCarEditor', 'dataDriverNoCar');

				$("#optionVendorCarEditor").val(detailData.IDVENDOR);
				$("#optionCarTypeEditor").val(detailData.IDCARTYPE);
				$("#optionDriverCarEditor").val(detailData.IDDRIVER);
				$("#brandVendorCar").val(detailData.BRAND);
				$("#modelVendorCar").val(detailData.MODEL);
				$("#optionTransmissionVendorCar").val(detailData.TRANSMISSION);
				$("#yearVendorCar").val(detailData.YEAR);
				$("#platNumberVendorCar").val(detailData.PLATNUMBER);
				$("#colorVendorCar").val(detailData.COLOR);
				$("#description").val(detailData.DESCRIPTION);
				$("#idCarVendor").val(idCarVendor);
				$('#modal-editor-vendorCar').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
	
}

function updateStatusVendorCar(idData, status, vendorName, brandName, model, platNumber){
	
	var strStatus	=	status == 1 ? "activate" : "deactivate";
	var confirmText	=	'The vendor car <b>'+brandName+' '+model+' - '+platNumber+'</b> owned by <b>'+vendorName+'</b> will be <b>'+strStatus+'</b>.<br/><br/>Are you sure?';
	
	$updateStatusDialog.find('#modal-confirm-body').html(confirmText);
	$updateStatusDialog.find('#confirmBtn').attr('data-idData', idData+'|'+status).attr('data-function', "updateStatusVendorCar");
	$updateStatusDialog.modal('show');
	
}

selfDriveSettingFunc();