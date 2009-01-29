<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Date
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * Include needed Date classes
 */
require_once 'Zend/Date/DateObject.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Locale/Format.php';
require_once 'Zend/Locale/Math.php';

/**
 * @category  Zend
 * @package   Zend_Date
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date extends Zend_Date_DateObject
{
    private $_locale  = null;

    // Fractional second variables
    private $_fractional = 0;
    private $_precision  = 3;

    private static $_options = array(
        'format_type'  => 'iso',      // format for date strings 'iso' or 'php'
        'fix_dst'      => true,       // fix dst on summer/winter time change
        'extend_month' => false,      // false - addMonth like SQL, true like excel
        'cache'        => null,       // cache to set
        'timesync'     => null        // timesync server to set
    );

    // Class wide Date Constants
    // day formats
    const DAY            = 'DAY';            // d - 2 digit day of month, 01-31
    const DAY_SHORT      = 'DAY_SHORT';      // j - 1,2 digit day of month, 1-31

    const DAY_SUFFIX     = 'DAY_SUFFIX';     // S - english suffix day of month, st-th
    const DAY_OF_YEAR    = 'DAY_OF_YEAR';    // z - Number of day of year

    const WEEKDAY        = 'WEEKDAY';        // l - full day name - locale aware, Monday - Sunday
    const WEEKDAY_SHORT  = 'WEEKDAY_SHORT';  // --- 3 letter day of week - locale aware, Mon-Sun
    const WEEKDAY_NARROW = 'WEEKDAY_NARROW'; // --- 1 letter day name - locale aware, M-S
    const WEEKDAY_NAME   = 'WEEKDAY_NAME';   // D - abbreviated day name, 1-3 letters - locale aware, Mon-Sun

    const WEEKDAY_8601   = 'WEEKDAY_8601';   // N - digit weekday ISO 8601, 1-7 1 = monday, 7=sunday
    const WEEKDAY_DIGIT  = 'WEEKDAY_DIGIT';  // w - weekday, 0-6 0=sunday, 6=saturday

    // week formats
    const WEEK           = 'WEEK';           // W - number of week ISO8601, 1-53

    // month formats
    const MONTH          = 'MONTH';          // m - 2 digit month, 01-12
    const MONTH_SHORT    = 'MONTH_SHORT';    // n - 1 digit month, no leading zeros, 1-12

    const MONTH_DAYS     = 'MONTH_DAYS';     // t - Number of days this month

    const MONTH_NAME        = 'MONTH_NAME';         // F - full month name - locale aware, January-December
    const MONTH_NAME_SHORT  = 'MONTH_NAME_SHORT';  // M - 3 letter monthname - locale aware, Jan-Dec
    const MONTH_NAME_NARROW = 'MONTH_NAME_NARROW'; // --- 1 letter month name - locale aware, J-D

    // year formats
    const YEAR           = 'YEAR';           // Y - 4 digit year
    const YEAR_SHORT     = 'YEAR_SHORT';     // y - 2 digit year, leading zeros 00-99

    const YEAR_8601      = 'YEAR_8601';      // o - number of year ISO8601
    const YEAR_SHORT_8601= 'YEAR_SHORT_8601';// --- 2 digit number of year ISO8601

    const LEAPYEAR       = 'LEAPYEAR';       // L - is leapyear ?, 0-1

    // time formats
    const MERIDIEM       = 'MERIDIEM';       // A,a - AM/PM - locale aware, AM/PM
    const SWATCH         = 'SWATCH';         // B - Swatch Internet Time

    const HOUR           = 'HOUR';           // H - 2 digit hour, leading zeros, 00-23
    const HOUR_SHORT     = 'HOUR_SHORT';     // G - 1 digit hour, no leading zero, 0-23

    const HOUR_AM        = 'HOUR_AM';        // h - 2 digit hour, leading zeros, 01-12 am/pm
    const HOUR_SHORT_AM  = 'HOUR_SHORT_AM';  // g - 1 digit hour, no leading zero, 1-12 am/pm

    const MINUTE         = 'MINUTE';         // i - 2 digit minute, leading zeros, 00-59
    const MINUTE_SHORT   = 'MINUTE_SHORT';   // --- 1 digit minute, no leading zero, 0-59

    const SECOND         = 'SECOND';         // s - 2 digit second, leading zeros, 00-59
    const SECOND_SHORT   = 'SECOND_SHORT';   // --- 1 digit second, no leading zero, 0-59

    const MILLISECOND    = 'MILLISECOND';    // --- milliseconds

    // timezone formats
    const TIMEZONE_NAME  = 'TIMEZONE_NAME';  // e - timezone string
    const DAYLIGHT       = 'DAYLIGHT';       // I - is Daylight saving time ?, 0-1
    const GMT_DIFF       = 'GMT_DIFF';       // O - GMT difference, -1200 +1200
    const GMT_DIFF_SEP   = 'GMT_DIFF_SEP';   // P - seperated GMT diff, -12:00 +12:00
    const TIMEZONE       = 'TIMEZONE';       // T - timezone, EST, GMT, MDT
    const TIMEZONE_SECS  = 'TIMEZONE_SECS';  // Z - timezone offset in seconds, -43200 +43200

    // date strings
    const ISO_8601       = 'ISO_8601';       // c - ISO 8601 date string
    const RFC_2822       = 'RFC_2822';       // r - RFC 2822 date string
    const TIMESTAMP      = 'TIMESTAMP';      // U - unix timestamp

    // additional formats
    const ERA            = 'ERA';            // --- short name of era, locale aware,
    const ERA_NAME       = 'ERA_NAME';       // --- full name of era, locale aware,
    const DATES          = 'DATES';          // --- standard date, locale aware
    const DATE_FULL      = 'DATE_FULL';      // --- full date, locale aware
    const DATE_LONG      = 'DATE_LONG';      // --- long date, locale aware
    const DATE_MEDIUM    = 'DATE_MEDIUM';    // --- medium date, locale aware
    const DATE_SHORT     = 'DATE_SHORT';     // --- short date, locale aware
    const TIMES          = 'TIMES';          // --- standard time, locale aware
    const TIME_FULL      = 'TIME_FULL';      // --- full time, locale aware
    const TIME_LONG      = 'TIME_LONG';      // --- long time, locale aware
    const TIME_MEDIUM    = 'TIME_MEDIUM';    // --- medium time, locale aware
    const TIME_SHORT     = 'TIME_SHORT';     // --- short time, locale aware
    const ATOM           = 'ATOM';           // --- DATE_ATOM
    const COOKIE         = 'COOKIE';         // --- DATE_COOKIE
    const RFC_822        = 'RFC_822';        // --- DATE_RFC822
    const RFC_850        = 'RFC_850';        // --- DATE_RFC850
    const RFC_1036       = 'RFC_1036';       // --- DATE_RFC1036
    const RFC_1123       = 'RFC_1123';       // --- DATE_RFC1123
    const RFC_3339       = 'RFC_3339';       // --- DATE_RFC3339
    const RSS            = 'RSS';            // --- DATE_RSS
    const W3C            = 'W3C';            // --- DATE_W3C

    /**
     * Generates the standard date object, could be a unix timestamp, localized date,
     * string, integer, array and so on. Also parts of dates or time are supported
     * Always set the default timezone: http://php.net/date_default_timezone_set
     * For example, in your bootstrap: date_default_timezone_set('America/Los_Angeles');
     * For detailed instructions please look in the docu.
     *
     * @param  string|integer|Zend_Date|array  $date    OPTIONAL Date value or value of date part to set
     *                                                 ,depending on $part. If null the actual time is set
     * @param  string                          $part    OPTIONAL Defines the input format of $date
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function __construct($date = null, $part = null, $locale = null)
    {
        if (($date !== null) and !($date instanceof Zend_TimeSync_Protocol) and (Zend_Locale::isLocale($date, true, false))) {
            $locale = $date;
            $date = null;
            $part = null;
        } else if (($part !== null) and (Zend_Locale::isLocale($part, null, false))) {
            $locale = $part;
            $part   = null;
        }

        if (empty($locale)) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Locale') === true) {
                $locale = Zend_Registry::get('Zend_Locale');
            }
        }

        $this->setLocale($locale);

        if (is_string($date) && defined('self::' . $date)) {
            $part = $date;
            $date = null;
        }

        if (is_null($date)) {
            $date = self::now($locale);
            if (($part !== null) && ($part !== self::TIMESTAMP)) {
                $date = $date->get($part);
            }
        }

        if ($date instanceof Zend_TimeSync_Protocol) {
            $date = $date->getInfo();
            $date = $this->_getTime($date['offset']);
            $part = null;
        } else if (parent::$_defaultOffset != 0) {
            $date = $this->_getTime(parent::$_defaultOffset);
        }

        // set the timezone and offset for $this
        $zone = @date_default_timezone_get();
        $this->setTimezone($zone);

        // try to get timezone from date-string
        if (!is_int($date)) {
            $zone = $this->getTimezoneFromString($date);
            $this->setTimezone($zone);
        }

        // set datepart
        if (($part !== null && $part !== self::TIMESTAMP) or (!is_numeric($date))) {
            // switch off dst handling for value setting
            $this->setUnixTimestamp($this->getGmtOffset());
            $this->set($date, $part, $this->_locale);

            // DST fix
            if ((is_array($date) === true) and (isset($date['hour']) === true)) {
                $hour = $this->toString('H');
                $hour = $date['hour'] - $hour;
                if ($hour !== 0) {
                    $this->addTimestamp($hour * 3600);
                }
            }
        } else {
            $this->setUnixTimestamp($date);
        }
    }

    /**
     * Sets class wide options, if no option was given, the actual set options will be returned
     *
     * @param  array  $options  Options to set
     * @throws Zend_Date_Exception
     * @return Options array if no option was given
     */
    public static function setOptions(array $options = array())
    {
        if (empty($options)) {
            return self::$_options;
        }
        foreach ($options as $name => $value) {
            $name  = strtolower($name);

            if (array_key_exists($name, self::$_options)) {
                switch($name) {
                    case 'format_type' :
                        if ((strtolower($value) != 'php') && (strtolower($value) != 'iso')) {
                            require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("Unknown format type ($value) for dates, only 'iso' and 'php' supported", $value);
                        }
                        break;
                    case 'fix_dst' :
                        if (!is_bool($value)) {
                            require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("'fix_dst' has to be boolean", $value);
                        }
                        break;
                    case 'extend_month' :
                        if (!is_bool($value)) {
                            require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("'extend_month' has to be boolean", $value);
                        }
                        break;
                    case 'cache' :
                        if (!$value instanceof Zend_Cache_Core) {
                            require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("Instance of Zend_Cache expected");
                        }
                        parent::$_cache = $value;
                        Zend_Locale_Data::setCache($value);
                        break;
                    case 'timesync' :
                        if (!$value instanceof Zend_TimeSync_Protocol) {
                            require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception("Instance of Zend_TimeSync expected");
                        }
                        $date = $value->getInfo();
                        parent::$_defaultOffset = $date['offset'];
                        break;
                }
                self::$_options[$name] = $value;
            }
            else {
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("Unknown option: $name = $value");
            }
        }
    }

    /**
     * Returns this object's internal UNIX timestamp (equivalent to Zend_Date::TIMESTAMP).
     * If the timestamp is too large for integers, then the return value will be a string.
     * This function does not return the timestamp as an object.
     * Use clone() or copyPart() instead.
     *
     * @return integer|string  UNIX timestamp
     */
    public function getTimestamp()
    {
        return $this->getUnixTimestamp();
    }

    /**
     * Returns the calculated timestamp
     * HINT: timestamps are always GMT
     *
     * @param  string                          $calc    Type of calculation to make
     * @param  string|integer|array|Zend_Date  $stamp   Timestamp to calculate, when null the actual timestamp is calculated
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _timestamp($calc, $stamp)
    {
        if ($stamp instanceof Zend_Date) {
            // extract timestamp from object
            $stamp = $stamp->get(self::TIMESTAMP, true);
        }

        if (is_array($stamp)) {
            if (isset($stamp['timestamp']) === true) {
                $stamp = $stamp['timestamp'];
            } else {
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('no timestamp given in array');
            }
        }

        if ($calc === 'set') {
            $return = $this->setUnixTimestamp($stamp);
        } else {
            $return = $this->_calcdetail($calc, $stamp, self::TIMESTAMP, null);
        }
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }

    /**
     * Sets a new timestamp
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to set
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function setTimestamp($timestamp)
    {
        return $this->_timestamp('set', $timestamp);
    }

    /**
     * Adds a timestamp
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to add
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function addTimestamp($timestamp)
    {
        return $this->_timestamp('add', $timestamp);
    }

    /**
     * Subtracts a timestamp
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to sub
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function subTimestamp($timestamp)
    {
        return $this->_timestamp('sub', $timestamp);
    }

    /**
     * Compares two timestamps, returning the difference as integer
     *
     * @param  integer|string|array|Zend_Date  $timestamp  Timestamp to compare
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTimestamp($timestamp)
    {
        return $this->_timestamp('cmp', $timestamp);
    }

    /**
     * Returns a string representation of the object
     * Supported format tokens are:
     * G - era, y - year, Y - ISO year, M - month, w - week of year, D - day of year, d - day of month
     * E - day of week, e - number of weekday (1-7), h - hour 1-12, H - hour 0-23, m - minute, s - second
     * A - milliseconds of day, z - timezone, Z - timezone offset, S - fractional second, a - period of day
     *
     * Additionally format tokens but non ISO conform are:
     * SS - day suffix, eee - php number of weekday(0-6), ddd - number of days per month
     * l - Leap year, B - swatch internet time, I - daylight saving time, X - timezone offset in seconds
     * r - RFC2822 format, U - unix timestamp
     *
     * Not supported ISO tokens are
     * u - extended year, Q - quarter, q - quarter, L - stand alone month, W - week of month
     * F - day of week of month, g - modified julian, c - stand alone weekday, k - hour 0-11, K - hour 1-24
     * v - wall zone
     *
     * @param  string              $format  OPTIONAL Rule for formatting output. If null the default date format is used
     * @param  string              $type    OPTIONAL Type for the format string which overrides the standard setting
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function toString($format = null, $type = null, $locale = null)
    {
        if ((strlen($format) != 2) and ($format !== null) and (Zend_Locale::isLocale($format, null, false))) {
            $locale = $format;
            $format = null;
        }

        if (($type !== null) and (Zend_Locale::isLocale($type, null, false))) {
            $locale = $type;
            $type = null;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($format === null) {
            $format = Zend_Locale_Format::getDateFormat($locale) . ' ' . Zend_Locale_Format::getTimeFormat($locale);
        } else if (((self::$_options['format_type'] == 'php') && ($type === null)) or ($type == 'php')) {
            $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
        }

        // get format tokens
        $j = 0;
        $comment = false;
        $output = array();
        for($i = 0; $i < strlen($format); ++$i) {

            if ($format[$i] == "'") {
                if ($comment == false) {
                    $comment = true;
                    ++$j;
                    $output[$j] = "'";
                } else if (isset($format[$i+1]) and ($format[$i+1] == "'")) {
                    $output[$j] .= "'";
                    ++$i;
                } else {
                    $comment = false;
                }
                continue;
            }

            if (isset($output[$j]) and ($output[$j][0] == $format[$i]) or
                ($comment == true)) {
                $output[$j] .= $format[$i];
            } else {
                ++$j;
                $output[$j] = $format[$i];
            }
        }

        $notset = false;
        // fill format tokens with date information
        for($i = 1; $i <= count($output); ++$i) {
            // fill fixed tokens
            switch ($output[$i]) {

                // special formats
                case 'SS' :
                    $output[$i] = $this->date('S', $this->getUnixTimestamp(), false);
                    break;

                case 'eee' :
                    $output[$i] = $this->date('N', $this->getUnixTimestamp(), false);
                    break;

                case 'ddd' :
                    $output[$i] = $this->date('t', $this->getUnixTimestamp(), false);
                    break;

                case 'l' :
                    $output[$i] = $this->date('L', $this->getUnixTimestamp(), false);
                    break;

                case 'B' :
                    $output[$i] = $this->date('B', $this->getUnixTimestamp(), false);
                    break;

                case 'I' :
                    $output[$i] = $this->date('I', $this->getUnixTimestamp(), false);
                    break;

                case 'X' :
                    $output[$i] = $this->date('Z', $this->getUnixTimestamp(), false);
                    break;

                case 'r' :
                    $output[$i] = $this->date('r', $this->getUnixTimestamp(), false);
                    break;

                case 'U' :
                    $output[$i] = $this->getUnixTimestamp();
                    break;

                    // eras
                case 'GGGGG' :
                    $output[$i] = iconv_substr($this->get(self::ERA, $locale), 0, 1, 'UTF-8') . ".";
                    break;

                case 'GGGG' :
                    $output[$i] = $this->get(self::ERA_NAME, $locale);
                    break;

                case 'GGG' :
                case 'GG'  :
                case 'G'   :
                    $output[$i] = $this->get(self::ERA, $locale);
                    break;

                // years
                case 'yy' :
                    $output[$i] = str_pad($this->get(self::YEAR_SHORT, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                // ISO years
                case 'YY' :
                    $output[$i] = str_pad($this->get(self::YEAR_SHORT_8601, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                // months
                case 'MMMMM' :
                    $output[$i] = iconv_substr($this->get(self::MONTH_NAME_NARROW, $locale), 0, 1, 'UTF-8');
                    break;

                case 'MMMM' :
                    $output[$i] = $this->get(self::MONTH_NAME, $locale);
                    break;

                case 'MMM' :
                    $output[$i] = $this->get(self::MONTH_NAME_SHORT, $locale);
                    break;

                case 'MM' :
                    $output[$i] = $this->get(self::MONTH, $locale);
                    break;

                case 'M' :
                    $output[$i] = $this->get(self::MONTH_SHORT, $locale);
                    break;

                // week
                case 'ww' :
                    $output[$i] = str_pad($this->get(self::WEEK, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'w' :
                    $output[$i] = $this->get(self::WEEK, $locale);
                    break;

                // monthday
                case 'dd' :
                    $output[$i] = $this->get(self::DAY, $locale);
                    break;

                case 'd' :
                    $output[$i] = $this->get(self::DAY_SHORT, $locale);
                    break;

                // yearday
                case 'DDD' :
                    $output[$i] = str_pad($this->get(self::DAY_OF_YEAR, $locale), 3, '0', STR_PAD_LEFT);
                    break;

                case 'DD' :
                    $output[$i] = str_pad($this->get(self::DAY_OF_YEAR, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'D' :
                    $output[$i] = $this->get(self::DAY_OF_YEAR, $locale);
                    break;

                // weekday
                case 'EEEEE' :
                    $output[$i] = $this->get(self::WEEKDAY_NARROW, $locale);
                    break;

                case 'EEEE' :
                    $output[$i] = $this->get(self::WEEKDAY, $locale);
                    break;

                case 'EEE' :
                    $output[$i] = $this->get(self::WEEKDAY_SHORT, $locale);
                    break;

                case 'EE' :
                    $output[$i] = $this->get(self::WEEKDAY_NAME, $locale);
                    break;

                case 'E' :
                    $output[$i] = $this->get(self::WEEKDAY_NARROW, $locale);
                    break;

                // weekday number
                case 'ee' :
                    $output[$i] = str_pad($this->get(self::WEEKDAY_8601, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'e' :
                    $output[$i] = $this->get(self::WEEKDAY_8601, $locale);
                    break;


                // period
                case 'a' :
                    $output[$i] = $this->get(self::MERIDIEM, $locale);
                    break;

                // hour
                case 'hh' :
                    $output[$i] = $this->get(self::HOUR_AM, $locale);
                    break;

                case 'h' :
                    $output[$i] = $this->get(self::HOUR_SHORT_AM, $locale);
                    break;

                case 'HH' :
                    $output[$i] = $this->get(self::HOUR, $locale);
                    break;

                case 'H' :
                    $output[$i] = $this->get(self::HOUR_SHORT, $locale);
                    break;

                // minute
                case 'mm' :
                    $output[$i] = $this->get(self::MINUTE, $locale);
                    break;

                case 'm' :
                    $output[$i] = $this->get(self::MINUTE_SHORT, $locale);
                    break;

                // second
                case 'ss' :
                    $output[$i] = $this->get(self::SECOND, $locale);
                    break;

                case 's' :
                    $output[$i] = $this->get(self::SECOND_SHORT, $locale);
                    break;

                case 'S' :
                    $output[$i] = $this->get(self::MILLISECOND, $locale);
                    break;

                // zone
                // @todo  v needs to be reworked as it's the long wall time and not the timezone
                case 'vvvv' :
                case 'zzzz' :
                    $output[$i] = $this->get(self::TIMEZONE_NAME, $locale);
                    break;

                // @todo  v needs to be reworked as it's the short wall time and not the timezone
                case 'v' :
                case 'zzz' :
                case 'zz'  :
                case 'z'   :
                    $output[$i] = $this->get(self::TIMEZONE, $locale);
                    break;

                // zone offset
                case 'ZZZZ' :
                    $output[$i] = $this->get(self::GMT_DIFF_SEP, $locale);
                    break;

                case 'ZZZ' :
                case 'ZZ'  :
                case 'Z'   :
                    $output[$i] = $this->get(self::GMT_DIFF, $locale);
                    break;

                default :
                    $notset = true;
                    break;
            }

            // fill variable tokens
            if ($notset == true) {
                if (($output[$i][0] !== "'") and (preg_match('/y+/', $output[$i]))) {
                    $length     = iconv_strlen($output[$i], 'UTF-8');
                    $output[$i] = $this->get(self::YEAR, $locale);
                    $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
                }

                if (($output[$i][0] !== "'") and (preg_match('/Y+/', $output[$i]))) {
                    $length     = iconv_strlen($output[$i], 'UTF-8');
                    $output[$i] = $this->get(self::YEAR_8601, $locale);
                    $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
                }

                if (($output[$i][0] !== "'") and (preg_match('/A+/', $output[$i]))) {
                    $length = iconv_strlen($output[$i], 'UTF-8');
                    $hour   = $this->get(self::HOUR,        $locale);
                    $minute = $this->get(self::MINUTE,      $locale);
                    $second = $this->get(self::SECOND,      $locale);
                    $milli  = $this->get(self::MILLISECOND, $locale);

                    $seconds    = $milli + ($second * 1000) + ($minute * 60000) + ($hour * 3600000);
                    $output[$i] = str_pad($seconds, $length, '0', STR_PAD_LEFT);
                }

                if ($output[$i][0] === "'") {
                    $output[$i] = iconv_substr($output[$i],
                                               1,
                                               iconv_strlen($output[$i], 'UTF-8') - 1,
                                               'UTF-8');
                }
            }
            $notset = false;
        }

        return implode('', $output);
    }

    /**
     * Returns a string representation of the date which is equal with the timestamp
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString(null, $this->_locale);
    }

    /**
     * Returns a integer representation of the object
     * But returns false when the given part is no value f.e. Month-Name
     *
     * @param  string|integer|Zend_Date  $part  OPTIONAL Defines the date or datepart to return as integer
     * @return integer|false
     */
    public function toValue($part = null)
    {
        $result = $this->get($part);
        if (is_numeric($result)) {
          return intval("$result");
        } else {
          return false;
        }
    }

    /**
     * Returns an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return array('day'       => $this->get(self::DAY_SHORT),
                     'month'     => $this->get(self::MONTH_SHORT),
                     'year'      => $this->get(self::YEAR),
                     'hour'      => $this->get(self::HOUR_SHORT),
                     'minute'    => $this->get(self::MINUTE_SHORT),
                     'second'    => $this->get(self::SECOND_SHORT),
                     'timezone'  => $this->get(self::TIMEZONE),
                     'timestamp' => $this->get(self::TIMESTAMP),
                     'weekday'   => $this->get(self::WEEKDAY_8601),
                     'dayofyear' => $this->get(self::DAY_OF_YEAR),
                     'week'      => $this->get(self::WEEK),
                     'gmtsecs'   => $this->get(self::TIMEZONE_SECS));
    }

    /**
     * Returns a representation of a date or datepart
     * This could be for example a localized monthname, the time without date,
     * the era or only the fractional seconds. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string              $part    OPTIONAL Part of the date to return, if null the timestamp is returned
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return integer|string  date or datepart
     */
    public function get($part = null, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if (($part !== null) and (Zend_Locale::isLocale($part, null, false))) {
            $locale = $part;
            $part = null;
        }

        if ($part === null) {
            $part = self::TIMESTAMP;
        }

        if (!defined("self::".$part)) {
            return $this->toString($part, $locale);
        }

        switch($part) {

            // day formats
            case self::DAY :
                return $this->date('d', $this->getUnixTimestamp(), false);
                break;

            case self::WEEKDAY_SHORT :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'wide', $weekday));
                return iconv_substr($day, 0, 3, 'UTF-8');
                break;

            case self::DAY_SHORT :
                return $this->date('j', $this->getUnixTimestamp(), false);
                break;

            case self::WEEKDAY :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                return Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'wide', $weekday));
                break;

            case self::WEEKDAY_8601 :
                return $this->date('N', $this->getUnixTimestamp(), false);
                break;

            case self::DAY_SUFFIX :
                return $this->date('S', $this->getUnixTimestamp(), false);
                break;

            case self::WEEKDAY_DIGIT :
                return $this->date('w', $this->getUnixTimestamp(), false);
                break;

            case self::DAY_OF_YEAR :
                return $this->date('z', $this->getUnixTimestamp(), false);
                break;

            case self::WEEKDAY_NARROW :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'abbreviated', $weekday));
                return iconv_substr($day, 0, 1, 'UTF-8');
                break;

            case self::WEEKDAY_NAME :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), false));
                return Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'format', 'abbreviated', $weekday));
                break;

            // week formats
            case self::WEEK :
                return $this->date('W', $this->getUnixTimestamp(), false);
                break;

            // month formats
            case self::MONTH_NAME :
                $month = $this->date('n', $this->getUnixTimestamp(), false);
                return Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'wide', $month));
                break;

            case self::MONTH :
                return $this->date('m', $this->getUnixTimestamp(), false);
                break;

            case self::MONTH_NAME_SHORT :
                $month = $this->date('n', $this->getUnixTimestamp(), false);
                return Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                break;

            case self::MONTH_SHORT :
                return $this->date('n', $this->getUnixTimestamp(), false);
                break;

            case self::MONTH_DAYS :
                return $this->date('t', $this->getUnixTimestamp(), false);
                break;

            case self::MONTH_NAME_NARROW :
                $month = $this->date('n', $this->getUnixTimestamp(), false);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return iconv_substr($mon, 0, 1, 'UTF-8');
                break;

            // year formats
            case self::LEAPYEAR :
                return $this->date('L', $this->getUnixTimestamp(), false);
                break;

            case self::YEAR_8601 :
                return $this->date('o', $this->getUnixTimestamp(), false);
                break;

            case self::YEAR :
                return $this->date('Y', $this->getUnixTimestamp(), false);
                break;

            case self::YEAR_SHORT :
                return $this->date('y', $this->getUnixTimestamp(), false);
                break;


            case self::YEAR_SHORT_8601 :
                return substr($this->date('o', $this->getUnixTimestamp(), false), -2, 2);
                break;

            // time formats
            case self::MERIDIEM :
                $am = $this->date('a', $this->getUnixTimestamp(), false);
                if ($am == 'am') {
                    return Zend_Locale_Data::getContent($locale, 'am');
                }
                return Zend_Locale_Data::getContent($locale, 'pm');
                break;

            case self::SWATCH :
                return $this->date('B', $this->getUnixTimestamp(), false);
                break;

            case self::HOUR_SHORT_AM :
                return $this->date('g', $this->getUnixTimestamp(), false);
                break;

            case self::HOUR_SHORT :
                return $this->date('G', $this->getUnixTimestamp(), false);
                break;

            case self::HOUR_AM :
                return $this->date('h', $this->getUnixTimestamp(), false);
                break;

            case self::HOUR :
                return $this->date('H', $this->getUnixTimestamp(), false);
                break;

            case self::MINUTE :
                return $this->date('i', $this->getUnixTimestamp(), false);
                break;

            case self::SECOND :
                return $this->date('s', $this->getUnixTimestamp(), false);
                break;

            case self::MINUTE_SHORT :
                return $this->date('i', $this->getUnixTimestamp(), false);
                break;

            case self::SECOND_SHORT :
                return $this->date('s', $this->getUnixTimestamp(), false);
                break;

            case self::MILLISECOND :
                return $this->_fractional;
                break;

            // timezone formats
            case self::TIMEZONE_NAME :
                return $this->date('e', $this->getUnixTimestamp(), false);
                break;

            case self::DAYLIGHT :
                return $this->date('I', $this->getUnixTimestamp(), false);
                break;

            case self::GMT_DIFF :
                return $this->date('O', $this->getUnixTimestamp(), false);
                break;

            case self::GMT_DIFF_SEP :
                return $this->date('P', $this->getUnixTimestamp(), false);
                break;

            case self::TIMEZONE :
                return $this->date('T', $this->getUnixTimestamp(), false);
                break;

            case self::TIMEZONE_SECS :
                return $this->date('Z', $this->getUnixTimestamp(), false);
                break;

            // date strings
            case self::ISO_8601 :
                return $this->date('c', $this->getUnixTimestamp(), false);
                break;

            case self::RFC_2822 :
                return $this->date('r', $this->getUnixTimestamp(), false);
                break;

            case self::TIMESTAMP :
                return $this->getUnixTimestamp();
                break;

            // additional formats
            case self::ERA :
                $year = $this->date('Y', $this->getUnixTimestamp(), false);
                if ($year < 0) {
                    return Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Abbr', '0'));
                }
                return Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Abbr', '1'));
                break;

            case self::ERA_NAME :
                $year = $this->date('Y', $this->getUnixTimestamp(), false);
                if ($year < 0) {
                    return Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Names', '0'));
                }
                return Zend_Locale_Data::getContent($locale, 'era', array('gregorian', 'Names', '1'));
                break;

            case self::DATES :
                return $this->toString(Zend_Locale_Format::getDateFormat($locale), 'iso', $locale);
                break;

            case self::DATE_FULL :
                $date = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'full'));
                return $this->toString($date, 'iso', $locale);
                break;

            case self::DATE_LONG :
                $date = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'long'));
                return $this->toString($date, 'iso', $locale);
                break;

            case self::DATE_MEDIUM :
                $date = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'medium'));
                return $this->toString($date, 'iso', $locale);
                break;

            case self::DATE_SHORT :
                $date = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'short'));
                return $this->toString($date, 'iso', $locale);
                break;

            case self::TIMES :
                return $this->toString(Zend_Locale_Format::getTimeFormat($locale), 'iso', $locale);
                break;

            case self::TIME_FULL :
                $time = Zend_Locale_Data::getContent($locale, 'time', 'full');
                return $this->toString($time, 'iso', $locale);
                break;

            case self::TIME_LONG :
                $time = Zend_Locale_Data::getContent($locale, 'time', 'long');
                return $this->toString($time, 'iso', $locale);
                break;

            case self::TIME_MEDIUM :
                $time = Zend_Locale_Data::getContent($locale, 'time', 'medium');
                return $this->toString($time, 'iso', $locale);
                break;

            case self::TIME_SHORT :
                $time = Zend_Locale_Data::getContent($locale, 'time', 'short');
                return $this->toString($time, 'iso', $locale);
                break;

            case self::ATOM :
                return $this->date('Y\-m\-d\TH\:i\:sP', $this->getUnixTimestamp(), false);
                break;

            case self::COOKIE :
                return $this->date('l\, d\-M\-y H\:i\:s e', $this->getUnixTimestamp(), false);
                break;

            case self::RFC_822 :
                return $this->date('D\, d M y H\:i\:s O', $this->getUnixTimestamp(), false);
                break;

            case self::RFC_850 :
                return $this->date('l\, d\-M\-y H\:i\:s e', $this->getUnixTimestamp(), false);
                break;

            case self::RFC_1036 :
                return $this->date('D\, d M y H\:i\:s O', $this->getUnixTimestamp(), false);
                break;

            case self::RFC_1123 :
                return $this->date('D\, d M Y H\:i\:s O', $this->getUnixTimestamp(), false);
                break;

            case self::RFC_3339 :
                return $this->date('Y\-m\-d\TH\:i\:sP', $this->getUnixTimestamp(), false);
                break;

            case self::RSS :
                return $this->date('D\, d M Y H\:i\:s O', $this->getUnixTimestamp(), false);
                break;

            case self::W3C :
                return $this->date('Y\-m\-d\TH\:i\:sP', $this->getUnixTimestamp(), false);
                break;
        }
    }

    /**
     * Return digit from standard names (english)
     * Faster implementation than locale aware searching
     *
     * @param  string  $name
     * @return integer  Number of this month
     * @throws Zend_Date_Exception
     */
    private function _getDigitFromName($name)
    {
        switch($name) {
            case "Jan":
                return 1;

            case "Feb":
                return 2;

            case "Mar":
                return 3;

            case "Apr":
                return 4;

            case "May":
                return 5;

            case "Jun":
                return 6;

            case "Jul":
                return 7;

            case "Aug":
                return 8;

            case "Sep":
                return 9;

            case "Oct":
                return 10;

            case "Nov":
                return 11;

            case "Dec":
                return 12;

            default:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('Month ($name) is not a known month');
        }
    }

    /**
     * Counts the exact year number
     * < 70 - 2000 added, >70 < 100 - 1900, others just returned
     *
     * @param  integer  $value year number
     * @return integer  Number of year
     */
    public static function getFullYear($value)
    {
        if ($value >= 0) {
            if ($value < 70) {
                $value += 2000;
            } else if ($value < 100) {
                $value += 1900;
            }
        }
        return $value;
    }

    /**
     * Sets the given date as new date or a given datepart as new datepart returning the new datepart
     * This could be for example a localized dayname, the date without time,
     * the month or only the seconds. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to set
     * @param  string                          $part    OPTIONAL Part of the date to set, if null the timestamp is set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function set($date, $part = null, $locale = null)
    {
        $zone = $this->getTimezoneFromString($date);
        $this->setTimezone($zone);

        $result = $this->_calculate('set', $date, $part, $locale);
        return $result;
    }

    /**
     * Adds a date or datepart to the existing date, by extracting $part from $date,
     * and modifying this object by adding that part.  The $part is then extracted from
     * this object and returned as an integer or numeric string (for large values, or $part's
     * corresponding to pre-defined formatted date strings).
     * This could be for example a ISO 8601 date, the hour the monthname or only the minute.
     * There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu.
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to add
     * @param  string                          $part    OPTIONAL Part of the date to add, if null the timestamp is added
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function add($date, $part = null, $locale = null)
    {
        $this->_calculate('add', $date, $part, $locale);
        $result = $this->get($part, $locale);

        return $result;
    }

    /**
     * Subtracts a date from another date.
     * This could be for example a RFC2822 date, the time,
     * the year or only the timestamp. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     * Be aware: Adding -2 Months is not equal to Subtracting 2 Months !!!
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to subtract
     * @param  string                          $part    OPTIONAL Part of the date to sub, if null the timestamp is subtracted
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function sub($date, $part = null, $locale = null)
    {
        $this->_calculate('sub', $date, $part, $locale);
        $result = $this->get($part, $locale);

        return $result;
    }

    /**
     * Compares a date or datepart with the existing one.
     * Returns -1 if earlier, 0 if equal and 1 if later.
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to compare with the date object
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is subtracted
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compare($date, $part = null, $locale = null)
    {
        $compare = $this->_calculate('cmp', $date, $part, $locale);

        if ($compare > 0) {
            return 1;
        } else if ($compare < 0) {
            return -1;
        }
        return 0;
    }

    /**
     * Returns a new instance of Zend_Date with the selected part copied.
     * To make an exact copy, use PHP's clone keyword.
     * For a complete list of supported date part values look into the docu.
     * If a date part is copied, all other date parts are set to standard values.
     * For example: If only YEAR is copied, the returned date object is equal to
     * 01-01-YEAR 00:00:00 (01-01-1970 00:00:00 is equal to timestamp 0)
     * If only HOUR is copied, the returned date object is equal to
     * 01-01-1970 HOUR:00:00 (so $this contains a timestamp equal to a timestamp of 0 plus HOUR).
     *
     * @param  string              $part    Part of the date to compare, if null the timestamp is subtracted
     * @param  string|Zend_Locale  $locale  OPTIONAL New object's locale.  No adjustments to timezone are made.
     * @return Zend_Date
     */
    public function copyPart($part, $locale = null)
    {
        $clone = clone $this;           // copy all instance variables
        $clone->setUnixTimestamp(0);    // except the timestamp
        if ($locale != null) {
            $clone->setLocale($locale); // set an other locale if selected
        }
        $clone->set($this, $part);
        return $clone;
    }

    /**
     * Internal function, returns the offset of a given timezone
     *
     * @param string $zone
     * @return integer
     */
    public function getTimezoneFromString($zone)
    {
        if (is_array($zone)) {
            return $this->getTimezone();
        }
        if ($zone instanceof Zend_Date) {
            return $zone->getTimezone();
        }
        preg_match('/([+-]\d{2}):{0,1}\d{2}/', $zone, $match);
        if (!empty($match) and ($match[count($match) - 1] <= 12) and ($match[count($match) - 1] >= -12)) {
            $zone = "Etc/GMT";
            $zone .= ($match[count($match) - 1] < 0) ? "+" : "-";
            $zone .= (int) abs($match[count($match) - 1]);
            return $zone;
        }

        preg_match('/([[:alpha:]\/]{3,30})/', $zone, $match);
        try {
            if (!empty($match) and (!is_int($match[count($match) - 1]))) {
                $oldzone = $this->getTimezone();
                $this->setTimezone($match[count($match) - 1]);
                $result = $this->getTimezone();
                $this->setTimezone($oldzone);
                if ($result !== $oldzone) {
                    return $match[count($match) - 1];
                }
            }
        } catch (Exception $e) {
            // fall through
        }

        return $this->getTimezone();
    }

    /**
     * Calculates the date or object
     *
     * @param  string                    $calc  Calculation to make
     * @param  string|integer            $date  Date for calculation
     * @param  string|integer            $comp  Second date for calculation
     * @param  boolean|integer           $dst   Use dst correction if option is set
     * @return integer|string|Zend_Date  new timestamp or Zend_Date depending on calculation
     */
    private function _assign($calc, $date, $comp = 0, $dst = false)
    {
        switch ($calc) {
            case 'set' :
                if (!empty($comp)) {
                    $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$sub, $this->getUnixTimestamp(), $comp));
                }
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$add, $this->getUnixTimestamp(), $date));
                $value = $this->getUnixTimestamp();
                break;
            case 'add' :
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$add, $this->getUnixTimestamp(), $date));
                $value = $this->getUnixTimestamp();
                break;
            case 'sub' :
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$sub, $this->getUnixTimestamp(), $date));
                $value = $this->getUnixTimestamp();
                break;
            default :
                // cmp - compare
                return call_user_func(Zend_Locale_Math::$comp, $comp, $date);
                break;
        }

        // dst-correction if 'fix_dst' = true and dst !== false but only for non UTC and non GMT
        if ((self::$_options['fix_dst'] === true) and ($dst !== false) and ($this->_dst === true)) {
            $hour = $this->get(self::HOUR);
            if ($hour != $dst) {
                if (($dst == ($hour + 1)) or ($dst == ($hour - 23))) {
                    $value += 3600;
                } else if (($dst == ($hour - 1)) or ($dst == ($hour + 23))) {
                    $value -= 3600;
                }
                $this->setUnixTimestamp($value);
            }
        }
        return $this->getUnixTimestamp();
    }


    /**
     * Calculates the date or object
     *
     * @param  string                          $calc    Calculation to make, one of: 'add'|'sub'|'cmp'|'copy'|'set'
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to calculate with
     * @param  string                          $part    Part of the date to calculate, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|string|Zend_Date        new timestamp
     * @throws Zend_Date_Exception
     */
    private function _calculate($calc, $date, $part, $locale)
    {
        if (is_null($date) === true) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        if (($part !== null) and (Zend_Locale::isLocale($part, null, false))) {
            $locale = $part;
            $part   = null;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $locale = (string) $locale;

        // Create date parts
        $year   = $this->get(self::YEAR);
        $month  = $this->get(self::MONTH_SHORT);
        $day    = $this->get(self::DAY_SHORT);
        $hour   = $this->get(self::HOUR_SHORT);
        $minute = $this->get(self::MINUTE_SHORT);
        $second = $this->get(self::SECOND_SHORT);
        // If object extract value
        if ($date instanceof Zend_Date) {
            $date = $date->get($part, $locale);
        }

        if (is_array($date) === true) {
            if (empty($part) === false) {
                switch($part) {
                    // Fall through
                    case self::DAY:
                    case self::DAY_SHORT:
                        if (isset($date['day']) === true) {
                            $date = $date['day'];
                        }
                        break;
                    // Fall through
                    case self::WEEKDAY_SHORT:
                    case self::WEEKDAY:
                    case self::WEEKDAY_8601:
                    case self::WEEKDAY_DIGIT:
                    case self::WEEKDAY_NARROW:
                    case self::WEEKDAY_NAME:
                        if (isset($date['weekday']) === true) {
                            $date = $date['weekday'];
                            $part = self::WEEKDAY_DIGIT;
                        }
                        break;
                    case self::DAY_OF_YEAR:
                        if (isset($date['day_of_year']) === true) {
                            $date = $date['day_of_year'];
                        }
                        break;
                    // Fall through
                    case self::MONTH:
                    case self::MONTH_SHORT:
                    case self::MONTH_NAME:
                    case self::MONTH_NAME_SHORT:
                    case self::MONTH_NAME_NARROW:
                        if (isset($date['month']) === true) {
                            $date = $date['month'];
                        }
                        break;
                    // Fall through
                    case self::YEAR:
                    case self::YEAR_SHORT:
                    case self::YEAR_8601:
                    case self::YEAR_SHORT_8601:
                        if (isset($date['year']) === true) {
                            $date = $date['year'];
                        }
                        break;
                    // Fall through
                    case self::HOUR:
                    case self::HOUR_AM:
                    case self::HOUR_SHORT:
                    case self::HOUR_SHORT_AM:
                        if (isset($date['hour']) === true) {
                            $date = $date['hour'];
                        }
                        break;
                    // Fall through
                    case self::MINUTE:
                    case self::MINUTE_SHORT:
                        if (isset($date['minute']) === true) {
                            $date = $date['minute'];
                        }
                        break;
                    // Fall through
                    case self::SECOND:
                    case self::SECOND_SHORT:
                        if (isset($date['second']) === true) {
                            $date = $date['second'];
                        }
                        break;
                    // Fall through
                    case self::TIMEZONE:
                    case self::TIMEZONE_NAME:
                        if (isset($date['timezone']) === true) {
                            $date = $date['timezone'];
                        }
                        break;
                    case self::TIMESTAMP:
                        if (isset($date['timestamp']) === true) {
                            $date = $date['timestamp'];
                        }
                        break;
                    case self::WEEK:
                        if (isset($date['week']) === true) {
                            $date = $date['week'];
                        }
                        break;
                    case self::TIMEZONE_SECS:
                        if (isset($date['gmtsecs']) === true) {
                            $date = $date['gmtsecs'];
                        }
                        break;
                    default:
                        require_once 'Zend/Date/Exception.php';
                        throw new Zend_Date_Exception("datepart for part ($part) not found in array");
                        break;
                }
            } else {
                $hours = 0;
                if (isset($date['hour']) === true) {
                    $hours = $date['hour'];
                }
                $minutes = 0;
                if (isset($date['minute']) === true) {
                    $minutes = $date['minute'];
                }
                $seconds = 0;
                if (isset($date['second']) === true) {
                    $seconds = $date['second'];
                }
                $months = 0;
                if (isset($date['month']) === true) {
                    $months = $date['month'];
                }
                $days = 0;
                if (isset($date['day']) === true) {
                    $days = $date['day'];
                }
                $years = 0;
                if (isset($date['year']) === true) {
                    $years = $date['year'];
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, $months, $days, $years, true),
                                             $this->mktime($hour, $minute, $second, $month, $day, $year, true), $hour);
            }
        }

        // $date as object, part of foreign date as own date
        switch($part) {

            // day formats
            case self::DAY:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + intval($day), 1970, true), $hour);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", $date);
                break;

            case self::WEEKDAY_SHORT:
                $daylist = Zend_Locale_Data::getList($locale, 'day');
                $weekday = (int) $this->get(self::WEEKDAY_DIGIT, $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper(iconv_substr($value, 0, 3, 'UTF-8')) == strtoupper($date)) {
                         $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case self::DAY_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + intval($day), 1970, true), $hour);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", $date);
                break;

            case self::WEEKDAY:
                $daylist = Zend_Locale_Data::getList($locale, 'day');
                $weekday = (int) $this->get(self::WEEKDAY_DIGIT, $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case self::WEEKDAY_8601:
                $weekday = (int) $this->get(self::WEEKDAY_8601, $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case self::DAY_SUFFIX:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('day suffix not supported', $date);
                break;

            case self::WEEKDAY_DIGIT:
                $weekday = (int) $this->get(self::WEEKDAY_DIGIT, $locale);
                if (is_numeric($date) and (intval($date) >= 0) and (intval($date) < 7)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $date, 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case self::DAY_OF_YEAR:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $date, 1970, true),
                                                 $this->mktime(0, 0, 0, $month, 1 + $day, 1970, true), $hour);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", $date);
                break;

            case self::WEEKDAY_NARROW:
                $daylist = Zend_Locale_Data::getList($locale, 'day', array('gregorian', 'format', 'abbreviated'));
                $weekday = (int) $this->get(self::WEEKDAY_DIGIT, $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper(iconv_substr($value, 0, 1, 'UTF-8')) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case self::WEEKDAY_NAME:
                $daylist = Zend_Locale_Data::getList($locale, 'day', array('gregorian', 'format', 'abbreviated'));
                $weekday = (int) $this->get(self::WEEKDAY_DIGIT, $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found, 1970, true),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, true), $hour);
                }

                // Weekday not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            // week formats
            case self::WEEK:
                if (is_numeric($date)) {
                    $week = (int) $this->get(self::WEEK, $locale);
                    return $this->_assign($calc, parent::mktime(0, 0, 0, 1, 1 + ($date * 7), 1970, true),
                                                 parent::mktime(0, 0, 0, 1, 1 + ($week * 7), 1970, true), $hour);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, week expected", $date);
                break;

            // month formats
            case self::MONTH_NAME:
                $monthlist = Zend_Locale_Data::getList($locale, 'month');
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }
                $date = array_search($date, $monthlist);

                // Monthname found
                if ($cnt < 12) {
                    $fixday = 0;
                    if ($calc == 'add') {
                        $date += $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc == 'sub') {
                        $date = $month - $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date,  $day + $fixday, $year, true),
                                                 $this->mktime(0, 0, 0, $month, $day, $year, true), $hour);
                }

                // Monthname not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case self::MONTH:
                if (is_numeric($date)) {
                    $fixday = 0;
                    if ($calc == 'add') {
                        $date += $month;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc == 'sub') {
                        $date = $month - $date;
                        $calc = 'set';
                        if (self::$_options['extend_month'] == false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date, $day + $fixday, $year, true),
                                                 $this->mktime(0, 0, 0, $month, $day, $year, true), $hour);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case self::MONTH_NAME_SHORT:
                $monthlist = Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'format', 'abbreviated'));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }
                $date = array_search($date, $monthlist);

                // Monthname found
                if ($cnt < 12) {
                    $fixday = 0;
                    if ($calc == 'add') {
                        $date += $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc == 'sub') {
                        $date = $month - $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date, $day + $fixday, $year, true),
                                                 $this->mktime(0, 0, 0, $month, $day, $year, true), $hour);
                }

                // Monthname not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case self::MONTH_SHORT:
                if (is_numeric($date) === true) {
                    $fixday = 0;
                    if ($calc === 'add') {
                        $date += $month;
                        $calc  = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc === 'sub') {
                        $date = $month - $date;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }

                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date,  $day + $fixday, $year, true),
                                                 $this->mktime(0, 0, 0, $month, $day,           $year, true), $hour);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case self::MONTH_DAYS:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('month days not supported', $date);
                break;

            case self::MONTH_NAME_NARROW:
                $monthlist = Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'stand-alone', 'narrow'));
                $cnt       = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) === strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }
                $date = array_search($date, $monthlist);

                // Monthname found
                if ($cnt < 12) {
                    $fixday = 0;
                    if ($calc === 'add') {
                        $date += $found;
                        $calc  = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    } else if ($calc === 'sub') {
                        $date = $month - $found;
                        $calc = 'set';
                        if (self::$_options['extend_month'] === false) {
                            $parts = $this->getDateParts($this->mktime(0, 0, 0, $date, $day, $year, false));
                            if ($parts['mday'] != $day) {
                                $fixday = ($parts['mday'] < $day) ? -$parts['mday'] : ($parts['mday'] - $day);
                            }
                        }
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $date,  $day + $fixday, $year, true),
                                                 $this->mktime(0, 0, 0, $month, $day,           $year, true), $hour);
                }

                // Monthname not found
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            // year formats
            case self::LEAPYEAR:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('leap year not supported', $date);
                break;

            case self::YEAR_8601:
                if (is_numeric($date)) {
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, intval($date), true),
                                                 $this->mktime(0, 0, 0, $month, $day, $year,         true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;

            case self::YEAR:
                if (is_numeric($date)) {
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, intval($date), true),
                                                 $this->mktime(0, 0, 0, $month, $day, $year,         true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;

            case self::YEAR_SHORT:
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($calc == 'set') || ($calc == 'cmp')) {
                        $date = self::getFullYear($date);
                    }
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, $date, true),
                                                 $this->mktime(0, 0, 0, $month, $day, $year, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;

            case self::YEAR_SHORT_8601:
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($calc === 'set') || ($calc === 'cmp')) {
                        $date = self::getFullYear($date);
                    }
                    if ($calc === 'add') {
                        $date += $year;
                        $calc  = 'set';
                    } else if ($calc === 'sub') {
                        $date = $year - $date;
                        $calc = 'set';
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, $month, $day, $date, true),
                                                 $this->mktime(0, 0, 0, $month, $day, $year, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;

            // time formats
            case self::MERIDIEM:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('meridiem not supported', $date);
                break;

            case self::SWATCH:
                if (is_numeric($date)) {
                    $rest    = intval($date);
                    $hours   = floor($rest * 24 / 1000);
                    $rest    = $rest - ($hours * 1000 / 24);
                    $minutes = floor($rest * 1440 / 1000);
                    $rest    = $rest - ($minutes * 1000 / 1440);
                    $seconds = floor($rest * 86400 / 1000);
                    return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1, 1, 1970, true),
                                                 $this->mktime($hour,  $minute,  $second,  1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, swatchstamp expected", $date);
                break;

            case self::HOUR_SHORT_AM:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case self::HOUR_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case self::HOUR_AM:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case self::HOUR:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, true),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case self::MINUTE:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, intval($date), 0, 1, 1, 1970, true),
                                                 $this->mktime(0, $minute,       0, 1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, minute expected", $date);
                break;

            case self::SECOND:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, intval($date), 1, 1, 1970, true),
                                                 $this->mktime(0, 0, $second,       1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, second expected", $date);
                break;

            case self::MILLISECOND:
                if (is_numeric($date)) {
                    switch($calc) {
                        case 'set' :
                            return $this->setMillisecond($date);
                            break;
                        case 'add' :
                            return $this->addMillisecond($date);
                            break;
                        case 'sub' :
                            return $this->subMillisecond($date);
                            break;
                    }
                    return $this->compareMillisecond($date);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, milliseconds expected", $date);
                break;

            case self::MINUTE_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, intval($date), 0, 1, 1, 1970, true),
                                                 $this->mktime(0, $minute,       0, 1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, minute expected", $date);
                break;

            case self::SECOND_SHORT:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, intval($date), 1, 1, 1970, true),
                                                 $this->mktime(0, 0, $second,       1, 1, 1970, true), false);
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, second expected", $date);
                break;

            // timezone formats
            // break intentionally omitted
            case self::TIMEZONE_NAME:
            case self::TIMEZONE:
            case self::TIMEZONE_SECS:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('timezone not supported', $date);
                break;

            case self::DAYLIGHT:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('daylight not supported', $date);
                break;

            case self::GMT_DIFF:
            case self::GMT_DIFF_SEP:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('gmtdiff not supported', $date);
                break;

            // date strings
            case self::ISO_8601:
                // (-)YYYY-MM-dd
                preg_match('/^(-{0,1}\d{4})-(\d{2})-(\d{2})/', $date, $datematch);
                // (-)YY-MM-dd
                if (empty($datematch)) {
                    preg_match('/^(-{0,1}\d{2})-(\d{2})-(\d{2})/', $date, $datematch);
                }
                // (-)YYYYMMdd
                if (empty($datematch)) {
                    preg_match('/^(-{0,1}\d{4})(\d{2})(\d{2})/', $date, $datematch);
                }
                // (-)YYMMdd
                if (empty($datematch)) {
                    preg_match('/^(-{0,1}\d{2})(\d{2})(\d{2})/', $date, $datematch);
                }
                $tmpdate = $date;
                if (!empty($datematch)) {
                    $dateMatchCharCount = iconv_strlen($datematch[0], 'UTF-8');
                    $tmpdate = iconv_substr($date,
                                            $dateMatchCharCount,
                                            iconv_strlen($date, 'UTF-8') - $dateMatchCharCount,
                                            'UTF-8');
                }
                // (T)hh:mm:ss
                preg_match('/[T,\s]{0,1}(\d{2}):(\d{2}):(\d{2})/', $tmpdate, $timematch);
                if (empty($timematch)) {
                    preg_match('/[T,\s]{0,1}(\d{2})(\d{2})(\d{2})/', $tmpdate, $timematch);
                }
                if (empty($datematch) and empty($timematch)) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("unsupported ISO8601 format ($date)", $date);
                }
                if (!empty($timematch)) {
                    $timeMatchCharCount = iconv_strlen($timematch[0], 'UTF-8');
                    $tmpdate = iconv_substr($tmpdate,
                                            $timeMatchCharCount,
                                            iconv_strlen($tmpdate, 'UTF-8') - $timeMatchCharCount,
                                            'UTF-8');
                }
                if (empty($datematch)) {
                    $datematch[1] = 1970;
                    $datematch[2] = 1;
                    $datematch[3] = 1;
                } else if (iconv_strlen($datematch[1], 'UTF-8') == 2) {
                    $datematch[1] = self::getFullYear($datematch[1]);
                }
                if (empty($timematch)) {
                    $timematch[1] = 0;
                    $timematch[2] = 0;
                    $timematch[3] = 0;
                }

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$datematch[2];
                    --$month;
                    --$datematch[3];
                    --$day;
                    $datematch[1] -= 1970;
                    $year         -= 1970;
                }
                return $this->_assign($calc, $this->mktime($timematch[1], $timematch[2], $timematch[3], 1 + $datematch[2], 1 + $datematch[3], 1970 + $datematch[1], false),
                                             $this->mktime($hour,         $minute,       $second,       1 + $month,        1 + $day,          1970 + $year,         false), false);
                break;

            case self::RFC_2822:
                $result = preg_match('/^\w{3},\s(\d{1,2})\s(\w{3})\s(\d{4})\s(\d{2}):(\d{2}):{0,1}(\d{0,2})\s([+-]{1}\d{4})$/', $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no RFC 2822 format ($date)", $date);
                }

                $months  = $this->_getDigitFromName($match[2]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], false),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     false), false);
                break;

            case self::TIMESTAMP:
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->getUnixTimestamp());
                }
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("invalid date ($date) operand, timestamp expected", $date);
                break;

            // additional formats
            // break intentionally omitted
            case self::ERA:
            case self::ERA_NAME:
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception('era not supported', $date);
                break;

            case self::DATES:
                try {
                    $parsed = Zend_Locale_Format::getDate($date, array('locale' => $locale, 'format_type' => 'iso', 'fix_date' => true));

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                                                 $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::DATE_FULL:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                                                 $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::DATE_LONG:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')){
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                                                 $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::DATE_MEDIUM:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                                                 $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::DATE_SHORT:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'date', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));

                    $parsed['year'] = self::getFullYear($parsed['year']);

                    if (($calc == 'set') || ($calc == 'cmp')) {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'], true),
                                                 $this->mktime(0, 0, 0, 1 + $month,           1 + $day,           1970 + $year,           true), $hour);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::TIMES:
                try {
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    $parsed = Zend_Locale_Format::getTime($date, array('locale' => $locale, 'format_type' => 'iso', 'fix_date' => true));
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                                                 $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::TIME_FULL:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], 0,       $month, $day, $year, true),
                                                 $this->mktime($hour,           $minute,           $second, $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::TIME_LONG:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                                                 $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::TIME_MEDIUM:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $month, $day, $year, true),
                                                 $this->mktime($hour,           $minute,           $second,           $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case self::TIME_SHORT:
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'time', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getTime($date, array('date_format' => $format, 'format_type' => 'iso', 'locale' => $locale));
                    if ($calc != 'set') {
                        $month = 1;
                        $day   = 1;
                        $year  = 1970;
                    }
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], 0,       $month, $day, $year, true),
                                                 $this->mktime($hour,           $minute,           $second, $month, $day, $year, true), false);
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            // ATOM and RFC_3339 are identical
            case self::ATOM:
            case self::RFC_3339:
                $result = preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})\d{0,4}([+-]{1}\d{2}:\d{2}|Z)$/', $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, ATOM format expected", $date);
                }

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$match[2];
                    --$month;
                    --$match[3];
                    --$day;
                    $match[1] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $match[2], 1 + $match[3], 1970 + $match[1], true),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,    1 + $day,      1970 + $year,     true), false);
                break;

            case self::COOKIE:
                $result = preg_match("/^\w{6,9},\s(\d{2})-(\w{3})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})\s.{3,20}$/", $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, COOKIE format expected", $date);
                }
                $matchStartPos = iconv_strpos($match[0], ' ', 0, 'UTF-8') + 1;
                $match[0] = iconv_substr($match[0],
                                         $matchStartPos,
                                         iconv_strlen($match[0], 'UTF-8') - $matchStartPos,
                                         'UTF-8');

                $months    = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::RFC_822:
            case self::RFC_1036:
                // new RFC 822 format, identical to RFC 1036 standard
                $result = preg_match('/^\w{0,3},{0,1}\s{0,1}(\d{1,2})\s(\w{3})\s(\d{2})\s(\d{2}):(\d{2}):{0,1}(\d{0,2})\s([+-]{1}\d{4}|\w{1,20})$/', $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 822 date format expected", $date);
                }

                $months    = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], false),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     false), false);
                break;

            case self::RFC_850:
                $result = preg_match('/^\w{6,9},\s(\d{2})-(\w{3})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})\s.{3,21}$/', $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 850 date format expected", $date);
                }

                $months    = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::RFC_1123:
                $result = preg_match('/^\w{0,3},{0,1}\s{0,1}(\d{1,2})\s(\w{3})\s(\d{2,4})\s(\d{2}):(\d{2}):{0,1}(\d{0,2})\s([+-]{1}\d{4}|\w{1,20})$/', $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 1123 date format expected", $date);
                }

                $months  = $this->_getDigitFromName($match[2]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::RSS:
                $result = preg_match('/^\w{3},\s(\d{2})\s(\w{3})\s(\d{2,4})\s(\d{1,2}):(\d{2}):(\d{2})\s.{1,21}$/', $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, RSS date format expected", $date);
                }

                $months  = $this->_getDigitFromName($match[2]);
                $match[3] = self::getFullYear($match[3]);

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$months;
                    --$month;
                    --$match[1];
                    --$day;
                    $match[3] -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $months, 1 + $match[1], 1970 + $match[3], true),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,  1 + $day,      1970 + $year,     true), false);
                break;

            case self::W3C:
                $result = preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})[+-]{1}\d{2}:\d{2}$/', $date, $match);
                if (!$result) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("invalid date ($date) operand, W3C date format expected", $date);
                }

                if (($calc == 'set') || ($calc == 'cmp')) {
                    --$match[2];
                    --$month;
                    --$match[3];
                    --$day;
                    $match[1] -= 1970;
                    $year     -= 1970;
                }
                return $this->_assign($calc, $this->mktime($match[4], $match[5], $match[6], 1 + $match[2], 1 + $match[3], 1970 + $match[1], true),
                                             $this->mktime($hour,     $minute,   $second,   1 + $month,    1 + $day,      1970 + $year,     true), false);
                break;

            default:
                if (!is_numeric($date) || !empty($part)) {
                    try {
                        if (self::$_options['format_type'] == 'php') {
                            $part = Zend_Locale_Format::convertPhpToIsoFormat($part);
                        }
                        if (empty($part)) {
                            $part  = Zend_Locale_Format::getDateFormat($locale) . " ";
                            $part .= Zend_Locale_Format::getTimeFormat($locale);
                        }
                        $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $part, 'locale' => $locale, 'fix_date' => true, 'format_type' => 'iso'));
                        if ((strpos(strtoupper($part), 'YY') !== false) and (strpos(strtoupper($part), 'YYYY') === false)) {
                            $parsed['year'] = self::getFullYear($parsed['year']);
                        }
                        if (($calc == 'set') || ($calc == 'cmp')) {
                            if (isset($parsed['month'])) {
                                --$parsed['month'];
                            } else {
                                $parsed['month'] = 0;
                            }
                            if (isset($parsed['day'])) {
                                --$parsed['day'];
                            } else {
                                $parsed['day'] = 0;
                            }
                            if (isset($parsed['year'])) {
                                $parsed['year'] -= 1970;
                            } else {
                                $parsed['year'] = 0;
                            }
                        }
                        return $this->_assign($calc, $this->mktime(
                            isset($parsed['hour']) ? $parsed['hour'] : 0,
                            isset($parsed['minute']) ? $parsed['minute'] : 0,
                            isset($parsed['second']) ? $parsed['second'] : 0,
                            1 + $parsed['month'], 1 + $parsed['day'], 1970 + $parsed['year'],
                            false), $this->getUnixTimestamp(), false);
                    } catch (Zend_Locale_Exception $e) {
                        if (!is_numeric($date)) {
                            require_once 'Zend/Date/Exception.php';
                            throw new Zend_Date_Exception($e->getMessage(), $date);
                        }
                    }
                }
                return $this->_assign($calc, $date, $this->getUnixTimestamp(), false);
                break;
        }
    }

    /**
     * Returns true when both date objects or date parts are equal.
     * For example:
     * 15.May.2000 <-> 15.June.2000 Equals only for Day or Year... all other will return false
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to equal with
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function equals($date, $part = null, $locale = null)
    {
        $result = $this->compare($date, $part, $locale);

        if ($result == 0) {
            return true;
        }
        return false;
    }

    /**
     * Returns if the given date or datepart is earlier
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for day, year and date, but not for month
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to compare with
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isEarlier($date, $part = null, $locale = null)
    {
        $result = $this->compare($date, $part, $locale);

        if ($result == -1) {
            return true;
        }
        return false;
    }

    /**
     * Returns if the given date or datepart is later
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for month but false for day, year and date
     * Returns if the given date is later
     *
     * @param  string|integer|array|Zend_Date  $date    Date or datepart to compare with
     * @param  string                          $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isLater($date, $part = null, $locale = null)
    {
        $result = $this->compare($date, $part, $locale);

        if ($result == 1) {
            return true;
        }
        return false;
    }

    /**
     * Returns only the time of the date as new Zend_Date object
     * For example:
     * 15.May.2000 10:11:23 will return a dateobject equal to 01.Jan.1970 10:11:23
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getTime($locale = null)
    {
        return $this->copyPart(self::TIME_MEDIUM, $locale);
    }

    /**
     * Returns the calculated time
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $time    Time to calculate with, if null the actual time is taken
     * @param  string                          $format  Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    private function _time($calc, $time, $format, $locale)
    {
        if (is_null($time)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $time must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($time instanceof Zend_Date) {
            // extract time from object
            $time = $time->get(self::TIME_MEDIUM, $locale);
        } else {
            if (is_array($time)) {
                if ((isset($time['hour']) === true) or (isset($time['minute']) === true) or
                    (isset($time['second']) === true)) {
                    $parsed = $time;
                } else {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no hour, minute or second given in array");
                }
            } else {
                if (self::$_options['format_type'] == 'php') {
                    $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
                }
                try {
                    $parsed = Zend_Locale_Format::getTime($time, array('date_format' => $format, 'locale' => $locale, 'format_type' => 'iso'));
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage());
                }
            }
            $time = new self(0, self::TIMESTAMP, $locale);
            $time->setTimezone('UTC');
            $time->set($parsed['hour'],   self::HOUR);
            $time->set($parsed['minute'], self::MINUTE);
            $time->set($parsed['second'], self::SECOND);
            $time = $time->get(self::TIME_MEDIUM, $locale);
        }

        $return = $this->_calcdetail($calc, $time, self::TIME_MEDIUM, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new time for the date object. Format defines how to parse the time string.
     * Also a complete date can be given, but only the time is used for setting.
     * For example: dd.MMMM.yyTHH:mm' and 'ss sec'-> 10.May.07T25:11 and 44 sec => 1h11min44sec + 1 day
     * Returned is the new date object and the existing date is left as it was before
     *
     * @param  string|integer|array|Zend_Date  $time    Time to set
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function setTime($time, $format = null, $locale = null)
    {
        return $this->_time('set', $time, $format, $locale);
    }


    /**
     * Adds a time to the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> +10 hours
     *
     * @param  string|integer|array|Zend_Date  $time    Time to add
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function addTime($time, $format = null, $locale = null)
    {
        return $this->_time('add', $time, $format, $locale);
    }


    /**
     * Subtracts a time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> -10 hours
     *
     * @param  string|integer|array|Zend_Date  $time    Time to sub
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function subTime($time, $format = null, $locale = null)
    {
        return $this->_time('sub', $time, $format, $locale);
    }


    /**
     * Compares the time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to default.
     * If no format us given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> 10 hours
     *
     * @param  string|integer|array|Zend_Date  $time    Time to compare
     * @param  string                          $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTime($time, $format = null, $locale = null)
    {
        return $this->_time('cmp', $time, $format, $locale);
    }

    /**
     * Returns a clone of $this, with the time part set to 00:00:00.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDate($locale = null)
    {
        $date = $this->copyPart(self::DATE_FULL, $locale);
        $date->addTimestamp($this->getGmtOffset());
        return $date;
    }

    /**
     * Returns the calculated date
     *
     * @param  string                          $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $date    Date to calculate with, if null the actual date is taken
     * @param  string                          $format  Date format for parsing
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _date($calc, $date, $format, $locale)
    {
        if (is_null($date)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($date instanceof Zend_Date) {
            // extract date from object
            $date = $date->get(self::DATE_FULL, $locale);
        } else {
            if (is_array($date)) {
                if ((isset($time['year']) === true) or (isset($time['month']) === true) or
                    (isset($time['day']) === true)) {
                    $parsed = $time;
                } else {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no day,month or year given in array");
                }
            } else {
                if (self::$_options['format_type'] == 'php') {
                    $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
                }
                try {
                    $parsed = Zend_Locale_Format::getDate($date, array('date_format' => $format, 'locale' => $locale, 'format_type' => 'iso'));
                    if ((strpos(strtoupper($format), 'YY') !== false) and (strpos(strtoupper($format), 'YYYY') === false)) {
                        $parsed['year'] = self::getFullYear($parsed['year']);
                    }
                } catch (Zend_Locale_Exception $e) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception($e->getMessage());
                }
            }
            $date = new self(0, self::TIMESTAMP, $locale);
            $date->setTimezone('UTC');
            $date->set($parsed['year'], self::YEAR);
            $date->set($parsed['month'], self::MONTH);
            $date->set($parsed['day'], self::DAY);
            $date = $date->get(self::DATE_FULL, $locale);
        }

        $return = $this->_calcdetail($calc, $date, self::DATE_FULL, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new date for the date object. Format defines how to parse the date string.
     * Also a complete date with time can be given, but only the date is used for setting.
     * For example: MMMM.yy HH:mm-> May.07 22:11 => 01.May.07 00:00
     * Returned is the new date object and the existing time is left as it was before
     *
     * @param  string|integer|array|Zend_Date  $date    Date to set
     * @param  string                          $format  OPTIONAL Date format for parsing
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setDate($date, $format = null, $locale = null)
    {
        return $this->_date('set', $date, $format, $locale);
    }


    /**
     * Adds a date to the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> +10 months
     *
     * @param  string|integer|array|Zend_Date  $date    Date to add
     * @param  string                          $format  OPTIONAL Date format for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addDate($date, $format = null, $locale = null)
    {
        return $this->_date('add', $date, $format, $locale);
    }


    /**
     * Subtracts a date from the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> -10 months
     * Be aware: Subtracting 2 months is not equal to Adding -2 months !!!
     *
     * @param  string|integer|array|Zend_Date  $date    Date to sub
     * @param  string                          $format  OPTIONAL Date format for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subDate($date, $format = null, $locale = null)
    {
        return $this->_date('sub', $date, $format, $locale);
    }


    /**
     * Compares the date from the existing date object, ignoring the time.
     * Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: 10.01.2000 => 10.02.1999 -> false
     *
     * @param  string|integer|array|Zend_Date  $date    Date to compare
     * @param  string                          $format  OPTIONAL Date format for parsing input
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function compareDate($date, $format = null, $locale = null)
    {
        return $this->_date('cmp', $date, $format, $locale);
    }


    /**
     * Returns the full ISO 8601 date from the date object.
     * Always the complete ISO 8601 specifiction is used. If an other ISO date is needed
     * (ISO 8601 defines several formats) use toString() instead.
     * This function does not return the ISO date as object. Use copy() instead.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function getIso($locale = null)
    {
        return $this->get(self::ISO_8601, $locale);
    }


    /**
     * Sets a new date for the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> 01.Sept.2005 00:00:00, 20050201T10:00:30 -> 01.Feb.2005 10h00m30s
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setIso($date, $locale = null)
    {
        return $this->_calcvalue('set', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Adds a ISO date to the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> + 01.Sept.2005 00:00:00, 10:00:00 -> +10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addIso($date, $locale = null)
    {
        return $this->_calcvalue('add', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Subtracts a ISO date from the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subIso($date, $locale = null)
    {
        return $this->_calcvalue('sub', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Compares a ISO date with the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareIso($date, $locale = null)
    {
        return $this->_calcvalue('cmp', $date, 'iso', self::ISO_8601, $locale);
    }


    /**
     * Returns a RFC 822 compilant datestring from the date object.
     * This function does not return the RFC date as object. Use copy() instead.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function getArpa($locale = null)
    {
        return $this->get(self::RFC_822, $locale);
    }


    /**
     * Sets a RFC 822 date as new date for the date object.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setArpa($date, $locale = null)
    {
        return $this->_calcvalue('set', $date, 'arpa', self::RFC_822, $locale);
    }


    /**
     * Adds a RFC 822 date to the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addArpa($date, $locale = null)
    {
        return $this->_calcvalue('add', $date, 'arpa', self::RFC_822, $locale);
    }


    /**
     * Subtracts a RFC 822 date from the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subArpa($date, $locale = null)
    {
        return $this->_calcvalue('sub', $date, 'arpa', self::RFC_822, $locale);
    }


    /**
     * Compares a RFC 822 compilant date with the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareArpa($date, $locale = null)
    {
        return $this->_calcvalue('cmp', $date, 'arpa', self::RFC_822, $locale);
    }


    /**
     * Check if location is supported
     *
     * @param $location array - locations array
     * @return $horizon float
     */
    private function _checkLocation($location)
    {
        if (!isset($location['longitude']) or !isset($location['latitude'])) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('Location must include \'longitude\' and \'latitude\'', $location);
        }
        if (($location['longitude'] > 180) or ($location['longitude'] < -180)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('Longitude must be between -180 and 180', $location);
        }
        if (($location['latitude'] > 90) or ($location['latitude'] < -90)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('Latitude must be between -90 and 90', $location);
        }

        if (!isset($location['horizon'])){
            $location['horizon'] = 'effective';
        }

        switch ($location['horizon']) {
            case 'civil' :
                return -0.104528;
                break;
            case 'nautic' :
                return -0.207912;
                break;
            case 'astronomic' :
                return -0.309017;
                break;
            default :
                return -0.0145439;
                break;
        }
    }


    /**
     * Returns the time of sunrise for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of sunrise
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunrise($location)
    {
        $horizon = $this->_checkLocation($location);
        $result = clone $this;
        $result->set($this->calcSun($location, $horizon, true), self::TIMESTAMP);
        return $result;
    }


    /**
     * Returns the time of sunset for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of sunset
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunset($location)
    {
        $horizon = $this->_checkLocation($location);
        $result = clone $this;
        $result->set($this->calcSun($location, $horizon, false), self::TIMESTAMP);
        return $result;
    }


    /**
     * Returns an array with the sunset and sunrise dates for all horizon types
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of suninfo
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return array - [sunset|sunrise][effective|civil|nautic|astronomic]
     * @throws Zend_Date_Exception
     */
    public function getSunInfo($location)
    {
        $suninfo = array();
        for ($i = 0; $i < 4; ++$i) {
            switch ($i) {
                case 0 :
                    $location['horizon'] = 'effective';
                    break;
                case 1 :
                    $location['horizon'] = 'civil';
                    break;
                case 2 :
                    $location['horizon'] = 'nautic';
                    break;
                case 3 :
                    $location['horizon'] = 'astronomic';
                    break;
            }
            $horizon = $this->_checkLocation($location);
            $result = clone $this;
            $result->set($this->calcSun($location, $horizon, true), self::TIMESTAMP);
            $suninfo['sunrise'][$location['horizon']] = $result;
            $result = clone $this;
            $result->set($this->calcSun($location, $horizon, false), self::TIMESTAMP);
            $suninfo['sunset'][$location['horizon']]  = $result;
        }
        return $suninfo;
    }


    /**
     * Check a given year for leap year.
     *
     * @param  integer|array|Zend_Date  $year  Year to check
     * @return boolean
     */
    public static function checkLeapYear($year)
    {
        if ($year instanceof Zend_Date) {
            $year = (int) $year->get(self::YEAR);
        }
        if (is_array($year)) {
            if (isset($year['year']) === true) {
                $year = $year['year'];
            } else {
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("no year given in array");
            }
        }
        if (!is_numeric($year)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("year ($year) has to be integer for checkLeapYear()", $year);
        }

        return (bool) parent::isYearLeapYear($year);
    }


    /**
     * Returns true, if the year is a leap year.
     *
     * @return boolean
     */
    public function isLeapYear()
    {
        return self::checkLeapYear($this);
    }


    /**
     * Returns if the set date is todays date
     *
     * @return boolean
     */
    public function isToday()
    {
        $today = $this->date('Ymd', $this->_getTime());
        $day   = $this->date('Ymd', $this->getUnixTimestamp());
        return ($today == $day);
    }


    /**
     * Returns if the set date is yesterdays date
     *
     * @return boolean
     */
    public function isYesterday()
    {
        list($year, $month, $day) = explode('-', $this->date('Y-m-d', $this->_getTime()));
        // adjusts for leap days and DST changes that are timezone specific
        $yesterday = $this->date('Ymd', $this->mktime(0, 0, 0, $month, $day -1, $year));
        $day   = $this->date('Ymd', $this->getUnixTimestamp());
        return $day == $yesterday;
    }


    /**
     * Returns if the set date is tomorrows date
     *
     * @return boolean
     */
    public function isTomorrow()
    {
        list($year, $month, $day) = explode('-', $this->date('Y-m-d', $this->_getTime()));
        // adjusts for leap days and DST changes that are timezone specific
        $tomorrow = $this->date('Ymd', $this->mktime(0, 0, 0, $month, $day +1, $year));
        $day   = $this->date('Ymd', $this->getUnixTimestamp());
        return $day == $tomorrow;
    }

    /**
     * Returns the actual date as new date object
     *
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public static function now($locale = null)
    {
        return new Zend_Date(time(), self::TIMESTAMP, $locale);
    }

    /**
     * Calculate date details
     *
     * @param  string                          $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $date    Date or Part to calculate
     * @param  string                          $part    Datepart for Calculation
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|string  new date
     * @throws Zend_Date_Exception
     */
    private function _calcdetail($calc, $date, $type, $locale)
    {
        switch($calc) {
            case 'set' :
                return $this->set($date, $type, $locale);
                break;
            case 'add' :
                return $this->add($date, $type, $locale);
                break;
            case 'sub' :
                return $this->sub($date, $type, $locale);
                break;
        }
        return $this->compare($date, $type, $locale);
    }

    /**
     * Internal calculation, returns the requested date type
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $value   Datevalue to calculate with, if null the actual value is taken
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _calcvalue($calc, $value, $type, $parameter, $locale)
    {
        if (is_null($value)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("parameter $type must be set, null is not allowed");
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($value instanceof Zend_Date) {
            // extract value from object
            $value = $value->get($parameter, $locale);
        } else if (!is_array($value) && !is_numeric($value) && ($type != 'iso') && ($type != 'arpa')) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid $type ($value) operand", $value);
        }

        $return = $this->_calcdetail($calc, $value, $parameter, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Returns only the year from the date object as new object.
     * For example: 10.May.2000 10:30:00 -> 01.Jan.2000 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getYear($locale = null)
    {
        return $this->copyPart(self::YEAR, $locale);
    }


    /**
     * Sets a new year
     * If the year is between 0 and 69, 2000 will be set (2000-2069)
     * If the year if between 70 and 99, 1999 will be set (1970-1999)
     * 3 or 4 digit years are set as expected. If you need to set year 0-99
     * use set() instead.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $date    Year to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setYear($year, $locale = null)
    {
        return $this->_calcvalue('set', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Adds the year to the existing date object
     * If the year is between 0 and 69, 2000 will be added (2000-2069)
     * If the year if between 70 and 99, 1999 will be added (1970-1999)
     * 3 or 4 digit years are added as expected. If you need to add years from 0-99
     * use add() instead.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $date    Year to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addYear($year, $locale = null)
    {
        return $this->_calcvalue('add', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Subs the year from the existing date object
     * If the year is between 0 and 69, 2000 will be subtracted (2000-2069)
     * If the year if between 70 and 99, 1999 will be subtracted (1970-1999)
     * 3 or 4 digit years are subtracted as expected. If you need to subtract years from 0-99
     * use sub() instead.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $date    Year to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subYear($year, $locale = null)
    {
        return $this->_calcvalue('sub', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Compares the year with the existing date object, ignoring other date parts.
     * For example: 10.03.2000 -> 15.02.2000 -> true
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $year    Year to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareYear($year, $locale = null)
    {
        return $this->_calcvalue('cmp', $year, 'year', self::YEAR, $locale);
    }


    /**
     * Returns only the month from the date object as new object.
     * For example: 10.May.2000 10:30:00 -> 01.May.1970 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getMonth($locale = null)
    {
        return $this->copyPart(self::MONTH, $locale);
    }


    /**
     * Returns the calculated month
     *
     * @param  string                          $calc    Calculation to make
     * @param  string|integer|array|Zend_Date  $month   Month to calculate with, if null the actual month is taken
     * @param  string|Zend_Locale              $locale  Locale for parsing input
     * @return integer|Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    private function _month($calc, $month, $locale)
    {
        if (is_null($month)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $month must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($month instanceof Zend_Date) {
            // extract month from object
            $found = $month->get(self::MONTH_SHORT, $locale);
        } else {
            if (is_numeric($month)) {
                $found = $month;
            } else if (is_array($month)) {
                if (isset($month['month']) === true) {
                    $month = $month['month'];
                } else {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("no month given in array");
                }
            } else {
                $monthlist  = Zend_Locale_Data::getList($locale, 'month');
                $monthlist2 = Zend_Locale_Data::getList($locale, 'month', array('gregorian', 'format', 'abbreviated'));

                $monthlist = array_merge($monthlist, $monthlist2);
                $found = 0;
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($month)) {
                        $found = $key + 1;
                        break;
                    }
                    ++$cnt;
                }
                if ($found == 0) {
                    foreach ($monthlist2 as $key => $value) {
                        if (strtoupper(iconv_substr($value, 0, 1, 'UTF-8')) == strtoupper($month)) {
                            $found = $key + 1;
                            break;
                        }
                        ++$cnt;
                    }
                }
                if ($found == 0) {
                    require_once 'Zend/Date/Exception.php';
                    throw new Zend_Date_Exception("unknown month name ($month)", $month);
                }
            }
        }
        $return = $this->_calcdetail($calc, $found, self::MONTH_SHORT, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new month
     * The month can be a number or a string. Setting months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $month   Month to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setMonth($month, $locale = null)
    {
        return $this->_month('set', $month, $locale);
    }


    /**
     * Adds months to the existing date object.
     * The month can be a number or a string. Adding months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $month   Month to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addMonth($month, $locale = null)
    {
        return $this->_month('add', $month, $locale);
    }


    /**
     * Subtracts months from the existing date object.
     * The month can be a number or a string. Subtracting months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|array|Zend_Date  $month   Month to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subMonth($month, $locale = null)
    {
        return $this->_month('sub', $month, $locale);
    }


    /**
     * Compares the month with the existing date object, ignoring other date parts.
     * For example: 10.03.2000 -> 15.03.1950 -> true
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $month   Month to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareMonth($month, $locale = null)
    {
        return $this->_month('cmp', $month, $locale);
    }


    /**
     * Returns the day as new date object
     * Example: 20.May.1986 -> 20.Jan.1970 00:00:00
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDay($locale = null)
    {
        return $this->copyPart(self::DAY_SHORT, $locale);
    }


    /**
     * Returns the calculated day
     *
     * @param $calc    string                    Type of calculation to make
     * @param $day     string|integer|Zend_Date  Day to calculate, when null the actual day is calculated
     * @param $locale  string|Zend_Locale        Locale for parsing input
     * @return Zend_Date|integer
     */
    private function _day($calc, $day, $locale)
    {
        if (is_null($day)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $day must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($day instanceof Zend_Date) {
            $day = $day->get(self::DAY_SHORT, $locale);
        }

        if (is_numeric($day)) {
            $type = self::DAY_SHORT;
        } else if (is_array($day)) {
            if (isset($day['day']) === true) {
                $day = $day['day'];
                $type = self::WEEKDAY;
            } else {
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("no day given in array");
            }
        } else {
            switch (iconv_strlen($day, 'UTF-8')) {
                case 1 :
                   $type = self::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = self::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = self::WEEKDAY_SHORT;
                    break;
                default:
                    $type = self::WEEKDAY;
                    break;
            }
        }
        $return = $this->_calcdetail($calc, $day, $type, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new day
     * The day can be a number or a string. Setting days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: setDay('Montag', 'de_AT'); will set the monday of this week as day.
     *
     * @param  string|integer|array|Zend_Date  $month   Day to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setDay($day, $locale = null)
    {
        return $this->_day('set', $day, $locale);
    }


    /**
     * Adds days to the existing date object.
     * The day can be a number or a string. Adding days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: addDay('Montag', 'de_AT'); will add the number of days until the next monday
     *
     * @param  string|integer|array|Zend_Date  $month   Day to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addDay($day, $locale = null)
    {
        return $this->_day('add', $day, $locale);
    }


    /**
     * Subtracts days from the existing date object.
     * The day can be a number or a string. Subtracting days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: subDay('Montag', 'de_AT'); will sub the number of days until the previous monday
     *
     * @param  string|integer|array|Zend_Date  $month   Day to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subDay($day, $locale = null)
    {
        return $this->_day('sub', $day, $locale);
    }


    /**
     * Compares the day with the existing date object, ignoring other date parts.
     * For example: 'Monday', 'en' -> 08.Jan.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $day     Day to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareDay($day, $locale = null)
    {
        return $this->_day('cmp', $day, $locale);
    }


    /**
     * Returns the weekday as new date object
     * Weekday is always from 1-7
     * Example: 09-Jan-2007 -> 2 = Tuesday -> 02-Jan-1970 (when 02.01.1970 is also Tuesday)
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getWeekday($locale = null)
    {
        return $this->copyPart(self::WEEKDAY, $locale);
    }


    /**
     * Returns the calculated weekday
     *
     * @param  $calc     string                          Type of calculation to make
     * @param  $weekday  string|integer|array|Zend_Date  Weekday to calculate, when null the actual weekday is calculated
     * @param  $locale   string|Zend_Locale              Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _weekday($calc, $weekday, $locale)
    {
        if (is_null($weekday)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('parameter $weekday must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($weekday instanceof Zend_Date) {
            $weekday = $weekday->get(self::WEEKDAY_8601, $locale);
        }

        if (is_numeric($weekday)) {
            $type = self::WEEKDAY_8601;
        } else if (is_array($weekday)) {
            if (isset($weekday['weekday']) === true) {
                $weekday = $weekday['weekday'];
                $type = self::WEEKDAY;
            } else {
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("no weekday given in array");
            }
        } else {
            switch(iconv_strlen($weekday, 'UTF-8')) {
                case 1:
                   $type = self::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = self::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = self::WEEKDAY_SHORT;
                    break;
                default:
                    $type = self::WEEKDAY;
                    break;
            }
        }
        $return = $this->_calcdetail($calc, $weekday, $type, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new weekday
     * The weekday can be a number or a string. If a localized weekday name is given,
     * then it will be parsed as a date in $locale (defaults to the same locale as $this).
     * Returned is the new date object.
     * Example: setWeekday(3); will set the wednesday of this week as day.
     *
     * @param  string|integer|array|Zend_Date  $month   Weekday to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setWeekday($weekday, $locale = null)
    {
        return $this->_weekday('set', $weekday, $locale);
    }


    /**
     * Adds weekdays to the existing date object.
     * The weekday can be a number or a string.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: addWeekday(3); will add the difference of days from the begining of the month until
     * wednesday.
     *
     * @param  string|integer|array|Zend_Date  $month   Weekday to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addWeekday($weekday, $locale = null)
    {
        return $this->_weekday('add', $weekday, $locale);
    }


    /**
     * Subtracts weekdays from the existing date object.
     * The weekday can be a number or a string.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: subWeekday(3); will subtract the difference of days from the begining of the month until
     * wednesday.
     *
     * @param  string|integer|array|Zend_Date  $month   Weekday to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subWeekday($weekday, $locale = null)
    {
        return $this->_weekday('sub', $weekday, $locale);
    }


    /**
     * Compares the weekday with the existing date object, ignoring other date parts.
     * For example: 'Monday', 'en' -> 08.Jan.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $weekday  Weekday to compare
     * @param  string|Zend_Locale              $locale   OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareWeekday($weekday, $locale = null)
    {
        return $this->_weekday('cmp', $weekday, $locale);
    }


    /**
     * Returns the day of year as new date object
     * Example: 02.Feb.1986 10:00:00 -> 02.Feb.1970 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDayOfYear($locale = null)
    {
        return $this->copyPart(self::DAY_OF_YEAR, $locale);
    }


    /**
     * Sets a new day of year
     * The day of year is always a number.
     * Returned is the new date object
     * Example: 04.May.2004 -> setDayOfYear(10) -> 10.Jan.2004
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('set', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Adds a day of year to the existing date object.
     * The day of year is always a number.
     * Returned is the new date object
     * Example: addDayOfYear(10); will add 10 days to the existing date object.
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('add', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Subtracts a day of year from the existing date object.
     * The day of year is always a number.
     * Returned is the new date object
     * Example: subDayOfYear(10); will subtract 10 days from the existing date object.
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('sub', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Compares the day of year with the existing date object.
     * For example: compareDayOfYear(33) -> 02.Feb.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $day     Day of Year to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareDayOfYear($day, $locale = null)
    {
        return $this->_calcvalue('cmp', $day, 'day of year', self::DAY_OF_YEAR, $locale);
    }


    /**
     * Returns the hour as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 10:00:00
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getHour($locale = null)
    {
        return $this->copyPart(self::HOUR, $locale);
    }


    /**
     * Sets a new hour
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setHour(7); -> 04.May.1993 07:07:25
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setHour($hour, $locale = null)
    {
        return $this->_calcvalue('set', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Adds hours to the existing date object.
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addHour(12); -> 05.May.1993 01:07:25
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addHour($hour, $locale = null)
    {
        return $this->_calcvalue('add', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Subtracts hours from the existing date object.
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subHour(6); -> 05.May.1993 07:07:25
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subHour($hour, $locale = null)
    {
        return $this->_calcvalue('sub', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Compares the hour with the existing date object.
     * For example: 10:30:25 -> compareHour(10) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $hour    Hour to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareHour($hour, $locale = null)
    {
        return $this->_calcvalue('cmp', $hour, 'hour', self::HOUR_SHORT, $locale);
    }


    /**
     * Returns the minute as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 00:30:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getMinute($locale = null)
    {
        return $this->copyPart(self::MINUTE, $locale);
    }


    /**
     * Sets a new minute
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setMinute(29); -> 04.May.1993 13:29:25
     *
     * @param  string|integer|array|Zend_Date  $minute  Minute to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setMinute($minute, $locale = null)
    {
        return $this->_calcvalue('set', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Adds minutes to the existing date object.
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addMinute(65); -> 04.May.1993 13:12:25
     *
     * @param  string|integer|array|Zend_Date  $minute  Minute to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addMinute($minute, $locale = null)
    {
        return $this->_calcvalue('add', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Subtracts minutes from the existing date object.
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subMinute(9); -> 04.May.1993 12:58:25
     *
     * @param  string|integer|array|Zend_Date  $minute  Minute to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subMinute($minute, $locale = null)
    {
        return $this->_calcvalue('sub', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Compares the minute with the existing date object.
     * For example: 10:30:25 -> compareMinute(30) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date  $minute  Hour to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareMinute($minute, $locale = null)
    {
        return $this->_calcvalue('cmp', $minute, 'minute', self::MINUTE_SHORT, $locale);
    }


    /**
     * Returns the second as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 00:00:25
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getSecond($locale = null)
    {
        return $this->copyPart(self::SECOND, $locale);
    }


    /**
     * Sets new seconds to the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setSecond(100); -> 04.May.1993 13:08:40
     *
     * @param  string|integer|array|Zend_Date $second Second to set
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setSecond($second, $locale = null)
    {
        return $this->_calcvalue('set', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Adds seconds to the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addSecond(65); -> 04.May.1993 13:08:30
     *
     * @param  string|integer|array|Zend_Date $second Second to add
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addSecond($second, $locale = null)
    {
        return $this->_calcvalue('add', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Subtracts seconds from the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subSecond(10); -> 04.May.1993 13:07:15
     *
     * @param  string|integer|array|Zend_Date $second Second to sub
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subSecond($second, $locale = null)
    {
        return $this->_calcvalue('sub', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Compares the second with the existing date object.
     * For example: 10:30:25 -> compareSecond(25) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|array|Zend_Date $second Second to compare
     * @param  string|Zend_Locale             $locale (Optional) Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareSecond($second, $locale = null)
    {
        return $this->_calcvalue('cmp', $second, 'second', self::SECOND_SHORT, $locale);
    }


    /**
     * Returns the precision for fractional seconds
     *
     * @return integer
     */
    public function getFractionalPrecision()
    {
        return $this->_precision;
    }


    /**
     * Sets a new precision for fractional seconds
     *
     * @param  integer $precision Precision for the fractional datepart 3 = milliseconds
     * @throws Zend_Date_Exception
     * @return void
     */
    public function setFractionalPrecision($precision)
    {
        if (!intval($precision) or ($precision < 0) or ($precision > 9)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }
        $this->_precision = (int) $precision;
    }


    /**
     * Returns the milliseconds of the date object
     *
     * @return integer
     */
    public function getMilliSecond()
    {
        return $this->_fractional;
    }


    /**
     * Sets new milliseconds for the date object
     * Example: setMilliSecond(550, 2) -> equals +5 Sec +50 MilliSec
     *
     * @param  integer|Zend_Date $milli     (Optional) Millisecond to set, when null the actual millisecond is set
     * @param  integer           $precision (Optional) Fraction precision of the given milliseconds
     * @return integer|string
     */
    public function setMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
            $precision = 6;
        } else if (!is_numeric($milli)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", $milli);
        }

        if ($precision === null) {
            $precision = $this->_precision;
        } else if (!is_int($precision) || $precision < 1 || $precision > 9) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }

        $this->_fractional = 0;
        $this->addMilliSecond($milli, $precision);
        return $this->_fractional;
    }


    /**
     * Adds milliseconds to the date object
     *
     * @param  integer|Zend_Date $milli     (Optional) Millisecond to add, when null the actual millisecond is added
     * @param  integer           $precision (Optional) Fractional precision for the given milliseconds
     * @return integer|string
     */
    public function addMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
        } else if (!is_numeric($milli)) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", $milli);
        }

        if ($precision === null) {
            $precision = $this->_precision;
        } else if (!is_int($precision) || $precision < 1 || $precision > 9) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }

        if ($precision != $this->_precision) {
            if ($precision > $this->_precision) {
                $diff = $precision - $this->_precision;
                $milli = (int) ($milli / (10 * $diff));
            } else {
                $diff = $this->_precision - $precision;
                $milli = (int) ($milli * (10 * $diff));
            }
        }

        $this->_fractional += $milli;
        // Add/sub milliseconds + add/sub seconds

        $max = pow(10, $this->_precision);
        // Milli includes seconds
        if ($this->_fractional >= $max) {
            while ($this->_fractional >= $max) {
                $this->addSecond(1);
                $this->_fractional -= $max;
            }
        }

        if ($this->_fractional < 0) {
            while ($this->_fractional < 0) {
                $this->subSecond(1);
                $this->_fractional += $max;
            }
        }

        return $this->_fractional;
    }


    /**
     * Subtracts a millisecond
     *
     * @param  integer|Zend_Date $milli     (Optional) Millisecond to sub, when null the actual millisecond is subtracted
     * @param  integer           $precision (Optional) Fractional precision for the given milliseconds
     * @return integer
     */
    public function subMilliSecond($milli = null, $precision = null)
    {
        return $this->addMilliSecond(0 - $milli);
    }

    /**
     * Compares only the millisecond part, returning the difference
     *
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to compare, when null the actual millisecond is compared
     * @param  integer            $precision  OPTIONAL Fractional precision for the given milliseconds
     * @return integer
     */
    public function compareMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
        } else if (is_numeric($milli) === false) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", $milli);
        }

        if ($precision === null) {
            $precision = $this->_precision;
        } else if (!is_int($precision) || $precision < 1 || $precision > 9) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }

        if ($precision === 0) {
            require_once 'Zend/Date/Exception.php';
            throw new Zend_Date_Exception('precision is 0');
        }

        if ($precision != $this->_precision) {
            if ($precision > $this->_precision) {
                $diff = $precision - $this->_precision;
                $milli = (int) ($milli / (10 * $diff));
            } else {
                $diff = $this->_precision - $precision;
                $milli = (int) ($milli * (10 * $diff));
            }
        }

        $comp = $this->_fractional - $milli;
        if ($comp < 0) {
            return -1;
        } else if ($comp > 0) {
            return 1;
        }
        return 0;
    }

    /**
     * Returns the week as new date object using monday as begining of the week
     * Example: 12.Jan.2007 -> 08.Jan.1970 00:00:00
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getWeek($locale = null)
    {
        return $this->copyPart(self::WEEK, $locale);
    }

    /**
     * Sets a new week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> setWeek(1); -> 02.Jan.2007 13:07:25
     *
     * @param  string|integer|array|Zend_Date  $week    Week to set
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function setWeek($week, $locale = null)
    {
        return $this->_calcvalue('set', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Adds a week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> addWeek(1); -> 16.Jan.2007 13:07:25
     *
     * @param  string|integer|array|Zend_Date  $week    Week to add
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function addWeek($week, $locale = null)
    {
        return $this->_calcvalue('add', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Subtracts a week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> subWeek(1); -> 02.Jan.2007 13:07:25
     *
     * @param  string|integer|array|Zend_Date  $week    Week to sub
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function subWeek($week, $locale = null)
    {
        return $this->_calcvalue('sub', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Compares only the week part, returning the difference
     * Returned is the new date object
     * Returns if equal, earlier or later
     * Example: 09.Jan.2007 13:07:25 -> compareWeek(2); -> 0
     *
     * @param  string|integer|array|Zend_Date  $week    Week to compare
     * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return integer 0 = equal, 1 = later, -1 = earlier
     */
    public function compareWeek($week, $locale = null)
    {
        return $this->_calcvalue('cmp', $week, 'week', self::WEEK, $locale);
    }

    /**
     * Sets a new standard locale for the date object.
     * This locale will be used for all functions
     * Returned is the really set locale.
     * Example: 'de_XX' will be set to 'de' because 'de_XX' does not exist
     * 'xx_YY' will be set to 'root' because 'xx' does not exist
     *
     * @param  string|Zend_Locale $locale (Optional) Locale for parsing input
     * @throws Zend_Date_Exception When the given locale does not exist
     * @return Zend_Date Provides fluent interface
     */
    public function setLocale($locale = null)
    {
        if (!Zend_Locale::isLocale($locale, true, false)) {
            if (!Zend_Locale::isLocale($locale, false, false)) {
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("Given locale ({$locale}) does not exist", (string) $locale);
            }

            $locale = new Zend_Locale($locale);
        }

        $this->_locale = (string) $locale;
        return $this;
    }

    /**
     * Returns the actual set locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Checks if the given date is a real date or datepart.
     * Returns false if a expected datepart is missing or a datepart exceeds its possible border.
     * But the check will only be done for the expected dateparts which are given by format.
     * If no format is given the standard dateformat for the actual locale is used.
     * f.e. 30.February.2007 will return false if format is 'dd.MMMM.YYYY'
     *
     * @param  string             $date   Date to parse for correctness
     * @param  string             $format (Optional) Format for parsing the date string
     * @param  string|Zend_Locale $locale (Optional) Locale for parsing date parts
     * @return boolean            True when all date parts are correct
     */
    public static function isDate($date, $format = null, $locale = null)
    {
        if (!is_string($date) and !is_numeric($date) and !($date instanceof Zend_Date)) {
            return false;
        }

        if (($format !== null) and (Zend_Locale::isLocale($format, null, false))) {
            $locale = $format;
            $format = null;
        }

        if (empty($locale)) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Locale') === true) {
                $locale = Zend_Registry::get('Zend_Locale');
            }
        }

        if (!Zend_Locale::isLocale($locale, true, false)) {
            if (!Zend_Locale::isLocale($locale, false, false)) {
                require_once 'Zend/Date/Exception.php';
                throw new Zend_Date_Exception("Given locale ({$locale}) does not exist", (string) $locale);
            }

            $locale = new Zend_Locale($locale);
        }

        $locale = (string) $locale;
        if ($format === null) {
            $format = Zend_Locale_Format::getDateFormat($locale);
        } else if (self::$_options['format_type'] == 'php') {
            $format = Zend_Locale_Format::convertPhpToIsoFormat($format);
        }

        try {
            $parsed = Zend_Locale_Format::getDate($date, array('locale' => $locale,
                                                  'date_format' => $format, 'format_type' => 'iso',
                                                  'fix_date' => false));

            if (isset($parsed['year']) and ((strpos(strtoupper($format), 'YY') !== false) and
                (strpos(strtoupper($format), 'YYYY') === false))) {
                $parsed['year'] = self::getFullYear($parsed['year']);
            }
        } catch (Zend_Locale_Exception $e) {
            // Date can not be parsed
            return false;
        }

        if (((strpos($format, 'Y') !== false) or (strpos($format, 'y') !== false)) and
            (!isset($parsed['year']))) {
            // Year expected but not found
            return false;
        }

        if ((strpos($format, 'M') !== false) and (!isset($parsed['month']))) {
            // Month expected but not found
            return false;
        }

        if ((strpos($format, 'd') !== false) and (!isset($parsed['day']))) {
            // Day expected but not found
            return false;
        }

        if (((strpos($format, 'H') !== false) or (strpos($format, 'h') !== false)) and
            (!isset($parsed['hour']))) {
            // Hour expected but not found
            return false;
        }

        if ((strpos($format, 'm') !== false) and (!isset($parsed['minute']))) {
            // Minute expected but not found
            return false;
        }

        if ((strpos($format, 's') !== false) and (!isset($parsed['second']))) {
            // Second expected  but not found
            return false;
        }

        // Set not given dateparts
        if (isset($parsed['hour']) === false) {
            $parsed['hour'] = 0;
        }

        if (isset($parsed['minute']) === false) {
            $parsed['minute'] = 0;
        }

        if (isset($parsed['second']) === false) {
            $parsed['second'] = 0;
        }

        if (isset($parsed['month']) === false) {
            $parsed['month'] = 1;
        }

        if (isset($parsed['day']) === false) {
            $parsed['day'] = 1;
        }

        if (isset($parsed['year']) === false) {
            $parsed['year'] = 1970;
        }

        $date      = new self($parsed, null, $locale);
        $timestamp = $date->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                   $parsed['month'], $parsed['day'], $parsed['year']);
        if ($parsed['year'] != $date->date('Y', $timestamp)) {
            // Given year differs from parsed year
            return false;
        }

        if ($parsed['month'] != $date->date('n', $timestamp)) {
            // Given month differs from parsed month
            return false;
        }

        if ($parsed['day'] != $date->date('j', $timestamp)) {
            // Given day differs from parsed day
            return false;
        }

        if ($parsed['hour'] != $date->date('G', $timestamp)) {
            // Given hour differs from parsed hour
            return false;
        }

        if ($parsed['minute'] != $date->date('i', $timestamp)) {
            // Given minute differs from parsed minute
            return false;
        }

        if ($parsed['second'] != $date->date('s', $timestamp)) {
            // Given second differs from parsed second
            return false;
        }

        return true;
    }

}
