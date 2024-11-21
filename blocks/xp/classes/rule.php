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
 * Rule interface.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Rule interface.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class block_xp_rule implements renderable {

    /**
     * Create a ruleset object from exported data.
     *
     * This method helps restoring a tree of rules without having to check
     * what the first rule is. Simply call block_xp_rule::create($properties).
     *
     * This then calls the method {@see self::import()}.
     *
     * @param array $properties Array of properties acquired from {@see self::export()}.
     * @return block_xp_rule|false The rule object.
     */
    public static function create(array $properties) {
        $classname = $properties['_class'];
        if (!class_exists($classname) || !is_subclass_of($classname, 'block_xp_rule')) {
            return false;
        }
        $class = new $classname();
        unset($properties['_class']);
        $class->import($properties);
        return $class;
    }

    /**
     * Returns a string describing the rule.
     *
     * @return string
     */
    abstract public function get_description();

    /**
     * Returns a form element for this rule.
     *
     * This MUST be extended, and this MUST be called.
     *
     * @param string $basename The form element base name.
     * @return string
     */
    public function get_form($basename) {
        return html_writer::empty_tag('input', ['type' => 'hidden', 'name' => $basename . '[_class]',
            'value' => get_class($this), ]);
    }

    /**
     * Get the renderer.
     *
     * Somes rules seem to be making use of the renderer, but the renderer should
     * not be initialised with the object, so we it's best that we provide a
     * method to get the renderer instead of letting each rule decide of the best
     * way to load it.
     *
     * @return renderer_base
     */
    protected function get_renderer() {
        return \block_xp\di::get('renderer');
    }

    /**
     * Export the properties and their values.
     *
     * This must return all the values required by the {@see self::import()} method.
     * It also must include the key '_class'.
     *
     * You will have to override this method to add more data, and handle special keys.
     *
     * @return array Keys are properties, values are the values.
     */
    public function export() {
        return ['_class' => get_class($this)];
    }

    /**
     * Re-import the values that were exported.
     *
     * This should not be called directly, use {@see self::create()} instead.
     *
     * Override this method to handle special keys.
     *
     * @param array $properties Properties.
     * @return void
     */
    protected function import(array $properties) {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Does the $subject match the rules.
     *
     * @param mixed $subject The subject of the comparison.
     * @return bool Whether or not it matches.
     */
    abstract public function match($subject);

    /**
     * Update the rule after a restore.
     *
     * @param string $restoreid The restore ID.
     * @param int $courseid The course ID.
     * @param base_logger $logger The logger.
     * @return void
     */
    public function update_after_restore($restoreid, $courseid, base_logger $logger) {
    }

    /**
     * Validate the data.
     *
     * @param array $data The data to validate.
     * @return bool
     */
    public static function validate_data($data) {
        $valid = true;

        foreach ($data as $key => $value) {
            if (!$valid) {
                break;
            }

            if ($key === '_class') {
                $reflexion = new ReflectionClass($value);
                $valid = $reflexion->isSubclassOf('block_xp_rule');
            } else if (is_array($value)) {
                $valid = self::validate_data($value);
            }
        }

        return $valid;
    }

}
