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
 * Admin rules controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use coding_exception;
use html_writer;
use block_xp\local\routing\url;

/**
 * Admin rules controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_rules_controller extends admin_route_controller {

    /** @var string The section name. */
    protected $sectionname = 'block_xp_default_rules';
    /** @var array Existing filters. */
    protected $existingfilters;
    /** @var admin_filter_manager The manager. */
    protected $filtermanager;

    protected function define_optional_params() {
        return [
            ['revert', false, PARAM_BOOL, false],
            ['reset', false, PARAM_BOOL, false],
            ['confirm', false, PARAM_BOOL, false],
        ];
    }

    protected function pre_content() {
        $this->filtermanager = new \block_xp\local\xp\admin_filter_manager(\block_xp\di::get('db'));

        // Revert to defaults.
        if ($this->get_param('revert') && confirm_sesskey()) {
            if ($this->get_param('confirm')) {
                $this->filtermanager->reset();
                $this->redirect(new url($this->pageurl));
            }
        }

        // Reset all courses to defaults.
        if ($this->get_param('reset') && confirm_sesskey()) {
            if ($this->get_param('confirm')) {
                $this->filtermanager->reset_all_courses_to_defaults();
                $this->redirect(new url($this->pageurl), get_string('allcoursesreset', 'block_xp'));
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

    /**
     * Handle save.
     *
     * @return void
     */
    protected function handle_save() {
        $category = \block_xp_filter::CATEGORY_EVENTS;
        $filters = isset($_POST['filters']) ? $_POST['filters'] : [];
        $this->save_filters($filters, $this->filtermanager->get_filters($category), $category);
    }

    /**
     * Save the filters.
     *
     * @param array $filters Filters data.
     * @param array $existingfilters Existing filters data.
     * @param int|null $category The category constant.
     * @return void
     */
    protected function save_filters($filters, $existingfilters, $category = null) {
        $filterids = [];
        foreach ($filters as $filterdata) {
            $data = $filterdata;
            $data['ruledata'] = json_encode($data['rule'], true);
            unset($data['rule']);
            $data['courseid'] = 0;
            if ($category !== null) {
                $data['category'] = $category;
            }

            if (!\block_xp_filter::validate_data($data)) {
                throw new coding_exception('Data could not be validated');
            }

            $filter = \block_xp_filter::load_from_data($data);
            if ($filter->get_id() && !array_key_exists($filter->get_id(), $existingfilters)) {
                throw new coding_exception('Invalid filter ID');
            }

            $filter->save();
            $filterids[$filter->get_id()] = true;
        }

        // Check for filters to be deleted.
        foreach ($existingfilters as $filterid => $filter) {
            // Note that the defaults filters do not have a real ID.
            if ($filter->get_id() && !array_key_exists($filterid, $filterids)) {
                $filter->delete();
            }
            unset($existingfilters[$filterid]);
        }

        // Remember that we've customised the admin filters.
        $this->filtermanager->mark_as_customised();
    }

    /**
     * Get available rules.
     *
     * @return array
     */
    protected function get_available_rules() {
        $forwholesite = \block_xp\di::get('config')->get('context') == CONTEXT_SYSTEM;
        $rules = [
            (object) [
                'name' => get_string('ruleevent', 'block_xp'),
                'info' => get_string('ruleeventinfo', 'block_xp'),
                'rule' => new \block_xp_rule_event(),
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
        return $rules;
    }

    /**
     * Get default filters.
     *
     * @return \block_xp_filter
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
                $this->filtermanager->get_filters()
            ),
            get_string('eventsrules', 'block_xp'),
            null,
            new \help_icon('eventsrules', 'block_xp')
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

    protected function page_plus_promo_content() {
        $promourl = $this->urlresolver->reverse('admin/promo');
        echo $this->get_renderer()->notification_without_close(
            get_string('promorulesdidyouknow', 'block_xp', ['url' => $promourl->out(false)]),
            \core\output\notification::NOTIFY_INFO
        );
    }

    protected function page_rules_content() {
        $output = $this->get_renderer();
        echo $output->render($this->get_widget_group());
    }

    /**
     * Echo the content.
     *
     * @return void
     */
    protected function content() {
        $output = $this->get_renderer();
        $forwholesite = \block_xp\di::get('config')->get('context') == CONTEXT_SYSTEM;
        echo $output->heading(get_string('defaultrules', 'block_xp'));

        if ($this->get_param('revert')) {
            echo $output->confirm_step(
                get_string('reverttopluginsdefaults', 'block_xp'),
                get_string('reallyreverttopluginsdefaults', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['revert' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url()),
                ['confirmlabel' => get_string('revert', 'core')]
            );
            return;

        } else if ($this->get_param('reset')) {
            echo $output->confirm_reset(
                get_string('resetallcoursestodefaults', 'block_xp'),
                get_string('reallyresetallcoursestodefaults', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['reset' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url())
            );
            return;
        }

        $this->page_warning_editing_defaults('rules');
        $this->page_plus_promo_content();
        echo html_writer::tag('p', get_string('admindefaultrulesintro', 'block_xp'));
        $this->page_rules_content();

        $hasdangerzone = $this->filtermanager->is_customised() || !$forwholesite;
        if ($hasdangerzone) {
            echo $output->heading_with_divider(get_string('dangerzone', 'block_xp'));
        }

        // Revert button.
        if ($this->filtermanager->is_customised()) {

            echo html_writer::tag('p', get_string('reverttopluginsdefaultsintro', 'block_xp'));
            $url = new url($this->pageurl, ['revert' => 1, 'sesskey' => sesskey()]);
            echo html_writer::tag('p',
                $output->render($output->make_single_button(
                    $url->get_compatible_url(),
                    get_string('reverttopluginsdefaults', 'block_xp'),
                    ['danger' => true]
                ))
            );

        }

        // Reset courses.
        if (!$forwholesite) {
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

}
