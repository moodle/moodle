<?php
/**
 * General exception thrown by the {@link calendarsystem_update_checker} class
 */
class calendarsystem_update_checker_exception extends moodle_exception {

    /**
     * @param string $errorcode exception description identifier
     * @param mixed $debuginfo debugging data to display
     */
    public function __construct($errorcode, $debuginfo=null) {
        parent::__construct($errorcode, 'calendarsystem', '', null, print_r($debuginfo, true));
    }
}

class calendarsystem_update_checker {

    /** @var calendarsystem_update_checker holds the singleton instance */
    protected static $singletoninstance;
    /** @var null|int the timestamp of when the most recent response was fetched */
    protected $recentfetch = null;
    /** @var null|array the recent response from the update notification provider */
    protected $recentresponse = null;
    /** @var null|string the numerical version of the local Moodle code */
    protected $currentversion = null;
    /** @var null|string the release info of the local Moodle code */
    protected $currentrelease = null;
    /** @var null|string branch of the local Moodle code */
    protected $currentbranch = null;
    /** @var array of (string)frankestyle => (string)version list of additional plugins deployed at this site */
    protected $currentplugins = array();

    /**
     * Direct initiation not allowed, use the factory method {@link self::instance()}
     */
    protected function __construct() {
    }

    /**
     * Sorry, this is singleton
     */
    protected function __clone() {
    }

    /**
     * Factory method for this class
     *
     * @return calendarsystem_update_checker the singleton instance
     */
    public static function instance() {
        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    /**
     * Returns the timestamp of the last execution of {@link fetch()}
     *
     * @return int|null null if it has never been executed or we don't known
     */
    public function get_last_timefetched() {

        $this->restore_response();

        if (!empty($this->recentfetch)) {
            return $this->recentfetch;

        } else {
            return null;
        }
    }

    /**
     * Fetches the available update status from the remote site
     *
     * @throws available_update_checker_exception
     */
    public function fetch() {
        $response = $this->get_response();
        $this->validate_response($response);
        $this->store_response($response);
    }

    /**
     * Returns the available update information for the given component
     *
     * This method returns null if the most recent response does not contain any information
     * about it. The returned structure is an array of available updates for the given
     * component. Each update info is an object with at least one property called
     * 'version'. Other possible properties are 'release', 'maturity', 'url' and 'downloadurl'.
     *
     * For the 'core' component, the method returns real updates only (those with higher version).
     * For all other components, the list of all known remote updates is returned and the caller
     * (usually the {@link plugin_manager}) is supposed to make the actual comparison of versions.
     *
     * @param string $component frankenstyle
     * @param array $options with supported keys 'minmaturity' and/or 'notifybuilds'
     * @return null|array null or array of calendarsystem_update_info objects
     */
    public function get_update_info($component, array $options = array()) {

        if ($component == 'core') {
            $this->load_current_environment();
        }

        $this->restore_response();

        if (empty($this->recentresponse['updates'][$component])) {
            return null;
        }

        $updates = array();
        foreach ($this->recentresponse['updates'][$component] as $info) {
            $update = new calendarsystem_update_info($component, $info);
            if ($update->version <= $this->currentversion) {
                continue;
            }
            $updates[] = $update;
        }

        if (empty($updates)) {
            return null;
        }

        return $updates;
    }

    /**
     * Makes cURL request to get data from the remote site
     *
     * @return string raw request result
     * @throws calendarsystem_update_checker_exception
     */
    protected function get_response() {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $curl = new curl(array('proxy' => true));
        $response = $curl->post($this->prepare_request_url(), $this->prepare_request_params());
        $curlerrno = $curl->get_errno();
        if (!empty($curlerrno)) {
            throw new calendarsystem_update_checker_exception('err_response_curl', 'cURL error '.$curlerrno.': '.$curl->error);
        }
        $curlinfo = $curl->get_info();
        if ($curlinfo['http_code'] != 200) {
            throw new calendarsystem_update_checker_exception('err_response_http_code', $curlinfo['http_code']);
        }
        return $response;
    }

///////////////////////////
// ino ezafe karde boodam
    /**
     * Makes sure the response is valid, has correct API format etc.
     *
     * @param string $response raw response as returned by the {@link self::get_response()}
     * @throws calendarsystem_update_checker_exception
     */
    protected function validate_response($response) {

        $response = $this->decode_response($response);

        if (empty($response)) {
            throw new calendarsystem_update_checker_exception('err_response_empty');
        }

        if (empty($response['status']) or $response['status'] !== 'OK') {
            throw new calendarsystem_update_checker_exception('err_response_status', $response['status']);
        }
    }

    /* Decodes the raw string response from the update notifications provider
     *
     * @param string $response as returned by {@link self::get_response()}
     * @return array decoded response structure
     */
    protected function decode_response($response) {
        return json_decode($response, true);
    }

    /**
     * Stores the valid fetched response for later usage
     *
     * This implementation uses the config_plugins table as the permanent storage.
     *
     * @param string $response raw valid data returned by {@link self::get_response()}
     */
    protected function store_response($response) {

        set_config('recentfetch', time(), 'calendarsystem_plugin');
        set_config('recentresponse', $response, 'calendarsystem_plugin');

        $this->restore_response(true);
    }

    /**
     * Loads the most recent raw response record we have fetched
     *
     * After this method is called, $this->recentresponse is set to an array. If the
     * array is empty, then either no data have been fetched yet or the fetched data
     * do not have expected format (and thence they are ignored and a debugging
     * message is displayed).
     *
     * This implementation uses the config_plugins table as the permanent storage.
     *
     * @param bool $forcereload reload even if it was already loaded
     */
    protected function restore_response($forcereload = false) {

        if (!$forcereload and !is_null($this->recentresponse)) {
            // we already have it, nothing to do
            return;
        }

        $config = get_config('calendarsystem_plugin');

        if (!empty($config->recentresponse) and !empty($config->recentfetch)) {
            try {
                $this->validate_response($config->recentresponse);
                $this->recentfetch = $config->recentfetch;
                $this->recentresponse = $this->decode_response($config->recentresponse);
            } catch (calendarsystem_update_checker_exception $e) {
                // The server response is not valid. Behave as if no data were fetched yet.
                // This may happen when the most recent update info (cached locally) has been
                // fetched with the previous branch of Moodle (like during an upgrade from 2.x
                // to 2.y) or when the API of the response has changed.
                $this->recentresponse = array();
            }

        } else {
            $this->recentresponse = array();
        }
    }

    /**
     * Returns the URL to send update requests to
     *
     * @return string URL
     */
    protected function prepare_request_url() {
        return 'http://foodle.org/calendarsystem/api/updates.php';
    }

    /**
     * Sets the properties currentversion, currentrelease, currentbranch and currentplugins
     *
     * @param bool $forcereload
     */
    protected function load_current_environment($forcereload=false) {
        global $CFG;

        if (!is_null($this->currentversion) and !$forcereload) {
            // nothing to do
            return;
        }

        $version = null;
        $plugin  = new stdClass();

        include($CFG->dirroot.'/calendarsystem/version.php');
        $this->currentversion = $version;

        $calendars = get_plugin_list('calendarsystem');

        foreach ($calendars as $calendar => $calendarrootdir) {
            include($calendarrootdir.'/version.php');
            $this->currentplugins[$calendar] = $plugin->version;
        }
    }

    /**
     * Returns the list of HTTP params to be sent to the updates provider URL
     *
     * @return array of (string)param => (string)value
     */
    protected function prepare_request_params() {
        global $CFG;

        $this->load_current_environment();
//        $this->restore_response();

        $params = array();
        $params['format'] = 'json';

        if (isset($this->currentversion)) {
            $params['version'] = $this->currentversion;
        } else {
            throw new coding_exception('Main calendarsystem version must be already known here');
        }

        $plugins = array();
        foreach ($this->currentplugins as $plugin => $version) {
            $plugins[] = $plugin.'@'.$version;
        }
        if (!empty($plugins)) {
            $params['plugins'] = implode(',', $plugins);
        }

        $params['url'] = $CFG->wwwroot;

        return $params;
    }

}

/**
 * Defines the structure of objects returned by {@link calendarsystem_update_checker::get_update_info()}
 */
class calendarsystem_update_info {

    /** @var string frankenstyle component name */
    public $component;
    /** @var int the available version of the component */
    public $version;
    /** @var string|null optional URL of a page with more info about the update */
    public $url = null;
    /** @var string|null optional URL of a ZIP package that can be downloaded and installed */
    public $download = null;
    /** @var string|null if self::download is set, then this must be the MD5 hash of the ZIP */
    public $downloadmd5 = null;

    /**
     * Creates new instance of the class
     *
     * The $info array must provide at least the 'version' value and optionally all other
     * values to populate the object's properties.
     *
     * @param string $name the frankenstyle component name
     * @param array $info associative array with other properties
     */
    public function __construct($name, array $info) {
        $this->component = $name;
        foreach ($info as $k => $v) {
            if (property_exists('calendarsystem_update_info', $k) and $k != 'component') {
                $this->$k = $v;
            }
        }
    }
}
?>