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

use moodle_url;
use renderer_base;
use single_button;

/**
 * Class recipients_action_bar - Display the action bar
 *
 * @package   core_badges
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recipients_action_bar extends manage_badge_action_bar {
    /**
     * The template that this tertiary nav should use.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_badges/award_badge';
    }

    /**
     * Export the action bar
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $elements = parent::export_for_template($output);

        // Add button for badge manual award.
        if ($this->badge->has_manual_award_criteria()
                && has_capability('moodle/badges:awardbadge', $this->page->context) && $this->badge->is_active()) {
            $url = new moodle_url('/badges/award.php', ['id' => $this->badge->id]);
            $button = new single_button($url, get_string('award', 'badges'), 'post', true);
            $elements['awardbutton'] = $button->export_for_template($output);
        }
        $thirdpartynav = $this->get_third_party_nav_action($output);
        $elements += $thirdpartynav ?: [];

        return $elements;
    }
}
