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

namespace core_admin\table;

use core_plugin_manager;
use flexible_table;
use html_writer;
use stdClass;

defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/tablelib.php");

/**
 * Plugin Management table.
 *
 * @package    core_admin
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_list_table extends flexible_table {

    /** @var \core\plugininfo\base[] The plugin list */
    protected array $plugins = [];

    /** @var int The number of enabled plugins of this type */
    protected int $enabledplugincount = 0;

    /** @var core_plugin_manager */
    protected core_plugin_manager $pluginmanager;

    /** @var string The plugininfo class for this plugintype */
    protected string $plugininfoclass;

    /** @var stdClass[] The list of emitted hooks with metadata */
    protected array $emitters;

    public function __construct() {
        global $CFG;

        $this->define_baseurl('/admin/hooks.php');
        parent::__construct('core_admin-hook_list_table');

        // Add emitted hooks.
        $this->emitters = \core\hook\manager::discover_known_hooks();

        $this->setup_column_configuration();
        $this->setup();
    }

    /**
     * Set up the column configuration for this table.
     */
    protected function setup_column_configuration(): void {
        $columnlist = [
            'details' => get_string('hookname', 'core_admin'),
            'callbacks' => get_string('hookcallbacks', 'core_admin'),
            'deprecates' => get_string('hookdeprecates', 'core_admin'),
        ];
        $this->define_columns(array_keys($columnlist));
        $this->define_headers(array_values($columnlist));

        $columnswithhelp = [
            'callbacks' => new \help_icon('hookcallbacks', 'admin'),
        ];
        $columnhelp = array_map(function (string $column) use ($columnswithhelp): ?\renderable {
            if (array_key_exists($column, $columnswithhelp)) {
                return $columnswithhelp[$column];
            }

            return null;
        }, array_keys($columnlist));
        $this->define_help_for_headers($columnhelp);
    }

    /**
     * Print the table.
     */
    public function out(): void {
        // All hook consumers referenced from the db/hooks.php files.
        $hookmanager = \core\di::get(\core\hook\manager::class);
        $allhooks = (array)$hookmanager->get_all_callbacks();

        // Add any unused hooks.
        foreach (array_keys($this->emitters) as $classname) {
            if (isset($allhooks[$classname])) {
                continue;
            }
            $allhooks[$classname] = [];
        }

        // Order rows by hook name, putting core first.
        \core_collator::ksort($allhooks);
        $corehooks = [];
        foreach ($allhooks as $classname => $consumers) {
            if (str_starts_with($classname, 'core\\')) {
                $corehooks[$classname] = $consumers;
                unset($allhooks[$classname]);
            }
        }
        $allhooks = array_merge($corehooks, $allhooks);

        foreach ($allhooks as $classname => $consumers) {
            $this->add_data_keyed(
                $this->format_row((object) [
                    'classname' => $classname,
                    'callbacks' => $consumers,
                ]),
                $this->get_row_class($classname),
            );
        }

        $this->finish_output(false);
    }

    protected function col_details(stdClass $row): string {
        return $row->classname .
            $this->get_description($row) .
            html_writer::div($this->get_tags_for_row($row));
    }

    /**
     * Show the name column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function get_description(stdClass $row): string {
        if (!array_key_exists($row->classname, $this->emitters)) {
            return '';
        }

        return html_writer::tag(
            'small',
            clean_text(markdown_to_html($this->emitters[$row->classname]['description']), FORMAT_HTML),
        );
    }

    protected function col_deprecates(stdClass $row): string {
        if (!class_exists($row->classname)) {
            return '';
        }

        $deprecates = \core\hook\manager::get_replaced_callbacks($row->classname);
        if (count($deprecates) === 0) {
            return '';
        }
        $content = html_writer::start_tag('ul');

        foreach ($deprecates as $deprecatedmethod) {
            $content .= html_writer::tag('li', $deprecatedmethod);
        }
        $content .= html_writer::end_tag('ul');
        return $content;
    }

    protected function col_callbacks(stdClass $row): string {
        global $CFG;

        $hookclass = $row->classname;
        $cbinfo = [];
        foreach ($row->callbacks as $definition) {
            $iscallable = is_callable($definition['callback'], false, $callbackname);
            $isoverridden = isset($CFG->hooks_callback_overrides[$hookclass][$definition['callback']]);
            $info = "{$callbackname}&nbsp;({$definition['priority']})";
            if (!$iscallable) {
                $info .= '&nbsp;';
                $info .= $this->get_tag(
                    get_string('error'),
                    'danger',
                    get_string('hookcallbacknotcallable', 'core_admin', $callbackname),
                );
            }
            if ($isoverridden) {
                // The lang string meaning should be close enough here.
                $info .= $this->get_tag(
                    get_string('hookconfigoverride', 'core_admin'),
                    'warning',
                    get_string('hookconfigoverride_help', 'core_admin'),
                );
            }

            $cbinfo[] = $info;
        }

        if ($cbinfo) {
            $output = html_writer::start_tag('ol');
            foreach ($cbinfo as $callback) {
                $class = '';
                if ($definition['disabled']) {
                    $class = 'dimmed_text';
                }
                $output .= html_writer::tag('li', $callback, ['class' => $class]);
            }
            $output .= html_writer::end_tag('ol');
            return $output;
        } else {
            return '';
        }
    }

    /**
     * Get the HTML to display the badge with tooltip.
     *
     * @param string $tag The main text to display
     * @param null|string $type The pill type
     * @param null|string $tooltip The content of the tooltip
     * @return string
     */
    protected function get_tag(
        string $tag,
        ?string $type = null,
        ?string $tooltip = null,
    ): string {
        $attributes = [];

        if ($type === null) {
            $type = 'info';
        }

        if ($tooltip) {
            $attributes['data-bs-toggle'] = 'tooltip';
            $attributes['title'] = $tooltip;
        }
        return html_writer::span($tag, "badge badge-{$type}", $attributes);
    }

    /**
     * Get the code to display a set of tags for this table row.
     *
     * @param stdClass $row
     * @return string
     */
    protected function get_tags_for_row(stdClass $row): string {
        if (!array_key_exists($row->classname, $this->emitters)) {
            // This hook has been defined in the db/hooks.php file
            // but does not refer to a hook in this version of Moodle.
            return $this->get_tag(
                get_string('hookunknown', 'core_admin'),
                'warning',
                get_string('hookunknown_desc', 'core_admin'),
            );
        }

        if (!class_exists($row->classname)) {
            // This hook has been defined in a hook discovery agent, but the class it refers to could not be found.
            return $this->get_tag(
                get_string('hookclassmissing', 'core_admin'),
                'warning',
                get_string('hookclassmissing_desc', 'core_admin'),
            );
        }

        $tags = $this->emitters[$row->classname]['tags'] ?? [];
        $taglist = array_map(function($tag): string {
            if (is_array($tag)) {
                return $this->get_tag(...$tag);
            }
            return $this->get_tag($tag, 'badge bg-info text-white');
        }, $tags);

        return implode("\n", $taglist);
    }

    protected function get_row_class(string $classname): string {
        return '';
    }
}
