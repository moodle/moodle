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
 * Class for loading/storing competency frameworks from the DB.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use stdClass;
use cm_info;
use context;
use context_helper;
use context_system;
use context_course;
use context_module;
use context_user;
use coding_exception;
use require_login_exception;
use moodle_exception;
use moodle_url;
use required_capability_exception;

/**
 * Class for doing things with competency frameworks.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Returns whether competencies are enabled.
     *
     * This method should never do more than checking the config setting, the reason
     * being that some other code could be checking the config value directly
     * to avoid having to load this entire file into memory.
     *
     * @return boolean True when enabled.
     */
    public static function is_enabled() {
        return get_config('core_competency', 'enabled');
    }

    /**
     * Throws an exception if competencies are not enabled.
     *
     * @return void
     * @throws moodle_exception
     */
    public static function require_enabled() {
        if (!static::is_enabled()) {
            throw new moodle_exception('competenciesarenotenabled', 'core_competency');
        }
    }

    /**
     * Checks whether a scale is used anywhere in the plugin.
     *
     * This public API has two exceptions:
     * - It MUST NOT perform any capability checks.
     * - It MUST ignore whether competencies are enabled or not ({@link self::is_enabled()}).
     *
     * @param int $scaleid The scale ID.
     * @return bool
     */
    public static function is_scale_used_anywhere($scaleid) {
        global $DB;
        $sql = "SELECT s.id
                  FROM {scale} s
             LEFT JOIN {" . competency_framework::TABLE ."} f
                    ON f.scaleid = :scaleid1
             LEFT JOIN {" . competency::TABLE ."} c
                    ON c.scaleid = :scaleid2
                 WHERE f.id IS NOT NULL
                    OR c.id IS NOT NULL";
        return $DB->record_exists_sql($sql, ['scaleid1' => $scaleid, 'scaleid2' => $scaleid]);
    }

    /**
     * Validate if current user have acces to the course_module if hidden.
     *
     * @param mixed $cmmixed The cm_info class, course module record or its ID.
     * @param bool $throwexception Throw an exception or not.
     * @return bool
     */
    protected static function validate_course_module($cmmixed, $throwexception = true) {
        $cm = $cmmixed;
        if (!is_object($cm)) {
            $cmrecord = get_coursemodule_from_id(null, $cmmixed);
            $modinfo = get_fast_modinfo($cmrecord->course);
            $cm = $modinfo->get_cm($cmmixed);
        } else if (!$cm instanceof cm_info) {
            // Assume we got a course module record.
            $modinfo = get_fast_modinfo($cm->course);
            $cm = $modinfo->get_cm($cm->id);
        }

        if (!$cm->uservisible) {
            if ($throwexception) {
                throw new require_login_exception('Course module is hidden');
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate if current user have acces to the course if hidden.
     *
     * @param mixed $courseorid The course or it ID.
     * @param bool $throwexception Throw an exception or not.
     * @return bool
     */
    protected static function validate_course($courseorid, $throwexception = true) {
        $course = $courseorid;
        if (!is_object($course)) {
            $course = get_course($course);
        }

        $coursecontext = context_course::instance($course->id);
        if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
            if ($throwexception) {
                throw new require_login_exception('Course is hidden');
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a competency from a record containing all the data for the class.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     * @return competency
     */
    public static function create_competency(stdClass $record) {
        static::require_enabled();
        $competency = new competency(0, $record);

        // First we do a permissions check.
        require_capability('moodle/competency:competencymanage', $competency->get_context());

        // Reset the sortorder, use reorder instead.
        $competency->set_sortorder(null);
        $competency->create();

        \core\event\competency_created::create_from_competency($competency)->trigger();

        // Reset the rule of the parent.
        $parent = $competency->get_parent();
        if ($parent) {
            $parent->reset_rule();
            $parent->update();
        }

        return $competency;
    }

    /**
     * Delete a competency by id.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param int $id The record to delete. This will delete alot of related data - you better be sure.
     * @return boolean
     */
    public static function delete_competency($id) {
        global $DB;
        static::require_enabled();
        $competency = new competency($id);

        // First we do a permissions check.
        require_capability('moodle/competency:competencymanage', $competency->get_context());

        $events = array();
        $competencyids = array(intval($competency->get_id()));
        $contextid = $competency->get_context()->id;
        $competencyids = array_merge(competency::get_descendants_ids($competency), $competencyids);
        if (!competency::can_all_be_deleted($competencyids)) {
            return false;
        }
        $transaction = $DB->start_delegated_transaction();

        try {

            // Reset the rule of the parent.
            $parent = $competency->get_parent();
            if ($parent) {
                $parent->reset_rule();
                $parent->update();
            }

            // Delete the competency separately so the after_delete event can be triggered.
            $competency->delete();

            // Delete the competencies.
            competency::delete_multiple($competencyids);

            // Delete the competencies relation.
            related_competency::delete_multiple_relations($competencyids);

            // Delete competency evidences.
            user_evidence_competency::delete_by_competencyids($competencyids);

            // Register the competencies deleted events.
            $events = \core\event\competency_deleted::create_multiple_from_competencyids($competencyids, $contextid);

        } catch (\Exception $e) {
            $transaction->rollback($e);
        }

        $transaction->allow_commit();
        // Trigger events.
        foreach ($events as $event) {
            $event->trigger();
        }

        return true;
    }

    /**
     * Reorder this competency.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param int $id The id of the competency to move.
     * @return boolean
     */
    public static function move_down_competency($id) {
        static::require_enabled();
        $current = new competency($id);

        // First we do a permissions check.
        require_capability('moodle/competency:competencymanage', $current->get_context());

        $max = self::count_competencies(array('parentid' => $current->get_parentid(),
                                              'competencyframeworkid' => $current->get_competencyframeworkid()));
        if ($max > 0) {
            $max--;
        }

        $sortorder = $current->get_sortorder();
        if ($sortorder >= $max) {
            return false;
        }
        $sortorder = $sortorder + 1;
        $current->set_sortorder($sortorder);

        $filters = array('parentid' => $current->get_parentid(),
                         'competencyframeworkid' => $current->get_competencyframeworkid(),
                         'sortorder' => $sortorder);
        $children = self::list_competencies($filters, 'id');
        foreach ($children as $needtoswap) {
            $needtoswap->set_sortorder($sortorder - 1);
            $needtoswap->update();
        }

        // OK - all set.
        $result = $current->update();

        return $result;
    }

    /**
     * Reorder this competency.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param int $id The id of the competency to move.
     * @return boolean
     */
    public static function move_up_competency($id) {
        static::require_enabled();
        $current = new competency($id);

        // First we do a permissions check.
        require_capability('moodle/competency:competencymanage', $current->get_context());

        $sortorder = $current->get_sortorder();
        if ($sortorder == 0) {
            return false;
        }

        $sortorder = $sortorder - 1;
        $current->set_sortorder($sortorder);

        $filters = array('parentid' => $current->get_parentid(),
                         'competencyframeworkid' => $current->get_competencyframeworkid(),
                         'sortorder' => $sortorder);
        $children = self::list_competencies($filters, 'id');
        foreach ($children as $needtoswap) {
            $needtoswap->set_sortorder($sortorder + 1);
            $needtoswap->update();
        }

        // OK - all set.
        $result = $current->update();

        return $result;
    }

    /**
     * Move this competency so it sits in a new parent.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param int $id The id of the competency to move.
     * @param int $newparentid The new parent id for the competency.
     * @return boolean
     */
    public static function set_parent_competency($id, $newparentid) {
        global $DB;
        static::require_enabled();
        $current = new competency($id);

        // First we do a permissions check.
        require_capability('moodle/competency:competencymanage', $current->get_context());
        if ($id == $newparentid) {
            throw new coding_exception('Can not set a competency as a parent of itself.');
        } if ($newparentid == $current->get_parentid()) {
            throw new coding_exception('Can not move a competency to the same location.');
        }

        // Some great variable assignment right here.
        $currentparent = $current->get_parent();
        $parent = !empty($newparentid) ? new competency($newparentid) : null;
        $parentpath = !empty($parent) ? $parent->get_path() : '/0/';

        // We're going to change quite a few things.
        $transaction = $DB->start_delegated_transaction();

        // If we are moving a node to a child of itself:
        // - promote all the child nodes by one level.
        // - remove the rule on self.
        // - re-read the parent.
        $newparents = explode('/', $parentpath);
        if (in_array($current->get_id(), $newparents)) {
            $children = competency::get_records(array('parentid' => $current->get_id()), 'id');
            foreach ($children as $child) {
                $child->set_parentid($current->get_parentid());
                $child->update();
            }

            // Reset the rule on self as our children have changed.
            $current->reset_rule();

            // The destination parent is one of our descendants, we need to re-fetch its values (path, parentid).
            $parent->read();
        }

        // Reset the rules of initial parent and destination.
        if (!empty($currentparent)) {
            $currentparent->reset_rule();
            $currentparent->update();
        }
        if (!empty($parent)) {
            $parent->reset_rule();
            $parent->update();
        }

        // Do the actual move.
        $current->set_parentid($newparentid);
        $result = $current->update();

        // All right, let's commit this.
        $transaction->allow_commit();

        return $result;
    }

    /**
     * Update the details for a competency.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param stdClass $record The new details for the competency.
     *                         Note - must contain an id that points to the competency to update.
     *
     * @return boolean
     */
    public static function update_competency($record) {
        static::require_enabled();
        $competency = new competency($record->id);

        // First we do a permissions check.
        require_capability('moodle/competency:competencymanage', $competency->get_context());

        // Some things should not be changed in an update - they should use a more specific method.
        $record->sortorder = $competency->get_sortorder();
        $record->parentid = $competency->get_parentid();
        $record->competencyframeworkid = $competency->get_competencyframeworkid();

        $competency->from_record($record);
        require_capability('moodle/competency:competencymanage', $competency->get_context());

        // OK - all set.
        $result = $competency->update();

        // Trigger the update event.
        \core\event\competency_updated::create_from_competency($competency)->trigger();

        return $result;
    }

    /**
     * Read a the details for a single competency and return a record.
     *
     * Requires moodle/competency:competencyview capability at the system context.
     *
     * @param int $id The id of the competency to read.
     * @param bool $includerelated Include related tags or not.
     * @return stdClass
     */
    public static function read_competency($id, $includerelated = false) {
        static::require_enabled();
        $competency = new competency($id);

        // First we do a permissions check.
        $context = $competency->get_context();
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // OK - all set.
        if ($includerelated) {
            $relatedcompetency = new related_competency();
            if ($related = $relatedcompetency->list_relations($id)) {
                $competency->relatedcompetencies = $related;
            }
        }

        return $competency;
    }

    /**
     * Perform a text search based and return all results and their parents.
     *
     * Requires moodle/competency:competencyview capability at the framework context.
     *
     * @param string $textsearch A string to search for.
     * @param int $competencyframeworkid The id of the framework to limit the search.
     * @return array of competencies
     */
    public static function search_competencies($textsearch, $competencyframeworkid) {
        static::require_enabled();
        $framework = new competency_framework($competencyframeworkid);

        // First we do a permissions check.
        $context = $framework->get_context();
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // OK - all set.
        $competencies = competency::search($textsearch, $competencyframeworkid);
        return $competencies;
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires moodle/competency:competencyview capability at some context.
     *
     * @param array $filters A list of filters to apply to the list.
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @return array of competencies
     */
    public static function list_competencies($filters, $sort = '', $order = 'ASC', $skip = 0, $limit = 0) {
        static::require_enabled();
        if (!isset($filters['competencyframeworkid'])) {
            $context = context_system::instance();
        } else {
            $framework = new competency_framework($filters['competencyframeworkid']);
            $context = $framework->get_context();
        }

        // First we do a permissions check.
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // OK - all set.
        return competency::get_records($filters, $sort, $order, $skip, $limit);
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires moodle/competency:competencyview capability at some context.
     *
     * @param array $filters A list of filters to apply to the list.
     * @return int
     */
    public static function count_competencies($filters) {
        static::require_enabled();
        if (!isset($filters['competencyframeworkid'])) {
            $context = context_system::instance();
        } else {
            $framework = new competency_framework($filters['competencyframeworkid']);
            $context = $framework->get_context();
        }

        // First we do a permissions check.
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // OK - all set.
        return competency::count_records($filters);
    }

    /**
     * Create a competency framework from a record containing all the data for the class.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     * @return competency_framework
     */
    public static function create_framework(stdClass $record) {
        static::require_enabled();
        $framework = new competency_framework(0, $record);
        require_capability('moodle/competency:competencymanage', $framework->get_context());

        // Account for different formats of taxonomies.
        if (isset($record->taxonomies)) {
            $framework->set_taxonomies($record->taxonomies);
        }

        $framework = $framework->create();

        // Trigger a competency framework created event.
        \core\event\competency_framework_created::create_from_framework($framework)->trigger();

        return $framework;
    }

    /**
     * Duplicate a competency framework by id.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param int $id The record to duplicate. All competencies associated and related will be duplicated.
     * @return competency_framework the framework duplicated
     */
    public static function duplicate_framework($id) {
        global $DB;
        static::require_enabled();

        $framework = new competency_framework($id);
        require_capability('moodle/competency:competencymanage', $framework->get_context());
        // Starting transaction.
        $transaction = $DB->start_delegated_transaction();

        try {
            // Get a uniq idnumber based on the origin framework.
            $idnumber = competency_framework::get_unused_idnumber($framework->get_idnumber());
            $framework->set_idnumber($idnumber);
            // Adding the suffix copy to the shortname.
            $framework->set_shortname(get_string('duplicateditemname', 'core_competency', $framework->get_shortname()));
            $framework->set_id(0);
            $framework = $framework->create();

            // Array that match the old competencies ids with the new one to use when copying related competencies.
            $frameworkcompetency = competency::get_framework_tree($id);
            $matchids = self::duplicate_competency_tree($framework->get_id(), $frameworkcompetency, 0, 0);

            // Copy the related competencies.
            $relcomps = related_competency::get_multiple_relations(array_keys($matchids));

            foreach ($relcomps as $relcomp) {
                $compid = $relcomp->get_competencyid();
                $relcompid = $relcomp->get_relatedcompetencyid();
                if (isset($matchids[$compid]) && isset($matchids[$relcompid])) {
                    $newcompid = $matchids[$compid]->get_id();
                    $newrelcompid = $matchids[$relcompid]->get_id();
                    if ($newcompid < $newrelcompid) {
                        $relcomp->set_competencyid($newcompid);
                        $relcomp->set_relatedcompetencyid($newrelcompid);
                    } else {
                        $relcomp->set_competencyid($newrelcompid);
                        $relcomp->set_relatedcompetencyid($newcompid);
                    }
                    $relcomp->set_id(0);
                    $relcomp->create();
                } else {
                    // Debugging message when there is no match found.
                    debugging('related competency id not found');
                }
            }

            // Setting rules on duplicated competencies.
            self::migrate_competency_tree_rules($frameworkcompetency, $matchids);

            $transaction->allow_commit();

        } catch (\Exception $e) {
            $transaction->rollback($e);
        }

        // Trigger a competency framework created event.
        \core\event\competency_framework_created::create_from_framework($framework)->trigger();

        return $framework;
    }

    /**
     * Delete a competency framework by id.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param int $id The record to delete. This will delete alot of related data - you better be sure.
     * @return boolean
     */
    public static function delete_framework($id) {
        global $DB;
        static::require_enabled();
        $framework = new competency_framework($id);
        require_capability('moodle/competency:competencymanage', $framework->get_context());

        $events = array();
        $competenciesid = competency::get_ids_by_frameworkid($id);
        $contextid = $framework->get_contextid();
        if (!competency::can_all_be_deleted($competenciesid)) {
            return false;
        }
        $transaction = $DB->start_delegated_transaction();
        try {
            if (!empty($competenciesid)) {
                // Delete competencies.
                competency::delete_by_frameworkid($id);

                // Delete the related competencies.
                related_competency::delete_multiple_relations($competenciesid);

                // Delete the evidences for competencies.
                user_evidence_competency::delete_by_competencyids($competenciesid);
            }

            // Create a competency framework deleted event.
            $event = \core\event\competency_framework_deleted::create_from_framework($framework);
            $result = $framework->delete();

            // Register the deleted events competencies.
            $events = \core\event\competency_deleted::create_multiple_from_competencyids($competenciesid, $contextid);

        } catch (\Exception $e) {
            $transaction->rollback($e);
        }

        // Commit the transaction.
        $transaction->allow_commit();

        // If all operations are successfull then trigger the delete event.
        $event->trigger();

        // Trigger deleted event competencies.
        foreach ($events as $event) {
            $event->trigger();
        }

        return $result;
    }

    /**
     * Update the details for a competency framework.
     *
     * Requires moodle/competency:competencymanage capability at the system context.
     *
     * @param stdClass $record The new details for the framework. Note - must contain an id that points to the framework to update.
     * @return boolean
     */
    public static function update_framework($record) {
        static::require_enabled();
        $framework = new competency_framework($record->id);

        // Check the permissions before update.
        require_capability('moodle/competency:competencymanage', $framework->get_context());

        // Account for different formats of taxonomies.
        $framework->from_record($record);
        if (isset($record->taxonomies)) {
            $framework->set_taxonomies($record->taxonomies);
        }

        // Trigger a competency framework updated event.
        \core\event\competency_framework_updated::create_from_framework($framework)->trigger();

        return $framework->update();
    }

    /**
     * Read a the details for a single competency framework and return a record.
     *
     * Requires moodle/competency:competencyview capability at the system context.
     *
     * @param int $id The id of the framework to read.
     * @return competency_framework
     */
    public static function read_framework($id) {
        static::require_enabled();
        $framework = new competency_framework($id);
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $framework->get_context())) {
            throw new required_capability_exception($framework->get_context(), 'moodle/competency:competencyview',
                'nopermissions', '');
        }
        return $framework;
    }

    /**
     * Logg the competency framework viewed event.
     *
     * @param competency_framework|int $frameworkorid The competency_framework object or competency framework id
     * @return bool
     */
    public static function competency_framework_viewed($frameworkorid) {
        static::require_enabled();
        $framework = $frameworkorid;
        if (!is_object($framework)) {
            $framework = new competency_framework($framework);
        }
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $framework->get_context())) {
            throw new required_capability_exception($framework->get_context(), 'moodle/competency:competencyview',
                'nopermissions', '');
        }
        \core\event\competency_framework_viewed::create_from_framework($framework)->trigger();
        return true;
    }

    /**
     * Logg the competency viewed event.
     *
     * @param competency|int $competencyorid The competency object or competency id
     * @return bool
     */
    public static function competency_viewed($competencyorid) {
        static::require_enabled();
        $competency = $competencyorid;
        if (!is_object($competency)) {
            $competency = new competency($competency);
        }

        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $competency->get_context())) {
            throw new required_capability_exception($competency->get_context(), 'moodle/competency:competencyview',
                'nopermissions', '');
        }

        \core\event\competency_viewed::create_from_competency($competency)->trigger();
        return true;
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires moodle/competency:competencyview capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @param bool $onlyvisible If true return only visible frameworks
     * @param string $query A string to use to filter down the frameworks.
     * @return array of competency_framework
     */
    public static function list_frameworks($sort, $order, $skip, $limit, $context, $includes = 'children',
                                           $onlyvisible = false, $query = '') {
        global $DB;
        static::require_enabled();

        // Get all the relevant contexts.
        $contexts = self::get_related_contexts($context, $includes,
            array('moodle/competency:competencyview', 'moodle/competency:competencymanage'));

        if (empty($contexts)) {
            throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // OK - all set.
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        $select = "contextid $insql";
        if ($onlyvisible) {
            $select .= " AND visible = :visible";
            $inparams['visible'] = 1;
        }

        if (!empty($query) || is_numeric($query)) {
            $sqlnamelike = $DB->sql_like('shortname', ':namelike', false);
            $sqlidnlike = $DB->sql_like('idnumber', ':idnlike', false);

            $select .= " AND ($sqlnamelike OR $sqlidnlike) ";
            $inparams['namelike'] = '%' . $DB->sql_like_escape($query) . '%';
            $inparams['idnlike'] = '%' . $DB->sql_like_escape($query) . '%';
        }

        return competency_framework::get_records_select($select, $inparams, $sort . ' ' . $order, '*', $skip, $limit);
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires moodle/competency:competencyview capability at the system context.
     *
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @return int
     */
    public static function count_frameworks($context, $includes) {
        global $DB;
        static::require_enabled();

        // Get all the relevant contexts.
        $contexts = self::get_related_contexts($context, $includes,
            array('moodle/competency:competencyview', 'moodle/competency:competencymanage'));

        if (empty($contexts)) {
            throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // OK - all set.
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        return competency_framework::count_records_select("contextid $insql", $inparams);
    }

    /**
     * Fetches all the relevant contexts.
     *
     * Note: This currently only supports system, category and user contexts. However user contexts
     * behave a bit differently and will fallback on the system context. This is what makes the most
     * sense because a user context does not have descendants, and only has system as a parent.
     *
     * @param context $context The context to start from.
     * @param string $includes Defines what other contexts to find.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @param array $hasanycapability Array of capabilities passed to {@link has_any_capability()} in each context.
     * @return context[] An array of contexts where keys are context IDs.
     */
    public static function get_related_contexts($context, $includes, array $hasanycapability = null) {
        global $DB;
        static::require_enabled();

        if (!in_array($includes, array('children', 'parents', 'self'))) {
            throw new coding_exception('Invalid parameter value for \'includes\'.');
        }

        // If context user swap it for the context_system.
        if ($context->contextlevel == CONTEXT_USER) {
            $context = context_system::instance();
        }

        $contexts = array($context->id => $context);

        if ($includes == 'children') {
            $params = array('coursecatlevel' => CONTEXT_COURSECAT, 'path' => $context->path . '/%');
            $pathlike = $DB->sql_like('path', ':path');
            $sql = "contextlevel = :coursecatlevel AND $pathlike";
            $rs = $DB->get_recordset_select('context', $sql, $params);
            foreach ($rs as $record) {
                $ctxid = $record->id;
                context_helper::preload_from_record($record);
                $contexts[$ctxid] = context::instance_by_id($ctxid);
            }
            $rs->close();

        } else if ($includes == 'parents') {
            $children = $context->get_parent_contexts();
            foreach ($children as $ctx) {
                $contexts[$ctx->id] = $ctx;
            }
        }

        // Filter according to the capabilities required.
        if (!empty($hasanycapability)) {
            foreach ($contexts as $key => $ctx) {
                if (!has_any_capability($hasanycapability, $ctx)) {
                    unset($contexts[$key]);
                }
            }
        }

        return $contexts;
    }

    /**
     * Count all the courses using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return int
     */
    public static function count_courses_using_competency($competencyid) {
        static::require_enabled();

        // OK - all set.
        $courses = course_competency::list_courses_min($competencyid);
        $count = 0;

        // Now check permissions on each course.
        foreach ($courses as $course) {
            if (!self::validate_course($course, false)) {
                continue;
            }

            $context = context_course::instance($course->id);
            $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
            if (!has_any_capability($capabilities, $context)) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    /**
     * List all the courses modules using a competency in a course.
     *
     * @param int $competencyid The id of the competency to check.
     * @param int $courseid The id of the course to check.
     * @return array[int] Array of course modules ids.
     */
    public static function list_course_modules_using_competency($competencyid, $courseid) {
        static::require_enabled();

        $result = array();
        self::validate_course($courseid);

        $coursecontext = context_course::instance($courseid);

        // We will not check each module - course permissions should be enough.
        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $coursecontext)) {
            throw new required_capability_exception($coursecontext, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        }

        $cmlist = course_module_competency::list_course_modules($competencyid, $courseid);
        foreach ($cmlist as $cmid) {
            if (self::validate_course_module($cmid, false)) {
                array_push($result, $cmid);
            }
        }

        return $result;
    }

    /**
     * List all the competencies linked to a course module.
     *
     * @param mixed $cmorid The course module, or its ID.
     * @return array[competency] Array of competency records.
     */
    public static function list_course_module_competencies_in_course_module($cmorid) {
        static::require_enabled();
        $cm = $cmorid;
        if (!is_object($cmorid)) {
            $cm = get_coursemodule_from_id('', $cmorid, 0, true, MUST_EXIST);
        }

        // Check the user have access to the course module.
        self::validate_course_module($cm);
        $context = context_module::instance($cm->id);

        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
            throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        }

        $result = array();

        $cmclist = course_module_competency::list_course_module_competencies($cm->id);
        foreach ($cmclist as $id => $cmc) {
            array_push($result, $cmc);
        }

        return $result;
    }

    /**
     * List all the courses using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return array[stdClass] Array of stdClass containing id and shortname.
     */
    public static function list_courses_using_competency($competencyid) {
        static::require_enabled();

        // OK - all set.
        $courses = course_competency::list_courses($competencyid);
        $result = array();

        // Now check permissions on each course.
        foreach ($courses as $id => $course) {
            $context = context_course::instance($course->id);
            $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
            if (!has_any_capability($capabilities, $context)) {
                unset($courses[$id]);
                continue;
            }
            if (!self::validate_course($course, false)) {
                unset($courses[$id]);
                continue;
            }
            array_push($result, $course);
        }

        return $result;
    }

    /**
     * Count the proficient competencies in a course for one user.
     *
     * @param int $courseid The id of the course to check.
     * @param int $userid The id of the user to check.
     * @return int
     */
    public static function count_proficient_competencies_in_course_for_user($courseid, $userid) {
        static::require_enabled();
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        }

        // OK - all set.
        return user_competency_course::count_proficient_competencies($courseid, $userid);
    }

    /**
     * Count all the competencies in a course.
     *
     * @param int $courseid The id of the course to check.
     * @return int
     */
    public static function count_competencies_in_course($courseid) {
        static::require_enabled();
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        }

        // OK - all set.
        return course_competency::count_competencies($courseid);
    }

    /**
     * List the competencies associated to a course.
     *
     * @param mixed $courseorid The course, or its ID.
     * @return array( array(
     *                   'competency' => \core_competency\competency,
     *                   'coursecompetency' => \core_competency\course_competency
     *              ))
     */
    public static function list_course_competencies($courseorid) {
        static::require_enabled();
        $course = $courseorid;
        if (!is_object($courseorid)) {
            $course = get_course($courseorid);
        }

        // Check the user have access to the course.
        self::validate_course($course);
        $context = context_course::instance($course->id);

        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
            throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        }

        $result = array();

        // TODO We could improve the performance of this into one single query.
        $coursecompetencies = course_competency::list_course_competencies($course->id);
        $competencies = course_competency::list_competencies($course->id);

        // Build the return values.
        foreach ($coursecompetencies as $key => $coursecompetency) {
            $result[] = array(
                'competency' => $competencies[$coursecompetency->get_competencyid()],
                'coursecompetency' => $coursecompetency
            );
        }

        return $result;
    }

    /**
     * Get a user competency.
     *
     * @param int $userid The user ID.
     * @param int $competencyid The competency ID.
     * @return user_competency
     */
    public static function get_user_competency($userid, $competencyid) {
        static::require_enabled();
        $existing = user_competency::get_multiple($userid, array($competencyid));
        $uc = array_pop($existing);

        if (!$uc) {
            $uc = user_competency::create_relation($userid, $competencyid);
            $uc->create();
        }

        if (!$uc->can_read()) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }
        return $uc;
    }

    /**
     * Get a user competency by ID.
     *
     * @param int $usercompetencyid The user competency ID.
     * @return user_competency
     */
    public static function get_user_competency_by_id($usercompetencyid) {
        static::require_enabled();
        $uc = new user_competency($usercompetencyid);
        if (!$uc->can_read()) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }
        return $uc;
    }

    /**
     * List the competencies associated to a course module.
     *
     * @param mixed $cmorid The course module, or its ID.
     * @return array( array(
     *                   'competency' => \core_competency\competency,
     *                   'coursemodulecompetency' => \core_competency\course_module_competency
     *              ))
     */
    public static function list_course_module_competencies($cmorid) {
        static::require_enabled();
        $cm = $cmorid;
        if (!is_object($cmorid)) {
            $cm = get_coursemodule_from_id('', $cmorid, 0, true, MUST_EXIST);
        }

        // Check the user have access to the course module.
        self::validate_course_module($cm);
        $context = context_module::instance($cm->id);

        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
            throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        }

        $result = array();

        // TODO We could improve the performance of this into one single query.
        $coursemodulecompetencies = course_competency::list_course_module_competencies($cm->id);
        $competencies = course_module_competency::list_competencies($cm->id);

        // Build the return values.
        foreach ($coursemodulecompetencies as $key => $coursemodulecompetency) {
            $result[] = array(
                'competency' => $competencies[$coursemodulecompetency->get_competencyid()],
                'coursemodulecompetency' => $coursemodulecompetency
            );
        }

        return $result;
    }

    /**
     * Get a user competency in a course.
     *
     * @param int $courseid The id of the course to check.
     * @param int $userid The id of the course to check.
     * @param int $competencyid The id of the competency.
     * @return user_competency_course
     */
    public static function get_user_competency_in_course($courseid, $userid, $competencyid) {
        static::require_enabled();
        // First we do a permissions check.
        $context = context_course::instance($courseid);

        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
            throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        } else if (!user_competency::can_read_user_in_course($userid, $courseid)) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        }

        // This will throw an exception if the competency does not belong to the course.
        $competency = course_competency::get_competency($courseid, $competencyid);

        $params = array('courseid' => $courseid, 'userid' => $userid, 'competencyid' => $competencyid);
        $exists = user_competency_course::get_record($params);
        // Create missing.
        if ($exists) {
            $ucc = $exists;
        } else {
            $ucc = user_competency_course::create_relation($userid, $competency->get_id(), $courseid);
            $ucc->create();
        }

        return $ucc;
    }

    /**
     * List all the user competencies in a course.
     *
     * @param int $courseid The id of the course to check.
     * @param int $userid The id of the course to check.
     * @return array of user_competency_course objects
     */
    public static function list_user_competencies_in_course($courseid, $userid) {
        static::require_enabled();
        // First we do a permissions check.
        $context = context_course::instance($courseid);
        $onlyvisible = 1;

        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
            throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        } else if (!user_competency::can_read_user_in_course($userid, $courseid)) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        }

        // OK - all set.
        $competencylist = course_competency::list_competencies($courseid, false);

        $existing = user_competency_course::get_multiple($userid, $courseid, $competencylist);
        // Create missing.
        $orderedusercompetencycourses = array();

        $somemissing = false;
        foreach ($competencylist as $coursecompetency) {
            $found = false;
            foreach ($existing as $usercompetencycourse) {
                if ($usercompetencycourse->get_competencyid() == $coursecompetency->get_id()) {
                    $found = true;
                    $orderedusercompetencycourses[$usercompetencycourse->get_id()] = $usercompetencycourse;
                    break;
                }
            }
            if (!$found) {
                $ucc = user_competency_course::create_relation($userid, $coursecompetency->get_id(), $courseid);
                $ucc->create();
                $orderedusercompetencycourses[$ucc->get_id()] = $ucc;
            }
        }

        return $orderedusercompetencycourses;
    }

    /**
     * List the user competencies to review.
     *
     * The method returns values in this format:
     *
     * array(
     *     'competencies' => array(
     *         (stdClass)(
     *             'usercompetency' => (user_competency),
     *             'competency' => (competency),
     *             'user' => (user)
     *         )
     *     ),
     *     'count' => (int)
     * )
     *
     * @param int $skip The number of records to skip.
     * @param int $limit The number of results to return.
     * @param int $userid The user we're getting the competencies to review for.
     * @return array Containing the keys 'count', and 'competencies'. The 'competencies' key contains an object
     *               which contains 'competency', 'usercompetency' and 'user'.
     */
    public static function list_user_competencies_to_review($skip = 0, $limit = 50, $userid = null) {
        global $DB, $USER;
        static::require_enabled();
        if ($userid === null) {
            $userid = $USER->id;
        }

        $capability = 'moodle/competency:usercompetencyreview';
        $ucfields = user_competency::get_sql_fields('uc', 'uc_');
        $compfields = competency::get_sql_fields('c', 'c_');
        $usercols = array('id') + get_user_fieldnames();
        $userfields = array();
        foreach ($usercols as $field) {
            $userfields[] = "u." . $field . " AS usr_" . $field;
        }
        $userfields = implode(',', $userfields);

        $select = "SELECT $ucfields, $compfields, $userfields";
        $countselect = "SELECT COUNT('x')";
        $sql = "  FROM {" . user_competency::TABLE . "} uc
                  JOIN {" . competency::TABLE . "} c
                    ON c.id = uc.competencyid
                  JOIN {user} u
                    ON u.id = uc.userid
                 WHERE (uc.status = :waitingforreview
                    OR (uc.status = :inreview AND uc.reviewerid = :reviewerid))";
        $ordersql = " ORDER BY c.shortname ASC";
        $params = array(
            'inreview' => user_competency::STATUS_IN_REVIEW,
            'reviewerid' => $userid,
            'waitingforreview' => user_competency::STATUS_WAITING_FOR_REVIEW,
        );
        $countsql = $countselect . $sql;

        // Primary check to avoid the hard work of getting the users in which the user has permission.
        $count = $DB->count_records_sql($countselect . $sql, $params);
        if ($count < 1) {
            return array('count' => 0, 'competencies' => array());
        }

        // TODO MDL-52243 Use core function.
        list($insql, $inparams) = self::filter_users_with_capability_on_user_context_sql(
            $capability, $userid, SQL_PARAMS_NAMED);
        $params += $inparams;
        $countsql = $countselect . $sql . " AND uc.userid $insql";
        $getsql = $select . $sql . " AND uc.userid $insql " . $ordersql;

        // Extracting the results.
        $competencies = array();
        $records = $DB->get_recordset_sql($getsql, $params, $skip, $limit);
        foreach ($records as $record) {
            $objects = (object) array(
                'usercompetency' => new user_competency(0, user_competency::extract_record($record, 'uc_')),
                'competency' => new competency(0, competency::extract_record($record, 'c_')),
                'user' => persistent::extract_record($record, 'usr_'),
            );
            $competencies[] = $objects;
        }
        $records->close();

        return array(
            'count' => $DB->count_records_sql($countsql, $params),
            'competencies' => $competencies
        );
    }

    /**
     * Add a competency to this course module.
     *
     * @param mixed $cmorid The course module, or id of the course module
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_course_module($cmorid, $competencyid) {
        static::require_enabled();
        $cm = $cmorid;
        if (!is_object($cmorid)) {
            $cm = get_coursemodule_from_id('', $cmorid, 0, true, MUST_EXIST);
        }

        // Check the user have access to the course module.
        self::validate_course_module($cm);

        // First we do a permissions check.
        $context = context_module::instance($cm->id);

        require_capability('moodle/competency:coursecompetencymanage', $context);

        // Check that the competency belongs to the course.
        $exists = course_competency::get_records(array('courseid' => $cm->course, 'competencyid' => $competencyid));
        if (!$exists) {
            throw new coding_exception('Cannot add a competency to a module if it does not belong to the course');
        }

        $record = new stdClass();
        $record->cmid = $cm->id;
        $record->competencyid = $competencyid;

        $coursemodulecompetency = new course_module_competency();
        $exists = $coursemodulecompetency->get_records(array('cmid' => $cm->id, 'competencyid' => $competencyid));
        if (!$exists) {
            $coursemodulecompetency->from_record($record);
            if ($coursemodulecompetency->create()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove a competency from this course module.
     *
     * @param mixed $cmorid The course module, or id of the course module
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function remove_competency_from_course_module($cmorid, $competencyid) {
        static::require_enabled();
        $cm = $cmorid;
        if (!is_object($cmorid)) {
            $cm = get_coursemodule_from_id('', $cmorid, 0, true, MUST_EXIST);
        }
        // Check the user have access to the course module.
        self::validate_course_module($cm);

        // First we do a permissions check.
        $context = context_module::instance($cm->id);

        require_capability('moodle/competency:coursecompetencymanage', $context);

        $record = new stdClass();
        $record->cmid = $cm->id;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);
        $exists = course_module_competency::get_record(array('cmid' => $cm->id, 'competencyid' => $competencyid));
        if ($exists) {
            return $exists->delete();
        }
        return false;
    }

    /**
     * Move the course module competency up or down in the display list.
     *
     * Requires moodle/competency:coursecompetencymanage capability at the course module context.
     *
     * @param mixed $cmorid The course module, or id of the course module
     * @param int $competencyidfrom The id of the competency we are moving.
     * @param int $competencyidto The id of the competency we are moving to.
     * @return boolean
     */
    public static function reorder_course_module_competency($cmorid, $competencyidfrom, $competencyidto) {
        static::require_enabled();
        $cm = $cmorid;
        if (!is_object($cmorid)) {
            $cm = get_coursemodule_from_id('', $cmorid, 0, true, MUST_EXIST);
        }
        // Check the user have access to the course module.
        self::validate_course_module($cm);

        // First we do a permissions check.
        $context = context_module::instance($cm->id);

        require_capability('moodle/competency:coursecompetencymanage', $context);

        $down = true;
        $matches = course_module_competency::get_records(array('cmid' => $cm->id, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
             throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = course_module_competency::get_records(array('cmid' => $cm->id, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
             throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = course_module_competency::get_records(array('cmid' => $cm->id), 'sortorder', 'ASC', 0, 0);

        if ($competencyfrom->get_sortorder() > $competencyto->get_sortorder()) {
            // We are moving up, so put it before the "to" item.
            $down = false;
        }

        foreach ($all as $id => $coursemodulecompetency) {
            $sort = $coursemodulecompetency->get_sortorder();
            if ($down && $sort > $competencyfrom->get_sortorder() && $sort <= $competencyto->get_sortorder()) {
                $coursemodulecompetency->set_sortorder($coursemodulecompetency->get_sortorder() - 1);
                $coursemodulecompetency->update();
            } else if (!$down && $sort >= $competencyto->get_sortorder() && $sort < $competencyfrom->get_sortorder()) {
                $coursemodulecompetency->set_sortorder($coursemodulecompetency->get_sortorder() + 1);
                $coursemodulecompetency->update();
            }
        }
        $competencyfrom->set_sortorder($competencyto->get_sortorder());
        return $competencyfrom->update();
    }

    /**
     * Update ruleoutcome value for a course module competency.
     *
     * @param int|course_module_competency $coursemodulecompetencyorid The course_module_competency, or its ID.
     * @param int $ruleoutcome The value of ruleoutcome.
     * @return bool True on success.
     */
    public static function set_course_module_competency_ruleoutcome($coursemodulecompetencyorid, $ruleoutcome) {
        static::require_enabled();
        $coursemodulecompetency = $coursemodulecompetencyorid;
        if (!is_object($coursemodulecompetency)) {
            $coursemodulecompetency = new course_module_competency($coursemodulecompetencyorid);
        }

        $cm = get_coursemodule_from_id('', $coursemodulecompetency->get_cmid(), 0, true, MUST_EXIST);

        self::validate_course_module($cm);
        $context = context_module::instance($cm->id);

        require_capability('moodle/competency:coursecompetencymanage', $context);

        $coursemodulecompetency->set_ruleoutcome($ruleoutcome);
        return $coursemodulecompetency->update();
    }

    /**
     * Add a competency to this course.
     *
     * @param int $courseid The id of the course
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_course($courseid, $competencyid) {
        static::require_enabled();
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('moodle/competency:coursecompetencymanage', $context);

        $record = new stdClass();
        $record->courseid = $courseid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);

        // Can not add a competency that belong to a hidden framework.
        if ($competency->get_framework()->get_visible() == false) {
            throw new coding_exception('A competency belonging to hidden framework can not be linked to course');
        }

        $coursecompetency = new course_competency();
        $exists = $coursecompetency->get_records(array('courseid' => $courseid, 'competencyid' => $competencyid));
        if (!$exists) {
            $coursecompetency->from_record($record);
            if ($coursecompetency->create()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove a competency from this course.
     *
     * @param int $courseid The id of the course
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function remove_competency_from_course($courseid, $competencyid) {
        static::require_enabled();
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('moodle/competency:coursecompetencymanage', $context);

        $record = new stdClass();
        $record->courseid = $courseid;
        $record->competencyid = $competencyid;

        $coursecompetency = new course_competency();
        $exists = course_competency::get_record(array('courseid' => $courseid, 'competencyid' => $competencyid));
        if ($exists) {
            // Delete all course_module_competencies for this competency in this course.
            $cmcs = course_module_competency::get_records_by_competencyid_in_course($competencyid, $courseid);
            foreach ($cmcs as $cmc) {
                $cmc->delete();
            }
            return $exists->delete();
        }
        return false;
    }

    /**
     * Move the course competency up or down in the display list.
     *
     * Requires moodle/competency:coursecompetencymanage capability at the course context.
     *
     * @param int $courseid The course
     * @param int $competencyidfrom The id of the competency we are moving.
     * @param int $competencyidto The id of the competency we are moving to.
     * @return boolean
     */
    public static function reorder_course_competency($courseid, $competencyidfrom, $competencyidto) {
        static::require_enabled();
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('moodle/competency:coursecompetencymanage', $context);

        $down = true;
        $coursecompetency = new course_competency();
        $matches = $coursecompetency->get_records(array('courseid' => $courseid, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
             throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = $coursecompetency->get_records(array('courseid' => $courseid, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
             throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = $coursecompetency->get_records(array('courseid' => $courseid), 'sortorder', 'ASC', 0, 0);

        if ($competencyfrom->get_sortorder() > $competencyto->get_sortorder()) {
            // We are moving up, so put it before the "to" item.
            $down = false;
        }

        foreach ($all as $id => $coursecompetency) {
            $sort = $coursecompetency->get_sortorder();
            if ($down && $sort > $competencyfrom->get_sortorder() && $sort <= $competencyto->get_sortorder()) {
                $coursecompetency->set_sortorder($coursecompetency->get_sortorder() - 1);
                $coursecompetency->update();
            } else if (!$down && $sort >= $competencyto->get_sortorder() && $sort < $competencyfrom->get_sortorder()) {
                $coursecompetency->set_sortorder($coursecompetency->get_sortorder() + 1);
                $coursecompetency->update();
            }
        }
        $competencyfrom->set_sortorder($competencyto->get_sortorder());
        return $competencyfrom->update();
    }

    /**
     * Update ruleoutcome value for a course competency.
     *
     * @param int|course_competency $coursecompetencyorid The course_competency, or its ID.
     * @param int $ruleoutcome The value of ruleoutcome.
     * @return bool True on success.
     */
    public static function set_course_competency_ruleoutcome($coursecompetencyorid, $ruleoutcome) {
        static::require_enabled();
        $coursecompetency = $coursecompetencyorid;
        if (!is_object($coursecompetency)) {
            $coursecompetency = new course_competency($coursecompetencyorid);
        }

        $courseid = $coursecompetency->get_courseid();
        self::validate_course($courseid);
        $coursecontext = context_course::instance($courseid);

        require_capability('moodle/competency:coursecompetencymanage', $coursecontext);

        $coursecompetency->set_ruleoutcome($ruleoutcome);
        return $coursecompetency->update();
    }

    /**
     * Create a learning plan template from a record containing all the data for the class.
     *
     * Requires moodle/competency:templatemanage capability.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     * @return template
     */
    public static function create_template(stdClass $record) {
        static::require_enabled();
        $template = new template(0, $record);

        // First we do a permissions check.
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templatemanage',
                'nopermissions', '');
        }

        // OK - all set.
        $template = $template->create();

        // Trigger a template created event.
        \core\event\competency_template_created::create_from_template($template)->trigger();

        return $template;
    }

    /**
     * Duplicate a learning plan template.
     *
     * Requires moodle/competency:templatemanage capability at the template context.
     *
     * @param int $id the template id.
     * @return template
     */
    public static function duplicate_template($id) {
        static::require_enabled();
        $template = new template($id);

        // First we do a permissions check.
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templatemanage',
                'nopermissions', '');
        }

        // OK - all set.
        $competencies = template_competency::list_competencies($id, false);

        // Adding the suffix copy.
        $template->set_shortname(get_string('duplicateditemname', 'core_competency', $template->get_shortname()));
        $template->set_id(0);

        $duplicatedtemplate = $template->create();

        // Associate each competency for the duplicated template.
        foreach ($competencies as $competency) {
            self::add_competency_to_template($duplicatedtemplate->get_id(), $competency->get_id());
        }

        // Trigger a template created event.
        \core\event\competency_template_created::create_from_template($duplicatedtemplate)->trigger();

        return $duplicatedtemplate;
    }

    /**
     * Delete a learning plan template by id.
     * If the learning plan template has associated cohorts they will be deleted.
     *
     * Requires moodle/competency:templatemanage capability.
     *
     * @param int $id The record to delete.
     * @param boolean $deleteplans True to delete plans associaated to template, false to unlink them.
     * @return boolean
     */
    public static function delete_template($id, $deleteplans = true) {
        global $DB;
        static::require_enabled();
        $template = new template($id);

        // First we do a permissions check.
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templatemanage',
                'nopermissions', '');
        }

        $transaction = $DB->start_delegated_transaction();
        $success = true;

        // Check if there are cohorts associated.
        $templatecohorts = template_cohort::get_relations_by_templateid($template->get_id());
        foreach ($templatecohorts as $templatecohort) {
            $success = $templatecohort->delete();
            if (!$success) {
                break;
            }
        }

        // Still OK, delete or unlink the plans from the template.
        if ($success) {
            $plans = plan::get_records(array('templateid' => $template->get_id()));
            foreach ($plans as $plan) {
                $success = $deleteplans ? self::delete_plan($plan->get_id()) : self::unlink_plan_from_template($plan);
                if (!$success) {
                    break;
                }
            }
        }

        // Still OK, delete the template comptencies.
        if ($success) {
            $success = template_competency::delete_by_templateid($template->get_id());
        }

        // OK - all set.
        if ($success) {
            // Create a template deleted event.
            $event = \core\event\competency_template_deleted::create_from_template($template);

            $success = $template->delete();
        }

        if ($success) {
            // Trigger a template deleted event.
            $event->trigger();

            // Commit the transaction.
            $transaction->allow_commit();
        } else {
            $transaction->rollback(new moodle_exception('Error while deleting the template.'));
        }

        return $success;
    }

    /**
     * Update the details for a learning plan template.
     *
     * Requires moodle/competency:templatemanage capability.
     *
     * @param stdClass $record The new details for the template. Note - must contain an id that points to the template to update.
     * @return boolean
     */
    public static function update_template($record) {
        global $DB;
        static::require_enabled();
        $template = new template($record->id);

        // First we do a permissions check.
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templatemanage',
                'nopermissions', '');

        } else if (isset($record->contextid) && $record->contextid != $template->get_contextid()) {
            // We can never change the context of a template.
            throw new coding_exception('Changing the context of an existing tempalte is forbidden.');

        }

        $updateplans = false;
        $before = $template->to_record();

        $template->from_record($record);
        $after = $template->to_record();

        // Should we update the related plans?
        if ($before->duedate != $after->duedate ||
                $before->shortname != $after->shortname ||
                $before->description != $after->description ||
                $before->descriptionformat != $after->descriptionformat) {
            $updateplans = true;
        }

        $transaction = $DB->start_delegated_transaction();
        $success = $template->update();

        if (!$success) {
            $transaction->rollback(new moodle_exception('Error while updating the template.'));
            return $success;
        }

        // Trigger a template updated event.
        \core\event\competency_template_updated::create_from_template($template)->trigger();

        if ($updateplans) {
            plan::update_multiple_from_template($template);
        }

        $transaction->allow_commit();

        return $success;
    }

    /**
     * Read a the details for a single learning plan template and return a record.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param int $id The id of the template to read.
     * @return template
     */
    public static function read_template($id) {
        static::require_enabled();
        $template = new template($id);
        $context = $template->get_context();

        // First we do a permissions check.
        if (!$template->can_read()) {
             throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        // OK - all set.
        return $template;
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @param bool $onlyvisible If should list only visible templates
     * @return array of competency_framework
     */
    public static function list_templates($sort, $order, $skip, $limit, $context, $includes = 'children', $onlyvisible = false) {
        global $DB;
        static::require_enabled();

        // Get all the relevant contexts.
        $contexts = self::get_related_contexts($context, $includes,
            array('moodle/competency:templateview', 'moodle/competency:templatemanage'));

        // First we do a permissions check.
        if (empty($contexts)) {
             throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
        }

        // Make the order by.
        $orderby = '';
        if (!empty($sort)) {
            $orderby = $sort . ' ' . $order;
        }

        // OK - all set.
        $template = new template();
        list($insql, $params) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        $select = "contextid $insql";

        if ($onlyvisible) {
            $select .= " AND visible = :visible";
            $params['visible'] = 1;
        }
        return $template->get_records_select($select, $params, $orderby, '*', $skip, $limit);
    }

    /**
     * Perform a search based on the provided filters and return how many results there are.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @return int
     */
    public static function count_templates($context, $includes) {
        global $DB;
        static::require_enabled();

        // First we do a permissions check.
        $contexts = self::get_related_contexts($context, $includes,
            array('moodle/competency:templateview', 'moodle/competency:templatemanage'));

        if (empty($contexts)) {
             throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
        }

        // OK - all set.
        $template = new template();
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        return $template->count_records_select("contextid $insql", $inparams);
    }

    /**
     * Count all the templates using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return int
     */
    public static function count_templates_using_competency($competencyid) {
        static::require_enabled();
        // First we do a permissions check.
        $context = context_system::instance();
        $onlyvisible = 1;

        $capabilities = array('moodle/competency:templateview', 'moodle/competency:templatemanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
        }

        if (has_capability('moodle/competency:templatemanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        return template_competency::count_templates($competencyid, $onlyvisible);
    }

    /**
     * List all the learning plan templatesd using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return array[stdClass] Array of stdClass containing id and shortname.
     */
    public static function list_templates_using_competency($competencyid) {
        static::require_enabled();
        // First we do a permissions check.
        $context = context_system::instance();
        $onlyvisible = 1;

        $capabilities = array('moodle/competency:templateview', 'moodle/competency:templatemanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
        }

        if (has_capability('moodle/competency:templatemanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        return template_competency::list_templates($competencyid, $onlyvisible);

    }

    /**
     * Count all the competencies in a learning plan template.
     *
     * @param  template|int $templateorid The template or its ID.
     * @return int
     */
    public static function count_competencies_in_template($templateorid) {
        static::require_enabled();
        // First we do a permissions check.
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        // OK - all set.
        return template_competency::count_competencies($template->get_id());
    }

    /**
     * Count all the competencies in a learning plan template with no linked courses.
     *
     * @param  template|int $templateorid The template or its ID.
     * @return int
     */
    public static function count_competencies_in_template_with_no_courses($templateorid) {
        // First we do a permissions check.
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        // OK - all set.
        return template_competency::count_competencies_with_no_courses($template->get_id());
    }

    /**
     * List all the competencies in a template.
     *
     * @param  template|int $templateorid The template or its ID.
     * @return array of competencies
     */
    public static function list_competencies_in_template($templateorid) {
        static::require_enabled();
        // First we do a permissions check.
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        // OK - all set.
        return template_competency::list_competencies($template->get_id());
    }

    /**
     * Add a competency to this template.
     *
     * @param int $templateid The id of the template
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_template($templateid, $competencyid) {
        static::require_enabled();
        // First we do a permissions check.
        $template = new template($templateid);
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templatemanage',
                'nopermissions', '');
        }

        $record = new stdClass();
        $record->templateid = $templateid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);

        // Can not add a competency that belong to a hidden framework.
        if ($competency->get_framework()->get_visible() == false) {
            throw new coding_exception('A competency belonging to hidden framework can not be added');
        }

        $exists = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyid));
        if (!$exists) {
            $templatecompetency = new template_competency(0, $record);
            $templatecompetency->create();
            return true;
        }
        return false;
    }

    /**
     * Remove a competency from this template.
     *
     * @param int $templateid The id of the template
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function remove_competency_from_template($templateid, $competencyid) {
        static::require_enabled();
        // First we do a permissions check.
        $template = new template($templateid);
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templatemanage',
                'nopermissions', '');
        }

        $record = new stdClass();
        $record->templateid = $templateid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);

        $exists = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyid));
        if ($exists) {
            $link = array_pop($exists);
            return $link->delete();
        }
        return false;
    }

    /**
     * Move the template competency up or down in the display list.
     *
     * Requires moodle/competency:templatemanage capability at the system context.
     *
     * @param int $templateid The template id
     * @param int $competencyidfrom The id of the competency we are moving.
     * @param int $competencyidto The id of the competency we are moving to.
     * @return boolean
     */
    public static function reorder_template_competency($templateid, $competencyidfrom, $competencyidto) {
        static::require_enabled();
        // First we do a permissions check.
        $context = context_system::instance();

        require_capability('moodle/competency:templatemanage', $context);

        $down = true;
        $matches = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = template_competency::get_records(array('templateid' => $templateid), 'sortorder', 'ASC', 0, 0);

        if ($competencyfrom->get_sortorder() > $competencyto->get_sortorder()) {
            // We are moving up, so put it before the "to" item.
            $down = false;
        }

        foreach ($all as $id => $templatecompetency) {
            $sort = $templatecompetency->get_sortorder();
            if ($down && $sort > $competencyfrom->get_sortorder() && $sort <= $competencyto->get_sortorder()) {
                $templatecompetency->set_sortorder($templatecompetency->get_sortorder() - 1);
                $templatecompetency->update();
            } else if (!$down && $sort >= $competencyto->get_sortorder() && $sort < $competencyfrom->get_sortorder()) {
                $templatecompetency->set_sortorder($templatecompetency->get_sortorder() + 1);
                $templatecompetency->update();
            }
        }
        $competencyfrom->set_sortorder($competencyto->get_sortorder());
        return $competencyfrom->update();
    }

    /**
     * Create a relation between a template and a cohort.
     *
     * This silently ignores when the relation already existed.
     *
     * @param  template|int $templateorid The template or its ID.
     * @param  stdClass|int $cohortorid   The cohort ot its ID.
     * @return template_cohort
     */
    public static function create_template_cohort($templateorid, $cohortorid) {
        global $DB;
        static::require_enabled();

        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }
        require_capability('moodle/competency:templatemanage', $template->get_context());

        $cohort = $cohortorid;
        if (!is_object($cohort)) {
            $cohort = $DB->get_record('cohort', array('id' => $cohort), '*', MUST_EXIST);
        }

        // Replicate logic in cohort_can_view_cohort() because we can't use it directly as we don't have a course context.
        $cohortcontext = context::instance_by_id($cohort->contextid);
        if (!$cohort->visible && !has_capability('moodle/cohort:view', $cohortcontext)) {
            throw new required_capability_exception($cohortcontext, 'moodle/cohort:view', 'nopermissions', '');
        }

        $tplcohort = template_cohort::get_relation($template->get_id(), $cohort->id);
        if (!$tplcohort->get_id()) {
            $tplcohort->create();
        }

        return $tplcohort;
    }

    /**
     * Remove a relation between a template and a cohort.
     *
     * @param  template|int $templateorid The template or its ID.
     * @param  stdClass|int $cohortorid   The cohort ot its ID.
     * @return boolean True on success or when the relation did not exist.
     */
    public static function delete_template_cohort($templateorid, $cohortorid) {
        global $DB;
        static::require_enabled();

        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }
        require_capability('moodle/competency:templatemanage', $template->get_context());

        $cohort = $cohortorid;
        if (!is_object($cohort)) {
            $cohort = $DB->get_record('cohort', array('id' => $cohort), '*', MUST_EXIST);
        }

        $tplcohort = template_cohort::get_relation($template->get_id(), $cohort->id);
        if (!$tplcohort->get_id()) {
            return true;
        }

        return $tplcohort->delete();
    }

    /**
     * Lists user plans.
     *
     * @param int $userid
     * @return \core_competency\plan[]
     */
    public static function list_user_plans($userid) {
        global $DB, $USER;
        static::require_enabled();
        $select = 'userid = :userid';
        $params = array('userid' => $userid);
        $context = context_user::instance($userid);

        // Check that we can read something here.
        if (!plan::can_read_user($userid) && !plan::can_read_user_draft($userid)) {
            throw new required_capability_exception($context, 'moodle/competency:planview', 'nopermissions', '');
        }

        // The user cannot view the drafts.
        if (!plan::can_read_user_draft($userid)) {
            list($insql, $inparams) = $DB->get_in_or_equal(plan::get_draft_statuses(), SQL_PARAMS_NAMED, 'param', false);
            $select .= " AND status $insql";
            $params += $inparams;
        }
        // The user cannot view the non-drafts.
        if (!plan::can_read_user($userid)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array(plan::STATUS_ACTIVE, plan::STATUS_COMPLETE),
                SQL_PARAMS_NAMED, 'param', false);
            $select .= " AND status $insql";
            $params += $inparams;
        }

        return plan::get_records_select($select, $params, 'name ASC');
    }

    /**
     * List the plans to review.
     *
     * The method returns values in this format:
     *
     * array(
     *     'plans' => array(
     *         (stdClass)(
     *             'plan' => (plan),
     *             'template' => (template),
     *             'owner' => (stdClass)
     *         )
     *     ),
     *     'count' => (int)
     * )
     *
     * @param int $skip The number of records to skip.
     * @param int $limit The number of results to return.
     * @param int $userid The user we're getting the plans to review for.
     * @return array Containing the keys 'count', and 'plans'. The 'plans' key contains an object
     *               which contains 'plan', 'template' and 'owner'.
     */
    public static function list_plans_to_review($skip = 0, $limit = 100, $userid = null) {
        global $DB, $USER;
        static::require_enabled();

        if ($userid === null) {
            $userid = $USER->id;
        }

        $planfields = plan::get_sql_fields('p', 'plan_');
        $tplfields = template::get_sql_fields('t', 'tpl_');
        $usercols = array('id') + get_user_fieldnames();
        $userfields = array();
        foreach ($usercols as $field) {
            $userfields[] = "u." . $field . " AS usr_" . $field;
        }
        $userfields = implode(',', $userfields);

        $select = "SELECT $planfields, $tplfields, $userfields";
        $countselect = "SELECT COUNT('x')";

        $sql = "  FROM {" . plan::TABLE . "} p
                  JOIN {user} u
                    ON u.id = p.userid
             LEFT JOIN {" . template::TABLE . "} t
                    ON t.id = p.templateid
                 WHERE (p.status = :waitingforreview
                    OR (p.status = :inreview AND p.reviewerid = :reviewerid))
                   AND p.userid != :userid";

        $params = array(
            'waitingforreview' => plan::STATUS_WAITING_FOR_REVIEW,
            'inreview' => plan::STATUS_IN_REVIEW,
            'reviewerid' => $userid,
            'userid' => $userid
        );

        // Primary check to avoid the hard work of getting the users in which the user has permission.
        $count = $DB->count_records_sql($countselect . $sql, $params);
        if ($count < 1) {
            return array('count' => 0, 'plans' => array());
        }

        // TODO MDL-52243 Use core function.
        list($insql, $inparams) = self::filter_users_with_capability_on_user_context_sql('moodle/competency:planreview',
            $userid, SQL_PARAMS_NAMED);
        $sql .= " AND p.userid $insql";
        $params += $inparams;

        // Order by ID just to have some ordering in place.
        $ordersql = " ORDER BY p.id ASC";

        $plans = array();
        $records = $DB->get_recordset_sql($select . $sql . $ordersql, $params, $skip, $limit);
        foreach ($records as $record) {
            $plan = new plan(0, plan::extract_record($record, 'plan_'));
            $template = null;

            if ($plan->is_based_on_template()) {
                $template = new template(0, template::extract_record($record, 'tpl_'));
            }

            $plans[] = (object) array(
                'plan' => $plan,
                'template' => $template,
                'owner' => persistent::extract_record($record, 'usr_'),
            );
        }
        $records->close();

        return array(
            'count' => $DB->count_records_sql($countselect . $sql, $params),
            'plans' => $plans
        );
    }

    /**
     * Creates a learning plan based on the provided data.
     *
     * @param stdClass $record
     * @return \core_competency\plan
     */
    public static function create_plan(stdClass $record) {
        global $USER;
        static::require_enabled();
        $plan = new plan(0, $record);

        if ($plan->is_based_on_template()) {
            throw new coding_exception('To create a plan from a template use api::create_plan_from_template().');
        } else if ($plan->get_status() == plan::STATUS_COMPLETE) {
            throw new coding_exception('A plan cannot be created as complete.');
        }

        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $plan->create();

        // Trigger created event.
        \core\event\competency_plan_created::create_from_plan($plan)->trigger();
        return $plan;
    }

    /**
     * Create a learning plan from a template.
     *
     * @param  mixed $templateorid The template object or ID.
     * @param  int $userid
     * @return false|\core_competency\plan Returns false when the plan already exists.
     */
    public static function create_plan_from_template($templateorid, $userid) {
        static::require_enabled();
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // The user must be able to view the template to use it as a base for a plan.
        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }
        // Can not create plan from a hidden template.
        if ($template->get_visible() == false) {
            throw new coding_exception('A plan can not be created from a hidden template');
        }

        // Convert the template to a plan.
        $record = $template->to_record();
        $record->templateid = $record->id;
        $record->userid = $userid;
        $record->name = $record->shortname;
        $record->status = plan::STATUS_ACTIVE;

        unset($record->id);
        unset($record->timecreated);
        unset($record->timemodified);
        unset($record->usermodified);

        // Remove extra keys.
        $properties = plan::properties_definition();
        foreach ($record as $key => $value) {
            if (!array_key_exists($key, $properties)) {
                unset($record->$key);
            }
        }

        $plan = new plan(0, $record);
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage',
                'nopermissions', '');
        }

        // We first apply the permission checks as we wouldn't want to leak information by returning early that
        // the plan already exists.
        if (plan::record_exists_select('templateid = :templateid AND userid = :userid', array(
                'templateid' => $template->get_id(), 'userid' => $userid))) {
            return false;
        }

        $plan->create();

        // Trigger created event.
        \core\event\competency_plan_created::create_from_plan($plan)->trigger();
        return $plan;
    }

    /**
     * Create learning plans from a template and cohort.
     *
     * @param  mixed $templateorid The template object or ID.
     * @param  int $cohortid The cohort ID.
     * @param  bool $recreateunlinked When true the plans that were unlinked from this template will be re-created.
     * @return int The number of plans created.
     */
    public static function create_plans_from_template_cohort($templateorid, $cohortid, $recreateunlinked = false) {
        global $DB, $CFG;
        static::require_enabled();
        require_once($CFG->dirroot . '/cohort/lib.php');

        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // The user must be able to view the template to use it as a base for a plan.
        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        // Can not create plan from a hidden template.
        if ($template->get_visible() == false) {
            throw new coding_exception('A plan can not be created from a hidden template');
        }

        // Replicate logic in cohort_can_view_cohort() because we can't use it directly as we don't have a course context.
        $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
        $cohortcontext = context::instance_by_id($cohort->contextid);
        if (!$cohort->visible && !has_capability('moodle/cohort:view', $cohortcontext)) {
            throw new required_capability_exception($cohortcontext, 'moodle/cohort:view', 'nopermissions', '');
        }

        // Convert the template to a plan.
        $recordbase = $template->to_record();
        $recordbase->templateid = $recordbase->id;
        $recordbase->name = $recordbase->shortname;
        $recordbase->status = plan::STATUS_ACTIVE;

        unset($recordbase->id);
        unset($recordbase->timecreated);
        unset($recordbase->timemodified);
        unset($recordbase->usermodified);

        // Remove extra keys.
        $properties = plan::properties_definition();
        foreach ($recordbase as $key => $value) {
            if (!array_key_exists($key, $properties)) {
                unset($recordbase->$key);
            }
        }

        // Create the plans.
        $created = 0;
        $userids = template_cohort::get_missing_plans($template->get_id(), $cohortid, $recreateunlinked);
        foreach ($userids as $userid) {
            $record = (object) (array) $recordbase;
            $record->userid = $userid;

            $plan = new plan(0, $record);
            if (!$plan->can_manage()) {
                // Silently skip members where permissions are lacking.
                continue;
            }

            $plan->create();
            // Trigger created event.
            \core\event\competency_plan_created::create_from_plan($plan)->trigger();
            $created++;
        }

        return $created;
    }

    /**
     * Unlink a plan from its template.
     *
     * @param  \core_competency\plan|int $planorid The plan or its ID.
     * @return bool
     */
    public static function unlink_plan_from_template($planorid) {
        global $DB;
        static::require_enabled();

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        // The user must be allowed to manage the plans of the user, nothing about the template.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        // Only plan with status DRAFT or ACTIVE can be unliked..
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            throw new coding_exception('Only draft or active plan can be unliked from a template');
        }

        // Early exit, it's already done...
        if (!$plan->is_based_on_template()) {
            return true;
        }

        // Fetch the template.
        $template = new template($plan->get_templateid());

        // Now, proceed by copying all competencies to the plan, then update the plan.
        $transaction = $DB->start_delegated_transaction();
        $competencies = template_competency::list_competencies($template->get_id(), false);
        $i = 0;
        foreach ($competencies as $competency) {
            $record = (object) array(
                'planid' => $plan->get_id(),
                'competencyid' => $competency->get_id(),
                'sortorder' => $i++
            );
            $pc = new plan_competency(null, $record);
            $pc->create();
        }
        $plan->set_origtemplateid($template->get_id());
        $plan->set_templateid(null);
        $success = $plan->update();
        $transaction->allow_commit();

        // Trigger unlinked event.
        \core\event\competency_plan_unlinked::create_from_plan($plan)->trigger();

        return $success;
    }

    /**
     * Updates a plan.
     *
     * @param stdClass $record
     * @return \core_competency\plan
     */
    public static function update_plan(stdClass $record) {
        static::require_enabled();

        $plan = new plan($record->id);

        // Validate that the plan as it is can be managed.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');

        } else if ($plan->get_status() == plan::STATUS_COMPLETE) {
            // A completed plan cannot be edited.
            throw new coding_exception('Completed plan cannot be edited.');

        } else if ($plan->is_based_on_template()) {
            // Prevent a plan based on a template to be edited.
            throw new coding_exception('Cannot update a plan that is based on a template.');

        } else if (isset($record->templateid) && $plan->get_templateid() != $record->templateid) {
            // Prevent a plan to be based on a template.
            throw new coding_exception('Cannot base a plan on a template.');

        } else if (isset($record->userid) && $plan->get_userid() != $record->userid) {
            // Prevent change of ownership as the capabilities are checked against that.
            throw new coding_exception('A plan cannot be transfered to another user');

        } else if (isset($record->status) && $plan->get_status() != $record->status) {
            // Prevent change of status.
            throw new coding_exception('To change the status of a plan use the appropriate methods.');

        }

        $plan->from_record($record);
        $plan->update();

        // Trigger updated event.
        \core\event\competency_plan_updated::create_from_plan($plan)->trigger();

        return $plan;
    }

    /**
     * Returns a plan data.
     *
     * @param int $id
     * @return \core_competency\plan
     */
    public static function read_plan($id) {
        static::require_enabled();
        $plan = new plan($id);

        if (!$plan->can_read()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planview', 'nopermissions', '');
        }

        return $plan;
    }

    /**
     * Plan event viewed.
     *
     * @param mixed $planorid The id or the plan.
     * @return boolean
     */
    public static function plan_viewed($planorid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($plan)) {
            $plan = new plan($plan);
        }

        // First we do a permissions check.
        if (!$plan->can_read()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planview', 'nopermissions', '');
        }

        // Trigger a template viewed event.
        \core\event\competency_plan_viewed::create_from_plan($plan)->trigger();

        return true;
    }

    /**
     * Deletes a plan.
     *
     * Plans based on a template can be removed just like any other one.
     *
     * @param int $id
     * @return bool Success?
     */
    public static function delete_plan($id) {
        global $DB;
        static::require_enabled();

        $plan = new plan($id);

        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planmanage', 'nopermissions', '');
        }

        // Wrap the suppression in a DB transaction.
        $transaction = $DB->start_delegated_transaction();

        // Delete plan competencies.
        $plancomps = plan_competency::get_records(array('planid' => $plan->get_id()));
        foreach ($plancomps as $plancomp) {
            $plancomp->delete();
        }

        // Delete archive user competencies if the status of the plan is complete.
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            self::remove_archived_user_competencies_in_plan($plan);
        }
        $event = \core\event\competency_plan_deleted::create_from_plan($plan);
        $success = $plan->delete();

        $transaction->allow_commit();

        // Trigger deleted event.
        $event->trigger();

        return $success;
    }

    /**
     * Cancel the review of a plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function plan_cancel_review_request($planorid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($plan)) {
            $plan = new plan($plan);
        }

        // We need to be able to view the plan at least.
        if (!$plan->can_read()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planview', 'nopermissions', '');
        }

        if ($plan->is_based_on_template()) {
            throw new coding_exception('Template plans cannot be reviewed.');   // This should never happen.
        } else if ($plan->get_status() != plan::STATUS_WAITING_FOR_REVIEW) {
            throw new coding_exception('The plan review cannot be cancelled at this stage.');
        } else if (!$plan->can_request_review()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $plan->set_status(plan::STATUS_DRAFT);
        $result = $plan->update();

        // Trigger review request cancelled event.
        \core\event\competency_plan_review_request_cancelled::create_from_plan($plan)->trigger();

        return $result;
    }

    /**
     * Request the review of a plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function plan_request_review($planorid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($plan)) {
            $plan = new plan($plan);
        }

        // We need to be able to view the plan at least.
        if (!$plan->can_read()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planview', 'nopermissions', '');
        }

        if ($plan->is_based_on_template()) {
            throw new coding_exception('Template plans cannot be reviewed.');   // This should never happen.
        } else if ($plan->get_status() != plan::STATUS_DRAFT) {
            throw new coding_exception('The plan cannot be sent for review at this stage.');
        } else if (!$plan->can_request_review()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $plan->set_status(plan::STATUS_WAITING_FOR_REVIEW);
        $result = $plan->update();

        // Trigger review requested event.
        \core\event\competency_plan_review_requested::create_from_plan($plan)->trigger();

        return $result;
    }

    /**
     * Start the review of a plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function plan_start_review($planorid) {
        global $USER;
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($plan)) {
            $plan = new plan($plan);
        }

        // We need to be able to view the plan at least.
        if (!$plan->can_read()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planview', 'nopermissions', '');
        }

        if ($plan->is_based_on_template()) {
            throw new coding_exception('Template plans cannot be reviewed.');   // This should never happen.
        } else if ($plan->get_status() != plan::STATUS_WAITING_FOR_REVIEW) {
            throw new coding_exception('The plan review cannot be started at this stage.');
        } else if (!$plan->can_review()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $plan->set_status(plan::STATUS_IN_REVIEW);
        $plan->set_reviewerid($USER->id);
        $result = $plan->update();

        // Trigger review started event.
        \core\event\competency_plan_review_started::create_from_plan($plan)->trigger();

        return $result;
    }

    /**
     * Stop reviewing a plan.
     *
     * @param  int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function plan_stop_review($planorid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($plan)) {
            $plan = new plan($plan);
        }

        // We need to be able to view the plan at least.
        if (!$plan->can_read()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planview', 'nopermissions', '');
        }

        if ($plan->is_based_on_template()) {
            throw new coding_exception('Template plans cannot be reviewed.');   // This should never happen.
        } else if ($plan->get_status() != plan::STATUS_IN_REVIEW) {
            throw new coding_exception('The plan review cannot be stopped at this stage.');
        } else if (!$plan->can_review()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $plan->set_status(plan::STATUS_DRAFT);
        $plan->set_reviewerid(null);
        $result = $plan->update();

        // Trigger review stopped event.
        \core\event\competency_plan_review_stopped::create_from_plan($plan)->trigger();

        return $result;
    }

    /**
     * Approve a plan.
     *
     * This means making the plan active.
     *
     * @param  int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function approve_plan($planorid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($plan)) {
            $plan = new plan($plan);
        }

        // We need to be able to view the plan at least.
        if (!$plan->can_read()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planview', 'nopermissions', '');
        }

        // We can approve a plan that is either a draft, in review, or waiting for review.
        if ($plan->is_based_on_template()) {
            throw new coding_exception('Template plans are already approved.');   // This should never happen.
        } else if (!$plan->is_draft()) {
            throw new coding_exception('The plan cannot be approved at this stage.');
        } else if (!$plan->can_review()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $plan->set_status(plan::STATUS_ACTIVE);
        $plan->set_reviewerid(null);
        $result = $plan->update();

        // Trigger approved event.
        \core\event\competency_plan_approved::create_from_plan($plan)->trigger();

        return $result;
    }

    /**
     * Unapprove a plan.
     *
     * This means making the plan draft.
     *
     * @param  int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function unapprove_plan($planorid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($plan)) {
            $plan = new plan($plan);
        }

        // We need to be able to view the plan at least.
        if (!$plan->can_read()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planview', 'nopermissions', '');
        }

        if ($plan->is_based_on_template()) {
            throw new coding_exception('Template plans are always approved.');   // This should never happen.
        } else if ($plan->get_status() != plan::STATUS_ACTIVE) {
            throw new coding_exception('The plan cannot be sent back to draft at this stage.');
        } else if (!$plan->can_review()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $plan->set_status(plan::STATUS_DRAFT);
        $result = $plan->update();

        // Trigger unapproved event.
        \core\event\competency_plan_unapproved::create_from_plan($plan)->trigger();

        return $result;
    }

    /**
     * Complete a plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function complete_plan($planorid) {
        global $DB;
        static::require_enabled();

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        // Validate that the plan can be managed.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        // Check if the plan was already completed.
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            throw new coding_exception('The plan is already completed.');
        }

        $originalstatus = $plan->get_status();
        $plan->set_status(plan::STATUS_COMPLETE);

        // The user should also be able to manage the plan when it's completed.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');
        }

        // Put back original status because archive needs it to extract competencies from the right table.
        $plan->set_status($originalstatus);

        // Do the things.
        $transaction = $DB->start_delegated_transaction();
        self::archive_user_competencies_in_plan($plan);
        $plan->set_status(plan::STATUS_COMPLETE);
        $success = $plan->update();

        if (!$success) {
            $transaction->rollback(new moodle_exception('The plan could not be updated.'));
            return $success;
        }

        $transaction->allow_commit();

        // Trigger updated event.
        \core\event\competency_plan_completed::create_from_plan($plan)->trigger();

        return $success;
    }

    /**
     * Reopen a plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function reopen_plan($planorid) {
        global $DB;
        static::require_enabled();

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        // Validate that the plan as it is can be managed.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planmanage', 'nopermissions', '');
        }

        $beforestatus = $plan->get_status();
        $plan->set_status(plan::STATUS_ACTIVE);

        // Validate if status can be changed.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planmanage', 'nopermissions', '');
        }

        // Wrap the updates in a DB transaction.
        $transaction = $DB->start_delegated_transaction();

        // Delete archived user competencies if the status of the plan is changed from complete to another status.
        $mustremovearchivedcompetencies = ($beforestatus == plan::STATUS_COMPLETE && $plan->get_status() != plan::STATUS_COMPLETE);
        if ($mustremovearchivedcompetencies) {
            self::remove_archived_user_competencies_in_plan($plan);
        }

        // If duedate less than or equal to duedate_threshold unset it.
        if ($plan->get_duedate() <= time() + plan::DUEDATE_THRESHOLD) {
            $plan->set_duedate(0);
        }

        $success = $plan->update();

        if (!$success) {
            $transaction->rollback(new moodle_exception('The plan could not be updated.'));
            return $success;
        }

        $transaction->allow_commit();

        // Trigger reopened event.
        \core\event\competency_plan_reopened::create_from_plan($plan)->trigger();

        return $success;
    }

    /**
     * Get a single competency from the user plan.
     *
     * @param  int $planorid The plan, or its ID.
     * @param  int $competencyid The competency id.
     * @return (object) array(
     *                      'competency' => \core_competency\competency,
     *                      'usercompetency' => \core_competency\user_competency
     *                      'usercompetencyplan' => \core_competency\user_competency_plan
     *                  )
     *         The values of of keys usercompetency and usercompetencyplan cannot be defined at the same time.
     */
    public static function get_plan_competency($planorid, $competencyid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        if (!user_competency::can_read_user($plan->get_userid())) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }

        $competency = $plan->get_competency($competencyid);

        // Get user competencies from user_competency_plan if the plan status is set to complete.
        $iscompletedplan = $plan->get_status() == plan::STATUS_COMPLETE;
        if ($iscompletedplan) {
            $usercompetencies = user_competency_plan::get_multiple($plan->get_userid(), $plan->get_id(), array($competencyid));
            $ucresultkey = 'usercompetencyplan';
        } else {
            $usercompetencies = user_competency::get_multiple($plan->get_userid(), array($competencyid));
            $ucresultkey = 'usercompetency';
        }

        $found = count($usercompetencies);

        if ($found) {
            $uc = array_pop($usercompetencies);
        } else {
            if ($iscompletedplan) {
                throw new coding_exception('A user competency plan is missing');
            } else {
                $uc = user_competency::create_relation($plan->get_userid(), $competency->get_id());
                $uc->create();
            }
        }

        $plancompetency = (object) array(
            'competency' => $competency,
            'usercompetency' => null,
            'usercompetencyplan' => null
        );
        $plancompetency->$ucresultkey = $uc;

        return $plancompetency;
    }

    /**
     * List the competencies in a user plan.
     *
     * @param  int $planorid The plan, or its ID.
     * @return array((object) array(
     *                            'competency' => \core_competency\competency,
     *                            'usercompetency' => \core_competency\user_competency
     *                            'usercompetencyplan' => \core_competency\user_competency_plan
     *                        ))
     *         The values of of keys usercompetency and usercompetencyplan cannot be defined at the same time.
     */
    public static function list_plan_competencies($planorid) {
        static::require_enabled();
        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        if (!$plan->can_read()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planview', 'nopermissions', '');
        }

        $result = array();
        $competencies = $plan->get_competencies();

        // Get user competencies from user_competency_plan if the plan status is set to complete.
        $iscompletedplan = $plan->get_status() == plan::STATUS_COMPLETE;
        if ($iscompletedplan) {
            $usercompetencies = user_competency_plan::get_multiple($plan->get_userid(), $plan->get_id(), $competencies);
            $ucresultkey = 'usercompetencyplan';
        } else {
            $usercompetencies = user_competency::get_multiple($plan->get_userid(), $competencies);
            $ucresultkey = 'usercompetency';
        }

        // Build the return values.
        foreach ($competencies as $key => $competency) {
            $found = false;

            foreach ($usercompetencies as $uckey => $uc) {
                if ($uc->get_competencyid() == $competency->get_id()) {
                    $found = true;
                    unset($usercompetencies[$uckey]);
                    break;
                }
            }

            if (!$found) {
                if ($iscompletedplan) {
                    throw new coding_exception('A user competency plan is missing');
                } else {
                    $uc = user_competency::create_relation($plan->get_userid(), $competency->get_id());
                }
            }

            $plancompetency = (object) array(
                'competency' => $competency,
                'usercompetency' => null,
                'usercompetencyplan' => null
            );
            $plancompetency->$ucresultkey = $uc;
            $result[] = $plancompetency;
        }

        return $result;
    }

    /**
     * Add a competency to a plan.
     *
     * @param int $planid The id of the plan
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_plan($planid, $competencyid) {
        static::require_enabled();
        $plan = new plan($planid);

        // First we do a permissions check.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'moodle/competency:planmanage', 'nopermissions', '');

        } else if ($plan->is_based_on_template()) {
            throw new coding_exception('A competency can not be added to a learning plan based on a template');
        }

        if (!$plan->can_be_edited()) {
            throw new coding_exception('A competency can not be added to a learning plan completed');
        }

        $competency = new competency($competencyid);

        // Can not add a competency that belong to a hidden framework.
        if ($competency->get_framework()->get_visible() == false) {
            throw new coding_exception('A competency belonging to hidden framework can not be added');
        }

        $exists = plan_competency::get_record(array('planid' => $planid, 'competencyid' => $competencyid));
        if (!$exists) {
            $record = new stdClass();
            $record->planid = $planid;
            $record->competencyid = $competencyid;
            $plancompetency = new plan_competency(0, $record);
            $plancompetency->create();
        }

        return true;
    }

    /**
     * Remove a competency from a plan.
     *
     * @param int $planid The plan id
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function remove_competency_from_plan($planid, $competencyid) {
        static::require_enabled();
        $plan = new plan($planid);

        // First we do a permissions check.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planmanage', 'nopermissions', '');

        } else if ($plan->is_based_on_template()) {
            throw new coding_exception('A competency can not be removed from a learning plan based on a template');
        }

        if (!$plan->can_be_edited()) {
            throw new coding_exception('A competency can not be removed from a learning plan completed');
        }

        $link = plan_competency::get_record(array('planid' => $planid, 'competencyid' => $competencyid));
        if ($link) {
            return $link->delete();
        }
        return false;
    }

    /**
     * Move the plan competency up or down in the display list.
     *
     * Requires moodle/competency:planmanage capability at the system context.
     *
     * @param int $planid The plan  id
     * @param int $competencyidfrom The id of the competency we are moving.
     * @param int $competencyidto The id of the competency we are moving to.
     * @return boolean
     */
    public static function reorder_plan_competency($planid, $competencyidfrom, $competencyidto) {
        static::require_enabled();
        $plan = new plan($planid);

        // First we do a permissions check.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'moodle/competency:planmanage', 'nopermissions', '');

        } else if ($plan->is_based_on_template()) {
            throw new coding_exception('A competency can not be reordered in a learning plan based on a template');
        }

        if (!$plan->can_be_edited()) {
            throw new coding_exception('A competency can not be reordered in a learning plan completed');
        }

        $down = true;
        $matches = plan_competency::get_records(array('planid' => $planid, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = plan_competency::get_records(array('planid' => $planid, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = plan_competency::get_records(array('planid' => $planid), 'sortorder', 'ASC', 0, 0);

        if ($competencyfrom->get_sortorder() > $competencyto->get_sortorder()) {
            // We are moving up, so put it before the "to" item.
            $down = false;
        }

        foreach ($all as $id => $plancompetency) {
            $sort = $plancompetency->get_sortorder();
            if ($down && $sort > $competencyfrom->get_sortorder() && $sort <= $competencyto->get_sortorder()) {
                $plancompetency->set_sortorder($plancompetency->get_sortorder() - 1);
                $plancompetency->update();
            } else if (!$down && $sort >= $competencyto->get_sortorder() && $sort < $competencyfrom->get_sortorder()) {
                $plancompetency->set_sortorder($plancompetency->get_sortorder() + 1);
                $plancompetency->update();
            }
        }
        $competencyfrom->set_sortorder($competencyto->get_sortorder());
        return $competencyfrom->update();
    }

    /**
     * Cancel a user competency review request.
     *
     * @param  int $userid       The user ID.
     * @param  int $competencyid The competency ID.
     * @return bool
     */
    public static function user_competency_cancel_review_request($userid, $competencyid) {
        static::require_enabled();
        $context = context_user::instance($userid);
        $uc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$uc || !$uc->can_read()) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        } else if ($uc->get_status() != user_competency::STATUS_WAITING_FOR_REVIEW) {
            throw new coding_exception('The competency can not be cancel review request at this stage.');
        } else if (!$uc->can_request_review()) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyrequestreview', 'nopermissions', '');
        }

        $uc->set_status(user_competency::STATUS_IDLE);
        $result = $uc->update();
        if ($result) {
            \core\event\competency_user_competency_review_request_cancelled::create_from_user_competency($uc)->trigger();
        }
        return $result;
    }

    /**
     * Request a user competency review.
     *
     * @param  int $userid       The user ID.
     * @param  int $competencyid The competency ID.
     * @return bool
     */
    public static function user_competency_request_review($userid, $competencyid) {
        static::require_enabled();
        $uc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$uc) {
            $uc = user_competency::create_relation($userid, $competencyid);
            $uc->create();
        }

        if (!$uc->can_read()) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        } else if ($uc->get_status() != user_competency::STATUS_IDLE) {
            throw new coding_exception('The competency can not be sent for review at this stage.');
        } else if (!$uc->can_request_review()) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:usercompetencyrequestreview',
                'nopermissions', '');
        }

        $uc->set_status(user_competency::STATUS_WAITING_FOR_REVIEW);
        $result = $uc->update();
        if ($result) {
            \core\event\competency_user_competency_review_requested::create_from_user_competency($uc)->trigger();
        }
        return $result;
    }

    /**
     * Start a user competency review.
     *
     * @param  int $userid       The user ID.
     * @param  int $competencyid The competency ID.
     * @return bool
     */
    public static function user_competency_start_review($userid, $competencyid) {
        global $USER;
        static::require_enabled();

        $context = context_user::instance($userid);
        $uc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$uc || !$uc->can_read()) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        } else if ($uc->get_status() != user_competency::STATUS_WAITING_FOR_REVIEW) {
            throw new coding_exception('The competency review can not be started at this stage.');
        } else if (!$uc->can_review()) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyreview', 'nopermissions', '');
        }

        $uc->set_status(user_competency::STATUS_IN_REVIEW);
        $uc->set_reviewerid($USER->id);
        $result = $uc->update();
        if ($result) {
            \core\event\competency_user_competency_review_started::create_from_user_competency($uc)->trigger();
        }
        return $result;
    }

    /**
     * Stop a user competency review.
     *
     * @param  int $userid       The user ID.
     * @param  int $competencyid The competency ID.
     * @return bool
     */
    public static function user_competency_stop_review($userid, $competencyid) {
        static::require_enabled();
        $context = context_user::instance($userid);
        $uc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$uc || !$uc->can_read()) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        } else if ($uc->get_status() != user_competency::STATUS_IN_REVIEW) {
            throw new coding_exception('The competency review can not be stopped at this stage.');
        } else if (!$uc->can_review()) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyreview', 'nopermissions', '');
        }

        $uc->set_status(user_competency::STATUS_IDLE);
        $result = $uc->update();
        if ($result) {
            \core\event\competency_user_competency_review_stopped::create_from_user_competency($uc)->trigger();
        }
        return $result;
    }

    /**
     * Log user competency viewed event.
     *
     * @param user_competency|int $usercompetencyorid The user competency object or user competency id
     * @return bool
     */
    public static function user_competency_viewed($usercompetencyorid) {
        static::require_enabled();
        $uc = $usercompetencyorid;
        if (!is_object($uc)) {
            $uc = new user_competency($uc);
        }

        if (!$uc || !$uc->can_read()) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }

        \core\event\competency_user_competency_viewed::create_from_user_competency_viewed($uc)->trigger();
        return true;
    }

    /**
     * Log user competency viewed in plan event.
     *
     * @param user_competency|int $usercompetencyorid The user competency object or user competency id
     * @param int $planid The plan ID
     * @return bool
     */
    public static function user_competency_viewed_in_plan($usercompetencyorid, $planid) {
        static::require_enabled();
        $uc = $usercompetencyorid;
        if (!is_object($uc)) {
            $uc = new user_competency($uc);
        }

        if (!$uc || !$uc->can_read()) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }
        $plan = new plan($planid);
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            throw new coding_exception('To log the user competency in completed plan use user_competency_plan_viewed method.');
        }

        \core\event\competency_user_competency_viewed_in_plan::create_from_user_competency_viewed_in_plan($uc, $planid)->trigger();
        return true;
    }

    /**
     * Log user competency viewed in course event.
     *
     * @param user_competency_course|int $usercoursecompetencyorid The user competency course object or its ID.
     * @param int $courseid The course ID
     * @return bool
     */
    public static function user_competency_viewed_in_course($usercoursecompetencyorid) {
        static::require_enabled();
        $ucc = $usercoursecompetencyorid;
        if (!is_object($ucc)) {
            $ucc = new user_competency_course($ucc);
        }

        if (!$ucc || !user_competency::can_read_user_in_course($ucc->get_userid(), $ucc->get_courseid())) {
            throw new required_capability_exception($ucc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }

        // Validate the course, this will throw an exception if not valid.
        self::validate_course($ucc->get_courseid());

        \core\event\competency_user_competency_viewed_in_course::create_from_user_competency_viewed_in_course($ucc)->trigger();
        return true;
    }

    /**
     * Log user competency plan viewed event.
     *
     * @param user_competency_plan|int $usercompetencyplanorid The user competency plan object or user competency plan id
     * @return bool
     */
    public static function user_competency_plan_viewed($usercompetencyplanorid) {
        static::require_enabled();
        $ucp = $usercompetencyplanorid;
        if (!is_object($ucp)) {
            $ucp = new user_competency_plan($ucp);
        }

        if (!$ucp || !user_competency::can_read_user($ucp->get_userid())) {
            throw new required_capability_exception($ucp->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }
        $plan = new plan($ucp->get_planid());
        if ($plan->get_status() != plan::STATUS_COMPLETE) {
            throw new coding_exception('To log the user competency in non-completed plan use '
                . 'user_competency_viewed_in_plan method.');
        }

        \core\event\competency_user_competency_plan_viewed::create_from_user_competency_plan($ucp)->trigger();
        return true;
    }

    /**
     * Check if template has related data.
     *
     * @param int $templateid The id of the template to check.
     * @return boolean
     */
    public static function template_has_related_data($templateid) {
        static::require_enabled();
        // First we do a permissions check.
        $template = new template($templateid);

        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        // OK - all set.
        return $template->has_plans();
    }

    /**
     * List all the related competencies.
     *
     * @param int $competencyid The id of the competency to check.
     * @return competency[]
     */
    public static function list_related_competencies($competencyid) {
        static::require_enabled();
        $competency = new competency($competencyid);

        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $competency->get_context())) {
            throw new required_capability_exception($competency->get_context(), 'moodle/competency:competencyview',
                'nopermissions', '');
        }

        return $competency->get_related_competencies();
    }

    /**
     * Add a related competency.
     *
     * @param int $competencyid The id of the competency
     * @param int $relatedcompetencyid The id of the related competency.
     * @return bool False when create failed, true on success, or if the relation already existed.
     */
    public static function add_related_competency($competencyid, $relatedcompetencyid) {
        static::require_enabled();
        $competency1 = new competency($competencyid);
        $competency2 = new competency($relatedcompetencyid);

        require_capability('moodle/competency:competencymanage', $competency1->get_context());

        $relatedcompetency = related_competency::get_relation($competency1->get_id(), $competency2->get_id());
        if (!$relatedcompetency->get_id()) {
            $relatedcompetency->create();
            return true;
        }

        return true;
    }

    /**
     * Remove a related competency.
     *
     * @param int $competencyid The id of the competency.
     * @param int $relatedcompetencyid The id of the related competency.
     * @return bool True when it was deleted, false when it wasn't or the relation doesn't exist.
     */
    public static function remove_related_competency($competencyid, $relatedcompetencyid) {
        static::require_enabled();
        $competency = new competency($competencyid);

        // This only check if we have the permission in either competency because both competencies
        // should belong to the same framework.
        require_capability('moodle/competency:competencymanage', $competency->get_context());

        $relatedcompetency = related_competency::get_relation($competencyid, $relatedcompetencyid);
        if ($relatedcompetency->get_id()) {
            return $relatedcompetency->delete();
        }

        return false;
    }

    /**
     * Read a user evidence.
     *
     * @param int $id
     * @return user_evidence
     */
    public static function read_user_evidence($id) {
        static::require_enabled();
        $userevidence = new user_evidence($id);

        if (!$userevidence->can_read()) {
            $context = $userevidence->get_context();
            throw new required_capability_exception($context, 'moodle/competency:userevidenceview', 'nopermissions', '');
        }

        return $userevidence;
    }

    /**
     * Create a new user evidence.
     *
     * @param  object $data        The data.
     * @param  int    $draftitemid The draft ID in which files have been saved.
     * @return user_evidence
     */
    public static function create_user_evidence($data, $draftitemid = null) {
        static::require_enabled();
        $userevidence = new user_evidence(null, $data);
        $context = $userevidence->get_context();

        if (!$userevidence->can_manage()) {
            throw new required_capability_exception($context, 'moodle/competency:userevidencemanage', 'nopermissions', '');
        }

        $userevidence->create();
        if (!empty($draftitemid)) {
            $fileareaoptions = array('subdirs' => true);
            $itemid = $userevidence->get_id();
            file_save_draft_area_files($draftitemid, $context->id, 'core_competency', 'userevidence', $itemid, $fileareaoptions);
        }

        // Trigger an evidence of prior learning created event.
        \core\event\competency_user_evidence_created::create_from_user_evidence($userevidence)->trigger();

        return $userevidence;
    }

    /**
     * Create a new user evidence.
     *
     * @param  object $data        The data.
     * @param  int    $draftitemid The draft ID in which files have been saved.
     * @return user_evidence
     */
    public static function update_user_evidence($data, $draftitemid = null) {
        static::require_enabled();
        $userevidence = new user_evidence($data->id);
        $context = $userevidence->get_context();

        if (!$userevidence->can_manage()) {
            throw new required_capability_exception($context, 'moodle/competency:userevidencemanage', 'nopermissions', '');

        } else if (array_key_exists('userid', $data) && $data->userid != $userevidence->get_userid()) {
            throw new coding_exception('Can not change the userid of a user evidence.');
        }

        $userevidence->from_record($data);
        $userevidence->update();

        if (!empty($draftitemid)) {
            $fileareaoptions = array('subdirs' => true);
            $itemid = $userevidence->get_id();
            file_save_draft_area_files($draftitemid, $context->id, 'core_competency', 'userevidence', $itemid, $fileareaoptions);
        }

        // Trigger an evidence of prior learning updated event.
        \core\event\competency_user_evidence_updated::create_from_user_evidence($userevidence)->trigger();

        return $userevidence;
    }

    /**
     * Delete a user evidence.
     *
     * @param  int $id The user evidence ID.
     * @return bool
     */
    public static function delete_user_evidence($id) {
        static::require_enabled();
        $userevidence = new user_evidence($id);
        $context = $userevidence->get_context();

        if (!$userevidence->can_manage()) {
            throw new required_capability_exception($context, 'moodle/competency:userevidencemanage', 'nopermissions', '');
        }

        // Delete the user evidence.
        $userevidence->delete();

        // Delete associated files.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'core_competency', 'userevidence', $id);

        // Delete relation between evidence and competencies.
        $userevidence->set_id($id);     // Restore the ID to fully mock the object.
        $competencies = user_evidence_competency::get_competencies_by_userevidenceid($id);
        foreach ($competencies as $competency) {
            static::delete_user_evidence_competency($userevidence, $competency->get_id());
        }

        // Trigger an evidence of prior learning deleted event.
        \core\event\competency_user_evidence_deleted::create_from_user_evidence($userevidence)->trigger();

        $userevidence->set_id(0);       // Restore the object.

        return true;
    }

    /**
     * List the user evidence of a user.
     *
     * @param  int $userid The user ID.
     * @return user_evidence[]
     */
    public static function list_user_evidence($userid) {
        static::require_enabled();
        if (!user_evidence::can_read_user($userid)) {
            $context = context_user::instance($userid);
            throw new required_capability_exception($context, 'moodle/competency:userevidenceview', 'nopermissions', '');
        }

        $evidence = user_evidence::get_records(array('userid' => $userid), 'name');
        return $evidence;
    }

    /**
     * Link a user evidence with a competency.
     *
     * @param  user_evidence|int $userevidenceorid User evidence or its ID.
     * @param  int $competencyid Competency ID.
     * @return user_evidence_competency
     */
    public static function create_user_evidence_competency($userevidenceorid, $competencyid) {
        global $USER;
        static::require_enabled();

        $userevidence = $userevidenceorid;
        if (!is_object($userevidence)) {
            $userevidence = self::read_user_evidence($userevidence);
        }

        // Perform user evidence capability checks.
        if (!$userevidence->can_manage()) {
            $context = $userevidence->get_context();
            throw new required_capability_exception($context, 'moodle/competency:userevidencemanage', 'nopermissions', '');
        }

        // Perform competency capability checks.
        $competency = self::read_competency($competencyid);

        // Get (and create) the relation.
        $relation = user_evidence_competency::get_relation($userevidence->get_id(), $competency->get_id());
        if (!$relation->get_id()) {
            $relation->create();

            $link = url::user_evidence($userevidence->get_id());
            self::add_evidence(
                $userevidence->get_userid(),
                $competency,
                $userevidence->get_context(),
                evidence::ACTION_LOG,
                'evidence_evidenceofpriorlearninglinked',
                'core_competency',
                $userevidence->get_name(),
                false,
                $link->out(false),
                null,
                $USER->id
            );
        }

        return $relation;
    }

    /**
     * Delete a relationship between a user evidence and a competency.
     *
     * @param  user_evidence|int $userevidenceorid User evidence or its ID.
     * @param  int $competencyid Competency ID.
     * @return bool
     */
    public static function delete_user_evidence_competency($userevidenceorid, $competencyid) {
        global $USER;
        static::require_enabled();

        $userevidence = $userevidenceorid;
        if (!is_object($userevidence)) {
            $userevidence = self::read_user_evidence($userevidence);
        }

        // Perform user evidence capability checks.
        if (!$userevidence->can_manage()) {
            $context = $userevidence->get_context();
            throw new required_capability_exception($context, 'moodle/competency:userevidencemanage', 'nopermissions', '');
        }

        // Get (and delete) the relation.
        $relation = user_evidence_competency::get_relation($userevidence->get_id(), $competencyid);
        if (!$relation->get_id()) {
            return true;
        }

        $success = $relation->delete();
        if ($success) {
            self::add_evidence(
                $userevidence->get_userid(),
                $competencyid,
                $userevidence->get_context(),
                evidence::ACTION_LOG,
                'evidence_evidenceofpriorlearningunlinked',
                'core_competency',
                $userevidence->get_name(),
                false,
                null,
                null,
                $USER->id
            );
        }

        return $success;
    }

    /**
     * Send request review for user evidence competencies.
     *
     * @param  int $id The user evidence ID.
     * @return bool
     */
    public static function request_review_of_user_evidence_linked_competencies($id) {
        $userevidence = new user_evidence($id);
        $context = $userevidence->get_context();
        $userid = $userevidence->get_userid();

        if (!$userevidence->can_manage()) {
            throw new required_capability_exception($context, 'moodle/competency:userevidencemanage', 'nopermissions', '');
        }

        $usercompetencies = user_evidence_competency::get_user_competencies_by_userevidenceid($id);
        foreach ($usercompetencies as $usercompetency) {
            if ($usercompetency->get_status() == user_competency::STATUS_IDLE) {
                static::user_competency_request_review($userid, $usercompetency->get_competencyid());
            }
        }

        return true;
    }

    /**
     * Recursively duplicate competencies from a tree, we start duplicating from parents to children to have a correct path.
     * This method does not copy the related competencies.
     *
     * @param int $frameworkid - framework id
     * @param competency[] $tree - array of competencies object
     * @param int $oldparent - old parent id
     * @param int $newparent - new parent id
     * @return competency[] $matchids - List of old competencies ids matched with new competencies object.
     */
    protected static function duplicate_competency_tree($frameworkid, $tree, $oldparent = 0, $newparent = 0) {
        $matchids = array();
        foreach ($tree as $node) {
            if ($node->competency->get_parentid() == $oldparent) {
                $parentid = $node->competency->get_id();

                // Create the competency.
                $competency = new competency(0, $node->competency->to_record());
                $competency->set_competencyframeworkid($frameworkid);
                $competency->set_parentid($newparent);
                $competency->set_path('');
                $competency->set_id(0);
                $competency->reset_rule();
                $competency->create();

                // Trigger the created event competency.
                \core\event\competency_created::create_from_competency($competency)->trigger();

                // Match the old id with the new one.
                $matchids[$parentid] = $competency;

                if (!empty($node->children)) {
                    // Duplicate children competency.
                    $childrenids = self::duplicate_competency_tree($frameworkid, $node->children, $parentid, $competency->get_id());
                    // Array_merge does not keep keys when merging so we use the + operator.
                    $matchids = $matchids + $childrenids;
                }
            }
        }
        return $matchids;
    }

    /**
     * Recursively migrate competency rules.
     *
     * @param competency[] $tree - array of competencies object
     * @param competency[] $matchids - List of old competencies ids matched with new competencies object
     */
    protected static function migrate_competency_tree_rules($tree, $matchids) {

        foreach ($tree as $node) {
            $oldcompid = $node->competency->get_id();
            if ($node->competency->get_ruletype() && array_key_exists($oldcompid, $matchids)) {
                try {
                    // Get the new competency.
                    $competency = $matchids[$oldcompid];
                    $class = $node->competency->get_ruletype();
                    $newruleconfig = $class::migrate_config($node->competency->get_ruleconfig(), $matchids);
                    $competency->set_ruleconfig($newruleconfig);
                    $competency->set_ruletype($class);
                    $competency->set_ruleoutcome($node->competency->get_ruleoutcome());
                    $competency->update();
                } catch (\Exception $e) {
                    debugging('Could not migrate competency rule from: ' . $oldcompid . ' to: ' . $competency->get_id() . '.' .
                        ' Exception: ' . $e->getMessage(), DEBUG_DEVELOPER);
                    $competency->reset_rule();
                }
            }

            if (!empty($node->children)) {
                self::migrate_competency_tree_rules($node->children, $matchids);
            }
        }
    }

    /**
     * Archive user competencies in a plan.
     *
     * @param int $plan The plan object.
     * @return void
     */
    protected static function archive_user_competencies_in_plan($plan) {

        // Check if the plan was already completed.
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            throw new coding_exception('The plan is already completed.');
        }

        $competencies = $plan->get_competencies();
        $usercompetencies = user_competency::get_multiple($plan->get_userid(), $competencies);

        $i = 0;
        foreach ($competencies as $competency) {
            $found = false;

            foreach ($usercompetencies as $uckey => $uc) {
                if ($uc->get_competencyid() == $competency->get_id()) {
                    $found = true;

                    $ucprecord = $uc->to_record();
                    $ucprecord->planid = $plan->get_id();
                    $ucprecord->sortorder = $i;
                    unset($ucprecord->id);
                    unset($ucprecord->status);
                    unset($ucprecord->reviewerid);

                    $usercompetencyplan = new user_competency_plan(0, $ucprecord);
                    $usercompetencyplan->create();

                    unset($usercompetencies[$uckey]);
                    break;
                }
            }

            // If the user competency doesn't exist, we create a new relation in user_competency_plan.
            if (!$found) {
                $usercompetencyplan = user_competency_plan::create_relation($plan->get_userid(), $competency->get_id(),
                        $plan->get_id());
                $usercompetencyplan->set_sortorder($i);
                $usercompetencyplan->create();
            }
            $i++;
        }
    }

    /**
     * Delete archived user competencies in a plan.
     *
     * @param int $plan The plan object.
     * @return void
     */
    protected static function remove_archived_user_competencies_in_plan($plan) {
        $competencies = $plan->get_competencies();
        $usercompetenciesplan = user_competency_plan::get_multiple($plan->get_userid(), $plan->get_id(), $competencies);

        foreach ($usercompetenciesplan as $ucpkey => $ucp) {
            $ucp->delete();
        }
    }

    /**
     * List all the evidence for a user competency.
     *
     * @param int $userid The user id - only used if usercompetencyid is 0.
     * @param int $competencyid The competency id - only used it usercompetencyid is 0.
     * @param int $planid The plan id - not used yet - but can be used to only list archived evidence if a plan is completed.
     * @param string $sort The field to sort the evidence by.
     * @param string $order The ordering of the sorting.
     * @param int $skip Number of records to skip.
     * @param int $limit Number of records to return.
     * @return \core_competency\evidence[]
     * @return array of \core_competency\evidence
     */
    public static function list_evidence($userid = 0, $competencyid = 0, $planid = 0, $sort = 'timecreated',
                                         $order = 'DESC', $skip = 0, $limit = 0) {
        static::require_enabled();

        if (!user_competency::can_read_user($userid)) {
            $context = context_user::instance($userid);
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        }

        $usercompetency = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$usercompetency) {
            return array();
        }

        $plancompleted = false;
        if ($planid != 0) {
            $plan = new plan($planid);
            if ($plan->get_status() == plan::STATUS_COMPLETE) {
                $plancompleted = true;
            }
        }

        $select = 'usercompetencyid = :usercompetencyid';
        $params = array('usercompetencyid' => $usercompetency->get_id());
        if ($plancompleted) {
            $select .= ' AND timecreated <= :timecompleted';
            $params['timecompleted'] = $plan->get_timemodified();
        }

        $orderby = $sort . ' ' . $order;
        $orderby .= !empty($orderby) ? ', id DESC' : 'id DESC'; // Prevent random ordering.

        $evidence = evidence::get_records_select($select, $params, $orderby, '*', $skip, $limit);
        return $evidence;
    }

    /**
     * List all the evidence for a user competency in a course.
     *
     * @param int $userid The user ID.
     * @param int $courseid The course ID.
     * @param int $competencyid The competency ID.
     * @param string $sort The field to sort the evidence by.
     * @param string $order The ordering of the sorting.
     * @param int $skip Number of records to skip.
     * @param int $limit Number of records to return.
     * @return \core_competency\evidence[]
     */
    public static function list_evidence_in_course($userid = 0, $courseid = 0, $competencyid = 0, $sort = 'timecreated',
                                                   $order = 'DESC', $skip = 0, $limit = 0) {
        static::require_enabled();

        if (!user_competency::can_read_user_in_course($userid, $courseid)) {
            $context = context_user::instance($userid);
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        }

        $usercompetency = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$usercompetency) {
            return array();
        }

        $params = array(
            'usercompetencyid' => $usercompetency->get_id(),
            'contextid' => context_course::instance($courseid)->id
        );
        return evidence::get_records($params, $sort, $order, $skip, $limit);
    }

    /**
     * Create an evidence from a list of parameters.
     *
     * Requires no capability because evidence can be added in many situations under any user.
     *
     * @param int $userid The user id for which evidence is added.
     * @param competency|int $competencyorid The competency, or its id for which evidence is added.
     * @param context|int $contextorid The context in which the evidence took place.
     * @param int $action The type of action to take on the competency. \core_competency\evidence::ACTION_*.
     * @param string $descidentifier The strings identifier.
     * @param string $desccomponent The strings component.
     * @param mixed $desca Any arguments the string requires.
     * @param bool $recommend When true, the user competency will be sent for review.
     * @param string $url The url the evidence may link to.
     * @param int $grade The grade, or scale ID item.
     * @param int $actionuserid The ID of the user who took the action of adding the evidence. Null when system.
     *                          This should be used when the action was taken by a real person, this will allow
     *                          to keep track of all the evidence given by a certain person.
     * @param string $note A note to attach to the evidence.
     * @return evidence
     * @throws coding_exception
     * @throws invalid_persistent_exception
     * @throws moodle_exception
     */
    public static function add_evidence($userid, $competencyorid, $contextorid, $action, $descidentifier, $desccomponent,
                                        $desca = null, $recommend = false, $url = null, $grade = null, $actionuserid = null,
                                        $note = null) {
        global $DB;
        static::require_enabled();

        // Some clearly important variable assignments right there.
        $competencyid = $competencyorid;
        $competency = null;
        if (is_object($competencyid)) {
            $competency = $competencyid;
            $competencyid = $competency->get_id();
        }
        $contextid = $contextorid;
        $context = $contextorid;
        if (is_object($contextorid)) {
            $contextid = $contextorid->id;
        } else {
            $context = context::instance_by_id($contextorid);
        }
        $setucgrade = false;
        $ucgrade = null;
        $ucproficiency = null;
        $usercompetencycourse = null;

        // Fetch or create the user competency.
        $usercompetency = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$usercompetency) {
            $usercompetency = user_competency::create_relation($userid, $competencyid);
            $usercompetency->create();
        }

        // What should we be doing?
        switch ($action) {

            // Completing a competency.
            case evidence::ACTION_COMPLETE:
                // The logic here goes like this:
                //
                // if rating outside a course
                // - set the default grade and proficiency ONLY if there is no current grade
                // else we are in a course
                // - set the defautl grade and proficiency in the course ONLY if there is no current grade in the course
                // - then check the course settings to see if we should push the rating outside the course
                // - if we should push it
                // --- push it only if the user_competency (outside the course) has no grade
                // Done.

                if ($grade !== null) {
                    throw new coding_exception("The grade MUST NOT be set with a 'completing' evidence.");
                }

                // Fetch the default grade to attach to the evidence.
                if (empty($competency)) {
                    $competency = new competency($competencyid);
                }
                list($grade, $proficiency) = $competency->get_default_grade();

                // Add user_competency_course record when in a course or module.
                if (in_array($context->contextlevel, array(CONTEXT_COURSE, CONTEXT_MODULE))) {
                    $coursecontext = $context->get_course_context();
                    $courseid = $coursecontext->instanceid;
                    $filterparams = array(
                        'userid' => $userid,
                        'competencyid' => $competencyid,
                        'courseid' => $courseid
                    );
                    // Fetch or create user competency course.
                    $usercompetencycourse = user_competency_course::get_record($filterparams);
                    if (!$usercompetencycourse) {
                        $usercompetencycourse = user_competency_course::create_relation($userid, $competencyid, $courseid);
                        $usercompetencycourse->create();
                    }
                    // Only update the grade and proficiency if there is not already a grade.
                    if ($usercompetencycourse->get_grade() === null) {
                        // Set grade.
                        $usercompetencycourse->set_grade($grade);
                        // Set proficiency.
                        $usercompetencycourse->set_proficiency($proficiency);
                    }

                    // Check the course settings to see if we should push to user plans.
                    $coursesettings = course_competency_settings::get_by_courseid($courseid);
                    $setucgrade = $coursesettings->get_pushratingstouserplans();

                    if ($setucgrade) {
                        // Only push to user plans if there is not already a grade.
                        if ($usercompetency->get_grade() !== null) {
                            $setucgrade = false;
                        } else {
                            $ucgrade = $grade;
                            $ucproficiency = $proficiency;
                        }
                    }
                } else {

                    // When completing the competency we fetch the default grade from the competency. But we only mark
                    // the user competency when a grade has not been set yet. Complete is an action to use with automated systems.
                    if ($usercompetency->get_grade() === null) {
                        $setucgrade = true;
                        $ucgrade = $grade;
                        $ucproficiency = $proficiency;
                    }
                }

                break;

            // We override the grade, even overriding back to not set.
            case evidence::ACTION_OVERRIDE:
                $setucgrade = true;
                $ucgrade = $grade;
                if (empty($competency)) {
                    $competency = new competency($competencyid);
                }
                if ($ucgrade !== null) {
                    $ucproficiency = $competency->get_proficiency_of_grade($ucgrade);
                }

                // Add user_competency_course record when in a course or module.
                if (in_array($context->contextlevel, array(CONTEXT_COURSE, CONTEXT_MODULE))) {
                    $coursecontext = $context->get_course_context();
                    $courseid = $coursecontext->instanceid;
                    $filterparams = array(
                        'userid' => $userid,
                        'competencyid' => $competencyid,
                        'courseid' => $courseid
                    );
                    // Fetch or create user competency course.
                    $usercompetencycourse = user_competency_course::get_record($filterparams);
                    if (!$usercompetencycourse) {
                        $usercompetencycourse = user_competency_course::create_relation($userid, $competencyid, $courseid);
                        $usercompetencycourse->create();
                    }
                    // Get proficiency.
                    $proficiency = $ucproficiency;
                    if ($proficiency === null) {
                        if (empty($competency)) {
                            $competency = new competency($competencyid);
                        }
                        $proficiency = $competency->get_proficiency_of_grade($grade);
                    }
                    // Set grade.
                    $usercompetencycourse->set_grade($grade);
                    // Set proficiency.
                    $usercompetencycourse->set_proficiency($proficiency);

                    $coursesettings = course_competency_settings::get_by_courseid($courseid);
                    if (!$coursesettings->get_pushratingstouserplans()) {
                        $setucgrade = false;
                    }
                }

                break;

            // Simply logging an evidence.
            case evidence::ACTION_LOG:
                if ($grade !== null) {
                    throw new coding_exception("The grade MUST NOT be set when 'logging' an evidence.");
                }
                break;

            // Whoops, this is not expected.
            default:
                throw new coding_exception('Unexpected action parameter when registering an evidence.');
                break;
        }

        // Should we recommend?
        if ($recommend && $usercompetency->get_status() == user_competency::STATUS_IDLE) {
            $usercompetency->set_status(user_competency::STATUS_WAITING_FOR_REVIEW);
        }

        // Setting the grade and proficiency for the user competency.
        $wascompleted = false;
        if ($setucgrade == true) {
            if (!$usercompetency->get_proficiency() && $ucproficiency) {
                $wascompleted = true;
            }
            $usercompetency->set_grade($ucgrade);
            $usercompetency->set_proficiency($ucproficiency);
        }

        // Prepare the evidence.
        $record = new stdClass();
        $record->usercompetencyid = $usercompetency->get_id();
        $record->contextid = $contextid;
        $record->action = $action;
        $record->descidentifier = $descidentifier;
        $record->desccomponent = $desccomponent;
        $record->grade = $grade;
        $record->actionuserid = $actionuserid;
        $record->note = $note;
        $evidence = new evidence(0, $record);
        $evidence->set_desca($desca);
        $evidence->set_url($url);

        // Validate both models, we should not operate on one if the other will not save.
        if (!$usercompetency->is_valid()) {
            throw new invalid_persistent_exception($usercompetency->get_errors());
        } else if (!$evidence->is_valid()) {
            throw new invalid_persistent_exception($evidence->get_errors());
        }

        // Save the user_competency_course record.
        if ($usercompetencycourse !== null) {
            // Validate and update.
            if (!$usercompetencycourse->is_valid()) {
                throw new invalid_persistent_exception($usercompetencycourse->get_errors());
            }
            $usercompetencycourse->update();
        }

        // Finally save. Pheww!
        $usercompetency->update();
        $evidence->create();

        // Trigger the evidence_created event.
        \core\event\competency_evidence_created::create_from_evidence($evidence, $usercompetency, $recommend)->trigger();

        // The competency was marked as completed, apply the rules.
        if ($wascompleted) {
            self::apply_competency_rules_from_usercompetency($usercompetency, $competency);
        }

        return $evidence;
    }

    /**
     * Read an evidence.
     * @param int $evidenceid The evidence ID.
     * @return evidence
     */
    public static function read_evidence($evidenceid) {
        static::require_enabled();

        $evidence = new evidence($evidenceid);
        $uc = new user_competency($evidence->get_usercompetencyid());
        if (!$uc->can_read()) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:usercompetencyview',
                'nopermissions', '');
        }

        return $evidence;
    }

    /**
     * Delete an evidence.
     *
     * @param evidence|int $evidenceorid The evidence, or its ID.
     * @return bool
     */
    public static function delete_evidence($evidenceorid) {
        $evidence = $evidenceorid;
        if (!is_object($evidence)) {
            $evidence = new evidence($evidenceorid);
        }

        $uc = new user_competency($evidence->get_usercompetencyid());
        if (!evidence::can_delete_user($uc->get_userid())) {
            throw new required_capability_exception($uc->get_context(), 'moodle/competency:evidencedelete', 'nopermissions', '');
        }

        return $evidence->delete();
    }

    /**
     * Apply the competency rules from a user competency.
     *
     * The user competency passed should be one that was recently marked as complete.
     * A user competency is considered 'complete' when it's proficiency value is true.
     *
     * This method will check if the parent of this usercompetency's competency has any
     * rules and if so will see if they match. When matched it will take the required
     * step to add evidence and trigger completion, etc...
     *
     * @param  user_competency $usercompetency The user competency recently completed.
     * @param  competency|null $competency     The competency of the user competency, useful to avoid unnecessary read.
     * @return void
     */
    protected static function apply_competency_rules_from_usercompetency(user_competency $usercompetency,
                                                                         competency $competency = null) {

        // Perform some basic checks.
        if (!$usercompetency->get_proficiency()) {
            throw new coding_exception('The user competency passed is not completed.');
        }
        if ($competency === null) {
            $competency = $usercompetency->get_competency();
        }
        if ($competency->get_id() != $usercompetency->get_competencyid()) {
            throw new coding_exception('Mismatch between user competency and competency.');
        }

        // Fetch the parent.
        $parent = $competency->get_parent();
        if ($parent === null) {
            return;
        }

        // The parent should have a rule, and a meaningful outcome.
        $ruleoutcome = $parent->get_ruleoutcome();
        if ($ruleoutcome == competency::OUTCOME_NONE) {
            return;
        }
        $rule = $parent->get_rule_object();
        if ($rule === null) {
            return;
        }

        // Fetch or create the user competency for the parent.
        $userid = $usercompetency->get_userid();
        $parentuc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $parent->get_id()));
        if (!$parentuc) {
            $parentuc = user_competency::create_relation($userid, $parent->get_id());
            $parentuc->create();
        }

        // Does the rule match?
        if (!$rule->matches($parentuc)) {
            return;
        }

        // Figuring out what to do.
        $recommend = false;
        if ($ruleoutcome == competency::OUTCOME_EVIDENCE) {
            $action = evidence::ACTION_LOG;

        } else if ($ruleoutcome == competency::OUTCOME_RECOMMEND) {
            $action = evidence::ACTION_LOG;
            $recommend = true;

        } else if ($ruleoutcome == competency::OUTCOME_COMPLETE) {
            $action = evidence::ACTION_COMPLETE;

        } else {
            throw new moodle_exception('Unexpected rule outcome: ' + $ruleoutcome);
        }

        // Finally add an evidence.
        static::add_evidence(
            $userid,
            $parent,
            $parent->get_context()->id,
            $action,
            'evidence_competencyrule',
            'core_competency',
            null,
            $recommend
        );
    }

    /**
     * Observe when a course module is marked as completed.
     *
     * Note that the user being logged in while this happens may be anyone.
     * Do not rely on capability checks here!
     *
     * @param  \core\event\course_module_completion_updated $event
     * @return void
     */
    public static function observe_course_module_completion_updated(\core\event\course_module_completion_updated $event) {
        if (!static::is_enabled()) {
            return;
        }

        $eventdata = $event->get_record_snapshot('course_modules_completion', $event->objectid);

        if ($eventdata->completionstate == COMPLETION_COMPLETE
                || $eventdata->completionstate == COMPLETION_COMPLETE_PASS) {
            $coursemodulecompetencies = course_module_competency::list_course_module_competencies($eventdata->coursemoduleid);

            $cm = get_coursemodule_from_id(null, $eventdata->coursemoduleid);
            $fastmodinfo = get_fast_modinfo($cm->course)->cms[$cm->id];

            $cmname = $fastmodinfo->name;
            $url = $fastmodinfo->url;

            foreach ($coursemodulecompetencies as $coursemodulecompetency) {
                $outcome = $coursemodulecompetency->get_ruleoutcome();
                $action = null;
                $recommend = false;
                $strdesc = 'evidence_coursemodulecompleted';

                if ($outcome == course_module_competency::OUTCOME_EVIDENCE) {
                    $action = evidence::ACTION_LOG;

                } else if ($outcome == course_module_competency::OUTCOME_RECOMMEND) {
                    $action = evidence::ACTION_LOG;
                    $recommend = true;

                } else if ($outcome == course_module_competency::OUTCOME_COMPLETE) {
                    $action = evidence::ACTION_COMPLETE;

                } else {
                    throw new moodle_exception('Unexpected rule outcome: ' + $outcome);
                }

                static::add_evidence(
                    $event->relateduserid,
                    $coursemodulecompetency->get_competencyid(),
                    $event->contextid,
                    $action,
                    $strdesc,
                    'core_competency',
                    $cmname,
                    $recommend,
                    $url
                );
            }
        }
    }

    /**
     * Observe when a course is marked as completed.
     *
     * Note that the user being logged in while this happens may be anyone.
     * Do not rely on capability checks here!
     *
     * @param  \core\event\course_completed $event
     * @return void
     */
    public static function observe_course_completed(\core\event\course_completed $event) {
        if (!static::is_enabled()) {
            return;
        }

        $sql = 'courseid = :courseid AND ruleoutcome != :nooutcome';
        $params = array(
            'courseid' => $event->courseid,
            'nooutcome' => course_competency::OUTCOME_NONE
        );
        $coursecompetencies = course_competency::get_records_select($sql, $params);

        $course = get_course($event->courseid);
        $courseshortname = format_string($course->shortname, null, array('context' => $event->contextid));

        foreach ($coursecompetencies as $coursecompetency) {

            $outcome = $coursecompetency->get_ruleoutcome();
            $action = null;
            $recommend = false;
            $strdesc = 'evidence_coursecompleted';

            if ($outcome == course_competency::OUTCOME_EVIDENCE) {
                $action = evidence::ACTION_LOG;

            } else if ($outcome == course_competency::OUTCOME_RECOMMEND) {
                $action = evidence::ACTION_LOG;
                $recommend = true;

            } else if ($outcome == course_competency::OUTCOME_COMPLETE) {
                $action = evidence::ACTION_COMPLETE;

            } else {
                throw new moodle_exception('Unexpected rule outcome: ' + $outcome);
            }

            static::add_evidence(
                $event->relateduserid,
                $coursecompetency->get_competencyid(),
                $event->contextid,
                $action,
                $strdesc,
                'core_competency',
                $courseshortname,
                $recommend,
                $event->get_url()
            );
        }
    }

    /**
     * Action to perform when a course module is deleted.
     *
     * Do not call this directly, this is reserved for core use.
     *
     * @param stdClass $cm The CM object.
     * @return void
     */
    public static function hook_course_module_deleted(stdClass $cm) {
        global $DB;
        $DB->delete_records(course_module_competency::TABLE, array('cmid' => $cm->id));
    }

    /**
     * Action to perform when a course is deleted.
     *
     * Do not call this directly, this is reserved for core use.
     *
     * @param stdClass $course The course object.
     * @return void
     */
    public static function hook_course_deleted(stdClass $course) {
        global $DB;
        $DB->delete_records(course_competency::TABLE, array('courseid' => $course->id));
        $DB->delete_records(course_competency_settings::TABLE, array('courseid' => $course->id));
        $DB->delete_records(user_competency_course::TABLE, array('courseid' => $course->id));
    }

    /**
     * Action to perform when a course is being reset.
     *
     * Do not call this directly, this is reserved for core use.
     *
     * @param int $courseid The course ID.
     * @return void
     */
    public static function hook_course_reset_competency_ratings($courseid) {
        global $DB;
        $DB->delete_records(user_competency_course::TABLE, array('courseid' => $courseid));
    }

    /**
     * Action to perform when a cohort is deleted.
     *
     * Do not call this directly, this is reserved for core use.
     *
     * @param \stdClass $cohort The cohort object.
     * @return void
     */
    public static function hook_cohort_deleted(\stdClass $cohort) {
        global $DB;
        $DB->delete_records(template_cohort::TABLE, array('cohortid' => $cohort->id));
    }

    /**
     * Manually grade a user competency.
     *
     * @param int $userid
     * @param int $competencyid
     * @param int $grade
     * @param string $note A note to attach to the evidence
     * @return array of \core_competency\user_competency
     */
    public static function grade_competency($userid, $competencyid, $grade, $note = null) {
        global $USER;
        static::require_enabled();

        $uc = static::get_user_competency($userid, $competencyid);
        $context = $uc->get_context();
        if (!user_competency::can_grade_user($uc->get_userid())) {
            throw new required_capability_exception($context, 'moodle/competency:competencygrade', 'nopermissions', '');
        }

        // Throws exception if competency not in plan.
        $competency = $uc->get_competency();
        $competencycontext = $competency->get_context();
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $competencycontext)) {
            throw new required_capability_exception($competencycontext, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        $action = evidence::ACTION_OVERRIDE;
        $desckey = 'evidence_manualoverride';

        $result = self::add_evidence($uc->get_userid(),
                                  $competency,
                                  $context->id,
                                  $action,
                                  $desckey,
                                  'core_competency',
                                  null,
                                  false,
                                  null,
                                  $grade,
                                  $USER->id,
                                  $note);
        if ($result) {
            $uc->read();
            $event = \core\event\competency_user_competency_rated::create_from_user_competency($uc);
            $event->trigger();
        }
        return $result;
    }

    /**
     * Manually grade a user competency from the plans page.
     *
     * @param mixed $planorid
     * @param int $competencyid
     * @param int $grade
     * @param string $note A note to attach to the evidence
     * @return array of \core_competency\user_competency
     */
    public static function grade_competency_in_plan($planorid, $competencyid, $grade, $note = null) {
        global $USER;
        static::require_enabled();

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        $context = $plan->get_context();
        if (!user_competency::can_grade_user($plan->get_userid())) {
            throw new required_capability_exception($context, 'moodle/competency:competencygrade', 'nopermissions', '');
        }

        // Throws exception if competency not in plan.
        $competency = $plan->get_competency($competencyid);
        $competencycontext = $competency->get_context();
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $competencycontext)) {
            throw new required_capability_exception($competencycontext, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        $action = evidence::ACTION_OVERRIDE;
        $desckey = 'evidence_manualoverrideinplan';

        $result = self::add_evidence($plan->get_userid(),
                                  $competency,
                                  $context->id,
                                  $action,
                                  $desckey,
                                  'core_competency',
                                  $plan->get_name(),
                                  false,
                                  null,
                                  $grade,
                                  $USER->id,
                                  $note);
        if ($result) {
            $uc = static::get_user_competency($plan->get_userid(), $competency->get_id());
            $event = \core\event\competency_user_competency_rated_in_plan::create_from_user_competency($uc, $plan->get_id());
            $event->trigger();
        }
        return $result;
    }

    /**
     * Manually grade a user course competency from the course page.
     *
     * This may push the rating to the user competency
     * if the course is configured this way.
     *
     * @param mixed $courseorid
     * @param int $userid
     * @param int $competencyid
     * @param int $grade
     * @param string $note A note to attach to the evidence
     * @return array of \core_competency\user_competency
     */
    public static function grade_competency_in_course($courseorid, $userid, $competencyid, $grade, $note = null) {
        global $USER, $DB;
        static::require_enabled();

        $course = $courseorid;
        if (!is_object($courseorid)) {
            $course = $DB->get_record('course', array('id' => $courseorid));
        }
        $context = context_course::instance($course->id);

        // Check that we can view the user competency details in the course.
        if (!user_competency::can_read_user_in_course($userid, $course->id)) {
            throw new required_capability_exception($context, 'moodle/competency:usercompetencyview', 'nopermissions', '');
        }

        // Validate the permission to grade.
        if (!user_competency::can_grade_user_in_course($userid, $course->id)) {
            throw new required_capability_exception($context, 'moodle/competency:competencygrade', 'nopermissions', '');
        }

        // Check that competency is in course and visible to the current user.
        $competency = course_competency::get_competency($course->id, $competencyid);
        $competencycontext = $competency->get_context();
        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'),
                $competencycontext)) {
            throw new required_capability_exception($competencycontext, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        // Check that the user is enrolled in the course, and is "gradable".
        if (!is_enrolled($context, $userid, 'moodle/competency:coursecompetencygradable')) {
            throw new coding_exception('The competency may not be rated at this time.');
        }

        $action = evidence::ACTION_OVERRIDE;
        $desckey = 'evidence_manualoverrideincourse';

        $result = self::add_evidence($userid,
                                  $competency,
                                  $context->id,
                                  $action,
                                  $desckey,
                                  'core_competency',
                                  $context->get_context_name(),
                                  false,
                                  null,
                                  $grade,
                                  $USER->id,
                                  $note);
        if ($result) {
            $all = user_competency_course::get_multiple($userid, $course->id, array($competency->get_id()));
            $uc = reset($all);
            $event = \core\event\competency_user_competency_rated_in_course::create_from_user_competency_course($uc);
            $event->trigger();
        }
        return $result;
    }

    /**
     * Count the plans in the template, filtered by status.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param mixed $templateorid The id or the template.
     * @param int $status One of the plan status constants (or 0 for all plans).
     * @return int
     */
    public static function count_plans_for_template($templateorid, $status = 0) {
        static::require_enabled();
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // First we do a permissions check.
        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        return plan::count_records_for_template($template->get_id(), $status);
    }

    /**
     * Count the user-completency-plans in the template, optionally filtered by proficiency.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param mixed $templateorid The id or the template.
     * @param mixed $proficiency If true, filter by proficiency, if false filter by not proficient, if null - no filter.
     * @return int
     */
    public static function count_user_competency_plans_for_template($templateorid, $proficiency = null) {
        static::require_enabled();
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // First we do a permissions check.
        if (!$template->can_read()) {
             throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        return user_competency_plan::count_records_for_template($template->get_id(), $proficiency);
    }

    /**
     * List the plans in the template, filtered by status.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param mixed $templateorid The id or the template.
     * @param int $status One of the plan status constants (or 0 for all plans).
     * @param int $skip The number of records to skip
     * @param int $limit The max number of records to return
     * @return plan[]
     */
    public static function list_plans_for_template($templateorid, $status = 0, $skip = 0, $limit = 100) {
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // First we do a permissions check.
        if (!$template->can_read()) {
             throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        return plan::get_records_for_template($template->get_id(), $status, $skip, $limit);
    }

    /**
     * Get the most often not completed competency for this course.
     *
     * Requires moodle/competency:coursecompetencyview capability at the course context.
     *
     * @param int $courseid The course id
     * @param int $skip The number of records to skip
     * @param int $limit The max number of records to return
     * @return competency[]
     */
    public static function get_least_proficient_competencies_for_course($courseid, $skip = 0, $limit = 100) {
        static::require_enabled();
        $coursecontext = context_course::instance($courseid);

        if (!has_any_capability(array('moodle/competency:competencyview', 'moodle/competency:competencymanage'), $coursecontext)) {
            throw new required_capability_exception($coursecontext, 'moodle/competency:competencyview', 'nopermissions', '');
        }

        return user_competency_course::get_least_proficient_competencies_for_course($courseid, $skip, $limit);
    }

    /**
     * Get the most often not completed competency for this template.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param mixed $templateorid The id or the template.
     * @param int $skip The number of records to skip
     * @param int $limit The max number of records to return
     * @return competency[]
     */
    public static function get_least_proficient_competencies_for_template($templateorid, $skip = 0, $limit = 100) {
        static::require_enabled();
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // First we do a permissions check.
        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        return user_competency_plan::get_least_proficient_competencies_for_template($template->get_id(), $skip, $limit);
    }

    /**
     * Template event viewed.
     *
     * Requires moodle/competency:templateview capability at the system context.
     *
     * @param mixed $templateorid The id or the template.
     * @return boolean
     */
    public static function template_viewed($templateorid) {
        static::require_enabled();
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // First we do a permissions check.
        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'moodle/competency:templateview',
                'nopermissions', '');
        }

        // Trigger a template viewed event.
        \core\event\competency_template_viewed::create_from_template($template)->trigger();

        return true;
    }

    /**
     * Get the competency settings for a course.
     *
     * Requires moodle/competency:coursecompetencyview capability at the course context.
     *
     * @param int $courseid The course id
     * @return course_competency_settings
     */
    public static function read_course_competency_settings($courseid) {
        static::require_enabled();

        // First we do a permissions check.
        if (!course_competency_settings::can_read($courseid)) {
            $context = context_course::instance($courseid);
            throw new required_capability_exception($context, 'moodle/competency:coursecompetencyview', 'nopermissions', '');
        }

        return course_competency_settings::get_by_courseid($courseid);
    }

    /**
     * Update the competency settings for a course.
     *
     * Requires moodle/competency:coursecompetencyconfigure capability at the course context.
     *
     * @param int $courseid The course id
     * @param stdClass $settings List of settings. The only valid setting ATM is pushratginstouserplans (boolean).
     * @return bool
     */
    public static function update_course_competency_settings($courseid, $settings) {
        static::require_enabled();

        $settings = (object) $settings;

        // Get all the valid settings.
        $pushratingstouserplans = isset($settings->pushratingstouserplans) ? $settings->pushratingstouserplans : false;

        // First we do a permissions check.
        if (!course_competency_settings::can_manage_course($courseid)) {
            $context = context_course::instance($courseid);
            throw new required_capability_exception($context, 'moodle/competency:coursecompetencyconfigure', 'nopermissions', '');
        }

        $exists = course_competency_settings::get_record(array('courseid' => $courseid));

        // Now update or insert.
        if ($exists) {
            $settings = $exists;
            $settings->set_pushratingstouserplans($pushratingstouserplans);
            return $settings->update();
        } else {
            $data = (object) array('courseid' => $courseid, 'pushratingstouserplans' => $pushratingstouserplans);
            $settings = new course_competency_settings(0, $data);
            $result = $settings->create();
            return !empty($result);
        }
    }


    /**
     * Function used to return a list of users where the given user has a particular capability.
     *
     * This is used e.g. to find all the users where someone is able to manage their learning plans,
     * it also would be useful for mentees etc.
     *
     * @param string $capability - The capability string we are filtering for. If '' is passed,
     *                             an always matching filter is returned.
     * @param int $userid - The user id we are using for the access checks. Defaults to current user.
     * @param int $type - The type of named params to return (passed to $DB->get_in_or_equal).
     * @param string $prefix - The type prefix for the db table (passed to $DB->get_in_or_equal).
     * @return list($sql, $params) Same as $DB->get_in_or_equal().
     * @todo MDL-52243 Move this function to lib/accesslib.php
     */
    public static function filter_users_with_capability_on_user_context_sql($capability, $userid = 0, $type = SQL_PARAMS_QM,
                                                                            $prefix='param') {

        global $USER, $DB;
        $allresultsfilter = array('> 0', array());
        $noresultsfilter = array('= -1', array());

        if (empty($capability)) {
            return $allresultsfilter;
        }

        if (!$capinfo = get_capability_info($capability)) {
            throw new coding_exception('Capability does not exist: ' . $capability);
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        // Make sure the guest account and not-logged-in users never get any risky caps no matter what the actual settings are.
        if (($capinfo->captype === 'write') or ($capinfo->riskbitmask & (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))) {
            if (isguestuser($userid) or $userid == 0) {
                return $noresultsfilter;
            }
        }

        if (is_siteadmin($userid)) {
            // No filtering for site admins.
            return $allresultsfilter;
        }

        // Check capability on system level.
        $syscontext = context_system::instance();
        $hassystem = has_capability($capability, $syscontext, $userid);

        $access = get_user_access_sitewide($userid);
        // Build up a list of level 2 contexts (candidates to be user context).
        $filtercontexts = array();
        foreach ($access['ra'] as $path => $role) {
            $parts = explode('/', $path);
            if (count($parts) == 3) {
                $filtercontexts[$parts[2]] = $parts[2];
            } else if (count($parts) > 3) {
                // We know this is not a user context because there is another path with more than 2 levels.
                unset($filtercontexts[$parts[2]]);
            }
        }

        // Add all contexts in which a role may be overidden.
        foreach ($access['rdef'] as $pathandroleid => $def) {
            $matches = array();
            if (!isset($def[$capability])) {
                // The capability is not mentioned, we can ignore.
                continue;
            }

            list($contextpath, $roleid) = explode(':', $pathandroleid, 2);
            $parts = explode('/', $contextpath);
            if (count($parts) != 3) {
                // Only get potential user contexts, they only ever have 2 slashes /parentId/Id.
                continue;
            }

            $filtercontexts[$parts[2]] = $parts[2];
        }

        // No interesting contexts - return all or no results.
        if (empty($filtercontexts)) {
            if ($hassystem) {
                return $allresultsfilter;
            } else {
                return $noresultsfilter;
            }
        }
        // Fetch all interesting contexts for further examination.
        list($insql, $params) = $DB->get_in_or_equal($filtercontexts, SQL_PARAMS_NAMED);
        $params['level'] = CONTEXT_USER;
        $fields = context_helper::get_preload_record_columns_sql('ctx');
        $interestingcontexts = $DB->get_recordset_sql('SELECT ' . $fields . '
                                                       FROM {context} ctx
                                                       WHERE ctx.contextlevel = :level
                                                         AND ctx.id ' . $insql . '
                                                       ORDER BY ctx.id', $params);
        if ($hassystem) {
            // If allowed at system, search for exceptions prohibiting the capability at user context.
            $excludeusers = array();
            foreach ($interestingcontexts as $contextrecord) {
                $candidateuserid = $contextrecord->ctxinstance;
                context_helper::preload_from_record($contextrecord);
                $usercontext = context_user::instance($candidateuserid);
                // Has capability should use the data already preloaded.
                if (!has_capability($capability, $usercontext, $userid)) {
                    $excludeusers[$candidateuserid] = $candidateuserid;
                }
            }

            // Construct SQL excluding users with this role assigned for this user.
            if (empty($excludeusers)) {
                $interestingcontexts->close();
                return $allresultsfilter;
            }
            list($sql, $params) = $DB->get_in_or_equal($excludeusers, $type, $prefix, false);
        } else {
            // If not allowed at system, search for exceptions allowing the capability at user context.
            $allowusers = array();
            foreach ($interestingcontexts as $contextrecord) {
                $candidateuserid = $contextrecord->ctxinstance;
                context_helper::preload_from_record($contextrecord);
                $usercontext = context_user::instance($candidateuserid);
                // Has capability should use the data already preloaded.
                if (has_capability($capability, $usercontext, $userid)) {
                    $allowusers[$candidateuserid] = $candidateuserid;
                }
            }

            // Construct SQL excluding users with this role assigned for this user.
            if (empty($allowusers)) {
                $interestingcontexts->close();
                return $noresultsfilter;
            }
            list($sql, $params) = $DB->get_in_or_equal($allowusers, $type, $prefix);
        }
        $interestingcontexts->close();

        // Return the goods!.
        return array($sql, $params);
    }

}
