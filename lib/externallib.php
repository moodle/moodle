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

use core_external\util;

defined('MOODLE_INTERNAL') || die;

// Please note that this file and all of the classes and functions listed below will be deprecated from Moodle 4.6.
// This deprecation is delayed to aid plugin developers when maintaining plugins for multiple Moodle versions.
// See MDL-76583 for further information.

// If including this file for unit testing, it _must_ be run in an isolated process to prevent
// any side effect upon other tests.
require_phpunit_isolation();

class_alias(\core_external\external_api::class, 'external_api');
class_alias(\core_external\restricted_context_exception::class, 'restricted_context_exception');
class_alias(\core_external\external_description::class, 'external_description');
class_alias(\core_external\external_value::class, 'external_value');
class_alias(\core_external\external_format_value::class, 'external_format_value');
class_alias(\core_external\external_single_structure::class, 'external_single_structure');
class_alias(\core_external\external_multiple_structure::class, 'external_multiple_structure');
class_alias(\core_external\external_function_parameters::class, 'external_function_parameters');
class_alias(\core_external\util::class, 'external_util');
class_alias(\core_external\external_files::class, 'external_files');
class_alias(\core_external\external_warnings::class, 'external_warnings');
class_alias(\core_external\external_settings::class, 'external_settings');

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
 * @since Moodle 2.0
 */
function external_generate_token($tokentype, $serviceorid, $userid, $contextorid, $validuntil = 0, $iprestriction = '') {
    if (is_numeric($serviceorid)) {
        $service = util::get_service_by_id($serviceorid);
    } else if (is_string($serviceorid)) {
        $service = util::get_service_by_name($serviceorid);
    } else {
        $service = $serviceorid;
    }

    if (!is_object($contextorid)) {
        $context = context::instance_by_id($contextorid, MUST_EXIST);
    } else {
        $context = $contextorid;
    }

    return util::generate_token(
        $tokentype,
        $service,
        $userid,
        $context,
        $validuntil,
        $iprestriction
    );
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
function external_create_service_token($servicename, $contextid) {
    global $USER;

    return util::generate_token(
        EXTERNAL_TOKEN_EMBEDDED,
        util::get_service_by_name($servicename),
        $USER->id,
        \context::instance_by_id($contextid)
    );
}

/**
 * Delete all pre-built services (+ related tokens) and external functions information defined in the specified component.
 *
 * @param string $component name of component (moodle, etc.)
 */
function external_delete_descriptions($component) {
    util::delete_service_descriptions($component);
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
    return util::validate_format($format);
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
function external_format_string($str, $context, $striplinks = true, $options = []) {
    if (!$context instanceof context) {
        $context = context::instance_by_id($context);
    }

    return util::format_string($str, $context, $striplinks, $options);
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
 * @param context|int $context This parameter and the next two identify the file area to use.
 * @param string $component
 * @param string $filearea helps identify the file area.
 * @param int $itemid helps identify the file area.
 * @param object/array $options text formatting options
 * @return array text + textformat
 * @since Moodle 2.3
 * @since Moodle 3.2 component, filearea and itemid are optional parameters
 */
function external_format_text($text, $textformat, $context, $component = null, $filearea = null, $itemid = null, $options = null) {
    if (!$context instanceof context) {
        $context = context::instance_by_id($context);
    }

    return util::format_text($text, $textformat, $context, $component, $filearea, $itemid, $options);
}

/**
 * Generate or return an existing token for the current authenticated user.
 * This function is used for creating a valid token for users authenticathing via login/token.php or admin/tool/mobile/launch.php.
 *
 * @param stdClass $service external service object
 * @return stdClass token object
 * @since Moodle 3.2
 */
function external_generate_token_for_current_user($service) {
    return util::generate_token_for_current_user($service);
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
function external_log_token_request($token): void {
    util::log_token_request($token);
}
