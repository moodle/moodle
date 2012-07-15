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
 * This plugin is used to access picasa pictures
 *
 * @since 2.0
 * @package    repository_picasa
 * @copyright  2009 Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir.'/googleapi.php');

/**
 * Picasa Repository Plugin
 *
 * @since 2.0
 * @package    repository
 * @subpackage picasa
 * @copyright  2009 Dan Poltawski
 * @author     Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_picasa extends repository {
    private $googleoauth = null;

    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);

        $returnurl = new moodle_url('/repository/repository_callback.php');
        $returnurl->param('callback', 'yes');
        $returnurl->param('repo_id', $this->id);
        $returnurl->param('sesskey', sesskey());

        $clientid = get_config('picasa', 'clientid');
        $secret = get_config('picasa', 'secret');
        $this->googleoauth = new google_oauth($clientid, $secret, $returnurl, google_picasa::REALM);

        $this->check_login();
    }

    public function check_login() {
        return $this->googleoauth->is_logged_in();
    }

    public function print_login() {
        $url = $this->googleoauth->get_login_url();

        if ($this->options['ajax']) {
            $popup = new stdClass();
            $popup->type = 'popup';
            $popup->url = $url->out(false);
            return array('login' => array($popup));
        } else {
            echo '<a target="_blank" href="'.$url->out(false).'">'.get_string('login', 'repository').'</a>';
        }
    }

    public function get_listing($path='', $page = '') {
        $picasa = new google_picasa($this->googleoauth);

        $ret = array();
        $ret['dynload'] = true;
        $ret['manage'] = google_picasa::MANAGE_URL;
        $ret['list'] = $picasa->get_file_list($path);
        $ret['path'] = array((object)array('name'=>get_string('home'), 'path' => ''));
        if ($path) {
            $ret['path'][] = (object)array('name'=>$picasa->get_last_album_name(), 'path' => $path);
        }
        return $ret;
    }

    public function search($search_text, $page = 0) {
        $picasa = new google_picasa($this->googleoauth);

        $ret = array();
        $ret['manage'] = google_picasa::MANAGE_URL;
        $ret['list'] =  $picasa->do_photo_search($search_text);
        return $ret;
    }

    public function logout() {
        $this->googleoauth->log_out();
        return parent::logout();
    }

    public function supported_filetypes() {
        return array('web_image');
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    public static function get_type_option_names() {
        return array('clientid', 'secret', 'pluginname');
    }

    public static function type_config_form($mform, $classname = 'repository') {
        $a = new stdClass;
        $a->docsurl = get_docs_url('Google_OAuth_2.0_setup');
        $a->callbackurl = google_oauth::callback_url()->out(false);

        $mform->addElement('static', null, '', get_string('oauthinfo', 'repository_picasa', $a));

        parent::type_config_form($mform);
        $mform->addElement('text', 'clientid', get_string('clientid', 'repository_picasa'));
        $mform->setType('clientid', PARAM_RAW_TRIMMED);
        $mform->addElement('text', 'secret', get_string('secret', 'repository_picasa'));
        $mform->setType('secret', PARAM_RAW_TRIMMED);

        $strrequired = get_string('required');
        $mform->addRule('clientid', $strrequired, 'required', null, 'client');
        $mform->addRule('secret', $strrequired, 'required', null, 'client');
    }
}

// Icon for this plugin retrieved from http://www.iconspedia.com/icon/picasa-2711.html
// Where the license is said documented to be Free.
