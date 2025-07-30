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
 * Base capability table.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class represents a table with one row for each of a list of capabilities
 * where the first cell in the row contains the capability name, and there is
 * arbitrary stuff in the rest of the row. This class is used by
 * admin/roles/manage.php, override.php and check.php.
 *
 * An ajaxy search UI shown at the top, if JavaScript is on.
 */
abstract class core_role_capability_table_base {
    /** The context this table relates to. */
    protected $context;

    /** The capabilities to display. Initialised as $context->get_capabilities(). */
    protected $capabilities = array();

    /** Added as an id="" attribute to the table on output. */
    protected $id;

    /** Added to the class="" attribute on output. */
    protected $classes = ['rolecap table w-auto table-hover'];

    /** Default number of capabilities in the table for the search UI to be shown. */
    const NUM_CAPS_FOR_SEARCH = 12;

    /**
     * Constructor.
     * @param context $context the context this table relates to.
     * @param string $id what to put in the id="" attribute.
     */
    public function __construct(context $context, $id) {
        $this->context = $context;
        $this->capabilities = $context->get_capabilities();
        $this->id = $id;
    }

    /**
     * Use this to add class="" attributes to the table. You get the rolecap by
     * default.
     * @param array $classnames of class names.
     */
    public function add_classes($classnames) {
        $this->classes = array_unique(array_merge($this->classes, $classnames));
    }

    /**
     * Display the table.
     */
    public function display() {
        if (count($this->capabilities) > self::NUM_CAPS_FOR_SEARCH) {
            global $PAGE;
            $jsmodule = array(
                'name' => 'rolescapfilter',
                'fullpath' => '/admin/roles/module.js',
                'strings' => array(
                    array('filter', 'moodle'),
                    array('clear', 'moodle'),                ),
                'requires' => array('node', 'cookie', 'escape')
            );
            $PAGE->requires->js_init_call('M.core_role.init_cap_table_filter', array($this->id, $this->context->id), false,
                $jsmodule);
        }
        echo '<table class="' . implode(' ', $this->classes) . '" id="' . $this->id . '">' . "\n<thead>\n";
        echo '<tr><th class="name" align="left" scope="col">' . get_string('capability', 'core_role') . '</th>';
        $this->add_header_cells();
        echo "</tr>\n</thead>\n<tbody>\n";

        // Loop over capabilities.
        $contextlevel = 0;
        $component = '';
        foreach ($this->capabilities as $capability) {
            if ($this->skip_row($capability)) {
                continue;
            }

            // Prints a breaker if component or name or context level has changed.
            if (component_level_changed($capability, $component, $contextlevel)) {
                $this->print_heading_row($capability);
            }
            $contextlevel = $capability->contextlevel;
            $component = $capability->component;

            // Start the row.
            $rowattributes = $this->get_row_attributes($capability);
            // Handle class attributes same as other.
            $rowclasses = array_unique(array_merge(array('rolecap'), $this->get_row_classes($capability)));
            if (array_key_exists('class', $rowattributes)) {
                $rowclasses = array_unique(array_merge($rowclasses, array($rowattributes['class'])));
            }
            $rowattributes['class']  = implode(' ', $rowclasses);

            // Table cell for the capability name.
            $contents = '<th scope="row" class="name"><span class="cap-desc">' . get_capability_docs_link($capability) .
                '<span class="cap-name">' . $capability->name . '</span></span></th>';

            // Add the cells specific to this table.
            $contents .= $this->add_row_cells($capability);

            echo html_writer::tag('tr', $contents, $rowattributes);
        }

        // End of the table.
        echo "</tbody>\n</table>\n";
    }

    /**
     * Used to output a heading rows when the context level or component changes.
     * @param stdClass $capability gives the new component and contextlevel.
     */
    protected function print_heading_row($capability) {
        echo '<tr class="rolecapheading header"><td colspan="' . (1 + $this->num_extra_columns()) . '" class="header"><strong>' .
            get_component_string($capability->component, $capability->contextlevel) .
            '</strong></td></tr>';

    }

    /**
     * For subclasses to override, output header cells, after the initial capability one.
     */
    abstract protected function add_header_cells();

    /**
     * For subclasses to override, return the number of cells that add_header_cells/add_row_cells output.
     */
    abstract protected function num_extra_columns();

    /**
     * For subclasses to override. Allows certain capabilties
     * to be left out of the table.
     *
     * @param object $capability the capability this row relates to.
     * @return boolean. If true, this row is omitted from the table.
     */
    protected function skip_row($capability) {
        return false;
    }

    /**
     * For subclasses to override. A change to reaturn class names that are added
     * to the class="" attribute on the &lt;tr> for this capability.
     *
     * @param stdClass $capability the capability this row relates to.
     * @return array of class name strings.
     */
    protected function get_row_classes($capability) {
        return array();
    }

    /**
     * For subclasses to override. Additional attributes to be added to
     * each table row for the capability
     *
     * @param stdClass $capability the capability this row relates to.
     * @return array attribute names and their values.
     */
    protected function get_row_attributes($capability) {
        return array();
    }

    /**
     * For subclasses to override. Output the data cells for this capability. The
     * capability name cell will already have been output.
     *
     * You can rely on get_row_classes always being called before add_row_cells.
     *
     * @param stdClass $capability the capability this row relates to.
     * @return string html of row cells
     */
    abstract protected function add_row_cells($capability);
}
