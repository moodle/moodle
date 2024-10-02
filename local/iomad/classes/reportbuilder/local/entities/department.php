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

declare(strict_types=1);

namespace local_iomad\reportbuilder\local\entities;

use context_course;
use context_helper;
use context_system;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{select, text};
use core_reportbuilder\local\report\{column, filter};

defined('MOODLE_INTERNAL') or die;

global $CFG;
require_once("{$CFG->dirroot}/local/iomad/lib/iomad.php");

/**
 * Company department entity
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class department extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'department' => 'dptmnt',
            'context' => 'dptmntctx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('departmentdetails', 'block_iomad_company_admin');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        // All the filters defined by the entity can also be used as conditions.
        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;

        $departmentalias = $this->get_table_alias('department');
        $contextalias = $this->get_table_alias('context');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$departmentalias}.name")
            ->set_is_sortable(true);

        // Short name.
        $columns[] = (new column(
            'shortname',
            new lang_string('shortname'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$departmentalias}.shortname")
            ->set_is_sortable(true);

        // Company.
        $columns[] = (new column(
            'company',
            new lang_string('company', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$departmentalias}.company")
            ->set_is_sortable(false);

        // parentid.
        $columns[] = (new column(
            'parentid',
            new lang_string('parent', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$departmentalias}.parentid")
            ->set_is_sortable(false);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $departmentalias = $this->get_table_alias('department');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$departmentalias}.name"
        ))
            ->add_joins($this->get_joins());

        // Shortname.
        $filters[] = (new filter(
            select::class,
            'shortname',
            new lang_string('shortname', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$departmentalias}.shortname"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        return $filters;
    }
}
