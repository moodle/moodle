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
 * Admin levels controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use block_xp\di;
use block_xp\local\config\config;
use block_xp\local\routing\url;
use block_xp\local\serializer\url_serializer;
use html_writer;

/**
 * Admin levels controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_levels_controller extends admin_route_controller {

    /** @var config The config. */
    protected $config;
    /** @var moodleform The form. */
    protected $form;
    /** @var string Admin section name. */
    protected $sectionname = 'block_xp_default_levels';

    protected function define_optional_params() {
        return [
            ['reset', false, PARAM_BOOL, false],
            ['confirm', false, PARAM_BOOL, false],
        ];
    }

    protected function post_login() {
        parent::post_login();
        $this->config = \block_xp\di::get('config');
    }

    protected function pre_content() {
        parent::pre_content();

        // Reset levels to defaults.
        if ($this->get_param('reset') && confirm_sesskey()) {
            if ($this->get_param('confirm')) {
                // We should probably move this to the levels_info_writer, although it only knows about config.
                di::get('db')->set_field_select('block_xp_config', 'levelsdata', '', 'courseid > 0', []);
                $this->redirect(new url($this->pageurl), get_string('allcoursesreset', 'block_xp'));
            }
        }

    }

    protected function content() {
        $output = $this->get_renderer();
        $forwholesite = di::get('config')->get('context') == CONTEXT_SYSTEM;

        echo $output->heading(get_string('defaultlevels', 'block_xp'));

        if ($this->get_param('reset')) {
            echo $output->confirm_reset(
                get_string('resetallcoursestodefaults', 'block_xp'),
                get_string('reallyresetallcourselevelstodefaults', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['reset' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url())
            );
            return;
        }

        $this->page_warning_editing_defaults('levels');
        list($module, $props) = $this->get_react_module();
        echo $output->react_module($module, $props);

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

    protected function get_react_module() {
        $urlserializer = new url_serializer();
        $badgeurlresolver = di::get('badge_url_resolver');
        $defaultbadges = array_reduce(range(1, 20), function($carry, $level) use ($badgeurlresolver, $urlserializer) {
            $url = $badgeurlresolver->get_url_for_level($level);
            $carry[$level] = $urlserializer->serialize($url);
            return $carry;
        }, []);

        $levelsinfo = di::get('levels_info_factory')->get_default_levels_info();
        $serializer = di::get('serializer_factory')->get_levels_info_serializer();
        return [
            'block_xp/ui-levels-lazy',
            [
                'courseId' => 0,
                'levelsInfo' => $serializer->serialize($levelsinfo),
                'defaultBadgeUrls' => $defaultbadges,
                'addon' => [
                    'activated' => di::get('addon')->is_activated(),
                    'promourl' => $this->urlresolver->reverse('admin/promo')->out(false),
                ],
            ],
        ];
    }
}
