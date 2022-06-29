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

namespace core_reportbuilder;

use core_reportbuilder\local\report\action;
use lang_string;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use moodle_url;
use pix_icon;

/**
 * Testable system report fixture
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_available extends system_report {

    /**
     * Initialise the report
     */
    protected function initialise(): void {
        $this->set_main_table('user', 'u');
        $this->annotate_entity('user', new lang_string('user'));

        $this->add_column((new column(
            'username',
            new lang_string('username'),
            'user'
        ))
            ->add_joins($this->get_joins())
            ->add_field('u.firstname')
        );

        $withfilters = $this->get_parameter('withfilters', false, PARAM_BOOL);
        if ($withfilters) {
            $this->add_filter((new filter(
                text::class,
                'username',
                new lang_string('username'),
                'user',
                'u.username'
            ))
                ->add_joins($this->get_joins())
            );
        }

        $withactions = $this->get_parameter('withactions', false, PARAM_BOOL);
        if ($withactions) {
            $this->add_action(new action(
                new moodle_url('/user/profile.php', ['id' => ':id']),
                new pix_icon('e/search', get_string('view')),
                [],
                true,
            ));
        }
    }

    /**
     * Ensure we can view the report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return true;
    }

    /**
     * Explicitly set availability of report
     *
     * @return bool
     */
    public static function is_available(): bool {
        return true;
    }
}
