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
 * Serves the result of the AJAX search.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(__dir__) . '/../config.php');

require_login();
// Might be overkill but would probably stop DOS attack from lots of DB reads.
require_sesskey();

$term = required_param('term', PARAM_TEXT);

$PAGE->set_context(context_system::instance());
$courserenderer = $PAGE->get_renderer('core', 'course');

echo json_encode($courserenderer->inspector_ajax($term));
