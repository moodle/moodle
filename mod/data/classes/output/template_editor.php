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

use templatable;
use renderable;
use mod_data\manager;
use moodle_url;
use texteditor;

/**
 * Renderable class for template editor.
 *
 * @package    mod_data
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_editor implements templatable, renderable {

    /** @var manager manager instance. */
    private $manager;

    /** @var string the template name. */
    private $templatename;

    /**
     * The class constructor.
     *
     * @param manager $manager the activity instance manager
     * @param string $templatename the template to edit
     */
    public function __construct(manager $manager, string $templatename) {
        $this->manager = $manager;
        $this->templatename = $templatename;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $instance = $this->manager->get_instance();
        $cm = $this->manager->get_coursemodule();

        $data = [
            'title' => get_string('header' . $this->templatename, 'data'),
            'sesskey' => sesskey(),
            'disableeditor' => true,
            'url' => new moodle_url('/mod/data/templates.php', ['id' => $cm->id, 'mode' => $this->templatename]),
        ];

        // Determine whether to use HTML editors.
        $usehtmleditor = false;
        $disableeditor = false;
        if (($this->templatename !== 'csstemplate') && ($this->templatename !== 'jstemplate')) {
            $usehtmleditor = data_get_config($instance, "editor_{$this->templatename}", true);
            $disableeditor = true;
        }
        $data['usehtmleditor'] = $usehtmleditor;
        // Some templates, like CSS, cannot enable the wysiwyg editor.
        $data['disableeditor'] = $disableeditor;

        $tools = new template_editor_tools($this->manager, $this->templatename);
        $data['toolbar'] = $tools->export_for_template($output);
        $data['editors'] = $this->get_editors_data($usehtmleditor);

        return $data;
    }

    /**
     * Get the editors data.
     *
     * @param bool $usehtmleditor if the user wants wysiwyg editor or not
     * @return array editors data
     */
    private function get_editors_data(bool $usehtmleditor): array {
        global $PAGE;

        $result = [];
        $manager = $this->manager;
        $instance = $manager->get_instance();

        // Setup editor.
        editors_head_setup();
        $PAGE->requires->js_call_amd(
            'mod_data/templateseditor',
            'init',
            ['d' => $instance->id, 'mode' => $this->templatename]
        );

        $format = FORMAT_PLAIN;
        if ($usehtmleditor) {
            $format = FORMAT_HTML;
        }

        $editor = editors_get_preferred_editor($format);

        // Add editors.
        if ($this->templatename === 'listtemplate') {
            $template = $manager->get_template('listtemplateheader');
            $result[] = $this->generate_editor_data(
                $editor,
                'header',
                'listtemplateheader',
                $template->get_template_content()
            );
            $maineditorname = 'multientry';
        } else {
            $maineditorname = $this->templatename;
        }

        $template = $manager->get_template($this->templatename);
        $result[] = $this->generate_editor_data(
            $editor,
            $maineditorname,
            $this->templatename,
            $template->get_template_content()
        );

        if ($this->templatename === 'listtemplate') {
            $template = $manager->get_template('listtemplatefooter');
            $result[] = $this->generate_editor_data(
                $editor,
                'footer',
                'listtemplatefooter',
                $template->get_template_content()
            );
        }

        if ($this->templatename === 'rsstemplate') {
            $template = $manager->get_template('rsstitletemplate');
            $result[] = $this->generate_editor_data(
                $editor,
                'rsstitletemplate',
                'rsstitletemplate',
                $template->get_template_content()
            );
        }

        return $result;
    }

    /**
     * Generate a single editor data.
     *
     * @param texteditor $editor the editor object
     * @param string $name the editor name
     * @param string $fieldname the field name
     * @param string|null $value the current value
     * @return array the editor data
     */
    private function generate_editor_data(
        texteditor $editor,
        string $name,
        string $fieldname,
        ?string $value
    ): array {
        $options = [
            'trusttext' => false,
            'forcehttps' => false,
            'subdirs' => false,
            'maxfiles' => 0,
            'maxbytes' => 0,
            'changeformat' => 0,
            'noclean' => false,
        ];

        $result = [
            'name' => get_string($name, 'data'),
            'fieldname' => $fieldname,
            'value' => $value,
        ];
        $editor->set_text($value);
        $editor->use_editor($fieldname, $options);
        return $result;
    }
}
