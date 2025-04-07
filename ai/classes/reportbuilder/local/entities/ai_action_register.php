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

namespace core_ai\reportbuilder\local\entities;

use core\di;
use core_ai\manager;
use core\component;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\number;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core\output\help_icon;
use lang_string;

/**
 * AI action register entity.
 *
 * Defines all the columns and filters that can be added to reports that use this entity.
 *
 * @package    core_ai
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_action_register extends base {

    #[\Override]
    protected function get_default_tables(): array {
        return [
            'ai_action_register',
        ];
    }

    #[\Override]
    protected function get_default_entity_title(): lang_string {
        return new lang_string('aiactionregister', 'core_ai');
    }

    #[\Override]
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
     * Returns list of all available columns.
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        $mainalias = $this->get_table_alias('ai_action_register');
        $generatetextalias = 'aagt';
        $summarisetextalias = 'aast';
        $explaintextalias = 'aaet';

        // Action name column.
        $columns[] = (new column(
            'actionname',
            new lang_string('action', 'core_ai'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$mainalias}.actionname")
            ->set_is_sortable(true)
            ->add_callback(static function(string $actionname): string {
                return get_string("action_{$actionname}", 'core_ai');
            });

        // Provider column.
        $columns[] = (new column(
            'provider',
            new lang_string('provider', 'core_ai'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$mainalias}.provider")
            ->set_is_sortable(true)
            ->add_callback(static function(string $provider): string {
                if (get_string_manager()->string_exists('pluginname', $provider)) {
                    return get_string('pluginname', $provider);
                } else {
                    // Return as is if the lang string does not exist.
                    return $provider;
                }
            });

        // Success column.
        $columns[] = (new column(
            'success',
            new lang_string('success', 'moodle'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_field("{$mainalias}.success")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'boolean_as_text']);

        // Time created column.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timegenerated', 'core_ai'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$mainalias}.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Prompt tokens column.
        // Only available for summarise_text, generate_text actions and explain_text actions.
        $columns[] = (new column(
            'prompttokens',
            new lang_string('prompttokens', 'core_ai'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_join("
                LEFT JOIN {ai_action_generate_text} {$generatetextalias}
                       ON {$mainalias}.actionid = {$generatetextalias}.id
                      AND {$mainalias}.actionname = 'generate_text'")
            ->add_join("
                LEFT JOIN {ai_action_summarise_text} {$summarisetextalias}
                       ON {$mainalias}.actionid = {$summarisetextalias}.id
                      AND {$mainalias}.actionname = 'summarise_text'")
            ->add_join("
                LEFT JOIN {ai_action_explain_text} {$explaintextalias}
                       ON {$mainalias}.actionid = {$explaintextalias}.id
                      AND {$mainalias}.actionname = 'explain_text'")
            ->set_type(column::TYPE_INTEGER)
            ->add_field("COALESCE({$generatetextalias}.prompttokens, {$summarisetextalias}.prompttokens,
                    {$explaintextalias}.prompttokens)", 'prompttokens')
            ->set_is_sortable(true)
            ->set_help_icon(new help_icon('prompttokens', 'core_ai'))
            ->add_callback(static function(?int $value): string {
                return $value ?? get_string('unknownvalue', 'core_ai');
            });

        // Completion tokens column.
        // Only available for summarise_text, generate_text actions and explain_text actions.
        $columns[] = (new column(
            'completiontokens',
            new lang_string('completiontokens', 'core_ai'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_join("
                LEFT JOIN {ai_action_generate_text} {$generatetextalias}
                       ON {$mainalias}.actionid = {$generatetextalias}.id
                      AND {$mainalias}.actionname = 'generate_text'")
            ->add_join("
                LEFT JOIN {ai_action_summarise_text} {$summarisetextalias}
                       ON {$mainalias}.actionid = {$summarisetextalias}.id
                      AND {$mainalias}.actionname = 'summarise_text'")
            ->add_join("
                LEFT JOIN {ai_action_explain_text} {$explaintextalias}
                       ON {$mainalias}.actionid = {$explaintextalias}.id
                      AND {$mainalias}.actionname = 'explain_text'")
            ->set_type(column::TYPE_INTEGER)
            ->add_field("COALESCE({$generatetextalias}.completiontoken, {$summarisetextalias}.completiontoken,
                    {$explaintextalias}.completiontoken)", 'completiontokens')
            ->set_is_sortable(true)
            ->set_help_icon(new help_icon('completiontokens', 'core_ai'))
            ->add_callback(static function(?int $value): string {
                return $value ?? get_string('unknownvalue', 'core_ai');
            });

        return $columns;
    }

    /**
     * Return list of all available filters.
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $mainalias = $this->get_table_alias('ai_action_register');
        $generatetextalias = 'aagt';
        $summarisetextalias = 'aast';
        $explaintextalias = 'aaet';

        // Action name filter.
        $filters[] = (new filter(
            select::class,
            'actionname',
            new lang_string('action', 'core_ai'),
            $this->get_entity_name(),
            "{$mainalias}.actionname",
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                'explain_text' => new lang_string('action_explain_text', 'core_ai'),
                'generate_image' => new lang_string('action_generate_image', 'core_ai'),
                'generate_text' => new lang_string('action_generate_text', 'core_ai'),
                'summarise_text' => new lang_string('action_summarise_text', 'core_ai'),
            ]);

        // Provider filter.
        $filters[] = (new filter(
            select::class,
            'provider',
            new lang_string('provider', 'core_ai'),
            $this->get_entity_name(),
            "{$mainalias}.provider",
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                $providers = [];

                $records = di::get(manager::class)->get_provider_records();
                foreach ($records as $record) {
                    $component = component::get_component_from_classname($record->provider);
                    $providers[$component] = get_string('pluginname', $component);
                }

                return $providers;
            });

        // Time created filter.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timegenerated', 'core_ai'),
            $this->get_entity_name(),
            "{$mainalias}.timecreated",
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_RANGE,
                date::DATE_PREVIOUS,
                date::DATE_CURRENT,
            ]);

        // Prompt tokens filter.
        $filters[] = (new filter(
            number::class,
            'prompttokens',
            new lang_string('prompttokens', 'core_ai'),
            $this->get_entity_name(),
            "COALESCE({$generatetextalias}.prompttokens, {$summarisetextalias}.prompttokens,
                    {$explaintextalias}.prompttokens)",
        ))
            ->add_joins($this->get_joins());

        // Completion tokens filter.
        $filters[] = (new filter(
            number::class,
            'completiontokens',
            new lang_string('completiontokens', 'core_ai'),
            $this->get_entity_name(),
            "COALESCE({$generatetextalias}.completiontoken, {$summarisetextalias}.completiontoken,
                    {$explaintextalias}.completiontoken)",
        ))
            ->add_joins($this->get_joins());

        // Success filter.
        $filters[] = (new filter(
            boolean_select::class,
            'success',
            new lang_string('success', 'moodle'),
            $this->get_entity_name(),
           "{$mainalias}.success",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
