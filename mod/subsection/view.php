<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_subsection.
 *
 * @package     mod_subsection
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_subsection\manager;
use core_courseformat\formatactions;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course module id.
$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('subsection', $id, 0, false, MUST_EXIST);
$manager = manager::create_from_coursemodule($cm);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$moduleinstance = $manager->get_instance();

require_login($course, true, $cm);

$modulecontext = $manager->get_context();
$manager->set_module_viewed($course);

$modinfo = get_fast_modinfo($course);

$delegatesection = $modinfo->get_section_info_by_component(manager::PLUGINNAME, $moduleinstance->id);
if (!$delegatesection) {
    // Some restorations can produce a situation where the section is not found.
    // In that case, we create a new one.
    formatactions::section($course)->create_delegated(
        manager::PLUGINNAME,
        $id,
        (object) [
            'name' => $moduleinstance->name,
        ]
    );
}
redirect(new moodle_url('/course/section.php', ['id' => $delegatesection->id]));
