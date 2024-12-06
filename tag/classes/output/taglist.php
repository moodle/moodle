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
 * Contains class core_tag\output\taglist
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\output;

use templatable;
use renderer_base;
use stdClass;
use core_tag_tag;
use context;

/**
 * Class to preapare a list of tags for display, usually the list of tags some entry is tagged with.
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class taglist implements templatable {

    /** @var array */
    protected $tags;

    /** @var string */
    protected $label;

    /** @var bool $accesshidelabel if true, the label should have class="accesshide" added. */
    protected $accesshidelabel;

    /** @var string */
    protected $classes;

    /** @var int */
    protected $limit;

    /** @var bool */
    protected $displaylink;

    /**
     * Constructor
     *
     * @param array $tags list of instances of \core_tag_tag or \stdClass
     * @param string $label label to display in front, by default 'Tags' (get_string('tags')), set to null
     *               to use default, set to '' (empty string) to omit the label completely
     * @param string $classes additional classes for the enclosing div element
     * @param int $limit limit the number of tags to display, if size of $tags is more than this limit the "more" link
     *               will be appended to the end, JS will toggle the rest of the tags. 0 means no limit.
     * @param context $pagecontext specify if needed to overwrite the current page context for the view tag link
     * @param bool $accesshidelabel if true, the label should have class="accesshide" added.
     * @param bool $displaylink Indicates whether the tag should be displayed as a link.
     */
    public function __construct($tags, $label = null, $classes = '',
            $limit = 10, $pagecontext = null, $accesshidelabel = false, $displaylink = true) {
        global $PAGE;
        $canmanagetags = has_capability('moodle/tag:manage', \context_system::instance());

        $this->label = ($label === null) ? get_string('tags') : $label;
        $this->accesshidelabel = $accesshidelabel;
        $this->classes = $classes;
        $this->displaylink = $displaylink;
        $fromctx = $pagecontext ? $pagecontext->id :
                (($PAGE->context->contextlevel == CONTEXT_SYSTEM) ? 0 : $PAGE->context->id);

        $this->tags = array();
        foreach ($tags as $idx => $tag) {
            $this->tags[$idx] = new stdClass();

            $this->tags[$idx]->name = core_tag_tag::make_display_name($tag, false);

            if ($canmanagetags && !empty($tag->flag)) {
                $this->tags[$idx]->flag = 1;
            }

            if ($displaylink) {
                $viewurl = core_tag_tag::make_url($tag->tagcollid, $tag->rawname, 0, $fromctx);
                $this->tags[$idx]->viewurl = $viewurl->out(false);
            }

            if (isset($tag->isstandard)) {
                $this->tags[$idx]->isstandard = $tag->isstandard ? 1 : 0;
            }

            if ($limit && count($this->tags) > $limit) {
                $this->tags[$idx]->overlimit = 1;
            }
        }
        $this->limit = $limit;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $cnt = count($this->tags);
        return (object)array(
            'tags' => array_values($this->tags),
            'label' => $this->label,
            'accesshidelabel' => $this->accesshidelabel,
            'tagscount' => $cnt,
            'overflow' => ($this->limit && $cnt > $this->limit) ? 1 : 0,
            'classes' => $this->classes,
            'displaylink' => $this->displaylink,
        );
    }
}
