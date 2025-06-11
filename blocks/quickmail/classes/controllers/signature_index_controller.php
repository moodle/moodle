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
use block_quickmail_string;
use block_quickmail\persistents\signature;

class signature_index_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/signatures.php';

    public static $views = [
        'signature' => [],
    ];

    /**
     * Returns the query string which this controller's forms will append to target URLs
     *
     * NOTE: this overrides the base controller method
     *
     * @return array
     */
    public function get_form_url_params() {
        return [
            'id' => $this->props->signature_id,
            'courseid' => $this->props->course_id
        ];
    }

    /**
     * Manage user signatures
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function signature(controller_request $request) {
        // Fetch the requested signature, if any, which must belong to the auth user.
        $signature = signature::find_user_signature_or_null($this->props->signature_id, $this->props->user->id);

        $form = $this->make_form('signature_index\signature_form', [
            'context' => $this->context,
            'selected_signature' => $signature,
            'user_signature_array' => signature::get_flat_array_for_user($this->props->user->id)
        ]);

        // List of form submission subactions that may be handled in addition to "back" or "next".
        $subactions = [
            'save',
            'update',
            'delete',
        ];

        // Route the form submission, if any.
        if ($form->is_submitted_subaction('save', $subactions, true)) {
            return $this->post($request, 'signature', 'save');
        } else if ($form->is_submitted_subaction('update', $subactions, true)) {
            return $this->post($request, 'signature', 'update');
        } else if ($form->is_submitted_subaction('delete', $subactions, true)) {
            return $this->post($request, 'signature', 'delete');
        } else if ($form->is_cancelled()) {
            $request->redirect_to_course_or_my($this->props->course_id);
        }

        $this->render_form($form);
    }

    /**
     * Handles post of signature form, save subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_signature_save(controller_request $request) {
        // Attempt to create a new signature.
        $signature = signature::create_new([
            'user_id' => $this->props->user->id,
            'title' => $request->input->title,
            'signature' => $request->input->signature_editor['text'],
            'default_flag' => property_exists($request->input, 'default_flag') ? $request->input->default_flag : 0,
        ]);

        $request->redirect_as_success(get_string('changessaved'), static::$baseuri, [
            'id' => $signature->get('id'),
            'courseid' => $this->props->course_id
        ]);
    }

    /**
     * Handles post of signature form, update subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_signature_update(controller_request $request) {
        // Fetch the requested signature, if any, which must belong to the auth user.
        $signature = signature::find_user_signature_or_null($request->input->signature_id, $this->props->user->id);

        // Update the signature.
        $signature->set('title', $request->input->title);
        $signature->set('signature', $request->input->signature_editor['text']);
        $signature->set('default_flag', property_exists($request->input, 'default_flag') ? $request->input->default_flag : 0);
        $signature->update();

        $request->redirect_as_success(get_string('changessaved'), static::$baseuri, [
            'id' => $signature->get('id'),
            'courseid' => $this->props->course_id
        ]);
    }

    /**
     * Handles post of signature form, delete subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_signature_delete(controller_request $request) {
        // Fetch the requested signature, if any, which must belong to the auth user.
        $signature = signature::find_user_signature_or_null($request->input->signature_id, $this->props->user->id);

        // Soft delete the signature, flagging a new default if possible.
        $signature->soft_delete();

        // Get the user's default signature, if any, to redirect back to.
        if (!$defaultsignature = signature::get_default_signature_for_user($this->props->user->id)) {
            $redirectsignatureid = 0;
        } else {
            $redirectsignatureid = $defaultsignature->get('id');
        }

        $request->redirect_as_success(block_quickmail_string::get('user_signature_deleted'), static::$baseuri, [
            'id' => $redirectsignatureid,
            'courseid' => $this->props->course_id
        ]);
    }

}
