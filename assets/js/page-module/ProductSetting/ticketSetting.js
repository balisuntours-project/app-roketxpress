var $confirmDeleteDialog= $('#modal-confirm-action');

if (ticketSettingFunc == null){
	var ticketSettingFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionVendorFilter', 'dataVendorTicket');
			setOptionHelper('optionVendorEditor', 'dataVendorTicket');
			setOptionHelper('optionProductEditor', 'dataProductTicket');
			getDataTicketVendorPrice();
			$("#optionVendorFilter").select2();
		});	
	}
}

$('#optionVendorFilter').off('change');
$('#optionVendorFilter').on('change', function(e) {
	getDataTicketVendorPrice();
});

$('#productName').off('keydown');
$('#productName').on('keydown', function(e) {
	if(e.which === 13){
		getDataTicketVendorPrice();
	}
});

function getDataTicketVendorPrice(){
	
	var $tableBody	=	$('#table-ticketVendorPrice > tbody'),
		columnNumber=	$('#table-ticketVendorPrice > thead > tr > th').length,
		productName	=	$('#productName').val(),
		idVendor	=	$('#optionVendorFilter').val(),
		dataSend	=	{productName:productName, idVendor:idVendor};
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/ticket/getDataTicketVendorPrice",
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
					
					var btnEdit			=	'<button class="button button-xs button-box button-primary" onclick="getDetailTicketVendorPrice('+array.IDVENDORTICKETPRICE+')">'+
												'<i class="fa fa-pencil"></i>'+
											'</button>',
						btnDelete		=	'<button class="button button-xs button-box button-danger" onclick="confirmDeleteDataTicketVendorPrice('+array.IDVENDORTICKETPRICE+', \''+array.VENDORNAME+'\', \''+array.PRODUCTNAME+'\')">'+
												'<i class="fa fa-trash"></i>'+
											'</button>',
						voucherStatus	=	array.VOUCHERSTATUS == "1" ? "Yes" : "No";
					rows				+=	"<tr>"+
												"<td align='right'>"+number+"</td>"+
												"<td>"+array.PRODUCTNAME+"</td>"+
												"<td>"+array.VENDORNAME+"</td>"+
												"<td>"+voucherStatus+"</td>"+
												"<td>"+array.MINPAX+" - "+array.MAXPAX+"</td>"+
												"<td align='right'>"+numberFormat(array.PRICEADULT)+"</td>"+
												"<td align='right'>"+numberFormat(array.PRICECHILD)+"</td>"+
												"<td align='right'>"+numberFormat(array.PRICEINFANT)+"</td>"+
												"<td>"+array.NOTES+"</td>"+
												"<td align='center'>"+btnEdit+btnDelete+"</td>"+
											"</tr>";
					number++;
					
				});
				
				$tableBody.html(rows);
			
			}
			
		}
		
	});
	
}

$('#modal-editor-ticketVendorPrice').off('show.bs.modal');
$('#modal-editor-ticketVendorPrice').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  
  if ($activeElement.is('[data-toggle]')) {
    if (event.type === 'show') {
      if($activeElement.attr('data-action') == "insert"){
		$("#paxRangeMin, #paxRangeMax").val(1);
		$("#optionVoucherStatus").val($("#optionVoucherStatus option:first").val());
		$("#optionProductEditor").val($("#optionProductEditor option:first").val());
		$("#priceAdult, #priceChild, #priceInfant, #idVendorTicketPrice").val(0);
		$("#notes").val("");
		$('#optionProductEditor, #optionVendorEditor').select2({
			dropdownParent: $("#modal-editor-ticketVendorPrice")
		});
	  }
    }
  }

});

$('#editor-ticketVendorPrice').off('submit');
$('#editor-ticketVendorPrice').on('submit', function(e) {
	
	e.preventDefault();
	var idVendorTicketPrice		=	$("#idVendorTicketPrice").val(),
		actionURL				=	idVendorTicketPrice == 0 ? "addTicketVendorPrice" : "updateTicketVendorPrice";
		dataForm				=	$("#editor-ticketVendorPrice :input").serializeArray(),
		dataSend				=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/ticket/"+actionURL,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-ticketVendorPrice :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-ticketVendorPrice :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-editor-ticketVendorPrice').modal('hide');
				getDataTicketVendorPrice();
			}
			
		}
	});
});

function getDetailTicketVendorPrice(idVendorTicketPrice){
	
	var dataSend		=	{idVendorTicketPrice:idVendorTicketPrice};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/ticket/detailTicketVendorPrice",
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

				$("#optionProductEditor").val(detailData.IDPRODUCT);
				$("#optionVendorEditor").val(detailData.IDVENDOR);
				$("#optionVoucherStatus").val(detailData.VOUCHERSTATUS);
				$("#paxRangeMin").val(numberFormat(detailData.MINPAX));
				$("#paxRangeMax").val(numberFormat(detailData.MAXPAX));
				$("#priceAdult").val(numberFormat(detailData.PRICEADULT));
				$("#priceChild").val(numberFormat(detailData.PRICECHILD));
				$("#priceInfant").val(numberFormat(detailData.PRICEINFANT));
				$("#notes").val(detailData.NOTES);
				$("#idVendorTicketPrice").val(idVendorTicketPrice);
				$('#modal-editor-ticketVendorPrice').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
	
}

function confirmDeleteDataTicketVendorPrice(idVendorTicketPrice, vendorName, productName){
	
	var confirmText	=	'The ticket price data will be deleted. Details ;<br/><br/>Vendor : <b>'+vendorName+'</b><br/>Ticket : <b>'+productName+'</b>.<br/><br/>Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idVendorTicketPrice).attr('data-function', "deleteTicketVendorPrice");
	$confirmDeleteDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var idData	=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData'),
		funcName=	$confirmDeleteDialog.find('#confirmBtn').attr('data-function'),
		dataSend=	{idData:idData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"productSetting/ticket/"+funcName,
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
				getDataTicketVendorPrice();
			}
		}
	});
});

ticketSettingFunc();