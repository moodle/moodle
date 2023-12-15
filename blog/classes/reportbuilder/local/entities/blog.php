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

namespace core_blog\reportbuilder\local\entities;

use blog_entry_attachment;
use context_system;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Blog entity
 *
 * @package     core_blog
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'post' => 'bp',
            'tag_instance' => 'bti',
            'tag' => 'bt',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('blog', 'core_blog');
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

        $postalias = $this->get_table_alias('post');

        // Title.
        $columns[] = (new column(
            'title',
            new lang_string('entrytitle', 'core_blog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$postalias}.subject")
            ->set_is_sortable(true);

        // Body.
        $summaryfieldsql = "{$postalias}.summary";
        if ($DB->get_dbfamily() === 'oracle') {
            $summaryfieldsql = $DB->sql_order_by_text($summaryfieldsql, 1024);
        }

        $columns[] = (new column(
            'body',
            new lang_string('entrybody', 'core_blog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($summaryfieldsql, 'summary')
            ->add_fields("{$postalias}.summaryformat, {$postalias}.id")
            ->add_callback(static function(?string $summary, stdClass $blog): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                if ($summary === null) {
                    return '';
                }

                // All blog files are stored in system context.
                $context = context_system::instance();
                $summary = file_rewrite_pluginfile_urls($summary, 'pluginfile.php', $context->id, 'blog', 'post', $blog->id);

                return format_text($summary, $blog->summaryformat, ['context' => $context->id]);
            });

        // Attachment.
        $columns[] = (new column(
            'attachment',
            new lang_string('attachment', 'core_repository'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$postalias}.attachment, {$postalias}.id")
            ->add_callback(static function(?bool $attachment, stdClass $post): string {
                global $CFG, $PAGE;
                require_once("{$CFG->dirroot}/blog/locallib.php");

                if (!$attachment) {
                    return '';
                }

                $renderer = $PAGE->get_renderer('core_blog');
                $attachments = '';

                // Loop over attached files, use blog renderer to generate appropriate content.
                $files = get_file_storage()->get_area_files(context_system::instance()->id, 'blog', 'attachment', $post->id,
                    'filename', false);
                foreach ($files as $file) {
                    $attachments .= $renderer->render(new blog_entry_attachment($file, $post->id));
                }

                return $attachments;
            })
            ->set_disabled_aggregation_all();

        // Publish state.
        $columns[] = (new column(
            'publishstate',
            new lang_string('publishto', 'core_blog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$postalias}.publishstate")
            ->set_is_sortable(true)
            ->add_callback(static function(?string $publishstate): string {
                $states = [
                    'draft' => new lang_string('publishtonoone', 'core_blog'),
                    'site' => new lang_string('publishtosite', 'core_blog'),
                    'public' => new lang_string('publishtoworld', 'core_blog'),
                ];

                return (string) ($states[$publishstate] ?? $publishstate ?? '');
            });

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$postalias}.created")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$postalias}.lastmodified")
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
        global $DB;

        $postalias = $this->get_table_alias('post');

        // Title.
        $filters[] = (new filter(
            text::class,
            'title',
            new lang_string('entrytitle', 'core_blog'),
            $this->get_entity_name(),
            "{$postalias}.subject"
        ))
            ->add_joins($this->get_joins());

        // Body.
        $filters[] = (new filter(
            text::class,
            'body',
            new lang_string('entrybody', 'core_blog'),
            $this->get_entity_name(),
            $DB->sql_cast_to_char("{$postalias}.summary")
        ))
            ->add_joins($this->get_joins());

        // Attachment.
        $filters[] = (new filter(
            boolean_select::class,
            'attachment',
            new lang_string('attachment', 'core_repository'),
            $this->get_entity_name(),
            $DB->sql_cast_char2int("{$postalias}.attachment")
        ))
            ->add_joins($this->get_joins());

        // Publish state.
        $filters[] = (new filter(
            select::class,
            'publishstate',
            new lang_string('publishto', 'core_blog'),
            $this->get_entity_name(),
            "{$postalias}.publishstate"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                'draft' => new lang_string('publishtonoone', 'core_blog'),
                'site' => new lang_string('publishtosite', 'core_blog'),
                'public' => new lang_string('publishtoworld', 'core_blog'),
            ]);

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$postalias}.created"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_CURRENT,
                date::DATE_LAST,
                date::DATE_RANGE,
            ]);

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$postalias}.lastmodified"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_CURRENT,
                date::DATE_LAST,
                date::DATE_RANGE,
            ]);

        return $filters;
    }

    /**
     * Return joins necessary for retrieving tags
     *
     * @return string[]
     */
    public function get_tag_joins(): array {
        return $this->get_tag_joins_for_entity('core', 'post', $this->get_table_alias('post') . '.id');
    }
}
