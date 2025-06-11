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

namespace theme_snap;

defined('MOODLE_INTERNAL') || die();
use theme_snap\color_contrast;
global $CFG;
require_once($CFG->dirroot.'/theme/snap/lib.php');

/**
 * Class to render input of type url in settings pages.
 * @package theme_snap
 * @author SL
 * @copyright Blackboard Ltd 2017
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcolorwithcontrast extends \admin_setting_configcolourpicker {

    private $identifier;

    const BASICS = 1;

    const NAVIGATION_BAR = 2;

    const NAVIGATION_BAR_BUTTON = 3;

    const FEATURESPOT_BACK = 4;

    const FOOTER = 5;

    /**
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param string $defaultsetting
     * @param array $previewconfig Array('selector'=>'.some .css .selector','style'=>'backgroundColor');
     */
    public function __construct($identifier, $name, $visiblename, $description, $defaultsetting, array $previewconfig = null,
                                $usedefaultwhenempty = true) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $previewconfig, $usedefaultwhenempty);
        $this->identifier = $identifier;
    }

    public function output_html($data, $query='') {
        $contrast = color_contrast::compare_colors($this->identifier);
        $html = parent::output_html($data, $query);

        if ($this->identifier === self::FEATURESPOT_BACK) {
            $html = $this->contrast_message($contrast[0], $html, get_string('feature_spot_title_color_lower', 'theme_snap'));
            $html = $this->contrast_message($contrast[1], $html, get_string('feature_spot_description_color_lower', 'theme_snap'));
        } else {
            $html = $this->contrast_message($contrast, $html);
        }
        return $html;
    }

    public function contrast_message($contrast, $html, $additionalinfo = '') {
        global $OUTPUT;
        if ($contrast < 4.5) {
            if (!empty($additionalinfo)) {
                $message = get_string('spotinvalidratio', 'theme_snap',
                    [
                        'name' => $additionalinfo,
                        'value' => number_format((float)$contrast, 2),
                    ]);
            } else {
                $message = get_string('invalidratio', 'theme_snap', number_format((float)$contrast, 2));
            }
            $html .= $OUTPUT->notification($message, \core\output\notification::NOTIFY_WARNING);
        }
        return $html;
    }
}
