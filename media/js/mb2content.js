/**
 * @package		Mb2 Content
 * @version		1.4.0
 * @author		Mariusz Boloz (http://marbol2.com)
 * @copyright	Copyright (C) 2013 - 2015 Mariusz Boloz (http://marbol2.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/







(function($){$(window).load(function(){	

	
	
	
	
	$('.mb2-content-list').each(function(){		
		
		
		var carouselList = $(this);
		
		
		if (carouselList.hasClass('is-carousel'))
		{
			
			
			carouselList.fadeIn(450);
			
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
						breakpoint:480,
						settings: {
							item:1,
							slideMove:1
						  }
					}
				],
				keyPress: true
				
			}); 
			
			
			
		}
		
		
		
		//var maxItems = carouselList.data('itemmax');
//		var scrollItems = carouselList.data('scroll');
//		var animtype = carouselList.data('animation');
//		var pauseTime = carouselList.data('duration');
//		var touch = carouselList.data('touch');
//		var is_play = carouselList.data('play');
//		var id = carouselList.data('id');
//		var item_margin_lr = carouselList.data('margin');
		
		
		
		
		
		
		
		//is-carousel
		
		//// Add touch param
//		if(touch == 1){
//			var is_touch = true
//			var is_mouse = true
//		}
//		else{
//			var is_touch = false
//			var is_mouse = false
//		}
//		
//		
//		// Add autoplay param
//		if (is_play == 1)
//		{
//			is_newplay = true
//		}
//		else
//		{
//			is_newplay = false	
//		}
//		
//		
//		
//		// Set animation type
//		if (animtype == 'fade')
//		{
//			}
		
		
			
		
		//$(this).carouFredSel({
//			responsive:true,
//			auto:{
//				play: is_newplay,
//				timeoutDuration: pauseTime
//			},
//			scroll: animtype ? {fx : 'crossfade'} : scrollItems,
//			prev: '#mb2-content-prev-'+id,
//			next: '#mb2-content-next-'+id,
//			pagination: '#mb2-content-pager-'+id,
//			items:{width:400,height:'auto',visible:{min:1,max:maxItems}},
//			swipe: {
//	        	onTouch: is_touch,
//	        	onMouse: is_mouse
//	    	}		
//		});	
		
		
		
		
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