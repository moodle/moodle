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
 * Checks and tracks if files in use by a component.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

use coding_exception;
use context;
use context_course;
use stdClass;
use stored_file;

/**
 * Checks and tracks if files in use by a component.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class files_in_use {

    protected static $instance = null;

    /**
     * Check and see if a file is in use.
     *
     * @param stored_file $file
     * @param context|null $context Context if you already have it. Prevents needing to reload it.
     * @return bool
     */
    public static function check_file_in_use(stored_file $file, context $context = null): bool {
        if (!get_config('tool_ally', 'excludeunused')) {
            // If the exclude setting is disabled, always return that the file is in use.
            return true;
        }

        $componentstr = $file->get_component();
        $filearea = $file->get_filearea();

        if (in_array($componentstr . '~' . $filearea, file_validator::ALWAYS_IN_USE)) {
            // This is a shortcut, since certain file areas are always 'in use', so we don't need to check more deeply.
            return true;
        }

        $existing = static::instance()->get_file_in_use_record($file);

        if ($existing && empty($existing->needsupdate)) {
            return (bool) $existing->inuse;
        }

        if (is_null($context)) {
            $contextid = $file->get_contextid();
            try {
                $context = context::instance_by_id($file->get_contextid());
            } catch (\Exception $e) {
                // Just return true in the unknown case.
                debugging("Could not get context id {$contextid}.", DEBUG_DEVELOPER);
                return true;
            }
        }

        return static::instance()->file_in_use_update($file, $context, $existing);
    }

    /**
     * Get an instance of this.
     *
     * @return files_in_use
     */
    public static function instance(): self {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Set any records from the provided context to need updating.
     *
     * @param context $context
     */
    public static function set_context_needs_updating(context $context): void {
        global $DB;

        $DB->set_field('tool_ally_file_in_use', 'needsupdate', 1, ['contextid' => $context->id]);
    }

    /**
     * Set the file id as needing updating.
     *
     * @param int $fileid
     */
    public static function set_file_needs_updating(int $fileid): void {
        global $DB;

        $DB->set_field('tool_ally_file_in_use', 'needsupdate', 1, ['fileid' => $fileid]);
    }

    /**
     * Mark any files related to a group description as needing updating.
     *
     * @param int $groupid
     * @param int $contextid
     */
    public static function set_group_needs_updating(int $groupid, int $contextid) {
        global $DB;

        // Specifically designed to use index on files table.
        $select = 'fileid IN (SELECT id FROM {files}
                               WHERE component = ?
                                 AND filearea = ?
                                 AND contextid = ?
                                 AND itemid = ?)';
        $params = ['group', 'description', $contextid, $groupid];

        $DB->set_field_select('tool_ally_file_in_use', 'needsupdate', 1, $select, $params);
    }

    /**
     * Delete the record for the specified file.
     *
     * @param int $fileid
     */
    public static function delete_file_record(int $fileid): void {
        global $DB;

        $DB->delete_records('tool_ally_file_in_use', ['fileid' => $fileid]);
    }

    /**
     * Delete all records for a specified course.
     *
     * @param int $courseid
     */
    public static function delete_course_records(int $courseid): void {
        global $DB;

        $DB->delete_records('tool_ally_file_in_use', ['courseid' => $courseid]);
    }

    /**
     * Delete all the records for a specified context.
     *
     * @param int $contextid
     */
    public static function delete_context_records(int $contextid): void {
        global $DB;

        $DB->delete_records('tool_ally_file_in_use', ['contextid' => $contextid]);
    }

    /**
     * Get the record for a specified file instance.
     *
     * @param stored_file $file
     * @return stdClass|null
     */
    protected function get_file_in_use_record(stored_file $file): ?stdClass {
        global $DB;

        $record = $DB->get_record('tool_ally_file_in_use', ['fileid' => $file->get_id()]);

        if (!$record) {
            return null;
        }

        return $record;
    }

    /**
     * Get true file in use status, and update the record if it has changed.
     *
     * @param stored_file $file
     * @param context $context
     * @param stdClass|null $existing
     * @return bool
     */
    protected function file_in_use_update(stored_file $file, context $context, stdClass $existing = null): bool {
        global $DB;

        $componentstr = $file->get_component();
        $filearea = $file->get_filearea();

        if (in_array($componentstr . '~' . $filearea, file_validator::ALWAYS_IN_USE)) {
            // This is a shortcut, since certain file areas are always 'in use', so we don't need to check more deeply.
            return true;
        }

        if (class_exists('\restore_controller', false) && \restore_controller::is_executing()) {
            // This is a special case, because during a restore a bunch of things can be broken.
            if (!$existing) {
                // If it doesn't exist, then we will create it, and mark it as needing updating.
                // This will send the file to Ally on the next cron run.
                $record = [
                    'fileid' => $file->get_id(),
                    'contextid' => $context->id,
                    'courseid' => local_file::courseid($file),
                    'inuse' => 0,
                    'needsupdate' => 1
                ];
                $DB->insert_record_raw('tool_ally_file_in_use', $record, false);

                return false;
            }

            // In this case, there is an existing record, we mark it as needing updating, and just resturn the current value.
            static::set_file_needs_updating($file->get_id());
            if (isset($existing->inuse)) {
                return (bool)$existing->inuse;
            }
            return false;
        }

        if ($component = local_content::component_instance($componentstr)) {
            // If we have component support, we are going use that to check if the file is in use.
            $inuse = $component->check_file_in_use($file, $context);
            $this->update_file_in_use($file, $context, $inuse, $existing);
            return $inuse;
        }

        // Check if it is a group description file, and if it is in use. Returns null only if not a group description file.
        $inuse = $this->check_group_file_in_use($file, $context);

        if (is_null($inuse)) {
            // Do the final fallback course module check.
            $inuse = $this->fallback_check_file_in_use($file, $context);
        }

        $this->update_file_in_use($file, $context, $inuse, $existing);

        return $inuse;
    }

    /**
     * Use a backup method to see if a file is in use. This tries to find a table/column for an unknown module.
     * Default to 'in use' if we can't figure it out.
     *
     * @param stored_file $file
     * @param context $context
     * @return bool
     */
    protected function fallback_check_file_in_use(stored_file $file, context $context): bool {
        global $DB;

        $componentstr = $file->get_component();
        $filearea = $file->get_filearea();

        $cleancomponentstr = local::clean_component_string($componentstr);
        // For module components, we are going to check to see if there is a column with the filearea name that is
        // a text or varchar, and if so check the contents for the file.
        if (strpos($componentstr, 'mod_') !== 0 || !($columns = $DB->get_columns($cleancomponentstr))
            || !isset($columns[$filearea])) {
            debugging("Not from a module component {$cleancomponentstr}", DEBUG_DEVELOPER);
            return true;
        }

        if (!$instanceid = local::get_instanceid_for_cmid($context->instanceid)) {
            debugging("Could not get instance id for cm {$context->instanceid}", DEBUG_DEVELOPER);
            return true;
        }

        if (!$record = $DB->get_record($cleancomponentstr, ['id' => $instanceid])) {
            debugging("Could not get instance record for {$cleancomponentstr} id {$instanceid}", DEBUG_DEVELOPER);
            return true;
        }

        // Get the full path and name of the file.
        return $this->check_file_is_in_html($file, $record->$filearea, $context);
    }

    /**
     * Checks if a file is for a group description, and if so, returns if it is in use or not.
     * Returns null if file is not for a group description.
     *
     * @param stored_file $file
     * @param context $context
     * @return bool|null Returns null if file is not for a group description.
     */
    protected function check_group_file_in_use(stored_file $file, context $context): ?bool {
        global $DB;

        if ($file->get_component() !== 'group' || $file->get_filearea() !== 'description') {
            return null;
        }

        if (!$record = $DB->get_record('groups', ['id' => $file->get_itemid()])) {
            debugging("Could not get instance record for group id {$file->get_itemid()}", DEBUG_DEVELOPER);
            return true;
        }

        return $this->check_file_is_in_html($file, $record->description, $context);
    }

    /**
     * Checks if a given file is in use in a provided HTML string.
     *
     * @param stored_file $file
     * @param string $html
     * @param context $context
     */
    protected function check_file_is_in_html(stored_file $file, string $html, context $context): bool {

        $fullfilename = ltrim($file->get_filepath() . $file->get_filename(), '/');
        $foundfiles = local_content::get_pluginfiles_in_html($html);
        if (empty($foundfiles)) {
            return false;
        }

        // We are going to check the found files to see if it matches the filename we have.
        foreach ($foundfiles as $foundfile) {
            if ($foundfile->type == 'fullurl') {
                $props = local_file::get_fileurlproperties($foundfile->src);
                if (empty($props) || empty($props->filename) || $context->id != $props->contextid) {
                    // If we didn't get the properties, filename, or the context doesn't match the current one, skip.
                    continue;
                }

                if ($props->filename == $fullfilename) {
                    return true;
                }
            } else if ($foundfile->type == 'pathonly') {
                if ($foundfile->src == $fullfilename) {
                    return true;
                }
            }
        }

        // If we got here, then it means we didn't find the file.
        return false;
    }

    /**
     * Update the file in use record as required.
     *
     * @param stored_file $file
     * @param context $context
     * @param bool $inuse
     * @param stdClass|null $existing The existing record. If null, assumes no record exists.
     */
    protected function update_file_in_use(stored_file $file, context $context, bool $inuse, stdClass $existing = null): void {
        global $DB;

        if (is_null($existing)) {
            // If there is no existing record, then send the file if in use. Ignore otherwise.
            $record = [
                'fileid' => $file->get_id(),
                'contextid' => $context->id,
                'courseid' => local_file::courseid($file),
                'inuse' => $inuse ? 1 : 0,
                'needsupdate' => 0
            ];
            $DB->insert_record_raw('tool_ally_file_in_use', $record, false);

            if ($inuse) {
                $this->queue_file_update($file, $inuse);
            }

            return;
        }

        if ((bool)$existing->inuse === $inuse) {
            // The record already exists, and there is no change.
            $DB->set_field('tool_ally_file_in_use', 'needsupdate', 0, ['id' => $existing->id]);
            return;
        }

        // We are now at the point where there is a change in in use status. Send that change, and update the record.
        $record = [
            'id' => $existing->id,
            'inuse' => $inuse ? 1 : 0,
            'needsupdate' => 0
        ];
        $DB->update_record_raw('tool_ally_file_in_use', $record);

        $this->queue_file_update($file, $inuse);

    }

    /**
     * Actually queue up or send the updated file state to Ally.
     *
     * @param stored_file $file
     * @param bool $inuse
     */
    protected function queue_file_update(stored_file $file, bool $inuse) {
        if ($inuse) {
            // TODO - need to queue this up and send with task.
            file_processor::push_file_update($file, false);
            cache::instance()->invalidate_file_keys($file);

            return;
        } else {
            local_file::queue_file_for_deletion($file);
            return;
        }
    }
}
