<?php

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$bodyclasses = array();
if ($hassidepre && !$hassidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($hassidepost && !$hassidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$hassidepost && !$hassidepre) {
    $bodyclasses[] = 'content-only';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <meta name="description" content="<?php p(strip_tags(format_text($SITE->summary, FORMAT_HTML))) ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">

<!-- START OF HEADER -->

	<div id="wrapper" class="clearfix">

    	<div id="page-header">
			<div id="page-header-wrapper" class="clearfix">
	        	<h1 class="headermain"><?php echo $PAGE->heading ?></h1>
	    	    <div class="headermenu">
    	    		<?php
	    	    	    echo $OUTPUT->login_info();
    	    	    	echo $OUTPUT->lang_menu();
	        		    echo $PAGE->headingmenu;
			        ?>
		    	</div>
	    	</div>
	    </div>

        <div class="clearer"></div> <!-- temporarily added on 06/25/10 -->

<!-- END OF HEADER -->

<!-- START OF CONTENT -->

		<div id="page-content-wrapper" class="clearfix">
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
            		    <div id="region-pre">
                		    <div class="region-content">
                    		    <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
		                    </div>
    		            </div>
        		        <?php } ?>

	        	        <?php if ($hassidepost) { ?>
    	        	    <div id="region-post">
        	        	    <div class="region-content">
            	        	    <?php echo $OUTPUT->blocks_for_region('side-post') ?>
	                	    </div>
		                </div>
    		            <?php } ?>

	        	    </div>
	    	    </div>
		    </div>
		</div>

<!-- END OF CONTENT -->

<!-- START OF FOOTER -->

	    <div id="page-footer">
    	    <p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>

	        <?php
		        echo $OUTPUT->login_info();
    		    echo $OUTPUT->home_link();
        		echo $OUTPUT->standard_footer_html();
	        ?>
    	</div>

<!-- END OF FOOTER -->

	</div> <!-- END #wrapper -->

</div><!-- END #page -->

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>