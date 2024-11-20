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

namespace core\output;

use core\context\user as context_user;
use moodle_url;
use stdClass;

/**
 * Data structure representing a file picker.
 *
 * @copyright 2010 Dongsheng Cai
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class file_picker implements renderable {
    /**
     * @var stdClass An object containing options for the file picker
     */
    public $options;

    /**
     * Constructs a file picker object.
     *
     * The following are possible options for the filepicker:
     *    - accepted_types  (*)
     *    - return_types    (FILE_INTERNAL)
     *    - env             (filepicker)
     *    - client_id       (uniqid)
     *    - itemid          (0)
     *    - maxbytes        (-1)
     *    - maxfiles        (1)
     *    - buttonname      (false)
     *
     * @param stdClass $options An object containing options for the file picker.
     */
    public function __construct(stdClass $options) {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot . '/repository/lib.php');
        $defaults = [
            'accepted_types' => '*',
            'return_types' => FILE_INTERNAL,
            'env' => 'filepicker',
            'client_id' => uniqid(),
            'itemid' => 0,
            'maxbytes' => -1,
            'maxfiles' => 1,
            'buttonname' => false,
        ];
        foreach ($defaults as $key => $value) {
            if (empty($options->$key)) {
                $options->$key = $value;
            }
        }

        $options->currentfile = '';
        if (!empty($options->itemid)) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if (empty($options->filename)) {
                if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $options->itemid, 'id DESC', false)) {
                    $file = reset($files);
                }
            } else {
                $file = $fs->get_file($usercontext->id, 'user', 'draft', $options->itemid, $options->filepath, $options->filename);
            }
            if (!empty($file)) {
                $options->currentfile = html_writer::link(moodle_url::make_draftfile_url(
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename(),
                ), $file->get_filename());
            }
        }

        // Initialise options, getting files in root path.
        $this->options = initialise_filepicker($options);

        // Copying other options.
        foreach ($options as $name => $value) {
            if (!isset($this->options->$name)) {
                $this->options->$name = $value;
            }
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(file_picker::class, \file_picker::class);
