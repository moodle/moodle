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
 * Rules controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use block_xp\di;
use block_xp\local\course_world;
use html_writer;
use moodle_exception;
use block_xp\local\routing\url;
use block_xp_filter;

/**
 * Rules controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rules_controller extends page_controller {

    /** @var string The nav name. */
    protected $navname = 'rules';
    /** @var string The route name. */
    protected $routename = 'rules';
    /** @var \block_xp\local\course_filter_manager The filter manager. */
    protected $filtermanager;
    /** @var array User filters. */
    protected $userfilters;
    /** @var array Whether to show legacy headings. */
    protected $legacyheadings;

    protected function define_optional_params() {
        return [
            ['reset', false, PARAM_BOOL, false],
            ['confirm', false, PARAM_BOOL, false],
        ];
    }

    protected function post_login() {
        parent::post_login();
        $this->filtermanager = $this->world->get_filter_manager();
        $this->userfilters = $this->filtermanager->get_user_filters();
        $this->legacyheadings = di::get('addon')->is_activated() && di::get('addon')->is_older_than(2023100402);
    }

    protected function pre_content() {

        // Reset course rules to defaults.
        if ($this->get_param('reset') && confirm_sesskey()) {
            if ($this->get_param('confirm')) {
                $this->world->reset_filters_to_defaults(block_xp_filter::CATEGORY_EVENTS);
                $this->redirect(new url($this->pageurl));
            }
        }

        // Saving the data.
        if (!empty($_POST['save'])) {
            require_sesskey();
            $this->handle_save();
            $this->redirect(null, get_string('changessaved'));

        } else if (!empty($_POST['cancel'])) {
            $this->redirect();
        }
    }

    protected function handle_save() {
        $filters = isset($_POST['filters']) ? $_POST['filters'] : [];
        $this->userfilters = $this->save_filters($filters, $this->userfilters);
    }

    protected function save_filters($filters, $existingfilters, $category = null) {
        static::save_rules_filters($this->world, $filters, $existingfilters, $category);
    }

    protected function get_page_html_head_title() {
        return get_string('eventsrules', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('eventsrules', 'block_xp');
    }

    /**
     * Get available rules.
     *
     * @return array
     */
    protected function get_available_rules() {
        return [
            (object) [
                'name' => get_string('ruleevent', 'block_xp'),
                'info' => get_string('ruleeventinfo', 'block_xp'),
                'rule' => new \block_xp_rule_event(),
            ],
            (object) [
                'name' => get_string('rulecm', 'block_xp'),
                'info' => get_string('rulecminfo', 'block_xp'),
                'rule' => new \block_xp_rule_cm($this->courseid),
            ],
            (object) [
                'name' => get_string('ruleproperty', 'block_xp'),
                'info' => get_string('rulepropertyinfo', 'block_xp'),
                'rule' => new \block_xp_rule_property(),
            ],
            (object) [
                'name' => get_string('ruleset', 'block_xp'),
                'info' => get_string('rulesetinfo', 'block_xp'),
                'rule' => new \block_xp_ruleset(),
            ],
        ];
    }

    /**
     * Get default filters.
     *
     * @return block_xp_filter
     */
    protected function get_default_filter() {
        return \block_xp_filter::load_from_data(['rule' => new \block_xp_ruleset()]);
    }

    /**
     * Get events widget element.
     *
     * @return renderable
     */
    protected function get_events_widget_element() {
        return new \block_xp\output\filters_widget_element(
            new \block_xp\output\filters_widget(
                $this->get_default_filter(),
                $this->get_available_rules(),
                $this->userfilters
            ),
            $this->legacyheadings ? get_string('eventsrules', 'block_xp') : null,
            null,
            $this->legacyheadings ? new \help_icon('eventsrules', 'block_xp') : null
        );
    }

    /**
     * Get widget group.
     *
     * @return renderable
     */
    protected function get_widget_group() {
        return new \block_xp\output\filters_widget_group([$this->get_events_widget_element()]);
    }

    protected function page_content() {
        global $PAGE;
        $output = $this->get_renderer();

        if ($this->get_param('reset')) {
            echo $output->confirm_reset(
                get_string('resettodefaults', 'block_xp'),
                get_string('reallyresetcourserulestodefaults', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['reset' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url())
            );
            return;
        }

        echo $output->rules_page_loading_check_init();
        $this->page_plus_promo_content();
        $this->page_rules_content();
        $this->page_danger_zone_content();
        echo $output->rules_page_loading_check_success();
    }

    protected function page_plus_promo_content() {
    }

    protected function page_advanced_heading() {
        global $PAGE;
        $output = $this->get_renderer();
        $url = new url($this->pageurl, ['reset' => 1, 'sesskey' => sesskey()]);

        echo $output->advanced_heading(get_string('eventsrules', 'block_xp'), [
            'intro' => new \lang_string('eventsrulesintro', 'block_xp'),
            'help' => new \help_icon('eventsrules', 'block_xp'),
            'menu' => [
                [
                    'label' => get_string('cheatguard', 'block_xp'),
                    'data-action' => 'open-form',
                    'data-form-class' => di::get('cheatguard_form_class'),
                    'data-form-args__contextid' => $this->world->get_context()->id,
                    'href' => '#',
                ],
                [],
                [
                    'label' => get_string('resettodefaults', 'block_xp'),
                    'danger' => true,
                    'href' => $url,
                ],
            ],
        ]);
        $PAGE->requires->js_call_amd('block_xp/modal-form', 'registerOpen', ['[data-action="open-form"]']);
    }

    protected function page_rules_content() {
        $output = $this->get_renderer();

        if (!$this->legacyheadings) {
            $this->page_advanced_heading();
        }

        echo $output->render($this->get_widget_group());
    }

    protected function page_danger_zone_content() {
    }

    /**
     * Save the rules filters.
     *
     * @param course_world $world The course world.
     * @param array $filters The filters to save.
     * @param array $existingfilters The list of existing filters.
     * @param int|null $category The category of filters.
     */
    public static function save_rules_filters(course_world $world, $filters, $existingfilters, $category = null) {
        $courseid = $world->get_courseid();
        $filtermanager = $world->get_filter_manager();

        $filterids = [];
        foreach ($filters as $filterdata) {
            $data = $filterdata;
            $data['ruledata'] = json_encode($data['rule'], true);
            unset($data['rule']);
            $data['courseid'] = $courseid;
            if ($category !== null) {
                $data['category'] = $category;
            }

            if (!\block_xp_filter::validate_data($data)) {
                throw new moodle_exception('Data could not be validated');
            }

            $filter = \block_xp_filter::load_from_data($data);
            if ($filter->get_id() && !array_key_exists($filter->get_id(), $existingfilters)) {
                throw new moodle_exception('Invalid filter ID');
            }

            $filter->save();
            $filterids[$filter->get_id()] = true;
        }

        // Check for filters to be deleted.
        foreach ($existingfilters as $filterid => $filter) {
            if (!array_key_exists($filterid, $filterids)) {
                $filter->delete();
            }
            unset($existingfilters[$filterid]);
        }

        if ($category !== null) {
            $filtermanager->invalidate_filters_cache($category);
        } else {
            $filtermanager->invalidate_filters_cache();
        }

        return $existingfilters;
    }
}
