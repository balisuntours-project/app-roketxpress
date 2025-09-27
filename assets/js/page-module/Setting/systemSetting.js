if (systemSettingFunc == null){
	var systemSettingFunc	=	function(){
		$(document).ready(function () {
			getDataSystemSetting();
		});	
	}
}

$('#btnRefreshSystemSettings').off('click');
$('#btnRefreshSystemSettings').on('click', function(e) {
	e.preventDefault();
	getDataSystemSetting();
});

function getDataSystemSetting(){
	
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/systemSetting/getDataSystemSetting",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			NProgress.done();
			$('#window-loader').modal('hide');
			setUserToken(response);
							
			if(response.status == 200){
				
				var dataSetting	=	response.dataSetting;
				$.each(dataSetting, function(index, array) {
					
					var idVariable	=	array.IDSYSTEMSETTINGVARIABLE;
					$("#variableName"+idVariable).html(array.VARIABLENAME);
					$("#variableDescription"+idVariable).html(array.DESCRIPTION);
					$("#dataInput"+idVariable).val(array.VALUE);
					
				});
								
			}
			
		}

	});
	
}

$('#btnSaveSystemSettings').off('click');
$('#btnSaveSystemSettings').on('click', function(e) {
	
	e.preventDefault();
	var arrSystemSettingVar	=	[];
	
	$('.dataInput').each(function() {
		var idSystemSettingVar		=	$(this).attr('data-idSetting'),
			valueSystemSettingVar	=	$("#dataInput"+idSystemSettingVar).val();
		arrSystemSettingVar.push([idSystemSettingVar, valueSystemSettingVar]);
	});
		
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/systemSetting/saveDataSystemSetting",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend({arrSystemSettingVar:arrSystemSettingVar}),
		beforeSend:function(){
			$(".dataInput").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$(".dataInput").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');
			
		}
	});
});

systemSettingFunc();