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

namespace theme_boost\output\core;

use plugin_renderer_base;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/files/renderer.php');

/**
 * Rendering of files viewer related widgets.
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Rendering of files viewer related widgets.
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class files_renderer extends \core_files_renderer {
    /**
     * FileManager JS template for window with file information/actions.
     *
     */
    protected function fm_js_template_fileselectlayout() {
        $context = [
            'helpicon' => $this->help_icon('setmainfile', 'repository')
        ];
        return $this->render_from_template('core/filemanager_fileselect', $context);
    }

    /**
     * FileManager JS template for popup confirm dialogue window.
     *
     * @return string
     */
    protected function fm_js_template_confirmdialog() {
        return $this->render_from_template('core/filemanager_confirmdialog', []);
    }

    /**
     * Template for FilePicker with general layout (not QuickUpload).
     *
     *
     * @return string
     */
    protected function fp_js_template_generallayout() {
        return $this->render_from_template('core/filemanager_modal_generallayout', []);
    }

    /**
     * Returns html for displaying one file manager
     *
     * @param form_filemanager $fm
     * @return string
     */
    protected function fm_print_generallayout($fm) {
        $context = [
            'client_id' => $fm->options->client_id,
            'helpicon' => $this->help_icon('setmainfile', 'repository'),
            'restrictions' => $this->fm_print_restrictions($fm)
        ];
        return $this->render_from_template('core/filemanager_page_generallayout', $context);
    }

    /**
     * Returns HTML for default repository searchform to be passed to Filepicker
     *
     * This will be used as contents for search form defined in generallayout template
     * (form with id {TOOLSEARCHID}).
     * Default contents is one text input field with name="s"
     */
    public function repository_default_searchform() {
        return $this->render_from_template('core/filemanager_default_searchform', []);
    }

    /**
     * FilePicker JS template for 'Upload file' repository
     *
     * @return string
     */
    protected function fp_js_template_uploadform() {
        return $this->render_from_template('core/filemanager_uploadform', []);
    }

    /**
     * FilePicker JS template for repository login form including templates for each element type
     *
     * @return string
     */
    protected function fp_js_template_loginform() {
        return $this->render_from_template('core/filemanager_loginform', []);
    }

    /**
     * FilePicker JS template for window appearing to select a file.
     *
     * @return string
     */
    protected function fp_js_template_selectlayout() {
        return $this->render_from_template('core/filemanager_selectlayout', []);
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists
     * (multiple-file version).
     *
     * @return string
     */
    protected function fp_js_template_processexistingfilemultiple() {
        return $this->render_from_template('core/filemanager_processexistingfilemultiple', []);
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists.
     *
     * @return string
     */
    protected function fp_js_template_processexistingfile() {
        return $this->render_from_template('core/filemanager_processexistingfile', []);
    }
}
