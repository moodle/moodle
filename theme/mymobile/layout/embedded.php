<?php
if (!empty($PAGE->theme->settings->mswatch)) {
$showswatch = $PAGE->theme->settings->mswatch;
} else {
$showswatch = "light";
}
if ($showswatch == "light") {
$dtheme = "d";
$dthemeb = "d";
$datatheme = "data-theme=b";
$databodytheme = "data-theme=d";
}
else {
$dtheme = "d";
$dthemeb = "c";
$datatheme = "data-theme=a";
$databodytheme = "data-theme=c";
}

?>
<?php echo $OUTPUT->doctype() ?>
<?php $mypagetype = $this->page->pagetype; ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $SITE->shortname ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php if ($mypagetype != 'mod-chat-gui_ajax-index') { ?>
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" /> 
    <?php } ?>  
    <?php echo $OUTPUT->standard_head_html() ?>
        
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>



<!-- END OF HEADER -->
<?php if ($mypagetype == 'mod-chat-gui_ajax-index') {
?>
<div data-role="page" id="chatpage" data-fullscreen="true" data-title="<?php p($SITE->shortname) ?>">
<?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
<input type="button" value="back" data-role="none" id="chatback" onClick="history.back()">
</div>
<?php } else { ?>
    <div id="content2" data-role="page" data-title="<?php p($SITE->shortname) ?>" <?php p($datatheme); ?>> 
    <div data-role="header" <?php p($datatheme) ?>><h1><?php echo $PAGE->heading ?>&nbsp;</h1>
    <?php if ($mypagetype != "help") { ?>
     <a class="ui-btn-right" data-ajax="false" data-icon="home" href="<?php p($CFG->wwwroot) ?>" data-iconpos="notext"><?php p(get_string('home')); ?></a>
     <?php } ?>
    </div>
    <div data-role="content" <?php p($databodytheme); ?>>
        <?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
    </div>
    </div>
<?php } ?>  
<!-- START OF FOOTER -->



<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>