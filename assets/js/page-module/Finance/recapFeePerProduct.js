if (recapFeePerProductFunc == null){
	var recapFeePerProductFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionProductType', 'dataProductType');
			getDataRecapFeePerProduct();
		});	
	}
}

$('#startDate, #endDate, #optionProductType').off('change');
$('#startDate, #endDate, #optionProductType').on('change', function(e) {
	getDataRecapFeePerProduct();
});

$('#productNameFilter').off('keypress');
$("#productNameFilter").on('keypress',function(e) {
    if(e.which == 13) {
        getDataRecapFeePerProduct();
    }
});

function generateDataTable(page){
	getDataRecapFeePerProduct(page);
}

function getDataRecapFeePerProduct(page = 1){
	
	var $tableBody		=	$('#table-recapFeePerProduct > tbody'),
		columnNumber	=	$('#table-recapFeePerProduct > thead > tr > th').length,
		idProductType	=	$('#optionProductType').val(),
		productName		=	$('#productNameFilter').val(),
		startDate		=	$('#startDate').val(),
		endDate			=	$('#endDate').val(),
		dataSend		=	{page:page, productName:productName, idProductType:idProductType, startDate:startDate, endDate:endDate};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"finance/recapFeePerProduct/getDataRecapFeePerProduct",
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
				$('#excelDataRecapFeeProduct').addClass('d-none').off("click").attr("href", "");
			} else {
				
				if(response.urlExcelRecap != "") $('#excelDataRecapFeeProduct').removeClass('d-none').on("click").attr("href", response.urlExcelRecap);
				$.each(data, function(index, array) {
					
					rows	+=	"<tr>"+
									"<td>"+array.PRODUCTTYPE+"</td>"+
									"<td>"+array.PRODUCTNAME+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALRESERVATION)+"</td>"+
									"<td align='right'>"+numberFormat(array.TOTALFEE)+"</td>"+
								"</tr>";
					
				});
				
			}

			generatePagination("tablePaginationRecapFeePerProduct", page, response.result.pageTotal);
			generateDataInfo("tableDataCountRecapFeePerProduct", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			
		}
	});
	
}

recapFeePerProductFunc();