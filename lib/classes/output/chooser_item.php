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
 * The chooser_item renderable.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use coding_exception;
use context;
use pix_icon;
use renderer_base;
use renderable;
use stdClass;
use templatable;

/**
 * The chooser_item renderable class.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chooser_item implements renderable, templatable {

    /** @var string An identifier for the item. */
    public $id;
    /** @var string The label of this item. */
    public $label;
    /** @var string The value this item represents. */
    public $value;
    /** @var pix_icon The icon for this item. */
    public $icon;
    /** @var string The item description. */
    public $description;
    /** @var context The relevant context. */
    public $context;

    /**
     * Constructor.
     */
    public function __construct($id, $label, $value, pix_icon $icon, $description = null, context $context = null) {
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->icon = $icon;
        $this->description = $description;

        if (!empty($description) && empty($context)) {
            throw new coding_exception('The context must be passed when there is a description.');
        }
        $this->context = $context;
    }

    /**
     * Export for template.
     *
     * @param renderer_base  The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->id = $this->id;
        $data->label = $this->label;
        $data->value = $this->value;
        $data->icon = $this->icon->export_for_template($output);

        $options = new stdClass();
        $options->trusted = false;
        $options->noclean = false;
        $options->smiley = false;
        $options->filter = false;
        $options->para = true;
        $options->newlines = false;
        $options->overflowdiv = false;

        $data->description = '';
        if (!empty($this->description)) {
            list($data->description) = \core_external\util::format_text((string) $this->description, FORMAT_MARKDOWN,
                $this->context->id, null, null, null, $options);
        }

        return $data;
    }

}
