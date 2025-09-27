if (userLevelMenuFunc == null){
	var userLevelMenuFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionUserLevel', 'dataUserLevel', false, function(){
				var idUserLevel	=	$('#optionUserLevel > option:selected').val();
				generateTableLevelUserMenu(idUserLevel);
			});
			
		});
	}
}
			
$('#btnSaveLevelMenu').on('click', function(e) {
	
	var userLevelText		=	$('#optionUserLevel > option:selected').text();
	
	$('#textUserLevel').html(userLevelText);
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
	
	var dataSend	=	{userLevel:$('#optionUserLevel').val(), arrIdMenu:arrIdMenu};

	$.ajax({
		type: 'POST',
		url: baseURL+"setting/userLevelMenu/saveDataLevelMenu",
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
				generateTableLevelUserMenu(response.idUserLevel);
			}
			
		}
	});
	
});

function generateTableLevelUserMenu(idUserLevel){
	
	var dataSend	=	{idUserLevel : idUserLevel};
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/userLevelMenu/getDataLevelMenu",
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
											"<label class='adomx-radio-2'><input type='radio' class='idLevelMenu' value='1' "+selectedYes+" name='radioMenu"+array.IDMENU+"'>"+
												"<i class='icon'></i> Yes"+
											"</label>"+
											"<label class='adomx-radio-2'><input type='radio' class='idLevelMenu' value='0' "+selectedNo+" name='radioMenu"+array.IDMENU+"'>"+
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

userLevelMenuFunc();