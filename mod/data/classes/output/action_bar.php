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

namespace mod_data\output;

use moodle_url;

/**
 * Class responsible for generating the action bar elements in the database module pages.
 *
 * @package    mod_data
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_bar {

    /** @var int $id The database module id. */
    private $id;

    /** @var moodle_url $currenturl The URL of the current page. */
    private $currenturl;

    /**
     * The class constructor.
     *
     * @param int $id The database module id.
     * @param moodle_url $pageurl The URL of the current page.
     */
    public function __construct(int $id, moodle_url $pageurl) {
        $this->id = $id;
        $this->currenturl = $pageurl;
    }

    /**
     * Generate the output for the action bar in the field page.
     *
     * @param bool $hasfieldselect Whether the field selector element should be rendered.
     * @param bool $hassaveaspreset Whether the save as preset button element should be rendered.
     * @param bool $hasexportpreset Whether the export as preset button element should be rendered.
     * @return string The HTML code for the action bar.
     */
    public function get_fields_action_bar(bool $hasfieldselect = false, bool $hassaveaspreset = false,
            bool $hasexportpreset = false): string {
        global $PAGE, $DB;

        $createfieldlink = new moodle_url('/mod/data/field.php', ['d' => $this->id]);
        $importlink = new moodle_url('/mod/data/field.php', ['d' => $this->id, 'mode' => 'import']);
        $presetslink = new moodle_url('/mod/data/field.php', ['d' => $this->id, 'mode' => 'usepreset']);

        $menu = [
            $createfieldlink->out(false) => get_string('managefields', 'mod_data'),
            $importlink->out(false) => get_string('importpreset', 'mod_data'),
            $presetslink->out(false) => get_string('usestandard', 'mod_data'),
        ];

        $selected = $createfieldlink->out(false);
        $mode = $this->currenturl->get_param('mode');

        if ($mode == 'import') {
            $selected = $importlink->out(false);
        } else if ($mode === 'usepreset') {
            $selected = $presetslink->out(false);
        }

        $urlselect = new \url_select($menu, $selected, null, 'fieldactionselect');
        $urlselect->set_label(get_string('fieldsnavigation', 'mod_data'), ['class' => 'sr-only']);

        $fieldselect = null;
        if ($hasfieldselect) {
            // Get the list of possible fields (plugins).
            $plugins = \core_component::get_plugin_list('datafield');
            $menufield = [];

            foreach ($plugins as $plugin => $fulldir) {
                $menufield[$plugin] = get_string('pluginname', "datafield_{$plugin}");
            }
            asort($menufield);

            $fieldselecturl = new moodle_url('/mod/data/field.php', ['d' => $this->id, 'mode' => 'new']);
            $fieldselect = new \single_select($fieldselecturl, 'newtype', $menufield, null, get_string('newfield', 'data'),
                'fieldform');
            $fieldselect->set_label(get_string('newfield', 'mod_data'), ['class' => 'sr-only']);
        }

        $saveaspresetbutton = null;
        $exportpresetbutton = null;
        $hasfields = $DB->record_exists('data_fields', ['dataid' => $this->id]);

        if ($hasfields) {
            if ($hassaveaspreset) {
                $saveaspresetlink = new moodle_url('/mod/data/preset.php',
                    ['d' => $this->id, 'action' => 'export']);
                $saveaspresetbutton = new \single_button($saveaspresetlink,
                    get_string('saveaspreset', 'mod_data'), 'post', false);
            }

            if ($hasexportpreset) {
                $exportpresetlink = new moodle_url('/mod/data/preset.php',
                    ['d' => $this->id, 'action' => 'export']);
                $exportpresetbutton = new \single_button($exportpresetlink,
                    get_string('exportpreset', 'mod_data'), 'get', false);
            }
        }
        $renderer = $PAGE->get_renderer('mod_data');
        $fieldsactionbar = new fields_action_bar($this->id, $urlselect, $fieldselect, $saveaspresetbutton,
            $exportpresetbutton);

        return $renderer->render_fields_action_bar($fieldsactionbar);
    }

    /**
     * Generate the output for the action selector in the view page.
     *
     * @param bool $hasentries Whether entries exist.
     * @return string The HTML code for the action selector.
     */
    public function get_view_action_bar(bool $hasentries): string {
        global $PAGE;

        $viewlistlink = new moodle_url('/mod/data/view.php', ['d' => $this->id]);
        $viewsinglelink = new moodle_url('/mod/data/view.php', ['d' => $this->id, 'mode' => 'single']);

        $menu = [
            $viewlistlink->out(false) => get_string('listview', 'mod_data'),
            $viewsinglelink->out(false) => get_string('singleview', 'mod_data'),
        ];

        $activeurl = $this->currenturl;

        if ($this->currenturl->get_param('rid') || $this->currenturl->get_param('mode') == 'single') {
            $activeurl = $viewsinglelink;
        }

        $urlselect = new \url_select($menu, $activeurl->out(false), null, 'viewactionselect');
        $urlselect->set_label(get_string('viewnavigation', 'mod_data'), ['class' => 'sr-only']);
        $renderer = $PAGE->get_renderer('mod_data');
        $viewactionbar = new view_action_bar($this->id, $urlselect, $hasentries);

        return $renderer->render_view_action_bar($viewactionbar);
    }

    /**
     * Generate the output for the action selector in the templates page.
     *
     * @return string The HTML code for the action selector.
     */
    public function get_templates_action_bar(): string {
        global $PAGE, $DB;

        $listtemplatelink = new moodle_url('/mod/data/templates.php', ['d' => $this->id,
            'mode' => 'listtemplate']);
        $singletemplatelink = new moodle_url('/mod/data/templates.php', ['d' => $this->id,
            'mode' => 'singletemplate']);
        $advancedsearchtemplatelink = new moodle_url('/mod/data/templates.php', ['d' => $this->id,
            'mode' => 'asearchtemplate']);
        $addtemplatelink = new moodle_url('/mod/data/templates.php', ['d' => $this->id, 'mode' => 'addtemplate']);
        $rsstemplatelink = new moodle_url('/mod/data/templates.php', ['d' => $this->id, 'mode' => 'rsstemplate']);
        $csstemplatelink = new moodle_url('/mod/data/templates.php', ['d' => $this->id, 'mode' => 'csstemplate']);
        $jstemplatelink = new moodle_url('/mod/data/templates.php', ['d' => $this->id, 'mode' => 'jstemplate']);

        $menu = [
            $listtemplatelink->out(false) => get_string('listtemplate', 'mod_data'),
            $singletemplatelink->out(false) => get_string('singletemplate', 'mod_data'),
            $advancedsearchtemplatelink->out(false) => get_string('asearchtemplate', 'mod_data'),
            $addtemplatelink->out(false) => get_string('addtemplate', 'mod_data'),
            $rsstemplatelink->out(false) => get_string('rsstemplate', 'mod_data'),
            $csstemplatelink->out(false) => get_string('csstemplate', 'mod_data'),
            $jstemplatelink->out(false) => get_string('jstemplate', 'mod_data'),
        ];

        $urlselect = new \url_select($menu, $this->currenturl->out(false), null, 'templatesactionselect');
        $urlselect->set_label(get_string('templatesnavigation', 'mod_data'), ['class' => 'sr-only']);

        $hasfields = $DB->record_exists('data_fields', ['dataid' => $this->id]);

        $saveaspresetbutton = null;
        $exportpresetbutton = null;

        if ($hasfields) {
            $saveaspresetlink = new moodle_url('/mod/data/preset.php',
                ['d' => $this->id, 'action' => 'export']);
            $saveaspresetbutton = new \single_button($saveaspresetlink,
                get_string('saveaspreset', 'mod_data'), 'post', false);

            $exportpresetlink = new moodle_url('/mod/data/preset.php',
                ['d' => $this->id, 'action' => 'export', 'sesskey' => sesskey()]);
            $exportpresetbutton = new \single_button($exportpresetlink,
                get_string('exportpreset', 'mod_data'), 'get', false);
        }

        $renderer = $PAGE->get_renderer('mod_data');
        $templatesactionbar = new templates_action_bar($this->id, $urlselect, $saveaspresetbutton,
            $exportpresetbutton);

        return $renderer->render_templates_action_bar($templatesactionbar);
    }

    /**
     * Generate the output for the action selector in the presets page.
     *
     * @return string The HTML code for the action selector.
     */
    public function get_presets_action_bar(): string {
        global $PAGE;

        $renderer = $PAGE->get_renderer('mod_data');
        $presetsactionbar = new presets_action_bar($this->id);

        return $renderer->render_presets_action_bar($presetsactionbar);
    }
}
