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
 * Rule base.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Rule base class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class block_xp_rule_base extends block_xp_rule {

    /** Contains comparison. */
    const CT = 'contains';
    /** Equal comparison. */
    const EQ = 'eq';
    /** Equal strict comparison. */
    const EQS = 'eqs';
    /** Greater than comparison. */
    const GT = 'gt';
    /** Greater than or equal comparison. */
    const GTE = 'gte';
    /** Less than comparison. */
    const LT = 'lt';
    /** Less than or equal comparison. */
    const LTE = 'lte';
    /** Regex comparison. */
    const RX = 'regex';

    /**
     * The raw value to compare against.
     *
     * @var mixed
     */
    protected $value;

    /**
     * The constant value used as compare rule.
     *
     * @var string
     */
    protected $compare;

    /**
     * Constructor.
     *
     * Read the parameters as follow:
     *  - Subject must be $compare'd with $value.
     *  - Subject must be equal to $value.
     *  - Subject must be lower than $value.
     *  - Subject must match regex $value.
     *
     * @param string $compare Constant value.
     * @param mixed $value The value.
     */
    public function __construct($compare = self::EQ, $value = '') {
        $this->compare = $compare;
        $this->value = $value;
    }

    /**
     * Export the properties and their values.
     *
     * This must return all the values required by the {@see self::create()} method.
     *
     * @return array Keys are properties, values are the values.
     */
    public function export() {
        $properties = parent::export();
        $properties['compare'] = $this->compare;
        $properties['value'] = $this->value;
        return $properties;
    }

    /**
     * Return the compare select.
     *
     * @param string $basename
     * @return string
     */
    protected function get_compare_select($basename) {
        return html_writer::select([
                self::CT => get_string('rule:' . self::CT, 'block_xp'),
                self::EQ => get_string('rule:' . self::EQ, 'block_xp'),
            ], $basename . '[compare]', $this->compare, '', ['id' => '', 'class' => '']);
    }

    /**
     * Get the value to use during comparison.
     *
     * Override this method if your $value is a complex object.
     *
     * @return mixed The value to use.
     */
    protected function get_value() {
        return $this->value;
    }

    /**
     * Get the value to use during comparison from the subject.
     *
     * Override this method when the object passed by the user
     * needs to be converted into a suitable value.
     *
     * @param mixed $subject The subject.
     * @return mixed The value to use.
     */
    protected function get_subject_value($subject) {
        return $subject;
    }

    /**
     * Does the $subject match the rules.
     *
     * @param mixed $subject The subject of the comparison.
     * @return bool Whether or not it matches.
     */
    public function match($subject) {
        $subj = $this->get_subject_value($subject);
        $value = $this->get_value();
        $method = 'match_' . $this->compare;
        return $this->$method($subj, $value);
    }

    /**
     * Contains match.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_contains($subj, $value) {
        return strpos($subj, $value) !== false;
    }

    /**
     * Equal match.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_eq($subj, $value) {
        return $subj == $value;
    }

    /**
     * Equal strict match.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_eqs($subj, $value) {
        return $subj === $value;
    }

    /**
     * Greather than match.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_gt($subj, $value) {
        return $subj > $value;
    }

    /**
     * Greater than or equal match.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_gte($subj, $value) {
        return $subj >= $value;
    }

    /**
     * Lower than.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_lt($subj, $value) {
        return $subj < $value;
    }

    /**
     * Lower than or equal.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_lte($subj, $value) {
        return $subj <= $value;
    }

    /**
     * Regex match.
     *
     * @param mixed $subj The subject value.
     * @param mixed $value The value to compare with.
     * @return bool Whether or not it matches.
     */
    protected function match_regex($subj, $value) {
        return (bool) preg_match($value, $subj);
    }
}
