var $confirmDeleteDialog= $('#modal-confirm-action');

if (currencyExchangeFunc == null){
	var currencyExchangeFunc	=	function(){
		$(document).ready(function () {
			getDataCurrencyExchange();
		});	
	}
}

$('#optionCurrency').off('change');
$('#optionCurrency').on('change', function(e) {
	getDataCurrencyExchange();
});

function generateDataTable(page){
	getDataCurrencyExchange(page);
}

function getDataCurrencyExchange(page = 1){
	
	var $tableBody		=	$('#table-currencyExchange > tbody'),
		columnNumber	=	$('#table-currencyExchange > thead > tr > th').length,
		currency		=	$('#optionCurrency').val(),
		dataSend		=	{page:page, currency:currency};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/currencyExchange/getDataCurrencyExchange",
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
			
			if(data.length === 0){
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {

				var currentExchange	=	response.currentExchange;
				$("#currentExchange").html(numberFormat(currentExchange));

				$.each(data, function(index, array) {
					var btnDelete		=	"",
						btnEdit			=	'<button class="button button-xs button-box button-primary" onclick="getDetailCurrencyExchange('+array.IDCURRENCYEXCHANGE+', \''+array.CURRENCY+'\', \''+array.DATESTARTSTREDITOR+'\', '+array.EXCHANGEVALUE+')">'+
												'<i class="fa fa-pencil"></i>'+
											'</button>';
											
					if(array.DELETEABLE == "1"){
						btnDelete		=	'<button class="button button-xs button-box button-danger" onclick="confirmDeleteCurrencyExchange('+array.IDCURRENCYEXCHANGE+', \''+array.CURRENCY+'\', \''+array.DATESTARTSTR+'\', \''+array.DATESTART+'\', '+array.EXCHANGEVALUE+')">'+
												'<i class="fa fa-trash"></i>'+
											'</button>';
					}
					
					rows	+=	"<tr>"+
									"<td>"+array.DATESTARTSTR+"</td>"+
									"<td align='right'>"+numberFormat(array.EXCHANGEVALUE)+"</td>"+
									"<td align='center'>"+btnEdit+" "+btnDelete+"</td>"+
								"</tr>";
				});
				
			}

			generatePagination("tablePaginationCurrencyExchange", page, response.result.pageTotal);
			generateDataInfo("tableDataCountCurrencyExchange", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

$('#modal-editor-currencyExchange').off('show.bs.modal');
$('#modal-editor-currencyExchange').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  
	if ($activeElement.is('[data-toggle]')) {
		if (event.type === 'show') {
			if($activeElement.attr('data-action') == "insert"){
				$("#exchangeValue").val(1);
				$("#originDateStart").val("");
				$("#optionCurrencyEditor").attr("disabled", false);
				$("#idCurrencyExchange").val(0);
				$("#actionType").val("insert");
			}
		}
	}
});

$('#editor-currencyExchange').off('submit');
$('#editor-currencyExchange').on('submit', function(e) {
	
	e.preventDefault();
	var idCurrencyExchange	=	$("#idCurrencyExchange").val(),
		actionURL			=	idCurrencyExchange == 0 ? "addDataCurrencyExchange" : "updateCurrencyExchange";
		dataForm			=	$("#editor-currencyExchange :input").serializeArray(),
		currency			=	$("#optionCurrencyEditor").find('option:selected').val(),
		dataSend			=	{currency:currency};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/currencyExchange/"+actionURL,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-currencyExchange :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-currencyExchange :input").attr("disabled", false);
			if(idCurrencyExchange != 0) $("#optionCurrencyEditor").attr("disabled", true);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-editor-currencyExchange').modal('hide');
				getDataCurrencyExchange();
			}
			
		}
	});
});

function getDetailCurrencyExchange(idCurrencyExchange, currencyName, dateStart, exchangeValue){
	
	$("#optionCurrencyEditor").val(currencyName);
	$("#dateStart, #originDateStart").val(dateStart);
	$("#exchangeValue").val(numberFormat(exchangeValue));
	$("#idCurrencyExchange").val(idCurrencyExchange);
	$("#actionType").val("update");
	$("#optionCurrencyEditor").attr("disabled", true);
	$('#modal-editor-currencyExchange').modal('show');
	
}

function confirmDeleteCurrencyExchange(idCurrencyExchange, currencyName, dateStartStr, dateStart, exchangeValue){
	
	var confirmText	=	'The currency exchange data will be deleted. Details ;<br/><br/>'+
						'<div class="order-details-customer-info">'+
							'<ul class="ml-5">'+
								'<li> <span>Currency</span>			<span>'+currencyName+'</span> </li>'+
								'<li> <span>Date Start</span>		<span>'+dateStartStr+'</span> </li>'+
								'<li> <span>Exchange Value</span>	<span>'+numberFormat(exchangeValue)+'</span> </li>'+
							'</ul>'+
						'</div><br/>'+
						'Are you sure?';
		
	$confirmDeleteDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData', idCurrencyExchange).attr('data-currencyName', currencyName).attr('data-dateStart', dateStart).attr('data-function', "deleteCurrencyExchange");
	$confirmDeleteDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var idData		=	$confirmDeleteDialog.find('#confirmBtn').attr('data-idData'),
		currencyName=	$confirmDeleteDialog.find('#confirmBtn').attr('data-currencyName'),
		dateStart	=	$confirmDeleteDialog.find('#confirmBtn').attr('data-dateStart'),
		funcName	=	$confirmDeleteDialog.find('#confirmBtn').attr('data-function'),
		dataSend	=	{idData:idData, currencyName:currencyName, dateStart:dateStart};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/currencyExchange/"+funcName,
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
				getDataCurrencyExchange();
			}
		}
	});
});

currencyExchangeFunc();