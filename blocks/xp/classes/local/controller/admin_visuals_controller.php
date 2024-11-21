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
 * Admin visuals controller.
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
use block_xp\local\config\course_world_config;
use block_xp\local\routing\url;
use block_xp\local\world;
use context_system;
use html_writer;

/**
 * Admin visuals controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_visuals_controller extends admin_route_controller {

    /** @var string The section name. */
    protected $sectionname = 'block_xp_default_visuals';
    /** @var moodleform The form. */
    private $form;

    protected function define_optional_params() {
        return [
            ['reset', false, PARAM_BOOL, false],
            ['confirm', false, PARAM_BOOL, false],
        ];
    }

    /**
     * Define the form.
     *
     * @return moodleform
     */
    protected function define_form() {
        return new \block_xp\form\visuals($this->pageurl->out(false), [
            'fmoptions' => $this->get_filemanager_options(),
            'promourl' => $this->urlresolver->reverse('admin/promo'),
        ]);
    }

    /**
     * Get manager context.
     *
     * @return context
     */
    final protected function get_filemanager_context() {
        return context_system::instance();
    }

    /**
     * Get file manager options.
     *
     * @return array
     */
    final protected function get_filemanager_options() {
        return [
            'subdirs' => 0,
            'accepted_types' => ['.jpg', '.png', '.svg', '.gif'],
        ];
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

    /**
     * Get the initial form data.
     *
     * @return array
     */
    protected function get_initial_form_data() {
        $draftitemid = file_get_submitted_draft_itemid('badges');
        file_prepare_draft_area($draftitemid, $this->get_filemanager_context()->id, 'block_xp', 'defaultbadges',
            0, $this->get_filemanager_options());

        return [
            'badges' => $draftitemid,
        ];
    }

    protected function pre_content() {
        // Capture form submission.
        $form = $this->get_form();
        $form->set_data((object) $this->get_initial_form_data());
        if ($data = $form->get_data()) {
            $this->save_form_data($data);
            $this->redirect();
        }

        // Reset appearance to defaults.
        if ($this->get_param('reset') && confirm_sesskey()) {
            if ($this->get_param('confirm')) {
                $this->reset_all_worlds_to_defaults();
                $this->redirect(null, get_string('allcoursesreset', 'block_xp'));
            }
        }
    }

    /**
     * Reset all worlds to defaults.
     */
    final protected function reset_all_worlds_to_defaults() {
        // This is not really the way we should obtain all worlds, but it works.
        $courseids = di::get('db')->get_fieldset_select('block_xp_config', 'courseid', 'courseid > 0', []);
        $courseworldfactory = di::get('course_world_factory');

        // This is slow, but that's safer than trying to write to the database directly.
        foreach ($courseids as $courseid) {
            $world = $courseworldfactory->get_world($courseid);
            $this->reset_world_to_defaults($world);
        }
    }

    /**
     * Reset a world to its defaults.
     *
     * @param world $world The world.
     */
    protected function reset_world_to_defaults(world $world) {
        $config = $world->get_config();
        $config->set('enablecustomlevelbadges', course_world_config::CUSTOM_BADGES_MISSING);
        $fs = get_file_storage();
        $fs->delete_area_files($world->get_context()->id, 'block_xp', 'badges', 0);
    }

    /**
     * Save the form data.
     *
     * @param stdClass $data The form data.
     * @return void
     */
    protected function save_form_data($data) {
        file_save_draft_area_files($data->badges, $this->get_filemanager_context()->id, 'block_xp', 'defaultbadges', 0,
            $this->get_filemanager_options());
    }

    /**
     * Echo the content.
     *
     * @return void
     */
    protected function content() {
        $form = $this->get_form();
        $output = $this->get_renderer();
        $forwholesite = di::get('config')->get('context') == CONTEXT_SYSTEM;

        echo $output->heading(get_string('defaultvisuals', 'block_xp'));

        if ($this->get_param('reset')) {
            echo $output->confirm_reset(
                get_string('resetallcoursestodefaults', 'block_xp'),
                get_string('reallyresetallcoursevisualstodefaults', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['reset' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url())
            );
            return;
        }

        $this->page_warning_editing_defaults('visuals');

        $this->intro();

        $form->display();

        // Preview.
        echo $output->heading_with_divider(get_string('preview', 'core'));
        $this->preview();

        // Reset courses.
        if (!$forwholesite) {
            echo $output->heading_with_divider(get_string('dangerzone', 'block_xp'));
            echo html_writer::tag('p', markdown_to_html(get_string('resetallcoursestodefaultsintro', 'block_xp')));
            $url = new url($this->pageurl, ['reset' => 1, 'sesskey' => sesskey()]);
            echo html_writer::tag('p',
                $output->render($output->make_single_button(
                    $url->get_compatible_url(),
                    get_string('resetallcoursestodefaults', 'block_xp'),
                    ['danger' => true]
                ))
            );
        }
    }

    /**
     * Print the intro part.
     *
     * @return void
     */
    protected function intro() {
        echo html_writer::tag('p', get_string('admindefaultvisualsintro', 'block_xp'));
    }

    /**
     * Print the preview part.
     *
     * @return void
     */
    protected function preview() {
        $output = $this->get_renderer();
        echo $output->levels_preview($this->get_levels_info()->get_levels());
    }

    /**
     * Get the levels info.
     *
     * @return levels_info
     */
    final protected function get_levels_info() {
        // TODO We should get the levels info from somewhere else.
        $config = \block_xp\di::get('config');
        $resolver = \block_xp\di::get('badge_url_resolver');
        $data = json_decode($config->get('levelsdata'), true);
        if (!$data) {
            $levelsinfo = \block_xp\local\xp\algo_levels_info::make_from_defaults($resolver);
        } else {
            $levelsinfo = new \block_xp\local\xp\algo_levels_info($data, $resolver);
        }
        return $levelsinfo;
    }

}
