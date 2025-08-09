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

namespace core_comment;

use coding_exception;
use context;
use core_component;
use html_writer;
use moodle_page;
use moodle_url;
use stdClass;

/**
 * Helper class to add/delete comments anywhere in moodle.
 *
 * @package   core
 * @category  comment
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** @var int|string there may be several comment box in one page so we need a client_id to recognize them */
    private int|string $cid;
    /** @var string commentarea is used to specify different parts shared the same itemid */
    private string $commentarea;
    /** @var int itemid is used to associate with commenting content */
    private int $itemid;
    /** @var string this html snippet will be used as a template to build comment content */
    private string $template;
    /** @var int The context id for comments */
    private int $contextid;
    /** @var context The context itself */
    private context $context;
    /** @var int The course id for comments */
    private int $courseid;
    /** @var \cm_info|stdClass|null course module object, only be used to help find pluginname automatically */
    private \cm_info|stdClass|null $cm = null;
    /**
     * The component that this comment is for.
     *
     * It is STRONGLY recommended to set this.
     * Added as a database field in 2.9, old comments will have a null component.
     *
     * @var string
     */
    private string $component;
    /** @var string This is calculated by normalising the component */
    private string $pluginname;
    /** @var string This is calculated by normalising the component */
    private string $plugintype;
    /** @var bool Whether the user has the required capabilities/permissions to view comments. */
    private bool $viewcap = false;
    /** @var bool Whether the user has the required capabilities/permissions to post comments. */
    private bool $postcap = false;
    /** @var string to customize link text */
    private string $linktext;
    /** @var bool If set to true then comment sections won't be able to be opened and closed instead they will always be visible. */
    protected bool $notoggle = false;
    /** @var bool If set to true comments are automatically loaded as soon as the page loads. */
    protected bool $autostart = false;
    /** @var bool If set to true the total count of comments is displayed when displaying comments. */
    protected bool $displaytotalcount = false;
    /** @var bool If set to true a cancel button will be shown on the form used to submit comments. */
    protected bool $displaycancel = false;
    /** @var int|null The number of comments associated with this comments params */
    protected int|null $totalcommentcount = null;

    /**
     * Set to true to remove the col attribute from the textarea making it full width.
     * @var bool
     */
    protected bool $fullwidth = false;

    /** @var bool Use non-javascript UI */
    private static bool $nonjs = false;
    /** @var int|null comment itemid used in non-javascript UI */
    private static ?int $nojscommentitemid = null;
    /** @var int|null comment context used in non-javascript UI */
    private static ?int $nojscommentcontext = null;
    /** @var string|null comment area used in non-javascript UI */
    private static ?string $nojscommentarea = null;
    /** @var string|null comment page used in non-javascript UI */
    private static ?string $nojscommentpage = null;
    /** @var string|null comment itemid component in non-javascript UI */
    private static ?string $nojscommentcomponent = null;

    /** @var stdClass comment paramaters for callback. */
    protected stdClass $commentparams;

    /**
     * Construct function of comment class, initialise
     * class members
     *
     * @param stdClass $options {
     *            context => context context to use for the comment [required]
     *            component => string which plugin will comment being added to [required]
     *            itemid  => int the id of the associated item (forum post, glossary item etc) [required]
     *            area    => string comment area
     *            cm      => stdClass course module
     *            course  => course course object
     *            client_id => string an unique id to identify comment area
     *            autostart => boolean automatically expend comments
     *            showcount => boolean display the number of comments
     *            displaycancel => boolean display cancel button
     *            notoggle => boolean don't show/hide button
     *            linktext => string title of show/hide button
     * }
     */
    public function __construct(stdClass $options) {
        $this->viewcap = false;
        $this->postcap = false;

        if (!empty($options->client_id)) {
            $this->cid = $options->client_id;
        } else {
            $this->cid = uniqid();
        }

        // Setup context.
        if (!empty($options->context) && $options->context instanceof context) {
            $this->context = $options->context;
            $this->contextid = $this->context->id;
        } else if (!empty($options->context) && !($options->context instanceof context)) {
            $this->context = context::instance_by_id($this->context->id);
            $this->contextid = $options->context->id;
        } else if (!empty($options->contextid)) {
            $this->contextid = $options->contextid;
            $this->context = context::instance_by_id($this->contextid);
        } else {
            throw new \moodle_exception('invalidcontext');
        }

        if (!empty($options->component)) {
            $this->set_component($options->component);
        } else {
            throw new comment_exception('invalidcomponent');
        }

        // Setup course: course will be used to generate user profile link.
        if (!empty($options->course)) {
            $this->courseid = $options->course->id;
        } else if (!empty($options->courseid)) {
            $this->courseid = $options->courseid;
        } else {
            if ($coursecontext = $this->context->get_course_context(false)) {
                $this->courseid = $coursecontext->instanceid;
            } else {
                $this->courseid = SITEID;
            }
        }

        if (!empty($options->cm)) {
            $this->cm = $options->cm;
        }

        if (!empty($options->area)) {
            $this->commentarea = $options->area;
        }

        if (!empty($options->itemid)) {
            $this->itemid = $options->itemid;
        } else {
            $this->itemid = 0;
        }

        if (!empty($options->linktext)) {
            $this->linktext = $options->linktext;
        } else {
            $this->linktext = get_string('comments');
        }

        // Setup options for callback functions.
        $this->commentparams = (object) [
            'context' => $this->context,
            'courseid' => $this->courseid,
            'cm' => $this->cm,
            'commentarea' => $this->commentarea,
            'itemid' => $this->itemid,
        ];

        if (!empty($options->notoggle)) {
            $this->set_notoggle($options->notoggle);
        }

        if (!empty($options->autostart)) {
            $this->set_autostart($options->autostart);
        }

        if (!empty($options->displaycancel)) {
            $this->set_displaycancel($options->displaycancel);
        }

        if (!empty($options->showcount)) {
            $this->set_displaytotalcount($options->showcount);
        }

        // Setting post and view permissions.
        $this->check_permissions();

        // Generate HTML for the template.
        $this->template = html_writer::start_tag('div', ['class' => 'comment-message']);

        $this->template .= html_writer::start_tag('div', ['class' => 'comment-message-meta me-3']);

        $this->template .= html_writer::tag('span', '___picture___', ['class' => 'picture']);
        $this->template .= html_writer::tag('span', '___name___', ['class' => 'user']) . ' - ';
        $this->template .= html_writer::tag('span', '___time___', ['class' => 'time']);

        $this->template .= html_writer::end_tag('div'); // End .comment-message-meta.
        $this->template .= html_writer::tag('div', '___content___', ['class' => 'text']);

        $this->template .= html_writer::end_tag('div'); // End .comment-message.

        if (!empty($this->plugintype)) {
            $this->template = plugin_callback(
                $this->plugintype,
                $this->pluginname,
                'comment',
                'template',
                [$this->commentparams],
                $this->template,
            );
        }

        unset($options);
    }

    /**
     * Receive nonjs comment parameters
     *
     * @param moodle_page|null $page The page object to initialise comments within
     *                          If not provided the global $PAGE is used
     */
    public static function init(?moodle_page $page = null) {
        global $PAGE;

        if (empty($page)) {
            $page = $PAGE;
        }
        // Setup variables for non-js interface.
        self::$nonjs = optional_param('nonjscomment', '', PARAM_ALPHANUM);
        self::$nojscommentitemid = (int) optional_param('comment_itemid', '', PARAM_INT);
        self::$nojscommentcomponent = optional_param('comment_component', '', PARAM_COMPONENT);
        self::$nojscommentcontext = (int) optional_param('comment_context', '', PARAM_INT);
        self::$nojscommentpage = (int) optional_param('comment_page', '', PARAM_INT);
        self::$nojscommentarea = optional_param('comment_area', '', PARAM_AREA);

        $page->requires->strings_for_js(
            [
                'addcomment',
                'comments',
                'commentsrequirelogin',
                'deletecommentbyon',
            ],
            'moodle',
        );
    }

    /**
     * Sets the component.
     *
     * This method shouldn't be public, changing the component once it has been set potentially
     * invalidates permission checks.
     * A coding_error is now thrown if code attempts to change the component.
     *
     * @throws coding_exception if you try to change the component after it has been set.
     * @param string $component
     */
    public function set_component($component) {
        if (!empty($this->component) && $this->component !== $component) {
            throw new coding_exception('You cannot change the component of a comment once it has been set');
        }
        $this->component = $component;
        [$this->plugintype, $this->pluginname] = core_component::normalize_component($component);
    }

    /**
     * Determines if the user can view the comment.
     *
     * @param bool $value
     */
    public function set_view_permission($value) {
        $this->viewcap = (bool)$value;
    }

    /**
     * Determines if the user can post a comment
     *
     * @param bool $value
     */
    public function set_post_permission($value) {
        $this->postcap = (bool)$value;
    }

    /**
     * check posting comments permission
     * It will check based on user roles and ask modules
     * If you need to check permission by modules, a
     * function named $pluginname_check_comment_post must be implemented
     */
    private function check_permissions() {
        $this->postcap = has_capability('moodle/comment:post', $this->context);
        $this->viewcap = has_capability('moodle/comment:view', $this->context);
        if (!empty($this->plugintype)) {
            $permissions = plugin_callback(
                $this->plugintype,
                $this->pluginname,
                'comment',
                'permissions',
                [$this->commentparams],
                ['post' => false, 'view' => false],
            );
            $this->postcap = $this->postcap && $permissions['post'];
            $this->viewcap = $this->viewcap && $permissions['view'];
        }
    }

    /**
     * Gets a link for this page that will work with JS disabled.
     *
     * @param moodle_page|null $page
     * @return moodle_url
     */
    public function get_nojslink(?moodle_page $page = null) {
        if ($page === null) {
            global $PAGE;
            $page = $PAGE;
        }

        $link = new moodle_url(
            $page->url,
            [
                'nonjscomment'    => true,
                'comment_itemid'  => $this->itemid,
                'commentcontext' => $this->context->id,
                'comment_component' => $this->get_component(),
                'comment_area'    => $this->commentarea,
            ],
        );
        $link->remove_params(['comment_page']);
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
     * Sets the displaytotalcount option
     *
     * If set to true then the total number of comments will be displayed
     * when printing comments.
     *
     * @param bool $newvalue
     */
    public function set_displaytotalcount($newvalue = true) {
        $this->displaytotalcount = (bool)$newvalue;
    }

    /**
     * Initialises the JavaScript that enchances the comment API.
     *
     * @param moodle_page $page The moodle page object that the JavaScript should be
     *                          initialised for.
     */
    public function initialise_javascript(moodle_page $page) {

        $options = (object)[
            'client_id' => $this->cid,
            'commentarea' => $this->commentarea,
            'itemid' => $this->itemid,
            'page' => 0,
            'courseid' => $this->courseid,
            'contextid' => $this->contextid,
            'component' => $this->component,
            'notoggle' => $this->notoggle,
            'autostart' => $this->autostart,
        ];

        $page->requires->js_init_call('M.core_comment.init', [$options], true);

        return true;
    }

    /**
     * Prepare comment code in html
     * @param  boolean $return
     * @return string|void
     */
    public function output($return = true) {
        global $PAGE, $OUTPUT;
        static $templateprinted;

        $this->initialise_javascript($PAGE);

        if (!empty(self::$nonjs)) {
            // Return non js comments interface.
            return $this->print_comments(self::$nojscommentpage, $return, true);
        }

        $html = '';

        // Javascript will use the template to render new comments.
        if (empty($templateprinted) && $this->can_view()) {
            $html .= html_writer::tag('div', $this->template, ['style' => 'display:none', 'id' => 'cmt-tmpl']);
            $templateprinted = true;
        }

        if ($this->can_view()) {
            // Print commenting icon and tooltip.
            $html .= html_writer::start_tag('div', ['class' => 'mdl-left']);
            $html .= html_writer::link(
                $this->get_nojslink($PAGE),
                get_string('showcommentsnonjs'),
                ['class' => 'showcommentsnonjs'],
            );

            if (!$this->notoggle) {
                // If toggling is enabled (notoggle=false) then print the controls to toggle
                // comments open and closed.
                $countstring = '';
                if ($this->displaytotalcount) {
                    $countstring = '(' . html_writer::span($this->count(), 'comment-link-count') . ')';
                }
                $collapsedimage = 't/collapsed';
                if (right_to_left()) {
                    $collapsedimage = 't/collapsed_rtl';
                }
                $html .= html_writer::start_tag('a', [
                    'class' => 'comment-link',
                    'id' => 'comment-link-' . $this->cid,
                    'href' => '#',
                    'role' => 'button',
                    'aria-expanded' => 'false',
                ]);
                $html .= html_writer::img(
                    $OUTPUT->image_url($collapsedimage),
                    $this->linktext,
                    ['id' => 'comment-img-' . $this->cid, 'class' => 'icon'],
                );
                $html .= html_writer::tag(
                    'span',
                    $this->linktext . ' ' . $countstring,
                    ['id' => 'comment-link-text-' . $this->cid],
                );
                $html .= html_writer::end_tag('a');
            }

            $html .= html_writer::start_tag(
                'div',
                ['id' => 'comment-ctrl-' . $this->cid,
                'class' => 'comment-ctrl'],
            );

            if ($this->autostart) {
                // If autostart has been enabled print the comments list immediatly.
                $html .= html_writer::start_tag(
                    'ul',
                    ['id' => 'comment-list-' . $this->cid, 'class' => 'comment-list comments-loaded'],
                );
                $html .= html_writer::tag('li', '', ['class' => 'first']);
                $html .= $this->print_comments(0, true, false);
                $html .= html_writer::end_tag('ul'); // End .comment-list.
                $html .= $this->get_pagination(0);
            } else {
                $html .= html_writer::start_tag('ul', ['id' => 'comment-list-' . $this->cid, 'class' => 'comment-list']);
                $html .= html_writer::tag('li', '', ['class' => 'first']);
                $html .= html_writer::end_tag('ul'); // End .comment-list.
                $html .= html_writer::tag(
                    'div',
                    '',
                    ['id' => 'comment-pagination-' . $this->cid, 'class' => 'comment-pagination'],
                );
            }

            if ($this->can_post()) {
                // Print posting textarea.
                $textareaattrs = [
                    'name' => 'content',
                    'rows' => 2,
                    'id' => 'dlg-content-' . $this->cid,
                    'aria-label' => get_string('addcomment'),
                ];
                if (!$this->fullwidth) {
                    $textareaattrs['cols'] = '20';
                } else {
                    $textareaattrs['class'] = 'fullwidth';
                }

                $html .= html_writer::start_tag('div', ['class' => 'comment-area']);
                $html .= html_writer::start_tag('div', ['class' => 'db']);
                $html .= html_writer::tag(
                    'label',
                    get_string('comment', 'comment'),
                    ['for' => 'dlg-content-' . $this->cid, 'class' => 'visually-hidden'],
                );
                $html .= html_writer::tag('textarea', '', $textareaattrs);
                $html .= html_writer::end_tag('div'); // End .db.

                $html .= html_writer::start_tag('div', ['class' => 'fd', 'id' => 'comment-action-' . $this->cid]);
                $html .= html_writer::link('#', get_string('savecomment'), ['id' => 'comment-action-post-' . $this->cid]);

                if ($this->displaycancel) {
                    $html .= html_writer::tag('span', ' | ');
                    $html .= html_writer::link('#', get_string('cancel'), ['id' => 'comment-action-cancel-' . $this->cid]);
                }

                $html .= html_writer::end_tag('div'); // End .fd.
                $html .= html_writer::end_tag('div'); // End .comment-area.
                $html .= html_writer::tag('div', '', ['class' => 'clearer']);
            }

            $html .= html_writer::end_tag('div'); // End .comment-ctrl.
            $html .= html_writer::end_tag('div'); // End .mdl-left.
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
     * @param  string $sortdirection sort direction, ASC or DESC
     * @return array|false
     */
    public function get_comments($page = '', $sortdirection = 'DESC') {
        global $DB, $CFG, $OUTPUT;
        if (!$this->can_view()) {
            return false;
        }
        if (!is_numeric($page)) {
            $page = 0;
        }
        $params = [];
        $perpage = (!empty($CFG->commentsperpage)) ? $CFG->commentsperpage : 15;
        $start = $page * $perpage;
        $userfieldsapi = \core_user\fields::for_userpic();
        $ufields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;

        [$componentwhere, $component] = $this->get_component_select_sql('c');
        if ($component) {
            $params['component'] = $component;
        }

        $sortdirection = ($sortdirection === 'ASC') ? 'ASC' : 'DESC';
        $sql = "SELECT $ufields, c.id AS cid, c.content AS ccontent, c.format AS cformat, c.timecreated AS ctimecreated
                  FROM {comments} c
                  JOIN {user} u ON u.id = c.userid
                 WHERE c.contextid = :contextid AND
                       c.commentarea = :commentarea AND
                       c.itemid = :itemid AND
                       $componentwhere
              ORDER BY c.timecreated $sortdirection, c.id $sortdirection";
        $params['contextid'] = $this->contextid;
        $params['commentarea'] = $this->commentarea;
        $params['itemid'] = $this->itemid;

        $comments = [];
        $formatoptions = ['overflowdiv' => true, 'blanktarget' => true];
        $rs = $DB->get_recordset_sql($sql, $params, $start, $perpage);
        foreach ($rs as $u) {
            $c = new stdClass();
            $c->id          = $u->cid;
            $c->content     = $u->ccontent;
            $c->format      = $u->cformat;
            $c->timecreated = $u->ctimecreated;
            $c->strftimeformat = get_string('strftimerecentfull', 'langconfig');
            $url = new moodle_url('/user/view.php', ['id' => $u->id, 'course' => $this->courseid]);
            $c->profileurl = $url->out(false); // URL should not be escaped just yet.
            $c->fullname = fullname($u);
            $c->time = userdate($c->timecreated, $c->strftimeformat);
            $c->content = format_text($c->content, $c->format, $formatoptions);
            $c->avatar = $OUTPUT->user_picture($u, ['size' => 16]);
            $c->userid = $u->id;

            if ($this->can_delete($c)) {
                $c->delete = true;
            }
            $comments[] = $c;
        }
        $rs->close();

        if (!empty($this->plugintype)) {
            // Moodle module will filter comments.
            $comments = plugin_callback(
                $this->plugintype,
                $this->pluginname,
                'comment',
                'display',
                [$comments, $this->commentparams],
                $comments,
            );
        }

        return $comments;
    }

    /**
     * Returns an SQL fragment and param for selecting on component.
     *
     * @param string $alias
     * @return array
     */
    protected function get_component_select_sql($alias = '') {
        $component = $this->get_component();
        if ($alias) {
            $alias = $alias . '.';
        }
        if (empty($component)) {
            $componentwhere = "{$alias}component IS NULL";
            $component = null;
        } else {
            $componentwhere = "({$alias}component IS NULL OR {$alias}component = :component)";
        }
        return [$componentwhere, $component];
    }

    /**
     * Returns the number of comments associated with the details of this object
     *
     * @return int
     */
    public function count() {
        global $DB;
        if ($this->totalcommentcount === null) {
            [$where, $component] = $this->get_component_select_sql();
            $where .= ' AND itemid = :itemid AND commentarea = :commentarea AND contextid = :contextid';
            $params = [
                'itemid' => $this->itemid,
                'commentarea' => $this->commentarea,
                'contextid' => $this->context->id,
            ];
            if ($component) {
                $params['component'] = $component;
            }

            $this->totalcommentcount = $DB->count_records_select('comments', $where, $params);
        }
        return $this->totalcommentcount;
    }

    /**
     * Returns HTML to display a pagination bar
     *
     * @param int $page
     * @return string
     */
    public function get_pagination($page = 0) {
        global $CFG, $OUTPUT;
        $count = $this->count();
        $perpage = (!empty($CFG->commentsperpage)) ? $CFG->commentsperpage : 15;
        $pages = (int)ceil($count / $perpage);
        if ($pages == 1 || $pages == 0) {
            return html_writer::tag('div', '', ['id' => 'comment-pagination-' . $this->cid, 'class' => 'comment-pagination']);
        }
        if (!empty(self::$nonjs)) {
            // Used in non-js interface.
            return $OUTPUT->paging_bar($count, $page, $perpage, $this->get_nojslink(), 'comment_page');
        } else {
            // Return ajax paging bar.
            $str = '';
            $str .= '<div class="comment-paging" id="comment-pagination-' . $this->cid . '">';
            for ($p = 0; $p < $pages; $p++) {
                if ($p == $page) {
                    $class = 'curpage';
                } else {
                    $class = 'pageno';
                }
                $str .= '<a href="#" class="' . $class . '" id="comment-page-' . $this->cid . '-' . $p . '">' . ($p + 1) . '</a> ';
            }
            $str .= '</div>';
        }
        return $str;
    }

    /**
     * Add a new comment
     *
     * @param string $content
     * @param int $format
     * @return stdClass
     */
    public function add($content, $format = FORMAT_MOODLE) {
        global $CFG, $DB, $USER, $OUTPUT;
        if (!$this->can_post()) {
            throw new comment_exception('nopermissiontocomment');
        }
        $now = time();
        $newcmt = (object)[
            'contextid' => $this->contextid,
            'commentarea' => $this->commentarea,
            'itemid' => $this->itemid,
            'component' => !empty($this->component) ? $this->component : null,
            'content' => $content,
            'format' => $format,
            'userid' => $USER->id,
            'timecreated' => $now,
        ];

        // This callback allow module to modify the content of comment, such as filter or replacement.
        plugin_callback($this->plugintype, $this->pluginname, 'comment', 'add', [&$newcmt, $this->commentparams]);

        $cmtid = $DB->insert_record('comments', $newcmt);
        if (!empty($cmtid)) {
            $newcmt->id = $cmtid;
            $newcmt->strftimeformat = get_string('strftimerecentfull', 'langconfig');
            $newcmt->fullname = fullname($USER);
            $url = new moodle_url('/user/view.php', ['id' => $USER->id, 'course' => $this->courseid]);
            $newcmt->profileurl = $url->out();
            $formatoptions = ['overflowdiv' => true, 'blanktarget' => true];
            $newcmt->content = format_text($newcmt->content, $newcmt->format, $formatoptions);
            $newcmt->avatar = $OUTPUT->user_picture($USER, ['size' => 16]);

            $commentlist = [$newcmt];

            if (!empty($this->plugintype)) {
                // Call the display callback to allow the plugin to format the newly added comment.
                $commentlist = plugin_callback(
                    $this->plugintype,
                    $this->pluginname,
                    'comment',
                    'display',
                    [$commentlist, $this->commentparams],
                    $commentlist,
                );
                $newcmt = $commentlist[0];
            }
            $newcmt->time = userdate($newcmt->timecreated, $newcmt->strftimeformat);

            // Trigger comment created event.
            if (core_component::is_core_subsystem($this->component)) {
                $eventclassname = '\\core\\event\\' . $this->component . '_comment_created';
            } else {
                $eventclassname = '\\' . $this->component . '\\event\comment_created';
            }
            if (class_exists($eventclassname)) {
                $event = $eventclassname::create(
                    [
                        'context' => $this->context,
                        'objectid' => $newcmt->id,
                        'other' => [
                            'itemid' => $this->itemid,
                        ],
                    ],
                );
                $event->trigger();
            }

            return $newcmt;
        } else {
            throw new comment_exception('dbupdatefailed');
        }
    }

    /**
     * Delete by context, commentarea and itemid
     * @param stdClass|array $param {
     *            contextid => int the context in which the comments exist [required]
     *            commentarea => string the comment area [optional]
     *            itemid => int comment itemid [optional]
     * }
     * @return boolean
     */
    public static function delete_comments($param) {
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
     *
     * @param context $context course context
     */
    public static function reset_course_page_comments($context) {
        global $DB;
        $contexts = [];
        $contexts[] = $context->id;
        $children = $context->get_child_contexts();
        foreach ($children as $c) {
            $contexts[] = $c->id;
        }
        [$ids, $params] = $DB->get_in_or_equal($contexts);
        $DB->delete_records_select('comments', "commentarea='page_comments' AND contextid $ids", $params);
    }

    /**
     * Delete a comment
     *
     * @param  int|stdClass $comment The id of a comment, or a comment record.
     * @return bool
     */
    public function delete($comment) {
        global $DB;
        if (is_object($comment)) {
            $commentid = $comment->id;
        } else {
            $commentid = $comment;
            $comment = $DB->get_record('comments', ['id' => $commentid]);
        }

        if (!$comment) {
            throw new comment_exception('dbupdatefailed');
        }
        if (!$this->can_delete($comment)) {
            throw new comment_exception('nopermissiontocomment');
        }
        $DB->delete_records('comments', ['id' => $commentid]);
        // Trigger comment delete event.
        if (core_component::is_core_subsystem($this->component)) {
            $eventclassname = '\\core\\event\\' . $this->component . '_comment_deleted';
        } else {
            $eventclassname = '\\' . $this->component . '\\event\comment_deleted';
        }
        if (class_exists($eventclassname)) {
            $event = $eventclassname::create(
                [
                    'context' => $this->context,
                    'objectid' => $commentid,
                    'other' => [
                        'itemid' => $this->itemid,
                    ],
                ],
            );
            $event->add_record_snapshot('comments', $comment);
            $event->trigger();
        }
        return true;
    }

    /**
     * Print comments
     *
     * @param int $page
     * @param bool $return return comments list string or print it out
     * @param bool $nonjs print nonjs comments list or not?
     * @return string|void
     */
    public function print_comments($page = 0, $return = true, $nonjs = true) {
        global $DB, $CFG, $PAGE;

        if (!$this->can_view()) {
            return '';
        }

        if (
            self::$nojscommentitemid == $this->itemid
            || self::$nojscommentcontext == $this->context->id
            || self::$nojscommentarea == $this->commentarea
            || self::$nojscommentcomponent == $this->component
        ) {
            $page = 0;
        }
        $comments = $this->get_comments($page);

        $html = '';
        if ($nonjs) {
            $html .= html_writer::tag('h3', get_string('comments'));
            $html .= html_writer::start_tag('ul', ['id' => 'comment-list-' . $this->cid, 'class' => 'comment-list']);
        }
        // Reverse the comments array to display them in the correct direction.
        foreach (array_reverse($comments) as $cmt) {
            $html .= html_writer::tag('li', $this->print_comment($cmt, $nonjs), ['id' => 'comment-' . $cmt->id . '-' . $this->cid]);
        }
        if ($nonjs) {
            $html .= html_writer::end_tag('ul');
            $html .= $this->get_pagination($page);
        }
        if ($nonjs && $this->can_post()) {
            // Form to add comments.
            $html .= html_writer::start_tag('form', ['method' => 'post', 'action' => new moodle_url('/comment/comment_post.php')]);
            // Comment parameters.
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'contextid', 'value' => $this->contextid]);
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'add']);
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'area', 'value' => $this->commentarea]);
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'component', 'value' => $this->component]);
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'itemid', 'value' => $this->itemid]);
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'courseid', 'value' => $this->courseid]);
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
            $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'returnurl', 'value' => $PAGE->url]);
            // Textarea for the actual comment.
            $html .= html_writer::tag('textarea', '', ['name' => 'content', 'rows' => 2]);
            // Submit button to add the comment.
            $html .= html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('submit')]);
            $html .= html_writer::end_tag('form');
        }
        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

    /**
     * Returns an array containing comments in HTML format.
     *
     * @param stdClass $cmt {
     *          id => int comment id
     *          content => string comment content
     *          format  => int comment text format
     *          timecreated => int comment's timecreated
     *          profileurl  => string link to user profile
     *          fullname    => comment author's full name
     *          avatar      => string user's avatar
     *          delete      => boolean does user have permission to delete comment?
     * }
     * @param bool $nonjs
     * @return array
     */
    public function print_comment($cmt, $nonjs = true) {
        global $OUTPUT;
        $patterns = [];
        $replacements = [];

        if (!empty($cmt->delete) && empty($nonjs)) {
            $strdelete = get_string('deletecommentbyon', 'moodle', (object)['user' => $cmt->fullname, 'time' => $cmt->time]);
            $deletelink  = html_writer::start_tag('div', ['class' => 'comment-delete']);
            $deletelink .= html_writer::start_tag(
                'a',
                [
                    'href' => '#',
                    'id' => 'comment-delete-' . $this->cid . '-' . $cmt->id,
                    'class' => 'icon-no-margin',
                    'title' => $strdelete,
                ],
            );

            $deletelink .= $OUTPUT->pix_icon('t/delete', $strdelete);
            $deletelink .= html_writer::end_tag('a');
            $deletelink .= html_writer::end_tag('div');
            $cmt->content = $deletelink . $cmt->content;
        }
        $patterns[] = '___picture___';
        $patterns[] = '___name___';
        $patterns[] = '___content___';
        $patterns[] = '___time___';
        $replacements[] = $cmt->avatar;
        $replacements[] = html_writer::link($cmt->profileurl, $cmt->fullname);
        $replacements[] = $cmt->content;
        $replacements[] = $cmt->time;

        // Use html template to format a single comment.
        return str_replace($patterns, $replacements, $this->template);
    }

    /**
     * Revoke validate callbacks
     *
     * @param array $params addtionall parameters need to add to callbacks
     */
    protected function validate($params = []) {
        foreach ($params as $key => $value) {
            $this->commentparams->$key = $value;
        }
        $validation = plugin_callback(
            $this->plugintype,
            $this->pluginname,
            'comment',
            'validate',
            [$this->commentparams],
            false,
        );
        if (!$validation) {
            throw new comment_exception('invalidcommentparam');
        }
    }

    /**
     * Returns true if the user is able to view comments
     * @return bool
     */
    public function can_view() {
        $this->validate();
        return !empty($this->viewcap);
    }

    /**
     * Returns true if the user can add comments against this comment description
     * @return bool
     */
    public function can_post() {
        $this->validate();
        return isloggedin() && !empty($this->postcap);
    }

    /**
     * Returns true if the user can delete this comment.
     *
     * The user can delete comments if it is one they posted and they can still make posts,
     * or they have the capability to delete comments.
     *
     * A database call is avoided if a comment record is passed.
     *
     * @param int|stdClass $comment The id of a comment, or a comment record.
     * @return bool
     */
    public function can_delete($comment) {
        global $USER, $DB;
        if (is_object($comment)) {
            $commentid = $comment->id;
        } else {
            $commentid = $comment;
        }

        $this->validate(['commentid' => $commentid]);

        if (!is_object($comment)) {
            // Get the comment record from the database.
            $comment = $DB->get_record('comments', ['id' => $commentid], 'id, userid', MUST_EXIST);
        }

        $hascapability = has_capability('moodle/comment:delete', $this->context);
        $owncomment = $USER->id == $comment->userid;

        return ($hascapability || ($owncomment && $this->can_post()));
    }

    /**
     * Get comments created since a given time.
     *
     * @param  stdClass $course    course object
     * @param  stdClass $context   context object
     * @param  string $component   component name
     * @param  int $since          the time to check
     * @param  \cm_info|null $cm optional course module object
     * @return array list of comments db records since the given timelimit
     * @since Moodle 3.2
     */
    public static function get_component_comments_since(
        stdClass $course,
        stdClass $context,
        string $component,
        int $since,
        ?\cm_info $cm = null
    ): array {
        global $DB;

        $result = [];
        $where = 'contextid = ? AND component = ? AND timecreated > ?';
        $comments = $DB->get_records_select('comments', $where, [$context->id, $component, $since]);

        // Check item by item if we have permissions.
        $managersviewstatus = [];
        foreach ($comments as $comment) {
            $cachedkey = $comment->commentarea . '/' . $comment->itemid;

            if (!isset($managersviewstatus[$cachedkey])) {
                $args = (object)[
                    'area' => $comment->commentarea,
                    'itemid' => $comment->itemid,
                    'context' => $context,
                    'course' => $course,
                    'client_id' => 0,
                    'component' => $component,
                    'cm' => $cm,
                ];
                $manager = new self($args);
                $managersviewstatus[$cachedkey] = $manager->can_view();
            }

            if ($managersviewstatus[$cachedkey]) {
                $result[$comment->id] = $comment;
            }
        }
        return $result;
    }

    /**
     * Returns the component associated with the comment.
     *
     * @return string
     */
    public function get_component() {
        return $this->component;
    }

    /**
     * Do not call! I am a deprecated method because of the typo in my name.
     *
     * @deprecated since 2.9
     * @see comment::get_component()
     * @return string
     */
    public function get_compontent() {
        return $this->get_component();
    }

    /**
     * Returns the context associated with the comment
     *
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Returns the course id associated with the comment
     * @return int
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Returns the course module associated with the comment
     *
     * @return stdClass
     */
    public function get_cm() {
        return $this->cm;
    }

    /**
     * Returns the item id associated with the comment
     *
     * @return int
     */
    public function get_itemid() {
        return $this->itemid;
    }

    /**
     * Returns the comment area associated with the commentarea
     *
     * @return string
     */
    public function get_commentarea() {
        return $this->commentarea;
    }

    /**
     * Make the comments textarea fullwidth.
     *
     * @since 2.8.1 + 2.7.4
     * @param bool $fullwidth
     */
    public function set_fullwidth($fullwidth = true) {
        $this->fullwidth = (bool)$fullwidth;
    }

    /**
     * Return the template.
     *
     * @since 3.1
     * @return string
     */
    public function get_template() {
        return $this->template;
    }

    /**
     * Return the cid.
     *
     * @since 3.1
     * @return string
     */
    public function get_cid() {
        return $this->cid;
    }

    /**
     * Return the link text.
     *
     * @since 3.1
     * @return string
     */
    public function get_linktext() {
        return $this->linktext;
    }

    /**
     * Return no toggle.
     *
     * @since 3.1
     * @return bool
     */
    public function get_notoggle() {
        return $this->notoggle;
    }

    /**
     * Return display total count.
     *
     * @since 3.1
     * @return bool
     */
    public function get_displaytotalcount() {
        return $this->displaytotalcount;
    }

    /**
     * Return display cancel.
     *
     * @since 3.1
     * @return bool
     */
    public function get_displaycancel() {
        return $this->displaycancel;
    }

    /**
     * Return fullwidth.
     *
     * @since 3.1
     * @return bool
     */
    public function get_fullwidth() {
        return $this->fullwidth;
    }

    /**
     * Return autostart.
     *
     * @since 3.1
     * @return bool
     */
    public function get_autostart() {
        return $this->autostart;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manager::class, \comment::class);
