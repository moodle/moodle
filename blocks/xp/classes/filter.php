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
 * Filter.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Filter class.
 *
 * The filter only works with block_xp_rule_property, or rulesets containing them.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_filter implements renderable {

    /** The category for events filters. */
    const CATEGORY_EVENTS = 0;
    /** The category for grades filters. */
    const CATEGORY_GRADES = 1;

    /**
     * The course ID.
     *
     * @var int
     */
    protected $courseid;

    /**
     * Whether or not this filter is editable.
     *
     * This is not stored in the database, it is just a flag.
     *
     * @var bool
     */
    protected $editable = true;

    /**
     * The ID.
     *
     * @var int
     */
    protected $id;

    /**
     * Points for this filter.
     *
     * @var int
     */
    protected $points = 0;

    /**
     * Rule.
     *
     * This is not stored in the DB, it is constructed when needed.
     *
     * @var block_xp_rule
     */
    protected $rule;

    /**
     * The rule data.
     *
     * @var string
     */
    protected $ruledata = '';

    /**
     * The sort order.
     *
     * @var int
     */
    protected $sortorder = 0;

    /** @var int The category constant, defaults to CATEGORY_EVENTS */
    protected $category = 0;

    /**
     * Constructor.
     *
     * Use {@see self::load_from_data()} instead.
     */
    protected function __construct() {
    }

    /**
     * Delete the rule.
     *
     * @return void
     */
    public function delete() {
        global $DB;
        if (!$this->id) {
            throw new coding_exception('ID of the filter is unknown.');
        }
        $DB->delete_records('block_xp_filters', ['id' => $this->id]);
    }

    /**
     * Get the record data.
     *
     * @return stdClass
     */
    public function export() {
        $record = new stdClass();
        $record->courseid = $this->courseid;
        $record->points = $this->points;
        $record->ruledata = $this->ruledata;
        $record->sortorder = $this->sortorder;
        $record->category = $this->category;
        return $record;
    }

    /**
     * Return whether or not the filter is editable.
     *
     * @return boolean
     */
    public function is_editable() {
        return $this->editable;
    }

    /**
     * Return the category the filter belongs to.
     *
     * This is not a course category, this is a mean to use filters
     * for different purposes, but stored in the same table.
     *
     * @return int
     */
    public function get_category() {
        return $this->category;
    }

    /**
     * Return the ID.
     *
     * @return int|null
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Return the points.
     *
     * @return int points.
     */
    public function get_points() {
        return $this->points;
    }

    /**
     * Return the rule object.
     *
     * @return block_xp_rule
     */
    public function get_rule() {
        if (!$this->rule) {
            $this->load_rule();
        }
        return $this->rule;
    }

    /**
     * Return the sortorder.
     *
     * @return int
     */
    public function get_sortorder() {
        return $this->sortorder;
    }

    /**
     * Create the filter from data.
     *
     * Do not combine the keys 'rule' and 'ruledata' as it could lead to random behaviours.
     *
     * @param stdClass|array $record Information of the filter, from DB or not.
     * @return block_xp_filter The filter.
     */
    public static function load_from_data($record) {
        $filter = new static();
        $record = (array) $record;
        foreach ($record as $key => $value) {
            if ($key == 'rule') {
                $filter->set_rule($value);
                continue;
            }

            if ($key == 'points') {
                // Prevent negatives.
                $value = abs(intval($value));
            } else if ($key == 'sortorder') {
                // Int.
                $value = intval($value);
            } else if ($key == 'id') {
                // Null or int.
                $value = !empty($value) ? intval($value) : null;
            } else if ($key == 'courseid') {
                // Always int.
                $value = max(0, intval($value));
            } else if ($key == 'category') {
                // Must be a valid category.
                $value = intval($value);
                if (!in_array($value, [self::CATEGORY_EVENTS, self::CATEGORY_GRADES])) {
                    continue;
                }
            }

            $filter->$key = $value;
        }
        return $filter;
    }

    /**
     * Load the rule from {@see self::$ruledata}.
     *
     * @return void
     */
    protected function load_rule() {
        $ruledata = json_decode($this->ruledata, true);
        $this->rule = block_xp_rule::create($ruledata);

        // There was a problem loading the rule, let's ignore it and create an empty one.
        // Most likely the class used went missing, or someone played tricks on us.
        if ($this->rule === false) {
            $this->set_rule(new \block_xp_ruleset());
        }
    }

    /**
     * Does the event match the filter.
     *
     * @param \core\event\base $event The event.
     * @return bool Whether or not it matches.
     */
    public function match(\core\event\base $event) {
        if (!$this->rule) {
            $this->load_rule();
        }
        return $this->rule->match($event);
    }

    /**
     * Save the record to the database.
     *
     * @return block_xp_filter
     */
    public function save() {
        global $DB;
        if (!$this->editable) {
            throw new coding_exception('Non-editable filters cannot be saved.');
        }
        $record = $this->export();
        if (!$this->id) {
            $this->id = $DB->insert_record('block_xp_filters', $record);
        } else {
            $record->id = $this->id;
            $DB->update_record('block_xp_filters', $record);
        }
    }

    /**
     * Set the points.
     *
     * @param int $points
     */
    public function set_points($points) {
        $this->points = $points;
    }

    /**
     * Overrides the rule of the filter.
     *
     * @param block_xp_rule $rule
     */
    public function set_rule(block_xp_rule $rule) {
        $this->rule = $rule;
        $this->ruledata = json_encode($rule->export());
    }

    /**
     * Set the sortorder.
     *
     * @param int $sortorder
     */
    public function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
    }

    /**
     * Update after a restore.
     *
     * @param string $restoreid The restore ID.
     * @param int $courseid The course ID.
     * @param base_logger $logger The logger.
     * @return void
     */
    public function update_after_restore($restoreid, $courseid, base_logger $logger) {
        if (!$this->rule) {
            $this->load_rule();
        }

        $this->rule->update_after_restore($restoreid, $courseid, $logger);

        $newdata = json_encode($this->rule->export());
        if ($newdata !== $this->ruledata) {
            $this->ruledata = $newdata;
            $this->save();
        }
    }

    /**
     * Validate the data of this filter.
     *
     * @param array $data Data to validate.
     * @return bool
     */
    public static function validate_data($data) {
        $valid = true;

        // Empty values ("", "0", or 0) will be cast to int and will be 0 in self::load_from_data.
        // If the values are not set, they won't be cast and they property won't be assigned.
        if (!empty($data['courseid'])) {
            $valid = $valid && clean_param($data['courseid'], PARAM_INT) == $data['courseid'];
        }
        if (!empty($data['points'])) {
            $valid = $valid && clean_param($data['points'], PARAM_INT) == $data['points'];
        }
        if (!empty($data['sortorder'])) {
            $valid = $valid && clean_param($data['sortorder'], PARAM_INT) == $data['sortorder'];
        }
        if (!empty($data['id'])) {
            $valid = $valid && clean_param($data['id'], PARAM_INT) == $data['id'];
        }
        if (!empty($data['category'])) {
            $valid = $valid && in_array(clean_param($data['category'], PARAM_INT), [self::CATEGORY_EVENTS, self::CATEGORY_GRADES]);
        }

        if (isset($data['ruledata'])) {
            $ruledata = json_decode($data['ruledata'], true);
            $valid = $valid && $ruledata !== false;
            if ($valid) {
                $valid = $valid && self::validate_ruledata($ruledata);
            }
        }
        if (isset($data['rule'])) {
            throw new coding_exception('Validation for rule property is not implemented');
        }

        return $valid;
    }

    /**
     * Validate the rule data.
     *
     * @param array $ruledata Data to validate.
     * @return bool
     */
    protected static function validate_ruledata($ruledata) {
        $valid = true;
        foreach ($ruledata as $key => $value) {
            if (!$valid) {
                break;
            }

            if ($key == '_class') {
                $reflexion = new ReflectionClass($value);
                $valid = $reflexion->isSubclassOf('block_xp_rule');
            } else if (is_array($value)) {
                $valid = block_xp_rule::validate_data($value);
            }
        }
        return $valid;
    }

}
