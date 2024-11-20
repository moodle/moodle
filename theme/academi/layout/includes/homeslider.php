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
 * Slideshow layout
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot."/theme/academi/classes/helper.php");

/**
 * General config setting for the home page slider.
 *
 * @return $general general data settings.
 */
function general() {
    $general = [];
    $general['status'] = theme_academi_get_setting('toggleslideshow');
    $interval = intval(theme_academi_get_setting('slideinterval'));
    $autoslideshow = theme_academi_get_setting('autoslideshow');
    $general['interval'] = (!empty($interval)) ? $interval : 3000;
    $general['overlay'] = theme_academi_get_setting('slideOverlay');
    if ($autoslideshow == 1) {
        $general["autoplay"] = 'true';
    } else {
        $general["autoplay"] = 'false';
    }
    return $general;
}

/**
 * Home page slider data.
 *
 * @return array $data data for home pageslider.
 */
function homeslider() {
    global $PAGE;
    $data = [];
    $data['numofslide'] = theme_academi_get_setting('numberofslides');
    $helperobj = new theme_academi\helper();
    (int) $slider = 0;
    for ($s = 1; $s <= $data['numofslide']; $s++) {
        $slide = [];
        $slide['slidestatus'] = theme_academi_get_setting('slide' . $s .'status');
        $slide['slideimg'] = $helperobj->render_slideimg($s, 'slide' . $s . 'image');
        $slide['slidecontentstatus'] = theme_academi_get_setting('slide' . $s .'contentstatus');
        $slide['caption'] = theme_academi_lang(theme_academi_get_setting('slide' . $s . 'caption'));
        $slide['desc'] = theme_academi_lang(theme_academi_get_setting('slide' . $s . 'desc', 'format_html'));
        $slide['btntxt'] = theme_academi_lang(theme_academi_get_setting('slide' . $s . 'btntext'));
        $slide['btnlink'] = theme_academi_get_setting('slide' . $s . 'btnurl');
        $btntarget = theme_academi_lang(theme_academi_get_setting('slide' . $s . 'btntarget'));
        $slide['btntarget'] = ($btntarget == 1) ? '_blank' : '_self';
        $contwidth = theme_academi_get_setting('slide' . $s . 'contFullwidth');

        if ((!empty($slide['slidestatus'])) && (!empty($slide['slideimg']))) {
            $slider = $slider + 1;
        }

        if ($contwidth == "auto") {
            $contwidth = "auto";
        } else {
            $contwidth = intval($contwidth);
            if ($contwidth > '100' ) {
                $contwidth = '100%';
            } else if ($contwidth <= 0) {
                $contwidth = "auto";
            } else {
                $contwidth = $contwidth.'%';
            }
        }
        $slide['contentwidth'] = $contwidth;
        $slide['contentAnimation'] = "ScrollRight";
        $slide['contentAclass'] = "animated ". $slide['contentAnimation'];
        $postition = theme_academi_get_setting('slide' . $s . 'contentPosition');
        $slide['contentpostion'] = $postition;
        $slide['contentClass'] = (!empty($postition)) ? 'content-'.$postition : 'content-centerRight';
        if ($slide['slideimg']) {
            $data['slides'][] = $slide;
        }
    }
    $status = theme_academi_get_setting('toggleslideshow');
    $data['sliderblockstatus'] = ($slider == 0) ? false : $status;
    if (!$data['sliderblockstatus']) {
        $data['isblockempty'] = is_siteadmin() || $PAGE->user_is_editing() ? true : false;
    }
    return $data;
}

$sliderconfig = [];
$slidergeneral = general();
$sliderconfig += $slidergeneral;
$sliderconfig += homeslider();
$PAGE->requires->js_call_amd('theme_academi/homeslider', 'init', ['selector' => '#homepage-carousel', 'options' => $slidergeneral]);
$PAGE->requires->css("/theme/academi/style/animate.css");
