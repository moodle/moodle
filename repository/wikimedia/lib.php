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

require_once('wikimedia.php');

/**
 * repository_wikimedia class
 * This is a class used to browse images from wikimedia
 *
 * @since 2.0
 * @package    repository
 * @subpackage wikimedia
 * @copyright  2009 Dongsheng Cai
 * @author     Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_wikimedia extends repository {
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        global $SESSION;
        parent::__construct($repositoryid, $context, $options);
        $this->keyword = optional_param('wikimedia_keyword', '', PARAM_RAW);
        if (empty($this->keyword)) {
            $this->keyword = optional_param('s', '', PARAM_RAW);
        }
        $sess_keyword = 'wikimedia_'.$this->id.'_keyword';
        if (empty($this->keyword) && optional_param('page', '', PARAM_RAW)) {
            // This is the request of another page for the last search, retrieve the cached keyword
            if (isset($SESSION->{$sess_keyword})) {
                $this->keyword = $SESSION->{$sess_keyword};
            }
        } else if (!empty($this->keyword)) {
            // save the search keyword in the session so we can retrieve it later
            $SESSION->{$sess_keyword} = $this->keyword;
        }
    }
    public function get_listing($path = '', $page = '') {
        $client = new wikimedia;
        $list = array();
        $list['page'] = (int)$page;
        if ($list['page'] < 1) {
            $list['page'] = 1;
        }
        $list['list'] = $client->search_images($this->keyword, $list['page'] - 1);
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
        if ($this->options['ajax']) {
            $form = array();
            $form['login'] = array($keyword);
            $form['nologin'] = true;
            $form['norefresh'] = true;
            $form['nosearch'] = true;
            $form['allowcaching'] = true; // indicates that login form can be cached in filepicker.js
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
    public function search($search_text) {
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
}
