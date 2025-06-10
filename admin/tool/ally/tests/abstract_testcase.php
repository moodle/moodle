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
 * Base test case.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

global $CFG;

use tool_ally\local;
use tool_ally\local_content;
use tool_ally\models\component;
use tool_ally\models\component_content;

require_once($CFG->dirroot.'/webservice/tests/helpers.php');

/**
 * Base test case.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class abstract_testcase extends \externallib_advanced_testcase {
    /**
     * Given a resource activity, return its associated file.
     *
     * @param stdClass $resource
     * @return stored_file
     * @throws coding_exception
     */
    protected function get_resource_file($resource) {
        $context = \context_module::instance($resource->cmid);
        $files   = get_file_storage()->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);

        if (count($files) < 1) {
            throw new \coding_exception('Failed to find any files associated to resource activity');
        }

        return reset($files);
    }

    /**
     * Given an assign activity, return an associated file in a whitelisted filearea.
     *
     * @param stdClass $module
     * @param string $name
     * @return stored_file
     * @throws coding_exception
     */
    protected function create_whitelisted_assign_file($module, $name = '') {
        return $this->create_assign_file($module, 'introattachment', $name);

    }

    /**
     * Given an assign activity, return an associated file in not whitelisted filearea.
     *
     * @param stdClass $module
     * @param string $name
     * @return stored_file
     * @throws coding_exception
     */
    protected function create_notwhitelisted_assign_file($module, $name = '') {
        return $this->create_assign_file($module, 'notwhitelisted', $name);
    }

    /**
     * Creates a file for a given mod_assign and filearea.
     *
     * @param stdClass $module
     * @param string $filearea
     * @param string $name
     * @return stored_file
     * @throws coding_exception
     */
    private function create_assign_file($module, $filearea, $name = '') {
        $context = \context_module::instance($module->cmid);

        $fs = get_file_storage();

        // Prepare file record object.
        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'mod_assign',
            'filearea' => $filearea,
            'itemid' => 0,
            'filepath' => '/',
            'filename' => empty($name) ? 'myfile.txt' : $name);

        // Create file containing text 'hello world'.
        return $fs->create_file_from_string($fileinfo, 'hello world');

    }

    /**
     * Create and return draft file.
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function create_draft_file($filename = 'red dot.png') {
        global $USER;
        $usercontext = \context_user::instance($USER->id);
        $filecontent = "iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38"
            . "GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==";
        $draftfile = \core_files_external::upload($usercontext->id, 'user', 'draft', 0, '/', $filename, $filecontent, null, null);
        $draftfile['filecontent'] = $filecontent;
        return $draftfile;
    }

    /**
     * Create test file.
     * @param int $contextid
     * @param string $component
     * @param string $filearea
     * @return stored_file
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    protected function create_test_file($contextid, $component, $filearea, $itemid = 0, $filename = 'gd logo.png') {
        global $CFG;
        $filepath = $CFG->libdir.'/tests/fixtures/gd-logo.png';
        $filerecord = array(
            'contextid' => $contextid,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = \get_file_storage();
        $file = $fs->create_file_from_pathname($filerecord, $filepath);
        return $file;
    }

    /**
     * Assert that two stored files are the same.
     *
     * @param stored_file $expected
     * @param stored_file $actual
     */
    public function assertStoredFileEquals(\stored_file $expected, \stored_file $actual) { // @codingStandardsIgnoreLine
        $this->assertEquals($expected->get_pathnamehash(), $actual->get_pathnamehash(), 'Stored files should be the same');
    }

    /**
     * Verifies if a file and an array of attributes gotten via webservice match.
     * @param stdClass $course
     * @param stored_file $expectedfile
     * @param array $servicefile
     */
    protected function match_files($course, $expectedfile, $servicefile) {
        $this->assertEquals($expectedfile->get_pathnamehash(), $servicefile['id']);
        $this->assertEquals($course->id, $servicefile['courseid']);
        $this->assertEquals($expectedfile->get_filename(), $servicefile['name']);
        $this->assertEquals($expectedfile->get_mimetype(), $servicefile['mimetype']);
        $this->assertEquals($expectedfile->get_contenthash(), $servicefile['contenthash']);
        $this->assertEquals($expectedfile->get_timemodified(), local::iso_8601_to_timestamp($servicefile['timemodified']));
    }


    /**
     * Is a component model instance within an array?
     * @param component $component
     * @param array $contentitems
     * @return bool
     */
    protected function assert_component_is_in_array(component $component, array $contentitems) {
        $fields = ['component', 'table', 'field', 'courseid', 'contentformat', 'title'];

        $found = false;
        foreach ($contentitems as $item) {
            $fcount = 0;
            $expected = count($fields);
            foreach ($fields as $field) {
                if ($item->$field === $component->$field) {
                    $fcount ++;
                }
            }
            if ($found = $found || ($fcount === $expected)) {
                break;
            }
        }

        $this->assertTrue($found, 'Failed to find on component within content item set');
    }

    /**
     * Is component content model within an array?
     * @param component_content $component
     * @param array $contentitems
     * @return bool
     */
    protected function component_content_is_in_array(component_content $componentcontent, array $contentitems) {
        $fields = ['content', 'id', 'component', 'table', 'field', 'contentformat', 'title'];

        foreach ($contentitems as $item) {
            $nomatch = false;
            foreach ($fields as $field) {
                if ($item->$field !== $componentcontent->$field) {
                    $nomatch = true;
                    break;
                }
            }
            if (!$nomatch) {
                // Matched all appropriate fields.
                return true;
            }
        }

        return false;
    }

    /**
     * Return an array of file ids that are considered to be present in the given context.
     * Uses files_iterator and file_validator to determine which files are included.
     *
     * @param context $context
     * @return array
     */
    protected function get_file_ids_in_context(\context $context): array {
        $files = \tool_ally\local_file::iterator();
        $files->in_context($context);
        $fileids = [];
        foreach ($files as $file) {
            $fileids[] = $file->get_id();
        }

        return $fileids;
    }

    /**
     * Creates two files to be used for file use testing.
     *
     * @param context $context
     * @param string $componentstr
     * @param string $filearea
     * @param int $itemid
     * @param stdClass|null $user
     * @return stored_file[]
     */
    protected function setup_check_files(\context $context,
                                         string $componentstr,
                                         string $filearea,
                                         int $itemid,
                                         \stdClass $user = null): array {

        $generator = $this->getDataGenerator()->get_plugin_generator('tool_ally');

        $record = [
            'contextid' => $context->id,
            'component' => $componentstr,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
        ];

        if (!is_null($user)) {
            $record['userid'] = $user->id;
        }

        $file1 = $generator->create_file($record);
        $file2 = $generator->create_file($record);

        return [$file1, $file2];
    }

    /**
     * Checks a provided component table/field to see if HTML 'in use' checking is working for associated files.
     * Returns the used and unused files for use for checking.
     *
     * @param context $context
     * @param string $componentstr
     * @param int $id
     * @param string $table
     * @param string $field
     * @param stdClass|null $user
     * @return stored_file[] First file is the used file, second is unused.
     */
    protected function check_html_files_in_use(\context $context,
                                               string $componentstr,
                                               int $id,
                                               string $table,
                                               string $field,
                                               \stdClass $user = null): array {
        global $DB;

        $component = local_content::component_instance($componentstr);
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_ally');

        list($usedfile, $unusedfile) = $this->setup_check_files($context, $componentstr, $component->get_file_area($table, $field),
            $component->get_file_item($table, $field, $id), $user);

        // Update the intro with the link.
        $link = $generator->create_pluginfile_link_for_file($usedfile);
        $DB->set_field($table, $field, $link, ['id' => $id]);
        $DB->set_field($table, $field . 'format', FORMAT_HTML, ['id' => $id]);

        // Now check it.
        $this->assertTrue($component->check_file_in_use($usedfile));
        $this->assertFalse($component->check_file_in_use($unusedfile));

        // Return the files, in case further test want to be done.
        return [$usedfile, $unusedfile];
    }

    /**
     * Take a context and checks the settings of excludeunused to see if results are expected.
     *
     * @param context $context
     * @param array $usedfiles
     * @param array $unusedfiles
     */
    protected function check_file_iterator_exclusion(\context $context, array $usedfiles, array $unusedfiles): void {
        set_config('excludeunused', 1, 'tool_ally');

        // And we are going to get all the files in the context to double check.
        $fileids = $this->get_file_ids_in_context($context);
        $this->assertCount(count($usedfiles), $fileids);

        // See if each file is used or not used as expected.
        foreach ($usedfiles as $file) {
            $this->assertTrue(in_array($file->get_id(), $fileids));
        }
        foreach ($unusedfiles as $file) {
            $this->assertFalse(in_array($file->get_id(), $fileids));
        }

        set_config('excludeunused', 0, 'tool_ally');

        // Merge them together and see that they are now all present.
        $allfiles = array_merge($usedfiles, $unusedfiles);

        $fileids = $this->get_file_ids_in_context($context);
        $this->assertCount(count($allfiles), $fileids);

        foreach ($allfiles as $file) {
            $this->assertTrue(in_array($file->get_id(), $fileids));
        }
    }
}
