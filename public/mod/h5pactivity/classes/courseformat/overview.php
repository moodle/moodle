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

namespace mod_h5pactivity\courseformat;

use cm_info;
use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\text_align;
use core\output\local\properties\button;
use core\url;
use core_courseformat\output\local\overview\overviewdialog;
use mod_h5pactivity\local\manager;

/**
 * H5P activity overview integration.
 *
 * @package    mod_h5pactivity
 * @copyright  2025 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {

    /** @var manager H5P activity manager. */
    private $manager;

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
    ) {
        parent::__construct($cm);

        $this->manager = manager::create_from_coursemodule($cm);
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {

        if (!$this->manager->can_view_all_attempts()) {
            return null;
        }

        $viewresults = get_string('view');
        $content = new action_link(
            url: new url('/mod/h5pactivity/report.php', ['id' => $this->cm->id]),
            text: $viewresults,
            attributes: ['class' => button::BODY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: get_string('actions'),
            value: '',
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'h5ptype' => $this->get_extra_h5ptype_overview(),
            'attempted' => $this->get_extra_studentsattempted_overview(),
            'totalattempts' => $this->get_extra_totalattempts_overview(),
            'myattempts' => $this->get_extra_userattempts_overview(),
        ];
    }

    /**
     * Get the attempts of the given user.
     *
     * @param int|null $userid The user to return the attempts from (uses current user if null).
     * @return overviewitem|null The overview item or null for teachers.
     */
    private function get_extra_userattempts_overview(?int $userid = null): ?overviewitem {
        global $USER;

        if ($this->manager->can_view_all_attempts()) {
            return null;
        }

        if ($userid === null) {
            $userid = $USER->id;
        }

        $attempts = $this->manager->count_attempts($userid);
        return new overviewitem(
            name: get_string('attempts', 'mod_h5pactivity'),
            value: $attempts,
            content: $attempts ?? '-',
            textalign: text_align::END,
        );
    }

    /**
     * Get the students who attempted.
     *
     * @return overviewitem|null The overview item or null for students.
     */
    private function get_extra_studentsattempted_overview(): ?overviewitem {

        if (!$this->manager->can_view_all_attempts()) {
            return null;
        }

        $groups = $this->get_groups_for_filtering();
        $attempts = $this->manager->count_users_attempts($groups);
        $participants = get_users_by_capability(
            context: $this->context,
            capability: 'mod/h5pactivity:submit',
            groups: array_keys($groups),
        );
        $params = [
            'count' => count($attempts),
            'total' => count($participants),
        ];
        return new overviewitem(
            name: get_string('attempted', 'mod_h5pactivity'),
            value: count($attempts),
            content: get_string('count_of_total', 'core', $params),
            textalign: text_align::END,
        );
    }

    /**
     * Get the "Total attempts" colum data.
     *
     * @return overviewitem|null The overview item or null for students.
     */
    private function get_extra_totalattempts_overview(): ?overviewitem {

        if (!$this->manager->can_view_all_attempts()) {
            return null;
        }

        $groups = $this->get_groups_for_filtering();
        $totalattempts = $this->manager->count_attempts(groups: $groups);
        $totalusers = $this->manager->count_users_attempts(groups: $groups);

        $averageattempts = 0;
        if ($totalusers && count($totalusers) > 0 && $totalattempts) {
            $averageattempts = round($totalattempts / count($totalusers), 1);
        }
        $content = new overviewdialog(
            buttoncontent: $totalattempts,
            title: get_string('totalattempts', 'mod_h5pactivity'),
            definition: ['buttonclasses' => button::BODY_OUTLINE->classes()],
        );
        $method = $this->manager::get_grading_methods()[$this->manager->get_instance()->grademethod];
        $content->add_item(get_string('gradingmethod', 'grading'), $method);
        $content->add_item(get_string('averageattempts', 'mod_h5pactivity'), $averageattempts);

        return new overviewitem(
            name: get_string('totalattempts', 'mod_h5pactivity'),
            value: $totalattempts,
            content: $content,
        );
    }

    /**
     * Get the H5P content type.
     *
     * @return overviewitem|null The overview item or null for students.
     */
    private function get_extra_h5ptype_overview(): ?overviewitem {

        if (!$this->manager->can_view_all_attempts()) {
            return null;
        }

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_h5pactivity', 'package', 0, 'id', false);
        $file = reset($files);

        $h5p = \core_h5p\api::get_content_from_pathnamehash($file->get_pathnamehash());

        $unknonwoverview = new overviewitem(
            name: get_string('contenttype', 'mod_h5pactivity'),
            value: get_string('unknowntype', 'mod_h5pactivity'),
            content: get_string('unknowntype', 'mod_h5pactivity'),
        );

        if (empty($h5p)) {
            return $unknonwoverview;
        }

        $h5plib = \core_h5p\api::get_library($h5p->mainlibraryid);

        // If the content is not yet deployed we cannot show the content type.
        if (empty($h5plib)) {
            return $unknonwoverview;
        }

        return new overviewitem(
            name: get_string('contenttype', 'mod_h5pactivity'),
            value: $h5plib->title,
            content: $h5plib->title,
        );
    }
}
