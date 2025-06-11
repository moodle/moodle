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
 * Mobile output class for qtype_essayautograde
 *
 * @package    qtype_essayautograde
 * @copyright  2019 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_essayautograde\output;
defined('MOODLE_INTERNAL') || die();
/**
 * Mobile output class for essayautograde question type
 *
 * @package    qtype_essayautograde
 * @copyright  2019 Gordon Bateson with grateful thanks to Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {
    /**
     * Returns the essayautograde quetion type for the quiz the mobile app.
     *
     * @return void
     */
    public static function mobile_get_essayautograde($args) {
        global $CFG;

        // $args does not contain anything very useful, except userid:
        // appcustomurlscheme, appid, appisdesktop, appismobile, appiswide,
        // applang, appplatform, appversioncode, appversionname, userid

        // Cache path to the "mobile" folder for this plugin.
        $mobile = $CFG->dirroot.'/question/type/essayautograde/mobile';

        // Get HTML and JS content.
        $html = file_get_contents("$mobile/qtype_essayautograde.html");
        $js = file_get_contents("$mobile/mobile.js");

        // The templates use ionic framework tags:
        // https://ionicframework.com/docs/api/textarea

        // The HTML could be generated from a mustache template and an array of $data
        // $OUTPUT->render_from_template('qtype_essayautograde/mobile_view_page', $data)

        // The template would in: essayautograde/templates/mobile_view_page.mustache
        // For example, see: mod/attendance/templates/mobile_view_page.mustache

        return array(
            'templates' => array(
                array(
                    'id' => 'main',
                    'html' => $html
                )
            ),
            'javascript' => $js
        );
    }
}
