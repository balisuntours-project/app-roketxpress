var $confirmDialog= $('#modal-confirm-action');
if (userProfileSettingFunc == null){
	var userProfileSettingFunc	=	function(){
		$(document).ready(function () {
			$('.summernote').summernote({
				height: 250,
				 toolbar: [
					['style', ['style']],
					['font', ['bold', 'underline', 'italic', 'clear']],
					['fontname', ['fontname']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['insert', ['link', 'picture', 'video']],
					['view', ['fullscreen', 'codeview', 'help']]
				]
			}); 
			getDetailUserProfileSetting();
		});	
	}
}

function getDetailUserProfileSetting() {
	$.ajax({
		type: 'POST',
		url: baseURL+"settingUser/detailUserProfileSetting",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(),
		beforeSend:function(){
			$("#noDataMailMessageTemplate").removeClass('d-none');
			$("#mailTemplateElem").remove();
			$('#window-loader').modal('show');
			NProgress.set(0.4);
		},
		success:function(response){
			setUserToken(response);
			NProgress.done();
			$('#window-loader').modal('hide');

			if(response.status == 200){
				var detailProfile	=	response.detailProfile,
					dataMailTemplate=	response.dataMailTemplate;
					
				$('#str-userInitialName').html(detailProfile.INITIALNAME);
				$('#str-userFullName').html(detailProfile.NAME);
				$('#str-userLevelName').html(detailProfile.LEVELNAME);
				$('#str-userEmailAddress').html(detailProfile.EMAIL);

				$('#userProfileSetting-name').val(detailProfile.NAME);
				$('#userProfileSetting-email').val(detailProfile.EMAIL);
				$('#userProfileSetting-username').val(detailProfile.USERNAME);
				
				$('#userProfileSetting-name, #userProfileSetting-email, #userProfileSetting-username, #userProfileSetting-oldPassword, #userProfileSetting-newPassword, #userProfileSetting-repeatPassword, #userProfileSetting-btnSubmitUserInformation, #userProfileSetting-btnSubmitUserCredential').prop('disabled', false);
				
				if(dataMailTemplate && dataMailTemplate.length > 0){
					var mailTemplateElem	=	'<ul class="list-group pt-20 pl-15" id="mailTemplateElem">';
					$.each(dataMailTemplate, function(index, array) {
						mailTemplateElem	+=	'<li>'+
													'<span class="list-group-item list-group-item-action mb-1">'+
														'<b class="badge badge-dark">'+array.LABEL+'</b>'+
														'<i class="fa fa-pencil pull-right ml-2" data-toggle="modal" data-target="#modal-mailTemplate" data-action="update" data-idMailMessageTemplate="'+array.IDMAILMESSAGETEMPLATE+'"></i>'+
														'<i class="fa fa-trash pull-right ml-2" onclick="confirmDeleteMailMessageTemplate('+array.IDMAILMESSAGETEMPLATE+', \''+array.LABEL+'\')"></i><br/><br/>'+
														array.CONTENT+
													'</span>'+
												'</li>';
					});
					mailTemplateElem		+=	'</div>';

					$("#noDataMailMessageTemplate").after(mailTemplateElem);
					$("#noDataMailMessageTemplate").addClass('d-none');
				}
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');
			}
		}
	});
}

$('#userProfileSetting-formUserInformation, #userProfileSetting-formUserCredential').off('submit');
$('#userProfileSetting-formUserInformation, #userProfileSetting-formUserCredential').on('submit', function(e) {
	
	e.preventDefault();				
	var dataFormInformation	=	$("#userProfileSetting-formUserInformation :input").serializeArray(),
		dataFormCredential	=	$("#userProfileSetting-formUserCredential :input").serializeArray(),
		dataSend			=	{};
		
	$.each(dataFormInformation, function() {
		dataSend[this.name] = this.value;
	});
	
	$.each(dataFormCredential, function() {
		dataSend[this.name] = this.value;
	});
	$("#userProfileSetting-formUserInformation :input, #userProfileSetting-formUserCredential :input").attr("disabled", true);
	
	$.ajax({
		type: 'POST',
		url: baseURL+"settingUser/saveSetting",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$('#window-loader').modal('show');
			NProgress.set(0.4);
		},
		success:function(response){
			
			$("#userProfileSetting-formUserInformation :input, #userProfileSetting-formUserCredential :input").attr("disabled", false);
			setUserToken(response);
			NProgress.done();
			$('#window-loader').modal('hide');

			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				if(response.urlLogout != ''){
					window.location.replace(urlLogout);
				} else {
					$('#spanNameUser, #linkNameUser').html(response.name);
					$('#linkEmailUser').html(response.email);
					getDetailUserProfileSetting();
				}
			}
			
		}
	});
	
});

$('#modal-mailTemplate').off('show.bs.modal');
$('#modal-mailTemplate').on('show.bs.modal', function(event) {
    var actionType	=	$(event.relatedTarget).attr('data-action');

	$('input[name="templateType"][value="0"]').prop('checked', true);
	$("#templateName").val("");
	$("#templateContent").summernote("code", "");
	$("#idMailMessageTemplate").val(0);
	$('#actionType').val("insertMailTemplate");

	if(actionType != "insert"){
		var idMailMessageTemplate	=	$(event.relatedTarget).attr('data-idMailMessageTemplate'),
			dataSend				=	{idMailMessageTemplate:idMailMessageTemplate};

		$.ajax({
			type: 'POST',
			url: baseURL+"settingUser/detailMailMessageTemplate",
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
					var detailData		=	response.detailData;

					$('input[name="templateType"][value="'+detailData.STATUSSIGNATURE+'"]').prop('checked', true);
					$("#templateName").val(detailData.LABEL);
					$("#templateContent").summernote("code", detailData.CONTENT);
					$("#idMailMessageTemplate").val(idMailMessageTemplate);
					$('#actionType').val("updateMailTemplate");
				} else {
					$('#modalWarning').on('show.bs.modal', function() {
						$('#modalWarningBody').html(response.msg);			
					});
					$('#modalWarning').modal('show');
					$('#modal-mailTemplate').modal('hide');
				}					
			}
		});
	}
});

function setTemplateSignature(isSignature){
	$("#templateName").val("").prop('readonly', false);
	if(isSignature) $("#templateName").val("Signature").prop('readonly', true);
}

$('#editor-mailTemplate').off('submit');
$('#editor-mailTemplate').on('submit', function(e) {
	e.preventDefault();
	var dataForm	=	$("#editor-mailTemplate :input").serializeArray(),
		functionUrl	=	$("#editor-mailTemplate #actionType").val(),
		dataSend	=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"settingUser/"+functionUrl,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-mailTemplate :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-mailTemplate :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-mailTemplate').modal('hide');
				getDetailUserProfileSetting();
			}		
		}
	});
});

function confirmDeleteMailMessageTemplate(idMailMessageTemplate, templateName){
	var confirmText	=	'Your mail template will be deleted. Details;'+
						'<div class="order-details-customer-info mt-10 mb-10">'+
							'<ul class="ml-5">'+
								'<li> <span>Name/Label</span> <span>'+templateName+'</span> </li>'+
							'</ul>'+
						'</div>'+
						'Are you sure?';
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idMailMessageTemplate).attr('data-function', "deleteMailTemplate");
	$confirmDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData	=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
		funcName=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		dataSend=	{idData:idData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"settingUser/"+funcName,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$confirmDialog.modal('hide');
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
				getDetailUserProfileSetting();
			}
		}
	});
});

userProfileSettingFunc();