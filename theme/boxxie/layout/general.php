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

if ($hascustommenu) {
    $bodyclasses[] = 'has-custom-menu';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
  <title><?php echo $PAGE->title; ?></title>
  <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
  <?php echo $OUTPUT->standard_head_html() ?>
</head>

<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php if ($hasheading || $hasnavbar) { ?>

<div id="page-wrapper">
  <div id="page" class="clearfix">

    <div id="page-header" class="clearfix">
      <?php if ($PAGE->heading) { ?>
        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
        <div class="headermenu">
          <?php echo $OUTPUT->login_info();
          if (!empty($PAGE->layout_options['langmenu'])) {
            echo $OUTPUT->lang_menu();
          }
          echo $PAGE->headingmenu; ?>
        </div>
      <?php } ?>
      <?php if ($hascustommenu) { ?>
      <div id="custommenu"><?php echo $custommenu; ?></div>
      <?php } ?>
    </div>

 <div class="myclear"></div>

      <?php if ($hasnavbar) { ?>
        <div class="navbar clearfix">
          <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
          <div class="navbutton"> <?php echo $PAGE->button; ?></div>
        </div>
      <?php } ?>

<?php } ?>

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

    <div class="myclear"></div>
<?php if ($hasfooter) { ?>

    <div id="page-footer" class="clearfix">
      <p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>
      <?php echo $OUTPUT->login_info(); ?>
    </div>

<?php }

if ($hasheading || $hasnavbar) { ?>
   <div class="myclear"></div>
  </div> <!-- END #page -->

</div> <!-- END #page-wrapper -->

<?php } ?>

<div id="page-footer-bottom">

<?php if ($hasfooter) {

  echo $OUTPUT->home_link();
  echo $OUTPUT->standard_footer_html();

} ?>

</div>


<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>