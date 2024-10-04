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

namespace mod_glossary\courseformat;

use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\button;
use core\output\local\properties\text_align;
use core\url;
use cm_info;
use mod_glossary_entry_query_builder;

/**
 * Glossary overview integration class.
 *
 * @package    mod_glossary
 * @copyright  2025 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     * @param \core\output\renderer_helper $rendererhelper the renderer helper.
     */
    public function __construct(
        cm_info $cm,
        /** @var \core\output\renderer_helper $rendererhelper the renderer helper */
        protected readonly \core\output\renderer_helper $rendererhelper,
        /** @var \core_string_manager $stringmanager the string manager */
        protected readonly \core_string_manager $stringmanager,
    ) {
        parent::__construct($cm);
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'totalentries' => $this->get_extra_totalentries_overview(),
            'myentries' => $this->get_extra_myentries_overview(),
        ];
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!has_capability('mod/glossary:approve', $this->context)) {
            return null;
        }

        $qb = new mod_glossary_entry_query_builder($this->cm->get_instance_record());
        $qb->filter_by_non_approved(mod_glossary_entry_query_builder::NON_APPROVED_ONLY);
        $entriescount = $qb->count_records();

        $renderer = $this->rendererhelper->get_core_renderer();
        $badge = $renderer->notice_badge(
            contents: $entriescount,
            title: $this->stringmanager->get_string('numberofentriesneedapprove', 'mod_glossary'),
        );

        $content = new action_link(
            url: new url('/mod/glossary/view.php', ['id' => $this->cm->id, 'mode' => 'approval']),
            text: $this->stringmanager->get_string('approve', 'mod_glossary') . $badge,
            attributes: ['class' => button::SECONDARY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: $this->stringmanager->get_string('actions'),
            value: $entriescount,
            content: $entriescount ? $content : '-',
            textalign: text_align::CENTER,
        );
    }

    /**
     * Get the "Total entries" overview item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_totalentries_overview(): overviewitem {
        $columnheader = $this->stringmanager->get_string('entries', 'mod_glossary');
        if (!has_capability('mod/glossary:approve', $this->context)) {
            $columnheader = $this->stringmanager->get_string('totalentries', 'mod_glossary');
        }

        $qb = new mod_glossary_entry_query_builder($this->cm->get_instance_record());
        $qb->filter_by_non_approved(mod_glossary_entry_query_builder::NON_APPROVED_NONE);
        $entriescount = $qb->count_records();

        $content = new action_link(
            url: new url('/mod/glossary/view.php', ['id' => $this->cm->id]),
            text: $entriescount,
            attributes: [
                'class' => button::SECONDARY_OUTLINE->classes(),
                'title' => $this->stringmanager->get_string('seeallentries', 'mod_glossary'),
            ],
        );

        return new overviewitem(
            name: $columnheader,
            value: $entriescount,
            content: $entriescount ? $content : '-',
            textalign: text_align::CENTER,
        );
    }

    /**
     * Get the "My entries" overview item.
     *
     * @return overviewitem|null The overview item (or null if the user can approve entries).
     */
    private function get_extra_myentries_overview(): ?overviewitem {
        global $USER;

        if (has_capability('mod/glossary:approve', $this->context)) {
            return null;
        }

        $qb = new mod_glossary_entry_query_builder($this->cm->get_instance_record());
        $qb->join_user(true);
        $qb->where('id', 'user', $USER->id);
        $entriescount = $qb->count_records();

        return new overviewitem(
            name: $this->stringmanager->get_string('myentries', 'mod_glossary'),
            value: $entriescount,
            content: $entriescount ?: '-',
            textalign: text_align::CENTER,
        );
    }
}
