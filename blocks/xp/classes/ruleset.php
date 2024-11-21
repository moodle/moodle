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
 * Ruleset.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Ruleset class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_ruleset extends block_xp_rule {

    /** All rules should match. */
    const ALL = 'all';
    /** Any rule should match. */
    const ANY = 'any';
    /** None should match. */
    const NONE = 'none';

    /**
     * What is the method of this ruleset.
     *
     * Accepts any constant value.
     *
     * @var bool
     */
    protected $method;

    /**
     * The rules to compare against.
     *
     * Those must implement the interface block_xp_rule.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Constructor.
     *
     * Read the parameters as follow:
     *  - Subject must be $compare'd with $value.
     *  - Subject must be equal to $value.
     *  - Subject must be lower than $value.
     *  - Subject must match regex $value.
     *
     * @param array $rules The rules to add in this set.
     * @param string $method The method.
     */
    public function __construct(array $rules = [], $method = self::ANY) {
        $this->rules = $rules;
        $this->method = $method;
    }

    /**
     * Add a rule to the rule stack.
     *
     * @param block_xp_rule $rule The rule to add.
     */
    public function add_rule(block_xp_rule $rule) {
        $this->rules[] = $rule;
    }

    /**
     * Returns a string describing the rule.
     *
     * @return string
     */
    public function get_description() {
        return get_string('ruleset:' . $this->method, 'block_xp');
    }

    /**
     * Returns a form element for this rule.
     *
     * @param string $basename The form element base name.
     * @return string
     */
    public function get_form($basename) {
        $o = parent::get_form($basename);
        $o .= html_writer::select([
            self::ALL => get_string('ruleset:all', 'block_xp'),
            self::ANY => get_string('ruleset:any', 'block_xp'),
            self::NONE => get_string('ruleset:none', 'block_xp'),
        ], $basename . '[method]', $this->method, '', ['class' => '', 'id' => '']);
        return $o;
    }

    /**
     * Returns the rules in this set.
     *
     * @return block_xp_rule[]
     */
    public function get_rules() {
        return $this->rules;
    }

    /**
     * Export the properties and their values.
     *
     * @return array Keys are properties, values are the values.
     */
    public function export() {
        $properties = parent::export();
        $properties['method'] = $this->method;
        $properties['rules'] = [];
        foreach ($this->rules as $rule) {
            $properties['rules'][] = $rule->export();
        }
        return $properties;
    }

    /**
     * Import the properties.
     *
     * @param array $properties Array of properties acquired from {@see self::export()}.
     * @return exportable
     */
    protected function import(array $properties) {
        if (isset($properties['rules'])) {
            $ruleslist = [];
            foreach ($properties['rules'] as $rule) {
                $ruleobject = block_xp_rule::create($rule);
                if ($ruleobject) {
                    $ruleslist[] = $ruleobject;
                }
            }
            $this->rules = $ruleslist;
        }
        unset($properties['rules']);
        parent::import($properties);
    }

    /**
     * Test the subject against the rules.
     *
     * @param mixed $subject The subject.
     * @return boolean If it meets the condition.
     */
    public function match($subject) {
        if (empty($this->rules)) {
            return true;
        }
        $method = 'match_' . $this->method;
        return $this->$method($subject);
    }

    /**
     * Check that all the rules match the subject.
     *
     * @param mixed $subject The subject.
     * @return boolean If it meets the condition.
     */
    protected function match_all($subject) {
        foreach ($this->rules as $rule) {
            if (!$rule->match($subject)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check that any of the rules match the subject.
     *
     * @param mixed $subject The subject.
     * @return boolean If it meets the condition.
     */
    protected function match_any($subject) {
        foreach ($this->rules as $rule) {
            if ($rule->match($subject)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check that none of the rules match the subject.
     *
     * @param mixed $subject The subject.
     * @return boolean If it meets the condition.
     */
    protected function match_none($subject) {
        foreach ($this->rules as $rule) {
            if ($rule->match($subject)) {
                return false;
            }
        }
        return true;
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
        foreach ($this->rules as $rule) {
            $rule->update_after_restore($restoreid, $courseid, $logger);
        }
    }
}
