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
 * LTI preview and selection renderer library.
 *
 * @package    tinymce_kalturamedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */
define('TINMCE_KALTURAMEDIA_OBJECT_TAG_ID', 'objecttagcontainer');
define('TINMCE_KALTURAMEDIA_PREVIEW_IFRAME_TAG_ID', 'video_preview_frame');
define('TINMCE_KALTURAMEDIA_OBJECT_TAG_HEIGHT', '500');

/**
 * Returns HTML markup for a form used to preview and insert the video markup into the page.
 * @return string HTML markup.
 */
function tinymce_kalturamedia_preview_embed_form() {
    // Create hidden elements.
    $hiddenelements = html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'entry_id', 'value' => ''));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'source', 'value' => ''));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'kafuri', 'value' => local_kaltura_get_config()->kaf_uri));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'video_title', 'value' => ''));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'uiconf_id', 'value' => ''));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'widescreen', 'value' => ''));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'height', 'value' => ''));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'width', 'value' => ''));
    $hiddenelements .= html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'lti_launch_context_id', 'value' => ''));

    // Create LTI launch and preview container divs
    $ltilaunchcontainer = html_writer::tag('div', '', array('id' => TINMCE_KALTURAMEDIA_OBJECT_TAG_ID));
    $previewcontainer = html_writer::tag('div', '', array('id' => TINMCE_KALTURAMEDIA_PREVIEW_IFRAME_TAG_ID));

    // Create cancel button.
    $param = array(
        'type' => 'submit',
        'id' => 'cancel',
        'name' => 'cancel',
        // Using the language string from the local plug-in.  TODO: figure out how to access the TinyMCE language strings.
        'value' => get_string('cancelbtn', 'local_kaltura'),
        'onclick' => 'ed.newWindow.close();'
    );
    $cancelbutton = html_writer::empty_tag('input', $param);
    $cancelbuttondiv = html_writer::tag('div', $cancelbutton, array('style' => 'float: right'));

    $buttonscontainer = html_writer::tag('div', $cancelbuttondiv, array('class' => 'mceActionPanel'));

    // This element is used so that the ltiservice.js can simulate a 'click' event.  This tells the plug-in that the user has choosen a video to embed on the page
    // and it will enable the insert button.
    $simulateclickdiv = html_writer::tag('input', '', array('id' => 'closeltipanel', 'type' => 'hidden', 'value' => ''));

    $content = $simulateclickdiv.$ltilaunchcontainer.$previewcontainer.$hiddenelements.$buttonscontainer;
    return html_writer::tag('form', $content, array('onsubmit' => 'insertMedia();return false', 'action' => '#'));
}
