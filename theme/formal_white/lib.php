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
 * Moodle's formal_white theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_formal_white
 * @copyright 2013 Mediatouch 2000, mediatouch.it
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function formal_white_user_settings($css, $theme) {

    // Set the font reference size
    if (empty($theme->settings->fontsizereference)) {
        $fontsizereference = '13'; // default
    } else {
        $fontsizereference = $theme->settings->fontsizereference;
    }
    $css = formal_white_set_fontsizereference($css, $fontsizereference);

    // Set the frame margin
    if (!isset($theme->settings->framemargin)) {
        $framemargin = 15; // default
    } else {
        $framemargin = $theme->settings->framemargin;
    }
    $css = formal_white_set_framemargin($css, $framemargin);

    // Set the images according to color trend
    if (!isset($theme->settings->trendcolor)) {
        $trendcolor = 'mink'; // default
    } else {
        $trendcolor = $theme->settings->trendcolor;
    }
    $css = formal_white_set_trendcolor($css, $trendcolor);

    // Set the page header background color
    if (empty($theme->settings->headerbgc)) {
        $headerbgc = '#E3DFD4'; // default
    } else {
        $headerbgc = $theme->settings->headerbgc;
    }
    $css = formal_white_set_headerbgc($css, $headerbgc);

    // Set the block content background color
    if (empty($theme->settings->blockcontentbgc)) {
        $blockcontentbgc = '#F6F6F6'; // default
    } else {
        $blockcontentbgc = $theme->settings->blockcontentbgc;
    }
    $css = formal_white_set_blockcontentbgc($css, $blockcontentbgc);

    // Set the left block column background color
    if (empty($theme->settings->lblockcolumnbgc)) {
        $lblockcolumnbgc = '#E3DFD4'; // default
    } else {
        $lblockcolumnbgc = $theme->settings->lblockcolumnbgc;
    }
    $css = formal_white_set_lblockcolumnbgc($css, $lblockcolumnbgc);

    // Set the right block column background color
    if (empty($theme->settings->rblockcolumnbgc)) {
        $rblockcolumnbgc = $lblockcolumnbgc; // default
    } else {
        $rblockcolumnbgc = $theme->settings->rblockcolumnbgc;
    }
    $css = formal_white_set_rblockcolumnbgc($css, $rblockcolumnbgc);

    // set the width of the two blocks columns
    if (!empty($theme->settings->blockcolumnwidth)) {
        $blockcolumnwidth = $theme->settings->blockcolumnwidth;
    } else {
        $blockcolumnwidth = '200'; // default
    }
    $css = formal_white_set_blockcolumnwidth($css, $blockcolumnwidth);

    // set blocks margin
    if (!empty($theme->settings->blockpadding)) {
        $blockpadding = $theme->settings->blockpadding;
    } else {
        $blockpadding = '8'; // default
    }
    $css = formal_white_set_blockpadding($css, $blockcolumnwidth, $blockpadding);

    // set the customcss
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = formal_white_set_customcss($css, $customcss);

    return $css;
}

/**
 * Sets the link color variable in CSS
 *
 */
function formal_white_set_fontsizereference($css, $fontsizereference) {
    $tag = '[[setting:fontsizereference]]';
    $css = str_replace($tag, $fontsizereference.'px', $css);
    return $css;
}

function formal_white_set_framemargin($css, $framemargin) {
    $tag = '[[setting:framemargin]]';
    $css = str_replace($tag, $framemargin.'px', $css);

    // Set .headermenu margin
    $calculated = $framemargin + 22; // 17px is the width of the frame; 5px to avoid to have all stuck
    $tag = '[[calculated:headermenumargin]]';
    $css = str_replace($tag, $calculated.'px', $css);

    return $css;
}

function formal_white_set_trendcolor($css, $trendcolor) {
    // __setting_trendcolor__ is part of URLS so it is already between double square bracket.
    // I can not enclose it between double square bracket once again otherwise images path parser get confused.
    $tag = urlencode('__setting_trendcolor__'); // urlencode is useless but it is correct to put it here
    $css = str_replace($tag, $trendcolor, $css);

    // of the basis of the general choosed trend, I need some colour definition.
    switch ($trendcolor) {
        case 'blueberry':
            // .courses .coursebox
            $tag = '[[setting:hovercolor]]';
            $css = str_replace($tag, '#CEE5FD', $css);

            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#DAF1FF', $css);

            // block header background
            $tag = '[[setting:blockheaderbackground]]';
            $css = str_replace($tag, '#CDE2F3', $css);
            break;
        case 'lemon':
            // .courses .coursebox
            $tag = '[[setting:hovercolor]]';
            $css = str_replace($tag, '#FFEA68', $css);

            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#FFEB9A', $css);

            // block header background
            $tag = '[[setting:blockheaderbackground]]';
            $css = str_replace($tag, '#F7DA41', $css);
            break;
        case 'lime':
            // .courses .coursebox
            $tag = '[[setting:hovercolor]]';
            $css = str_replace($tag, '#EBEDB1', $css);

            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#F0F5BB', $css);

            // block header background
            $tag = '[[setting:blockheaderbackground]]';
            $css = str_replace($tag, '#E2E499', $css);
            break;
        case 'mink':
            // .courses .coursebox
            $tag = '[[setting:hovercolor]]';
            $css = str_replace($tag, '#E3DFD4', $css);

            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#EFEFEF', $css);

            // block header background
            $tag = '[[setting:blockheaderbackground]]';
            $css = str_replace($tag, '#E3DFD4', $css);
            break;
        case 'orange':
            // .courses .coursebox
            $tag = '[[setting:hovercolor]]';
            $css = str_replace($tag, '#FFCF88', $css);

            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#FFD46C', $css);

            // block header background
            $tag = '[[setting:blockheaderbackground]]';
            $css = str_replace($tag, '#FDC06D', $css);
            break;
        case 'peach':
            // .courses .coursebox
            $tag = '[[setting:hovercolor]]';
            $css = str_replace($tag, '#FFCEB8', $css);

            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#FCD3BC', $css);

            // block header background
            $tag = '[[setting:blockheaderbackground]]';
            $css = str_replace($tag, '#F7C099', $css);
            break;
        case 'silver':
            // .courses .coursebox
            $tag = '[[setting:hovercolor]]';
            $css = str_replace($tag, '#E0DFDD', $css);

            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#EFF0F2', $css);

            // block header background
            $tag = '[[setting:blockheaderbackground]]';
            $css = str_replace($tag, '#E0DFDD', $css);
            break;
        default:
            debugging('It seems a colour has been added to the formal_white trend colours folder but was not fully managed. The code must be updated by a developer.');
    }
    return $css;
}

function formal_white_set_headerbgc($css, $headerbgc) {
    $tag = '[[setting:headerbgc]]';
    $css = str_replace($tag, $headerbgc, $css);
    return $css;
}

function formal_white_set_blockcontentbgc($css, $blockcontentbgc) {
    $tag = '[[setting:blockcontentbgc]]';
    $css = str_replace($tag, $blockcontentbgc, $css);
    return $css;
}

function formal_white_set_lblockcolumnbgc($css, $lblockcolumnbgc) {
    $tag = '[[setting:lblockcolumnbgc]]';
    $css = str_replace($tag, $lblockcolumnbgc, $css);
    return $css;
}

function formal_white_set_rblockcolumnbgc($css, $rblockcolumnbgc) {
    $tag = '[[setting:rblockcolumnbgc]]';
    $css = str_replace($tag, $rblockcolumnbgc, $css);
    return $css;
}

function formal_white_set_blockcolumnwidth($css, $blockcolumnwidth) {
    $tag = '[[setting:blockcolumnwidth]]';
    $css = str_replace($tag, $blockcolumnwidth.'px', $css);

    $calculated = -2*$blockcolumnwidth;
    $tag = '[[calculated:minusdoubleblockcolumnwidth]]';
    $css = str_replace($tag, $calculated.'px', $css);

    $calculated = 2*$blockcolumnwidth;
    $tag = '[[calculated:doubleblockcolumnwidth]]';
    $css = str_replace($tag, $calculated.'px', $css);

    // set the min-width of the page to provide: content region min-width = block region width
    // I do not care $framemargin because the min-width applies to #frametop that is free from $framemargin
    // I need to add twice the width of the frame because it is inside #frametop
    // (this code here because it HAS TO come later than $blockcolumnwidth definition)
    $calculated = 3*$blockcolumnwidth + 34; // 34 = 2*17 (17px is the width of the frame)
    $tag = '[[calculated:minwidth]]';
    $css = str_replace($tag, $calculated.'px', $css);

    return $css;
}

function formal_white_set_blockpadding($css, $blockcolumnwidth, $blockpadding) {
    $tag = '[[setting:blockpadding]]';
    $css = str_replace($tag, $blockpadding.'px', $css);

    // I need to know the field width in pixel because width:100%; and width:auto; don't work as expected
    // once $blockcolumnwidth and $blockpadding are known, $lb_fieldswidth can be applied
    // the process has not been optimized at all but it is executed only once
    $lb_fieldswidth = $blockcolumnwidth;

    // #page-content .region-content {padding:[[setting:blockpadding]] [[setting:blockpadding]] 0 [[setting:blockpadding]];} in pagelayout.css
    $lb_fieldswidth -= 2*$blockpadding;

    // .block {border:[[static:lb_blockborderwidth]] solid #C6BDA8; [...] }
    $lb_fieldsborderwidth = 1;
    $tag = '[[static:lb_blockborderwidth]]'; // It is static, it is not a setting. I just hardcoded its definition here.
    $css = str_replace($tag, $lb_fieldsborderwidth.'px', $css);
    $lb_fieldswidth -= 2*$lb_fieldsborderwidth;

    // .block_login .content {padding:[[static:lb_contentpadding]];}
    $lb_fieldspadding = 4;
    $tag = '[[static:lb_contentpadding]]'; // It is static, it is not a setting. I just hardcoded its definition here.
    $css = str_replace($tag, $lb_fieldspadding.'px', $css);
    $lb_fieldswidth -= 2*$lb_fieldspadding;

    // .block_login #login_username, .block_login #login_password {margin:4px 0 4px [[static:lb_fieldsmargin]];}
    $lb_fieldsmargin = 14;
    $tag = '[[static:lb_fieldsmargin]]'; // It is static, it is not a setting. I just hardcoded its definition here.
    $css = str_replace($tag, $lb_fieldsmargin.'px', $css);
    $lb_fieldswidth -= $lb_fieldsmargin; // without 2* because it is only left margin

    // fields default factory border: 3px
    $lb_fieldswidth -= 2*3;

    // leave few pixel on the right reducing once again the field length
    $lb_fieldswidth -= 12;

    $tag = '[[static:lb_fieldswidth]]';
    $css = str_replace($tag, $lb_fieldswidth.'px', $css);
    return $css;
}

function formal_white_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $css = str_replace($tag, $customcss, $css);
    return $css;
}

/**
 * Serves formal white files.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param type $args
 * @param array $forcedownload
 * @param array $options
 * @return bool
 */
function theme_formal_white_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'customlogourl') {
        $theme = theme_config::load('formal_white');
        return $theme->setting_file_serve('customlogourl', $args, $forcedownload, $options);
    } else if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'frontpagelogourl') {
        $theme = theme_config::load('formal_white');
        return $theme->setting_file_serve('frontpagelogourl', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}