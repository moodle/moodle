<?php
$properties = new stdClass;
$properties->sidepre = theme_enable_block_region('side-pre');
$properties->sidepost = theme_enable_block_region('side-post');
$properties->hasnavbar = false;

$bodyclasses = array();

if ($properties->sidepre && !$properties->sidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($properties->sidepost && !$properties->sidepre) {
    $bodyclasses[] = 'side-post-only';
}

if (!$properties->sidepost && !$properties->sidepre) {
    $bodyclasses[] = 'noblocks';
}
if (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar()) {
    $bodyclasses[] = 'hasnavbar';
    $properties->hasnavbar = true;
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php echo $PAGE->pagetype ?>" class="<?php echo $PAGE->bodyclasses.' '.join(' ', $bodyclasses) ?>">
<?php echo $OUTPUT->standard_top_of_body_html(); ?>
<div id="page">

    <div class="page-header">
        <div class="rounded-corner top-left"></div>
        <div class="rounded-corner top-right"></div>
        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
        <div class="headermenu"><?php
            echo $OUTPUT->login_info();
            if (!empty($PAGE->layout_options['langmenu'])) {
                echo $OUTPUT->lang_menu();
            }
            echo $PAGE->headingmenu
        ?></div>
        <?php if ($properties->hasnavbar) { // This is the navigation bar with breadcrumbs  ?>
        <div class="navbar clearfix">
            <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
            <div class="navbutton"><?php echo $PAGE->button; ?></div>
        </div>
        <?php } ?>
    </div>
    <div class="page-middle">
        <div class="column-container">
            <div class="column-mask">
                <div class="column-centre">
                    <div class="column-wrap">
                        <div class="column-pad">
                            <div class="column-content">
                                <!-- MAIN CONTENT START -->
                                <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
                                <!-- MAIN CONTENT END -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column-left">
                    <div class="column-content  block-region side-pre"><?php
                        if ($properties->sidepre) {
                            echo $OUTPUT->blocks_for_region('side-pre');
                        } ?>
                    </div>
                </div>
                <div class="column-right">
                    <div class="column-content block-region side-post"><?php
                        if ($properties->sidepost) {
                            echo $OUTPUT->blocks_for_region('side-post');
                        } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php if (empty($PAGE->layout_options['nofooter'])) { ?>
    <div class="page-footer">
        <p class="helplink">
            <?php echo page_doc_link(get_string('moodledocslink')) ?>
        </p>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
        <div class="rounded-corner bottom-left"></div>
        <div class="rounded-corner bottom-right"></div>
    </div>
    <?php } ?>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>