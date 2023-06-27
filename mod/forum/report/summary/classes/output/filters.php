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
 * Forum summary report filters renderable.
 *
 * @package    forumreport_summary
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace forumreport_summary\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use forumreport_summary;

defined('MOODLE_INTERNAL') || die();

/**
 * Forum summary report filters renderable.
 *
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filters implements renderable, templatable {

    /**
     * Course modules the report relates to.
     * Array of stdClass objects
     *
     * @var array $cms
     */
    protected $cms;

    /**
     * Course ID where the report is being generated.
     *
     * @var int $courseid
     */
    protected $courseid;

    /**
     * Moodle URL used as the form action on the generate button.
     *
     * @var moodle_url $actionurl
     */
    protected $actionurl;

    /**
     * Details of groups available for filtering.
     * Stored in the format groupid => groupname.
     *
     * @var array $groupsavailable
     */
    protected $groupsavailable = [];

    /**
     * IDs of groups selected for filtering.
     *
     * @var array $groupsselected
     */
    protected $groupsselected = [];

    /**
     * IDs of discussions required for export links.
     * If a subset of groups available are selected, this will include the discussion IDs
     * within that group in the forum.
     * If all groups are selected, or no groups mode is enabled, this will be empty as
     * no discussion filtering is required in the export.
     *
     * @var array $discussionids
     */
    protected $discussionids = [];

    /**
     * HTML for dates filter.
     *
     * @var array $datesdata
     */
    protected $datesdata = [];

    /**
     * Text to display on the dates filter button.
     *
     * @var string $datesbuttontext
     */
    protected $datesbuttontext;

    /**
     * Builds renderable filter data.
     *
     * @param stdClass $course The course object.
     * @param array $cms Array of course module objects.
     * @param moodle_url $actionurl The form action URL.
     * @param array $filterdata (optional) Associative array of data that has been set on available filters, if any,
     *                                     in the format filtertype => [values]
     */
    public function __construct(stdClass $course, array $cms, moodle_url $actionurl, array $filterdata = []) {
        $this->cms = $cms;
        $this->courseid = $course->id;
        $this->actionurl = $actionurl;

        // Prepare groups filter data.
        $groupsdata = $filterdata['groups'] ?? [];
        $this->prepare_groups_data($groupsdata);

        // Prepare dates filter data.
        $datefromdata = $filterdata['datefrom'] ?? [];
        $datetodata = $filterdata['dateto'] ?? [];
        $this->prepare_dates_data($datefromdata, $datetodata);
    }

    /**
     * Prepares groups data and sets relevant property values.
     *
     * @param array $groupsdata Groups selected for filtering.
     * @return void.
     */
    protected function prepare_groups_data(array $groupsdata): void {
        global $DB, $USER;

        $groupsavailable = [];
        $allowedgroupsobj = [];

        $usergroups = groups_get_all_groups($this->courseid, $USER->id);
        $coursegroups = groups_get_all_groups($this->courseid);
        $forumids = [];
        $allgroups = false;
        $hasgroups = false;

        // Check if any forum gives the user access to all groups and no groups.
        foreach ($this->cms as $cm) {
            $forumids[] = $cm->instance;

            // Only need to check for all groups access if not confirmed by a previous check.
            if (!$allgroups) {
                $groupmode = groups_get_activity_groupmode($cm);

                // If no groups mode enabled on the forum, nothing to prepare.
                if (!in_array($groupmode, [VISIBLEGROUPS, SEPARATEGROUPS])) {
                    continue;
                }

                $hasgroups = true;

                // Fetch for the current cm's forum.
                $context = \context_module::instance($cm->id);
                $aag = has_capability('moodle/site:accessallgroups', $context);

                if ($groupmode == VISIBLEGROUPS || $aag) {
                    $allgroups = true;
                }
            }
        }

        // If no groups mode enabled, nothing to prepare.
        if (!$hasgroups) {
            return;
        }

        // Any groups, and no groups.
        if ($allgroups) {
            $nogroups = new stdClass();
            $nogroups->id = -1;
            $nogroups->name = get_string('groupsnone');

            $allowedgroupsobj = $coursegroups + [$nogroups];
        } else {
            $allowedgroupsobj = $usergroups;
        }

        foreach ($allowedgroupsobj as $group) {
            $groupsavailable[$group->id] = $group->name;
        }

        // Set valid groups selected.
        $groupsselected = array_intersect($groupsdata, array_keys($groupsavailable));

        // Overwrite groups properties.
        $this->groupsavailable = $groupsavailable;
        $this->groupsselected = $groupsselected;

        $groupsselectedcount = count($groupsselected);
        if ($groupsselectedcount > 0 && $groupsselectedcount < count($groupsavailable)) {
            list($forumidin, $forumidparams) = $DB->get_in_or_equal($forumids, SQL_PARAMS_NAMED);
            list($groupidin, $groupidparams) = $DB->get_in_or_equal($groupsselected, SQL_PARAMS_NAMED);

            $discussionswhere = "course = :courseid AND forum {$forumidin} AND groupid {$groupidin}";
            $discussionsparams = ['courseid' => $this->courseid];
            $discussionsparams += $forumidparams + $groupidparams;

            $discussionids = $DB->get_fieldset_select('forum_discussions', 'DISTINCT id', $discussionswhere, $discussionsparams);

            foreach ($discussionids as $discussionid) {
                $this->discussionids[] = ['discid' => $discussionid];
            }
        }
    }

    /**
     * Prepares from date, to date and button text.
     * Empty data will default to a disabled filter with today's date.
     *
     * @param array $datefromdata From date selected for filtering, and whether the filter is enabled.
     * @param array $datetodata To date selected for filtering, and whether the filter is enabled.
     * @return void.
     */
    private function prepare_dates_data(array $datefromdata, array $datetodata): void {
        $timezone = \core_date::get_user_timezone_object();
        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        $timestamptoday = time();
        $datetoday  = $calendartype->timestamp_to_date_array($timestamptoday, $timezone);

        // Prepare date/enabled data.
        if (empty($datefromdata['enabled'])) {
            $fromdate = $datetoday;
            $fromtimestamp = $timestamptoday;
            $fromenabled = false;
        } else {
            $fromdate = $calendartype->timestamp_to_date_array($datefromdata['timestamp'], $timezone);
            $fromtimestamp = $datefromdata['timestamp'];
            $fromenabled = true;
        }

        if (empty($datetodata['enabled'])) {
            $todate = $datetoday;
            $totimestamp = $timestamptoday;
            $toenabled = false;
        } else {
            $todate = $calendartype->timestamp_to_date_array($datetodata['timestamp'], $timezone);
            $totimestamp = $datetodata['timestamp'];
            $toenabled = true;
        }

        $this->datesdata = [
            'from' => [
                'day'       => $fromdate['mday'],
                'month'     => $fromdate['mon'],
                'year'      => $fromdate['year'],
                'timestamp' => $fromtimestamp,
                'enabled'   => $fromenabled,
            ],
            'to' => [
                'day'       => $todate['mday'],
                'month'     => $todate['mon'],
                'year'      => $todate['year'],
                'timestamp' => $totimestamp,
                'enabled'   => $toenabled,
            ],
        ];

        // Prepare button string data.
        $displayformat = get_string('strftimedatemonthabbr', 'langconfig');
        $fromdatestring = $calendartype->timestamp_to_date_string($fromtimestamp, $displayformat, $timezone, true, true);
        $todatestring = $calendartype->timestamp_to_date_string($totimestamp, $displayformat, $timezone, true, true);

        if ($fromenabled && $toenabled) {
            $datestrings = [
                'datefrom' => $fromdatestring,
                'dateto'   => $todatestring,
            ];
            $this->datesbuttontext = get_string('filter:datesfromto', 'forumreport_summary', $datestrings);
        } else if ($fromenabled) {
            $this->datesbuttontext = get_string('filter:datesfrom', 'forumreport_summary', $fromdatestring);
        } else if ($toenabled) {
            $this->datesbuttontext = get_string('filter:datesto', 'forumreport_summary', $todatestring);
        } else {
            $this->datesbuttontext = get_string('filter:datesname', 'forumreport_summary');
        }
    }

    /**
     * Export data for use as the context of a mustache template.
     *
     * @param renderer_base $renderer The renderer to be used to display report filters.
     * @return array Data in a format compatible with a mustache template.
     */
    public function export_for_template(renderer_base $renderer): stdClass {
        $output = new stdClass();

        // Set formaction URL.
        $output->actionurl = $this->actionurl->out(false);

        // Set groups filter data.
        if (!empty($this->groupsavailable)) {
            $output->hasgroups = true;

            $groupscount = count($this->groupsselected);

            if (count($this->groupsavailable) <= $groupscount) {
                $output->filtergroupsname = get_string('filter:groupscountall', 'forumreport_summary');
            } else if (!empty($this->groupsselected)) {
                $output->filtergroupsname = get_string('filter:groupscountnumber', 'forumreport_summary', $groupscount);
            } else {
                $output->filtergroupsname = get_string('filter:groupsname', 'forumreport_summary');
            }

            // Set groups filter.
            $groupsdata = [];

            foreach ($this->groupsavailable as $groupid => $groupname) {
                $groupsdata[] = [
                    'groupid' => $groupid,
                    'groupname' => $groupname,
                    'checked' => in_array($groupid, $this->groupsselected),
                ];
            }

            $output->filtergroups = $groupsdata;
        } else {
            $output->hasgroups = false;
        }

        // Set discussion IDs for use by export links (always included, as it will be empty if not required).
        $output->discussionids = $this->discussionids;

        // Set date button and generate dates popover mform.
        $datesformdata = [];

        if ($this->datesdata['from']['enabled']) {
            $datesformdata['filterdatefrompopover'] = $this->datesdata['from'];
        }

        if ($this->datesdata['to']['enabled']) {
            $datesformdata['filterdatetopopover'] = $this->datesdata['to'];
        }

        $output->filterdatesname = $this->datesbuttontext;
        $datesform = new forumreport_summary\form\dates_filter_form();
        $datesform->set_data($datesformdata);
        $output->filterdatesform = $datesform->render();

         // Set dates filter data within filters form.
        $disableddate = [
            'day' => '',
            'month' => '',
            'year' => '',
            'enabled' => '0',
        ];
        $datefromdata = ['type' => 'from'] + ($this->datesdata['from']['enabled'] ? $this->datesdata['from'] : $disableddate);
        $datetodata = ['type' => 'to'] + ($this->datesdata['to']['enabled'] ? $this->datesdata['to'] : $disableddate);
        $output->filterdatesdata = [$datefromdata, $datetodata];

        return $output;
    }
}
