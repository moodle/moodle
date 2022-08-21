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
 * Prints an instance of mod_plugnmeet.
 *
 * @package     mod_plugnmeet
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$p = optional_param('p', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('plugnmeet', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('plugnmeet', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('plugnmeet', array('id' => $p), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('plugnmeet', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

$context = context_module::instance($cm->id);
require_login($course, true, $cm);
require_capability('mod/plugnmeet:view', $context);

$PAGE->set_url('/mod/plugnmeet/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$isadmin = has_capability('moodle/course:update', $context) ? 1 : 0;
$canedit = has_capability('mod/plugnmeet:edit', $context) ? 1 : 0;

if (!time_restriction_check_pass($moduleinstance) && !$isadmin) {
    echo $OUTPUT->header();
    echo get_string('notavailable');
    echo $OUTPUT->footer();
    exit();
}

$event = \mod_plugnmeet\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('plugnmeet', $moduleinstance);
$event->trigger();

echo $OUTPUT->header();

?>
    <div class="container mt-5 mb-5">
        <?php if (!empty($moduleinstance->intro)) : ?>
            <div class="row mb-3"><?php echo $moduleinstance->intro ?></div>
        <?php endif; ?>
        <div class="row">
            <?php require(__DIR__ . "/views/join_part.php") ?>
        </div>
        <div class="row">
            <?php require(__DIR__ . "/views/recordings_part.php") ?>
        </div>
    </div>

<?php
echo $OUTPUT->footer();
