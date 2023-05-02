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
 * Calendar form element. Provides checkbox to enable/disable calendar and options for sync behavior.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\calsync\form\element;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->libdir/form/advcheckbox.php");

/**
 * Calendar form element. Provides checkbox to enable/disable calendar and options for sync behavior.
 */
class calendar extends \HTML_QuickForm_advcheckbox {
    /** @var bool Whether the calendar is checked (subscribed) or not. */
    protected $checked = false;

    /** @var string The o365 calendar id. */
    protected $syncwith = null;

    /** @var string Sync behaviour: in/out/both. */
    protected $syncbehav = 'out';

    /** @var string html for help button, if empty then no help will icon will be displayed. */
    public $_helpbutton = '';

    /**
     * Constructor, accessed through __call constructor workaround.
     *
     * @param string $elementName The name of the element.
     * @param string $elementLabel The label of the element.
     * @param string $text Text that appears after the checkbox.
     * @param array $attributes Array of checkbox attributes.
     * @param array $customdata Array of form custom data.
     */
    public function calendarconstruct($elementName = null, $elementLabel = null, $text = null, $attributes = null,
        $customdata = []) {
        parent::__construct($elementName, $elementLabel, $text, $attributes, null);
        $this->customdata = $customdata;
        $this->_type = 'advcheckbox';
    }

    /**
     * Constructor.
     *
     * @param string $elementName The name of the element.
     * @param string $elementLabel The label of the element.
     * @param string $text Text that appears after the checkbox.
     * @param array $attributes Array of checkbox attributes.
     * @param array $customdata Array of form custom data.
     */
    public function __construct($elementName = null, $elementLabel = null, $text = null, $attributes = null, $customdata = []) {
        parent::__construct($elementName, $elementLabel, $text, $attributes, null);
        $this->customdata = $customdata;
    }

    /**
     * Magic method to run the proper constructor since formslib uses named constructors.
     *
     * @param string $method The method called.
     * @param array $arguments Array of arguments used in call.
     */
    public function __call($method, $arguments) {
        if ($method === 'local_o365\feature\calsync\form\element\calendar') {
            $func = [$this, 'calendarconstruct'];
            call_user_func_array($func, $arguments);
        }
    }

    /**
     * Set element value.
     *
     * @param array $value Array of information to set.
     */
    public function setValue($value) {
        if (!empty($value['checked'])) {
            $this->checked = true;
        }
        if (!empty($value['syncwith'])) {
            $this->syncwith = $value['syncwith'];
        }
        if (!empty($value['syncbehav'])) {
            $this->syncbehav = $value['syncbehav'];
        }
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * Export value for the element.
     *
     * @param array $submitValues Array of all submitted values.
     * @param bool $assoc
     * @return array Exported value.
     */
    public function exportValue(&$submitValues, $assoc = false) {
        $value = $this->_findValue($submitValues);
        return $this->_prepareValue($value, $assoc);
    }

    /**
     * Returns HTML for calendar form element.
     *
     * @return string The element HTML.
     */
    public function toHtml() {
        global $SITE;
        $checkboxid = $this->getAttribute('id').'_checkbox';
        $checkboxname = $this->getName().'[checked]';
        $checkboxchecked = ($this->checked === true) ? 'checked="checked"' : '';
        $checkboxonclick = 'if($(this).is(\':checked\')){$(this).parent().siblings().show();}else{$(this).parent().siblings()'.
            '.hide();}';
        $html = '<div>';
        $html .= '<input type="checkbox" name="'.$checkboxname.'" onclick="'.$checkboxonclick.'" id="'.$checkboxid.'" '.
            $checkboxchecked.'/>';
        $html .= \html_writer::label($this->_text, $checkboxid);
        $html .= '</div>';

        $showcontrols = ($this->checked === true) ? 'display:block;' : 'display:none;';
        $stylestr = 'margin-left: 2rem;'.$showcontrols;

        $availableo365calendars = (isset($this->customdata['o365calendars'])) ? $this->customdata['o365calendars'] : [];
        $availcalid = $this->getAttribute('id').'_syncwith';
        $availcalname = $this->getName().'[syncwith]';
        $html .= '<div style="'.$stylestr.'">';
        $html .= \html_writer::label(get_string('ucp_syncwith_title', 'local_o365'), $availcalid);
        $calselectopts = [];
        foreach ($availableo365calendars as $i => $info) {
            $calselectopts[$info['id']] = $info['name'];
        }
        if (empty($this->syncwith)) {
            $selectedoption = array_search($SITE->fullname, $calselectopts);
            $html .= \html_writer::select($calselectopts, $availcalname, $selectedoption, false, ['id' => $availcalid]);
        } else {
            $html .= \html_writer::select($calselectopts, $availcalname, $this->syncwith, false, ['id' => $availcalid]);
        }
        $html .= '</div>';

        $syncbehavior = [
            'out' => get_string('ucp_syncdir_out', 'local_o365'),
        ];
        if ($this->customdata['cansyncin'] === true) {
            $syncbehavior['in'] = get_string('ucp_syncdir_in', 'local_o365');
            $syncbehavior['both'] = get_string('ucp_syncdir_both', 'local_o365');
        }
        $syncbehavid = $this->getAttribute('id').'_syncbehav';
        $syncbehavname = $this->getName().'[syncbehav]';
        $html .= '<div style="'.$stylestr.'">';
        $html .= \html_writer::label(get_string('ucp_syncdir_title', 'local_o365'), $syncbehavid);
        $html .= \html_writer::select($syncbehavior, $syncbehavname, $this->syncbehav, false, ['id' => $syncbehavid]);
        $html .= '</div>';

        return $html;
    }
}
