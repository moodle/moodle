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
 * Discussion summaries exporter.
 *
 * @package     mod_forum
 * @copyright   2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\exporters;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\exporters\post as post_exporter;
use core\external\exporter;
use renderer_base;

/**
 * Discussion summaries exporter.
 *
 * @copyright   2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion_summaries extends exporter {
    /** @var discussion_summary_entity[] The list of discussion summaries to export */
    private $discussions;

    /** @var stdClass[] The group information for each author */
    private $groupsbyid;

    /** @var stdClass[] The group information for each author */
    private $groupsbyauthorid;

    /** @var int[] Discussion reply counts indexed by dicussion id */
    private $discussionreplycount;

    /** @var int[] Discussion unread counts indexed by dicussion id */
    private $discussionunreadcount;

    /** @var array The latest post in each discussion */
    private $latestpostids;

    /** @var int[] The context ids for the first and latest post authors (indexed by author id) */
    private $postauthorcontextids;

    /**
     * Constructor.
     *
     * @param discussion_summary_entity[] $discussion The list of discussion summaries to export
     * @param stdClass[] $groupsbyid The group information for each author
     * @param stdClass[] $groupsbyauthorid The group information for each author
     * @param int[] $discussionreplycount Discussion reply counts indexed by dicussion id
     * @param int[] $discussionunreadcount Discussion unread counts indexed by dicussion id
     * @param int[] $latestpostids List of latest post ids indexed by discussion id
     * @param int[] $postauthorcontextids The context ids for the first and latest post authors (indexed by author id)
     * @param array $related The related
     */
    public function __construct(
        array $discussions,
        array $groupsbyid,
        array $groupsbyauthorid,
        array $discussionreplycount,
        array $discussionunreadcount,
        array $latestpostids,
        array $postauthorcontextids,
        array $related = []
    ) {
        $this->discussions = $discussions;
        $this->groupsbyid = $groupsbyid;
        $this->groupsbyauthorid = $groupsbyauthorid;
        $this->discussionreplycount = $discussionreplycount;
        $this->discussionunreadcount = $discussionunreadcount;
        $this->latestpostids = $latestpostids;
        $this->postauthorcontextids = $postauthorcontextids;
        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'summaries' => [
                'type' => discussion_summary::read_properties_definition(),
                'multiple' => true
            ],
            'state' => [
                'type' => [
                    'hasdiscussions' => ['type' => PARAM_BOOL],
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
        $exporteddiscussions = [];
        $related = $this->related;

        foreach ($this->discussions as $discussion) {
            $discussionid = $discussion->get_discussion()->get_id();
            $replycount = isset($this->discussionreplycount[$discussionid]) ? $this->discussionreplycount[$discussionid] : 0;
            $unreadcount = isset($this->discussionunreadcount[$discussionid]) ? $this->discussionunreadcount[$discussionid] : 0;
            $latestpostid = isset($this->latestpostids[$discussionid]) ? $this->latestpostids[$discussionid] : 0;
            $exporter = new discussion_summary(
                    $discussion,
                    $this->groupsbyid,
                    $this->groupsbyauthorid,
                    $replycount,
                    $unreadcount,
                    $latestpostid,
                    $this->postauthorcontextids[$discussion->get_first_post_author()->get_id()],
                    $this->postauthorcontextids[$discussion->get_latest_post_author()->get_id()],
                    $related
                );
            $exporteddiscussions[] = $exporter->export($output);
        }

        return [
            'summaries' => $exporteddiscussions,
            'state' => [
                'hasdiscussions' => !empty($exporteddiscussions),
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
            'context' => 'context',
            'forum' => 'mod_forum\local\entities\forum',
            'capabilitymanager' => 'mod_forum\local\managers\capability',
            'urlfactory' => 'mod_forum\local\factories\url',
            'user' => 'stdClass',
            'favouriteids' => 'int[]?'
        ];
    }
}
