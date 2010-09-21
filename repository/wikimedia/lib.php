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
        parent::__construct($repositoryid, $context, $options);
        $this->keyword = optional_param('wikimedia_keyword', '', PARAM_RAW);
        if (empty($this->keyword)) {
            $this->keyword = optional_param('s', '', PARAM_RAW);
        }
    }
    public function get_listing($path = '', $page = '') {
        $client = new wikimedia;
        $list = array();
        $list['list'] = $client->search_images($this->keyword);
        $list['nologin'] = true;
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

        $form = array();
        $form['login'] = array($keyword);
        return $form;
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
