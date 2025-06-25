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
 * Forum Exporter.
 *
 * @package     mod_forum
 * @copyright   2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\exporters;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\forum as forum_entity;
use mod_forum\local\exporters\post as post_exporter;
use core\external\exporter;
use renderer_base;
use stdClass;

/**
 * Forum class.
 *
 * @copyright   2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum extends exporter {
    /** @var forum_entity The entity relating to the forum being displayed */
    private $forum;

    /**
     * Constructor for the forum exporter.
     *
     * @param   forum_entity    $forum The forum being displayed
     * @param   array           $related The related objects
     */
    public function __construct(forum_entity $forum, $related = []) {
        $this->forum = $forum;
        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'id' => ['type' => PARAM_INT],
            'name' => ['type' => PARAM_RAW],
            'state' => [
                'type' => [
                    'groupmode' => ['type' => PARAM_INT],
                    'gradingenabled' => ['type' => PARAM_BOOL],
                ],
            ],
            'userstate' => [
                'type' => [
                    'tracked' => ['type' => PARAM_INT],
                    'subscribed' => ['type' => PARAM_INT],
                ],
            ],
            'capabilities' => [
                'type' => [
                    'viewdiscussions' => ['type' => PARAM_BOOL],
                    'create' => ['type' => PARAM_BOOL],
                    'subscribe' => ['type' => PARAM_BOOL],
                    'grade' => ['type' => PARAM_BOOL],
                ]
            ],
            'urls' => [
                'type' => [
                    'create' => ['type' => PARAM_URL],
                    'markasread' => ['type' => PARAM_URL],
                    'view' => ['type' => PARAM_URL],
                    'sortrepliesasc' => ['type' => PARAM_URL],
                    'sortrepliesdesc' => ['type' => PARAM_URL],
                    'sortlastpostasc' => ['type' => PARAM_URL],
                    'sortlastpostdesc' => ['type' => PARAM_URL],
                    'sortcreatedasc' => ['type' => PARAM_URL],
                    'sortcreateddesc' => ['type' => PARAM_URL],
                    'sortdiscussionasc' => ['type' => PARAM_URL],
                    'sortdiscussiondesc' => ['type' => PARAM_URL],
                    'sortstarterasc' => ['type' => PARAM_URL],
                    'sortstarterdesc' => ['type' => PARAM_URL],
                    'sortgroupasc' => ['type' => PARAM_URL],
                    'sortgroupdesc' => ['type' => PARAM_URL],
                ],
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $capabilitymanager = $this->related['capabilitymanager'];
        $urlfactory = $this->related['urlfactory'];
        $user = $this->related['user'];
        $currentgroup = $this->related['currentgroup'];
        $vaultfactory = $this->related['vaultfactory'];
        $discussionvault = $vaultfactory->get_discussions_in_forum_vault();

        return [
            'id' => $this->forum->get_id(),
            'name' => $this->forum->get_name(),
            'state' => [
                'groupmode' => $this->forum->get_effective_group_mode(),
                'gradingenabled' => $this->forum->is_grading_enabled()
            ],
            'userstate' => [
                'tracked' => (int) forum_tp_is_tracked($this->get_forum_record(), $this->related['user']),
                'subscribed' => (int) \mod_forum\subscriptions::is_subscribed(
                    $this->related['user']->id,
                    $this->get_forum_record(),
                ),
            ],
            'capabilities' => [
                'viewdiscussions' => $capabilitymanager->can_view_discussions($user),
                'create' => $capabilitymanager->can_create_discussions($user, $currentgroup),
                'selfenrol' => $capabilitymanager->can_self_enrol($user),
                'subscribe' => $capabilitymanager->can_subscribe_to_forum($user),
                'grade' => $capabilitymanager->can_grade($user),
            ],
            'urls' => [
                'create' => $urlfactory->get_discussion_create_url($this->forum)->out(false),
                'markasread' => $urlfactory->get_mark_all_discussions_as_read_url($this->forum)->out(false),
                'view' => $urlfactory->get_forum_view_url_from_forum($this->forum)->out(false),
                'sortrepliesasc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_REPLIES_ASC)->out(false),
                'sortrepliesdesc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_REPLIES_DESC)->out(false),
                'sortlastpostasc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_LASTPOST_ASC)->out(false),
                'sortlastpostdesc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_LASTPOST_DESC)->out(false),
                'sortcreatedasc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_CREATED_ASC)->out(false),
                'sortcreateddesc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_CREATED_DESC)->out(false),
                'sortdiscussionasc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_DISCUSSION_ASC)->out(false),
                'sortdiscussiondesc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_DISCUSSION_DESC)->out(false),
                'sortstarterasc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_STARTER_ASC)->out(false),
                'sortstarterdesc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_STARTER_DESC)->out(false),
                'sortgroupasc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_GROUP_ASC)->out(false),
                'sortgroupdesc' => $urlfactory->get_forum_view_url_from_forum($this->forum, null,
                    $discussionvault::SORTORDER_GROUP_DESC)->out(false),
            ],
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'legacydatamapperfactory' => 'mod_forum\local\factories\legacy_data_mapper',
            'capabilitymanager' => 'mod_forum\local\managers\capability',
            'urlfactory' => 'mod_forum\local\factories\url',
            'user' => 'stdClass',
            'currentgroup' => 'int?',
            'vaultfactory' => 'mod_forum\local\factories\vault'
        ];
    }

    /**
     * Get the legacy forum record for this forum.
     *
     * @return  stdClass
     */
    private function get_forum_record(): stdClass {
        $forumdbdatamapper = $this->related['legacydatamapperfactory']->get_forum_data_mapper();
        return $forumdbdatamapper->to_legacy_object($this->forum);
    }
}
