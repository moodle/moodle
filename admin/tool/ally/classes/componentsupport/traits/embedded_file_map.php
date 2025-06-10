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
 * Trait for supporting embedded file mapping for html content.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport\traits;

use tool_ally\componentsupport\component_base;
use tool_ally\local;
use tool_ally\local_file;
use tool_ally\local_content;
use tool_ally\models\component;
use tool_ally\models\component_content;

use cache;
use coding_exception;
use context;
use context_block;
use context_course;
use file_storage;
use stored_file;
use moodle_url;

defined ('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/filestorage/file_storage.php');

trait embedded_file_map {

    /**
     * General purpose function for applying embedded file map to component content.
     *
     * @param component_content|null $content
     * @return component_content
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function apply_embedded_file_map(?component_content $content) {
        if (empty($content->content)) {
            return $content;
        }
        $html = $content->content;
        $results = local_content::get_pluginfiles_in_html($html);

        if (empty($results)) {
            return $content;
        }

        $fs = new file_storage();
        $component = local::get_component_instance($content->component);

        $componenttype = local::get_component_support_type($content->component);
        if ($componenttype === component_base::TYPE_MOD) {
            if ($content->table === $content->component) {
                try {
                    list($course, $cm) = get_course_and_cm_from_instance($content->id, $content->component);
                } catch (\Throwable $e) {
                    // We couldn't get $cm, we will get it next time. Just send the embedded draft file if present.
                    $drafturl = new moodle_url('/draftfile.php');
                    $drafturl = $drafturl->out(false);
                    foreach ($results as $result) {
                        if ($result->type == 'fullurl' && strpos($result->src, $drafturl) !== false) {
                            $props = local_file::get_fileurlproperties($result->src);
                            $file = local_file::get_file_fromprops($props);
                            $content->embeddedfiles[] = [
                                'filename' => rawurlencode($file->get_filename()),
                                'contenthash' => $file->get_contenthash(),
                                'pathnamehash' => $file->get_pathnamehash(),
                                'tag' => $result->tagname
                            ];
                        }
                    }
                    return $content;
                }
            } else {
                // Sub table detected - e.g. forum discussion, book chapter, etc...
                $moduleinstanceid = $component->resolve_module_instance_id($content->table, $content->id);
                list($course, $cm) = get_course_and_cm_from_instance($moduleinstanceid, $content->component);
            }
            $context = $cm->context;

            $compstr = 'mod_'.$content->component;
        } else if ($componenttype === component_base::TYPE_BLOCK) {
            $context = context_block::instance($content->id);
            $compstr = $content->component;
        } else {
            $courseid = $content->get_courseid();
            if (!$courseid) {
                return $content;
            }
            $context = context_course::instance($courseid);
            $compstr = $content->component;
        }

        foreach ($results as $result) {
            $file = null;
            if ($result->type == 'fullurl') {
                $props = local_file::get_fileurlproperties($result->src);
                if ($context->id != $props->contextid) {
                    // This link doesn't have the correct context, so we are going to skip it.
                    continue;
                }

                $file = local_file::get_file_fromprops($props);
            } else if ($result->type == 'pathonly') {
                $filename = $result->src;
                $filearea = $component->get_file_area($content->table, $content->field);
                if (!$filearea) {
                    throw new coding_exception('Failed to get filearea for component_content '.
                        var_export($content, true));
                }
                $fileitem = $component->get_file_item($content->table, $content->field, $content->id);
                $filepath = $component->get_file_path($content->table, $content->field, $content->id);
                $file = $fs->get_file($context->id, $compstr, $filearea, $fileitem, $filepath, $filename);
            }

            if ($file) {
                $content->embeddedfiles[] = [
                    'filename' => rawurlencode($file->get_filename()),
                    'pathnamehash' => $file->get_pathnamehash(),
                    'contenthash' => $file->get_contenthash(),
                    'tag' => $result->tagname
                ];
            }
        }

        return $content;
    }

    /**
     * Use the embedded filemap to check if the passed file is in use.
     *
     * @param stored_file $file The file to check
     * @param context $context The context to check in
     * @return bool
     */
    protected function check_embedded_file_in_use(stored_file $file, ?context $context = null): bool {
        global $DB;

        // We are going to cache any files used in this item instaces, so we don't have to search again.
        $cache = cache::make('tool_ally', 'fileinusecache');
        $contextid = $file->get_contextid();

        $files = $cache->get($contextid);
        if ($files == false) {
            $files = [];
            $filescontenthash = [];
            if (is_null($context)) {
                $context = context::instance_by_id($contextid);
            }

            if ($this->component_type() === self::TYPE_MOD) {
                if (!$instanceid = local::get_instanceid_for_cmid($context->instanceid)) {
                    // This is a pretty bad case. Just say the file is in use.
                    debugging("Could not get instance id for cm {$context->instanceid}", DEBUG_DEVELOPER);
                    return true;
                }
            } else {
                $instanceid = $context->instanceid;
            }

            $contents = $this->get_all_files_search_html($instanceid);
            if (is_null($contents)) {
                $contents = $this->get_all_html_content($instanceid);
            }

            foreach ($contents as $content) {
                if ($content instanceof component) {
                    // For some reason, some components return component, instead of component_contents at times. Like glossary.
                    $content = local_content::get_html_content($content->id, $content->component, $content->table, $content->field,
                        $content->courseid);
                }
                if (!$content) {
                    // If the content is non-existent, then skip.
                    continue;
                }

                $contentmap = $this->apply_embedded_file_map($content);

                foreach ($contentmap->embeddedfiles as $embeddedfile) {
                    $files[] = $embeddedfile['pathnamehash'];
                    $filescontenthash[] = $embeddedfile['contenthash'];
                }

            }

            if (!local::duringtesting()) {
                // We need to skip caching this during unit tests, otherwise... problems.
                $cache->set($contextid, $files);
            }
        }

        if (in_array($file->get_pathnamehash(), $files)) {
            return true;
        }

        // When Ally asks too early, this might be a file going from the draft area into the intro area.
        if (in_array($file->get_contenthash(), $filescontenthash)) {
            return true;
        }

        return false;
    }
}
