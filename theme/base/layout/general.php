<?php

$regionsinfo = 'pagelayout';
if ($PAGE->blocks->region_has_content('side-pre', $OUTPUT)) {
    $regionsinfo .= '-pre';
}
if ($PAGE->blocks->region_has_content('side-post', $OUTPUT)) {
    $regionsinfo .= '-post';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php echo $PAGE->pagetype ?>" class="<?php echo $PAGE->bodyclasses ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page"  class="<?php echo $regionsinfo ?>">

<?php if ($PAGE->heading) { ?>
    <div id="header" class="clearfix">
        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
        <div class="headermenu"><?php echo $PAGE->headingmenu ?></div>
    </div>
<?php } ?>

<?php if (empty($PAGE->layout_options['nonavbar']) and $PAGE->has_navbar()) { // This is the navigation bar with breadcrumbs  ?>
    <div class="navbar clearfix">
        <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
        <div class="navbutton"><?php echo $PAGE->button; ?></div>
    </div>
<?php } ?>
<!-- END OF HEADER -->

    <div class="regions-outer clearfix">
        <div id="regions">
            <div class="regions-inner">
                <div class="contentwrap">
                    <div id="content">
                        <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
                    </div>
                </div>
                <?php if ($PAGE->blocks->region_has_content('side-pre', $OUTPUT)) { ?>
                <div id="region-side-pre" class="block-region">
                    <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                </div>
                <?php } ?>
                <?php if ($PAGE->blocks->region_has_content('side-post', $OUTPUT)) { ?>
                <div id="region-side-post" class="block-region">
                    <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

<!-- START OF FOOTER -->
    <?php if (empty($PAGE->layout_options['nofooter'])) { ?>
    <div id="footer" class="clearfix">
        <p class="helplink">
        <?php echo page_doc_link(get_string('moodledocslink')) ?>
        </p>

        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </div>
    <?php } ?>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>