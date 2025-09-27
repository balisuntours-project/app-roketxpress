if (knowledgeFunc == null){
	var knowledgeFunc	=	function(){
		$(document).ready(function () {
			getDataKnowledge();
		});	
	}
}

function getDataKnowledge(){
	$.ajax({
		type: 'POST',
		url: baseURL+"knowledge/getDataKnowledge",
		contentType: 'application/json',
		dataType: 'json',
		cache: false,
		data: mergeDataSend({}),
		beforeSend:function(){
			NProgress.set(0.4);
		},
		success:function(response){
			NProgress.done();
			setUserToken(response);
			
			var dataResult	=	response.dataResult;
			
			if(dataResult){
				var elemTabsPanel	=	elemTabsContent	=	'',
					arrNameGroup	=	[];
				$.each(dataResult, function(index, array) {
					var nameGroup	=	array.NAMEGROUP,
						classShow	=	index == 0 ? 'show' : '',
						classActive	=	index == 0 ? 'active' : '',
						classText	=	index == 0 ? 'text-white' : '',
						urlFilePdf	=	array.URLFILEPDF;
					if(!arrNameGroup.includes(nameGroup)){
						var tabName		=	nameGroup.replace(/[^a-zA-Z0-9]/g, '');
						elemTabsPanel	+=	'<li class="nav-item"><a class="nav-link '+classActive+'" data-toggle="tab" href="#'+tabName+'Tab">'+nameGroup+'</a></li>';
						
						if(elemTabsContent != ''){
							elemTabsContent	+=		'</ul>'+
												'</div>';
						}
						
						elemTabsContent	+=	'<div class="tab-pane fade '+classShow+' '+classActive+'" id="'+tabName+'Tab">'+
												'<ul class="list-group list-group-horizontal">';
						
						arrNameGroup.push(nameGroup);
					}
					
					elemTabsContent	+=	'<li class="list-group-item listGroupItemKnowledge px-3 p-2 '+classActive+' '+classText+'" data-urlFilePdf="'+urlFilePdf+'">'+
											'<h6 class="listGroupItemKnowledge-h6 mb-0 '+classText+'">'+array.NAMEDETAIL+'</h6>'+
											'<p>'+array.DESCRIPTION+'</p>'+
										'</li>';
					if(index == 0) renderPDFFile(urlFilePdf);
				});

				elemTabsContent	+=		'</ul>'+
									'</div>';
				$("#knowledge-tabsPanel").html(elemTabsPanel);
				$("#knowledge-tabsContent").html(elemTabsContent);
				activateOnclickListGroupItemKnowledge();
			}
		}
	});
}

function renderPDFFile(url){
	var pdfContainer = document.getElementById('pdf-viewer-container');
	var scale = 0.95;
	
	$("#knowledge-btnNewTab").attr('href', url);
	pdfContainer.setAttribute('data-urlFilePdf', url);
	pdfContainer.innerHTML = '';
    pdfjsLib.getDocument(url).promise.then(function (pdf) {
		for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
			pdf.getPage(pageNumber).then(function(page) {
				var canvas = document.createElement('canvas');
				var context = canvas.getContext('2d');

				var viewport = page.getViewport({ scale: 1 });
				scale = pdfContainer.clientWidth / viewport.width * 80 / 100;
				viewport = page.getViewport({ scale: scale });

				canvas.width = viewport.width;
				canvas.height = viewport.height;
				pdfContainer.appendChild(canvas);

				var renderContext = {
					canvasContext: context,
					viewport: viewport
				};

				page.render(renderContext);
				canvas.style.display = 'block';
				canvas.style.margin = 'auto';
			});
		}
		
		function updateScale(newScale) {
			const canvas = document.querySelector('#pdf-viewer-container canvas');
			scale = newScale;

			canvas.style.transform = `scale(${scale})`;
			canvas.style.transformOrigin = 'top center';
			console.log(canvas.style.transform);
		}
		
		$("#knowledge-zoom-in").off("click");
		$("#knowledge-zoom-in").on("click", function(e) {
			let currentScale = getCurrentCanvasScale();
			updateScale(currentScale + 0.2);
		});
		
		$("#knowledge-zoom-out").off("click");
		$("#knowledge-zoom-out").on("click", function(e) {
			let currentScale = getCurrentCanvasScale();
			updateScale(Math.max(0.5, currentScale - 0.2));
		});
		
		function getCurrentCanvasScale(){
			const canvas = document.querySelector('#pdf-viewer-container canvas'),
				scaleStr= canvas.style.transform;
				
			return extractScaleValue(scaleStr);
		}
		
		function extractScaleValue(scaleStr) {
			const match = scaleStr.match(/scale\(([^)]+)\)/);
			return match ? parseFloat(match[1]) : 1;
		}
    });
}

function activateOnclickListGroupItemKnowledge(){
	$(".listGroupItemKnowledge").off("click");
	$(".listGroupItemKnowledge").on("click", function(e) {
		var urlFilePdf			=	$(this).attr('data-urlFilePdf'),
			currentUrlFilePdf	=	$('#pdf-viewer-container').attr('data-urlFilePdf');

		$('.listGroupItemKnowledge').removeClass('active text-white');
		$('.listGroupItemKnowledge-h6').removeClass('text-white');

		$(this).addClass('active text-white');
		$(this).find('h6').addClass('text-white');
		if(currentUrlFilePdf != urlFilePdf) renderPDFFile(urlFilePdf);
	});
}

knowledgeFunc();