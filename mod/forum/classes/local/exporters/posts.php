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
 * Posts exporter class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\exporters;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\author as author_entity;
use mod_forum\local\entities\post as post_entity;
use mod_forum\local\exporters\post as post_exporter;
use core\external\exporter;
use renderer_base;

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Posts exporter class.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class posts extends exporter {
    /** @var post_entity[] $posts List of posts to export */
    private $posts;
    /** @var author_entity[] $authorsbyid List of authors for the posts indexed by author id */
    private $authorsbyid;
    /** @var array $attachmentsbypostid List of attachments indexed by post id */
    private $attachmentsbypostid;
    /** @var array $groupsbyauthorid List of author's groups indexed by author id */
    private $groupsbyauthorid;
    /** @var array $tagsbypostid List of tags indexed by post id */
    private $tagsbypostid;
    /** @var array $ratingbypostid List of ratings indexed by post id */
    private $ratingbypostid;

    /**
     * Constructor.
     *
     * @param post_entity[] $posts List of posts to export
     * @param author_entity[] $authorsbyid List of authors for the posts indexed by author id
     * @param array $attachmentsbypostid List of attachments indexed by post id
     * @param array $groupsbyauthorid List of author's groups indexed by author id
     * @param array $tagsbypostid List of tags indexed by post id
     * @param array $ratingbypostid List of ratings indexed by post id
     * @param array $related The related objects for exporting
     */
    public function __construct(
        array $posts,
        array $authorsbyid = [],
        array $attachmentsbypostid = [],
        array $groupsbyauthorid = [],
        array $tagsbypostid = [],
        array $ratingbypostid = [],
        array $related = []
    ) {
        $this->posts = $posts;
        $this->authorsbyid = $authorsbyid;
        $this->attachmentsbypostid = $attachmentsbypostid;
        $this->groupsbyauthorid = $groupsbyauthorid;
        $this->tagsbypostid = $tagsbypostid;
        $this->ratingbypostid = $ratingbypostid;
        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'posts' => [
                'type' => post_exporter::read_properties_definition(),
                'multiple' => true
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
        $related = $this->related;
        $authorsbyid = $this->authorsbyid;
        $attachmentsbypostid = $this->attachmentsbypostid;
        $groupsbyauthorid = $this->groupsbyauthorid;
        $tagsbypostid = $this->tagsbypostid;
        $ratingbypostid = $this->ratingbypostid;
        $exportedposts = array_map(
            function($post) use (
                $related,
                $authorsbyid,
                $attachmentsbypostid,
                $groupsbyauthorid,
                $tagsbypostid,
                $ratingbypostid,
                $output
            ) {
                $authorid = $post->get_author_id();
                $postid = $post->get_id();
                $author = isset($authorsbyid[$authorid]) ? $authorsbyid[$authorid] : [];
                $attachments = isset($attachmentsbypostid[$postid]) ? $attachmentsbypostid[$postid] : [];
                $authorgroups = isset($groupsbyauthorid[$authorid]) ? $groupsbyauthorid[$authorid] : [];
                $tags = isset($tagsbypostid[$postid]) ? $tagsbypostid[$postid] : [];
                $rating = isset($ratingbypostid[$postid]) ? $ratingbypostid[$postid] : null;
                $exporter = new post_exporter($post, array_merge($related, [
                    'author' => $author,
                    'attachments' => $attachments,
                    'authorgroups' => $authorgroups,
                    'tags' => $tags,
                    'rating' => $rating
                ]));
                return $exporter->export($output);
            },
            $this->posts
        );

        return [
            'posts' => $exportedposts
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'capabilitymanager' => 'mod_forum\local\managers\capability',
            'urlfactory' => 'mod_forum\local\factories\url',
            'forum' => 'mod_forum\local\entities\forum',
            'discussion' => 'mod_forum\local\entities\discussion',
            'readreceiptcollection' => 'mod_forum\local\entities\post_read_receipt_collection?',
            'user' => 'stdClass',
            'context' => 'context',
            'includehtml' => 'bool'
        ];
    }
}
