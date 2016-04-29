<?php
/**
 * @package		Mb2 Content
 * @version		1.5.0
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2016 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/



defined('_JEXEC') or die;




abstract class JHtmlItemdetails {
	
	
	
		
		
		
		
	/**
	 *
	 * Method to get item title
	 *
	 */
	public static function itemTitle ($text, &$params, $attribs = array())
	{		
		
		$output = '';
				
		$output .= '<' . $attribs['tag'] . ' class="mb2-content-item-title">';		
		$output .= $attribs['link']!='' ? '<a href="' . $attribs['link'] . '">' : '';
		$output .= $text;
		$output .= $attribs['link']!='' ? '</a>' : '';		
		$output .= '</' . $attribs['tag'] . '>';
			
		return $output;
		
	}
	
	
	
	
	
	
	/**
	 *
	 * Method to get item date
	 *
	 */
	public static function itemDate ($item,&$params,$attribs = array())
	{		
		
		$output = '';
		
		$output .= '<span class="mb2-content-item-meta-date' . $attribs['cls'] . '">';		
		$output .= $params->get('meta_date_text', '')!='' ? $params->get('meta_date_text', '') . ' ' : '';		
		$output .= JText::sprintf(JHtml::_('date', $item->publish_up, $params->get('meta_date_format','d M, Y')));
		$output .= '</span>';
					
		return $output;
		
	}
	
	
	
	
	
	
	
	
	
	
	/**
	 *
	 * Method to get item author
	 *
	 */
	public static function itemAuthor ($item,&$params, $attribs = array())
	{		
		
		$output = '';		
		
		$output .= '<span class="mb2-content-item-meta-author">';		
		$output .= $params->get('meta_author_text','')!='' ? $params->get('meta_author_text','') . ' ' : '';
		$output .= $params->get('meta_author_link', 0) == 1 ? '<a href="' . $item->authorLink . '">' : '';		
		$output .= $item->author;
		$output .= $params->get('meta_author_link', 0) == 1 ? '</a>' : '';	
		$output .= '</span>';
					
		return $output;
		
	}
	
	
	
	
	
	
	
	
	
	
	/**
	 *
	 * Method to get item category
	 *
	 */
	public static function itemCategory ($item,&$params, $attribs = array())
	{		
		
		$output = '';
		
		$output .= '<span class="mb2-content-item-meta-category">';		
		$output .= $params->get('meta_category_text', '')!='' ? $params->get('meta_category_text', '') . ' ' : '';
		$output .= $params->get('meta_category_link', 0) == 1 ? '<a href="' . $item->categoryLink . '">' : '';		
		$output .= $item->categoryname;
		$output .= $params->get('meta_category_link', 0) == 1 ? '</a>' : '';	
		$output .= '</span>';
					
		return $output;
		
	}
	
	
	
	
	
	
	
	
	
	/**
	 *
	 * Method to get item readmore link
	 *
	 */
	public static function itemReadMore ($item,&$params,$attribs = array())
	{		
		
		$output = '';
		
		$output .= '<div class="mb2-content-item-readmore">';		
		$output .= '<a class="' . $params->get('readmore_btn_cls', 'btn btn-default') . '" href="' . $item->link . '">';		
		$output .= $params->get('readmore_text', 'Read More');
		$output .= '</a>';
		$output .= '</div>';
					
		return $output;
		
		
	}
	
	
	
		
		
		
		
		
	/**
	 *
	 * Method to get thumbnail links
	 *
	 */
	public static function itemLinks ($item,&$params, $attribs = array())
	{
		
		
		$output = '';
		
		$link_img = '<a href="' . $attribs['link'] . '" class="mb2-content-img-link'.$attribs['lcls'].'" title="' . $item->title . '"' . $attribs['ldata'] . '>' . $attribs['content'] . '</a>';
		$link_article = '<a href="' . $attribs['link'] . '" class="mb2-content-img-link" title="' . $item->title . '">' . $attribs['content'] . '</a>';
		
		$output .= ($attribs['type'] == 1 && ($params->get('thumb_link', 0) == 1 || $params->get('thumb_link', 0) == 3)) ? $link_img : '';
		$output .= ($attribs['type'] == 2 && ($params->get('thumb_link', 0) == 2 || $params->get('thumb_link', 0) == 3)) ? $link_article : '';
		
		return $output;
		
		
	}
	
	
	
		
		
		
		
		
	
	
	
	
	
	
	
}