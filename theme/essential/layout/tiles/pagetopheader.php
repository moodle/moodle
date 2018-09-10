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
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

echo '<div id="page-top-header" class="clearfix">';
echo '<div id="page-navbar" class="clearfix row-fluid">';

echo '<div class="breadcrumb-nav span9">';
echo $OUTPUT->navbar();
echo '</div>';

echo '<nav class="breadcrumb-button span3">';
echo $OUTPUT->page_heading_button();
echo '</nav>';
echo '</div>';
echo $OUTPUT->page_top_header();
echo '</div>';
echo $OUTPUT->essential_blocks('header', 'row-fluid', 'aside', 'headerblocksperrow');