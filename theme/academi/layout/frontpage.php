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
 * A drawer based layout for the boost theme.
 *
 * @package   theme_academi
 * @copyright 2021 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) .'/includes/layoutdata.php');
require_once(dirname(__FILE__) .'/includes/homeslider.php');

$PAGE->requires->css(new moodle_url('/theme/academi/style/slick.css'));
$PAGE->requires->js_call_amd('theme_academi/frontpage', 'init');
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
// Jumbotron class.
$jumbotronclass = (!empty(theme_academi_get_setting('jumbotronstatus'))) ? 'jumbotron-element' : '';
// Slide show contnet added in the templatecontext.
$templatecontext += $sliderconfig;
$templatecontext += [
    'bodyattributes' => $bodyattributes,
    'jumbotronclass' => $jumbotronclass,
];

echo $OUTPUT->render_from_template('theme_academi/frontpage', $templatecontext);
