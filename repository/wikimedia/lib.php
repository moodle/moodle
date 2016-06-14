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
 * This plugin is used to access wikimedia files
 *
 * @since Moodle 2.0
 * @package    repository_wikimedia
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once(dirname(__FILE__) . '/wikimedia.php');

/**
 * repository_wikimedia class
 * This is a class used to browse images from wikimedia
 *
 * @since Moodle 2.0
 * @package    repository_wikimedia
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_wikimedia extends repository {

    /**
     * Returns maximum width for images
     *
     * Takes the maximum width for images eithre from search form or from
     * user preferences, updates user preferences if needed
     *
     * @return int
     */
    public function get_maxwidth() {
        $param = optional_param('wikimedia_maxwidth', 0, PARAM_INT);
        $pref = get_user_preferences('repository_wikimedia_maxwidth', WIKIMEDIA_IMAGE_SIDE_LENGTH);
        if ($param > 0 && $param != $pref) {
            $pref = $param;
            set_user_preference('repository_wikimedia_maxwidth', $pref);
        }
        return $pref;
    }

    /**
     * Returns maximum height for images
     *
     * Takes the maximum height for images eithre from search form or from
     * user preferences, updates user preferences if needed
     *
     * @return int
     */
    public function get_maxheight() {
        $param = optional_param('wikimedia_maxheight', 0, PARAM_INT);
        $pref = get_user_preferences('repository_wikimedia_maxheight', WIKIMEDIA_IMAGE_SIDE_LENGTH);
        if ($param > 0 && $param != $pref) {
            $pref = $param;
            set_user_preference('repository_wikimedia_maxheight', $pref);
        }
        return $pref;
    }

    public function get_listing($path = '', $page = '') {
        $client = new wikimedia;
        $list = array();
        $list['page'] = (int)$page;
        if ($list['page'] < 1) {
            $list['page'] = 1;
        }
        $list['list'] = $client->search_images($this->keyword, $list['page'] - 1,
                array('iiurlwidth' => $this->get_maxwidth(),
                    'iiurlheight' => $this->get_maxheight()));
        $list['nologin'] = true;
        $list['norefresh'] = true;
        $list['nosearch'] = true;
        if (!empty($list['list'])) {
            $list['pages'] = -1; // means we don't know exactly how many pages there are but we can always jump to the next page
        } else if ($list['page'] > 1) {
            $list['pages'] = $list['page']; // no images available on this page, this is the last page
        } else {
            $list['pages'] = 0; // no paging
        }
        return $list;
    }
   // login
    public function check_login() {
        global $SESSION;
        $this->keyword = optional_param('wikimedia_keyword', '', PARAM_RAW);
        if (empty($this->keyword)) {
            $this->keyword = optional_param('s', '', PARAM_RAW);
        }
        $sess_keyword = 'wikimedia_'.$this->id.'_keyword';
        if (empty($this->keyword) && optional_param('page', '', PARAM_RAW)) {
            // This is the request of another page for the last search, retrieve the cached keyword.
            if (isset($SESSION->{$sess_keyword})) {
                $this->keyword = $SESSION->{$sess_keyword};
            }
        } else if (!empty($this->keyword)) {
            // Save the search keyword in the session so we can retrieve it later.
            $SESSION->{$sess_keyword} = $this->keyword;
        }
        return !empty($this->keyword);
    }
    // if check_login returns false,
    // this function will be called to print a login form.
    public function print_login() {
        $keyword = new stdClass();
        $keyword->label = get_string('keyword', 'repository_wikimedia').': ';
        $keyword->id    = 'input_text_keyword';
        $keyword->type  = 'text';
        $keyword->name  = 'wikimedia_keyword';
        $keyword->value = '';
        $maxwidth = array(
            'label' => get_string('maxwidth', 'repository_wikimedia').': ',
            'type' => 'text',
            'name' => 'wikimedia_maxwidth',
            'value' => get_user_preferences('repository_wikimedia_maxwidth', WIKIMEDIA_IMAGE_SIDE_LENGTH),
        );
        $maxheight = array(
            'label' => get_string('maxheight', 'repository_wikimedia').': ',
            'type' => 'text',
            'name' => 'wikimedia_maxheight',
            'value' => get_user_preferences('repository_wikimedia_maxheight', WIKIMEDIA_IMAGE_SIDE_LENGTH),
        );
        if ($this->options['ajax']) {
            $form = array();
            $form['login'] = array($keyword, (object)$maxwidth, (object)$maxheight);
            $form['nologin'] = true;
            $form['norefresh'] = true;
            $form['nosearch'] = true;
            $form['allowcaching'] = false; // indicates that login form can NOT
            // be cached in filepicker.js (maxwidth and maxheight are dynamic)
            return $form;
        } else {
            echo <<<EOD
<table>
<tr>
<td>{$keyword->label}</td><td><input name="{$keyword->name}" type="text" /></td>
</tr>
</table>
<input type="submit" />
EOD;
        }
    }
    //search
    // if this plugin support global search, if this function return
    // true, search function will be called when global searching working
    public function global_search() {
        return false;
    }
    public function search($search_text, $page = 0) {
        $client = new wikimedia;
        $search_result = array();
        $search_result['list'] = $client->search_images($search_text);
        return $search_result;
    }
    // when logout button on file picker is clicked, this function will be
    // called.
    public function logout() {
        return $this->print_login();
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    /**
     * Return the source information
     *
     * @param stdClass $url
     * @return string|null
     */
    public function get_file_source_info($url) {
        return $url;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }
}
