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
 * Discussion summary class.
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\entities;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\entities\post as post_entity;
use mod_forum\local\entities\author as author_entity;

/**
 * Discussion summary class.
 *
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion_summary {
    /** @var discussion_entity $discussion The discussion being summarised */
    private $discussion;
    /** @var author_entity $firstpostauthor Author of the first post in the discussion */
    private $firstpostauthor;
    /** @var post_entity $firstpost First post in the discussion */
    private $firstpost;
    /** @var author_entity $latestpostauthor Author of the last post in the discussion */
    private $latestpostauthor;

    /**
     * Constructor.
     *
     * @param discussion_entity $discussion The discussion being summarised
     * @param post_entity $firstpost First post in the discussion
     * @param author_entity $firstpostauthor Author of the first post in the discussion
     * @param author_entity $latestpostauthor Author of the last post in the discussion
     */
    public function __construct(
        discussion_entity $discussion,
        post_entity $firstpost,
        author_entity $firstpostauthor,
        author_entity $latestpostauthor
    ) {
        $this->discussion = $discussion;
        $this->firstpostauthor = $firstpostauthor;
        $this->firstpost = $firstpost;
        $this->latestpostauthor = $latestpostauthor;
    }

    /**
     * Get the discussion entity.
     *
     * @return discussion_entity
     */
    public function get_discussion() : discussion_entity {
        return $this->discussion;
    }

    /**
     * Get the author entity for the first post.
     *
     * @return author_entity
     */
    public function get_first_post_author() : author_entity {
        return $this->firstpostauthor;
    }

    /**
     * Get the author entity for the last post.
     *
     * @return author_entity
     */
    public function get_latest_post_author() : author_entity {
        return $this->latestpostauthor;
    }

    /**
     * Get the post entity for the first post.
     *
     * @return post_entity
     */
    public function get_first_post() : post_entity {
        return $this->firstpost;
    }
}
