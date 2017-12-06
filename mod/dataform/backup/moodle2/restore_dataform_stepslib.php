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
 * @package mod_dataform
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_dataform_activity_task.
 */

/**
 * Structure step to restore one dataform activity.
 */
class restore_dataform_activity_structure_step extends restore_activity_structure_step {

    /**
     *
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo'); // Restore content and user info (requires the backup users).

        $paths[] = new restore_path_element('dataform', '/activity/dataform');
        $paths[] = new restore_path_element('dataform_field', '/activity/dataform/fields/field');
        $paths[] = new restore_path_element('dataform_filter', '/activity/dataform/filters/filter');
        $paths[] = new restore_path_element('dataform_view', '/activity/dataform/views/view');

        if ($userinfo) {
            $paths[] = new restore_path_element('dataform_entry', '/activity/dataform/entries/entry');
            $paths[] = new restore_path_element('dataform_content', '/activity/dataform/entries/entry/contents/content');
            $paths[] = new restore_path_element('dataform_rating', '/activity/dataform/entries/entry/ratings/rating');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     *
     */
    protected function process_dataform($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timeavailable = $this->apply_date_offset($data->timeavailable);
        $data->timedue = $this->apply_date_offset($data->timedue);

        if ($data->grade < 0) {
            // Scale found, get mapping.
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        $newitemid = $this->task->get_activityid();

        if ($newitemid) {
            $data->id = $newitemid;
            $DB->update_record('dataform', $data);
        } else {
            // Insert the dataform record.
            $newitemid = $DB->insert_record('dataform', $data);
        }
        $this->apply_activity_instance($newitemid);
    }

    /**
     * This must be invoked immediately after creating/updating the "module" activity record
     * and will adjust the new activity id (the instance) in various places
     * Overriding the parent method to handle restoring into the activity.
     */
    protected function apply_activity_instance($newitemid) {
        global $DB;

        if ($newitemid == $this->task->get_activityid()) {
            // Remap task module id.
            $this->set_mapping('course_module', $this->task->get_old_moduleid(), $this->task->get_moduleid());
            // Remap task context id.
            $this->set_mapping('context', $this->task->get_old_contextid(), $this->task->get_contextid());
        } else {
            // Save activity id in task.
            $this->task->set_activityid($newitemid);
            // Apply the id to course_modules->instance.
            $DB->set_field('course_modules', 'instance', $newitemid, array('id' => $this->task->get_moduleid()));
        }
        // Do the mapping for modulename, preparing it for files by oldcontext.
        $oldid = $this->task->get_old_activityid();
        $this->set_mapping('dataform', $oldid, $newitemid, true);
    }

    /**
     *
     */
    protected function process_dataform_field($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->dataid = $this->get_new_parentid('dataform');

        // Insert the dataform_fields record.
        $newitemid = $DB->insert_record('dataform_fields', $data);
        $this->set_mapping('dataform_field', $oldid, $newitemid, true);

        // Process any field specific data.
        $data->id = $newitemid;
        $restoreclass = 'dataformfield_'. $data->type. "_restore";
        if (method_exists($restoreclass, 'process')) {
            $restoreclass::process($this, $data);
        }
    }

    /**
     *
     */
    protected function process_dataform_filter($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->dataid = $this->get_new_parentid('dataform');

        // Adjust customsort field ids.
        if ($data->customsort) {
            $customsort = unserialize($data->customsort);
            $sortfields = array();
            foreach ($customsort as $sortfield => $sortdir) {
                if ($sortfield > 0) {
                    $sortfields[$this->get_mappingid('dataform_field', $sortfield)] = $sortdir;
                } else {
                    $sortfields[$sortfield] = $sortdir;
                }
            }
            $data->customsort = serialize($sortfields);
        }

        // Adjust customsearch field ids.
        if ($data->customsearch) {
            $customsearch = unserialize($data->customsearch);
            $searchfields = array();
            foreach ($customsearch as $searchfield => $options) {
                if ($searchfield > 0) {
                    $searchfields[$this->get_mappingid('dataform_field', $searchfield)] = $options;
                } else {
                    $searchfields[$searchfield] = $options;
                }
            }
            $data->customsearch = serialize($searchfields);
        }

        // Insert the dataform_filters record.
        $newitemid = $DB->insert_record('dataform_filters', $data);
        $this->set_mapping('dataform_filter', $oldid, $newitemid, false); // No files associated.
    }

    /**
     *
     */
    protected function process_dataform_view($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->dataid = $this->get_new_parentid('dataform');

        // Adjust view filter id.
        if ($data->filterid) {
            $data->filterid = $this->get_mappingid('dataform_filter', $data->filterid);
        }

        // Adjust pattern field ids.
        if ($data->patterns) {
            $patterns = unserialize($data->patterns);
            $newpatterns = array();

            if (!empty($patterns['view'])) {
                $newpatterns['view'] = $patterns['view'];
            }

            if (!empty($patterns['field'])) {
                $newpatterns['field'] = array();
                foreach ($patterns['field'] as $fieldid => $tags) {
                    if ($fieldid > 0) {
                        $newpatterns['field'][$this->get_mappingid('dataform_field', $fieldid)] = $tags;
                    } else {
                        $newpatterns['field'][$fieldid] = $tags;
                    }
                }
            }
            $data->patterns = serialize($newpatterns);
        }

        // Insert the dataform_views record.
        $newitemid = $DB->insert_record('dataform_views', $data);
        $this->set_mapping('dataform_view', $oldid, $newitemid, true);

        // Process any view specific data.
        $data->id = $newitemid;
        $restoreclass = 'dataformview_'. $data->type. "_restore";
        if (method_exists($restoreclass, 'process')) {
            $restoreclass::process($this, $data);
        }
    }

    /**
     *
     */
    protected function process_dataform_entry($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->dataid = $this->get_new_parentid('dataform');

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        if ($userid = $this->task->get_ownerid()) {
            $data->userid = $userid;
        } else {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }
        $data->groupid = $this->get_mappingid('group', $data->groupid);

        // Insert the dataform_entries record.
        $newitemid = $DB->insert_record('dataform_entries', $data);
        $this->set_mapping('dataform_entry', $oldid, $newitemid, false); // No files associated.
    }

    /**
     *
     */
    protected function process_dataform_content($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->fieldid = $this->get_mappingid('dataform_field', $data->fieldid);
        $data->entryid = $this->get_new_parentid('dataform_entry');

        // Insert the data_content record.
        $newitemid = $DB->insert_record('dataform_contents', $data);
        $this->set_mapping('dataform_content', $oldid, $newitemid, true); // Files by this item id.
    }

    /**
     *
     */
    protected function process_dataform_rating($data) {
        $data = (object)$data;
        $data->itemid = $this->get_new_parentid('dataform_entry');
        $this->process_this_rating($data);
    }

    /**
     *
     */
    protected function process_this_rating($data) {
        global $DB;
        $data = (object)$data;

        $data->contextid = $this->task->get_contextid();
        if ($data->scaleid < 0) {
            // Scale found, get mapping.
            $data->scaleid = -($this->get_mappingid('scale', abs($data->scaleid)));
        }
        $data->rating = $data->value;
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('rating', $data);
    }

    /**
     * TODO Add preset related files, matching by itemname (data_content).
     * $this->add_related_files('mod_dataform', 'course_presets', 'dataform');
     *
     */
    protected function after_execute() {
        global $DB;
        // Add dataform related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_dataform', 'intro', null);
        $this->add_related_files('mod_dataform', 'activityicon', null);

        // Add content related files, matching by item id (dataform_content).
        $this->add_related_files('mod_dataform', 'content', 'dataform_content');

        $dataformnewid = $this->get_new_parentid('dataform');

        // Inline view.
        if ($inlineview = $DB->get_field('dataform', 'inlineview', array('id' => $dataformnewid))) {
            if ($inlineview = $this->get_mappingid('dataform_view', $inlineview)) {
                $DB->set_field('dataform', 'inlineview', $inlineview, array('id' => $dataformnewid));
            }
        }

        // Default view.
        if ($defaultview = $DB->get_field('dataform', 'defaultview', array('id' => $dataformnewid))) {
            if ($defaultview = $this->get_mappingid('dataform_view', $defaultview)) {
                $DB->set_field('dataform', 'defaultview', $defaultview, array('id' => $dataformnewid));
            }
        }

        // Default filter.
        if ($defaultfilter = $DB->get_field('dataform', 'defaultfilter', array('id' => $dataformnewid))) {
            if ($defaultfilter = $this->get_mappingid('dataform_filter', $defaultfilter)) {
                $DB->set_field('dataform', 'defaultfilter', $defaultfilter, array('id' => $dataformnewid));
            }
        }

        // Submission redirect view.
        $this->adjust_view_submission_redirect();

        // Process dataformview after execute.
        $this->after_execute_dataform_plugin('dataformview', 'dataform_view');

        // Process dataformfield after execute.
        $this->after_execute_dataform_plugin('dataformfield', 'dataform_field');
    }

    /**
     *
     */
    protected function after_execute_dataform_plugin($plugintype, $source) {
        $plugins = core_component::get_plugin_list($plugintype);
        foreach ($plugins as $type => $unused) {
            // Process any plugin specific data.
            $restoreclass = $plugintype. "_$type". "_restore";
            if (method_exists($restoreclass, 'after_execute')) {
                $restoreclass::after_execute($this);
            }

            // Add related files, matching by item id.
            $pluginclass = $plugintype. "_$type". "_$type";
            foreach ($pluginclass::get_file_areas() as $filearea) {
                $this->add_related_files($pluginclass, $filearea, $source);
            }
        }
    }

    /**
     * Adjusts view submission redirect view id.
     */
    protected function adjust_view_submission_redirect() {
        global $DB;

        $dataformnewid = $this->get_new_parentid('dataform');
        if ($views = $DB->get_records('dataform_views', array('dataid' => $dataformnewid))) {
            foreach ($views as $viewid => $view) {
                if (empty($view->submission)) {
                    continue;
                }
                $submission = unserialize(base64_decode($view->submission));
                if (empty($submission['redirect'])) {
                    continue;
                }
                $submission['redirect'] = $this->get_mappingid('dataform_view', $submission['redirect']);
                $submission = base64_encode(serialize($submission));

                $DB->set_field('dataform_views', 'submission', $submission, array('id' => $viewid));
            }
        }
    }

}
