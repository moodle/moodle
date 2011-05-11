<?php
defined('MOODLE_INTERNAL') || die();

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses) ?>">
<?php echo $OUTPUT->standard_top_of_body_html(); ?>

    <div id="page-content">
        <div id="region-main-box">
            <div id="region-post-box">

                <!-- main mandatory content of the moodle page  -->
                <div id="region-main-wrap">
                    <div id="region-main">
                        <div class="region-content">
                            <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
                        </div>
                    </div>
                </div>
                <!-- end of main mandatory content of the moodle page -->

            </div>
        </div>
    </div>

<?php echo $OUTPUT->standard_end_of_body_html(); ?>
</body>
</html>