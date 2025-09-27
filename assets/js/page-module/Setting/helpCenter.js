var $confirmDialog= $('#modal-confirm-action');
if (helpCenterFunc == null){
	var helpCenterFunc	=	function(){
		$(document).ready(function () {
			$('.summernote').summernote({
				height: 250,
				 toolbar: [
					['style', ['style']],
					['font', ['bold', 'underline', 'italic', 'clear']],
					['fontname', ['fontname']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['insert', ['link', 'picture', 'video']],
					['view', ['fullscreen', 'codeview', 'help']]
				]
			}); 
			getDataHelpCenterContentList();
		});	
	}
}

$('#optionPartnerType').off('change');
$('#optionPartnerType').on('change', function(e) {
	getDataHelpCenterContentList();
});

$('#searchKeyword').off('keypress');
$("#searchKeyword").on('keypress',function(e) {
    if(e.which == 13) {
        getDataHelpCenterContentList();
    }
});

function getDataHelpCenterContentList(){
	
	var $tableBody		=	$('#bodyHelpCenterContentList'),
		idPartnerType	=	$('#optionPartnerType').val(),
		searchKeyword	=	$('#searchKeyword').val(),
		dataSend		=	{idPartnerType:idPartnerType, searchKeyword:searchKeyword};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/helpCenter/getDataHelpCenterContentList",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$tableBody.html("<center><i class='fa fa-spinner fa-pulse'></i><br/>Loading data...</center>");
			$('#window-loader').modal('show');
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			$('#window-loader').modal('hide');
			
			var rows			=	"";
			if(response.status != 200){
				$tableBody.addClass("text-center");
				rows	=	'<img src="'+ASSET_IMG_URL+'no-data.png" width="120px"/>'+
							'<h5>No Data Found</h5>'+
							'<p>There are no content found for <b>partner type</b> you have selected</p>';
			} else {
				
				$tableBody.removeClass("text-center");
				var data	=	response.result,
					number	=	1;
				$.each(data, function(index, array) {
					
					var dataArticleList	=	array.ARTICLELIST,
						rowsArticleList	=	'',
						strPartnerType	=	idPartnerType == 1 ? 'Vendor' : 'Driver',
						btnEdit			=	'<i class="fa fa-pencil pull-right ml-2" onclick="getDetailHelpCenterCategory('+array.IDHELPCENTERCATEGORY+')"></i>',
						btnDelete		=	'<i class="fa fa-trash pull-right ml-2" onclick="confirmDeleteHelpCenterCategory('+array.IDHELPCENTERCATEGORY+', \''+strPartnerType+'\', \''+array.CATEGORYNAME+'\')"></i>',
						btnAddArticle	=	'<button type="button" class="button button-success button-sm pull-right mr-2" onclick="openFormAddHelpCenterArticle('+array.IDHELPCENTERCATEGORY+', \''+strPartnerType+'\', \''+array.CATEGORYNAME+'\')"><span><i class="fa fa-plus"></i>New Article</span></button>';
					
					if(dataArticleList == false || dataArticleList.length <= 0){
						rowsArticleList	=	'<p class="text-center">No article content found</p>';
					} else {
						rowsArticleList		+=	'<ul class="list-group px-2">';
						$.each(dataArticleList, function(index, arrayArticleList) {
							var btnEditArticle	=	'<i class="fa fa-pencil pull-right ml-2" onclick="getDetailHelpCenterArticle('+arrayArticleList.IDHELPCENTERARTICLE+')"></i>',
								btnDeleteArticle=	'<i class="fa fa-trash pull-right ml-2" onclick="confirmDeleteHelpCenterArticle('+arrayArticleList.IDHELPCENTERARTICLE+', \''+strPartnerType+'\', \''+array.CATEGORYNAME+'\', \''+arrayArticleList.ARTICLETITLE+'\')"></i>',
								badgeViewStatus	=	arrayArticleList.STATUSVIEW == "1" ? '<span class="badge badge-pill badge-primary pull-right">Seen</span>' : '<span class="badge badge-pill badge-danger pull-right">Hidden</span>';
							rowsArticleList		+=	'<li class="font-weight-bold">'+
														'<span class="list-group-item list-group-item-action mb-1">'+
															arrayArticleList.ARTICLETITLE+badgeViewStatus+'<br/>'+
															'<small>By : '+arrayArticleList.INPUTUSER+'. Last Update : '+arrayArticleList.INPUTDATETIME+'</small>'+
															btnEditArticle+
															btnDeleteArticle+
														'</span>'+
													'</li>';
						});
						rowsArticleList		+=	'</ul>';
					}
					
					rows				+=	'<div class="card">'+
												'<div class="card-header">'+
													'<h2>'+
														'<button class="collapsed" data-toggle="collapse" data-target="#collapse'+number+'" aria-expanded="false">'+
															'<i class="'+array.ICON+'"></i> ['+array.TOTALARTICLE+' Article] '+array.CATEGORYNAME+
															btnDelete+btnEdit+'<br/>'+
															'<small>'+array.DESCRIPTION+'</small>'+
														'</button>'+
													'</h2>'+
												'</div>'+
												'<div id="collapse'+number+'" class="collapse" data-parent="#bodyHelpCenterContentList" style="">'+
													'<div class="card-body">'+
														'<div class="row">'+
															'<div class="col-12 mb-10">'+
																btnAddArticle+
															'</div>'+
															'<div class="col-12">'+
																rowsArticleList+
															'</div>'+
														'</div>'+
													'</div>'+
												'</div>'+
											'</div>';
					number++;
					
				});
				
			}

			$tableBody.html(rows);
			
		}
	});
	
}

$('#modal-helpCenterCategory').off('show.bs.modal');
$('#modal-helpCenterCategory').on('show.bs.modal', function(event) {
  var $activeElement = $(document.activeElement);
  
  if ($activeElement.is('[data-toggle]')) {
    if (event.type === 'show') {
      if($activeElement.attr('data-action') == "insert"){
		  $("#optionPartnerTypeEditor").val($("#optionPartnerType").val());
		  $("#iconCode, #categoryName, #description").val("");
		  $("#idData").val(0);
		  $('#actionType').val("insertHelpCenterCategory");
	  }
    }
  }
});

$('#editor-helpCenterCategory').off('submit');
$('#editor-helpCenterCategory').on('submit', function(e) {
	
	e.preventDefault();
	var dataForm	=	$("#editor-helpCenterCategory :input").serializeArray(),
		functionUrl	=	$("#actionType").val(),
		dataSend	=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/helpCenter/"+functionUrl,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-helpCenterCategory :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-helpCenterCategory :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-helpCenterCategory').modal('hide');
				getDataHelpCenterContentList();
			}
			
		}
	});

});

function getDetailHelpCenterCategory(idHelpCenterCategory){
	
	var dataSend		=	{idHelpCenterCategory:idHelpCenterCategory};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/helpCenter/getDetailHelpCenterCategory",
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

				$("#optionPartnerTypeEditor").val(detailData.IDPARTNERTYPE);
				$("#iconCode").val(detailData.ICON);
				$("#categoryName").val(detailData.CATEGORYNAME);
				$("#description").val(detailData.DESCRIPTION);
				$("#idData").val(idHelpCenterCategory);
				$("#actionType").val("updateHelpCenterCategory");
				
				$('#modal-helpCenterCategory').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
	
}

function confirmDeleteHelpCenterCategory(idHelpCenterCategory, partnerType, categoryName){
	
	var confirmText	=	'Data help center category will be deleted. Details;'+
						'<div class="order-details-customer-info mt-10 mb-10">'+
							'<ul class="ml-5">'+
								'<li> <span>Partner Type</span> <span>'+partnerType+'</span> </li>'+
								'<li> <span>Category Name</span> <span>'+categoryName+'</span> </li>'+
							'</ul>'+
						'</div>'+
						'Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idHelpCenterCategory).attr('data-function', "deleteHelpCenterCategory");
	$confirmDialog.modal('show');
	
}

function openFormAddHelpCenterArticle(idHelpCenterCategory, strPartnerType, categoryName){
	
	$("#helpCenterPartnerTypeStr").html(strPartnerType);
	$("#helpCenterCategoryStr").html(categoryName);
	$("#articleTitle, #articleContent").val("");
	$('input[name="radioStatusView"][value="1"]').prop('checked', true);
	$("#idHelpCenterCategory").val(idHelpCenterCategory);
	$("#idHelpCenterArticle").val(0);
	$("#actionTypeArticle").val("insertHelpCenterArticle");
	
	$('#modal-helpCenterArticle').modal('show');
	
}

$('#editor-helpCenterArticle').off('submit');
$('#editor-helpCenterArticle').on('submit', function(e) {
	
	e.preventDefault();
	var dataForm	=	$("#editor-helpCenterArticle :input").serializeArray(),
		functionUrl	=	$("#actionTypeArticle").val(),
		dataSend	=	{};
		
	$.each(dataForm, function() {
		dataSend[this.name] = this.value;
	});
	
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/helpCenter/"+functionUrl,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			$("#editor-helpCenterArticle :input").attr("disabled", true);
			NProgress.set(0.4);
			$('#window-loader').modal('show');
		},
		success:function(response){
			
			setUserToken(response);
			$('#window-loader').modal('hide');
			NProgress.done();
			$("#editor-helpCenterArticle :input").attr("disabled", false);
			
			$('#modalWarning').on('show.bs.modal', function() {
				$('#modalWarningBody').html(response.msg);
			});
			$('#modalWarning').modal('show');

			if(response.status == 200){
				$('#modal-helpCenterArticle').modal('hide');
				getDataHelpCenterContentList();
			}
			
		}
	});

});

function getDetailHelpCenterArticle(idHelpCenterArticle){
	
	var dataSend		=	{idHelpCenterArticle:idHelpCenterArticle};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/helpCenter/getDetailHelpCenterArticle",
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

				$("#helpCenterPartnerTypeStr").html(detailData.PARTNERTYPE);
				$("#helpCenterCategoryStr").html(detailData.CATEGORYNAME);
				$("#articleTitle").val(detailData.ARTICLETITLE);
				$('input[name="radioStatusView"][value="'+detailData.STATUSVIEW+'"]').prop('checked', true);
				$("#articleContent").val("");
				$(".summernote").summernote("code", detailData.ARTICLECONTENT);
				$("#idHelpCenterCategory").val(detailData.IDHELPCENTERCATEGORY);
				$("#idHelpCenterArticle").val(idHelpCenterArticle);
				$("#actionTypeArticle").val("updateHelpCenterArticle");
				
				$('#modal-helpCenterArticle').modal('show');
			} else {
				$('#modalWarning').on('show.bs.modal', function() {
					$('#modalWarningBody').html(response.msg);			
				});
				$('#modalWarning').modal('show');
			}
			
		}
	});
	
}

function confirmDeleteHelpCenterArticle(idHelpCenterArticle, partnerType, categoryName, articleTitle){
	
	var confirmText	=	'Data help center article will be deleted. Details;'+
						'<div class="order-details-customer-info mt-10 mb-10">'+
							'<ul class="ml-5">'+
								'<li> <span>Partner Type</span> <span>'+partnerType+'</span> </li>'+
								'<li> <span>Category Name</span> <span>'+categoryName+'</span> </li>'+
								'<li> <span>Article Title</span> <span>'+articleTitle+'</span> </li>'+
							'</ul>'+
						'</div>'+
						'Are you sure?';
		
	$confirmDialog.find('#modal-confirm-body').html(confirmText);
	$confirmDialog.find('#confirmBtn').attr('data-idData', idHelpCenterArticle).attr('data-function', "deleteHelpCenterArticle");
	$confirmDialog.modal('show');
	
}

$('#confirmBtn').off('click');
$('#confirmBtn').on('click', function(e) {
	
	var idData	=	$confirmDialog.find('#confirmBtn').attr('data-idData'),
		funcName=	$confirmDialog.find('#confirmBtn').attr('data-function'),
		dataSend=	{idData:idData};
	
	$.ajax({
		type: 'POST',
		url: baseURL+"setting/helpCenter/"+funcName,
		contentType: 'application/json',
		dataType: 'json',
		data: mergeDataSend(dataSend),
		beforeSend:function(){
			NProgress.set(0.4);
			$confirmDialog.modal('hide');
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
				getDataHelpCenterContentList();
			}
		}
	});
});

helpCenterFunc();