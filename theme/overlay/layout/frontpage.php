<?php

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));

$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$showsidepre = ($hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT));
$showsidepost = ($hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT));

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($showsidepre && !$showsidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($showsidepost && !$showsidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$showsidepost && !$showsidepre) {
    $bodyclasses[] = 'content-only';
}

if (!empty($PAGE->theme->settings->footertext)) {
    $footnote = $PAGE->theme->settings->footertext;
} else {
    $footnote = '<!-- There was no custom footnote set -->';
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
    <div id="page-header">
		<div id="page-header-wrapper" class="wrapper clearfix">
	        <?php if ($hascustommenu) { ?>
 					<div id="custommenu"><?php echo $custommenu; ?></div>
				<?php } ?>
    	    <div class="headermenu">
        		<?php
	        	    echo $OUTPUT->login_info();
    	        	echo $OUTPUT->lang_menu();
	        	    echo $PAGE->headingmenu;
		        ?>
	    	</div>
	    </div>
    </div>
<!-- END OF HEADER -->

<!-- START OF CONTENT -->
<div id="top">

	<div id="page-content-wrapper" class="wrapper clearfix">
	    <div id="page-content">
    	    <div id="region-main-box">
        	    <div id="region-post-box">

            	    <div id="region-main-wrap">

                	    <div id="region-main">
                    	    <div id="newheader">
                    	    	<div id="newheaderleft"></div>
                    	    	<h1 class="headermain"><?php echo $PAGE->heading ?></h1>

                    	    </div>
                    	    <div class="region-content">



                        	    <?php echo $OUTPUT->main_content() ?>
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
	</div>

</div>
<!-- END OF CONTENT -->


</div>
<!-- START OF FOOTER -->

    <div id="page-footer" class="wrapper2">
        <p class="helplink">
        <?php echo page_doc_link(get_string('moodledocslink')) ?>
        </p>

        <?php echo $footnote ?>

        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </div>

<!-- END OF FOOTER -->

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>