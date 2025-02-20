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
 * Database activity renderer.
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_data
 */

use mod_data\manager;

defined('MOODLE_INTERNAL') || die();

class mod_data_renderer extends plugin_renderer_base {

    /**
     * @deprecated since Moodle 4.1 MDL-75140 - please do not use this class any more.
     */
     #[\core\attribute\deprecated('mod_data_renderer::importing_preset()', since: '4.1', mdl: 'MDL-75140', final: true)]
    public function import_setting_mappings(): void {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * Importing a preset on a database module.
     *
     * @param stdClass $datamodule  Database module to import to.
     * @param \mod_data\local\importer\preset_importer $importer Importer instance to use for the importing.
     *
     * @return string
     */
    public function importing_preset(stdClass $datamodule, \mod_data\local\importer\preset_importer $importer): string {

        $strwarning = get_string('mappingwarning', 'data');

        $params = $importer->settings;
        $newfields = $params->importfields;
        $currentfields = $params->currentfields;

        $html = html_writer::start_tag('div', ['class' => 'presetmapping']);
        $html .= html_writer::start_tag('form', ['method' => 'post', 'action' => '']);
        $html .= html_writer::start_tag('div');
        $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'finishimport']);
        $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'd', 'value' => $datamodule->id]);

        $inputselector = $importer->get_preset_selector();
        $html .= html_writer::empty_tag(
                'input',
                ['type' => 'hidden', 'name' => $inputselector['name'], 'value' => $inputselector['value']]
        );

        if (!empty($newfields)) {
            $table = new html_table();
            $table->data = array();

            foreach ($newfields as $nid => $newfield) {
                $row = array();
                $row[0] = html_writer::tag('label', $newfield->name, array('for'=>'id_'.$newfield->name));
                $attrs = ['name' => 'field_' . $nid, 'id' => 'id_' . $newfield->name, 'class' => 'form-select'];
                $row[1] = html_writer::start_tag('select', $attrs);

                $selected = false;
                foreach ($currentfields as $cid => $currentfield) {
                    if ($currentfield->type != $newfield->type) {
                        continue;
                    }
                    if ($currentfield->name == $newfield->name) {
                        $row[1] .= html_writer::tag(
                            'option',
                            get_string('mapexistingfield', 'data', $currentfield->name),
                            ['value' => $cid, 'selected' => 'selected']
                        );
                        $selected = true;
                    } else {
                        $row[1] .= html_writer::tag(
                            'option',
                            get_string('mapexistingfield', 'data', $currentfield->name),
                            ['value' => $cid]
                        );
                    }
                }

                if ($selected) {
                    $row[1] .= html_writer::tag('option', get_string('mapnewfield', 'data'), array('value'=>'-1'));
                } else {
                    $row[1] .= html_writer::tag('option', get_string('mapnewfield', 'data'), array('value'=>'-1', 'selected'=>'selected'));
                }

                $row[1] .= html_writer::end_tag('select');
                $table->data[] = $row;
            }
            $html .= html_writer::table($table);
            $html .= html_writer::tag('p', $strwarning);
        } else {
            $html .= $this->output->notification(get_string('nodefinedfields', 'data'));
        }

        $html .= html_writer::start_tag('div', array('class'=>'overwritesettings'));
        $attrs = ['type' => 'checkbox', 'name' => 'overwritesettings', 'id' => 'overwritesettings', 'class' => 'me-2'];
        $html .= html_writer::empty_tag('input', $attrs);
        $html .= html_writer::tag('label', get_string('overwritesettings', 'data'), ['for' => 'overwritesettings']);
        $html .= html_writer::end_tag('div');

        $actionbuttons = html_writer::start_div();
        $cancelurl = new moodle_url('/mod/data/field.php', ['d' => $datamodule->id]);
        $actionbuttons .= html_writer::tag('a', get_string('cancel') , [
            'href' => $cancelurl->out(false),
            'class' => 'btn btn-secondary mx-1',
            'role' => 'button',
        ]);
        $actionbuttons .= html_writer::empty_tag('input', [
            'type' => 'submit',
            'class' => 'btn btn-primary mx-1',
            'value' => get_string('continue'),
        ]);
        $actionbuttons .= html_writer::end_div();

        $stickyfooter = new core\output\sticky_footer($actionbuttons);
        $html .= $this->render($stickyfooter);

        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');
        $html .= html_writer::end_tag('div');

        return $html;
    }

    /**
     * Renders the action bar for the field page.
     *
     * @param \mod_data\output\fields_action_bar $actionbar
     * @return string The HTML output
     */
    public function render_fields_action_bar(\mod_data\output\fields_action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this);
        return $this->render_from_template('mod_data/action_bar', $data);
    }

    /**
     * Renders the fields page footer.
     *
     * @param manager $manager the instance manager
     * @return string The HTML output
     *
     * @deprecated since Moodle 4.5 - please do not use this function anymore
     */
    #[\core\attribute\deprecated(null, reason: 'It is no longer used', since: '4.5')]
    public function render_fields_footer(manager $manager): string {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $cm = $manager->get_coursemodule();
        $pageurl = new moodle_url('/mod/data/templates.php', ['id' => $cm->id]);
        return $this->render_from_template('mod_data/fields_footer', [
            'pageurl' => $pageurl->out(false),
        ]);
    }

    /**
     * Renders the action bar for the view page.
     *
     * @param \mod_data\output\view_action_bar $actionbar
     * @return string The HTML output
     */
    public function render_view_action_bar(\mod_data\output\view_action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this);
        return $this->render_from_template('mod_data/view_action_bar', $data);
    }

    /**
     * Renders the action bar for the template page.
     *
     * @param \mod_data\output\templates_action_bar $actionbar
     * @return string The HTML output
     */
    public function render_templates_action_bar(\mod_data\output\templates_action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this);
        return $this->render_from_template('mod_data/templates_action_bar', $data);
    }

    /**
     * Renders the action bar for the preset page.
     *
     * @param \mod_data\output\presets_action_bar $actionbar
     * @return string The HTML output
     */
    public function render_presets_action_bar(\mod_data\output\presets_action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this);
        return $this->render_from_template('mod_data/presets_action_bar', $data);
    }

    /**
     * Renders the presets table in the preset page.
     *
     * @param \mod_data\output\presets $presets
     * @return string The HTML output
     */
    public function render_presets(\mod_data\output\presets $presets): string {
        $data = $presets->export_for_template($this);
        return $this->render_from_template('mod_data/presets', $data);
    }

    /**
     * Renders the default template.
     *
     * @param \mod_data\output\defaulttemplate $template
     * @return string The HTML output
     */
    public function render_defaulttemplate(\mod_data\output\defaulttemplate $template): string {
        $data = $template->export_for_template($this);
        return $this->render_from_template($template->get_templatename(), $data);
    }

    /**
     * Renders the action bar for the zero state (no fields created) page.
     *
     * @param \mod_data\manager $manager The manager instance.
     *
     * @return string The HTML output
     */
    public function render_database_zero_state(\mod_data\manager $manager): string {
        $actionbar = new \mod_data\output\zero_state_action_bar($manager);
        $data = $actionbar->export_for_template($this);
        if (empty($data)) {
            // No actions for the user.
            $data['title'] = get_string('activitynotready');
            $data['intro'] = get_string('comebacklater');
            $data['noitemsimgurl'] = $this->output->image_url('noentries_zero_state', 'mod_data')->out();
        } else {
            $data['title'] = get_string('startbuilding', 'mod_data');
            $data['intro'] = get_string('createactivity', 'mod_data');
            $data['noitemsimgurl'] = $this->output->image_url('view_zero_state', 'mod_data')->out();
        }

        return $this->render_from_template('mod_data/zero_state', $data);
    }

    /**
     * Renders the action bar for an empty database view page.
     *
     * @param \mod_data\manager $manager The manager instance.
     *
     * @return string The HTML output
     */
    public function render_empty_database(\mod_data\manager $manager): string {
        $actionbar = new \mod_data\output\empty_database_action_bar($manager);
        $data = $actionbar->export_for_template($this);
        $data['noitemsimgurl'] = $this->output->image_url('view_zero_state', 'mod_data')->out();

        return $this->render_from_template('mod_data/view_noentries', $data);
    }

    /**
     * Renders the action bar for the zero state (no fields created) page.
     *
     * @param \mod_data\manager $manager The manager instance.
     *
     * @return string The HTML output
     */
    public function render_fields_zero_state(\mod_data\manager $manager): string {
        $data = [
            'noitemsimgurl' => $this->output->image_url('fields_zero_state', 'mod_data')->out(),
            'title' => get_string('nofields', 'mod_data'),
            'intro' => get_string('createfields', 'mod_data'),
            ];
        if ($manager->can_manage_templates()) {
            $actionbar = new \mod_data\output\action_bar($manager->get_instance()->id, $this->page->url);
            $createfieldbutton = $actionbar->get_create_fields();
            $data['createfieldbutton'] = $createfieldbutton->export_for_template($this);
        }

        return $this->render_from_template('mod_data/zero_state', $data);
    }

    /**
     * Renders the action bar for the templates zero state (no fields created) page.
     *
     * @param \mod_data\manager $manager The manager instance.
     *
     * @return string The HTML output
     */
    public function render_templates_zero_state(\mod_data\manager $manager): string {
        $actionbar = new \mod_data\output\zero_state_action_bar($manager);
        $data = $actionbar->export_for_template($this);
        $data['title'] = get_string('notemplates', 'mod_data');
        $data['intro'] = get_string('createtemplates', 'mod_data');
        $data['noitemsimgurl'] = $this->output->image_url('templates_zero_state', 'mod_data')->out();

        return $this->render_from_template('mod_data/zero_state', $data);
    }
}
