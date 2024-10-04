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
 * Representation of a suggested bulk action.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Representation of a suggested bulk action.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulk_action extends action {

    /**
     * Prediction action constructor.
     *
     * @param string $actionname They should match a-zA-Z_0-9-, as we apply a PARAM_ALPHANUMEXT filter
     * @param \moodle_url $actionurl The final URL where the user should be forwarded.
     * @param \pix_icon $icon Link icon
     * @param string $text Link text
     * @param bool $primary Primary button or secondary.
     * @param array $attributes Link attributes
     * @param string|false $type
     * @return void
     */
    public function __construct($actionname, \moodle_url $actionurl, \pix_icon $icon,
                                $text, $primary = false, $attributes = array(), $type = false) {
        global $OUTPUT;

        $this->actionname = $actionname;
        $this->text = $text;
        $this->set_type($type);

        // We want to track how effective are our suggested actions, we pass users through a script that will log these actions.
        $params = array('action' => $this->actionname, 'forwardurl' => $actionurl->out(false));
        $this->url = new \moodle_url('/report/insights/action.php', $params);

        $label = $OUTPUT->render($icon) . $this->text;
        $this->actionlink = new \single_button($this->url, $label,
            'get',
            $primary ? \single_button::BUTTON_PRIMARY : \single_button::BUTTON_SECONDARY,
            $attributes);
    }
}
