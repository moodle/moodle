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
        return array(
            'contextid' => array(
                'type' => PARAM_INT,
                'description' => 'The context id.',
            ),
            'status' => array(
                'choices' => [
                    self::STATUS_EXPIRED,
                    self::STATUS_APPROVED,
                    self::STATUS_CLEANED,
                ],
                'type' => PARAM_INT,
                'description' => 'The deletion status of the context.',
            ),
        );
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
     * Create a new expired_context based on the context, and expiry_info object.
     *
     * @param   \context        $context
     * @param   expiry_info     $info
     * @return  expired_context
     */
    public static function create_from_expiry_info(\context $context, expiry_info $info) : expired_context {
        $record = (object) [
            'contextid' => $context->id,
            'status' => self::STATUS_EXPIRED,
        ];

        $expiredcontext = new static(0, $record);
        $expiredcontext->save();

        return $expiredcontext;
    }

    /**
     * Update the expired_context from an expiry_info object which relates to this context.
     *
     * @param   expiry_info     $info
     * @return  $this
     */
    public function update_from_expiry_info(expiry_info $info) : expired_context {
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
}
