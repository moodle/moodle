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
 * Handles content item return.
 *
 * @package    atto_panoptoltibutton
 * @copyright  2020 Panopto
 * @author     Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../../config.php');
require_once(dirname(__FILE__) . '/lib/panopto_lti_utility.php');


$courseid = required_param('course', PARAM_INT);
$callback = required_param('callback', PARAM_ALPHANUMEXT);
$contentitemsraw = required_param('content_items', PARAM_RAW_TRIMMED);

require_login($courseid);

$context = context_course::instance($courseid);

// Students will access this tool for the student submission workflow. Assume student can submit an assignment?
if (!\panopto_lti_utility::panoptoltibutton_is_active_user_enrolled($context)) {
    require_capability('moodle/course:manageactivities', $context);
    require_capability('mod/lti:addcoursetool', $context);
}

$contentitems = json_decode($contentitemsraw);

$errors = [];

// Affirm that the content item is a JSON object.
if (!is_object($contentitems) && !is_array($contentitems)) {
    $errors[] = 'invalidjson';
}
?>

<script type="text/javascript">
    <?php if (count($errors) > 0): ?>
        parent.document.CALLBACKS.handleError(<?php echo json_encode($errors); ?>);
    <?php else: ?>
        parent.document.CALLBACKS.<?php echo $callback ?>(<?php echo json_encode($contentitems) ?>);
    <?php endif; ?>
</script>