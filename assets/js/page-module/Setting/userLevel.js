if (userLevelFunc == null){
	var userLevelFunc	=	function(){
		$(document).ready(function () {
			var $deleteDialog	= $('#footable-confirm-delete');
			$.ajax({
				type: 'POST',
				url: baseURL+"setting/userLevel/getDataUserLevel",
				contentType: 'application/json',
				dataType: 'json',
				data: mergeDataSend(),
				beforeSend:function(){
					NProgress.set(0.4);
				},
				success:function(response){
					setUserToken(response);
					
					var $modal			= $('#footable-editor-modal-userLevel'),
						$editor			= $('#footable-editor-userLevel'),
						$editorTitle	= $('#footable-editor-title-userLevel'),
						ft				= FooTable.init('.data-table-footable-userLevel', {
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
													$("input[value='3']").prop('checked', false);
													$("input[value='4']").prop('checked', false);
													$("input[value='5']").prop('checked', false);
													$("input[value='6']").prop('checked', false);
													$("input[value='7']").prop('checked', false);

													$("input[value='PMSADDDRIVERSCHEDULE']").prop('checked', false);
													$("input[value='PMSDELETEDRIVERSCHEDULE']").prop('checked', false);
													$modal.modal('show');
												},
												editRow: function(row){
													var values = row.val();
													$editor.find('#levelName').val(values.levelName);
													$editor.find('#notes').val(values.notes);
													$editor.find('#idUserLevel').val(values.idUserLevel);
													
													var checkedNotifMail			=	values.notifMailValue == 1 ? true : false;
													var checkedNotifReservation		=	values.notifReservationValue == 1 ? true : false;
													var checkedNotifScheduleDriver	=	values.notifScheduleDriverValue == 1 ? true : false;
													var checkedNotifScheduleCar		=	values.notifScheduleCarValue == 1 ? true : false;
													var checkedNotifAdditionalCost	=	values.notifAdditionalCostValue == 1 ? true : false;
													var checkedNotifAdditionalIncome=	values.notifAdditionalIncomeValue == 1 ? true : false;
													var checkedNotifFinance			=	values.notifFinanceValue == 1 ? true : false;
													var checkedAddDriverSchedule	=	values.PMSADDDRIVERSCHEDULE == 1 ? true : false;
													var checkedDeleteDriverSchedule	=	values.PMSDELETEDRIVERSCHEDULE == 1 ? true : false;
													
													$("input.cbNotificationType[value='1']").prop('checked', checkedNotifMail);
													$("input.cbNotificationType[value='2']").prop('checked', checkedNotifReservation);
													$("input.cbNotificationType[value='3']").prop('checked', checkedNotifScheduleDriver);
													$("input.cbNotificationType[value='4']").prop('checked', checkedNotifScheduleCar);
													$("input.cbNotificationType[value='5']").prop('checked', checkedNotifAdditionalCost);
													$("input.cbNotificationType[value='6']").prop('checked', checkedNotifFinance);
													$("input.cbNotificationType[value='7']").prop('checked', checkedNotifAdditionalIncome);

													$("input.cbActionPermission[value='PMSADDDRIVERSCHEDULE']").prop('checked', checkedAddDriverSchedule);
													$("input.cbActionPermission[value='PMSDELETEDRIVERSCHEDULE']").prop('checked', checkedDeleteDriverSchedule);
													
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
							notifMailValue				=	$("input.cbNotificationType[value='1']").prop('checked') == true ? 1 : 0,
							notifReservationValue		=	$("input.cbNotificationType[value='2']").prop('checked') == true ? 1 : 0,
							notifScheduleDriverValue	=	$("input.cbNotificationType[value='3']").prop('checked') == true ? 1 : 0,
							notifScheduleCarValue		=	$("input.cbNotificationType[value='4']").prop('checked') == true ? 1 : 0,
							notifAdditionalCostValue	=	$("input.cbNotificationType[value='5']").prop('checked') == true ? 1 : 0,
							notifFinanceValue			=	$("input.cbNotificationType[value='6']").prop('checked') == true ? 1 : 0,
							notifAdditionalIncomeValue	=	$("input.cbNotificationType[value='7']").prop('checked') == true ? 1 : 0,
							notifMailText				=	$("input.cbNotificationType[value='1']").prop('checked') == true ? "Yes" : "No",
							notifReservationText		=	$("input.cbNotificationType[value='2']").prop('checked') == true ? "Yes" : "No",
							notifScheduleDriverText		=	$("input.cbNotificationType[value='3']").prop('checked') == true ? "Yes" : "No",
							notifScheduleCarText		=	$("input.cbNotificationType[value='4']").prop('checked') == true ? "Yes" : "No",
							notifAdditionalCostText		=	$("input.cbNotificationType[value='5']").prop('checked') == true ? "Yes" : "No",
							notifFinanceText			=	$("input.cbNotificationType[value='6']").prop('checked') == true ? "Yes" : "No",
							notifAdditionalIncomeText	=	$("input.cbNotificationType[value='7']").prop('checked') == true ? "Yes" : "No",
							PMSADDDRIVERSCHEDULE		=	$("input.cbActionPermission[value='PMSADDDRIVERSCHEDULE']").prop('checked') == true ? 1 : 0,
							PMSDELETEDRIVERSCHEDULE		=	$("input.cbActionPermission[value='PMSDELETEDRIVERSCHEDULE']").prop('checked') == true ? 1 : 0;
						var row		= $modal.data('row'),
							values	= {
										levelName: $editor.find('#levelName').val(),
										notes: $editor.find('#notes').val(),
										idUserLevel: $editor.find('#idUserLevel').val(),
										notifMailValue: notifMailValue,
										notifReservationValue: notifReservationValue,
										notifScheduleDriverValue: notifScheduleDriverValue,
										notifScheduleCarValue: notifScheduleCarValue,
										notifAdditionalCostValue: notifAdditionalCostValue,
										notifAdditionalIncomeValue: notifAdditionalIncomeValue,
										notifFinanceValue: notifFinanceValue,
										notifMailText: notifMailText,
										notifReservationText: notifReservationText,
										notifScheduleDriverText: notifScheduleDriverText,
										notifScheduleCarText: notifScheduleCarText,
										notifAdditionalCostText: notifAdditionalCostText,
										notifAdditionalIncomeText: notifAdditionalIncomeText,
										notifFinanceText: notifFinanceText,
										PMSADDDRIVERSCHEDULE: PMSADDDRIVERSCHEDULE,
										PMSDELETEDRIVERSCHEDULE: PMSDELETEDRIVERSCHEDULE
									  };

						if (row instanceof FooTable.Row){
							$.ajax({
								type: 'POST',
								url: baseURL+"setting/userLevel/updateDataUserLevel",
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
										clearAppData(false);
										$modal.modal('hide');
									}
								}
							});
						} else {
							$.ajax({
								type: 'POST',
								url: baseURL+"setting/userLevel/insertDataUserLevel",
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
										clearAppData(false);
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

userLevelFunc();