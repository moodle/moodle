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
 * External moodleoverflow API
 *
 * @package    mod_moodleoverflow
 * @copyright  2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Class implementing the external API, esp. for AJAX functions.
 *
 * @package    mod_moodleoverflow
 * @copyright  2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_moodleoverflow_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function record_vote_parameters() {
        return new external_function_parameters(
            array(
                'discussionid' => new external_value(PARAM_INT, 'id of discussion'),
                'postid'       => new external_value(PARAM_INT, 'id of post'),
                'ratingid'     => new external_value(PARAM_INT, 'rating'),
                'sesskey'      => new external_value(PARAM_TEXT, 'session key'),
            )
        );
    }

    /**
     * Returns the result of the vote (new rating and reputations).
     * @return external_multiple_structure
     */
    public static function record_vote_returns() {
        return new external_single_structure(
            array(
                'postrating'      => new external_value(PARAM_INT, 'new post rating'),
                'ownerreputation' => new external_value(PARAM_INT, 'new reputation of post owner'),
                'raterreputation' => new external_value(PARAM_INT, 'new reputation of rater'),
                'ownerid'         => new external_value(PARAM_INT, 'user id of post owner'),
            )
        );
    }

    /**
     * Records upvotes and downvotes.
     *
     * @param int $discussionid ID of discussion
     * @param int $postid ID of post
     * @param int $ratingid Rating value
     * @param int $sesskey Session key
     * @return array with updated information about rating /reputation
     */
    public static function record_vote($discussionid, $postid, $ratingid, $sesskey) {
        global $DB, $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::record_vote_parameters(), array(
            'discussionid' => $discussionid,
            'postid'       => $postid,
            'ratingid'     => $ratingid,
            'sesskey'      => $sesskey,
        ));

        $transaction = $DB->start_delegated_transaction();

        // Check if the discussion is valid.
        if (!$discussion = $DB->get_record('moodleoverflow_discussions', array('id' => $params['discussionid']))) {
            throw new moodle_exception('invaliddiscussionid', 'moodleoverflow');
        }

        // Check if the related moodleoverflow instance is valid.
        if (!$moodleoverflow = $DB->get_record('moodleoverflow', array('id' => $discussion->moodleoverflow))) {
            throw new moodle_exception('invalidmoodleoverflowid', 'moodleoverflow');
        }

        // Check if the related moodleoverflow instance is valid.
        if (!$course = $DB->get_record('course', array('id' => $discussion->course))) {
            throw new moodle_exception('invalidcourseid');
        }

        // Get the related coursemodule and its context.
        if (!$cm = get_coursemodule_from_instance('moodleoverflow', $moodleoverflow->id, $course->id)) {
            throw new moodle_exception('invalidcoursemodule');
        }

        // Security checks.
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/moodleoverflow:ratepost', $context);
        if (!confirm_sesskey($sesskey)) {
            throw new moodle_exception('invalidsesskey');
        }

        // Rate the post.
        if (!\mod_moodleoverflow\ratings::moodleoverflow_add_rating($moodleoverflow,
            $params['postid'], $params['ratingid'], $cm)) {
            throw new moodle_exception('ratingfailed', 'moodleoverflow');
        }

        $post = moodleoverflow_get_post_full($params['postid']);
        $postownerid = $post->userid;
        $rating      = \mod_moodleoverflow\ratings::moodleoverflow_get_ratings_by_discussion($discussion->id,
            $params['postid']);
        $ownerrating = \mod_moodleoverflow\ratings::moodleoverflow_get_reputation($moodleoverflow->id, $postownerid);
        $raterrating = \mod_moodleoverflow\ratings::moodleoverflow_get_reputation($moodleoverflow->id, $USER->id);

        $cannotseeowner = \mod_moodleoverflow\anonymous::is_post_anonymous($discussion, $moodleoverflow, $USER->id) &&
            $USER->id != $postownerid;

        $params['postrating']      = $rating->upvotes - $rating->downvotes;
        $params['ownerreputation'] = $cannotseeowner ? null : $ownerrating;
        $params['raterreputation'] = $raterrating;
        $params['ownerid']         = $cannotseeowner ? null : $postownerid;

        $transaction->allow_commit();

        moodleoverflow_update_user_grade($moodleoverflow, $ownerrating, $postownerid);
        moodleoverflow_update_user_grade($moodleoverflow, $raterrating, $USER->id);

        return $params;
    }
}
