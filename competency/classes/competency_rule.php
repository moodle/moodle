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
 * Competency rule base.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use coding_exception;

/**
 * Competency rule base abstract class.
 *
 * Rules are attached to a competency and then tested against a user competency
 * to determine whether or not it matches.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class competency_rule {

    /** @var competency The competency. */
    protected $competency;

    /**
     * Constructor.
     *
     * @param  competency $competency The competency.
     */
    public function __construct(competency $competency) {
        $class = $competency->get_ruletype();
        if (!$class || !($this instanceof $class)) {
            throw new coding_exception('This competency does not use this rule.');
        }

        $this->competency = $competency;
    }

    /**
     * Get the rule config.
     *
     * @return mixed
     */
    protected function get_config() {
        return $this->competency->get_ruleconfig();
    }

    /**
     * Whether or not the rule is matched.
     *
     * @param user_competency $usercompetency The user competency to test against.
     * @return bool
     */
    abstract public function matches(user_competency $usercompetency);

    /**
     * Validate the rule config.
     *
     * @param string $value The value to validate.
     * @return bool
     */
    abstract public function validate_config($value);

    /**
     * The name of the rule.
     *
     * @return lang_string
     */
    public static function get_name() {
        throw new coding_exception('Method not implemented.');
    }

    /**
     * Migrate rule config from one set of competencies to another.
     *
     * Exceptions should be thrown when the migration can not be performed.
     *
     * @param string $config Original config rule of a competency.
     * @param array $mappings Array that matches the original competency IDs with the new competencies objects.
     * @return string New configuration.
     * @throws Exception
     */
    public static function migrate_config($config, $mappings) {
        return $config;
    }

}
