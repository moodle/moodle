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
 * This file keeps track of upgrades to
 * the lesson module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @param int $oldversion
 * @return bool
 */
function xmldb_lesson_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013050101) {
        // Fixed page order for missing page in lesson
        upgrade_set_timeout(600);  // increase excution time for large sites

        $lessons = $DB->get_records('lesson');

        foreach ($lessons as $lesson) {
            $pages = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id));

            $iscorrupt = false;

            // Validate lesson prev and next pages.
            foreach ($pages as $id => $page) {
                // Setting up prev and next id to 0 is only valid if lesson only has 1 page.
                // Other than that, it indicates lesson page links are corrupted.
                if ($page->prevpageid == 0 && $page->nextpageid == 0 && count($pages) != 1) {
                    $iscorrupt = true;
                    break;
                // Make sure page links to an existing page within the lesson.
                } else if (($page->prevpageid != 0 && !isset($pages[$page->prevpageid])) ||
                    ($page->nextpageid != 0 && !isset($pages[$page->nextpageid]))) {
                    $iscorrupt = true;
                    break;
                //  Check the pages linked correctly
                } else if((($page->prevpageid == 0 || $page->nextpageid != 0 ) && $pages[$page->nextpageid]->prevpageid != $page->id) ||
                    (($page->prevpageid != 0 || $page->nextpageid == 0) && $pages[$page->prevpageid]->nextpageid != $page->id)) {
                    $iscorrupt = true;
                    break;
                }
            }

            // Process the update for the corrupted lesson pages.
            $count = 0;
            $lastpageid = 0;
            if ($iscorrupt) {
                foreach($pages as $page) {
                    $count++;
                    if ($lastpageid == 0) {  // First page
                        $DB->set_field('lesson_pages', 'prevpageid', 0, array('id' => $page->id));
                        $DB->set_field('lesson_pages', 'nextpageid', 0, array('id' => $page->id));
                    } elseif (count($pages) == $count) {
                        $DB->set_field('lesson_pages', 'prevpageid', $lastpageid, array('id' => $page->id));
                        $DB->set_field('lesson_pages', 'nextpageid', 0, array('id' => $page->id));
                        $DB->set_field('lesson_pages', 'nextpageid', $page->id, array('id' => $lastpageid));
                    } else {
                        $DB->set_field('lesson_pages', 'prevpageid', $lastpageid, array('id' => $page->id));
                        $DB->set_field('lesson_pages', 'nextpageid', $page->id, array('id' => $lastpageid));
                    }
                    $lastpageid = $page->id;
                }
            }
        }
        upgrade_mod_savepoint(true, 2013050101, 'lesson');
    }
    return true;
}
