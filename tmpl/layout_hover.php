<?php
/**
 * @package		Mb2 Content
 * @version		1.6.3
 * @author		Mariusz Boloz (http://mb2extensions.com)
 * @copyright	Copyright (C) 2013 - 2017 Mariusz Boloz (http://mb2extensions.com). All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
**/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_SITE . '/modules/mod_mb2content/helpers');
$count = count($list);
$isimage = (isset($item->image)&& $item->image !='');
$layout = $params->get('item_layout', 'media-above');
$newsbar_bg = '';


// Define item meta
$itemmeta = ($params->get('meta_category',1)==1 || $params->get('meta_author',1)==1 || $params->get('meta_date',1)==1);


if ($isimage) : ?>
	<div class="mb2-content-item-media">
		<div class="mb2-content-item-media-inner mb2-content-hover-container mb2-content-clr">
      	<?php
			$alttext =  $is_k2 ? $item->title : $item->image_alt;
			$gid = 'mb2content'.$module->id;
			$thumbnail_url = modMb2contentHelper::cropImage($item->image, $params, $params->get('imgquality', 75), array('pref'=>$module->id . '_' . $item->id));
			$lcls = modMb2contentHelper::lightboxAttribs($params,array('type'=>1,'gid'=>$gid));
			$ldata = modMb2contentHelper::lightboxAttribs($params,array('type'=>2,'gid'=>$gid));
			$item_links = (($params->get('image_links', 1) == 1 && $params->get('thumb_link', 0) > 0) || $params->get('thumb_link', 0) == 3);
			$item_no_links = ($params->get('image_links', 1) == 0 && ($params->get('thumb_link', 0) == 1 || $params->get('thumb_link', 0) == 2));
			$item_link = $params->get('thumb_link', 0) == 2 ? $item->link : $item->image;
			$newsbar_bg = $layout === 'news-bar' ? ' style="background-image:url(\'' . $thumbnail_url . '\');"' : '';
		?>
    		<div class="mb2-content-hover-bg<?php echo $hover_bg_cls; ?>"<?php echo $newsbar_bg ; ?>>
             	<div class="mb2-content-hover-content">
              		<div class="mb2-content-hover-content-inner">
						<?php if ($params->get('title', 1) == 1) :			
							$title_text = modMb2contentHelper::wordLimit($item->title, $params->get('title_limit', 999), '...');
							$title_link = $params->get('title_link', 1) == 1 ? $item->link : '';			
							echo JHtml::_('itemdetails.itemTitle',$title_text,$params,array('link'=>$title_link,'tag'=>$params->get('title_heading', 'h4'))); 			
						endif; ?>
                        <?php if ($itemmeta) : ?>
                        	<div class="mb2-content-item-meta  mb2-content-clr">
								<?php if ($params->get('meta_date', 1) == 1) : ?>
                                    <?php echo JHtml::_('itemdetails.itemDate',$item,$params,array('cls'=>'')); ?>
                                <?php endif; ?>
                                <?php if ($params->get('meta_author', 1) == 1) : ?>
                                    <?php echo JHtml::_('itemdetails.itemAuthor',$item,$params);?>
                                <?php endif; ?>
                                <?php if ($params->get('meta_category', 1) == 1) : ?>
                                    <?php echo JHtml::_('itemdetails.itemCategory',$item,$params);?>
                                <?php endif; ?>
                            </div><!-- end .mb2-content-item-meta -->
                        <?php endif; ?>
                        <?php if ($params->get('introtext', 1) == 1 && $item->introtext !='') :			
							$introtext = modMb2contentHelper::wordLimit($item->introtext, $params->get('introtext_limit', 999), '...');
						?>
							<div class="mb2-content-item-desc">
								<?php echo $introtext; ?>
							</div><!-- end .mb2-content-item-desc -->
						<?php endif; ?>
                        <?php if ($item_links) : ?>
                        	<div class="mb2-content-img-links">
								<?php echo JHtml::_('itemdetails.itemLinks',$item,$params,array('type'=>1,'link'=>$item->image,'content'=>'<span class="mb2c_icon-resize-full"></span>','gid'=>$gid,'lcls'=>$lcls,'ldata'=>$ldata)); ?>
                            	<?php echo JHtml::_('itemdetails.itemLinks',$item,$params,array('type'=>2,'link'=>$item->link,'content'=>'<span class="mb2c_icon-plus"></span>','gid'=>$gid,'lcls'=>$lcls,'ldata'=>$ldata)); ?>
                            </div><!-- end .mb2-content-img-links -->
                        <?php endif; ?>
            		</div><!-- end .mb2-content-hover-content-inner --> 
     			</div><!-- end .mb2-content-hover-content --> 
     		</div><!-- end .mb2-content-hover-bg --> 
            <?php if ($layout !== 'news-bar') : ?>
        		<img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $alttext; ?>" />
            <?php endif; ?>
   		</div><!-- end .mb2-content-item-media-inner -->            
	</div><!-- end .mb2-content-item-media -->              
<?php endif; ?>