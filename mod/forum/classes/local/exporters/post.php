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
 * Post exporter class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\exporters;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\post as post_entity;
use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\exporters\author as author_exporter;
use mod_forum\local\factories\exporter as exporter_factory;
use core\external\exporter;
use core_files\external\stored_file_exporter;
use context;
use core_tag_tag;
use renderer_base;
use stdClass;

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Post exporter class.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post extends exporter {
    /** @var post_entity $post The post to export */
    private $post;

    /**
     * Constructor.
     *
     * @param post_entity $post The post to export
     * @param array $related List of related data
     */
    public function __construct(post_entity $post, array $related = []) {
        $this->post = $post;
        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        $attachmentdefinition = stored_file_exporter::read_properties_definition();
        $attachmentdefinition['urls'] = [
            'type' => [
                'export' => [
                    'type' => PARAM_URL,
                    'description' => 'The URL used to export the attachment',
                    'optional' => true,
                    'default' => null,
                    'null' => NULL_ALLOWED
                ]
            ]
        ];
        $attachmentdefinition['html'] = [
            'type' => [
                'plagiarism' => [
                    'type' => PARAM_RAW,
                    'description' => 'The HTML source for the Plagiarism Response',
                    'optional' => true,
                    'default' => null,
                    'null' => NULL_ALLOWED
                ],
            ]
        ];

        return [
            'id' => ['type' => PARAM_INT],
            'subject' => ['type' => PARAM_TEXT],
            'replysubject' => ['type' => PARAM_TEXT],
            'message' => ['type' => PARAM_RAW],
            'messageformat' => ['type' => PARAM_INT],
            'author' => ['type' => author_exporter::read_properties_definition()],
            'discussionid' => ['type' => PARAM_INT],
            'hasparent' => ['type' => PARAM_BOOL],
            'parentid' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'unread' => [
                'type' => PARAM_BOOL,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'isdeleted' => ['type' => PARAM_BOOL],
            'isprivatereply' => ['type' => PARAM_BOOL],
            'haswordcount' => ['type' => PARAM_BOOL],
            'wordcount' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'charcount' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'capabilities' => [
                'type' => [
                    'view' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can view the post',
                    ],
                    'edit' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can edit the post',
                    ],
                    'delete' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can delete the post',
                    ],
                    'split' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can split the post',
                    ],
                    'reply' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can reply to the post',
                    ],
                    'selfenrol' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can self enrol into the course',
                    ],
                    'export' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can export the post',
                    ],
                    'controlreadstatus' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can control the read status of the post',
                    ],
                    'canreplyprivately' => [
                        'type' => PARAM_BOOL,
                        'null' => NULL_ALLOWED,
                        'description' => 'Whether the user can post a private reply',
                    ]
                ]
            ],
            'urls' => [
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED,
                'type' => [
                    'view' => [
                        'description' => 'The URL used to view the post',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'viewisolated' => [
                        'description' => 'The URL used to view the post in isolation',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'viewparent' => [
                        'description' => 'The URL used to view the parent of the post',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'edit' => [
                        'description' => 'The URL used to edit the post',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'delete' => [
                        'description' => 'The URL used to delete the post',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'split' => [
                        'description' => 'The URL used to split the discussion ' .
                            'with the selected post being the first post in the new discussion',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'reply' => [
                        'description' => 'The URL used to reply to the post',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'export' => [
                        'description' => 'The URL used to export the post',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'markasread' => [
                        'description' => 'The URL used to mark the post as read',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'markasunread' => [
                        'description' => 'The URL used to mark the post as unread',
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ],
                    'discuss' => [
                        'type' => PARAM_URL,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ]
                ]
            ],
            'attachments' => [
                'multiple' => true,
                'type' => $attachmentdefinition
            ],
            'messageinlinefiles' => [
                'optional' => true,
                'multiple' => true,
                'type' => stored_file_exporter::read_properties_definition(),
            ],
            'tags' => [
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED,
                'multiple' => true,
                'type' => [
                    'id' => [
                        'type' => PARAM_INT,
                        'description' => 'The ID of the Tag',
                        'null' => NULL_NOT_ALLOWED,
                    ],
                    'tagid' => [
                        'type' => PARAM_INT,
                        'description' => 'The tagid',
                        'null' => NULL_NOT_ALLOWED,
                    ],
                    'isstandard' => [
                        'type' => PARAM_BOOL,
                        'description' => 'Whether this is a standard tag',
                        'null' => NULL_NOT_ALLOWED,
                    ],
                    'displayname' => [
                        'type' => PARAM_TEXT,
                        'description' => 'The display name of the tag',
                        'null' => NULL_NOT_ALLOWED,
                    ],
                    'flag' => [
                        'type' => PARAM_BOOL,
                        'description' => 'Wehther this tag is flagged',
                        'null' => NULL_NOT_ALLOWED,
                    ],
                    'urls' => [
                        'description' => 'URLs associated with the tag',
                        'null' => NULL_NOT_ALLOWED,
                        'type' => [
                            'view' => [
                                'type' => PARAM_URL,
                                'description' => 'The URL to view the tag',
                                'null' => NULL_NOT_ALLOWED,
                            ],
                        ]
                    ]
                ]
            ],
            'html' => [
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED,
                'type' => [
                    'rating' => [
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED,
                        'type' => PARAM_RAW,
                        'description' => 'The HTML source to rate the post',
                    ],
                    'taglist' => [
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED,
                        'type' => PARAM_RAW,
                        'description' => 'The HTML source to view the list of tags',
                    ],
                    'authorsubheading' => [
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED,
                        'type' => PARAM_RAW,
                        'description' => 'The HTML source to view the author details',
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
        $post = $this->post;
        $authorgroups = $this->related['authorgroups'];
        $forum = $this->related['forum'];
        $discussion = $this->related['discussion'];
        $author = $this->related['author'];
        $authorcontextid = $this->related['authorcontextid'];
        $user = $this->related['user'];
        $readreceiptcollection = $this->related['readreceiptcollection'];
        $rating = $this->related['rating'];
        $tags = $this->related['tags'];
        $attachments = $this->related['attachments'];
        $inlineattachments = $this->related['messageinlinefiles'];
        $includehtml = $this->related['includehtml'];
        $isdeleted = $post->is_deleted();
        $isprivatereply = $post->is_private_reply();
        $hasrating = $rating != null;
        $hastags = !empty($tags);
        $discussionid = $post->get_discussion_id();
        $parentid = $post->get_parent_id();

        $capabilitymanager = $this->related['capabilitymanager'];
        $canview = $capabilitymanager->can_view_post($user, $discussion, $post);
        $canedit = $capabilitymanager->can_edit_post($user, $discussion, $post);
        $candelete = $capabilitymanager->can_delete_post($user, $discussion, $post);
        $cansplit = $capabilitymanager->can_split_post($user, $discussion, $post);
        $canreply = $capabilitymanager->can_reply_to_post($user, $discussion, $post);
        $canexport = $capabilitymanager->can_export_post($user, $post);
        $cancontrolreadstatus = $capabilitymanager->can_manually_control_post_read_status($user);
        $canselfenrol = $capabilitymanager->can_self_enrol($user);
        $canreplyprivately = $capabilitymanager->can_reply_privately_to_post($user, $post);

        $urlfactory = $this->related['urlfactory'];
        $viewurl = $canview ? $urlfactory->get_view_post_url_from_post($post) : null;
        $viewisolatedurl = $canview ? $urlfactory->get_view_isolated_post_url_from_post($post) : null;
        $viewparenturl = $post->has_parent() ? $urlfactory->get_view_post_url_from_post_id($discussionid, $parentid) : null;
        $editurl = $canedit ? $urlfactory->get_edit_post_url_from_post($forum, $post) : null;
        $deleteurl = $candelete ? $urlfactory->get_delete_post_url_from_post($post) : null;
        $spliturl = $cansplit ? $urlfactory->get_split_discussion_at_post_url_from_post($post) : null;
        $replyurl = $canreply || $canselfenrol ? $urlfactory->get_reply_to_post_url_from_post($post) : null;
        $exporturl = $canexport ? $urlfactory->get_export_post_url_from_post($post) : null;
        $markasreadurl = $cancontrolreadstatus ? $urlfactory->get_mark_post_as_read_url_from_post($post) : null;
        $markasunreadurl = $cancontrolreadstatus ? $urlfactory->get_mark_post_as_unread_url_from_post($post) : null;
        $discussurl = $canview ? $urlfactory->get_discussion_view_url_from_post($post) : null;

        $authorexporter = new author_exporter(
            $author,
            $authorcontextid,
            $authorgroups,
            $canview,
            $this->related
        );
        $exportedauthor = $authorexporter->export($output);
        // Only bother loading the content if the user can see it.
        $loadcontent = $canview && !$isdeleted;
        $exportattachments = $loadcontent && !empty($attachments);
        $exportinlineattachments = $loadcontent && !empty($inlineattachments);

        if ($loadcontent) {
            $subject = $post->get_subject();
            $timecreated = $this->get_start_time($discussion, $post);
            $message = $this->get_message($post);
        } else {
            $subject = $isdeleted ? get_string('forumsubjectdeleted', 'forum') : get_string('forumsubjecthidden', 'forum');
            $message = $isdeleted ? get_string('forumbodydeleted', 'forum') : get_string('forumbodyhidden', 'forum');
            $timecreated = null;
        }

        $replysubject = $subject;
        $strre = get_string('re', 'forum');
        if (!(substr($replysubject, 0, strlen($strre)) == $strre)) {
            $replysubject = "{$strre} {$replysubject}";
        }

        $showwordcount = $forum->should_display_word_count();
        if ($showwordcount) {
            $wordcount = $post->get_wordcount() ?? count_words($message);
            $charcount = $post->get_charcount() ?? count_letters($message);
        } else {
            $wordcount = null;
            $charcount = null;
        }

        return [
            'id' => $post->get_id(),
            'subject' => $subject,
            'replysubject' => $replysubject,
            'message' => $message,
            'messageformat' => $post->get_message_format(),
            'author' => $exportedauthor,
            'discussionid' => $post->get_discussion_id(),
            'hasparent' => $post->has_parent(),
            'parentid' => $post->has_parent() ? $post->get_parent_id() : null,
            'timecreated' => $timecreated,
            'timemodified' => $post->get_time_modified(),
            'unread' => ($loadcontent && $readreceiptcollection) ? !$readreceiptcollection->has_user_read_post($user, $post) : null,
            'isdeleted' => $isdeleted,
            'isprivatereply' => $isprivatereply,
            'haswordcount' => $showwordcount,
            'wordcount' => $wordcount,
            'charcount' => $charcount,
            'capabilities' => [
                'view' => $canview,
                'edit' => $canedit,
                'delete' => $candelete,
                'split' => $cansplit,
                'reply' => $canreply,
                'export' => $canexport,
                'controlreadstatus' => $cancontrolreadstatus,
                'canreplyprivately' => $canreplyprivately,
                'selfenrol' => $canselfenrol
            ],
            'urls' => [
                'view' => $viewurl ? $viewurl->out(false) : null,
                'viewisolated' => $viewisolatedurl ? $viewisolatedurl->out(false) : null,
                'viewparent' => $viewparenturl ? $viewparenturl->out(false) : null,
                'edit' => $editurl ? $editurl->out(false) : null,
                'delete' => $deleteurl ? $deleteurl->out(false) : null,
                'split' => $spliturl ? $spliturl->out(false) : null,
                'reply' => $replyurl ? $replyurl->out(false) : null,
                'export' => $exporturl && $exporturl ? $exporturl->out(false) : null,
                'markasread' => $markasreadurl ? $markasreadurl->out(false) : null,
                'markasunread' => $markasunreadurl ? $markasunreadurl->out(false) : null,
                'discuss' => $discussurl ? $discussurl->out(false) : null,
            ],
            'attachments' => ($exportattachments) ? $this->export_attachments($attachments, $post, $output, $canexport) : [],
            'messageinlinefiles' => ($exportinlineattachments) ? $this->export_inline_attachments($inlineattachments,
                $post, $output) : [],
            'tags' => ($loadcontent && $hastags) ? $this->export_tags($tags) : [],
            'html' => $includehtml ? [
                'rating' => ($loadcontent && $hasrating) ? $output->render($rating) : null,
                'taglist' => ($loadcontent && $hastags) ? $output->tag_list($tags) : null,
                'authorsubheading' => ($loadcontent) ? $this->get_author_subheading_html($exportedauthor, $timecreated) : null
            ] : null
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
            'readreceiptcollection' => 'mod_forum\local\entities\post_read_receipt_collection?',
            'urlfactory' => 'mod_forum\local\factories\url',
            'forum' => 'mod_forum\local\entities\forum',
            'discussion' => 'mod_forum\local\entities\discussion',
            'author' => 'mod_forum\local\entities\author',
            'authorcontextid' => 'int?',
            'user' => 'stdClass',
            'context' => 'context',
            'authorgroups' => 'stdClass[]',
            'attachments' => '\stored_file[]?',
            'messageinlinefiles' => '\stored_file[]?',
            'tags' => '\core_tag_tag[]?',
            'rating' => 'rating?',
            'includehtml' => 'bool'
        ];
    }

    /**
     * This method returns the parameters for the post's message to
     * use with the function \core_external\util::format_text().
     *
     * @return array
     */
    protected function get_format_parameters_for_message() {
        return [
            'component' => 'mod_forum',
            'filearea' => 'post',
            'itemid' => $this->post->get_id(),
            'options' => [
                'para' => false,
                'trusted' => $this->post->is_message_trusted()
            ]
        ];
    }

    /**
     * Get the message text from a post.
     *
     * @param post_entity $post The post
     * @return string
     */
    private function get_message(post_entity $post): string {
        global $CFG;

        $message = $post->get_message();

        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            $forum = $this->related['forum'];
            $message .= plagiarism_get_links([
                'userid' => $post->get_author_id(),
                'content' => $message,
                'cmid' => $forum->get_course_module_record()->id,
                'course' => $forum->get_course_id(),
                'forum' => $forum->get_id()
            ]);
        }

        return $message;
    }

    /**
     * Get the exported attachments for a post.
     *
     * @param stored_file[] $attachments The list of attachments for the post
     * @param post_entity $post The post being exported
     * @param renderer_base $output Renderer base
     * @param bool $canexport If the user can export the post (relates to portfolios not exporters like this class)
     * @return array
     */
    private function export_attachments(array $attachments, post_entity $post, renderer_base $output, bool $canexport): array {
        global $CFG;

        $urlfactory = $this->related['urlfactory'];
        $enableplagiarism = $CFG->enableplagiarism;
        $forum = $this->related['forum'];
        $context = $this->related['context'];

        if ($enableplagiarism) {
            require_once($CFG->libdir . '/plagiarismlib.php' );
        }

        return array_map(function($attachment) use (
            $output,
            $enableplagiarism,
            $canexport,
            $context,
            $forum,
            $post,
            $urlfactory
        ) {
            $exporter = new stored_file_exporter($attachment, ['context' => $context]);
            $exportedattachment = $exporter->export($output);
            $exporturl = $canexport ? $urlfactory->get_export_attachment_url_from_post_and_attachment($post, $attachment) : null;

            if ($enableplagiarism) {
                $plagiarismhtml = plagiarism_get_links([
                    'userid' => $post->get_author_id(),
                    'file' => $attachment,
                    'cmid' => $forum->get_course_module_record()->id,
                    'course' => $forum->get_course_id(),
                    'forum' => $forum->get_id()
                ]);
            } else {
                $plagiarismhtml = null;
            }

            $exportedattachment->urls = [
                'export' => $exporturl ? $exporturl->out(false) : null
            ];
            $exportedattachment->html = [
                'plagiarism' => $plagiarismhtml
            ];

            return $exportedattachment;
        }, $attachments);
    }

    /**
     * Get the exported inline attachments for a post.
     *
     * @param array $inlineattachments The list of inline attachments for the post
     * @param post_entity $post The post being exported
     * @param renderer_base $output Renderer base
     * @return array
     */
    private function export_inline_attachments(array $inlineattachments, post_entity $post, renderer_base $output): array {

        return array_map(function($attachment) use (
            $output,
            $post
        ) {
            $exporter = new stored_file_exporter($attachment, ['context' => $this->related['context']]);
            return $exporter->export($output);;
        }, $inlineattachments);
    }

    /**
     * Export the list of tags.
     *
     * @param core_tag_tag[] $tags List of tags to export
     * @return array
     */
    private function export_tags(array $tags): array {
        $user = $this->related['user'];
        $context = $this->related['context'];
        $capabilitymanager = $this->related['capabilitymanager'];
        $canmanagetags = $capabilitymanager->can_manage_tags($user);

        return array_values(array_map(function($tag) use ($context, $canmanagetags) {
            $viewurl = core_tag_tag::make_url($tag->tagcollid, $tag->rawname, 0, $context->id);
            return [
                'id' => $tag->taginstanceid,
                'tagid' => $tag->id,
                'isstandard' => $tag->isstandard,
                'displayname' => $tag->get_display_name(),
                'flag' => $canmanagetags && !empty($tag->flag),
                'urls' => [
                    'view' => $viewurl->out(false)
                ]
            ];
        }, $tags));
    }

    /**
     * Get the HTML to display as a subheading in a post.
     *
     * @param stdClass $exportedauthor The exported author object
     * @param int $timecreated The post time created timestamp if it's to be displayed
     * @return string
     */
    private function get_author_subheading_html(stdClass $exportedauthor, int $timecreated): string {
        $fullname = $exportedauthor->fullname;
        $profileurl = $exportedauthor->urls['profile'] ?? null;
        $name = $profileurl ? "<a href=\"{$profileurl}\">{$fullname}</a>" : $fullname;
        $date = userdate_htmltime($timecreated, get_string('strftimedaydatetime', 'core_langconfig'));
        return get_string('bynameondate', 'mod_forum', ['name' => $name, 'date' => $date]);
    }

    /**
     * Get the start time for a post.
     *
     * @param discussion_entity $discussion entity
     * @param post_entity $post entity
     * @return int The start time (timestamp) for a post
     */
    private function get_start_time(discussion_entity $discussion, post_entity $post) {
        global $CFG;

        $posttime = $post->get_time_created();
        $discussiontime = $discussion->get_time_start();
        if (!empty($CFG->forum_enabletimedposts) && ($discussiontime > $posttime)) {
            return $discussiontime;
        }
        return $posttime;
    }
}
