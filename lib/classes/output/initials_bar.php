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

use moodle_url;
use stdClass;

/**
 * Component representing initials bar.
 *
 * @copyright 2017 Ilya Tregubov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 * @package core
 * @category output
 */
class initials_bar implements renderable, templatable {
    /**
     * @var string Currently selected letter.
     */
    public $current;

    /**
     * @var string Class name to add to this initial bar.
     */
    public $class;

    /**
     * @var string The name to put in front of this initial bar.
     */
    public $title;

    /**
     * @var string URL parameter name for this initial.
     */
    public $urlvar;

    /**
     * @var moodle_url URL object.
     */
    public $url;

    /**
     * @var array An array of letters in the alphabet.
     */
    public $alpha;

    /**
     * @var bool Omit links if we are doing a mini render.
     */
    public $minirender;

    /**
     * Constructor initials_bar with only the required params.
     *
     * @param string $current the currently selected letter.
     * @param string $class class name to add to this initial bar.
     * @param string $title the name to put in front of this initial bar.
     * @param string $urlvar URL parameter name for this initial.
     * @param string $url URL object.
     * @param array $alpha of letters in the alphabet.
     * @param bool $minirender Return a trimmed down view of the initials bar.
     */
    public function __construct($current, $class, $title, $urlvar, $url, $alpha = null, bool $minirender = false) {
        $this->current       = $current;
        $this->class    = $class;
        $this->title    = $title;
        $this->urlvar    = $urlvar;
        $this->url    = $url;
        $this->alpha    = $alpha;
        $this->minirender = $minirender;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        if ($this->alpha == null) {
            $this->alpha = explode(',', get_string('alphabet', 'langconfig'));
        }

        if ($this->current == 'all') {
            $this->current = '';
        }

        // We want to find a letter grouping size which suits the language so
        // find the largest group size which is less than 15 chars.
        // The choice of 15 chars is the largest number of chars that reasonably
        // fits on the smallest supported screen size. By always using a max number
        // of groups which is a factor of 2, we always get nice wrapping, and the
        // last row is always the shortest.
        $groupsize = count($this->alpha);
        $groups = 1;
        while ($groupsize > 15) {
            $groups *= 2;
            $groupsize = ceil(count($this->alpha) / $groups);
        }

        $groupsizelimit = 0;
        $groupnumber = 0;
        foreach ($this->alpha as $letter) {
            if ($groupsizelimit++ > 0 && $groupsizelimit % $groupsize == 1) {
                $groupnumber++;
            }
            $groupletter = new stdClass();
            $groupletter->name = $letter;
            if (!$this->minirender) {
                $groupletter->url = $this->url->out(false, [$this->urlvar => $letter]);
            } else {
                $groupletter->input = $letter;
            }
            if ($letter == $this->current) {
                $groupletter->selected = $this->current;
            }
            if (!isset($data->group[$groupnumber])) {
                $data->group[$groupnumber] = new stdClass();
            }
            $data->group[$groupnumber]->letter[] = $groupletter;
        }

        $data->class = $this->class;
        $data->title = $this->title;
        if (!$this->minirender) {
            $data->url = $this->url->out(false, [$this->urlvar => '']);
        } else {
            $data->input = 'ALL';
        }
        $data->current = $this->current;
        $data->all = get_string('all');

        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(initials_bar::class, \initials_bar::class);
