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
 * Local IntelliData running adhoc task report.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_intellidata\output\tables\adhoctasks_table;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\TasksHelper;

require('../../../config.php');

$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_TEXT);

require_login();

if (!empty($action)) {
    require_sesskey();
}

$context = context_system::instance();
require_capability('local/intellidata:viewadhoctasks', $context);

$pageurl = new \moodle_url('/local/intellidata/logs/adhoctasks.php');
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout(SettingsHelper::get_page_layout());

if ($id && $action == 'delete' &&
    has_capability('local/intellidata:deleteadhoctasks', $context)) {
    $msg = TasksHelper::delete_adhoc_task($id) ? 'taskdeleted' : 'tasknotdeleted';

    redirect($pageurl, get_string($msg, ParamsHelper::PLUGIN));
}

$title = get_string('exportadhoctasks', ParamsHelper::PLUGIN);

$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$table = new adhoctasks_table('adhoctasks_table');

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$table->out(20, true);

echo $OUTPUT->footer();
