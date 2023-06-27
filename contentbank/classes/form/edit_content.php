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
 * Provides {@see \core_contentbank\form\edit_content} class.
 *
 * @package    core_contentbank
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Defines the form for editing a content.
 *
 * @package    core_contentbank
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class edit_content extends moodleform {

    /** @var int Context the content belongs to. */
    protected $contextid;

    /** @var string Content type plugin name. */
    protected $plugin;

    /** @var int Content id in the content bank. */
    protected $id;

    /**
     * Constructor.
     *
     * @param string $action The action attribute for the form.
     * @param array $customdata Data to set during instance creation.
     * @param string $method Form method.
     */
    public function __construct(string $action = null, array $customdata = null, string $method = 'post') {
        parent::__construct($action, $customdata, $method);
        $this->contextid = $customdata['contextid'];
        $this->plugin = $customdata['plugin'];
        $this->id = $customdata['id'];

        $mform =& $this->_form;
        $mform->addElement('hidden', 'contextid', $this->contextid);
        $this->_form->setType('contextid', PARAM_INT);

        $mform->addElement('hidden', 'plugin', $this->plugin);
        $this->_form->setType('plugin', PARAM_PLUGIN);

        $mform->addElement('hidden', 'id', $this->id);
        $this->_form->setType('id', PARAM_INT);
    }

    /**
     * Overrides formslib's add_action_buttons() method.
     *
     *
     * @param bool $cancel
     * @param string|null $submitlabel
     *
     * @return void
     */
    public function add_action_buttons($cancel = true, $submitlabel = null): void {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('save');
        }
        parent::add_action_buttons($cancel, $submitlabel);
    }
}
