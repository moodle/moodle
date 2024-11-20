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
 * The definition of a set of files in a filearea to be exported.
 *
 * @package     core
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core\content\export\exportable_items;

use context;
use core\content\export\exportable_item;
use core\content\export\exported_item;
use core\content\export\zipwriter;
use moodle_url;
use stored_file;

/**
 * The definition of a set of files in a filearea to be exported.
 *
 * All files mustbe in a single filearea and itemid combination.
 *
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exportable_filearea extends exportable_item {

    /** @var string The destination path of the text content */
    protected $folderpath;

    /** @var string $filearea The file to be exported */
    protected $filearea;

    /** @var bool|int The itemid in the Files API */
    protected $itemid;

    /** @var int The itemid to use in the pluginfile URL */
    protected $pluginfileitemid;

    /**
     * Create a new exportable_item instance.
     *
     * If no filearea or itemid  is specified the no attempt will be made to export files.
     *
     * @param   context $context The context that this content belongs to
     * @param   string $component
     * @param   string $uservisiblename The name displayed to the user when filtering
     * @param   string $filearea The file area in the Files API where these files are located
     * @param   int $itemid The itemid in the Files API where these files are located
     * @param   null|int $pluginfileitemid The itemid as used in the Pluginfile URL
     * @param   string $folderpath Any sub-directory to place files in
     */
    public function __construct(
        context $context,
        string $component,
        string $uservisiblename,
        string $filearea,
        int $itemid,
        ?int $pluginfileitemid = null,
        string $folderpath = ''
    ) {
        parent::__construct($context, $component, $uservisiblename);

        $this->filearea = $filearea;
        $this->itemid = $itemid;
        $this->pluginfileitemid = $pluginfileitemid;
        $this->folderpath = $folderpath;
    }

    /**
     * Add the content to the archive.
     *
     * @param   zipwriter $archive
     */
    public function add_to_archive(zipwriter $archive): ?exported_item {
        $fs = get_file_storage();

        $files = $fs->get_area_files($this->context->id, $this->component, $this->filearea, $this->itemid);

        $exporteditem = new exported_item();
        $exporteditem->set_title($this->get_user_visible_name());

        foreach ($files as $file) {
            if ($file->is_directory()) {
                // Skip folders. The zipwriter cannot handle them.
                continue;
            }
            // Export the content to [contextpath]/[filepath].
            $relativefilepath = $this->get_filepath_for_file($file);

            $archive->add_file_from_stored_file(
                $this->get_context(),
                $relativefilepath,
                $file
            );

            if ($archive->is_file_in_archive($this->context, $relativefilepath)) {
                // The file was successfully added to the archive.
                $exporteditem->add_file($relativefilepath, false);
            } else {
                // The file was not added. Link to the live version instead.
                $exporteditem->add_file(
                    $relativefilepath,
                    false,
                    self::get_pluginfile_url_for_stored_file($file, $this->pluginfileitemid)
                );
            }
        }

        return $exporteditem;
    }

    /**
     * Get the filepath for the specified stored_file.
     *
     * @param   stored_file $file The file to get a filepath for
     * @return  string The generated filepath
     */
    protected function get_filepath_for_file(stored_file $file): string {
        $folderpath = rtrim($this->folderpath);

        if (!empty($folderpath)) {
            $folderpath .= '/';
        }
        return sprintf(
            '%s%s%s%s',
            $folderpath,
            $file->get_filearea(),
            $file->get_filepath(),
            $file->get_filename()
        );
    }

    /**
     * Get the pluginfile URL for a stored file.
     *
     * Note: The itemid in the pluginfile may be omitted in some URLs, despite an itemid being present in the database.
     * Equally, the itemid in the URL may not match the itemid in the files table.
     *
     * The pluginfileitemid argument provided to this function is the variant in the URL, and not the one in the files
     * table.
     *
     * @param   stored_file $file The file whose link will be generated
     * @param   null|int $pluginfileitemid The itemid of the file in pluginfile URL.
     *
     */
    protected static function get_pluginfile_url_for_stored_file(stored_file $file, ?int $pluginfileitemid): string {
        $link = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $pluginfileitemid,
            $file->get_filepath(),
            $file->get_filename(),
            true,
            true
        );

        return $link->out(false);
    }
}
