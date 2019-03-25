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
 * The renderable for core/checkbox-toggleall.
 *
 * @package    core
 * @copyright  2019 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * The checkbox-toggleall renderable class.
 *
 * @package    core
 * @copyright  2019 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkbox_toggleall implements renderable, templatable {

    /** @var string The name of the group of checkboxes to be toggled. */
    protected $togglegroup;

    /** @var bool $ismaster Whether we're rendering for a master checkbox or a slave checkbox. */
    protected $ismaster;

    /** @var array $options The options for the checkbox. */
    protected $options;

    /** @var bool $isbutton Whether to render this as a button. Applies to master checkboxes only. */
    protected $isbutton;

    /**
     * Constructor.
     *
     * @param string $togglegroup The name of the group of checkboxes to be toggled.
     * @param bool $ismaster Whether we're rendering for a master checkbox or a slave checkbox.
     * @param array $options The options for the checkbox. Valid options are:
     *     <ul>
     *         <li><b>id          </b> string - The element ID.</li>
     *         <li><b>name        </b> string - The element name.</li>
     *         <li><b>classes     </b> string - CSS classes that you want to add for your checkbox.</li>
     *         <li><b>value       </b> string|int - The element's value.</li>
     *         <li><b>checked     </b> boolean - Whether to render this initially as checked.</li>
     *         <li><b>label       </b> string - The label for the checkbox element.</li>
     *         <li><b>labelclasses</b> string - CSS classes that you want to add for your label.</li>
     *         <li><b>selectall   </b> string - Master only. The language string that will be used to indicate that clicking on
     *                                 the master will select all of the slave checkboxes. Defaults to "Select all".</li>
     *         <li><b>deselectall </b> string - Master only. The language string that will be used to indicate that clicking on
     *                                 the master will select all of the slave checkboxes. Defaults to "Deselect all".</li>
     *     </ul>
     * @param bool $isbutton Whether to render this as a button. Applies to master only.
     */
    public function __construct(string $togglegroup, bool $ismaster, $options = [], $isbutton = false) {
        $this->togglegroup = $togglegroup;
        $this->ismaster = $ismaster;
        $this->options = $options;
        $this->isbutton = $ismaster && $isbutton;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = (object)[
            'togglegroup' => $this->togglegroup,
            'id' => $this->options['id'] ?? null,
            'name' => $this->options['name'] ?? null,
            'value' => $this->options['value'] ?? null,
            'classes' => $this->options['classes'] ?? null,
            'label' => $this->options['label'] ?? null,
            'labelclasses' => $this->options['labelclasses'] ?? null,
            'checked' => $this->options['checked'] ?? false,
        ];

        if ($this->ismaster) {
            $data->selectall = $this->options['selectall'] ?? get_string('selectall');
            $data->deselectall = $this->options['deselectall'] ?? get_string('deselectall');
        }

        return $data;
    }

    /**
     * Fetches the appropriate template for the checkbox toggle all element.
     *
     * @return string
     */
    public function get_template() {
        if ($this->ismaster) {
            if ($this->isbutton) {
                return 'core/checkbox-toggleall-master-button';
            } else {
                return 'core/checkbox-toggleall-master';
            }
        }
        return 'core/checkbox-toggleall-slave';
    }
}
