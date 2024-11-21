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
 * Default course world navigation factory.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;

use block_xp\di;
use block_xp\local\course_world;
use block_xp\local\config\config;
use block_xp\local\permission\access_logs_permissions;
use block_xp\local\permission\access_report_permissions;
use block_xp\local\routing\url_resolver;

/**
 * Default course world navigation factory.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_course_world_navigation_factory implements course_world_navigation_factory {

    /** @var config The admin config. */
    protected $adminconfig;
    /** @var url_resolver The URL resolver. */
    protected $resolver;
    /** @var array The navigation cache. */
    protected $navcache = [];

    /**
     * Constructor.
     *
     * @param url_resolver $resolver The URL resolver.
     * @param config $adminconfig Admin config.
     */
    public function __construct(url_resolver $resolver, config $adminconfig) {
        $this->resolver = $resolver;
        $this->adminconfig = $adminconfig;
    }

    /**
     * Get the navigation.
     *
     * Returns an array containing:
     * - id
     * - text
     * - url
     *
     * @param course_world $world The world.
     * @return array
     */
    public function get_course_navigation(course_world $world) {
        if (!isset($this->navcache[$world->get_courseid()])) {
            $this->navcache[$world->get_courseid()] = $this->make_course_navigation($world);
        }
        return $this->navcache[$world->get_courseid()];
    }

    /**
     * Make the course navigation.
     *
     * @param course_world $world
     * @return array
     */
    protected function make_course_navigation(course_world $world) {
        $links = [];
        $courseid = $world->get_courseid();
        $urlresolver = $this->resolver;
        $renderer = \block_xp\di::get('renderer');
        $accessperms = $world->get_access_permissions();
        $hasaddon = di::get('addon')->is_activated();
        $showpromo = $this->adminconfig->get('enablepromoincourses');
        $config = $world->get_config();
        $canmanage = $accessperms->can_manage();

        if ($config->get('enableinfos') || $canmanage) {
            $links[] = [
                'id' => 'infos',
                'url' => $urlresolver->reverse('infos', ['courseid' => $courseid]),
                'text' => get_string('navinfos', 'block_xp'),
            ];
        }

        $laddernav = null;
        if ($config->get('enableladder') || $canmanage) {
            $laddernav = [
                'id' => 'ladder',
                'url' => $urlresolver->reverse('ladder', ['courseid' => $courseid]),
                'text' => get_string('participants', 'block_xp'),
            ];
        }

        $isteamladderenabled = $config->has('enablegroupladder') && (bool) $config->get('enablegroupladder');
        $teamladdernav = null;
        if ($isteamladderenabled || ($canmanage && ($showpromo || $hasaddon))) {
            $teamladdernav = [
                'id' => 'group_ladder',
                'url' => $urlresolver->reverse('group_ladder', ['courseid' => $courseid]),
                'text' => get_string('teams', 'block_xp'),
                'addonrequired' => !$hasaddon,
            ];
        }
        $links[] = [
            'id' => 'ladder',
            'url' => null,
            'text' => get_string('navladder', 'block_xp'),
            'children' => array_filter([$laddernav, $teamladdernav]),
        ];

        $canviewlogs = $accessperms instanceof access_logs_permissions && $accessperms->can_access_logs();
        $canviewreport = $accessperms instanceof access_report_permissions && $accessperms->can_access_report();
        if ($canviewreport || $canviewlogs) {

            // The link is always called report, but leads to the logs if we can't view the report.
            $mainurl = $urlresolver->reverse('report', ['courseid' => $courseid]);
            if (!$canviewreport) {
                $mainurl = $urlresolver->reverse('log', ['courseid' => $courseid]);
            }

            $reportnav = [
                'id' => 'report',
                'url' => $urlresolver->reverse('report', ['courseid' => $courseid]),
                'text' => get_string('navreport', 'block_xp'),
            ];
            $lognav = [
                'id' => 'log',
                'url' => $urlresolver->reverse('log', ['courseid' => $courseid]),
                'text' => get_string('navlog', 'block_xp'),
            ];

            $links[] = [
                'id' => 'report',
                'url' => $mainurl,
                'text' => get_string('navreport', 'block_xp'),
                'children' => array_filter([
                    $canviewreport ? $reportnav : null,
                    $canviewlogs ? $lognav : null,
                ]),
            ];
        }

        if ($canmanage) {
            $links[] = [
                'id' => 'levels',
                'url' => $urlresolver->reverse('levels', ['courseid' => $courseid]),
                'text' => get_string('navlevels', 'block_xp'),
                'children' => [
                    [
                        'id' => 'levels',
                        'url' => $urlresolver->reverse('levels', ['courseid' => $courseid]),
                        'text' => get_string('navlevelssetup', 'block_xp'),
                    ],
                    [
                        'id' => 'visuals',
                        'url' => $urlresolver->reverse('visuals', ['courseid' => $courseid]),
                        'text' => get_string('navvisuals', 'block_xp'),
                    ],

                ],
            ];
            $links[] = [
                'id' => 'rules',
                'url' => $urlresolver->reverse('rules', ['courseid' => $courseid]),
                'text' => get_string('navpoints', 'block_xp'),
                'children' => array_filter([
                    [
                        'id' => 'rules',
                        'url' => $urlresolver->reverse('rules', ['courseid' => $courseid]),
                        'text' => get_string('naveventrules', 'block_xp'),
                    ],
                    $showpromo || $hasaddon ?
                    [
                        'id' => 'completionrules',
                        'url' => $urlresolver->reverse('completionrules', ['courseid' => $courseid]),
                        'text' => get_string('navcompletionrules', 'block_xp'),
                        'addonrequired' => !$hasaddon,
                    ] : null,
                    $showpromo || $hasaddon ?
                    [
                        'id' => 'graderules',
                        'url' => $urlresolver->reverse('graderules', ['courseid' => $courseid]),
                        'text' => get_string('navgraderules', 'block_xp'),
                        'addonrequired' => !$hasaddon,
                    ] : null,
                    $showpromo || $hasaddon ?
                    [
                        'id' => 'drops',
                        'url' => $urlresolver->reverse('drops', ['courseid' => $courseid]),
                        'text' => get_string('navdrops', 'block_xp'),
                        'addonrequired' => !$hasaddon,
                    ] : null,
                    $showpromo || $hasaddon ? [
                        'id' => 'import',
                        'url' => $urlresolver->reverse('import', ['courseid' => $courseid]),
                        'text' => get_string('navimport', 'block_xp'),
                        'addonrequired' => !$hasaddon,
                    ] : null,
                ]),
            ];

            $links[] = [
                'id' => 'config',
                'url' => $urlresolver->reverse('config', ['courseid' => $courseid]),
                'text' => get_string('navsettings', 'block_xp'),
            ];

            // @codingStandardsIgnoreStart
            //
            // If you got here and you want to disable the promo page, there is no need
            // to hack the code my friend. You can add the following line to your config.php:
            //
            //   $CFG->forced_plugin_settings = ['block_xp' => ['enablepromoincourses' => 0]];
            //
            // @codingStandardsIgnoreEnd
            if ($showpromo || $hasaddon) {
                $star = $renderer->pix_icon('star', '', 'block_xp', ['class' => 'icon']);
                if ($hasaddon) {
                    $star = '';
                }

                $hasnew = '';
                if (\block_xp\local\controller\promo_controller::has_new_content()) {
                    // I'm not proud of this check, there must be a better way.
                    $hasnew = $renderer->new_dot();
                }

                $links[] = [
                    'id' => 'promo',
                    'url' => $urlresolver->reverse('promo', ['courseid' => $courseid]),
                    'text' => $star . get_string('navpromo', 'block_xp') . $hasnew,
                ];
            }
        }

        return $links;
    }

}
