if (recapPerDateFunc == null){
	var recapPerDateFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMonth', 'optionMonth', thisMonth, false);
			setOptionHelper('optionYear', 'optionYear', false, false);
			getDataRecapPerDate();
		});	
	}
}

$('#optionMonth, #optionYear').off('change');
$('#optionMonth, #optionYear').on('change', function(e) {
	getDataRecapPerDate();
});

function generateDataTable(page){
	getDataRecapPerDate(page);
}

function getDataRecapPerDate(page = 1){
	
	var $tableBody		=	$('#table-recapPerDate > tbody'),
		columnNumber	=	$('#table-recapPerDate > thead > tr > th').length;
		optionMonth		=	$('#optionMonth').val(),
		optionYear		=	$('#optionYear').val(),
		dataSend		=	{page:page, month:optionMonth, year:optionYear};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"report/recapPerDate/getDataRecapPerDate",
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
			
			var data			=	response.result.data,
				rows			=	"";
			
			if(data.length === 0){
				$('#excelReport').addClass('d-none').off("click").attr("href", "");
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$('#excelReport').removeClass('d-none').on("click").attr("href", response.urlExcelReport);
				$.each(data, function(index, array) {
					
					rows	+=	"<tr>"+
									"<td align='center'>"+array.DATERESERVATION+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALRESERVATION)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALACTIVERESERVATION)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALCANCELRESERVATION)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALHANDLEBYDRIVER)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALHANDLEBYVENDOR)+"</td>"+
								"</tr>";
					
				});
				
			}

			generatePagination("tablePaginationRecapPerDate", page, response.result.pageTotal);
			generateDataInfo("tableDataCountRecapPerDate", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

recapPerDateFunc();