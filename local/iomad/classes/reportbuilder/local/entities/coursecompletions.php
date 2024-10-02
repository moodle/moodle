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
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\filters\{select, text};
use core_reportbuilder\local\report\{column, filter};

defined('MOODLE_INTERNAL') or die;

global $CFG;
require_once("{$CFG->dirroot}/local/iomad/lib/iomad.php");

/**
 * Course completions entity
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecompletions extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'coursecompletions' => 'lit',
            'context' => 'litctx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('coursecompletionsdetails', 'block_iomad_company_admin');
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

        $coursecompletionsalias = $this->get_table_alias('coursecompletions');
        $contextalias = $this->get_table_alias('context');

        // userid.
        $columns[] = (new column(
            'userid',
            new lang_string('userid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$coursecompletionsalias}.userid")
            ->set_is_sortable(false);

        // courseid.
        $columns[] = (new column(
            'courseid',
            new lang_string('courseid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$coursecompletionsalias}.courseid")
            ->set_is_sortable(false);

        // coursename.
        $columns[] = (new column(
            'coursename',
            new lang_string('coursename', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$coursecompletionsalias}.coursename")
            ->set_is_sortable(false);

        // timecompleted.
        $columns[] = (new column(
            'timecompleted',
            new lang_string('timecompleted', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$coursecompletionsalias}.timecompleted")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // timestarted.
        $columns[] = (new column(
            'timestarted',
            new lang_string('timestarted', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$coursecompletionsalias}.timestarted")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // timeenrolled.
        $columns[] = (new column(
            'timeenrolled',
            new lang_string('timeenrolled', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$coursecompletionsalias}.timeenrolled")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // finalscore.
        $columns[] = (new column(
            'finalscore',
            new lang_string('finalscore', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$coursecompletionsalias}.finalscore")
            ->set_is_sortable(false);

        // companyid.
        $columns[] = (new column(
            'companyid',
            new lang_string('companyid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$coursecompletionsalias}.companyid")
            ->set_is_sortable(false);

        // licenseid.
        $columns[] = (new column(
            'licenseid',
            new lang_string('licenseid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$coursecompletionsalias}.licenseid")
            ->set_is_sortable(false);

        // licensename.
        $columns[] = (new column(
            'licensename',
            new lang_string('licensename', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$coursecompletionsalias}.licensename")
            ->set_is_sortable(false);

        // licenseallocated.
        $columns[] = (new column(
            'licenseallocated',
            new lang_string('licenseallocated', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$coursecompletionsalias}.licenseallocated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // coursecleared.
        $columns[] = (new column(
            'coursecleared',
            new lang_string('coursecleared', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$coursecompletionsalias}.coursecleared")
            ->set_is_sortable(false);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $coursecompletionsalias = $this->get_table_alias('coursecompletions');

        // coursename.
        $filters[] = (new filter(
            select::class,
            'coursename',
            new lang_string('coursename', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.coursename"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // timeenrolled.
        $filters[] = (new filter(
            select::class,
            'timeenrolled',
            new lang_string('timeenrolled', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.timeenrolled"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // timestarted.
        $filters[] = (new filter(
            select::class,
            'timestarted',
            new lang_string('timestarted', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.timestarted"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // timecompleted.
        $filters[] = (new filter(
            select::class,
            'timecompleted',
            new lang_string('timecompleted', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.timecompleted"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // timeexpires.
        $filters[] = (new filter(
            select::class,
            'timeexpires',
            new lang_string('timeexpires', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.timeexpires"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // finalscore.
        $filters[] = (new filter(
            select::class,
            'finalscore',
            new lang_string('finalscore', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.finalscore"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // licensename.
        $filters[] = (new filter(
            select::class,
            'licensename',
            new lang_string('licensename', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.licensename"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // licenseallocated.
        $filters[] = (new filter(
            select::class,
            'licenseallocated',
            new lang_string('licenseallocated', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$coursecompletionsalias}.licenseallocated"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        return $filters;

    }
}
