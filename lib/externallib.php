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
 * Support for external API
 *
 * @package    core_webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class_alias(\core_external\external_api::class, 'external_api');
class_alias(\core_external\restricted_context_exception::class, 'restricted_context_exception');
class_alias(\core_external\external_description::class, 'external_description');
class_alias(\core_external\external_value::class, 'external_value');
class_alias(\core_external\external_single_structure::class, 'external_single_structure');
class_alias(\core_external\external_multiple_structure::class, 'external_multiple_structure');
class_alias(\core_external\external_function_parameters::class, 'external_function_parameters');
class_alias(\core_external\util::class, 'external_util');
class_alias(\core_external\external_files::class, 'external_files');
class_alias(\core_external\external_warnings::class, 'external_warnings');

/**
 * Generate a token
 *
 * @param string $tokentype EXTERNAL_TOKEN_EMBEDDED|EXTERNAL_TOKEN_PERMANENT
 * @param stdClass|int $serviceorid service linked to the token
 * @param int $userid user linked to the token
 * @param stdClass|int $contextorid
 * @param int $validuntil date when the token expired
 * @param string $iprestriction allowed ip - if 0 or empty then all ips are allowed
 * @return string generated token
 * @author  2010 Jamie Pratt
 * @since Moodle 2.0
 */
function external_generate_token($tokentype, $serviceorid, $userid, $contextorid, $validuntil=0, $iprestriction=''){
    global $DB, $USER;
    // make sure the token doesn't exist (even if it should be almost impossible with the random generation)
    $numtries = 0;
    do {
        $numtries ++;
        $generatedtoken = md5(uniqid(rand(),1));
        if ($numtries > 5){
            throw new moodle_exception('tokengenerationfailed');
        }
    } while ($DB->record_exists('external_tokens', array('token'=>$generatedtoken)));
    $newtoken = new stdClass();
    $newtoken->token = $generatedtoken;
    if (!is_object($serviceorid)){
        $service = $DB->get_record('external_services', array('id' => $serviceorid));
    } else {
        $service = $serviceorid;
    }
    if (!is_object($contextorid)){
        $context = context::instance_by_id($contextorid, MUST_EXIST);
    } else {
        $context = $contextorid;
    }
    if (empty($service->requiredcapability) || has_capability($service->requiredcapability, $context, $userid)) {
        $newtoken->externalserviceid = $service->id;
    } else {
        throw new moodle_exception('nocapabilitytousethisservice');
    }
    $newtoken->tokentype = $tokentype;
    $newtoken->userid = $userid;
    if ($tokentype == EXTERNAL_TOKEN_EMBEDDED){
        $newtoken->sid = session_id();
    }

    $newtoken->contextid = $context->id;
    $newtoken->creatorid = $USER->id;
    $newtoken->timecreated = time();
    $newtoken->validuntil = $validuntil;
    if (!empty($iprestriction)) {
        $newtoken->iprestriction = $iprestriction;
    }
    // Generate the private token, it must be transmitted only via https.
    $newtoken->privatetoken = random_string(64);
    $DB->insert_record('external_tokens', $newtoken);
    return $newtoken->token;
}

/**
 * Create and return a session linked token. Token to be used for html embedded client apps that want to communicate
 * with the Moodle server through web services. The token is linked to the current session for the current page request.
 * It is expected this will be called in the script generating the html page that is embedding the client app and that the
 * returned token will be somehow passed into the client app being embedded in the page.
 *
 * @param string $servicename name of the web service. Service name as defined in db/services.php
 * @param int $context context within which the web service can operate.
 * @return int returns token id.
 * @since Moodle 2.0
 */
function external_create_service_token($servicename, $context){
    global $USER, $DB;
    $service = $DB->get_record('external_services', array('name'=>$servicename), '*', MUST_EXIST);
    return external_generate_token(EXTERNAL_TOKEN_EMBEDDED, $service, $USER->id, $context, 0);
}

/**
 * Delete all pre-built services (+ related tokens) and external functions information defined in the specified component.
 *
 * @param string $component name of component (moodle, mod_assignment, etc.)
 */
function external_delete_descriptions($component) {
    global $DB;

    $params = array($component);

    $DB->delete_records_select('external_tokens',
            "externalserviceid IN (SELECT id FROM {external_services} WHERE component = ?)", $params);
    $DB->delete_records_select('external_services_users',
            "externalserviceid IN (SELECT id FROM {external_services} WHERE component = ?)", $params);
    $DB->delete_records_select('external_services_functions',
            "functionname IN (SELECT name FROM {external_functions} WHERE component = ?)", $params);
    $DB->delete_records('external_services', array('component'=>$component));
    $DB->delete_records('external_functions', array('component'=>$component));
}

/**
 * A pre-filled external_value class for text format.
 *
 * Default is FORMAT_HTML
 * This should be used all the time in external xxx_params()/xxx_returns functions
 * as it is the standard way to implement text format param/return values.
 *
 * @package    core_webservice
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */
class external_format_value extends external_value {

    /**
     * Constructor
     *
     * @param string $textfieldname Name of the text field
     * @param int $required if VALUE_REQUIRED then set standard default FORMAT_HTML
     * @param int $default Default value.
     * @since Moodle 2.3
     */
    public function __construct($textfieldname, $required = VALUE_REQUIRED, $default = null) {

        if ($default == null && $required == VALUE_DEFAULT) {
            $default = FORMAT_HTML;
        }

        $desc = $textfieldname . ' format (' . FORMAT_HTML . ' = HTML, '
                . FORMAT_MOODLE . ' = MOODLE, '
                . FORMAT_PLAIN . ' = PLAIN or '
                . FORMAT_MARKDOWN . ' = MARKDOWN)';

        parent::__construct(PARAM_INT, $desc, $required, $default);
    }
}

/**
 * Validate text field format against known FORMAT_XXX
 *
 * @param array $format the format to validate
 * @return the validated format
 * @throws coding_exception
 * @since Moodle 2.3
 */
function external_validate_format($format) {
    $allowedformats = array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN);
    if (!in_array($format, $allowedformats)) {
        throw new moodle_exception('formatnotsupported', 'webservice', '' , null,
                'The format with value=' . $format . ' is not supported by this Moodle site');
    }
    return $format;
}

/**
 * Format the string to be returned properly as requested by the either the web service server,
 * either by an internally call.
 * The caller can change the format (raw) with the external_settings singleton
 * All web service servers must set this singleton when parsing the $_GET and $_POST.
 *
 * <pre>
 * Options are the same that in {@link format_string()} with some changes:
 *      filter      : Can be set to false to force filters off, else observes {@link external_settings}.
 * </pre>
 *
 * @param string $str The string to be filtered. Should be plain text, expect
 * possibly for multilang tags.
 * @param boolean $striplinks To strip any link in the result text. Moodle 1.8 default changed from false to true! MDL-8713
 * @param context|int $contextorid The id of the context for the string or the context (affects filters).
 * @param array $options options array/object or courseid
 * @return string text
 * @since Moodle 3.0
 */
function external_format_string($str, $contextorid, $striplinks = true, $options = array()) {

    // Get settings (singleton).
    $settings = external_settings::get_instance();
    if (empty($contextorid)) {
        throw new coding_exception('contextid is required');
    }

    if (!$settings->get_raw()) {
        if (is_object($contextorid) && is_a($contextorid, 'context')) {
            $context = $contextorid;
        } else {
            $context = context::instance_by_id($contextorid);
        }
        $options['context'] = $context;
        $options['filter'] = isset($options['filter']) && !$options['filter'] ? false : $settings->get_filter();
        $str = format_string($str, $striplinks, $options);
    }

    return $str;
}

/**
 * Format the text to be returned properly as requested by the either the web service server,
 * either by an internally call.
 * The caller can change the format (raw, filter, file, fileurl) with the external_settings singleton
 * All web service servers must set this singleton when parsing the $_GET and $_POST.
 *
 * <pre>
 * Options are the same that in {@link format_text()} with some changes in defaults to provide backwards compatibility:
 *      trusted     :   If true the string won't be cleaned. Default false.
 *      noclean     :   If true the string won't be cleaned only if trusted is also true. Default false.
 *      nocache     :   If true the string will not be cached and will be formatted every call. Default false.
 *      filter      :   Can be set to false to force filters off, else observes {@link external_settings}.
 *      para        :   If true then the returned string will be wrapped in div tags. Default (different from format_text) false.
 *                      Default changed because div tags are not commonly needed.
 *      newlines    :   If true then lines newline breaks will be converted to HTML newline breaks. Default true.
 *      context     :   Not used! Using contextid parameter instead.
 *      overflowdiv :   If set to true the formatted text will be encased in a div with the class no-overflow before being
 *                      returned. Default false.
 *      allowid     :   If true then id attributes will not be removed, even when using htmlpurifier. Default (different from
 *                      format_text) true. Default changed id attributes are commonly needed.
 *      blanktarget :   If true all <a> tags will have target="_blank" added unless target is explicitly specified.
 * </pre>
 *
 * @param string $text The content that may contain ULRs in need of rewriting.
 * @param int $textformat The text format.
 * @param context|int $contextorid This parameter and the next two identify the file area to use.
 * @param string $component
 * @param string $filearea helps identify the file area.
 * @param int $itemid helps identify the file area.
 * @param object/array $options text formatting options
 * @return array text + textformat
 * @since Moodle 2.3
 * @since Moodle 3.2 component, filearea and itemid are optional parameters
 */
function external_format_text($text, $textformat, $contextorid, $component = null, $filearea = null, $itemid = null,
                                $options = null) {
    global $CFG;

    // Get settings (singleton).
    $settings = external_settings::get_instance();

    if (is_object($contextorid) && is_a($contextorid, 'context')) {
        $context = $contextorid;
        $contextid = $context->id;
    } else {
        $context = null;
        $contextid = $contextorid;
    }

    if ($component and $filearea and $settings->get_fileurl()) {
        require_once($CFG->libdir . "/filelib.php");
        $text = file_rewrite_pluginfile_urls($text, $settings->get_file(), $contextid, $component, $filearea, $itemid);
    }

    // Note that $CFG->forceclean does not apply here if the client requests for the raw database content.
    // This is consistent with web clients that are still able to load non-cleaned text into editors, too.

    if (!$settings->get_raw()) {
        $options = (array)$options;

        // If context is passed in options, check that is the same to show a debug message.
        if (isset($options['context'])) {
            if ((is_object($options['context']) && $options['context']->id != $contextid)
                    || (!is_object($options['context']) && $options['context'] != $contextid)) {
                debugging('Different contexts found in external_format_text parameters. $options[\'context\'] not allowed.
                    Using $contextid parameter...', DEBUG_DEVELOPER);
            }
        }

        $options['filter'] = isset($options['filter']) && !$options['filter'] ? false : $settings->get_filter();
        $options['para'] = isset($options['para']) ? $options['para'] : false;
        $options['context'] = !is_null($context) ? $context : context::instance_by_id($contextid);
        $options['allowid'] = isset($options['allowid']) ? $options['allowid'] : true;

        $text = format_text($text, $textformat, $options);
        $textformat = FORMAT_HTML; // Once converted to html (from markdown, plain... lets inform consumer this is already HTML).
    }

    return array($text, $textformat);
}

/**
 * Generate or return an existing token for the current authenticated user.
 * This function is used for creating a valid token for users authenticathing via login/token.php or admin/tool/mobile/launch.php.
 *
 * @param stdClass $service external service object
 * @return stdClass token object
 * @since Moodle 3.2
 * @throws moodle_exception
 */
function external_generate_token_for_current_user($service) {
    global $DB, $USER, $CFG;

    core_user::require_active_user($USER, true, true);

    // Check if there is any required system capability.
    if ($service->requiredcapability and !has_capability($service->requiredcapability, context_system::instance())) {
        throw new moodle_exception('missingrequiredcapability', 'webservice', '', $service->requiredcapability);
    }

    // Specific checks related to user restricted service.
    if ($service->restrictedusers) {
        $authoriseduser = $DB->get_record('external_services_users',
            array('externalserviceid' => $service->id, 'userid' => $USER->id));

        if (empty($authoriseduser)) {
            throw new moodle_exception('usernotallowed', 'webservice', '', $service->shortname);
        }

        if (!empty($authoriseduser->validuntil) and $authoriseduser->validuntil < time()) {
            throw new moodle_exception('invalidtimedtoken', 'webservice');
        }

        if (!empty($authoriseduser->iprestriction) and !address_in_subnet(getremoteaddr(), $authoriseduser->iprestriction)) {
            throw new moodle_exception('invalidiptoken', 'webservice');
        }
    }

    // Check if a token has already been created for this user and this service.
    $conditions = array(
        'userid' => $USER->id,
        'externalserviceid' => $service->id,
        'tokentype' => EXTERNAL_TOKEN_PERMANENT
    );
    $tokens = $DB->get_records('external_tokens', $conditions, 'timecreated ASC');

    // A bit of sanity checks.
    foreach ($tokens as $key => $token) {

        // Checks related to a specific token. (script execution continue).
        $unsettoken = false;
        // If sid is set then there must be a valid associated session no matter the token type.
        if (!empty($token->sid)) {
            if (!\core\session\manager::session_exists($token->sid)) {
                // This token will never be valid anymore, delete it.
                $DB->delete_records('external_tokens', array('sid' => $token->sid));
                $unsettoken = true;
            }
        }

        // Remove token is not valid anymore.
        if (!empty($token->validuntil) and $token->validuntil < time()) {
            $DB->delete_records('external_tokens', array('token' => $token->token, 'tokentype' => EXTERNAL_TOKEN_PERMANENT));
            $unsettoken = true;
        }

        // Remove token if its IP is restricted.
        if (isset($token->iprestriction) and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
            $unsettoken = true;
        }

        if ($unsettoken) {
            unset($tokens[$key]);
        }
    }

    // If some valid tokens exist then use the most recent.
    if (count($tokens) > 0) {
        $token = array_pop($tokens);
    } else {
        $context = context_system::instance();
        $isofficialservice = $service->shortname == MOODLE_OFFICIAL_MOBILE_SERVICE;

        if (($isofficialservice and has_capability('moodle/webservice:createmobiletoken', $context)) or
                (!is_siteadmin($USER) && has_capability('moodle/webservice:createtoken', $context))) {

            // Create a new token.
            $token = new stdClass;
            $token->token = md5(uniqid(rand(), 1));
            $token->userid = $USER->id;
            $token->tokentype = EXTERNAL_TOKEN_PERMANENT;
            $token->contextid = context_system::instance()->id;
            $token->creatorid = $USER->id;
            $token->timecreated = time();
            $token->externalserviceid = $service->id;
            // By default tokens are valid for 12 weeks.
            $token->validuntil = $token->timecreated + $CFG->tokenduration;
            $token->iprestriction = null;
            $token->sid = null;
            $token->lastaccess = null;
            // Generate the private token, it must be transmitted only via https.
            $token->privatetoken = random_string(64);
            $token->id = $DB->insert_record('external_tokens', $token);

            $eventtoken = clone $token;
            $eventtoken->privatetoken = null;
            $params = array(
                'objectid' => $eventtoken->id,
                'relateduserid' => $USER->id,
                'other' => array(
                    'auto' => true
                )
            );
            $event = \core\event\webservice_token_created::create($params);
            $event->add_record_snapshot('external_tokens', $eventtoken);
            $event->trigger();
        } else {
            throw new moodle_exception('cannotcreatetoken', 'webservice', '', $service->shortname);
        }
    }
    return $token;
}

/**
 * Set the last time a token was sent and trigger the \core\event\webservice_token_sent event.
 *
 * This function is used when a token is generated by the user via login/token.php or admin/tool/mobile/launch.php.
 * In order to protect the privatetoken, we remove it from the event params.
 *
 * @param  stdClass $token token object
 * @since  Moodle 3.2
 */
function external_log_token_request($token) {
    global $DB, $USER;

    $token->privatetoken = null;

    // Log token access.
    $DB->set_field('external_tokens', 'lastaccess', time(), array('id' => $token->id));

    $params = array(
        'objectid' => $token->id,
    );
    $event = \core\event\webservice_token_sent::create($params);
    $event->add_record_snapshot('external_tokens', $token);
    $event->trigger();

    // Check if we need to notify the user about the new login via token.
    $loginip = getremoteaddr();
    if ($USER->lastip != $loginip &&
            ((!WS_SERVER && !CLI_SCRIPT && NO_MOODLE_COOKIES) || PHPUNIT_TEST)) {

        $logintime = time();
        $useragent = \core_useragent::get_user_agent_string();
        $ismoodleapp = \core_useragent::is_moodle_app();

        // Schedule adhoc task to sent a login notification to the user.
        $task = new \core\task\send_login_notifications();
        $task->set_userid($USER->id);
        $task->set_custom_data(compact('ismoodleapp', 'useragent', 'loginip', 'logintime'));
        $task->set_component('core');
        // We need sometime so the mobile app will send to Moodle the device information after login.
        $task->set_next_run_time($logintime + (2 * MINSECS));
        \core\task\manager::reschedule_or_queue_adhoc_task($task);
    }
}

/**
 * Singleton to handle the external settings.
 *
 * We use singleton to encapsulate the "logic"
 *
 * @package    core_webservice
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */
class external_settings {

    /** @var object the singleton instance */
    public static $instance = null;

    /** @var boolean Should the external function return raw text or formatted */
    private $raw = false;

    /** @var boolean Should the external function filter the text */
    private $filter = false;

    /** @var boolean Should the external function rewrite plugin file url */
    private $fileurl = true;

    /** @var string In which file should the urls be rewritten */
    private $file = 'webservice/pluginfile.php';

    /** @var string The session lang */
    private $lang = '';

    /** @var string The timezone to use during this WS request */
    private $timezone = '';

    /**
     * Constructor - protected - can not be instanciated
     */
    protected function __construct() {
        if ((AJAX_SCRIPT == false) && (CLI_SCRIPT == false) && (WS_SERVER == false)) {
            // For normal pages, the default should match the default for format_text.
            $this->filter = true;
            // Use pluginfile.php for web requests.
            $this->file = 'pluginfile.php';
        }
    }

    /**
     * Return only one instance
     *
     * @return \external_settings
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new external_settings;
        }

        return self::$instance;
    }

    /**
     * Set raw
     *
     * @param boolean $raw
     */
    public function set_raw($raw) {
        $this->raw = $raw;
    }

    /**
     * Get raw
     *
     * @return boolean
     */
    public function get_raw() {
        return $this->raw;
    }

    /**
     * Set filter
     *
     * @param boolean $filter
     */
    public function set_filter($filter) {
        $this->filter = $filter;
    }

    /**
     * Get filter
     *
     * @return boolean
     */
    public function get_filter() {
        return $this->filter;
    }

    /**
     * Set fileurl
     *
     * @param boolean $fileurl
     */
    public function set_fileurl($fileurl) {
        $this->fileurl = $fileurl;
    }

    /**
     * Get fileurl
     *
     * @return boolean
     */
    public function get_fileurl() {
        return $this->fileurl;
    }

    /**
     * Set file
     *
     * @param string $file
     */
    public function set_file($file) {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function get_file() {
        return $this->file;
    }

    /**
     * Set lang
     *
     * @param string $lang
     */
    public function set_lang($lang) {
        $this->lang = $lang;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function get_lang() {
        return $this->lang;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     */
    public function set_timezone($timezone) {
        $this->timezone = $timezone;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function get_timezone() {
        return $this->timezone;
    }
}
