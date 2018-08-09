<?php
// This file is part of The Bootstrap 3 Moodle theme
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

defined('MOODLE_INTERNAL') || die();

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_kmboost
 * @copyright  2012
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot."/course/renderer.php");

class theme_kmboost_core_renderer extends core_course_renderer {

    protected function render_custom_menu(custom_menu $menu) {
        global $CFG, $USER, $DB;
        
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if ($haslangmenu) {
            $strlang =  get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }
        
        $menu->add(get_string('home'), new moodle_url($CFG->wwwroot), '', 1);
        $menu->add(get_string('aboutus', 'theme_kmboost'), new moodle_url($CFG->wwwroot."/course/view.php?id=201"), '', 3);
        $menu->add(get_string('techsupport', 'theme_kmboost'), new moodle_url($CFG->wwwroot."/local/customlogin/forgot.php"), '', 4);
        $abtar = $menu->add(get_string('availability', 'theme_kmboost'), null,'atbar', 5);
        $abtar->add(get_string('availability', 'theme_kmboost'), new moodle_url($CFG->wwwroot . "/theme/kmboost/doc/accessibility.pdf"));
/*old version   
       $mycourses = $menu->add(get_string('mycourses'), null, '', 2);
        
        $courses = enrol_get_all_users_courses($USER->id, true, null, 'visible DESC, sortorder ASC');
        foreach ($courses as $currcourse) {
            $mycourses->add(format_string($currcourse->fullname), new moodle_url($CFG->wwwroot."/course/view.php?id=".$currcourse->id));
        }
*/

 $courses = enrol_get_all_users_courses($USER->id, true, null, 'visible DESC, sortorder ASC');

        if (!empty($courses)) {
            $mycourses = $menu->add(get_string('mycourses'), null, '', 2);
            foreach ($courses as $currcourse) {
                $mycourses->add(format_string($currcourse->fullname), new moodle_url($CFG->wwwroot . "/course/view.php?id=" . $currcourse->id));
            }
        }        

        $content = '';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        $children = $menu->get_children();
        if (count($children) == 0) {
            return false;
        }

        

        return $content;
    }

    public function render_custom_menu_item(custom_menu_item $menunode, $level = 0, $direction = '') {
        $content = theme_bootstrap_core_renderer::render_custom_menu_item($menunode, $level, $direction);
        
        return $content;
    }

    /**
     * Produces a header for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_header(block_contents $bc) {

        $title = '';
        if ($bc->title) {
            $attributes = array();
            if ($bc->blockinstanceid) {
                $attributes['id'] = 'instance-'.$bc->blockinstanceid.'-header';
            }
            $title = html_writer::tag('h2', $bc->title, $attributes);
            $title .= html_writer::empty_tag('hr', array('class' => 'star-primary'));
        }

        $blockid = null;
        if (isset($bc->attributes['id'])) {
            $blockid = $bc->attributes['id'];
        }
        $controlshtml = $this->block_controls($bc->controls, $blockid);

        $output = '';
        if ($title || $controlshtml) {
            $output .= html_writer::tag('div', html_writer::tag('div', html_writer::tag('div', '', array('class'=>'block_action')). $title . $controlshtml, array('class' => 'title')), array('class' => 'header'));
        }
        return $output;
    }

    public function user_picture(stdClass $user, array $options = null) {
        global $PAGE;
        if ($PAGE->bodyid == 'page-mod-forum-discuss' || $PAGE->bodyid == 'page-mod-forum-post' ) {
            $options = array('size' => '100');
        }
        if ($PAGE->bodyid == 'page-site-index' && isset($options['courseid'])) {
            $options = array('size' => '100');
        }

        $userpicture = new user_picture($user);
        foreach ((array)$options as $key=>$value) {
            if (array_key_exists($key, $userpicture)) {
                $userpicture->$key = $value;
            }
        }
        return $this->render($userpicture);
    }
}
