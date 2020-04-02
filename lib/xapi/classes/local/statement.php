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

    /** @var actor The statement actor. */
    protected $actor = null;

    /** @var verb The statement verb. */
    protected $verb = null;

    /** @var object The statement object. */
    protected $object = null;

    /** @var result The statement result. */
    protected $result = null;

    /** @var context The statement context. */
    protected $context = null;

    /** @var timestamp The statement timestamp. */
    protected $timestamp = null;

    /** @var stored The statement stored. */
    protected $stored = null;

    /** @var authority The statement authority. */
    protected $authority = null;

    /** @var version The statement version. */
    protected $version = null;

    /** @var attachments The statement attachments. */
    protected $attachments = null;

    /** @var additionalfields list of additional fields. */
    private static $additionalsfields = [
        'context', 'result', 'timestamp', 'stored', 'authority', 'version', 'attachments'
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

        // Store other generic xAPI statement fields.
        foreach (self::$additionalsfields as $additional) {
            if (isset($data->$additional)) {
                $method = 'set_'.$additional;
                $result->$method(item::create_from_data($data->$additional));
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
     * @param item $context context item element
     */
    public function set_context(item $context): void {
        $this->context = $context;
    }

    /**
     * Set the statement result.
     *
     * @param item $result result item element
     */
    public function set_result(item $result): void {
        $this->result = $result;
    }

    /**
     * Set the statement timestamp.
     *
     * @param item $timestamp timestamp item element
     */
    public function set_timestamp(item $timestamp): void {
        $this->timestamp = $timestamp;
    }

    /**
     * Set the statement stored.
     *
     * @param item $stored stored item element
     */
    public function set_stored(item $stored): void {
        $this->stored = $stored;
    }

    /**
     * Set the statement authority.
     *
     * @param item $authority authority item element
     */
    public function set_authority(item $authority): void {
        $this->authority = $authority;
    }

    /**
     * Set the statement version.
     *
     * @param item $version version item element
     */
    public function set_version(item $version): void {
        $this->version = $version;
    }

    /**
     * Set the statement attachments.
     *
     * @param item $attachments attachments item element
     */
    public function set_attachments(item $attachments): void {
        $this->attachments = $attachments;
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
    public function get_context(): ?item {
        return $this->context;
    }

    /**
     * Return the statement result if it is defined.
     *
     * @return item|null
     */
    public function get_result(): ?item {
        return $this->result;
    }

    /**
     * Return the statement timestamp if it is defined.
     *
     * @return item|null
     */
    public function get_timestamp(): ?item {
        return $this->timestamp;
    }

    /**
     * Return the statement stored if it is defined.
     *
     * @return item|null
     */
    public function get_stored(): ?item {
        return $this->stored;
    }

    /**
     * Return the statement authority if it is defined.
     *
     * @return item|null
     */
    public function get_authority(): ?item {
        return $this->authority;
    }

    /**
     * Return the statement version if it is defined.
     *
     * @return item|null
     */
    public function get_version(): ?item {
        return $this->version;
    }

    /**
     * Return the statement attachments if it is defined.
     *
     * @return item|null
     */
    public function get_attachments(): ?item {
        return $this->attachments;
    }
}
