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

/**
 * Class to help display tag
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag implements renderable, templatable {

    /** @var stdClass */
    protected $record;

    /**
     * Constructor
     *
     * @param stdClass $tag
     */
    public function __construct($tag) {
        $tag = (array)$tag +
            array(
                'name' => '',
                'rawname' => '',
                'description' => '',
                'descriptionformat' => FORMAT_HTML,
                'flag' => 0,
                'tagtype' => 'default',
                'id' => 0
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
        $r->rawname = clean_param($this->record->rawname, PARAM_TAG);
        $r->name = clean_param($this->record->name, PARAM_TAG);
        $format = clean_param($this->record->descriptionformat, PARAM_INT);
        list($r->description, $r->descriptionformat) = external_format_text($this->record->description,
            $format, \context_system::instance()->id, 'core', 'tag', $r->id);
        $r->flag = clean_param($this->record->flag, PARAM_INT);
        if (isset($this->record->official)) {
            $r->official = clean_param($this->record->official, PARAM_INT);
        } else {
            $r->official = ($this->record->tagtype === 'official') ? 1 : 0;
        }

        $url = new moodle_url('/tag/index.php', array('id' => $this->record->id));
        $r->viewurl = $url->out(false);

        $manageurl = new moodle_url('/tag/manage.php', array('sesskey' => sesskey(),
            'tagid' => $this->record->id));
        $url = new moodle_url($manageurl);
        $url->param('action', 'changetype');
        $url->param('tagtype', $r->official ? 'default' : 'official');
        $r->changetypeurl = $url->out(false);

        $url = new moodle_url($manageurl);
        $url->param('action', $this->record->flag ? 'resetflag' : 'setflag');
        $r->changeflagurl = $url->out(false);

        return $r;
    }
}
