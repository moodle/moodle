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

defined('MOODLE_INTERNAL') || die();

/**
 * assign module data generator class
 *
 * @package mod_assign
 * @category test
 * @copyright 2012 Paul Charsley
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_generator extends testing_module_generator {

    /**
     * Create a new instance of the assignment activity.
     *
     * @param array|stdClass|null $record
     * @param array|null $options
     * @return stdClass
     */
    public function create_instance($record = null, array $options = null) {
        $record = (object)(array)$record;

        $defaultsettings = array(
            'alwaysshowdescription'             => 1,
            'submissiondrafts'                  => 1,
            'requiresubmissionstatement'        => 0,
            'sendnotifications'                 => 0,
            'sendstudentnotifications'          => 1,
            'sendlatenotifications'             => 0,
            'duedate'                           => 0,
            'allowsubmissionsfromdate'          => 0,
            'grade'                             => 100,
            'cutoffdate'                        => 0,
            'gradingduedate'                    => 0,
            'teamsubmission'                    => 0,
            'requireallteammemberssubmit'       => 0,
            'teamsubmissiongroupingid'          => 0,
            'blindmarking'                      => 0,
            'attemptreopenmethod'               => 'none',
            'maxattempts'                       => -1,
            'markingworkflow'                   => 0,
            'markingallocation'                 => 0,
        );

        if (property_exists($record, 'teamsubmissiongroupingid')) {
            $record->teamsubmissiongroupingid = $this->get_grouping_id($record->teamsubmissiongroupingid);
        }

        foreach ($defaultsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, (array)$options);
    }

    /**
     * Create an assignment submission.
     *
     * @param array $data
     */
    public function create_submission(array $data): void {
        global $USER;

        $currentuser = $USER;
        $user = \core_user::get_user($data['userid']);
        $this->set_user($user);

        $submission = (object) [
            'userid' => $user->id,
        ];

        [$course, $cm] = get_course_and_cm_from_cmid($data['assignid'], 'assign');
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);

        foreach ($assign->get_submission_plugins() as $plugin) {
            $pluginname = $plugin->get_type();
            if (array_key_exists($pluginname, $data)) {
                $plugingenerator = $this->datagenerator->get_plugin_generator("assignsubmission_{$pluginname}");
                $plugingenerator->add_submission_data($submission, $assign, $data);
            }
        }

        $assign->save_submission((object) $submission, $notices);

        $this->set_user($currentuser);
    }

    /**
     * Gets the grouping id from it's idnumber.
     *
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_grouping_id(string $idnumber): int {
        global $DB;

        // Do not fetch grouping ID for empty grouping idnumber.
        if (empty($idnumber)) {
            return null;
        }

        if (!$id = $DB->get_field('groupings', 'id', ['idnumber' => $idnumber])) {
            if (is_numeric($idnumber)) {
                return $idnumber;
            }
            throw new Exception('The specified grouping with idnumber "' . $idnumber . '" does not exist');
        }

        return $id;
    }

    /**
     * Create an assign override (either user or group).
     *
     * @param array $data must specify assignid, and one of userid or groupid.
     * @throws coding_exception
     */
    public function create_override(array $data): void {
        global $DB;

        if (!isset($data['assignid'])) {
            throw new coding_exception('Must specify assignid when creating an assign override.');
        }

        if (!isset($data['userid']) && !isset($data['groupid'])) {
            throw new coding_exception('Must specify one of userid or groupid when creating an assign override.');
        }

        if (isset($data['userid']) && isset($data['groupid'])) {
            throw new coding_exception('Cannot specify both userid and groupid when creating an assign override.');
        }

        $DB->insert_record('assign_overrides', (object) $data);
    }
}
