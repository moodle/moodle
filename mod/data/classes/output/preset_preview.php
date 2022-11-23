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
use mod_data\preset;
use mod_data\template;
use moodle_page;
use moodle_url;

/**
 * Preset preview output class.
 *
 * @package    mod_data
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preset_preview implements templatable, renderable {

    /** @var manager manager instance. */
    private $manager;

    /** @var preset the preset. */
    private $preset;

    /** @var string the template to preview. */
    private $templatename;

    /**
     * The class constructor.
     *
     * @param manager $manager the activity instance manager
     * @param preset $preset the preset
     * @param string $templatename the templatename
     */
    public function __construct(manager $manager, preset $preset, string $templatename) {
        $this->manager = $manager;
        $this->preset = $preset;
        $this->templatename = $templatename;
    }

    /**
     * Add the preset CSS and JS to the page.
     *
     * @param moodle_page $page the current page instance
     */
    public function prepare_page(moodle_page $page) {
        $instance = $this->manager->get_instance();
        $preset = $this->preset;
        // Add CSS and JS.
        $csscontent = $preset->get_template_content('csstemplate');
        if (!empty($csscontent)) {
            $url = new moodle_url('/mod/data/css.php', ['d' => $instance->id, 'preset' => $preset->get_fullname()]);
            $page->requires->css($url);
        }
        $jscontent = $preset->get_template_content('jstemplate');
        if (!empty($jscontent)) {
            $url = new moodle_url('/mod/data/js.php', ['d' => $instance->id, 'preset' => $preset->get_fullname()]);
            $page->requires->js($url);
        }
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $coursemodule = $this->manager->get_coursemodule();
        $preset = $this->preset;

        // Get fields for preview.
        $count = ($this->templatename == 'listtemplate') ? 2 : 1;
        $fields = $preset->get_fields(true);
        $entries = $preset->get_sample_entries($count);
        $templatecontent = $preset->get_template_content($this->templatename);
        $useurl = new moodle_url('/mod/data/field.php');

        // Generate preview content.
        $options = ['templatename' => $this->templatename];
        if ($this->templatename == 'listtemplate') {
            $options['showmore'] = true;
        }
        $parser = new template($this->manager, $templatecontent, $options, $fields);
        $content = $parser->parse_entries($entries);
        if ($this->templatename == 'listtemplate') {
            $listtemplateheader = $preset->get_template_content('listtemplateheader');
            $listtemplatefooter = $preset->get_template_content('listtemplatefooter');
            $content = $listtemplateheader . $content . $listtemplatefooter;
        }

        return [
            'cmid' => $coursemodule->id,
            'description' => $preset->description ?? '',
            'preview' => $content,
            'formactionurl' => $useurl->out(),
            'userid' => $preset->get_userid() ?? 0,
            'shortname' => $preset->shortname,
        ];
    }
}
