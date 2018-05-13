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
 * This file contains the moodle format implementation of the content writer.
 *
 * @package core_privacy
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\tests\request;

defined('MOODLE_INTERNAL') || die();

/**
 * An implementation of the content_writer for use in unit tests.
 *
 * This implementation does not export any data but instead stores it in
 * structures within the instance which can be easily queried for use
 * during unit tests.
 *
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_writer implements \core_privacy\local\request\content_writer {
    /**
     * @var \context The context currently being exported.
     */
    protected $context;

    /**
     * @var \stdClass The collection of metadata which has been exported.
     */
    protected $metadata;

    /**
     * @var \stdClass The data which has been exported.
     */
    protected $data;

    /**
     * @var \stdClass The related data which has been exported.
     */
    protected $relateddata;

    /**
     * @var \stdClass The list of stored files which have been exported.
     */
    protected $files;

    /**
     * @var \stdClass The custom files which have been exported.
     */
    protected $customfiles;

    /**
     * @var \stdClass The user preferences which have been exported.
     */
    protected $userprefs;

    /**
     * Whether any data has been exported at all within the current context.
     *
     * @param array $subcontext The location within the current context that this data belongs -
     *   in this method it can be partial subcontext path (or none at all to check presence of any data anywhere).
     *   User preferences never have subcontext, if $subcontext is specified, user preferences are not checked.
     * @return  bool
     */
    public function has_any_data($subcontext = []) {
        if (empty($subcontext)) {
            // When subcontext is not specified check presence of user preferences in this context and in system context.
            $hasuserprefs = !empty($this->userprefs->{$this->context->id});
            $systemcontext = \context_system::instance();
            $hasglobaluserprefs = !empty($this->userprefs->{$systemcontext->id});
            if ($hasuserprefs || $hasglobaluserprefs) {
                return true;
            }
        }

        foreach (['data', 'relateddata', 'metadata', 'files', 'customfiles'] as $datatype) {
            if (!property_exists($this->$datatype, $this->context->id)) {
                // No data of this type for this context at all. Continue to the next data type.
                continue;
            }
            $basepath = $this->$datatype->{$this->context->id};
            foreach ($subcontext as $subpath) {
                if (!isset($basepath->children->$subpath)) {
                    // No data of this type is present for this path. Continue to the next data type.
                    continue 2;
                }
                $basepath = $basepath->children->$subpath;
            }
            if (!empty($basepath)) {
                // Some data found for this type for this subcontext.
                return true;
            }
        }
        return false;
    }

    /**
     * Whether any data has been exported for any context.
     *
     * @return  bool
     */
    public function has_any_data_in_any_context() {
        $checkfordata = function($location) {
            foreach ($location as $context => $data) {
                if (!empty($data)) {
                    return true;
                }
            }

            return false;
        };

        $hasanydata = $checkfordata($this->data);
        $hasanydata = $hasanydata || $checkfordata($this->relateddata);
        $hasanydata = $hasanydata || $checkfordata($this->metadata);
        $hasanydata = $hasanydata || $checkfordata($this->files);
        $hasanydata = $hasanydata || $checkfordata($this->customfiles);
        $hasanydata = $hasanydata || $checkfordata($this->userprefs);

        return $hasanydata;
    }

    /**
     * Constructor for the content writer.
     *
     * Note: The writer_factory must be passed.
     * @param   \core_privacy\local\request\writer          $writer    The writer factory.
     */
    public function __construct(\core_privacy\local\request\writer $writer) {
        $this->data = (object) [];
        $this->relateddata = (object) [];
        $this->metadata = (object) [];
        $this->files = (object) [];
        $this->customfiles = (object) [];
        $this->userprefs = (object) [];
    }

    /**
     * Set the context for the current item being processed.
     *
     * @param   \context        $context    The context to use
     */
    public function set_context(\context $context) {
        $this->context = $context;

        if (isset($this->data->{$this->context->id}) && empty((array) $this->data->{$this->context->id})) {
            $this->data->{$this->context->id} = (object) [
                'children' => (object) [],
                'data' => [],
            ];
        }

        if (isset($this->relateddata->{$this->context->id}) && empty((array) $this->relateddata->{$this->context->id})) {
            $this->relateddata->{$this->context->id} = (object) [
                'children' => (object) [],
                'data' => [],
            ];
        }

        if (isset($this->metadata->{$this->context->id}) && empty((array) $this->metadata->{$this->context->id})) {
            $this->metadata->{$this->context->id} = (object) [
                'children' => (object) [],
                'data' => [],
            ];
        }

        if (isset($this->files->{$this->context->id}) && empty((array) $this->files->{$this->context->id})) {
            $this->files->{$this->context->id} = (object) [
                'children' => (object) [],
                'data' => [],
            ];
        }

        if (isset($this->customfiles->{$this->context->id}) && empty((array) $this->customfiles->{$this->context->id})) {
            $this->customfiles->{$this->context->id} = (object) [
                'children' => (object) [],
                'data' => [],
            ];
        }

        if (isset($this->userprefs->{$this->context->id}) && empty((array) $this->userprefs->{$this->context->id})) {
            $this->userprefs->{$this->context->id} = (object) [
                'children' => (object) [],
                'data' => [],
            ];
        }

        return $this;
    }

    /**
     * Return the current context.
     *
     * @return  \context
     */
    public function get_current_context() {
        return $this->context;
    }

    /**
     * Export the supplied data within the current context, at the supplied subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   \stdClass       $data       The data to be exported
     */
    public function export_data(array $subcontext, \stdClass $data) {
        $current = $this->fetch_root($this->data, $subcontext);
        $current->data = $data;

        return $this;
    }

    /**
     * Get all data within the subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @return  array                       The metadata as a series of keys to value + descrition objects.
     */
    public function get_data(array $subcontext = []) {
        return $this->fetch_data_root($this->data, $subcontext);
    }

    /**
     * Export metadata about the supplied subcontext.
     *
     * Metadata consists of a key/value pair and a description of the value.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $key        The metadata name.
     * @param   string          $value      The metadata value.
     * @param   string          $description    The description of the value.
     * @return  $this
     */
    public function export_metadata(array $subcontext, $key, $value, $description) {
        $current = $this->fetch_root($this->metadata, $subcontext);
        $current->data[$key] = (object) [
                'value' => $value,
                'description' => $description,
            ];

        return $this;
    }

    /**
     * Get all metadata within the subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @return  array                       The metadata as a series of keys to value + descrition objects.
     */
    public function get_all_metadata(array $subcontext = []) {
        return $this->fetch_data_root($this->metadata, $subcontext);
    }

    /**
     * Get the specified metadata within the subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $key        The metadata to be fetched within the context + subcontext.
     * @param   boolean         $valueonly  Whether to fetch only the value, rather than the value + description.
     * @return  array                       The metadata as a series of keys to value + descrition objects.
     */
    public function get_metadata(array $subcontext = [], $key, $valueonly = true) {
        $keys = $this->get_all_metadata($subcontext);

        if (isset($keys[$key])) {
            $metadata = $keys[$key];
        } else {
            return null;
        }

        if ($valueonly) {
            return $metadata->value;
        } else {
            return $metadata;
        }
    }

    /**
     * Export a piece of related data.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $name       The name of the file to be exported.
     * @param   \stdClass       $data       The related data to export.
     */
    public function export_related_data(array $subcontext, $name, $data) {
        $current = $this->fetch_root($this->relateddata, $subcontext);
        $current->data[$name] = $data;

        return $this;
    }

    /**
     * Get all data within the subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $filename   The name of the intended filename.
     * @return  array                       The metadata as a series of keys to value + descrition objects.
     */
    public function get_related_data(array $subcontext = [], $filename = null) {
        $current = $this->fetch_data_root($this->relateddata, $subcontext);

        if (null === $filename) {
            return $current;
        }

        if (isset($current[$filename])) {
            return $current[$filename];
        }

        return [];
    }

    /**
     * Export a piece of data in a custom format.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $filename   The name of the file to be exported.
     * @param   string          $filecontent    The content to be exported.
     */
    public function export_custom_file(array $subcontext, $filename, $filecontent) {
        $filename = clean_param($filename, PARAM_FILE);

        $current = $this->fetch_root($this->customfiles, $subcontext);
        $current->data[$filename] = $filecontent;

        return $this;
    }

    /**
     * Get the specified custom file within the subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $filename   The name of the file to be fetched within the context + subcontext.
     * @return  string                      The content of the file.
     */
    public function get_custom_file(array $subcontext = [], $filename = null) {
        $current = $this->fetch_data_root($this->customfiles, $subcontext);

        if (null === $filename) {
            return $current;
        }

        if (isset($current[$filename])) {
            return $current[$filename];
        }

        return null;
    }

    /**
     * Prepare a text area by processing pluginfile URLs within it.
     *
     * Note that this method does not implement the pluginfile URL rewriting. Such a job tightly depends on how the
     * actual writer exports files so it can be reliably tested only in real writers such as
     * {@link core_privacy\local\request\moodle_content_writer}.
     *
     * However we have to remove @@PLUGINFILE@@ since otherwise {@link format_text()} shows debugging messages
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $component  The name of the component that the files belong to.
     * @param   string          $filearea   The filearea within that component.
     * @param   string          $itemid     Which item those files belong to.
     * @param   string          $text       The text to be processed
     * @return  string                      The processed string
     */
    public function rewrite_pluginfile_urls(array $subcontext, $component, $filearea, $itemid, $text) {
        return str_replace('@@PLUGINFILE@@/', 'files/', $text);
    }

    /**
     * Export all files within the specified component, filearea, itemid combination.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $component  The name of the component that the files belong to.
     * @param   string          $filearea   The filearea within that component.
     * @param   string          $itemid     Which item those files belong to.
     */
    public function export_area_files(array $subcontext, $component, $filearea, $itemid) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, $component, $filearea, $itemid);
        foreach ($files as $file) {
            $this->export_file($subcontext, $file);
        }

        return $this;
    }

    /**
     * Export the specified file in the target location.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   \stored_file    $file       The file to be exported.
     */
    public function export_file(array $subcontext, \stored_file $file) {
        if (!$file->is_directory()) {
            $filepath = $file->get_filepath();
            // Directory separator in the stored_file class should always be '/'. The following line is just a fail safe.
            $filepath = str_replace(DIRECTORY_SEPARATOR, '/', $filepath);
            $filepath = explode('/', $filepath);
            $filepath[] = $file->get_filename();
            $filepath = array_filter($filepath);
            $filepath = implode('/', $filepath);

            $current = $this->fetch_root($this->files, $subcontext);
            $current->data[$filepath] = $file;
        }

        return $this;
    }

    /**
     * Get all files in the specfied subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @return  \stored_file[]              The list of stored_files in this context + subcontext.
     */
    public function get_files(array $subcontext = []) {
        return $this->fetch_data_root($this->files, $subcontext);
    }

    /**
     * Export the specified user preference.
     *
     * @param   string          $component  The name of the component.
     * @param   string          $key        The name of th key to be exported.
     * @param   string          $value      The value of the preference
     * @param   string          $description    A description of the value
     * @return  \core_privacy\local\request\content_writer
     */
    public function export_user_preference(
        $component,
        $key,
        $value,
        $description
    ) {
        $prefs = $this->fetch_root($this->userprefs, []);

        if (!isset($prefs->{$component})) {
            $prefs->{$component} = (object) [];
        }

        $prefs->{$component}->$key = (object) [
            'value' => $value,
            'description' => $description,
        ];

        return $this;
    }

    /**
     * Get all user preferences for the specified component.
     *
     * @param   string          $component  The name of the component.
     * @return  \stdClass
     */
    public function get_user_preferences($component) {
        $context = \context_system::instance();
        $prefs = $this->fetch_root($this->userprefs, [], $context->id);
        if (isset($prefs->{$component})) {
            return $prefs->{$component};
        } else {
            return (object) [];
        }
    }

    /**
     * Get all user preferences for the specified component.
     *
     * @param   string          $component  The name of the component.
     * @return  \stdClass
     */
    public function get_user_context_preferences($component) {
        $prefs = $this->fetch_root($this->userprefs, []);
        if (isset($prefs->{$component})) {
            return $prefs->{$component};
        } else {
            return (object) [];
        }
    }

    /**
     * Perform any required finalisation steps and return the location of the finalised export.
     *
     * @return  string
     */
    public function finalise_content() {
        return 'mock_path';
    }

    /**
     * Fetch the entire root record at the specified location type, creating it if required.
     *
     * @param   \stdClass   $base The base to use - e.g. $this->data
     * @param   array       $subcontext The subcontext to fetch
     * @param   int         $temporarycontextid A temporary context ID to use for the fetch.
     * @return  array
     */
    protected function fetch_root($base, $subcontext, $temporarycontextid = null) {
        $contextid = !empty($temporarycontextid) ? $temporarycontextid : $this->context->id;
        if (!isset($base->{$contextid})) {
            $base->{$contextid} = (object) [
                'children' => (object) [],
                'data' => [],
            ];
        }

        $current = $base->{$contextid};
        foreach ($subcontext as $node) {
            if (!isset($current->children->{$node})) {
                $current->children->{$node} = (object) [
                    'children' => (object) [],
                    'data' => [],
                ];
            }
            $current = $current->children->{$node};
        }

        return $current;
    }

    /**
     * Fetch the data region of the specified root.
     *
     * @param   \stdClass   $base The base to use - e.g. $this->data
     * @param   array       $subcontext The subcontext to fetch
     * @return  array
     */
    protected function fetch_data_root($base, $subcontext) {
        $root = $this->fetch_root($base, $subcontext);

        return $root->data;
    }
}
