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
 * This file contains the interface required to implmeent a content writer.
 *
 * @package core_privacy
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * The interface for a Moodle content writer.
 *
 * @package core_privacy
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 */
interface content_writer {

    /**
     * Constructor for the content writer.
     *
     * Note: The writer_factory must be passed.
     * @param   writer          $writer    The factory.
     */
    public function __construct(writer $writer);

    /**
     * Set the context for the current item being processed.
     *
     * @param   \context        $context    The context to use
     * @return  content_writer
     */
    public function set_context(\context $context);

    /**
     * Export the supplied data within the current context, at the supplied subcontext.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   \stdClass       $data       The data to be exported
     * @return  content_writer
     */
    public function export_data(array $subcontext, \stdClass $data);

    /**
     * Export metadata about the supplied subcontext.
     *
     * Metadata consists of a key/value pair and a description of the value.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $name       The metadata name.
     * @param   string          $value      The metadata value.
     * @param   string          $description    The description of the value.
     * @return  content_writer
     */
    public function export_metadata(array $subcontext, $name, $value, $description);

    /**
     * Export a piece of related data.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $name       The name of the file to be exported.
     * @param   \stdClass       $data       The related data to export.
     * @return  content_writer
     */
    public function export_related_data(array $subcontext, $name, $data);

    /**
     * Export a piece of data in a custom format.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $filename   The name of the file to be exported.
     * @param   string          $filecontent    The content to be exported.
     * @return  content_writer
     */
    public function export_custom_file(array $subcontext, $filename, $filecontent);

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
    public function rewrite_pluginfile_urls(array $subcontext, $component, $filearea, $itemid, $text);

    /**
     * Export all files within the specified component, filearea, itemid combination.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   string          $component  The name of the component that the files belong to.
     * @param   string          $filearea   The filearea within that component.
     * @param   string          $itemid     Which item those files belong to.
     * @return  content_writer
     */
    public function export_area_files(array $subcontext, $component, $filearea, $itemid);

    /**
     * Export the specified file in the target location.
     *
     * @param   array           $subcontext The location within the current context that this data belongs.
     * @param   \stored_file    $file       The file to be exported.
     * @return  content_writer
     */
    public function export_file(array $subcontext, \stored_file $file);

    /**
     * Export the specified user preference.
     *
     * @param   string          $component  The name of the component.
     * @param   string          $key        The name of th key to be exported.
     * @param   string          $value      The value of the preference
     * @param   string          $description    A description of the value
     * @return  content_writer
     */
    public function export_user_preference($component, $key, $value, $description);

    /**
     * Perform any required finalisation steps and return the location of the finalised export.
     *
     * @return  string
     */
    public function finalise_content();
}
