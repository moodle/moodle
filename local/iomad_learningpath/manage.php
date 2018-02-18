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

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Security
$context = context_system::instance();
require_login();
iomad::require_capability('local/iomad_learningpath:manage', $context);

// Page boilerplate stuff.
$url = new moodle_url('/local/iomad_learningpath/manage.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('managetitle', 'local_iomad_learningpath'));
$PAGE->set_heading(get_string('learningpathmanage', 'local_iomad_learningpath'));
echo $OUTPUT->header();


echo $OUTPUT->footer();
