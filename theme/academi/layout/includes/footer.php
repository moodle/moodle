<?php
$footnote = theme_academi_get_setting('footnote', 'format_html');

$fburl = theme_academi_get_setting('fburl');
$pinurl = theme_academi_get_setting('pinurl');
$twurl = theme_academi_get_setting('twurl');
$gpurl = theme_academi_get_setting('gpurl');

$address = theme_academi_get_setting('address');
$emailid = theme_academi_get_setting('emailid');
$phoneno = theme_academi_get_setting('phoneno');
$copyright_footer = theme_academi_get_setting('copyright_footer');
$infolink = theme_academi_get_setting('infolink');

?>
<footer id="footer">
	<div class="footer-main">
  	<div class="container-fluid">
    	<div class="row-fluid">
      	<div class="span5">
        	<div class="infoarea">
          	<div class="footer-logo"><a href="#"><img src="<?php echo get_logo_url(); ?>" width="243" height="77" alt="Academi"></a></div>
             <?php echo $footnote; ?>
          </div>
        </div>
      	<div class="span3">
        	<div class="foot-links">
          	<h2>Info</h2>
             <ul> 
             <?php 
			 $info_settings =	explode("\n",$infolink);
			 
			 	foreach($info_settings as $key => $settingval)
				{
					$exp_set = explode("|",$settingval);
					list($ltxt,$lurl) = $exp_set;
					$ltxt = trim($ltxt);
					$lurl = trim($lurl);
					if(empty($ltxt))
					    continue;
					echo '<li><a href="'.$lurl.'" target="_blank">'.$ltxt.'</a></li>';
				}
			//	$atto_settings = $natto_settings;
			 
			 ?>
             </ul>
            <ul style="display:none;">            	
              <li><a href="<?php echo new moodle_url('/'); ?>">Home</a></li>
              <li><a href="https://moodle.org/forums/" target="_blank">Moodle community</a></li>
              <li><a href="https://docs.moodle.org" target="_blank">Moodle Docs</a></li>
              <li><a href="https://moodle.org/course/" target="_blank">Moodle support</a></li>
            </ul>
          </div>
        </div>
      	<div class="span4">
          <div class="contact-info">
            <h2 class="nopadding">Contact us</h2>
            <p><?php echo $address; ?><br>
              <i class="fa fa-phone-square"></i> Phone: <?php echo $phoneno; ?><br>
              <i class="fa fa-envelope"></i> E-mail: <a class="mail-link" href="mailto:<?php echo $emailid; ?>"><?php echo $emailid; ?></a><br>
            </p>
          </div>
         <?php 
		 if($fburl!='' || $pinurl!='' || $twurl!='' || $gpurl!='')
		 {
		 ?>
          <div class="social-media">
            <h6>Follow us</h6>
            <ul>
             <?php if($fburl!=''){?> <li class="smedia-01"><a href="<?php echo $fburl; ?>"><i class="fa fa-facebook-square"></i></a></li><?php }?>
               <?php if($pinurl!=''){?><li class="smedia-02"><a href="<?php echo $pinurl; ?>"><i class="fa fa-pinterest-square"></i></a></li><?php }?>
              <?php if($twurl!=''){?> <li class="smedia-03"><a href="<?php echo $twurl; ?>"><i class="fa fa-twitter-square"></i></a></li><?php }?>
              <?php if($gpurl!=''){?> <li class="smedia-04"><a href="<?php echo $gpurl; ?>"><i class="fa fa-google-plus-square"></i></a></li><?php }?>
            </ul>
          </div>
         <?php
		 }
		 ?>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bootom">
  	
    <?php if ($copyright_footer): ?>
      	<p><?php echo $copyright_footer; ?></p>
       <?php endif; ?>
  </div>
</footer>
<!--E.O.Footer-->

<footer>
<?php  echo $OUTPUT->standard_footer_html(); ?>
</footer>
<?php  echo $OUTPUT->standard_end_of_body_html() ?>