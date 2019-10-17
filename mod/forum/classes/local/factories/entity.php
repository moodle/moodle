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
 * Entity factory.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\factories;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\author as author_entity;
use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\entities\discussion_summary as discussion_summary_entity;
use mod_forum\local\entities\forum as forum_entity;
use mod_forum\local\entities\post as post_entity;
use mod_forum\local\entities\post_read_receipt_collection as post_read_receipt_collection_entity;
use mod_forum\local\entities\sorter as sorter_entity;
use stdClass;
use context;
use cm_info;
use user_picture;
use moodle_url;

/**
 * Entity factory to create the forum entities.
 *
 * See:
 * https://designpatternsphp.readthedocs.io/en/latest/Creational/SimpleFactory/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entity {
    /**
     * Create a forum entity from a stdClass (legacy forum object).
     *
     * @param stdClass $record The forum record
     * @param context $context The forum module context
     * @param stdClass $coursemodule Course module record for the forum
     * @param stdClass $course Course the forum belongs to
     * @return forum_entity
     */
    public function get_forum_from_stdclass(
        stdClass $record,
        context $context,
        stdClass $coursemodule,
        stdClass $course
    ) : forum_entity {
        // Note: cm_info::create loads a cm_info in the context of the current user which
        // creates hidden dependency on the logged in user (very bad) however it's the best
        // option to load some data we need which doesn't require the logged in user.
        // Only use properties which do not require the logged in user.
        $cm = \cm_info::create($coursemodule);

        return new forum_entity(
            $context,
            $coursemodule,
            $course,
            // This property is a general module property that isn't affected by the logged in user.
            $cm->effectivegroupmode,
            $record->id,
            $record->course,
            $record->type,
            $record->name,
            $record->intro,
            $record->introformat,
            $record->assessed,
            $record->assesstimestart,
            $record->assesstimefinish,
            $record->scale,
            $record->maxbytes,
            $record->maxattachments,
            $record->forcesubscribe,
            $record->trackingtype,
            $record->rsstype,
            $record->rssarticles,
            $record->timemodified,
            $record->warnafter,
            $record->blockafter,
            $record->blockperiod,
            $record->completiondiscussions,
            $record->completionreplies,
            $record->completionposts,
            $record->displaywordcount,
            $record->lockdiscussionafter,
            $record->duedate,
            $record->cutoffdate
        );
    }

    /**
     * Create a discussion entity from an stdClass (legacy dicussion object).
     *
     * @param stdClass $record Discussion record
     * @return discussion_entity
     */
    public function get_discussion_from_stdclass(stdClass $record) : discussion_entity {
        return new discussion_entity(
            $record->id,
            $record->course,
            $record->forum,
            $record->name,
            $record->firstpost,
            $record->userid,
            $record->groupid,
            $record->assessed,
            $record->timemodified,
            $record->usermodified,
            $record->timestart,
            $record->timeend,
            $record->pinned,
            $record->timelocked
        );
    }

    /**
     * Create a post entity from an stdClass (legacy post object).
     *
     * @param stdClass $record The post record
     * @return post_entity
     */
    public function get_post_from_stdclass(stdClass $record) : post_entity {
        return new post_entity(
            $record->id,
            $record->discussion,
            $record->parent,
            $record->userid,
            $record->created,
            $record->modified,
            $record->mailed,
            $record->subject,
            $record->message,
            $record->messageformat,
            $record->messagetrust,
            $record->attachment,
            $record->totalscore,
            $record->mailnow,
            $record->deleted,
            $record->privatereplyto,
            $record->wordcount,
            $record->charcount
        );
    }

    /**
     * Create an author entity from a user record.
     *
     * @param stdClass $record The user record
     * @return author_entity
     */
    public function get_author_from_stdclass(stdClass $record) : author_entity {
        return new author_entity(
            $record->id,
            $record->picture,
            $record->firstname,
            $record->lastname,
            fullname($record),
            $record->email,
            $record->deleted,
            $record->middlename,
            $record->firstnamephonetic,
            $record->lastnamephonetic,
            $record->alternatename,
            $record->imagealt
        );
    }

    /**
     * Create a discussion summary enttiy from stdClasses.
     *
     * @param stdClass $discussion The discussion record
     * @param stdClass $firstpost A post record for the first post in the discussion
     * @param stdClass $firstpostauthor A user record for the author of the first post
     * @param stdClass $latestpostauthor A user record for the author of the latest post in the discussion
     * @return discussion_summary_entity
     */
    public function get_discussion_summary_from_stdclass(
        stdClass $discussion,
        stdClass $firstpost,
        stdClass $firstpostauthor,
        stdClass $latestpostauthor
    ) : discussion_summary_entity {

        $firstpostauthorentity = $this->get_author_from_stdclass($firstpostauthor);
        return new discussion_summary_entity(
            $this->get_discussion_from_stdclass($discussion),
            $this->get_post_from_stdclass($firstpost, $firstpostauthorentity),
            $firstpostauthorentity,
            $this->get_author_from_stdclass($latestpostauthor)
        );
    }

    /**
     * Create a post read receipt collection entity from a list of read receipt records.
     *
     * @param array $records A list of read receipt records.
     * @return post_read_receipt_collection_entity
     */
    public function get_post_read_receipt_collection_from_stdclasses(array $records) : post_read_receipt_collection_entity {
        return new post_read_receipt_collection_entity($records);
    }

    /**
     * Create a sorter entity to sort post entities.
     *
     * @return sorter_entity
     */
    public function get_posts_sorter() : sorter_entity {
        return new sorter_entity(
            // Get id function for a post_entity.
            function(post_entity $post) {
                return $post->get_id();
            },
            // Get parent id function for a post_entity.
            function(post_entity $post) {
                return $post->get_parent_id();
            }
        );
    }

    /**
     * Create a sorter entity to sort exported posts.
     *
     * @return sorter_entity
     */
    public function get_exported_posts_sorter() : sorter_entity {
        return new sorter_entity(
            // Get id function for an exported post.
            function(stdClass $post) {
                return $post->id;
            },
            // Get parent id function for an exported post.
            function(stdClass $post) {
                return $post->parentid;
            }
        );
    }
}
