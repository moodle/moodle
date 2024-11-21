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
 * Report controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use block_xp\di;
use core_user;
use html_writer;
use single_button;
use block_xp\local\routing\url;
use block_xp\output\report_table_filterset;
use core_table\local\filter\filterset;
use core_table\local\filter\string_filter;

/**
 * Report controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_controller extends page_controller {

    /** @var bool Requires a wide view. */
    protected $iswideview = true;
    /** @var bool The page supports groups. */
    protected $supportsgroups = true;
    /** @var string The route name. */
    protected $routename = 'report';

    /** @var bool Whether we're using an old XP+. */
    protected $isusingoldxpp = false;

    /** @var moodleform The form. */
    protected $form;
    /** @var flexible_table The table. */
    protected $table;

    protected function define_optional_params() {
        return [
            ['userid', null, PARAM_INT],
            ['resetdata', 0, PARAM_INT, false],
            ['confirm', 0, PARAM_INT, false],
            ['delete', 0, PARAM_INT, false],
            ['term', null, PARAM_NOTAGS],
            ['page', 0, PARAM_INT],     // To keep the table page in URL.

            // Deprecated since XP 3.17.
            ['action', null, PARAM_ALPHA],
        ];
    }

    protected function permissions_checks() {
        $accessperms = $this->world->get_access_permissions();
        if (!($accessperms instanceof \block_xp\local\permission\access_report_permissions)) {
            throw new \coding_exception('Access permissions object requires report permissions.');
        }
        $accessperms->require_access_report();
    }

    protected function post_login() {
        parent::post_login();

        $addon = di::get('addon');
        $this->isusingoldxpp = $addon->is_older_than(2024090500);
    }

    protected function pre_content() {
        if (!$this->world->get_access_permissions()->can_manage()) {
            return;
        }

        // Reset data.
        if ($this->get_param('resetdata')) {
            if ($this->get_param('confirm') && confirm_sesskey()) {
                $store = $this->world->get_store();
                if ($this->get_groupid()) {
                    // Make sure that we've got a compatible store first.
                    if ($store instanceof \block_xp\local\xp\course_state_store) {
                        $store->reset_by_group($this->get_groupid());
                    }
                } else {
                    $store->reset();
                }
                $this->redirect(new url($this->pageurl));
            }
        }

        $userid = $this->get_param('userid');

        // Delete user.
        if ($this->get_param('delete')) {
            if ($this->get_param('confirm') && confirm_sesskey()) {
                $nexturl = new url($this->pageurl, ['userid' => null]);
                $this->world->get_store()->delete($userid);
                $this->redirect($nexturl);
            }
        }
    }

    protected function get_page_html_head_title() {
        return get_string('coursereport', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('coursereport', 'block_xp');
    }

    /**
     * Get the edit form.
     *
     * @param int $userid The user ID.
     * @deprecated Since XP 3.17
     */
    protected function get_form($userid) {
        if (!$this->form) {
            $state = $this->world->get_store()->get_state($userid);
            $form = new \block_xp\form\user_xp($this->pageurl->out(false));
            $form->set_data(['userid' => $userid, 'level' => $state->get_level()->get_level(), 'xp' => $state->get_xp()]);
            $this->form = $form;
        }
        return $form;
    }

    protected function get_table() {
        if (!$this->table) {
            $this->table = new \block_xp\output\report_table(
                \block_xp\di::get('db'),
                $this->world,
                $this->get_renderer(),
                $this->world->get_store(),
                $this->get_groupid()
            );
            $this->table->define_baseurl($this->pageurl);

            $filterset = $this->get_filterset();
            if ($filterset) {
                $this->table->set_filterset($filterset);
            }
        }
        return $this->table;
    }

    /**
     * Get the advanced heading options.
     *
     * @return array
     */
    protected function get_advanced_heading_options() {
        $config = di::get('config');
        $hasaddon = di::get('addon')->is_activated();

        $groupid = $this->get_groupid();
        $reseturl = new url($this->pageurl, [
            'resetdata' => 1,
            'group' => $groupid,
        ]);

        // Make sure that we can reset for a group only.
        $strreset = null;
        if (empty($groupid)) {
            $strreset = get_string('resetcoursedata', 'block_xp');
        } else if ($this->world->get_store() instanceof \block_xp\local\xp\course_state_store) {
            $strreset = get_string('resetgroupdata', 'block_xp');
        }

        return [
            'intro' => new \lang_string('coursereportintro', 'block_xp'),
            'menu' => array_filter([
                $config->get('enablepromoincourses') && !$hasaddon ? [
                    'label' => get_string('exportdata', 'block_xp'),
                    'href' => '#',
                    'disabled' => true,
                    'addonrequired' => true,
                ] : null,
                [], // Divider.
                $strreset ? [
                    'label' => $strreset,
                    'danger' => true,
                    'href' => $reseturl,
                ] : null,
            ], function($value) {
                return $value !== null;
            }),
        ];
    }

    /**
     * Get the bottom action buttons.
     *
     * @return single_button[]
     */
    protected function get_bottom_action_buttons() {
        return [];
    }

    /**
     * Get the filterset.
     *
     * @return filterset|null
     */
    protected function get_filterset(): ?filterset {
        $filterset = new report_table_filterset();
        if ($term = $this->get_param('term')) {
            $filterset->add_filter(new string_filter('term', null, [$term]));
        }
        return $filterset;
    }

    protected function page_advanced_heading() {
        $output = $this->get_renderer();
        echo $output->advanced_heading(get_string('coursereport', 'block_xp'), $this->get_advanced_heading_options());
    }

    protected function page_content() {
        global $PAGE;

        $canmanage = $this->world->get_access_permissions()->can_manage();
        $output = $this->get_renderer();
        $groupid = $this->get_groupid();

        // Confirming reset data.
        if ($canmanage && $this->get_param('resetdata')) {
            echo $this->get_renderer()->confirm_reset(
                empty($groupid) ? get_string('resetcoursedata', 'block_xp') : get_string('resetgroupdata', 'block_xp'),
                empty($groupid) ? get_string('reallyresetdata', 'block_xp') : get_string('reallyresetgroupdata', 'block_xp'),
                new url($this->pageurl->get_compatible_url(), ['resetdata' => 1, 'confirm' => 1,
                    'sesskey' => sesskey(), 'group' => $groupid, ]),
                new url($this->pageurl->get_compatible_url())
            );
            return;
        }

        // Confirming delete data.
        if ($canmanage && $this->get_param('delete')) {
            $user = core_user::get_user($this->get_param('userid'));
            echo $this->get_renderer()->confirm_step(
                $user ? fullname($user) : get_string('delete', 'core'),
                markdown_to_html(get_string('reallydeleteuserstateandlogs', 'block_xp')),
                new url($this->pageurl->get_compatible_url(), ['delete' => 1, 'confirm' => 1, 'sesskey' => sesskey()]),
                new url($this->pageurl->get_compatible_url(), ['userid' => null]),
                ['confirmlabel' => get_string('delete', 'core')]
            );
            return;
        }

        // Display the heading.
        $this->page_advanced_heading();

        // Display the group menu.
        $this->print_group_menu();

        // Display the user filter.
        $this->page_user_filter();

        // Displaying the report.
        echo html_writer::start_div('xp-cancel-overflow'); // Else dropdown menu is cropped on some versions.
        echo $this->get_table()->out(20, $this->isusingoldxpp);
        echo html_writer::end_div();

        // Output the bottom actions.
        $actions = !$canmanage ? [] : $this->get_bottom_action_buttons();
        if (!empty($actions)) {
            echo html_writer::tag('p', implode('', array_map(function($button) use ($output) {
                return $output->render($button);
            }, $actions)));
        }

        $PAGE->requires->js_call_amd('block_xp/modal-form', 'registerOpen', ['[data-action="open-form"]']);
    }

    protected function page_user_filter() {
        if ($this->isusingoldxpp) {
            return null;
        }

        $formfields = [];
        foreach ($this->pageurl->params() as $name => $value) {
            if ($name === 'term') {
                continue;
            }
            $formfields[] = ['name' => $name, 'value' => $value];
        }

        echo $this->get_renderer()->render_from_template('block_xp/table/report-filters', [
            'term' => $this->get_param('term'),
            'action' => $this->pageurl->out(false),
            'hiddenfields' => $formfields,
        ]);
    }

}
