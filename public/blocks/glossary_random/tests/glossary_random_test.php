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

namespace block_glossary_random;

use advanced_testcase;
use block_glossary_random;
use context_course;
use moodle_page;
use stdClass;

/**
 * PHPUnit block_glossary_random tests
 *
 * @package    block_glossary_random
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\block_glossary_random::class)]
final class glossary_random_test extends advanced_testcase {
    /** @var stdClass the course the glossary and the block belong to. */
    private stdClass $course;

    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . '/../../moodleblock.class.php');
        require_once(__DIR__ . '/../block_glossary_random.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test the behaviour of can_block_be_added() method.
     */
    public function test_can_block_be_added(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and prepare the page where the block will be added.
        $course = $this->getDataGenerator()->create_course();
        $page = new \moodle_page();
        $page->set_context(context_course::instance($course->id));
        $page->set_pagelayout('course');

        $block = new block_glossary_random();
        $pluginclass = \core_plugin_manager::resolve_plugininfo_class('mod');

        // If glossary module is enabled, the method should return true.
        $pluginclass::enable_plugin('glossary', 1);
        $this->assertTrue($block->can_block_be_added($page));

        // However, if the glossary module is disabled, the method should return false.
        $pluginclass::enable_plugin('glossary', 0);
        $this->assertFalse($block->can_block_be_added($page));
    }

    /**
     * Create a course with a glossary holding three entries, and add a random glossary block to it.
     *
     * The entries are given distinct modification times, so that the display orders which depend on
     * the modification time are well defined.
     *
     * @param string $type the display order to configure, one of the BGR_* constants.
     * @return int the id of the block instance which was created.
     */
    private function create_block_with_entries(string $type): int {
        $generator = $this->getDataGenerator();
        $this->course = $generator->create_course();
        $glossary = $generator->create_module('glossary', ['course' => $this->course->id, 'name' => 'animals']);

        $glossarygenerator = $generator->get_plugin_generator('mod_glossary');
        $now = time();
        $entries = [
            ['concept' => 'zebra', 'definition' => 'zebra def', 'timemodified' => $now - 3],
            ['concept' => 'aardvark', 'definition' => 'aardvark def', 'timemodified' => $now - 2],
            ['concept' => 'kangaroo', 'definition' => 'kangaroo def', 'timemodified' => $now - 1],
        ];
        foreach ($entries as $entry) {
            $glossarygenerator->create_content($glossary, $entry);
        }

        $instance = $generator->create_block('glossary_random', [
            'parentcontextid' => context_course::instance($this->course->id)->id,
            'pagetypepattern' => 'course-view-*',
            'configdata' => base64_encode(serialize((object) [
                'title' => 'Glossary Block',
                'glossary' => $glossary->id,
                'type' => $type,
                'refresh' => 0,
                'showconcept' => 1,
            ])),
        ]);

        return (int) $instance->id;
    }

    /**
     * Load the block the way a page request does, and return the concept it displayed.
     *
     * The instance is read from the database on every call, so that the configuration committed by the
     * previous load, such as the index of the entry which was displayed then, is taken into account.
     *
     * @param int $blockinstanceid the block instance to load.
     * @return string the concept displayed in the block.
     */
    private function get_displayed_concept(int $blockinstanceid): string {
        global $DB;

        $page = new moodle_page();
        $page->set_course($this->course);
        $page->set_pagetype('course-view-topics');

        $instance = $DB->get_record('block_instances', ['id' => $blockinstanceid], '*', MUST_EXIST);
        $block = \block_instance('glossary_random', $instance, $page);

        $this->assertSame(1, preg_match('~<h3>(.*)</h3>~U', $block->config->cache, $matches));

        return $matches[1];
    }

    /**
     * Change the display order of an existing block instance, the way the block configuration form does.
     *
     * @param int $blockinstanceid the block instance to reconfigure.
     * @param string $type the display order to configure, one of the BGR_* constants.
     */
    private function set_display_order(int $blockinstanceid, string $type): void {
        global $DB;

        $instance = $DB->get_record('block_instances', ['id' => $blockinstanceid], '*', MUST_EXIST);
        $config = unserialize_object(base64_decode($instance->configdata));
        $config->type = $type;
        $DB->set_field('block_instances', 'configdata', base64_encode(serialize($config)), ['id' => $blockinstanceid]);
    }

    /**
     * Data provider for {@see test_display_order}.
     *
     * The display orders are given as their raw values rather than as the BGR_* constants, because the
     * block is not loaded at the point where the data provider runs.
     *
     * @return array[] the display order, and the concepts expected on three successive page loads.
     */
    public static function display_order_provider(): array {
        return [
            'Last modified entry' => ['1', ['kangaroo', 'kangaroo', 'kangaroo']],
            'Next entry' => ['2', ['zebra', 'aardvark', 'kangaroo']],
            'Alphabetical order' => ['3', ['aardvark', 'kangaroo', 'zebra']],
        ];
    }

    /**
     * Test the display orders which show the entries in the same sequence every time.
     *
     * @param string $type the display order to configure, one of the BGR_* constants.
     * @param string[] $expected the concepts expected on each successive page load.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('display_order_provider')]
    public function test_display_order(string $type, array $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $blockinstanceid = $this->create_block_with_entries($type);

        $displayed = [];
        foreach ($expected as $ignored) {
            $displayed[] = $this->get_displayed_concept($blockinstanceid);
        }

        $this->assertEquals($expected, $displayed);
    }

    /**
     * Test that the random display order eventually displays every entry.
     *
     * Each page load picks one of the three entries at random, so enough loads are sampled for the chance
     * of an entry never being picked to be negligible, around three in ten thousand million.
     */
    public function test_random_display_order(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $blockinstanceid = $this->create_block_with_entries(BGR_RANDOMLY);

        $displayed = [];
        for ($i = 0; $i < 60; $i++) {
            $displayed[$this->get_displayed_concept($blockinstanceid)] = true;
        }

        $displayed = array_keys($displayed);
        sort($displayed);
        $this->assertEquals(['aardvark', 'kangaroo', 'zebra'], $displayed);
    }

    /**
     * Test that changing the display order of a block which has already displayed entries takes effect.
     */
    public function test_display_order_can_be_changed(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $blockinstanceid = $this->create_block_with_entries(BGR_NEXTONE);

        $this->assertEquals('zebra', $this->get_displayed_concept($blockinstanceid));
        $this->assertEquals('aardvark', $this->get_displayed_concept($blockinstanceid));

        $this->set_display_order($blockinstanceid, BGR_LASTMODIFIED);

        $this->assertEquals('kangaroo', $this->get_displayed_concept($blockinstanceid));
        $this->assertEquals('kangaroo', $this->get_displayed_concept($blockinstanceid));
    }
}
