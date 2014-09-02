/*---------------------*/

function loadTwitterPosts(method,direction){
	
	if(typeof(method)==='undefined'){
		method = 'add';
	}
	
	count 		= parseInt($( "#tx-moox-social-twitter-count" ).val());
	page 		= parseInt($( "#tx-moox-social-twitter-page" ).val());
	offset 		=  parseInt($( "#tx-moox-social-twitter-offset" ).val());
	perrequest 	=  parseInt($( "#tx-moox-social-twitter-perrequest" ).val());		
	
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
	
	query = 'tx_mooxsocial_pi2[controller]=Twitter&tx_mooxsocial_pi2[action]=listAjax';	
	query = query + '&tx_mooxsocial_pi2[offset]=' + newoffset;
	query = query + '&tx_mooxsocial_pi2[perrequest]=' + perrequest;
	<f:if condition="{source}=='api'">
	query = query + '&tx_mooxsocial_pi2[source]=api';
	query = query + '&tx_mooxsocial_pi2[page]={page}';
	</f:if>	
	query = query + '&type=89657303'		
	$.ajax({
		url: "{ajaxurl}",
		data: query,
		success: function(result) {
			if(method=='add'){				
				if(result!=" "){					
					$( ".tx-moox-social-twitter-listing" ).append(result);					
				} else {
					<f:if condition="{source}=='api'">
					$( "#tx-moox-social-twitter-loadmore-add" ).hide(0);								
					</f:if>
				}				
			} else {
				if(result!=" "){					
					$( ".tx-moox-social-twitter-listing" ).html(result);					
				}				
				<f:if condition="{source}=='api'">
				if(result==" "){					
					$( ".tx-moox-social-twitter-loadmore-next" ).hide(0);					
				}
				</f:if>
			}
			$( "#tx-moox-social-twitter-offset" ).val(newoffset);
			$('.image-lightbox').magnificPopup({
				type: 'image',
				closeOnContentClick: true,     
				image: {
					verticalFit: true
				}
			});
			$('.tx-moox-social-twitter-text').magnificPopup({
				delegate: 'span',
				removalDelay: 500, //delay removal by X to allow out-animation
				callbacks: {
				  beforeOpen: function() {
				     this.st.mainClass = this.st.el.attr('data-effect');
				  }
				},
				midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
			});
			if(method=='replace'){
				if(direction=='previous'){				
					if(newoffset<=0){
						$( ".tx-moox-social-twitter-loadmore-previous" ).hide(0);
					}
					$( ".tx-moox-social-twitter-loadmore-next" ).show(0);					
					$( "#tx-moox-social-twitter-page" ).val(newpage);
				} else {					
					check = newoffset + perrequest;
					if(check>=count){
						$( ".tx-moox-social-twitter-loadmore-next" ).hide(0);
					}
					$( ".tx-moox-social-twitter-loadmore-previous" ).show(0);					
					$( "#tx-moox-social-twitter-page" ).val(newpage);
				}
				
				correction = 0;
				if(newpage<5){
					correction = 5-newpage;
				}				
				$( ".tx-moox-social-twitter-pagination-item" ).each( function( index, element ){
					itemId = $( this ).attr('id');		
					pageId   = parseInt(itemId.replace("tx-moox-social-twitter-pagination-",""));
					$( this ).removeClass("tx-moox-social-twitter-pagination-item-active");
					if(pageId<(newpage-4+correction) || pageId>(newpage+5+correction)){
						$( this ).hide(0);
					} else {
						$( this ).show(0);
					}
				});
				$( "#tx-moox-social-twitter-pagination-" + newpage).addClass("tx-moox-social-twitter-pagination-item-active");
			} else {
				check = newoffset + perrequest;				
				if(check>=count){					
					$( "#tx-moox-social-twitter-loadmore-add" ).hide(0);
				}
			}
		}
	});
	//$( "#tx-moox-social-twitter-ajaxquery" ).html('<strong>LIST:</strong> <a href="{ajaxurl}&' + query + '" target="_blank">' + query.replace("tx_mooxsocial_pi2[controller]=Facebook&tx_mooxsocial_pi2[action]=listAjax", "") + '</a><br />' + $( "#tx-moox-social-twitter-ajaxquery" ).html());
	//$( "#tx-moox-social-twitter-ajaxquery" ).show(0);
}


$(document).ready(function() {	
	
	$(this).ajaxStart(function(){
		$("body").append("<div id='tx-moox-social-overlay'><img src='typo3conf/ext/moox_social/Resources/Public/Images/ajax-loader.gif' /></div>");
	});
	
	$(this).ajaxStop(function(){
		$("#tx-moox-social-overlay").remove();
	});
	
	
	$( "#tx-moox-social-twitter-loadmore-add" ).click(function() {		
		loadTwitterPosts('add','');		
	});	
	$( ".tx-moox-social-twitter-loadmore-previous" ).click(function() {		
		loadTwitterPosts('replace','previous');		
	});
	$( ".tx-moox-social-twitter-loadmore-next" ).click(function() {		
		loadTwitterPosts('replace','next');		
	});
	$('.tx-moox-social-twitter-text').magnificPopup({
		delegate: 'span',
		removalDelay: 500, //delay removal by X to allow out-animation
		callbacks: {
		  beforeOpen: function() {
		     this.st.mainClass = this.st.el.attr('data-effect');
		  }
		},
		midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
	});
});