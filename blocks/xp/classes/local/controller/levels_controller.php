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
 * Levels controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use block_xp\di;
use block_xp\local\routing\url;
use block_xp\local\serializer\url_serializer;

/**
 * Levels controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class levels_controller extends page_controller {

    /** @var string The route name. */
    protected $routename = 'levels';

    protected function define_optional_params() {
        return [
            ['reset', false, PARAM_BOOL, false],
            ['confirm', false, PARAM_BOOL, false],
        ];
    }

    protected function pre_content() {
        parent::pre_content();

        // Reset levels to defaults.
        if ($this->get_param('reset') && confirm_sesskey()) {
            if ($this->get_param('confirm')) {
                $this->world->get_config()->set('levelsdata', '');
                $this->redirect(new url($this->pageurl));
            }
        }

    }

    protected function get_page_html_head_title() {
        return get_string('levels', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('levels', 'block_xp');
    }

    protected function get_react_module() {
        global $USER;

        $world = $this->world;
        $courseid = $world->get_courseid();

        $urlserializer = new url_serializer();
        $badgeurlresolver = di::get('badge_url_resolver_course_world_factory')->get_url_resolver($world);
        $defaultbadges = array_reduce(range(1, 20), function($carry, $level) use ($badgeurlresolver, $urlserializer) {
            $url = $badgeurlresolver->get_url_for_level($level);
            $carry[$level] = $urlserializer->serialize($url);
            return $carry;
        }, []);

        $levelsinfo = di::get('levels_info_factory')->get_world_levels_info($this->world);
        $serializer = di::get('serializer_factory')->get_levels_info_serializer();
        return [
            'block_xp/ui-levels-lazy',
            [
                'courseId' => $courseid,
                'levelsInfo' => $serializer->serialize($levelsinfo),
                'resetToDefaultsUrl' => $this->get_reset_url()->out(false),
                'defaultBadgeUrls' => $defaultbadges,
                'badges' => array_values(di::get('badge_manager')->get_compatible_badges($world->get_context(), $USER->id)),
                'addon' => [
                    'activated' => di::get('addon')->is_activated(),
                    'enablepromo' => (bool) di::get('config')->get('enablepromoincourses'),
                    'promourl' => $this->urlresolver->reverse('promo', ['courseid' => $world->get_courseid()])->out(false),
                ],
            ],
        ];
    }

    protected function get_reset_url() {
        return new url($this->pageurl, ['reset' => 1, 'sesskey' => sesskey()]);
    }

    protected function page_content() {
        $output = $this->get_renderer();

        if ($this->get_param('reset')) {
            echo $output->confirm_reset(
                get_string('resettodefaults', 'block_xp'),
                get_string('reallyresetcourselevelstodefaults', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['reset' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url())
            );
            return;
        }

        list($module, $props) = $this->get_react_module();
        echo $output->react_module($module, $props);

        $this->page_danger_zone_content();
    }

    protected function page_danger_zone_content() {
    }

}
