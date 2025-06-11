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
 * The comments block helper functions and callbacks
 *
 * @package   block_comments
 * @copyright 2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Validate comment parameter before perform other comments actions
 *
 * @package  block_comments
 * @category comment
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return boolean
 */
function block_comments_comment_validate($comment_param) {
    if ($comment_param->commentarea != 'page_comments') {
        throw new comment_exception('invalidcommentarea');
    }
    if ($comment_param->itemid != 0) {
        throw new comment_exception('invalidcommentitemid');
    }
    return true;
}

/**
 * Running addtional permission check on plugins
 *
 * @package  block_comments
 * @category comment
 *
 * @param stdClass $args
 * @return array
 */
function block_comments_comment_permissions($args) {
    global $DB, $USER;
    // By default, anyone can post and view comments.
    $canpost = $canview = true;
    // Check if it's the user context and not the owner's profile.
    if ($args->context->contextlevel == CONTEXT_USER && $USER->id != $args->context->instanceid) {
        // Check whether the context owner has a comment block in the user's profile.
        $sqlparam = [
            'blockname' => 'comments',
            'parentcontextid' => $args->context->id,
            'pagetypepattern' => 'user-profile',
        ];
        // If the comment block is not present at the target user's profile,
        // then the logged-in user cannot post or view comments.
        $canpost = $canview = $DB->record_exists_select(
            'block_instances',
            'blockname = :blockname AND parentcontextid = :parentcontextid AND pagetypepattern = :pagetypepattern',
            $sqlparam,
        );
    }
    return ['post' => $canpost, 'view' => $canview];
}

/**
 * Validate comment data before displaying comments
 *
 * @package  block_comments
 * @category comment
 *
 * @param stdClass $comment
 * @param stdClass $args
 * @return boolean
 */
function block_comments_comment_display($comments, $args) {
    if ($args->commentarea != 'page_comments') {
        throw new comment_exception('invalidcommentarea');
    }
    if ($args->itemid != 0) {
        throw new comment_exception('invalidcommentitemid');
    }
    return $comments;
}
