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
 * Post class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\entities;

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * Post class.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post {
    /** @var int $id ID */
    private $id;
    /** @var int $discussionid The id of the discussion this post belongs to */
    private $discussionid;
    /** @var int $parentid The id of the post that this post is replying to. Zero if it isn't a reply. */
    private $parentid;
    /** @var int $authorid The id of user who authored the post */
    private $authorid;
    /** @var int $timecreated Timestamp for when the post was created */
    private $timecreated;
    /** @var int $timemodified Timestamp for when the post last modified */
    private $timemodified;
    /** @var bool $mailed If the post has been mailed */
    private $mailed;
    /** @var string $subject Post subject */
    private $subject;
    /** @var string $message Post message */
    private $message;
    /** @var int $messageformat Format of the post message */
    private $messageformat;
    /** @var bool $messagetrust Is this a trusted message, i.e. created by a trusted user. */
    private $messagetrust;
    /** @var bool $hasattachments Does the post have attachments */
    private $hasattachments;
    /** @var int $totalscore Total score */
    private $totalscore;
    /** @var bool $mailnow Should this post be mailed immediately */
    private $mailnow;
    /** @var bool $deleted Is the post deleted */
    private $deleted;
    /** @var int $privatereplyto The user being privately replied to */
    private $privatereplyto;
    /** @var int $wordcount Number of words in the message */
    private $wordcount;
    /** @var int $charcount Number of chars in the message */
    private $charcount;

    /**
     * Constructor.
     *
     * @param int $id ID
     * @param int $discussionid The id of the discussion this post belongs to
     * @param int $parentid The id of the post that this post is replying to. Zero if it isn't a reply.
     * @param int $authorid The id of user who authored the post
     * @param int $timecreated Timestamp for when the post was created
     * @param int $timemodified Timestamp for when the post last modified
     * @param bool $mailed If the post has been mailed
     * @param string $subject Post subject
     * @param string $message Post message
     * @param int $messageformat Format of the post message
     * @param bool $messagetrust Is this a trusted message, i.e. created by a trusted user.
     * @param bool $hasattachments Does the post have attachments
     * @param int $totalscore Total score
     * @param bool $mailnow Should this post be mailed immediately
     * @param bool $deleted Is the post deleted
     * @param int $privatereplyto Which user this reply is intended for in a private reply situation
     */
    public function __construct(
        int $id,
        int $discussionid,
        int $parentid,
        int $authorid,
        int $timecreated,
        int $timemodified,
        bool $mailed,
        string $subject,
        string $message,
        int $messageformat,
        bool $messagetrust,
        bool $hasattachments,
        int $totalscore,
        bool $mailnow,
        bool $deleted,
        int $privatereplyto,
        ?int $wordcount,
        ?int $charcount
    ) {
        $this->id = $id;
        $this->discussionid = $discussionid;
        $this->parentid = $parentid;
        $this->authorid = $authorid;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
        $this->mailed = $mailed;
        $this->subject = $subject;
        $this->message = $message;
        $this->messageformat = $messageformat;
        $this->messagetrust = $messagetrust;
        $this->hasattachments = $hasattachments;
        $this->totalscore = $totalscore;
        $this->mailnow = $mailnow;
        $this->deleted = $deleted;
        $this->privatereplyto = $privatereplyto;
        $this->wordcount = $wordcount;
        $this->charcount = $charcount;
    }

    /**
     * Get the post id.
     *
     * @return int
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Get the discussion id.
     *
     * @return int
     */
    public function get_discussion_id(): int {
        return $this->discussionid;
    }

    /**
     * Get the id of the parent post. Returns zero if this post is not a reply.
     *
     * @return int
     */
    public function get_parent_id(): int {
        return $this->parentid;
    }

    /**
     * Does this post have a parent? I.e. is it a reply?
     *
     * @return bool
     */
    public function has_parent(): bool {
        return $this->get_parent_id() > 0;
    }

    /**
     * Get the id of the user that authored the post.
     *
     * @return int
     */
    public function get_author_id(): int {
        return $this->authorid;
    }

    /**
     * Get the timestamp for when this post was created.
     *
     * @return int
     */
    public function get_time_created(): int {
        return $this->timecreated;
    }

    /**
     * Get the timestamp for when this post was last modified.
     *
     * @return int
     */
    public function get_time_modified(): int {
        return $this->timemodified;
    }

    /**
     * Has this post been mailed?
     *
     * @return bool
     */
    public function has_been_mailed(): bool {
        return $this->mailed;
    }

    /**
     * Get the post subject.
     *
     * @return string
     */
    public function get_subject(): string {
        return $this->subject;
    }

    /**
     * Get the post message.
     *
     * @return string
     */
    public function get_message(): string {
        return $this->message;
    }

    /**
     * Get the post message format.
     *
     * @return int
     */
    public function get_message_format(): int {
        return $this->messageformat;
    }

    /**
     * Is this a trusted message? I.e. was it authored by a trusted user?
     *
     * @return bool
     */
    public function is_message_trusted(): bool {
        return $this->messagetrust;
    }

    /**
     * Does this post have attachments?
     *
     * @return bool
     */
    public function has_attachments(): bool {
        return $this->hasattachments;
    }

    /**
     * Get the total score.
     *
     * @return int
     */
    public function get_total_score(): int {
        return $this->totalscore;
    }

    /**
     * Should this post be mailed now?
     *
     * @return bool
     */
    public function should_mail_now(): bool {
        return $this->mailnow;
    }

    /**
     * Is this post deleted?
     *
     * @return bool
     */
    public function is_deleted(): bool {
        return $this->deleted;
    }

    /**
     * Is this post private?
     *
     * @return bool
     */
    public function is_private_reply(): bool {
        return !empty($this->privatereplyto);
    }

    /**
     * Get the id of the user that this post was intended for.
     *
     * @return int
     */
    public function get_private_reply_recipient_id(): int {
        return $this->privatereplyto;
    }


    /**
     * Get the post's age in seconds.
     *
     * @return int
     */
    public function get_age(): int {
        return time() - $this->get_time_created();
    }

    /**
     * Check if the given user authored this post.
     *
     * @param stdClass $user The user to check.
     * @return bool
     */
    public function is_owned_by_user(stdClass $user): bool {
        return $this->get_author_id() == $user->id;
    }

    /**
     * Check if the given post is a private reply intended for the given user.
     *
     * @param stdClass $user The user to check.
     * @return bool
     */
    public function is_private_reply_intended_for_user(stdClass $user): bool {
        return $this->get_private_reply_recipient_id() == $user->id;
    }

    /**
     * Returns the word count.
     *
     * @return int|null
     */
    public function get_wordcount(): ?int {
        return $this->wordcount;
    }

    /**
     * Returns the char count.
     *
     * @return int|null
     */
    public function get_charcount(): ?int {
        return $this->charcount;
    }

    /**
     * This methods adds/updates forum posts' word count and char count attributes based on $data->message.
     *
     * @param \stdClass $record A record ready to be inserted / updated in DB.
     * @return void.
     */
    public static function add_message_counts(\stdClass $record): void {
        if (!empty($record->message)) {
            $record->wordcount = count_words($record->message, $record->messageformat);
            $record->charcount = count_letters($record->message, $record->messageformat);
        }
    }
}
