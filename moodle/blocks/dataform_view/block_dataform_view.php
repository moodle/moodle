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
 * @package block_dataform_view
 * @copyright 2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 */
class block_dataform_view extends block_base {

    /**
     * Set the applicable formats for this block to all
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     *
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_dataform_view');
    }

    /**
     *
     */
    public function specialization() {
        global $CFG;

        $this->course = $this->page->course;

        // Load userdefined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_dataform_view');
        } else {
            $this->title = $this->config->title;
        }

        if (empty($this->config->dataform)) {
            if (!empty($this->config->view)) {
                $this->config->view = null;
                $this->config->filter = null;
            }
            return false;
        }

        if (empty($this->config->view)) {
            return false;
        }
    }

    /**
     *
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     *
     */
    public function get_content() {
        global $CFG, $DB, $SITE;

        $dataformid = !empty($this->config->dataform) ? $this->config->dataform : 0;
        $viewid = !empty($this->config->view) ? $this->config->view : 0;
        $filterid = !empty($this->config->filter) ? $this->config->filter : 0;
        $containerstyle = !empty($this->config->style) ? $this->config->style : null;

        // Validate dataform and reconfigure if needed.
        if (!$dataformid or !$DB->record_exists('dataform', array('id' => $dataformid))) {
            // We can get here if the dataform has been deleted.
            if (isset($this->config)) {
                $this->config->dataform = 0;
                $this->config->view = 0;
                $this->config->filter = 0;
                $this->instance_config_commit();
            }

            return null;
        }

        // Validate view and reconfigure if needed.
        if (!$viewid or !$DB->record_exists('dataform_views', array('id' => $viewid, 'dataid' => $dataformid))) {
            // We can get here if the view has been deleted.
            if (isset($this->config)) {
                $this->config->view = 0;
                $this->instance_config_commit();
            }

            return null;
        }

        // Validate filter.
        if (!$filterid or !$DB->record_exists('dataform_filters', array('id' => $filterid, 'dataid' => $dataformid))) {
            // Someone deleted the view after configuration.
            if (isset($this->config)) {
                $this->config->filter = 0;
                $this->instance_config_commit();
            }
        }

        // Return already generated content.
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (!empty($this->config->embed)) {
            $content = mod_dataform_dataform::get_content_embedded($dataformid, $viewid, $filterid, $containerstyle);
        } else {
            $content = mod_dataform_dataform::get_content_inline($dataformid, $viewid, $filterid);
        }

        if (!empty($content)) {
            $this->content->text = $content;
        }
        return $this->content;
    }

    /**
     *
     */
    public function hide_header() {
        if (isset($this->config->title) and empty($this->config->title)) {
            return true;
        }
        return false;
    }

}