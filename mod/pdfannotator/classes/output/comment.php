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

namespace mod_pdfannotator\output;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderable for comments.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comment implements \renderable, \templatable {

    private $comments = [];
    private $questionvisibility;

    /**
     * Constructor of renderable for comments.
     *
     * @param object $data Comment or array of comments
     * @param object $cm Course module
     * @param object $context Context
     * @return type
     */
    public function __construct($data, $cm, $context) {
        global $USER;

        if (!is_array($data)) {
            $data = [$data];
        }

        $report = has_capability('mod/pdfannotator:report', $context);
        $closequestion = has_capability('mod/pdfannotator:closequestion', $context);
        $closeanyquestion = has_capability('mod/pdfannotator:closeanyquestion', $context);
        $editanypost = has_capability('mod/pdfannotator:editanypost', $context);
        $seehiddencomments = has_capability('mod/pdfannotator:seehiddencomments', $context);
        $hidecomments = has_capability('mod/pdfannotator:hidecomments', $context);
        $deleteany = has_capability('mod/pdfannotator:deleteany', $context);
        $deleteown = has_capability('mod/pdfannotator:deleteown', $context);
        $subscribe = has_capability('mod/pdfannotator:subscribe', $context);
        $forwardquestions = has_capability('mod/pdfannotator:forwardquestions', $context);
        $solve = has_capability('mod/pdfannotator:markcorrectanswer', $context);

        $this->questionvisibility = $data[0]->visibility;
        foreach ($data as $comment) {

            $comment->buttons = [];

            $comment->isdeleted = boolval($comment->isdeleted);
            $comment->isquestion = boolval($comment->isquestion);
            $comment->solved = boolval($comment->solved);

            $owner = ($comment->userid == $USER->id);
            $comment->owner = ($comment->userid == $USER->id);

            $comment->private = ($comment->visibility == "private");
            $comment->protected = ($comment->visibility == "protected");

            $this->addcssclasses($comment, $owner);

            $this->setvotes($comment);

            $this->addreportbutton($comment, $report, $cm);
            $this->addcloseopenbutton($comment, $closequestion, $closeanyquestion);
            $this->addeditbutton($comment, $editanypost);
            $this->addhidebutton($comment, $seehiddencomments, $hidecomments);
            $this->adddeletebutton($comment, $deleteown, $deleteany);
            $this->addsubscribebutton($comment, $subscribe);
            $this->addforwardbutton($comment, $forwardquestions, $cm);
            $this->addmarksolvedbutton($comment, $solve);

            $this->addsolvedicon($comment);

            if ($comment->isdeleted || isset($comment->type)) {
                $comment->displaycontent = '<em>' . $comment->displaycontent . '</em>';
            }

            if (!empty($comment->modifiedby) && ($comment->modifiedby != $comment->userid) && ($comment->userid != 0)) {
                $comment->modifiedby = get_string('modifiedby', 'pdfannotator') . ' '.
                    pdfannotator_get_username($comment->modifiedby);
            } else {
                $comment->modifiedby = null;
            }

            if ($comment->isquestion || !$comment->isdeleted) {
                $comment->dropdown = true;
            }

            $this->comments[] = $comment;
        }
        return;
    }

    /**
     * This function is required by any renderer to retrieve the data structure
     * passed into the template.
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {
        $data = [];
        $data['comments'] = $this->comments;
        $data['status'] = 'success';
        return $data;
    }

    private function addcssclasses($comment, $owner) {
        $comment->wrapperClass = 'chat-message comment-list-item';
        if ($comment->isquestion) {
            $comment->wrapperClass .= ' questioncomment';
        } else if ($comment->solved) {
            $comment->wrapperClass .= ' correct';
        }
        if ($owner) {
            $comment->wrapperClass .= ' owner';
        }
        if ($comment->usevotes) {
            $comment->wrapperClass .= ' usevotes';
        }
    }

    public function setvotes($comment) {
        if ($comment->usevotes && !$comment->isdeleted) {
            if ($comment->owner) {
                $comment->voteBtn = get_string('likeOwnComment', 'pdfannotator');
            } else if ($comment->isvoted && $comment->isquestion) {
                $comment->voteBtn = get_string('likeQuestionForbidden', 'pdfannotator');
            } else if ($comment->isvoted && !$comment->isquestion) {
                $comment->voteBtn = get_string('likeAnswerForbidden', 'pdfannotator');
            } else if (!$comment->isvoted && $comment->isquestion) {
                $comment->voteBtn = get_string('likeQuestion', 'pdfannotator');
            } else if (!$comment->isvoted && !$comment->isquestion) {
                $comment->voteBtn = get_string('likeAnswer', 'pdfannotator');
            }

            if (!$comment->votes) {
                $comment->votes = "0";
            }
            if ($comment->isquestion) {
                $comment->voteTitle = $comment->votes . " " . get_string('likeCountQuestion', 'pdfannotator');
            } else {
                $comment->voteTitle = $comment->votes . " " . get_string('likeCountAnswer', 'pdfannotator');
            }
        }
    }

    /**
     * Add check icon if comment is marked as correct.
     * @param type $comment
     */
    public function addsolvedicon($comment) {
        if ($comment->solved) {
            if ($comment->isquestion) {
                $comment->solvedicon = ["classes" => "icon fa fa-lock fa-fw solvedquestionicon",
                    "title" => get_string('questionSolved', 'pdfannotator')];
            } else if (!$comment->isdeleted) {
                $comment->solvedicon = ["classes" => "icon fa fa-check fa-fw correctanswericon",
                    "title" => get_string('answerSolved', 'pdfannotator')];
            }
        }
    }

    /**
     * Report comment if user is not the owner.
     * @param type $comment
     * @param type $owner
     * @param type $report
     */
    private function addreportbutton($comment, $report, $cm) {
        if (!$comment->isdeleted && $report && !$comment->owner && !isset($comment->type)) {
            $comment->report = true;
            $comment->cm = json_encode($cm);  // Course module object.
            $comment->cmid = $cm->id;
        }
    }

    /**
     * Open/close question if user is owner of the question or manager.
     * @param type $comment
     * @param type $owner
     * @param type $closequestion
     * @param type $closeanyquestion
     */
    private function addcloseopenbutton($comment, $closequestion, $closeanyquestion) {

        if (!isset($comment->type) && $comment->isquestion // Only set for textbox and drawing.
                && (($comment->owner && $closequestion) || $closeanyquestion)  && $comment->visibility != 'private') {

            if ($comment->solved) {
                $comment->buttons[] = ["classes" => "comment-solve-a", "faicon" => ["class" => "fa-unlock"],
                    "text" => get_string('markUnsolved', 'pdfannotator')];
            } else {
                $comment->buttons[] = ["classes" => "comment-solve-a", "faicon" => ["class" => "fa-lock"],
                    "text" => get_string('markSolved', 'pdfannotator')];
            }
        }
    }

    /**
     * Button for editing comment if user is owner of the comment or manager.
     * @param type $comment
     * @param type $owner
     * @param type $editanypost
     */
    private function addeditbutton($comment, $editanypost) {
        if (!$comment->isdeleted && !isset($comment->type) && ($comment->owner || $editanypost)) {
            $comment->buttons[] = ["classes" => "comment-edit-a", "attributes" => ["name" => "id",
                "value" => "editButton" . $comment->uuid], "moodleicon" => ["key" => "i/edit", "component" => "core",
                "title" => get_string('edit', 'pdfannotator')],
                "text" => get_string('edit', 'pdfannotator')];
        }
    }

    private function addhidebutton($comment, $seehiddencomments, $hidecomments) {
        // Don't need to hide personal notes.
        if ($this->questionvisibility == 'private') {
            return;
        }
        if (!empty($comment->ishidden) && !isset($comment->type)) {
            if ($seehiddencomments) {
                $comment->dimmed = 'dimmed_text';
                $comment->displayhidden = 1;
                $comment->buttons[] = ["attributes" => ["name" => "id", "value" => "hideButton" . $comment->uuid],
                    "moodleicon" => ["key" => "i/hide", "component" => "core",
                    "title" => get_string('removehidden', 'pdfannotator')],
                    "text" => get_string('removehidden', 'pdfannotator')];
            } else {
                $comment->visibility = 'anonymous';
                $comment->displaycontent = '<em>' . get_string('hiddenComment', 'pdfannotator') . '</em>';
            }
        } else if (!isset($comment->type)) {
            if ($hidecomments) {
                $comment->buttons[] = ["attributes" => ["name" => "id", "value" => "hideButton" . $comment->uuid],
                    "moodleicon" => ["key" => "i/show", "component" => "core", "title" => get_string('markhidden', 'pdfannotator')],
                    "text" => get_string('markhidden', 'pdfannotator')];
            }
        }
    }

    /**
     * Delete comment if user is owner of the comment or manager.
     * @param type $comment
     * @param type $owner
     * @param type $deleteown
     * @param type $deleteany
     */
    private function adddeletebutton($comment, $deleteown, $deleteany) {
        if (!$comment->isdeleted && ($deleteany || ($deleteown && $comment->owner))) {
            $comment->buttons[] = ["classes" => "comment-delete-a", "text" => get_string('delete', 'pdfannotator'),
                "moodleicon" => ["key" => "delete", "component" => "pdfannotator",
                    "title" => get_string('delete', 'pdfannotator')]];
        }
    }

    private function addsubscribebutton($comment, $subscribe) {
        if (!isset($comment->type) && $comment->isquestion && $subscribe && $comment->visibility != 'private') {
            // Only set for textbox and drawing.
            if (!empty($comment->issubscribed)) {
                $comment->buttons[] = ["classes" => "comment-subscribe-a", "faicon" => ["class" => "fa-bell-slash"],
                    "text" => get_string('unsubscribeQuestion', 'pdfannotator')];
            } else {
                $comment->buttons[] = ["classes" => "comment-subscribe-a", "faicon" => ["class" => "fa-bell"],
                    "text" => get_string('subscribeQuestion', 'pdfannotator')];
            }
        }
    }

    private function addforwardbutton($comment, $forwardquestions, $cm) {
        if (!isset($comment->type) && $comment->isquestion && !$comment->isdeleted && $forwardquestions &&
            $comment->visibility != 'private') {
            global $CFG;
            $urlparams = ['id' => $cm->id, 'action' => 'forwardquestion', 'commentid' => $comment->uuid, 'sesskey' => sesskey()];
            $url = new moodle_url($CFG->wwwroot . '/mod/pdfannotator/view.php', $urlparams);

            $comment->buttons[] = ["classes" => "comment-forward-a", "attributes" => ["name" => "onclick",
                "value" => "window.location.href = '$url';"], "faicon" => ["class" => "fa-share"],
                "text" => get_string('forward', 'pdfannotator')];
        }
    }

    private function addmarksolvedbutton($comment, $solve) {
        if ($solve && !$comment->isquestion && !$comment->isdeleted && !isset($comment->type) &&
            $this->questionvisibility != 'private') {
            if ($comment->solved) {
                $comment->buttons[] = ["classes" => "comment-solve-a", "text" => get_string('removeCorrect', 'pdfannotator'),
                    "moodleicon" => ["key" => "i/completion-manual-n", "component" => "core",
                        "title" => get_string('removeCorrect', 'pdfannotator')]];
            } else {
                $comment->buttons[] = ["classes" => "comment-solve-a", "text" => get_string('markCorrect', 'pdfannotator'),
                    "moodleicon" => ["key" => "i/completion-manual-enabled", "component" => "core",
                        "title" => get_string('markCorrect', 'pdfannotator')]];
            }
        }
    }

}
