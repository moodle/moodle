<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Load the migration files
 *
 * Load the our theme js and css file
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Front page init.
 *
 * @param moodle_page $PAGE page init.
 *
 */
function theme_educard_page_init(moodle_page $PAGE) {
    $PAGE->requires->css('/theme/educard/style/animate.min.css');
    $PAGE->requires->css('/theme/educard/style/splide-default.min.css');
    $PAGE->requires->css('/theme/educard/style/boxicons.min.css');
    $PAGE->requires->css('/theme/educard/style/educardmodal.min.css');
    $PAGE->requires->js('/theme/educard/js/main.min.js');
    $PAGE->requires->js('/theme/educard/js/navbar.min.js');
    $PAGE->requires->js('/theme/educard/js/navbarm.min.js');
    $PAGE->requires->js('/theme/educard/js/splide.min.js');
    $PAGE->requires->js('/theme/educard/js/splideblock.min.js');
    $PAGE->requires->js('/theme/educard/js/stopvideo.min.js');
    // Create font change css.
    if (educard_delete_css()) {
        educard_create_css();
    }
    if (educard_read_css()) {
        $PAGE->requires->css('/pluginfile.php/1/theme_educard/css/0/educard-fonts.css');
    } else {
        if (educard_create_css()) {
            $PAGE->requires->css('/pluginfile.php/1/theme_educard/css/0/educard-fonts.css');
        }
    }

    // Silinecek theme versiyon değiştirme.
    global $DB;
    $cplugin = $DB->get_record('config_plugins', ['plugin' => 'theme_educard', 'name' => 'version'] );
    if ($cplugin->value == "20230531000") {
        // Update.
        $cp = new stdclass;
        $cp->id = $cplugin->id;
        $cp->value = "2023053100";

        $sql = $DB->update_record('config_plugins', $cp);

        if (!$sql) {
            echo "Could not update";
        } else {
            echo "Successful ".$cplugin->value;
        }
    }
}
/**
 * Front site css create.
 *
 */
function educard_create_css() {
    $fs = get_file_storage();
    $theme = theme_config::load('educard');
    // Prepare file record object.
    $fileinfo = [
        'contextid' => 1,
        'component' => 'theme_educard',
        'filearea' => 'css',
        'itemid' => 0,
        'filepath' => '/',
        'filename' => 'educard-fonts.css', ];

    // Create file containing text.
    if (!empty($theme->settings->fontimport) && !empty($theme->settings->fontfamily)) {
        $text = $theme->settings->fontimport;
        $text .= "body { ". PHP_EOL;
        $text .= $theme->settings->fontfamily. PHP_EOL;
        $text .= "max-width: 1920px;". PHP_EOL;
        $text .= "margin: auto;". PHP_EOL;
        $text .= " } ";
        $file = $fs->create_file_from_string($fileinfo, $text);
    } else {
        $text = "@import url('https://rsms.me/inter/inter.css');". PHP_EOL;
        $text .= "body { ". PHP_EOL;
        $text .= "font-family: 'inter', sans-serif;". PHP_EOL;
        $text .= "max-width: 1920px;". PHP_EOL;
        $text .= "margin: auto;". PHP_EOL;
        $text .= " } ";
        $file = $fs->create_file_from_string($fileinfo, $text);
    }
    return $file;
}
/**
 * Front site css read.
 *
 */
function educard_read_css() {
    global $CFG;
    $fs = get_file_storage();
    // Prepare file record object.
    $fileinfo = [
        'component' => 'theme_educard',
        'filearea' => 'css',
        'itemid' => 0,
        'contextid' => 1,
        'filepath' => '/',
        'filename' => 'educard-fonts.css', ];

    // Get file.
    $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                        $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
    $context = $fileinfo['contextid'];
    $component = $fileinfo['component'];
    $filearea = $fileinfo['filearea'];
    $itemid = $fileinfo['itemid'];
    $filename = $fileinfo['filename'];
    $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$context/$component/$filearea/$itemid/$filename");
    $url = preg_replace('|^https?://|i', '//', $url->out(false));
    // Read contents.
    if ($file) {
        return $url;
    } else {
        // File doesn't exist - do something.
        return "";
    }
}
/**
 * Front site css delete.
 *
 */
function educard_delete_css() {
    $fs = get_file_storage();

    // Prepare file record object.
    $fileinfo = [
        'component' => 'theme_educard',
        'filearea' => 'css',
        'itemid' => 0,
        'contextid' => 1,
        'filepath' => '/',
        'filename' => 'educard-fonts.css', ];

    // Get file.
    $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
            $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

    // Delete it if it exists.
    if ($file) {
        $file->delete();
        return true;
    }
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_educard_get_extra_scss($theme) {
    $content = '';
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');

    // Sets the background image, and its settings.
    if (!empty($imageurl)) {
        $content .= '@media (min-width: 768px) {';
        $content .= 'body { ';
        $content .= "background-image: url('$imageurl'); background-size: cover;";
        $content .= ' } }';
    }

    // Sets the login background image.
    $loginbackgroundimageurl = $theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    if (!empty($loginbackgroundimageurl)) {
        $content .= 'body.pagelayout-login #page { ';
        $content .= "background-image: url('$loginbackgroundimageurl'); background-size: cover;";
        $content .= ' }';
    }

    // Always return the background image with the scss when we have it.
    return !empty($theme->settings->scss) ? $theme->settings->scss . ' ' . $content : $content;
}
/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_educard_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo' ||
        $filearea === 'backgroundimage' || $filearea === 'loginbackgroundimage' || $filearea === 'css' ||
        substr($filearea, 0, 11) === 'sliderimage' ||
        substr($filearea, 0, 5) === 'block' || substr($filearea, 0, 3) === 'img')) {
        $theme = theme_config::load('educard');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_educard_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/educard/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/educard/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_educard', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/educard/scss/preset/default.scss');
    }
    return $scss;
}
/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_educard_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/style/moodle.css');
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_educard_get_pre_scss($theme) {
    global $CFG;
    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['primary'],
        'backcolor' => ['white'],
        'navbarlogobackcolor' => ['navbarlogobackcolor'],
        'navbarcolor' => ['navbarcolor'],
        'navbarlinkcolor' => ['navbarlinkcolor'],
        'navbarlinkhovercolor' => ['navbarlinkhovercolor'],
        'navbarcolordark' => ['navbarcolordark'],
        'navbarlinkcolordark' => ['navbarlinkcolordark'],
        'navbarlinkhovercolordark' => ['navbarlinkhovercolordark'],
        'footerbackcolor' => ['hyfooterbackcolor'],
        'sitecolor' => ['hythemecolor'],
        'sitecolor2' => ['hythemecolor2'],
     ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }
    // Site color.
    $context = $theme->settings->sitecolor;
    if (empty($context)) {
        $sitecolor = theme_educard_frontpagecolor();
        $color = $sitecolor['sitecolor'];
        $scss .= '$' . 'hythemecolor' . ': ' . $color . ";\n";
        $color = $sitecolor['navbarcolor'];
        $scss .= '$' . 'navbarcolor' . ': ' . $color . ";\n";
    }
    // Blocks background color image scss class.
    $context = theme_educard_block_bg_img_color();
    if (!empty($context)) {
        $scss .= $context;
    }
    // Blocks gradient bg image.
    $context = theme_educard_frontpage_bg_img();
    if (!empty($context)) {
        $scss .= $context;
    }
    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}
require('lib/frontpage_settings.php');
require('lib/slideshow.php');
require('lib/frontpage_block.php');
require('lib/frontpage_banner.php');
require('lib/frontpage_pages.php');
