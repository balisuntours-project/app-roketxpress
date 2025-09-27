var $confirmDeleteDialog= $('#modal-confirm-action');

if (transportSettingFunc == null){
	var transportSettingFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionDriverType', 'dataDriverType');
			setOptionHelper('optionDriverTypeEditor', 'dataDriverType');
			setOptionHelper('optionSourceEditor', 'dataSourceOTA');
			setOptionHelper('optionArea', 'dataArea');
			getDataDriverFees();
			
			$('#optionProductTransport').select2({
				dropdownParent: $("#editor-modal-driverFee")
			});
		});	
	}
}

$('#optionDriverType').off('change');
$('#optionDriverType').on('change', function(e) {
	getDataDriverFees();
});

$('#searchKeyword').off('keydown');
$('#searchKeyword').on('keydown', function(e) {
	if(e.which === 13){
		getDataDriverFees();
	}
});

function getDataDriverFees(){
	var $tableBody		=	$('#table-dataTransportSetting > tbody'),
		columnNumber	=	$('#table-dataTransportSetting > thead > tr > th').length,
		driverType		=	$('#optionDriverType').val(),
		searchKeyword	=	$('#searchKeyword').val(),
		dataSend		=	{driverType:driverType, searchKeyword:searchKeyword};
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/transport/getDataDriverFees",
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
			
			var rows	=	"";
			if(response.status != 200){
				rows	=	"<tr><td colspan='"+columnNumber+"' align='center'><center>"+response.msg+"</center></td></tr>";
			} else {
				var data	=	response.result;
				$.each(data, function(index, array) {
					var btnEdit		=	'<button class="button button-xs button-box button-primary" data-toggle="modal" data-target="#editor-modal-driverFee" data-idDriverFee="'+array.IDDRIVERFEE+'"><i class="fa fa-pencil"></i></button>',
						btnDelete	=	'<button class="button button-xs button-box button-danger" onclick="confirmDeleteDriverFee('+array.IDDRIVERFEE+', \''+array.PRODUCTNAME+'\', \''+array.DRIVERTYPE+'\', \''+array.SOURCENAME+'\')">'+
											'<i class="fa fa-times"></i>'+
										'</button>',
						driverType	=	jobType		=	jobRate	=	scheduleType	=	sourceName	=	"-",
						listFeeCost	=	"<li> <span>Driver Fee</span> <span>"+numberFormat(array.FEENOMINAL)+"</span> </li>";
						
					switch(array.IDDRIVERTYPE){
						case "1"	:	driverType	=	'<p class="badge badge-primary text-white">'+array.DRIVERTYPE+'</p>'; break;
						case "2"	:	driverType	=	'<p class="badge badge-warning text-white">'+array.DRIVERTYPE+'</p>'; break;
						case "3"	:	driverType	=	'<p class="badge badge-info text-white">'+array.DRIVERTYPE+'</p>'; break;
						default		:	driverType	=	''; break;
					}

					switch(array.JOBTYPE){
						case "1"	:	jobType	=	"Short"; break;
						case "2"	:	jobType	=	"Standard"; break;
						case "3"	:	jobType	=	"Long"; break;
						default		:	jobType	=	"-"; break;
					}

					switch(array.JOBRATE){
						case "1"	:	jobRate	=	'<p class="badge badge-pill badge-warning text-white">Standard</p>'; break;
						case "2"	:	jobRate	=	'<p class="badge badge-pill badge-primary text-white">Good</p>'; break;
						case "3"	:	jobRate	=	'<p class="badge badge-pill badge-success text-white">Top</p>'; break;
						default		:	jobRate	=	''; break;
					}
						
					switch(array.SCHEDULETYPE){
						case "1"	:	scheduleType	=	"Auto"; break;
						case "2"	:	scheduleType	=	"Manual"; break;
						default		:	scheduleType	=	"-"; break;
					}
						
					switch(array.IDSOURCE){
						case "0"	:	sourceName	=	"<b class='text-danger'>"+array.SOURCENAME+"</b>"; break;
						default		:	sourceName	=	"<b>"+array.SOURCENAME+"</b>"; break;
					}
					
					if(array.COSTTICKETTYPE != "0"){
						var costTicketType	=	array.COSTTICKETTYPE == "2" ? " / Pax" : "";
						listFeeCost			+=	"<li> <span>Ticket</span> <span>"+numberFormat(array.COSTTICKET)+costTicketType+"</span> </li>";
					}
					
					if(array.COSTPARKINGTYPE != "0"){
						var costParkingType	=	array.COSTPARKINGTYPE == "2" ? " / Pax" : "";
						listFeeCost			+=	"<li> <span>Parking</span> <span>"+numberFormat(array.COSTPARKING)+costParkingType+"</span> </li>";
					}
					
					if(array.COSTMINERALWATERTYPE != "0"){
						var costMineralWaterType=	array.COSTMINERALWATERTYPE == "2" ? " / Pax" : "";
						listFeeCost				+=	"<li> <span>Min. Water</span> <span>"+numberFormat(array.COSTMINERALWATER)+costMineralWaterType+"</span> </li>";
					}
					
					if(array.COSTBREAKFASTTYPE != "0"){
						var costBreakfastType	=	array.COSTBREAKFASTTYPE == "2" ? " / Pax" : "";
						listFeeCost				+=	"<li> <span>Breakfast</span> <span>"+numberFormat(array.COSTBREAKFAST)+costBreakfastType+"</span> </li>";
					}
					
					if(array.COSTLUNCHTYPE != "0"){
						var costLunchType	=	array.COSTLUNCHTYPE == "2" ? " / Pax" : "";
						listFeeCost			+=	"<li> <span>Lunch</span> <span>"+numberFormat(array.COSTLUNCH)+costLunchType+"</span> </li>";
					}
					
					if(array.BONUSTYPE != "0"){
						var bonusType		=	array.BONUSTYPE == "2" ? " / Pax" : "";
						listFeeCost			+=	"<li> <span>Bonus</span> <span>"+numberFormat(array.BONUS)+bonusType+"</span> </li>";
					}
					
					rows	+=	"<tr style='border-top: 1px solid #dee2e6;'>"+
									"<td><b>"+array.PRODUCTNAME+"</b><br/><br/>Additional Info :<br/>"+array.ADDITIONALINFO+"</td>"+
									"<td>"+
										"<div class='order-details-customer-info'>"+
											"<ul>"+
												"<li> <span>Rate</span> <span>"+jobRate+"</span> </li>"+
												"<li> <span>Driver Type</span> <span>"+driverType+"</span> </li>"+
												"<li> <span>Area/Zone</span> <span>"+array.AREANAME+"</span> </li>"+
												"<li> <span>Duration</span> <span>"+jobType+"</span> </li>"+
												"<li> <span>Schedule</span> <span>"+scheduleType+"</span> </li>"+
												"<li> <span>Source</span> <span>"+sourceName+"</span> </li>"+
											"</ul>"+
										"</div>"+
									"</td>"+
									"<td>"+
										"<div class='order-details-customer-info'>"+
											"<ul>"+listFeeCost+"</ul>"+
										"</div>"+
									"</td>"+
									"<td align='center'>"+btnEdit+"<br/>"+btnDelete+"</td>"+
								"</tr>";
				});
			}
			$tableBody.html(rows);
		}
	});
}

$('#editor-modal-driverFee').off('show.bs.modal');
$('#editor-modal-driverFee').on('show.bs.modal', function(e) {
	var idElemTrigger	=	$(e.relatedTarget).attr('id'),
		idDriverFee		=	$(e.relatedTarget).attr('data-idDriverFee');

	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/transport/getOptionTransportProduct",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(),
		beforeSend:function(){
			$("#editor-driverFee :input").attr("disabled", true);
			$('#window-loader').modal('show');
		},
		success:function(response){
			$('#window-loader').modal('hide');
			$("#editor-driverFee :input").attr("disabled", false);

			var dataProduct		=	response.dataProduct;
			$('#optionProductTransport').empty();
			
			if(dataProduct.length > 0){
				$.each(dataProduct, function(index, array) {
					var isProductSet	=	array.ISPRODUCTSET,
						newTagText		=	isProductSet == 0 || isProductSet == "0" ? "* Not Set | " : "";
					$('#optionProductTransport').append($('<option></option>').val(array.IDPRODUCT).html(newTagText+array.PRODUCTNAME));
				});
			}
			
			if(idElemTrigger == "btnAddDriverFee"){
				$("#optionArea").val(0);
				$("#idDriverFee, #lastJobrate").val("");
				$("#notes").val("-");
				$("#feeNominal, #costTicketNominal, #costParkingNominal, #costMineralWaterNominal, #costBreakfastNominal, #costLunchNominal, #bonusNominal").val(0);
				$("#optionCostTicketType, #optionCostBreakfastType, #optionCostLunchType, #optionBonusType").val(2);
				$("#optionCostParkingType, #optionCostMineralWaterType").val(1);
				$("#optionSourceEditor").val(0);
			
				$('#optionProductTransport').select2({
					dropdownParent: $("#editor-modal-driverFee")
				});
			} else {
				var dataSend		=	{idDriverFee:idDriverFee};
				$.ajax({
					type: 'POST',
					url: baseURL+"productSetting/transport/getDetailDriverFee",
					contentType: 'application/json',
					dataType: 'json',
					cache: false,
					data: mergeDataSend(dataSend),
					beforeSend:function(){
						NProgress.set(0.4);
						$("#editor-driverFee :input").attr("disabled", true);
						$('#window-loader').modal('show');
					},
					success:function(response){
						$('#window-loader').modal('hide');
						NProgress.done();
						setUserToken(response);
						
						if(response.status == 200){
							var detailData		=	response.detailData;

							$("#editor-driverFee :input").attr("disabled", false);
							$("#optionProductTransport").val(detailData.IDPRODUCT);
							$("#optionDriverTypeEditor").val(detailData.IDDRIVERTYPE);
							$("#optionSourceEditor").val(detailData.IDSOURCE);
							$("#optionJobType").val(detailData.JOBTYPE);
							$("#optionJobRate").val(detailData.JOBRATE);
							$("#optionArea").val(detailData.IDAREA);
							$("#optionScheduleType").val(detailData.SCHEDULETYPE);
							$("#feeNominal").val(numberFormat(detailData.FEENOMINAL));
							$("#notes").val(detailData.ADDITIONALINFO);
							$("#optionCostTicketType").val(detailData.COSTTICKETTYPE);
							$("#optionCostParkingType").val(detailData.COSTPARKINGTYPE);
							$("#optionCostMineralWaterType").val(detailData.COSTMINERALWATERTYPE);
							$("#optionCostBreakfastType").val(detailData.COSTBREAKFASTTYPE);
							$("#optionCostLunchType").val(detailData.COSTLUNCHTYPE);
							$("#optionBonusType").val(detailData.BONUSTYPE);
							$("#costTicketNominal").val(numberFormat(detailData.COSTTICKET));
							$("#costParkingNominal").val(numberFormat(detailData.COSTPARKING));
							$("#costMineralWaterNominal").val(numberFormat(detailData.COSTMINERALWATER));
							$("#costBreakfastNominal").val(numberFormat(detailData.COSTBREAKFAST));
							$("#costLunchNominal").val(numberFormat(detailData.COSTLUNCH));
							$("#bonusNominal").val(numberFormat(detailData.BONUS));
							$("#idDriverFee").val(detailData.IDDRIVERFEE);
							$("#lastJobrate").val(detailData.JOBRATE);
			
							$('#optionProductTransport').select2({
								dropdownParent: $("#editor-modal-driverFee")
							});
						} else {
							$('#modalWarning').on('show.bs.modal', function() {
								$('#modalWarningBody').html(response.msg);			
							});
							$('#modalWarning').modal('show');
						}
					}
				});
			}
		}
	});
});

$('#editor-driverFee').off('submit');
$('#editor-driverFee').on('submit', function(e) {
	e.preventDefault();
	var dataForm	=	$("#editor-driverFee :input").serializeArray(),
		dataSend	=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/transport/saveDriverFee",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-driverFee :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-driverFee :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#editor-modal-driverFee').modal('hide');
				getDataDriverFees();
			}
		}
	});
});

function confirmDeleteDriverFee(idDriverFee, productName, driverType, sourceName){
	var confirmText	=	'The transport fee for driver will be deleted. Details ;<br/><br/>'+
						'<div class="order-details-customer-info">'+
							'<ul class="ml-5">'+
								'<li> <span>Driver Type</span>	<span><b>'+driverType+'</b></span> </li>'+
								'<li> <span>Source</span>		<span><b>'+sourceName+'</b></span> </li>'+
								'<li> <span>Product</span>		<span><b>'+productName+'</b></span> </li>'+
						'<br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idDriverFee).attr('data-function', "deleteDriverFee");
	$confirmDeleteDialog.modal('show');
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	var idData	=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData'),
		dataSend=	{idData:idData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/transport/deleteDriverFee",
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
				getDataDriverFees();
			}
		}
	});
});

$('#modal-productRank').off('shown.bs.modal');
$('#modal-productRank').on('shown.bs.modal', function (e) {
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/transport/getTransportProductRank",
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
			
			var transportProductRank	=	response.transportProductRank,
				listTransportProduct	=	"";
			
			if(transportProductRank.length > 0){
				$.each(transportProductRank, function(index, array) {
					var badgeJobRate	=	driverType	=	'';
					switch(array.JOBRATE){
						case "1"	:	badgeJobRate	=	'<span class="badge badge-pill badge-warning text-white pull-right">Standard</span>'; break;
						case "2"	:	badgeJobRate	=	'<span class="badge badge-pill badge-primary text-white pull-right">Good</span>'; break;
						case "3"	:	badgeJobRate	=	'<span class="badge badge-pill badge-success text-white pull-right">Top</span>'; break;
						default		:	badgeJobRate	=	''; break;
					}
					
					switch(array.IDDRIVERTYPE){
						case "1"	:	driverType	=	"<b class='text-primary'>"+array.DRIVERTYPE+" | </b>"; break;
						case "2"	:	driverType	=	"<b class='text-warning'>"+array.DRIVERTYPE+" | </b>"; break;
						case "3"	:	driverType	=	"<b class='text-info'>"+array.DRIVERTYPE+" | </b>"; break;
						default		:	driverType	=	''; break;
					}

					listTransportProduct	+=	'<div class="list-group-item" data-id="'+array.IDPRODUCT+'">'+
													'<i class="fa fa-arrows mr-2" aria-hidden="true"></i> '+driverType+array.PRODUCTNAME+
													badgeJobRate+
												'</div>';
				});
			}

			$("#sortableTransportProduct").html(listTransportProduct);
			var sortableProduct	=	Sortable.create(sortableTransportProduct, {
										animation: 150
									});

			$("#editor-productRank").off("submit");
			$("#editor-productRank").on("submit", function(e){
				e.preventDefault();
				var arrProductRank	=	sortableProduct.toArray(),
					dataSend		=	{arrProductRank:arrProductRank};
					
				$.ajax({
					type: 'POST',
					url: baseURL+"productSetting/transport/saveTransportProductRank",
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
							$('#modal-productRank').modal('hide');
						}	
					}
				});
			});	
		}
	});	
});

transportSettingFunc();