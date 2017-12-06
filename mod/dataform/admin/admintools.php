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
 * @package mod_dataform
 * @category admin
 * @copyright  2013 Itamar Tzadok
 */

require_once('../../../config.php');
require_once("$CFG->libdir/adminlib.php");

$tool = required_param('tool', PARAM_ALPHA);

admin_externalpage_setup("moddataform_$tool");

$toolclass = "mod_dataform\admin\\$tool";

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'dataform'));
echo html_writer::tag('h3', $toolclass::get_visible_name());

echo $toolclass::run();

echo $OUTPUT->footer();
