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
 * Content processor for Ally.
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\logging\logger;
use tool_ally\models\component_content;

/**
 * Content processor for Ally.
 * Can be used to process individual or groups of content.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_processor extends traceable_processor {

    protected static $pushtrace = [];

    protected static $updates;

    /**
     * Push content update to Ally without batching, etc.
     * @param component_content[] | component_content $content
     * @param string $eventname
     * @return array
     * @throws \coding_exception
     */
    public static function build_payload($content, $eventname) {
        if (!is_array($content)) {
            $content = [$content];
        }

        $payload = [];
        foreach ($content as $item) {
            if (!$item instanceof component_content) {
                throw new \coding_exception('$content array should only contain instances of component_content');
            }
            if (strval($item->contentformat) !== FORMAT_HTML) {
                // Only HTML formatted content is supported.
                continue;
            }
            $payload[] = local_content::to_crud($item, $eventname);
        }

        return $payload;
    }

    /**
     * @param component_content[]|component_content $content
     * @param string $eventname
     */
    private static function add_to_content_queue($content, $eventname) {
        global $DB;

        $config = self::get_config();
        logger::get()->info('logger:addingconenttoqueue', [
            'configvalid' => $config->is_valid(),
            'configclionly' => $config->is_cli_only(),
            'content' => $content
        ]);
        if (!array($content)) {
            $content = [$content];
        }
        $dataobjects = [];
        foreach ($content as $contentitem) {
            if (empty($contentitem->content)) {
                continue;
            }
            $dataobjects[] = (object) [
                'comprowid' => $contentitem->id,
                'component' => $contentitem->component,
                'comptable' => $contentitem->table,
                'compfield' => $contentitem->field,
                'courseid' => $contentitem->get_courseid(),
                'eventtime' => time(),
                'eventname' => $eventname,
                'content' => $contentitem->content
            ];
        }
        $DB->insert_records('tool_ally_content_queue', $dataobjects);
    }

    /**
     * Push update(s) for content.
     * @param component_content[]|component_content $content
     * @param string $eventname
     * @return bool
     */
    public static function push_content_update($content, $eventname) {
        $config = self::get_config();
        if (!$config->is_valid() || $config->is_cli_only()) {
            self::add_to_content_queue($content, $eventname);
            return false;
        }
        if (empty(self::$updates)) {
            self::$updates = new push_content_updates($config);
        }
        $success = self::push_update(self::$updates, $content, $eventname);
        if (!$success) {
            self::add_to_content_queue($content, $eventname);
        }
        return $success;
    }

}
