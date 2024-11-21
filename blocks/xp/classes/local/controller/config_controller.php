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
 * Config controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use html_writer;

/**
 * Config controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_controller extends page_controller {

    /** @var string The route name. */
    protected $routename = 'config';
    /** @var moodleform The form. */
    private $form;

    /**
     * Define the form.
     *
     * @param bool $unused No longer used.
     * @return moodleform
     */
    protected function define_form($unused = false) {
        return new \block_xp\form\config($this->pageurl->out(false), $this->define_form_customdata());
    }

    /**
     * Define form custom data.
     *
     * @return array
     */
    protected function define_form_customdata() {
        return [
            'promourl' => $this->urlresolver->reverse('promo', ['courseid' => $this->courseid]),
            'world' => $this->world,
        ];
    }

    /**
     * Get the form.
     *
     * Private so that we do not override this one.
     *
     * @return moodleform
     */
    private function get_form() {
        if (!$this->form) {
            $this->form = $this->define_form();
        }
        return $this->form;
    }

    protected function pre_content() {
        $config = $this->world->get_config();
        $form = $this->get_form();
        $form->set_data($config->get_all());
        if ($data = $form->get_data()) {
            $data = (array) $data;

            // Save the config.
            $config->set_many($data);

            // TODO Display a message.
            $this->redirect();
        }
    }

    protected function get_page_html_head_title() {
        return get_string('coursesettings', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('coursesettings', 'block_xp');
    }

    protected function page_content() {
        $form = $this->get_form();
        $form->display();
        $this->page_note();
    }

    protected function page_note() {
        $configlocked = \block_xp\di::get('config_locked');
        $thoselocked = array_filter($configlocked->get_all());
        if ($thoselocked) {
            echo html_writer::tag('p', html_writer::tag('small', get_string('notesomesettingslocked', 'block_xp')));
        }
    }

}
