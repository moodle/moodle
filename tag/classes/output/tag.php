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
 * Contains class core_tag\output\tag
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use core_tag_tag;

/**
 * Class to help display tag
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag implements renderable, templatable {

    /** @var core_tag_tag|stdClass */
    protected $record;

    /**
     * Constructor
     *
     * @param core_tag_tag|stdClass $tag
     */
    public function __construct($tag) {
        if ($tag instanceof core_tag_tag) {
            $this->record = $tag;
            return;
        }
        $tag = (array)$tag +
            array(
                'name' => '',
                'rawname' => '',
                'description' => '',
                'descriptionformat' => FORMAT_HTML,
                'flag' => 0,
                'isstandard' => 0,
                'id' => 0,
                'tagcollid' => 0,
            );
        $this->record = (object)$tag;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        require_once($CFG->libdir . '/externallib.php');

        $r = new stdClass();
        $r->id = (int)$this->record->id;
        $r->tagcollid = clean_param($this->record->tagcollid, PARAM_INT);
        $r->rawname = clean_param($this->record->rawname, PARAM_TAG);
        $r->name = clean_param($this->record->name, PARAM_TAG);
        $format = clean_param($this->record->descriptionformat, PARAM_INT);
        list($r->description, $r->descriptionformat) = external_format_text($this->record->description,
            $format, \context_system::instance()->id, 'core', 'tag', $r->id);
        $r->flag = clean_param($this->record->flag, PARAM_INT);
        if (isset($this->record->isstandard)) {
            $r->isstandard = clean_param($this->record->isstandard, PARAM_INT) ? 1 : 0;
        }
        $r->official = $r->isstandard; // For backwards compatibility.

        $url = core_tag_tag::make_url($r->tagcollid, $r->rawname);
        $r->viewurl = $url->out(false);

        return $r;
    }
}
