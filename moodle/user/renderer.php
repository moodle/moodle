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
 * Provides user rendering functionality such as printing private files tree and displaying a search utility
 *
 * @package    core_user
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides user rendering functionality such as printing private files tree and displaying a search utility
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_renderer extends plugin_renderer_base {

    /**
     * Prints user files tree view
     * @return string
     */
    public function user_files_tree() {
        return $this->render(new user_files_tree);
    }

    /**
     * Render user files tree
     *
     * @param user_files_tree $tree
     * @return string HTML
     */
    public function render_user_files_tree(user_files_tree $tree) {
        if (empty($tree->dir['subdirs']) && empty($tree->dir['files'])) {
            $html = $this->output->box(get_string('nofilesavailable', 'repository'));
        } else {
            $htmlid = 'user_files_tree_'.uniqid();
            $module = array('name' => 'core_user', 'fullpath' => '/user/module.js');
            $this->page->requires->js_init_call('M.core_user.init_tree', array(false, $htmlid), false, $module);
            $html = '<div id="'.$htmlid.'">';
            $html .= $this->htmllize_tree($tree, $tree->dir);
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     * @param user_files_tree $tree
     * @param array $dir
     * @return string HTML
     */
    protected function htmllize_tree($tree, $dir) {
        global $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon(file_folder_icon(), $subdir['dirname'], 'moodle', array('class' => 'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.s($subdir['dirname']).'</div> '.
                $this->htmllize_tree($tree, $subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$tree->context->id.'/user/private'.
                $file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
            $image = $this->output->pix_icon(file_file_icon($file), $filename, 'moodle', array('class' => 'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.html_writer::link($url, $filename).
                '</div></li>';
        }
        $result .= '</ul>';

        return $result;
    }

    /**
     * Prints user search utility that can search user by first initial of firstname and/or first initial of lastname
     * Prints a header with a title and the number of users found within that subset
     * @param string $url the url to return to, complete with any parameters needed for the return
     * @param string $firstinitial the first initial of the firstname
     * @param string $lastinitial the first initial of the lastname
     * @param int $usercount the amount of users meeting the search criteria
     * @param int $totalcount the amount of users of the set/subset being searched
     * @param string $heading heading of the subset being searched, default is All Participants
     * @return string html output
     */
    public function user_search($url, $firstinitial, $lastinitial, $usercount, $totalcount, $heading = null) {
        global $OUTPUT;

        if ($firstinitial !== 'all') {
            set_user_preference('ifirst', $firstinitial);
        }
        if ($lastinitial !== 'all') {
            set_user_preference('ilast', $lastinitial);
        }

        if (!isset($heading)) {
            $heading = get_string('allparticipants');
        }

        $content = html_writer::start_tag('form', array('action' => new moodle_url($url)));
        $content .= html_writer::start_tag('div');

        // Search utility heading.
        $content .= $OUTPUT->heading($heading.get_string('labelsep', 'langconfig').$usercount.'/'.$totalcount, 3);

        // Initials bar.
        $prefixfirst = 'sifirst';
        $prefixlast = 'silast';
        $content .= $OUTPUT->initials_bar($firstinitial, 'firstinitial', get_string('firstname'), $prefixfirst, $url);
        $content .= $OUTPUT->initials_bar($lastinitial, 'lastinitial', get_string('lastname'), $prefixlast, $url);

        $content .= html_writer::end_tag('div');
        $content .= html_writer::tag('div', '&nbsp;');
        $content .= html_writer::end_tag('form');

        return $content;
    }

    /**
     * Displays the list of tagged users
     *
     * @param array $userlist
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @return string
     */
    public function user_list($userlist, $exclusivemode) {
        $tagfeed = new core_tag\output\tagfeed();
        foreach ($userlist as $user) {
            $userpicture = $this->output->user_picture($user, array('size' => $exclusivemode ? 100 : 35));
            $fullname = fullname($user);
            if (user_can_view_profile($user)) {
                $profilelink = new moodle_url('/user/view.php', array('id' => $user->id));
                $fullname = html_writer::link($profilelink, $fullname);
            }
            $tagfeed->add($userpicture, $fullname);
        }

        $items = $tagfeed->export_for_template($this->output);

        if ($exclusivemode) {
            $output = '<div><ul class="inline-list">';
            foreach ($items['items'] as $item) {
                $output .= '<li><div class="user-box">'. $item['img'] . $item['heading'] ."</div></li>\n";
            }
            $output .= "</ul></div>\n";
            return $output;
        }

        return $this->output->render_from_template('core_tag/tagfeed', $items);
    }

}

/**
 * User files tree
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_files_tree implements renderable {

    /**
     * @var context_user $context
     */
    public $context;

    /**
     * @var array $dir
     */
    public $dir;

    /**
     * Create user files tree object
     */
    public function __construct() {
        global $USER;
        $this->context = context_user::instance($USER->id);
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, 'user', 'private', 0);
    }
}
