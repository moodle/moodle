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
     * @var array The list of plugins that have been checked to see if they are installed.
     */
    protected $checkedplugins = [];

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
        // Need to take into consideration the subcontext to provide the full path to this file.
        $subcontextpath = '';
        if (!empty($subcontext)) {
            $subcontextpath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $subcontext);
        }
        $path = $this->get_context_path();
        $path = implode(DIRECTORY_SEPARATOR, $path) . $subcontextpath;
        $returnstring = $path . DIRECTORY_SEPARATOR . $this->get_files_target_url($component, $filearea, $itemid) . '/';
        $returnstring = clean_param($returnstring, PARAM_PATH);

        return str_replace('@@PLUGINFILE@@/', $returnstring, $text);
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
            $id = '_.' . $context->id;
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

        // This weird code is to look for a subcontext that contains a number and append an '_' to the front.
        // This is because there seems to be some weird problem with array_merge_recursive used in finalise_content().
        $subcontext = array_map(function($data) {
            if (stripos($data, DIRECTORY_SEPARATOR) !== false) {
                $newpath = explode(DIRECTORY_SEPARATOR, $data);
                $newpath = array_map(function($value) {
                    if (is_numeric($value)) {
                        return '_' . $value;
                    }
                    return $value;
                }, $newpath);
                return implode(DIRECTORY_SEPARATOR, $newpath);
            } else if (is_numeric($data)) {
                $data = '_' . $data;
            }
            return $data;
        }, $subcontext);

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
            $parts[] = '_' . $itemid;
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
     * Copy a file to the specified path.
     *
     * @param  array  $path        Current location of the file.
     * @param  array  $destination Destination path to copy the file to.
     */
    protected function copy_data(array $path, array $destination) {
        global $CFG;
        $filename = array_pop($destination);
        $destdirectory = implode(DIRECTORY_SEPARATOR, $destination);
        $fulldestination = $this->path . DIRECTORY_SEPARATOR . $destdirectory;
        check_dir_exists($fulldestination, true, true);
        $fulldestination .= $filename;
        $currentpath = $CFG->dirroot . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path);
        copy($currentpath, $fulldestination);
        $this->files[$destdirectory . DIRECTORY_SEPARATOR . $filename] = $fulldestination;
    }

    /**
     * This creates three different bits of data from all of the files that will be
     * exported.
     * $tree - A multidimensional array of the navigation tree structure.
     * $treekey - An array with the short path of the file and element data for
     *            html (data_file_{number} or 'No var')
     * $allfiles - All *.json files that need to be added as an index to be referenced
     *             by the js files to display the user data.
     *
     * @return array returns a tree, tree key, and a list of all files.
     */
    protected function prepare_for_export() : Array {
        $tree = [];
        $treekey = [];
        $allfiles = [];
        $i = 1;
        foreach ($this->files as $shortpath => $fullfile) {

            // Generate directory tree as an associative array.
            $items = explode(DIRECTORY_SEPARATOR, $shortpath);
            $newitems = $this->condense_array($items);
            $tree = array_merge_recursive($tree, $newitems);

            if (is_string($fullfile)) {
                $filearray = explode(DIRECTORY_SEPARATOR, $shortpath);
                $filename = array_pop($filearray);
                $filenamearray = explode('.', $filename);
                // Don't process files that are not json files.
                if (end($filenamearray) !== 'json') {
                    continue;
                }
                // Chop the last two characters of the extension. json => js.
                $filename = substr($filename, 0, -2);
                array_push($filearray, $filename);
                $newshortpath = implode(DIRECTORY_SEPARATOR, $filearray);

                $varname = 'data_file_' . $i;
                $i++;

                $quicktemp = clean_param($shortpath, PARAM_PATH);
                $treekey[$quicktemp] = $varname;
                $allfiles[$varname] = clean_param($newshortpath, PARAM_PATH);

                // Need to load up the current json file and add a variable (varname mentioned above) at the start.
                // Then save it as a js file.
                $content = $this->get_file_content($fullfile);
                $jsondecodedcontent = json_decode($content);
                $jsonencodedcontent = json_encode($jsondecodedcontent, JSON_PRETTY_PRINT);
                $variablecontent = 'var ' . $varname . ' = ' . $jsonencodedcontent;

                $this->write_data($newshortpath, $variablecontent);
            } else {
                $treekey[clean_param($shortpath, PARAM_PATH)] = 'No var';
            }
        }
        return [$tree, $treekey, $allfiles];
    }

    /**
     * Add more detail to the tree to help with sorting and display in the renderer.
     *
     * @param  array  $tree       The file structure currently as a multidimensional array.
     * @param  array  $treekey    An array of the current file paths.
     * @param  array  $currentkey The current short path of the tree.
     * @return array An array of objects that has additional data.
     */
    protected function make_tree_object(array $tree, array $treekey, array $currentkey = []) : Array {
        $newtree = [];
        // Try to extract the context id and then add the context object.
        $addcontext = function($index, $object) {
            if (stripos($index, '_.') !== false) {
                $namearray = explode('_.', $index);
                $contextid = array_pop($namearray);
                if (is_numeric($contextid)) {
                    $object[$index]->name = implode('_.', $namearray);
                    $object[$index]->context = \context::instance_by_id($contextid);
                }
            } else {
                $object[$index]->name = $index;
            }
        };
        // Just add the final data to the tree object.
        $addfinalfile = function($directory, $treeleaf, $file) use ($treekey) {
            $url = implode(DIRECTORY_SEPARATOR, $directory);
            $url = clean_param($url, PARAM_PATH);
            $treeleaf->name = $file;
            $treeleaf->itemtype = 'item';
            $gokey = clean_param($url . '/' . $file, PARAM_PATH);
            if (isset($treekey[$gokey]) && $treekey[$gokey] !== 'No var') {
                $treeleaf->datavar = $treekey[$gokey];
            } else {
                $treeleaf->url = new \moodle_url($url . '/' . $file);
            }
        };

        foreach ($tree as $key => $value) {
            $newtree[$key] = new \stdClass();
            if (is_array($value)) {
                $newtree[$key]->itemtype = 'treeitem';
                // The array merge recursive adds a numeric index, and so we only add to the current
                // key if it is now numeric.
                $currentkey = is_numeric($key) ? $currentkey : array_merge($currentkey, [$key]);

                // Try to extract the context id and then add the context object.
                $addcontext($key, $newtree);
                $newtree[$key]->children = $this->make_tree_object($value, $treekey, $currentkey);

                if (!is_numeric($key)) {
                    // We're heading back down the tree, so remove the last key.
                    array_pop($currentkey);
                }
            } else {
                // If the key is not numeric then we want to add a directory and put the file under that.
                if (!is_numeric($key)) {
                    $newtree[$key]->itemtype = 'treeitem';
                    // Try to extract the context id and then add the context object.
                    $addcontext($key, $newtree);
                     array_push($currentkey, $key);

                    $newtree[$key]->children[$value] = new \stdClass();
                    $addfinalfile($currentkey, $newtree[$key]->children[$value], $value);
                    array_pop($currentkey);
                } else {
                    // If the key is just a number then we just want to show the file instead.
                    $addfinalfile($currentkey, $newtree[$key], $value);
                }
            }
        }
        return $newtree;
    }

    /**
     * Sorts the tree list into an order that makes more sense.
     * Order is:
     * 1 - Items with a context first, the lower the number the higher up the tree.
     * 2 - Items that are directories.
     * 3 - Items that are log directories.
     * 4 - Links to a page.
     *
     * @param  array $tree The tree structure to order.
     */
    protected function sort_my_list(array &$tree) {
        uasort($tree, function($a, $b) {
            if (isset($a->context) && isset($b->context)) {
                return $a->context->contextlevel <=> $b->context->contextlevel;
            }
            if (isset($a->context) && !isset($b->context)) {
                return -1;
            }
            if (isset($b->context) && !isset($a->context)) {
                return 1;
            }
            if ($a->itemtype == 'treeitem' && $b->itemtype == 'treeitem') {
                // Ugh need to check that this plugin has not been uninstalled.
                if ($this->check_plugin_is_installed('tool_log')) {
                    if (trim($a->name) == get_string('privacy:path:logs', 'tool_log')) {
                        return 1;
                    } else if (trim($b->name) == get_string('privacy:path:logs', 'tool_log')) {
                        return -1;
                    }
                    return 0;
                }
            }
            if ($a->itemtype == 'treeitem' && $b->itemtype == 'item') {
                return -1;
            }
            if ($b->itemtype == 'treeitem' && $a->itemtype == 'item') {
                return 1;
            }
            return 0;
        });
        foreach ($tree as $treeobject) {
            if (isset($treeobject->children)) {
                $this->sort_my_list($treeobject->children);
            }
        }
    }

    /**
     * Check to see if a specified plugin is installed.
     *
     * @param  string $component The component name e.g. tool_log
     * @return bool Whether this component is installed.
     */
    protected function check_plugin_is_installed(string $component) : Bool {
        if (!isset($this->checkedplugins[$component])) {
            $pluginmanager = \core_plugin_manager::instance();
            $plugin = $pluginmanager->get_plugin_info($component);
            $this->checkedplugins[$component] = !is_null($plugin);
        }
        return $this->checkedplugins[$component];
    }

    /**
     * Writes the appropriate files for creating an HTML index page for human navigation of the user data export.
     */
    protected function write_html_data() {
        global $PAGE, $SITE, $USER, $CFG;

        // Do this first before adding more files to $this->files.
        list($tree, $treekey, $allfiles) = $this->prepare_for_export();
        // Add more detail to the tree such as contexts.
        $richtree = $this->make_tree_object($tree, $treekey);
        // Now that we have more detail we can use that to sort it.
        $this->sort_my_list($richtree);

        // Copy over the JavaScript required to display the html page.
        $jspath = ['privacy', 'export_files', 'general.js'];
        $targetpath = ['js', 'general.js'];
        $this->copy_data($jspath, $targetpath);

        $jquery = ['lib', 'jquery', 'jquery-3.2.1.min.js'];
        $jquerydestination = ['js', 'jquery-3.2.1.min.js'];
        $this->copy_data($jquery, $jquerydestination);

        $requirecurrentpath = ['lib', 'requirejs', 'require.min.js'];
        $destination = ['js', 'require.min.js'];
        $this->copy_data($requirecurrentpath, $destination);

        $treepath = ['lib', 'amd', 'build', 'tree.min.js'];
        $destination = ['js', 'tree.min.js'];
        $this->copy_data($treepath, $destination);

        // Icons to be used.
        $expandediconpath = ['pix', 't', 'expanded.svg'];
        $this->copy_data($expandediconpath, ['pix', 'expanded.svg']);
        $collapsediconpath = ['pix', 't', 'collapsed.svg'];
        $this->copy_data($collapsediconpath, ['pix', 'collapsed.svg']);
        $naviconpath = ['pix', 'i', 'navigationitem.svg'];
        $this->copy_data($naviconpath, ['pix', 'navigationitem.svg']);
        $moodleimgpath = ['pix', 'moodlelogo.svg'];
        $this->copy_data($moodleimgpath, ['pix', 'moodlelogo.svg']);

        // Additional required css.
        $csspath = ['theme', 'boost', 'style', 'moodle.css'];
        $destination = ['moodle.css'];
        $this->copy_data($csspath, $destination);

        $csspath = ['privacy', 'export_files', 'general.css'];
        $destination = ['general.css'];
        $this->copy_data($csspath, $destination);

        // Create an index file that lists all, to be newly created, js files.
        $encoded = json_encode($allfiles,  JSON_PRETTY_PRINT);
        $encoded = 'var user_data_index = ' . $encoded;

        $path = 'js' . DIRECTORY_SEPARATOR . 'data_index.js';
        $this->write_data($path, $encoded);

        $output = $PAGE->get_renderer('core_privacy');
        $navigationpage = new \core_privacy\output\exported_navigation_page(current($richtree));
        $navigationhtml = $output->render_navigation($navigationpage);

        $systemname = $SITE->fullname;
        $fullusername = fullname($USER);
        $siteurl = $CFG->wwwroot;

        // Create custom index.html file.
        $rtl = right_to_left();
        $htmlpage = new \core_privacy\output\exported_html_page($navigationhtml, $systemname, $fullusername, $rtl, $siteurl);
        $outputpage = $output->render_html_page($htmlpage);
        $this->write_data('index.html', $outputpage);
    }

    /**
     * Perform any required finalisation steps and return the location of the finalised export.
     *
     * @return  string
     */
    public function finalise_content() : string {
        $this->write_html_data();

        $exportfile = make_request_directory() . '/export.zip';

        $fp = get_file_packer();
        $fp->archive_to_pathname($this->files, $exportfile);

        // Reset the writer to prevent any further writes.
        writer::reset();

        return $exportfile;
    }

    /**
     * Creates a multidimensional array out of array elements.
     *
     * @param  array  $array Array which items are to be condensed into a multidimensional array.
     * @return array The multidimensional array.
     */
    protected function condense_array(array $array) : Array {
        if (count($array) === 2) {
            return [$array[0] => $array[1]];
        }
        if (isset($array[0])) {
            return [$array[0] => $this->condense_array(array_slice($array, 1))];
        }
        return [];
    }

    /**
     * Get the contents of a file.
     *
     * @param  string $filepath The file path.
     * @return string contents of the file.
     */
    protected function get_file_content(string $filepath) : String {
        $filepointer = fopen($filepath, 'r');
        $content = '';
        while (!feof($filepointer)) {
            $content .= fread($filepointer, filesize($filepath));
        }
        return $content;
    }
}
