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

namespace mod_data\courseformat;

use cm_info;
use core\url;
use mod_data\dates;
use mod_data\manager;
use core_calendar\output\humandate;
use core\output\local\properties\text_align;
use core_courseformat\local\overview\overviewitem;
use core_courseformat\output\local\overview\overviewaction;

/**
 * Database activity overview integration.
 *
 * @package    mod_data
 * @copyright  2025 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {

    /** @var manager database activity manager. */
    private $manager;

    /** @var bool whether the user can see pendent entries or not. */
    private $canviewall;

    /** @var array All the entries belonging to groups that the current user can view. */
    private $allentries = [];

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     * @param \core\output\renderer_helper $rendererhelper the renderer helper.
     * @param \core_string_manager $stringmanager the string manager.
     */
    public function __construct(
            cm_info $cm,
            /** @var \core\output\renderer_helper $rendererhelper the renderer helper */
            protected readonly \core\output\renderer_helper $rendererhelper,
            /** @var \core_string_manager $stringmanager the string manager */
            protected readonly \core_string_manager $stringmanager,
    ) {
        parent::__construct($cm);

        $this->manager = manager::create_from_coursemodule($cm);
        $this->canviewall = has_capability('mod/data:approve', $cm->context);
        $this->allentries = $this->manager->get_all_entries($this->get_groups_for_filtering());
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {
        global $USER;

        $dates = new dates($this->cm, $USER->id);
        $duedate = $dates->get_due_date();
        $name = get_string('duedate', 'data');

        if (empty($duedate)) {
            return new overviewitem(
                name: $name,
                value: null,
                content: '-',
            );
        }

        $content = humandate::create_from_timestamp($duedate);

        return new overviewitem(
            name: $name,
            value: $duedate,
            content: $content,
        );
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!$this->canviewall) {
            return null;
        }

        $name = get_string('view', 'moodle');
        $toapprove = 0;
        if ($this->manager->get_approval_requested()) {
            // Let's calculate how many entries need to be approved.
            $entries = $this->manager->filter_entries_by_approval($this->allentries, 0);
            $toapprove = count($entries);
            if ($toapprove > 0) {
                $name = get_string('approve', 'data');
            }
        }
        $alertlabel = get_string('numberofentriestoapprove', 'data', $toapprove);

        $content = new overviewaction(
            url: new url('/mod/data/view.php', ['id' => $this->cm->id]),
            text: $name,
            badgevalue: $toapprove > 0 ? $toapprove : null,
            badgetitle: $toapprove > 0 ? $alertlabel : null,
        );

        return new overviewitem(
            name: get_string('actions'),
            value: $toapprove,
            content: $content,
            textalign: text_align::CENTER,
            alertcount: $toapprove,
            alertlabel: $alertlabel,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'totalentries' => $this->get_extra_entries_overview(),
            'myentries' => $this->get_extra_myentries_overview(),
            'comments' => $this->get_extra_comments_overview(),
        ];
    }

    /**
     * Get the "Total entries" overview item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_totalentries_overview(): overviewitem {
        $allentries = $this->manager->get_all_entries();
        if ($this->manager->get_approval_requested()) {
            $allentries = $this->manager->filter_entries_by_approval($allentries, 1);
        }
        $totalentries = count($allentries);

        // Add total entries.
        return new overviewitem(
            name: $this->stringmanager->get_string('totalentries', 'data'),
            value: $totalentries,
            content: $totalentries,
            textalign: text_align::END,
        );
    }

    /**
     * Get the "My entries" overview item.
     *
     * @return ?overviewitem The overview item or null when the user is a student.
     */
    private function get_extra_myentries_overview(): ?overviewitem {
        global $USER;

        if ($this->canviewall) {
            return null;
        }

        $myentries = $this->manager->filter_entries_by_user($this->allentries, $USER->id);
        $totalmyentries = count($myentries);

        return new overviewitem(
            name: get_string('myentries', 'data'),
            value: $totalmyentries,
            content: $totalmyentries,
            textalign: text_align::END,
        );
    }

    /**
     * Get the "Entries" overview item for teachers.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_entries_overview(): overviewitem {
        if ($this->canviewall) {
            $name = get_string('entries', 'data');
            $totalentries = count($this->allentries);
        } else {
            $allentries = $this->allentries;
            if ($this->manager->get_approval_requested()) {
                $allentries = $this->manager->filter_entries_by_approval($this->allentries, 1);
            }
            $name = get_string('totalentries', 'data');
            $totalentries = count($allentries);
        }
        return new overviewitem(
            name: $name,
            value: $totalentries,
            content: $totalentries,
            textalign: text_align::END,
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
        if (empty($CFG->usecomments) || (empty($this->manager->get_instance()->comments))) {
            return new overviewitem(
                name: get_string('comments', 'data'),
                value: 0,
                content: '-',
                textalign: text_align::END,
            );
        }

        $approved = ($this->canviewall) ? null : 1;
        $comments = $this->manager->get_comments(approved: $approved, groups: $this->get_groups_for_filtering());
        $totalcomments = ($comments) ? count($comments) : 0;
        return new overviewitem(
            name: get_string('comments', 'data'),
            value: $totalcomments,
            content: $totalcomments,
            textalign: text_align::END,
        );
    }
}
