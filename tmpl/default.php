<?php
/**
 * @package		Mb2 Content
 * @version		1.5.1
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2016 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/



// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_SITE . '/modules/mod_mb2content/helpers');
$count = count($list);
$listtag = $carousel ? 'ul' : 'div';
$litag = $carousel ? 'li' : 'div';
$layout = $params->get('item_layout', 'media-above');
$responsivecls = ($carousel || $params->get('responsive',1) == 0)? ' mb2-content-no-responsive' : ' mb2-content-responsive';
$multicolcls = $params->get('cols',4) > 2 ? ' mb2-content-multicol' : ' mb2-content-no-multicol';
if($count>0)
{
?>
<div class="mb2-content mb2-content-<?php echo $module->id . ' ' . $params->get('item_layout', 'media-above') . $responsivecls . $multicolcls; ?> mb2-content-clr"<?php echo $mdata; ?>>	
	<?php echo '<' . $listtag . ' class="mb2-content-list mb2-content-clr' . $carousel_cls . '"' . $carousel_data . '>'?>
		<?php 
		$i=0;
		$x=0;
		foreach($list as $item) :			
			$i++;
			$x++;		
			
			// Add 'last' class for last item
			$last_item = ($x == $count) ? ' last' : '';
			?>
			<?php echo '<' . $litag . ' class="mb2-content-item mb2-content-item-col-' . $params->get('cols', 4) . $last_item . '">'; ?>
				<div class="mb2-content-item-inner mb2-content-clr">
					<?php if ($layout == 'desc-hover') : ?>
						<?php require JModuleHelper::getLayoutPath('mod_mb2content', $params->get('layout', 'layout_hover')); ?>
					<?php else : ?>
						<?php require JModuleHelper::getLayoutPath('mod_mb2content', $params->get('layout', 'layout_default')); ?>
					<?php endif; ?>          
				</div><!-- end .mb2-content-item-inner -->
			<?php echo '</' . $litag . '><!-- end .mb2-content-item -->'; ?>      
			<?php		
			if($params->get('cols', 4) > 1 && $params->get('cols', 4) == $i && !$carousel){		
				echo '<div class="mb2-content-clr"></div>';	
				$i=0;	
			}		
		endforeach; 
		?>
	<?php echo '</' . $listtag . '><!-- end .mb2-content-list -->' ?>
</div><!-- end .mb2-content-wrap -->
<?php
} // End if count list
else
{	
	echo JText::_('MOD_MB2CONTENT_NO_ITEMS_FOUND');
}