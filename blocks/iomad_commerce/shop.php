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

$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_courses, PARAM_INT);        // How many per page.

global $DB;

// Setup the navbar.
// Set the name for the page.
$linktext = get_string('shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/shop.php');

// Page stuff.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add($linktext);


$baseurl = new moodle_url('/blocks/iomad_commerce/shop.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;

// Display the page header.
echo $OUTPUT->header();

flush();

show_basket_info();

// ...**********tag listing and filtering*****************.
if (array_key_exists('tag', $_GET)) {
    $shoptags = get_shop_tags();
    if (in_array( $_GET['tag'], $shoptags )) {
        $SESSION->shoptag = optional_param('tag', '', PARAM_NOTAGS);
    } else {
        unset($SESSION->shoptag);
    }
}

$tagjoin = '';
$tagwhere = '';
$sqlparams = array();
$tagfilters = '';
if (isset($SESSION->shoptag) && $SESSION->shoptag != '') {
    $tagfilters = "<li>";
    $tagfilters .= get_string('filtered_by_tag', 'block_iomad_commerce', '<em>' . $SESSION->shoptag . '</em>' );
    $tagfilters .= "</li>";

    $tagjoin = 'INNER JOIN {course_shoptag} cst ON cst.courseid = c.id
                INNER JOIN {shoptag} st ON cst.shoptagid = st.id';
    $tagwhere = ' AND st.tag = :tag ';
    $sqlparams['tag'] = $SESSION->shoptag;
} else {
    $shoptags = get_shop_tags();
    if (count($shoptags)) {
        echo get_string('filter_by_tag', 'block_iomad_commerce');
        foreach ($shoptags as $shoptag) {
            echo "<a href='?tag=$shoptag'>$shoptag</a> ";
        }
    }
}

echo "<ul id='filtering'>";
echo $tagfilters;

// ...***********search*****************.
$searchkey = '';
if (array_key_exists('q', $_GET)) {
    $searchkey = optional_param('q', '', PARAM_NOTAGS);
    if ($searchkey) {
        $SESSION->shopsearch = $searchkey;
    } else {
        unset($SESSION->shopsearch);
    }
}

$searchwhere = '';
if (isset($SESSION->shopsearch)) {
    $searchkey = $SESSION->shopsearch;
    echo "<li>";
    echo get_string('filtered_by_search', 'block_iomad_commerce', '<em>' . $searchkey . '</em>');
    echo "</li>";

    $searchwhere = ' AND
        (c.fullname like :searchkey1
         OR
         c.shortname like :searchkey2
         OR
         c.summary like :searchkey3
         OR
         css.short_description like :searchkey4
         OR
         css.long_description like :searchkey5
        )
    ';
    for ($i = 1; $i < 6; $i++) {
        $sqlparams['searchkey' . $i] = '%' . $searchkey . '%';
    }
}

// Deal with company specific and shared courses.
if (iomad::is_company_user()) {
    // Get the company courses and the company shared courses.
    $companyid = iomad::get_my_companyid(context_system::instance());
    $company = new company($companyid);
    $sharedsql = " AND ( c.id in ( select courseid from {company_course} where companyid= $company->id)
	               or c.id in ( select courseid from {iomad_courses} where shared=1)
	               or c.id in ( select courseid from {company_shared_courses} where companyid = $company->id)) ";
} else if (is_siteadmin() || iomad::has_capability('block/iomad_company_admin:company_view_all', context_system::instance())) {
    $sharedsql = "";
} else {
    $sharedsql = " AND c.id in ( select courseid from {iomad_courses} where shared=1) ";
}

if (count($sqlparams)) {
    echo '<a href="?tag=&q=">' . get_string('remove_filter', 'block_iomad_commerce') . '</a>';
}

echo "</ul>";

echo get_string('search');
echo "<form method='get'><input type='text' name='q' value='$searchkey' /></form>";

// ...***********create course list sql (includes filtering on tags)*****************.

$sql = 'FROM {course_shopsettings} css
            INNER JOIN {course} c ON c.id = css.courseid ' . $sharedsql .'
            ' . $tagjoin . '
            LEFT JOIN {course_shopblockprice} sbp ON (c.id = sbp.courseid
                                                  AND sbp.id = (SELECT id FROM {course_shopblockprice}
                                                  WHERE courseid = c.id ORDER BY price LIMIT 1 ))
        WHERE css.enabled = 1
        ' . $tagwhere . $searchwhere . '
        GROUP BY c.id, sbp.id, css.id, c.fullname ORDER BY c.fullname';

// Get the number of companies.
$objectcount = $DB->count_records_sql( 'SELECT COUNT(*) ' . $sql, $sqlparams);
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

if ($objectcount) {
    if ($courses = $DB->get_recordset_sql('SELECT c.id AS thecourseid, css.*, c.fullname, sbp.* '
                                          . $sql, $sqlparams, $page, $perpage)) {
        $strbuynow = get_string('buynow', 'block_iomad_commerce');
        $strmoreinfo = get_string('moreinfo', 'block_iomad_commerce');

        $table = new html_table();
        $table->head = array (get_string('Course', 'block_iomad_commerce'), "", "");
        $table->align = array ("left", "center", "center");
        $table->width = "600px";

        foreach ($courses as $course_shopsetting) {
            $available = $course_shopsetting->allow_single_purchase || $course_shopsetting->allow_license_blocks;
            if ($available) {
                $buynowbutton = "<a href=\"course.php?id=$course_shopsetting->thecourseid#buynow\">$strbuynow</a>";

                $price = get_lowest_price_text($course_shopsetting);
                $moreinfobutton = "$price <a href='course.php?id=$course_shopsetting->thecourseid'>$strmoreinfo</a>";
            } else {
                $buynowbutton = "";
                $moreinfobutton = "<a href='course.php?id=$course_shopsetting->thecourseid'>$strmoreinfo</a>";
            }


            $table->data[] = array ("<h3>$course_shopsetting->fullname</h3><p>$course_shopsetting->short_description</p>",
                                $moreinfobutton,
                                $buynowbutton);
        }

        if (!empty($table)) {
            echo html_writer::table($table);
            echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
        }

        $courses->close();
    }
} else {
    echo "<p>" . get_string('nocoursesontheshop', 'block_iomad_commerce') . "</p>";
}

echo $OUTPUT->footer();
