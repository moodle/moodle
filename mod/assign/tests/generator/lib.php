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

global $CFG;
require_once($CFG->libdir . '/gradelib.php');

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
    public function create_instance($record = null, ?array $options = null) {
        $record = (object)(array)$record;

        $defaultsettings = [
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
            'attemptreopenmethod'               => 'untilpass',
            'maxattempts'                       => 1,
            'markingworkflow'                   => 0,
            'markingallocation'                 => 0,
            'markinganonymous'                  => 0,
            'activityformat'                    => 0,
            'timelimit'                         => 0,
            'submissionattachments'             => 0,
        ];

        if (property_exists($record, 'teamsubmissiongroupingid')) {
            $record->teamsubmissiongroupingid = $this->get_grouping_id($record->teamsubmissiongroupingid);
        }

        if (property_exists($record, 'gradetype')) {
            if ((int)$record->gradetype === GRADE_TYPE_SCALE && property_exists($record, 'gradescale')) {
                // Get the scale id and apply it.
                $defaultsettings['grade[modgrade_type]'] = GRADE_TYPE_SCALE;
                $defaultsettings['grade[modgrade_scale]'] = $record->gradescale;
                $defaultsettings['grade'] = -$record->gradescale;
            } else if ((int)$record->gradetype === GRADE_TYPE_NONE) {
                $defaultsettings['grade[modgrade_type]'] = GRADE_TYPE_NONE;
                $defaultsettings['grade'] = 0;
            }
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
     * @param array $data with keys userid, cmid and
     *      then data for each assignsubmission plugin used.
     *      For backwards compatibility, you can pass cmid as 'assignid' but that generates a warning.
     */
    public function create_submission(array $data): void {
        global $USER;

        if (array_key_exists('assignid', $data)) {
            debugging(
                'The cmid passed to create_submission should have array key cmid, not assignid.',
                DEBUG_DEVELOPER,
            );
            $data['cmid'] = $data['assignid'];
            unset($data['assignid']);
        }

        $currentuser = $USER;
        $user = \core_user::get_user($data['userid']);
        $this->set_user($user);

        $submission = (object) [
            'userid' => $user->id,
        ];

        [$course, $cm] = get_course_and_cm_from_cmid($data['cmid'], 'assign');
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);

        foreach ($assign->get_submission_plugins() as $plugin) {
            $pluginname = $plugin->get_type();
            if (array_key_exists($pluginname, $data)) {
                $plugingenerator = $this->datagenerator->get_plugin_generator("assignsubmission_{$pluginname}");
                $plugingenerator->add_submission_data($submission, $assign, $data);
            }
        }

        $assign->save_submission($submission, $notices);

        $this->set_user($currentuser);
    }

    /**
     * Create an assignment extension.
     *
     * @param array $data must have keys cmid, userid, extensionduedate.
     */
    public function create_extension(array $data): void {
        $user = \core_user::get_user($data['userid'], '*', MUST_EXIST);

        [$course, $cm] = get_course_and_cm_from_cmid($data['cmid'], 'assign');
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);

        if (!$assign->save_user_extension($user->id, $data['extensionduedate'] ?: null)) {
            throw new \core\exception\coding_exception('The requested extension could not be created.');
        }
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
            throw new \core\exception\coding_exception('idnumber cannot be empty');
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
