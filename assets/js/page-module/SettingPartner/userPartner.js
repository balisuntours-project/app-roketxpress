if (userPartnerFunc == null){
	var userPartnerFunc	=	function(){
		$(document).ready(function () {
			var $deleteDialog	= $('#footable-confirm-delete');
			$.ajax({
				type: 'POST',
				url: baseURL+"settingPartner/userPartner/getDataUserPartner",
				contentType: 'application/json',
				dataType: 'json',
				data: mergeDataSend(),
				beforeSend:function(){
					NProgress.set(0.4);
				},
				success:function(response){
					setUserToken(response);
					
					var $modal			= $('#footable-editor-modal-userPartner'),
						$editor			= $('#footable-editor-userPartner'),
						$editorTitle	= $('#footable-editor-title-userPartner'),
						ft				= FooTable.init('.data-table-footable-userPartner', {
											columns	:	response.header,
											rows	:	response.data,
											editing	:	{
												enabled: true,
												addRow: function(){
													$modal.removeData('row');
													setOptionHelper('optionUserLevel', 'dataUserPartnerLevel', false);
													setOptionHelper('optionPartner', 'dataPartner', false);
													$editor.find('#idUserPartner').val("");
													$editor[0].reset();
													$editorTitle.text('Add new data');
													$('#oldPasswordContainer').hide();
													$modal.modal('show');
												},
												editRow: function(row){
													var values = row.val();

													$editor[0].reset();
													$editor.find('#nameUser').val(values.nameUser);
													$editor.find('#userEmail').val(values.email);
													$editor.find('#username').val(values.username);
													$editor.find('#idUserPartner').val(values.idUserPartner);
													setOptionHelper('optionUserLevel', 'dataUserPartnerLevel', values.idUserLevel);
													setOptionHelper('optionPartner', 'dataPartner', values.idPartner);

													$('#oldPasswordContainer').show();
													$modal.data('row', row);
													$editorTitle.text('Edit data');
													$modal.modal('show');
												},
												deleteRow: function(row){
													$deleteDialog.find('#deleteBtn').attr('data-idData', row.value.idUserPartner);
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
							url: baseURL+"settingPartner/userPartner/deleteUserPartner",
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
									$(".data-table-footable-userPartner tr").each(function(){
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
						
						var row		= $modal.data('row'),
							values	= {
										idUserPartner: $editor.find('#idUserPartner').val(),
										idPartner: $editor.find('#optionPartner').val(),
										idUserLevel: $editor.find('#optionUserLevel').val(),
										levelUserPartner: $editor.find('#optionUserLevel').val(),
										partnerName: $editor.find('#optionPartner option:selected').text(),
										nameUser: $editor.find('#nameUser').val(),
										email: $editor.find('#userEmail').val(),
										userEmail: $editor.find('#userEmail').val(),
										level: $editor.find('#optionUserLevel option:selected').text(),
										username: $editor.find('#username').val(),
										oldUserPassword: $editor.find('#oldUserPassword').val(),
										newUserPassword: $editor.find('#newUserPassword').val(),
										repeatUserPassword: $editor.find('#repeatUserPassword').val()
									  };
									  
						if (row instanceof FooTable.Row){
							$.ajax({
								type: 'POST',
								url: baseURL+"settingPartner/userPartner/updateDataUserPartner",
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
								url: baseURL+"settingPartner/userPartner/insertDataUserPartner",
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
											$(".data-table-footable-userPartner tr").each(function(){
												if($(this).find('td:eq(0)').html() == response.idInsert){
													tr	= $(this).find('td:eq(0)').closest("tr").remove();
												}
											});
										}

										values.idUserPartner	= response.idInsert;
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

userPartnerFunc();