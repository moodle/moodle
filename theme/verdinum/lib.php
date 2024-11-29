<?php
defined('MOODLE_INTERNAL') || die();

function theme_verdinum_page_init(moodle_page $page)
{
    // require_once($CFG->dirroot . '/theme/educard/lib.php');
    theme_educard_page_init($page);
    $page->requires->js('/theme/verdinum/js/main.js');
}

function theme_verdinum_get_extra_scss($theme) {
    $loginbackgroundimageurl = theme_config::load('educard')->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    $imageurl = theme_config::load('educard')->setting_file_url('backgroundimage', 'backgroundimage');
    $extrascss = '';

    // Sets the background image, and its settings.
    if (!empty($imageurl)) {
        $extrascss .= '@media (min-width: 768px) {';
        $extrascss .= 'body { ';
        $extrascss .= "background-image: url('$imageurl'); background-size: cover;";
        $extrascss .= ' } }';
    }

    if (!empty($loginbackgroundimageurl)) {
        $extrascss .= 'body.pagelayout-login #page { ';
        $extrascss .= "background-image: url('$loginbackgroundimageurl'); background-size: cover;";
        $extrascss .= ' }';
    }
    return $extrascss . "\n" . $theme->settings->scss;
}
