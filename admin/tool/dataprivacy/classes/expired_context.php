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
 * Class that represents an expired context.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;
use dml_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that represents an expired context.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expired_context extends \core\persistent {

    /**
     * Database table.
     */
    const TABLE = 'tool_dataprivacy_ctxexpired';

    /**
     * Expired contexts with no delete action scheduled.
     */
    const STATUS_EXPIRED = 0;

    /**
     * Expired contexts approved for deletion.
     */
    const STATUS_APPROVED = 1;

    /**
     * Already processed expired contexts.
     */
    const STATUS_CLEANED = 2;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'contextid' => [
                'type' => PARAM_INT,
                'description' => 'The context id.',
            ],
            'defaultexpired' => [
                'type' => PARAM_INT,
                'description' => 'Whether to default retention period for the purpose has been reached',
                'default' => 1,
            ],
            'expiredroles' => [
                'type' => PARAM_TEXT,
                'description' => 'This list of roles to include during deletion',
                'default'  => '',
            ],
            'unexpiredroles' => [
                'type' => PARAM_TEXT,
                'description' => 'This list of roles to exclude during deletion',
                'default'  => '',
            ],
            'status' => [
                'choices' => [
                    self::STATUS_EXPIRED,
                    self::STATUS_APPROVED,
                    self::STATUS_CLEANED,
                ],
                'type' => PARAM_INT,
                'description' => 'The deletion status of the context.',
            ],
        ];
    }

    /**
     * Returns expired_contexts instances that match the provided level and status.
     *
     * @param int $contextlevel The context level filter criterion.
     * @param bool $status The expired context record's status.
     * @param string $sort The sort column. Must match the column name in {tool_dataprivacy_ctxexpired} table
     * @param int $offset The query offset.
     * @param int $limit The query limit.
     * @return expired_context[]
     * @throws dml_exception
     */
    public static function get_records_by_contextlevel($contextlevel = null, $status = false, $sort = 'timecreated',
                                                       $offset = 0, $limit = 0) {
        global $DB;

        $sql = "SELECT expiredctx.*
                  FROM {" . self::TABLE . "} expiredctx
                  JOIN {context} ctx
                    ON ctx.id = expiredctx.contextid";
        $params = [];
        $conditions = [];

        if (!empty($contextlevel)) {
            $conditions[] = "ctx.contextlevel = :contextlevel";
            $params['contextlevel'] = intval($contextlevel);
        }

        if ($status !== false) {
            $conditions[] = "expiredctx.status = :status";
            $params['status'] = intval($status);
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY expiredctx.{$sort}";

        $records = $DB->get_records_sql($sql, $params, $offset, $limit);

        // We return class instances.
        $instances = array();
        foreach ($records as $key => $record) {
            $instances[$key] = new static(0, $record);
        }

        return $instances;
    }

    /**
     * Returns the number of expired_contexts instances that match the provided level and status.
     *
     * @param int $contextlevel
     * @param bool $status
     * @return int
     * @throws dml_exception
     */
    public static function get_record_count_by_contextlevel($contextlevel = null, $status = false) {
        global $DB;

        $sql = "SELECT COUNT(1)
                  FROM {" . self::TABLE . "} expiredctx
                  JOIN {context} ctx
                    ON ctx.id = expiredctx.contextid";

        $conditions = [];
        $params = [];

        if (!empty($contextlevel)) {
            $conditions[] = "ctx.contextlevel = :contextlevel";
            $params['contextlevel'] = intval($contextlevel);
        }

        if ($status !== false) {
            $sql .= " AND expiredctx.status = :status";
            $params['status'] = intval($status);
        }
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Set the list of role IDs for either expiredroles, or unexpiredroles.
     *
     * @param   string  $field
     * @param   int[]   $roleids
     * @return  expired_context
     */
    protected function set_roleids_for(string $field, array $roleids) : expired_context {
        $roledata = json_encode($roleids);

        $this->raw_set($field, $roledata);

        return $this;
    }

    /**
     * Get the list of role IDs for either expiredroles, or unexpiredroles.
     *
     * @param   string  $field
     * @return  int[]
     */
    protected function get_roleids_for(string $field) {
        $value = $this->raw_get($field);
        if (empty($value)) {
            return [];
        }

        return json_decode($value);
    }

    /**
     * Set the list of unexpired role IDs.
     *
     * @param   int[]   $roleids
     * @return  expired_context
     */
    protected function set_unexpiredroles(array $roleids) : expired_context {
        $this->set_roleids_for('unexpiredroles', $roleids);

        return $this;
    }

    /**
     * Add a set of role IDs to the list of expired role IDs.
     *
     * @param   int[]   $roleids
     * @return  expired_context
     */
    public function add_expiredroles(array $roleids) : expired_context {
        $existing = $this->get('expiredroles');
        $newvalue = array_merge($existing, $roleids);

        $this->set('expiredroles', $newvalue);

        return $this;
    }

    /**
     * Add a set of role IDs to the list of unexpired role IDs.
     *
     * @param   int[]   $roleids
     * @return  unexpired_context
     */
    public function add_unexpiredroles(array $roleids) : expired_context {
        $existing = $this->get('unexpiredroles');
        $newvalue = array_merge($existing, $roleids);

        $this->set('unexpiredroles', $newvalue);

        return $this;
    }

    /**
     * Set the list of expired role IDs.
     *
     * @param   int[]   $roleids
     * @return  expired_context
     */
    protected function set_expiredroles(array $roleids) : expired_context {
        $this->set_roleids_for('expiredroles', $roleids);

        return $this;
    }

    /**
     * Get the list of expired role IDs.
     *
     * @return  int[]
     */
    protected function get_expiredroles() {
        return $this->get_roleids_for('expiredroles');
    }

    /**
     * Get the list of unexpired role IDs.
     *
     * @return  int[]
     */
    protected function get_unexpiredroles() {
        return $this->get_roleids_for('unexpiredroles');
    }

    /**
     * Create a new expired_context based on the context, and expiry_info object.
     *
     * @param   \context        $context
     * @param   expiry_info     $info
     * @param   boolean         $save
     * @return  expired_context
     */
    public static function create_from_expiry_info(\context $context, expiry_info $info, bool $save = true) : expired_context {
        $record = (object) [
            'contextid' => $context->id,
            'status' => self::STATUS_EXPIRED,
            'defaultexpired' => (int) $info->is_default_expired(),
        ];

        $expiredcontext = new static(0, $record);
        $expiredcontext->set('expiredroles', $info->get_expired_roles());
        $expiredcontext->set('unexpiredroles', $info->get_unexpired_roles());

        if ($save) {
            $expiredcontext->save();
        }

        return $expiredcontext;
    }

    /**
     * Update the expired_context from an expiry_info object which relates to this context.
     *
     * @param   expiry_info     $info
     * @return  $this
     */
    public function update_from_expiry_info(expiry_info $info) : expired_context {
        $save = false;

        // Compare the expiredroles.
        $thisexpired = $this->get('expiredroles');
        $infoexpired = $info->get_expired_roles();

        sort($thisexpired);
        sort($infoexpired);
        if ($infoexpired != $thisexpired) {
            $this->set('expiredroles', $infoexpired);
            $save = true;
        }

        // Compare the unexpiredroles.
        $thisunexpired = $this->get('unexpiredroles');
        $infounexpired = $info->get_unexpired_roles();

        sort($thisunexpired);
        sort($infounexpired);
        if ($infounexpired != $thisunexpired) {
            $this->set('unexpiredroles', $infounexpired);
            $save = true;
        }

        if (empty($this->get('defaultexpired')) == $info->is_default_expired()) {
            $this->set('defaultexpired', (int) $info->is_default_expired());
            $save = true;
        }

        if ($save) {
            $this->set('status', self::STATUS_EXPIRED);
            $this->save();
        }

        return $this;

    }

    /**
     * Check whether this expired_context record is in a state ready for deletion to actually take place.
     *
     * @return  bool
     */
    public function can_process_deletion() : bool {
        return ($this->get('status') == self::STATUS_APPROVED);
    }

    /**
     * Check whether this expired_context record has already been cleaned.
     *
     * @return  bool
     */
    public function is_complete() : bool {
        return ($this->get('status') == self::STATUS_CLEANED);
    }

    /**
     * Whether this context has 'fully' expired.
     * That is to say that the default retention period has been reached, and that there are no unexpired roles.
     *
     * @return  bool
     */
    public function is_fully_expired() : bool {
        return $this->get('defaultexpired') && empty($this->get('unexpiredroles'));
    }
}
