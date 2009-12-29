<?php echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php echo $PAGE->pagetype ?>" class="<?php echo $PAGE->bodyclasses ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">

<?php if ($PAGE->heading) { ?>
    <div id="header" class="clearfix">
        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
        <div class="headermenu"><?php
            echo $OUTPUT->login_info();
            if (!empty($PAGE->layout_options['langmenu'])) {
                echo $OUTPUT->lang_menu();
            }
            echo $PAGE->headingmenu
        ?></div>
    </div>
<?php } ?>

<?php if ($PAGE->has_navbar()) { // This is the navigation bar with breadcrumbs  ?>
    <div class="navbar clearfix">
        <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
        <div class="navbutton"><?php echo $PAGE->button; ?></div>
    </div>
<?php } else if ($PAGE->heading) { // If no navigation, but a heading, then print a line ?>
    <hr />
<?php } ?>
<!-- END OF HEADER -->

<!-- Note, we will not be using tables for layout evenutally. However, for now
     I have enough other things to worry about that I don't have time to make
     a multi-column cross-browser layout too, so this is a temporary hack. -->
    <table id="layout-table" summary="layout">
        <tr>
            <?php if ($PAGE->blocks->region_has_content('side-pre', $OUTPUT)) { ?>
            <td id="region-side-pre" class="block-region">
                <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
            </td>
            <?php } ?>
            <td id="content">
                <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
            </td>
            <?php if ($PAGE->blocks->region_has_content('side-post', $OUTPUT)) { ?>
            <td id="region-side-post" class="block-region">
                <?php echo $OUTPUT->blocks_for_region('side-post') ?>
            </td>
            <?php } ?>
        </tr>
    </table>

<!-- START OF FOOTER -->
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
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>