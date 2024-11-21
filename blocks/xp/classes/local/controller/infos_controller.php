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
 * Infos controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use html_writer;
use block_xp\form\instructions;

/**
 * Infos controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class infos_controller extends page_controller {

    /** @var bool */
    protected $requiremanage = false;
    /** @var string */
    protected $routename = 'infos';
    /** @var object */
    protected $form;

    protected function define_optional_params() {
        return [
            ['edit', false, PARAM_BOOL, true],
        ];
    }

    /**
     * Is visible to viewers?
     *
     * @return bool
     */
    protected function is_visible_to_viewers() {
        return (bool) $this->world->get_config()->get('enableinfos');
    }

    protected function get_form() {
        if (!$this->form) {
            $this->form = new instructions($this->pageurl->out(false));
        }
        return $this->form;
    }

    protected function get_page_html_head_title() {
        return get_string('infos', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('infos', 'block_xp');
    }

    protected function page_content() {
        global $PAGE;

        $output = $this->get_renderer();
        $levelsinfo = $this->world->get_levels_info();
        $canmanage = $this->world->get_access_permissions()->can_manage();
        $config = $this->world->get_config();

        $instructions = $config->get('instructions');
        $instructionsformat = $config->get('instructions_format');
        $cleanedinstructions = trim(strip_tags($instructions));
        $hasinstructions = !empty($cleanedinstructions);
        $isediting = $this->get_param('edit') && $canmanage;

        if ($canmanage) {
            echo $output->advanced_heading(get_string('infos', 'block_xp'), [
                'intro' => new \lang_string('infosintro', 'block_xp'),
                'help' => new \help_icon('infos', 'block_xp'),
                'visible' => $this->is_visible_to_viewers(),
                'menu' => [
                    [
                        'label' => get_string('pagesettings', 'block_xp'),
                        'data-action' => 'open-form',
                        'data-form-class' => 'block_xp\form\info',
                        'data-form-args__contextid' => $this->world->get_context()->id,
                        'href' => '#',
                    ],
                    [
                        'label' => get_string('customizelevels', 'block_xp'),
                        'href' => $this->urlresolver->reverse('levels', ['courseid' => $this->world->get_courseid()]),
                    ],
                ],
            ]);
            $PAGE->requires->js_call_amd('block_xp/modal-form', 'registerOpen', ['[data-action="open-form"]']);
        }

        if ($hasinstructions) {
            // Display the instructions when not editing.
            echo html_writer::div(format_text($instructions, $instructionsformat), 'block_xp-instructions');
        }

        echo $output->levels_grid($levelsinfo->get_levels());
    }

}
