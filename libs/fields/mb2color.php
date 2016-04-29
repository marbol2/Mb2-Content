<?php
/**
 * @package		Mb2 Content
 * @version		1.5.0
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2016 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/




defined('_JEXEC') or die;



class JFormFieldMb2color extends JFormField
{
	
	
	
	protected $type = 'Mb2color';

	
	
	
	protected function getInput()
	{
		
		$output = '';		
		
		// Load js color script
		$doc = JFactory::getDocument();
		$doc->addStylesheet(JURI::root(true) . '/modules/mod_mb2content/libs/fields/mb2color/css/spectrum.css');	
		$doc->addScript(JURI::root(true) . '/modules/mod_mb2content/libs/fields/mb2color/js/spectrum.js');
		
		
		$css = 'input#' . $this->id;
		$css .= '{';
		$css .= 'display:none!important;';
		$css .= '}';
		
		$doc->addStyleDeclaration($css);
		
		$js = 'jQuery(document).ready(function($){';
		$js .= '$("#' . $this->id . '").spectrum({';
		$js .= 'showInput: true,';
		$js .= 'preferredFormat: \'rgb\',';
		$js .= 'allowEmpty: true,';
		$js .= 'color: \'\',';
		$js .= 'showAlpha: true';
		$js .= '});';	
		$js .= '});';
		
		
		$doc->addScriptDeclaration($js);


		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
				
		$output .= '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $onchange . '/>';		
			
			
		return $output;		
		
	}
}