/**
 * @package		Mb2 Content
 * @version		1.6.3
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2017 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/
!function(a){a(window).load(function(){a(".mb2-content-list").each(function(){var t=a(this);if(t.hasClass("is-carousel")){var i=t.data("items"),n=t.data("move"),e=t.data("margin"),o=(t.data("anim"),t.data("anim_speed")),s=t.data("anim_pause"),c=t.data("auto"),d=t.data("loop"),r=t.data("dirnav"),l=t.data("cnav");isDnav=1==r?!0:!1,isCnav=1==l?!0:!1,isAuto=1==c?!0:!1,isLoop=1==d?!0:!1,t.lightSlider({item:i,slideMargin:e,slideMove:n,speed:o,auto:isAuto,loop:isLoop,pause:s,controls:isDnav,pager:isCnav,vertical:!1,prevHtml:'<span class="mb2c_icon-left-open-big"></span>',nextHtml:'<span class="mb2c_icon-right-open-big"></span>',responsive:[{breakpoint:768,settings:{item:i>=2?2:1}},{breakpoint:600,settings:{item:1,slideMove:1}}],keyPress:!0})}})})}(jQuery),jQuery(document).ready(function(a){a(".mb2-content").each(function(){var t=a(this),i=t.data("lbx");1==i&&t.on("mouseover",function(){a(".mb2-content-lightbox").nivoLightbox()})})});