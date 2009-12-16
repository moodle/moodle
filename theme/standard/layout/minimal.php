<?php echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->old_icon_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php echo $PAGE->pagetype ?>" class="<?php echo $PAGE->bodyclasses ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">

<?php if ($PAGE->heading) { ?>
    <div id="header" class="clearfix">
        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
        <div class="headermenu"><?php echo $PAGE->headingmenu ?></div>
    </div>
    <hr />
<?php } ?>
<!-- END OF HEADER -->

    <div id="content" class="clearfix">
        <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
    </div>

<!-- START OF FOOTER -->
    <div id="footer" class="clearfix">
        <?php echo $OUTPUT->standard_footer_html() ?>
    </div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>