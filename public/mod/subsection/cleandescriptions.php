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
 * Remove or migrate subsection descriptions
 *
 * @copyright 2026 Sara Arjona <sara@moodle.com>
 * @package   mod_subsection
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_admin();

$action = required_param('action', PARAM_ALPHA);
$count = optional_param('count', 0, PARAM_INT);
$return = new moodle_url('/admin/settings.php', ['section' => 'mod_subsection_settings']);

$PAGE->set_url('/mod/subsection/cleandescriptions.php');
$PAGE->set_context(context_system::instance());

require_sesskey();
if ($action === 'delete') {
    // Remove all existing subsection descriptions.
    $DB->set_field('course_sections', 'summary', '', ['component' => 'mod_subsection']);
    redirect(
        $return,
        get_string('descriptionsdeletedsuccess', 'mod_subsection', $count),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
} else if ($action === 'migrate') {
    // Schedule the ad-hoc task to migrate subsection descriptions.
    \core\task\manager::queue_adhoc_task(new \mod_subsection\task\migrate_subsection_descriptions_task(), true);
    redirect($return);
} else {
    throw new moodle_exception('invalidaction', 'mod_subsection');
}
