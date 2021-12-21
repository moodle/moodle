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

namespace mod_forum\output;

use renderable;
use renderer_base;
use templatable;
use moodle_url;
use help_icon;
use mod_forum\local\entities\forum as forum_entity;

/**
 * Render activity page for tertiary nav
 *
 * Render elements search forum, add new discussion button and subscribe all
 * to the page action.
 *
 * @package mod_forum
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum_actionbar implements renderable, templatable {
    /**
     * @var forum_entity $forum
     */
    private $forum;

    /**
     * @var \stdClass $course
     */
    private $course;

    /**
     * @var mixed $groupid
     */
    private $groupid;

    /**
     * @var string $search
     */
    private $search;

    /**
     * forum_actionbar constructor.
     *
     * @param forum_entity $forum The forum object.
     * @param \stdClass $course The course object.
     * @param int|null $groupid The group id.
     * @param string $search The search string.
     */
    public function __construct(forum_entity $forum, \stdClass $course, ?int $groupid, string $search) {
        $this->forum = $forum;
        $this->course = $course;
        $this->groupid = $groupid;
        $this->search = $search;
    }

    /**
     * Render the new discussion button.
     *
     * @return string HTML button
     */
    private function get_new_discussion_topic_button(): string {
        global $USER;
        $renderfactory = \mod_forum\local\container::get_renderer_factory();
        $discussionrenderer = $renderfactory->get_discussion_list_renderer($this->forum);
        return $discussionrenderer->render_new_discussion($USER, $this->groupid);
    }

    /**
     * Data for the template.
     *
     * @param renderer_base $output The render_base object.
     * @return array data for the template
     */
    public function export_for_template(renderer_base $output): array {
        global $USER;
        $actionurl = (new moodle_url('/mod/forum/search.php'))->out(false);
        $helpicon = new help_icon('search', 'core');
        $hiddenfields = [
            (object) ['name' => 'id', 'value' => $this->course->id],
        ];
        $shownewdiscussionbtn = '';
        if ($this->forum->get_type() !== 'single') {
            $shownewdiscussionbtn = $this->get_new_discussion_topic_button();
        }
        $data = [
            'action' => $actionurl,
            'hiddenfields' => $hiddenfields,
            'query' => $this->search,
            'helpicon' => $helpicon->export_for_template($output),
            'inputname' => 'search',
            'searchstring' => get_string('searchforums', 'mod_forum'),
            'newdiscussionbtn' => $shownewdiscussionbtn,
        ];

        $legacydatamapperfactory = \mod_forum\local\container::get_legacy_data_mapper_factory();
        $forumobject = $legacydatamapperfactory->get_forum_data_mapper()->to_legacy_object($this->forum);
        $context = $this->forum->get_context();
        $activeenrolled = is_enrolled($context, $USER, '', true);
        $canmanage = has_capability('mod/forum:managesubscriptions', $context);
        $cansubscribe = $activeenrolled && !($this->forum->get_subscription_mode() === FORUM_FORCESUBSCRIBE) &&
            (!($this->forum->get_subscription_mode() === FORUM_DISALLOWSUBSCRIBE) || $canmanage);
        if ($cansubscribe) {
            $returnurl =
                (new moodle_url('/mod/forum/view.php', ['id' => $this->forum->get_course_module_record()->id]))->out(false);
            if (!\mod_forum\subscriptions::is_subscribed($USER->id, $forumobject, null, $this->forum->get_course_module_record())) {
                $data['subscribetoforum'] = (new moodle_url(
                    '/mod/forum/subscribe.php',
                    ['id' => $forumobject->id, 'sesskey' => sesskey(), 'returnurl' => $returnurl]
                    ))->out(false);
            } else {
                $data['unsubscribefromforum'] = (new moodle_url(
                    '/mod/forum/subscribe.php',
                    ['id' => $forumobject->id, 'sesskey' => sesskey(), 'returnurl' => $returnurl]
                ))->out(false);
            }
        }
        return $data;
    }
}
