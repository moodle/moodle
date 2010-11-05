<?php

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-pre', $OUTPUT));
$hassidepost = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-post', $OUTPUT));

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
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}

if (!empty($PAGE->theme->settings->logo)) {
    $logourl = $PAGE->theme->settings->logo;
} else {
    $logourl = $OUTPUT->pix_url('logo', 'theme');
}

if (!empty($PAGE->theme->settings->footnote)) {
    $footnote = $PAGE->theme->settings->footnote;
} else {
    $footnote = '<!-- There was no custom footnote set -->';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php echo $PAGE->bodyid ?>" class="<?php echo $PAGE->bodyclasses.' '.join(' ', $bodyclasses) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="page">
    <div id="page2">
        <div id="headerleft" class="headerleft"><div>&nbsp;</div></div>
        <div id="bodyleft" class="bodyleft">
            <div id="bodyright" class="bodyright">
                <div id="header-i3" class="i3">
                <?php  if ($hasheading || $hasnavbar) {  // This is what gets printed on the home page only
                ?>
                    <div id="header-home" class="clearfix">
                        <div id="headerenvelop">

                            <!-- //echo '<h1 class="logo headermain">'.$PAGE->heading.'</h1>'; -->
                            <?php echo '<div id="logo"><img class="sitelogo" src="'.$logourl.'" alt="Custom logo here" /></div>';
                            echo '<div class="headermenu">';
                                echo $OUTPUT->login_info();
                                if (!empty($PAGE->theme->settings->alwayslangmenu)) {
                                    echo $OUTPUT->lang_menu();
                                }
                                echo $PAGE->headingmenu;
                            echo '</div>'; // closes: <div class="headermenu">

                        echo '</div>'; // closes: <div id="headerenvelop">
                    echo '</div>'; // closes: <div id="header-home" class="clearfix">

                    if ($hascustommenu) {
                        echo '<div id="custommenu">'.$custommenu.'</div>';
                    }

                    //Accessibility: breadcrumb trail/navbar now a DIV, not a table.
                    if ($hasnavbar) {
                        echo '<div class="navbar clearfix">';
                        echo '    <div class="breadcrumb">'.$OUTPUT->navbar().'</div>';
                        echo '    <div class="navbutton">'.$PAGE->button.'</div>';
                        echo '</div>';
                    }

                } ?>

<!-- END OF HEADER -->

                    <div id="page-content" class="clearfix shrinker">
                        <div id="report-main-content">
                            <div class="region-content">
                                <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
                            </div>
                        </div>
                        <?php if ($hassidepre) { ?>
                        <div id="report-region-wrap">
                            <div id="report-region-pre" class="block-region">
                                <div class="region-content">
                                    <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

				</div> <!-- closes: <div id="header-i3" class="i3"> -->
			</div> <!-- closes: <div id="bodyright" class="bodyright"> -->
		</div> <!-- closes: <div id="bodyleft" class="bodyleft"> -->
        <div id="contentfooter" class="contentfooter"><div>&nbsp;</div></div>
    </div>  <!-- closes: <div id="page2"> -->
</div> <!-- closes:<div id="page"> -->

<!-- START OF FOOTER -->
<?php if ($hasfooter) { ?>
    <div id="page-footer" class="clearfix">
        <?php echo $footnote; ?>
        <p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </div>
<?php } ?>

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>