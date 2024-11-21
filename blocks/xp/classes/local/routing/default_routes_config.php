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

/**
 * Routes config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\routing;

/**
 * Routes config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_routes_config extends static_routes_config {

    /**
     * Constructor.
     */
    public function __construct() {
        $routes = [
            new route_definition(
                'home',
                '/',
                '~^/$~',
                'index'
            ),
            new route_definition(
                'config',
                '/config/:courseid',
                '~^/config/(\d+)$~',
                'config',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'infos',
                '/infos/:courseid',
                '~^/infos/(\d+)$~',
                'infos',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'ladder',
                '/ladder/:courseid',
                '~^/ladder/(\d+)$~',
                'ladder',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'group_ladder',
                '/group/ladder/:courseid',
                '~^/group/ladder/(\d+)$~',
                'group_ladder',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'levels',
                '/levels/:courseid',
                '~^/levels/(\d+)$~',
                'levels',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'log',
                '/log/:courseid',
                '~^/log/(\d+)$~',
                'log',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'report',
                '/report/:courseid',
                '~^/report/(\d+)$~',
                'report',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'rules',
                '/rules/:courseid',
                '~^/rules/(\d+)$~',
                'rules',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'completionrules',
                '/completionrules/:courseid',
                '~^/completionrules/(\d+)$~',
                'completion_rules',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'graderules',
                '/graderules/:courseid',
                '~^/graderules/(\d+)$~',
                'grade_rules',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'drops',
                '/drops/:courseid',
                '~^/drops/(\d+)$~',
                'drops',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'import',
                '/import/:courseid',
                '~^/import/(\d+)$~',
                'import',
                [
                    1 => 'courseid',
                ]
            ),
            new route_definition(
                'visuals',
                '/visuals/:courseid',
                '~^/visuals/(\d+)$~',
                'visuals',
                [
                    1 => 'courseid',
                ]
            ),

            // Admin routes.
            new route_definition(
                'admin/levels',
                '/admin/levels',
                '~^/admin/levels$~',
                'admin_levels'
            ),
            new route_definition(
                'admin/rules',
                '/admin/rules',
                '~^/admin/rules$~',
                'admin_rules'
            ),
            new route_definition(
                'admin/visuals',
                '/admin/visuals',
                '~^/admin/visuals$~',
                'admin_visuals'
            ),
            new route_definition(
                'debug',
                '/i/am/dev',
                '~^/i/am/dev$~',
                'debug'
            ),

            // Promo.
            new route_definition(
                'admin/promo',
                '/promo',
                '~^/promo$~',
                'promo'
            ),
            new route_definition(
                'promo',
                '/promo/:courseid',
                '~^/promo/(\d+)$~',
                'promo',
                [
                    1 => 'courseid',
                ]
            ),
        ];

        parent::__construct($routes);
    }

}
