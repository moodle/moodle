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
 * Company license users entity
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class companylicenseusers extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'companylicenseusers' => 'cmpnylicu',
            'context' => 'cmpnylicuctx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('companylicenseusersdetails', 'block_iomad_company_admin');
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

        $companylicenseusersalias = $this->get_table_alias('companylicenseusers');
        $contextalias = $this->get_table_alias('context');

        // licenseid.
        $columns[] = (new column(
            'licenseid',
            new lang_string('licenseid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicenseusersalias}.licenseid")
            ->set_is_sortable(true);

        // userid.
        $columns[] = (new column(
            'userid',
            new lang_string('userid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicenseusersalias}.userid")
            ->set_is_sortable(false);

        // licensecourseid.
        $columns[] = (new column(
            'licensecourseid',
            new lang_string('licensecourseid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicenseusersalias}.licensecourseid")
            ->set_is_sortable(false);

        // issuedate.
        $columns[] = (new column(
            'issuedate',
            new lang_string('issuedate', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$companylicenseusersalias}.issuedate")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // isusing.
        $columns[] = (new column(
            'isusing',
            new lang_string('isusing', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicenseusersalias}.isusing")
            ->set_is_sortable(true)
            ->add_callback(static function($isusing) {
                if ($isusing) {
                    return get_string('yes');
                } else {
                    return get_string('no');
                }
                });

        // timecompleted.
        $columns[] = (new column(
            'timecompleted',
            new lang_string('timecompleted', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$companylicenseusersalias}.timecompleted")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $companylicenseusersalias = $this->get_table_alias('companylicenseusers');

        // issuedate.
        $filters[] = (new filter(
            select::class,
            'issuedate',
            new lang_string('issuedate', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicenseusersalias}.issuedate"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // name.
        $filters[] = (new filter(
            select::class,
            'name',
            new lang_string('name', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicenseusersalias}.name"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // isusing.
        $filters[] = (new filter(
            select::class,
            'isusing',
            new lang_string('isusing', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicenseusersalias}.isusing"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        return $filters;

    }
}
