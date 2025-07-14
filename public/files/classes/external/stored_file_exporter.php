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

namespace core_files\external;

use coding_exception;
use core_text;
use moodle_url;
use renderer_base;
use stdClass;
use stored_file;

/**
 * Class for exporting stored_file data.
 *
 * @package    core_files
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stored_file_exporter extends \core\external\exporter {

    /** @var int Length of the shortened filename */
    protected const FILENAMESHORT_LENGTH = 25;

    /** @var stored_file */
    protected $file;

    public function __construct(stored_file $file, $related = array()) {
        $this->file = $file;

        $data = new stdClass();
        $data->contextid = $file->get_contextid();
        $data->component = $file->get_component();
        $data->filearea = $file->get_filearea();
        $data->itemid = $file->get_itemid();
        $data->filepath = $file->get_filepath();
        $data->filename = $file->get_filename();
        $data->isdir = $file->is_directory();
        $data->isimage = $file->is_valid_image();
        $data->timemodified = $file->get_timemodified();
        $data->timecreated = $file->get_timecreated();
        $data->filesize = $file->get_filesize();
        $data->author = $file->get_author();
        $data->license = $file->get_license();

        if ($related['context']->id != $data->contextid) {
            throw new coding_exception('Unexpected context ID received.');
        }

        parent::__construct($data, $related);
    }

    protected static function define_related() {
        return array('context' => 'context');
    }

    protected static function define_properties() {
        return array(
            'contextid' => array(
                'type' => PARAM_INT
            ),
            'component' => array(
                'type' => PARAM_COMPONENT
            ),
            'filearea' => array(
                'type' => PARAM_AREA
            ),
            'itemid' => array(
                'type' => PARAM_INT
            ),
            'filepath' => array(
                'type' => PARAM_PATH
            ),
            'filename' => array(
                'type' => PARAM_FILE
            ),
            'isdir' => array(
                'type' => PARAM_BOOL
            ),
            'isimage' => array(
                'type' => PARAM_BOOL
            ),
            'timemodified' => array(
                'type' => PARAM_INT
            ),
            'timecreated' => array(
                'type' => PARAM_INT
            ),
            'filesize' => array(
                'type' => PARAM_INT
            ),
            'author' => array(
                'type' => PARAM_TEXT
            ),
            'license' => array(
                'type' => PARAM_TEXT
            )
        );
    }

    protected static function define_other_properties() {
        return array(
            'filenameshort' => array(
                'type' => PARAM_RAW,
            ),
            'filesizeformatted' => array(
                'type' => PARAM_RAW
            ),
            'icon' => array(
                'type' => PARAM_RAW,
            ),
            'timecreatedformatted' => array(
                'type' => PARAM_RAW
            ),
            'timemodifiedformatted' => array(
                'type' => PARAM_RAW
            ),
            'url' => array(
                'type' => PARAM_URL
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $filename = $this->file->get_filename();
        $filenameshort = $filename;

        if (core_text::strlen($filename) > static::FILENAMESHORT_LENGTH) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $extensionlength = core_text::strlen($extension) + 1;
            $filenameshort = core_text::substr($filename, 0, -$extensionlength);
            $filenameshort = shorten_text($filenameshort, static::FILENAMESHORT_LENGTH - $extensionlength, true, '..') .
                ".{$extension}";
        }

        $icon = $this->file->is_directory() ? file_folder_icon() : file_file_icon($this->file);

        $url = moodle_url::make_pluginfile_url(
            $this->file->get_contextid(),
            $this->file->get_component(),
            $this->file->get_filearea(),
            $this->file->get_itemid(),
            $this->file->get_filepath(),
            $this->file->get_filename(),
            true
        );

        return array(
            'filenameshort' => $filenameshort,
            'filesizeformatted' => display_size((int) $this->file->get_filesize()),
            'icon' => $icon,
            'url' => $url->out(false),
            'timecreatedformatted' => userdate($this->file->get_timecreated()),
            'timemodifiedformatted' => userdate($this->file->get_timemodified()),
        );
    }

}
