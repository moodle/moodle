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

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');

\block_iomad_commerce\helper::require_commerce_enabled();

$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_courses, PARAM_INT);        // How many per page.

// Setup the navbar.
// Set the name for the page.
$linktext = get_string('shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/shop.php');

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->navbar->add($linktext);

$baseurl = new moodle_url('/blocks/iomad_commerce/shop.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;

// Display the page header.
echo $OUTPUT->header();

flush();

\block_iomad_commerce\helper::show_basket_info();

// ...**********tag listing and filtering*****************.
if (array_key_exists('tag', $_GET)) {
    $shoptags = \block_iomad_commerce\helper::get_shop_tags();
    if (in_array( $_GET['tag'], $shoptags )) {
        $SESSION->shoptag = optional_param('tag', '', PARAM_NOTAGS);
    } else {
        unset($SESSION->shoptag);
    }
}

$tagjoin = '';
$tagwhere = '';
$sqlparams = ['companyid' => $companyid];
$tagfilters = '';
if (isset($SESSION->shoptag) && $SESSION->shoptag != '') {
    $tagfilters = "<li>";
    $tagfilters .= get_string('filtered_by_tag', 'block_iomad_commerce', '<em>' . $SESSION->shoptag . '</em>' );
    $tagfilters .= "</li>";

    $tagjoin = 'INNER JOIN {course_shoptag} cst ON cst.itemid = csc.id
                INNER JOIN {shoptag} st ON cst.shoptagid = st.id';
    $tagwhere = ' AND st.tag = :tag ';
    $sqlparams['tag'] = $SESSION->shoptag;
} else {
    $shoptags = \block_iomad_commerce\helper::get_shop_tags();
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
        (' . $DB->sql_like("c.fullname", ":searchkey1") . '
         OR
         ' . $DB->sql_like("c.shortname", ":searchkey2") . '
         OR
         ' . $DB->sql_like("c.summary", ":searchkey3") . '
         OR
         ' . $DB->sql_like("css.short_description", ":searchkey4") . '
         OR
         ' . $DB->sql_like("css.long_description", ":searchkey5") . '
         OR
         ' . $DB->sql_like("css.name", ":searchkey6") . '
        )
    ';
    for ($i = 1; $i < 7; $i++) {
        $sqlparams['searchkey' . $i] = '%' . $searchkey . '%';
    }
}

if (count($sqlparams) > 1) {
    echo '<a href="?tag=&q=">' . get_string('remove_filter', 'block_iomad_commerce') . '</a>';
}

echo "</ul>";

echo html_writer::start_tag('div', ['style' =>'display:inline-flex;padding-bottom:10px;', 'id' => 'shoptagsearch']);
echo html_writer::tag('span', get_string('search'));
echo "<form method='get'><input type='text' name='q' class='form-control' style='width: 195px;margin-left:20px;' value='$searchkey' /></form>";
echo html_writer::end_tag('div');

// ...***********create course list sql (includes filtering on tags)*****************.
$typewhere = "";
if (!iomad::has_capability('block/iomad_commerce:buyinbulk', $companycontext)) {
    $typewhere = " AND css.allow_single_purchase = 1 ";
}

$sql = 'FROM {course_shopsettings} css
        JOIN {course_shopsettings_courses} csc ON (css.id = csc.itemid)
        JOIN {course} c ON (csc.courseid = c.id)
            ' . $tagjoin . '
            LEFT JOIN {course_shopblockprice} sbp ON (css.id = sbp.itemid
                                                  AND sbp.id = (SELECT id FROM {course_shopblockprice}
                                                  WHERE itemid = css.id ORDER BY price LIMIT 1 ))
        WHERE css.enabled = 1
        AND css.companyid =:companyid
        ' . $tagwhere . $searchwhere . $typewhere . '
        GROUP BY css.id, sbp.id ORDER BY css.name';

// Get the number of Courses.
$items = $DB->get_records_sql( 'SELECT distinct css.* ' . $sql, $sqlparams);
$itemcount = count($items);

echo $OUTPUT->paging_bar($itemcount, $page, $perpage, $baseurl);

if ($itemcount) {
    $strbuynow = get_string('buynow', 'block_iomad_commerce');
    $strmoreinfo = get_string('moreinfo', 'block_iomad_commerce');

    $table = new html_table();
    $table->head = [get_string('Course', 'block_iomad_commerce'), "", ""];
    $table->align = ["left", "center", "center"];
    $table->width = "95%";

    foreach ($items as $item) {
        $available = ($item->allow_single_purchase || $item->allow_license_blocks) &&
                     (iomad::has_capability('block/iomad_commerce:buyitnow', $companycontext) || iomad::has_capability('block/iomad_commerce:buyinbulk', $companycontext));
        $price = \block_iomad_commerce\helper::get_lowest_price_text($item);
        if ($available) {
            if ($item->allow_single_purchase) {
                $buynowurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_commerce/buynow.php", ['itemid' => $item->id]);
            } else {
                $buynowurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_commerce/item.php", ['itemid' => $item->id]);
            }
            $buynowbutton = "<a href='" . $buynowurl->out() . "' class='btn btn-primary'>$strbuynow</a>";

            $moreinfourl = new moodle_url($CFG->wwwroot . "/blocks/iomad_commerce/item.php", ['itemid' => $item->id]);
            $moreinfobutton = "$price <a href='" . $moreinfourl->out() . "' class='btn btn-secondary'>$strmoreinfo</a>";
        } else {
            $buynowbutton = "";
            $moreinfourl = new moodle_url($CFG->wwwroot . "/blocks/iomad_commerce/item.php", ['itemid' => $item->id]);
            $moreinfobutton = "$price <a href='" . $moreinfourl->out() . "' class='btn btn-secondary'>$strmoreinfo</a>";
        }


        $table->data[] = ["<h3>" . format_string($item->name) . "</h3><p>" .$item->short_description . "</p>",
                          $moreinfobutton,
                          $buynowbutton];
    }

    if (!empty($table)) {
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($itemcount, $page, $perpage, $baseurl);
    }

} else {
    echo "<p>" . get_string('nocoursesontheshop', 'block_iomad_commerce') . "</p>";
}

echo $OUTPUT->footer();