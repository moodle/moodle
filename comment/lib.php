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

    // static variable will be used by non-js comments UI
    private static $nonjs = false;
    private static $comment_itemid = null;
    private static $comment_context = null;
    private static $comment_area = null;
	private static $comment_page = null;
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

        // setup course
        // course will be used to generate user profile link
        if (!empty($options->course)) {
            $this->courseid = $options->course->id;
        } else if (!empty($options->courseid)) {
            $this->courseid = $options->courseid;
        } else {
            print_error('commentsrequirecourseid');
        }

        if (!empty($options->pluginname)) {
            $this->pluginname = $options->pluginname;
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
        // setting post and view permissions
        $this->check_permissions();

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

        $this->setup_plugin();

        // setup options for callback functions
        $this->args = new stdclass;
        $this->args->context     = $this->context;
        $this->args->courseid    = $this->courseid;
        $this->args->cm          = $this->cm;
        $this->args->commentarea = $this->commentarea;
        $this->args->itemid      = $this->itemid;

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
    }

    /**
     * Setup plugin type and plugin name
     */
    private function setup_plugin() {
        global $DB;
        // blog needs to set env as "blog"
        if ($this->env == 'blog') {
            $this->plugintype = 'moodle';
            $this->pluginname = 'blog';
        }
        // tag page needs to set env as "tag"
        if ($this->env == 'tag') {
            $this->plugintype = 'moodle';
            $this->pluginname = 'tag';
        }
        if ($this->context->contextlevel == CONTEXT_BLOCK) {
            if ($block = $DB->get_record('block_instances', array('id'=>$this->context->instanceid))) {
                $this->plugintype = 'block';
                $this->pluginname = $block->blockname;
            }
        }

        if ($this->context->contextlevel == CONTEXT_MODULE) {
            $this->plugintype = 'mod';
            // to improve performance, pluginname should be assigned before initilise comment object
            // if it is empty, we will try to guess, it will rarely be used.
            if (empty($this->pluginname)) {
                if (empty($this->course)) {
                    $this->course = $DB->get_record('course', array('id'=>$this->courseid), '*', MUST_EXIST);
                }
                $this->modinfo = get_fast_modinfo($this->course);
                $this->pluginname = $this->modinfo->cms[$this->cm->id]->modname;
            }
        }
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
            $permissions = plugin_callback($this->plugintype, $this->pluginname, FEATURE_COMMENT, 'permissions', $this->args, array('post'=>true, 'view'=>true));
            $this->postcap = $this->postcap && $permissions['post'];
            $this->viewcap = $this->viewcap && $permissions['view'];
        }
    }

    /**
     * Prepare comment code in html
     * @param  boolean $return
     * @return mixed
     */
    public function output($return = true) {
        global $PAGE, $OUTPUT;
		static $template_printed;

        $this->link = $PAGE->url;
        $murl = new moodle_url($this->link);
        $murl->remove_params('nonjscomment');
        $murl->param('nonjscomment', 'true');
        $murl->param('comment_itemid', $this->itemid);
        $murl->param('comment_context', $this->context->id);
        $murl->param('comment_area', $this->commentarea);
        $murl->remove_params('comment_page');
        $this->link = $murl->out();

        $options = new stdclass;
        $options->client_id = $this->cid;
        $options->commentarea = $this->commentarea;
        $options->itemid = $this->itemid;
        $options->page   = 0;
        $options->courseid = $this->courseid;
        $options->contextid = $this->contextid;
        $options->env = $this->env;
        if ($this->env == 'block_comments') {
            $options->autostart = true;
            $options->notoggle = true;
        }

        $PAGE->requires->js_init_call('M.core_comment.init', array($options), true);

        if (!empty(self::$nonjs)) {
            // return non js comments interface
            return $this->print_comments(self::$comment_page, $return, true);
        }

        $strsubmit = get_string('submit');
        $strcancel = get_string('cancel');
        $sesskey = sesskey();
        $html = '';
        // print html template
        // Javascript will use the template to render new comments
        if (empty($template_printed) && !empty($this->viewcap)) {
            $html .= '<div style="display:none" id="cmt-tmpl">' . $this->template . '</div>';
            $template_printed = true;
        }

        if (!empty($this->viewcap)) {
            // print commenting icon and tooltip
            $icon = $OUTPUT->pix_url('t/collapsed');
            $html .= <<<EOD
<div class="mdl-left">
<a id="comment-link-{$this->cid}" href="{$this->link}">
    <img id="comment-img-{$this->cid}" src="$icon" alt="{$this->linktext}" title="{$this->linktext}" />
    <span id="comment-link-text-{$this->cid}">{$this->linktext} {$this->count}</span>
</a>
<div id="comment-ctrl-{$this->cid}" class="comment-ctrl">
    <ul id="comment-list-{$this->cid}" class="comment-list">
EOD;
            // in comments block, we print comments list right away
            if ($this->env == 'block_comments') {
                $html .= $this->print_comments(0, true, false);
                $html .= '</ul>';
                $html .= $this->get_pagination(0);
            } else {
                $html .= <<<EOD
    </ul>
    <div id="comment-pagination-{$this->cid}" class="comment-pagination"></div>
EOD;
            }

            // print posting textarea
            if (!empty($this->postcap)) {
                $html .= <<<EOD
<div class='comment-area'>
    <div class="bd">
        <textarea name="content" rows="2" id="dlg-content-{$this->cid}"></textarea>
    </div>
    <div class="fd" id="comment-action-{$this->cid}">
        <a href="#" id="comment-action-post-{$this->cid}"> {$strsubmit} </a>
EOD;
        if ($this->env != 'block_comments') {
            $html .= <<<EOD
        <span> | </span>
        <a href="#" id="comment-action-cancel-{$this->cid}"> {$strcancel} </a>
EOD;
        }

        $html .= <<<EOD
    </div>
</div>
<div class="clearer"></div>
EOD;
            }

            $html .= <<<EOD
</div><!-- end of comment-ctrl -->
</div>
EOD;
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
        $sql = "SELECT c.id, c.userid, c.content, c.format, c.timecreated, u.picture, u.imagealt, u.username, u.firstname, u.lastname
            FROM {comments} c, {user} u WHERE u.id=c.userid AND c.contextid=? AND c.commentarea=? AND c.itemid=?
            ORDER BY c.timecreated DESC";
        $params[] = $this->contextid;
        $params[] = $this->commentarea;
        $params[] = $this->itemid;

        $comments = array();
        $candelete = has_capability('moodle/comment:delete', $this->context);
        if ($records = $DB->get_records_sql($sql, $params, $start, $CFG->commentsperpage)) {
            foreach ($records as &$c) {
                $url = $CFG->httpswwwroot.'/user/view.php?id='.$c->userid.'&amp;course='.$this->courseid;
                $c->username = '<a href="'.$url.'">'.fullname($c).'</a>';
                $c->time = userdate($c->timecreated, get_string('strftimerecent', 'langconfig'));
                $user = new stdclass;
                $user->id = $c->userid;
                $user->picture = $c->picture;
                $user->firstname = $c->firstname;
                $user->lastname  = $c->lastname;
                $user->imagealt  = $c->imagealt;
                $c->content = format_text($c->content, $c->format);
                $c->avatar = $OUTPUT->user_picture($user, array('size'=>18));
                if (($USER->id == $c->userid) || !empty($candelete)) {
                    $c->delete = true;
                }
                $comments[] = $c;
            }
        }
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
            return '';
        }
        if (!empty(self::$nonjs)) {
            // used in non-js interface
            return $OUTPUT->paging_bar($count, $page, $CFG->commentsperpage, $this->link, 'comment_page');
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
        $newcmt = new stdclass;
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
            $newcmt->username = fullname($USER);
            $newcmt->content = format_text($newcmt->content);
            $newcmt->avatar = $OUTPUT->user_picture($USER, array('size'=>16));
            return $newcmt;
        } else {
            throw new comment_exception('dbupdatefailed');
        }
    }

    /**
     * delete by context, commentarea and itemid
     *
     */
    public function delete_comments() {
        global $DB;
        $DB->delete_records('comments', array(
            'contextid'=>$this->context->id,
            'commentarea'=>$this->commentarea,
            'itemid'=>$this->itemid)
        );
        return true;
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
            $cmt->content = '<div class="comment-delete"><a href="#" id ="comment-delete-'.$this->cid.'-'.$cmt->id.'"><img src="'.$OUTPUT->pix_url('t/delete').'" /></a></div>' . $cmt->content;
            // add the button
        }
        $patterns[] = '___picture___';
        $patterns[] = '___name___';
        $patterns[] = '___content___';
        $patterns[] = '___time___';
        $replacements[] = $cmt->avatar;
        $replacements[] = fullname($cmt);
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
