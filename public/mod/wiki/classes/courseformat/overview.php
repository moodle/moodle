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

namespace mod_wiki\courseformat;

use cm_info;
use core\url;
use mod_wiki\manager;
use core\output\action_link;
use core\output\renderer_helper;
use core\output\local\properties\button;
use core\output\local\properties\text_align;
use core_courseformat\local\overview\overviewitem;

/**
 * Wiki overview integration.
 *
 * @package    mod_wiki
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /**
     * @var manager the wiki manager.
     */
    private manager $manager;

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     * @param renderer_helper $rendererhelper the renderer helper.
     */
    public function __construct(
        cm_info $cm,
        /** @var renderer_helper $rendererhelper the renderer helper */
        protected readonly renderer_helper $rendererhelper,
    ) {
        parent::__construct($cm);
        $this->manager = manager::create_from_coursemodule($cm);
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!has_capability('mod/wiki:managewiki', $this->cm->context)) {
            return null; // If the user cannot manage the wiki, we don't show the actions.
        }

        $pageid = $this->manager->get_main_wiki_pageid();
        if ($pageid) {
            // If the main page of the wiki exists, link to the map view.
            $url = new url(
                '/mod/wiki/map.php',
                ['pageid' => $pageid],
            );
        } else {
            $url = new url(
                '/mod/wiki/view.php',
                ['id' => $this->cm->id],
            );
        }

        $text = get_string('view');
        $content = new action_link(
            url: $url,
            text: $text,
            attributes: ['class' => button::BODY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: get_string('actions'),
            value: $text,
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'wiki_type' => $this->get_extra_wiki_type(),
            'totalentries' => $this->get_total_entries(),
            'my_entries' => $this->get_extra_my_entries(),
        ];
    }
    /**
     * Get the overview item for wiki type.
     *
     * @return overviewitem An overview item for the wiki type.
     */
    private function get_extra_wiki_type(): overviewitem {
        return new overviewitem(
            name: get_string('wikimode', 'wiki'),
            value: $this->manager->get_wiki_mode()->value,
            content: $this->manager->get_wiki_mode()->to_string(),
        );
    }

    /**
     * Get the entries for a user
     *
     * @return overviewitem|null An overview item, or null if the user lacks the required capability.
     */
    private function get_extra_my_entries(): ?overviewitem {
        global $USER;
        if (has_capability('mod/wiki:managewiki', $this->cm->context)) {
            return null; // If the user manage the wiki, we don't show the my entries.
        }
        $entriescount  = $this->manager->get_user_entries_count($USER->id);
        return new overviewitem(
            name: get_string('myentries', 'wiki'),
            value: $entriescount,
            content: $entriescount,
            textalign: text_align::END,
        );
    }

    /**
     * Get the overview item for total entries.
     *
     * @return overviewitem An overview item for total entries.
     */
    private function get_total_entries(): overviewitem {
        global $USER;
        $entriescount = $this->manager->get_all_entries_count($USER->id);
        $label = get_string('totalentries', 'wiki');
        if (has_capability('mod/wiki:managewiki', $this->cm->context)) {
            $label = get_string('entries', 'wiki');
        }
        return new overviewitem(
            name: $label,
            value: $entriescount,
            content: $entriescount,
            textalign: text_align::END,
        );
    }
}
