<?php
/**
 * @package		Mb2 Content
 * @version		1.6.3
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2017 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/

defined('_JEXEC') or die();



class JFormFieldUpdates extends JFormField
{
	
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Updates';

	
	
	
	
	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
			
		
		$output = '';
		
		$jextpath = $this->element['jextpath'] ? (string) $this->element['jextpath'] : '';
		$isupdatetext = $this->element['isupdatetext'] ? (string) $this->element['isupdatetext'] : '';
		$noupdatetext = $this->element['noupdatetext'] ? (string) $this->element['noupdatetext'] : '';
		$exttype = $this->element['exttype'] ? (string) $this->element['exttype'] : '';
		$xtnname = $this->element['xtnname'] ? (string) $this->element['xtnname'] : '';
		$btntext = $this->element['btntext'] ? (string) $this->element['btntext'] : '';
		
		
				
		
		
		// Get module version from module xml file
		$extxmlpth = JPATH_SITE . '/' . $exttype . 's/' . $xtnname . '/' . $xtnname . '.xml';		
		$extxml = simplexml_load_file($extxmlpth);
		$extversion = $extxml->version;
		
		
		
		
		// Get module version form server update files		
		if (!function_exists('file_get_html'))
		{
			require( JPATH_SITE . '/modules/mod_mb2content/libs/simple_html_dom.php');	
		}
		
		
		if (file_exists($jextpath))
		{
			$extxmlserver = file_get_html($jextpath);
			$findversion = $extxmlserver->find('#header .data', 0)->plaintext;
			$versionsarr = explode(' ', $findversion);
			$extversionserver = $versionsarr[0];			
		}
		
		
		
		$output .= '</div>';
		
		
		$output .= '<div class="alert">';
		
		
		if ($extversion != $extversionserver)
		{
			
			$output .= $isupdatetext . ' <a href="http://mb2extensions.com/extensions/mb2-content" target="_blank" class="btn btn-primary">' . $btntext . '</a>';
			
		}
		else
		{
			
			$output .= $noupdatetext;	
		}
		
		
		
		$output .= '</div>';
		
		
		
		
		return $output;
		
		
	}
	
	
	
	

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		return '';
	}
	
	
	
	
}