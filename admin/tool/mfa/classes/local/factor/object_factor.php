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
 * MFA factor interface.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local\factor;

interface object_factor {

    /**
     * Returns true if factor is enabled, otherwise false.
     *
     * @return bool
     * @throws \dml_exception
     */
    public function is_enabled();

    /**
     * Returns configured factor weight.
     *
     * @return int
     * @throws \dml_exception
     */
    public function get_weight();

    /**
     * Returns factor name from language string.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_display_name();

    /**
     * Returns factor info from language string.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_info();

    /**
     * Defines setup_factor form definition page for particular factor.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function setup_factor_form_definition($mform);

    /**
     * Defines setup_factor form definition page after form data has been set.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function setup_factor_form_definition_after_data($mform);

    /**
     * Implements setup_factor form validation for particular factor.
     * Returns an array of errors, where array key = field id and array value = error text.
     *
     * @param array $data
     * @return array
     */
    public function setup_factor_form_validation($data);

    /**
     * Defines login form definition page for particular factor.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function login_form_definition($mform);

    /**
     * Defines login form definition page after form data has been set.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function login_form_definition_after_data($mform);

    /**
     * Implements login form validation for particular factor.
     * Returns an array of errors, where array key = field id and array value = error text.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation($data);

    /**
     * Setups given factor and adds it to user's active factors list.
     * Returns true if factor has been successfully added, otherwise false.
     *
     * @param array $data
     * @return stdClass the factor record, or null.
     */
    public function setup_user_factor($data);

    /**
     * Returns an array of all user factors of given type (both active and revoked).
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors($user);

    /**
     * Returns an array of active user factor records.
     * Filters get_all_user_factors() output.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_active_user_factors($user);

    /**
     * Returns true if factor class has factor records that might be revoked.
     * It means that user can revoke factor record from their profile.
     *
     * @return bool
     */
    public function has_revoke();

    /**
     * Marks factor record as revoked.
     * If factorid is not provided, revoke all instances of factor.
     *
     * @param int $factorid
     * @return bool
     */
    public function revoke_user_factor($factorid);

    /**
     * When validation code is correct - update lastverified field for given factor.
     * If factor id is not provided, update all factor entries for user.
     *
     * @param int $factorid
     * @return bool
     */
    public function update_lastverified($factorid);

    /**
     * Gets lastverified timestamp.
     *
     * @param int $factorid
     * @return int
     */
    public function get_lastverified($factorid);

    /**
     * Returns true if factor needs to be setup by user and has setup_form.
     *
     * @return bool
     */
    public function has_setup();

    /**
     * If has_setup returns true, decides if the setup buttons should be shown on the preferences page.
     *
     * @return bool
     */
    public function show_setup_buttons();

    /**
     * Returns true if factor requires user input for success or failure during login.
     *
     * @return bool
     */
    public function has_input();

    /**
     * Returns the state of the factor check
     *
     * @return mixed
     */
    public function get_state();

    /**
     * Sets the state of the factor check into the session.
     * Returns whether storing the var was successful.
     *
     * @param mixed $state
     * @return bool
     */
    public function set_state($state);

    /**
     * Fires any additional actions required by the factor once the user reaches the pass state.
     *
     * @return void
     */
    public function post_pass_state();

    /**
     * Retrieves label for a factorid.
     *
     * @param int $factorid
     * @return string
     */
    public function get_label($factorid);

    /**
     * Returns a list of urls to not redirect from.
     *
     * @return array
     */
    public function get_no_redirect_urls();

    /**
     * Returns all possible states for a user.
     *
     * @param \stdClass $user
     */
    public function possible_states($user);

    /**
     * Return summary condition for passing factor.
     *
     * @return array
     */
    public function get_summary_condition();

    /**
     * Checks whether the factor combination is valid based on factor behaviour.
     * E.g. a combination with nosetup and another factor is not valid,
     * as you cannot pass nosetup with another factor.
     *
     * @param array $combination array of factors that make up the combination
     * @return bool
     */
    public function check_combination($combination);

    /**
     * Gets the string for setup button on preferences page.
     *
     * @return string the string to display on the button.
     */
    public function get_setup_string();

    /**
     * Deletes all instances of a factor for user.
     *
     * @param stdClass $user the user to delete for.
     */
    public function delete_factor_for_user($user);

    /**
     * Process a cancel action from a user.
     *
     * @return void
     */
    public function process_cancel_action();

    /**
     * Hook point for global auth form action hooks.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return void
     */
    public function global_definition($mform);

    /**
     * Hook point for global auth form action hooks.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return void
     */
    public function global_definition_after_data($mform);

    /**
     * Hook point for global auth form action hooks.
     *
     * @param array $data Data from the form.
     * @param array $files Files form the form.
     * @return array of errors from validation.
     */
    public function global_validation($data, $files): array;

    /**
     * Hook point for global auth form action hooks.
     *
     * @param object $data Data from the form.
     */
    public function global_submit($data);
}
