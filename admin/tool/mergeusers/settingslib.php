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
 * Settings builders.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_mergeusers\local\config;
use tool_mergeusers\local\merger\quiz_attempts_table_merger;

/**
 * Builds the form options for table exception from processing.
 *
 * @return stdClass instance with attributes for defining exception options.
 * @throws coding_exception
 */
function tool_mergeusers_build_exceptions_options(): stdClass {
    $config = config::instance();
    $none = get_string('none');
    $options = ['none' => $none];
    foreach ($config->exceptions as $exception) {
        $options[$exception] = $exception;
    }
    // Duplicated records on 'my_pages' table make MyMoodle does not work.
    unset($options['my_pages']);

    $result = new stdClass();
    $result->defaultkey = 'none';
    $result->defaultvalue = $none;
    $result->options = $options;

    return $result;
}

/**
 * Builds the quiz attempts options for the plugin settings.
 *
 * @return stdClass instance with the options and defaultkey to be used.
 * @throws coding_exception
 */
function tool_mergeusers_build_quiz_options(): stdClass {
    $options = [
        quiz_attempts_table_merger::ACTION_RENUMBER,
        quiz_attempts_table_merger::ACTION_DELETE_FROM_SOURCE,
        quiz_attempts_table_merger::ACTION_DELETE_FROM_TARGET,
        quiz_attempts_table_merger::ACTION_REMAIN,
    ];
    $optionsstrings = new stdClass();
    $quizoptions = [];
    foreach ($options as $optionname) {
        $optionsstrings->{$optionname} = get_string('qa_action_' . $optionname, 'tool_mergeusers');
        $quizoptions[$optionname] = $optionsstrings->{$optionname};
    }

    $result = new stdClass();
    $result->allstrings = $optionsstrings;
    $result->defaultkey = quiz_attempts_table_merger::ACTION_RENUMBER;
    $result->options = $quizoptions;

    return $result;
}

/**
 * Informs whether there exist yet prior user profile fields from this plugin.
 *
 * In prior versions we added custom user profile fields to inform about
 * last merges related to the user on its profile.
 *
 * With this function we inform whether there are yet some of the custom
 * user profile fields and informs the administrator that they are no longer used,
 * and they can be securely deleted.
 *
 * We do not delete them on an upgrade to let administrators adapt to the new
 * way of proceeding.
 *
 * @throws dml_exception
 */
function tool_mergeusers_inform_about_pending_user_profile_fields(): stdClass {
    global $DB;

    // Upgrade and install code related to user profile fields was removed.
    // Using literals here for convenience.
    $shortnames = [
        'mergeusers_date',
        'mergeusers_logid',
        'mergeusers_olduserid',
        'mergeusers_newuserid',
    ];
    $results = [];
    $categories = [];
    foreach ($shortnames as $shortname) {
        $categoryid = $DB->get_field('user_info_field', 'categoryid', ['shortname' => $shortname]);
        if (!$categoryid) {
            continue;
        }
        $results[$shortname] = $shortname;
        $categories[$categoryid] = $DB->get_field('user_info_category', 'name', ['id' => $categoryid]);
    }

    $stillexists = (count($results) > 0);
    return (object)[
        'exists' => $stillexists,
        'shortnames' => implode(', ', $results),
        'categories' => implode(', ', $categories),
        'url' => (new moodle_url('/user/profile/index.php'))->out(false),
    ];
}
