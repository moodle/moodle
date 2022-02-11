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

namespace enrol_lti\local\ltiadvantage\repository;
use core_availability\info_module;
use enrol_lti\local\ltiadvantage\viewobject\published_resource;

/**
 * Class published_resource_repository for fetching the published_resource instances from the store.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class published_resource_repository {

    /**
     * Convert a list of stdClasses to a list of published_resource instances.
     *
     * @param array $records the records.
     * @return array the array of published_resource instances.
     */
    private function published_resources_from_records(array $records): array {
        $publishedresources = [];
        foreach ($records as $record) {
            $publishedresource = new published_resource(
                $record->name,
                $record->coursefullname,
                $record->courseid,
                $record->contextid,
                $record->id,
                $record->uuid,
                $record->supportsgrades,
                $record->grademax ?? null,
                $record->iscourse,
            );
            $publishedresources[] = $publishedresource;
        }
        return $publishedresources;
    }

    /**
     * Given a list of published resources, return a list of those which are available to the provided user.
     *
     * @param array $resources the array of records representing published resources.
     * @param int $userid the Moodle user id to check.
     * @return array an array of stdClasses containing data about resources which are available to the current user.
     */
    private function get_available_resources_from_records(array $resources, int $userid): array {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->libdir . '/moodlelib.php');

        $availableresources = [];

        foreach ($resources as $resource) {
            if ($resource->contextlevel == CONTEXT_COURSE) {
                // Shared item is a course.
                if (!can_access_course(get_course($resource->courseid), $userid)) {
                    continue;
                }
                $resource->name = format_string($resource->coursefullname, true, ['context' => $resource->contextid]);
                $resource->coursefullname = $resource->name;
                $resource->iscourse = true;
                $resource->supportsgrades = true;

                $coursegradeitem = \grade_item::fetch_course_item($resource->courseid);
                $resource->grademax = $coursegradeitem->grademax;

                $availableresources[] = $resource;
            } else if ($resource->contextlevel = CONTEXT_MODULE) {
                // Shared item is a module.
                $resource->coursefullname = format_string($resource->coursefullname, true,
                    ['context' => $resource->contextid]);

                $mods = get_fast_modinfo($resource->courseid, $userid)->get_cms();
                foreach ($mods as $mod) {
                    if ($mod->context->id == $resource->contextid) {
                        if (info_module::is_user_visible($mod->id, $userid, true)) {

                            $resource->iscourse = false;
                            $resource->name = $mod->name;
                            $resource->supportsgrades = false;
                            $resource->grademax = null;

                            // Only activities with GRADE_TYPE_VALUE are valid.
                            if (plugin_supports('mod', $mod->modname, FEATURE_GRADE_HAS_GRADE)) {
                                $gradeitem = \grade_item::fetch([
                                    'courseid' => $resource->courseid,
                                    'itemtype' => 'mod',
                                    'itemmodule' => $mod->modname,
                                    'iteminstance' => $mod->instance
                                ]);
                                if ($gradeitem && $gradeitem->gradetype == GRADE_TYPE_VALUE) {
                                    $gradinginfo = grade_get_grades($resource->courseid, 'mod', $mod->modname,
                                        $mod->instance);
                                    $resource->supportsgrades = true;
                                    $resource->grademax = (int) $gradinginfo->items[0]->grademax;
                                }
                            }
                            $availableresources[] = $resource;
                        }
                    }
                }
            }
        }
        return $availableresources;
    }

    /**
     * Find all published resources which are visible to the given user.
     *
     * @param int $userid the id of the user to check.
     * @return published_resource[] an array of published_resource view objects instances.
     */
    public function find_all_for_user(int $userid): array {
        global $DB, $CFG;
        require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        require_once($CFG->libdir . '/modinfolib.php');
        require_once($CFG->libdir . '/weblib.php');

        [$insql, $inparams] = $DB->get_in_or_equal(['LTI-1p3'], SQL_PARAMS_NAMED);
        $sql = "SELECT elt.id, elt.uuid, elt.enrolid, elt.contextid, elt.institution, elt.lang, elt.timezone,
                       elt.maxenrolled, elt.maildisplay, elt.city, elt.country, elt.gradesync, elt.gradesynccompletion,
                       elt.membersync, elt.membersyncmode, elt.roleinstructor, elt.rolelearner, e.name AS enrolname,
                       e.courseid, ctx.contextlevel, c.fullname AS coursefullname
                  FROM {enrol} e
                  JOIN {enrol_lti_tools} elt
                    ON (e.id = elt.enrolid and e.status = :enrolstatusenabled)
                  JOIN {course} c
                    ON (c.id = e.courseid)
                  JOIN {context} ctx
                    ON (ctx.id = elt.contextid)
                 WHERE elt.ltiversion $insql
              ORDER BY courseid";
        $params = array_merge($inparams, ['enrolstatusenabled' => ENROL_INSTANCE_ENABLED]);
        $resources = $DB->get_records_sql($sql, $params);

        // Only users who have the ability to publish content should see published content.
        $resources = array_filter($resources, function($resource) use ($userid) {
            return has_capability('enrol/lti:config', \context_course::instance($resource->courseid), $userid);
        });

        // Make sure the user can access each course or module, excluding those which are inaccessible from the return.
        $availableresources = $this->get_available_resources_from_records($resources, $userid);

        return $this->published_resources_from_records($availableresources);
    }

    /**
     * Find all published_resource instances matching the supplied ids for the current user.
     *
     * @param array $ids the array containing object ids to lookup
     * @param int $userid the id of the user to check
     * @return array an array of published_resource instances which are available to the user.
     */
    public function find_all_by_ids_for_user(array $ids, int $userid): array {
        global $DB, $CFG;

        if (empty($ids)) {
            return [];
        }

        require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        require_once($CFG->libdir . '/modinfolib.php');
        require_once($CFG->libdir . '/weblib.php');

        [$insql, $inparams] = $DB->get_in_or_equal(['LTI-1p3'], SQL_PARAMS_NAMED);
        [$idsinsql, $idsinparams] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $sql = "SELECT elt.id, elt.uuid, elt.enrolid, elt.contextid, elt.institution, elt.lang, elt.timezone,
                       elt.maxenrolled, elt.maildisplay, elt.city, elt.country, elt.gradesync, elt.gradesynccompletion,
                       elt.membersync, elt.membersyncmode, elt.roleinstructor, elt.rolelearner, e.name AS enrolname,
                       e.courseid, ctx.contextlevel, c.fullname AS coursefullname
                  FROM {enrol} e
                  JOIN {enrol_lti_tools} elt
                    ON (e.id = elt.enrolid and e.status = :enrolstatusenabled)
                  JOIN {course} c
                    ON (c.id = e.courseid)
                  JOIN {context} ctx
                    ON (ctx.id = elt.contextid)
                 WHERE elt.ltiversion $insql
                   AND elt.id $idsinsql
              ORDER BY courseid";
        $params = array_merge($inparams, $idsinparams, ['enrolstatusenabled' => ENROL_INSTANCE_ENABLED]);
        $resources = $DB->get_records_sql($sql, $params);

        // Make sure the user can access each course or module, excluding those which are inaccessible from the return.
        $availableresources = $this->get_available_resources_from_records($resources, $userid);

        return $this->published_resources_from_records($availableresources);
    }
}
