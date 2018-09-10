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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2017 Gareth J Barnard
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$coursetitleposition = \theme_essential\toolbox::get_setting('coursetitleposition');
if (empty($coursetitleposition)) {
    $coursetitleposition = 'within';
}
if ($coursetitleposition == 'above') {
    echo $OUTPUT->course_title(false);
}
if ($pagebottomregion) {
    echo '<div id="content" class="span12">';
} else if ($hasboringlayout) {
    echo '<div id="content" class="span9 pull-right">';
} else {
    echo '<div id="content" class="span9">';
}
if (\theme_essential\toolbox::get_setting('pagetopblocks')) {
    echo $OUTPUT->essential_blocks('page-top', 'row-fluid', 'aside', 'pagetopblocksperrow');
}
echo '<section id="region-main">';
if ($coursetitleposition == 'within') {
    echo $OUTPUT->course_title();
}
echo $OUTPUT->course_content_header();
echo $OUTPUT->main_content();
echo $OUTPUT->activity_navigation();
if (empty($PAGE->layout_options['nocoursefooter'])) {
    echo $OUTPUT->course_content_footer();
}
echo '</section>';
echo '</div>';
if (!$pagebottomregion) {
    if ($hasboringlayout) {
        echo $OUTPUT->essential_blocks('side-pre', 'span3 desktop-first-column');
    } else {
        echo $OUTPUT->essential_blocks('side-pre', 'span3');
    }
}
