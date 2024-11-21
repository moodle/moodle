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
 * Items per time element.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/group.php');

/**
 * Items per time element class.
 *
 * This form element allows the selection of a number of items for a time frame.
 * Note that a 0 value in either the time or the items number is considered to
 * be equivalent to not enabling this setting. This is not the best way to handle
 * this, null would be better maybe, but hey, it works for us...
 *
 * We cannot use namespaces because formslib sucks.
 *
 * Also, we had to copy part of the form duration class because extending or, or including
 * it for that matter, is making my life impossible so... never mind.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_form_itemspertime extends \MoodleQuickForm_group {

    /** @var array The units. */
    protected $_units;
    /** @var array Options. */
    protected $_options = ['defaultunit' => 60, 'maxunit' => 604800, 'itemlabel' => null, 'optional' => false];
    /** @var object The duration element. */
    protected $duration;

    /**
     * Real constructor.
     *
     * @param string $elementname Name.
     * @param string $elementlabel Label.
     * @param array $options Options.
     * @param array $attributes Attributes.
     */
    public function __construct($elementname = null, $elementlabel = null, $options = [], $attributes = null) {
        $options = (array) $options;
        $this->_options = array_merge($this->_options, $options);
        \MoodleQuickForm_group::__construct($elementname, $elementlabel, $attributes);
    }

    /**
     * Ugly constructor override...
     *
     * @param string $elementname Name.
     * @param string $elementlabel Label.
     * @param array $options Options.
     * @param array $attributes Attributes.
     */
    public function block_xp_form_itemspertime($elementname = null, $elementlabel = null, $options = [], $attributes = null) {
        $options = (array) $options;
        $this->_options = array_merge($this->_options, $options);
        $this->MoodleQuickForm_group($elementname, $elementlabel, $attributes);
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

    /**
     * Create the elements.
     *
     * @return void
     */
    function _createElements() { // @codingStandardsIgnoreLine
        $this->_elements = [];

        $item = $this->my_create_element('text', 'points', $this->_options['itemlabel'], [
            'size' => 4,
            'placeholder' => !empty($this->_options['itemlabel']) ? $this->_options['itemlabel'] : null,
        ]);
        if (method_exists($item, 'set_force_ltr')) {
            $item->set_force_ltr(true);
        }
        $this->_elements[] = $item;

        $in = $this->my_create_element('static', '', '', get_string('pointsintimelinker', 'block_xp'));
        $this->_elements[] = $in;

        $time = $this->my_create_element('text', 'time', get_string('time', 'form'), [
            'size' => 3,
        ]);
        if (method_exists($time, 'set_force_ltr')) {
            $time->set_force_ltr(true);
        }
        $this->_elements[] = $time;

        $timeunit = $this->my_create_element('select', 'timeunit', get_string('timeunit', 'form'), $this->get_units(), []);
        $this->_elements[] = $timeunit;

        if ($this->_options['optional']) {
            $optional = $this->my_create_element('checkbox', 'enabled', null, get_string('enable'), []);
            $this->_elements[] = $optional;
        }

        foreach ($this->_elements as $element) {
            if (method_exists($element, 'setHiddenLabel')) {
                $element->setHiddenLabel(true);
            }
        }
    }

    /**
     * Backwards compatibility createElement function.
     *
     * @return element
     */
    public function my_create_element() {
        if (method_exists($this, 'createFormElement')) {
            return call_user_func_array([$this, 'createFormElement'], func_get_args());
        } else {
            return @call_user_func_array('MoodleQuickForm::createElement', func_get_args());
        }
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller) { // @codingStandardsIgnoreLine
        if (method_exists($this, 'setMoodleForm')) {
            $this->setMoodleForm($caller);
        }
        switch ($event) {
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    if ($caller->isSubmitted()) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }

                $finalval = null;
                if (!is_array($value)) {
                    $finalval = ['enabled' => false];

                } else {
                    $finalval = [
                        'points' => isset($value['points']) ? max(0, $value['points']) : 0,
                        'enabled' => !isset($value['enabled']) || !empty($value['enabled']),
                    ];
                    if (!empty($value['time'])) {
                        if (!is_array($value['time'])) {
                            list($time, $timeunit) = $this->seconds_to_unit($value['time']);
                            $finalval += [
                                'time' => $time,
                                'timeunit' => $timeunit,
                            ];
                        } else {
                            $finalval += $value['time'];
                        }
                    }

                    $finalval['time'] = max(0, isset($finalval['time']) ? $finalval['time'] : 0);
                    if ($this->_options['optional'] && $finalval['time'] < 1 || $finalval['points'] < 1) {
                        $finalval['enabled'] = false;
                    }
                    if (!$finalval['points'] && !empty($this->_options['itemlabel'])) {
                        // We prefer displaying the placeholder than 0.
                        $finalval['points'] = null;
                    }
                }

                if ($finalval !== null) {
                    $this->setValue($finalval);
                }
                break;

            case 'createElement':
                if ($this->_options['optional']) {
                    $caller->disabledIf($arg[0], $arg[0] . '[enabled]');
                }
                $caller->setType($arg[0] . '[points]', PARAM_INT);
                $caller->setType($arg[0] . '[time]', PARAM_INT);
                $caller->setType($arg[0] . '[timeunit]', PARAM_INT);
                return parent::onQuickFormEvent($event, $arg, $caller);
                break;

            default:
                return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    /**
     * Output a timestamp. Give it the name of the group.
     * Override of standard quickforms method.
     *
     * @param  array $submitvalues
     * @param  bool  $notused Not used.
     * @return array field name => value. The value is the time interval in seconds.
     */
    function exportValue(&$submitvalues, $notused = false) { // @codingStandardsIgnoreLine
        // Get the values from all the child elements.
        $values = [];
        foreach ($this->_elements as $element) {
            $thisexport = $element->exportValue($submitvalues[$this->getName()], true);
            if ($thisexport !== null) {
                $values += $thisexport;
            }
        }

        // Convert the value to an integer number of seconds.
        if (empty($values)) {
            return [$this->getName() => ['time' => 0, 'points' => 0]];
        }

        // The thing is disabled.
        if ($this->_options['optional'] && empty($values['enabled'])) {
            return [$this->getName() => ['time' => 0, 'points' => 0]];
        }

        return [$this->getName() => [
            'time' => max(0, $values['time'] * $values['timeunit']),
            'points' => max(0, (int) $values['points']),
        ], ];
    }
}

// Auto register the element.
MoodleQuickForm::registerElementType('block_xp_form_itemspertime', __FILE__, 'block_xp_form_itemspertime');
