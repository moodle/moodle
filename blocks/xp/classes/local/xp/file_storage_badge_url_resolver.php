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
 * File storage badge URL resolver.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use context;
use moodle_url;

/**
 * File storage badge URL resolver.
 *
 * This resolver tries to find a file named after a level in a file area.
 * It is expected that the file mimetype is an image, and that the file
 * name is the bare level number followed by the file extension.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_storage_badge_url_resolver implements badge_url_resolver {

    /** @var context The context. */
    protected $context;
    /** @var string The component. */
    protected $component;
    /** @var string The file area. */
    protected $filearea;
    /** @var int The item ID. */
    protected $itemid;
    /** @var stored_file[] The files mapped by level. */
    protected $files;

    /**
     * Constructor.
     *
     * @param context $context The context.
     * @param string $component The component.
     * @param string $filearea The file area.
     * @param int $itemid The item ID, or null to search through the entire area.
     */
    public function __construct(context $context, $component, $filearea, $itemid = null) {
        $this->context = $context;
        $this->component = $component;
        $this->filearea = $filearea;
        $this->itemid = $itemid;
    }

    /**
     * Get badge URL for level.
     *
     * @param int $level The level, as an integer.
     * @return moodle_url|null
     */
    public function get_url_for_level($level) {
        $this->load();
        if (!isset($this->files[$level])) {
            return null;
        }

        $file = $this->files[$level];
        return moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename() . '/' . $file->get_timemodified()
        );
    }

    /**
     * Load the files.
     *
     * @return void
     */
    protected function load() {
        if ($this->files !== null) {
            return;
        }

        $fs = get_file_storage();
        $files = [];
        $allfiles = $fs->get_area_files($this->context->id, $this->component, $this->filearea, $this->itemid, 'filename', false);

        foreach ($allfiles as $file) {
            if (strpos($file->get_mimetype(), 'image/') !== 0) {
                continue;
            }
            $matches = [];
            if (!preg_match('~^(\d+)\.[a-z]+$~i', $file->get_filename(), $matches)) {
                continue;
            }
            $i = (int) $matches[1];
            $files[$i] = $file;
        }

        $this->files = $files;
    }

}
