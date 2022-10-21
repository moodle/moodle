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

namespace gradereport_singleview\report;

use context_course;
use grade_report;
use moodle_url;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/grade/report/lib.php');

/**
 * This class is the main class that must be implemented by a grade report plugin.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class singleview extends grade_report {

    /**
     * Return the list of valid screens, used to validate the input.
     *
     * @return array List of screens.
     */
    public static function valid_screens(): array {
        // This is a list of all the known classes representing a screen in this plugin.
        return ['user', 'select', 'grade', 'user_select', 'grade_select'];
    }

    /**
     * Process data from a form submission. Delegated to the current screen.
     *
     * @param array $data The data from the form
     * @return array|object List of warnings
     */
    public function process_data($data) {
        if (has_capability('moodle/grade:edit', $this->context)) {
            return $this->screen->process($data);
        }
    }

    /**
     * Unused - abstract function declared in the parent class.
     *
     * @param string $target
     * @param string $action
     */
    public function process_action($target, $action) {
    }

    /**
     * Constructor for this report. Creates the appropriate screen class based on itemtype.
     *
     * @param int $courseid The course id.
     * @param object $gpr grade plugin return tracking object
     * @param context_course $context
     * @param string $itemtype Should be user, select or grade
     * @param int|null $itemid The id of the user or grade item
     * @param string|null $unused Used to be group id but that was removed and this is now unused.
     */
    public function __construct(
        int $courseid,
        object $gpr,
        context_course $context,
        string $itemtype,
        ?int $itemid,
        ?string $unused = null
    ) {
        parent::__construct($courseid, $gpr, $context);

        $base = '/grade/report/singleview/index.php';

        $idparams = ['id' => $courseid];

        $this->baseurl = new moodle_url($base, $idparams);

        $this->pbarurl = new moodle_url($base, $idparams + [
                'item' => $itemtype,
                'itemid' => $itemid
            ]);

        //  The setup_group method is used to validate group mode and permissions and define the currentgroup value.
        $this->setup_groups();

        $screenclass = "\\gradereport_singleview\\local\\screen\\${itemtype}";

        $this->screen = new $screenclass($courseid, $itemid, $this->currentgroup);

        // Load custom or predifined js.
        $this->screen->js();
    }

    /**
     * Build the html for the screen.
     * @return string HTML to display
     */
    public function output(): string {
        global $OUTPUT;
        return $OUTPUT->container($this->screen->html(), 'reporttable');
    }
}
