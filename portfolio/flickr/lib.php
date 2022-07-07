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
require_once($CFG->libdir.'/flickrclient.php');

class portfolio_plugin_flickr extends portfolio_plugin_push_base {

    /** @var flickr_client */
    private $flickr;
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
                $photoid = $this->flickr->upload($file, [
                    'title' => $this->get_export_config('title'),
                    'description' => $this->get_export_config('description'),
                    'tags' => $this->get_export_config('tags'),
                    'is_public' => $this->get_export_config('is_public'),
                    'is_friend' => $this->get_export_config('is_friend'),
                    'is_family' => $this->get_export_config('is_family'),
                    'safety_level' => $this->get_export_config('safety_level'),
                    'content_type' => $this->get_export_config('content_type'),
                    'hidden' => $this->get_export_config('hidden'),
                ]);

                if ($photoid === false) {
                    $this->set_user_config([
                        'accesstoken' => null,
                        'accesstokensecret' => null,
                    ]);
                    throw new portfolio_plugin_exception('uploadfailed', 'portfolio_flickr', '', 'Authentication failed');
                }

                // Attach photo to a set if requested.
                if ($this->get_export_config('set')) {
                    $result = $this->flickr->call('photosets.addPhoto', [
                        'photoset_id' => $this->get_export_config('set'),
                        'photo_id' => $photoid,
                    ], 'POST');
                }
            }
        }
    }

    public static function allows_multiple_instances() {
        return false;
    }

    public function get_interactive_continue_url() {
        return 'https://www.flickr.com/photos/organize';
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
        $mform->setType('apikey', PARAM_RAW_TRIMMED);
        $mform->addElement('text', 'sharedsecret', get_string('sharedsecret', 'portfolio_flickr'));
        $mform->addRule('sharedsecret', $strrequired, 'required', null, 'client');
        $mform->setType('sharedsecret', PARAM_RAW_TRIMMED);
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
        return array('accesstoken', 'accesstokensecret');
    }

    public function steal_control($stage) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }

        $accesstoken = $this->get_user_config('accesstoken');
        $accesstokensecret = $this->get_user_config('accesstokensecret');

        $callbackurl = new moodle_url('/portfolio/add.php', ['postcontrol' => 1, 'type' => 'flickr']);
        $this->flickr = new flickr_client($this->get_config('apikey'), $this->get_config('sharedsecret'), $callbackurl);

        if (!empty($accesstoken) && !empty($accesstokensecret)) {
            // The user has authenticated us already.
            $this->flickr->set_access_token($accesstoken, $accesstokensecret);
            return false;
        }

        $reqtoken = $this->flickr->request_token();
        $this->flickr->set_request_token_secret(['caller' => 'portfolio_flickr'], $reqtoken['oauth_token_secret']);

        $authurl = new moodle_url($reqtoken['authorize_url'], ['perms' => 'write']);

        return $authurl->out(false);
    }

    public function post_control($stage, $params) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return;
        }

        if (empty($params['oauth_token']) || empty($params['oauth_verifier'])) {
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_flickr');
        }

        $callbackurl = new moodle_url('/portfolio/add.php', ['postcontrol' => 1, 'type' => 'flickr']);
        $this->flickr = new flickr_client($this->get_config('apikey'), $this->get_config('sharedsecret'), $callbackurl);

        $secret = $this->flickr->get_request_token_secret(['caller' => 'portfolio_flickr']);

        // Exchange the request token for the access token.
        $accesstoken = $this->flickr->get_access_token($params['oauth_token'], $secret, $params['oauth_verifier']);

        // Store the access token and the access token secret as the user
        // config so that we can use it on behalf of the user in next exports.
        $this->set_user_config([
            'accesstoken' => $accesstoken['oauth_token'],
            'accesstokensecret' => $accesstoken['oauth_token_secret'],
        ]);
    }

    public function export_config_form(&$mform) {
        $mform->addElement('text', 'plugin_title', get_string('title', 'portfolio_flickr'));
        $mform->setType('plugin_title', PARAM_TEXT);
        $mform->addElement('textarea', 'plugin_description', get_string('description'));
        $mform->setType('plugin_description', PARAM_CLEANHTML);
        $mform->addElement('text', 'plugin_tags', get_string('tags'));
        $mform->setType('plugin_tags', PARAM_TAGLIST);
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

        $rawsets = $this->get_sets();
        if (!empty($rawsets)) {
            $sets = array('0' => '----');
            foreach ($rawsets as $key => $value) {
                $sets[$key] = $value;
            }
            $mform->addElement('select', 'plugin_set', get_string('set', 'portfolio_flickr'), $sets);
        }
    }

    /**
     * Fetches a list of current user's photosets (albums) on flickr.
     *
     * @return array (int)id => (string)title
     */
    private function get_sets() {

        if (empty($this->raw_sets)) {
            $this->raw_sets = $this->flickr->call('photosets.getList');
        }

        if ($this->raw_sets === false) {
            // Authentication failed, drop the locally stored token to force re-authentication.
            $this->set_user_config([
                'accesstoken' => null,
                'accesstokensecret' => null,
            ]);
            return array();
        }

        $sets = array();
        foreach ($this->raw_sets->photosets->photoset as $set) {
            $sets[$set->id] = $set->title->_content;
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
