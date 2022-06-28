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
 * Privacy Subsystem implementation for mod_moodleoverflow.
 *
 * @package    mod_moodleoverflow
 * @copyright  2018 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_moodleoverflow\privacy;
use core_privacy\local\request\transform;
use \core_privacy\local\request\writer;
use mod_moodleoverflow\ratings;

/**
 * Subcontext helper class.
 *
 * @copyright  2017 Tamara Gunkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_export_helper {
    /**
     * Store all information about all discussions that we have detected this user to have access to.
     *
     * @param   int   $userid   The userid of the user whose data is to be exported.
     * @param   array $mappings A list of mappings from forumid => contextid.
     *
     * @return  array       Which forums had data written for them.
     */
    public static function export_discussion_data($userid, array $mappings) {
        global $DB;
        // Find all of the discussions, and discussion subscriptions for this forum.
        list($foruminsql, $forumparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);

        $sql = "SELECT
                    d.*,
                    dsub.preference
                  FROM {moodleoverflow} mof
            INNER JOIN {moodleoverflow_discussions} d ON d.moodleoverflow = mof.id
            LEFT JOIN {moodleoverflow_discuss_subs} dsub ON dsub.discussion = d.id
                 WHERE mof.id ${foruminsql}
                   AND (
                        d.userid    = :discussionuserid OR
                        d.usermodified = :dmuserid OR
                        dsub.userid = :dsubuserid
                   )
        ";
        $params = [
            'discussionuserid' => $userid,
            'dmuserid'         => $userid,
            'dsubuserid'       => $userid,
        ];
        $params += $forumparams;

        // Keep track of the forums which have data.
        $forumswithdata = [];
        $discussions = $DB->get_recordset_sql($sql, $params);
        foreach ($discussions as $discussion) {
            $forumswithdata[$discussion->moodleoverflow] = true;
            $context = \context::instance_by_id($mappings[$discussion->moodleoverflow]);

            // Store related metadata for this discussion.
            static::export_discussion_subscription_data($context, $discussion);
            $discussiondata = (object) [
                'name'                  => format_string($discussion->name, true),
                'timemodified'          => transform::datetime($discussion->timemodified),
                'creator_was_you'       => transform::yesno($discussion->userid == $userid),
                'last_modifier_was_you' => transform::yesno($discussion->usermodified == $userid)
            ];
            // Store the discussion content.
            writer::with_context($context)->export_data(
                static::get_discussion_area($discussion), $discussiondata);
            // Forum discussions do not have any files associately directly with them.
        }
        $discussions->close();

        return $forumswithdata;
    }

    /**
     * Store all information about all posts that we have detected this user to have access to.
     *
     * @param   int   $userid   The userid of the user whose data is to be exported.
     * @param   array $mappings A list of mappings from forumid => contextid.
     *
     * @return  array       Which forums had data written for them.
     */
    public static function export_all_posts($userid, array $mappings) {
        global $DB;

        // Find all of the posts, and post subscriptions for this forum.
        list($foruminsql, $forumparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);

        $sql = "SELECT
                    p.discussion AS id,
                    mof.id AS forumid,
                    d.name
                  FROM {moodleoverflow} mof
            INNER JOIN {moodleoverflow_discussions} d ON d.moodleoverflow = mof.id
            INNER JOIN {moodleoverflow_posts} p ON p.discussion = d.id
            LEFT JOIN {moodleoverflow_read} fr ON fr.postid = p.id
            LEFT JOIN {moodleoverflow_ratings} rat ON  rat.postid = p.id
                 WHERE mof.id ${foruminsql} AND
                (
                    p.userid = :postuserid OR
                    fr.userid = :readuserid OR
                    rat.userid = :ratinguserid
                )
              GROUP BY mof.id, p.discussion, d.name
        ";
        $params = [
            'postuserid'   => $userid,
            'readuserid'   => $userid,
            'ratinguserid' => $userid,
        ];
        $params += $forumparams;

        $discussions = $DB->get_records_sql($sql, $params);
        foreach ($discussions as $discussion) {
            $context = \context::instance_by_id($mappings[$discussion->forumid]);
            static::export_all_posts_in_discussion($userid, $context, $discussion);
        }
    }

    /**
     * Store all information about all posts that we have detected this user to have access to.
     * @param int       $userid     The userid of the user whose data is to be exported.
     * @param \context  $context    instance of the moodleoverflow context.
     * @param \stdClass $discussion The discussion whose data is being exported.
     */
    protected static function export_all_posts_in_discussion($userid, \context $context, \stdClass $discussion) {
        global $DB, $USER;
        $discussionid = $discussion->id;
        // Find all of the posts, and post subscriptions for this forum.
        $sql = "SELECT
                    p.*,
                    d.moodleoverflow AS forumid,
                    fr.firstread,
                    fr.lastread,
                    fr.id AS readflag,
                    rat.userid AS hasratings
                    FROM {moodleoverflow_discussions} d
              INNER JOIN {moodleoverflow_posts} p ON p.discussion = d.id
              LEFT JOIN {moodleoverflow_read} fr ON fr.postid = p.id AND fr.userid = :readuserid
              LEFT JOIN {moodleoverflow_ratings} rat ON rat.id = (
                SELECT MAX(id) FROM {moodleoverflow_ratings} ra
                WHERE ra.postid = p.id OR ra.userid = :ratinguserid
              )
                    WHERE d.id = :discussionid
        ";
        $params = [
            'discussionid' => $discussionid,
            'readuserid'   => $userid,
            'ratinguserid' => $userid
        ];

        // Keep track of the forums which have data.
        $structure = (object) [
            'children' => [],
        ];
        $posts = $DB->get_records_sql($sql, $params);
        foreach ($posts as $post) {
            $post->hasdata = (isset($post->hasdata)) ? $post->hasdata : false;
            $post->hasdata = $post->hasdata || !empty($post->hasratings);
            $post->hasdata = $post->hasdata || $post->readflag;
            $post->hasdata = $post->hasdata || ($post->userid == $USER->id);

            if (0 == $post->parent) {
                $structure->children[$post->id] = $post;
            } else {
                if (empty($posts[$post->parent]->children)) {
                    $posts[$post->parent]->children = [];
                }
                $posts[$post->parent]->children[$post->id] = $post;
            }
            // Set all parents.
            if ($post->hasdata) {
                $curpost = $post;
                while ($curpost->parent != 0) {
                    $curpost = $posts[$curpost->parent];
                    $curpost->hasdata = true;
                }
            }
        }
        $discussionarea = static::get_discussion_area($discussion);
        $discussionarea[] = get_string('posts', 'mod_moodleoverflow');
        static::export_posts_in_structure($userid, $context, $discussionarea, $structure);
    }

    /**
     * Export all posts in the provided structure.
     * @param int       $userid     The userid of the user whose data is to be exported.
     * @param \context  $context    instance of the moodleoverflow context.
     * @param array     $parentarea The subcontext fo the parent post.
     * @param \stdClass $structure  The post structure and all of its children
     */
    protected static function export_posts_in_structure($userid, \context $context, $parentarea, \stdClass $structure) {
        foreach ($structure->children as $post) {
            if (!$post->hasdata) {
                // This tree has no content belonging to the user. Skip it and all children.
                continue;
            }
            $postarea = array_merge($parentarea, static::get_post_area($post));
            // Store the post content.
            static::export_post_data($userid, $context, $postarea, $post);
            if (isset($post->children)) {
                // Now export children of this post.
                static::export_posts_in_structure($userid, $context, $postarea, $post);
            }
        }
    }

    /**
     * Export all data in the post.
     *
     * @param int        $userid     The userid of the user whose data is to be exported.
     * @param \context   $context    instance of the moodleoverflow context.
     * @param array      $postarea   The subcontext fo the parent post.
     * @param \stdClass  $post       The post structure and all of its children
     */
    protected static function export_post_data($userid, \context $context, $postarea, $post) {
        // Store related metadata.
        static::export_read_data($context, $postarea, $post);
        $postdata = (object) [
            'created'        => transform::datetime($post->created),
            'modified'       => transform::datetime($post->modified),
            'author_was_you' => transform::yesno($post->userid == $userid)
        ];
        $postdata->message = writer::with_context($context)->rewrite_pluginfile_urls(
            $postarea, 'mod_moodleoverflow', 'attachment', $post->id, $post->message);

        $postdata->message = format_text($postdata->message, $post->messageformat, (object) [
            'para'    => false,
            'context' => $context,
        ]);

        // Store the post and the associated files.
        writer::with_context($context)->export_data($postarea, $postdata)->export_area_files(
            $postarea, 'mod_moodleoverflow', 'attachment', $post->id);

        if ($post->userid == $userid) {
            // Store all ratings against this post as the post belongs to the user. All ratings on it are ratings of their content.
            $toexport = self::export_rating_data($post->id, false, $userid);
            writer::with_context($context)->export_related_data($postarea, 'rating', $toexport);
        } else {
            // Check for any ratings that the user has made on this post.
            $toexport = self::export_rating_data($post->id, true, $userid);
            writer::with_context($context)->export_related_data($postarea, 'rating', $toexport);
        }
    }

    /**
     * Export all ratings that belong to a post.
     * Export the rating of a post from a specific user if "onlyuser" is true.
     * @param int $postid The postid of the post which rating is requested.
     * @param boolean $onlyuser True, if only the rating of a specific user should be exported.
     * @param int $userid The userid of the user whose data is exported.
     *
     * @return object|\stdClass The requested rating data.
     */
    protected static function export_rating_data($postid, $onlyuser, $userid) {
        global $DB;
        $rating = new ratings();

        $ratingpost = $rating->moodleoverflow_get_rating($postid);

        // Get the user rating.
        $sql = "SELECT id, firstrated, rating
                  FROM {moodleoverflow_ratings}
                 WHERE userid = :userid AND postid = :postid";
        $ownratings = $DB->get_records_sql($sql, [
            'userid' => $userid,
            'postid' => $postid,
        ]);
        $userratings = array();
        foreach ($ownratings as $rating) {
            $userratings[] = (object) [
                'firstrated' => $rating->firstrated,
                'rating'     => $rating->rating
            ];
        }

        if (!$onlyuser) {
            $ratingdata = [
                'downvotes'            => $ratingpost->downvotes,
                'upvotes'              => $ratingpost->upvotes,
                'was_rated_as_helpful' => transform::yesno($ratingpost->ishelpful),
                'was_rated_as_solved'  => transform::yesno($ratingpost->issolved)
            ];
        }
        $ratingdata['your_rating'] = (object) $userratings;

        if (empty($ratingdata)) {
            // Returns an empty stdClass.
            return new \stdClass();
        }

        return (object) $ratingdata;
    }

    /**
     * Store data about whether the user subscribes to forum.
     * @param \stdClass $forum  The forum whose data is being exported.
     *
     * @return bool     Whether any data was stored.
     */
    public static function export_subscription_data(\stdClass $forum) {
        if (null !== $forum->subscribed) {
            // The user is subscribed to this forum.
            writer::with_context(\context_module::instance($forum->cmid))->export_metadata(
                [], 'subscriptionpreference', 1, get_string('privacy:subscribedtoforum', 'mod_moodleoverflow'));

            return true;
        }

        return false;
    }

    /**
     * Store data about whether the user subscribes to this particular discussion.
     *
     * @param \context_module $context      instance of the moodleoverflow context.
     * @param \stdClass       $discussion   The discussion whose data is being exported.
     *
     * @return bool         Whether any data was stored.
     */
    protected static function export_discussion_subscription_data(\context_module $context, \stdClass $discussion) {
        $area = static::get_discussion_area($discussion);
        if (null !== $discussion->preference) {
            // The user hass a specific subscription preference for this discussion.
            $a = (object) [];
            switch ($discussion->preference) {
                case \mod_moodleoverflow\subscriptions::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED:
                    $a->preference = get_string('unsubscribed', 'mod_moodleoverflow');
                    break;
                default:
                    $a->preference = get_string('subscribed', 'mod_moodleoverflow');
                    break;
            }
            writer::with_context($context)->export_metadata(
                    $area,
                    'subscriptionpreference',
                    $discussion->preference,
                    get_string('privacy:discussionsubscriptionpreference', 'mod_moodleoverflow', $a)
                );

            return true;
        }

        return true;
    }

    /**
     * Store moodleoverflow read-tracking data about a particular moodleoverflow.
     * This is whether a moodleoverflow has read-tracking enabled or not.
     *
     * @param \stdClass $forum  The moodleoverflow whose data is being exported.
     *
     * @return bool      Whether any data was stored.
     */
    public static function export_tracking_data(\stdClass $forum) {
        if (null !== $forum->tracked) {
            // The user has a main preference to track all forums, but has opted out of this one.
            writer::with_context(\context_module::instance($forum->cmid))->export_metadata(
                [], 'trackreadpreference', 0, get_string('privacy:readtrackingdisabled', 'mod_moodleoverflow'));

            return true;
        }

        return false;
    }

    /**
     * Exports grade Data
     * @param \stdClass $forum The
     * @return bool Whether any data was stored.
     * @throws \coding_exception
     */
    public static function export_grade_data(\stdClass $forum) {
        if ($forum->grade) {
            writer::with_context(\context_module::instance($forum->cmid))->export_metadata(
                    [], 'grade', $forum->grade, get_string('privacy:grade', 'mod_moodleoverflow'));

            return true;
        }
        return false;
    }

    /**
     * Store read-tracking information about a particular forum post.
     *
     * @param \context_module $context  The instance of the forum context.
     * @param array           $postarea The subcontext for this post.
     * @param \stdClass       $post     The post whose data is being exported.
     *
     * @return bool     Whether any data was stored.
     */
    protected static function export_read_data(\context_module $context, array $postarea, \stdClass $post) {
        if (null !== $post->firstread) {
            $a = (object) [
                'firstread' => $post->firstread,
                'lastread'  => $post->lastread,
            ];
            writer::with_context($context)->export_metadata(
                    $postarea,
                    'postread',
                    (object) [
                        'firstread' => $post->firstread,
                        'lastread'  => $post->lastread,
                    ],
                    get_string('privacy:postwasread', 'mod_moodleoverflow', $a)
                );

            return true;
        }

        return false;
    }

    /**
     * Get the discussion part of the subcontext.
     *
     * @param   \stdClass   $discussion
     * @return  array
     */
    protected static function get_discussion_area(\stdClass $discussion) {
        $pathparts = [];
        $parts = [
            $discussion->id,
            $discussion->name,
        ];
        $discussionname = implode('-', $parts);
        $pathparts[] = get_string('discussions', 'mod_moodleoverflow');
        $pathparts[] = $discussionname;
        return $pathparts;
    }

    /**
     * Get the post part of the subcontext.
     *
     * @param   \stdClass   $post
     * @return  array
     */
    protected static function get_post_area(\stdClass $post) {
        $parts = [
            $post->created,
            $post->id,
        ];
        $area[] = implode('-', $parts);
        return $area;
    }

    /**
     * Get the parent subcontext for the supplied moodleoverflow, discussion, and post combination.
     *
     * @param   \stdClass   $post The post.
     * @return  array
     */
    protected static function get_post_area_for_parent(\stdClass $post) {
        global $DB;
        $subcontext = [];
        if ($parent = $DB->get_record('moodleoverflow_posts', ['id' => $post->parent], 'id, created')) {
            $subcontext = array_merge($subcontext, static::get_post_area($parent));
        }
        $subcontext = array_merge($subcontext, static::get_post_area($post));
        return $subcontext;
    }

    /**
     * Get the subcontext for the supplied moodleoverflow, discussion, and post combination.
     *
     * @param   \stdClass   $discussion The discussion
     * @param   \stdClass   $post The post.
     * @return  array
     */
    public static function get_subcontext($discussion = null, $post = null) {
        $subcontext = [];
        if (null !== $discussion) {
            $subcontext += self::get_discussion_area($discussion);
            if (null !== $post) {
                $subcontext[] = get_string('posts', 'mod_moodleoverflow');
                $subcontext = array_merge($subcontext, static::get_post_area_for_parent($post));
            }
        }
        return $subcontext;
    }
}
