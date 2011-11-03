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
 * renderers file for mymobile theme
 *
 * @package    theme
 * @subpackage mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_mymobile_core_renderer extends core_renderer {
 
 public function themeswatch() {
    if (!empty($this->page->theme->settings->mswatch)) {
    $showswatch = $this->page->theme->settings->mswatch;
    } else {
    $showswatch = 'light';
    }
    if ($showswatch == 'light') {
    $dtheme = 'b';
    }
    else {
    $dtheme = 'd';
    }
    return $dtheme;
 }
 
 public function heading($text, $level = 2, $classes = 'main', $id = null) {
    
    if (!empty($this->page->theme->settings->mswatch)) {
    $showswatch = $this->page->theme->settings->mswatch;
    } else {
    $showswatch = 'light';
    }
    if ($showswatch == 'light') {
    $dtheme = 'b';
    }
    else {
    $dtheme = 'd';
    }

    if ($classes == 'helpheading') { 
    //keeps wrap from help headings in dialog 
     $content = parent::heading($text, $level, $classes, $id);
    } else {
    $content  = html_writer::start_tag('div', array('class'=>'headingwrap ui-bar-'.$dtheme .' ui-footer'));
    $classes .= ' ui-title';
    $content .= parent::heading($text, $level, $classes, $id);
    $content .= html_writer::end_tag('div');
    }
    return $content;
}
 
  public function box2($contents, $classes = 'generalbox2', $id = null) {
    if (!empty($this->page->theme->settings->mswatch)) {
    $showswatch = $this->page->theme->settings->mswatch;
    } else {
    $showswatch = 'light';
    }
    if ($showswatch == 'light') {
    $dtheme = 'b';
    }
    else {
    $dtheme = 'd';
    }
          $content  = html_writer::start_tag('ul', array('data-role'=>'listview', 'data-inset'=>'true', 'data-dividertheme'=>''.$dtheme .''));
          $content .= $contents;
          $content .= html_writer::end_tag('ul');
        return $content;
    }

 
        function block(block_contents $bc, $region) {
        if (!empty($this->page->theme->settings->mswatch)) {
        $showswatch = $this->page->theme->settings->mswatch;
        } else {
        $showswatch = '';
        }
        if ($showswatch == 'light') {
        $dtheme = 'd';
        }
        else {
        $dtheme = 'c';
         }

        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }
        if ($bc->collapsible == block_contents::HIDDEN) {
        
        }
        if (!empty($bc->controls)) {
          
        }
        
        $skiptitle = strip_tags($bc->title);
        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::tag('a', get_string('skipa', 'access', $skiptitle), array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'));
            $skipdest = html_writer::tag('span', '', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'));
        }
        $testb = $bc->attributes['class'];
        
        if ($testb == "block_calendar_month2  block") {
        $output  = html_writer::start_tag('span');
        }
        
        else if ($testb == "block_course_overview  block") {
        $output  = html_writer::start_tag('div');
        }
        
        else {
        $output  = html_writer::start_tag('div', array('data-role'=>'collapsible','data-collapsed'=>'true','data-content-theme'=>$dtheme));
        }
        
        $colheader = $this->block_header($bc);
        if ($colheader == "") {
            $colheader = "&nbsp;";
        }
        
        $output  .= html_writer::start_tag('h1');
        $output .= $colheader;
        $output .= html_writer::end_tag('h1');
        $output .= html_writer::start_tag('div', $bc->attributes);
        $output .= $this->block_content($bc);

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        $output .= $this->block_annotation($bc);

        $output .= $skipdest;

        return $output;
    }

 
  
    protected function block_header(block_contents $bc) {

        $title = '';
        if ($bc->title) {
            $title = $bc->title;
        }

            $controlshtml = "";
        $output = '';
        if ($title || $controlshtml) {
           $output .= html_writer::tag('div', html_writer::tag('div', html_writer::tag('div', '', array('class'=>'block_action')). $title . $controlshtml, array('class' => 'title')), array('class' => 'header'));
           
        }
        return $output;
    }

  protected function init_block_hider_js(block_contents $bc) {
        if (!empty($bc->attributes['id']) and $bc->collapsible != block_contents::NOT_HIDEABLE) {
            $config = new stdClass;
            $config->id = $bc->attributes['id'];
            $config->title = strip_tags($bc->title);
            $config->preference = 'block' . $bc->blockinstanceid . 'hidden';
            $config->tooltipVisible = get_string('hideblocka', 'access', $config->title);
            $config->tooltipHidden = get_string('showblocka', 'access', $config->title);

        }
    }
 
  public function navbar() {
         $items = $this->page->navbar->get_items();

        $count = 0;

        $htmlblocks = array();
        // Iterate the navarray and display each node
        $itemcount = count($items);
        $separator = "";
       
      for ($i=0;$i < $itemcount;$i++) {
            $item = $items[$i];
            $item->hideicon = true;
            
            if ($i===0) {
        
                $testg = $item->action;
                $content = html_writer::tag('option', $this->render($item), array('value' => ''.$testg.''));
                
            } else {
                //by john to check for action type and list div
                if (empty($item->action)) {
                  $content = "";
                } else {
                $testg = $item->action;
                $content = html_writer::tag('option', $this->render($item), array('value' => ''.$testg.''));
                }
            }
            $htmlblocks[] = $content;
        }

    
    if (!empty($this->page->theme->settings->mswatch)) {
    $showswatch = $this->page->theme->settings->mswatch;
    } else {
    $showswatch = "";
    }
    if ($showswatch == "light") {
    $dtheme = "b";
    }
    else {
    $dtheme = "d";
    }

        $navbarcontent =  join('', $htmlblocks);
        // XHTML
        return $navbarcontent;
    } 
 
 
 protected function render_navigation_node(navigation_node $item) {
        $content = $item->get_content();
        $title = $item->get_title();
        if ($item->icon instanceof renderable && !$item->hideicon) {
            $icon = $this->render($item->icon);
            $content = $icon.$content; // use CSS for spacing of icons
        }
        if ($item->helpbutton !== null) {
            $content = trim($item->helpbutton).html_writer::tag('span', $content, array('class'=>'clearhelpbutton'));
        }
        if ($content === '') {
            return '';
        }
        if ($item->action instanceof action_link) {
            //TODO: to be replaced with something else
            $link = $item->action;
            if ($item->hidden) {
                $link->add_class('dimmed');
            }
            $content = $this->render($link);
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
        return $content;
    }

 
   public function login_info() {
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
            $context = get_context_instance(CONTEXT_COURSE, $course->id);

            $fullname = fullname($USER, true);
            
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
           $username = "";
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo.get_string('loggedinasguest').
                          " (<a href=\"$loginurl\">".get_string('login').'</a>)';
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.format_string($role->name);
                }
             
                 
            } else {
                $loggedinas = $realuserinfo.$username.' '.
                          '<a id="mypower" data-inline="true" data-role="button" data-icon="mypower" data-ajax="false" class="ui-btn-right mypower" href="'.$CFG->wwwroot.'/login/logout.php?sesskey='.sesskey().'\">'.get_string('logout').'</a>';
            }
        } else {
             $loggedinas = '<a data-role="button" data-icon="alert" class="ui-btn-right nolog" href="'.$loginurl.'" data-prefetch>'.get_string('login').'</a>';            
        }

        $loggedinas = $loggedinas;

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
                        if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_SYSTEM))) {
                            $loggedinas .= ' (<a href="'.$CFG->wwwroot.'/course/report/log/index.php'.
                                                 '?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }

 
 public function login_infoB() {
        global $USER, $CFG, $DB, $SESSION;

        if (during_initial_install()) {
            return '';
        }

        $loginapge = ((string)$this->page->url === get_login_url());
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
            $context = get_context_instance(CONTEXT_COURSE, $course->id);

            $fullname = fullname($USER, true);
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            $username = '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$USER->id.'">'.$fullname.'</a>';
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo.get_string('loggedinasguest');
                if (!$loginapge) {
                    $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
                }
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.format_string($role->name);
                }
                $loggedinas = get_string('loggedinas', 'moodle', $username).$rolename.
                          " (<a href=\"$CFG->wwwroot/course/view.php?id=$course->id&amp;switchrole=0&amp;sesskey=".sesskey()."\">".get_string('switchrolereturn').'</a>)';
            } else {
                $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username).' '.
                          " (<a href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\" data-ajax=\"false\">".get_string('logout').'</a>)';
            }
        } else {
            $loggedinas = get_string('loggedinnot', 'moodle');
            if (!$loginapge) {
                $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
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
                        if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_SYSTEM))) {
                            $loggedinas .= ' (<a href="'.$CFG->wwwroot.'/course/report/log/index.php'.
                                                 '?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }

 
     public function redirect_message($encodedurl, $message, $delay, $debugdisableredirect) {
        global $CFG;
        $url = str_replace('&amp;', '&', $encodedurl);
        //the below to fix redirect issues with ajax... John
        $encodedurl = str_replace('#', '&', $encodedurl);
        
         $urlcheck = substr($encodedurl, 0, 4);
         
        
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
         if ($urlcheck != "http") {
         //if it is not a full http request, do local ajax load.
               $output .= '<div class="continuebutton"><a data-ajax="false" data-role="button" href="'. $encodedurl .'">'. get_string('continue') .'</a></div>';
               }
               else {
        $output .= '<div class="continuebutton"><a data-ajax="false" data-role="button" href="'. $encodedurl .'">'. get_string('continue') .'</a></div>';
                }
        if ($debugdisableredirect) {
            $output .= '<p><strong>Error output, so disabling automatic redirect.</strong></p>';
        }
        $output .= $this->footer();
        return $output;
    }
  



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
        $url = new moodle_url('/help.php', array('component' => $helpicon->component, 'identifier' => $helpicon->identifier, 'lang'=>current_language(), 'theme'=>'mymobile'));

        // note: this title is displayed only if JS is disabled, otherwise the link will have the new ajax tooltip
        $title = get_string('helpprefix2', '', trim($title, ". \t"));

        $attributes = array('href'=>$url, 'title'=>$title);
        $id = html_writer::random_id('helpicon');
        $attributes['id'] = $id;
        $attributes['rel'] = 'notexternal';
        $attributes['data-rel'] = 'dialog';
        $attributes['data-transition'] = 'slideup';
        $output = html_writer::tag('a', $output, $attributes);

        // and finally span
        return html_writer::tag('span', $output, array('class' => 'helplink2'));
    }
 
 

 
       protected function render_single_button(single_button $button) {
        $attributes = array('type'     => 'submit',
                            'value'    => $button->label,
                            'disabled' => $button->disabled ? 'disabled' : null,
                            'title'    => $button->tooltip);

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
        $output = html_writer::tag('div', $output, array('rel' => ''.$button->url->out_omit_querystring().''));
        
        // now the form itself around it
        $url = $button->url->out_omit_querystring(); // url without params
     
       $urlcheck = substr($url, 0, 4);
       if ($url === '') {
            $url = '#'; // there has to be always some action
        }
       
       //if the url has http, cool, if not we need to add it, JOHN 
       if ($urlcheck != 'http') {
       $url = $this->page->url->out_omit_querystring();
               }
       
        
        
        $attributes = array('method' => $button->method,
                            'action' => $url,
                            'id'     => $button->formid);
        $output = html_writer::tag('form', $output, $attributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $button->class));
    }
 
 
 
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
        $cutpos = strpos($rendered, self::MAIN_CONTENT_TOKEN);
        if ($cutpos === false) {
            //turned off error by john for ajax load of blocks without main content.
           // throw new coding_exception('page layout file ' . $layoutfile .
            //        ' does not contain the string "' . self::MAIN_CONTENT_TOKEN . '".');
        }
        $header = substr($rendered, 0, $cutpos);
        $footer = substr($rendered, $cutpos + strlen(self::MAIN_CONTENT_TOKEN));

        if (empty($this->contenttype)) {
            debugging('The page layout file did not call $OUTPUT->doctype()');
            $header = $this->doctype() . $header;
        }

        send_headers($this->contenttype, $this->page->cacheable);

        $this->opencontainers->push('header/footer', $footer);
        $this->page->set_state(moodle_page::STATE_IN_BODY);

        return $header . $this->skip_link_target('maincontent');
    }

  public function notification($message, $classes = 'notifyproblem') {
        return html_writer::tag('div', clean_text($message), array('data-role'=>'none', 'data-icon'=>'alert', 'data-theme'=>'d', 'class' => renderer_base::prepare_classes($classes)));
    }
 
 public function blocks_for_region($region) {
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $output = '';
        foreach ($blockcontents as $bc) {
            if ($bc instanceof block_contents) {
                if (!($bc->attributes['class'] == 'block_settings  block')
                        && !($bc->attributes['class'] == 'block_navigation  block')) {
                    $output .= $this->block($bc, $region);
                }
            } else if ($bc instanceof block_move_target) {
                $output .= $this->block_move_target($bc);
            } else {
                throw new coding_exception('Unexpected type of thing (' . get_class($bc) . ') found in list of block contents.');
            }
        }
        //by john for no blocks
        if ($output =="") {
        //$output = "<h2>No blocks found.</h2>";
        }
        
        return $output;
    }


  
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
        $output .= html_writer::empty_tag('input data-inline="true"', array('type'=>'submit', 'value'=>get_string('go')));
        $output .= html_writer::tag('noscript', html_writer::tag('div', $go), array('style'=>'inline'));

        $nothing = empty($select->nothing) ? false : key($select->nothing);
        $this->page->requires->js_init_call('M.util.init_select_autosubmit', array($select->formid, $select->attributes['id'], $nothing));

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output);

        // now the form itself around it
        $formattributes = array('method' => $select->method,
                                'action' => $select->url->out_omit_querystring(),
                                'id'     => $select->formid);
        $output = html_writer::tag('form', $output, $formattributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $select->class));
    }

}



class theme_mymobile_mod_choice_renderer extends plugin_renderer_base {

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
            $rande = rand();
            $option->attributes->name = 'answer';
            $option->attributes->type = 'radio';
            $option->attributes->id = "answer$rande";

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
     * @param object $choices
     * @param bool $forcepublish
     * @return string
     */
    public function display_result($choices, $forcepublish = false) {
        if (empty($forcepublish)) { //allow the publish setting to be overridden
            $forcepublish = $choices->publish;
        }

        $displaylayout = $choices->display;

        if ($forcepublish) {  //CHOICE_PUBLISH_NAMES
            return $this->display_publish_name_vertical($choices);
        } else { //CHOICE_PUBLISH_ANONYMOUS';
            if ($displaylayout == DISPLAY_HORIZONTAL_LAYOUT) {
                return $this->display_publish_anonymous_horizontal($choices);
            }
            return $this->display_publish_anonymous_vertical($choices);
        }
    }

    /**
     * Returns HTML to display choices result
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


    /**
     * Returns HTML to display choices result
     * @param object $choices
     * @return string
     */
    public function display_publish_anonymous_vertical($choices) {
        global $CHOICE_COLUMN_HEIGHT;

        $html = '';
        $table = new html_table();
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->attributes['class'] = 'results anonymous ';
        $table->data = array();
        $count = 0;
        ksort($choices->options);
        $columns = array();
        $rows = array();

        foreach ($choices->options as $optionid => $options) {
            $numberofuser = 0;
            if (!empty($options->user)) {
               $numberofuser = count($options->user);
            }
            $height = 0;
            $percentageamount = 0;
            if($choices->numberofuser > 0) {
               $height = ($CHOICE_COLUMN_HEIGHT * ((float)$numberofuser / (float)$choices->numberofuser));
               $percentageamount = ((float)$numberofuser/(float)$choices->numberofuser)*100.0;
            }

            $displaydiagram = html_writer::tag('img','', array('style'=>'height:'.$height.'px;width:49px;', 'alt'=>'', 'src'=>$this->output->pix_url('column', 'choice')));

            $cell = new html_table_cell();
            $cell->text = $displaydiagram;
            $cell->attributes = array('class'=>'graph vertical data');
            $columns[] = $cell;
        }
        $rowgraph = new html_table_row();
        $rowgraph->cells = $columns;
        $rows[] = $rowgraph;

        $columns = array();
        $printskiplink = true;
        foreach ($choices->options as $optionid => $options) {
            $columndata = '';
            $numberofuser = 0;
            if (!empty($options->user)) {
               $numberofuser = count($options->user);
            }

            if ($printskiplink) {
                $columndata .= html_writer::tag('div', '', array('class'=>'skip-block-to', 'id'=>'skipresultgraph'));
                $printskiplink = false;
            }

            if ($choices->showunanswered && $optionid == 0) {
                $columndata .= html_writer::tag('div', format_string(get_string('notanswered', 'choice')), array('class'=>'option'));
            } else if ($optionid > 0) {
                $columndata .= html_writer::tag('div', format_string($choices->options[$optionid]->text), array('class'=>'option'));
            }
            $columndata .= html_writer::tag('div', ' ('.$numberofuser.')', array('class'=>'numberofuser', 'title'=> get_string('numberofuser', 'choice')));

            if($choices->numberofuser > 0) {
               $percentageamount = ((float)$numberofuser/(float)$choices->numberofuser)*100.0;
            }
            $columndata .= html_writer::tag('div', format_float($percentageamount,1). '%', array('class'=>'percentage'));

            $cell = new html_table_cell();
            $cell->text = $columndata;
            $cell->attributes = array('class'=>'data header');
            $columns[] = $cell;
        }
        $rowdata = new html_table_row();
        $rowdata->cells = $columns;
        $rows[] = $rowdata;

        $table->data = $rows;

        $header = html_writer::tag('h2',format_string(get_string("responses", "choice")));
        $html .= html_writer::tag('div', $header, array('class'=>'responseheader'));
        $html .= html_writer::tag('a', get_string('skipresultgraph', 'choice'), array('href'=>'#skipresultgraph', 'class'=>'skip-block'));
        $html .= html_writer::tag('div', html_writer::table($table), array('class'=>'response'));

        return $html;
    }

    /**
     * Returns HTML to display choices result
     * @param object $choices
     * @return string
     */
    public function display_publish_anonymous_horizontal($choices) {
        global $CHOICE_COLUMN_WIDTH;

        $table = new html_table();
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->attributes['class'] = 'results anonymous ';
        $table->data = array();

        $count = 0;
        ksort($choices->options);

        $rows = array();
        foreach ($choices->options as $optionid => $options) {
            $numberofuser = 0;
            $graphcell = new html_table_cell();
            if (!empty($options->user)) {
               $numberofuser = count($options->user);
            }

            $width = 0;
            $percentageamount = 0;
            $columndata = '';
            if($choices->numberofuser > 0) {
               $width = ($CHOICE_COLUMN_WIDTH * ((float)$numberofuser / (float)$choices->numberofuser));
               $percentageamount = ((float)$numberofuser/(float)$choices->numberofuser)*100.0;
            }
            $displaydiagram = html_writer::tag('img','', array('style'=>'height:50px; width:'.$width.'px', 'alt'=>'', 'src'=>$this->output->pix_url('row', 'choice')));

            $skiplink = html_writer::tag('a', get_string('skipresultgraph', 'choice'), array('href'=>'#skipresultgraph'. $optionid, 'class'=>'skip-block'));
            $skiphandler = html_writer::tag('span', '', array('class'=>'skip-block-to', 'id'=>'skipresultgraph'.$optionid));

            $graphcell->text = $skiplink . $displaydiagram . $skiphandler;
            $graphcell->attributes = array('class'=>'graph horizontal');

            $datacell = new html_table_cell();
            if ($choices->showunanswered && $optionid == 0) {
                $columndata .= html_writer::tag('div', format_string(get_string('notanswered', 'choice')), array('class'=>'option'));
            } else if ($optionid > 0) {
                $columndata .= html_writer::tag('div', format_string($choices->options[$optionid]->text), array('class'=>'option'));
            }
            $columndata .= html_writer::tag('div', ' ('.$numberofuser.')', array('title'=> get_string('numberofuser', 'choice'), 'class'=>'numberofuser'));

            if($choices->numberofuser > 0) {
               $percentageamount = ((float)$numberofuser/(float)$choices->numberofuser)*100.0;
            }
            $columndata .= html_writer::tag('div', format_float($percentageamount,1). '%', array('class'=>'percentage'));

            $datacell->text = $columndata;
            $datacell->attributes = array('class'=>'header');

            $row = new html_table_row();
            $row->cells = array($datacell, $graphcell);
            $rows[] = $row;
        }

        $table->data = $rows;

        $html = '';
        $header = html_writer::tag('h2',format_string(get_string("responses", "choice")));
        $html .= html_writer::tag('div', $header, array('class'=>'responseheader'));
        $html .= html_writer::table($table);

        return $html;
    }
}