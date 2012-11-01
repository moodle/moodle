<?php

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-pre', $OUTPUT));
$hassidepost = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-post', $OUTPUT));
$haslogininfo = (empty($PAGE->layout_options['nologininfo']));

$showsidepre = ($hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT));
$showsidepost = ($hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT));

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$hasfootnote = (!empty($PAGE->theme->settings->footnote));

$courseheader = $coursecontentheader = $coursecontentfooter = $coursefooter = '';
if (empty($PAGE->layout_options['nocourseheaderfooter'])) {
    $courseheader = $OUTPUT->course_header();
    $coursecontentheader = $OUTPUT->course_content_header();
    if (empty($PAGE->layout_options['nocoursefooter'])) {
        $coursecontentfooter = $OUTPUT->course_content_footer();
        $coursefooter = $OUTPUT->course_footer();
    }
}

$bodyclasses = array();
if ($showsidepre && !$showsidepost) {
    if (!right_to_left()) {
        $bodyclasses[] = 'side-pre-only';
    } else {
        $bodyclasses[] = 'side-post-only';
    }
} else if ($showsidepost && !$showsidepre) {
    if (!right_to_left()) {
        $bodyclasses[] = 'side-post-only';
    } else {
        $bodyclasses[] = 'side-pre-only';
    }
} else if (!$showsidepost && !$showsidepre) {
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>

<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="page-wrapper">
  <div id="page">
    <?php if ($hasheading) { ?>
        <div id="page-header">
         <a class="logo" href="<?php echo $CFG->wwwroot; ?>" title="<?php print_string('home'); ?>"></a>
         <div class="headermenu"><?php
            if ($haslogininfo) {
                echo $OUTPUT->login_info();
            }
            if (!empty($PAGE->layout_options['langmenu'])) {
                echo $OUTPUT->lang_menu();
            }
            echo $PAGE->headingmenu
            ?></div>
        </div>
    <?php } ?>
<!-- END OF HEADER -->
<!-- START CUSTOMMENU AND NAVBAR -->
    <div id="navcontainer">
        <?php if ($hascustommenu) { ?>
                <div id="custommenu" class="javascript-disabled"><?php echo $custommenu; ?></div>
        <?php } ?>

    </div>

        <?php if (!empty($courseheader)) { ?>
            <div id="course-header"><?php echo $courseheader; ?></div>
        <?php } ?>

        <?php if ($hasnavbar) { ?>
            <div class="navbar clearfix">
                <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
                <div class="navbutton"> <?php echo $PAGE->button; ?></div>
            </div>
        <?php } ?>

<!-- END OF CUSTOMMENU AND NAVBAR -->
    <div id="page-content">
       <div id="region-main-box">
           <div id="region-pre-box">
               <div id="region-main">
                   <div class="region-content">
                       <?php echo $coursecontentheader; ?>
                       <?php echo $OUTPUT->main_content() ?>
                       <?php echo $coursecontentfooter; ?>
                   </div>
               </div>

               <?php if ($hassidepre OR (right_to_left() AND $hassidepost)) { ?>
               <div id="region-pre" class="block-region">
                   <div class="region-content">
                           <?php
                       if (!right_to_left()) {
                           echo $OUTPUT->blocks_for_region('side-pre');
                       } elseif ($hassidepost) {
                           echo $OUTPUT->blocks_for_region('side-post');
                   } ?>

                   </div>
               </div>
               <?php } ?>

               <?php if ($hassidepost OR (right_to_left() AND $hassidepre)) { ?>
               <div id="region-post" class="block-region">
                   <div class="region-content">
                          <?php
                      if (!right_to_left()) {
                          echo $OUTPUT->blocks_for_region('side-post');
                      } elseif ($hassidepre) {
                          echo $OUTPUT->blocks_for_region('side-pre');
                   } ?>
                   </div>
               </div>
               <?php } ?>

            </div>
        </div>
    </div>

    <!-- START OF FOOTER -->
    <?php if (!empty($coursefooter)) { ?>
        <div id="course-footer"><?php echo $coursefooter; ?></div>
    <?php } ?>
    <?php if ($hasfooter) { ?>
    <div id="page-footer" class="clearfix">

        <div class="footer-left">

            <?php if ($hasfootnote) { ?>
                    <div id="footnote"><?php echo $PAGE->theme->settings->footnote;?></div>
            <?php } ?>

            <a href="http://moodle.org" title="Moodle">
                <img src="<?php echo $OUTPUT->pix_url('footer/moodle-logo','theme')?>" alt="Moodle logo" />
            </a>
        </div>

        <div class="footer-right">
            <?php echo $OUTPUT->login_info();?>
        </div>

        <?php echo $OUTPUT->standard_footer_html(); ?>
    </div>
    <?php } ?>
    <div class="clearfix"></div>
</div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
