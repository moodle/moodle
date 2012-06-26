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
 * @package    portfolio
 * @subpackage flickr
 * @copyright  2008 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/portfolio/plugin.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/flickrlib.php');

class portfolio_plugin_flickr extends portfolio_plugin_push_base {

    private $flickr;
    private $token;
    private $raw_sets;

    public function supported_formats() {
        return array(PORTFOLIO_FORMAT_IMAGE);
    }

    public static function get_name() {
        return get_string('pluginname', 'portfolio_flickr');
    }

    public function prepare_package() {

    }

    public function send_package() {
        foreach ($this->exporter->get_tempfiles() as $file) {
            // @TODO get max size from flickr people_getUploadStatus
            $filesize = $file->get_filesize();

            if ($file->is_valid_image()) {
                $return = $this->flickr->upload($file, array(
                        'title'         => $this->get_export_config('title'),
                        'description'   => $this->get_export_config('description'),
                        'tags'          => $this->get_export_config('tags'),
                        'is_public'     => $this->get_export_config('is_public'),
                        'is_friend'     => $this->get_export_config('is_friend'),
                        'is_family'     => $this->get_export_config('is_family'),
                        'safety_level'  => $this->get_export_config('safety_level'),
                        'content_type'  => $this->get_export_config('content_type'),
                        'hidden'        => $this->get_export_config('hidden')));
                if ($return) {
                    // Attach photo to a set if requested
                    if ($this->get_export_config('set')) {
                        $this->flickr->photosets_addPhoto($this->get_export_config('set'),
                            $this->flickr->parsed_response['photoid']);
                    }
                } else {
                    throw new portfolio_plugin_exception('uploadfailed', 'portfolio_flickr',
                        $this->flickr->error_code . ': ' . $this->flickr->error_msg);
                }
            }
        }
    }

    public static function allows_multiple_instances() {
        return false;
    }

    public function get_interactive_continue_url() {
        return $this->flickr->urls_getUserPhotos();
    }

    public function expected_time($callertime) {
        return $callertime;
    }

    public static function get_allowed_config() {
        return array('apikey', 'sharedsecret');
    }

    public static function has_admin_config() {
        return true;
    }

    public static function admin_config_form(&$mform) {
        global $CFG;

        $strrequired = get_string('required');
        $mform->addElement('text', 'apikey', get_string('apikey', 'portfolio_flickr'), array('size' => 30));
        $mform->addRule('apikey', $strrequired, 'required', null, 'client');
        $mform->addElement('text', 'sharedsecret', get_string('sharedsecret', 'portfolio_flickr'));
        $mform->addRule('sharedsecret', $strrequired, 'required', null, 'client');
        $a = new stdClass();
        $a->applyurl = 'http://www.flickr.com/services/api/keys/apply/';
        $a->keysurl = 'http://www.flickr.com/services/api/keys/';
        $a->callbackurl = $CFG->wwwroot . '/portfolio/add.php?postcontrol=1&type=flickr';
        $mform->addElement('static', 'setupinfo', get_string('setupinfo', 'portfolio_flickr'),
            get_string('setupinfodetails', 'portfolio_flickr', $a));
    }

    public function has_export_config() {
        return true;
    }

    public function get_allowed_user_config() {
        return array('authtoken', 'nsid');
    }

    public function steal_control($stage) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }
        if ($this->token) {
            return false;
        }

        $token = $this->get_user_config('authtoken', $this->get('user')->id);
        $nsid = $this->get_user_config('nsid', $this->get('user')->id);

        $this->flickr = new phpFlickr($this->get_config('apikey'), $this->get_config('sharedsecret'), $token);

        if (!empty($token)) {
            $this->token = $token;
            $this->flickr = new phpFlickr($this->get_config('apikey'), $this->get_config('sharedsecret'), $token);
            return false;
        }
        return $this->flickr->auth('write');
    }

    public function post_control($stage, $params) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return;
        }
        if (!array_key_exists('frob', $params) || empty($params['frob'])) {
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_flickr');
        }

        $this->flickr = new phpFlickr($this->get_config('apikey'), $this->get_config('sharedsecret'));

        $auth_info = $this->flickr->auth_getToken($params['frob']);

        $this->set_user_config(array('authtoken' => $auth_info['token'], 'nsid' => $auth_info['user']['nsid']), $this->get('user')->id);
    }

    public function export_config_form(&$mform) {
        $mform->addElement('text', 'plugin_title', get_string('title', 'portfolio_flickr'));
        $mform->addElement('textarea', 'plugin_description', get_string('description'));
        $mform->addElement('text', 'plugin_tags', get_string('tags'));
        $mform->addElement('checkbox', 'plugin_is_public', get_string('ispublic', 'portfolio_flickr'));
        $mform->addElement('checkbox', 'plugin_is_family', get_string('isfamily', 'portfolio_flickr'));
        $mform->addElement('checkbox', 'plugin_is_friend', get_string('isfriend', 'portfolio_flickr'));

        $mform->disabledIf('plugin_is_friend', 'plugin_is_public', 'checked');
        $mform->disabledIf('plugin_is_family', 'plugin_is_public', 'checked');

        $safety_levels = array(1 => $this->get_export_value_name('safety_level', 1),
                               2 => $this->get_export_value_name('safety_level', 2),
                               3 => $this->get_export_value_name('safety_level', 3));

        $content_types = array(1 => $this->get_export_value_name('content_type', 1),
                               2 => $this->get_export_value_name('content_type', 2),
                               3 => $this->get_export_value_name('content_type', 3));

        $hidden_values = array(1,2);

        $mform->addElement('select', 'plugin_safety_level', get_string('safetylevel', 'portfolio_flickr'), $safety_levels);
        $mform->addElement('select', 'plugin_content_type', get_string('contenttype', 'portfolio_flickr'), $content_types);
        $mform->addElement('advcheckbox', 'plugin_hidden', get_string('hidefrompublicsearches', 'portfolio_flickr'), get_string('yes'), null, $hidden_values);

        $mform->setDefaults(array('plugin_is_public' => true));

        $sets = $this->get_sets();

        if (!empty($sets)) {
            $sets[0] = '----';
            $mform->addElement('select', 'plugin_set', get_string('set', 'portfolio_flickr'), $sets);
        }
    }

    private function get_sets() {
        if (empty($this->raw_sets)) {
            $this->raw_sets = $this->flickr->photosets_getList();
        }

        $sets = array();
        foreach ($this->raw_sets['photoset'] as $set_data) {
            $sets[$set_data['id']] = $set_data['title'];
        }
        return $sets;
    }

    public function get_allowed_export_config() {
        return array('set', 'title', 'description', 'tags', 'is_public', 'is_family', 'is_friend', 'safety_level', 'content_type', 'hidden');
    }

    public function get_export_summary() {
        return array(get_string('set', 'portfolio_flickr') => $this->get_export_value_name('set', $this->get_export_config('set')),
                     get_string('title', 'portfolio_flickr') => $this->get_export_config('title'),
                     get_string('description') => $this->get_export_config('description'),
                     get_string('tags') => $this->get_export_config('tags'),
                     get_string('ispublic', 'portfolio_flickr') => $this->get_export_value_name('is_public', $this->get_export_config('is_public')),
                     get_string('isfamily', 'portfolio_flickr') => $this->get_export_value_name('is_family', $this->get_export_config('is_family')),
                     get_string('isfriend', 'portfolio_flickr') => $this->get_export_value_name('is_friend', $this->get_export_config('is_friend')),
                     get_string('safetylevel', 'portfolio_flickr') => $this->get_export_value_name('safety_level', $this->get_export_config('safety_level')),
                     get_string('contenttype', 'portfolio_flickr') => $this->get_export_value_name('content_type', $this->get_export_config('content_type')),
                     get_string('hidefrompublicsearches', 'portfolio_flickr') => $this->get_export_value_name('hidden', $this->get_export_config('hidden')));
    }

    private function get_export_value_name($param, $value) {
        $params = array('set' => $this->get_sets(),
                        'is_public' => array(0 => get_string('no'), 1 => get_string('yes')),
                        'is_family' => array(0 => get_string('no'), 1 => get_string('yes')),
                        'is_friend' => array(0 => get_string('no'), 1 => get_string('yes')),
                        'safety_level' => array(1 => get_string('safe', 'portfolio_flickr'),
                                                2 => get_string('moderate', 'portfolio_flickr'),
                                                3 => get_string('restricted', 'portfolio_flickr')),
                        'content_type' => array(1 => get_string('photo', 'portfolio_flickr'),
                                                2 => get_string('screenshot', 'portfolio_flickr'),
                                                3 => get_string('other', 'portfolio_flickr')),
                        'hidden' => array(1 => get_string('no'), 2 => get_string('yes')));

        if (isset($params[$param][$value])) {
            return $params[$param][$value];
        } else {
            return '-';
        }
    }

    /**
     * For now, flickr doesn't support this because we can't dynamically construct callbackurl
     */
    public static function allows_multiple_exports() {
        return false;
    }
}
