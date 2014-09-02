/*---------------------*/

function loadYoutubePosts(method,direction){
	
	if(typeof(method)==='undefined'){
		method = 'add';
	}
	
	count 		= parseInt($( "#tx-moox-social-youtube-count" ).val());
	page 		= parseInt($( "#tx-moox-social-youtube-page" ).val());
	offset 		=  parseInt($( "#tx-moox-social-youtube-offset" ).val());
	perrequest 	=  parseInt($( "#tx-moox-social-youtube-perrequest" ).val());		
	
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
	
	query = 'tx_mooxsocial_pi3[controller]=Youtube&tx_mooxsocial_pi3[action]=listAjax';	
	query = query + '&tx_mooxsocial_pi3[offset]=' + newoffset;
	query = query + '&tx_mooxsocial_pi3[perrequest]=' + perrequest;
	<f:if condition="{source}=='api'">
	query = query + '&tx_mooxsocial_pi3[source]=api';
	query = query + '&tx_mooxsocial_pi3[page]={page}';
	</f:if>	
	query = query + '&type=89657404'		
	$.ajax({
		url: "{ajaxurl}",
		data: query,
		success: function(result) {
			if(method=='add'){				
				if(result!=" "){					
					$( ".tx-moox-social-youtube-listing" ).append(result);					
				} else {
					<f:if condition="{source}=='api'">
					$( "#tx-moox-social-youtube-loadmore-add" ).hide(0);								
					</f:if>
				}				
			} else {
				if(result!=" "){					
					$( ".tx-moox-social-youtube-listing" ).html(result);					
				}				
				<f:if condition="{source}=='api'">
				if(result==" "){					
					$( ".tx-moox-social-youtube-loadmore-next" ).hide(0);					
				}
				</f:if>
			}
			$( "#tx-moox-social-youtube-offset" ).val(newoffset);
			if(method=='replace'){
				if(direction=='previous'){				
					if(newoffset<=0){
						$( ".tx-moox-social-youtube-loadmore-previous" ).hide(0);
					}
					$( ".tx-moox-social-youtube-loadmore-next" ).show(0);					
					$( "#tx-moox-social-youtube-page" ).val(newpage);
				} else {					
					check = newoffset + perrequest;
					if(check>=count){
						$( ".tx-moox-social-youtube-loadmore-next" ).hide(0);
					}
					$( ".tx-moox-social-youtube-loadmore-previous" ).show(0);					
					$( "#tx-moox-social-youtube-page" ).val(newpage);
				}
				
				correction = 0;
				if(newpage<5){
					correction = 5-newpage;
				}				
				$( ".tx-moox-social-youtube-pagination-item" ).each( function( index, element ){
					itemId = $( this ).attr('id');		
					pageId   = parseInt(itemId.replace("tx-moox-social-youtube-pagination-",""));
					$( this ).removeClass("tx-moox-social-youtube-pagination-item-active");
					if(pageId<(newpage-4+correction) || pageId>(newpage+5+correction)){
						$( this ).hide(0);
					} else {
						$( this ).show(0);
					}
				});
				$( "#tx-moox-social-youtube-pagination-" + newpage).addClass("tx-moox-social-youtube-pagination-item-active");
			} else {
				check = newoffset + perrequest;				
				if(check>=count){					
					$( "#tx-moox-social-youtube-loadmore-add" ).hide(0);
				}
			}
		}
	});
	//$( "#tx-moox-social-youtube-ajaxquery" ).html('<strong>LIST:</strong> <a href="{ajaxurl}&' + query + '" target="_blank">' + query.replace("tx_mooxsocial_pi3[controller]=Youtube&tx_mooxsocial_pi3[action]=listAjax", "") + '</a><br />' + $( "#tx-moox-social-twitter-ajaxquery" ).html());
	//$( "#tx-moox-social-youtube-ajaxquery" ).show(0);
}


$(document).ready(function() {	
	
	$(this).ajaxStart(function(){
          $("body").append("<div id='tx-moox-social-overlay'><img src='typo3conf/ext/moox_social/Resources/Public/Images/ajax-loader.gif' /></div>");
    });
	
	$(this).ajaxStop(function(){
          $("#tx-moox-social-overlay").remove();
    });
	
	
	$( "#tx-moox-social-youtube-loadmore-add" ).click(function() {		
		loadYoutubePosts('add','');		
	});	
	$( ".tx-moox-social-youtube-loadmore-previous" ).click(function() {		
		loadYoutubePosts('replace','previous');		
	});
	$( ".tx-moox-social-youtube-loadmore-next" ).click(function() {		
		loadYoutubePosts('replace','next');		
	});
});