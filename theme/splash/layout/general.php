<?php

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($hassidepre && !$hassidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($hassidepost && !$hassidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$hassidepost && !$hassidepre) {
    $bodyclasses[] = 'content-only';
}

if (!empty($PAGE->theme->settings->logo)) {
    $logourl = $PAGE->theme->settings->logo;
} else {
    $logourl = NULL;
}

if (!empty($PAGE->theme->settings->tagline)) {
    $tagline = $PAGE->theme->settings->tagline;
} else {
    $tagline = "Virtual Learning Center";
}

if (!empty($PAGE->theme->settings->footnote)) {
    $footnote = $PAGE->theme->settings->footnote;
} else {
    $footnote = "";
}

if (!empty($PAGE->theme->settings->hide_tagline) && $PAGE->theme->settings->hide_tagline == 1) 
{
    $hidetagline = $PAGE->theme->settings->hide_tagline;
} else {
    $hidetagline = "";
}



echo $OUTPUT->doctype() ?>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <meta name="description" content="<?php echo strip_tags(format_text($SITE->summary, FORMAT_HTML)) ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>

    <link rel="alternate stylesheet" type="text/css" href="<?php echo $CFG->wwwroot .'/theme/'. current_theme() ?>/style/green.css" title="green" media="screen" />
     
     <link rel="alternate stylesheet" type="text/css" href="<?php echo $CFG->wwwroot .'/theme/'. current_theme() ?>/style/blue.css" title="blue" media="screen" />
      <link rel="alternate stylesheet" type="text/css" href="<?php echo $CFG->wwwroot .'/theme/'. current_theme() ?>/style/orange.css" title="orange" media="screen" />

     <noscript>
<style type="text/css">
#colourswitcher
{
	display:none;
}
</style>
</noscript>
    
</head>


<body id="<?php echo $PAGE->bodyid ?>" class="<?php echo $PAGE->bodyclasses.' '.join(' ', $bodyclasses) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>


<div id="page">

	<?php if ($hasheading || $hasnavbar) { ?>
    	<div id="page-header">
			<div id="page-header-wrapper" class="wrapper clearfix">
	        	<?php if ($hasheading) { ?>

		             <div id="headermenu">
                  		 <?php
                    if (isloggedin())
                    {
                        
                       	echo '<div id="userdetails"><h1>Hi '.$USER->firstname.'!</h1>';
                        echo '<p class="prolog"><a href="'.$CFG->wwwroot.'/user/profile.php?id='.$USER->id.'">'.get_string('myprofile').'</a> | <a href="'.$CFG->wwwroot.'/login/logout.php?sesskey='.sesskey().'">'.get_string('logout').'</a></p></div>';
                        echo '<div id="userimg">'.$OUTPUT->user_picture($USER, array('size'=>55)).'</div>';
                        
                     } else {
                        echo '<div id="userdetails_loggedout"><h1>Welcome,
							  <a href="'.$CFG->wwwroot.'/login/index.php?">Login here!</a></h1></div>';
                    }
                    
                    
                    ?>			    	     
                		<div class="clearer"></div>
               			<div id="colourswitcher" align="right">
              				<ul>
                     			<li><img src="<?php echo $CFG->wwwroot .'/theme/'. current_theme().'/pix/colour.jpg' ?>" /></li>
                           		<li><a onclick="setActiveStyleSheet('sl'); return false;" href="<?php echo $CFG->wwwroot  ?>" rel="sl" class="styleswitch"><img src="<?php echo $OUTPUT->pix_url('red-theme2', 'theme'); ?>" /></a></li>
                        		<li><a onclick="setActiveStyleSheet('green'); return false;" href="<?php echo $CFG->wwwroot  ?>" rel="green" class="styleswitch"><img src="<?php echo $OUTPUT->pix_url('green-theme2', 'theme'); ?>" /></a></li>
                        		<li><a onclick="setActiveStyleSheet('blue'); return false;" href="<?php echo $CFG->wwwroot ?>" rel="blue" class="styleswitch"><img src="<?php echo $OUTPUT->pix_url('blue-theme2', 'theme'); ?>" /></a></li>
                        		<li><a onclick="setActiveStyleSheet('orange'); return false;" href="<?php echo $CFG->wwwroot?>" rel="orange" class="styleswitch"><img src="<?php echo $OUTPUT->pix_url('orange-theme2', 'theme'); ?>" /></a></li>
                    		</ul>   
                		</div>

                 </div>
         
            
                <div id="logobox">
                	<?php if ($logourl == NULL) { ?>
                		<a class="nologoimage" href="<?php echo $CFG->wwwroot ?>">
                			<?php echo $PAGE->heading ?>
                	<?php } else { ?>
                		<a href="<?php echo $CFG->wwwroot ?>">
                			<img src="<?php echo $logourl ?>"  />
               		 <?php } ?>
                		</a>
                
                	<?php if ($hidetagline == 0) { ?>
                	<h4><?php echo $tagline ?></h4>
                	<?php } ?>
                
                </div>
                
           		<div class="clearer"></div>
           		<?php if ($logourl == NULL) { ?>
           		<h4 class="headermain inside">&nbsp;</h4>
           		<?php } else { ?>
    		    <h4 class="headermain inside"><?php echo $PAGE->heading ?></h4>
		        <?php } ?>
		        
	        <?php } ?>
	    
        
        
        
        <!-- DROP DOWN MENU -->
		<div class="clearer"></div>
		<div id="dropdownmenu">
 		<?php if ($hascustommenu) { ?>
 			<div id="custommenu"><?php echo $custommenu; ?></div>
		<?php } ?>

			<div class="navbar">
            	<div class="wrapper clearfix">
	               <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
    	           <div class="navbutton"> <?php echo $PAGE->button; ?></div>
    	    	</div>
        	</div>
            
		</div>


		</div>
<!-- END DROP DOWN MENU -->


        
    </div>

       
<?php } ?>



            
<!-- END OF HEADER -->





<!-- START OF CONTENT -->

    <div id="page-content">
        <div id="region-main-box">
            <div id="region-post-box">
            
                <div id="region-main-wrap">
                    <div id="region-main">
                        <div class="region-content">
                            <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($hassidepre) { ?>
                <div id="region-pre" class="block-region">
                    <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                    </div>
                </div>
                <?php } ?>
                
                <?php if ($hassidepost) { ?>
                <div id="region-post" class="block-region">
                    <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                    </div>
                </div>
                <?php } ?>
                
            </div>
        </div>
    </div>


	<!-- END OF CONTENT -->
	<div class="clearfix"></div>
<!-- END OF #Page -->
</div>

<!-- START OF FOOTER -->
<?php if ($hasfooter) { ?>
<div id="page-footer">
	<div id="footer-wrapper">
        <?php if ($footnote != "") { ?>
        <div id="footnote"><?php echo $footnote; ?></div>
       	<?php } ?>
        	<p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>
        	<?php
        	echo $OUTPUT->login_info();
        	echo $OUTPUT->home_link();
        	echo $OUTPUT->standard_footer_html();
        	?>
     </div>
</div>
    <?php } ?>

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>