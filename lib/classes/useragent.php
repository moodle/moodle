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
 * Environment class to aid with the detection and establishment of the working environment.
 *
 * @package    core
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The user agent class.
 *
 * It's important to note that we do not like browser sniffing and its use in core code is highly discouraged.
 * No new uses of this API will be integrated unless there is absolutely no alternative.
 *
 * This API supports the few browser checks we do have in core, all of which one day will hopefully be removed.
 * The API will remain to support any third party use out there, however at some point like all code it will be deprecated.
 *
 * Use sparingly and only with good cause!
 *
 * @package    core
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_useragent {

    /**
     * The default for devices, think of as a computer.
     */
    const DEVICETYPE_DEFAULT = 'default';
    /**
     * Legacy devices, or at least legacy browsers. These are older devices/browsers
     * that don't support standards.
     */
    const DEVICETYPE_LEGACY = 'legacy';
    /**
     * Mobile devices like your cell phone or hand held gaming device.
     */
    const DEVICETYPE_MOBILE = 'mobile';
    /**
     * Tables, larger than hand held, but still easily portable and smaller than a laptop.
     */
    const DEVICETYPE_TABLET = 'tablet';

    /**
     * An instance of this class.
     * @var core_useragent
     */
    protected static $instance = null;

    /**
     * The device types we track.
     * @var array
     */
    public static $devicetypes = array(
        self::DEVICETYPE_DEFAULT,
        self::DEVICETYPE_LEGACY,
        self::DEVICETYPE_MOBILE,
        self::DEVICETYPE_TABLET
    );

    /**
     * The current requests user agent string if there was one.
     * @var string|bool|null Null until initialised, false if none available, or string when available.
     */
    protected $useragent = null;

    /**
     * The users device type, one of self::DEVICETYPE_*.
     * @var string null until initialised
     */
    protected $devicetype = null;

    /**
     * Custom device types entered into the admin interface.
     * @var array
     */
    protected $devicetypecustoms = array();

    /**
     * True if the user agent supports the display of svg images. False if not.
     * @var bool|null Null until initialised, then true or false.
     */
    protected $supportssvg = null;

    /**
     * Get an instance of the user agent object.
     *
     * @param bool $reload If set to true the user agent will be reset and all ascertations remade.
     * @param string $forceuseragent The string to force as the user agent, don't use unless absolutely unavoidable.
     * @return core_useragent
     */
    public static function instance($reload = false, $forceuseragent = null) {
        if (!self::$instance || $reload) {
            self::$instance = new core_useragent($forceuseragent);
        }
        return self::$instance;
    }

    /**
     * Constructs a new user agent object. Publically you must use the instance method above.
     *
     * @param string|null $forceuseragent Optional a user agent to force.
     */
    protected function __construct($forceuseragent = null) {
        global $CFG;
        if (!empty($CFG->devicedetectregex)) {
            $this->devicetypecustoms = json_decode($CFG->devicedetectregex, true);
        }
        if ($this->devicetypecustoms === null) {
            // This shouldn't happen unless you're hardcoding the config value.
            debugging('Config devicedetectregex is not valid JSON object');
            $this->devicetypecustoms = array();
        }
        if ($forceuseragent !== null) {
            $this->useragent = $forceuseragent;
        } else if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $this->useragent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $this->useragent = false;
            $this->devicetype = self::DEVICETYPE_DEFAULT;
        }
    }

    /**
     * Returns the user agent string.
     * @return bool|string The user agent string or false if one isn't available.
     */
    public static function get_user_agent_string() {
        $instance = self::instance();
        return $instance->useragent;
    }

    /**
     * Returns the device type we believe is being used.
     * @return string
     */
    public static function get_device_type() {
        $instance = self::instance();
        if ($instance->devicetype === null) {
            return $instance->guess_device_type();
        }
        return $instance->devicetype;
    }

    /**
     * Guesses the device type the user agent is running on.
     *
     * @return string
     */
    protected function guess_device_type() {
        global $CFG;
        if (empty($CFG->enabledevicedetection)) {
            $this->devicetype = self::DEVICETYPE_DEFAULT;
            return $this->devicetype;
        }
        foreach ($this->devicetypecustoms as $value => $regex) {
            if (preg_match($regex, $this->useragent)) {
                $this->devicetype = $value;
                return $this->devicetype;
            }
        }
        if ($this->is_useragent_mobile()) {
            $this->devicetype = 'mobile';
        } else if ($this->is_useragent_tablet()) {
            $this->devicetype = 'tablet';
        } else if (substr($this->useragent, 0, 34) === 'Mozilla/4.0 (compatible; MSIE 6.0;') {
            // Safe way to check for IE6 and not get false positives for some IE 7/8 users.
            $this->devicetype = 'legacy';
        } else {
            $this->devicetype = self::DEVICETYPE_DEFAULT;
        }
        return $this->devicetype;
    }

    /**
     * Returns true if the user appears to be on a mobile device.
     * @return bool
     */
    protected function is_useragent_mobile() {
        // Mobile detection PHP direct copy from open source detectmobilebrowser.com.
        $phonesregex = '/android .+ mobile|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i';
        $modelsregex = '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i';
        return (preg_match($phonesregex, $this->useragent) || preg_match($modelsregex, substr($this->useragent, 0, 4)));
    }

    /**
     * Returns true if the user appears to be on a tablet.
     * @return int
     */
    protected function is_useragent_tablet() {
        $tabletregex = '/Tablet browser|android|iPad|iProd|GT-P1000|GT-I9000|SHW-M180S|SGH-T849|SCH-I800|Build\/ERE27|sholest/i';
        return (preg_match($tabletregex, $this->useragent));
    }

    /**
     * Gets a list of known device types.
     *
     * @param bool $includecustomtypes If set to true we'll include types that have been added by the admin.
     * @return array
     */
    public static function get_device_type_list($includecustomtypes = true) {
        $types = self::$devicetypes;
        if ($includecustomtypes) {
            $instance = self::instance();
            $types = array_merge($types, array_keys($instance->devicetypecustoms));
        }
        return $types;
    }

    /**
     * Returns the theme to use for the given device type.
     *
     * This used to be get_selected_theme_for_device_type.
     * @param null|string $devicetype The device type to find out for. Defaults to the device the user is using,
     * @return bool
     */
    public static function get_device_type_theme($devicetype = null) {
        global $CFG;
        if ($devicetype === null) {
            $devicetype = self::get_device_type();
        }
        $themevarname = self::get_device_type_cfg_var_name($devicetype);
        if (empty($CFG->$themevarname)) {
            return false;
        }
        return $CFG->$themevarname;
    }

    /**
     * Returns the CFG var used to find the theme to use for the given device.
     *
     * Used to be get_device_cfg_var_name.
     *
     * @param null|string $devicetype The device type to find out for. Defaults to the device the user is using,
     * @return string
     */
    public static function get_device_type_cfg_var_name($devicetype = null) {
        if ($devicetype == self::DEVICETYPE_DEFAULT || empty($devicetype)) {
            return 'theme';
        }
        return 'theme' . $devicetype;
    }

    /**
     * Gets the device type the user is currently using.
     * @return string
     */
    public static function get_user_device_type() {
        $device = self::get_device_type();
        $switched = get_user_preferences('switchdevice'.$device, false);
        if ($switched != false) {
            return $switched;
        }
        return $device;
    }

    /**
     * Switches the device type we think the user is using to what ever was given.
     * @param string $newdevice
     * @return bool
     * @throws coding_exception
     */
    public static function set_user_device_type($newdevice) {
        $devicetype = self::get_device_type();
        if ($newdevice == $devicetype) {
            unset_user_preference('switchdevice'.$devicetype);
            return true;
        } else {
            $devicetypes = self::get_device_type_list();
            if (in_array($newdevice, $devicetypes)) {
                set_user_preference('switchdevice'.$devicetype, $newdevice);
                return true;
            }
        }
        throw new coding_exception('Invalid device type provided to set_user_device_type');
    }

    /**
     * Returns true if the user agent matches the given brand and the version is equal to or greater than that specified.
     *
     * @param string $brand The branch to check for.
     * @param scalar $version The version if we need to find out if it is equal to or greater than that specified.
     * @return bool
     */
    public static function check_browser_version($brand, $version = null) {
        switch ($brand) {

            case 'MSIE':
                // Internet Explorer.
                return self::check_ie_version($version);

            case 'Firefox':
                // Mozilla Firefox browsers.
                return self::check_firefox_version($version);

            case 'Chrome':
                return self::check_chrome_version($version);

            case 'Opera':
                // Opera.
                return self::check_opera_version($version);

            case 'Safari':
                // Desktop version of Apple Safari browser - no mobile or touch devices.
                return self::check_safari_version($version);

            case 'Safari iOS':
                // Safari on iPhone, iPad and iPod touch.
                return self::check_safari_ios_version($version);

            case 'WebKit':
                // WebKit based browser - everything derived from it (Safari, Chrome, iOS, Android and other mobiles).
                return self::check_webkit_version($version);

            case 'Gecko':
                // Gecko based browsers.
                return self::check_gecko_version($version);

            case 'WebKit Android':
                // WebKit browser on Android.
                return self::check_webkit_android_version($version);

            case 'Camino':
                // OSX browser using Gecke engine.
                return self::check_camino_version($version);
        }
        // Who knows?! doesn't pass anyway.
        return false;
    }

    /**
     * Checks the user agent is camino based and that the version is equal to or greater than that specified.
     *
     * Camino browser is at the end of its life, its no longer being developed or supported, just don't worry about it.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    protected static function check_camino_version($version = null) {
        // OSX browser using Gecko engine.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'Camino') === false) {
            return false;
        }
        if (empty($version)) {
            return true; // No version specified.
        }
        if (preg_match("/Camino\/([0-9\.]+)/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is Firefox (of any version).
     *
     * @return bool true if firefox
     */
    public static function is_firefox() {
        return self::check_firefox_version();
    }

    /**
     * Checks the user agent is Firefox based and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_firefox_version($version = null) {
        // Mozilla Firefox browsers.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'Firefox') === false && strpos($useragent, 'Iceweasel') === false) {
            return false;
        }
        if (empty($version)) {
            return true; // No version specified..
        }
        if (preg_match("/(Iceweasel|Firefox)\/([0-9\.]+)/i", $useragent, $match)) {
            if (version_compare($match[2], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is Gecko based (of any version).
     *
     * @return bool true if Gecko based.
     */
    public static function is_gecko() {
        return self::check_gecko_version();
    }

    /**
     * Checks the user agent is Gecko based and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_gecko_version($version = null) {
        // Gecko based browsers.
        // Do not look for dates any more, we expect real Firefox version here.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (empty($version)) {
            $version = 1;
        } else if ($version > 20000000) {
            // This is just a guess, it is not supposed to be 100% accurate!
            if (preg_match('/^201/', $version)) {
                $version = 3.6;
            } else if (preg_match('/^200[7-9]/', $version)) {
                $version = 3;
            } else if (preg_match('/^2006/', $version)) {
                $version = 2;
            } else {
                $version = 1.5;
            }
        }
        if (preg_match("/(Iceweasel|Firefox)\/([0-9\.]+)/i", $useragent, $match)) {
            // Use real Firefox version if specified in user agent string.
            if (version_compare($match[2], $version) >= 0) {
                return true;
            }
        } else if (preg_match("/Gecko\/([0-9\.]+)/i", $useragent, $match)) {
            // Gecko might contain date or Firefox revision, let's just guess the Firefox version from the date.
            $browserver = $match[1];
            if ($browserver > 20000000) {
                // This is just a guess, it is not supposed to be 100% accurate!
                if (preg_match('/^201/', $browserver)) {
                    $browserver = 3.6;
                } else if (preg_match('/^200[7-9]/', $browserver)) {
                    $browserver = 3;
                } else if (preg_match('/^2006/', $version)) {
                    $browserver = 2;
                } else {
                    $browserver = 1.5;
                }
            }
            if (version_compare($browserver, $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is IE (of any version).
     *
     * @return bool true if internet exporeer
     */
    public static function is_ie() {
        return self::check_ie_version();
    }

    /**
     * Checks the user agent is IE and returns its main properties:
     * - browser version;
     * - whether running in compatibility view.
     *
     * @return bool|array False if not IE, otherwise an associative array of properties.
     */
    public static function check_ie_properties() {
        // Internet Explorer.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'Opera') !== false) {
            // Reject Opera.
            return false;
        }
        // See: http://www.useragentstring.com/pages/Internet%20Explorer/.
        if (preg_match("/MSIE ([0-9\.]+)/", $useragent, $match)) {
            $browser = $match[1];
        // See: http://msdn.microsoft.com/en-us/library/ie/bg182625%28v=vs.85%29.aspx for IE11+ useragent details.
        } else if (preg_match("/Trident\/[0-9\.]+/", $useragent) && preg_match("/rv:([0-9\.]+)/", $useragent, $match)) {
            $browser = $match[1];
        } else {
            return false;
        }
        $compatview = false;
        // IE8 and later versions may pretend to be IE7 for intranet sites, use Trident version instead,
        // the Trident should always describe the capabilities of IE in any emulation mode.
        if ($browser === '7.0' and preg_match("/Trident\/([0-9\.]+)/", $useragent, $match)) {
            $compatview = true;
            $browser = $match[1] + 4; // NOTE: Hopefully this will work also for future IE versions.
        }
        $browser = round($browser, 1);
        return array(
            'version'    => $browser,
            'compatview' => $compatview
        );
    }

    /**
     * Checks the user agent is IE and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_ie_version($version = null) {
        // Internet Explorer.
        $properties = self::check_ie_properties();
        if (!is_array($properties)) {
            return false;
        }
        // In case of IE we have to deal with BC of the version parameter.
        if (is_null($version)) {
            $version = 5.5; // Anything older is not considered a browser at all!
        }
        // IE uses simple versions, let's cast it to float to simplify the logic here.
        $version = round($version, 1);
        return ($properties['version'] >= $version);
    }

    /**
     * Checks the user agent is IE and that IE is running under Compatibility View setting.
     *
     * @return bool true if internet explorer runs in Compatibility View mode.
     */
    public static function check_ie_compatibility_view() {
        // IE User Agent string when in Compatibility View:
        // - IE  8: "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/4.0; ...)".
        // - IE  9: "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/5.0; ...)".
        // - IE 10: "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/6.0; ...)".
        // - IE 11: "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.3; Trident/7.0; ...)".
        // Refs:
        // - http://blogs.msdn.com/b/ie/archive/2009/01/09/the-internet-explorer-8-user-agent-string-updated-edition.aspx.
        // - http://blogs.msdn.com/b/ie/archive/2010/03/23/introducing-ie9-s-user-agent-string.aspx.
        // - http://blogs.msdn.com/b/ie/archive/2011/04/15/the-ie10-user-agent-string.aspx.
        // - http://msdn.microsoft.com/en-us/library/ie/hh869301%28v=vs.85%29.aspx.
        $properties = self::check_ie_properties();
        if (!is_array($properties)) {
            return false;
        }
        return $properties['compatview'];
    }

    /**
     * Checks the user agent is Opera (of any version).
     *
     * @return bool true if opera
     */
    public static function is_opera() {
        return self::check_opera_version();
    }

    /**
     * Checks the user agent is Opera and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_opera_version($version = null) {
        // Opera.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'Opera') === false) {
            return false;
        }
        if (empty($version)) {
            return true; // No version specified.
        }
        // Recent Opera useragents have Version/ with the actual version, e.g.:
        // Opera/9.80 (Windows NT 6.1; WOW64; U; en) Presto/2.10.289 Version/12.01
        // That's Opera 12.01, not 9.8.
        if (preg_match("/Version\/([0-9\.]+)/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        } else if (preg_match("/Opera\/([0-9\.]+)/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is webkit based
     *
     * @return bool true if webkit
     */
    public static function is_webkit() {
        return self::check_webkit_version();
    }

    /**
     * Checks the user agent is Webkit based and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_webkit_version($version = null) {
        // WebKit based browser - everything derived from it (Safari, Chrome, iOS, Android and other mobiles).
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'AppleWebKit') === false) {
            return false;
        }
        if (empty($version)) {
            return true; // No version specified.
        }
        if (preg_match("/AppleWebKit\/([0-9.]+)/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is Safari
     *
     * @return bool true if safari
     */
    public static function is_safari() {
        return self::check_safari_version();
    }

    /**
     * Checks the user agent is Safari based and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_safari_version($version = null) {
        // Desktop version of Apple Safari browser - no mobile or touch devices.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'AppleWebKit') === false) {
            return false;
        }
        // Look for AppleWebKit, excluding strings with OmniWeb, Shiira and SymbianOS and any other mobile devices.
        if (strpos($useragent, 'OmniWeb')) {
            // Reject OmniWeb.
            return false;
        }
        if (strpos($useragent, 'Shiira')) {
            // Reject Shiira.
            return false;
        }
        if (strpos($useragent, 'SymbianOS')) {
            // Reject SymbianOS.
            return false;
        }
        if (strpos($useragent, 'Android')) {
            // Reject Androids too.
            return false;
        }
        if (strpos($useragent, 'iPhone') or strpos($useragent, 'iPad') or strpos($useragent, 'iPod')) {
            // No Apple mobile devices here - editor does not work, course ajax is not touch compatible, etc.
            return false;
        }
        if (strpos($useragent, 'Chrome')) { // Reject chrome browsers - it needs to be tested explicitly.
            return false;
        }

        if (empty($version)) {
            return true; // No version specified.
        }
        if (preg_match("/AppleWebKit\/([0-9.]+)/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is Chrome
     *
     * @return bool true if chrome
     */
    public static function is_chrome() {
        return self::check_chrome_version();
    }

    /**
     * Checks the user agent is Chrome based and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_chrome_version($version = null) {
        // Chrome.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'Chrome') === false) {
            return false;
        }
        if (empty($version)) {
            return true; // No version specified.
        }
        if (preg_match("/Chrome\/(.*)[ ]+/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is webkit android based.
     *
     * @return bool true if webkit based and on Android
     */
    public static function is_webkit_android() {
        return self::check_webkit_android_version();
    }

    /**
     * Checks the user agent is Webkit based and on Android and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_webkit_android_version($version = null) {
        // WebKit browser on Android.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'Linux; U; Android') === false) {
            return false;
        }
        if (empty($version)) {
            return true; // No version specified.
        }
        if (preg_match("/AppleWebKit\/([0-9]+)/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the user agent is Safari on iOS
     *
     * @return bool true if Safari on iOS
     */
    public static function is_safari_ios() {
        return self::check_safari_ios_version();
    }

    /**
     * Checks the user agent is Safari on iOS and that the version is equal to or greater than that specified.
     *
     * @param string|int $version A version to check for, returns true if its equal to or greater than that specified.
     * @return bool
     */
    public static function check_safari_ios_version($version = null) {
        // Safari on iPhone, iPad and iPod touch.
        $useragent = self::get_user_agent_string();
        if ($useragent === false) {
            return false;
        }
        if (strpos($useragent, 'AppleWebKit') === false or strpos($useragent, 'Safari') === false) {
            return false;
        }
        if (!strpos($useragent, 'iPhone') and !strpos($useragent, 'iPad') and !strpos($useragent, 'iPod')) {
            return false;
        }
        if (empty($version)) {
            return true; // No version specified.
        }
        if (preg_match("/AppleWebKit\/([0-9]+)/i", $useragent, $match)) {
            if (version_compare($match[1], $version) >= 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user agent matches a given brand.
     *
     * Known brand: 'Windows','Linux','Macintosh','SGI','SunOS','HP-UX'
     *
     * @param string $brand
     * @return bool
     */
    public static function check_browser_operating_system($brand) {
        $useragent = self::get_user_agent_string();
        return ($useragent !== false && preg_match("/$brand/i", $useragent));
    }

    /**
     * Gets an array of CSS classes to represent the user agent.
     * @return array
     */
    public static function get_browser_version_classes() {
        $classes = array();
        if (self::is_ie()) {
            $classes[] = 'ie';
            for ($i = 12; $i >= 6; $i--) {
                if (self::check_ie_version($i)) {
                    $classes[] = 'ie'.$i;
                    break;
                }
            }
        } else if (self::is_firefox() || self::is_gecko() || self::check_camino_version()) {
            $classes[] = 'gecko';
            if (preg_match('/rv\:([1-2])\.([0-9])/', self::get_user_agent_string(), $matches)) {
                $classes[] = "gecko{$matches[1]}{$matches[2]}";
            }
        } else if (self::is_webkit()) {
            $classes[] = 'safari';
            if (self::is_safari_ios()) {
                $classes[] = 'ios';
            } else if (self::is_webkit_android()) {
                $classes[] = 'android';
            }
        } else if (self::is_opera()) {
            $classes[] = 'opera';
        }
        return $classes;
    }

    /**
     * Returns true if the user agent supports the display of SVG images.
     *
     * @return bool
     */
    public static function supports_svg() {
        // IE 5 - 8 don't support SVG at all.
        $instance = self::instance();
        if ($instance->supportssvg === null) {
            if ($instance->useragent === false) {
                // Can't be sure, just say no.
                $instance->supportssvg = false;
            } else if (self::is_ie() and !self::check_ie_version('9')) {
                // IE < 9 doesn't support SVG. Say no.
                $instance->supportssvg = false;
            } else if (self::is_ie() and !self::check_ie_version('10') and self::check_ie_compatibility_view()) {
                // IE 9 Compatibility View doesn't support SVG. Say no.
                $instance->supportssvg = false;
            } else if (preg_match('#Android +[0-2]\.#', $instance->useragent)) {
                // Android < 3 doesn't support SVG. Say no.
                $instance->supportssvg = false;
            } else if (self::is_opera()) {
                // Opera 12 still does not support SVG well enough. Say no.
                $instance->supportssvg = false;
            } else {
                // Presumed fine.
                $instance->supportssvg = true;
            }
        }
        return $instance->supportssvg;
    }

    /**
     * Returns true if the user agent supports the MIME media type for JSON text, as defined in RFC 4627.
     *
     * @return bool
     */
    public static function supports_json_contenttype() {
        // Modern browsers other than IE correctly supports 'application/json' media type.
        if (!self::is_ie()) {
            return true;
        }

        // IE8+ supports 'application/json' media type, when NOT in Compatibility View mode.
        // Refs:
        // - http://blogs.msdn.com/b/ie/archive/2008/09/10/native-json-in-ie8.aspx;
        // - MDL-39810: issues when using 'text/plain' in Compatibility View for the body of an HTTP POST response.
        if (self::check_ie_version(8) && !self::check_ie_compatibility_view()) {
            return true;
        }

        // This browser does not support json.
        return false;
    }
}
