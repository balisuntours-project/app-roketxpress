var $deleteDialog		= $('#footable-confirm-delete');
var $confirmationDialog	= $('#modal-confirm-action');
var arrOptionHelperName	= {masterSource:["dataSource"], masterProduct:["dataProductTicket"], masterDriver:["dataDriver"], masterVendor:["dataVendorCar", "dataVendorTicket"], masterCarType:["dataCarType"]};

if (dataMasterFunc == null){
	var dataMasterFunc	=	function(){
		$(document).ready(function () {
			activateTab('masterSource');
			$('a.tabMaster[data-toggle="tab"]').off('shown.bs.tab');
			$('a.tabMaster[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				var tableName			= $(e.target).attr("data-table");
				
				$("#keywordSearch").val("");
				$("#btnUpdateLastWithdraw").addClass('d-none');
				if (levelUser == 1) $("#btnUpdateLastWithdraw").removeClass('d-none');
				if (typeof tableName !== 'undefined') activateTab(tableName);				
			});
		});	
	}
}

function generateDataTable(page){
	var tableName	=	$('#tabsPanel > li.nav-item > a.nav-link.active').attr("data-table");
	activateTab(tableName, page)
}
	
function activateTab(tableName, page = 1){
	var $tableBody		=	$('#table-'+tableName+' > tbody'),
		columnNumber	=	$('#table-'+tableName+' > thead > tr > th').length,
		keywordSearch	=	$("#keywordSearch").val(),
		dataSend		=	{page:page, keywordSearch:keywordSearch};
	
	if(tableName == "masterDriver"){
		$("#rankDriverOrder").css("display", "inline-block");
	} else {
		$("#rankDriverOrder").css("display", "none");
	}
	
	$.ajax({
		type: 'POST',
		url: baseURL+tableName+"/getDataTable",
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
			
			var data	=	response.result.data,
				rows	=	"";
				
			if(tableName == "masterProduct"){
				$("#containerProductTypeDetail").html("");
				if(response.dataProductType != false){
					var cbProductType	=	"";
					$.each(response.dataProductType, function(index, array) {
						cbProductType	+=	'<label class="adomx-checkbox"><input type="checkbox" name="cbProductType[]" value="'+array.IDPRODUCTTYPE+'" class="cbProductType"> <i class="icon"></i> '+array.PRODUCTTYPE+'</label>';
					});
					$("#containerProductTypeDetail").html(cbProductType);
				}
			}
			
			if(data.length === 0){
				rows	=	"<tr><th colspan='"+columnNumber+"' align='center'><center>No data found</center></th></tr>";
			} else {
				
				var nomor		=	1;
				$.each(data, function(index, array) {
					
					var btnEdit		=	'<button class="button button-xs button-box button-success" onclick="editorData('+array.IDDATA+')">'+
											'<i class="fa fa-pencil"></i>'+
										'</button>';
					var btnDelete	=	'<button class="button button-xs button-box button-danger" onclick="deleteData('+array.IDDATA+')">'+
											'<i class="fa fa-trash"></i>'+
										'</button>';
										
					switch(tableName){
						case "masterSource"		:	var bonusReview		=	array.CALCULATEBONUSREVIEW == 1 ? "Yes" : "No",
														upsellingType	=	array.UPSELLINGTYPE == 1 ? "Yes" : "No";
													rows				+=	"<tr>"+
																				"<td>"+nomor+"</td>"+
																				"<td><img src='"+array.IMAGELOGO+"' height='60px'/></td>"+
																				"<td>"+array.SOURCENAME+"</td>"+
																				"<td>"+upsellingType+"</td>"+
																				"<td align='right'>"+array.REVIEW5STARPOINT+"</td>"+
																				"<td>"+bonusReview+"</td>"+
																				"<td>"+array.DEFAULTCURRENCY+"</td>"+
																				"<td>"+btnEdit+btnDelete+"</td>"+
																			"</tr>";
													break;
						case "masterProduct"	:	var listProductType	=	"";
													if(array.ARRPRODUCTTYPE){
														var arrProductType	=	array.ARRPRODUCTTYPE.split('|');
														for(var iProductType=0; iProductType<arrProductType.length; iProductType++){
															listProductType	+=	"- "+arrProductType[iProductType]+"<br/>";
														}
													}
													rows	+=	"<tr>"+
																	"<td>"+nomor+"</td>"+
																	"<td>"+array.PRODUCTNAME+"</td>"+
																	"<td>"+listProductType+"</td>"+
																	"<td align='right'>"+numberFormat(array.DURATIONHOUR)+"</td>"+
																	"<td>"+array.DESCRIPTION+"</td>"+
																	"<td>"+btnEdit+btnDelete+"</td>"+
																"</tr>";
													break;
						case "masterDriver"		:	var badgePartnership=	status	=	btnStatusUpdate = "",
														nameFull		=	array.NAMEFULL == '' ? '-' : array.NAMEFULL,
														scheduleType	=	array.SCHEDULETYPE == 1 ? "Auto" : "Manual",
														btnAreaPriority	=	'<button class="button button-xs button-primary btn-block mt-5" onclick="setAreaPriority('+array.IDDATA+', \''+array.NAME+'\')">'+
																				'<i class="fa fa-pencil"></i> Set Area Priority'+
																			'</button>',
														tempOTPData		=	levelUser == 1 ? "<br/><br/><b>Temporary OTP</b><br/>"+array.TEMPOTP : "";
													switch(array.PARTNERSHIPTYPE){
														case "1"	:	badgePartnership	=	'<span class="badge badge-success">Partner</span>';
																		break;
														case "2"	:	badgePartnership	=	'<span class="badge badge-primary">Freelance</span>';
																		break;
														case "3"	:	badgePartnership	=	'<span class="badge badge-info">Team</span>';
																		break;
														case "4"	:	badgePartnership	=	'<span class="badge badge-info">Office</span>';
																		break;
													}
													
													switch(array.STATUS){
														case "1"	:	status			=	'<span class="badge badge-success">Active</span>';
																		btnStatusUpdate	=	'<button class="button button-xs button-box button-warning" onclick="updateStatusData('+array.IDDATA+', -1, \''+array.NAME+'\', \'masterDriver\')">'+
																								'<i class="fa fa-eye-slash"></i>'+
																							'</button>';
																		break;
														case "-1"	:	
														default		:	status			=	'<span class="badge badge-danger">Inactive</span>';
																		btnStatusUpdate	=	'<button class="button button-xs button-box button-warning" onclick="updateStatusData('+array.IDDATA+', 1, \''+array.NAME+'\', \'masterDriver\')">'+
																								'<i class="fa fa-eye"></i>'+
																							'</button>';
																		break;
													}
													
													rows		+=	"<tr>"+
																		"<td align='right'>"+array.RANKNUMBER+"</td>"+
																		"<td>"+badgePartnership+"</td>"+
																		"<td>"+array.DRIVERTYPE+"<br/><b>Quota : </b>"+numberFormat(array.DRIVERQUOTA)+"</td>"+
																		"<td>"+array.CARCAPACITYNAME+" ("+array.CAPACITYDETAIL+")<br/><br/>"+array.CARNUMBERPLATE+"<br/>"+array.CARBRAND+"<br/>"+array.CARMODEL+"</td>"+
																		"<td>"+scheduleType+"</td>"+
																		"<td>"+array.NAME+"<br/>"+nameFull+"<br/>"+array.ADDRESS+"<br/>"+array.PHONE+"<br/>"+array.EMAIL+"</td>"+
																		"<td>"+array.DRIVERAREA+"<br/>"+btnAreaPriority+"</td>"+
																		"<td>"+array.LASTLOGIN+"<br/>"+array.LASTACTIVITY+tempOTPData+"</td>"+
																		"<td>"+status+"</td>"+
																		"<td>"+btnEdit+btnStatusUpdate+"</td>"+
																	"</tr>";
													break;
						case "masterVendor"		:	var status	=	transportService	=	btnStatusUpdate =	badgeFinanceScheme	=	"";
													switch(array.STATUS){
														case "1"	:	status			=	'<span class="badge badge-success">Active</span>';
																		btnStatusUpdate	=	'<button class="button button-xs button-box button-warning" onclick="updateStatusData('+array.IDDATA+', -1, \''+array.NAME+'\', \'masterVendor\')">'+
																								'<i class="fa fa-eye-slash"></i>'+
																							'</button>';
																		break;
														case "-1"	:	
														default		:	status			=	'<span class="badge badge-danger">Inactive</span>';
																		btnStatusUpdate	=	'<button class="button button-xs button-box button-warning" onclick="updateStatusData('+array.IDDATA+', 1, \''+array.NAME+'\', \'masterVendor\')">'+
																								'<i class="fa fa-eye"></i>'+
																							'</button>';
																		break;
													}
													
													switch(array.TRANSPORTSERVICE){
														case "1"	:	transportService=	'<span class="badge badge-success">Included</span>';
																		break;
														default		:	transportService=	'<span class="badge badge-danger">Not Included</span>';
																		break;
													}
													
													switch(array.FINANCESCHEMETYPE){
														case "1"	:	badgeFinanceScheme	=	'<span class="badge badge-primary">Withdrawal</span>';
																		break;
														case "2"	:	badgeFinanceScheme	=	'<span class="badge badge-warning">Deposit</span>';
																		break;
														default		:	badgeFinanceScheme	=	'<span class="badge badge-danger">Not Set</span>';
																		break;
													}
													
													rows		+=	"<tr>"+
																		"<td>"+array.VENDORTYPE+"</td>"+
																		"<td>"+array.NAME+"</td>"+
																		"<td>"+array.ADDRESS+"</td>"+
																		"<td>"+array.PHONE+"</td>"+
																		"<td>"+array.EMAIL+"</td>"+
																		"<td>"+badgeFinanceScheme+"</td>"+
																		"<td>"+status+"</td>"+
																		"<td>"+transportService+"</td>"+
																		"<td>"+btnEdit+btnStatusUpdate+"</td>"+
																	"</tr>";
													break;
						case "masterCarType"	:	rows	+=	"<tr>"+
																	"<td>"+nomor+"</td>"+
																	"<td>"+array.CARTYPE+"</td>"+
																	"<td>"+array.DESCRIPTION+"</td>"+
																	"<td>"+btnEdit+"</td>"+
																"</tr>";
													break;
					}
					nomor++;
					
				});
				
			}

			generatePagination("tablePagination", page, response.result.pageTotal);
			generateDataInfo("tableDataCount", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
			$('#addData').off('click');
			$('#addData').on('click', function(e) {
				var tableForm	=	$('#tabsPanel > li.nav-item > a.nav-link.active').attr("data-table");
				$('#editor-'+tableForm+' #actionType').val("insert");

				$('#editor-'+tableForm).trigger("reset");
				$('#editor-'+tableForm+' #idData').val("");
				$("#editor-"+tableForm+" :input").attr("disabled", false);
				$('#editor-modal-'+tableForm).modal('show');
				maskNumberInput();
				
				switch(tableForm){
					case "masterSource"	:	$("input[name=upsellingType][value=0]").prop('checked', true); 
											$("input[name=reviewBonusPunishment][value=0]").prop('checked', true); 
											$("input[name=review5StarPoint]").val(1); 
											break;
					case "masterProduct":	$(".cbProductType").prop('checked', false); break;
					case "masterDriver"	:	setOptionHelper('optionDriverType', 'dataDriverType');
											setOptionHelper('optionCarCapacity', 'dataCarCapacity');
											$('#driverQuota').val(1);
											$('#optionPartnershipType').val(1);
											$('#optionScheduleType').val(1).attr("disabled", false);
											$('#optionPartnershipType').off('change');
											$('#optionPartnershipType').on('change', function(e) {
												if($(this).val() == 1 || $(this).val() == 4){
													$('#optionScheduleType').val(1).attr("disabled", false);
													$('#carNumberPlate, #carBrand, #carModel').attr("disabled", false);
												} else {
													$('#optionScheduleType').val(2).attr("disabled", true);
													$('#carNumberPlate, #carBrand, #carModel').attr("disabled", false);
													
													if($(this).val() == 3){
														$('#carNumberPlate, #carBrand, #carModel').val("-").attr("disabled", true);
													}
												}
											});
											$("#checkboxReviewBonusPunishment").prop('checked', false);
											$("#containerSecretPINDriverStatus, #containerDriverNewFinanceScheme").addClass('d-none');
											break;
					case "masterVendor"	:	setOptionHelper('optionVendorType', 'dataVendorType');
											$("input[name=autoReduceCollectPayment][value=0]").prop('checked', true); 
											$("#containerSecretPINVendorStatus, #containerVendorNewFinanceScheme").html('');
											$('#btnUpdateLastWithdraw').addClass("d-none");
											break;
					default				:	break;
				}
				
				$("#editor-"+tableForm).off("submit");
				$("#editor-"+tableForm).on("submit", function(e){

					e.preventDefault();	
					if($('#editor-'+tableName+' #actionType').val() == "insert"){
						var dataForm	=	$("#editor-"+tableForm+" :input").serializeArray();
						var dataSend	=	{};
						$.each(dataForm, function() {
							dataSend[this.name] = this.value;
						});
					
						if(tableName == "masterProduct"){
							var arrIdProductType	=	[];
							$('input:checkbox.cbProductType').each(function () {
								var checkboxVal	=	(this.checked ? $(this).val() : false);
								
								if(checkboxVal){
									arrIdProductType.push(checkboxVal);
								}
							});
							dataSend["arrIdProductType"]	=	arrIdProductType;
						}

						if(tableName == "masterDriver") dataSend["optionScheduleType"]	=	$('#optionScheduleType').val();
						
						$.ajax({
							type: 'POST',
							url: baseURL+tableForm+"/insertData",
							contentType: 'application/json',
							dataType: 'json',
							data: mergeDataSend(dataSend),
							beforeSend:function(){
								NProgress.set(0.4);
								$("#editor-"+tableForm+" :input").attr("disabled", true);
							},
							success:function(response){
								$("#editor-"+tableForm+" :input").attr("disabled", false);
								setUserToken(response);
								NProgress.done();
								
								$('#modalWarning').on('show.bs.modal', function() {
									$('#modalWarningBody').html(response.msg);			
								});
								$('#modalWarning').modal('show');
								
								if(tableName == "masterDriver"){
									if($('#optionPartnershipType').val() == 1 || $('#optionPartnershipType').val() == 4){
										$('#optionScheduleType').attr("disabled", false);
									} else {
										$('#optionScheduleType').val(2).attr("disabled", true);
									}
								}
								
								if(tableName == "masterVendor" && levelUser == 1) $('#btnUpdateLastWithdraw').removeClass("d-none");

								if(response.status == 200){
									$('#editor-modal-'+tableForm).modal('hide');
									activateTab(tableForm);
									updateOptionHelper(tableName, response);
								}
							}
						});
					}
				})
			});

			$('#keywordSearch').off('keydown');
			$('#keywordSearch').on('keydown', function(e) {
				if(e.which === 13){
					var tableName	=	$('#tabsPanel > li.nav-item > a.nav-link.active').attr("data-table");
					activateTab(tableName, 1);
				}
			});
			
			$('#rankDriverOrder').off('click');
			$('#rankDriverOrder').on('click', function(e) {

				$('#editor-modal-driverRank').off('shown.bs.modal');
				$('#editor-modal-driverRank').on('shown.bs.modal', function (e) {
					$.ajax({
						type: 'POST',
						url: baseURL+"masterDriver/getDriverRank",
						contentType: 'application/json',
						dataType: 'json',
						data: mergeDataSend(),
						beforeSend:function(){
							NProgress.set(0.4);
							$('#window-loader').modal('show');
						},
						success:function(response){
							setUserToken(response);
							$('#window-loader').modal('hide');
							NProgress.done();
							
							var driverRankTour		=	response.driverRankTour,
								driverRankShuttle	=	response.driverRankShuttle,
								driverRankFreelance	=	response.driverRankFreelance,
								driverRankTeam		=	response.driverRankTeam,
								driverRankOffice	=	response.driverRankOffice,
								listDriverTour		=	listDriverShuttle	=	listDriverFreelance	=	listDriverTeam	=	listDriverOffice	=	"";
							
							if(driverRankTour.length > 0){
								$.each(driverRankTour, function(index, array) {
									listDriverTour	+=	'<div class="list-group-item" data-id="'+array.IDDRIVER+'">'+
															'<i class="fa fa-arrows mr-2" aria-hidden="true"></i> '+array.NAME+
															'<b class="pull-right">'+array.TOTALPOINT+'</b>'+
														'</div>';
								});
							}

							if(driverRankShuttle.length > 0){
								$.each(driverRankShuttle, function(index, array) {
									listDriverShuttle	+=	'<div class="list-group-item" data-id="'+array.IDDRIVER+'">'+
																'<i class="fa fa-arrows mr-2" aria-hidden="true"></i> '+array.NAME+
																'<b class="pull-right">'+array.TOTALPOINT+'</b>'+
															'</div>';
								});
							}

							if(driverRankFreelance.length > 0){
								$.each(driverRankFreelance, function(index, array) {
									listDriverFreelance	+=	'<div class="list-group-item" data-id="'+array.IDDRIVER+'">'+
																'<i class="fa fa-arrows mr-2" aria-hidden="true"></i> '+array.NAME+
																'<b class="pull-right">'+array.TOTALPOINT+'</b>'+
															'</div>';
								});
							}

							if(driverRankTeam.length > 0){
								$.each(driverRankTeam, function(index, array) {
									listDriverTeam		+=	'<div class="list-group-item" data-id="'+array.IDDRIVER+'">'+
																'<i class="fa fa-arrows mr-2" aria-hidden="true"></i> '+array.NAME+
																'<b class="pull-right">'+array.TOTALPOINT+'</b>'+
															'</div>';
								});
							}

							if(driverRankOffice.length > 0){
								$.each(driverRankOffice, function(index, array) {
									listDriverOffice	+=	'<div class="list-group-item" data-id="'+array.IDDRIVER+'">'+
																'<i class="fa fa-arrows mr-2" aria-hidden="true"></i> '+array.NAME+
																'<b class="pull-right">'+array.TOTALPOINT+'</b>'+
															'</div>';
								});
							}

							$("#sortableDriverTour").html(listDriverTour);
							$("#sortableDriverShuttle").html(listDriverShuttle);
							$("#sortableDriverFreelance").html(listDriverFreelance);
							$("#sortableDriverTeam").html(listDriverTeam);
							$("#sortableDriverOffice").html(listDriverOffice);
							
							var sortableTour	 	=	Sortable.create(sortableDriverTour, {
															animation: 150
														});
							
							var sortableShuttle		=	Sortable.create(sortableDriverShuttle, {
														  animation: 150
														});
							
							var sortableFreelance	=	Sortable.create(sortableDriverFreelance, {
														  animation: 150
														});
							
							var sortableTeam		 =	Sortable.create(sortableDriverTeam, {
														  animation: 150
														});
							
							var sortableOffice		 =	Sortable.create(sortableDriverOffice, {
														  animation: 150
														});
				
							$("#editor-driverRank").off("submit");
							$("#editor-driverRank").on("submit", function(e){

								e.preventDefault();
								var arrDriverTour		=	sortableTour.toArray(),
									arrDriverShuttle	=	sortableShuttle.toArray(),
									arrDriverFreelance	=	sortableFreelance.toArray(),
									arrDriverTeam		=	sortableTeam.toArray(),
									arrDriverOffice		=	sortableOffice.toArray(),
									dataSend			=	{
																arrDriverTour:arrDriverTour,
																arrDriverShuttle:arrDriverShuttle,
																arrDriverFreelance:arrDriverFreelance,
																arrDriverTeam:arrDriverTeam,
																arrDriverOffice:arrDriverOffice
															};
									
								$.ajax({
									type: 'POST',
									url: baseURL+"masterDriver/saveDriverRank",
									contentType: 'application/json',
									dataType: 'json',
									data: mergeDataSend(dataSend),
									beforeSend:function(){
										NProgress.set(0.4);
										$('#window-loader').modal('show');
									},
									success:function(response){
										setUserToken(response);
										NProgress.done();
										$('#window-loader').modal('hide');

										$('#modalWarning').on('show.bs.modal', function() {
											$('#modalWarningBody').html(response.msg);			
										});
										$('#modalWarning').modal('show');

										if(response.status == 200){
											activateTab("masterDriver");
											$('#editor-modal-driverRank').modal('hide');
										}
									}
								});
								
							});
							
						}
					});					
				});
				$('#editor-modal-driverRank').modal('show');
				
			});
		}
	});
}

function setAreaPriority(idDriver, driverName){
	
	$("#driverNameAreaOrder").html(driverName);
	$("#idDriverAreaOrder").val(idDriver);
	var dataSend	=	{idDriver:idDriver};
	$.ajax({
		type: 'POST',
		url: baseURL+"masterDriver/getDriverAreaOrder",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			
			var driverAreaOrder		=	response.driverAreaOrder,
				listDriverAreaOrder	=	"";
			
			if(driverAreaOrder.length > 0){
				$.each(driverAreaOrder, function(index, array) {
					listDriverAreaOrder	+=	'<div class="list-group-item" data-id="'+array.IDAREA+'">'+
												'<i class="fa fa-arrows mr-2" aria-hidden="true"></i> '+array.AREANAME+
											'</div>';
				});
			}

			$("#sortableDriverAreaOrder").html(listDriverAreaOrder);
			var sortableAreaOrder	 	=	Sortable.create(sortableDriverAreaOrder, {
													handle: '.fa-arrows',
													animation: 150
											});
			$('#editor-modal-driverAreaOrder').modal('show');

			$("#editor-driverAreaOrder").off("submit");
			$("#editor-driverAreaOrder").on("submit", function(e){

				e.preventDefault();
				var arrDriverAreaOrder	=	sortableAreaOrder.toArray(),
					idDriver			=	$("#idDriverAreaOrder").val(),
					dataSend			=	{idDriver:idDriver, arrDriverAreaOrder:arrDriverAreaOrder};
					
				$.ajax({
					type: 'POST',
					url: baseURL+"masterDriver/saveDriverAreaOrder",
					contentType: 'application/json',
					dataType: 'json',
					data: mergeDataSend(dataSend),
					beforeSend:function(){
						NProgress.set(0.4);
						$('#window-loader').modal('show');
					},
					success:function(response){
						setUserToken(response);
						NProgress.done();
						$('#window-loader').modal('hide');

						$('#modalWarning').on('show.bs.modal', function() {
							$('#modalWarningBody').html(response.msg);			
						});
						$('#modalWarning').modal('show');

						if(response.status == 200){
							activateTab("masterDriver");
							$('#editor-modal-driverAreaOrder').modal('hide');
						}
					}
				});
				
			});
			
		}
	});	
	
}

$('#editor-modal-masterSource').off('show.bs.modal');
$('#editor-modal-masterSource').on('show.bs.modal', function() {
	var idData	=	$("#editor-masterSource #idData").val();
	
	if(idData == ""){
		$("#imageLogoSourceEditor").attr("src", ASSET_IMG_URL+"noimage.jpg").attr("height", "100px");
		$("#logoSourceName").val("");
	}
	createUploaderLogoSource(idData);
});

function createUploaderLogoSource(idData){
	
	idData	=	idData == "" ? 0 : idData;
	$('.ajax-file-upload-container').remove();
	$("#uploaderLogoSource").uploadFile({
		url: baseURL+"masterSource/uploadLogoSource/"+idData,
		multiple:false,
		dragDrop:false,
		onSuccess:function(files,data,xhr,pd){
			if(data.status != 200){
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(data.msg);			
				});
				$('#modalWarning').modal('show');
			} else {
				$("#imageLogoSourceEditor").removeAttr('src').attr("src", data.urlLogoSource).attr("height", data.defaultHeight);
				$("#logoSourceName").val(data.logoSourceName);
			}
		}
	});
	
}

function editorData(idData, tableName){
	
	var tableName	=	$('#tabsPanel > li.nav-item > a.nav-link.active').attr("data-table");
	$('#editor-'+tableName).trigger("reset");
	$('#editor-'+tableName+' #actionType').val("update");
	var dataSend	=	{idData:idData};
	$.ajax({
		type: 'POST',
		url: baseURL+tableName+"/detailData",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#editor-"+tableName+" :input").attr("disabled", true);
			$('#btnUpdateLastWithdraw').addClass("d-none");
		},
		success:function(response){
			
			NProgress.done();
			setUserToken(response);
			var optionHelperName	=	[];

			$("#editor-"+tableName+" :input").attr("disabled", false);
			switch(tableName){
				case "masterSource"		:	$('#editor-'+tableName+' #idData').val(response.data.IDDATA);
											$('#editor-'+tableName+' #sourceName').val(response.data.SOURCENAME);
											$('#editor-'+tableName+' #review5StarPoint').val(response.data.REVIEW5STARPOINT);
											$("input[name=upsellingType][value="+response.data.UPSELLINGTYPE+"]").prop('checked', true);
											$("input[name=reviewBonusPunishment][value="+response.data.CALCULATEBONUSREVIEW+"]").prop('checked', true);
											$('#editor-'+tableName+' #defaultCurrency').val(response.data.DEFAULTCURRENCY);
											$("#imageLogoSourceEditor").attr("src", response.data.URLLOGO);
											$("#logoSourceName").val(response.data.LOGO);
											createUploaderLogoSource(response.data.IDDATA);
											break;
				case "masterProduct"	:	var arrIdProductType	=	response.data.ARRIDPRODUCTTYPE;
											if(arrIdProductType != "" && arrIdProductType != null){
												arrIdProductType	=	arrIdProductType.split('|');
												for(var iProductType=0; iProductType<arrIdProductType.length; iProductType++){
													$("input.cbProductType[value='"+arrIdProductType[iProductType]+"']").prop('checked', true);
												}
											}
											$('#editor-'+tableName+' #productName').val(response.data.PRODUCTNAME);
											$('#editor-'+tableName+' #durationHour').val(response.data.DURATIONHOUR);
											$('#editor-'+tableName+' #description').val(response.data.DESCRIPTION);
											$('#editor-'+tableName+' #idData').val(response.data.IDDATA);
											break;
				case "masterDriver"		:	var secretPinStatus		=	response.data.SECRETPINSTATUS,
												newFinanceScheme	=	response.data.NEWFINANCESCHEME,
												partnershipType		=	response.data.PARTNERSHIPTYPE,
												badgeSecretPin		=	badgeNewFinanceScheme	=	'';

											setOptionHelper('optionDriverType', 'dataDriverType', response.data.IDDRIVERTYPE);
											setOptionHelper('optionCarCapacity', 'dataCarCapacity', response.data.IDCARCAPACITY);
											$('#editor-'+tableName+' #optionScheduleType').val(response.data.SCHEDULETYPE);
											$('#editor-'+tableName+' #optionPartnershipType').val(partnershipType);
											$('#editor-'+tableName+' #driverName').val(response.data.NAME);
											$('#editor-'+tableName+' #driverNameFull').val(response.data.NAMEFULL);
											$('#editor-'+tableName+' #address').val(response.data.ADDRESS);
											$('#editor-'+tableName+' #driverQuota').val(response.data.DRIVERQUOTA);
											$('#editor-'+tableName+' #phone').val(response.data.PHONE);
											$('#editor-'+tableName+' #driverEmail').val(response.data.EMAIL);
											$('#editor-'+tableName+' #password').val(response.data.PASSWORDPLAIN);
											$('#editor-'+tableName+' #carNumberPlate').val(response.data.CARNUMBERPLATE);
											$('#editor-'+tableName+' #carBrand').val(response.data.CARBRAND);
											$('#editor-'+tableName+' #carModel').val(response.data.CARMODEL);
											$('#editor-'+tableName+' #idData').val(response.data.IDDATA);
													
											if(secretPinStatus == "2"){
												badgeSecretPin	=	'<div class="alert alert-success py-3 pr-4" role="alert">'+
																		'Driver secret PIN has been updated at '+response.data.SECRETPINLASTUPDATE+'. '+
																		'<button class="button button-xs button-primary pull-right" type="button" onclick="confirmResetPINPartner('+response.data.IDDATA+', \''+response.data.NAME+'\', 2)">'+
																			'<i class="fa fa-recycle text-white"></i><span>Reset PIN</span>'+
																		'</button>'+
																	'</div>';
											} else {
												badgeSecretPin	=	'<div class="alert alert-warning py-3 pr-4" role="alert">'+
																		'Default secret PIN is set. Driver must change secret PIN'+
																	'</div>';
											}
											
											if(newFinanceScheme != "1"){
												badgeNewFinanceScheme	=	'<div class="alert alert-warning py-3 pr-4" role="alert">'+
																				'This driver has not used the new finance scheme'+
																				'<button class="button button-xs button-primary pull-right" type="button" onclick="confirmSetPartnerNewFinanceScheme('+response.data.IDDATA+', \''+response.data.NAME+'\', 2)">'+
																					'<i class="fa fa-certificate text-white"></i><span>Set New Finance Scheme Now</span>'+
																				'</button>'+
																			'</div>';
											} else {
												badgeNewFinanceScheme	=	'<div class="alert alert-success py-3 pr-4" role="alert">'+
																				'This driver has used a new financial scheme'+
																			'</div>';
											}

											if(partnershipType == "2" || partnershipType == 2 || partnershipType == "3" || partnershipType == 3){
												$('#optionScheduleType').attr("disabled", true);
												
												if(partnershipType == 3){
													$('#carNumberPlate, #carBrand, #carModel').attr("disabled", true);
												}
											} else {
												$('#optionScheduleType').attr("disabled", false);
												$('#carNumberPlate, #carBrand, #carModel').attr("disabled", false);
											}
											
											$('#optionPartnershipType').off('change');
											$('#optionPartnershipType').on('change', function(e) {
												if($(this).val() == 1 || $(this).val() == 4){
													$('#optionScheduleType').attr("disabled", false);
												} else {
													$('#optionScheduleType').val(2).attr("disabled", true);
												}
											});
											
											if(response.data.REVIEWBONUSPUNISHMENT == "1"){
												$("#checkboxReviewBonusPunishment").prop('checked', true);
											} else {
												$("#checkboxReviewBonusPunishment").prop('checked', false);
											}
											
											$("#containerSecretPINDriverStatus").removeClass('d-none').html(badgeSecretPin);
											$("#containerDriverNewFinanceScheme").removeClass('d-none').html(badgeNewFinanceScheme);
											break;
				case "masterVendor"		:	var secretPinStatus		=	response.data.SECRETPINSTATUS,
												newFinanceScheme	=	response.data.NEWFINANCESCHEME,
												financeSchemeType	=	response.data.FINANCESCHEMETYPE,
												badgeSecretPin		=	badgeNewFinanceScheme	=	'';

											setOptionHelper('optionVendorType', 'dataVendorType', response.data.IDVENDORTYPE);
											$('#editor-'+tableName+' #vendorPhone').val(response.data.PHONE);
											$('#editor-'+tableName+' #vendorName').val(response.data.NAME);
											$('#editor-'+tableName+' #address').val(response.data.ADDRESS);
											$('#editor-'+tableName+' #vendorEmail').val(response.data.EMAIL);
											$('#editor-'+tableName+' #idData').val(response.data.IDDATA);
											$("input[name=autoReduceCollectPayment][value="+response.data.AUTOREDUCECOLLECTPAYMENT+"]").prop('checked', true);
													
											if(secretPinStatus == "2"){
												badgeSecretPin	=	'<div class="alert alert-success py-3" role="alert">'+
																		'Vendor secret PIN has been updated at '+response.data.SECRETPINLASTUPDATE+'. '+
																		'<button class="button button-xs button-primary pull-right" type="button" onclick="confirmResetPINPartner('+response.data.IDDATA+', \''+response.data.NAME+'\', 1)">'+
																			'<i class="fa fa-recycle text-white"></i><span>Reset PIN</span>'+
																		'</button>'+
																	'</div>';
											} else {
												badgeSecretPin	=	'<div class="alert alert-warning py-3" role="alert">'+
																		'Default secret PIN is set. Vendor must change secret PIN'+
																	'</div>';
											}
											
											if(newFinanceScheme != "1"){
												badgeNewFinanceScheme	=	'<div class="alert alert-warning py-3 pr-4" role="alert">'+
																				'This vendor has not used the new finance scheme'+
																				'<button class="button button-xs button-primary pull-right" type="button" onclick="confirmSetPartnerNewFinanceScheme('+response.data.IDDATA+', \''+response.data.NAME+'\', 1)">'+
																					'<i class="fa fa-certificate text-white"></i><span>Set New Finance Scheme Now</span>'+
																				'</button>'+
																			'</div>';
											} else {
												badgeNewFinanceScheme	=	'<div class="alert alert-success py-3 pr-4" role="alert">'+
																				'This vendor has used a new financial scheme'+
																			'</div>';
												
												if(financeSchemeType == 1 || financeSchemeType == '1'){
													$('#btnUpdateLastWithdraw').removeClass("d-none");
													$("#btnUpdateLastWithdraw").off('click');
													$("#btnUpdateLastWithdraw").on('click', function(e){
														e.preventDefault();
														confirmUpdateLastWithdrawVendor(response.data.IDDATA, response.data.NAME);
													});
												}
											}
											
											$("#containerSecretPINVendorStatus").html(badgeSecretPin);
											$("#containerVendorNewFinanceScheme").html(badgeNewFinanceScheme);
											break;
				case "masterCarType"	:	$('#editor-'+tableName+' #carTypeName').val(response.data.CARTYPE);
											$('#editor-'+tableName+' #carTypeDescription').val(response.data.DESCRIPTION);
											$('#editor-'+tableName+' #idData').val(response.data.IDDATA);
											break;
			}
			
			$('#editor-modal-'+tableName).modal('show');
			$("#editor-"+tableName).off('submit');
			$("#editor-"+tableName).on('submit', function(e){
				
				e.preventDefault();
				
				if($('#editor-'+tableName+' #actionType').val() == "update"){
					var dataForm	=	$("#editor-"+tableName+" :input").serializeArray();
					var dataSend	=	tableName == "masterDriver" ? {optionScheduleType:$("#optionScheduleType").val()} : {};
					
					$.each(dataForm, function() {
						dataSend[this.name] = this.value;
					});
					
					if(tableName == "masterProduct"){
						var arrIdProductType	=	[];
						$('input:checkbox.cbProductType').each(function () {
							var checkboxVal	=	(this.checked ? $(this).val() : false);
							
							if(checkboxVal){
								arrIdProductType.push(checkboxVal);
							}
						});
						dataSend["arrIdProductType"] = arrIdProductType;
					}
					
					$.ajax({
						type: 'POST',
						url: baseURL+tableName+"/updateData",
						contentType: 'application/json',
						dataType: 'json',
						data: mergeDataSend(dataSend),
						beforeSend:function(){
							$("#editor-"+tableName+" :input").attr("disabled", true);
							NProgress.set(0.4);
						},
						success:function(response){
							
							$("#editor-"+tableName+" :input").attr("disabled", false);
							NProgress.done();
							setUserToken(response);
							
							$('#modalWarning').on('show.bs.modal', function() {
								$('#modalWarningBody').html(response.msg);
							});
							$('#modalWarning').modal('show');

							if(response.status == 200){
								$('#editor-modal-'+tableName).modal('hide');
								activateTab(tableName);
								updateOptionHelper(tableName, response);
							}
						}
					});
				}
			});
		}
	});
}

function deleteData(idData){
	$deleteDialog.find('#deleteBtn').attr('data-idData', idData);
	$deleteDialog.modal('show');
}

$('#deleteBtn').off('click');
$('#deleteBtn').on('click', function(e) {
	
	var idData		=	$deleteDialog.find('#deleteBtn').attr('data-idData'),
		dataSend	=	{idData : idData},
		tableName	=	$('#tabsPanel > li.nav-item > a.nav-link.active').attr("data-table");
	
	$.ajax({
		type: 'POST',
		url: baseURL+tableName+"/deleteData",
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
				activateTab(tableName);
			}
		}
	});
});

function confirmResetPINPartner(idPartner, partnerName, partnerType){
	
	$("#editor-modal-masterDriver, #editor-modal-masterVendor").modal('hide');
	var tableName		=	partnerType == 1 ? "masterVendor" : "masterDriver";
		strPartnerType	=	partnerType == 1 ? "Vendor" : "Driver";
		confirmText		=	strPartnerType+' secret PIN will be reset.<br/>'+strPartnerType+' : <b>'+partnerName+'</b><br/><br/>Are you sure?';
	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', partnerType+"|"+idPartner+"|"+tableName).attr('data-function', 'resetPartnerSecretPin');
	$confirmationDialog.modal('show');
	
}

function confirmSetPartnerNewFinanceScheme(idPartner, partnerName, partnerType){
	
	$("#editor-modal-masterDriver, #editor-modal-masterVendor").modal('hide');
	var tableName		=	partnerType == 1 ? "masterVendor" : "masterDriver",
		strPartnerType	=	partnerType == 1 ? "Vendor" : "Driver",
		formDriver		=	'<div class="col-lg-6 col-sm-12">'+
								'<div class="form-group required">'+
									'<label for="totalLoanCar" class="control-label">Total Loan - Car</label>'+
									'<input type="text" class="form-control mb-10 text-right" id="totalLoanCar" name="totalLoanCar" value="0" onkeypress="maskNumberInput(0, 999999999, \'totalLoanCar\')">'+
								'</div>'+
							'</div>'+
							'<div class="col-lg-6 col-sm-12">'+
								'<div class="form-group required">'+
									'<label for="totalLoanCar" class="control-label">Total Loan - Personal</label>'+
									'<input type="text" class="form-control mb-10 text-right" id="totalLoanPersonal" name="totalLoanPersonal" value="0" onkeypress="maskNumberInput(0, 999999999, \'totalLoanPersonal\')">'+
								'</div>'+
							'</div>'+
							'<div class="col-sm-12">'+
								'<div class="form-group required">'+
									'<label for="totalPrepaidCapital" class="control-label">Total Prepaid Capital</label>'+
									'<input type="text" class="form-control mb-10 text-right" id="totalPrepaidCapital" name="totalPrepaidCapital" value="0" onkeypress="maskNumberInput(0, 999999999, \'totalPrepaidCapital\')">'+
								'</div>'+
							'</div>',
		formVendor		=	'<div class="col-lg-6 col-sm-12">'+
								'<div class="form-group required">'+
									'<label for="optionFinanceSchemeType" class="control-label">Finance Scheme Type</label>'+
									'<select id="optionFinanceSchemeType" name="optionFinanceSchemeType" class="form-control" onchange="onchangeFinanceSchemeType()">'+
										'<option value="1">Withdrawal</option>'+
										'<option value="2">Deposit</option>'+
									'</select>'+
								'</div>'+
							'</div>'+
							'<div class="col-lg-6 col-sm-12">'+
								'<div class="form-group required">'+
									'<label for="lastScheduleWithdrawal" class="control-label">Last Schedule Withdrawal</label>'+
									'<input type="text" class="form-control input-date-single text-center" id="lastScheduleWithdrawal" name="lastScheduleWithdrawal" value="'+dateToday+'">'+
								'</div>'+
							'</div>'+
							'<div class="col-sm-12">'+
								'<div class="form-group">'+
									'<label for="lastDepositBalance" class="control-label">Last Deposit Balance</label>'+
									'<input type="text" class="form-control text-right" id="lastDepositBalance" name="lastDepositBalance" value="0"  onkeypress="maskNumberInput(0, 99999999, \'lastDepositBalance\');" disabled>'+
								'</div>'+
							'</div>',
		confirmText		=	strPartnerType+' new finance scheme will be set to <b>'+partnerName+' ('+strPartnerType+')</b>. Please complete the required data form below<br/><br/>'+
							'<div class="row mt-5">[form]</div>'+
							'<div class="alert alert-danger" role="alert">'+
								'<b>Note : </b>This action will have an impact on the financial partner`s calculations. After updating this finance scheme, you cannot revert to the old scheme'+
							'</div>'+
							'<br/>Are you sure?';
	
	if(partnerType == 1){
		confirmText	=	confirmText.replace("[form]", formVendor);
	} else {
		confirmText	=	confirmText.replace("[form]", formDriver);
	}
	
	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', partnerType+"|"+idPartner+"|"+tableName).attr('data-function', 'setPartnerNewFinanceScheme');
	$confirmationDialog.modal('show');
	generateDatePickerElem();
}

function confirmUpdateLastWithdrawVendor(idPartner, partnerName){
	$("#editor-modal-masterVendor").modal('hide');
	var tableName		=	"masterVendor",
		formUpdate		=	'<div class="col-lg-6 col-sm-12">'+
								'<div class="form-group required">'+
									'<label for="lastDateWithdrawal" class="control-label">Last Schedule Withdrawal</label>'+
									'<input type="text" class="form-control input-date-single text-center" id="lastDateWithdrawal" name="lastDateWithdrawal" value="'+dateToday+'">'+
								'</div>'+
							'</div>',
		confirmText		=	'Update last withdraw for vendor <b>'+partnerName+'</b>. Please choose last date below<br/><br/>'+
							'<div class="row mt-10">[form]</div>'+
							'Click <b>Yes</b> to proceed data update';
	
	confirmText			=	confirmText.replace("[form]", formUpdate);
	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', idPartner).attr('data-function', 'updateLastWithdrawVendor');
	$confirmationDialog.modal('show');
	generateDatePickerElem();
}

function onchangeFinanceSchemeType(){
	var financeSchemeType	=	$("#optionFinanceSchemeType").val();
	
	if(financeSchemeType == "1") $("#lastDepositBalance").attr("disabled", true);
	if(financeSchemeType == "2") $("#lastDepositBalance").attr("disabled", false);
}
	
function updateStatusData(idData, status, confirmName, tableName){
	var confirmText	=	'';
	switch(tableName){
		case "masterDriver"	:	switch(status){
									case 1		:	confirmText	=	'The driver named  <b>'+confirmName+'</b> will be reactivated.<br/><br/>Are you sure?'; break;
									case -1		:	confirmText	=	'The driver named <b>'+confirmName+'</b> will be deactivated (driver will logout from mobile application).<br/><br/>Are you sure?'; break;
								}
								break;
		case "masterVendor"	:	switch(status){
									case 1		:	confirmText	=	'The vendor named  <b>'+confirmName+'</b> will be reactivated.<br/><br/>Are you sure?'; break;
									case -1		:	confirmText	=	'The vendor named <b>'+confirmName+'</b> will be deactivated (all vendor users will logout from partner application).<br/><br/>Are you sure?'; break;
								}
								break;
	}
	
	$confirmationDialog.find('#modal-confirm-body').html(confirmText);
	$confirmationDialog.find('#confirmBtn').attr('data-idData', idData+'|'+status+'|'+tableName).attr('data-function', 'updateStatus');
	$confirmationDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var dataFunction=	$confirmationDialog.find('#confirmBtn').attr('data-function');
	
	if(dataFunction == "resetPartnerSecretPin"){
		var dataForm	=	$confirmationDialog.find('#confirmBtn').attr('data-idData'),
			splitData	=	dataForm.split("|"),
			partnerType	=	splitData[0],
			idPartner	=	splitData[1],
			tableName	=	splitData[2],
			dataSend	=	{idPartner:idPartner},
			urlFunction	=	partnerType == 1 ? "masterVendor/resetVendorSecretPin" : "masterDriver/resetDriverSecretPin",
			urlFunction	=	baseURL+urlFunction;
	} else if(dataFunction == "setPartnerNewFinanceScheme"){
		var dataForm				=	$confirmationDialog.find('#confirmBtn').attr('data-idData'),
			splitData				=	dataForm.split("|"),
			partnerType				=	splitData[0],
			idPartner				=	splitData[1],
			tableName				=	splitData[2],
			totalLoanCar			=	partnerType == 1 ? 0 : $("#totalLoanCar").val().replace(/[^0-9\.]+/g, ''),
			totalLoanPersonal		=	partnerType == 1 ? 0 : $("#totalLoanPersonal").val().replace(/[^0-9\.]+/g, ''),
			totalPrepaidCapital		=	partnerType == 1 ? 0 : $("#totalPrepaidCapital").val().replace(/[^0-9\.]+/g, ''),
			financeSchemeType		=	partnerType == 1 ? $("#optionFinanceSchemeType").val() : "",
			lastDepositBalance		=	partnerType == 1 ? $("#lastDepositBalance").val().replace(/[^0-9\.]+/g, '') : 0,
			lastScheduleWithdrawal	=	partnerType == 1 ? $("#lastScheduleWithdrawal").val() : "",
			dataSend				=	{
											idPartner:idPartner,
											totalLoanCar:totalLoanCar,
											totalLoanPersonal:totalLoanPersonal,
											totalPrepaidCapital:totalPrepaidCapital,
											financeSchemeType:financeSchemeType,
											lastDepositBalance:lastDepositBalance,
											lastScheduleWithdrawal:lastScheduleWithdrawal
										},
			urlFunction				=	partnerType == 1 ? "masterVendor/"+dataFunction : "masterDriver/"+dataFunction,
			urlFunction				=	baseURL+urlFunction;
	} else if(dataFunction == "updateLastWithdrawVendor"){
		var idVendor			=	$confirmationDialog.find('#confirmBtn').attr('data-idData'),
			lastDateWithdrawal	=	$("#lastDateWithdrawal").val(),
			tableName			=	'masterVendor',
			dataSend			=	{idVendor:idVendor, lastDateWithdrawal:lastDateWithdrawal},
			urlFunction			=	baseURL+"masterVendor/updateLastWithdrawVendor";
	} else {
		var dataForm	=	$confirmationDialog.find('#confirmBtn').attr('data-idData'),
			splitData	=	dataForm.split("|"),
			idData		=	splitData[0],
			status		=	splitData[1],
			tableName	=	splitData[2],
			dataSend	=	{idData:idData, status:status},
			urlFunction	=	baseURL+tableName+"/updateStatus";
	}
	
	$.ajax({
		type: 'POST',
		url: urlFunction,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$confirmationDialog.modal('hide');
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
				activateTab(tableName);
				if(dataFunction != "resetPartnerSecretPin" && dataFunction != "setPartnerNewFinanceScheme" && dataFunction != "updateLastWithdrawVendor") updateOptionHelper(tableName, response);
			}
		}
	});
});

function updateOptionHelper(tableName, response){
	
	var optionHelperName	=	arrOptionHelperName[tableName];
	if(optionHelperName.length > 0){
		for(var i=0; i<optionHelperName.length; i++){
			var arrayName	=	optionHelperName[i],
				arrayValue	=	response.optionHelper[i];
			updateDataOptionHelper(arrayName, arrayValue);
		}
	}
								
}

dataMasterFunc();