/*---------------------*/

function loadSlidesharePosts(method,direction){
	
	if(typeof(method)==='undefined'){
		method = 'add';
	}
	
	count 		= parseInt($( "#tx-moox-social-slideshare-count" ).val());
	page 		= parseInt($( "#tx-moox-social-slideshare-page" ).val());
	offset 		=  parseInt($( "#tx-moox-social-slideshare-offset" ).val());
	perrequest 	=  parseInt($( "#tx-moox-social-slideshare-perrequest" ).val());		
	
	if(method=='replace'){
		if(direction=='previous'){
			newoffset	= offset - perrequest;
			newpage		= page-1;			
		} else {
			if(direction=='next'){
				newoffset	= offset + perrequest;	
				newpage		= page+1;
			} else {
				newoffset	=  parseInt(direction);
				newpage		= (newoffset/perrequest)+1
				if(newoffset>0){
					direction = 'next';
				} else {
					direction = 'previous';
				}
			}
							
		}
	} else {	
		newoffset	= offset + perrequest;		
	}
	
	query = 'tx_mooxsocial_pi5[controller]=Slideshare&tx_mooxsocial_pi5[action]=listAjax';	
	query = query + '&tx_mooxsocial_pi5[offset]=' + newoffset;
	query = query + '&tx_mooxsocial_pi5[perrequest]=' + perrequest;
	<f:if condition="{source}=='api'">
	query = query + '&tx_mooxsocial_pi5[source]=api';
	query = query + '&tx_mooxsocial_pi5[page]={page}';
	</f:if>	
	query = query + '&type=89657606'		
	$.ajax({
		url: "{ajaxurl}",
		data: query,
		success: function(result) {
			if(method=='add'){				
				if(result!=" "){					
					$( ".tx-moox-social-slideshare-listing" ).append(result);					
				} else {
					<f:if condition="{source}=='api'">
					$( "#tx-moox-social-slideshare-loadmore-add" ).hide(0);								
					</f:if>
				}				
			} else {
				if(result!=" "){					
					$( ".tx-moox-social-slideshare-listing" ).html(result);					
				}				
				<f:if condition="{source}=='api'">
				if(result==" "){					
					$( ".tx-moox-social-slideshare-loadmore-next" ).hide(0);					
				}
				</f:if>
			}
			$( "#tx-moox-social-slideshare-offset" ).val(newoffset);
			if(method=='replace'){
				if(direction=='previous'){				
					if(newoffset<=0){
						$( ".tx-moox-social-slideshare-loadmore-previous" ).hide(0);
					}
					$( ".tx-moox-social-slideshare-loadmore-next" ).show(0);					
					$( "#tx-moox-social-slideshare-page" ).val(newpage);
				} else {					
					check = newoffset + perrequest;
					if(check>=count){
						$( ".tx-moox-social-slideshare-loadmore-next" ).hide(0);
					}
					$( ".tx-moox-social-slideshare-loadmore-previous" ).show(0);					
					$( "#tx-moox-social-slideshare-page" ).val(newpage);
				}
				
				correction = 0;
				if(newpage<5){
					correction = 5-newpage;
				}				
				$( ".tx-moox-social-slideshare-pagination-item" ).each( function( index, element ){
					itemId = $( this ).attr('id');		
					pageId   = parseInt(itemId.replace("tx-moox-social-slideshare-pagination-",""));
					$( this ).removeClass("tx-moox-social-slideshare-pagination-item-active");
					if(pageId<(newpage-4+correction) || pageId>(newpage+5+correction)){
						$( this ).hide(0);
					} else {
						$( this ).show(0);
					}
				});
				$( "#tx-moox-social-slideshare-pagination-" + newpage).addClass("tx-moox-social-slideshare-pagination-item-active");
			} else {
				check = newoffset + perrequest;				
				if(check>=count){					
					$( "#tx-moox-social-slideshare-loadmore-add" ).hide(0);
				}
			}
		}
	});
	//$( "#tx-moox-social-slideshare-ajaxquery" ).html('<strong>LIST:</strong> <a href="{ajaxurl}&' + query + '" target="_blank">' + query.replace("tx_mooxsocial_pi5[controller]=Slideshare&tx_mooxsocial_pi5[action]=listAjax", "") + '</a><br />' + $( "#tx-moox-social-slideshare-ajaxquery" ).html());
	//$( "#tx-moox-social-slideshare-ajaxquery" ).show(0);
}


$(document).ready(function() {	
	
	$(this).ajaxStart(function(){
          $("body").append("<div id='tx-moox-social-overlay'><img src='typo3conf/ext/moox_social/Resources/Public/Images/ajax-loader.gif' /></div>");
    });
	
	$(this).ajaxStop(function(){
          $("#tx-moox-social-overlay").remove();
    });
	
	
	$( "#tx-moox-social-slideshare-loadmore-add" ).click(function() {		
		loadSlidesharePosts('add','');		
	});	
	$( ".tx-moox-social-slideshare-loadmore-previous" ).click(function() {		
		loadSlidesharePosts('replace','previous');		
	});
	$( ".tx-moox-social-slideshare-loadmore-next" ).click(function() {		
		loadSlidesharePosts('replace','next');		
	});
});