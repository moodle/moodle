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
 * Google Documents Portfolio Plugin
 *
 * @author Dan Poltawski <talktodan@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->libdir.'/portfolio/plugin.php');
require_once($CFG->libdir . '/google/lib.php');

class portfolio_plugin_googledocs extends portfolio_plugin_push_base {
    /**
     * Google Client.
     * @var Google_Client
     */
    private $client = null;

    /**
     * Google Drive Service.
     * @var Google_Service_Drive
     */
    private $service = null;

    /**
     * URL to redirect Google to.
     * @var string
     */
    const REDIRECTURL = '/admin/oauth2callback.php';
    /**
     * Key in session which stores token (_drive_file is access level).
     * @var string
     */
    const SESSIONKEY = 'googledrive_accesstoken_drive_file';

    public function supported_formats() {
        return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_RICHHTML);
    }

    public static function get_name() {
        return get_string('pluginname', 'portfolio_googledocs');
    }

    public function prepare_package() {
        // We send the files as they are, no prep required.
        return true;
    }

    public function get_interactive_continue_url() {
        return 'http://drive.google.com/';
    }

    public function expected_time($callertime) {
        // We're forcing this to be run 'interactively' because the plugin
        // does not support running in cron.
        return PORTFOLIO_TIME_LOW;
    }

    public function send_package() {
        if (!$this->client) {
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_googledocs');
        }

        foreach ($this->exporter->get_tempfiles() as $file) {
            try {
                // Create drivefile object and fill it with data.
                $drivefile = new Google_Service_Drive_DriveFile();
                $drivefile->setTitle($file->get_filename());
                $drivefile->setMimeType($file->get_mimetype());

                $filecontent = $file->get_content();
                $createdfile = $this->service->files->insert($drivefile,
                                                            array('data' => $filecontent,
                                                                  'mimeType' => $file->get_mimetype(),
                                                                  'uploadType' => 'multipart'));
            } catch ( Exception $e ) {
                throw new portfolio_plugin_exception('sendfailed', 'portfolio_gdocs', $file->get_filename());
            }
        }
        return true;
    }
    /**
     * Gets the access token from session and sets it to client.
     *
     * @return null|string null or token.
     */
    private function get_access_token() {
        global $SESSION;
        if (isset($SESSION->{self::SESSIONKEY}) && $SESSION->{self::SESSIONKEY}) {
            $this->client->setAccessToken($SESSION->{self::SESSIONKEY});
            return $SESSION->{self::SESSIONKEY};
        }
        return null;
    }
    /**
     * Sets the access token to session
     *
     * @param string $token access token in json format
     * @return
     */
    private function set_access_token($token) {
        global $SESSION;
        $SESSION->{self::SESSIONKEY} = $token;
    }

    public function steal_control($stage) {
        global $CFG;
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }

        $this->initialize_oauth();
        if ($this->get_access_token()) {
            // Ensure that token is not expired.
            if (!$this->client->isAccessTokenExpired()) {
                return false;
            }
        }
        return $this->client->createAuthUrl();

    }

    public function post_control($stage, $params) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return;
        }
        // Get the authentication code send by Google.
        $code = isset($params['oauth2code']) ? $params['oauth2code'] : null;
        // Try to authenticate (throws exception which is catched higher).
        $this->client->authenticate($code);
        // Make sure we accually have access token at this time
        // ...and store it for further use.
        if ($accesstoken = $this->client->getAccessToken()) {
            $this->set_access_token($accesstoken);
        } else {
            throw new portfolio_plugin_exception('nosessiontoken', 'portfolio_gdocs');
        }
    }

    public static function allows_multiple_instances() {
        return false;
    }

    public static function has_admin_config() {
        return true;
    }

    public static function get_allowed_config() {
        return array('clientid', 'secret');
    }

    public static function admin_config_form(&$mform) {
        $a = new stdClass;
        $a->docsurl = get_docs_url('Google_OAuth_2.0_setup');
        $a->callbackurl = (new moodle_url(self::REDIRECTURL))->out(false);

        $mform->addElement('static', null, '', get_string('oauthinfo', 'portfolio_googledocs', $a));

        $mform->addElement('text', 'clientid', get_string('clientid', 'portfolio_googledocs'));
        $mform->setType('clientid', PARAM_RAW_TRIMMED);
        $mform->addElement('text', 'secret', get_string('secret', 'portfolio_googledocs'));
        $mform->setType('secret', PARAM_RAW_TRIMMED);

        $strrequired = get_string('required');
        $mform->addRule('clientid', $strrequired, 'required', null, 'client');
        $mform->addRule('secret', $strrequired, 'required', null, 'client');
    }

    private function initialize_oauth() {
        $redirecturi = new moodle_url(self::REDIRECTURL);
        $returnurl = new moodle_url('/portfolio/add.php');
        $returnurl->param('postcontrol', 1);
        $returnurl->param('id', $this->exporter->get('id'));
        $returnurl->param('sesskey', sesskey());

        $clientid = $this->get_config('clientid');
        $secret = $this->get_config('secret');

        // Setup Google client.
        $this->client = get_google_client();
        $this->client->setClientId($clientid);
        $this->client->setClientSecret($secret);
        $this->client->setScopes(array(Google_Service_Drive::DRIVE_FILE));
        $this->client->setRedirectUri($redirecturi->out(false));
        // URL to be called when redirecting from authentication.
        $this->client->setState($returnurl->out_as_local_url(false));
        // Setup drive upload service.
        $this->service = new Google_Service_Drive($this->client);

    }

    public function instance_sanity_check() {
        $clientid = $this->get_config('clientid');
        $secret = $this->get_config('secret');

        // If there is no oauth config (e.g. plugins upgraded from < 2.3 then
        // there will be no config and this plugin should be disabled.
        if (empty($clientid) or empty($secret)) {
            return 'nooauthcredentials';
        }
        return 0;
    }
}
