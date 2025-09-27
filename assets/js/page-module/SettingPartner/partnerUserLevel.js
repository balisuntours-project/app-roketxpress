if (partnerUserLevelFunc == null){
	var partnerUserLevelFunc	=	function(){
		$(document).ready(function () {
			var $deleteDialog	= $('#footable-confirm-delete');
			$.ajax({
				type: 'POST',
				url: baseURL+"settingPartner/partnerUserLevel/getDataUserLevel",
				contentType: 'application/json',
				dataType: 'json',
				data: mergeDataSend(),
				beforeSend:function(){
					NProgress.set(0.4);
				},
				success:function(response){
					setUserToken(response);
					
					var $modal			= $('#footable-editor-modal-partnerUserLevel'),
						$editor			= $('#footable-editor-partnerUserLevel'),
						$editorTitle	= $('#footable-editor-title-partnerUserLevel'),
						ft				= FooTable.init('.data-table-footable-partnerUserLevel', {
											columns	:	response.header,
											rows	:	response.data,
											editing	:	{
												enabled: true,
												addRow: function(){
													$modal.removeData('row');
													$editor[0].reset();
													$editorTitle.text('Add new data');

													$("input[value='1']").prop('checked', false);
													$("input[value='2']").prop('checked', false);

													$modal.modal('show');
												},
												editRow: function(row){
													var values = row.val();
													$editor.find('#levelName').val(values.levelName);
													$editor.find('#idUserLevelPartner').val(values.idUserLevelPartner);
													
													var checkedNotifSchedule	=	values.notifScheduleValue == 1 ? true : false;
													var checkedNotifFinance		=	values.notifFinanceValue == 1 ? true : false;
													
													$("input.cbNotificationType[value='1']").prop('checked', checkedNotifSchedule);
													$("input.cbNotificationType[value='2']").prop('checked', checkedNotifFinance);

													$modal.data('row', row);
													$editorTitle.text('Edit data');
													$modal.modal('show');
												},
												allowDelete: false
											}
										});
					
					$editor.on('submit', function(e){
						if (this.checkValidity && !this.checkValidity()) return;
						e.preventDefault();
						
						var values,
							notifScheduleValue	=	$("input.cbNotificationType[value='1']").prop('checked') == true ? 1 : 0,
							notifFinanceValue	=	$("input.cbNotificationType[value='2']").prop('checked') == true ? 1 : 0,
							notifScheduleText	=	$("input.cbNotificationType[value='1']").prop('checked') == true ? "Yes" : "No",
							notifFinanceText	=	$("input.cbNotificationType[value='2']").prop('checked') == true ? "Yes" : "No";
						var row		= $modal.data('row'),
							values	= {
										levelName: $editor.find('#levelName').val(),
										idUserLevelPartner: $editor.find('#idUserLevelPartner').val(),
										notifScheduleValue: notifScheduleValue,
										notifFinanceValue: notifFinanceValue,
										notifScheduleText: notifScheduleText,
										notifFinanceText: notifFinanceText
									  };

						if (row instanceof FooTable.Row){
							$.ajax({
								type: 'POST',
								url: baseURL+"settingPartner/partnerUserLevel/updateDataUserLevel",
								contentType: 'application/json',
								dataType: 'json',
								data: mergeDataSend(values),
								beforeSend:function(){
									NProgress.set(0.4);
								},
								success:function(response){
									setUserToken(response);
									NProgress.done();
									
									$('#modalWarning').on('show.bs.modal', function() {
										$('#modalWarningBody').html(response.msg);			
									});
									$('#modalWarning').modal('show');

									if(response.status == 200){
										row.val(values);
										$modal.modal('hide');
									}
								}
							});
						} else {
							$.ajax({
								type: 'POST',
								url: baseURL+"settingPartner/partnerUserLevel/insertDataUserLevel",
								contentType: 'application/json',
								dataType: 'json',
								data: mergeDataSend(values),
								beforeSend:function(){
									NProgress.set(0.4);
								},
								success:function(response){
									setUserToken(response);
									NProgress.done();
									
									$('#modalWarning').on('show.bs.modal', function() {
										$('#modalWarningBody').html(response.msg);			
									});
									$('#modalWarning').modal('show');

									if(response.status == 200){
										values.idUserLevel	= response.idInsert;
										ft.rows.add(values);
										$modal.modal('hide');
									}
								}
							});
						}
					});
				}
			});
			
		});
	}
}

partnerUserLevelFunc();