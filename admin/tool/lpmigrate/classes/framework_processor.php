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
 * Processor.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lpmigrate;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use moodle_exception;
use core_competency\api;
use core_competency\competency;
use core_competency\course_competency;
use core_competency\course_module_competency;

/**
 * Processor class.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class framework_processor {

    /** @var array Indexed as courseid => competencyids */
    protected $coursescompetencies = array();
    /** @var array Indexed as courseid => competencyid => ruleoutcome*/
    protected $coursescompetenciesoutcomes = array();
    /** @var array Indexed as courseid => cmid => competencyids */
    protected $modulecompetencies = array();
    /** @var array Indexed as courseid => cmid => competencyid => ruleoutcome*/
    protected $modulecompetenciesoutcomes = array();
    /** @var array The IDs of the objects of origin. */
    protected $fromids = array();
    /** @var array The mapping originId => destinationId. */
    protected $mappings = array();

    /** @var array Courses found. */
    protected $coursesfound = array();
    /** @var array Course modules found. */
    protected $cmsfound = array();
    /** @var integer Number of migrations expected in courses. */
    protected $coursecompetencyexpectedmigrations = 0;
    /** @var integer Count of migrations in the course level. */
    protected $coursecompetencymigrations = 0;
    /** @var integer Count of removals in the course level. */
    protected $coursecompetencyremovals = 0;
    /** @var integer Number of migrations expected in CMs. */
    protected $modulecompetencyexpectedmigrations = 0;
    /** @var integer Count of migrations in CMs. */
    protected $modulecompetencymigrations = 0;
    /** @var integer Count of removals in CMs. */
    protected $modulecompetencyremovals = 0;
    /** @var array IDs of objects missing a mapping in origin, originId => true. */
    protected $missingmappings = array();
    /** @var array List of errors. */
    protected $errors = array();
    /** @var array List of warnings. */
    protected $warnings = array();

    /** @var array List of course IDs that can be migrated. */
    protected $allowedcourses = array();
    /** @var int Minimum start date of courses that can be migrated. */
    protected $coursestartdatefrom = 0;
    /** @var array List of course IDs that cannot be migrated. */
    protected $disallowedcourses = array();
    /** @var bool Whether to remove the original competency when its destination was already there. */
    protected $removeoriginalwhenalreadypresent = false;
    /** @var bool Whether to remove the competency from course, or cm, when a mapping is not found. */
    protected $removewhenmappingismissing = false;

    /** @var boolean Has this processor run? */
    protected $proceeded = false;
    /** @var framework_mapper The mapper. */
    protected $mapper;
    /** @var \core\progress\base The progress. */
    protected $progress;

    /**
     * Constructor.
     *
     * @param framework_mapper $mapper The mapper.
     * @param \core\progress\base $progress The progress object.
     */
    public function __construct(framework_mapper $mapper, \core\progress\base $progress = null) {
        $this->mapper = $mapper;

        if ($progress == null) {
            $progress = new \core\progress\none();
        }
        $this->progress = $progress;
    }

    /**
     * Process the mapping.
     * @return void
     */
    protected function process_mapping() {
        $this->mappings = $this->mapper->get_mappings();
        $this->fromids = $this->mapper->get_all_from();
    }

    /**
     * Identifies what courses and their competencies to work with.
     * @return void
     */
    protected function find_coursescompetencies() {
        global $DB;
        $this->progress->start_progress(get_string('findingcoursecompetencies', 'tool_lpmigrate'), 3);
        $this->progress->increment_progress();

        $joins = array();
        $conditions = array();
        $params = array();

        // Limit to mapped objects.
        list($insql, $inparams) = $DB->get_in_or_equal($this->fromids, SQL_PARAMS_NAMED);
        $conditions[] = "c.id $insql";
        $params += $inparams;

        // Restriction on course IDs.
        if (!empty($this->allowedcourses)) {
            list($insql, $inparams) = $DB->get_in_or_equal($this->allowedcourses, SQL_PARAMS_NAMED);
            $conditions[] = "cc.courseid $insql";
            $params += $inparams;
        }
        if (!empty($this->disallowedcourses)) {
            list($insql, $inparams) = $DB->get_in_or_equal($this->disallowedcourses, SQL_PARAMS_NAMED, 'param', false);
            $conditions[] = "cc.courseid $insql";
            $params += $inparams;
        }

        // Restriction on start date.
        if (!empty($this->coursestartdatefrom)) {
            $joins[] = "JOIN {course} co
                          ON co.id = cc.courseid";
            $conditions[] = "co.startdate >= :startdate";
            $params += array('startdate' => $this->coursestartdatefrom);
        }

        // Find the courses.
        $ccs = array();
        $ccsoutcomes = array();
        $joins = implode(' ', $joins);
        $conditions = implode(' AND ', $conditions);
        $sql = "SELECT cc.id, cc.courseid, cc.competencyid, cc.ruleoutcome
                  FROM {" . course_competency::TABLE . "} cc
                  JOIN {" . competency::TABLE . "} c
                    ON c.id = cc.competencyid
                       $joins
                 WHERE $conditions
              ORDER BY cc.sortorder, cc.id";

        $records = $DB->get_recordset_sql($sql, $params);
        $this->progress->increment_progress();

        foreach ($records as $record) {
            if (!isset($ccs[$record->courseid])) {
                $ccs[$record->courseid] = array();
                $ccsoutcomes[$record->courseid] = array();
            }
            $ccs[$record->courseid][] = $record->competencyid;
            $ccsoutcomes[$record->courseid][$record->competencyid] = $record->ruleoutcome;
        }
        $records->close();

        $this->coursescompetencies = $ccs;
        $this->coursescompetenciesoutcomes = $ccsoutcomes;
        $this->coursesfound = $ccs;

        $this->progress->increment_progress();
        $this->progress->end_progress();
    }

    /**
     * Identifies what course modules and their competencies to work with.
     * @return void
     */
    protected function find_modulecompetencies() {
        global $DB;
        if (empty($this->coursescompetencies)) {
            return;
        }

        $this->progress->start_progress(get_string('findingmodulecompetencies', 'tool_lpmigrate'), 3);
        $this->progress->increment_progress();

        // Limit to mapped objects.
        list($inidsql, $inidparams) = $DB->get_in_or_equal($this->fromids, SQL_PARAMS_NAMED);

        // Limit to known courses.
        list($incoursesql, $incourseparams) = $DB->get_in_or_equal(array_keys($this->coursescompetencies), SQL_PARAMS_NAMED);
        $sql = "SELECT mc.id, cm.course AS courseid, mc.cmid, mc.competencyid, mc.ruleoutcome
                  FROM {" . course_module_competency::TABLE . "} mc
                  JOIN {course_modules} cm
                    ON cm.id = mc.cmid
                   AND cm.course $incoursesql
                  JOIN {" . competency::TABLE . "} c
                    ON c.id = mc.competencyid
                 WHERE c.id $inidsql
              ORDER BY mc.sortorder, mc.id";
        $params = $inidparams + $incourseparams;

        $records = $DB->get_recordset_sql($sql, $params);
        $this->progress->increment_progress();
        $cmsfound = array();

        $cmcs = array();
        $cmcsoutcomes = array();
        foreach ($records as $record) {
            if (!isset($cmcs[$record->courseid])) {
                $cmcs[$record->courseid] = array();
                $cmcsoutcomes[$record->courseid] = array();
            }
            if (!isset($cmcs[$record->courseid][$record->cmid])) {
                $cmcs[$record->courseid][$record->cmid] = array();
                $cmcsoutcomes[$record->courseid][$record->cmid] = array();
            }
            $cmcs[$record->courseid][$record->cmid][] = $record->competencyid;
            $cmcsoutcomes[$record->courseid][$record->cmid][$record->competencyid] = $record->ruleoutcome;
            $cmsfound[$record->cmid] = true;
        }
        $records->close();

        $this->modulecompetencies = $cmcs;
        $this->modulecompetenciesoutcomes = $cmcsoutcomes;
        $this->cmsfound = $cmsfound;

        $this->progress->increment_progress();
        $this->progress->end_progress();
    }

    /**
     * Return a list of CMs found.
     * @return int
     */
    public function get_cms_found() {
        return $this->cmsfound;
    }

    /**
     * Return the number of CMs found.
     * @return int
     */
    public function get_cms_found_count() {
        return count($this->cmsfound);
    }

    /**
     * Return a list of courses found.
     * @return int
     */
    public function get_courses_found() {
        return $this->coursesfound;
    }

    /**
     * Return the number of courses found.
     * @return int
     */
    public function get_courses_found_count() {
        return count($this->coursesfound);
    }

    /**
     * Get the number of course migrations.
     * @return int
     */
    public function get_course_competency_migrations() {
        return $this->coursecompetencymigrations;
    }

    /**
     * Get the number of removals.
     * @return int
     */
    public function get_course_competency_removals() {
        return $this->coursecompetencyremovals;
    }

    /**
     * Get the number of expected course migrations.
     * @return int
     */
    public function get_expected_course_competency_migrations() {
        return $this->coursecompetencyexpectedmigrations;
    }

    /**
     * Get the number of expected course module migrations.
     * @return int
     */
    public function get_expected_module_competency_migrations() {
        return $this->modulecompetencyexpectedmigrations;
    }

    /**
     * Get the number of course module migrations.
     * @return int
     */
    public function get_module_competency_migrations() {
        return $this->modulecompetencymigrations;
    }

    /**
     * Get the number of removals.
     * @return int
     */
    public function get_module_competency_removals() {
        return $this->modulecompetencyremovals;
    }

    /**
     * Return a list of errors.
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Get the missing mappings.
     * @return array Where keys are origin IDs.
     */
    public function get_missing_mappings() {
        if (!$this->has_run()) {
            throw new coding_exception('The processor has not run yet.');
        }
        return $this->missingmappings;
    }

    /**
     * Return a list of warnings.
     * @return array
     */
    public function get_warnings() {
        return $this->warnings;
    }

    /**
     * Whether the processor has run.
     * @return boolean
     */
    public function has_run() {
        return $this->proceeded;
    }

    /**
     * Log an error.
     * @param int $courseid The course ID.
     * @param int $competencyid The competency ID.
     * @param int $cmid The CM ID.
     * @param string $message The error message.
     * @return void
     */
    protected function log_error($courseid, $competencyid, $cmid, $message) {
        $this->errors[] = array(
            'courseid' => $courseid,
            'competencyid' => $competencyid,
            'cmid' => $cmid,
            'message' => $message
        );
    }

    /**
     * Log a warning.
     * @param int $courseid The course ID.
     * @param int $competencyid The competency ID.
     * @param int $cmid The CM ID.
     * @param string $message The warning message.
     * @return void
     */
    protected function log_warning($courseid, $competencyid, $cmid, $message) {
        $this->warnings[] = array(
            'courseid' => $courseid,
            'competencyid' => $competencyid,
            'cmid' => $cmid,
            'message' => $message
        );
    }

    /**
     * Execute the whole task.
     * @return void
     */
    public function proceed() {
        if ($this->has_run()) {
            throw new coding_exception('The processor has already run.');
        } else if (!$this->mapper->has_mappings()) {
            throw new coding_exception('Mapping was not set.');
        }

        $this->proceeded = true;
        $this->process_mapping();
        $this->find_coursescompetencies();
        $this->find_modulecompetencies();
        $this->process_courses();
    }

    /**
     * Process each course individually.
     * @return void
     */
    protected function process_courses() {
        global $DB;
        $this->progress->start_progress(get_string('migratingcourses', 'tool_lpmigrate'), count($this->coursescompetencies));

        // Process each course.
        foreach ($this->coursescompetencies as $courseid => $competencyids) {
            $this->progress->increment_progress();

            $competenciestoremovefromcourse = array();
            $skipcompetencies = array();

            // First, add all the new competencies to the course.
            foreach ($competencyids as $key => $competencyid) {
                $this->coursecompetencyexpectedmigrations++;
                $mapto = isset($this->mappings[$competencyid]) ? $this->mappings[$competencyid] : false;

                // Skip the competencies that are not mapped.
                if ($mapto === false) {
                    $this->missingmappings[$competencyid] = true;

                    if ($this->removewhenmappingismissing) {
                        $competenciestoremovefromcourse[$competencyid] = true;
                    }

                    continue;
                }

                $transaction = $DB->start_delegated_transaction();
                try {
                    // Add the new competency to the course.
                    if (api::add_competency_to_course($courseid, $mapto)) {

                        // Find the added course competency.
                        $cc = course_competency::get_record(array('courseid' => $courseid, 'competencyid' => $mapto));

                        // Set the rule.
                        api::set_course_competency_ruleoutcome($cc, $this->coursescompetenciesoutcomes[$courseid][$competencyid]);

                        // Adapt the sortorder.
                        api::reorder_course_competency($courseid, $mapto, $competencyid);

                        $competenciestoremovefromcourse[$competencyid] = true;
                        $this->coursecompetencymigrations++;

                    } else {
                        // The competency was already in the course...
                        if ($this->removeoriginalwhenalreadypresent) {
                            $competenciestoremovefromcourse[$competencyid] = true;
                        } else {
                            $this->log_warning($courseid, $competencyid, null,
                                get_string('warningdestinationcoursecompetencyalreadyexists', 'tool_lpmigrate'));
                        }
                    }

                } catch (moodle_exception $e) {
                    // There was a major problem with this competency, we will ignore it entirely for the course.
                    $skipcompetencies[$competencyid] = true;

                    $this->log_error($courseid, $competencyid, null,
                        get_string('errorwhilemigratingcoursecompetencywithexception', 'tool_lpmigrate', $e->getMessage()));

                    try {
                        $transaction->rollback($e);
                    } catch (moodle_exception $e) {
                        // Catch the re-thrown exception.
                    }

                    continue;
                }
                $transaction->allow_commit();
            }

            // Then, convert the module competencies.
            if (!empty($this->modulecompetencies[$courseid])) {
                foreach ($this->modulecompetencies[$courseid] as $cmid => $competencyids) {
                    foreach ($competencyids as $competencyid) {
                        $this->modulecompetencyexpectedmigrations++;

                        // This mapped competency was not added to the course.
                        if (!empty($skipcompetencies[$competencyid])) {
                            continue;
                        }

                        $remove = true;
                        $mapto = isset($this->mappings[$competencyid]) ? $this->mappings[$competencyid] : false;

                        // We don't have mapping.
                        if ($mapto === false) {
                            if (!$this->removewhenmappingismissing) {
                                $remove = false;
                            }

                        } else {
                            // We have a mapping.
                            $transaction = $DB->start_delegated_transaction();
                            try {
                                // The competency was added successfully.
                                if (api::add_competency_to_course_module($cmid, $mapto)) {

                                    // Find the added module competency.
                                    $mc = course_module_competency::get_record(array('cmid' => $cmid, 'competencyid' => $mapto));

                                    // Set the competency rule.
                                    api::set_course_module_competency_ruleoutcome($mc,
                                        $this->modulecompetenciesoutcomes[$courseid][$cmid][$competencyid]);

                                    // Adapt the sortorder.
                                    api::reorder_course_module_competency($cmid, $mapto, $competencyid);

                                    $this->modulecompetencymigrations++;

                                } else {
                                    // The competency was already in the module.
                                    if (!$this->removeoriginalwhenalreadypresent) {
                                        $remove = false;
                                        $competencieswithissues[$competencyid] = true;
                                        $this->log_warning($courseid, $competencyid, $cmid,
                                            get_string('warningdestinationmodulecompetencyalreadyexists', 'tool_lpmigrate'));
                                    }
                                }

                            } catch (moodle_exception $e) {
                                // There was a major problem with this competency in this module.
                                $competencieswithissues[$competencyid] = true;
                                $message = get_string('errorwhilemigratingmodulecompetencywithexception', 'tool_lpmigrate',
                                    $e->getMessage());
                                $this->log_error($courseid, $competencyid, $cmid, $message);

                                try {
                                    $transaction->rollback($e);
                                } catch (moodle_exception $e) {
                                    // Catch the re-thrown exception.
                                }

                                continue;
                            }
                            $transaction->allow_commit();
                        }

                        try {
                            // Go away competency!
                            if ($remove && api::remove_competency_from_course_module($cmid, $competencyid)) {
                                $this->modulecompetencyremovals++;
                            }
                        } catch (moodle_exception $e) {
                            $competencieswithissues[$competencyid] = true;
                            $this->log_warning($courseid, $competencyid, $cmid,
                                get_string('warningcouldnotremovemodulecompetency', 'tool_lpmigrate'));
                        }
                    }
                }
            }

            // Finally, we remove the course competencies, but only for the 100% successful ones.
            foreach ($competenciestoremovefromcourse as $competencyid => $unused) {

                // Skip competencies with issues.
                if (isset($competencieswithissues[$competencyid])) {
                    continue;
                }

                try {
                    // Process the course competency.
                    api::remove_competency_from_course($courseid, $competencyid);
                    $this->coursecompetencyremovals++;
                } catch (moodle_exception $e) {
                    $this->log_warning($courseid, $competencyid, null,
                        get_string('warningcouldnotremovecoursecompetency', 'tool_lpmigrate'));
                }
            }
        }

        $this->progress->end_progress();
    }

    /**
     * Set the IDs of the courses that are allowed.
     * @param array $courseids
     */
    public function set_allowedcourses(array $courseids) {
        $this->allowedcourses = $courseids;
    }

    /**
     * Set the minimum start date for courses to be migrated.
     * @param int $value Timestamp, or 0.
     */
    public function set_course_start_date_from($value) {
        $this->coursestartdatefrom = intval($value);
    }

    /**
     * Set the IDs of the courses that are not allowed.
     * @param array $courseids
     */
    public function set_disallowedcourses(array $courseids) {
        $this->disallowedcourses = $courseids;
    }

    /**
     * Set whether we should remove original competencies when the destination competency was already there.
     * @param bool $value
     */
    public function set_remove_original_when_destination_already_present($value) {
        $this->removeoriginalwhenalreadypresent = $value;
    }

    /**
     * Set whether we should remove unmapped competencies.
     * @param bool $value
     */
    public function set_remove_when_mapping_is_missing($value) {
        $this->removewhenmappingismissing = $value;
    }

}
