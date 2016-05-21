<?php
/**
 * @package		Mb2 Content
 * @version		1.6.1
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2016 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/



defined('_JEXEC') or die;



abstract class JHtmlItemedia {
	
			
		
	/**
	 *
	 * Method to get thumbnail url 
	 *
	 */
	public static function img_html($item, $params, $attribs)
	{
		
		
		// Basic variales
		$output = '';
		
		// Define img class
		$cls = $params->get('hover_effect', 2) ? ' h-effect' . $params->get('hover_effect', 1) : '';
		
				
		// Get thumbnail url
		//$attribs['is_k2'] ? 
		//$thumburl = $item->image : 		
		$thumburl = JHtmlItemedia::crop_image($item->image, $params, 100, $attribs);
		
		
		
		// Get xlarge image from k2 item if sourceis k2 else get item image
		$attribs['is_k2'] ? $limage = $item->imageXLarge : $limage = $item->image;
		
		
		
		// Get image html and caption
		$attribs['is_k2'] ? $alttext = $item->title : $alttext = $item->image_alt;
		$img = '<img src="' . $thumburl . '" alt="' . $alttext . '" />';
		
		
		
		// Get image caption
		//($item->image_caption !='' && $params->get('show_caption', 0) == 1) ? 		
//		$caption = '<p class="mb2-content-img-caption">' . $item->image_caption . '</p>' : 
//		$caption = '';
		
		
		
		// Get link title
		$attribs['is_k2'] ? $ttext = $item->title : $ttext = $item->image_alt;
		$ttext !='' ? $ltitle = $ttext : $ltitle = $item->title;
		
		
		
		// Get link class and data
		$params->get('lightbox_image', 1) == 1 ? $lcls = 'mb2-content-nivo-link' : $lcls = '';
		$params->get('lightbox_gallery', 0) == 1 ? $ldata = ' data-lightbox-gallery="mb2-content-gall-' . $attribs['uniqid'] . '"' : $ldata = '';
		
					
			
		$output .= '<div class="mb2-content-img' . $cls . '" style="max-width:100%;">';	
		
		
		// Check if image have some link
		if ($params->get('thumb_link', 0) !=0)
		{
					
		
			// Image have link now we need to check if hover effect is enabled
			if ($params->get('hover_effect', 1))
			{
				
				$output .= JHtmlItemedia::img_html_el($item, $params, array('ltitle'=>$ltitle, 'lcls'=>$lcls, 'ldata'=>$ldata, 'limage'=>$limage)); 			
				$output .= $img;	
				
			}
			else
			{				
				
				// Get thumbnail link				
				$params->get('thumb_link', 0) == 2 ? $lurl = $item->link : $lurl = JURI::base(true) . '/' . $limage;
				
				// Check if item has link to big image or full article
				$output .= '<a href="' . $lurl . '" class="' . $lcls . '"' . $ldata . ' title="' . $ltitle . '">';	
				$output .= $img;
				$output .= '</a>';
				
			}
		
		
		}
		else
		{			
			// Imge do not have any link
			$output .= $img;
						
		}
				
		
		$output .= '</div>';		
		
		return $output;		
		
		
		
	}
		
		
		
		
		
		
		
		
		
		
	/**
	 *
	 * Method to get thumbnail elemets
	 *
	 */
	public static function mediaLinks($item, $params, $attribs)
	{
		
		
		// Basic variables
		$output = '';
		
		
				
		// Define image elements
		$mexpand = '<a href="' . $attribs['limage'] . '" class="mexpand '. $attribs['lcls'] .'"' . $attribs['ldata'] . ' title="' . $attribs['ltitle'] .'"><span class="mb2c_icon-resize-full"></span></a>';
		$murl = '<a href="' . $item->link . '" class="murl" title="' . $attribs['ltitle'] . '"><span class="mb2c_icon-plus"></span></a>';
		
		
		
		
		
		// Change class if mark div have more than one link
		$mcls = 'mark-inner';
		$mcls .= $params->get('thumb_link', 0) == 3 ? ' links' : ' link';				
										
		$output .= '<div class="mark">';
			
		$output .= '<div class="'. $mcls .'">';
					
			if($params->get('thumb_link', 0) == 1)
			{						
				$output .= $mexpand;										
			}
			elseif($params->get('thumb_link', 0) == 2)
			{
				$output .= $murl;
			}
			elseif($params->get('thumb_link', 0) == 3)
			{
				$output .= $mexpand . $murl;
			}	
								
		$output .= '</div>';	
			
		$output .= '</div>';		
		
		
		
		
		return $output;
		
		
		
		
		
	}	
		
		
		
		
		
		
		
		
	
	/**
	 *
	 * Method to get thumbnail url 
	 *
	 */
	public static function crop_image($url, $params, $quality, $attribs)
	{
		
		$output = '';	
		
		
		// Basic variables			
		$cropping = $params->get('resize', 1);
		$imgw = $params->get('thumb_width', 480);
		$imgh = $params->get('thumb_height', 380);
				
		
		// Make sure that cropping image param is enabled, image url filed is not empty and image file exists
		$cropimg = ($cropping && $url && file_exists(JPATH_SITE . '/' . $url));
		
		
		if($cropimg){			
		
			
			
						
			//check uploaded image format		
			$format_checker = substr($url,-4); 
						
			if ($format_checker == '.jpg'){
				$format = '.jpg';	
			}
			elseif ($format_checker == '.gif'){
				$format = '.gif';	
			}
			elseif ($format_checker == '.png'){
				$format = '.png';	
			}					
									
			// *** 1) Initialise / load image
			
			// Get class to resize image
			
			if(!class_exists('resize'))
			{
				require_once JPATH_SITE . '/modules/mod_mb2content/libs/image_resize_class.php';
			} 			
			
			$resizeObj = new resize($url);
				
			// *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
			$resizeObj -> resizeImage($imgw, $imgh, 'crop'); 
			
			
			//check if thumbnail folder exist. If not creat it
			if(!is_dir(JPATH_CACHE . '/mb2content')){
				jimport('joomla.filesystem.folder');
				JFolder::create( JPATH_CACHE . '/mb2content');
			}	
			
			
			// Get image name
			$thumbname = JHtmlItemedia::get_img_name($url);	
				
			
				
			// *** 3) Save image
			$resizeObj -> saveImage(JPATH_CACHE . '/mb2content/' . $thumbname . '_' . $imgw . 'x' . $imgh . $format, $quality);							
			
			
			//define thumbnail url
			$output .= JURI::base(true) . '/cache/mb2content/' . $thumbname . '_' . $imgw.'x' . $imgh . $format;	
		
		
		}
		else{
			
			$output .= JURI::base(true) . '/' . $url;		
			
		}
	
	
		return $output;	
		
			
		
	}	
	
	
	
	
	
	
	
	
	
	
	/**
	 *
	 * Method to get thumbnail name 
	 *
	 */
	public static function get_img_name($url, $format = 0){	
	
		
		
		// Get file name
		$img_parts = pathinfo($url);
						
		if(!isset($img_parts['filename']))
		{
			$img_parts['filename'] = substr($img_parts['basename'], 0, strrpos($img_parts['basename'], '.'));
		} 		
		
		
		// Check uploaded image format		
		$format_checker = substr($url,-4); 
		
					
		if ($format_checker == '.jpg')
		{
			$imgformat = '.jpg';	
		}
		elseif ($format_checker == '.gif')
		{
			$imgformat = '.gif';	
		}
		elseif ($format_checker == '.png')
		{
			$imgformat = '.png';	
		}					
		
		
		if($format == 1)
		{
			return $img_parts['filename'] . $imgformat;
		}
		else
		{
			return $img_parts['filename'];
		}					
		
			
		
	}
	
	
	
	
	
	
}