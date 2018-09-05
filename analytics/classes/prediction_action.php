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
 * Representation of a suggested action associated with a prediction.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Representation of a suggested action associated with a prediction.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prediction_action {

    /**
     * @var string
     */
    protected $actionname = null;

    /**
     * @var \action_menu_link
     */
    protected $actionlink = null;

    /**
     * Prediction action constructor.
     *
     * @param string $actionname They should match a-zA-Z_0-9-, as we apply a PARAM_ALPHANUMEXT filter
     * @param \core_analytics\prediction $prediction
     * @param \moodle_url $actionurl
     * @param \pix_icon $icon Link icon
     * @param string $text Link text
     * @param bool $primary Primary button or secondary.
     * @param array $attributes Link attributes
     * @return void
     */
    public function __construct($actionname, \core_analytics\prediction $prediction, \moodle_url $actionurl, \pix_icon $icon,
                                $text, $primary = false, $attributes = array()) {

        $this->actionname = $actionname;

        // We want to track how effective are our suggested actions, we pass users through a script that will log these actions.
        $params = array('action' => $this->actionname, 'predictionid' => $prediction->get_prediction_data()->id,
            'forwardurl' => $actionurl->out(false));
        $url = new \moodle_url('/report/insights/action.php', $params);

        if ($primary === false) {
            $this->actionlink = new \action_menu_link_secondary($url, $icon, $text, $attributes);
        } else {
            $this->actionlink = new \action_menu_link_primary($url, $icon, $text, $attributes);
        }
    }

    /**
     * Returns the action name.
     *
     * @return string
     */
    public function get_action_name() {
        return $this->actionname;
    }

    /**
     * Returns the link to the action.
     *
     * @return \action_menu_link
     */
    public function get_action_link() {
        return $this->actionlink;
    }
}
