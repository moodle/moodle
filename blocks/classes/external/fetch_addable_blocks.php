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
 * This is the external method used for fetching the addable blocks in a given page.
 *
 * @package    core_block
 * @since      Moodle 3.11
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_block\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

/**
 * This is the external method used for fetching the addable blocks in a given page.
 *
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch_addable_blocks extends external_api {

    /**
     * Describes the parameters for execute.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'pagecontextid' => new external_value(PARAM_INT, 'The context ID of the page.'),
                'pagetype' => new external_value(PARAM_ALPHANUMEXT, 'The type of the page.'),
                'pagelayout' => new external_value(PARAM_ALPHA, 'The layout of the page.'),
                'subpage' => new external_value(PARAM_TEXT, 'The subpage identifier', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     * Fetch the addable blocks in a given page.
     *
     * @param int $pagecontextid The context ID of the page
     * @param string $pagetype The type of the page
     * @param string $pagelayout The layout of the page
     * @param string $subpage The subpage identifier
     * @return array The blocks list
     */
    public static function execute(int $pagecontextid, string $pagetype, string $pagelayout, string $subpage = ''): array {
        global $PAGE;

        $params = self::validate_parameters(self::execute_parameters(),
            [
                'pagecontextid' => $pagecontextid,
                'pagetype' => $pagetype,
                'pagelayout' => $pagelayout,
                'subpage' => $subpage,
            ]
        );

        $context = \context::instance_by_id($params['pagecontextid']);
        // Validate the context. This will also set the context in $PAGE.
        self::validate_context($context);

        // We need to manually set the page layout and page type.
        $PAGE->set_pagelayout($params['pagelayout']);
        $PAGE->set_pagetype($params['pagetype']);
        $PAGE->set_subpage($params['subpage']);

        // Firstly, we need to load all currently existing page blocks to later determine which blocks are addable.
        $PAGE->blocks->load_blocks(false);
        $PAGE->blocks->create_all_block_instances();

        $addableblocks = $PAGE->blocks->get_addable_blocks();

        return array_map(function($block) {
            return [
                'name' => $block->name,
                'title' => get_string('pluginname', "block_{$block->name}")
            ];
        }, $addableblocks);
    }

    /**
     * Describes the execute return value.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'name' => new external_value(PARAM_PLUGIN, 'The name of the block.'),
                    'title' => new external_value(PARAM_RAW, 'The title of the block.'),
                ]
            ),
            'List of addable blocks in a given page.'
        );
    }
}
