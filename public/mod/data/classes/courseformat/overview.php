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

use core_calendar\output\humandate;
use cm_info;
use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\text_align;
use core\output\local\properties\button;
use core\url;
use mod_data\dates;
use mod_data\manager;

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
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {
        global $USER;

        $dates = new dates($this->cm, $USER->id);
        $duedate = $dates->get_due_date();
        $name = $this->stringmanager->get_string('duedate', 'data');

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

        $text = $this->stringmanager->get_string('view', 'moodle');
        $toapprove = 0;
        $alertlabel = $this->stringmanager->get_string('numberofentriestoapprove', 'data');
        if ($this->manager->get_approval_requested()) {
            // Let's calculate how many entries need to be approved.
            $entries = $this->manager->filter_entries_by_approval($this->manager->get_all_entries(), 0);
            $toapprove = count($entries);

            $name = $this->stringmanager->get_string('approve', 'data');
            if ($toapprove > 0) {
                $renderer = $this->rendererhelper->get_core_renderer();
                $badge = $renderer->notice_badge(
                    contents: $toapprove,
                    title: $alertlabel,
                );
                $text = $name . $badge;
            }
        }

        $content = new action_link(
            url: new url('/mod/data/view.php', ['id' => $this->cm->id]),
            text: $text,
            attributes: ['class' => button::BODY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: $this->stringmanager->get_string('actions'),
            value: $toapprove,
            content: $content,
            textalign: text_align::CENTER,
            alertcount: $toapprove,
            alertlabel: $alertlabel,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        $columns = [];
        // Add entry columns for each view.
        if ($this->canviewall) {
            $columns['totalentries'] = $this->get_extra_entries_overview();
        } else {
            $columns['totalentries'] = $this->get_extra_totalentries_overview();
            $columns['myentries'] = $this->get_extra_myentries_overview();
        }

        // Add comments column for all views.
        $columns['comments'] = $this->get_extra_comments_overview();

        return $columns;
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
            textalign: text_align::CENTER,
        );
    }

    /**
     * Get the "My entries" overview item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_myentries_overview(): overviewitem {
        global $USER;

        $myentries = $this->manager->filter_entries_by_user($this->manager->get_all_entries(), $USER->id);
        $totalmyentries = count($myentries);

        return new overviewitem(
            name: $this->stringmanager->get_string('myentries', 'data'),
            value: $totalmyentries,
            content: $totalmyentries,
            textalign: text_align::CENTER,
        );
    }

    /**
     * Get the "Entries" overview item for teachers.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_entries_overview(): overviewitem {
        $allentries = $this->manager->get_all_entries();
        $totalentries = count($allentries);

        // Add total entries.
        return new overviewitem(
            name: $this->stringmanager->get_string('entries', 'data'),
            value: $totalentries,
            content: $totalentries,
            textalign: text_align::CENTER,
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
                name: $this->stringmanager->get_string('comments', 'data'),
                value: 0,
                content: '-',
                textalign: text_align::CENTER,
            );
        }

        $approved = ($this->canviewall) ? null : 1;
        $comments = $this->manager->get_comments(approved: $approved);
        $totalcomments = ($comments) ? count($comments) : 0;
        return new overviewitem(
            name: $this->stringmanager->get_string('comments', 'data'),
            value: $totalcomments,
            content: $totalcomments,
            textalign: text_align::CENTER,
        );
    }
}
