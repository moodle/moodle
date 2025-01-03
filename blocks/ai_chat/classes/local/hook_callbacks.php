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

namespace block_ai_chat\local;

use context_block;
use context_system;
use stdClass;

/**
 * Hook listener callbacks.
 *
 * @package    block_ai_chat
 * @copyright  2024 ISB Bayern
 * @author     Tobias Garske
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Add a checkbox to add a ai-chat block.
     *
     * @param \core_course\hook\after_form_definition $hook
     */
    public static function handle_after_form_definition(\core_course\hook\after_form_definition $hook): void {
        $tenant = \core\di::get(\local_ai_manager\local\tenant::class);
        if ($tenant->is_tenant_allowed()) {
            $mform = $hook->mform;
            $mform->addElement('checkbox', 'addaichat', get_string('addblockinstance', 'block_ai_chat'), 'add_block_ai_chat');
            $mform->addHelpButton('addaichat', 'addblockinstance', 'block_ai_chat');
            $mform->setDefaults('addaichat', 1);
        }
    }

    /**
     * Check for addaichat form setting and add/remove ai-chat block.
     *
     * @param \core_course\hook\after_form_submission $hook
     */
    public static function handle_after_form_submission(\core_course\hook\after_form_submission $hook): void {
        global $DB;
        // Get form data.
        $data = $hook->get_data();

        // Check if block_ai_chat instance is present.
        $courseid = $data->id;
        $blockinstance = helper::has_block_in_course_context($courseid);

        if (!empty($data->addaichat) && $data->addaichat == '1') {
            if (!$blockinstance) {
                // Add block instance.
                $newinstance = new \stdClass;
                $newinstance->blockname = 'ai_chat';
                $newinstance->parentcontextid = \context_course::instance($courseid)->id;
                // We want to make the block usable for single activity courses as well, so display in subcontexts.
                $newinstance->showinsubcontexts = 1;
                $newinstance->pagetypepattern = '*';
                $newinstance->subpagepattern = null;
                $newinstance->defaultregion = 'side-pre';
                $newinstance->defaultweight = 1;
                $newinstance->configdata = '';
                $newinstance->timecreated = time();
                $newinstance->timemodified = $newinstance->timecreated;
                $newinstance->id = $DB->insert_record('block_instances', $newinstance);
            }
        } else {
            // If tenant is not allowed, $data->addaichat will be empty,
            // so an existing instance will be deleted by following lines.
            if ($blockinstance) {
                // Remove block instance.
                blocks_delete_instance($blockinstance);
            }
        }
    }

    /**
     * Check if block instance is present and set addaichat form setting.
     *
     * @param \core_course\hook\after_form_definition_after_data $hook
     * @return void
     * @throws \dml_exception
     */
    public static function handle_after_form_definition_after_data(\core_course\hook\after_form_definition_after_data $hook): void {
        // Get form data.
        $mform = $hook->mform;
        $formwrapper = $hook->formwrapper;
        if (!empty($formwrapper->get_course()->id)) {
            $courseid = $formwrapper->get_course()->id;

            $blockinstance = helper::has_block_in_course_context($courseid);
            if ($blockinstance) {
                // Block present, so set checkbox accordingly.
                $mform->setDefault('addaichat', "checked");
            }
        }
    }

    /**
     * Insert a chatbot floating button on pagetypes which are defined in the related admin setting.
     *
     * @param \core\hook\output\before_footer_html_generation $hook the before footer html generation hook object
     */
    public static function handle_before_footer_html_generation(\core\hook\output\before_footer_html_generation $hook): void {
        global $DB, $PAGE;
        if (!helper::show_global_block($PAGE)) {
            return;
        }
        $systemcontext = context_system::instance();
        $blockinstancerecord = $DB->get_record('block_instances',
                ['blockname' => 'ai_chat', 'parentcontextid' => $systemcontext->id, 'pagetypepattern' => '']);

        if (!$blockinstancerecord) {

            $defaultregion = $PAGE->blocks->get_default_region();
            // Add a special system-wide block instance.
            $blockinstancerecord = new stdClass;
            $blockinstancerecord->blockname = 'ai_chat';
            $blockinstancerecord->parentcontextid = $systemcontext->id;
            $blockinstancerecord->showinsubcontexts = false;
            $blockinstancerecord->requiredbytheme = false;
            $blockinstancerecord->pagetypepattern = '';
            $blockinstancerecord->subpagepattern = null;
            $blockinstancerecord->defaultregion = $defaultregion;
            $blockinstancerecord->defaultweight = 0;
            $blockinstancerecord->configdata = '';
            $blockinstancerecord->timecreated = time();
            $blockinstancerecord->timemodified = $blockinstancerecord->timecreated;
            $blockinstancerecord->id = $DB->insert_record('block_instances', $blockinstancerecord);

            // Ensure the block context is created.
            context_block::instance($blockinstancerecord->id);
        }
        // If the new instance was created, allow it to do additional setup.
        if ($block = block_instance('ai_chat', $blockinstancerecord)) {
            $block->instance_create();
        }
        echo $block->get_content()->text;
    }

    /**
     * Add a bodyclass depending on the replacehelp setting.
     *
     * @param \core\hook\output\before_http_headers $hook the before html attributes generation hook object
     */
    public static function handle_before_http_headers(\core\hook\output\before_http_headers $hook): void {
        global $PAGE;

        if (get_config('block_ai_chat', 'replacehelp')) {
            $PAGE->add_body_class('block_ai_chat_replacehelp');
        }
    }
}
