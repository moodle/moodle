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

namespace tool_usertours\local\target;

/**
 * Block target.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block extends base {
    /**
     * Convert the target value to a valid CSS selector for use in the
     * output configuration.
     *
     * @return string
     */
    public function convert_to_css() {
        // The block has the following CSS class selector style:
        // .block-region .block_[name] .
        return sprintf('.block-region .block_%s', $this->step->get_targetvalue());
    }

    /**
     * Convert the step target to a friendly name for use in the UI.
     *
     * @return string
     */
    public function get_displayname() {
        return get_string('block_named', 'tool_usertours', $this->get_block_name());
    }

    /**
     * Get the translated name of the block.
     *
     * @return string
     */
    protected function get_block_name() {
        return get_string('pluginname', self::get_frankenstyle($this->step->get_targetvalue()));
    }

    /**
     * Get the frankenstyle name of the block.
     *
     * @param   string  $block  The block name.
     * @return                  The frankenstyle block name.
     */
    protected static function get_frankenstyle($block) {
        return sprintf('block_%s', $block);
    }

    /**
     * Add the target type configuration to the form.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     * @return  $this
     */
    public static function add_config_to_form(\MoodleQuickForm $mform) {
        global $PAGE;

        $blocks = [];
        foreach ($PAGE->blocks->get_installed_blocks() as $block) {
            $blocks[$block->name] = get_string('pluginname', 'block_' . $block->name);
        }

        \core_collator::asort($blocks);

        $mform->addElement('select', 'targetvalue_block', get_string('block', 'tool_usertours'), $blocks);
    }

    /**
     * Add the disabledIf values.
     *
     * @param   MoodleQuickForm $mform      The form to add configuration to.
     */
    public static function add_disabled_constraints_to_form(\MoodleQuickForm $mform) {
        $mform->hideIf(
            'targetvalue_block',
            'targettype',
            'noteq',
            \tool_usertours\target::get_target_constant_for_class(self::class)
        );
    }

    /**
     * Prepare data to submit to the form.
     *
     * @param   object          $data       The data being passed to the form
     */
    public function prepare_data_for_form($data) {
        $data->targetvalue_block = $this->step->get_targetvalue();
    }

    /**
     * Fetch the targetvalue from the form for this target type.
     *
     * @param   stdClass        $data       The data submitted in the form
     * @return  string
     */
    public function get_value_from_form($data) {
        return $data->targetvalue_block;
    }
}
