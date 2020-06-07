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
 * Statement base object for xAPI structure checking and validation.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local;

use core_xapi\local\statement\item;
use core_xapi\local\statement\item_actor;
use core_xapi\local\statement\item_object;
use core_xapi\local\statement\item_verb;
use core_xapi\local\statement\item_result;
use core_xapi\local\statement\item_attachment;
use core_xapi\local\statement\item_context;
use core_xapi\xapi_exception;
use JsonSerializable;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for core_xapi implementing null_provider.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statement implements JsonSerializable {

    /** @var item_actor The statement actor. */
    protected $actor = null;

    /** @var item_verb The statement verb. */
    protected $verb = null;

    /** @var item_object The statement object. */
    protected $object = null;

    /** @var item_result The statement result. */
    protected $result = null;

    /** @var item_context The statement context. */
    protected $context = null;

    /** @var string The statement timestamp. */
    protected $timestamp = null;

    /** @var string The statement stored. */
    protected $stored = null;

    /** @var authority The statement authority. */
    protected $authority = null;

    /** @var string The statement version. */
    protected $version = null;

    /** @var item_attachment[] The statement attachments. */
    protected $attachments = null;

    /** @var additionalfields list of additional fields. */
    private static $additionalsfields = [
        'timestamp', 'stored', 'version'
    ];

    /**
     * Function to create a full statement from xAPI statement data.
     *
     * @param stdClass $data the original xAPI statement
     * @return statement statement object
     */
    public static function create_from_data(stdClass $data): self {

        $result  = new self();

        $requiredfields = ['actor', 'verb', 'object'];
        foreach ($requiredfields as $required) {
            if (!isset($data->$required)) {
                throw new xapi_exception("Missing '{$required}'");
            }
        }
        $result->set_actor(item_actor::create_from_data($data->actor));
        $result->set_verb(item_verb::create_from_data($data->verb));
        $result->set_object(item_object::create_from_data($data->object));

        if (isset($data->result)) {
            $result->set_result(item_result::create_from_data($data->result));
        }

        if (!empty($data->attachments)) {
            if (!is_array($data->attachments)) {
                throw new xapi_exception("Attachments must be an array");
            }
            foreach ($data->attachments as $attachment) {
                $result->add_attachment(item_attachment::create_from_data($attachment));
            }
        }

        if (isset($data->context)) {
            $result->set_context(item_context::create_from_data($data->context));
        }

        if (isset($data->authority)) {
            $result->set_authority(item_actor::create_from_data($data->authority));
        }

        // Store other generic xAPI statement fields.
        foreach (self::$additionalsfields as $additional) {
            if (isset($data->$additional)) {
                $method = 'set_'.$additional;
                $result->$method($data->$additional);
            }
        }
        return $result;
    }

    /**
     * Return the data to serialize in case JSON statement is needed.
     *
     * @return stdClass the statement data structure
     */
    public function jsonSerialize(): stdClass {
        $result = (object) [
            'actor' => $this->actor,
            'verb' => $this->verb,
            'object' => $this->object,
        ];
        if (!empty($this->result)) {
            $result->result = $this->result;
        }
        if (!empty($this->context)) {
            $result->context = $this->context;
        }
        if (!empty($this->authority)) {
            $result->authority = $this->authority;
        }
        if (!empty($this->attachments)) {
            $result->attachments = $this->attachments;
        }
        foreach (self::$additionalsfields as $additional) {
            if (!empty($this->$additional)) {
                $result->$additional = $this->$additional;
            }
        }
        return $result;
    }

    /**
     * Returns a minified version of a given statement.
     *
     * The returned structure is suitable to store in the "other" field
     * of logstore. xAPI standard specifies a list of attributes that can be calculated
     * instead of stored literally. This function get rid of these attributes.
     *
     * Note: it also converts stdClass to assoc array to make it compatible
     * with "other" field in the logstore
     *
     * @return array the minimal statement needed to be stored a part from logstore data
     */
    public function minify(): ?array {
        $result = [];
        $fields = ['verb', 'object',  'context', 'result', 'authority', 'attachments'];
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $result[$field] = $this->$field;
            }
        }
        return json_decode(json_encode($result), true);
    }

    /**
     * Set the statement actor.
     *
     * @param item_actor $actor actor item
     */
    public function set_actor(item_actor $actor): void {
        $this->actor = $actor;
    }

    /**
     * Set the statement verb.
     *
     * @param item_verb $verb verb element
     */
    public function set_verb(item_verb $verb): void {
        $this->verb = $verb;
    }

    /**
     * Set the statement object.
     *
     * @param item_object $object compatible object item
     */
    public function set_object(item_object $object): void {
        $this->object = $object;
    }

    /**
     * Set the statement context.
     *
     * @param item_context $context context item element
     */
    public function set_context(item_context $context): void {
        $this->context = $context;
    }

    /**
     * Set the statement result.
     *
     * @param item_result $result result item element
     */
    public function set_result(item_result $result): void {
        $this->result = $result;
    }

    /**
     * Set the statement timestamp.
     *
     * @param string $timestamp timestamp element
     */
    public function set_timestamp(string $timestamp): void {
        $this->timestamp = $timestamp;
    }

    /**
     * Set the statement stored.
     *
     * @param string $stored stored element
     */
    public function set_stored(string $stored): void {
        $this->stored = $stored;
    }

    /**
     * Set the statement authority.
     *
     * @param item $authority authority item element
     */
    public function set_authority(item_actor $authority): void {
        $this->authority = $authority;
    }

    /**
     * Set the statement version.
     *
     * @param string $version version element
     */
    public function set_version(string $version): void {
        $this->version = $version;
    }

    /**
     * Adds and attachment to the statement.
     *
     * @param item $attachments attachments item element
     */
    public function add_attachment(item_attachment $attachment): void {
        if ($this->attachments === null) {
            $this->attachments = [];
        }
        $this->attachments[] = $attachment;
    }

    /**
     * Returns the moodle user represented by this statement actor.
     *
     * @throws xapi_exception if it's a group statement
     * @return stdClass user record
     */
    public function get_user(): stdClass {
        if (!$this->actor) {
            throw new xapi_exception("No actor defined");
        }
        return $this->actor->get_user();
    }

    /**
     * Return all moodle users represented by this statement actor.
     *
     * @return array user records
     */
    public function get_all_users(): array {
        if (!$this->actor) {
            throw new xapi_exception("No actor defined");
        }
        return $this->actor->get_all_users();
    }

    /**
     * Return the moodle group represented by this statement actor.
     *
     * @throws xapi_exception if it is not a group statement
     * @return stdClass a group record
     */
    public function get_group(): stdClass {
        if (!$this->actor) {
            throw new xapi_exception("No actor defined");
        }
        if (method_exists($this->actor, 'get_group')) {
            return $this->actor->get_group();
        }
        throw new xapi_exception("Method not valid on this actor");
    }

    /**
     * Returns the statement verb ID.
     *
     * @throws xapi_exception in case the item is no yet defined
     * @return string verb ID
     */
    public function get_verb_id(): string {
        if (!$this->verb) {
            throw new xapi_exception("No verb defined");
        }
        return $this->verb->get_id();
    }

    /**
     * Returns the statement activity ID.
     *
     * @throws xapi_exception in case the item is no yet defined
     * @return string activity ID
     */
    public function get_activity_id(): string {
        if (!$this->object) {
            throw new xapi_exception("No object defined");
        }
        if (method_exists($this->object, 'get_id')) {
            return $this->object->get_id();
        }
        throw new xapi_exception("Method not valid on this object");
    }

    /**
     * Return the statement actor if it is defined.
     *
     * @return item_actor|null
     */
    public function get_actor(): ?item_actor {
        return $this->actor;
    }

    /**
     * Return the statement verb if it is defined.
     *
     * @return item_verb|null
     */
    public function get_verb(): ?item_verb {
        return $this->verb;
    }

    /**
     * Return the statement object if it is defined.
     *
     * @return item_object|null
     */
    public function get_object(): ?item_object {
        return $this->object;
    }

    /**
     * Return the statement context if it is defined.
     *
     * @return item|null
     */
    public function get_context(): ?item_context {
        return $this->context;
    }

    /**
     * Return the statement result if it is defined.
     *
     * @return item|null
     */
    public function get_result(): ?item_result {
        return $this->result;
    }

    /**
     * Return the statement timestamp if it is defined.
     *
     * @return string|null
     */
    public function get_timestamp(): ?string {
        return $this->timestamp;
    }

    /**
     * Return the statement stored if it is defined.
     *
     * @return string|null
     */
    public function get_stored(): ?string {
        return $this->stored;
    }

    /**
     * Return the statement authority if it is defined.
     *
     * @return item|null
     */
    public function get_authority(): ?item_actor {
        return $this->authority;
    }

    /**
     * Return the statement version if it is defined.
     *
     * @return string|null
     */
    public function get_version(): ?string {
        return $this->version;
    }

    /**
     * Return the statement attachments if it is defined.
     *
     * @return item_attachment[]|null
     */
    public function get_attachments(): ?array {
        return $this->attachments;
    }
}
