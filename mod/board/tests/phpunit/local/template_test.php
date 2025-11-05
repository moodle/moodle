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

namespace mod_board\phpunit\local;

use mod_board\board;
use mod_board\local\template;

/**
 * Test template helper class.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\local\template
 */
final class template_test extends \advanced_testcase {
    public function test_create(): void {
        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = \context_coursecat::instance($category->id);

        $this->setCurrentTimeStart();
        $template = template::create((object)['name' => 'Template 1', 'contextid' => $syscontext->id]);
        $this->assertSame('Template 1', $template->name);
        $this->assertSame((string)$syscontext->id, $template->contextid);
        $this->assertSame('', $template->description);
        $this->assertSame('', $template->columns);
        $this->assertSame('[]', $template->jsonsettings);
        $this->assertTimeCurrent($template->timecreated);

        $template = template::create((object)[
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'contextid' => $categorycontext->id,
            'columns' => "Col 1\r\nCol2",
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => board::SORTBYNONE,
        ]);
        $this->assertSame('My template', $template->name);
        $this->assertSame((string)$categorycontext->id, $template->contextid);
        $this->assertSame('Fancy <em>template</em>', $template->description);
        $this->assertSame("Col 1\nCol2", $template->columns);
        $this->assertSame('{"sortby":"3","singleusermode":"1"}', $template->jsonsettings);

        $template = template::create((object)[
            'name' => 'My template 3',
            'intro' => 'Fancy intro',
        ]);
        $this->assertSame('My template 3', $template->name);
        $this->assertSame('{"intro":"Fancy intro"}', $template->jsonsettings);

        $template = template::create((object)[
            'name' => 'My template 4',
            'intro_editor' => ['text' => 'Fancy <b>intro</b>', 'format' => 5],
        ]);
        $this->assertSame('My template 4', $template->name);
        $this->assertSame('{"intro":"Fancy <b>intro<\/b>"}', $template->jsonsettings);
    }

    public function test_update(): void {
        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = \context_coursecat::instance($category->id);

        $template = template::create((object)[
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'contextid' => $categorycontext->id,
            'columns' => "Col 1\r\nCol2",
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => board::SORTBYNONE,
        ]);

        $template = template::update((object)[
            'id' => $template->id,
            'name' => 'Your template',
            'description' => 'Fancy <strong>template</strong>',
            'contextid' => $syscontext->id,
            'columns' => "Col 1\r\nCol 2\nCol 3",
            'singleusermode' => '-1',
            'sortby' => board::SORTBYRATING,
            'hideheaders' => '1',
        ]);
        $this->assertSame('Your template', $template->name);
        $this->assertSame((string)$syscontext->id, $template->contextid);
        $this->assertSame('Fancy <strong>template</strong>', $template->description);
        $this->assertSame("Col 1\nCol 2\nCol 3", $template->columns);
        $this->assertSame('{"hideheaders":"1","sortby":"2"}', $template->jsonsettings);

        $template = template::update((object)[
            'id' => $template->id,
            'name' => 'Your template',
            'columns' => "Col 1\r\nCol 2\nCol 3",
            'intro' => 'Fancy intro',
        ]);
        $this->assertSame('{"intro":"Fancy intro"}', $template->jsonsettings);

        $template = template::update((object)[
            'id' => $template->id,
            'name' => 'Your template',
            'columns' => "Col 1\r\nCol 2\nCol 3",
            'intro_editor' => ['text' => 'More fancy <b>intro</b>'],
        ]);
        $this->assertSame('{"intro":"More fancy <b>intro<\/b>"}', $template->jsonsettings);
    }

    public function test_delete(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $template1 = $generator->create_template();
        $template2 = $generator->create_template();
        $this->assertTrue($DB->record_exists('board_templates', ['id' => $template1->id]));
        $this->assertTrue($DB->record_exists('board_templates', ['id' => $template2->id]));

        template::delete($template1->id);
        $this->assertFalse($DB->record_exists('board_templates', ['id' => $template1->id]));
        $this->assertTrue($DB->record_exists('board_templates', ['id' => $template2->id]));

        template::delete($template1->id);
    }

    public function test_fix_columns(): void {
        $this->assertSame('', template::fix_columns(''));
        $this->assertSame('', template::fix_columns(' '));
        $this->assertSame('', template::fix_columns("\n"));
        $this->assertSame('', template::fix_columns("\r\n\r"));
        $this->assertSame('abc', template::fix_columns('abc'));
        $this->assertSame('abc', template::fix_columns('    abc '));
        $this->assertSame("abc\ndef", template::fix_columns(" abc \r\n\ndef\r\n "));
        $this->assertSame('abc', template::fix_columns('a<em>b</em>c'));
    }

    public function test_format_columns(): void {
        $this->assertSame('', template::format_columns(''));
        $this->assertSame("abc", template::format_columns("abc"));
        $this->assertSame("abc<br />def", template::format_columns("abc\ndef"));
        $this->assertSame("abc<br />def<br />ijk", template::format_columns("abc\ndef\nijk"));
    }

    public function test_get_context_menu(): void {
        $this->resetAfterTest();

        $syscontext = \context_system::instance();
        $category0 = \core_course_category::get_default();
        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();
        $category3 = $this->getDataGenerator()->create_category(['parent' => $category2->id]);
        $categorycontext0 = \context_coursecat::instance($category0->id);
        $categorycontext1 = \context_coursecat::instance($category1->id);
        $categorycontext2 = \context_coursecat::instance($category2->id);
        $categorycontext3 = \context_coursecat::instance($category3->id);

        $expected = [
            $syscontext->id => 'System',
            $categorycontext0->id => 'Category 1',
            $categorycontext1->id => 'Course category 1',
            $categorycontext2->id => 'Course category 2',
        ];
        $this->assertSame($expected, template::get_context_menu(0));

        $expected = [
            $syscontext->id => 'System',
            $categorycontext0->id => 'Category 1',
            $categorycontext1->id => 'Course category 1',
            $categorycontext2->id => 'Course category 2',
        ];
        $this->assertSame($expected, template::get_context_menu($syscontext->id));

        $expected = [
            $syscontext->id => 'System',
            $categorycontext0->id => 'Category 1',
            $categorycontext1->id => 'Course category 1',
            $categorycontext2->id => 'Course category 2',
        ];
        $this->assertSame($expected, template::get_context_menu($categorycontext1->id));

        $expected = [
            $syscontext->id => 'System',
            $categorycontext0->id => 'Category 1',
            $categorycontext1->id => 'Course category 1',
            $categorycontext2->id => 'Course category 2',
            $categorycontext3->id => 'Course category 3',
        ];
        $this->assertSame($expected, template::get_context_menu($categorycontext3->id));

        $expected = [
            $syscontext->id => 'System',
            $categorycontext0->id => 'Category 1',
            $categorycontext1->id => 'Course category 1',
            $categorycontext2->id => 'Course category 2',
            666 => 'Error',
        ];
        $this->assertSame($expected, template::get_context_menu(666));
    }

    public function test_get_all_settings(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board = $this->getDataGenerator()->create_module('board', ['course' => $course->id]);

        $this->assertSame('1', get_config('mod_board', 'embed_allowed'));
        $this->assertSame('11', get_config('mod_board', 'allowed_singleuser_modes'));
        $allsettings = template::get_all_settings();
        foreach ($allsettings as $field => $setting) {
            $this->assertObjectHasProperty($field, $board);
            $this->assertIsString($setting['name']);
            if ($setting['type'] === 'select') {
                $this->assertArrayHasKey(-1, $setting['options']);
            } else if ($setting['type'] === 'html') {
                $this->assertArrayNotHasKey('options', $setting);
            } else {
                $this->fail('Unsupported setting type: ' . $setting['type']);
            }
        }
        $this->assertArrayHasKey('embed', $allsettings);
        $this->assertArrayHasKey('hidename', $allsettings);

        set_config('embed_allowed', '0', 'mod_board');
        $allsettings = template::get_all_settings();
        $this->assertArrayNotHasKey('embed', $allsettings);
        $this->assertArrayNotHasKey('hidename', $allsettings);
        set_config('embed_allowed', '1', 'mod_board');

        set_config('allowed_singleuser_modes', '11', 'mod_board');
        $allsettings = template::get_all_settings();
        $expected = [
            -1 => get_string('choosedots'),
            board::SINGLEUSER_DISABLED => get_string('singleusermodenone', 'mod_board'),
            board::SINGLEUSER_PRIVATE => get_string('singleusermodeprivate', 'mod_board'),
            board::SINGLEUSER_PUBLIC => get_string('singleusermodepublic', 'mod_board'),
        ];
        $this->assertSame($expected, $allsettings['singleusermode']['options']);

        set_config('allowed_singleuser_modes', '01', 'mod_board');
        $allsettings = template::get_all_settings();
        $expected = [
            -1 => get_string('choosedots'),
            board::SINGLEUSER_DISABLED => get_string('singleusermodenone', 'mod_board'),
            board::SINGLEUSER_PUBLIC => get_string('singleusermodepublic', 'mod_board'),
        ];
        $this->assertSame($expected, $allsettings['singleusermode']['options']);

        set_config('allowed_singleuser_modes', '10', 'mod_board');
        $allsettings = template::get_all_settings();
        $expected = [
            -1 => get_string('choosedots'),
            board::SINGLEUSER_DISABLED => get_string('singleusermodenone', 'mod_board'),
            board::SINGLEUSER_PRIVATE => get_string('singleusermodeprivate', 'mod_board'),
        ];
        $this->assertSame($expected, $allsettings['singleusermode']['options']);

        set_config('allowed_singleuser_modes', '00', 'mod_board');
        $allsettings = template::get_all_settings();
        $expected = [
            -1 => get_string('choosedots'),
            board::SINGLEUSER_DISABLED => get_string('singleusermodenone', 'mod_board'),
        ];
        $this->assertSame($expected, $allsettings['singleusermode']['options']);
    }

    public function test_get_settings(): void {
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $template = $generator->create_template([
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'columns' => "Col 1\r\nCol2",
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => board::SORTBYNONE,
        ]);
        $expected = [
            'sortby' => (string)board::SORTBYNONE,
            'singleusermode' => (string)board::SINGLEUSER_PRIVATE,
        ];
        $this->assertSame($expected, template::get_settings($template->jsonsettings));

        $template = $generator->create_template([
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'columns' => "Col 1\r\nCol2",
            'intro' => 'Some fancy <em>text</em>',
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => 999999,
        ]);
        $expected = [
            'intro' => 'Some fancy <em>text</em>',
            'singleusermode' => (string)board::SINGLEUSER_PRIVATE,
        ];
        $this->assertSame($expected, template::get_settings($template->jsonsettings));

        set_config('embed_allowed', 1, 'mod_board');
        $template = $generator->create_template([
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'columns' => "Col 1\r\nCol2",
            'intro' => '',
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'embed' => '1',
            'hidename' => '1',
        ]);
        $expected = [
            'singleusermode' => (string)board::SINGLEUSER_PRIVATE,
            'embed' => '1',
            'hidename' => '1',
        ];
        $this->assertSame($expected, template::get_settings($template->jsonsettings));

        set_config('embed_allowed', 0, 'mod_board');
        $expected = [
            'singleusermode' => (string)board::SINGLEUSER_PRIVATE,
        ];
        $this->assertSame($expected, template::get_settings($template->jsonsettings));
    }

    public function test_format_settings(): void {
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $template = $generator->create_template([
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'columns' => "Col 1\r\nCol2",
            'intro' => 'Some <x>text</x>',
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => board::SORTBYNONE,
        ]);
        $expected = 'Description: Some text<br />Sort by: None<br />Single user mode: Single user mode (private)';
        $this->assertSame($expected, template::format_settings($template->jsonsettings));
    }

    public function test_get_export_filename(): void {
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $template = $generator->create_template([
            'name' => 'My fancy."template"',
        ]);

        $this->assertSame('board_my_fancy_template.json', template::get_export_filename($template));
    }

    public function test_get_export_json(): void {
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $template = $generator->create_template([
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'columns' => "Col 1\r\nCol2",
            'intro' => 'Some fancy <em>text</em>',
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => board::SORTBYNONE,
        ]);
        $expected = '{
    "name": "My template",
    "description": "Fancy <em>template</em>",
    "columns": "Col 1\nCol2",
    "intro": "Some fancy <em>text</em>",
    "sortby": "3",
    "singleusermode": "1"
}';
        $this->assertSame($expected, template::get_export_json($template));
    }

    public function test_decode_import_file(): void {
        $content = '{
    "name": "My template",
    "description": "Fancy <em>template</em>",
    "columns": "Col 1\nCol2",
    "sortby": "3",
    "singleusermode": "1",
    "userscanedit": "2",
    "enableblanktarget": "-1",
    "abcdef": "0"
}';
        $expected = (array)json_decode($content);
        unset($expected['abcdef']);
        unset($expected['userscanedit']);
        unset($expected['enableblanktarget']);
        $this->assertSame($expected, (array)template::decode_import_file($content));

        $content = '{
    "name": "My template",
}';
        $expected = (array)json_decode($content);
        $this->assertSame($expected, (array)template::decode_import_file($content));

        $content = '{
    "name": "",
    "description": "Fancy <em>template</em>",
    "columns": "Col 1\nCol2",
    "sortby": "3",
    "singleusermode": "1",
}';
        $this->assertNull(template::decode_import_file($content));
    }

    public function test_get_applicable_templates(): void {
        global $DB;
        $this->resetAfterTest();

        // Delete built-in templates.
        $DB->delete_records('board_templates');

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $syscontext = \context_system::instance();
        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();
        $category3 = $this->getDataGenerator()->create_category(['parent' => $category2->id]);
        $categorycontext1 = \context_coursecat::instance($category1->id);
        $categorycontext2 = \context_coursecat::instance($category2->id);

        $course0 = get_site();
        $course1 = $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $course2 = $this->getDataGenerator()->create_course(['category' => $category2->id]);
        $course3 = $this->getDataGenerator()->create_course(['category' => $category3->id]);
        $coursecontext0 = \context_course::instance($course0->id);
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);
        $coursecontext3 = \context_course::instance($course3->id);

        $board0 = $this->getDataGenerator()->create_module('board', ['course' => $course0->id]);
        $board1 = $this->getDataGenerator()->create_module('board', ['course' => $course1->id]);
        $board2 = $this->getDataGenerator()->create_module('board', ['course' => $course2->id]);
        $board3 = $this->getDataGenerator()->create_module('board', ['course' => $course3->id]);
        $boardcontext0 = board::context_for_board($board0);
        $boardcontext1 = board::context_for_board($board1);
        $boardcontext2 = board::context_for_board($board2);
        $boardcontext3 = board::context_for_board($board3);

        $template0 = $generator->create_template(['contextid' => $syscontext->id]);
        $template1 = $generator->create_template(['contextid' => $categorycontext1->id]);
        $template2 = $generator->create_template(['contextid' => $categorycontext2->id]);

        $expected = [
            $template0->id => $template0->name,
        ];
        $this->assertSame($expected, template::get_applicable_templates($coursecontext0));
        $this->assertSame($expected, template::get_applicable_templates($boardcontext0));

        $expected = [
            $template0->id => $template0->name,
            $template1->id => $template1->name,
        ];
        $this->assertSame($expected, template::get_applicable_templates($coursecontext1));
        $this->assertSame($expected, template::get_applicable_templates($boardcontext1));

        $expected = [
            $template0->id => $template0->name,
            $template2->id => $template2->name,
        ];
        $this->assertSame($expected, template::get_applicable_templates($coursecontext2));
        $this->assertSame($expected, template::get_applicable_templates($coursecontext3));
        $this->assertSame($expected, template::get_applicable_templates($boardcontext2));
        $this->assertSame($expected, template::get_applicable_templates($boardcontext3));
    }

    public function test_apply(): void {
        global $DB;
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $template = $generator->create_template([
            'intro' => 'Some <em>intro</em>',
            'columns' => "Col 1\nCol 2",
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => board::SORTBYNONE,
        ]);

        $course = $this->getDataGenerator()->create_course();
        $board = $this->getDataGenerator()->create_module('board', [
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'sortby' => board::SORTBYRATING,
        ]);

        $board = template::apply($board->id, $template->id);
        $this->assertSame('Some <em>intro</em>', $board->intro);
        $this->assertSame(FORMAT_HTML, $board->introformat);
        $this->assertSame((string)board::SINGLEUSER_PRIVATE, $board->singleusermode);
        $this->assertSame((string)board::SORTBYNONE, $board->sortby);
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder ASC'));
        $this->assertSame('Col 1', $columns[0]->name);
        $this->assertSame('Col 2', $columns[1]->name);
        $this->assertCount(2, $columns);

        $template = $generator->create_template([
            'columns' => '',
            'singleusermode' => board::SINGLEUSER_PUBLIC,
        ]);

        $board = template::apply($board->id, $template->id);
        $this->assertSame('Some <em>intro</em>', $board->intro);
        $this->assertSame(FORMAT_HTML, $board->introformat);
        $this->assertSame((string)board::SINGLEUSER_PUBLIC, $board->singleusermode);
        $this->assertSame((string)board::SORTBYNONE, $board->sortby);
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder ASC'));
        $this->assertSame('Col 1', $columns[0]->name);
        $this->assertSame('Col 2', $columns[1]->name);
        $this->assertCount(2, $columns);

        $template = $generator->create_template([
            'columns' => "Sloupec 1\nSloupec 2\nSloupec 3",
        ]);

        $board = template::apply($board->id, $template->id);
        $columns = array_values($DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder ASC'));
        $this->assertSame('Sloupec 1', $columns[0]->name);
        $this->assertSame('Sloupec 2', $columns[1]->name);
        $this->assertSame('Sloupec 3', $columns[2]->name);
        $this->assertCount(3, $columns);
    }
}
