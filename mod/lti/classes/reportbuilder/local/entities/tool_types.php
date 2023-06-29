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

namespace mod_lti\reportbuilder\local\entities;

use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * Course external tools entity class implementation.
 *
 * Defines all the columns and filters that can be added to reports that use this entity.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_types extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return ['lti_types' => 'tt', 'lti' => 'ti'];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('entitycourseexternaltools', 'mod_lti');
    }

    /**
     * Initialize the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this->add_filter($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        $tablealias = $this->get_table_alias('lti_types');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name', 'core'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.name, {$tablealias}.icon")
            ->set_is_sortable(true)
            ->add_callback(static function(string $name, \stdClass $data) {
                global $OUTPUT;

                $iconurl = $data->icon ?: $OUTPUT->image_url('monologo', 'lti')->out();
                $iconclass = $data->icon ? ' nofilter' : '';
                $iconcontainerclass = 'activityiconcontainer smaller content';
                $name = $data->name;
                $img = \html_writer::img($iconurl, get_string('courseexternaltooliconalt', 'mod_lti', $name),
                    ['class' => 'activityicon' . $iconclass]);
                $name = \html_writer::span($name, 'align-self-center');
                return \html_writer::div(\html_writer::div($img, 'mr-2 '.$iconcontainerclass) . $name, 'd-flex');
            });

        // Description column.
        $columns[] = (new column(
            'description',
            new lang_string('description', 'core'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$tablealias}.description")
            ->set_is_sortable(true);

        // Course column.
        $columns[] = (new column(
            'course',
            new lang_string('course', 'core'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$tablealias}.course")
            ->set_is_sortable(true);

        // LTI Version column.
        $columns[] = (new column(
            'ltiversion',
            new lang_string('version'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$tablealias}.ltiversion")
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $tablealias = $this->get_table_alias('lti_types');

        return [
            // Name filter.
            (new filter(
                text::class,
                'name',
                new lang_string('name'),
                $this->get_entity_name(),
                "{$tablealias}.name"
            ))
                ->add_joins($this->get_joins()),

            // Description filter.
            (new filter(
                text::class,
                'description',
                new lang_string('description'),
                $this->get_entity_name(),
                "{$tablealias}.description"
            ))
                ->add_joins($this->get_joins()),

            // LTI Version filter.
            (new filter(
                select::class,
                'ltiversion',
                new lang_string('version'),
                $this->get_entity_name(),
                "{$tablealias}.ltiversion"
            ))
                ->add_joins($this->get_joins())
                ->set_options_callback(static function() : array {
                    return ['LTI-1p0' => 'Legacy LTI', '1.3.0' => "LTI Advantage"];
                })
        ];
    }
}
