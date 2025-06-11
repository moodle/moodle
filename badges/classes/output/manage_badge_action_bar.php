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

namespace core_badges\output;

use core_badges\badge;
use moodle_url;
use renderer_base;
use single_button;
use moodle_page;
use url_select;

/**
 * Class manage_badge_action_bar - Display the action bar
 *
 * @package   core_badges
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_badge_action_bar extends base_action_bar {
    /** @var badge $badge The badge we are managing. */
    protected $badge;

    /**
     * manage_badge_action_bar constructor
     *
     * @param badge $badge The badge we are viewing
     * @param moodle_page $page The page object
     */
    public function __construct(badge $badge, moodle_page $page) {
        parent::__construct($page, $badge->type);
        $this->badge = $badge;
    }

    /**
     * The template that this tertiary nav should use.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_badges/manage_badge';
    }

    /**
     * Export the action bar
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $elements = [];
        $params = ['type' => $this->type];
        if ($this->page->context->contextlevel == CONTEXT_COURSE) {
            $params['id'] = $this->page->context->instanceid;
        }
        $elements['button'] = new single_button(new moodle_url('/badges/index.php', $params), get_string('back'), 'get');
        $badgenav = $this->generate_badge_navigation();
        if ($badgenav) {
            $elements['urlselect'] = new url_select($badgenav, $this->page->url->out(false), null);
        }
        foreach ($elements as $key => $element) {
            $elements[$key] = $element->export_for_template($output);
        }
        $additional = $this->get_third_party_nav_action($output);
        $elements += $additional ?: [];

        return $elements;
    }

    /**
     * Returns a multi dimensional array of the links that should be displayed when creating a badge.
     * The keys of the array feed into the text shown to the user and content of each element contain the following:
     *  - url               URL for the option
     *  - additionalparams  Additional params to feed into the url
     *  - capability        The capabilities to check that governs visibility
     * @return array
     */
    protected function get_badge_administration_mapping_construct(): array {
        return [
            'boverview' => [
                'url' => '/badges/overview.php',
                'capability' => ''
            ],
            'bdetails' => [
                'url' => '/badges/edit.php',
                'additionalparams' => ['action' => 'badge'],
                'capability' => 'moodle/badges:configuredetails'
            ],
            'bcriteria' => [
                'url' => '/badges/criteria.php',
                'capability' => 'moodle/badges:configurecriteria'
            ],
            'bmessage' => [
                'url' => '/badges/edit.php',
                'additionalparams' => ['action' => 'message'],
                'capability' => 'moodle/badges:configuremessages'
            ],
            'bawards' => [
                'url' => '/badges/recipients.php',
                'capability' => 'moodle/badges:viewawarded'
            ],
            'bendorsement' => [
                'url' => '/badges/endorsement.php',
                'capability' => 'moodle/badges:configuredetails'
            ],
            'brelated' => [
                'url' => '/badges/related.php',
                'capability' => 'moodle/badges:configuredetails'
            ],
            'balignment' => [
                'url' => '/badges/alignment.php',
                'capability' => 'moodle/badges:configuredetails'
            ],
        ];
    }

    /**
     * Generate the options to be displayed when editing a badge. This feeds into a URL select which will be displayed
     * in the tertiary navigation.
     *
     * @return array
     */
    protected function generate_badge_navigation(): array {
        global $DB;

        $params = ['id' => $this->badge->id];
        $options = [];
        $construct = $this->get_badge_administration_mapping_construct();
        foreach ($construct as $stringidentifier => $checks) {
            if ($checks['capability'] && !has_capability($checks['capability'], $this->page->context)) {
                continue;
            }

            $sql = '';
            switch ($stringidentifier) {
                case 'bawards':
                    $sql = "SELECT COUNT(b.userid)
                              FROM {badge_issued} b
                        INNER JOIN {user} u ON b.userid = u.id
                             WHERE b.badgeid = :badgeid AND u.deleted = 0";
                    break;
                case 'brelated':
                    $sql = "SELECT COUNT(br.badgeid)
                              FROM {badge_related} br
                             WHERE (br.badgeid = :badgeid OR br.relatedbadgeid = :badgeid2)";
                    break;
                case 'balignment':
                    $sql = "SELECT COUNT(bc.id)
                              FROM {badge_alignment} bc
                             WHERE bc.badgeid = :badgeid";
                    break;
            }

            $content = null;
            if ($sql) {
                $content = $DB->count_records_sql($sql, ['badgeid' => $this->badge->id, 'badgeid2' => $this->badge->id]);
            }

            $url = new moodle_url($checks['url'], $params + ($checks['additionalparams'] ?? []));
            $options[get_string($stringidentifier, 'core_badges', $content)] = $url->out(false);
        }
        if (count($options) <= 1) {
            return [];
        }

        return array_flip($options);
    }
}
