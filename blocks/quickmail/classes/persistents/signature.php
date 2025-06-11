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

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use lang_string;
use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\belongs_to_a_user;
use block_quickmail\persistents\concerns\can_be_soft_deleted;

class signature extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        belongs_to_a_user,
        can_be_soft_deleted;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_signatures';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'user_id' => [
                'type' => PARAM_INT,
            ],
            'title' => [
                'type' => PARAM_TEXT,
            ],
            'signature' => [
                'type' => PARAM_RAW,
            ],
            'default_flag' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    // Validators.
    protected function validate_title($value) {
        // If this is a new signature attempting to be created, check to make sure this title is unique to the user.
        if (!$this->get('id') && self::count_records([
            'title' => $value,
            'user_id' => $this->get('user_id'),
            'timedeleted' => 0
        ])) {
            return new lang_string('signature_title_must_be_unique', 'block_quickmail');
        }

        if (empty($value)) {
            return new lang_string('signature_title_required', 'block_quickmail');
        }

        return true;
    }

    protected function validate_signature($value) {
        if (empty($value)) {
            return new lang_string('signature_signature_required', 'block_quickmail');
        }

        return true;
    }

    // Hooks.
    /**
     * Take appropriate actions before creating a new signature, including:
     *
     *   - if new signature is not default, and user has no signatures, make it the default
     *
     * @return void
     */
    protected function before_create() {
        $existinguserdefault = self::get_default_signature_for_user($this->get('user_id'));

        if (!$this->is_default() && empty($existinguserdefault)) {
            $this->set('default_flag', 1);
        }
    }

    /**
     * Take appropriate actions after creating a new signature, including:
     *
     *   - if new signature is default, and user has an existing default signature, make the existing default NOT default
     *
     * @return void
     */
    protected function after_create() {
        $existinguserdefault = self::get_default_signature_for_user($this->get('user_id'));

        if ($this->is_default() && ! empty($existinguserdefault)) {
            $existinguserdefault->set('default_flag', 0);
            $existinguserdefault->update();
        }
    }

    /**
     * Take appropriate actions after updating a signature, including:
     *
     *   - if this updated signature is now default, flag all others (if any), as non-default
     *   - if this updated signature is NOT default, make sure there is at least one default
     *
     * @param bool  $result  whether or not the update was successful
     * @return void
     */
    protected function after_update($result) {
        if ($result) {
            if ($this->is_default()) {
                global $DB;

                $sql = 'UPDATE {block_quickmail_signatures}
                        SET default_flag = 0
                        WHERE id <> ? AND user_id = ?';

                $DB->execute($sql, [
                    $this->get('id'),
                    $this->get('user_id'),
                ]);
            } else {
                $existinguserdefault = self::get_default_signature_for_user($this->get('user_id'));

                if (empty($existinguserdefault)) {
                    $this->set('default_flag', 1);
                    $this->update();
                }
            }
        }
    }

    /**
     * Take appropriate actions before deleting a signature, including:
     *
     *   - if user default signature is deleted, set a new one if possible
     *
     * @return void
     */
    protected function before_delete() {
        // If this signature being deleted is the default signature.
        if ($this->is_default()) {
            // Get this signature's owning user.
            $user = $this->get_user();

            // Mark this deleted signature as being NOT default.
            $this->set('default_flag', 0);

            // Get all signatures for this user, if any.
            $usersignatures = self::get_records(['user_id' => $user->id, 'timedeleted' => 0]);

            // If any signatures, set another as default.
            foreach ($usersignatures as $signature) {
                // If this is the signature being deleted, continue to next, if any.
                if ($signature->is_default()) {
                    continue;
                }

                // Save this signature as default.
                $signature->set('default_flag', 1);
                $signature->update();
            }
        }
    }

    // Custom Methods.
    /**
     * Reports whether or not this signature is the user's default
     *
     * @return bool
     */
    public function is_default() {
        return (bool) $this->get('default_flag');
    }

    // Custom Static Methods.
    /**
     * Fetches a signature by id which must belong to the given user id
     *
     * @param  integer $signatureid
     * @param  integer $userid
     * @return signature|null
     */
    public static function find_user_signature_or_null($signatureid = 0, $userid = 0) {
        // First, try to find the signature by id, returning null by default.
        if (!$signature = self::find_or_null($signatureid)) {
            return null;
        }

        // If this signature does not belong to this user, return null.
        if (!$signature->is_owned_by_user($userid)) {
            return null;
        }

        return $signature;
    }

    /**
     * Returns an array of signatures belonging to the given user id
     *
     * @param  int     $userid
     * @return array   (signature id => signature title)
     */
    public static function get_flat_array_for_user($userid) {
        // Get all signatures for this user, if any.
        $usersignatures = self::get_records(['user_id' => $userid, 'timedeleted' => 0]);

        $result = array_reduce($usersignatures, function ($carry, $signature) {
            $value = $signature->get('title');

            if ($signature->get('default_flag')) {
                $value .= ' (default)';
            }

            $carry[$signature->get('id')] = $value;

            return $carry;
        }, []);

        return $result;
    }

    /**
     * Returns a user's default signature, or null if none found
     *
     * @param  int        $userid
     * @return mixed      $signature|null
     */
    public static function get_default_signature_for_user($userid) {
        // Get all signatures for this user, if any.
        $usersignatures = self::get_records(['user_id' => $userid, 'timedeleted' => 0]);

        foreach ($usersignatures as $signature) {
            if ($signature->is_default()) {
                return $signature;
            }
        }

        return null;
    }

    /**
     * Handles the persistence and display of text editor content after updating a signature
     *
     * @param  object             $context
     * @param  signature          $signature
     * @param  request  $request
     * @return void
     */
    public static function handle_post_save_or_update($context, $signature, $request) {
        file_postupdate_standard_editor(
            $request->form->get_data(),
            'signature',
            \block_quickmail_config::get_editor_options($context),
            $context,
            \block_quickmail_plugin::$name,
            'signature_editor',
            $signature->get('id')
        );
    }

}
