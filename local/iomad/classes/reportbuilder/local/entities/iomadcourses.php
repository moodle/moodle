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
 * IOMAD courses entity
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class iomadcourses extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'iomadcourses' => 'iomadc',
            'context' => 'iomadcctx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('iomadcoursesdetails', 'block_iomad_company_admin');
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

        $iomadcoursesalias = $this->get_table_alias('iomadcourses');
        $contextalias = $this->get_table_alias('context');

        // courseid.
        $columns[] = (new column(
            'courseid',
            new lang_string('courseid', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.courseid")
            ->set_is_sortable(true);

        // licensed.
        $columns[] = (new column(
            'licensed',
            new lang_string('licensed', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.licensed")
            ->set_is_sortable(true)
            ->add_callback(static function($licensed) {
                if ($licensed) {
                    return get_string('yes');
                } else {
                    return get_string('no');
                }
                });

        // shared.
        $columns[] = (new column(
            'shared',
            new lang_string('shared', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.shared")
            ->set_is_sortable(true)
            ->add_callback(static function($shared) {
                $sharedselectoptions = array('0' => get_string('no'),
                                             '1' => get_string('open', 'block_iomad_company_admin'),
                                             '2' => get_string('closed', 'block_iomad_company_admin'));
                return $sharedselectoptions[$shared];
                });

        // validlength.
        $columns[] = (new column(
            'validlength',
            new lang_string('validlength', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.validlength")
            ->set_is_sortable(false);

        // warnexpire.
        $columns[] = (new column(
            'warnexpire',
            new lang_string('warnexpire', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.warnexpire")
            ->set_is_sortable(true);

        // warncompletion.
        $columns[] = (new column(
            'warncompletion',
            new lang_string('warncompletion', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.warncompletion")
            ->set_is_sortable(true);

        // notifyperiod.
        $columns[] = (new column(
            'notifyperiod',
            new lang_string('notifyperiod', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.notifyperiod")
            ->set_is_sortable(false);

        // expireafter.
        $columns[] = (new column(
            'expireafter',
            new lang_string('expireafter', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.expireafter")
            ->set_is_sortable(false);

        // warnnotstarted.
        $columns[] = (new column(
            'warnnotstarted',
            new lang_string('warnnotstarted', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.warnnotstarted")
            ->set_is_sortable(true);

        // hasgrade.
        $columns[] = (new column(
            'hasgrade',
            new lang_string('hasgrade', 'block_iomad_company_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$iomadcoursesalias}.hasgrade")
            ->set_is_sortable(true)
            ->add_callback(static function($hasgrade) {
                if ($hasgrade) {
                    return get_string('yes');
                } else {
                    return get_string('no');
                }
                });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $iomadcoursesalias = $this->get_table_alias('iomadcourses');

        // licensed.
        $filters[] = (new filter(
            select::class,
            'licensed',
            new lang_string('licensed', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$iomadcoursesalias}.licensed"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // shared.
        $filters[] = (new filter(
            select::class,
            'shared',
            new lang_string('shared', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$iomadcoursesalias}.shared"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // validlength.
        $filters[] = (new filter(
            select::class,
            'validlength',
            new lang_string('validlength', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$iomadcoursesalias}.validlength"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // warnexpire.
        $filters[] = (new filter(
            select::class,
            'warnexpire',
            new lang_string('warnexpire', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$iomadcoursesalias}.warnexpire"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // expireafter.
        $filters[] = (new filter(
            select::class,
            'expireafter',
            new lang_string('expireafter', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$iomadcoursesalias}.expireafter"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // warncompletion.
        $filters[] = (new filter(
            select::class,
            'warncompletion',
            new lang_string('warncompletion', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$iomadcoursesalias}.warncompletion"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        // hasgrade.
        $filters[] = (new filter(
            select::class,
            'hasgrade',
            new lang_string('hasgrade', 'block_iomad_company_admin'),
            $this->get_entity_name(),
            "{$iomadcoursesalias}.hasgrade"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
            ]);

        return $filters;

    }
}
