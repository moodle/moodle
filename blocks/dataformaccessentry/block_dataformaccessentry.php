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
 * @package block_dataformaccessentry
 * @copyright 2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class block_dataformaccessentry extends block_base {

    public $dataformid;

    static public function get_extra_capabilities() {
        $capabilities = array();
        // Own Entry.
        $capabilities[] = 'mod/dataform:entryownview';
        $capabilities[] = 'mod/dataform:entryownexport';
        $capabilities[] = 'mod/dataform:entryownadd';
        $capabilities[] = 'mod/dataform:entryownupdate';
        $capabilities[] = 'mod/dataform:entryowndelete';

        // Group entry.
        $capabilities[] = 'mod/dataform:entrygroupview';
        $capabilities[] = 'mod/dataform:entrygroupexport';
        $capabilities[] = 'mod/dataform:entrygroupadd';
        $capabilities[] = 'mod/dataform:entrygroupupdate';
        $capabilities[] = 'mod/dataform:entrygroupdelete';

        // Any entry.
        $capabilities[] = 'mod/dataform:entryanyview';
        $capabilities[] = 'mod/dataform:entryanyexport';
        $capabilities[] = 'mod/dataform:entryanyadd';
        $capabilities[] = 'mod/dataform:entryanyupdate';
        $capabilities[] = 'mod/dataform:entryanydelete';

        // Anonymous entry.
        $capabilities[] = 'mod/dataform:entryanonymousview';
        $capabilities[] = 'mod/dataform:entryanonymousexport';
        $capabilities[] = 'mod/dataform:entryanonymousadd';
        $capabilities[] = 'mod/dataform:entryanonymousupdate';
        $capabilities[] = 'mod/dataform:entryanonymousdelete';

        return $capabilities;
    }

    /**
     * Set the applicable formats for this block.
     * @return array
     */
    public function applicable_formats() {
        return array('mod-dataform-access-index' => true);
    }

    /**
     *
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_dataformaccessentry');
    }

    /**
     *
     */
    public function specialization() {
        global $DB;

        if (!empty($this->config->name)) {
            $this->title = $this->config->name;
        }

        $dataformcmid = $DB->get_field('context', 'instanceid', array('id' => $this->instance->parentcontextid));
        $this->dataformid = $DB->get_field('course_modules', 'instance', array('id' => $dataformcmid));
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
        return null;
    }

    /**
     * Returns true if the entry passed in the data meets the rule filter criteria.
     *
     * @param array $data Expects entry object.
     * @return bool
     */
    public function is_applicable(array $data) {
        if (empty($data['entry'])) {
            return false;
        }

        // Get the filter.
        $filter = $this->get_filter($data);

        // Get entries manager.
        $entries = new mod_dataform_entry_manager($this->dataformid);
        // Check if entries found.
        if ($entries->count_entries(array('filter' => $filter))) {
            return true;
        }
        return false;
    }

    /**
     * Generates the rule filter.
     *
     * @param array $data Expects entry object.
     * @return bool
     */
    protected function get_filter(array $data) {
        $config = $this->config;

        // Get the filter.
        $fm = mod_dataform_filter_manager::instance($this->dataformid);
        $filterid = !empty($config->filterid) ? $config->filterid : 0;
        $filter = $fm->get_filter_by_id($filterid);

        // Add custom search.
        if (!empty($config->customsearch)) {
            $filter->append_search_options($config->customsearch);
        }

        // Add a search criterion for the entry id.
        $filter->eids = $data['entry']->id;

        return $filter;
    }

}
