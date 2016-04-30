/**
 * @package		Mb2 Content
 * @version		1.5.1
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2016 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/







(function($){$(window).load(function(){	

	
	
	
	
	$('.mb2-content-list').each(function(){		
		
		
		var carouselList = $(this);
		
		
		if (carouselList.hasClass('is-carousel'))
		{
			
			
			
			//carouselList.hassClass('lightSlider')
//			{
//				carouselList.show();	
//			}
			
			
			
			
			var itemsCount = carouselList.data('items');
			var itemsMove = carouselList.data('move');
			var itemsMargin = carouselList.data('margin');
			var itemsAnim = carouselList.data('anim');
			var itemsAnimSpeed = carouselList.data('anim_speed');
			var itemsAnimPause = carouselList.data('anim_pause');
			var itemsAnimAuto = carouselList.data('auto');
			var itemsLoop = carouselList.data('loop');			
			var itemsDnav = carouselList.data('dirnav');
			var itemsCnav = carouselList.data('cnav');
			
			
			isDnav = itemsDnav == 1 ? true : false;
			isCnav = itemsCnav == 1 ? true : false;
			isAuto = itemsAnimAuto == 1 ? true : false;
			isLoop = itemsLoop == 1 ? true : false;
			
			
			
			
			carouselList.lightSlider({
				
				item: itemsCount,
				slideMargin: itemsMargin,
				slideMove: itemsMove,
				speed: itemsAnimSpeed, 
				auto: isAuto,
				loop: isLoop,
				pause: itemsAnimPause,
				controls: isDnav,
				pager: isCnav,
				vertical: false,
				prevHtml: '<span class="mb2c_icon-left-open-big"></span>',
				nextHtml: '<span class="mb2c_icon-right-open-big"></span>',
				responsive : [
					{
						breakpoint:768,
						settings: {
							item: itemsCount>=2 ? 2 : 1
						  }
					},
					{
						breakpoint:600,
						settings: {
							item:1,
							slideMove:1
						  }
					}
				],
				keyPress: true
				
			}); 
			
			
			
		}
		
		
		
		
		
	});
	
	
	
	



})})(jQuery);//end










jQuery(document).ready(function($){
	
	
	
	
	
	
	$('.mb2-content').each(function(){	
	
		
		
		var module = $(this);
		var isLightbox = module.data('lbx');
		
		
		if (isLightbox == 1)
		{
			
			module.on('mouseover',function(){
				
				$('.mb2-content-lightbox').nivoLightbox();
				
			});
			
			
		}
		
		
		
		
		
		
		
	
	
	});
	
	
	
	
	
	
	
	
});