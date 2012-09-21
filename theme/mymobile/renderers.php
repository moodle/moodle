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
 * Renderers for the mymobile theme
 *
 * @package    theme
 * @subpackage mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A custom renderer for the mymobile theme to produce snippets of content.
 *
 * @package    theme
 * @subpackage mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once ($CFG->dirroot. '/mod/choice/renderer.php');

class theme_mymobile_renderer extends plugin_renderer_base {

    /**
     * Produces the settings tree
     *
     * @param settings_navigation $navigation
     * @return string
     */
    public function settings_tree(settings_navigation $navigation) {
        $content = $this->navigation_node($navigation, array('class' => 'settings'));
        if (has_capability('moodle/site:config', context_system::instance())) {
            // TODO: Work out whether something is missing from here.
        }
        return $content;
    }

    /**
     * Produces the navigation tree
     *
     * @param global_navigation $navigation
     * @return string
     */
    public function navigation_tree(global_navigation $navigation) {
        return $this->navigation_node($navigation, array());
    }

    /**
     * Protected method to render a navigaiton node
     *
     * @param navigation_node $node
     * @param array $attrs
     * @return type
     */
    protected function navigation_node(navigation_node $node, $attrs = array()) {
        $items = $node->children;

        // exit if empty, we don't want an empty ul element
        if ($items->count() == 0) {
            return '';
        }

        // array of nested li elements
        $lis = array();
        foreach ($items as $item) {
            if (!$item->display) {
                continue;
            }

            $isbranch = ($item->children->count() > 0 || $item->nodetype == navigation_node::NODETYPE_BRANCH);
            $item->hideicon = true;

            $content = $this->output->render($item);
            $content .= $this->navigation_node($item);

            if ($isbranch && !(is_string($item->action) || empty($item->action))) {
                $content = html_writer::tag('li', $content, array('data-role' => 'list-divider', 'class' => (string)$item->key));
            } else if($isbranch) {
                $content = html_writer::tag('li', $content, array('data-role' => 'list-divider'));
            } else {
                $content = html_writer::tag('li', $content, array('class' => (string)$item->text));
            }
            $lis[] = $content;
        }
        if (!count($lis)) {
            return '';
        }
        return implode("\n", $lis);
    }
}

/**
 * Overridden core renderer for the mymobile theme
 *
 * @package    theme
 * @subpackage mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_mymobile_core_renderer extends core_renderer {

    /**
     * Returns the dtheme to use for the selected swatch
     *
     * @return string
     */
    protected function theme_swatch() {
        $showswatch = 'light';
        if (!empty($this->page->theme->settings->colourswatch)) {
            $showswatch = $this->page->theme->settings->colourswatch;
        }
        if ($showswatch == 'light') {
            $dtheme = 'b';
        } else {
            $dtheme = 'd';
        }
        return $dtheme;
     }

     /**
      * Produces a heading
      *
      * @param string $text
      * @param int $level
      * @param string $classes
      * @param string $id
      * @return string
      */
     public function heading($text, $level = 2, $classes = 'main', $id = null) {
        if ($classes == 'helpheading') {
            // Keeps wrap from help headings in dialog.
            $content = parent::heading($text, $level, $classes, $id);
        } else {
            $content  = html_writer::start_tag('div', array('class' => 'headingwrap ui-bar-'.$this->theme_swatch() .' ui-footer'));
            $content .= parent::heading($text, $level, $classes.' ui-title', $id);
            $content .= html_writer::end_tag('div');
        }
        return $content;
    }

    /**
     * Renders a block
     *
     * @param block_contents $bc
     * @param string $region
     * @return string
     */
    public function block(block_contents $bc, $region) {
        // Avoid messing up the object passed in.
        $bc = clone($bc);
        // The mymobile theme does not support collapsible blocks.
        $bc->collapsible = block_contents::NOT_HIDEABLE;
        // There are no controls that are usable within the
        $bc->controls = array();

        // TODO: Do we still need to support accessibility here? Surely screen
        // readers don't present themselves as mobile devices too often.
        $skiptitle = strip_tags($bc->title);
        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::tag('a', get_string('skipa', 'access', $skiptitle), array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'));
            $skipdest = html_writer::tag('span', '', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'));
        }
        $testb = $bc->attributes['class'];
        $testc = $bc->attributes['id'];
        // TODO: Find a better solution to this hardcoded block checks.
        if ($testb == "block_calendar_month2  block") {
            $output  = html_writer::start_tag('span');
        } else if ($testb == "block_course_overview  block") {
            $output  = html_writer::start_tag('div');
        } else {
            if (!empty($this->page->theme->settings->colourswatch)) {
                $showswatch = $this->page->theme->settings->colourswatch;
            } else {
                $showswatch = '';
            }
            if ($showswatch == 'light') {
                $dtheme = 'd';
            } else {
                $dtheme = 'c';
            }
            if ($testc == "mod_quiz_navblock") {
                $collap = 'false';
            } else {
                $collap = 'true';
            }
            $output  = html_writer::start_tag('div', array('data-role' => 'collapsible', 'data-collapsed' => $collap, 'data-content-theme' => $dtheme));
        }

        $output .= html_writer::tag('h1', $this->block_header($bc));
        $output .= html_writer::start_tag('div', $bc->attributes);
        $output .= $this->block_content($bc);
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        $output .= $this->block_annotation($bc);

        $output .= $skipdest;

        return $output;
    }

    /**
     * Produces a blocks header
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_header(block_contents $bc) {
        $title = '';
        if (!$bc->title) {
            return '&nbsp;';
        }
        $output  = html_writer::start_tag('div', array('class' => 'header'));
        $output .= html_writer::tag('div', html_writer::tag('div', '', array('class'=>'block_action')). $bc->title, array('class' => 'title'));
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * An evil function we don't want to execute
     *
     * @param block_contents $bc
     */
    protected function init_block_hider_js(block_contents $bc) {
        // The mymobile theme in no shape or form supports the hiding of blocks
        // this function has been defined and left empty intentionally so that
        // the block hider JS is not even included.
    }

    /**
     * Produces the navigation bar for the mymobile theme
     *
     * @return string
     */
    public function navbar() {
        $items = $this->page->navbar->get_items();

        $htmlblocks = array(html_writer::tag('option', get_string('navigation'), array('data-placeholder' => 'true', 'value' => '-1')));
        // Iterate the navarray and display each node
        $itemcount = count($items);
        $separator = "";

        for ($i = 0; $i < $itemcount; $i++) {
            $item = $items[$i];
            $item->hideicon = true;
            if ($i === 0) {
                $content = html_writer::tag('option', $this->render($item), array('value' => (string)$item->action));
            } else if (!empty($item->action)) {
                $content = html_writer::tag('option', $this->render($item), array('value' => (string)$item->action));
            } else {
                $content = '';
            }
            $htmlblocks[] = $content;
        }

        $navbarcontent  = html_writer::start_tag('form', array('id' => 'navselectform'));
        $navbarcontent .= html_writer::start_tag('select', array('id' => 'navselect', 'data-theme' => 'c', 'data-inline' => 'false', 'data-icon' => 'false'));
        $navbarcontent .= join('', $htmlblocks);
        $navbarcontent .= html_writer::end_tag('select');
        $navbarcontent .= html_writer::end_tag('form');
        // XHTML
        return $navbarcontent;
    }

    /**
     * Renders a navigation node
     *
     * This function has been overridden to remove tabindexs
     *
     * @param navigation_node $item
     * @return string
     */
    protected function render_navigation_node(navigation_node $item) {
        // Generate the content normally
        $content = parent::render_navigation_node($item);
        // Strip out any tabindex's
        $content = str_replace(' tabindex="0"', '', $content);
        $content = str_replace(' tabindex=\'0\'', '', $content);
        // Return the cleaned content
        return $content;
    }

    /**
     * Displays login info
     *
     * @return string
     */
    public function login_info($withlinks = null) {
        global $USER, $CFG, $DB, $SESSION;

        if (during_initial_install()) {
            return '';
        }

        $course = $this->page->course;

        if (session_is_loggedinas()) {
            $realuser = session_get_realuser();
            $fullname = fullname($realuser, true);
            $realuserinfo = " [<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;sesskey=".sesskey()."\">$fullname</a>] ";
        } else {
            $realuserinfo = '';
        }

        $loginurl = get_login_url();

        if (empty($course->id)) {
            // $course->id is not defined during installation
            return '';
        } else if (isloggedin()) {
            $context = context_course::instance($course->id);
            $fullname = fullname($USER, true);

            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            // TODO: Test what happens when someone is using this via mnet [for this as well as login_info_footer]
            // TODO: Test what happens when you use the loginas feature [for this as well as login_info_footer]
            $username = "";
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo.get_string('loggedinasguest')." (<a href=\"$loginurl\">".get_string('login').'</a>)';
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.format_string($role->name);
                }
            } else {
                $loggedinas = $realuserinfo.$username.'     <a id="mypower" data-inline="true" data-role="button" data-icon="mypower" data-ajax="false" class="ui-btn-right mypower" href="'.$CFG->wwwroot.'/login/logout.php?sesskey='.sesskey().'\">'.get_string('logout').'</a>';
            }
        } else {
            $loggedinas = '<a data-role="button" data-icon="alert" class="ui-btn-right nolog" href="'.$loginurl.'" data-ajax="false">'.get_string('login').'</a>';
        }

        // TODO: Enable $CFG->displayloginfailures and test as admin what happens after you succesfully
        //       log in after a failed log in attempt.  [for this as well as login_info_footer]
        //       This is probably totally unneeded
        if (isset($SESSION->justloggedin)) {
            unset($SESSION->justloggedin);
            if (!empty($CFG->displayloginfailures)) {
                if (!isguestuser()) {
                    if ($count = count_login_failures($CFG->displayloginfailures, $USER->username, $USER->lastlogin)) {
                        $loggedinas .= '&nbsp;<div class="loginfailures">';
                        if (empty($count->accounts)) {
                            $loggedinas .= get_string('failedloginattempts', '', $count);
                        } else {
                            $loggedinas .= get_string('failedloginattemptsall', '', $count);
                        }
                        if (file_exists("$CFG->dirroot/report/log/index.php") and has_capability('report/log:view', context_system::instance())) {
                            $loggedinas .= ' (<a href="'.$CFG->wwwroot.'/course/report/log/index.php?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }

    /**
     * Displays login info in the footer
     *
     * @return string
     */
    public function login_info_footer() {
        global $USER, $CFG, $DB, $SESSION;

        if (during_initial_install()) {
            return '';
        }

        $loginpage = ((string)$this->page->url === get_login_url());
        $course = $this->page->course;

        if (session_is_loggedinas()) {
            $realuser = session_get_realuser();
            $fullname = fullname($realuser, true);
            $realuserinfo = ' [<a href="'.$CFG->wwwroot.'/course/loginas.php?id=$course->id&amp;sesskey='.sesskey().'">$fullname</a>] ';
        } else {
            $realuserinfo = '';
        }

        $loginurl = get_login_url();

        if (empty($course->id)) {
            // $course->id is not defined during installation
            return '';
        } else if (isloggedin()) {
            $context = context_course::instance($course->id);

            $fullname = fullname($USER, true);
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            $username = '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$USER->id.'">'.$fullname.'</a>';
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo.get_string('loggedinasguest');
                if (!$loginpage) {
                    $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
                }
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.format_string($role->name);
                }
                $loggedinas = get_string('loggedinas', 'moodle', $username).$rolename." (<a href=\"$CFG->wwwroot/course/view.php?id=$course->id&amp;switchrole=0&amp;sesskey=".sesskey()."\">".get_string('switchrolereturn').'</a>)';
            } else {
                $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username).' '." (<a href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\" data-ajax=\"false\">".get_string('logout').'</a>)';
            }
        } else {
            $loggedinas = get_string('loggedinnot', 'moodle');
            if (!$loginpage) {
                $loggedinas .= " (<a href=\"$loginurl\" data-ajax=\"false\">".get_string('login').'</a>)';
            }
        }

        $loggedinas = '<div class="logininfo">'.$loggedinas.'</div>';

        if (isset($SESSION->justloggedin)) {
            unset($SESSION->justloggedin);
            if (!empty($CFG->displayloginfailures)) {
                if (!isguestuser()) {
                    if ($count = count_login_failures($CFG->displayloginfailures, $USER->username, $USER->lastlogin)) {
                        $loggedinas .= '&nbsp;<div class="loginfailures">';
                        if (empty($count->accounts)) {
                            $loggedinas .= get_string('failedloginattempts', '', $count);
                        } else {
                            $loggedinas .= get_string('failedloginattemptsall', '', $count);
                        }
                        if (has_capability('report/log:view', context_system::instance())) {
                            $loggedinas .= ' (<a href="'.$CFG->wwwroot.'/course/report/log/index.php?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }

    /**
     * Prints a message and redirects
     *
     * @param string $encodedurl
     * @param string $message
     * @param int $delay
     * @param true $debugdisableredirect
     * @return type
     */
    public function redirect_message($encodedurl, $message, $delay, $debugdisableredirect) {
        global $CFG;
        $url = str_replace('&amp;', '&', $encodedurl);
        // TODO: Find a much better solution for this... looks like it is just removing
        //       the anchor from the link.
        // The below to fix redirect issues with ajax... John
        $encodedurl = str_replace('#', '&', $encodedurl);

        switch ($this->page->state) {
            case moodle_page::STATE_BEFORE_HEADER :
                // No output yet it is safe to delivery the full arsenal of redirect methods
                if (!$debugdisableredirect) {
                    // Don't use exactly the same time here, it can cause problems when both redirects fire at the same time.
                    $this->metarefreshtag = '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />'."\n";
                }
                $output = $this->header();
                break;
            case moodle_page::STATE_PRINTING_HEADER :
                // We should hopefully never get here
                throw new coding_exception('You cannot redirect while printing the page header');
                break;
            case moodle_page::STATE_IN_BODY :
                // We really shouldn't be here but we can deal with this
                debugging("You should really redirect before you start page output");
                if (!$debugdisableredirect) {
                    $this->page->requires->js_function_call('document.location.replace', array($url), false, $delay);
                }
                $output = $this->opencontainers->pop_all_but_last();
                break;
            case moodle_page::STATE_DONE :
                // Too late to be calling redirect now
                throw new coding_exception('You cannot redirect after the entire page has been generated');
                break;
        }

        $output .= $this->notification($message, 'redirectmessage');
        $output .= '<div class="continuebutton"><a data-ajax="false" data-role="button" href="'. $encodedurl .'">'. get_string('continue') .'</a></div>';
        if ($debugdisableredirect) {
            $output .= '<p><strong>Error output, so disabling automatic redirect.</strong></p>';
        }
        $output .= $this->footer();
        return $output;
    }

    /**
     * Renders a help icon
     *
     * @param help_icon $helpicon
     * @return string
     */
    protected function render_help_icon(help_icon $helpicon) {
        global $CFG;

        // first get the help image icon
        $src = $this->pix_url('help');

        $title = get_string($helpicon->identifier, $helpicon->component);

        if (empty($helpicon->linktext)) {
            $alt = $title;
        } else {
            $alt = get_string('helpwiththis');
        }

        $attributes = array('src'=>$src, 'alt'=>$alt, 'class'=>'iconhelp', 'data-role'=>'button', 'data-inline'=>'true');
        $output = html_writer::empty_tag('img', $attributes);

        // add the link text if given
        if (!empty($helpicon->linktext)) {
            // the spacing has to be done through CSS
            $output .= $helpicon->linktext;
        }

        // now create the link around it
        // TODO: Do we need to specify the theme in the help.php link?
        $url = new moodle_url('/help.php', array('component' => $helpicon->component, 'identifier' => $helpicon->identifier, 'lang'=>current_language(), 'theme'=>'mymobile'));

        // note: this title is displayed only if JS is disabled, otherwise the link will have the new ajax tooltip
        $title = get_string('helpprefix2', '', trim($title, ". \t"));

        $attributes = array('href'=>$url, 'title'=>$title);
        $id = html_writer::random_id('helpicon');
        $attributes['id'] = $id;
        $attributes['rel'] = 'notexternal';
        $attributes['data-rel'] = 'dialog';
        $attributes['data-transition'] = 'flow';
        $output = html_writer::tag('a', $output, $attributes);

        // and finally span
        return html_writer::tag('span', $output, array('class' => 'helplink2'));
    }

    /**
     * Renders a single button
     *
     * @param single_button $button
     * @return string
     */
    protected function render_single_button(single_button $button) {
        $attributes = array(
            'type'     => 'submit',
            'value'    => $button->label,
            'disabled' => $button->disabled ? 'disabled' : null,
            'title'    => $button->tooltip
        );

        if ($button->actions) {
            $id = html_writer::random_id('single_button');
            $attributes['id'] = $id;
            foreach ($button->actions as $action) {
                $this->add_action_handler($action, $id);
            }
        }

        // first the input element
        $output = html_writer::empty_tag('input', $attributes);

        // then hidden fields
        $params = $button->url->params();
        if ($button->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        foreach ($params as $var => $val) {
            $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $var, 'value' => $val));
        }

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output, array('rel' => $button->url->out_omit_querystring()));

        // TODO: Test a single_button that has an anchor and is set to use post
        // now the form itself around it
        $url = $button->url->out_omit_querystring(); // url without params

        if ($url === '') {
            $url = '#'; // there has to be always some action
        }

        // TODO: This is surely a bug that needs fixing.. all of a sudden we've switched
        //       to the pages URL.
        //       Test an single button with an external URL as its url
        // If the url has http, cool, if not we need to add it, JOHN
        $urlcheck = substr($url, 0, 4);
        if ($urlcheck != 'http') {
            $url = $this->page->url->out_omit_querystring();
        }

        $attributes = array(
            'method' => $button->method,
            'action' => $url,
            'id'     => $button->formid
        );
        $output = html_writer::tag('form', $output, $attributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $button->class));
    }

    /**
     * Renders the header for the page
     *
     * @return string
     */
    public function header() {
        global $USER, $CFG;

        if (session_is_loggedinas()) {
            $this->page->add_body_class('userloggedinas');
        }

        $this->page->set_state(moodle_page::STATE_PRINTING_HEADER);

        // Find the appropriate page layout file, based on $this->page->pagelayout.
        $layoutfile = $this->page->theme->layout_file($this->page->pagelayout);
        // Render the layout using the layout file.
        $rendered = $this->render_page_layout($layoutfile);

        // Slice the rendered output into header and footer.
        $cutpos = strpos($rendered, $this->unique_main_content_token);
        if ($cutpos === false) {
            $cutpos = strpos($rendered, self::MAIN_CONTENT_TOKEN);
            $token = self::MAIN_CONTENT_TOKEN;
        } else {
            $token = $this->unique_main_content_token;
        }

        if ($cutpos === false) {
            // TODO: Search for a better solution to this... check this is even needed?
            //       The following code will lead to header containing nothing, and
            //       footer containing all of the content for the template.
            // turned off error by john for ajax load of blocks without main content.
            // throw new coding_exception('page layout file ' . $layoutfile .
            //        ' does not contain the string "' . self::MAIN_CONTENT_TOKEN . '".');
        }
        $header = substr($rendered, 0, $cutpos);
        $footer = substr($rendered, $cutpos + strlen($token));

        if (empty($this->contenttype)) {
            debugging('The page layout file did not call $OUTPUT->doctype()');
            $header = $this->doctype() . $header;
        }

        send_headers($this->contenttype, $this->page->cacheable);

        $this->opencontainers->push('header/footer', $footer);
        $this->page->set_state(moodle_page::STATE_IN_BODY);

        return $header . $this->skip_link_target('maincontent');
    }

    /**
     * Renders a notification
     *
     * @param string $message
     * @param string $classes
     * @return string
     */
    public function notification($message, $classes = 'notifyproblem') {
        return html_writer::tag('div', clean_text($message), array('data-role'=>'none', 'data-icon'=>'alert', 'data-theme'=>'d', 'class' => renderer_base::prepare_classes($classes)));
    }

    /**
     * Renders the blocks for a block region in the page
     *
     * @param type $region
     * @return string
     */
    public function blocks_for_region($region) {
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);

        $output = '';
        foreach ($blockcontents as $bc) {
            if ($bc instanceof block_contents) {
                // We don't want to print navigation and settings blocks here.
                if ($bc->attributes['class'] != 'block_settings  block' && $bc->attributes['class'] != 'block_navigation  block') {
                    $output .= $this->block($bc, $region);
                }
            } else if ($bc instanceof block_move_target) {
                $output .= $this->block_move_target($bc);
            } else {
                throw new coding_exception('Unexpected type of thing (' . get_class($bc) . ') found in list of block contents.');
            }
        }

        return $output;
    }

    /**
     * Renders a single select instance
     *
     * @param single_select $select
     * @return string
     */
    protected function render_single_select(single_select $select) {
        $select = clone($select);
        if (empty($select->formid)) {
            $select->formid = html_writer::random_id('single_select_f');
        }

        $output = '';
        $params = $select->url->params();
        if ($select->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        foreach ($params as $name=>$value) {
            $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>$name, 'value'=>$value));
        }

        if (empty($select->attributes['id'])) {
            $select->attributes['id'] = html_writer::random_id('single_select');
            //$select->attributes['data-native-menu'] = 'false';
            //above by john for select elements to use native style and help performance?
        }

        if ($select->disabled) {
            $select->attributes['disabled'] = 'disabled';
        }

        if ($select->tooltip) {
            $select->attributes['title'] = $select->tooltip;
        }

        $select->attributes['class'] = 'autosubmit';
        if ($select->class) {
            $select->attributes['class'] .= ' ' . $select->class;
        }

        if ($select->label) {
            $output .= html_writer::label($select->label, $select->attributes['id']);
        }

        if ($select->helpicon instanceof help_icon) {
            $output .= $this->render($select->helpicon);
        } else if ($select->helpicon instanceof old_help_icon) {
            $output .= $this->render($select->helpicon);
        }

        $output .= html_writer::select($select->options, $select->name, $select->selected, $select->nothing, $select->attributes);

        //by john show go button to fix selects
        $go = '';
        $output .= html_writer::empty_tag('input data-inline="true"', array('type' => 'submit','value' => get_string('go')));
        $output .= html_writer::tag('noscript', html_writer::tag('div', $go), array('style' => 'inline'));

        $nothing = empty($select->nothing) ? false : key($select->nothing);
        $this->page->requires->yui_module('moodle-core-formautosubmit',
            'M.core.init_formautosubmit',
            array(array('selectid' => $select->attributes['id'], 'nothing' => $nothing))
        );

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output);

        // now the form itself around it
        $formattributes = array(
            'method' => $select->method,
            'action' => $select->url->out_omit_querystring(),
            'id'     => $select->formid
        );
        $output = html_writer::tag('form', $output, $formattributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $select->class));
    }
}

/**
 * Overridden choie module renderer for the mymobile theme
 *
 * @package    theme
 * @subpackage mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_mymobile_mod_choice_renderer extends mod_choice_renderer {

    /**
     * Returns HTML to display choices of option
     * @param object $options
     * @param int  $coursemoduleid
     * @param bool $vertical
     * @return string
     */
    public function display_options($options, $coursemoduleid, $vertical = false) {
        $layoutclass = 'horizontal';
        if ($vertical) {
            $layoutclass = 'vertical';
        }
        $target = new moodle_url('/mod/choice/view.php');
        //changed below to post from target john
        $attributes = array('method'=>'POST', 'action'=>$target, 'class'=> $layoutclass);

        $html = html_writer::start_tag('form', $attributes);
        $html .= html_writer::start_tag('ul', array('class'=>'choices', 'data-role'=>'controlgroup' ));

        $availableoption = count($options['options']);
        foreach ($options['options'] as $option) {
            $html .= html_writer::start_tag('li', array('class'=>'option'));
            $option->attributes->name = 'answer';
            $option->attributes->type = 'radio';
            $option->attributes->id = 'answer'.html_writer::random_id();

            $labeltext = $option->text;
            if (!empty($option->attributes->disabled)) {
                $labeltext .= ' ' . get_string('full', 'choice');
                $availableoption--;
            }

            $html .= html_writer::empty_tag('input', (array)$option->attributes);
            $html .= html_writer::tag('label', $labeltext, array('for'=>$option->attributes->id));
            $html .= html_writer::end_tag('li');
        }
        $html .= html_writer::tag('li','', array('class'=>'clearfloat'));
        $html .= html_writer::end_tag('ul');
        $html .= html_writer::tag('div', '', array('class'=>'clearfloat'));
        $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));
        $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'id', 'value'=>$coursemoduleid));

        if (!empty($options['hascapability']) && ($options['hascapability'])) {
            if ($availableoption < 1) {
               $html .= html_writer::tag('label', get_string('choicefull', 'choice'));
            } else {
                $html .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('savemychoice','choice'), 'class'=>'button'));
            }

            if (!empty($options['allowupdate']) && ($options['allowupdate'])) {
                $url = new moodle_url('view.php', array('id'=>$coursemoduleid, 'action'=>'delchoice', 'sesskey'=>sesskey()));
                $html .= html_writer::link($url, get_string('removemychoice','choice'));
            }
        } else {
            $html .= html_writer::tag('label', get_string('havetologin', 'choice'));
        }

        $html .= html_writer::end_tag('ul');
        $html .= html_writer::end_tag('form');

        return $html;
    }

    /**
     * Returns HTML to display choices result
     *
     * TODO: There are differences between this method and the mod choice renderers function.
     *       This needs to be checked VERY careful as the minor changes look like they
     *       may lead to regressions.
     *
     * @param object $choices
     * @param bool $forcepublish
     * @return string
     */
    public function display_publish_name_vertical($choices) {
        $html ='';
        $html .= html_writer::tag('h2',format_string(get_string("responses", "choice")), array('class'=>'main'));

        $attributes = array('method'=>'POST');
        $attributes['action'] = new moodle_url('/mod/choice/view.php');
        $attributes['id'] = 'attemptsform';

        if ($choices->viewresponsecapability) {
            $html .= html_writer::start_tag('form', $attributes);
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'id', 'value'=> $choices->coursemoduleid));
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=> sesskey()));
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'mode', 'value'=>'overview'));
        }

        $table = new html_table();
        $table->cellpadding = 0;
        $table->cellspacing = 0;
        $table->attributes['class'] = 'results names ';
        $table->tablealign = 'center';
        $table->data = array();

        $count = 0;
        ksort($choices->options);

        $columns = array();
        foreach ($choices->options as $optionid => $options) {
            $coldata = '';
            if ($choices->showunanswered && $optionid == 0) {
                $coldata .= html_writer::tag('div', format_string(get_string('notanswered', 'choice')), array('class'=>'option'));
            } else if ($optionid > 0) {
                $coldata .= html_writer::tag('div', format_string($choices->options[$optionid]->text), array('class'=>'option'));
            }
            $numberofuser = 0;
            if (!empty($options->user) && count($options->user) > 0) {
                $numberofuser = count($options->user);
            }

            $coldata .= html_writer::tag('div', ' ('.$numberofuser. ')', array('class'=>'numberofuser', 'title' => get_string('numberofuser', 'choice')));
            $columns[] = $coldata;
        }

        $table->head = $columns;

        $coldata = '';
        $columns = array();
        foreach ($choices->options as $optionid => $options) {
            $coldata = '';
            if ($choices->showunanswered || $optionid > 0) {
                if (!empty($options->user)) {
                    foreach ($options->user as $user) {
                        $data = '';
                        if (empty($user->imagealt)){
                            $user->imagealt = '';
                        }

                        if ($choices->viewresponsecapability && $choices->deleterepsonsecapability  && $optionid > 0) {
                            $attemptaction = html_writer::checkbox('attemptid[]', $user->id,'');
                            $data .= html_writer::tag('div', $attemptaction, array('class'=>'attemptaction'));
                        }
                        $userimage = $this->output->user_picture($user, array('courseid'=>$choices->courseid));
                        $data .= html_writer::tag('div', $userimage, array('class'=>'image'));

                        $userlink = new moodle_url('/user/view.php', array('id'=>$user->id,'course'=>$choices->courseid));
                        $name = html_writer::tag('a', fullname($user, $choices->fullnamecapability), array('href'=>$userlink, 'class'=>'username'));
                        $data .= html_writer::tag('div', $name, array('class'=>'fullname'));
                        $data .= html_writer::tag('div','', array('class'=>'clearfloat'));
                        $coldata .= html_writer::tag('div', $data, array('class'=>'user'));
                    }
                }
            }

            $columns[] = $coldata;
            $count++;
        }

        $table->data[] = $columns;
        foreach ($columns as $d) {
            $table->colclasses[] = 'data';
        }
        $html .= html_writer::tag('div', html_writer::table($table), array('class'=>'response'));

        $actiondata = '';
        if ($choices->viewresponsecapability && $choices->deleterepsonsecapability) {
            $selecturl = new moodle_url('#');

            $selectallactions = new component_action('click',"select_all_in", array('div',null,'tablecontainer'));
            $selectall = new action_link($selecturl, get_string('selectall'), $selectallactions);
            $actiondata .= $this->output->render($selectall) . ' / ';

            $deselectallactions = new component_action('click',"deselect_all_in", array('div',null,'tablecontainer'));
            $deselectall = new action_link($selecturl, get_string('deselectall'), $deselectallactions);
            $actiondata .= $this->output->render($deselectall);
            //below john fixed
            $actiondata .= html_writer::tag('label', ' ' . get_string('withselected', 'choice') . ' ', array('for'=>'menuaction'));

            $actionurl = new moodle_url('/mod/choice/view.php', array('sesskey'=>sesskey(), 'action'=>'delete_confirmation()'));
            $select = new single_select($actionurl, 'action', array('delete'=>get_string('delete')), null, array(''=>get_string('moveselectedusersto', 'choice')), 'attemptsform');

            $actiondata .= $this->output->render($select);
        }
        $html .= html_writer::tag('div', $actiondata, array('class'=>'responseaction'));

        if ($choices->viewresponsecapability) {
            $html .= html_writer::end_tag('form');
        }

        return $html;
    }
}
