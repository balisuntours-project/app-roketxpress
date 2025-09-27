var $confirmDeleteDialog= $('#modal-confirm-action');

if (templateAutoCostFunc == null){
	var templateAutoCostFunc	=	function(){
		$(document).ready(function () {
			getDataTemplateAutoCost();
		});	
	}
}

$('#keywordSearch').off('keydown');
$('#keywordSearch').on('keydown', function(e) {
	if(e.which === 13){
		getDataTemplateAutoCost();
	}
});;

function getDataTemplateAutoCost(){
	
	var $tableBody	=	$('#table-templateAutoCost > tbody'),
		columnNumber=	$('#table-templateAutoCost > thead > tr > th').length,
		keyword		=	$('#keywordSearch').val(),
		dataSend	=	{keyword:keyword};
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/templateAutoCost/getDataTemplateAutoCost",
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
			
			if(response.status != 200){
				$tableBody.html("<tr><td colspan='"+columnNumber+"'><center>No data found</center></td></tr>");
			} else {

				var data	=	response.result,
					rows	=	"",
					number	=	1;
				
				$.each(data, function(index, array) {
					
					var arrExceptionTicket		=	[],
						arrExceptionTransport	=	[],
						idAutoDetailsTemplate	=	array.IDAUTODETAILSTEMPLATE,
						autoDetailTemplateName	=	array.AUTODETAILSTEMPLATENAME,
						btnEditTemplateName		=	'<i class="fa fa-pencil" onclick="showEditorTemplateName('+idAutoDetailsTemplate+', \''+autoDetailTemplateName+'\')"></i>',
						btnDelete				=	'<button class="button button-xs button-box button-danger" onclick="confirmDeleteDataTemplateAutoCost('+idAutoDetailsTemplate+', \''+autoDetailTemplateName+'\')">'+
														'<i class="fa fa-trash"></i>'+
													'</button>',
						btnAddNewKeyword		=	'<button class="button button-primary button-xs" '+
															 'data-actionUrl="insertTemplateAutoCostKeyword" '+
															 'data-triggerFrom="table" '+
															 'data-idAutoDetailsTemplate="'+idAutoDetailsTemplate+'" '+
															 'data-autoDetailsTemplateName="'+autoDetailTemplateName+'" '+
															 'data-toggle="modal" '+
															 'data-target="#modal-editor-templateAutoCostKeyword">'+
														'<span><i class="fa fa-plus"></i>New Keyword</span>'+
													'</button>';
					var arrKeywordList			=	array.KEYWORDLIST == "" || array.KEYWORDLIST == "null" || array.KEYWORDLIST == null ? [] : array.KEYWORDLIST.split('&&'),
						arrTicketPriceList		=	array.TICKETPRICELIST == "" || array.TICKETPRICELIST == "null" || array.TICKETPRICELIST == null ? [] : array.TICKETPRICELIST.split('&&'),
						arrDriverFeeList		=	array.DRIVERFEELIST == "" || array.DRIVERFEELIST == "null" || array.DRIVERFEELIST == null ? [] : array.DRIVERFEELIST.split('&&'),
						elemKeywordList			=	elemTicketPriceList	=	elemDriverFeeList	=	"";
					
					if(arrKeywordList.length > 0){
						for(var i=0; i<arrKeywordList.length; i++){
							var childKeywordList	=	arrKeywordList[i].split('|');
							elemKeywordList			+=	"<span>- "+childKeywordList[1]+"<i class='fa fa-trash pull-right' onclick='confirmDeleteDataKeyword("+childKeywordList[0]+", "+idAutoDetailsTemplate+", \""+childKeywordList[1]+"\", \""+autoDetailTemplateName+"\")'></i></span><br/>";
						}
					}

					if(arrTicketPriceList.length > 0){
						for(var i=0; i<arrTicketPriceList.length; i++){
							var childTicketPriceList=	arrTicketPriceList[i].split('|');
							arrExceptionTicket.push(childTicketPriceList[2]);
							elemTicketPriceList		+=	"<span>- "+childTicketPriceList[1]+"<i class='fa fa-trash pull-right' onclick='confirmDeleteItemTemplateAutoCost("+childTicketPriceList[0]+", "+idAutoDetailsTemplate+", \""+childTicketPriceList[1]+"\", \""+autoDetailTemplateName+"\")'></i></span><br/>";
						}
					}
					
					if(arrDriverFeeList.length > 0){
						for(var i=0; i<arrDriverFeeList.length; i++){
							var childDriverFeeList	=	arrDriverFeeList[i].split('|');
							arrExceptionTransport.push(childDriverFeeList[2]);
							elemDriverFeeList		+=	"<span>- "+childDriverFeeList[1]+"<i class='fa fa-trash pull-right' onclick='confirmDeleteItemTemplateAutoCost("+childDriverFeeList[0]+", "+idAutoDetailsTemplate+", \""+childDriverFeeList[1]+"\", \""+autoDetailTemplateName+"\")'></i></span><br/>";
						}
					}
					
					var btnAddNewCostDetail		=	'<button class="button button-primary button-xs" '+
															 'data-actionUrl="insertTemplateAutoCostDetail" '+
															 'data-triggerFrom="table" '+
															 'data-arrExceptionTicket="'+arrExceptionTicket.toString()+'" '+
															 'data-arrExceptionTransport="'+arrExceptionTransport.toString()+'" '+
															 'data-idAutoDetailsTemplate="'+idAutoDetailsTemplate+'" '+
															 'data-autoDetailsTemplateName="'+autoDetailTemplateName+'" '+
															 'data-toggle="modal" '+
															 'data-target="#modal-editor-templateAutoCostDetail">'+
														'<span><i class="fa fa-plus"></i>New Cost Detail</span>'+
													'</button>';
					rows			+=	"<tr>"+
											"<td>"+autoDetailTemplateName+" "+btnEditTemplateName+"</td>"+
											"<td>"+elemDriverFeeList+elemTicketPriceList+btnAddNewCostDetail+"</td>"+
											"<td>"+elemKeywordList+btnAddNewKeyword+"</td>"+
											"<td align='center'>"+btnDelete+"</td>"+
										"</tr>";
					number++;
					
				});
				
				$tableBody.html(rows);
			
			}
			
		}
		
	});
	
}

function showEditorTemplateName(idAutoDetailsTemplate, autoDetailsTemplateName){
	
	$("#templateAutoCostName").val(autoDetailsTemplateName);
	$("#idAutoDetailsTemplateEditName").val(idAutoDetailsTemplate);
	$('#modal-editor-templateAutoCostName').modal('show');
	
}

$('#editor-templateAutoCostName').off('submit');
$('#editor-templateAutoCostName').on('submit', function(e) {
	
	e.preventDefault();
	var idAutoDetailsTemplate	=	$("#idAutoDetailsTemplateEditName").val(),
		templateAutoCostName	=	$("#templateAutoCostName").val(),
		dataSend				=	{idAutoDetailsTemplate:idAutoDetailsTemplate, templateAutoCostName:templateAutoCostName};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/templateAutoCost/updateTemplateAutoCostName",
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-templateAutoCostName :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-templateAutoCostName :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-editor-templateAutoCostName').modal('hide');
				getDataTemplateAutoCost();
			}
			
		}
		
	});

});

$('#modal-editor-templateAutoCostDetail').off('show.bs.modal');
$('#modal-editor-templateAutoCostDetail').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  
  if ($activeElement.is('[data-toggle]')) {
    if (event.type === 'show') {
      if($activeElement.attr('data-triggerFrom') == "table"){
		  $("#textTemplateNameCostDetail").html($activeElement.attr('data-autoDetailsTemplateName'));
	  } else {
		  $("#textTemplateNameCostDetail").html($("#templateAutoCostNameEditor").val());
	  }
	  
	  $("#idAutoDetailsTemplateCostDetail").val($activeElement.attr('data-idAutoDetailsTemplate'));
	  $('#btnSaveAutoDetailsTemplateDetail').attr('data-actionUrl', $activeElement.attr('data-actionUrl'));
	  $("#optionCostType").val($("#optionCostType option:first").val());
	  $("#containerOptionTransportProduct").removeClass("d-none");
	  $("#containerOptionTicketProduct").addClass("d-none");
	  
	  var arrExceptionTicket	=	$activeElement.attr('data-arrExceptionTicket'),
		  arrExceptionTicket	=	arrExceptionTicket == "" || arrExceptionTicket == null ? "" : arrExceptionTicket.toString(),
		  arrExceptionTransport	=	$activeElement.attr('data-arrExceptionTransport'),
		  arrExceptionTransport	=	arrExceptionTransport == "" || arrExceptionTransport == null ? "" : arrExceptionTransport.toString(),
		  dataSend				=	{arrExceptionTicket:arrExceptionTicket, arrExceptionTransport:arrExceptionTransport};
	  
	  $.ajax({
		type: 'POST',
		url: baseURL+"productSetting/templateAutoCost/getDataProductTicketTransport",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-templateAutoCostDetail :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-templateAutoCostDetail :input").attr("disabled", false);

			var productTicket	=	response.productTicket,
				productTransport=	response.productTransport;
			
			$('#optionTicketProduct, #optionTransportProduct').empty();
			
			if(productTicket.length > 0){
				$.each(productTicket, function(index, array) {
					$('#optionTicketProduct').append($('<option></option>').val(array.VALUE).html(array.OPTIONTEXT));
				});
			}
			
			if(productTransport.length > 0){
				$.each(productTransport, function(index, array) {
					$('#optionTransportProduct').append($('<option></option>').val(array.VALUE).html(array.OPTIONTEXT));
				});
			}
			
			$('#optionTicketProduct, #optionTransportProduct').select2({
				dropdownParent: $("#modal-editor-templateAutoCostDetail")
			});
			
		}
	  });
    }
  }
});

$('#optionCostType').off('click');
$('#optionCostType').on('click', function(e) {
	
	var optionValue	=	$(this).val();
	if(optionValue == "1" || optionValue == 1){
	  $("#containerOptionTicketProduct").removeClass("d-none");
	  $("#containerOptionTransportProduct").addClass("d-none");
	} else {
	  $("#containerOptionTicketProduct").addClass("d-none");
	  $("#containerOptionTransportProduct").removeClass("d-none");
	}

});

$('#editor-templateAutoCostDetail').off('submit');
$('#editor-templateAutoCostDetail').on('submit', function(e) {
	
	e.preventDefault();
	var idAutoDetailsTemplate	=	$("#idAutoDetailsTemplateCostDetail").val(),
		idCostType				=	$("#optionCostType").val(),
		strCostType				=	$("#optionCostType option:selected").text(),
		idTicketProduct			=	$("#optionTicketProduct").val(),
		strTicketProduct		=	$("#optionTicketProduct option:selected").text(),
		idTranportProduct		=	$("#optionTransportProduct").val(),
		strTranportProduct		=	$("#optionTransportProduct option:selected").text(),
		actionURL				=	$('#btnSaveAutoDetailsTemplateDetail').attr('data-actionUrl'),
		dataSend				=	{
										idAutoDetailsTemplate:idAutoDetailsTemplate,
										idCostType:idCostType,
										idTicketProduct:idTicketProduct,
										idTranportProduct:idTranportProduct
									};
	
	if(actionURL == "insertTemplateAutoCostDetail"){
		
		$.ajax({
			type: 'POST',
			url: baseURL+"productSetting/templateAutoCost/"+actionURL,
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#editor-templateAutoCostDetail :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#editor-templateAutoCostDetail :input").attr("disabled", false);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					$('#modal-editor-templateAutoCostDetail').modal('hide');
					getDataTemplateAutoCost();
				}
				
			}
			
		});

	} else {
		
		var elemNumber	=	1,
			costDetail	=	idCostType == 1 ? strTicketProduct : strTranportProduct,
			idProductFee=	idCostType == 1 ? idTicketProduct : idTranportProduct,
			textDetail	=	"["+strCostType+"] "+costDetail;
		if($('.arrTemplateDetailCost').length > 0){
			elemNumber	=	$('.arrTemplateDetailCost:last').attr("data-number") * 1 + 1;
		}
		$("#containerListProduct").append('<li class="list-group-item d-flex justify-content-between align-items-center arrTemplateDetailCost" id="arrTemplateDetailCost'+elemNumber+'" data-number="'+elemNumber+'" data-idCostType="'+idCostType+'" data-idProductFee="'+idProductFee+'">'+textDetail+' <span class="badge badge-primary badge-pill" onclick="$(\'#arrTemplateDetailCost'+elemNumber+'\').remove()"><i class="fa fa-trash"></i></span></li>');
		$('#modal-editor-templateAutoCostDetail').modal('hide');
		
	}

});

$('#modal-editor-templateAutoCostKeyword').off('show.bs.modal');
$('#modal-editor-templateAutoCostKeyword').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  $("#templateAutoCostKeyword").val("");
  
  if ($activeElement.is('[data-toggle]')) {
    if (event.type === 'show') {
      if($activeElement.attr('data-triggerFrom') == "table"){
		  $("#textTemplateNameKeyword").html($activeElement.attr('data-autoDetailsTemplateName'));
	  } else {
		  $("#textTemplateNameKeyword").html($("#templateAutoCostNameEditor").val());
	  }
	  
	  $("#idAutoDetailsTemplateKeyword").val($activeElement.attr('data-idAutoDetailsTemplate'));
	  $('#btnSaveAutoDetailsTemplateKeyword').attr('data-actionUrl', $activeElement.attr('data-actionUrl'));
    }
  }
});

$('#editor-templateAutoCostKeyword').off('submit');
$('#editor-templateAutoCostKeyword').on('submit', function(e) {
	
	e.preventDefault();
	var idAutoDetailsTemplate	=	$("#idAutoDetailsTemplateKeyword").val(),
		actionURL				=	$('#btnSaveAutoDetailsTemplateKeyword').attr('data-actionUrl'),
		keyword					=	$("#templateAutoCostKeyword").val(),
		dataSend				=	{idAutoDetailsTemplate:idAutoDetailsTemplate, keyword:keyword};
	
	if(idAutoDetailsTemplate != 0 && actionURL == "insertTemplateAutoCostKeyword"){
		
		$.ajax({
			type: 'POST',
			url: baseURL+"productSetting/templateAutoCost/"+actionURL,
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#editor-templateAutoCostKeyword :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#editor-templateAutoCostKeyword :input").attr("disabled", false);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					$('#modal-editor-templateAutoCostKeyword').modal('hide');
					getDataTemplateAutoCost();
				}
				
			}
			
		});

	} else {
		var elemNumber	=	1;
		if($('.arrTemplateKeyword').length > 0){
			elemNumber	=	$('.arrTemplateKeyword:last').attr("data-number") * 1 + 1;
		}
		$("#containerListKeyword").append('<li class="list-group-item d-flex justify-content-between align-items-center arrTemplateKeywordList" id="arrTemplateKeyword'+elemNumber+'" data-number="'+elemNumber+'"><span class="arrTemplateKeyword">'+keyword+'</span> <span class="badge badge-primary badge-pill" onclick="$(\'#arrTemplateKeyword'+elemNumber+'\').remove()"><i class="fa fa-trash"></i></span></li>');
		$('#modal-editor-templateAutoCostKeyword').modal('hide');
	}

});

$('#editor-templateAutoCost').off('submit');
$('#editor-templateAutoCost').on('submit', function(e) {
	
	e.preventDefault();
	var templateAutoCostName	=	$("#templateAutoCostNameEditor").val(),
		arrTemplateProduct		=	[],
		arrTemplateKeyword		=	[],
		errorMsg				=	"";
	
	if(templateAutoCostName == "" || templateAutoCostName == null){
		errorMsg	=	"Please enter template name";
	}
	
	if($('.arrTemplateDetailCost').length <= 0){
		errorMsg	=	"Please enter at least 1 product for the new template";
	} else {
		$('.arrTemplateDetailCost').each(function() {
			var idCostType	=	$(this).attr('data-idCostType'),
				idProductFee=	$(this).attr('data-idProductFee');
			arrTemplateProduct.push([idCostType, idProductFee]);
		});
	}
	
	if($('.arrTemplateKeyword').length <= 0){
		errorMsg	=	"Please enter at least 1 keyword for the new template";
	} else {
		$('.arrTemplateKeyword').each(function() {
			arrTemplateKeyword.push($(this).html());
		});
	}
	
	if(errorMsg != ""){
		$('#modalWarning').on('show.bs.modal', function() {
			$('#modalWarningBody').html(errorMsg);
		});
		$('#modalWarning').modal('show');
	} else {
		var dataSend	=	{
								templateAutoCostName:templateAutoCostName,
								arrTemplateKeyword:arrTemplateKeyword,
								arrTemplateProduct:arrTemplateProduct
							};
		$.ajax({
			type: 'POST',
			url: baseURL+"productSetting/templateAutoCost/insertTemplateAutoCost",
			contentType: 'application/json',
			dataType: 'json',
			data: mergeDataSend(dataSend),
			beforeSend:function(){
				$("#editor-templateAutoCost :input").attr("disabled", true);
				NProgress.set(0.4);
				$('#window-loader').modal('show');
			},
			success:function(response){
				
				setUserToken(response);
				$('#window-loader').modal('hide');
				NProgress.done();
				$("#editor-templateAutoCost :input").attr("disabled", false);
				
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);
				});
				$('#modalWarning').modal('show');

				if(response.status == 200){
					$("#templateAutoCostNameEditor").val("");
					$('#modal-editor-templateAutoCost').modal('hide');
					$('.arrTemplateDetailCost').remove();
					$('.arrTemplateKeywordList').remove();
					getDataTemplateAutoCost();
				}
				
			}
		});
	}
});

function confirmDeleteItemTemplateAutoCost(idAutoDetailsTemplateItem, idAutoDetailsTemplate, autoDetailsItemTitle, autoDetailsTemplateName){
	
	var confirmText	=	'The item cost data will be deleted. Details ;<br/><br/>Template Name : <b>'+autoDetailsTemplateName+'</b><br/>Cost Detail : <b>'+autoDetailsItemTitle+'</b><br/><br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idAutoDetailsTemplateItem).attr('data-idAutoDetailsTemplate', idAutoDetailsTemplate).attr('data-function', "deleteTemplateAutoCostItem");
	$confirmDeleteDialog.modal('show');
	
}

function confirmDeleteDataKeyword(idAutoDetailsTitleKeyword, idAutoDetailsTemplate, autoDetailsTitleKeyword, autoDetailsTemplateName){
	
	var confirmText	=	'The keyword data will be deleted. Details ;<br/><br/>Template Name : <b>'+autoDetailsTemplateName+'</b><br/>Template Keyword : <b>'+autoDetailsTitleKeyword+'</b><br/><br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idAutoDetailsTitleKeyword).attr('data-idAutoDetailsTemplate', idAutoDetailsTemplate).attr('data-function', "deleteTemplateAutoCostKeyword");
	$confirmDeleteDialog.modal('show');
	
}

function confirmDeleteDataTemplateAutoCost(idAutoDetailsTemplate, autoDetailsTemplateName){
	
	var confirmText	=	'The template data will be deleted. Details ;<br/><br/>Template Name : <b>'+autoDetailsTemplateName+'</b><br/><br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idAutoDetailsTemplate).attr('data-function', "deleteTemplateAutoCost");
	$confirmDeleteDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var idAutoDetailsTemplate	=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idAutoDetailsTemplate'),
		idData					=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData'),
		funcName				=	$confirmDeleteDialog.find('#confirmBtn').attr('data-function'),
		dataSend				=	{idData:idData, idAutoDetailsTemplate:idAutoDetailsTemplate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/templateAutoCost/"+funcName,
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
				getDataTemplateAutoCost();
			}
		}
	});
});

templateAutoCostFunc();