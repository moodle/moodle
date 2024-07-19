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

namespace core_files\reportbuilder\local\entities;

use context;
use context_helper;
use core_collator;
use core_filetypes;
use html_writer;
use lang_string;
use license_manager;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\filters\{boolean_select, date, filesize, select, text};
use core_reportbuilder\local\report\{column, filter};

/**
 * File entity
 *
 * @package     core_files
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'files',
            'context',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('file');
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
        $filesalias = $this->get_table_alias('files');
        $contextalias = $this->get_table_alias('context');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('filename', 'core_repository'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$filesalias}.filename")
            ->set_is_sortable(true);

        // Size.
        $columns[] = (new column(
            'size',
            new lang_string('size'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$filesalias}.filesize")
            ->add_field("CASE WHEN {$filesalias}.filename = '.' THEN 1 ELSE 0 END", 'directory')
            ->set_is_sortable(true)
            ->add_callback(static function($filesize, stdClass $fileinfo): string {
                // Absent file size and/or directory should not return output.
                if ($fileinfo->filesize === null || $fileinfo->directory) {
                    return '';
                }
                return display_size($fileinfo->filesize);
            });

        // Path.
        $columns[] = (new column(
            'path',
            new lang_string('path'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$filesalias}.filepath")
            ->set_is_sortable(true);

        // Type.
        $columns[] = (new column(
            'type',
            new lang_string('type', 'core_repository'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$filesalias}.mimetype")
            ->add_field("CASE WHEN {$filesalias}.filename = '.' THEN 1 ELSE 0 END", 'directory')
            ->set_is_sortable(true)
            ->add_callback(static function($mimetype, stdClass $fileinfo): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                // Absent mime type and/or directory has pre-determined output.
                if ($fileinfo->mimetype === null && !$fileinfo->directory) {
                    return '';
                } else if ($fileinfo->directory) {
                    return get_string('directory');
                }

                return get_mimetype_description($fileinfo->mimetype);
            });

        // Icon.
        $columns[] = (new column(
            'icon',
            new lang_string('icon'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$filesalias}.mimetype")
            ->add_field("CASE WHEN {$filesalias}.filename = '.' THEN 1 ELSE 0 END", 'directory')
            ->set_disabled_aggregation_all()
            ->add_callback(static function($mimetype, stdClass $fileinfo): string {
                global $CFG, $OUTPUT;
                require_once("{$CFG->libdir}/filelib.php");

                if ($fileinfo->mimetype === null && !$fileinfo->directory) {
                    return '';
                }

                if ($fileinfo->directory) {
                    $icon = file_folder_icon();
                    $description = get_string('directory');
                } else {
                    $icon = file_file_icon($fileinfo);
                    $description = get_mimetype_description($fileinfo->mimetype);
                }

                return $OUTPUT->pix_icon($icon, $description, 'moodle', ['class' => 'iconsize-medium']);
            });

        // Author.
        $columns[] = (new column(
            'author',
            new lang_string('author', 'core_repository'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$filesalias}.author")
            ->set_is_sortable(true);

        // License.
        $columns[] = (new column(
            'license',
            new lang_string('license', 'core_repository'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$filesalias}.license")
            ->set_is_sortable(true)
            ->add_callback(static function(?string $license): string {
                global $CFG;
                require_once("{$CFG->libdir}/licenselib.php");

                $licenses = license_manager::get_licenses();
                if ($license === null || !array_key_exists($license, $licenses)) {
                    return '';
                }
                return $licenses[$license]->fullname;
            });

        // Context.
        $columns[] = (new column(
            'context',
            new lang_string('context'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_join("LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$filesalias}.contextid")
            ->add_fields("{$filesalias}.contextid, " . context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->set_is_deprecated('See \'context:name\' for replacement')
            ->add_callback(static function($contextid, stdClass $context): string {
                if ($contextid === null) {
                    return '';
                }

                context_helper::preload_from_record($context);
                return context::instance_by_id($contextid)->get_context_name();
            });

        // Context link.
        $columns[] = (new column(
            'contexturl',
            new lang_string('contexturl'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_join("LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$filesalias}.contextid")
            ->add_fields("{$filesalias}.contextid, " . context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->set_is_deprecated('See \'context:link\' for replacement')
            ->add_callback(static function($contextid, stdClass $context): string {
                if ($contextid === null) {
                    return '';
                }

                context_helper::preload_from_record($context);
                $context = context::instance_by_id($contextid);

                return html_writer::link($context->get_url(), $context->get_context_name());
            });

        // Content hash.
        $columns[] = (new column(
             'contenthash',
            new lang_string('contenthash', 'core_files'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$filesalias}.contenthash")
            ->set_is_sortable(true);

        // Component.
        $columns[] = (new column(
            'component',
            new lang_string('plugin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$filesalias}.component")
            ->set_is_sortable(true);

        // Area.
        $columns[] = (new column(
            'area',
            new lang_string('pluginarea'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$filesalias}.filearea")
            ->set_is_sortable(true);

        // Item ID.
        $columns[] = (new column(
            'itemid',
            new lang_string('pluginitemid'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$filesalias}.itemid")
            ->set_is_sortable(true);

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$filesalias}.timecreated")
            ->add_callback([format::class, 'userdate'])
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $filesalias = $this->get_table_alias('files');

        // Directory.
        $filters[] = (new filter(
            boolean_select::class,
            'directory',
            new lang_string('directory'),
            $this->get_entity_name(),
            "CASE WHEN {$filesalias}.filename = '.' THEN 1 ELSE 0 END"
        ))
            ->add_joins($this->get_joins());

        // Draft.
        $filters[] = (new filter(
            boolean_select::class,
            'draft',
            new lang_string('areauserdraft', 'core_repository'),
            $this->get_entity_name(),
            "CASE WHEN {$filesalias}.component = 'user' AND {$filesalias}.filearea = 'draft' THEN 1 ELSE 0 END"
        ))
            ->add_joins($this->get_joins());

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('filename', 'core_repository'),
            $this->get_entity_name(),
            "{$filesalias}.filename"
        ))
            ->add_joins($this->get_joins());

        // Size.
        $filters[] = (new filter(
            filesize::class,
            'size',
            new lang_string('size'),
            $this->get_entity_name(),
            "{$filesalias}.filesize"
        ))
            ->add_joins($this->get_joins());

        // Type.
        $filters[] = (new filter(
            select::class,
            'type',
            new lang_string('type', 'core_repository'),
            $this->get_entity_name(),
            "{$filesalias}.mimetype"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                $mimetypenames = array_column(core_filetypes::get_types(), 'type');

                // Convert the names into a map of name => description.
                $mimetypes = array_combine($mimetypenames, array_map(static function(string $mimetype): string {
                    return get_mimetype_description($mimetype);
                }, $mimetypenames));

                core_collator::asort($mimetypes);
                return $mimetypes;
            });

        // Author.
        $filters[] = (new filter(
            text::class,
            'author',
            new lang_string('author', 'core_repository'),
            $this->get_entity_name(),
            "{$filesalias}.author"
        ))
            ->add_joins($this->get_joins());

        // License (consider null = 'unknown/license not specified' for filtering purposes).
        $filters[] = (new filter(
            select::class,
            'license',
            new lang_string('license', 'core_repository'),
            $this->get_entity_name(),
            "COALESCE({$filesalias}.license, 'unknown')"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                global $CFG;
                require_once("{$CFG->libdir}/licenselib.php");

                $licenses = license_manager::get_licenses();

                return array_map(static function(stdClass $license): string {
                    return $license->fullname;
                }, $licenses);
            });

        // Content hash.
        $filters[] = (new filter(
            text::class,
            'contenthash',
            new lang_string('contenthash', 'core_files'),
            $this->get_entity_name(),
            "{$filesalias}.contenthash"
        ))
            ->add_joins($this->get_joins());

        // Component.
        $filters[] = (new filter(
            text::class,
            'component',
            new lang_string('plugin'),
            $this->get_entity_name(),
            "{$filesalias}.component"
        ))
            ->add_joins($this->get_joins());

        // Area.
        $filters[] = (new filter(
            text::class,
            'area',
            new lang_string('pluginarea'),
            $this->get_entity_name(),
            "{$filesalias}.filearea"
        ))
            ->add_joins($this->get_joins());

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$filesalias}.timecreated"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_RANGE,
                date::DATE_LAST,
                date::DATE_CURRENT,
            ]);

        return $filters;
    }
}
