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

namespace mod_wiki;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->dirroot . '/mod/wiki/pagelib.php');

/**
 * Unit tests for mod_wiki page classes.
 *
 * @package    mod_wiki
 * @copyright  2026 Vincent Schneider <vincent.schneider@sudile.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversMethod(\page_wiki_viewversion::class, 'set_versionid')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\page_wiki_restoreversion::class, 'set_versionid')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\page_wiki_confirmrestore::class, 'set_versionid')]
final class pagelib_test extends \advanced_testcase {
    /**
     * A version belonging to another page must not resolve on the current page.
     *
     * @param string $classname Page class exposing set_versionid().
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('version_page_classes_provider')]
    public function test_set_versionid_rejects_foreign_version(string $classname): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        $datagen = $this->getDataGenerator();
        $wikigen = $datagen->get_plugin_generator('mod_wiki');
        $course = $datagen->create_course();
        $wiki = $datagen->create_module('wiki', ['course' => $course->id]);

        // Page the caller legitimately owns, plus a second page whose version is foreign to it.
        $ownpage = $wikigen->create_first_page($wiki);
        $ownversion = wiki_get_current_version($ownpage->id);
        $otherpage = $wikigen->create_content($wiki, ['title' => 'Other page']);
        $foreignversion = wiki_get_current_version($otherpage->id);

        $cm = get_coursemodule_from_instance('wiki', $wiki->id);
        $subwiki = wiki_get_subwiki($ownpage->subwikiid);

        // Set up the page the same way the entry-point scripts do before constructing
        // the wiki page object (see mod/forum/tests/lib_test.php for the same idiom).
        $PAGE = new \moodle_page();
        $PAGE->set_context(\context_module::instance($cm->id));
        $PAGE->set_cm($cm, $course, $wiki);

        $page = new $classname($wiki, $subwiki, $cm);
        $page->set_page($ownpage);

        // The caller's own version resolves without error.
        $page->set_versionid($ownversion->id);

        // A version belonging to another page is rejected.
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('versionerror', 'wiki'));
        $page->set_versionid($foreignversion->id);
    }

    /**
     * Page classes whose set_versionid() must enforce page ownership.
     *
     * @return array
     */
    public static function version_page_classes_provider(): array {
        return [
            'viewversion' => [\page_wiki_viewversion::class],
            'restoreversion' => [\page_wiki_restoreversion::class],
            'confirmrestore' => [\page_wiki_confirmrestore::class],
        ];
    }
}
