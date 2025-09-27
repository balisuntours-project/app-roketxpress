if (notificationFunc == null){
	var notificationFunc	=	function(){
		$(document).ready(function () {
			setOptionHelper('optionMessageType', 'dataMessageAdminType');
			getDataUnreadNotification();			
			getDataReadNotification();			
		});	
	}
}

$('#optionMessageType').off('change');
$('#optionMessageType').on('change', function(e) {
	getDataUnreadNotification();			
	getDataReadNotification();			
});

$('#keywordSearch').off('keydown');
$('#keywordSearch').on('keydown', function(e) {
	if(e.which === 13){
		getDataUnreadNotification();
		getDataReadNotification();
	}
});

function getDataUnreadNotification(page = 1){
	
	var $tableBody		=	$('#tableUnreadMessage'),
		idMessageType	=	$('#optionMessageType').val(),
		keywordSearch	=	$('#keywordSearch').val(),
		dataSend		=	{page:page, status:0, idMessageType:idMessageType, keywordSearch:keywordSearch};

	$.ajax({
		type: 'POST',
		url: baseURL+"notification/getDataNotification",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#noDataUnreadMessage").remove();
			$tableBody.html("<center class='my-auto mx-auto'><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var data			=	response.result.data,
				rows			=	"";
			
			if(data.length === 0){
				rows	=	'<div class="col-12 mt-40 mb-30 text-center" id="noDataUnreadMessage">'+
								'<img src="'+ASSET_IMG_URL+'no-data.png" width="120px"/>'+
								'<h5>No Data Found</h5>'+
								'<p>There are no unread message</p>'+
							'</div>';
			} else {
			
				rows	+=	'<div class="accordion w-100" id="accordionUnreadMessage">';
				$.each(data, function(index, array) {
					
					var btnDetail	=	generateButtonDetail(array.IDMESSAGEADMIN, array.IDMESSAGEADMINTYPE, array.PARAMLIST);
					rows			+=	'<div class="card">'+
											'<div class="card-header">'+
												'<h2>'+
													'<button>'+
														'<i class="'+array.ICON+'"></i> '+array.TITLE+'<br/>'+
														'<p class="h6">'+array.MESSAGE+'</p>'+
														'<small>'+array.DATETIMEINSERT+'</small>'+
														btnDetail+
													'</button>'+
												'</h2>'+
											'</div>'+
										'</div>';
					
				});
				rows	+=	'</div>';
				
			}
			
			if(page != 1){
				$('#btnPreviousPageUnreadMessage').on('click', function(e) {
					getDataUnreadNotification(page - 1);
				});
				$("#btnPreviousPageUnreadMessage").removeClass("d-none");
			} else {
				$('#btnPreviousPageUnreadMessage').off('click').addClass("d-none");
			}

			if(page != response.result.pageTotal && data.length > 0){
				$('#btnNextPageUnreadMessage').on('click', function(e) {
					getDataUnreadNotification(page + 1);
				});
				$("#btnNextPageUnreadMessage").removeClass("d-none");
			} else {
				$('#btnNextPageUnreadMessage').off('click');
				$("#btnNextPageUnreadMessage").addClass("d-none");
			}

			generateDataInfo("tableDataCountUnreadMessage", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			$(".btnDetailNotification").off('click');
			$(".btnDetailNotification").on('click', function() {
				openMenuFromNotification(this);
			});
			
		}
	});
	
}

function getDataReadNotification(page = 1){
	
	var $tableBody		=	$('#tableReadMessage'),
		idMessageType	=	$('#optionMessageType').val(),
		keywordSearch	=	$('#keywordSearch').val(),
		dataSend		=	{page:page, status:1, idMessageType:idMessageType, keywordSearch:keywordSearch};

	$.ajax({
		type: 'POST',
		url: baseURL+"notification/getDataNotification",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$("#noDataReadMessage").remove();
			$tableBody.html("<center class='my-auto mx-auto'><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var data			=	response.result.data,
				rows			=	"";
			
			if(data.length === 0){
				rows	=	'<div class="col-12 mt-40 mb-30 text-center" id="noDataReadMessage">'+
								'<img src="'+ASSET_IMG_URL+'no-data.png" width="120px"/>'+
								'<h5>No Data Found</h5>'+
								'<p>There are no message</p>'+
							'</div>';
			} else {
			
				rows	+=	'<div class="accordion w-100" id="accordionReadMessage">';
				$.each(data, function(index, array) {
					
					var btnDetail	=	generateButtonDetail(array.IDMESSAGEADMIN, array.IDMESSAGEADMINTYPE, array.PARAMLIST);
					rows			+=	'<div class="card">'+
											'<div class="card-header">'+
												'<h2>'+
													'<button>'+
														'<i class="'+array.ICON+'"></i> '+array.TITLE+'<br/>'+
														'<p class="h6">'+array.MESSAGE+'</p>'+
														'<small>'+array.DATETIMEINSERT+'</small>'+
														btnDetail+
													'</button>'+
												'</h2>'+
											'</div>'+
										'</div>';
					
				});
				rows	+=	'</div>';
				
			}
			
			if(page != 1){
				$('#btnPreviousPageReadMessage').on('click', function(e) {
					getDataReadNotification(page - 1);
				});
				$("#btnPreviousPageReadMessage").removeClass("d-none");
			} else {
				$('#btnPreviousPageReadMessage').off('click').addClass("d-none");
			}

			if(page != response.result.pageTotal && data.length > 0){
				$('#btnNextPageReadMessage').on('click', function(e) {
					getDataReadNotification(page + 1);
				});
				$("#btnNextPageReadMessage").removeClass("d-none");
			} else {
				$('#btnNextPageReadMessage').off('click');
				$("#btnNextPageReadMessage").addClass("d-none");
			}

			generateDataInfo("tableDataCountReadMessage", response.result.dataStart, response.result.dataEnd, response.result.dataTotal)
			$tableBody.html(rows);
			$(".btnDetailNotification").off('click');
			$(".btnDetailNotification").on('click', function() {
				openMenuFromNotification(this);
			});
			
		}
	});
	
}

notificationFunc();