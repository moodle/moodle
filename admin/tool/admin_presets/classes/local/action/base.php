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

namespace tool_admin_presets\local\action;

use context_system;
use moodle_url;
use core_adminpresets\manager;
use tool_admin_presets\output\presets_list;
use tool_admin_presets\output\export_import;

/**
 * Admin tool presets main controller class.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base {

    /** @var array Array map for the events. **/
    protected static $eventsactionsmap = [
        'base' => 'presets_listed',
        'delete' => 'preset_deleted',
        'export' => 'preset_exported',
        'import' => 'preset_imported',
        'preview' => 'preset_previewed',
        'load' => 'preset_loaded',
        'rollback' => 'preset_reverted',
        'download_xml' => 'preset_downloaded'
    ];

    /** @var string The main action (delete, export, import, load...). **/
    protected $action;

    /** @var string The mode (show, execute...). **/
    protected $mode;

    /** @var int Admin preset identifier. **/
    protected $id;

    /** @var int The output content to display in the page. **/
    protected $outputs;

    /** @var \moodleform The moodle form to display in the page. **/
    protected $moodleform;

    /** @var manager The manager helper class instance. **/
    protected $manager;

    /**
     * Loads common class attributes.
     */
    public function __construct() {
        $this->manager = new manager();
        $this->action = optional_param('action', 'base', PARAM_ALPHA);
        $this->mode = optional_param('mode', 'show', PARAM_ALPHAEXT);
        $this->id = optional_param('id', false, PARAM_INT);
    }

    /**
     * Method to list the presets available on the system
     *
     * It allows users to access the different preset
     * actions (preview, load, download, delete and rollback)
     */
    public function show(): void {
        global $DB, $OUTPUT;

        $options = new export_import();
        $this->outputs = $OUTPUT->render($options);

        $presets = $DB->get_records('adminpresets');
        $list = new presets_list($presets, true);
        $this->outputs .= $OUTPUT->render($list);
    }

    /**
     * Main display method
     *
     * Prints the block header and the common block outputs, the
     * selected action outputs, his form and the footer
     *
     * $outputs value depends on $mode and $action selected
     */
    public function display(): void {
        global $OUTPUT;

        $this->display_header();

        // Other outputs.
        if (!empty($this->outputs)) {
            echo $this->outputs;
        }

        // Form.
        if ($this->moodleform) {
            $this->moodleform->display();
        }

        // Footer.
        echo $OUTPUT->footer();
    }

    /**
     * Displays the header
     */
    protected function display_header(): void {
        global $PAGE, $OUTPUT, $SITE;

        // Strings.
        $titlestr = get_string('pluginname', 'tool_admin_presets');

        // Header.
        $PAGE->set_title($titlestr);
        $PAGE->set_heading($SITE->fullname);

        $title = $this->get_title();
        $text = $this->get_explanatory_description();

        // Only add it to the navbar if it's different to the plugin name (to avoid duplicates in the navbar).
        if ($title != get_string('pluginname', 'tool_admin_presets')) {
            $PAGE->navbar->add($title);
        }

        if ($node = $PAGE->settingsnav->find('tool_admin_presets', \navigation_node::TYPE_SETTING)) {
            $node->make_active();
        }

        echo $OUTPUT->header();
        echo $OUTPUT->heading($title);
        if ($text) {
            echo $OUTPUT->box($text);
        }
    }

    /**
     * Get page title for this action.
     *
     * @return string The page title to display into the page.
     */
    protected function get_title(): string {
        if ($this->action == 'base') {
            return get_string('pluginname', 'tool_admin_presets');
        }

        return get_string($this->action . $this->mode, 'tool_admin_presets');
    }

    /**
     * Get explanatory description to be displayed below the heading. It's optional and might change depending on the
     * action and the mode.
     *
     * @return string|null The explanatory description for the current action and mode.
     */
    protected function get_explanatory_description(): ?string {
        $text = null;
        if ($this->action == 'base') {
            $text = get_string('basedescription', 'tool_admin_presets');
        }

        return $text;
    }

    /**
     * Trigger an event based on the current action.
     *
     * @return void
     */
    public function log(): void {
        // The only read action we store is list presets and preview.
        $islist = ($this->action == 'base' && $this->mode == 'show');
        $ispreview = ($this->action == 'load' && $this->mode == 'show');
        if ($this->mode != 'show' || $islist || $ispreview) {
            $action = $this->action;
            if ($ispreview) {
                $action = 'preview';
            }

            if ($this->mode != 'execute' && $this->mode != 'show') {
                $action = $this->mode;
            }

            if (array_key_exists($action, self::$eventsactionsmap)) {
                $eventnamespace = '\\tool_admin_presets\\event\\' . self::$eventsactionsmap[$action];
                $eventdata = [
                    'context' => context_system::instance(),
                    'objectid' => $this->id
                ];
                $event = $eventnamespace::create($eventdata);
                $event->trigger();
            }
        }
    }
}
