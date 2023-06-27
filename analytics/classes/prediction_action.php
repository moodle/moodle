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
class prediction_action extends action {

    /**
     * Prediction action constructor.
     *
     * @param string $actionname They should match a-zA-Z_0-9-, as we apply a PARAM_ALPHANUMEXT filter
     * @param \core_analytics\prediction $prediction
     * @param \moodle_url $actionurl The final URL where the user should be forwarded.
     * @param \pix_icon $icon Link icon
     * @param string $text Link text
     * @param bool $primary Primary button or secondary.
     * @param array $attributes Link attributes
     * @param string|false $type
     * @return void
     */
    public function __construct($actionname, \core_analytics\prediction $prediction, \moodle_url $actionurl, \pix_icon $icon,
                                $text, $primary = false, $attributes = array(), $type = false) {

        $this->actionname = $actionname;
        $this->text = $text;
        $this->set_type($type);

        $this->url = self::transform_to_forward_url($actionurl, $actionname, $prediction->get_prediction_data()->id);

        // The \action_menu_link items are displayed as an icon with a label, no need to show any text.
        if ($primary === false) {
            $this->actionlink = new \action_menu_link_secondary($this->url, $icon, '', $attributes);
        } else {
            $this->actionlink = new \action_menu_link_primary($this->url, $icon, '', $attributes);
        }
    }

    /**
     * Transforms the provided url to an action url so we can record the user actions.
     *
     * Note that it is the caller responsibility to check that the provided actionname is valid for the prediction target.
     *
     * @param  \moodle_url $actionurl
     * @param  string      $actionname
     * @param  int         $predictionid
     * @return \moodle_url
     */
    public static function transform_to_forward_url(\moodle_url $actionurl, string $actionname, int $predictionid): \moodle_url {

        // We want to track how effective are our suggested actions, we pass users through a script that will log these actions.
        $params = ['action' => $actionname, 'predictionid' => $predictionid,
            'forwardurl' => $actionurl->out(false)];
        return new \moodle_url('/report/insights/action.php', $params);
    }
}
