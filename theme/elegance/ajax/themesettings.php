<?php
// This file is part of the custom Moodle elegance theme
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
 * @package    theme
 * @subpackage elegance
 * @copyright  2015 Bas Brands, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('NO_DEBUG_DISPLAY', true);

require_once('../../../config.php');

$setting = required_param('setting', PARAM_TEXT);
$sesskey = required_param('sesskey', PARAM_RAW);

$PAGE->set_url('/theme/elegance/ajax/themesettings.php');

$context = context_system::instance();

$PAGE->set_context($context);

if (!confirm_sesskey()) {
    $error = array('error' => get_string('invalidsesskey', 'error'));
    die(json_encode($error));
}

$theme = theme_config::load('elegance');
$settings = $theme->settings;

if ($setting == 'loginbackgrounds') {
    $loginbgnumber = $settings->loginbgnumber;
    if ($loginbgnumber) {
        $result = new stdClass();
        $result->result = 'success';
        $result->loginimages = array();
        foreach (range(1, $loginbgnumber) as $i) {
            $loginimage = 'loginimage' . $i;
            $result->loginimages[] = $theme->setting_file_url($loginimage, $loginimage);
        }
        echo $OUTPUT->header();
        echo json_encode($result);
        echo $OUTPUT->footer();
        die();
    } else {
        $result = new stdClass();
        $result->result = 'failed';
        echo $OUTPUT->header();
        echo json_encode($result);
        echo $OUTPUT->footer();
        die();
    }
}

if ($setting == 'slidespeed') {
    $slidespeed = $settings->slidespeed;
    $result = new stdClass();
    
    if ($slidespeed > 300) {
        $result = new stdClass();
        $result->result = 'success';
        $result->slidespeed = intval($slidespeed);
    } else {
        $result->result = 'failed';
        $result->slidespeed = 600;
    }
    echo $OUTPUT->header();
    echo json_encode($result);
    echo $OUTPUT->footer();
    die();
}

$result = new stdClass();
$result->result = 'no allowed setting found';
echo $OUTPUT->header();
echo json_encode($result);
echo $OUTPUT->footer();
die();

