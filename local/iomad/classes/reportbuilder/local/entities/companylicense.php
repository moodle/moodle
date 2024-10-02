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
 * Company licenses entity
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class companylicense extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'companylicense' => 'cmpnylic',
            'context' => 'cmpnylicctx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('companylicensedetails', 'block_iomad_company_admin');
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

        $companylicensealias = $this->get_table_alias('companylicense');
        $contextalias = $this->get_table_alias('context');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companylicensealias}.name")
            ->set_is_sortable(true);

        // humanallocation.
        $columns[] = (new column(
            'humanallocation',
            new lang_string('humanallocation', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.humanallocation")
            ->set_is_sortable(true);

        // Company.
        $columns[] = (new column(
            'companyid',
            new lang_string('company', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.companyid")
            ->set_is_sortable(false);

        // validlength.
        $columns[] = (new column(
            'validlength',
            new lang_string('validlength', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.validlength")
            ->set_is_sortable(false);

        // startdate.
        $columns[] = (new column(
            'startdate',
            new lang_string('startdate', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$companylicensealias}.startdate")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // expirydate.
        $columns[] = (new column(
            'expirydate',
            new lang_string('expirydate', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$companylicensealias}.expirydate")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // used.
        $columns[] = (new column(
            'used',
            new lang_string('used', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.used")
            ->set_is_sortable(false);

        // parentid.
        $columns[] = (new column(
            'parentid',
            new lang_string('parentid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.parentid")
            ->set_is_sortable(false);

        // type.
        $columns[] = (new column(
            'type',
            new lang_string('type', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.type")
            ->set_is_sortable(true)
            ->add_callback(static function($type) {
                $licensetypes = [0 => get_string('standard', 'block_iomad_company_admin'),
                                 1 => get_string('reusable', 'block_iomad_company_admin'),
                                 2 => get_string('educator', 'block_iomad_company_admin'),
                                 3 => get_string('educatorreusable', 'block_iomad_company_admin'),
                                 4 => get_string('blanket', 'block_iomad_company_admin')];

                return $licensetypes[$type];
                });

        // program.
        $columns[] = (new column(
            'program',
            new lang_string('program', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.program")
            ->set_is_sortable(true)
            ->add_callback(static function($program) {
                if ($program) {
                    return get_string('yes');
                } else {
                    return get_string('no');
                }
                });

        // instant.
        $columns[] = (new column(
            'instant',
            new lang_string('instant', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.instant")
            ->set_is_sortable(true)
            ->add_callback(static function($instant) {
                if ($instant) {
                    return get_string('yes');
                } else {
                    return get_string('no');
                }
                });

        // cutoffdate.
        $columns[] = (new column(
            'cutoffdate',
            new lang_string('cutoffdate', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$companylicensealias}.cutoffdate")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // clearonexpire.
        $columns[] = (new column(
            'clearonexpire',
            new lang_string('clearonexpire', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$companylicensealias}.clearonexpire")
            ->set_is_sortable(true)
            ->add_callback(static function($clearonexpire) {
                if ($clearonexpire) {
                    return get_string('yes');
                } else {
                    return get_string('no');
                }
                });

        // reference.
        $columns[] = (new column(
            'reference',
            new lang_string('reference', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companylicensealias}.reference")
            ->set_is_sortable(false);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $companylicensealias = $this->get_table_alias('companylicense');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$companylicensealias}.name"
        ))
            ->add_joins($this->get_joins());

        // humanallocation.
        $filters[] = (new filter(
            select::class,
            'humanallocation',
            new lang_string('humanallocation', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.humanallocation"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // startdate.
        $filters[] = (new filter(
            select::class,
            'startdate',
            new lang_string('startdate', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.startdate"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // expirydate.
        $filters[] = (new filter(
            select::class,
            'expirydate',
            new lang_string('expirydate', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.expirydate"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // reference.
        $filters[] = (new filter(
            select::class,
            'reference',
            new lang_string('reference', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.reference"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // cutoffdate.
        $filters[] = (new filter(
            select::class,
            'cutoffdate',
            new lang_string('cutoffdate', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.cutoffdate"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // used.
        $filters[] = (new filter(
            select::class,
            'used',
            new lang_string('used', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.used"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // type.
        $filters[] = (new filter(
            select::class,
            'type',
            new lang_string('type', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.type"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // program.
        $filters[] = (new filter(
            select::class,
            'program',
            new lang_string('program', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companylicensealias}.program"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        return $filters;
    }
}
