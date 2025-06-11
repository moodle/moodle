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

namespace block_quickmail\controllers;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\controllers\support\base_controller;
use block_quickmail\controllers\support\controller_request;
use block_quickmail_plugin;
use block_quickmail_config;
use block_quickmail_string;
use block_quickmail\notifier\models\notification_model_helper;
use block_quickmail\notifier\notification_condition_summary;
use block_quickmail\notifier\notification_schedule_summary;
use block_quickmail\persistents\alternate_email;
use block_quickmail\persistents\signature;
use block_quickmail\persistents\reminder_notification;
use block_quickmail\persistents\event_notification;

class create_notification_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/create_notification.php';

    public static $views = [
        'select_type' => [
            'notification_type',
            'notification_name',
        ],
        'select_model' => [
            'notification_model',
        ],
        'select_object' => [
            'notification_object_id',
        ],
        'set_conditions' => [
            'condition_time_unit',
            'condition_time_relation',
            'condition_time_amount',
            'condition_grade_greater_than',
            'condition_grade_less_than',
        ],
        'create_schedule' => [
            'schedule_time_amount',
            'schedule_time_unit',
            'schedule_begin_at',
            'schedule_end_at',
            'schedule_max_per_interval',
        ],
        'set_event_details' => [
            'time_delay_unit',
            'time_delay_amount',
            'mute_time_unit',
            'mute_time_amount',
        ],
        'create_message' => [
            'message_alternate_email_id',
            'message_subject',
            'message_body',
            'message_type',
            'message_signature_id',
            'message_send_to_mentors',
        ],
        'review' => [
            'notification_is_enabled'
        ],
    ];

    /**
     * Returns the query string which this controller's forms will append to target URLs
     *
     * NOTE: this overrides the base controller method
     *
     * @return array
     */
    public function get_form_url_params() {
        return ['courseid' => $this->props->course->id];
    }

    // Select Type.
    /**
     * Select notification type and name
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function select_type(controller_request $request) {
        $form = $this->make_form('create_notification\select_type_form');

        // Route the form submission, if any.
        if ($form->is_validated_next()) {
            return $this->post($request, 'select_type', 'next');
        } else if ($form->is_cancelled()) {
            return $request->redirect_to_url('/course/view.php', ['id' => $this->props->course->id]);
        }

        $this->render_form($form);
    }

    /**
     * Handles post of select_type form, next subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_select_type_next(controller_request $request) {
        // If notification_type has changed.
        if ($this->stored_has_changed($request->input, ['notification_type'])) {
            // Reset data for all subsequent views.
            $this->clear_store_after_view('select_type');
        }

        // Persist inputs in session.
        $this->store($request->input, $this->view_data_keys('select_type'));

        // Go to select model.
        return $this->view($request, 'select_model');
    }

    // Select Model.
    /**
     * Select notification model type
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function select_model(controller_request $request) {
        // Include model keys available for the selected notification type.
        $form = $this->make_form('create_notification\select_model_form', [
            'available_model_keys' => notification_model_helper::get_available_model_keys_by_type(
                $this->stored('notification_type'))
        ]);

        // Route the form submission, if any.
        if ($form->is_validated_next()) {
            return $this->post($request, 'select_model', 'next');
        } else if ($form->is_submitted_back()) {
            return $this->post($request, 'select_model', 'back');
        } else if ($form->is_cancelled()) {
            return $request->redirect_to_url('/course/view.php', ['id' => $this->props->course->id]);
        }

        $this->render_form($form, [
            'heading' => block_quickmail_string::get('select_notification_model',
                block_quickmail_string::get('notification_type_' . $this->stored('notification_type')))
        ]);
    }

    /**
     * Handles post of select_model form, next subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_select_model_next(controller_request $request) {
        // Persist inputs in session.
        $this->store($request->input, $this->view_data_keys('select_model'));

        // If the selected model requires an object other than "user" or "course" (which are already available).
        if (notification_model_helper::model_requires_object($this->stored('notification_type'),
               $this->stored('notification_model'))) {
            return $this->view($request, 'select_object');
        }

        // No object required….
        // If the selected model requires conditions to be set.
        if (notification_model_helper::model_requires_conditions($this->stored('notification_type'),
               $this->stored('notification_model'))) {
            // Go to set conditions view.
            return $this->view($request, 'set_conditions');
        }

        // No conditions required….
        switch ($this->stored('notification_type')) {
            case 'reminder':
                // Go to create schedule.
                return $this->view($request, 'create_schedule');
                break;

            case 'event':
                // Go to set event details.
                return $this->view($request, 'set_event_details');
                break;

            default:
                // Otherwise, something is broken :/ this should not happen unless session is cleared.
                // Send back to start.
                return $this->view($request, 'select_type');
                break;
        }
    }

    /**
     * Handles post of select_model form, back subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_select_model_back(controller_request $request) {
        return $this->view($request, 'select_type');
    }

    // Select Object for events.
    // If event requires conditions (maybe on object?) then direct to set conditions, otherwise set_event_details.
    // Set Conditions.
    /**
     * Set conditions for this notification
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function set_conditions(controller_request $request) {
        // Include which condition keys should be required for the selected notification type/model.
        $form = $this->make_form('create_notification\set_conditions_form', [
            'condition_keys' => notification_model_helper::get_condition_keys_for_model(
                $this->stored('notification_type'), $this->stored('notification_model')),
        ]);

        // Route the form submission, if any.
        if ($form->is_validated_next()) {
            return $this->post($request, 'set_conditions', 'next');
        } else if ($form->is_submitted_back()) {
            return $this->post($request, 'set_conditions', 'back');
        }

        $this->render_form($form, [
            'heading' => block_quickmail_string::get('set_notification_conditions', (object) [
                'model' => block_quickmail_string::get('notification_model_'
                    . $this->stored('notification_type') . '_' . $this->stored('notification_model')),
                'type' => block_quickmail_string::get('notification_type_'
                    . $this->stored('notification_type'))
            ])
        ]);
    }

    /**
     * Handles post of set_conditions form, next subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_set_conditions_next(controller_request $request) {
        // Persist inputs in session.
        $this->store($request->input, $this->view_data_keys('set_conditions'));

        switch ($this->stored('notification_type')) {
            case 'reminder':
                // Go to create schedule.
                return $this->view($request, 'create_schedule');
                break;

            case 'event':
                // Go to set event details.
                return $this->view($request, 'set_event_details');
                break;

            default:
                // Otherwise, something is broken :/ this should not happen unless session is cleared.
                // Send back to start.
                return $this->view($request, 'select_type');
                break;
        }
    }

    /**
     * Handles post of set_conditions form, back subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_set_conditions_back(controller_request $request) {
        // If the selected model requires object to be set.
        if (notification_model_helper::model_requires_object(
               $this->stored('notification_type'), $this->stored('notification_model'))) {
            // Go to select object view.
            return $this->view($request, 'select_object');
        }

        return $this->view($request, 'select_model');
    }

    // Create Schedule.
    /**
     * Create schedule for this notification
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function create_schedule(controller_request $request) {
        $form = $this->make_form('create_notification\create_schedule_form');

        // Route the form submission, if any.
        if ($form->is_validated_next()) {
            // Grab the manipulated moodle post to handle timestamp conversion.
            $formdata = $form->get_data();

            return $this->post($request, 'create_schedule', 'next', [
                'schedule_begin_at' => ! empty($formdata->schedule_begin_at) ? $formdata->schedule_begin_at : '',
                'schedule_end_at' => ! empty($formdata->schedule_end_at) ? $formdata->schedule_end_at : '',
            ]);
        } else if ($form->is_submitted_back()) {
            return $this->post($request, 'create_schedule', 'back');
        }

        $this->render_form($form, [
            'heading' => block_quickmail_string::get('set_notification_schedule', (object) [
                'model' => block_quickmail_string::get('notification_model_reminder_' . $this->stored('notification_model')),
                'type' => block_quickmail_string::get('notification_type_' . $this->stored('notification_type'))
            ])
        ]);
    }

    /**
     * Handles post of create_schedule form, next subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_create_schedule_next(controller_request $request) {
        // Persist inputs in session.
        $this->store($request->input, $this->view_data_keys('create_schedule'));

        return $this->view($request, 'create_message');
    }

    /**
     * Handles post of create_schedule form, back subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_create_schedule_back(controller_request $request) {
        // If the selected model requires conditions to be set.
        if (notification_model_helper::model_requires_conditions(
               $this->stored('notification_type'), $this->stored('notification_model'))) {
            // Go to set conditions view.
            return $this->view($request, 'set_conditions');
        }

        // If the selected model requires object to be set.
        if (notification_model_helper::model_requires_object(
               $this->stored('notification_type'), $this->stored('notification_model'))) {
            // Go to select object view.
            return $this->view($request, 'select_object');
        }

        return $this->view($request, 'select_model');
    }

    // Set Event Details.
    /**
     * Specify details for this event notification
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function set_event_details(controller_request $request) {
        $form = $this->make_form('create_notification\set_event_details_form', [
            'is_one_time_event' => notification_model_helper::model_is_one_time_event($this->stored('notification_model')),
        ]);

        // Route the form submission, if any.
        if ($form->is_validated_next()) {
            // Grab the manipulated moodle post to handle timestamp conversion.
            $formdata = $form->get_data();

            return $this->post($request, 'set_event_details', 'next');
        } else if ($form->is_submitted_back()) {
            return $this->post($request, 'set_event_details', 'back');
        }

        $this->render_form($form, [
            'heading' => block_quickmail_string::get('set_event_details', (object) [
                'model' => block_quickmail_string::get('notification_model_event_' . $this->stored('notification_model')),
                'type' => block_quickmail_string::get('notification_type_' . $this->stored('notification_type'))
            ])
        ]);
    }

    /**
     * Handles post of set_event_details form, next subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_set_event_details_next(controller_request $request) {
        // Persist inputs in session.
        $this->store($request->input, $this->view_data_keys('set_event_details'));

        return $this->view($request, 'create_message');
    }

    /**
     * Handles post of set_event_details form, back subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_set_event_details_back(controller_request $request) {
        // If the selected model requires conditions to be set.
        if (notification_model_helper::model_requires_conditions(
               $this->stored('notification_type'), $this->stored('notification_model'))) {
            // Go to set conditions view.
            return $this->view($request, 'set_conditions');
        }

        // If the selected model requires object to be set.
        if (notification_model_helper::model_requires_object(
               $this->stored('notification_type'), $this->stored('notification_model'))) {
            // Go to select object view.
            return $this->view($request, 'select_object');
        }

        return $this->view($request, 'select_model');
    }

    // Create Message.
    /**
     * Create message details for this notification
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function create_message(controller_request $request) {
        // Get the user's default signature id, if any, defaulting to 0.
        if ($signature = signature::get_default_signature_for_user($this->props->user->id)) {
            $userdefaultsignatureid = $signature->get('id');
        } else {
            $userdefaultsignatureid = 0;
        }

        $form = $this->make_form('create_notification\create_message_form', [
            'editor_options' => block_quickmail_config::get_editor_options($this->context),
            // Get config variables for this course, defaulting to block level.
            'course_config_array' => block_quickmail_config::get('', $this->props->course),
            // Get the user's available alternate emails for this course.
            'user_alternate_email_array' => alternate_email::get_flat_array_for_course_user(
                $this->props->course->id, $this->props->user),
            // Get the user's current signatures as array (id => title).
            'user_signature_array' => signature::get_flat_array_for_user($this->props->user->id),
            'user_default_signature_id' => $userdefaultsignatureid,
            // Only allow users with hard set capabilities (not students) to copy mentors.
            'allow_mentor_copy' => block_quickmail_plugin::user_can_send('compose', $this->props->user, $this->context, '', false),
            'notification_object_type' => notification_model_helper::get_object_type_for_model(
                $this->stored('notification_type'), $this->stored('notification_model'))
        ]);

        // Route the form submission, if any.
        if ($form->is_validated_next()) {
            return $this->post($request, 'create_message', 'next');
        } else if ($form->is_submitted_back()) {
            return $this->post($request, 'create_message', 'back');
        }

        $this->render_form($form, [
            'heading' => block_quickmail_string::get('create_notification_message', (object) [
                'model' => block_quickmail_string::get('notification_model_'
                    . $this->stored('notification_type') . '_' . $this->stored('notification_model')),
                'type' => block_quickmail_string::get('notification_type_' . $this->stored('notification_type'))
            ])
        ]);
    }

    /**
     * Handles post of create_message form, next subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_create_message_next(controller_request $request) {
        // Persist inputs in session.
        $this->store($request->input, $this->view_data_keys('create_message'), [
            'message_body' => $request->input->message_body['text']
        ]);

        return $this->view($request, 'review');
    }

    /**
     * Handles post of create_message form, back subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_create_message_back(controller_request $request) {
        switch ($this->stored('notification_type')) {
            case 'reminder':
                // Go to create schedule.
                return $this->view($request, 'create_schedule');
                break;

            case 'event':
                // Go to set event details.
                return $this->view($request, 'set_event_details');
                break;

            default:
                // Otherwise, something is broken :/ this should not happen unless session is cleared.
                // Send back to start.
                return $this->view($request, 'select_type');
                break;
        }
    }

    // Review.
    /**
     * Show summary page for this notification yet to be created
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function review(controller_request $request) {
        // Build condition params.
        $conditionparams = [];
        foreach ($this->view_data_keys('set_conditions') as $key) {
            $conditionparams[str_replace('condition_', '', $key)] = $this->stored($key);
        }

        $form = $this->make_form('create_notification\review_form', [
            'condition_summary' => notification_condition_summary::get_model_condition_summary(
                $this->stored('notification_type'), $this->stored('notification_model'), $conditionparams),
            'schedule_summary' => notification_schedule_summary::get_from_params([
                'time_amount' => $this->stored('schedule_time_amount'),
                'time_unit' => $this->stored('schedule_time_unit'),
                'begin_at' => $this->stored('schedule_begin_at'),
                'end_at' => $this->stored('schedule_end_at'),
            ]),
            'is_one_time_event' => notification_model_helper::model_is_one_time_event($this->stored('notification_model')),
        ]);

        // List of form submission subactions that may be handled in addition to "back" or "next".
        $subactions = [
            'edit_select_type',
            'edit_set_conditions',
            'edit_create_schedule',
            'edit_set_event_details',
            'edit_create_message',
        ];

        // Route the form submission, if any.
        if ($form->is_submitted_subaction('edit_select_type', $subactions)) {
            return $this->post($request, 'review', 'edit_select_type');
        } else if ($form->is_submitted_subaction('edit_set_conditions', $subactions)) {
            return $this->post($request, 'review', 'edit_set_conditions');
        } else if ($form->is_submitted_subaction('edit_create_schedule', $subactions)) {
            return $this->post($request, 'review', 'edit_create_schedule');
        } else if ($form->is_submitted_subaction('edit_set_event_details', $subactions)) {
            return $this->post($request, 'review', 'edit_set_event_details');
        } else if ($form->is_submitted_subaction('edit_create_message', $subactions)) {
            return $this->post($request, 'review', 'edit_create_message');
        } else if ($form->is_validated_next()) {
            return $this->post($request, 'review', 'next');
        }

        $this->render_form($form, [
            'heading' => block_quickmail_string::get('notification_review')
        ]);
    }

    /**
     * Handles post of review form, edit_select_type subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_review_edit_select_type(controller_request $request) {
        return $this->view($request, 'select_type');
    }

    /**
     * Handles post of review form, edit_set_conditions subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_review_edit_set_conditions(controller_request $request) {
        return $this->view($request, 'set_conditions');
    }

    /**
     * Handles post of review form, edit_create_schedule subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_review_edit_create_schedule(controller_request $request) {
        return $this->view($request, 'create_schedule');
    }

    /**
     * Handles post of review form, edit_set_event_details subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_review_edit_set_event_details(controller_request $request) {
        return $this->view($request, 'set_event_details');
    }

    /**
     * Handles post of review form, edit_create_message subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_review_edit_create_message(controller_request $request) {
        return $this->view($request, 'create_message');
    }

    /**
     * Handles post of review form, next subaction (final submit)
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_review_next(controller_request $request) {
        // Persist inputs in session.
        $this->store($request->input, $this->view_data_keys('review'));

        try {
            // Create a notification.
            $this->stored('notification_type') == 'reminder'
                ? $this->create_reminder_notification()
                : $this->create_event_notification();

        } catch (\Exception $e) {
            var_dump($e->getMessage());die;
        }

        $request->redirect_as_success(block_quickmail_string::get('notification_created'),
            '/course/view.php', ['id' => $this->props->course->id]);
    }

    /**
     * Creates and returns a reminder notification model from this controller's session data
     *
     * @return reminder_notification
     */
    private function create_reminder_notification() {
        return reminder_notification::create_type(
            str_replace('_', '-', $this->stored('notification_model')),
            $this->props->course, // Need to pull the object here!
            $this->props->user,
            array_merge($this->get_notification_params(), [
                'schedule_unit' => $this->stored('schedule_time_unit'),
                'schedule_amount' => (int)$this->stored('schedule_time_amount'),
                'schedule_begin_at' => (int)$this->stored('schedule_begin_at'),
                'schedule_end_at' => (int)$this->stored('schedule_end_at'),
                'max_per_interval' => (int)$this->stored('schedule_max_per_interval'),
            ]),
            $this->props->course
        );
    }

    /**
     * Creates and returns an event notification model from this controller's session data
     *
     * @return event_notification
     */
    private function create_event_notification() {
        return event_notification::create_type(
            str_replace('_', '-', $this->stored('notification_model')),
            $this->props->course, // Need to pull the object here!
            $this->props->user,
            array_merge($this->get_notification_params(), [
                'time_delay_unit' => $this->stored('time_delay_unit'),
                'time_delay_amount' => $this->stored('time_delay_amount'),
                'mute_time_unit' => $this->stored('mute_time_unit'),
                'mute_time_amount' => $this->stored('mute_time_amount'),
            ]),
            $this->props->course
        );
    }

    /**
     * Returns an array of general notification creation params from this controller's session data
     *
     * @return array
     */
    private function get_notification_params() {
        return [
            'name' => $this->stored('notification_name'),
            'message_type' => $this->stored('message_type'),
            'subject' => $this->stored('message_subject'),
            'body' => $this->stored('message_body'),
            'is_enabled' => (int) $this->stored('notification_is_enabled'),
            'alternate_email_id' => (int) $this->stored('message_alternate_email_id'),
            'signature_id' => (int) $this->stored('message_signature_id'),
            'editor_format' => 1,
            'send_receipt' => 0,
            'send_to_mentors' => (int) $this->stored('message_send_to_mentors'),
            'no_reply' => 1,
            'condition_time_unit' => $this->stored('condition_time_unit'),
            'condition_time_amount' => (int) $this->stored('condition_time_amount'),
            'condition_time_relation' => $this->stored('condition_time_relation'),
            'condition_grade_greater_than' => $this->stored('condition_grade_greater_than'),
            'condition_grade_less_than' => $this->stored('condition_grade_less_than'),
        ];
    }
}
