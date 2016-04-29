<?php 
/**
 * @package		Mb2 Content
 * @version		1.5.0
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2016 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/



// no direct access
defined('_JEXEC') or die();
require_once __DIR__ . '/helper.php';


// Get articles
$list = modMb2contentHelper::getList($params);


$uniqid = uniqid();
$carousel = modMb2contentHelper::items_carousel($params, array('itemscount'=>count($list)));
$is_k2 = ($params->get('source', '') == 'k2' && (file_exists(JPATH_SITE . '/components/com_k2/k2.php') && JComponentHelper::isEnabled('com_k2', true)));


// Get style s and scripts
modMb2contentHelper::before_head($params, array('mod_id'=>$module->id,'carousel'=>$carousel));


// Check if items are display within carousel
$carousel_cls = modMb2contentHelper::carousel_cls($params, array('itemscount'=>count($list),'pos'=>' is-carousel', 'neg'=>' no-carousel'));
$carousel_data = modMb2contentHelper::carousel_data($params, array('carousel'=>$carousel));


// Define module data attribite
$use_lightbox = ($params->get('lightbox_image', 1) == 1 && ($params->get('thumb_link', 0) == 1 || $params->get('thumb_link', 0) == 3)) ? 1 : 0;
$mdata = ' data-lbx="' . $use_lightbox . '"';


// Define hover container bg
$hover_bg_cls = $params->get('hover_bg_margin', 1) == 1 ? ' margin' : '';


// Gt module layout
require JModuleHelper::getLayoutPath('mod_mb2content', $params->get('layout', 'default'));