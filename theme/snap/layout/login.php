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
 * Layout - default.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/header.php');
global $SESSION;
?>
<!-- moodle js hooks -->
<?php
$fullscreenclasses = '';
// Don't create the background login image in the sign up page.
if (!($PAGE->pagetype === 'login-signup')) {
    // Check if there is a background image configured for the login.
    if (empty(get_config('theme_snap', 'loginbgimg'))) {
        if (get_config('theme_snap', 'loginpagetemplate') == "stylish") {
            // If the login template is Stylish, then add a new div called page-stylish-content
            // and a new class called page-stylish-login.
            echo '<div id="page">';
            echo '<div id="page-stylish-content" class="page-stylish-login">';
        } else {
            echo '<div id="page">';
        }
    } else {
        if (get_config('theme_snap', 'loginpagetemplate') == "stylish") {
            // If the login template is Stylish and there are images for the background
            // add a new class called page-stylish-background and a new class called page-stylish-login.
            $imgsrc = $OUTPUT->login_bg_slides();
            $imageinitialurl = '';
            $imagid = 1;
            foreach ($imgsrc as $image) {
                $imageinitialurl = ($imagid == count($imgsrc)) ?
                    $imageinitialurl.'url('.$image.')' :
                    $imageinitialurl.'url('.$image.'),';
                $imagid ++;
            }
            echo '<div id="page" class="page-stylish-background" style="background-image: '.$imageinitialurl.';">';
            echo '<div id="snap-login-carousel" class="carousel slide page-stylish-login">';
        } else {
            $imgsrc = $OUTPUT->login_bg_slides();
            $imageinitialurl = '';
            $imagid = 1;
            foreach ($imgsrc as $image) {
                $imageinitialurl = ($imagid == count($imgsrc)) ?
                    $imageinitialurl.'url('.$image.')' :
                    $imageinitialurl.'url('.$image.'),';
                $imagid ++;
            }
            echo '<div id="page" style="background-image: '.$imageinitialurl.';">';
            echo '<div id="snap-login-carousel" class="carousel slide">';
        }
    }
}
?>
<div id="page-content">
<!--
////////////////////////// MAIN  ///////////////////////////////
-->
<div id="moodle-page" class="clearfix">
<div id="page-header" class="clearfix">
</div>

<section id="region-main">
<?php
if ($PAGE->title === get_string('restoredaccount')) {
    echo html_writer::start_div('loginerror-restoredaccount');
    echo $OUTPUT->main_content();
    echo html_writer::end_div();
} else {
    echo $OUTPUT->main_content();
}
?>
</section>
</div>
</div>
<?php
if (!empty(get_config('theme_snap', 'loginbgimg'))) {
    $images = $OUTPUT->login_bg_slides();
    $PAGE->requires->js_call_amd('theme_snap/carousel_login', 'init', ['images' => $images]);
}
// Set wantsutl to null to prevent redirect to draftfile after login.
if (isset($SESSION->wantsurl)) {
    if (preg_match('/draftfile/', $SESSION->wantsurl)) {
        $SESSION->wantsurl = null;
    }
}
?>
</div>
</div>
<!-- close moodle js hooks -->

<?php require(__DIR__.'/footer.php');
