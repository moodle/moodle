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

use moodle_url;
use renderer_base;
use url_select;
use renderable;
use templatable;

/**
 * Renders the subscribers page for this activity.
 *
 * @package   mod_forum
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscription_actionbar implements renderable, templatable {
    /** @var int course id */
    private $id;

    /** @var moodle_url */
    private $currenturl;

    /** @var \stdClass  */
    private $forum;

    /** @var int */
    private $edit;

    /**
     * subscription_actionbar constructor.
     *
     * @param int $id The forum id.
     * @param moodle_url $currenturl Current URL.
     * @param \stdClass $forum The forum object.
     * @param int $edit This argument decides to show view/manage subscribers view.
     */
    public function __construct(int $id, moodle_url $currenturl, \stdClass $forum, int $edit) {
        $this->id = $id;
        $this->currenturl = $currenturl;
        $this->forum = $forum;
        $this->edit = $edit;
    }

    /**
     * Create url select menu for subscription option
     *
     * @return url_select|null the url_select object
     */
    private function create_subscription_menu(): ?url_select {
        // When user is on manage subscription, we don't have to show the subscription selector.
        if ($this->edit === 1 && !\mod_forum\subscriptions::is_forcesubscribed($this->forum)) {
            return  null;
        }

        $sesskey = sesskey();
        $modeset = \mod_forum\subscriptions::get_subscription_mode($this->forum);
        $optionallink = new moodle_url('/mod/forum/subscribe.php',
            ['id' => $this->id, 'mode' => FORUM_CHOOSESUBSCRIBE, 'sesskey' => $sesskey, 'edit' => $this->edit]);
        $forcedlink = new moodle_url('/mod/forum/subscribe.php',
            ['id' => $this->id, 'mode' => FORUM_FORCESUBSCRIBE, 'sesskey' => $sesskey, 'edit' => $this->edit]);
        $autolink = new moodle_url('/mod/forum/subscribe.php',
            ['id' => $this->id, 'mode' => FORUM_INITIALSUBSCRIBE, 'sesskey' => $sesskey, 'edit' => $this->edit]);
        $disabledlink = new moodle_url('/mod/forum/subscribe.php',
            ['id' => $this->id, 'mode' => FORUM_DISALLOWSUBSCRIBE, 'sesskey' => $sesskey, 'edit' => $this->edit]);

        $menu = [
            $optionallink->out(false) => get_string('subscriptionoptional', 'forum'),
            $forcedlink->out(false) => get_string('subscriptionforced', 'forum'),
            $autolink->out(false) => get_string('subscriptionauto', 'forum'),
            $disabledlink->out(false) => get_string('subscriptiondisabled', 'forum'),
        ];

        switch ($modeset) {
            case FORUM_CHOOSESUBSCRIBE:
                $set = get_string('subscriptionoptional', 'forum');
                break;
            case FORUM_FORCESUBSCRIBE:
                $set = get_string('subscriptionforced', 'forum');
                break;
            case FORUM_INITIALSUBSCRIBE:
                $set = get_string('subscriptionauto', 'forum');
                break;
            case FORUM_DISALLOWSUBSCRIBE:
                $set = get_string('subscriptiondisabled', 'forum');
                break;
            default:
                throw new \moodle_exception(get_string('invalidforcesubscribe', 'forum'));
        }

        $menu = array_filter($menu, function($key) use ($set) {
            if ($key !== $set) {
                return true;
            }
        });
        $urlselect = new url_select($menu, $this->currenturl, ['' => $set], 'selectsubscriptionoptions');
        $urlselect->set_label(get_string('subscriptionmode', 'mod_forum'), ['class' => 'mr-1']);
        $urlselect->set_help_icon('subscriptionmode', 'mod_forum');
        $urlselect->class .= ' float-right';
        return $urlselect;
    }

    /**
     * Create view and manage subscribers select menu.
     *
     * @return url_select|null get url_select object.
     */
    private function create_view_manage_menu(): ?url_select {
        // If forced subscription is used then no need to show the view.
        if (\mod_forum\subscriptions::is_forcesubscribed($this->forum)) {
            return null;
        }

        $viewlink = new moodle_url('/mod/forum/subscribers.php', ['id' => $this->id, 'edit' => 'off']);
        $managelink = new moodle_url('/mod/forum/subscribers.php', ['id' => $this->id, 'edit' => 'on']);

        $menu = [
            $viewlink->out(false) => get_string('forum:viewsubscribers', 'forum'),
            $managelink->out(false) => get_string('managesubscriptionson', 'forum'),
        ];

        if ($this->edit === 0) {
            $this->currenturl = $viewlink;
        } else {
            $this->currenturl = $managelink;
        }
        $urlselect = new url_select($menu, $this->currenturl->out(false), null, 'selectviewandmanagesubscribers');
        $urlselect->set_label(get_string('subscribers', 'forum'), ['class' => 'accesshide']);
        return $urlselect;
    }

    /**
     * Data for the template.
     *
     * @param renderer_base $output The render_base object.
     * @return array data for template
     */
    public function export_for_template(renderer_base $output): array {
        $data = [];
        $subscribeoptionselect = $this->create_subscription_menu();
        $viewmanageselect = $this->create_view_manage_menu();

        if ($subscribeoptionselect) {
            $data ['subscriptionoptions'] = $subscribeoptionselect->export_for_template($output);
        }
        if ($viewmanageselect) {
            $data['viewandmanageselect'] = $viewmanageselect->export_for_template($output);
        }
        return $data;
    }
}
