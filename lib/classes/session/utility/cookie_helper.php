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

namespace core\session\utility;

/**
 * Helper class providing utils dealing with cookies, particularly 3rd party cookies.
 *
 * This class primarily provides a means to augment outbound cookie headers, in order to satisfy browser-specific
 * requirements for setting 3rd party cookies.
 *
 * @package    core
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cookie_helper {

    /**
     * Make sure the given attributes are set on the Set-Cookie response header identified by name=$cookiename.
     *
     * This function only affects Set-Cookie headers and modifies the headers directly with the required changes, if any.
     *
     * @param string $cookiename the cookie name.
     * @param array $attributes the attributes to set/ensure are set.
     * @return void
     */
    public static function add_attributes_to_cookie_response_header(string $cookiename, array $attributes): void {

        $setcookieheaders = array_filter(headers_list(), function($val) {
            return preg_match("/Set-Cookie:/i", $val);
        });
        if (empty($setcookieheaders)) {
            return;
        }

        $updatedheaders = self::cookie_response_headers_add_attributes($setcookieheaders, [$cookiename], $attributes);

        // Note: The header_remove() method is quite crude and removes all headers of that header name.
        header_remove('Set-Cookie');
        foreach ($updatedheaders as $header) {
            header($header, false);
        }
    }

    /**
     * Given a list of HTTP header strings, return a list of HTTP header strings where the matched 'Set-Cookie' headers
     * have been updated with the attributes defined in $attribute - an array of strings.
     *
     * This method does not verify whether a given attribute is valid or not. It blindly sets it and returns the header
     * strings. It's up to calling code to determine whether an attribute makes sense or not.
     *
     * @param array $headerstrings the array of header strings.
     * @param array $cookiestomatch the array of cookie names to match.
     * @param array $attributes the attributes to set on each matched 'Set-Cookie' header.
     * @param bool $casesensitive whether to match the attribute in a case-sensitive way.
     * @return array the updated array of header strings.
     */
    public static function cookie_response_headers_add_attributes(array $headerstrings, array $cookiestomatch, array $attributes,
            bool $casesensitive = false): array {

        return array_map(function($headerstring) use ($attributes, $casesensitive, $cookiestomatch) {
            if (!self::cookie_response_header_matches_names($headerstring, $cookiestomatch)) {
                return $headerstring;
            }
            foreach ($attributes as $attribute) {
                if (!self::cookie_response_header_contains_attribute($headerstring, $attribute, $casesensitive)) {
                    $headerstring = self::cookie_response_header_append_attribute($headerstring, $attribute);
                }
            }
            return $headerstring;
        }, $headerstrings);
    }

    /**
     * Forces the expiry of the MoodleSession cookie.
     *
     * This is useful to force a new Set-Cookie header on the next redirect.
     *
     * @return void
     */
    public static function expire_moodlesession(): void {
        global $CFG;

        $setcookieheader = array_filter(headers_list(), function($val) use ($CFG) {
            return self::cookie_response_header_matches_name($val, 'MoodleSession'.$CFG->sessioncookie);
        });
        if (!empty($setcookieheader)) {
            $expirestr = 'Expires='.gmdate(DATE_RFC7231, time() - 60);
            self::add_attributes_to_cookie_response_header('MoodleSession'.$CFG->sessioncookie, [$expirestr]);
        } else {
            setcookie('MoodleSession'.$CFG->sessioncookie, '', time() - 60);
        }
    }

    /**
     * Check whether the header string is a 'Set-Cookie' header for the cookie identified by $cookiename.
     *
     * @param string $headerstring the header string to check.
     * @param string $cookiename the name of the cookie to match.
     * @return bool true if the header string is a Set-Cookie header for the named cookie, false otherwise.
     */
    private static function cookie_response_header_matches_name(string $headerstring, string $cookiename): bool {
        // Generally match the format, but in a case-insensitive way so that 'set-cookie' and "SET-COOKIE" are both valid.
        return preg_match("/Set-Cookie: *$cookiename=/i", $headerstring)
            // Case-sensitive match on cookiename, which is case-sensitive.
            && preg_match("/: *$cookiename=/", $headerstring);
    }

    /**
     * Check whether the header string is a 'Set-Cookie' header for the cookies identified in the $cookienames array.
     *
     * @param string $headerstring the header string to check.
     * @param array $cookienames the array of cookie names to match.
     * @return bool true if the header string is a Set-Cookie header for one of the named cookies, false otherwise.
     */
    private static function cookie_response_header_matches_names(string $headerstring, array $cookienames): bool {
        foreach ($cookienames as $cookiename) {
            if (self::cookie_response_header_matches_name($headerstring, $cookiename)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether the header string contains the given attribute.
     *
     * @param string $headerstring the header string to check.
     * @param string $attribute the attribute to check for.
     * @param bool $casesensitive whether to perform a case-sensitive check.
     * @return bool true if the header contains the attribute, false otherwise.
     */
    private static function cookie_response_header_contains_attribute(string $headerstring, string $attribute,
            bool $casesensitive): bool {

        if ($casesensitive) {
            return str_contains($headerstring, $attribute);
        }
        return str_contains(strtolower($headerstring), strtolower($attribute));
    }

    /**
     * Append the given attribute to the header string.
     *
     * @param string $headerstring the header string to append to.
     * @param string $attribute the attribute to append.
     * @return string the updated header string.
     */
    private static function cookie_response_header_append_attribute(string $headerstring, string $attribute): string {
        $headerstring = rtrim($headerstring, ';'); // Sometimes included.
        return "$headerstring; $attribute;";
    }
}
