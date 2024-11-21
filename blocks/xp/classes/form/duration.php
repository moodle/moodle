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
 * Duration element.
 *
 * Heavily based on the duration element from the plugin local_mootivated.
 *
 * @see        https://moodle.org/plugins/local_mootivated
 * @see        http://mootivated.com/
 * @package    block_xp
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/duration.php');

/**
 * Duration element class.
 *
 * We cannot use namespaces because formslib sucks.
 *
 * @package    block_xp
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_form_duration extends \MoodleQuickForm_duration {

    /** @var array Options. */
    protected $_options = ['defaultunit' => 60, 'maxunit' => HOURSECS, 'optional' => false];
    /** @var array The units. */
    protected $_units;

    /**
     * Real constructor.
     *
     * @param string $elementname Name.
     * @param string $elementlabel Label.
     * @param array $options Options.
     * @param array $attributes Attributes.
     */
    public function __construct($elementname = null, $elementlabel = null, $options = [], $attributes = null) {
        if (isset($options['maxunit'])) {
            $this->_options['maxunit'] = $options['maxunit'];
        }
        \MoodleQuickForm_duration::__construct($elementname, $elementlabel, $options, $attributes);
    }

    /**
     * Ugly constructor override...
     *
     * @param string $elementname Name.
     * @param string $elementlabel Label.
     * @param array $options Options.
     * @param array $attributes Attributes.
     */
    public function block_xp_form_duration($elementname = null, $elementlabel = null, $options = [], $attributes = null) {
        if (isset($options['maxunit'])) {
            $this->_options['maxunit'] = $options['maxunit'];
        }
        $this->MoodleQuickForm_duration($elementname, $elementlabel, $options, $attributes);
    }

    /**
     * Returns time associative array of unit length.
     *
     * @return array unit length in seconds => string unit name.
     */
    public function get_units() {
        if (is_null($this->_units)) {
            $units = [
                1 => get_string('seconds'),
                60 => get_string('minutes'),
                3600 => get_string('hours'),
                86400 => get_string('days'),
                604800 => get_string('weeks'),
            ];
            $this->_units = array_reduce(array_keys($units), function($carry, $key) use ($units) {
                if ($key <= $this->_options['maxunit']) {
                    $carry[$key] = $units[$key];
                }
                return $carry;
            }, []);
        }
        return $this->_units;
    }

    /**
     * Converts seconds to the best possible time unit.
     *
     * We override this to allow for the units to be displayed from smallest to largest.
     * Doing so required us to reverse the order in which we loop through the units array.
     *
     * @param int $seconds an amout of time in seconds.
     * @return array associative array ($number => $unit)
     */
    public function seconds_to_unit($seconds) {
        if (!$seconds) {
            return [0, $this->_options['defaultunit']];
        }
        $units = $this->get_units();
        krsort($units);
        foreach ($units as $unit => $notused) {
            if (fmod($seconds, $unit) == 0) {
                return [$seconds / $unit, $unit];
            }
        }
        return [$seconds, 1];
    }

}

// Auto register the element.
MoodleQuickForm::registerElementType('block_xp_form_duration', __FILE__, 'block_xp_form_duration');
