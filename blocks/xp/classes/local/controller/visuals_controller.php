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
 * Visuals controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

use block_xp\di;
use context_system;
use html_writer;
use stdClass;
use block_xp\local\config\course_world_config;
use block_xp\local\routing\url;

/**
 * Visuals controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class visuals_controller extends page_controller {

    /** @var string The nav name. */
    protected $navname = 'levels';
    /** @var string The route name. */
    protected $routename = 'visuals';

    /** @var moodleform The form. */
    private $form;

    protected function define_optional_params() {
        return [
            ['reset', false, PARAM_BOOL, false],
            ['confirm', false, PARAM_BOOL, false],
        ];
    }

    /**
     * Get manager context.
     *
     * @return context
     */
    final protected function get_filemanager_context() {
        return $this->world->get_context();
    }

    /**
     * Get file manager options.
     *
     * @return array
     */
    final protected function get_filemanager_options() {
        return ['subdirs' => 0, 'accepted_types' => ['.jpg', '.png', '.gif', '.svg']];
    }

    /**
     * Define the form.
     *
     * @return moodleform
     */
    protected function define_form() {
        return new \block_xp\form\visuals($this->pageurl->out(false), [
            'showpromo' => di::get('config')->get('enablepromoincourses'),
            'promourl' => $this->urlresolver->reverse('promo', ['courseid' => $this->courseid]),
            'fmoptions' => $this->get_filemanager_options(),
        ]);
    }

    /**
     * Get the form.
     *
     * @return moodleform
     */
    final protected function get_form() {
        if (!$this->form) {
            $this->form = $this->define_form();
        }
        return $this->form;
    }

    protected function pre_content() {

        // Reset to defaults.
        if ($this->get_param('reset') && confirm_sesskey()) {
            if ($this->get_param('confirm')) {
                $this->reset_visuals_to_defaults();
                $this->redirect(new url($this->pageurl));
            }
        }

        $form = $this->get_form();
        $form->set_data((object) $this->get_initial_form_data());
        if ($data = $form->get_data()) {
            $this->save_form_data($data);
            // TODO Add a confirmation message.
            $this->redirect();

        } else if ($form->is_cancelled()) {
            $this->redirect();
        }
    }

    /**
     * Get the initial form data.
     *
     * @return array
     */
    protected function get_initial_form_data() {
        $config = $this->world->get_config();
        $draftitemid = file_get_submitted_draft_itemid('badges');

        // If the badges are missing, we copy them now.
        if ($config->get('enablecustomlevelbadges') == course_world_config::CUSTOM_BADGES_MISSING) {
            file_prepare_draft_area($draftitemid, context_system::instance()->id, 'block_xp', 'defaultbadges', 0,
                $this->get_filemanager_options());
        } else {
            file_prepare_draft_area($draftitemid, $this->get_filemanager_context()->id, 'block_xp', 'badges', 0,
                $this->get_filemanager_options());
        }

        return [
            'badges' => $draftitemid,
        ];
    }

    /**
     * Reset visuals to defaults.
     */
    protected function reset_visuals_to_defaults() {
        $config = $this->world->get_config();
        $config->set('enablecustomlevelbadges', course_world_config::CUSTOM_BADGES_MISSING);

        $fs = get_file_storage();
        $fs->delete_area_files($this->get_filemanager_context()->id, 'block_xp', 'badges', 0);
    }

    /**
     * Save the form data.
     *
     * @param stdClass $data The form data.
     * @return void
     */
    protected function save_form_data($data) {
        $config = $this->world->get_config();

        // Save the area.
        file_save_draft_area_files($data->badges, $this->get_filemanager_context()->id, 'block_xp', 'badges', 0,
            $this->get_filemanager_options());

        // When we save, we mark the flag as noop because either we copied the default badges,
        // when we loaded the draft area, or the user saved the page as they were in a legacy state,
        // and we want to take them out of it.
        $config->set('enablecustomlevelbadges', course_world_config::CUSTOM_BADGES_NOOP);
    }

    protected function get_page_html_head_title() {
        return get_string('levelsappearance', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('levelsappearance', 'block_xp');
    }

    protected function page_content() {
        $output = $this->get_renderer();

        if ($this->get_param('reset')) {
            echo $output->confirm_reset(
                get_string('resettodefaults', 'block_xp'),
                get_string('reallyresetcoursevisualstodefaults', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['reset' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url())
            );
            return;
        }

        echo $output->advanced_heading(get_string('levelsappearance', 'block_xp'), [
            'intro' => new \lang_string('visualsintro', 'block_xp'),
            'menu' => [
                [
                    'label' => get_string('resettodefaults', 'block_xp'),
                    'danger' => true,
                    'href' => new url($this->pageurl, ['reset' => 1, 'sesskey' => sesskey()]),
                ],
            ],
        ]);

        $this->get_form()->display();

        echo $output->heading_with_divider(get_string('preview', 'core'));

        $this->preview();

        $this->page_danger_zone_content();
    }

    /**
     * Preview.
     *
     * @return void
     */
    protected function preview() {
        $levelsinfo = $this->world->get_levels_info();
        echo $this->get_renderer()->levels_preview($levelsinfo->get_levels());
    }

    protected function page_danger_zone_content() {
    }

    /**
     * Introduction.
     *
     * @deprecated Since XP 3.15 without replacement.
     * @return void
     */
    protected function intro() {
    }

}
