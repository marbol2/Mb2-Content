<?php
/**
 * @package		Mb2 Content
 * @version		1.5.0
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2016 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/



defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');




abstract class modMb2contentHelper{
	
	
	
	
	
	/**
	 * 
	 * Method to get article list
	 * 
	 */
	public static function getList(&$params, $format = 'html'){
		
		
		
		
		
		// Check if k2 component is enabled and article source is set to K2
		if ($params->get('source', '') == 'k2' && (file_exists(JPATH_SITE . '/components/com_k2/k2.php') && JComponentHelper::isEnabled('com_k2', true)))
		{
			
			
			
			// Require core files
			require_once (JPATH_SITE . '/components/com_k2/helpers/route.php');
			require_once (JPATH_SITE . '/components/com_k2/helpers/utilities.php');
	
						
			
			jimport('joomla.filesystem.file');
			$mainframe = JFactory::getApplication();
			$limit = $params->get('count', 5);
			$cid = $params->get('category_id', NULL);		
			$ordering = $params->get('itemsOrdering', '');
			$componentParams = JComponentHelper::getParams('com_k2');
			$limitstart = JRequest::getInt('limitstart');
	
			$user = JFactory::getUser();
			$aid = $user->get('aid');
			$db = JFactory::getDBO();
	
			$jnow = JFactory::getDate();
			$now =  K2_JVERSION == '15'?$jnow->toMySQL():$jnow->toSql();
			$nullDate = $db->getNullDate();
	
	
				$query = "SELECT i.*,";
	
				if ($ordering == 'modified')
				{
					$query .= " CASE WHEN i.modified = 0 THEN i.created ELSE i.modified END as lastChanged,";
				}
				
				$query .= "c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams";
				
				if ($ordering == 'best')
					$query .= ", (r.rating_sum/r.rating_count) AS rating";
	
				if ($ordering == 'comments')
					$query .= ", COUNT(comments.id) AS numOfComments";
	
				$query .= " FROM #__k2_items as i RIGHT JOIN #__k2_categories c ON c.id = i.catid";
	
				if ($ordering == 'best')
					$query .= " LEFT JOIN #__k2_rating r ON r.itemID = i.id";
	
				if ($ordering == 'comments')
					$query .= " LEFT JOIN #__k2_comments comments ON comments.itemID = i.id";
	
				if (K2_JVERSION != '15')
				{
					$query .= " WHERE i.published = 1 AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).") AND i.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")  AND c.trash = 0";
				}
				else
				{
					$query .= " WHERE i.published = 1 AND i.access <= {$aid} AND i.trash = 0 AND c.published = 1 AND c.access <= {$aid} AND c.trash = 0";
				}
	
				$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
				$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";
				
				
				// Set catfilter to true if some category is selected
				$cid !=NULL ? $catfilter = true : $catfilter = false;
				
				if ($catfilter)
				{
					if (!is_null($cid))
					{
						if (is_array($cid))
						{
							if ($params->get('getChildren', 1))
							{
								$itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
								$categories = $itemListModel->getCategoryTree($cid);
								$sql = @implode(',', $categories);
								$query .= " AND i.catid IN ({$sql})";
	
							}
							else
							{
								JArrayHelper::toInteger($cid);
								$query .= " AND i.catid IN(".implode(',', $cid).")";
							}
	
						}
						else
						{
							if ($params->get('getChildren', 1))
							{
								$itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
								$categories = $itemListModel->getCategoryTree($cid);
								$sql = @implode(',', $categories);
								$query .= " AND i.catid IN ({$sql})";
							}
							else
							{
								$query .= " AND i.catid=".(int)$cid;
							}
	
						}
					}
				}
	
				if ($params->get('FeaturedItems') == '0')
					$query .= " AND i.featured != 1";
	
				if ($params->get('FeaturedItems') == '2')
					$query .= " AND i.featured = 1";
	
				if ($params->get('videosOnly'))
					$query .= " AND (i.video IS NOT NULL AND i.video!='')";
	
				if ($ordering == 'comments')
					$query .= " AND comments.published = 1";
	
				if (K2_JVERSION != '15')
				{
					if ($mainframe->getLanguageFilter())
					{
						$languageTag = JFactory::getLanguage()->getTag();
						$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
					}
				}
	
				switch ($ordering)
				{
	
					case 'date' :
						$orderby = 'i.created ASC';
						break;
	
					case 'rdate' :
						$orderby = 'i.created DESC';
						break;
	
					case 'alpha' :
						$orderby = 'i.title';
						break;
	
					case 'ralpha' :
						$orderby = 'i.title DESC';
						break;
	
					case 'order' :
						if ($params->get('FeaturedItems') == '2')
							$orderby = 'i.featured_ordering';
						else
							$orderby = 'i.ordering';
						break;
	
					case 'rorder' :
						if ($params->get('FeaturedItems') == '2')
							$orderby = 'i.featured_ordering DESC';
						else
							$orderby = 'i.ordering DESC';
						break;
	
					case 'hits' :
						if ($params->get('popularityRange'))
						{
							$datenow = JFactory::getDate();
							$date =  K2_JVERSION == '15'?$datenow->toMySQL():$datenow->toSql();
							$query .= " AND i.created > DATE_SUB('{$date}',INTERVAL ".$params->get('popularityRange')." DAY) ";
						}
						$orderby = 'i.hits DESC';
						break;
	
					case 'rand' :
						$orderby = 'RAND()';
						break;
	
					case 'best' :
						$orderby = 'rating DESC';
						break;
	
					case 'comments' :
						if ($params->get('popularityRange'))
						{
							$datenow = JFactory::getDate();
							$date =  K2_JVERSION == '15'?$datenow->toMySQL():$datenow->toSql();
							$query .= " AND i.created > DATE_SUB('{$date}',INTERVAL ".$params->get('popularityRange')." DAY) ";
						}
						$query .= " GROUP BY i.id ";
						$orderby = 'numOfComments DESC';
						break;
	
					case 'modified' :
						$orderby = 'lastChanged DESC';
						break;
	
					case 'publishUp' :
						$orderby = 'i.publish_up DESC';
						break;
	
					default :
						$orderby = 'i.id DESC';
						break;
				}
	
				$query .= " ORDER BY ".$orderby;
				$db->setQuery($query, 0, $limit);
				$items = $db->loadObjectList();
		
	
			$model = K2Model::getInstance('Item', 'K2Model');
	
			if (count($items))
			{
	
				foreach ($items as $item)
				{
					$item->event = new stdClass;
					
	
					//Clean title
					$item->title = JFilterOutput::ampReplace($item->title);
	
					//Images
					if ($params->get('itemImage', 1))
					{
	
						$date = JFactory::getDate($item->modified);
						$timestamp = '?t='.$date->toUnix();
	
						if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_XS.jpg'))
						{
							$item->imageXSmall = 'media/k2/items/cache/'.md5("Image".$item->id).'_XS.jpg';
							if ($componentParams->get('imageTimestamp'))
							{
								$item->imageXSmall .= $timestamp;
							}
						}
	
						if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_S.jpg'))
						{
							$item->imageSmall = 'media/k2/items/cache/'.md5("Image".$item->id).'_S.jpg';
							if ($componentParams->get('imageTimestamp'))
							{
								$item->imageSmall .= $timestamp;
							}
						}
	
						if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_M.jpg'))
						{
							$item->imageMedium = 'media/k2/items/cache/'.md5("Image".$item->id).'_M.jpg';
							if ($componentParams->get('imageTimestamp'))
							{
								$item->imageMedium .= $timestamp;
							}
						}
	
						if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_L.jpg'))
						{
							$item->imageLarge = 'media/k2/items/cache/'.md5("Image".$item->id).'_L.jpg';
							if ($componentParams->get('imageTimestamp'))
							{
								$item->imageLarge .= $timestamp;
							}
						}
	
						if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_XL.jpg'))
						{
							$item->imageXLarge = 'media/k2/items/cache/'.md5("Image".$item->id).'_XL.jpg';
							if ($componentParams->get('imageTimestamp'))
							{
								$item->imageXLarge .= $timestamp;
							}
						}
	
						if (JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item->id).'_Generic.jpg'))
						{
							$item->imageGeneric = 'media/k2/items/cache/'.md5("Image".$item->id).'_Generic.jpg';
							if ($componentParams->get('imageTimestamp'))
							{
								$item->imageGeneric .= $timestamp;
							}
						}
	
						$image = 'image'.$params->get('itemImgSize', 'Medium');
						if (isset($item->$image))
							$item->image = $item->$image;
	
					}
	
					//Read more link
					$item->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias))));
	
					//Tags
					if ($params->get('itemTags'))
					{
						$tags = $model->getItemTags($item->id);
						for ($i = 0; $i < sizeof($tags); $i++)
						{
							$tags[$i]->link = JRoute::_(K2HelperRoute::getTagRoute($tags[$i]->name));
						}
						$item->tags = $tags;
					}
	
					//Category link
					if ($params->get('itemCategory', 1))
						$item->categoryLink = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid.':'.urlencode($item->categoryalias))));
	
	
					//Comments counter
					if ($params->get('itemCommentsCounter'))
						$item->numOfComments = $model->countItemComments($item->id);
	
					//Attachments
					if ($params->get('itemAttachments'))
						$item->attachments = $model->getItemAttachments($item->id);
	
					//Import plugins
					if ($format != 'feed')
					{
						$dispatcher = JDispatcher::getInstance();
						JPluginHelper::importPlugin('content');
					}
	
					//Video
					if ($params->get('itemVideo') && $format != 'feed')
					{
						$params->set('vfolder', 'media/k2/videos');
						$params->set('afolder', 'media/k2/audio');
						$item->text = $item->video;
									if (K2_JVERSION == '15')
				{
					$dispatcher->trigger('onPrepareContent', array(&$item, &$params, $limitstart));
				}
				else
				{
					$dispatcher->trigger('onContentPrepare', array('mod_k2_content.', &$item, &$params, $limitstart));
				}
						$item->video = $item->text;
					}
	
					// Introtext
					$item->text = '';
					if ($params->get('itemIntroText', 1))
					{
						// Word limit
						if ($params->get('itemIntroTextWordLimit'))
						{
							$item->text .= K2HelperUtilities::wordLimit($item->introtext, $params->get('itemIntroTextWordLimit'));
						}
						else
						{
							$item->text .= $item->introtext;
						}
					}
	
	
					// Restore the intotext variable after plugins execution
					$item->introtext = $item->text;
	
					//Clean the plugin tags
					$item->introtext = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $item->introtext);
	
					//Author
					if (!empty($item->created_by_alias))
					{
						$item->author = $item->created_by_alias;
						$item->authorLink = Juri::root(true);
					}
					else
					{
						$author = JFactory::getUser($item->created_by);
						$item->author = $author->name;
	
						//Author Link
						$item->authorLink = JRoute::_(K2HelperRoute::getUserRoute($item->created_by));
					}
	
	
					$rows[] = $item;
				}
	
				return $rows;
	
			}
			
			
			
			
		}		
		
			
		
		// ---------------------------- End K2 items and start Joomla items ---------------------------- //
		
		
		
		
		
		else
		{
						
			
			
			// Get the dbo
			$db = JFactory::getDbo();
	
			// Get an instance of the generic articles model
			$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
	
			// Set application parameters in model
			$app = JFactory::getApplication();
			$appParams = $app->getParams();
			$model->setState('params', $appParams);
	
			// Set the filters based on the module params
			$model->setState('list.start', 0);
			$model->setState('list.limit', (int) $params->get('count', 5));
			$model->setState('filter.published', 1);
	
			// Access filter
			$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
			$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
			$model->setState('filter.access', $access);
	
			// Category filter
			$model->setState('filter.category_id', $params->get('catid', array()));
	
			// User filter
			$userId = JFactory::getUser()->get('id');
			switch ($params->get('user_id'))
			{
				case 'by_me':
					$model->setState('filter.author_id', (int) $userId);
					break;
				case 'not_me':
					$model->setState('filter.author_id', $userId);
					$model->setState('filter.author_id.include', false);
					break;
	
				case '0':
					break;
	
				default:
					$model->setState('filter.author_id', (int) $params->get('user_id'));
					break;
			}
	
			// Filter by language
			$model->setState('filter.language', $app->getLanguageFilter());
	
			//  Featured switch
			switch ($params->get('show_featured'))
			{
				case '1':
					$model->setState('filter.featured', 'only');
					break;
				case '0':
					$model->setState('filter.featured', 'hide');
					break;
				default:
					$model->setState('filter.featured', 'show');
					break;
			}
	
			// Set ordering
			$order_map = array(
				'm_dsc' => 'a.modified DESC, a.created',
				'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
				'c_dsc' => 'a.created',
				'p_dsc' => 'a.publish_up',
				'most_popular' => 'a.hits',
			);
			$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
			$dir = 'DESC';
	
			$model->setState('list.ordering', $ordering);
			$model->setState('list.direction', $dir);
	
			$items = $model->getItems();
	
			foreach ($items as &$item) {
				$item->slug = $item->id.':'.$item->alias;
				$item->catslug = $item->catid.':'.$item->category_alias;
	
				if ($access || in_array($item->access, $authorised)) {
					// We know that user has the privilege to view the article
					$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
				} else {
					$item->link = JRoute::_('index.php?option=com_users&view=login');
				}
				
				
				
				// Change category title to categoyt name
				$item->categoryname = $item->category_title; 
							
				
				// Define intro image, alt and caption text
				$item->image = json_decode($item->images)->image_intro;
				$item->image_alt = json_decode($item->images)->image_intro_alt;
				$item->image_caption = json_decode($item->images)->image_intro_caption;
				
				
				// Get category link
				$item->categoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug));
				
				
			}
	
			return $items;
			
		
			
			
			
		}
		
		
		
		
		
		
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 * Method to get module styles and scripts
	 * 
	 */
	public static function before_head(&$params, $attribs)
	{
		
		
		// Basic variables
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		
				
		
		// Load javascript framework
		JHtml::_('jquery.framework', false);	
		
			
		
						
		
		// Nivo lightbox style and script are loaded when:	
				
		if ($params->get('lightbox_image', 1) == 1 && ($params->get('thumb_link', 0) == 1 || $params->get('thumb_link', 0) == 3))
		{
					
			
			if ($params->get('lightbox_styles', 1) == 1)
			{
				// Style
				!modMb2contentHelper::isDeclaration('nivo-lightbox.css') ? $doc->addStyleSheet(JURI::base(true) . '/media/mb2content/css/nivo-lightbox.css') : '';
				!modMb2contentHelper::isDeclaration('nivo-lightbox-default.css') ? $doc->addStyleSheet(JURI::base(true) . '/media/mb2content/css/nivo-lightbox-default.css') : '';
			}
			
			if (!modMb2contentHelper::isDeclaration('nivo-lightbox.min.js') && $params->get('lightbox_script', 1) == 1)
			{
				// Script
				$doc->addScript(JURI::base(true) . '/media/mb2content/js/nivo-lightbox.min.js');
			}
			
		}		
		
		
		
		
		
		// Get carousel script will load if	
		$carousel = ($params->get('carousel_on', 0) == 1 && $attribs['carousel']);		
		
		if ($carousel)
		{			
			
			if (!modMb2contentHelper::isDeclaration('lightslider.css') && $params->get('carousel_style', 1) == 1)	
			{
				$doc->addStyleSheet(JURI::base(true) . '/media/mb2content/css/lightslider.css');	
			}
			
			if (!modMb2contentHelper::isDeclaration('lightslider.min.js') && $params->get('carousel_script', 1) == 1)
			{
				$doc->addScript(JURI::base(true) . '/media/mb2content/js/lightslider.min.js');
			}
			
		}
				
		
		
		
		// Get module style	
		if (file_exists(JPATH_THEMES . '/' . $app->getTemplate() . '/css/mb2content.css'))	
		{
			$doc->addStyleSheet(JURI::base(true) . '/templates/' . $app->getTemplate() . '/css/mb2content.css');
		}
		else
		{
			$doc->addStyleSheet(JURI::base(true) . '/media/mb2content/css/mb2content.css');	
		}
		
		
		// Get module script
		if (file_exists(JPATH_THEMES . '/' . $app->getTemplate() . '/js/mb2content.js'))
		{
			$doc->addScript(JURI::base(true) . '/templates/' . $app->getTemplate() . '/js/mb2content.js');
		}
		else
		{
			$doc->addScript(JURI::base(true) . '/media/mb2content/js/mb2content.js');
		}
		
		
		
		
		
		// Inline styles		
		$inl_style = modMb2contentHelper::layout($params, $attribs);
		$inl_style .= modMb2contentHelper::inline_style($params, $attribs);
		
		$doc->addStyleDeclaration($inl_style);
		
			 
		 
		 
		 
	}
	
	
	
	
	
	
	/**
	 *
	 * Method to check loading script and styles
	 *
	 */	
	public static function isDeclaration($name)
	{
				
		
		$doc = JFactory::getDocument();				
		$declarationarr = preg_match('@.css@',$name) ? $doc->_styleSheets : $doc->_scripts;
			
		foreach ($declarationarr as $k=>$v)
		{					
			if (preg_match('@' . $name . '@', $k))
			{				
				return true;		
			}			
		}
		
		return false;				
		
	}
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 * Method to get module styles and scripts
	 * 
	 */
	public static function inline_style(&$params, $attribs)
	{
		
		
		// Basic variables
		$output = '';		
		$pref = '.mb2-content-' . $attribs['mod_id'];
		
		
		
		
		// Active color
		if ($params->get('active_color', '') !='')
		{
						
			$output .= $pref . ' .lSAction > a,';
			$output .= $pref . ' .lSAction > a:hover,';
			$output .= $pref . ' .lSAction > a:focus';
			$output .= '{';
			$output .= 'color:' . $params->get('active_color', '') . ';';
			$output .= '}';
			
			
			$output .= $pref . ' .mb2-content-img-links a:hover,';
			$output .= $pref . ' .mb2-content-img-links a:focus,'; 
			$output .= $pref . ' .mb2-content-item-meta-date.above,'; 		
			$output .= $pref . ' .lSSlideOuter .lSPager.lSpg > li a,';
			$output .= $pref . ' .lSSlideOuter .lSPager.lSpg > li:hover a';			
			$output .= '{';
			$output .= 'background-color:' . $params->get('active_color', '') . ';';
			$output .= '}';		
			
			
			$output .= $pref . ' .mb2-content-img-links a:hover,';
			$output .= $pref . ' .mb2-content-img-links a:focus,';
			$output .= $pref . ' .lSSlideOuter .lSPager.lSpg > li.active a';		
			$output .= '{';
			$output .= 'border-color:' . $params->get('active_color', '') . ';';
			$output .= '}';				
			
		}
		
		
		
		
		// Image hover bg color
		if ($params->get('img_hover_bg', '') !='')
		{
						
			$output .= $pref . ' .mb2-content-hover-bg';
			$output .= '{';
			$output .= 'background-color:' . $params->get('img_hover_bg', '') . ';';
			$output .= '}';			
			
		}
		
		
		
		
		
		
		
		
		// Text color
		if ($params->get('color', '') !='')
		{			
			$output .= $pref . '{color:' . $params->get('color', '') . ';}'; 			
		}
		
		
		
		
		// Links color
		if ($params->get('link_color', '') !='' || $params->get('link_hover_color', '') !='')
		{
			
			// Normal liks
			$output .=  $params->get('link_color', '') !='' ? $pref . ' a{color:' . $params->get('link_color', '') . ';}' : ''; 
			
			// Hover links
			if ($params->get('link_hover_color', ''))
			{	
				
				$output .= $pref . ' a:hover,';
				$output .= $pref . ' a:active,';
				$output .= $pref . ' a:focus';
				$output .= '{';
				$output .= 'color:' . $params->get('link_hover_color', '') . ';';
				$output .= '}';
			}
			
						
		}
		
		
		
		
		// Title color
		if ($params->get('title_color', '') !='')
		{
			
			$output .= $pref . ' .mb2-content-item-title,'; 
			$output .= $pref . ' .mb2-content-item-title a'; 
			$output .= '{';
			$output .= 'color: ' . $params->get('title_color', '') . ';'; 
			$output .= '}';			
			
		}	
		
			
		
		// Meta color
		$output .= $params->get('meta_color', '') ? $pref . ' .mb2-content-item-meta span{color:' . $params->get('meta_color', '') . ';}' : '';
	
		
		
		
		// Custom css
		$output .= $params->get('custom_css', '') ? $params->get('custom_css', '') : '';
		
		
		
		
		return $output; 	
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 * Method to check if items are display within carousel
	 * 
	 */
	public static function items_carousel(&$params, $attribs = array())
	{
				
		
		$carousel = ($params->get('carousel_on', 0) == 1 && $attribs['itemscount']>$params->get('cols', 4));
		
		
		if($carousel)
		{
			return true;			
		}
		else		
		{			
			return false;	
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 * Method to get carousel class
	 * 
	 */
	public static function carousel_cls(&$params, $attribs)
	{
				
			
		$carousel = modMb2contentHelper::items_carousel($params, $attribs);
				
		return $carousel ? $attribs['pos'] : $attribs['neg'];		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 * Method to get carousel data attribute
	 * 
	 */
	public static function carousel_data(&$params, $attribs)
	{
				
		
		// Basic variables
		$output = '';	
		
		
		if ($attribs['carousel'])
		{			
			$output .= ' data-items="' . $params->get('cols', 4) . '"';
			$output .= ' data-margin="' . $params->get('margin_lr', 20) . '"';
			$output .= ' data-anim="' . $params->get('carousel_anim_type', 0) . '"';
			$output .= ' data-move="' . $params->get('carousel_scroll', 1) . '"';
			$output .= ' data-anim_speed="' . $params->get('carousel_nim_speed', 400) . '"';
			$output .= ' data-anim_pause="' . $params->get('carousel_pause_time', 2000) . '"';
			$output .= ' data-auto="' . $params->get('carousel_auto', 1) . '"';
			$output .= ' data-loop="' . $params->get('carousel_loop', 0) . '"';
			$output .= ' data-dirnav="' . $params->get('carousel_direct_nav', 1) . '"';
			$output .= ' data-cnav="' . $params->get('carousel_control_nav', 1) . '"';		
		}
		
		
		return $output;
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 * Method to get module layout
	 * 
	 */
	public static function layout(&$params, $attribs)
	{
		
		
		// Basic variables
		$output = '';
		$module_container = '.mb2-content-' . $attribs['mod_id'] . ' .mb2-content-list'; 
		$hor_gutter = round($params->get('margin_lr', 20)/2,0);
	
	
		// Define container amrgin left and right
		// Only if carousel is disabled
		if (!$attribs['carousel'] && $params->get('cols', 4) > 1)
		{
			$output .= $module_container;
			$output .= '{';
			$output .= 'margin-left:-' . $hor_gutter . 'px;';
			$output .= 'margin-right:-' . $hor_gutter . 'px;';
			$output .= '}';
		}
				
		
		
		//Content item core style
		$item_width = round(100/$params->get('cols', 4),10);
		
		$output .= $module_container . ' .mb2-content-item';
		$output .= '{';
		$output .= 'width:' . $item_width . '%;';
		$output .= !$attribs['carousel'] ? 'margin-bottom:' . $params->get('margin_b', 30) . 'px;' : '';
		$output .= '}';
		
		
		if (!$attribs['carousel'] && $params->get('cols', 4) > 1)
		{
			$output .= $module_container . ' .mb2-content-item-inner';
			$output .= '{';			
			$output .= 'margin-left:' . $hor_gutter . 'px;';
			$output .= 'margin-right:' . $hor_gutter . 'px;';
			$output .= 'margin-bottom:' . $params->get('margin_b', 30) . 'px;';		
			$output .= '}';
		}
		
		
		
		
			
		
		
		//style for module item elements (media and description)		
		$params->get('item_layout', 'media-above') == 'mb2-content-media-right' ? $item_parts_float = 'right' : $item_parts_float = 'left';
		
			
		if($params->get('item_layout', 'media-above') == 'mb2-content-media-left' || $params->get('item_layout', 'media-above') == 'mb2-content-media-right'){		
			
			$item_media_width = $params->get('media_width', 50);		
			$item_desc_width = 100 - $item_media_width;				
			
			$output .= $module_container .' .mb2-content-item-media';
			$output .= '{width:' . $item_media_width . '%;float:' . $item_parts_float . ';}';
			
			$output .= $module_container .' .mb2-content-item-deatils';
			$output .= '{width:' . $item_desc_width . '%;float:' . $item_parts_float . ';}';
			
			
			if ($params->get('item_layout', 'media-above') == 'mb2-content-media-left')
			{			
				$output .= $module_container .' .mb2-content-item-deatils-inner';
				$output .= '{';	
				$output .= 'padding-left:' . $params->get('media_desc_margin', 30) . 'px;';
				$output .= '}';	
			}
			elseif ($params->get('item_layout', 'media-above') == 'mb2-content-media-right')
			{
				$output .= $module_container .' .mb2-content-item-deatils-inner';
				$output .= '{';	
				$output .= 'padding-right:' . $params->get('media_desc_margin', 30) . 'px;';
				$output .= '}';		
			}
			
			
					
		}	
			
		
		
		
		
		
		
		
		
		
		return $output;	
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 *
	 * Method to get thumbnail url 
	 *
	 */
	public static function cropImage($url, &$params, $quality = 100, $attribs = array())
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
			$thumbname = modMb2contentHelper::imageName($url);	
				
			
				
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
	public static function imageName($url, $format = 0){	
	
		
		
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
	
	
	
	
	
	
	
	/**
	 * 
	 * Method to get module styles and scripts
	 * 
	 */
	public static function lightboxAttribs(&$params, $attribs = array())
	{
		
		
		$output = '';
		
		
		if ($params->get('lightbox_image',1) == 1) 
		{			
			$output .= $attribs['type'] == 1 ? ' mb2-content-lightbox' : '';
			$output .= $attribs['type'] == 2 ? ' data-lightbox-gallery="' . $attribs['gid'] . '"' : '';	
		}	
		
		
		return $output;
		
		
	}
	
	
	
	
	
	/**
	 *
	 * Method to get thumbnail url 
	 *
	 */
	public static function wordLimit($string, $word_limit = 999, $end_char = '...'){	
	
	
		// Check if user use word limit
		if($word_limit && $word_limit < 999){
			
			
			$content_limit = strip_tags($string);
			$words = explode(" ",$content_limit);
			$new_string = implode(" ",array_splice($words,0,$word_limit));	
			$word_count = str_word_count($string);
			
			
			// Get end of word limit
			if($end_char !=''){
				if($word_count > $word_limit){		
				$is_end_char = $end_char;
				}		
				else{
				$is_end_char = '';	
				}
			}
			else{
				$is_end_char = '';
			}
						
			
			return JHTML::_('content.prepare', $new_string) . $is_end_char;		
			
		}
		
		else{
					
			return JHTML::_('content.prepare', $string);	
				
		}
			
		
	}
	
	
	
	
	
	
	
	
	
	
	
}