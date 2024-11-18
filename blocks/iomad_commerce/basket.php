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

\block_iomad_commerce\helper::require_commerce_enabled();

$remove = optional_param('remove', 0, PARAM_INT);

global $DB;

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/shop.php');

// Page stuff:.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('basket', 'block_iomad_commerce'));

echo $OUTPUT->header();

flush();

if (!empty($SESSION->basketid)) {
    if ($remove) {
        // Before deleting
        // check that the record to be removed is on the current user's basket
        // (and not on an invoice or on somebody else's basket).
        if ($DB->record_exists_sql('SELECT ii.id
                                      FROM {invoiceitem} ii
                                     WHERE ii.id = :toberemoved
                                       AND
                                    EXISTS ( SELECT id
                                             FROM {invoice} i
                                             WHERE i.id = :basketid
                                             AND i.status = :status
                                             AND i.id = ii.invoiceid
                                             )', array('basketid' => $SESSION->basketid, 'status' => \block_iomad_commerce\helper::INVOICESTATUS_BASKET, 'toberemoved' => $remove))) {
            $DB->delete_records('invoiceitem', array('id' => $remove));
        }
    }

    $baskethtml = \block_iomad_commerce\helper::get_basket_html(1);

    if ($baskethtml) {
        echo $baskethtml;

        echo "<p>";
        if (!\block_iomad_commerce\helper::check_multiple_currencies($SESSION->basketid)) {
            echo '<a href="' . new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/checkout.php') . '" class="btn btn-primary">' . get_string('checkout', 'block_iomad_commerce') .
                 '</a> ' . get_string('or', 'block_iomad_commerce');
        }
        echo ' <a href="' . new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/shop.php') . '" class="btn btn-secondary">' . get_string('returntoshop', 'block_iomad_commerce') .
             '</a></p> ';

    } else {
        echo '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
        echo '<p><a href="' . new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/shop.php') . '" class="btn btn-secondary">' . get_string('returntoshop', 'block_iomad_commerce') .
              '</a></p> ';
    }
} else {
    echo '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
}

echo $OUTPUT->footer();
