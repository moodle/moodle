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
 * Discussion exporter class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\exporters;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\exporters\post as post_exporter;
use mod_forum\local\factories\exporter as exporter_factory;
use core\external\exporter;
use renderer_base;

/**
 * Discussion exporter class.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion extends exporter {
    /** @var discussion_entity $discussion Discussion to export */
    private $discussion;

    /**
     * Constructor.
     *
     * @param discussion_entity $discussion Discussion to export
     * @param array $related The related export data
     */
    public function __construct(discussion_entity $discussion, array $related = []) {
        $this->discussion = $discussion;

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
            'forumid' => ['type' => PARAM_INT],
            'pinned' => ['type' => PARAM_BOOL],
            'locked' => ['type' => PARAM_BOOL],
            'istimelocked' => ['type' => PARAM_BOOL],
            'name' => ['type' => PARAM_TEXT],
            'firstpostid' => ['type' => PARAM_INT],
            'group' => [
                'optional' => true,
                'type' => [
                    'name' => ['type' => PARAM_TEXT],
                    'urls' => [
                        'type' => [
                            'picture' => [
                                'optional' => true,
                                'type' => PARAM_URL,
                            ],
                            'userlist' => [
                                'optional' => true,
                                'type' => PARAM_URL,
                            ],
                        ],
                    ],
                ],
            ],
            'times' => [
                'type' => [
                    'modified' => ['type' => PARAM_INT],
                    'start' => ['type' => PARAM_INT],
                    'end' => ['type' => PARAM_INT],
                    'locked' => ['type' => PARAM_INT],
                ],
            ],
            'userstate' => [
                'type' => [
                    'subscribed' => ['type' => PARAM_BOOL],
                    'favourited' => ['type' => PARAM_BOOL],
                ],
            ],
            'capabilities' => [
                'type' => [
                    'subscribe' => ['type' => PARAM_BOOL],
                    'move' => ['type' => PARAM_BOOL],
                    'pin' => ['type' => PARAM_BOOL],
                    'post' => ['type' => PARAM_BOOL],
                    'manage' => ['type' => PARAM_BOOL],
                    'favourite' => ['type' => PARAM_BOOL]
                ]
            ],
            'urls' => [
                'type' => [
                    'view' => ['type' => PARAM_URL],
                    'viewlatest' => [
                        'optional' => true,
                        'type' => PARAM_URL
                    ],
                    'viewfirstunread' => [
                        'optional' => true,
                        'type' => PARAM_URL,
                    ],
                    'markasread' => ['type' => PARAM_URL],
                    'subscribe' => ['type' => PARAM_URL],
                    'pin' => [
                        'optional' => true,
                        'type' => PARAM_URL,
                    ],
                ],
            ],
            'timed' => [
                'type' => [
                    'istimed' => [
                        'type' => PARAM_BOOL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'visible' => [
                        'type' => PARAM_BOOL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ]
                ]
            ]
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
        $favouriteids = isset($this->related['favouriteids']) ? $this->related['favouriteids'] : [];

        $forum = $this->related['forum'];
        $forumrecord = $this->get_forum_record();
        $user = $this->related['user'];
        $discussion = $this->discussion;

        $groupdata = null;
        if ($discussion->has_group()) {
            $groupsbyid = $this->related['groupsbyid'];
            $group = $groupsbyid[$discussion->get_group_id()] ?? null;

            // We may not have received the group if the caller doesn't want to include it in the export
            // or if it's been deleted and the discussion record hasn't been updated.
            if ($group) {
                $groupdata = [
                    'name' => $group->name,
                    'urls' => [],
                ];

                if (!$group->hidepicture) {
                    $url = get_group_picture_url($group, $forum->get_course_id());
                    if (!empty($url)) {
                        $groupdata['urls']['picture'] = $url;
                    }
                }

                if ($capabilitymanager->can_view_participants($user, $discussion)) {
                    $groupdata['urls']['userlist'] = (new \moodle_url('/user/index.php', [
                        'id' => $forum->get_course_id(),
                        'group' => $group->id,
                    ]));
                }
            }
        }

        $viewfirstunreadurl = $urlfactory->get_discussion_view_first_unread_post_url_from_discussion($discussion);
        $data = [
            'id' => $discussion->get_id(),
            'forumid' => $forum->get_id(),
            'pinned' => $discussion->is_pinned(),
            'locked' => $forum->is_discussion_locked($discussion),
            'istimelocked' => $forum->is_discussion_time_locked($discussion),
            'name' => format_string($discussion->get_name(), true, [
                'context' => $this->related['context']
            ]),
            'firstpostid' => $discussion->get_first_post_id(),
            'times' => [
                'modified' => $discussion->get_time_modified(),
                'start' => $discussion->get_time_start(),
                'end' => $discussion->get_time_end(),
                'locked' => $discussion->get_locked()
            ],
            'userstate' => [
                'subscribed' => \mod_forum\subscriptions::is_subscribed($user->id, $forumrecord, $discussion->get_id()),
                'favourited' => in_array($discussion->get_id(), $favouriteids) ? true : false,
            ],
            'capabilities' => [
                'subscribe' => $capabilitymanager->can_subscribe_to_discussion($user, $discussion),
                'move' => $capabilitymanager->can_move_discussion($user, $discussion),
                'pin' => $capabilitymanager->can_pin_discussion($user, $discussion),
                'post' => $capabilitymanager->can_post_in_discussion($user, $discussion),
                'manage' => $capabilitymanager->can_manage_forum($user),
                'favourite' => $capabilitymanager->can_favourite_discussion($user) // Defaulting to true until we get capabilities sorted
            ],
            'urls' => [
                'view' => $urlfactory->get_discussion_view_url_from_discussion($discussion)->out(false),
                'viewfirstunread' => $viewfirstunreadurl->out(false),
                'markasread' => $urlfactory->get_mark_discussion_as_read_url_from_discussion($forum, $discussion)->out(false),
                'subscribe' => $urlfactory->get_discussion_subscribe_url($discussion)->out(false)
            ]
        ];

        if (!empty($this->related['latestpostid'])) {
            $data['urls']['viewlatest'] = $urlfactory->get_discussion_view_latest_post_url_from_discussion(
                    $discussion,
                    $this->related['latestpostid']
                )->out(false);
        }

        if ($capabilitymanager->can_pin_discussions($user)) {
            $data['urls']['pin'] = $urlfactory->get_pin_discussion_url_from_discussion($discussion)->out(false);
        }

        if ($groupdata) {
            $data['group'] = $groupdata;
        }

        $canviewhiddentimedposts = $capabilitymanager->can_view_hidden_posts($user);
        $canalwaysseetimedpost = $user->id == $discussion->get_user_id() || $canviewhiddentimedposts;
        $data['timed']['istimed'] = $canalwaysseetimedpost ? $discussion->is_timed_discussion() : null;
        $data['timed']['visible'] = $canalwaysseetimedpost ? $discussion->is_timed_discussion_visible() : null;

        return $data;
    }

    /**
     * Get the legacy forum record from the forum entity.
     *
     * @return stdClass
     */
    private function get_forum_record() {
        $forumdbdatamapper = $this->related['legacydatamapperfactory']->get_forum_data_mapper();
        return $forumdbdatamapper->to_legacy_object($this->related['forum']);
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'legacydatamapperfactory' => 'mod_forum\local\factories\legacy_data_mapper',
            'context' => 'context',
            'forum' => 'mod_forum\local\entities\forum',
            'capabilitymanager' => 'mod_forum\local\managers\capability',
            'urlfactory' => 'mod_forum\local\factories\url',
            'user' => 'stdClass',
            'groupsbyid' => 'stdClass[]',
            'latestpostid' => 'int?',
            'favouriteids' => 'int[]?'
        ];
    }
}
