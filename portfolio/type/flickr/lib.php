<?php
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/flickrlib.php');

class portfolio_plugin_flickr extends portfolio_plugin_push_base {

    private $flickr;

    public static function supported_formats() {
        return array(PORTFOLIO_FORMAT_IMAGE);
    }

    public static function get_name() {
        return get_string('pluginname', 'portfolio_flickr');
    }

    public function prepare_package() {
        $this->flickr = new phpFlickr($this->get_config('apikey'), $this->get_config('sharedsecret'));
    }

    public function send_package() {
        throw new portfolio_plugin_exception('notimplemented', 'portfolio', null, 'flickr');
    }

    public static function allows_multiple() {
        return false;
    }

    public function get_continue_url() {
        return 'http://www.flickr.com/files#0:f:' . $this->get_export_config('folder');
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

    public function admin_config_form(&$mform) {
        $strrequired = get_string('required');
        $mform->addElement('text', 'apikey', get_string('apikey', 'portfolio_flickr'));
        $mform->addRule('apikey', $strrequired, 'required', null, 'client');
        $mform->addElement('text', 'sharedsecret', get_string('sharedsecret', 'portfolio_flickr'));
        $mform->addRule('sharedsecret', $strrequired, 'required', null, 'client');
    }

}
