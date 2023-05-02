<?php
$shadow_effect = theme_lambda_get_setting('shadow_effect');
$hasslide1image = (!empty($PAGE->theme->settings->slide1image));
$hasslide2image = (!empty($PAGE->theme->settings->slide2image));
$hasslide3image = (!empty($PAGE->theme->settings->slide3image));
$hasslide4image = (!empty($PAGE->theme->settings->slide4image));
$hasslide5image = (!empty($PAGE->theme->settings->slide5image));
$hasslideshow = ($hasslide1image||$hasslide2image||$hasslide3image||$hasslide4image||$hasslide5image);
$pattern='';
if($PAGE->theme->settings->slideshowpattern==1) {$pattern='pattern_1';}
else if($PAGE->theme->settings->slideshowpattern==2) {$pattern='pattern_2';}
else if($PAGE->theme->settings->slideshowpattern==3) {$pattern='pattern_3';}
else if($PAGE->theme->settings->slideshowpattern==4) {$pattern='pattern_4';}

$txtfx='moveFromLeft';
if ($PAGE->theme->settings->slideshow_txtfx!='') {$txtfx=$PAGE->theme->settings->slideshow_txtfx;}
?>

<?php if ($hasslideshow) { ?>
	<div class="camera_wrap camera_emboss <?php echo $pattern; ?>" id="camera_wrap" <?php if (!$shadow_effect) { ?>style="margin-bottom:10px;"<?php } ?>>
    
    <?php if ($hasslide1image) { ?>
		<div data-src="<?php echo $PAGE->theme->setting_file_url('slide1image', 'slide1image'); ?>">
			<div class="camera_caption <?php echo $txtfx; ?>">
            	<?php if (!empty($PAGE->theme->settings->slide1)) { 
                	$slideheading_HTML = $PAGE->theme->settings->slide1;
					$slideheading_HTML = format_text($slideheading_HTML,FORMAT_HTML);				
				?>
				<h1><?php echo $slideheading_HTML; ?></h1>
                <?php } ?>
                <?php if (!empty($PAGE->theme->settings->slide1caption)) { 
                	$slidecaption_HTML = $PAGE->theme->settings->slide1caption;
					$slidecaption_HTML = format_text($slidecaption_HTML,FORMAT_HTML);
				?>
				<span><?php echo $slidecaption_HTML; ?>
                 <?php if (!empty($PAGE->theme->settings->slide1_url)) { ?>
                 <div style="text-align: right; margin-bottom: 0px;">
                   <a class="btn btn-default" href="<?php echo $PAGE->theme->settings->slide1_url; ?>"><?php echo get_string('more'); ?>&nbsp;...</a>
                 </div>
                <?php } ?>
                </span>
                <?php } ?>
			</div>
		</div>
    <?php } ?>
    
    <?php if ($hasslide2image) { ?>
		<div data-src="<?php echo $PAGE->theme->setting_file_url('slide2image', 'slide2image'); ?>">
			<div class="camera_caption <?php echo $txtfx; ?>">
            	<?php if (!empty($PAGE->theme->settings->slide2)) { 
                	$slideheading_HTML = $PAGE->theme->settings->slide2;
					$slideheading_HTML = format_text($slideheading_HTML,FORMAT_HTML);				
				?>
				<h1><?php echo $slideheading_HTML; ?></h1>
                <?php } ?>
                <?php if (!empty($PAGE->theme->settings->slide2caption)) { 
                	$slidecaption_HTML = $PAGE->theme->settings->slide2caption;
					$slidecaption_HTML = format_text($slidecaption_HTML,FORMAT_HTML);
				?>
				<span><?php echo $slidecaption_HTML; ?>
                 <?php if (!empty($PAGE->theme->settings->slide2_url)) { ?>
                 <div style="text-align: right; margin-bottom: 0px;">
                   <a class="btn btn-default" href="<?php echo $PAGE->theme->settings->slide2_url; ?>"><?php echo get_string('more'); ?>&nbsp;...</a>
                 </div>
                <?php } ?>
                </span>
                <?php } ?>
			</div>
		</div>
    <?php } ?>
    
    <?php if ($hasslide3image) { ?>
		<div data-src="<?php echo $PAGE->theme->setting_file_url('slide3image', 'slide3image'); ?>">
			<div class="camera_caption <?php echo $txtfx; ?>">
            	<?php if (!empty($PAGE->theme->settings->slide3)) { 
                	$slideheading_HTML = $PAGE->theme->settings->slide3;
					$slideheading_HTML = format_text($slideheading_HTML,FORMAT_HTML);				
				?>
				<h1><?php echo $slideheading_HTML; ?></h1>
                <?php } ?>
                <?php if (!empty($PAGE->theme->settings->slide3caption)) { 
                	$slidecaption_HTML = $PAGE->theme->settings->slide3caption;
					$slidecaption_HTML = format_text($slidecaption_HTML,FORMAT_HTML);
				?>
				<span><?php echo $slidecaption_HTML; ?>
                 <?php if (!empty($PAGE->theme->settings->slide3_url)) { ?>
                 <div style="text-align: right; margin-bottom: 0px;">
                   <a class="btn btn-default" href="<?php echo $PAGE->theme->settings->slide3_url; ?>"><?php echo get_string('more'); ?>&nbsp;...</a>
                 </div>
                <?php } ?>
                </span>
                <?php } ?>
			</div>
		</div>
    <?php } ?>
    
    <?php if ($hasslide4image) { ?>
		<div data-src="<?php echo $PAGE->theme->setting_file_url('slide4image', 'slide4image'); ?>">
			<div class="camera_caption <?php echo $txtfx; ?>">
            	<?php if (!empty($PAGE->theme->settings->slide4)) { 
                	$slideheading_HTML = $PAGE->theme->settings->slide4;
					$slideheading_HTML = format_text($slideheading_HTML,FORMAT_HTML);				
				?>
				<h1><?php echo $slideheading_HTML; ?></h1>
                <?php } ?>
                <?php if (!empty($PAGE->theme->settings->slide4caption)) { 
                	$slidecaption_HTML = $PAGE->theme->settings->slide4caption;
					$slidecaption_HTML = format_text($slidecaption_HTML,FORMAT_HTML);
				?>
				<span><?php echo $slidecaption_HTML; ?>
                 <?php if (!empty($PAGE->theme->settings->slide4_url)) { ?>
                 <div style="text-align: right; margin-bottom: 0px;">
                   <a class="btn btn-default" href="<?php echo $PAGE->theme->settings->slide4_url; ?>"><?php echo get_string('more'); ?>&nbsp;...</a>
                 </div>
                <?php } ?>
                </span>
                <?php } ?>
			</div>
		</div>
    <?php } ?>
    
    <?php if ($hasslide5image) { ?>
		<div data-src="<?php echo $PAGE->theme->setting_file_url('slide5image', 'slide5image'); ?>">
			<div class="camera_caption <?php echo $txtfx; ?>">
            	<?php if (!empty($PAGE->theme->settings->slide5)) { 
                	$slideheading_HTML = $PAGE->theme->settings->slide5;
					$slideheading_HTML = format_text($slideheading_HTML,FORMAT_HTML);				
				?>
				<h1><?php echo $slideheading_HTML; ?></h1>
                <?php } ?>
                <?php if (!empty($PAGE->theme->settings->slide5caption)) { 
                	$slidecaption_HTML = $PAGE->theme->settings->slide5caption;
					$slidecaption_HTML = format_text($slidecaption_HTML,FORMAT_HTML);
				?>
				<span><?php echo $slidecaption_HTML; ?>
                 <?php if (!empty($PAGE->theme->settings->slide5_url)) { ?>
                 <div style="text-align: right; margin-bottom: 0px;">
                   <a class="btn btn-default" href="<?php echo $PAGE->theme->settings->slide5_url; ?>"><?php echo get_string('more'); ?>&nbsp;...</a>
                 </div>
                <?php } ?>
                </span>
                <?php } ?>
			</div>
		</div>
    <?php } ?>
    				
	</div>
    
<?php if ($shadow_effect) { ?>
	<div class="container-fluid"><img src="<?php echo $OUTPUT->image_url('bg/lambda-shadow', 'theme'); ?>" class="lambda-shadow" alt=""></div>
<?php } ?>
    
<?php } ?>