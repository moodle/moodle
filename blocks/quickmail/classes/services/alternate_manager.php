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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\services;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\alternate_email;
use block_quickmail\validators\create_alternate_form_validator;
use block_quickmail\exceptions\validation_exception;
use block_quickmail_plugin;
use block_quickmail_emailer;
use block_quickmail_string;
use html_writer;
use moodle_url;
use context_course;

class alternate_manager {

    /**
     * Creates an alternate email for the given user with the given data
     *
     * @param  object  $user  the creating user
     * @param  array  $params [availability,email,firstname,lastname,allowed_role_ids]
     * @param  int     $courseid  (optional) a course id to scope this alternate to if desired
     * @return alternate_email
     */
    public static function create_alternate_for_user($user, $params, $courseid = 0) {
        // Validate form data.
        $validator = new create_alternate_form_validator((object) $params);
        $validator->validate();

        // If errors, throw exception.
        if ($validator->has_errors()) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'),
                $validator->errors
            );
        }

        /*
         * Alternate_availability_only (user + course)
         * Alternate_availability_user (user)
         * Alternate_availability_course (course)
         */

        // If an availability requiring a scoped course is selected.
        if ($params['availability'] !== 'user') {
            // If no course was given, throw an error.
            if (!$courseid) {
                throw new validation_exception(
                    block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('course_required')
                    ]);
            }
        }

        // If this is an availability which does not allow for role ids, clear the param.
        if (in_array($params['availability'], ['user', 'only'])) {
            $params['allowed_role_ids'] = '';
        }

        // If we still have allowed_role_ids, make sure this user is allowed to assign them.
        if (!empty($params['allowed_role_ids'])) {
            // Pull the course context to determine capability of this user.
            $coursecontext = context_course::instance($courseid);

            // Determine if this user is able to assign shared role ids in this course.
            if (!$allowcoursealternates = block_quickmail_plugin::user_has_capability(
                                              'allowcoursealternate',
                                              $user,
                                              $coursecontext)) {
                throw new validation_exception(
                    block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('coursealternate_not_allowed')
                    ]);
            }
        }

        // Create the new alternate email.
        $alternate = alternate_email::create_new([
            'setup_user_id' => $user->id,
            'email' => $params['email'],
            'firstname' => $params['firstname'],
            'lastname' => $params['lastname'],
            'allowed_role_ids' => $params['allowed_role_ids'] ? implode(',', $params['allowed_role_ids']) : '',
            'course_id' => $courseid,
            'user_id' => $params['availability'] !== 'course' ? $user->id : 0,
        ]);

        // Send the set up user a confirmation email.
        self::send_confirmation_email($alternate);

        return $alternate;
    }

    /**
     * Resends a confirmation email to the alternate email's set up user (if the given user is the set up user)
     *
     * @param  int     $alternateemailid
     * @param  object  $user
     * @return void
     */
    public static function resend_confirmation_email_for_user($alternateemailid, $user) {
        // Attempt to fetch the alternate.
        if (!$alternate = alternate_email::find_or_null($alternateemailid)) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_email_not_found')
                ]);
        }

        // Make sure the requesting user is this alternate's setup user.
        if ($user->id !== $alternate->get('setup_user_id')) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_owner_must_confirm')
                ]);
        }

        // Make sure the alternate is not already confirmed (validated).
        if ($alternate->get('is_validated')) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_already_confirmed')
                ]);
        }

        self::send_confirmation_email($alternate);
    }

    /**
     * Updates an alternate email to confirmed status given the correct token and user
     *
     * @param  int     $alternateemailid
     * @param  string  $token               generated by moodle, comes from the confirmation email's URL
     * @param  object  $user                the requesting user
     * @return alternate_email
     */
    public static function confirm_alternate_for_user($alternateemailid, $token, $user) {
        // Attempt to fetch the alternate.
        if (!$alternate = alternate_email::find_or_null($alternateemailid)) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_email_not_found')
                ]);
        }

        // Make sure the alternate is not already confirmed (validated).
        if ($alternate->get('is_validated')) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_already_confirmed')
                ]);
        }

        global $DB;

        // Fetch the user key from the token.
        if (!$key = $DB->get_record('user_private_key', [
            'instance' => $alternate->get('id'),
            'value' => $token,
            'userid' => $user->id,
            'script' => 'blocks/quickmail'
        ])) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_invalid_token')
                ]);
        }

        // Mark this alternate email as validated.
        $alternate->set('is_validated', 1);
        $alternate->update();

        // Delete the key.
        $DB->delete_records('user_private_key', ['id' => $key->id]);

        return $alternate;
    }

    /**
     * Attempts to soft delete the alternate email address for a given user
     *
     * @param  int  $alternateemailid
     * @param  object  $user        the user attempting to delete the alternate
     * @return bool
     */
    public static function delete_alternate_email_for_user($alternateemailid, $user) {
        // Attempt to fetch the alternate.
        if (!$alternate = alternate_email::find_or_null($alternateemailid)) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_email_not_found')
                ]);
        }

        // Make sure the given user is the owner of this alternate.
        if ($alternate->get('setup_user_id') !== $user->id) {
            throw new validation_exception(
                block_quickmail_string::get('validation_exception_message'), [
                    block_quickmail_string::get('alternate_owner_must_delete')
                ]);
        }

        // Attempt to soft delete alternate.
        $alternate->soft_delete();

        return true;
    }

    /**
     * Sends a confirmation email to the given alternate's email
     *
     * This email will contain a confirmation URL with a generated token
     * that will need to be hit by the user for confirmation of the alternate
     *
     * @param  alternate_email  $alternateemail
     * @return void
     */
    private static function send_confirmation_email($alternateemail) {
        // Get the user who created this alternate.
        $user = $alternateemail->get_setup_user();

        // Generate, or fetch existing, token for this user and alternate instance.
        // Note: this does not expire!
        $token = get_user_key('blocks/quickmail', $user->id, $alternateemail->get('id'));

        // Build the confirmation "landing" url.
        $approvalurl = new moodle_url('/blocks/quickmail/alternate.php', [
            'action' => 'confirm',
            'id' => $alternateemail->get('id'),
            'token' => $token
        ]);

        // Construct the confirmation email content.
        $a = (object)[];
        $a->email = $alternateemail->get('email');
        $a->url = html_writer::link($approvalurl, $approvalurl->out());
        $a->plugin_name = block_quickmail_string::get('pluginname');
        $a->fullname = fullname($user);
        $htmlbody = block_quickmail_string::get('alternate_body', $a);
        $body = strip_tags($htmlbody);

        // Send the email.
        $emailer = new block_quickmail_emailer(
            $alternateemail->get_setup_user(),
            block_quickmail_string::get('alternate_subject'),
            $body
        );
        $emailer->to_email($alternateemail->get('email'));
        $emailer->send();
    }

}
