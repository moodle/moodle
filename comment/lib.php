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
 * Comment is helper class to add/delete comments anywhere in moodle
 *
 * @package   comment
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class comment {
    /**
     * @var integer
     */
    private $page;
    /**
     * there may be several comment box in one page
     * so we need a client_id to recognize them
     * @var integer
     */
    private $cid;
    private $contextid;
    /**
     * commentarea is used to specify different
     * parts shared the same itemid
     * @var string
     */
    private $commentarea;
    /**
     * itemid is used to associate with commenting content
     * @var integer
     */
    private $itemid;

    /**
     * this html snippet will be used as a template
     * to build comment content
     * @var string
     */
    private $template;
    private $context;
    private $courseid;
    /**
     * course module object, only be used to help find pluginname automatically
     * if pluginname is specified, it won't be used at all
     * @var string
     */
    private $cm;
    private $plugintype;
    /**
     * When used in module, it is recommended to use it
     * @var string
     */
    private $pluginname;
    private $viewcap;
    private $postcap;
    /**
     * to tell comments api where it is used
     * @var string
     */
    private $env;
    /**
     * to costomize link text
     * @var string
     */
    private $linktext;

    /**
     * If set to true then comment sections won't be able to be opened and closed
     * instead they will always be visible.
     * @var bool
     */
    protected $notoggle = false;

    /**
     * If set to true comments are automatically loaded as soon as the page loads.
     * Normally this happens when the user expands the comment section.
     * @var bool
     */
    protected $autostart = false;

    /**
     * If set to true a cancel button will be shown on the form used to submit comments.
     * @var bool
     */
    protected $displaycancel = false;

    // static variable will be used by non-js comments UI
    private static $nonjs = false;
    private static $comment_itemid = null;
    private static $comment_context = null;
    private static $comment_area = null;
    private static $comment_page = null;
    private static $comment_component = null;

    /**
     * Construct function of comment class, initialise
     * class members
     * @param object $options
     */
    public function __construct($options) {
        global $CFG, $DB;

        if (empty($CFG->commentsperpage)) {
            $CFG->commentsperpage = 15;
        }

        $this->viewcap = false;
        $this->postcap = false;

        // setup client_id
        if (!empty($options->client_id)) {
            $this->cid = $options->client_id;
        } else {
            $this->cid = uniqid();
        }

        // setup context
        if (!empty($options->context)) {
            $this->context = $options->context;
            $this->contextid = $this->context->id;
        } else if(!empty($options->contextid)) {
            $this->contextid = $options->contextid;
            $this->context = get_context_instance_by_id($this->contextid);
        } else {
            print_error('invalidcontext');
        }

        if (!empty($options->component)) {
            $this->set_component($options->component);
        }

        // setup course
        // course will be used to generate user profile link
        if (!empty($options->course)) {
            $this->courseid = $options->course->id;
        } else if (!empty($options->courseid)) {
            $this->courseid = $options->courseid;
        } else {
            $this->courseid = SITEID;
        }

        // setup coursemodule
        if (!empty($options->cm)) {
            $this->cm = $options->cm;
        } else {
            $this->cm = null;
        }

        // setup commentarea
        if (!empty($options->area)) {
            $this->commentarea = $options->area;
        }

        // setup itemid
        if (!empty($options->itemid)) {
            $this->itemid = $options->itemid;
        } else {
            $this->itemid = 0;
        }

        // setup env
        if (!empty($options->env)) {
            $this->env = $options->env;
        } else {
            $this->env = '';
        }

        // setup customized linktext
        if (!empty($options->linktext)) {
            $this->linktext = $options->linktext;
        } else {
            $this->linktext = get_string('comments');
        }

        if (!empty($options->ignore_permission)) {
            $this->ignore_permission = true;
        } else {
            $this->ignore_permission = false;
        }

        // setup notoggle
        if (!empty($options->notoggle)) {
            $this->set_notoggle($options->notoggle);
        }

        // setup notoggle
        if (!empty($options->autostart)) {
            $this->set_autostart($options->autostart);
        }

        // setup displaycancel
        if (!empty($options->displaycancel)) {
            $this->set_displaycancel($options->displaycancel);
        }

        if (!empty($options->showcount)) {
            $count = $this->count();
            if (empty($count)) {
                $this->count = '';
            } else {
                $this->count = '('.$count.')';
            }
        } else {
            $this->count = '';
        }

        // setup options for callback functions
        $this->args = new stdClass();
        $this->args->context     = $this->context;
        $this->args->courseid    = $this->courseid;
        $this->args->cm          = $this->cm;
        $this->args->commentarea = $this->commentarea;
        $this->args->itemid      = $this->itemid;

        // setting post and view permissions
        $this->check_permissions();

        // load template
        $this->template = <<<EOD
<div class="comment-userpicture">___picture___</div>
<div class="comment-content">
    ___name___ - <span>___time___</span>
    <div>___content___</div>
</div>
EOD;
        if (!empty($this->plugintype)) {
            $this->template = plugin_callback($this->plugintype, $this->pluginname, FEATURE_COMMENT, 'template', $this->args, $this->template);
        }

        unset($options);
    }

    /**
     * Receive nonjs comment parameters
     */
    public static function init() {
        global $PAGE, $CFG;
        // setup variables for non-js interface
        self::$nonjs = optional_param('nonjscomment', '', PARAM_ALPHA);
        self::$comment_itemid  = optional_param('comment_itemid',  '', PARAM_INT);
        self::$comment_context = optional_param('comment_context', '', PARAM_INT);
        self::$comment_page    = optional_param('comment_page',    '', PARAM_INT);
        self::$comment_area    = optional_param('comment_area',    '', PARAM_ALPHAEXT);

        $PAGE->requires->string_for_js('addcomment', 'moodle');
        $PAGE->requires->string_for_js('deletecomment', 'moodle');
        $PAGE->requires->string_for_js('comments', 'moodle');
        $PAGE->requires->string_for_js('commentsrequirelogin', 'moodle');
    }

    public function set_component($component) {
        $this->component = $component;
        list($this->plugintype, $this->pluginname) = normalize_component($component);
        return null;
    }

    public function set_view_permission($value) {
        $this->viewcap = $value;
    }

    public function set_post_permission($value) {
        $this->postcap = $value;
    }

    /**
     * check posting comments permission
     * It will check based on user roles and ask modules
     * If you need to check permission by modules, a
     * function named $pluginname_check_comment_post must be implemented
     */
    private function check_permissions() {
        global $CFG;
        $this->postcap = has_capability('moodle/comment:post', $this->context);
        $this->viewcap = has_capability('moodle/comment:view', $this->context);
        if (!empty($this->plugintype)) {
            $permissions = plugin_callback($this->plugintype, $this->pluginname, FEATURE_COMMENT, 'permissions', array($this->args), array('post'=>true, 'view'=>true));
            if ($this->ignore_permission) {
                $this->postcap = $permissions['post'];
                $this->viewcap = $permissions['view'];
            } else {
                $this->postcap = $this->postcap && $permissions['post'];
                $this->viewcap = $this->viewcap && $permissions['view'];
            }
        }
    }

    /**
     * Gets a link for this page that will work with JS disabled.
     *
     * @global moodle_page $PAGE
     * @param moodle_page $page
     * @return moodle_url
     */
    public function get_nojslink(moodle_page $page = null) {
        if ($page === null) {
            global $PAGE;
            $page = $PAGE;
        }

        $link = new moodle_url($page->url, array(
            'nonjscomment'    => true,
            'comment_itemid'  => $this->itemid,
            'comment_context' => $this->context->id,
            'comment_area'    => $this->commentarea,
        ));
        $link->remove_params(array('nonjscomment', 'comment_page'));
        return $link;
    }

    /**
     * Sets the value of the notoggle option.
     *
     * If set to true then the user will not be able to expand and collase
     * the comment section.
     *
     * @param bool $newvalue
     */
    public function set_notoggle($newvalue = true) {
        $this->notoggle = (bool)$newvalue;
    }

    /**
     * Sets the value of the autostart option.
     *
     * If set to true then the comments will be loaded during page load.
     * Normally this happens only once the user expands the comment section.
     *
     * @param bool $newvalue
     */
    public function set_autostart($newvalue = true) {
        $this->autostart = (bool)$newvalue;
    }

    /**
     * Sets the displaycancel option
     *
     * If set to true then a cancel button will be shown when using the form
     * to post comments.
     *
     * @param bool $newvalue
     */
    public function set_displaycancel($newvalue = true) {
        $this->displaycancel = (bool)$newvalue;
    }

    /**
     * Initialises the JavaScript that enchances the comment API.
     *
     * @param moodle_page $page The moodle page object that the JavaScript should be
     *                          initialised for.
     */
    public function initialise_javascript(moodle_page $page) {

        $options = new stdClass;
        $options->client_id   = $this->cid;
        $options->commentarea = $this->commentarea;
        $options->itemid      = $this->itemid;
        $options->page        = 0;
        $options->courseid    = $this->courseid;
        $options->contextid   = $this->contextid;
        $options->env         = $this->env;
        $options->component   = $this->component;
        $options->notoggle    = $this->notoggle;
        $options->autostart   = $this->autostart;

        $page->requires->js_init_call('M.core_comment.init', array($options), true);

        return true;
    }

    /**
     * Prepare comment code in html
     * @param  boolean $return
     * @return mixed
     */
    public function output($return = true) {
        global $PAGE, $OUTPUT;
        static $template_printed;

        $this->initialise_javascript($PAGE);

        if (!empty(self::$nonjs)) {
            // return non js comments interface
            return $this->print_comments(self::$comment_page, $return, true);
        }

        $html = '';

        // print html template
        // Javascript will use the template to render new comments
        if (empty($template_printed) && !empty($this->viewcap)) {
            $html .= html_writer::tag('div', $this->template, array('style' => 'display:none', 'id' => 'cmt-tmpl'));
            $template_printed = true;
        }

        if (!empty($this->viewcap)) {
            // print commenting icon and tooltip
            $html .= html_writer::start_tag('div', array('class' => 'mdl-left'));
            $html .= html_writer::link($this->get_nojslink($PAGE), get_string('showcommentsnonjs'), array('class' => 'showcommentsnonjs'));

            if (!$this->notoggle) {
                // If toggling is enabled (notoggle=false) then print the controls to toggle
                // comments open and closed
                $html .= html_writer::start_tag('a', array('class' => 'comment-link', 'id' => 'comment-link-'.$this->cid, 'href' => '#'));
                $html .= html_writer::empty_tag('img', array('id' => 'comment-img-'.$this->cid, 'src' => $OUTPUT->pix_url('t/collapsed'), 'alt' => $this->linktext, 'title' => $this->linktext));
                $html .= html_writer::tag('span', $this->linktext.' '.$this->count, array('id' => 'comment-link-text-'.$this->cid));
                $html .= html_writer::end_tag('a');
            }

            $html .= html_writer::start_tag('div', array('id' => 'comment-ctrl-'.$this->cid, 'class' => 'comment-ctrl'));
            $html .= html_writer::start_tag('ul', array('id' => 'comment-list-'.$this->cid, 'class' => 'comment-list'));
            $html .= html_writer::tag('li', '', array('class' => 'first'));

            if ($this->autostart) {
                // If autostart has been enabled print the comments list immediatly
                $html .= $this->print_comments(0, true, false);
                $html .= html_writer::end_tag('ul'); // .comment-list
                $html .= $this->get_pagination(0);
            } else {
                $html .= html_writer::end_tag('ul'); // .comment-list
                $html .= html_writer::tag('div', '', array('id' => 'comment-pagination-'.$this->cid, 'class' => 'comment-pagination'));
            }

            if (!empty($this->postcap)) {
                // print posting textarea
                $html .= html_writer::start_tag('div', array('class' => 'comment-area'));
                $html .= html_writer::start_tag('div', array('class' => 'db'));
                $html .= html_writer::tag('textarea', '', array('name' => 'content', 'rows' => 2, 'cols' => 20, 'id' => 'dlg-content-'.$this->cid));
                $html .= html_writer::end_tag('div'); // .db

                $html .= html_writer::start_tag('div', array('class' => 'fd', 'id' => 'comment-action-'.$this->cid));
                $html .= html_writer::link('#', get_string('savecomment'), array('id' => 'comment-action-post-'.$this->cid));

                if ($this->displaycancel) {
                    $html .= html_writer::tag('span', ' | ');
                    $html .= html_writer::link('#', get_string('cancel'), array('id' => 'comment-action-cancel-'.$this->cid));
                }

                $html .= html_writer::end_tag('div'); // .fd
                $html .= html_writer::end_tag('div'); // .comment-area
                $html .= html_writer::tag('div', '', array('class' => 'clearer'));
            }

            $html .= html_writer::end_tag('div'); // .comment-ctrl
            $html .= html_writer::end_tag('div'); // .mdl-left
        } else {
            $html = '';
        }

        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

    /**
     * Return matched comments
     *
     * @param  int $page
     * @return mixed
     */
    public function get_comments($page = '') {
        global $DB, $CFG, $USER, $OUTPUT;
        if (empty($this->viewcap)) {
            return false;
        }
        if (!is_numeric($page)) {
            $page = 0;
        }
        $this->page = $page;
        $params = array();
        $start = $page * $CFG->commentsperpage;
        $ufields = user_picture::fields('u');
        $sql = "SELECT $ufields, c.id AS cid, c.content AS ccontent, c.format AS cformat, c.timecreated AS ctimecreated
                  FROM {comments} c
                  JOIN {user} u ON u.id = c.userid
                 WHERE c.contextid = :contextid AND c.commentarea = :commentarea AND c.itemid = :itemid
              ORDER BY c.timecreated DESC";
        $params['contextid'] = $this->contextid;
        $params['commentarea'] = $this->commentarea;
        $params['itemid'] = $this->itemid;

        $comments = array();
        $candelete = has_capability('moodle/comment:delete', $this->context);
        $formatoptions = array('overflowdiv' => true);
        $rs = $DB->get_recordset_sql($sql, $params, $start, $CFG->commentsperpage);
        foreach ($rs as $u) {
            $c = new stdClass();
            $c->id          = $u->cid;
            $c->content     = $u->ccontent;
            $c->format      = $u->cformat;
            $c->timecreated = $u->ctimecreated;
            $url = new moodle_url('/user/view.php', array('id'=>$u->id, 'course'=>$this->courseid));
            $c->profileurl = $url->out();
            $c->fullname = fullname($u);
            $c->time = userdate($c->timecreated, get_string('strftimerecent', 'langconfig'));
            $c->content = format_text($c->content, $c->format, $formatoptions);

            $c->avatar = $OUTPUT->user_picture($u, array('size'=>18));
            if (($USER->id == $u->id) || !empty($candelete)) {
                $c->delete = true;
            }
            $comments[] = $c;
        }
        $rs->close();

        if (!empty($this->plugintype)) {
            // moodle module will filter comments
            $comments = plugin_callback($this->plugintype, $this->pluginname, FEATURE_COMMENT, 'display', array($comments, $this->args), $comments);
        }

        return $comments;
    }

    public function count() {
        global $DB;
        if ($count = $DB->count_records('comments', array('itemid'=>$this->itemid, 'commentarea'=>$this->commentarea, 'contextid'=>$this->context->id))) {
            return $count;
        } else {
            return 0;
        }
    }

    public function get_pagination($page = 0) {
        global $DB, $CFG, $OUTPUT;
        $count = $this->count();
        $pages = (int)ceil($count/$CFG->commentsperpage);
        if ($pages == 1 || $pages == 0) {
            return html_writer::tag('div', '', array('id' => 'comment-pagination-'.$this->cid, 'class' => 'comment-pagination'));
        }
        if (!empty(self::$nonjs)) {
            // used in non-js interface
            return $OUTPUT->paging_bar($count, $page, $CFG->commentsperpage, $this->get_nojslink(), 'comment_page');
        } else {
            // return ajax paging bar
            $str = '';
            $str .= '<div class="comment-paging" id="comment-pagination-'.$this->cid.'">';
            for ($p=0; $p<$pages; $p++) {
                if ($p == $page) {
                    $class = 'curpage';
                } else {
                    $class = 'pageno';
                }
                $str .= '<a href="#" class="'.$class.'" id="comment-page-'.$this->cid.'-'.$p.'">'.($p+1).'</a> ';
            }
            $str .= '</div>';
        }
        return $str;
    }

    /**
     * Add a new comment
     * @param string $content
     * @return mixed
     */
    public function add($content, $format = FORMAT_MOODLE) {
        global $CFG, $DB, $USER, $OUTPUT;
        if (empty($this->postcap)) {
            throw new comment_exception('nopermissiontocomment');
        }
        $now = time();
        $newcmt = new stdClass();
        $newcmt->contextid    = $this->contextid;
        $newcmt->commentarea  = $this->commentarea;
        $newcmt->itemid       = $this->itemid;
        $newcmt->content      = $content;
        $newcmt->format       = $format;
        $newcmt->userid       = $USER->id;
        $newcmt->timecreated  = $now;

        if (!empty($this->plugintype)) {
            // moodle module will check content
            $ret = plugin_callback($this->plugintype, $this->pluginname, FEATURE_COMMENT, 'add', array(&$newcmt, $this->args), true);
            if (!$ret) {
                throw new comment_exception('modulererejectcomment');
            }
        }

        $cmt_id = $DB->insert_record('comments', $newcmt);
        if (!empty($cmt_id)) {
            $newcmt->id = $cmt_id;
            $newcmt->time = userdate($now, get_string('strftimerecent', 'langconfig'));
            $newcmt->fullname = fullname($USER);
            $url = new moodle_url('/user/view.php', array('id'=>$USER->id, 'course'=>$this->courseid));
            $newcmt->profileurl = $url->out();
            $newcmt->content = format_text($newcmt->content, $format, array('overflowdiv'=>true));
            $newcmt->avatar = $OUTPUT->user_picture($USER, array('size'=>16));
            return $newcmt;
        } else {
            throw new comment_exception('dbupdatefailed');
        }
    }

    /**
     * delete by context, commentarea and itemid
     * @param object $param {
     *            contextid => int the context in which the comments exist [required]
     *            commentarea => string the comment area [optional]
     *            itemid => int comment itemid [optional]
     * }
     * @return boolean
     */
    public function delete_comments($param) {
        global $DB;
        $param = (array)$param;
        if (empty($param['contextid'])) {
            return false;
        }
        $DB->delete_records('comments', $param);
        return true;
    }

    /**
     * Delete page_comments in whole course, used by course reset
     * @param object $context course context
     */
    public function reset_course_page_comments($context) {
        global $DB;
        $contexts = array();
        $contexts[] = $context->id;
        $children = get_child_contexts($context);
        foreach ($children as $c) {
            $contexts[] = $c->id;
        }
        list($ids, $params) = $DB->get_in_or_equal($contexts);
        $DB->delete_records_select('comments', "commentarea='page_comments' AND contextid $ids", $params);
    }

    /**
     * Delete a comment
     * @param  int $commentid
     * @return mixed
     */
    public function delete($commentid) {
        global $DB, $USER;
        $candelete = has_capability('moodle/comment:delete', $this->context);
        if (!$comment = $DB->get_record('comments', array('id'=>$commentid))) {
            throw new comment_exception('dbupdatefailed');
        }
        if (!($USER->id == $comment->userid || !empty($candelete))) {
            throw new comment_exception('nopermissiontocomment');
        }
        $DB->delete_records('comments', array('id'=>$commentid));
        return true;
    }

    /**
     * Print comments
     * @param int $page
     * @param boolean $return return comments list string or print it out
     * @param boolean $nonjs print nonjs comments list or not?
     * @return mixed
     */
    public function print_comments($page = 0, $return = true, $nonjs = true) {
        global $DB, $CFG, $PAGE;
        $html = '';
        if (!(self::$comment_itemid == $this->itemid &&
            self::$comment_context == $this->context->id &&
            self::$comment_area == $this->commentarea)) {
            $page = 0;
        }
        $comments = $this->get_comments($page);

        $html = '';
        if ($nonjs) {
            $html .= '<h3>'.get_string('comments').'</h3>';
            $html .= "<ul id='comment-list-$this->cid' class='comment-list'>";
        }
        $results = array();
        $list = '';

        foreach ($comments as $cmt) {
            $list = '<li id="comment-'.$cmt->id.'-'.$this->cid.'">'.$this->print_comment($cmt, $nonjs).'</li>' . $list;
        }
        $html .= $list;
        if ($nonjs) {
            $html .= '</ul>';
            $html .= $this->get_pagination($page);
        }
        $sesskey = sesskey();
        $returnurl = $PAGE->url;
        $strsubmit = get_string('submit');
        if ($nonjs) {
        $html .= <<<EOD
<form method="POST" action="{$CFG->wwwroot}/comment/comment_post.php">
<textarea name="content" rows="2"></textarea>
<input type="hidden" name="contextid" value="$this->contextid" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="area" value="$this->commentarea" />
<input type="hidden" name="component" value="$this->component" />
<input type="hidden" name="itemid" value="$this->itemid" />
<input type="hidden" name="courseid" value="{$this->courseid}" />
<input type="hidden" name="sesskey" value="{$sesskey}" />
<input type="hidden" name="returnurl" value="{$returnurl}" />
<input type="submit" value="{$strsubmit}" />
</form>
EOD;
        }
        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

    public function print_comment($cmt, $nonjs = true) {
        global $OUTPUT;
        $patterns = array();
        $replacements = array();

        if (!empty($cmt->delete) && empty($nonjs)) {
            $cmt->content = '<div class="comment-delete"><a href="#" id ="comment-delete-'.$this->cid.'-'.$cmt->id.'"><img src="'.$OUTPUT->pix_url('t/delete').'" alt="'.get_string('delete').'" /></a></div>' . $cmt->content;
            // add the button
        }
        $patterns[] = '___picture___';
        $patterns[] = '___name___';
        $patterns[] = '___content___';
        $patterns[] = '___time___';
        $replacements[] = $cmt->avatar;
        $replacements[] = html_writer::link($cmt->profileurl, $cmt->fullname);
        $replacements[] = $cmt->content;
        $replacements[] = userdate($cmt->timecreated, get_string('strftimerecent', 'langconfig'));

        // use html template to format a single comment.
        return str_replace($patterns, $replacements, $this->template);
    }
}

class comment_exception extends moodle_exception {
    public $message;
    function __construct($errorcode) {
        $this->errorcode = $errorcode;
        $this->message = get_string($errorcode, 'error');
    }
}
