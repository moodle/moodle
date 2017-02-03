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
 * Class to display a tag cloud - set of tags where each has a weight.
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagcloud implements templatable {

    /** @var array */
    protected $tagset;

    /** @var int */
    protected $totalcount;

    /**
     * Constructor
     *
     * @param array $tagset array of core_tag or stdClass elements, each of them must have attributes:
     *              name, rawname, tagcollid
     *              preferrably also have attributes:
     *              isstandard, count, flag
     * @param int $totalcount total count of tags (for example to indicate that there are more tags than the count of tagset)
     *            leave 0 if count of tagset is the actual count of tags
     * @param int $fromctx context id where this tag cloud is displayed
     * @param int $ctx context id for tag view link
     * @param int $rec recursive argument for tag view link
     */
    public function __construct($tagset, $totalcount = 0, $fromctx = 0, $ctx = 0, $rec = 1) {
        $canmanagetags = has_capability('moodle/tag:manage', \context_system::instance());

        $maxcount = 1;
        foreach ($tagset as $tag) {
            if (isset($tag->count) && $tag->count > $maxcount) {
                $maxcount = $tag->count;
            }
        }

        $this->tagset = array();
        foreach ($tagset as $idx => $tag) {
            $this->tagset[$idx] = new stdClass();

            $this->tagset[$idx]->name = core_tag_tag::make_display_name($tag, false);

            if ($canmanagetags && !empty($tag->flag)) {
                $this->tagset[$idx]->flag = 1;
            }

            $viewurl = core_tag_tag::make_url($tag->tagcollid, $tag->rawname, 0, $fromctx, $ctx, $rec);
            $this->tagset[$idx]->viewurl = $viewurl->out(false);

            if (isset($tag->isstandard)) {
                $this->tagset[$idx]->isstandard = $tag->isstandard ? 1 : 0;
            }

            if (!empty($tag->count)) {
                $this->tagset[$idx]->count = $tag->count;
                $this->tagset[$idx]->size = (int)($tag->count / $maxcount * 20);
            }
        }

        $this->totalcount = $totalcount ? $totalcount : count($this->tagset);
    }

    /**
     * Returns number of tags in the cloud
     * @return int
     */
    public function get_count() {
        return count($this->tagset);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $cnt = count($this->tagset);
        return (object)array(
            'tags' => $this->tagset,
            'tagscount' => $cnt,
            'totalcount' => $this->totalcount,
            'overflow' => ($this->totalcount > $cnt) ? 1 : 0,
        );
    }
}
