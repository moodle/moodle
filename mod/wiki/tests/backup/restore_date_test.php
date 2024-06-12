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

namespace mod_wiki\backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

/**
 * Restore date tests.
 *
 * @package    mod_wiki
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_date_test extends \restore_date_testcase {

    /**
     * Test restore dates.
     */
    public function test_restore_dates(): void {
        global $DB;

        $record = ['editbegin' => 100, 'editend' => 100, 'timemodified' => 100];
        list($course, $wiki) = $this->create_course_and_module('wiki', $record);

        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');
        $page = $wikigenerator->create_first_page($wiki);
        $version = $DB->get_record('wiki_versions', ['pageid' => $page->id, 'version' => 1]);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newwiki = $DB->get_record('wiki', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($wiki, $newwiki, ['timecreated', 'timemodified']);
        $props = ['editend', 'editbegin'];
        $this->assertFieldsRolledForward($wiki, $newwiki, $props);

        $newsubwiki = $DB->get_record('wiki_subwikis', ['wikiid' => $newwiki->id]);
        $newpage = $DB->get_record('wiki_pages', ['subwikiid' => $newsubwiki->id]);
        $newversion = $DB->get_record('wiki_versions', ['pageid' => $newpage->id, 'version' => 1]);

        // Wiki page time checks.
        $this->assertEquals($page->timecreated, $newpage->timecreated);
        $this->assertEquals($page->timemodified, $newpage->timemodified);
        $this->assertEquals($page->timerendered, $newpage->timerendered);

        // Wiki version time checks.
        $this->assertEquals($version->timecreated, $newversion->timecreated);

    }
}
