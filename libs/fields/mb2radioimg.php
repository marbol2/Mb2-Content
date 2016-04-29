<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;



class JFormFieldMb2radioimg extends JFormField
{
	
	
	protected $type = 'Mb2radioimg';

	
	
	protected function getInput()
	{
		$html = array();
		
		// Get the field options.
		$options = $this->getOptions();
				
		// Initialize some field attributes.
		$class     = ' class="mb2-radioimage"';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$readonly  = $this->readonly;

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' >';
		$html[] = '<a href="#" class="mb2-radioimage-btn btn btn-modal" title="Layout">+</a>';
		
		
		$doc = JFactory::getDocument();
		
		$doc->addStyleDeclaration('.mb2-radioimage-wrap {
	display: none;
	padding-top: 10px;
}

.mb2-radioimage input[type="radio"] {
	display:none;
}



.mb2-radioimage label {
	display:block-inline;
	float:left;	
	width: 100px;
	margin:0 5px 5px 0;
	border:solid 1px #d0d0d0;
	opacity:0.35;
	-webkit-transition: 0.1s ease-out;
  	-moz-transition: 0.1s ease-out;
  	-o-transition: 0.1s ease-out;
  	transition: 0.1s ease-out;
}



.mb2-radioimage label img {
	width: 100%;
	height: auto;
}



.mb2-radioimage label:hover,
.mb2-radioimage label:focus {
	opacity:1;	
}


.mb2-radioimage input[type="radio"]:checked + label {
	border-color:#52a351;
	opacity:1;
}');


$doc->addScriptDeclaration('jQuery(document).ready(function($){$(\'.mb2-radioimage-btn\').click(function(e){		
		
		$(this).siblings(\'.mb2-radioimage-wrap\').slideToggle(100);
		$(this).toggleClass(\'active\');
		
		e.preventDefault();	
		
	});
	
});');
		
		
		$html[] = '<div class="mb2-radioimage-wrap">';
		
		
		
		// Get image path
		$imgpath = JURI::root(true) . '/modules/mod_mb2content/libs/fields/img/';
		

		// Build the radio field output.
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';

			$disabled = !empty($option->disable) || ($readonly && !$checked);

			$disabled = $disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';
			
			$tattr = '';
			
			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $required . $onclick
				. $onchange . $disabled . ' />';

			$html[] = '<label for="' . $this->id . $i . '" title="' . $tattr . '"' . $class . ' ><img src="' 
				. $imgpath
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)).'" alt=""/></label>';

			$required = '';
		}
		
		
		$html[] = '<div>';
		

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	/**
	 * Method to get the field options for radio buttons.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				$disabled
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
			$tmp->onchange = (string) $option['onchange'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}
