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
 * Contains class core_tag\output\tagindex
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
 * Class to display items tagged with a specific tag
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagindex implements templatable {

    /** @var core_tag_tag|stdClass */
    protected $tag;

    /** @var stdClass */
    protected $tagarea;

    /** @var stdClass */
    protected $record;

    /**
     * Constructor
     *
     * @param core_tag_tag|stdClass $tag
     * @param string $component
     * @param string $itemtype
     * @param string $content
     * @param bool $exclusivemode
     * @param int $fromctx context id where the link was displayed, may be used by callbacks
     *            to display items in the same context first
     * @param int $ctx context id where we need to search for items
     * @param int $rec search items in sub contexts as well
     * @param int $page
     * @param bool $totalpages
     */
    public function __construct($tag, $component, $itemtype, $content,
            $exclusivemode = false, $fromctx = 0, $ctx = 0, $rec = 1, $page = 0, $totalpages = 1) {
        $this->record = new stdClass();
        $this->tag = $tag;

        $tagareas = \core_tag_area::get_areas();
        if (!isset($tagareas[$itemtype][$component])) {
            throw new \coding_exception('Tag area for component '.$component.' and itemtype '.$itemtype.' is not defined');
        }
        $this->tagarea = $tagareas[$itemtype][$component];
        $this->record->tagid = $tag->id;
        $this->record->ta = $this->tagarea->id;
        $this->record->itemtype = $itemtype;
        $this->record->component = $component;

        $a = (object)array(
            'tagarea' => \core_tag_area::display_name($component, $itemtype),
            'tag' => \core_tag_tag::make_display_name($tag)
        );
        if ($exclusivemode) {
            $this->record->title = get_string('itemstaggedwith', 'tag', $a);
        } else {
            $this->record->title = (string)$a->tagarea;
        }
        $this->record->content = $content;

        $this->record->nextpageurl = null;
        $this->record->prevpageurl = null;
        $this->record->exclusiveurl = null;

        $url = core_tag_tag::make_url($tag->tagcollid, $tag->rawname, $exclusivemode, $fromctx, $ctx, $rec);
        $urlparams = array('ta' => $this->tagarea->id);
        if ($totalpages > $page + 1) {
            $this->record->nextpageurl = new moodle_url($url, $urlparams + array('page' => $page + 1));
        }
        if ($page > 0) {
            $this->record->prevpageurl = new moodle_url($url, $urlparams + array('page' => $page - 1));
        }
        if (!$exclusivemode && ($totalpages > 1 || $page)) {
            $this->record->exclusiveurl = new moodle_url($url, $urlparams + array('excl' => 1));
        }
        $this->record->exclusivetext = get_string('exclusivemode', 'tag', $a);
        $this->record->hascontent = ($totalpages > 1 || $page || $content);
        $this->record->anchor = $component . '_' . $itemtype;
    }

    /**
     * Magic setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->record->$name = $value;
    }

    /**
     * Magic getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->record->$name;
    }

    /**
     * Magic isset
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->record->$name);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        if ($this->record->nextpageurl && $this->record->nextpageurl instanceof moodle_url) {
            $this->record->nextpageurl = $this->record->nextpageurl->out(false);
        }
        if ($this->record->prevpageurl && $this->record->prevpageurl instanceof moodle_url) {
            $this->record->prevpageurl = $this->record->prevpageurl->out(false);
        }
        if ($this->record->exclusiveurl && $this->record->exclusiveurl instanceof moodle_url) {
            $this->record->exclusiveurl = $this->record->exclusiveurl->out(false);
        }
        return $this->record;
    }
}
