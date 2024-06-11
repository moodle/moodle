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

namespace core_tag\reportbuilder\local\systemreports;

use core\context\system;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\{action, column};
use core_reportbuilder\system_report;
use core_tag\output\{tagflag, tagisstandard, tagname};
use core_tag\reportbuilder\local\entities\{instance, tag};
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;

/**
 * Tags collection system report
 *
 * @package     core_tag
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags extends system_report {

    /**
     * Report initialisation
     */
    protected function initialise(): void {
        $tag = new tag();
        $this->add_entity($tag);

        $tagalias = $tag->get_table_alias('tag');
        $this->set_main_table('tag', $tagalias);

        // Base fields required for various callbacks.
        $this->add_base_fields("{$tagalias}.id, {$tagalias}.rawname, {$tagalias}.flag, {$tagalias}.tagcollid");
        $this->set_checkbox_toggleall(static function(stdClass $tag): array {
            return [$tag->id, get_string('selecttag', 'core_tag', $tag->rawname)];
        });

        // Limit tags to current collection.
        $this->add_base_condition_simple("{$tagalias}.tagcollid", $this->get_parameter('collection', 0, PARAM_INT));

        // Join the instance entity to tag.
        $instance = new instance();
        $instancealias = $instance->get_table_alias('tag_instance');
        $this->add_entity($instance
            ->add_join("LEFT JOIN {tag_instance} {$instancealias} ON {$instancealias}.tagid = {$tagalias}.id")
        );

        // Join the user entity to represent the tag author.
        $user = new user();
        $useralias = $user->get_table_alias('user');
        $this->add_entity($user->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$tagalias}.userid"));

        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        $this->set_downloadable(false);
    }

    /**
     * Report access
     *
     * @return bool
     */
    protected function can_view(): bool {
        global $CFG;

        return !empty($CFG->usetags) && has_capability('moodle/tag:manage', system::instance());
    }

    /**
     * Report columns
     */
    protected function add_columns(): void {
        $tag = $this->get_entity('tag');

        $tagentityname = $tag->get_entity_name();
        $tagalias = $tag->get_table_alias('tag');

        // Name (editable).
        $this->add_column((new column(
            'nameeditable',
            new lang_string('name', 'core_tag'),
            $tagentityname,
        ))
            ->add_fields("{$tagalias}.name, {$tagalias}.rawname, {$tagalias}.tagcollid, {$tagalias}.id")
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true)
            ->set_callback(static function(string $name, stdClass $tag): string {
                global $PAGE;
                $editable = new tagname($tag);
                return $editable->render($PAGE->get_renderer('core'));
            })
        );

        // User.
        $this->add_column_from_entity('user:fullnamewithlink');

        // Instance count.
        $this->add_column_from_entity('instance:component')
            ->set_title(new lang_string('count', 'core_tag'))
            ->set_aggregation('count');

        // Flag (editable).
        $this->add_column((new column(
            'flageditable',
            new lang_string('flag', 'core_tag'),
            $tagentityname,
        ))
            ->add_fields("{$tagalias}.flag, {$tagalias}.id")
            ->set_type(column::TYPE_BOOLEAN)
            ->set_is_sortable(true)
            ->set_callback(static function(bool $flag, stdClass $tag): string {
                global $PAGE;
                $editable = new tagflag($tag);
                return $editable->render($PAGE->get_renderer('core'));
            })
        );

        // Time modified.
        $this->add_column_from_entity('tag:timemodified')
            ->set_callback(fn($timemodified) => format_time(time() - $timemodified));

        // Standard (editable).
        $this->add_column((new column(
            'standardeditable',
            new lang_string('standardtag', 'core_tag'),
            $tagentityname,
        ))
            ->add_fields("{$tagalias}.isstandard, {$tagalias}.id")
            ->set_type(column::TYPE_BOOLEAN)
            ->set_is_sortable(true)
            ->set_callback(static function(bool $standard, stdClass $tag): string {
                global $PAGE;
                $editable = new tagisstandard($tag);
                return $editable->render($PAGE->get_renderer('core'));
            })
        );

        $this->set_initial_sort_column('tag:flageditable', SORT_DESC);
    }

    /**
     * Report filters
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'tag:name',
            'tag:standard',
            'tag:flagged',
        ]);
    }

    /**
     * Report actions
     */
    protected function add_actions(): void {

        // Edit.
        $this->add_action((new action(
            new moodle_url('/tag/edit.php', [
                'id' => ':id',
                'returnurl' => ':returnurl',
            ]),
            new pix_icon('t/edit', ''),
            [],
            false,
            new lang_string('edit'),
        ))
            ->add_callback(static function(stdClass $tag): bool {
                $tag->returnurl = (new moodle_url('/tag/manage.php', ['tc' => $tag->tagcollid]))->out_as_local_url(false);
                return true;
            })
        );

        // Delete.
        $this->add_action(new action(
            new moodle_url('/tag/manage.php', [
                'tc' => ':tagcollid',
                'tagid' => ':id',
                'action' => 'delete',
                'sesskey' => sesskey(),
            ]),
            new pix_icon('t/delete', ''),
            [
                'class' => 'tagdelete text-danger',
            ],
            false,
            new lang_string('delete'),
        ));
    }

    /**
     * Report row class
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return $row->flag ? 'table-warning' : '';
    }
}
