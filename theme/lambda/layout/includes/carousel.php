<?php
$hascarousel = (!empty($PAGE->theme->settings->carousel_image_1));
$numberofslides = $PAGE->theme->settings->carousel_slides;
$hasheading = (!empty($PAGE->theme->settings->carousel_h));
$has_additional_html = (!empty($PAGE->theme->settings->carousel_add_html));
?>

<?php if ($hascarousel) { ?>

<div class="row-fluid frontpage">
<?php if ($has_additional_html) { ?>
  <div class="span4">
  <?php 
  		$additional_html = $PAGE->theme->settings->carousel_add_html;
		$additional_html = format_text($additional_html, FORMAT_HTML);
  		echo $additional_html;
	?>  
  </div>
<div class="span8">
<?php } ?>

	<div class="carousel">
    <?php if ($hasheading) {
		$carouselheading = $PAGE->theme->settings->carousel_h;
		$carouselheading = format_text($carouselheading, FORMAT_HTML);
  		echo '<h'.$PAGE->theme->settings->carousel_hi.' class="bx-heading">'.$carouselheading.'</h'.$PAGE->theme->settings->carousel_hi.'>';	
		echo '<span id="slider-prev"></span><span id="slider-next"></span>';
	} else {
		echo '<div style="padding-bottom: 20px;"><span id="slider-prev"></span><span id="slider-next"></span></div>';
	} ?>
  	
	</div>
	<div class="slider1">    	
        <?php for ($i = 1; $i <= $numberofslides; $i++) { ?>
        	<?php
            $current_image = 'carousel_image_'.$i;
			if (!empty($PAGE->theme->settings->$current_image)) { ?>
            	<?php 
				$current_heading = 'carousel_heading_'.$i;
				if ($PAGE->theme->settings->$current_heading!='') echo '<div class="caption-hover">'; 
				?>
        		<div class="slide">                
                <?php $image = $PAGE->theme->setting_file_url($current_image, $current_image);?>
                <img src="<?php echo $image; ?>" alt="<?php echo $current_image; ?>"/>

        		</div>
                <?php
				if ($PAGE->theme->settings->$current_heading!='') {			
					$current_color = 'carousel_color_'.$i;
					$color_number = $PAGE->theme->settings->$current_color;
					//fallback previous theme versions
					if ($color_number=='0') {$color_number = '#8ec63f';}
					else if ($color_number=='1') {$color_number = '#92499e';}
					else if ($color_number=='2') {$color_number = '#f1592a';}
					else if ($color_number=='3') {$color_number = '#38b9ec';}
					else if ($color_number=='4') {$color_number = '#fdb53f';}
					else if ($color_number=='5') {$color_number = '#29a294';}
					$color_number = substr($color_number,1,6);
					$split = str_split($color_number, 2);
					$r = hexdec($split[0]);
					$g = hexdec($split[1]);
					$b = hexdec($split[2]);
					$color = "rgba(" . $r . ", " . $g . ", " . $b . ", 0.85)";
					echo '<div class="mask" style="background-color: '.$color.'">';
					$heading = $PAGE->theme->settings->$current_heading;
					$heading = format_text($heading, FORMAT_HTML);
					echo '<h2>'.$heading.'</h2>';
					$current_caption = 'carousel_caption_'.$i;
					$caption = $PAGE->theme->settings->$current_caption;
					$caption = format_text($caption, FORMAT_HTML);
					echo '<p>'.$caption.'</p>';
					$current_url = 'carousel_url_'.$i;
					$url = $PAGE->theme->settings->$current_url;
					$current_btntext = 'carousel_btntext_'.$i;
					$btntext = $PAGE->theme->settings->$current_btntext;
					$btntext = format_text($btntext, FORMAT_HTML);
					if ($url!='') echo '<a class="info" href="'.$url.'">'.$btntext.'</a>';
					echo '</div></div>';
				}
				?>					
    
			<?php } ?>
		<?php } ?>
        
	</div>

<?php if ($has_additional_html) { ?>    
  </div>
<?php } ?>
</div>

<?php } ?>