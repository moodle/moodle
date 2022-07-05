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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once('lib.php');

require_commerce_enabled();

$courseid         = required_param('id', PARAM_INT);
$licenseformempty = optional_param('licenseformempty', 0, PARAM_INT);

global $DB;

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/shop.php');

// Page stuff.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add($linktext, $linkurl);

if ($course = $DB->get_record_sql('SELECT css.*, c.*, sbp.*
                                   FROM {course_shopsettings} css
                                   INNER JOIN {course} c ON c.id = css.courseid
                                   LEFT JOIN {course_shopblockprice} sbp ON c.id = sbp.courseid
                                   AND sbp.id =
                                    (SELECT id FROM {course_shopblockprice}
                                    WHERE courseid = c.id
                                    ORDER BY price LIMIT 1)
                                   WHERE css.enabled = 1
                                   AND c.id = :id', array('id' => $courseid))) {
    $PAGE->navbar->add($course->fullname);
}

echo $OUTPUT->header();

flush();

show_basket_info();

if ($course) {
    $strbuynow = get_string('buynow', 'block_iomad_commerce');
    $strmoreinfo = get_string('moreinfo', 'block_iomad_commerce');

    echo "<h3>$course->fullname</h3>";

    if ($course->long_description) {
        echo $course->long_description;
    } else {
        echo $course->summary;
    }

    if ($course->allow_single_purchase || $course->allow_license_blocks) {
        $table = new html_table();
        $table->head = array (get_string('priceoptions', 'block_iomad_commerce'), "", "");
        $table->align = array ("left", "center", "center");
        $table->width = "600px";


        if ($course->allow_single_purchase) {
            $table->data[] = array(get_string('single_purchase', 'block_iomad_commerce'),
                                   $course->single_purchase_currency . number_format($course->single_purchase_price, 2),
                                   "<a href='buynow.php?courseid=$course->courseid'>" .
                                   get_string('buynow', 'block_iomad_commerce') .
                                   "<a>");
        }

        $form = '';

        if ($course->allow_license_blocks) {
            $priceblocks = $DB->get_records('course_shopblockprice', array('courseid' => $course->courseid), 'price_bracket_start');

            if (count($priceblocks)) {
                if (iomad::is_company_admin()) {
                    foreach ($priceblocks as $priceblock) {
                        $table->data[] = array(get_string('licenseblock_n', 'block_iomad_commerce',
                                                           $priceblock->price_bracket_start),
                                                $priceblock->currency . ' ' . number_format($priceblock->price, 2),
                                                '');
                    }
                } else {
                    $price = get_lowest_price_text($course);
                    if (!isloggedin()) {
                        $table->data[] = array(get_string('loginforlicenseoptions', 'block_iomad_commerce'), $price, "");
                    } else {
                        $table->data[] = array(get_string('licenseoptionsavailableforregisteredcompanies', 'block_iomad_commerce'),
                                               $price, "");
                    }
                }

                if (isloggedin() && iomad::is_company_admin()) {
                    $msg = '';
                    if ($licenseformempty) {
                        $msg = "<p class='error'>" . get_string('licenseformempty', 'block_iomad_commerce') . "</p>";
                    }

                    $form = '<form action="buynow.php?courseid=$course->courseid" method="get">
                        <input type="hidden" name="courseid" value="' . $courseid . '" />
                        <input type="hidden" name="licenses" value="1" />
                        ' . $msg . '
                        <label for="id_nlicenses"> How many licenses? </label>
                        <input type="text" name="nlicenses" id="id_nlicenses" value="" />
                        <input type="submit" value="' . get_string('buynow', 'block_iomad_commerce') . '" />
                    </form>';
                }
            }
        }

        if (!empty($table)) {
            echo "<a name='buynow'></a>";
            echo html_writer::table($table);
            echo $form;
        }
    }

} else {
    echo "<p>" . get_string('courseunavailable', 'block_iomad_commerce') . "</p>";
}

echo $OUTPUT->footer();
