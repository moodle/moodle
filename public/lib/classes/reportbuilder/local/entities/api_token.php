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

namespace core\reportbuilder\local\entities;

use core\api\entity\api_token_entity;
use core\api\token_manager;
use core\clock;
use core\di;
use core\url;
use core\output\help_icon;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, select, text};
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\{column, filter};
use lang_string;

/**
 * Personal access token report builder entity.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_token extends base {
    /** @var int How many scopes a row shows before the rest are folded away. */
    protected const int SCOPES_SHOWN = 5;

    /** @var int Filter value for a token which has not lapsed. */
    public const int STATUS_ACTIVE = 0;

    /** @var int Filter value for a token which has lapsed. */
    public const int STATUS_EXPIRED = 1;

    #[\Override]
    protected function get_default_tables(): array {
        return [
            'rest_api_tokens',
        ];
    }

    #[\Override]
    protected function get_default_entity_title(): lang_string {
        return new lang_string('personalaccesstokens');
    }

    #[\Override]
    protected function get_available_columns(): array {
        $alias = $this->get_table_alias('rest_api_tokens');

        // The description sits under the name in the same cell: it explains that token rather
        // than standing on its own, and a column of its own would mostly be empty.
        $columns[] = (new column(
            'name',
            new lang_string('pat_name'),
            $this->get_entity_name(),
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$alias}.name, {$alias}.description")
            ->set_is_sortable(true, ["{$alias}.name"])
            ->add_attributes(['class' => 'w-25'])
            ->add_callback(static function ($value, \stdClass $row): string {
                // Both are user entered, so both are escaped here rather than trusted as markup.
                $name = \core\output\html_writer::div(s($row->name), 'fw-bold');

                if ($row->description === null || trim($row->description) === '') {
                    return $name;
                }

                return $name . \core\output\html_writer::div(s($row->description), 'text-muted');
            });

        // Scopes are stored space separated, so they are unrolled into one badge each.
        $columns[] = (new column(
            'scopes',
            new lang_string('pat_scopes'),
            $this->get_entity_name(),
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$alias}.scopes")
            ->set_is_sortable(false)
            ->add_callback(static function (?string $value): string {
                global $OUTPUT;

                if ($value === null || trim($value) === '') {
                    return '';
                }

                // Resolved once per report rather than once per row: every row maps the same
                // identifiers, and building the list instantiates every scope class in the site.
                static $names = null;
                $names ??= di::get(token_manager::class)->get_scope_names();
                $badges = array_map(
                    // A scope withdrawn by its plugin still shows, by identifier, rather than vanishing.
                    // text-nowrap keeps each name on one line: allowed to wrap, a name breaks into a
                    // narrow centred stack that is unreadable in a phone-width column.
                    static fn(string $scope): string => \core\output\html_writer::span(
                        $names[$scope] ?? $scope,
                        'badge text-bg-secondary text-nowrap',
                    ),
                    array_filter(explode(' ', $value), static fn($scope) => $scope !== ''),
                );

                // Wrapping happens between badges rather than inside them.
                $all = \core\output\html_writer::div(implode('', $badges), 'd-flex flex-wrap gap-1');

                if (count($badges) <= self::SCOPES_SHOWN) {
                    return $all;
                }

                // A token granted a dozen scopes would otherwise set the height of every other
                // row. core/showmore carries the toggle and flips its own label between showing
                // more and showing less, so neither wording has to be invented here.
                return $OUTPUT->render_from_template('core/showmore', [
                    'collapsedcontent' => \core\output\html_writer::div(
                        implode('', array_slice($badges, 0, self::SCOPES_SHOWN)),
                        'd-flex flex-wrap gap-1',
                    ),
                    'expandedcontent' => $all,
                    // Reversed so the button sits below the scopes rather than above them, which
                    // also drops the float the component would otherwise place it with.
                    'extraclasses' => 'd-flex flex-column-reverse gap-2',
                    'buttonextraclasses' => 'align-self-start',
                ]);
            });

        $columns[] = (new column(
            'expirytime',
            new lang_string('pat_validuntil'),
            $this->get_entity_name(),
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$alias}.expirytime")
            ->set_is_sortable(true)
            ->set_help_icon(new help_icon('pat_validuntil', 'core', \core_date::get_user_timezone()))
            ->add_callback(static function (?int $value): string {
                global $OUTPUT;

                if ($value === null) {
                    return get_string('pat_never');
                }

                $formatted = token_manager::format_datetime($value);

                if (!token_manager::is_expiring_soon($value)) {
                    return $formatted;
                }

                // The icon carries its own text, so the warning is not colour or shape alone.
                return $formatted . ' ' . $OUTPUT->pix_icon(
                    'i/warning',
                    get_string('pat_expiringsoon', '', token_manager::EXPIRY_IMMINENT_DAYS),
                );
            });

        $columns[] = (new column(
            'timecreated',
            new lang_string('pat_timecreated'),
            $this->get_entity_name(),
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$alias}.timecreated")
            ->set_is_sortable(true)
            // Every date in this report is rendered in the viewer's own time zone, which is worth
            // saying once: a token created "today" can look like yesterday to a colleague abroad.
            ->set_help_icon(new help_icon('pat_timecreated', 'core', \core_date::get_user_timezone()))
            ->add_callback(static fn(?int $value): string => $value === null
                ? ''
                : token_manager::format_datetime($value));

        // When and where a token was last used answer the same question, so they share a cell:
        // "last used yesterday" alone does not tell the owner whether it was them.
        $columns[] = (new column(
            'lastaccess',
            new lang_string('pat_lastaccess'),
            $this->get_entity_name(),
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$alias}.lastaccessed, {$alias}.lastaccessip")
            ->set_is_sortable(true, ["{$alias}.lastaccessed"])
            ->add_callback(static function ($value, \stdClass $row): string {
                if ($row->lastaccessed === null) {
                    return get_string('pat_neverused');
                }

                $when = \core\output\html_writer::div(token_manager::format_datetime((int) $row->lastaccessed));

                if ($row->lastaccessip === null || trim($row->lastaccessip) === '') {
                    return $when;
                }

                // Labelled, because the column no longer says what this second value is — a
                // screen reader would otherwise announce a bare number after the date.
                return $when . \core\output\html_writer::div(
                    get_string('pat_ip', '', s($row->lastaccessip)),
                    'text-muted',
                );
            });

        // Status is not stored: it is what the expiry means once the report has run.
        $columns[] = (new column(
            'status',
            new lang_string('pat_status'),
            $this->get_entity_name(),
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$alias}.expirytime")
            ->set_is_sortable(false)
            ->add_callback(static function ($value, \stdClass $row): string {
                $expired = api_token_entity::expiry_has_passed(
                    $row->expirytime === null ? null : (int) $row->expirytime,
                );

                // The subtle pairs, which carry their own foreground: measured 10.77:1 and
                // 10.58:1 in light mode, 7.38:1 and 6.74:1 in dark. A badge that sets only a
                // background inherits whatever foreground it lands on, which is how an earlier
                // revision of this column shipped at 1.49:1.
                return \core\output\html_writer::span(
                    get_string($expired ? 'pat_statusexpired' : 'pat_statusactive'),
                    'badge ' . ($expired
                        ? 'bg-danger-subtle text-danger-emphasis'
                        : 'bg-success-subtle text-success-emphasis'),
                );
            });

        $columns[] = (new column(
            'actions',
            new lang_string('actions'),
            $this->get_entity_name(),
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$alias}.id, {$alias}.name, {$alias}.expirytime")
            ->set_is_sortable(false)
            ->add_callback(static fn($value, \stdClass $row): string => self::get_row_action($row));

        return $columns;
    }

    /**
     * The control this row offers: revoking while the token still grants access, deleting once
     * it does not. Rendered as a column rather than a report action, because report actions are
     * always drawn behind a kebab menu and this is the only thing a row can do.
     *
     * @param \stdClass $row The row, carrying the id, name and expiry.
     * @return string
     */
    protected static function get_row_action(\stdClass $row): string {
        $expired = api_token_entity::expiry_has_passed($row->expirytime === null ? null : (int) $row->expirytime);

        // A posted form rather than a link: removing a token changes state, and a link that
        // changes state can be followed by a prefetcher, a scanner or a crawler. The modal
        // has no destination to navigate to, so on confirmation it resubmits this button and
        // the form posts. See lib/amd/src/utility.js.
        $attributes = [
            'type' => 'submit',
            'class' => 'btn btn-link text-danger p-0',
            'data-modal' => 'confirmation',
            'data-modal-title' => get_string($expired ? 'pat_deleteconfirm' : 'pat_revokeconfirm', '', $row->name),
            'data-modal-content' => get_string($expired ? 'pat_deleteexpiredwarning' : 'pat_deleteactivewarning'),
            'data-modal-yes-button' => get_string($expired ? 'pat_delete' : 'pat_revoke'),
        ];

        if (!$expired) {
            // Revoking cuts off access that currently works, so it gets the destructive modal.
            $attributes['data-modal-type'] = 'delete';
        }

        $hidden = '';

        foreach (['action' => 'delete', 'id' => $row->id, 'confirm' => 1, 'sesskey' => sesskey()] as $name => $value) {
            $hidden .= \core\output\html_writer::empty_tag('input', [
                'type' => 'hidden',
                'name' => $name,
                'value' => $value,
            ]);
        }

        return \core\output\html_writer::tag(
            'form',
            $hidden . \core\output\html_writer::tag(
                'button',
                get_string($expired ? 'pat_delete' : 'pat_revoke'),
                $attributes,
            ),
            [
                'method' => 'post',
                'action' => (new url('/user/personalaccesstokens.php'))->out(false),
            ],
        );
    }

    #[\Override]
    protected function get_available_filters(): array {
        $alias = $this->get_table_alias('rest_api_tokens');

        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('pat_name'),
            $this->get_entity_name(),
            "{$alias}.name",
        ));

        $filters[] = (new filter(
            date::class,
            'expirytime',
            new lang_string('pat_validuntil'),
            $this->get_entity_name(),
            "{$alias}.expirytime",
        ));

        $filters[] = (new filter(
            text::class,
            'lastaccessip',
            new lang_string('pat_lastaccessip'),
            $this->get_entity_name(),
            "{$alias}.lastaccessip",
        ));

        $filters[] = (new filter(
            date::class,
            'lastaccessed',
            new lang_string('pat_lastaccess'),
            $this->get_entity_name(),
            "{$alias}.lastaccessed",
        ));

        // Status is not a stored column, so the filter asks the database the same question the
        // status column asks in PHP: has this token's expiry passed?
        $now = database::generate_param_name();
        $filters[] = (new filter(
            select::class,
            'status',
            new lang_string('pat_status'),
            $this->get_entity_name(),
            "CASE WHEN {$alias}.expirytime IS NOT NULL AND {$alias}.expirytime <= :{$now}
                  THEN " . self::STATUS_EXPIRED . "
                  ELSE " . self::STATUS_ACTIVE . " END",
            [$now => di::get(clock::class)->time()],
        ))
            ->set_options([
                self::STATUS_ACTIVE => new lang_string('pat_statusactive'),
                self::STATUS_EXPIRED => new lang_string('pat_statusexpired'),
            ]);

        return $filters;
    }
}
