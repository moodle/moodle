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
 * Author exporter.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\exporters;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\author as author_entity;
use mod_forum\local\exporters\group as group_exporter;
use core\external\exporter;
use renderer_base;

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Author exporter.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class author extends exporter {
    /** @var author_entity $author Author entity */
    private $author;
    /** @var int|null $authorcontextid The context id for the author entity */
    private $authorcontextid;
    /** @var array $authorgroups List of groups that the author belongs to */
    private $authorgroups;
    /** @var bool $canview Should the author be anonymised? */
    private $canview;

    /**
     * Constructor.
     *
     * @param author_entity $author The author entity to export
     * @param int|null $authorcontextid The context id for the author entity to export (null if the user doesn't have one)
     * @param stdClass[] $authorgroups The list of groups that the author belongs to
     * @param bool $canview Can the requesting user view this author or should it be anonymised?
     * @param array $related The related data for the export.
     */
    public function __construct(
        author_entity $author,
        ?int $authorcontextid,
        array $authorgroups = [],
        bool $canview = true,
        array $related = []
    ) {
        $this->author = $author;
        $this->authorcontextid = $authorcontextid;
        $this->authorgroups = $authorgroups;
        $this->canview = $canview;
        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'fullname' => [
                'type' => PARAM_TEXT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'isdeleted' => [
                'type' => PARAM_BOOL,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'groups' => [
                'multiple' => true,
                'optional' => true,
                'type' => [
                    'id' => ['type' => PARAM_INT],
                    'name' => ['type' => PARAM_TEXT],
                    'urls' => [
                        'type' => [
                            'image' => [
                                'type' => PARAM_URL,
                                'optional' => true,
                                'default' => null,
                                'null' => NULL_ALLOWED
                            ]
                        ]
                    ]
                ]
            ],
            'urls' => [
                'type' => [
                    'profile' => [
                        'description' => 'The URL for the use profile page',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'profileimage' => [
                        'description' => 'The URL for the use profile image',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
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
        $author = $this->author;
        $authorcontextid = $this->authorcontextid;
        $urlfactory = $this->related['urlfactory'];
        $context = $this->related['context'];
        $forum = $this->related['forum'];

        if ($this->canview) {
            if ($author->is_deleted()) {
                return [
                    'id' => $author->get_id(),
                    'fullname' => get_string('deleteduser', 'mod_forum'),
                    'isdeleted' => true,
                    'groups' => [],
                    'urls' => [
                        'profile' => ($urlfactory->get_author_profile_url($author, $forum->get_course_id()))->out(false),
                        'profileimage' => ($urlfactory->get_author_profile_image_url($author, $authorcontextid))->out(false)
                    ]
                ];
            } else {
                $groups = array_map(function($group) use ($urlfactory, $context, $output) {
                    $imageurl = null;
                    $groupurl = null;
                    if (!$group->hidepicture) {
                        $imageurl = get_group_picture_url($group, $group->courseid, true);
                        if (empty($imageurl)) {
                            // Get a generic group image URL.
                            $imageurl = $output->image_url('g/g1');
                        }
                    }
                    if (course_can_view_participants($context)) {
                        $groupurl = $urlfactory->get_author_group_url($group);
                    }

                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'urls' => [
                            'image' => $imageurl ? $imageurl->out(false) : null,
                            'group' => $groupurl ? $groupurl->out(false) : null

                        ]
                    ];
                }, $this->authorgroups);

                return [
                    'id' => $author->get_id(),
                    'fullname' => $author->get_full_name(),
                    'isdeleted' => false,
                    'groups' => $groups,
                    'urls' => [
                        'profile' => ($urlfactory->get_author_profile_url($author, $forum->get_course_id()))->out(false),
                        'profileimage' => ($urlfactory->get_author_profile_image_url($author, $authorcontextid))->out(false)
                    ]
                ];
            }
        } else {
            // The author should be anonymised.
            return [
                'id' => null,
                'fullname' => get_string('forumauthorhidden', 'mod_forum'),
                'isdeleted' => null,
                'groups' => [],
                'urls' => [
                    'profile' => null,
                    'profileimage' => null
                ]
            ];
        }
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'urlfactory' => 'mod_forum\local\factories\url',
            'context' => 'context',
            'forum' => 'mod_forum\local\entities\forum',
        ];
    }
}
