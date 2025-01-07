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
 * Entry caching for glossary filter.
 *
 * @package    mod_glossary
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\local;
defined('MOODLE_INTERNAL') || die();

/**
 * Concept caching for glossary filter.
 *
 * @package    mod_glossary
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class concept_cache {
    /**
     * Event observer, do not call directly.
     * @param \core\event\course_module_updated $event
     */
    public static function cm_updated(\core\event\course_module_updated $event) {
        if ($event->other['modulename'] !== 'glossary') {
            return;
        }
        // We do not know what changed exactly, so let's reset everything that might be affected.
        concept_cache::reset_course_muc($event->courseid);
        concept_cache::reset_global_muc();
    }

    /**
     * Reset concept related caches.
     * @param bool $phpunitreset
     */
    public static function reset_caches($phpunitreset = false) {
        if ($phpunitreset) {
            return;
        }
        $cache = \cache::make('mod_glossary', 'concepts');
        $cache->purge();
    }

    /**
     * Reset the cache for course concepts.
     * @param int $courseid
     */
    public static function reset_course_muc($courseid) {
        if (empty($courseid)) {
            return;
        }
        $cache = \cache::make('mod_glossary', 'concepts');
        $cache->delete((int)$courseid);
    }

    /**
     * Reset the cache for global concepts.
     */
    public static function reset_global_muc() {
        $cache = \cache::make('mod_glossary', 'concepts');
        $cache->delete(0);
    }

    /**
     * Utility method to purge caches related to given glossary.
     * @param \stdClass $glossary
     */
    public static function reset_glossary($glossary) {
        if (!$glossary->usedynalink) {
            return;
        }
        self::reset_course_muc($glossary->course);
        if ($glossary->globalglossary) {
            self::reset_global_muc();
        }
    }

    /**
     * Fetch concepts for given glossaries.
     * @param int[] $glossaries
     * @return array
     */
    protected static function fetch_concepts(array $glossaries) {
        global $DB;

        $glossarylist = implode(',', $glossaries);

        $sql = "SELECT id, glossaryid, concept, casesensitive, 0 AS category, fullmatch
                  FROM {glossary_entries}
                 WHERE glossaryid IN ($glossarylist) AND usedynalink = 1 AND approved = 1

                 UNION

                SELECT id, glossaryid, name AS concept, 1 AS casesensitive, 1 AS category, 1 AS fullmatch
                  FROM {glossary_categories}
                 WHERE glossaryid IN ($glossarylist) AND usedynalink = 1

                UNION

                SELECT ge.id, ge.glossaryid, ga.alias AS concept, ge.casesensitive, 0 AS category, ge.fullmatch
                  FROM {glossary_alias} ga
                  JOIN {glossary_entries} ge ON (ga.entryid = ge.id)
                 WHERE ge.glossaryid IN ($glossarylist) AND ge.usedynalink = 1 AND ge.approved = 1";

        $concepts = array();
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $concept) {
            $currentconcept = trim($concept->concept);

            // Turn ampersands into &amp; but keep HTML format for filters.
            $currentconcept = replace_ampersands_not_followed_by_entity($currentconcept);

            if (empty($currentconcept)) {
                continue;
            }

            // Rule out any small integers, see MDL-1446.
            if (is_number($currentconcept) and $currentconcept < 1000) {
                continue;
            }

            $concept->concept = $currentconcept;

            $concepts[$concept->glossaryid][] = $concept;
        }
        $rs->close();

        return $concepts;
    }

    /**
     * Get all linked concepts from course.
     * @param int $courseid
     * @return array
     */
    protected static function get_course_concepts($courseid) {
        global $DB;

        if (empty($courseid)) {
            return array(array(), array());
        }

        $courseid = (int)$courseid;

        // Get info on any glossaries in this course.
        $modinfo = get_fast_modinfo($courseid);
        $cminfos = $modinfo->get_instances_of('glossary');
        if (!$cminfos) {
            // No glossaries in this course, so don't do any work.
            return array(array(), array());
        }

        $cache = \cache::make('mod_glossary', 'concepts');
        $data = $cache->get($courseid);
        if (is_array($data)) {
            list($glossaries, $allconcepts) = $data;

        } else {
            // Find all course glossaries.
            $sql = "SELECT g.id, g.name
                      FROM {glossary} g
                      JOIN {course_modules} cm ON (cm.instance = g.id)
                      JOIN {modules} m ON (m.name = 'glossary' AND m.id = cm.module)
                     WHERE g.usedynalink = 1 AND g.course = :course AND cm.visible = 1 AND m.visible = 1
                  ORDER BY g.globalglossary, g.id";
            $glossaries = $DB->get_records_sql_menu($sql, array('course' => $courseid));
            if (!$glossaries) {
                $data = array(array(), array());
                $cache->set($courseid, $data);
                return $data;
            }
            foreach ($glossaries as $id => $name) {
                $name = str_replace(':', '-', $name);
                $glossaries[$id] = $name;
            }

            $allconcepts = self::fetch_concepts(array_keys($glossaries));
            foreach ($glossaries as $gid => $unused) {
                if (!isset($allconcepts[$gid])) {
                    unset($glossaries[$gid]);
                }
            }
            if (!$glossaries) {
                // This means there are no interesting concepts in the existing glossaries.
                $data = array(array(), array());
                $cache->set($courseid, $data);
                return $data;
            }
            $cache->set($courseid, array($glossaries, $allconcepts));
        }

        $concepts = $allconcepts;

        // Verify access control to glossary instances.
        foreach ($concepts as $modid => $unused) {
            if (!isset($cminfos[$modid])) {
                // This should not happen.
                unset($concepts[$modid]);
                unset($glossaries[$modid]);
                continue;
            }
            if (!$cminfos[$modid]->uservisible) {
                unset($concepts[$modid]);
                unset($glossaries[$modid]);
                continue;
            }
        }

        return array($glossaries, $concepts);
    }

    /**
     * Get all linked global concepts.
     * @return array
     */
    protected static function get_global_concepts() {
        global $DB;

        $cache = \cache::make('mod_glossary', 'concepts');
        $data = $cache->get(0);
        if (is_array($data)) {
            list($glossaries, $allconcepts) = $data;

        } else {
            // Find all global glossaries - no access control here.
            $sql = "SELECT g.id, g.name
                      FROM {glossary} g
                      JOIN {course_modules} cm ON (cm.instance = g.id)
                      JOIN {modules} m ON (m.name = 'glossary' AND m.id = cm.module)
                     WHERE g.usedynalink = 1 AND g.globalglossary = 1 AND cm.visible = 1 AND m.visible = 1
                  ORDER BY g.globalglossary, g.id";
            $glossaries = $DB->get_records_sql_menu($sql);
            if (!$glossaries) {
                $data = array(array(), array());
                $cache->set(0, $data);
                return $data;
            }
            foreach ($glossaries as $id => $name) {
                $name = str_replace(':', '-', $name);
                $glossaries[$id] = replace_ampersands_not_followed_by_entity($name);
            }
            $allconcepts = self::fetch_concepts(array_keys($glossaries));
            foreach ($glossaries as $gid => $unused) {
                if (!isset($allconcepts[$gid])) {
                    unset($glossaries[$gid]);
                }
            }
            $cache->set(0, array($glossaries, $allconcepts));
        }

        // NOTE: no access control is here because it would be way too expensive to check access
        //       to all courses that contain the global glossaries.
        return array($glossaries, $allconcepts);
    }

    /**
     * Get all concepts that should be linked in the given course.
     * @param int $courseid
     * @return array with two elements - array of glossaries and concepts for each glossary
     */
    public static function get_concepts($courseid) {
        list($glossaries, $concepts) = self::get_course_concepts($courseid);
        list($globalglossaries, $globalconcepts) = self::get_global_concepts();

        foreach ($globalconcepts as $gid => $cs) {
            if (!isset($concepts[$gid])) {
                $concepts[$gid] = $cs;
            }
        }
        foreach ($globalglossaries as $gid => $name) {
            if (!isset($glossaries[$gid])) {
                $glossaries[$gid] = $name;
            }
        }

        return array($glossaries, $concepts);
    }
}
