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
namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * The moodle_content_writer is the default Moodle implementation of a content writer.
 *
 * It exports data to a rich tree structure using Moodle's context system,
 * and produces a single zip file with all content.
 *
 * Objects of data are stored as JSON.
 *
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_content_writer implements content_writer {
    /**
     * @var string The base path on disk for this instance.
     */
    protected $path = null;

    /**
     * @var \context The current context of the writer.
     */
    protected $context = null;

    /**
     * @var \stored_file[] The list of files to be exported.
     */
    protected $files = [];

    /**
     * Constructor for the content writer.
     *
     * Note: The writer factory must be passed.
     *
     * @param   writer          $writer     The factory.
     */
    public function __construct(writer $writer) {
        $this->path = make_request_directory();
    }

    /**
     * Set the context for the current item being processed.
     *
     * @param   \context        $context    The context to use
     */
    public function set_context(\context $context) : content_writer {
        $this->context = $context;

        return $this;
    }

    /**
     * Export the supplied data within the current context, at the supplied subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   \stdClass       $data       The data to be exported
     * @return  content_writer
     */
    public function export_data(array $subcontext, \stdClass $data) : content_writer {
        $path = $this->get_path($subcontext, 'data.json');

        $this->write_data($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $this;
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
     * @return  content_writer
     */
    public function export_metadata(array $subcontext, string $key, $value, string $description) : content_writer {
        $path = $this->get_full_path($subcontext, 'metadata.json');

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path));
        } else {
            $data = (object) [];
        }

        $data->$key = (object) [
            'value' => $value,
            'description' => $description,
        ];

        $path = $this->get_path($subcontext, 'metadata.json');
        $this->write_data($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $this;
    }

    /**
     * Export a piece of related data.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $name       The name of the file to be exported.
     * @param   \stdClass       $data       The related data to export.
     * @return  content_writer
     */
    public function export_related_data(array $subcontext, $name, $data) : content_writer {
        $path = $this->get_path($subcontext, "{$name}.json");

        $this->write_data($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $this;
    }

    /**
     * Export a piece of data in a custom format.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $filename   The name of the file to be exported.
     * @param   string          $filecontent    The content to be exported.
     */
    public function export_custom_file(array $subcontext, $filename, $filecontent) : content_writer {
        $filename = clean_param($filename, PARAM_FILE);
        $path = $this->get_path($subcontext, $filename);
        $this->write_data($path, $filecontent);

        return $this;
    }

    /**
     * Prepare a text area by processing pluginfile URLs within it.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $component  The name of the component that the files belong to.
     * @param   string          $filearea   The filearea within that component.
     * @param   string          $itemid     Which item those files belong to.
     * @param   string          $text       The text to be processed
     * @return  string                      The processed string
     */
    public function rewrite_pluginfile_urls(array $subcontext, $component, $filearea, $itemid, $text) : string {
        return str_replace('@@PLUGINFILE@@/', $this->get_files_target_url($component, $filearea, $itemid).'/', $text);
    }

    /**
     * Export all files within the specified component, filearea, itemid combination.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $component  The name of the component that the files belong to.
     * @param   string          $filearea   The filearea within that component.
     * @param   string          $itemid     Which item those files belong to.
     */
    public function export_area_files(array $subcontext, $component, $filearea, $itemid) : content_writer {
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
    public function export_file(array $subcontext, \stored_file $file) : content_writer {
        if (!$file->is_directory()) {
            $pathitems = array_merge(
                $subcontext,
                [$this->get_files_target_path($file->get_component(), $file->get_filearea(), $file->get_itemid())],
                [$file->get_filepath()]
            );
            $path = $this->get_path($pathitems, $file->get_filename());
            check_dir_exists(dirname($path), true, true);
            $this->files[$path] = $file;
        }

        return $this;
    }

    /**
     * Export the specified user preference.
     *
     * @param   string          $component  The name of the component.
     * @param   string          $key        The name of th key to be exported.
     * @param   string          $value      The value of the preference
     * @param   string          $description    A description of the value
     * @return  content_writer
     */
    public function export_user_preference(string $component, string $key, string $value, string $description) : content_writer {
        $subcontext = [
            get_string('userpreferences'),
        ];
        $fullpath = $this->get_full_path($subcontext, "{$component}.json");
        $path = $this->get_path($subcontext, "{$component}.json");

        if (file_exists($fullpath)) {
            $data = json_decode(file_get_contents($fullpath));
        } else {
            $data = (object) [];
        }

        $data->$key = (object) [
            'value' => $value,
            'description' => $description,
        ];
        $this->write_data($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $this;
    }

    /**
     * Determine the path for the current context.
     *
     * @return  array                       The context path.
     */
    protected function get_context_path() : Array {
        $path = [];
        $contexts = array_reverse($this->context->get_parent_contexts(true));
        foreach ($contexts as $context) {
            $name = $context->get_context_name();
            $id = $context->id;
            $path[] = shorten_filename(clean_param("{$name} {$id}", PARAM_FILE), MAX_FILENAME_SIZE, true);
        }

        return $path;
    }

    /**
     * Get the relative file path within the current context, and subcontext, using the specified filename.
     *
     * @param   string[]        $subcontext The location within the current context to export this data.
     * @param   string          $name       The intended filename, including any extensions.
     * @return  string                      The fully-qualfiied file path.
     */
    protected function get_path(array $subcontext, string $name) : string {
        $subcontext = shorten_filenames($subcontext, MAX_FILENAME_SIZE, true);
        $name = shorten_filename($name, MAX_FILENAME_SIZE, true);

        // Combine the context path, and the subcontext data.
        $path = array_merge(
            $this->get_context_path(),
            $subcontext
        );

        // Join the directory together with the name.
        $filepath = implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . $name;

        // To use backslash, it must be doubled ("\\\\" PHP string).
        $separator = str_replace('\\', '\\\\', DIRECTORY_SEPARATOR);
        return preg_replace('@(' . $separator . '|/)+@', $separator, $filepath);
    }

    /**
     * Get the fully-qualified file path within the current context, and subcontext, using the specified filename.
     *
     * @param   string[]        $subcontext The location within the current context to export this data.
     * @param   string          $name       The intended filename, including any extensions.
     * @return  string                      The fully-qualfiied file path.
     */
    protected function get_full_path(array $subcontext, string $name) : string {
        $path = array_merge(
            [$this->path],
            [$this->get_path($subcontext, $name)]
        );

        // Join the directory together with the name.
        $filepath = implode(DIRECTORY_SEPARATOR, $path);

        // To use backslash, it must be doubled ("\\\\" PHP string).
        $separator = str_replace('\\', '\\\\', DIRECTORY_SEPARATOR);
        return preg_replace('@(' . $separator . '|/)+@', $separator, $filepath);
    }

    /**
     * Get a path within a subcontext where exported files should be written to.
     *
     * @param string $component The name of the component that the files belong to.
     * @param string $filearea The filearea within that component.
     * @param string $itemid Which item those files belong to.
     * @return string The path
     */
    protected function get_files_target_path($component, $filearea, $itemid) : string {

        // We do not need to include the component because we organise things by context.
        $parts = ['_files', $filearea];

        if (!empty($itemid)) {
            $parts[] = $itemid;
        }

        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * Get a relative url to the directory of the exported files within a subcontext.
     *
     * @param string $component The name of the component that the files belong to.
     * @param string $filearea The filearea within that component.
     * @param string $itemid Which item those files belong to.
     * @return string The url
     */
    protected function get_files_target_url($component, $filearea, $itemid) : string {
        // We do not need to include the component because we organise things by context.
        $parts = ['_files', $filearea];

        if (!empty($itemid)) {
            $parts[] = $itemid;
        }

        return implode('/', $parts);
    }

    /**
     * Write the data to the specified path.
     *
     * @param   string          $path       The path to export the data at.
     * @param   string          $data       The data to be exported.
     */
    protected function write_data(string $path, string $data) {
        $targetpath = $this->path . DIRECTORY_SEPARATOR . $path;
        check_dir_exists(dirname($targetpath), true, true);
        file_put_contents($targetpath, $data);
        $this->files[$path] = $targetpath;
    }

    /**
     * Perform any required finalisation steps and return the location of the finalised export.
     *
     * @return  string
     */
    public function finalise_content() : string {
        $exportfile = make_request_directory() . '/export.zip';

        $fp = get_file_packer();
        $fp->archive_to_pathname($this->files, $exportfile);

        // Reset the writer to prevent any further writes.
        writer::reset();

        return $exportfile;
    }
}
