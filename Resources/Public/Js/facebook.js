/*---------------------*/

function loadFacebookPosts(method,direction){
	
	if(typeof(method)==='undefined'){
		method = 'add';
	}
	
	count 		= parseInt($( "#tx-moox-social-facebook-count" ).val());
	page 		= parseInt($( "#tx-moox-social-facebook-page" ).val());
	offset 		=  parseInt($( "#tx-moox-social-facebook-offset" ).val());
	perrequest 	=  parseInt($( "#tx-moox-social-facebook-perrequest" ).val());		
	
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
	
	query = 'tx_mooxsocial_pi1[controller]=Facebook&tx_mooxsocial_pi1[action]=listAjax';	
	query = query + '&tx_mooxsocial_pi1[offset]=' + newoffset;
	query = query + '&tx_mooxsocial_pi1[perrequest]=' + perrequest;
	<f:if condition="{source}=='api'">
	query = query + '&tx_mooxsocial_pi1[source]=api';
	query = query + '&tx_mooxsocial_pi1[page]={page}';
	</f:if>	
	query = query + '&type=89657202'		
	$.ajax({
		url: "{ajaxurl}",
		data: query,
		success: function(result) {
			if(method=='add'){				
				if(result!=" "){					
					$( ".tx-moox-social-facebook-listing" ).append(result);					
				} else {
					<f:if condition="{source}=='api'">
					$( "#tx-moox-social-facebook-loadmore-add" ).hide(0);								
					</f:if>
				}				
			} else {
				if(result!=" "){					
					$( ".tx-moox-social-facebook-listing" ).html(result);					
				}				
				<f:if condition="{source}=='api'">
				if(result==" "){					
					$( ".tx-moox-social-facebook-loadmore-next" ).hide(0);					
				}
				</f:if>
			}
			$( "#tx-moox-social-facebook-offset" ).val(newoffset);
			$('.image-lightbox').magnificPopup({
				type: 'image',
				closeOnContentClick: true,     
				image: {
					verticalFit: true
				}
			});
			if(method=='replace'){
				if(direction=='previous'){				
					if(newoffset<=0){
						$( ".tx-moox-social-facebook-loadmore-previous" ).hide(0);
					}
					$( ".tx-moox-social-facebook-loadmore-next" ).show(0);					
					$( "#tx-moox-social-facebook-page" ).val(newpage);
				} else {					
					check = newoffset + perrequest;
					if(check>=count){
						$( ".tx-moox-social-facebook-loadmore-next" ).hide(0);
					}
					$( ".tx-moox-social-facebook-loadmore-previous" ).show(0);					
					$( "#tx-moox-social-facebook-page" ).val(newpage);
				}
				
				correction = 0;
				if(newpage<5){
					correction = 5-newpage;
				}				
				$( ".tx-moox-social-facebook-pagination-item" ).each( function( index, element ){
					itemId = $( this ).attr('id');		
					pageId   = parseInt(itemId.replace("tx-moox-social-facebook-pagination-",""));
					$( this ).removeClass("tx-moox-social-facebook-pagination-item-active");
					if(pageId<(newpage-4+correction) || pageId>(newpage+5+correction)){
						$( this ).hide(0);
					} else {
						$( this ).show(0);
					}
				});
				$( "#tx-moox-social-facebook-pagination-" + newpage).addClass("tx-moox-social-facebook-pagination-item-active");
			} else {
				check = newoffset + perrequest;				
				if(check>=count){					
					$( "#tx-moox-social-facebook-loadmore-add" ).hide(0);
				}
			}
		}
	});
	//( "#tx-moox-social-facebook-ajaxquery" ).html('<strong>LIST:</strong> <a href="' + ajaxurl + '&' + query + '" target="_blank">' + query.replace("tx_mooxsocial_pi1[controller]=Facebook&tx_mooxsocial_pi1[action]=listAjax", "") + '</a><br />' + $( "#tx-moox-social-facebook-ajaxquery" ).html());
	//$( "#tx-moox-social-facebook-ajaxquery" ).show(0);
}


$(document).ready(function() {	
	
	$(this).ajaxStart(function(){
          $("body").append("<div id='tx-moox-social-overlay'><img src='typo3conf/ext/moox_social/Resources/Public/Images/ajax-loader.gif' /></div>");
    });
	
	$(this).ajaxStop(function(){
          $("#tx-moox-social-overlay").remove();
    });
	
	
	$( "#tx-moox-social-facebook-loadmore-add" ).click(function() {		
		loadFacebookPosts('add','');		
	});	
	$( ".tx-moox-social-facebook-loadmore-previous" ).click(function() {		
		loadFacebookPosts('replace','previous');		
	});
	$( ".tx-moox-social-facebook-loadmore-next" ).click(function() {		
		loadFacebookPosts('replace','next');		
	});
});