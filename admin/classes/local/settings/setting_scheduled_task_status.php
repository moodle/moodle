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
 * Admin setting to show current scheduled task's status.
 *
 * @package core
 * @copyright 2021 Universitat Rovira i Virgili
 * @author Jordi Pujol-Ahulló <jpahullo@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\local\settings;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/moodlelib.php');

use admin_setting_description;
use core\task\manager;
use core\task\scheduled_task;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;

/**
 * This admin setting tells whether a given scheduled task is enabled, providing a link to its configuration page.
 *
 * The goal of this setting is to help contextualizing the configuration settings with related scheduled task status,
 * providing the big picture of that part of the system.
 *
 * @package core
 * @copyright 2021 Universitat Rovira i Virgili
 * @author Jordi Pujol-Ahulló <jpahullo@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_scheduled_task_status extends admin_setting_description {
    /**
     * @var string fully qualified class name of a scheduled task.
     */
    private $classname;
    /**
     * @var string additional text to append to the description.
     */
    private $extradescription;

    /**
     * setting_scheduled_task_status constructor.
     * @param string $name unique setting name.
     * @param string $scheduledtaskclassname full classpath class name of the scheduled task.
     * @param string $extradescription extra detail to append to the scheduled task status to add context in the setting
     * page.
     */
    public function __construct(string $name, string $scheduledtaskclassname, string $extradescription = '') {
        $visiblename = new lang_string('task_status', 'admin');
        $this->classname = $scheduledtaskclassname;
        $this->extradescription = $extradescription;

        parent::__construct($name, $visiblename, '');
    }

    /**
     * Calculates lazily the content of the description.
     * @param mixed $data nothing expected in this case.
     * @param string $query nothing expected in this case.
     * @return string the HTML content to print for this setting.
     */
    public function output_html($data, $query = ''): string {
        if (empty($this->description)) {
            $this->description = $this->get_task_description();
        }

        return parent::output_html($data, $query);
    }

    /**
     * Returns the HTML to print as the description.
     * @return string description to be printed.
     */
    private function get_task_description(): string {
        $task = manager::get_scheduled_task($this->classname);
        if ($task->is_enabled()) {
            $taskenabled = get_string('enabled', 'admin');
        } else {
            $taskenabled = get_string('disabled', 'admin');
        }
        $taskenabled = strtolower($taskenabled);
        $gotourl = new moodle_url(
            '/admin/tool/task/scheduledtasks.php',
            [],
            scheduled_task::get_html_id($this->classname)
        );
        if (!empty($this->extradescription)) {
            $this->extradescription = '<br />' . $this->extradescription;
        }

        $taskdetail = new stdClass();
        $taskdetail->class = $this->classname;
        $taskdetail->name = $task->get_name();
        $taskdetail->status = $taskenabled;
        $taskdetail->gotourl = $gotourl->out(false);
        $taskdetail->extradescription = $this->extradescription;

        return html_writer::tag('p', get_string('task_status_desc', 'admin', $taskdetail));
    }
}
