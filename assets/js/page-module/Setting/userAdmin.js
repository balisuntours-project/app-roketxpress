if (userAdminFunc == null){
	var userAdminFunc	=	function(){
		$(document).ready(function () {
			var $deleteDialog	= $('#footable-confirm-delete');
			$.ajax({
				type: 'POST',
				url: baseURL+"setting/userAdmin/getDataUserAdmin",
				contentType: 'application/json',
				dataType: 'json',
				data: mergeDataSend(),
				beforeSend:function(){
					NProgress.set(0.4);
				},
				success:function(response){
					setUserToken(response);
					
					var $modal			=	$('#footable-editor-modal-userAdmin'),
						$editor			=	$('#footable-editor-userAdmin'),
						$editorTitle	=	$('#footable-editor-title-userAdmin'),
						ft				=	FooTable.init('.data-table-footable-userAdmin', {
							columns	:	response.header,
							rows	:	response.data,
							editing	:	{
								enabled: true,
								addRow: function(){
									$modal.removeData('row');
									$editor[0].reset();
									setOptionHelper('optionUserLevel', 'dataUserLevel', false);
									setOptionHelper('optionReservationType', 'dataReservationType', '1');
									$editor.find("#checkboxPartnerContact").prop('checked', false);
									$editorTitle.text('Add new data');
									$('#oldPasswordContainer').hide();
									$modal.modal('show');
								},
								editRow: function(row){
									var values 					=	row.val(),
										statusPartnerContact	=	values.statusPartnerContact,
										statusPartnerContactBool=	statusPartnerContact == 1 || statusPartnerContact == '1' ? true : false;
									
									$editor[0].reset();
									$editor.find('#nameUser').val(values.nameUser);
									$editor.find('#userEmail').val(values.email);
									$editor.find('#username').val(values.username);
									$editor.find('#whatsappNumber').val(values.partnerContactNumber.replace("+62", ""));
									$editor.find('#idUser').val(values.idUser);
									setOptionHelper('optionUserLevel', 'dataUserLevel', values.idUserLevel);
									setOptionHelper('optionReservationType', 'dataReservationType', values.idReservationType);

									if(statusPartnerContactBool){
										$editor.find("#checkboxPartnerContact").prop('checked', true);
									} else {
										$editor.find("#checkboxPartnerContact").prop('checked', false);
									}

									$('#oldPasswordContainer').show();
									$modal.data('row', row);
									$editorTitle.text('Edit data');
									$modal.modal('show');
								},
								deleteRow: function(row){
									$deleteDialog.find('#deleteBtn').attr('data-idData', row.value.idUser);
									$deleteDialog.modal('show');
								}
							}
						});
					
					$('#deleteBtn').off('click');
					$('#deleteBtn').on('click', function(e) {
						var idData		=	$deleteDialog.find('#deleteBtn').attr('data-idData'),
							dataSend	=	{idData : idData};
						
						$.ajax({
							type: 'POST',
							url: baseURL+"setting/userAdmin/deleteUserAdmin",
							contentType: 'application/json',
							dataType: 'json',
							data: mergeDataSend(dataSend),
							beforeSend:function(){
								NProgress.set(0.4);
								$deleteDialog.modal('hide');
							},
							success:function(response){
								setUserToken(response);
								NProgress.done();
								
								$('#modalWarning').on('show.bs.modal', function() {
									$('#modalWarningBody').html(response.msg);			
								});
								$('#modalWarning').modal('show');

								if(response.status == 200){
									var tr;
									$(".data-table-footable-userAdmin tr").each(function(){
										if($(this).find('td:eq(0)').html() == idData){
											tr	= $(this).find('td:eq(0)').closest("tr");
										}
									});
									var row	= FooTable.getRow(tr);
									row.delete();
								}
							}
						});
					});
					
					$editor.on('submit', function(e){
						if (this.checkValidity && !this.checkValidity()) return;
						e.preventDefault();
						
						var row					=	$modal.data('row'),
							statusPartnerContact=	$editor.find('#checkboxPartnerContact').is(":checked") ? 1 : 0
							values				=	{
								idUser: $editor.find('#idUser').val(),
								idUserLevel: $editor.find('#optionUserLevel').val(),
								idReservationType: $editor.find('#optionReservationType').val(),
								levelUserAdmin: $editor.find('#optionUserLevel').val(),
								nameUser: $editor.find('#nameUser').val(),
								email: $editor.find('#userEmail').val(),
								userEmail: $editor.find('#userEmail').val(),
								level: $editor.find('#optionUserLevel option:selected').text(),
								reservationType: $editor.find('#optionReservationType option:selected').text(),
								username: $editor.find('#username').val(),
								statusPartnerContact: statusPartnerContact,
								partnerContact: statusPartnerContact == 1 ? "Yes" : "No",
								partnerContactNumber: $editor.find('#whatsappNumber').val(),
								oldUserPassword: $editor.find('#oldUserPassword').val(),
								newUserPassword: $editor.find('#newUserPassword').val(),
								repeatUserPassword: $editor.find('#repeatUserPassword').val()
						};
									  
						if (row instanceof FooTable.Row){
							$.ajax({
								type: 'POST',
								url: baseURL+"setting/userAdmin/updateDataUserAdmin",
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
								url: baseURL+"setting/userAdmin/insertDataUserAdmin",
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
										if(response.newData == 0){
											var tr;
											$(".data-table-footable-userAdmin tr").each(function(){
												if($(this).find('td:eq(0)').html() == response.idInsert){
													tr	= $(this).find('td:eq(0)').closest("tr").remove();
												}
											});
										}

										values.idUser	= response.idInsert;
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

userAdminFunc();