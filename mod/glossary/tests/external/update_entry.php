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
 * External function test for update_entry.
 *
 * @package    mod_glossary
 * @category   external
 * @since      Moodle 3.10
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_external\external_api;
use externallib_advanced_testcase;
use mod_glossary_external;
use context_module;
use context_user;
use core_external\util as external_util;

/**
 * External function test for update_entry.
 *
 * @package    mod_glossary
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_entry_testcase extends externallib_advanced_testcase {

    /**
     * test_update_entry_without_optional_settings
     */
    public function test_update_entry_without_optional_settings() {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = '<p>A definition</p>';
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);
        $entryid = $return['entryid'];

        // Updates the entry.
        $concept .= ' Updated!';
        $definition .= ' <p>Updated!</p>';
        $return = update_entry::execute($entryid, $concept, $definition, FORMAT_HTML);
        $return = external_api::clean_returnvalue(update_entry::execute_returns(), $return);

        // Get entry from DB.
        $entry = $DB->get_record('glossary_entries', ['id' => $entryid]);

        $this->assertEquals($concept, $entry->concept);
        $this->assertEquals($definition, $entry->definition);
        $this->assertEquals($CFG->glossary_linkentries, $entry->usedynalink);
        $this->assertEquals($CFG->glossary_casesensitive, $entry->casesensitive);
        $this->assertEquals($CFG->glossary_fullmatch, $entry->fullmatch);
        $this->assertEmpty($DB->get_records('glossary_alias', ['entryid' => $entryid]));
        $this->assertEmpty($DB->get_records('glossary_entries_categories', ['entryid' => $entryid]));
    }

    /**
     * test_update_entry_duplicated
     */
    public function test_update_entry_duplicated() {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id, 'allowduplicatedentries' => 1]);

        // Create three entries.
        $this->setAdminUser();
        $concept = 'A concept';
        $definition = '<p>A definition</p>';
        mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML);

        $concept = 'B concept';
        $definition = '<p>B definition</p>';
        mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML);

        $concept = 'Another concept';
        $definition = '<p>Another definition</p>';
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);
        $entryid = $return['entryid'];

        // Updates the entry using an existing entry name when duplicateds are allowed.
        $concept = 'A concept';
        update_entry::execute($entryid, $concept, $definition, FORMAT_HTML);

        // Updates the entry using an existing entry name when duplicateds are NOT allowed.
        $DB->set_field('glossary', 'allowduplicatedentries', 0, ['id' => $glossary->id]);
        $concept = 'B concept';
        $this->expectExceptionMessage(get_string('errconceptalreadyexists', 'glossary'));
        update_entry::execute($entryid, $concept, $definition, FORMAT_HTML);
    }

    /**
     * test_update_entry_with_aliases
     */
    public function test_update_entry_with_aliases() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = 'A definition';
        $paramaliases = 'abc, def, gez';
        $options = [
            [
                'name' => 'aliases',
                'value' => $paramaliases,
            ]
        ];
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);
        $entryid = $return['entryid'];

        // Updates the entry.
        $newaliases = 'abz, xyz';
        $options[0]['value'] = $newaliases;
        $return = update_entry::execute($entryid, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(update_entry::execute_returns(), $return);

        $aliases = $DB->get_records('glossary_alias', ['entryid' => $entryid]);
        $this->assertCount(2, $aliases);
        foreach ($aliases as $alias) {
            $this->assertContains($alias->alias, $newaliases);
        }
    }

    /**
     * test_update_entry_in_categories
     */
    public function test_update_entry_in_categories() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $cat1 = $gg->create_category($glossary);
        $cat2 = $gg->create_category($glossary);
        $cat3 = $gg->create_category($glossary);

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = 'A definition';
        $paramcategories = "$cat1->id, $cat2->id";
        $options = [
            [
                'name' => 'categories',
                'value' => $paramcategories,
            ]
        ];
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);
        $entryid = $return['entryid'];

        // Updates the entry.
        $newcategories = "$cat1->id, $cat3->id";
        $options[0]['value'] = $newcategories;
        $return = update_entry::execute($entryid, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(update_entry::execute_returns(), $return);

        $categories = $DB->get_records('glossary_entries_categories', ['entryid' => $entryid]);
        $this->assertCount(2, $categories);
        foreach ($categories as $category) {
            $this->assertContains($category->categoryid, $newcategories);
        }
    }

    /**
     * test_update_entry_with_attachments
     */
    public function test_update_entry_with_attachments() {
        global $DB, $USER;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);
        $context = context_module::instance($glossary->cmid);

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = 'A definition';

        // Draft files.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        $usercontext = context_user::instance($USER->id);
        $filerecordinline = [
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidinlineattach,
            'filepath'  => '/',
            'filename'  => 'shouldbeanimage.png',
        ];
        $fs = get_file_storage();

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        $options = [
            [
                'name' => 'inlineattachmentsid',
                'value' => $draftidinlineattach,
            ],
            [
                'name' => 'attachmentsid',
                'value' => $draftidattach,
            ]
        ];
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);
        $entryid = $return['entryid'];
        $entry = $DB->get_record('glossary_entries', ['id' => $entryid]);

        list($definitionoptions, $attachmentoptions) = glossary_get_editor_and_attachment_options($course, $context, $entry);

        $entry = file_prepare_standard_editor($entry, 'definition', $definitionoptions, $context, 'mod_glossary', 'entry',
            $entry->id);
        $entry = file_prepare_standard_filemanager($entry, 'attachment', $attachmentoptions, $context, 'mod_glossary', 'attachment',
            $entry->id);

        $inlineattachmentsid = $entry->definition_editor['itemid'];
        $attachmentsid = $entry->attachment_filemanager;

        // Change the file areas.

        // Delete one inline editor file.
        $selectedfile = (object)[
            'filename' => $filerecordinline['filename'],
            'filepath' => $filerecordinline['filepath'],
        ];
        $return = repository_delete_selected_files($usercontext, 'user', 'draft', $inlineattachmentsid, [$selectedfile]);

        // Add more files.
        $filerecordinline['filename'] = 'newvideo.mp4';
        $filerecordinline['itemid'] = $inlineattachmentsid;

        $filerecordattach['filename'] = 'newattach.txt';
        $filerecordattach['itemid'] = $attachmentsid;

        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        // Updates the entry.
        $options[0]['value'] = $inlineattachmentsid;
        $options[1]['value'] = $attachmentsid;
        $return = update_entry::execute($entryid, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(update_entry::execute_returns(), $return);

        $editorfiles = external_util::get_area_files($context->id, 'mod_glossary', 'entry', $entryid);
        $attachmentfiles = external_util::get_area_files($context->id, 'mod_glossary', 'attachment', $entryid);

        $this->assertCount(1, $editorfiles);
        $this->assertCount(2, $attachmentfiles);

        $this->assertEquals('newvideo.mp4', $editorfiles[0]['filename']);
        $this->assertEquals('attachment.txt', $attachmentfiles[0]['filename']);
        $this->assertEquals('newattach.txt', $attachmentfiles[1]['filename']);
    }
}
