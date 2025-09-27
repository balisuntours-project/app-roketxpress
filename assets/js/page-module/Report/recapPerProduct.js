if (recapPerProductFunc == null){
	var recapPerProductFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionSource', 'dataSource');
			getDataRecapPerProduct();
		});	
	}
}

$('#filterDataRecapPerProduct').off('click');
$('#filterDataRecapPerProduct').on('click', function(e) {
	getDataRecapPerProduct();
});

function generateDataTable(page){
	getDataRecapPerProduct(page);
}

function getDataRecapPerProduct(page = 1){
	
	var $tableBody		=	$('#table-recapPerProduct > tbody'),
		columnNumber	=	$('#table-recapPerProduct > thead > tr > th').length,
		productName		=	$('#productNameFilter').val(),
		idSource		=	$('#optionSource').val(),
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		dataSend		=	{page:page, productName:productName, idSource:idSource, startDate:startDate, endDate:endDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"report/recapPerProduct/getDataRecapPerProduct",
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
				rows	=	"<tr>"+
								"<td colspan='"+columnNumber+"' align='center'><center>No data found</center></td>"+
							"</tr>";
			} else {
				
				$.each(data, function(index, array) {
					
					rows	+=	"<tr>"+
									"<td>"+array.PRODUCTTYPE+"</td>"+
									"<td>"+array.PARTNERNAME+"</td>"+
									"<td>"+array.PRODUCTNAME+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALRESERVATION)+"</td>"+
								"</tr>";
					
				});
				
			}

			generatePagination("tablePaginationRecapPerProduct", page, response.result.pageTotal);
			generateDataInfo("tableDataCountRecapPerProduct", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

recapPerProductFunc();