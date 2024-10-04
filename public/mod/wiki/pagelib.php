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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains several classes uses to render the diferent pages
 * of the wiki module
 *
 * @package mod_wiki
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Daniel Serrano
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/wiki/edit_form.php');

/**
 * Class page_wiki contains the common code between all pages
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class page_wiki {

    /**
     * @var object Current subwiki
     */
    protected $subwiki;

    /**
     * @var int Current page
     */
    protected $page;

    /**
     * @var string Current page title
     */
    protected $title;

    /**
     * @var int Current group ID
     */
    protected $gid;

    /**
     * @var object module context object
     */
    protected $modcontext;

    /**
     * @var int Current user ID
     */
    protected $uid;
    /**
     * @var array The tabs set used in wiki module
     */
    protected $tabs = array('view' => 'view', 'edit' => 'edit', 'comments' => 'comments',
                            'history' => 'history', 'map' => 'map', 'files' => 'files',
                            'admin' => 'admin');
    /**
     * @var array tabs options
     */
    protected $tabs_options = array();
    /**
     * @var mod_wiki_renderer wiki renderer
     */
    protected $wikioutput;
    /**
     * @var stdClass course module.
     */
    protected $cm;

    /**
     * The page_wiki constructor.
     *
     * @param stdClass $wiki Current wiki
     * @param stdClass $subwiki Current subwiki.
     * @param stdClass $cm Current course_module.
     * @param string|null $activesecondarytab Secondary navigation node to be activated on the page, if required
     */
    public function __construct($wiki, $subwiki, $cm, ?string $activesecondarytab = null) {
        global $PAGE, $CFG;
        $this->subwiki = $subwiki;
        $this->cm = $cm;
        $this->modcontext = context_module::instance($this->cm->id);

        // initialise wiki renderer
        $this->wikioutput = $PAGE->get_renderer('mod_wiki');
        $PAGE->set_cacheable(true);
        $PAGE->set_cm($cm);
        $PAGE->set_activity_record($wiki);
        if ($activesecondarytab) {
            $PAGE->set_secondary_active_tab($activesecondarytab);
        }
        $PAGE->add_body_class('limitedwidth');
        // the search box
        if (!empty($subwiki->id)) {
            $search = optional_param('searchstring', null, PARAM_TEXT);
            $PAGE->set_button(wiki_search_form($cm, $search, $subwiki));
        }
    }

    /**
     * This method prints the top of the page.
     */
    function print_header() {
        global $OUTPUT, $PAGE, $SESSION;

        $PAGE->set_heading($PAGE->course->fullname);

        $this->set_url();
        if (isset($SESSION->wikipreviousurl) && is_array($SESSION->wikipreviousurl)) {
            $this->process_session_url();
        }
        $this->set_session_url();

        $this->create_navbar();

        echo $OUTPUT->header();

        if (!empty($this->page)) {
            echo $this->action_bar($this->page->id, $PAGE->url);
        }
    }

    /**
     * This method returns the action bar.
     *
     * @param int $pageid The page id.
     * @param moodle_url $pageurl The page url.
     * @return string The HTML for the action bar.
     */
    protected function action_bar(int $pageid, moodle_url $pageurl): string {
        $actionbar = new \mod_wiki\output\action_bar($pageid, $pageurl);
        return $this->wikioutput->render_action_bar($actionbar);
    }

    /**
     * Protected method to print current page title.
     */
    protected function print_pagetitle() {
        global $OUTPUT;
        $html = '';

        $html .= $OUTPUT->container_start('wiki_headingtitle');
        $html .= $OUTPUT->heading(format_string($this->title), 3);
        $html .= $OUTPUT->container_end();
        echo $html;
    }

    /**
     * Setup page tabs, if options is empty, will set up active tab automatically
     * @param array $options, tabs options
     */
    protected function setup_tabs($options = array()) {
        global $CFG, $PAGE;
        $groupmode = groups_get_activity_groupmode($this->cm);

        if (empty($CFG->usecomments) || !has_capability('mod/wiki:viewcomment', $PAGE->context)){
            unset($this->tabs['comments']);
        }

        if (!has_capability('mod/wiki:editpage', $PAGE->context)){
            unset($this->tabs['edit']);
        }

        if ($groupmode and $groupmode == VISIBLEGROUPS) {
            $currentgroup = groups_get_activity_group($this->cm);
            $manage = has_capability('mod/wiki:managewiki', $this->modcontext);
            $edit = has_capability('mod/wiki:editpage', $PAGE->context);
            if (!$manage and !($edit and groups_is_member($currentgroup))) {
                unset($this->tabs['edit']);
            }
        }

        if (empty($options)) {
            $this->tabs_options = array('activetab' => substr(get_class($this), 10));
        } else {
            $this->tabs_options = $options;
        }

    }

    /**
     * This method must be overwritten to print the page content.
     */
    function print_content() {
        throw new coding_exception('Page wiki class does not implement method print_content()');
    }

    /**
     * Method to set the current page
     *
     * @param object $page Current page
     */
    function set_page($page) {
        global $PAGE;

        $this->page = $page;
        $this->title = $page->title;
        // set_title calls format_string itself so no probs there
        $PAGE->set_title($this->title);
    }

    /**
     * Method to set the current page title.
     * This method must be called when the current page is not created yet.
     * @param string $title Current page title.
     */
    function set_title($title) {
        global $PAGE;

        $this->page = null;
        $this->title = $title;
        // set_title calls format_string itself so no probs there
        $PAGE->set_title($this->title);
    }

    /**
     * Method to set current group id
     * @param int $gid Current group id
     */
    function set_gid($gid) {
        $this->gid = $gid;
    }

    /**
     * Method to set current user id
     * @param int $uid Current user id
     */
    function set_uid($uid) {
        $this->uid = $uid;
    }

    /**
     * Method to set the URL of the page.
     * This method must be overwritten by every type of page.
     */
    protected function set_url() {
        throw new coding_exception('Page wiki class does not implement method set_url()');
    }

    /**
     * Protected method to create the common items of the navbar in every page type.
     */
    protected function create_navbar() {
        global $PAGE, $CFG;

        $PAGE->navbar->add(format_string($this->title), $CFG->wwwroot . '/mod/wiki/view.php?pageid=' . $this->page->id);
    }

    /**
     * This method print the footer of the page.
     */
    function print_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    protected function process_session_url() {
        global $USER, $SESSION;

        //delete locks if edit
        $url = $SESSION->wikipreviousurl;
        switch ($url['page']) {
        case 'edit':
            wiki_delete_locks($url['params']['pageid'], $USER->id, $url['params']['section'], false);
            break;
        }
    }

    protected function set_session_url() {
        global $SESSION;
        unset($SESSION->wikipreviousurl);
    }

}

/**
 * View a wiki page
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_wiki_view extends page_wiki {

    function print_header() {
        global $PAGE;

        parent::print_header();

        $this->wikioutput->wiki_print_subwiki_selector($PAGE->activityrecord, $this->subwiki, $this->page, 'view');

        //echo $this->wikioutput->page_index();

        $this->print_pagetitle();
    }

    /**
     * This method returns the action bar.
     *
     * @param int $pageid The page id.
     * @param moodle_url $pageurl The page url.
     * @return string The HTML for the action bar.
     */
    protected function action_bar(int $pageid, moodle_url $pageurl): string {
        $actionbar = new \mod_wiki\output\action_bar($pageid, $pageurl, true);
        return $this->wikioutput->render_action_bar($actionbar);
    }

    function print_content() {
        global $PAGE, $CFG;

        if (wiki_user_can_view($this->subwiki)) {

            if (!empty($this->page)) {
                wiki_print_page_content($this->page, $this->modcontext, $this->subwiki->id);
                $wiki = $PAGE->activityrecord;
            } else {
                print_string('nocontent', 'wiki');
                // TODO: fix this part
                $swid = 0;
                if (!empty($this->subwiki)) {
                    $swid = $this->subwiki->id;
                }
            }
        } else {
            echo get_string('cannotviewpage', 'wiki');
        }
    }

    function set_url() {
        global $PAGE, $CFG;
        $params = array();

        if (isset($this->cm->id)) {
            $params['id'] = $this->cm->id;
        } else if (!empty($this->page) and $this->page != null) {
            $params['pageid'] = $this->page->id;
        } else if (!empty($this->gid)) {
            $params['wid'] = $this->cm->instance;
            $params['group'] = $this->gid;
        } else if (!empty($this->title)) {
            $params['swid'] = $this->subwiki->id;
            $params['title'] = $this->title;
        } else {
            throw new \moodle_exception(get_string('invalidparameters', 'wiki'));
        }
        $PAGE->set_url(new moodle_url($CFG->wwwroot . '/mod/wiki/view.php', $params));
    }

    protected function create_navbar() {
        global $PAGE;

        $PAGE->navbar->add(format_string($this->title));
        $PAGE->navbar->add(get_string('view', 'wiki'));
    }
}

/**
 * Wiki page editing page
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_wiki_edit extends page_wiki {

    public static $attachmentoptions;

    protected $sectioncontent;
    /** @var string the section name needed to be edited */
    protected $section;
    protected $overridelock = false;
    protected $versionnumber = -1;
    protected $upload = false;
    protected $attachments = 0;
    protected $deleteuploads = array();
    protected $format;

    /**
     * The page_wiki_edit constructor.
     *
     * @param stdClass $wiki Current wiki
     * @param stdClass $subwiki Current subwiki.
     * @param stdClass $cm Current course_module.
     * @param string|null $activesecondarytab Secondary navigation node to be activated on the page, if required
     */
    public function __construct($wiki, $subwiki, $cm, ?string $activesecondarytab = null) {
        global $CFG, $PAGE;
        parent::__construct($wiki, $subwiki, $cm, $activesecondarytab);
        $showfilemanager = false;
        if (has_capability('mod/wiki:managefiles', context_module::instance($cm->id))) {
            $showfilemanager = true;
        }
        self::$attachmentoptions = array('subdirs' => false, 'maxfiles' => - 1, 'maxbytes' => $CFG->maxbytes,
                'accepted_types' => '*', 'enable_filemanagement' => $showfilemanager);
        $PAGE->requires->js_init_call('M.mod_wiki.renew_lock', null, true);
    }

    protected function print_pagetitle() {
        global $OUTPUT;

        $title = $this->title;
        if (isset($this->section)) {
            $title .= ' : ' . $this->section;
        }
        echo $OUTPUT->container_start('wiki_clear wiki_headingtitle');
        echo $OUTPUT->heading(format_string($title), 3);
        echo $OUTPUT->container_end();
    }

    function print_header() {
        global $OUTPUT, $PAGE;
        $PAGE->requires->data_for_js('wiki', array('renew_lock_timeout' => LOCK_TIMEOUT - 5, 'pageid' => $this->page->id, 'section' => $this->section));

        parent::print_header();

        $this->print_pagetitle();

        print '<noscript>' . $OUTPUT->box(get_string('javascriptdisabledlocks', 'wiki'), 'errorbox') . '</noscript>';
    }

    function print_content() {
        global $PAGE;

        if (wiki_user_can_edit($this->subwiki)) {
            $this->print_edit();
        } else {
            echo get_string('cannoteditpage', 'wiki');
        }
    }

    protected function set_url() {
        global $PAGE, $CFG;

        $params = array('pageid' => $this->page->id);

        if (isset($this->section)) {
            $params['section'] = $this->section;
        }

        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/edit.php', $params);
    }

    protected function set_session_url() {
        global $SESSION;

        $SESSION->wikipreviousurl = array('page' => 'edit', 'params' => array('pageid' => $this->page->id, 'section' => $this->section));
    }

    protected function process_session_url() {
    }

    function set_section($sectioncontent, $section) {
        $this->sectioncontent = $sectioncontent;
        $this->section = $section;
    }

    public function set_versionnumber($versionnumber) {
        $this->versionnumber = $versionnumber;
    }

    public function set_overridelock($override) {
        $this->overridelock = $override;
    }

    function set_format($format) {
        $this->format = $format;
    }

    public function set_upload($upload) {
        $this->upload = $upload;
    }

    public function set_attachments($attachments) {
        $this->attachments = $attachments;
    }

    public function set_deleteuploads($deleteuploads) {
        $this->deleteuploads = $deleteuploads;
    }

    protected function create_navbar() {
        global $PAGE, $CFG;

        parent::create_navbar();

        $PAGE->navbar->add(get_string('edit', 'wiki'));
    }

    protected function check_locks() {
        global $OUTPUT, $USER, $CFG;

        if (!wiki_set_lock($this->page->id, $USER->id, $this->section, true)) {
            print $OUTPUT->box(get_string('pageislocked', 'wiki'), 'generalbox boxwidthnormal boxaligncenter');

            if ($this->overridelock) {
                $params = 'pageid=' . $this->page->id;

                if ($this->section) {
                    $params .= '&section=' . urlencode($this->section);
                }

                $form = '<form method="post" action="' . $CFG->wwwroot . '/mod/wiki/overridelocks.php?' . $params . '">';
                $form .= '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
                $form .= '<input type="submit" class="btn btn-secondary" value="' . get_string('overridelocks', 'wiki') . '" />';
                $form .= '</form>';

                print $OUTPUT->box($form, 'generalbox boxwidthnormal boxaligncenter');
            }
            return false;
        }
        return true;
    }

    protected function print_edit($content = null) {
        global $CFG, $OUTPUT, $USER, $PAGE;

        if (!$this->check_locks()) {
            return;
        }

        //delete old locks (> 1 hour)
        wiki_delete_old_locks();

        $version = wiki_get_current_version($this->page->id);
        $format = $version->contentformat;

        if ($content == null) {
            if (empty($this->section)) {
                $content = $version->content;
            } else {
                $content = $this->sectioncontent;
            }
        }

        $versionnumber = $version->version;
        if ($this->versionnumber >= 0) {
            if ($version->version != $this->versionnumber) {
                print $OUTPUT->box(get_string('wrongversionlock', 'wiki'), 'errorbox');
                $versionnumber = $this->versionnumber;
            }
        }

        $url = $CFG->wwwroot . '/mod/wiki/edit.php?pageid=' . $this->page->id;
        if (!empty($this->section)) {
            $url .= "&section=" . urlencode($this->section);
        }

        $params = array(
            'attachmentoptions' => page_wiki_edit::$attachmentoptions,
            'format' => $version->contentformat,
            'version' => $versionnumber,
            'pagetitle' => $this->page->title,
            'contextid' => $this->modcontext->id
        );

        $data = new StdClass();
        $data->newcontent = $content;
        $data->version = $versionnumber;
        $data->format = $format;

        switch ($format) {
        case 'html':
            $data->newcontentformat = FORMAT_HTML;
            // Append editor context to editor options, giving preference to existing context.
            page_wiki_edit::$attachmentoptions = array_merge(array('context' => $this->modcontext), page_wiki_edit::$attachmentoptions);
            $data = file_prepare_standard_editor($data, 'newcontent', page_wiki_edit::$attachmentoptions, $this->modcontext, 'mod_wiki', 'attachments', $this->subwiki->id);
            break;
        default:
            break;
        }

        if ($version->contentformat != 'html') {
            $params['fileitemid'] = $this->subwiki->id;
            $params['component']  = 'mod_wiki';
            $params['filearea']   = 'attachments';
        }

        $data->tags = core_tag_tag::get_item_tags_array('mod_wiki', 'wiki_pages', $this->page->id);

        $form = new mod_wiki_edit_form($url, $params);
        $form->set_data($data);
        $form->display();
    }

}

/**
 * Class that models the behavior of wiki's view comments page
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_wiki_comments extends page_wiki {

    function print_header() {

        parent::print_header();

        $this->print_pagetitle();

    }

    function print_content() {
        global $CFG, $OUTPUT, $USER, $PAGE;
        require_once($CFG->dirroot . '/mod/wiki/locallib.php');

        $page = $this->page;
        $subwiki = $this->subwiki;
        $wiki = $PAGE->activityrecord;
        list($context, $course, $cm) = get_context_info_array($this->modcontext->id);

        require_capability('mod/wiki:viewcomment', $this->modcontext, NULL, true, 'noviewcommentpermission', 'wiki');

        $comments = wiki_get_comments($this->modcontext->id, $page->id);

        if (has_capability('mod/wiki:editcomment', $this->modcontext)) {
            echo '<div class="midpad"><a href="' . $CFG->wwwroot . '/mod/wiki/editcomments.php?action=add&amp;pageid=' . $page->id . '">' . get_string('addcomment', 'wiki') . '</a></div>';
        }

        $options = array('swid' => $this->page->subwikiid, 'pageid' => $page->id);
        $version = wiki_get_current_version($this->page->id);
        $format = $version->contentformat;

        if (empty($comments)) {
            echo html_writer::tag('p', get_string('nocomments', 'wiki'), array('class' => 'bold'));
        }

        foreach ($comments as $comment) {

            $user = wiki_get_user_info($comment->userid);

            $fullname = fullname($user, has_capability('moodle/site:viewfullnames', context_course::instance($course->id)));
            $by = new stdclass();
            $by->name = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&amp;course=' . $course->id . '">' . $fullname . '</a>';
            $by->date = userdate($comment->timecreated);

            $t = new html_table();
            $t->id = 'wiki-comments';
            $cell1 = new html_table_cell($OUTPUT->user_picture($user, array('popup' => true)));
            $cell2 = new html_table_cell(get_string('bynameondate', 'forum', $by));
            $cell3 = new html_table_cell();
            $cell4 = new html_table_cell();
            $cell5 = new html_table_cell();

            $row1 = new html_table_row();
            $row1->cells[] = $cell1;
            $row1->cells[] = $cell2;
            $row2 = new html_table_row();
            $row2->cells[] = $cell3;

            if ($format != 'html') {
                if ($format == 'creole') {
                    $parsedcontent = wiki_parse_content('creole', $comment->content, $options);
                } else if ($format == 'nwiki') {
                    $parsedcontent = wiki_parse_content('nwiki', $comment->content, $options);
                }

                $cell4->text = format_text(html_entity_decode($parsedcontent['parsed_text'], ENT_QUOTES, 'UTF-8'), FORMAT_HTML);
            } else {
                $cell4->text = format_text($comment->content, FORMAT_HTML);
            }

            $row2->cells[] = $cell4;

            $t->data = array($row1, $row2);

            $canedit = $candelete = false;
            if ((has_capability('mod/wiki:editcomment', $this->modcontext)) and ($USER->id == $user->id)) {
                $candelete = $canedit = true;
            }
            if ((has_capability('mod/wiki:managecomment', $this->modcontext))) {
                $candelete = true;
            }

            $editicon = $deleteicon = '';
            if ($canedit) {
                $urledit = new moodle_url('/mod/wiki/editcomments.php', array('commentid' => $comment->id, 'pageid' => $page->id, 'action' => 'edit'));
                $editicon = $OUTPUT->action_icon($urledit, new pix_icon('t/edit', get_string('edit'), '', array('class' => 'iconsmall')));
            }
            if ($candelete) {
                $urldelete = new moodle_url('/mod/wiki/instancecomments.php', array('commentid' => $comment->id, 'pageid' => $page->id, 'action' => 'delete'));
                $deleteicon = $OUTPUT->action_icon($urldelete,
                                                  new pix_icon('t/delete',
                                                               get_string('delete'),
                                                               '',
                                                               array('class' => 'iconsmall')));
            }

            if ($candelete || $canedit) {
                $cell6 = new html_table_cell($editicon.$deleteicon);
                $row3 = new html_table_row();
                $row3->cells[] = $cell5;
                $row3->cells[] = $cell6;
                $t->data[] = $row3;
            }

            echo html_writer::tag('div', html_writer::table($t), ['class' => 'table-responsive']);

        }
    }

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/comments.php', array('pageid' => $this->page->id));
    }

    protected function create_navbar() {
        global $PAGE, $CFG;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('comments', 'wiki'));
    }

}

/**
 * Class that models the behavior of wiki's edit comment
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_wiki_editcomment extends page_wiki {
    private $comment;
    private $action;
    private $form;
    private $format;

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/comments.php', array('pageid' => $this->page->id));
    }

    function print_header() {
        parent::print_header();
        $this->print_pagetitle();
    }

    function print_content() {
        global $PAGE;

        require_capability('mod/wiki:editcomment', $this->modcontext, NULL, true, 'noeditcommentpermission', 'wiki');

        if ($this->action == 'add') {
            $this->add_comment_form();
        } else if ($this->action == 'edit') {
            $this->edit_comment_form($this->comment);
        }
    }

    function set_action($action, $comment) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/wiki/comments_form.php');

        $this->action = $action;
        $this->comment = $comment;
        $version = wiki_get_current_version($this->page->id);
        $this->format = $version->contentformat;

        if ($this->format == 'html') {
            $destination = $CFG->wwwroot . '/mod/wiki/instancecomments.php?pageid=' . $this->page->id;
            $this->form = new mod_wiki_comments_form($destination);
        }
    }

    protected function create_navbar() {
        global $PAGE, $CFG;

        $PAGE->navbar->add(get_string('comments', 'wiki'), $CFG->wwwroot . '/mod/wiki/comments.php?pageid=' . $this->page->id);

        if ($this->action == 'add') {
            $PAGE->navbar->add(get_string('insertcomment', 'wiki'));
        } else {
            $PAGE->navbar->add(get_string('editcomment', 'wiki'));
        }
    }

    protected function setup_tabs($options = array()) {
        parent::setup_tabs(array('linkedwhenactive' => 'comments', 'activetab' => 'comments'));
    }

    /**
     * This method returns the action bar.
     *
     * @param int $pageid The page id.
     * @param moodle_url $pageurl The page url.
     * @return string The HTML for the action bar.
     */
    protected function action_bar(int $pageid, moodle_url $pageurl): string {
        // The given page does not require an action bar.
        return '';
    }

    private function add_comment_form() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/wiki/editors/wiki_editor.php');

        $pageid = $this->page->id;

        if ($this->format == 'html') {
            $com = new stdClass();
            $com->action = 'add';
            $com->commentoptions = array('trusttext' => true, 'maxfiles' => 0);
            $this->form->set_data($com);
            $this->form->display();
        } else {
            wiki_print_editor_wiki($this->page->id, null, $this->format, -1, null, false, null, 'addcomments');
        }
    }

    private function edit_comment_form($com) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/wiki/comments_form.php');
        require_once($CFG->dirroot . '/mod/wiki/editors/wiki_editor.php');

        if ($this->format == 'html') {
            $com->action = 'edit';
            $com->entrycomment_editor['text'] = clean_text($com->content, $this->format);
            $com->commentoptions = array('trusttext' => true, 'maxfiles' => 0);

            $this->form->set_data($com);
            $this->form->display();
        } else {
            wiki_print_editor_wiki($this->page->id, $com->content, $this->format, -1, null, false, array(), 'editcomments', $com->id);
        }

    }

}

/**
 * Wiki page search page
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_wiki_search extends page_wiki {
    private $search_result;

    protected function create_navbar() {
        global $PAGE, $CFG;

        $PAGE->navbar->add(format_string($this->title));
    }

    function set_search_string($search, $searchcontent) {
        $swid = $this->subwiki->id;
        if ($searchcontent) {
            $this->search_result = wiki_search_all($swid, $search);
        } else {
            $this->search_result = wiki_search_title($swid, $search);
        }

    }

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/search.php');
    }

    function print_header() {
        global $PAGE;

        parent::print_header();

        $wiki = $PAGE->activityrecord;
        $page = (object)array('title' => $wiki->firstpagetitle);
        $this->wikioutput->wiki_print_subwiki_selector($wiki, $this->subwiki, $page, 'search');
    }

    function print_content() {
        global $PAGE;

        require_capability('mod/wiki:viewpage', $this->modcontext, NULL, true, 'noviewpagepermission', 'wiki');

        echo $this->wikioutput->search_result($this->search_result, $this->subwiki);
    }
}

/**
 *
 * Class that models the behavior of wiki's
 * create page
 *
 */
class page_wiki_create extends page_wiki {

    private $format;
    private $swid;
    private $wid;
    private $action;
    private $mform;
    private $groups;

    function print_header() {
        $this->set_url();
        parent::print_header();
    }

    function set_url() {
        global $PAGE, $CFG;

        $params = array();
        $params['swid'] = $this->swid;
        if ($this->action == 'new') {
            $params['action'] = 'new';
            $params['wid'] = $this->wid;
            if ($this->title != get_string('newpage', 'wiki')) {
                $params['title'] = $this->title;
            }
        } else {
            $params['action'] = 'create';
        }
        $PAGE->set_url(new moodle_url('/mod/wiki/create.php', $params));
    }

    function set_format($format) {
        $this->format = $format;
    }

    function set_wid($wid) {
        $this->wid = $wid;
    }

    function set_swid($swid) {
        $this->swid = $swid;
    }

    function set_availablegroups($group) {
        $this->groups = $group;
    }

    function set_action($action) {
        global $PAGE;
        $this->action = $action;

        require_once(__DIR__ . '/create_form.php');
        $url = new moodle_url('/mod/wiki/create.php', array('action' => 'create', 'wid' => $PAGE->activityrecord->id, 'group' => $this->gid, 'uid' => $this->uid));
        $formats = wiki_get_formats();
        $options = array('formats' => $formats, 'defaultformat' => $PAGE->activityrecord->defaultformat, 'forceformat' => $PAGE->activityrecord->forceformat, 'groups' => $this->groups);
        if ($this->title != get_string('newpage', 'wiki')) {
            $options['disable_pagetitle'] = true;
        }
        $this->mform = new mod_wiki_create_form($url->out(false), $options);
    }

    protected function create_navbar() {
        global $PAGE;
        // navigation_node::get_content formats this before printing.
        $PAGE->navbar->add($this->title);
    }

    function print_content($pagetitle = '') {
        global $PAGE;

        // @TODO: Change this to has_capability and show an alternative interface.
        require_capability('mod/wiki:createpage', $this->modcontext, NULL, true, 'nocreatepermission', 'wiki');
        $data = new stdClass();
        if (!empty($pagetitle)) {
            $data->pagetitle = $pagetitle;
        }
        $data->pageformat = $PAGE->activityrecord->defaultformat;

        $this->mform->set_data($data);
        $this->mform->display();
    }

    function create_page($pagetitle) {
        global $USER, $PAGE;

        $data = $this->mform->get_data();
        if (isset($data->groupinfo)) {
            $groupid = $data->groupinfo;
        } else if (!empty($this->gid)) {
            $groupid = $this->gid;
        } else {
            $groupid = '0';
        }
        if (empty($this->subwiki)) {
            // If subwiki is not set then try find one and set else create one.
            if (!$this->subwiki = wiki_get_subwiki_by_group($this->wid, $groupid, $this->uid)) {
                $swid = wiki_add_subwiki($PAGE->activityrecord->id, $groupid, $this->uid);
                $this->subwiki = wiki_get_subwiki($swid);
            }
        }
        if ($data) {
            $this->set_title($data->pagetitle);
            $id = wiki_create_page($this->subwiki->id, $data->pagetitle, $data->pageformat, $USER->id);
        } else {
            $this->set_title($pagetitle);
            $id = wiki_create_page($this->subwiki->id, $pagetitle, $PAGE->activityrecord->defaultformat, $USER->id);
        }
        $this->page = $id;
        return $id;
    }
}

class page_wiki_preview extends page_wiki_edit {

    private $newcontent;

    function print_header() {
        global $PAGE, $CFG;

        parent::print_header();

    }

    function print_content() {
        global $PAGE;

        require_capability('mod/wiki:editpage', $this->modcontext, NULL, true, 'noeditpermission', 'wiki');

        $this->print_preview();
    }

    function set_newcontent($newcontent) {
        $this->newcontent = $newcontent;
    }

    function set_url() {
        global $PAGE, $CFG;

        $params = array('pageid' => $this->page->id
        );

        if (isset($this->section)) {
            $params['section'] = $this->section;
        }

        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/edit.php', $params);
    }

    protected function setup_tabs($options = array()) {
        parent::setup_tabs(array('linkedwhenactive' => 'view', 'activetab' => 'view'));
    }

    protected function check_locks() {
        return true;
    }

    protected function print_preview() {
        global $CFG, $PAGE, $OUTPUT;

        $version = wiki_get_current_version($this->page->id);
        $format = $version->contentformat;
        $content = $version->content;

        $url = $CFG->wwwroot . '/mod/wiki/edit.php?pageid=' . $this->page->id;
        if (!empty($this->section)) {
            $url .= "&section=" . urlencode($this->section);
        }
        $params = array(
            'attachmentoptions' => page_wiki_edit::$attachmentoptions,
            'format' => $this->format,
            'version' => $this->versionnumber,
            'contextid' => $this->modcontext->id
        );

        if ($this->format != 'html') {
            $params['component'] = 'mod_wiki';
            $params['filearea'] = 'attachments';
            $params['fileitemid'] = $this->page->id;
        }
        $form = new mod_wiki_edit_form($url, $params);


        $options = array('swid' => $this->page->subwikiid, 'pageid' => $this->page->id, 'pretty_print' => true);

        if ($data = $form->get_data()) {
            if (isset($data->newcontent)) {
                // wiki fromat
                $text = $data->newcontent;
            } else {
                // html format
                $text = $data->newcontent_editor['text'];
            }
            $parseroutput = wiki_parse_content($data->contentformat, $text, $options);
            $this->set_newcontent($text);
            echo $OUTPUT->notification(get_string('previewwarning', 'wiki'), 'notifyproblem');
            $content = format_text($parseroutput['parsed_text'], FORMAT_HTML, array('overflowdiv'=>true, 'filter'=>false));
            echo $OUTPUT->box($content, 'generalbox wiki_previewbox');
            $content = $this->newcontent;
        }

        $this->print_edit($content);
    }

}

/**
 *
 * Class that models the behavior of wiki's
 * view differences
 *
 */
class page_wiki_diff extends page_wiki {

    private $compare;
    private $comparewith;

    function print_header() {
        global $OUTPUT;

        parent::print_header();

        $this->print_pagetitle();
        $vstring = new stdClass();
        $vstring->old = $this->compare;
        $vstring->new = $this->comparewith;
        echo html_writer::tag('div', get_string('comparewith', 'wiki', $vstring), array('class' => 'wiki_headingtitle'));
    }

    /**
     * Print the diff view
     */
    function print_content() {
        global $PAGE;

        require_capability('mod/wiki:viewpage', $this->modcontext, NULL, true, 'noviewpagepermission', 'wiki');

        $this->print_diff_content();
    }

    function set_url() {
        global $PAGE, $CFG;

        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/diff.php', array('pageid' => $this->page->id, 'comparewith' => $this->comparewith, 'compare' => $this->compare));
    }

    function set_comparison($compare, $comparewith) {
        $this->compare = $compare;
        $this->comparewith = $comparewith;
    }

    protected function create_navbar() {
        global $PAGE, $CFG;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('history', 'wiki'), $CFG->wwwroot . '/mod/wiki/history.php?pageid=' . $this->page->id);
        $PAGE->navbar->add(get_string('diff', 'wiki'));
    }

    protected function setup_tabs($options = array()) {
        parent::setup_tabs(array('linkedwhenactive' => 'history', 'activetab' => 'history'));
    }

    /**
     * This method returns the action bar.
     *
     * @param int $pageid The page id.
     * @param moodle_url $pageurl The page url.
     * @return string The HTML for the action bar.
     */
    protected function action_bar(int $pageid, moodle_url $pageurl): string {
        $backlink = new moodle_url('/mod/wiki/history.php', ['pageid' => $pageid]);
        return html_writer::link($backlink, get_string('back'), ['class' => 'btn btn-secondary mb-4']);
    }

    /**
     * Given two versions of a page, prints a page displaying the differences between them.
     *
     * @global object $CFG
     * @global object $OUTPUT
     * @global object $PAGE
     */
    private function print_diff_content() {
        global $CFG, $OUTPUT, $PAGE;

        $pageid = $this->page->id;
        $total = wiki_count_wiki_page_versions($pageid) - 1;

        $oldversion = wiki_get_wiki_page_version($pageid, $this->compare);

        $newversion = wiki_get_wiki_page_version($pageid, $this->comparewith);

        if ($oldversion && $newversion) {

            $oldtext = format_text(file_rewrite_pluginfile_urls($oldversion->content, 'pluginfile.php', $this->modcontext->id, 'mod_wiki', 'attachments', $this->subwiki->id));
            $newtext = format_text(file_rewrite_pluginfile_urls($newversion->content, 'pluginfile.php', $this->modcontext->id, 'mod_wiki', 'attachments', $this->subwiki->id));
            list($diff1, $diff2) = ouwiki_diff_html($oldtext, $newtext);
            $oldversion->diff = $diff1;
            $oldversion->user = wiki_get_user_info($oldversion->userid);
            $newversion->diff = $diff2;
            $newversion->user = wiki_get_user_info($newversion->userid);

            echo $this->wikioutput->diff($pageid, $oldversion, $newversion, array('total' => $total));
        } else {
            throw new \moodle_exception('versionerror', 'wiki');
        }
    }
}

/**
 *
 * Class that models the behavior of wiki's history page
 *
 */
class page_wiki_history extends page_wiki {
    /**
     * @var int $paging current page
     */
    private $paging;

    /**
     * @var int @rowsperpage Items per page
     */
    private $rowsperpage = 10;

    /**
     * @var int $allversion if $allversion != 0, all versions will be printed in a signle table
     */
    private $allversion;

    /**
     * The page_wiki_history constructor.
     *
     * @param stdClass $wiki Current wiki.
     * @param stdClass $subwiki Current subwiki.
     * @param stdClass $cm Current course_module.
     * @param string|null $activesecondarytab Secondary navigation node to be activated on the page, if required
     */
    public function __construct($wiki, $subwiki, $cm, ?string $activesecondarytab = null) {
        global $PAGE;
        parent::__construct($wiki, $subwiki, $cm, $activesecondarytab);
        $PAGE->requires->js_init_call('M.mod_wiki.history', null, true);
    }

    function print_header() {
        parent::print_header();
        $this->print_pagetitle();
    }

    function print_pagetitle() {
        global $OUTPUT;
        $html = '';

        $html .= $OUTPUT->container_start('wiki_headingtitle');
        $html .= $OUTPUT->heading_with_help(format_string($this->title), 'history', 'wiki', '', '', 3);
        $html .= $OUTPUT->container_end();
        echo $html;
    }

    function print_content() {
        global $PAGE;

        require_capability('mod/wiki:viewpage', $this->modcontext, NULL, true, 'noviewpagepermission', 'wiki');

        $this->print_history_content();
    }

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/history.php', array('pageid' => $this->page->id));
    }

    function set_paging($paging) {
        $this->paging = $paging;
    }

    function set_allversion($allversion) {
        $this->allversion = $allversion;
    }

    protected function create_navbar() {
        global $PAGE, $CFG;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('history', 'wiki'));
    }

    /**
     * Prints the history for a given wiki page
     *
     * @global object $CFG
     * @global object $OUTPUT
     * @global object $PAGE
     */
    private function print_history_content() {
        global $CFG, $OUTPUT, $PAGE;

        $pageid = $this->page->id;
        $offset = $this->paging * $this->rowsperpage;
        // vcount is the latest version
        $vcount = wiki_count_wiki_page_versions($pageid) - 1;
        if ($this->allversion) {
            $versions = wiki_get_wiki_page_versions($pageid, 0, $vcount);
        } else {
            $versions = wiki_get_wiki_page_versions($pageid, $offset, $this->rowsperpage);
        }
        // We don't want version 0 to be displayed
        // version 0 is blank page
        if (end($versions)->version == 0) {
            array_pop($versions);
        }

        $contents = array();

        $version0page = wiki_get_wiki_page_version($this->page->id, 0);
        $creator = wiki_get_user_info($version0page->userid);
        $a = new StdClass;
        $a->date = userdate($this->page->timecreated, get_string('strftimedaydatetime', 'langconfig'));
        $a->username = fullname($creator);
        echo html_writer::tag ('div', get_string('createddate', 'wiki', $a), array('class' => 'wiki_headingtime'));
        if ($vcount > 0) {

            /// If there is only one version, we don't need radios nor forms
            if (count($versions) == 1) {

                $row = array_shift($versions);

                $username = wiki_get_user_info($row->userid);
                $picture = $OUTPUT->user_picture($username);
                $date = userdate($row->timecreated, get_string('strftimedate', 'langconfig'));
                $time = userdate($row->timecreated, get_string('strftimetime', 'langconfig'));
                $versionid = wiki_get_version($row->id);
                $versionlink = new moodle_url('/mod/wiki/viewversion.php', array('pageid' => $pageid, 'versionid' => $versionid->id));
                $userlink = new moodle_url('/user/view.php', array('id' => $username->id, 'course' => $this->cm->course));
                $contents[] = array('', html_writer::link($versionlink->out(false), $row->version), $picture . html_writer::link($userlink->out(false), fullname($username)), $time, $OUTPUT->container($date, 'wiki_histdate'));

                $table = new html_table();
                $table->head = array('', get_string('version'), get_string('user'), get_string('modified'), '');
                $table->data = $contents;

                echo html_writer::table($table);

            } else {

                $checked = $vcount - $offset;
                $rowclass = array();

                foreach ($versions as $version) {
                    $user = wiki_get_user_info($version->userid);
                    $picture = $OUTPUT->user_picture($user, array('popup' => true));
                    $date = userdate($version->timecreated, get_string('strftimedate'));
                    $rowclass[] = 'wiki_histnewdate';
                    $time = userdate($version->timecreated, get_string('strftimetime', 'langconfig'));
                    $versionid = wiki_get_version($version->id);
                    if ($versionid) {
                        $url = new moodle_url('/mod/wiki/viewversion.php', array('pageid' => $pageid, 'versionid' => $versionid->id));
                        $viewlink = html_writer::link($url->out(false), $version->version);
                    } else {
                        $viewlink = $version->version;
                    }
                    $userlink = new moodle_url('/user/view.php', array('id' => $version->userid, 'course' => $this->cm->course));
                    $contents[] = array($this->choose_from_radio(array($version->version  => null), 'compare', 'M.mod_wiki.history()', $checked - 1, true) . $this->choose_from_radio(array($version->version  => null), 'comparewith', 'M.mod_wiki.history()', $checked, true), $viewlink, $picture . html_writer::link($userlink->out(false), fullname($user)), $time, $OUTPUT->container($date, 'wiki_histdate'));
                }

                $table = new html_table();

                $icon = $OUTPUT->help_icon('diff', 'wiki');

                $table->head = array(get_string('diff', 'wiki') . $icon, get_string('version'), get_string('user'), get_string('modified'), '');
                $table->data = $contents;
                $table->attributes['class'] = 'table generaltable';
                $table->rowclasses = $rowclass;

                // Print the form.
                echo html_writer::start_tag('form', array('action'=>new moodle_url('/mod/wiki/diff.php'), 'method'=>'get', 'id'=>'diff'));
                echo html_writer::tag('div', html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'pageid', 'value'=>$pageid)));
                echo html_writer::table($table);
                echo html_writer::start_tag('div');
                echo html_writer::empty_tag('input', array('type'=>'submit', 'class'=>'wiki_form-button btn btn-secondary', 'value'=>get_string('comparesel', 'wiki')));
                echo html_writer::end_tag('div');
                echo html_writer::end_tag('form');
            }
        } else {
            print_string('nohistory', 'wiki');
        }
        if (!$this->allversion) {
            //$pagingbar = moodle_paging_bar::make($vcount, $this->paging, $this->rowsperpage, $CFG->wwwroot.'/mod/wiki/history.php?pageid='.$pageid.'&amp;');
            // $pagingbar->pagevar = $pagevar;
            echo $OUTPUT->paging_bar($vcount, $this->paging, $this->rowsperpage, $CFG->wwwroot . '/mod/wiki/history.php?pageid=' . $pageid . '&amp;');
            //print_paging_bar($vcount, $paging, $rowsperpage,$CFG->wwwroot.'/mod/wiki/history.php?pageid='.$pageid.'&amp;','paging');
            } else {
            $link = new moodle_url('/mod/wiki/history.php', array('pageid' => $pageid));
            $OUTPUT->container(html_writer::link($link->out(false), get_string('viewperpage', 'wiki', $this->rowsperpage)));
        }
        if ($vcount > $this->rowsperpage && !$this->allversion) {
            $link = new moodle_url('/mod/wiki/history.php', array('pageid' => $pageid, 'allversion' => 1));
            $OUTPUT->container(html_writer::link($link->out(false), get_string('viewallhistory', 'wiki')));
        }
    }

    /**
     * Given an array of values, creates a group of radio buttons to be part of a form
     *
     * @param array  $options  An array of value-label pairs for the radio group (values as keys).
     * @param string $name     Name of the radiogroup (unique in the form).
     * @param string $onclick  Function to be executed when the radios are clicked.
     * @param string $checked  The value that is already checked.
     * @param bool   $return   If true, return the HTML as a string, otherwise print it.
     *
     * @return mixed If $return is false, returns nothing, otherwise returns a string of HTML.
     */
    private function choose_from_radio($options, $name, $onclick = '', $checked = '', $return = false) {

        static $idcounter = 0;

        if (!$name) {
            $name = 'unnamed';
        }

        $output = '<span class="radiogroup ' . $name . "\">\n";

        if (!empty($options)) {
            $currentradio = 0;
            foreach ($options as $value => $label) {
                $htmlid = 'auto-rb' . sprintf('%04d', ++$idcounter);
                $output .= ' <span class="radioelement ' . $name . ' rb' . $currentradio . "\">";
                $output .= '<input name="' . $name . '" id="' . $htmlid . '" type="radio" value="' . $value . '"';
                if ($value == $checked) {
                    $output .= ' checked="checked"';
                }
                if ($onclick) {
                    $output .= ' onclick="' . $onclick . '"';
                }
                if ($label === '') {
                    $output .= ' /> <label for="' . $htmlid . '">' . $value . '</label></span>' . "\n";
                } else {
                    $output .= ' /> <label for="' . $htmlid . '">' . $label . '</label></span>' . "\n";
                }
                $currentradio = ($currentradio + 1) % 2;
            }
        }

        $output .= '</span>' . "\n";

        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }
}

/**
 * Class that models the behavior of wiki's map page
 *
 */
class page_wiki_map extends page_wiki {

    /**
     * @var int wiki view option
     */
    private $view;

    /** @var renderer_base */
    protected $output;

    function print_header() {
        parent::print_header();
        $this->print_pagetitle();
    }

    function print_content() {
        global $CFG, $PAGE;

        require_capability('mod/wiki:viewpage', $this->modcontext, NULL, true, 'noviewpagepermission', 'wiki');

        if ($this->view > 0) {
            //echo '<div><a href="' . $CFG->wwwroot . '/mod/wiki/map.php?pageid=' . $this->page->id . '">' . get_string('backtomapmenu', 'wiki') . '</a></div>';
        }

        switch ($this->view) {
        case 1:
            echo $this->wikioutput->menu_map($this->page->id, $this->view);
            $this->print_contributions_content();
            break;
        case 2:
            echo $this->wikioutput->menu_map($this->page->id, $this->view);
            $this->print_navigation_content();
            break;
        case 3:
            echo $this->wikioutput->menu_map($this->page->id, $this->view);
            $this->print_orphaned_content();
            break;
        case 4:
            echo $this->wikioutput->menu_map($this->page->id, $this->view);
            $this->print_index_content();
            break;
        case 6:
            echo $this->wikioutput->menu_map($this->page->id, $this->view);
            $this->print_updated_content();
            break;
        case 5:
        default:
            echo $this->wikioutput->menu_map($this->page->id, $this->view);
            $this->print_page_list_content();
        }
    }

    function set_view($option) {
        $this->view = $option;
    }

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/map.php', array('pageid' => $this->page->id));
    }

    protected function create_navbar() {
        global $PAGE;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('map', 'wiki'));
    }

    /**
     * Prints the contributions tab content
     *
     * @uses $OUTPUT, $USER
     *
     */
    private function print_contributions_content() {
        global $CFG, $OUTPUT, $USER;
        $page = $this->page;

        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $fresh = wiki_refresh_cachedcontent($page);
            $page = $fresh['page'];
        }

        $swid = $this->subwiki->id;

        $table = new html_table();
        $table->head = array(get_string('contributions', 'wiki') . $OUTPUT->help_icon('contributions', 'wiki'));
        $table->attributes['class'] = 'generalbox table';
        $table->data = array();
        $table->rowclasses = array();

        $lastversions = array();
        $pages = array();
        $users = array();

        if ($contribs = wiki_get_contributions($swid, $USER->id)) {
            foreach ($contribs as $contrib) {
                if (!array_key_exists($contrib->pageid, $pages)) {
                    $page = wiki_get_page($contrib->pageid);
                    $pages[$contrib->pageid] = $page;
                } else {
                    continue;
                }

                if (!array_key_exists($page->id, $lastversions)) {
                    $version = wiki_get_last_version($page->id);
                    $lastversions[$page->id] = $version;
                } else {
                    $version = $lastversions[$page->id];
                }

                if (!array_key_exists($version->userid, $users)) {
                    $user = wiki_get_user_info($version->userid);
                    $users[$version->userid] = $user;
                } else {
                    $user = $users[$version->userid];
                }

                $link = wiki_parser_link($page->title, array('swid' => $swid));
                $class = ($link['new']) ? 'class="wiki_newentry"' : '';

                $linkpage = '<a href="' . $link['url'] . '"' . $class . '>' . format_string($link['content'], true, array('context' => $this->modcontext)) . '</a>';
                $icon = $OUTPUT->user_picture($user, array('popup' => true));

                $table->data[] = array("$icon&nbsp;$linkpage");
            }
        } else {
            $table->data[] = array(get_string('nocontribs', 'wiki'));
        }
        echo html_writer::table($table);
    }

    /**
     * Prints the navigation tab content
     *
     * @uses $OUTPUT
     *
     */
    private function print_navigation_content() {
        global $OUTPUT;
        $page = $this->page;

        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $fresh = wiki_refresh_cachedcontent($page);
            $page = $fresh['page'];
        }

        $tolinks = wiki_get_linked_to_pages($page->id);
        $fromlinks = wiki_get_linked_from_pages($page->id);

        $table = new html_table();
        $table->attributes['class'] = 'wiki_navigation_from table';
        $table->head = array(get_string('navigationfrom', 'wiki') . $OUTPUT->help_icon('navigationfrom', 'wiki') . ':');
        $table->data = array();
        $table->rowclasses = array();
        foreach ($fromlinks as $link) {
            $lpage = wiki_get_page($link->frompageid);
            $link = new moodle_url('/mod/wiki/view.php', array('pageid' => $lpage->id));
            $table->data[] = array(html_writer::link($link->out(false), format_string($lpage->title)));
        }

        $table_left = $OUTPUT->container(html_writer::table($table), 'col-md-6');

        $table = new html_table();
        $table->attributes['class'] = 'wiki_navigation_to table';
        $table->head = array(get_string('navigationto', 'wiki') . $OUTPUT->help_icon('navigationto', 'wiki') . ':');
        $table->data = array();
        $table->rowclasses = array();
        foreach ($tolinks as $link) {
            if ($link->tomissingpage) {
                $viewlink = new moodle_url('/mod/wiki/create.php', array('swid' => $page->subwikiid, 'title' => $link->tomissingpage, 'action' => 'new'));
                $table->data[] = array(html_writer::link($viewlink->out(false), format_string($link->tomissingpage), array('class' => 'wiki_newentry')));
            } else {
                $lpage = wiki_get_page($link->topageid);
                $viewlink = new moodle_url('/mod/wiki/view.php', array('pageid' => $lpage->id));
                $table->data[] = array(html_writer::link($viewlink->out(false), format_string($lpage->title)));
            }
        }
        $table_right = $OUTPUT->container(html_writer::table($table), 'col-md-6');
        echo $OUTPUT->container($table_left . $table_right, 'wiki_navigation_container row');
    }

    /**
     * Prints the index page tab content
     *
     *
     */
    private function print_index_content() {
        global $OUTPUT;
        $page = $this->page;

        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $fresh = wiki_refresh_cachedcontent($page);
            $page = $fresh['page'];
        }

        // navigation_node get_content calls format string for us
        $node = new navigation_node($page->title);

        $keys = array();
        $tree = array();
        $tree = wiki_build_tree($page, $node, $keys);

        $table = new html_table();
        $table->head = array(get_string('pageindex', 'wiki') . $OUTPUT->help_icon('pageindex', 'wiki'));
        $table->attributes['class'] = 'generalbox table';
        $table->data[] = array($this->render_navigation_node($tree));

        echo html_writer::table($table);
    }

    /**
     * Prints the page list tab content
     *
     *
     */
    private function print_page_list_content() {
        global $OUTPUT;
        $page = $this->page;

        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $fresh = wiki_refresh_cachedcontent($page);
            $page = $fresh['page'];
        }

        $pages = wiki_get_page_list($this->subwiki->id);

        $stdaux = new stdClass();
        $strspecial = get_string('special', 'wiki');

        foreach ($pages as $page) {
            // We need to format the title here to account for any filtering
            $letter = format_string($page->title, true, array('context' => $this->modcontext));
            $letter = core_text::substr($letter, 0, 1);
            if (preg_match('/^[a-zA-Z]$/', $letter)) {
                $letter = core_text::strtoupper($letter);
                $stdaux->{$letter}[] = wiki_parser_link($page);
            } else {
                $stdaux->{$strspecial}[] = wiki_parser_link($page);
            }
        }

        $table = new html_table();
        $table->head = array(get_string('pagelist', 'wiki') . $OUTPUT->help_icon('pagelist', 'wiki'));
        $table->attributes['class'] = 'generalbox table';
        foreach ($stdaux as $key => $elem) {
            $table->data[] = array($key);
            foreach ($elem as $e) {
                $table->data[] = array(html_writer::link($e['url'], format_string($e['content'], true, array('context' => $this->modcontext))));
            }
        }
        echo html_writer::table($table);
    }

    /**
     * Prints the orphaned tab content
     *
     *
     */
    private function print_orphaned_content() {
        global $OUTPUT;

        $page = $this->page;

        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $fresh = wiki_refresh_cachedcontent($page);
            $page = $fresh['page'];
        }

        $swid = $this->subwiki->id;

        $table = new html_table();
        $table->head = array(get_string('orphaned', 'wiki') . $OUTPUT->help_icon('orphaned', 'wiki'));
        $table->attributes['class'] = 'generalbox table';
        $table->data = array();
        $table->rowclasses = array();

        if ($orphanedpages = wiki_get_orphaned_pages($swid)) {
            foreach ($orphanedpages as $page) {
                $link = wiki_parser_link($page->title, array('swid' => $swid));
                $class = ($link['new']) ? 'class="wiki_newentry"' : '';
                $table->data[] = array('<a href="' . $link['url'] . '"' . $class . '>' . format_string($link['content']) . '</a>');
            }
        } else {
            $table->data[] = array(get_string('noorphanedpages', 'wiki'));
        }

        echo html_writer::table($table);
    }

    /**
     * Prints the updated tab content
     *
     * @uses $COURSE, $OUTPUT
     *
     */
    private function print_updated_content() {
        global $COURSE, $OUTPUT;
        $page = $this->page;

        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $fresh = wiki_refresh_cachedcontent($page);
            $page = $fresh['page'];
        }

        $swid = $this->subwiki->id;

        $table = new html_table();
        $table->head = array(get_string('updatedpages', 'wiki') . $OUTPUT->help_icon('updatedpages', 'wiki'));
        $table->attributes['class'] = 'generalbox table';
        $table->data = array();
        $table->rowclasses = array();

        if ($pages = wiki_get_updated_pages_by_subwiki($swid)) {
            $strdataux = '';
            foreach ($pages as $page) {
                $user = wiki_get_user_info($page->userid);
                $strdata = date('d M Y', $page->timemodified);
                if ($strdata != $strdataux) {
                    $table->data[] = array($OUTPUT->heading($strdata, 4));
                    $strdataux = $strdata;
                }
                $link = wiki_parser_link($page->title, array('swid' => $swid));
                $class = ($link['new']) ? 'class="wiki_newentry"' : '';

                $linkpage = '<a href="' . $link['url'] . '"' . $class . '>' . format_string($link['content']) . '</a>';
                $icon = $OUTPUT->user_picture($user, array($COURSE->id));
                $table->data[] = array("$icon&nbsp;$linkpage");
            }
        } else {
            $table->data[] = array(get_string('noupdatedpages', 'wiki'));
        }

        echo html_writer::table($table);
    }

    protected function render_navigation_node($items, $attrs = array(), $expansionlimit = null, $depth = 1) {

        // exit if empty, we don't want an empty ul element
        if (count($items) == 0) {
            return '';
        }

        // array of nested li elements
        $lis = array();
        foreach ($items as $item) {
            if (!$item->display) {
                continue;
            }
            $content = $item->get_content();
            $title = $item->get_title();
            if ($item->icon instanceof renderable) {
                $icon = $this->wikioutput->render($item->icon);
                $content = $icon . '&nbsp;' . $content; // use CSS for spacing of icons
                }
            if ($item->helpbutton !== null) {
                $content = trim($item->helpbutton) . html_writer::tag('span', $content, array('class' => 'clearhelpbutton'));
            }

            if ($content === '') {
                continue;
            }

            if ($item->action instanceof action_link) {
                //TODO: to be replaced with something else
                $link = $item->action;
                if ($item->hidden) {
                    $link->add_class('dimmed');
                }
                $content = $this->output->render($link);
            } else if ($item->action instanceof moodle_url) {
                $attributes = array();
                if ($title !== '') {
                    $attributes['title'] = $title;
                }
                if ($item->hidden) {
                    $attributes['class'] = 'dimmed_text';
                }
                $content = html_writer::link($item->action, $content, $attributes);

            } else if (is_string($item->action) || empty($item->action)) {
                $attributes = array();
                if ($title !== '') {
                    $attributes['title'] = $title;
                }
                if ($item->hidden) {
                    $attributes['class'] = 'dimmed_text';
                }
                $content = html_writer::tag('span', $content, $attributes);
            }

            // this applies to the li item which contains all child lists too
            $liclasses = array($item->get_css_type(), 'depth_' . $depth);
            if ($item->has_children() && (!$item->forceopen || $item->collapse)) {
                $liclasses[] = 'collapsed';
            }
            if ($item->isactive === true) {
                $liclasses[] = 'current_branch';
            }
            $liattr = array('class' => join(' ', $liclasses));
            // class attribute on the div item which only contains the item content
            $divclasses = array('tree_item');
            if ((empty($expansionlimit) || $item->type != $expansionlimit) && ($item->children->count() > 0 || ($item->nodetype == navigation_node::NODETYPE_BRANCH && $item->children->count() == 0 && isloggedin()))) {
                $divclasses[] = 'branch';
            } else {
                $divclasses[] = 'leaf';
            }
            if (!empty($item->classes) && count($item->classes) > 0) {
                $divclasses[] = join(' ', $item->classes);
            }
            $divattr = array('class' => join(' ', $divclasses));
            if (!empty($item->id)) {
                $divattr['id'] = $item->id;
            }
            $content = html_writer::tag('p', $content, $divattr) . $this->render_navigation_node($item->children, array(), $expansionlimit, $depth + 1);
            if (!empty($item->preceedwithhr) && $item->preceedwithhr === true) {
                $content = html_writer::empty_tag('hr') . $content;
            }
            $content = html_writer::tag('li', $content, $liattr);
            $lis[] = $content;
        }

        if (count($lis)) {
            return html_writer::tag('ul', implode("\n", $lis), $attrs);
        } else {
            return '';
        }
    }

}

/**
 * Class that models the behavior of wiki's restore version page
 *
 */
class page_wiki_restoreversion extends page_wiki {
    private $version;

    function print_header() {
        parent::print_header();
        $this->print_pagetitle();
    }

    function print_content() {
        global $PAGE;

        $wiki = $PAGE->activityrecord;
        if (wiki_user_can_edit($this->subwiki, $wiki)) {
            $this->print_restoreversion();
        } else {
            echo get_string('cannoteditpage', 'wiki');
        }

    }

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/viewversion.php', array('pageid' => $this->page->id, 'versionid' => $this->version->id));
    }

    function set_versionid($versionid) {
        $this->version = wiki_get_version($versionid);
    }

    protected function create_navbar() {
        global $PAGE, $CFG;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('restoreversion', 'wiki'));
    }

    protected function setup_tabs($options = array()) {
        parent::setup_tabs(array('linkedwhenactive' => 'history', 'activetab' => 'history'));
    }

    /**
     * This method returns the action bar.
     *
     * @param int $pageid The page id.
     * @param moodle_url $pageurl The page url.
     * @return string The HTML for the action bar.
     */
    protected function action_bar(int $pageid, moodle_url $pageurl): string {
        // The given page does not require an action bar.
        return '';
    }

    /**
     * Prints the restore version content
     *
     * @uses $CFG
     *
     * @param page $page The page whose version will be restored
     * @param int  $versionid The version to be restored
     * @param bool $confirm If false, shows a yes/no confirmation page.
     *     If true, restores the old version and redirects the user to the 'view' tab.
     */
    private function print_restoreversion() {
        global $OUTPUT;

        $version = wiki_get_version($this->version->id);

        $optionsyes = array('confirm'=>1, 'pageid'=>$this->page->id, 'versionid'=>$version->id, 'sesskey'=>sesskey());
        $restoreurl = new moodle_url('/mod/wiki/restoreversion.php', $optionsyes);
        $return = new moodle_url('/mod/wiki/viewversion.php', array('pageid'=>$this->page->id, 'versionid'=>$version->id));

        echo $OUTPUT->container_start();
        echo html_writer::tag('div', get_string('restoreconfirm', 'wiki', $version->version));
        echo $OUTPUT->container_start('mt-2', 'wiki_restoreform');
        $yesbutton = new single_button($restoreurl, get_string('yes'), 'post');
        $nobutton = new single_button($return, get_string('no'), 'post');
        $nobutton->class .= ' ms-2';
        echo $OUTPUT->render($yesbutton);
        echo $OUTPUT->render($nobutton);
        echo $OUTPUT->container_end();
        echo $OUTPUT->container_end();
    }
}
/**
 * Class that models the behavior of wiki's delete comment confirmation page
 *
 */
class page_wiki_deletecomment extends page_wiki {
    private $commentid;

    function print_header() {
        parent::print_header();
        $this->print_pagetitle();
    }

    function print_content() {
        $this->printconfirmdelete();
    }

    function set_url() {
        global $PAGE;
        $PAGE->set_url('/mod/wiki/instancecomments.php', array('pageid' => $this->page->id, 'commentid' => $this->commentid));
    }

    public function set_action($action, $commentid, $content) {
        $this->commentid = $commentid;
    }

    protected function create_navbar() {
        global $PAGE;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('deletecommentcheck', 'wiki'));
    }

    protected function setup_tabs($options = array()) {
        parent::setup_tabs(array('linkedwhenactive' => 'comments', 'activetab' => 'comments'));
    }

    /**
     * This method returns the action bar.
     *
     * @param int $pageid The page id.
     * @param moodle_url $pageurl The page url.
     * @return string The HTML for the action bar.
     */
    protected function action_bar(int $pageid, moodle_url $pageurl): string {
        // The given page does not require an action bar.
        return '';
    }

    /**
     * Prints the comment deletion confirmation form
     */
    private function printconfirmdelete() {
        global $OUTPUT;

        $strdeletecheckfull = get_string('deletecommentcheckfull', 'wiki');

        //ask confirmation
        $optionsyes = array('confirm'=>1, 'pageid'=>$this->page->id, 'action'=>'delete', 'commentid'=>$this->commentid, 'sesskey'=>sesskey());
        $deleteurl = new moodle_url('/mod/wiki/instancecomments.php', $optionsyes);
        $return = new moodle_url('/mod/wiki/comments.php', array('pageid'=>$this->page->id));

        echo $OUTPUT->confirm($strdeletecheckfull, $deleteurl, $return);
    }
}

/**
 * Class that models the behavior of wiki's
 * save page
 *
 */
class page_wiki_save extends page_wiki_edit {

    private $newcontent;

    function print_header() {
    }

    function print_content() {
        global $PAGE;

        $context = context_module::instance($this->cm->id);
        require_capability('mod/wiki:editpage', $context, NULL, true, 'noeditpermission', 'wiki');

        $this->print_save();
    }

    function set_newcontent($newcontent) {
        $this->newcontent = $newcontent;
    }

    protected function set_session_url() {
    }

    protected function print_save() {
        global $CFG, $USER, $OUTPUT, $PAGE;

        $url = $CFG->wwwroot . '/mod/wiki/edit.php?pageid=' . $this->page->id;
        if (!empty($this->section)) {
            $url .= "&section=" . urlencode($this->section);
        }

        $params = array(
            'attachmentoptions' => page_wiki_edit::$attachmentoptions,
            'format' => $this->format,
            'version' => $this->versionnumber,
            'contextid' => $this->modcontext->id
        );

        if ($this->format != 'html') {
            $params['fileitemid'] = $this->page->id;
            $params['component']  = 'mod_wiki';
            $params['filearea']   = 'attachments';
        }

        $form = new mod_wiki_edit_form($url, $params);

        $save = false;
        $data = false;
        if ($data = $form->get_data()) {
            if ($this->format == 'html') {
                $data = file_postupdate_standard_editor($data, 'newcontent', page_wiki_edit::$attachmentoptions, $this->modcontext, 'mod_wiki', 'attachments', $this->subwiki->id);
            }

            if (isset($this->section)) {
                $save = wiki_save_section($this->page, $this->section, $data->newcontent, $USER->id);
            } else {
                $save = wiki_save_page($this->page, $data->newcontent, $USER->id);
            }
        }

        if ($save && $data) {
            core_tag_tag::set_item_tags('mod_wiki', 'wiki_pages', $this->page->id, $this->modcontext, $data->tags);

            $message = '<p>' . get_string('saving', 'wiki') . '</p>';

            if (!empty($save['sections'])) {
                foreach ($save['sections'] as $s) {
                    $message .= '<p>' . get_string('repeatedsection', 'wiki', $s) . '</p>';
                }
            }

            if ($this->versionnumber + 1 != $save['version']) {
                $message .= '<p>' . get_string('wrongversionsave', 'wiki') . '</p>';
            }

            if (isset($errors) && !empty($errors)) {
                foreach ($errors as $e) {
                    $message .= "<p>" . get_string('filenotuploadederror', 'wiki', $e->get_filename()) . "</p>";
                }
            }

            //deleting old locks
            wiki_delete_locks($this->page->id, $USER->id, $this->section);
            $url = new moodle_url('/mod/wiki/view.php', array('pageid' => $this->page->id, 'group' => $this->subwiki->groupid));
            redirect($url);
        } else {
            throw new \moodle_exception('savingerror', 'wiki');
        }
    }
}

/**
 * Class that models the behavior of wiki's view an old version of a page
 *
 */
class page_wiki_viewversion extends page_wiki {

    private $version;

    function print_header() {
        parent::print_header();
        $this->print_pagetitle();
    }

    function print_content() {
        global $PAGE;

        require_capability('mod/wiki:viewpage', $this->modcontext, NULL, true, 'noviewpagepermission', 'wiki');

        $this->print_version_view();
    }

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/viewversion.php', array('pageid' => $this->page->id, 'versionid' => $this->version->id));
    }

    function set_versionid($versionid) {
        $this->version = wiki_get_version($versionid);
    }

    protected function create_navbar() {
        global $PAGE, $CFG;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('history', 'wiki'), $CFG->wwwroot . '/mod/wiki/history.php?pageid=' . $this->page->id);
        $PAGE->navbar->add(get_string('versionnum', 'wiki', $this->version->version));
    }

    protected function setup_tabs($options = array()) {
        parent::setup_tabs(array('linkedwhenactive' => 'history', 'activetab' => 'history', 'inactivetabs' => array('edit')));
    }

    /**
     * This method returns the action bar.
     *
     * @param int $pageid The page id.
     * @param moodle_url $pageurl The page url.
     * @return string The HTML for the action bar.
     */
    protected function action_bar(int $pageid, moodle_url $pageurl): string {
        $backlink = new moodle_url('/mod/wiki/history.php', ['pageid' => $pageid]);
        return html_writer::link($backlink, get_string('back'), ['class' => 'btn btn-secondary mb-4']);
    }

    /**
     * Given an old page version, output the version content
     *
     * @global object $CFG
     * @global object $OUTPUT
     * @global object $PAGE
     */
    private function print_version_view() {
        global $CFG, $OUTPUT, $PAGE;
        $pageversion = wiki_get_version($this->version->id);

        if ($pageversion) {
            $restorelink = new moodle_url('/mod/wiki/restoreversion.php', array('pageid' => $this->page->id, 'versionid' => $this->version->id));
            echo html_writer::tag('div', get_string('viewversion', 'wiki', $pageversion->version) . '<br />' .
                html_writer::link($restorelink->out(false), '(' . get_string('restorethis', 'wiki') .
                ')', array('class' => 'wiki_restore')) . '&nbsp;', array('class' => 'wiki_headingtitle'));
            $userinfo = wiki_get_user_info($pageversion->userid);
            $heading = '<p><strong>' . get_string('modified', 'wiki') . ':</strong>&nbsp;' . userdate($pageversion->timecreated, get_string('strftimedatetime', 'langconfig'));
            $viewlink = new moodle_url('/user/view.php', array('id' => $userinfo->id));
            $heading .= '&nbsp;&nbsp;&nbsp;<strong>' . get_string('user') . ':</strong>&nbsp;' . html_writer::link($viewlink->out(false), fullname($userinfo));
            $heading .= '&nbsp;&nbsp;&rarr;&nbsp;' . $OUTPUT->user_picture(wiki_get_user_info($pageversion->userid), array('popup' => true)) . '</p>';
            echo $OUTPUT->container($heading, 'wiki_headingtime', 'wiki_modifieduser');
            $options = array('swid' => $this->subwiki->id, 'pretty_print' => true, 'pageid' => $this->page->id);

            $pageversion->content = file_rewrite_pluginfile_urls($pageversion->content, 'pluginfile.php', $this->modcontext->id, 'mod_wiki', 'attachments', $this->subwiki->id);

            $parseroutput = wiki_parse_content($pageversion->contentformat, $pageversion->content, $options);
            $content = $OUTPUT->container(format_text($parseroutput['parsed_text'], FORMAT_HTML, ['overflowdiv' => true]));
            echo $OUTPUT->box($content, 'generalbox wiki_contentbox');

        } else {
            throw new \moodle_exception('versionerror', 'wiki');
        }
    }
}

class page_wiki_confirmrestore extends page_wiki_save {

    private $version;

    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/viewversion.php', array('pageid' => $this->page->id, 'versionid' => $this->version->id));
    }

    function print_header() {
        $this->set_url();
    }

    function print_content() {
        global $CFG, $PAGE;

        $version = wiki_get_version($this->version->id);
        $wiki = $PAGE->activityrecord;
        if (wiki_user_can_edit($this->subwiki, $wiki) &&
                wiki_restore_page($this->page, $version, $this->modcontext)) {
            redirect($CFG->wwwroot . '/mod/wiki/view.php?pageid=' . $this->page->id, get_string('restoring', 'wiki', $version->version), 3);
        } else {
            throw new \moodle_exception('restoreerror', 'wiki', $version->version);
        }
    }

    function set_versionid($versionid) {
        $this->version = wiki_get_version($versionid);
    }
}

class page_wiki_prettyview extends page_wiki {

    function __construct($wiki, $subwiki, $cm) {
        global $PAGE;
        $PAGE->set_pagelayout('embedded');
        $PAGE->activityheader->disable();
        parent::__construct($wiki, $subwiki, $cm);
    }

    function print_header() {
        global $OUTPUT;
        $this->set_url();

        echo $OUTPUT->header();
        // Print dialog link.
        $printtext = get_string('print', 'wiki');
        $printlinkatt = array('onclick' => 'window.print();return false;', 'class' => 'printicon');
        $printiconlink = html_writer::link('#', $printtext, $printlinkatt);
        echo html_writer::tag('div', $printiconlink, array('class' => 'displayprinticon'));
        echo html_writer::tag('h1', format_string($this->title), array('id' => 'wiki_printable_title'));
    }

    function print_content() {
        global $PAGE;

        require_capability('mod/wiki:viewpage', $this->modcontext, NULL, true, 'noviewpagepermission', 'wiki');

        $this->print_pretty_view();
    }

    function set_url() {
        global $PAGE, $CFG;

        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/prettyview.php', array('pageid' => $this->page->id));
    }

    private function print_pretty_view() {
        $version = wiki_get_current_version($this->page->id);

        $content = wiki_parse_content($version->contentformat, $version->content, array('printable' => true, 'swid' => $this->subwiki->id, 'pageid' => $this->page->id, 'pretty_print' => true));

        $html = $content['parsed_text'];
        $id = $this->subwiki->wikiid;
        if ($cm = get_coursemodule_from_instance("wiki", $id)) {
            $context = context_module::instance($cm->id);
            $html = file_rewrite_pluginfile_urls($html, 'pluginfile.php', $context->id, 'mod_wiki', 'attachments', $this->subwiki->id);
        }
        echo '<div id="wiki_printable_content">';
        echo format_text($html, FORMAT_HTML);
        echo '</div>';
    }
}

class page_wiki_handlecomments extends page_wiki {
    private $action;
    private $content;
    private $commentid;
    private $format;

    function print_header() {
        $this->set_url();
    }

    public function print_content() {
        global $CFG, $PAGE, $USER;

        if ($this->action == 'add') {
            require_capability('mod/wiki:editcomment', $this->modcontext);
            $this->add_comment($this->content, $this->commentid);
        } else if ($this->action == 'edit') {
            require_capability('mod/wiki:editcomment', $this->modcontext);

            $comment = wiki_get_comment($this->commentid);
            $owner = ($comment->userid == $USER->id);

            if ($owner) {
                $this->add_comment($this->content, $this->commentid);
            }
        } else if ($this->action == 'delete') {
            $comment = wiki_get_comment($this->commentid);

            $manage = has_capability('mod/wiki:managecomment', $this->modcontext);
            $edit = has_capability('mod/wiki:editcomment', $this->modcontext);
            $owner = ($comment->userid == $USER->id);

            if ($manage || ($owner && $edit)) {
                $this->delete_comment($this->commentid);
                redirect($CFG->wwwroot . '/mod/wiki/comments.php?pageid=' . $this->page->id, get_string('deletecomment', 'wiki'), 2);
            } else {
                throw new \moodle_exception('nopermissiontoeditcomment');
            }
        }

    }

    public function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/comments.php', array('pageid' => $this->page->id));
    }

    public function set_action($action, $commentid, $content) {
        $this->action = $action;
        $this->commentid = $commentid;
        $this->content = $content;

        $version = wiki_get_current_version($this->page->id);
        $format = $version->contentformat;

        $this->format = $format;
    }

    private function add_comment($content, $idcomment) {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . "/mod/wiki/locallib.php");

        $pageid = $this->page->id;

        wiki_add_comment($this->modcontext, $pageid, $content, $this->format);

        if (!$idcomment) {
            redirect($CFG->wwwroot . '/mod/wiki/comments.php?pageid=' . $pageid, get_string('createcomment', 'wiki'), 2);
        } else {
            $this->delete_comment($idcomment);
            redirect($CFG->wwwroot . '/mod/wiki/comments.php?pageid=' . $pageid, get_string('editingcomment', 'wiki'), 2);
        }
    }

    private function delete_comment($commentid) {
        global $CFG, $PAGE;

        $pageid = $this->page->id;

        wiki_delete_comment($commentid, $this->modcontext, $pageid);
    }

}

class page_wiki_lock extends page_wiki_edit {

    public function print_header() {
        $this->set_url();
    }

    protected function set_url() {
        global $PAGE, $CFG;

        $params = array('pageid' => $this->page->id);

        if ($this->section) {
            $params['section'] = $this->section;
        }

        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/lock.php', $params);
    }

    protected function set_session_url() {
    }

    public function print_content() {
        global $USER, $PAGE;

        require_capability('mod/wiki:editpage', $this->modcontext, NULL, true, 'noeditpermission', 'wiki');

        wiki_set_lock($this->page->id, $USER->id, $this->section);
    }

    public function print_footer() {
    }
}

class page_wiki_overridelocks extends page_wiki_edit {
    function print_header() {
        $this->set_url();
    }

    function print_content() {
        global $CFG, $PAGE;

        require_capability('mod/wiki:overridelock', $this->modcontext, NULL, true, 'nooverridelockpermission', 'wiki');

        wiki_delete_locks($this->page->id, null, $this->section, true, true);

        $args = "pageid=" . $this->page->id;

        if (!empty($this->section)) {
            $args .= "&section=" . urlencode($this->section);
        }

        redirect($CFG->wwwroot . '/mod/wiki/edit.php?' . $args, get_string('overridinglocks', 'wiki'), 2);
    }

    function set_url() {
        global $PAGE, $CFG;

        $params = array('pageid' => $this->page->id);

        if (!empty($this->section)) {
            $params['section'] = $this->section;
        }

        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/overridelocks.php', $params);
    }

    protected function set_session_url() {
    }

    private function print_overridelocks() {
        global $CFG;

        wiki_delete_locks($this->page->id, null, $this->section, true, true);

        $args = "pageid=" . $this->page->id;

        if (!empty($this->section)) {
            $args .= "&section=" . urlencode($this->section);
        }

        redirect($CFG->wwwroot . '/mod/wiki/edit.php?' . $args, get_string('overridinglocks', 'wiki'), 2);
    }

}

/**
 * This class will let user to delete wiki pages and page versions
 *
 */
class page_wiki_admin extends page_wiki {

    public $view, $action;
    public $listorphan = false;

    /**
     * Constructor
     *
     * @global object $PAGE
     * @param mixed $wiki instance of wiki
     * @param mixed $subwiki instance of subwiki
     * @param stdClass $cm course module
     * @param string|null $activesecondarytab Secondary navigation node to be activated on the page, if required
     */
    public function __construct($wiki, $subwiki, $cm, ?string $activesecondarytab = null) {
        global $PAGE;
        parent::__construct($wiki, $subwiki, $cm, $activesecondarytab);
        $PAGE->requires->js_init_call('M.mod_wiki.deleteversion', null, true);
    }

    /**
     * Prints header for wiki page
     */
    function print_header() {
        parent::print_header();
        $this->print_pagetitle();
    }

    /**
     * This function will display administration view to users with managewiki capability
     */
    function print_content() {
        //make sure anyone trying to access this page has managewiki capabilities
        require_capability('mod/wiki:managewiki', $this->modcontext, NULL, true, 'noviewpagepermission', 'wiki');

        //update wiki cache if timedout
        $page = $this->page;
        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $fresh = wiki_refresh_cachedcontent($page);
            $page = $fresh['page'];
        }

        //dispaly admin menu
        echo $this->wikioutput->menu_admin($this->page->id, $this->view);

        //Display appropriate admin view
        switch ($this->view) {
            case 1: //delete page view
                $this->print_delete_content($this->listorphan);
                break;
            case 2: //delete version view
                $this->print_delete_version();
                break;
            default: //default is delete view
                $this->print_delete_content($this->listorphan);
                break;
        }
    }

    /**
     * Sets admin view option
     *
     * @param int $view page view id
     * @param bool $listorphan is only valid for view 1.
     */
    public function set_view($view, $listorphan = true) {
        $this->view = $view;
        $this->listorphan = $listorphan;
    }

    /**
     * Sets page url
     *
     * @global object $PAGE
     * @global object $CFG
     */
    function set_url() {
        global $PAGE, $CFG;
        $PAGE->set_url($CFG->wwwroot . '/mod/wiki/admin.php', array('pageid' => $this->page->id));
    }

    /**
     * sets navigation bar for the page
     *
     * @global object $PAGE
     */
    protected function create_navbar() {
        global $PAGE;

        parent::create_navbar();
        $PAGE->navbar->add(get_string('admin', 'wiki'));
    }

    /**
     * Show wiki page delete options
     *
     * @param bool $showorphan
     */
    protected function print_delete_content($showorphan = true) {
        $contents = array();
        $table = new html_table();
        $table->head = array('', get_string('pagename','wiki'));
        $table->attributes['class'] = 'table generaltable';
        $swid = $this->subwiki->id;
        if ($showorphan) {
            if ($orphanedpages = wiki_get_orphaned_pages($swid)) {
                $this->add_page_delete_options($orphanedpages, $swid, $table);
            } else {
                $table->data[] = array('', get_string('noorphanedpages', 'wiki'));
            }
        } else {
            if ($pages = wiki_get_page_list($swid)) {
                $this->add_page_delete_options($pages, $swid, $table);
            } else {
                $table->data[] = array('', get_string('nopages', 'wiki'));
            }
        }

        ///Print the form
        echo html_writer::start_tag('form', array(
                                                'action' => new moodle_url('/mod/wiki/admin.php'),
                                                'method' => 'post'));
        echo html_writer::tag('div', html_writer::empty_tag('input', array(
                                                                         'type'  => 'hidden',
                                                                         'name'  => 'pageid',
                                                                         'value' => $this->page->id)));

        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'option', 'value' => $this->view));
        echo html_writer::table($table);
        echo html_writer::start_tag('div');
        if (!$showorphan) {
            echo html_writer::empty_tag('input', array(
                                                     'type'    => 'submit',
                                                     'class'   => 'wiki_form-button',
                                                     'value'   => get_string('listorphan', 'wiki'),
                                                     'sesskey' => sesskey()));
        } else {
            echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'listall', 'value'=>'1'));
            echo html_writer::empty_tag('input', array(
                                                     'type'    => 'submit',
                                                     'class'   => 'wiki_form-button btn btn-secondary',
                                                     'value'   => get_string('listall', 'wiki'),
                                                     'sesskey' => sesskey()));
        }
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('form');
    }

    /**
     * helper function for print_delete_content. This will add data to the table.
     *
     * @global object $OUTPUT
     * @param array $pages objects of wiki pages in subwiki
     * @param int $swid id of subwiki
     * @param object $table reference to the table in which data needs to be added
     */
    protected function add_page_delete_options($pages, $swid, &$table) {
        global $OUTPUT;
        foreach ($pages as $page) {
            $link = wiki_parser_link($page->title, array('swid' => $swid));
            $class = ($link['new']) ? 'class="wiki_newentry"' : '';
            $pagelink = '<a href="' . $link['url'] . '"' . $class . '>' . format_string($link['content']) . '</a>';
            $urledit = new moodle_url('/mod/wiki/edit.php', array('pageid' => $page->id, 'sesskey' => sesskey()));
            $urldelete = new moodle_url('/mod/wiki/admin.php', array(
                                                                   'pageid'  => $this->page->id,
                                                                   'delete'  => $page->id,
                                                                   'option'  => $this->view,
                                                                   'listall' => !$this->listorphan?'1': '',
                                                                   'sesskey' => sesskey()));

            $editlinks = $OUTPUT->action_icon($urledit, new pix_icon('t/edit', get_string('edit')));
            $editlinks .= $OUTPUT->action_icon($urldelete, new pix_icon('t/delete', get_string('delete')));
            $table->data[] = array($editlinks, $pagelink);
        }
    }

    /**
     * Prints lists of versions which can be deleted
     *
     * @global core_renderer $OUTPUT
     * @global moodle_page $PAGE
     */
    private function print_delete_version() {
        global $OUTPUT, $PAGE;
        $pageid = $this->page->id;

        // versioncount is the latest version
        $versioncount = wiki_count_wiki_page_versions($pageid) - 1;
        $versions = wiki_get_wiki_page_versions($pageid, 0, $versioncount);

        // We don't want version 0 to be displayed
        // version 0 is blank page
        if (end($versions)->version == 0) {
            array_pop($versions);
        }

        $contents = array();
        $version0page = wiki_get_wiki_page_version($this->page->id, 0);
        $creator = wiki_get_user_info($version0page->userid);
        $a = new stdClass();
        $a->date = userdate($this->page->timecreated, get_string('strftimedaydatetime', 'langconfig'));
        $a->username = fullname($creator);
        echo $OUTPUT->heading(get_string('createddate', 'wiki', $a), 4);
        if ($versioncount > 0) {
            /// If there is only one version, we don't need radios nor forms
            if (count($versions) == 1) {
                $row = array_shift($versions);
                $username = wiki_get_user_info($row->userid);
                $picture = $OUTPUT->user_picture($username);
                $date = userdate($row->timecreated, get_string('strftimedate', 'langconfig'));
                $time = userdate($row->timecreated, get_string('strftimetime', 'langconfig'));
                $versionid = wiki_get_version($row->id);
                $versionlink = new moodle_url('/mod/wiki/viewversion.php', array('pageid' => $pageid, 'versionid' => $versionid->id));
                $userlink = new moodle_url('/user/view.php', array('id' => $username->id, 'course' => $this->cm->course));
                $picturelink = $picture . html_writer::link($userlink->out(false), fullname($username));
                $historydate = $OUTPUT->container($date, 'wiki_histdate');
                $contents[] = array('', html_writer::link($versionlink->out(false), $row->version), $picturelink, $time, $historydate);

                //Show current version
                $table = new html_table();
                $table->head = array('', get_string('version'), get_string('user'), get_string('modified'), '');
                $table->data = $contents;

                echo html_writer::table($table);
            } else {
                $lastdate = '';
                $rowclass = array();

                foreach ($versions as $version) {
                    $user = wiki_get_user_info($version->userid);
                    $picture = $OUTPUT->user_picture($user, array('popup' => true));
                    $date = userdate($version->timecreated, get_string('strftimedate'));
                    if ($date == $lastdate) {
                        $date = '';
                        $rowclass[] = '';
                    } else {
                        $lastdate = $date;
                        $rowclass[] = 'wiki_histnewdate';
                    }

                    $time = userdate($version->timecreated, get_string('strftimetime', 'langconfig'));
                    $versionid = wiki_get_version($version->id);
                    if ($versionid) {
                        $url = new moodle_url('/mod/wiki/viewversion.php', array('pageid' => $pageid, 'versionid' => $versionid->id));
                        $viewlink = html_writer::link($url->out(false), $version->version);
                    } else {
                        $viewlink = $version->version;
                    }

                    $userlink = new moodle_url('/user/view.php', array('id' => $version->userid, 'course' => $this->cm->course));
                    $picturelink = $picture . html_writer::link($userlink->out(false), fullname($user));
                    $historydate = $OUTPUT->container($date, 'wiki_histdate');
                    $radiofromelement = $this->choose_from_radio(array($version->version  => null), 'fromversion', 'M.mod_wiki.deleteversion()', $versioncount, true);
                    $radiotoelement = $this->choose_from_radio(array($version->version  => null), 'toversion', 'M.mod_wiki.deleteversion()', $versioncount, true);
                    $contents[] = array( $radiofromelement . $radiotoelement, $viewlink, $picturelink, $time, $historydate);
                }

                $table = new html_table();
                $table->head = array(get_string('deleteversions', 'wiki'), get_string('version'), get_string('user'), get_string('modified'), '');
                $table->data = $contents;
                $table->attributes['class'] = 'table generaltable';
                $table->rowclasses = $rowclass;

                ///Print the form
                echo html_writer::start_tag('form', array('action'=>new moodle_url('/mod/wiki/admin.php'), 'method' => 'post'));
                echo html_writer::tag('div', html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'pageid', 'value' => $pageid)));
                echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'option', 'value' => $this->view));
                echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' =>  sesskey()));
                echo html_writer::table($table);
                echo html_writer::start_tag('div');
                echo html_writer::empty_tag('input', array('type' => 'submit', 'class' => 'wiki_form-button btn btn-secondary', 'value' => get_string('deleteversions', 'wiki')));
                echo html_writer::end_tag('div');
                echo html_writer::end_tag('form');
            }
        } else {
            print_string('nohistory', 'wiki');
        }
    }

    /**
     * Given an array of values, creates a group of radio buttons to be part of a form
     * helper function for print_delete_version
     *
     * @param array  $options  An array of value-label pairs for the radio group (values as keys).
     * @param string $name     Name of the radiogroup (unique in the form).
     * @param string $onclick  Function to be executed when the radios are clicked.
     * @param string $checked  The value that is already checked.
     * @param bool   $return   If true, return the HTML as a string, otherwise print it.
     *
     * @return mixed If $return is false, returns nothing, otherwise returns a string of HTML.
     */
    private function choose_from_radio($options, $name, $onclick = '', $checked = '', $return = false) {

        static $idcounter = 0;

        if (!$name) {
            $name = 'unnamed';
        }

        $output = '<span class="radiogroup ' . $name . "\">\n";

        if (!empty($options)) {
            $currentradio = 0;
            foreach ($options as $value => $label) {
                $htmlid = 'auto-rb' . sprintf('%04d', ++$idcounter);
                $output .= ' <span class="radioelement ' . $name . ' rb' . $currentradio . "\">";
                $output .= '<input name="' . $name . '" id="' . $htmlid . '" type="radio" value="' . $value . '"';
                if ($value == $checked) {
                    $output .= ' checked="checked"';
                }
                if ($onclick) {
                    $output .= ' onclick="' . $onclick . '"';
                }
                if ($label === '') {
                    $output .= ' /> <label for="' . $htmlid . '">' . $value . '</label></span>' . "\n";
                } else {
                    $output .= ' /> <label for="' . $htmlid . '">' . $label . '</label></span>' . "\n";
                }
                $currentradio = ($currentradio + 1) % 2;
            }
        }

        $output .= '</span>' . "\n";

        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }
}
