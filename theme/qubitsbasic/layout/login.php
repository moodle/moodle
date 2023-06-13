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
 * login.php
 *
 * @package    theme_klass
 * @copyright  2013 Moodle, moodle.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$bodyattributes = $OUTPUT->body_attributes();
// require_once($CFG->dirroot . '/theme/klass/layout/includes/themedata.php');
// Theme - login page background carousel.
// $templatecontext += theme_klass_login_bgcarousel();

$logourl = new moodle_url("/theme/qubitsbasic/pix/qubits-full-logo.svg");

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'pagetypelogin' => true,
    'loginimage' => $logourl,
    'qubits_logo'=> $OUTPUT->image_url('qubits-logo', 'theme')
];


echo $OUTPUT->render_from_template('theme_qubitsbasic/login', $templatecontext);

