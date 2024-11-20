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
 * Company entity
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class company extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'company' => 'cmpny',
            'context' => 'cmpnyctx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('companydetails', 'block_iomad_company_admin');
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

        $companyalias = $this->get_table_alias('company');
        $contextalias = $this->get_table_alias('context');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.name")
            ->set_is_sortable(true);

        // shortname.
        $columns[] = (new column(
            'shortname',
            new lang_string('shortname'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.shortname")
            ->set_is_sortable(true);

        // code.
        $columns[] = (new column(
            'code',
            new lang_string('code', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.code")
            ->set_is_sortable(true);

        // address.
        $columns[] = (new column(
            'address',
            new lang_string('address'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.address")
            ->set_is_sortable(true);

        // city.
        $columns[] = (new column(
            'city',
            new lang_string('city'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.city")
            ->set_is_sortable(true);

        // region.
        $columns[] = (new column(
            'region',
            new lang_string('region', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.region")
            ->set_is_sortable(true);

        // postcode.
        $columns[] = (new column(
            'postcode',
            new lang_string('postcode', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.postcode")
            ->set_is_sortable(true);

        // country.
        $columns[] = (new column(
            'country',
            new lang_string('country'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.country")
            ->set_is_sortable(true);

        // suspended.
        $columns[] = (new column(
            'suspended',
            new lang_string('suspended'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.suspended")
            ->set_is_sortable(true);

        // parentid.
        $columns[] = (new column(
            'parentid',
            new lang_string('parentid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.parentid")
            ->set_is_sortable(true);

        // eccommerce.
        $columns[] = (new column(
            'eccommerce',
            new lang_string('eccommerce', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.eccommerce")
            ->set_is_sortable(true);

        // hostname.
        $columns[] = (new column(
            'hostname',
            new lang_string('hostname', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.hostname")
            ->set_is_sortable(true);

        // maxusers.
        $columns[] = (new column(
            'maxusers',
            new lang_string('maxusers', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.maxusers")
            ->set_is_sortable(true);

        // validto.
        $columns[] = (new column(
            'validto',
            new lang_string('validto', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.validto")
            ->set_is_sortable(true);

        // suspendafter.
        $columns[] = (new column(
            'suspendafter',
            new lang_string('suspendafter', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.suspendafter")
            ->set_is_sortable(true);

        // companyterminated.
        $columns[] = (new column(
            'companyterminated',
            new lang_string('companyterminated', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$companyalias}.companyterminated")
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $companyalias = $this->get_table_alias('company');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$companyalias}.name"
        ))
            ->add_joins($this->get_joins());

        // Shortname.
        $filters[] = (new filter(
            select::class,
            'shortname',
            new lang_string('shortname', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companyalias}.shortname"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // Address.
        $filters[] = (new filter(
            select::class,
            'address',
            new lang_string('address'),
            $this->get_entity_name(),
            "{$companyalias}.address"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // Region.
        $filters[] = (new filter(
            select::class,
            'region',
            new lang_string('region', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companyalias}.region"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // City.
        $filters[] = (new filter(
            select::class,
            'city',
            new lang_string('city'),
            $this->get_entity_name(),
            "{$companyalias}.city"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // Code.
        $filters[] = (new filter(
            select::class,
            'code',
            new lang_string('code', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companyalias}.code"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // Postcode.
        $filters[] = (new filter(
            select::class,
            'postcode',
            new lang_string('postcode', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$companyalias}.postcode"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // Country.
        $filters[] = (new filter(
            select::class,
            'country',
            new lang_string('country'),
            $this->get_entity_name(),
            "{$companyalias}.country"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        return $filters;
    }
}
