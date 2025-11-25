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

use core\url;
use mod_glossary_entry_query_builder;
use core\output\local\properties\text_align;
use core_courseformat\local\overview\overviewitem;
use core_courseformat\output\local\overview\overviewaction;

/**
 * Glossary overview integration class.
 *
 * @package    mod_glossary
 * @copyright  2025 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'totalentries' => $this->get_extra_totalentries_overview(),
            'myentries' => $this->get_extra_myentries_overview(),
            'comments' => $this->get_extra_comments_overview(),
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
        if ($entriescount > 0) {
            $url = new url('/mod/glossary/view.php', ['id' => $this->cm->id, 'mode' => 'approval']);
            $text = get_string('approve', 'mod_glossary');
        } else {
            $url = new url('/mod/glossary/view.php', ['id' => $this->cm->id]);
            $text = get_string('view');
        }

        $alertlabel = get_string('numberofentriesneedapprove', 'mod_glossary');
        $content = new overviewaction(
            url: $url,
            text: $text,
            badgevalue: $entriescount > 0 ? $entriescount : null,
            badgetitle: $entriescount > 0 ? $alertlabel : null,
        );

        return new overviewitem(
            name: get_string('actions'),
            value: $entriescount,
            content: $content,
            textalign: text_align::CENTER,
            alertcount: $entriescount,
            alertlabel: $alertlabel,
        );
    }

    /**
     * Get the "Comments" overview item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_comments_overview(): overviewitem {
        global $CFG;

        // Add comments column for all views.
        if (empty($CFG->usecomments) || !$this->cm->get_instance_record()->allowcomments) {
            return new overviewitem(
                name: get_string('comments', 'glossary'),
                value: 0,
                content: '-',
                textalign: text_align::END,
            );
        }

        // Get comments from the glossary.
        $comments = mod_glossary_get_comments($this->cm);
        $totalcomments = ($comments) ? count($comments) : 0;
        return new overviewitem(
            name: get_string('comments', 'glossary'),
            value: $totalcomments,
            content: $totalcomments,
            textalign: text_align::END,
        );
    }

    /**
     * Get the "Total entries" overview item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_totalentries_overview(): overviewitem {
        $columnheader = get_string('entries', 'mod_glossary');
        if (!has_capability('mod/glossary:approve', $this->context)) {
            $columnheader = get_string('totalentries', 'mod_glossary');
        }

        $qb = new mod_glossary_entry_query_builder($this->cm->get_instance_record());
        $qb->filter_by_non_approved(mod_glossary_entry_query_builder::NON_APPROVED_NONE);
        $entriescount = $qb->count_records();

        return new overviewitem(
            name: $columnheader,
            value: $entriescount,
            textalign: text_align::END,
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
            name: get_string('myentries', 'mod_glossary'),
            value: $entriescount,
            content: $entriescount,
            textalign: text_align::END,
        );
    }
}
