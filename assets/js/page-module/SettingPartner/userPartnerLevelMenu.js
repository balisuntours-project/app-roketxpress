if (userPartnerLevelMenuFunc == null){
	var userPartnerLevelMenuFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionUserPartnerLevel', 'dataUserPartnerLevel', false, function(){
				var idUserPartnerLevel	=	$('#optionUserPartnerLevel > option:selected').val();
				generateTableLevelUserPartnerMenu(idUserPartnerLevel);
			});
		});
	}
}
			
$('#btnSaveLevelMenu').on('click', function(e) {
	var userPartnerLevelText		=	$('#optionUserPartnerLevel > option:selected').text();
	$('#textUserPartnerLevel').html(userPartnerLevelText);
	$('#levelmenu-confirm-save').modal('show');
});

$('#btnConfirmSaveLevelMenu').off('click');
$('#btnConfirmSaveLevelMenu').on('click', function(e) {
	
	$('#levelmenu-confirm-save').modal('hide');
	
	var arrIdMenu	=	{};
	$('.idLevelMenu:radio:checked').each(function() {
		var $this			=	$(this);
		var arrName			=	$this.attr('name').replace("radioMenu", "");
		arrIdMenu[arrName]	=	$this.val();
	});
	
	var dataSend	=	{userPartnerLevel:$('#optionUserPartnerLevel').val(), arrIdMenu:arrIdMenu};

	$.ajax({
		type: 'POST',
		url: baseURL+"settingPartner/userPartnerLevelMenu/saveDataLevelMenu",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);			
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				generateTableLevelUserPartnerMenu(response.idUserPartnerLevel);
			}
			
		}
	});
	
});

function generateTableLevelUserPartnerMenu(idUserPartnerLevel){
	
	var dataSend	=	{idUserPartnerLevel : idUserPartnerLevel};
	$.ajax({
		type: 'POST',
		url: baseURL+"settingPartner/userPartnerLevelMenu/getDataLevelMenu",
		contentType: 'application/json',
		dataType: 'json',
		beforeSend:function(){
			$('#bodyLevelMenu').html("<tr><td colspan='3'><center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center></td></tr>");
		},
		data: mergeDataSend(dataSend),
		success:function(response){
			
			setUserToken(response);
			var data	=	response.data,
				rowMenu	=	"";
				
			if(data == undefined || data == null || data.length == 0){
				rowMenu	=	"<tr><td colspan='3' align='center'>No data found</td></tr>";
			} else {
				var arrGroupname	=	[];
				$.each(data, function(index, array) {
					
					var displayName	=	array.GROUPNAME == array.DISPLAYNAME ? "" : array.DISPLAYNAME,
						groupName	=	array.GROUPNAME,
						selectedYes = selectedNo = "";
						
					if(array.OPEN == "0"){
						selectedNo	=	"checked";
					} else {
						selectedYes	=	"checked";
					}
					
					var radioMenu	=	"<div class='adomx-checkbox-radio-group inline'>"+
											"<label class='adomx-radio-2'><input type='radio' class='idLevelMenu' value='1' "+selectedYes+" name='radioMenu"+array.IDMENUPARTNER+"'>"+
												"<i class='icon'></i> Yes"+
											"</label>"+
											"<label class='adomx-radio-2'><input type='radio' class='idLevelMenu' value='0' "+selectedNo+" name='radioMenu"+array.IDMENUPARTNER+"'>"+
												"<i class='icon'></i> No"+
											"</label>"+
										"</div>";
					
					if(arrGroupname.includes(groupName)){
						groupName		=	"";
					} else {
						arrGroupname.push(array.GROUPNAME);
					}
					
					rowMenu	+=	"<tr>"+
									"<td>"+groupName+"</td>"+
									"<td>"+displayName+"</td>"+
									"<td>"+radioMenu+"</td>"+
								"</tr>";
				});
				
			}
			
			$('#bodyLevelMenu').html(rowMenu);
			
		}
	});
}

userPartnerLevelMenuFunc();