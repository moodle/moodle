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
 * Testing generator.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_ally\logging\logger;

/**
 * Testing generator.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_ally_generator extends component_generator_base {
    /**
     * Create a draft file for the current user.
     *
     * Note: The file's item ID is the draft ID.
     *
     * @param array $record Draft file record
     * @param string $content File contents
     * @return stored_file
     */
    public function create_draft_file(array $record = [], $content = 'Test file') {
        global $USER;

        if (empty($USER->username) || $USER->username === 'guest') {
            throw new coding_exception('Requires a current user');
        }

        $defaults = [
            'component' => 'user',
            'filearea'  => 'draft',
            'contextid' => context_user::instance($USER->id)->id,
            'itemid'    => file_get_unused_draft_itemid(),
            'filename'  => 'attachment.html',
            'filepath'  => '/'
        ];

        return get_file_storage()->create_file_from_string($record + $defaults, $content);
    }

    /**
     * Create a file based on the provided info. Will create random contents and filename, if none is provided.
     *
     * @param array $record
     * @param string|null $content
     * @return stored_file|null
     * @throws coding_exception
     */
    public function create_file(array $record = [], ?string $content = null): ?stored_file {

        if (empty($record['component']) || empty($record['filearea']) || empty($record['contextid'])) {
            throw new coding_exception('component, filearea, and contextid must be set when creating a file');
        }

        if (is_null($content)) {
            // Make some content that is very likely to be random.
            $content = random_bytes(mt_rand(10, 50));
        }

        $defaults = [
                'itemid'    => 0,
                'filename'  => sha1(random_bytes(20)) . '.txt',
                'filepath'  => '/'
        ];

        return get_file_storage()->create_file_from_string($record + $defaults, $content);
    }

    /**
     * Take a stored file and return a PLUGINFILE style link.
     *
     * @param stored_file $file
     * @return string
     */
    public function create_pluginfile_link_for_file(stored_file $file): string {
        return '<a href="@@PLUGINFILE@@' . $file->get_filepath() . $file->get_filename() . '">Link</a>';
    }

    /**
     * Return a full link string for the provided stored file. This contains the entire file URL, including the wwwroot.
     *
     * @param stored_file $file
     * @param bool $useitemid If true, include the itemid in the link. Not all modules/areas do this.
     * @return string
     */
    public function create_full_link_for_file(stored_file $file, $useitemid = true): string {
        $itemid = null;
        if ($useitemid) {
            $itemid = $file->get_itemid();
        }
        $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                $itemid, $file->get_filepath(), $file->get_filename());
        return '<a href="' . $url->out() . '">Link</a>';
    }

    /**
     * Stolen from /Users/guy/Development/www/moodle_test/blocks/tests/privacy_test.php
     * Get the block manager.
     *
     * @param array $regions The regions.
     * @param context $context The context.
     * @param string $pagetype The page type.
     * @param string $subpage The sub page.
     * @return block_manager
     */
    protected function get_block_manager($regions, $context, $pagetype = 'page-type', $subpage = '') {
        global $CFG;
        require_once($CFG->libdir.'/blocklib.php');
        $page = new moodle_page();
        $page->set_context($context);
        $page->set_pagetype($pagetype);
        $page->set_subpage($subpage);
        $page->set_url(new moodle_url('/'));

        $blockmanager = new block_manager($page);
        $blockmanager->add_regions($regions, false);
        $blockmanager->set_default_region($regions[0]);

        return $blockmanager;
    }

    /**
     * Add block to specific context and return instance row.
     * @param context $context
     * @param $title
     * @param $content
     * @param string $region
     * @param string $pagetypepattern
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     */
    public function add_block(context $context,
                              $title, $content,
                              $region = 'side-pre',
                              $pagetypepattern = 'course-view-*') {
        global $DB;

        $bm = $this->get_block_manager([$region], $context);
        $bm->add_block('html', $region, 1, true, $pagetypepattern); // Wow - doesn't return anything useful like say, the block id!
        $blocks = $DB->get_records('block_instances', [], 'id DESC', 'id', 0, 1);
        if (empty($blocks)) {
            throw new coding_exception('Created a block but block instances empty!');
        }
        $block = reset($blocks);
        $blockconfig = (object) [
            'title' => $title,
            'format' => FORMAT_HTML,
            'text' => $content
        ];
        $block->configdata = base64_encode(serialize($blockconfig));
        $DB->update_record('block_instances', $block);
        $block = $DB->get_record('block_instances', ['id' => $block->id]);
        return $block;
    }

    /**
     * Create a ally log entry in the database.
     *
     * @param array $record Contains values to be set in the log entry.
     *                      Currently supports level, message, context, and time.
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     */
    public function create_log_entry(array $record = []) {
        global $DB;

        $logger = logger::get();

        if (isset($record['level'])) {
            $level = $record['level'];
        } else {
            $level = 'info';
        }

        if (isset($record['message'])) {
            $message = $record['message'];
        } else {
            $message = 'Default message';
        }

        if (isset($record['context'])) {
            $context = $record['context'];
        } else {
            $context = [];
        }

        $logid = $logger->log($level, $message, $context);
        if (empty($logid)) {
            throw new coding_exception("Log insert didn't return an id.");
        }

        if (isset($record['time'])) {
            $DB->set_field('tool_ally_log', 'time', (int)$record['time'], ['id' => $logid]);
        }

        return $DB->get_record('tool_ally_log', ['id' => $logid]);

    }
}
