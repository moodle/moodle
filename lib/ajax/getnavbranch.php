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
 * This file is used to deliver a branch from the navigation structure
 * in XML format back to a page from an AJAX call
 *
 * @since 2.0
 * @package core
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

/** Include config */
require_once(dirname(__FILE__) . '/../../config.php');
/** Include course lib for its functions */
require_once($CFG->dirroot.'/course/lib.php');

try {
    // Start buffer capture so that we can `remove` any errors
    ob_start();
    // Require id This is the key for whatever branch we want to get.
    // This accepts alphanum because the courses and my courses branches don't have numerical keys.
    // For those branches we return the alphanum key, courses and mycourses.
    $branchid = required_param('id', PARAM_ALPHANUM);
    // This identifies the type of the branch we want to get
    $branchtype = required_param('type', PARAM_INT);
    // This identifies the block instance requesting AJAX extension
    $instanceid = optional_param('instance', null, PARAM_INT);

    $PAGE->set_context(context_system::instance());

    // Create a global nav object
    $navigation = new global_navigation_for_ajax($PAGE, $branchtype, $branchid);

    $linkcategories = false;

    if ($instanceid!==null) {
        // Get the db record for the block instance
        $blockrecord = $DB->get_record('block_instances', array('id'=>$instanceid,'blockname'=>'navigation'));
        if ($blockrecord!=false) {

            // Instantiate a block_instance object so we can access config
            $block = block_instance('navigation', $blockrecord);

            $trimmode = block_navigation::TRIM_RIGHT;
            $trimlength = 50;

            // Set the trim mode
            if (!empty($block->config->trimmode)) {
                $trimmode = (int)$block->config->trimmode;
            }
            // Set the trim length
            if (!empty($block->config->trimlength)) {
                $trimlength = (int)$block->config->trimlength;
            }
            if (!empty($block->config->linkcategories) && $block->config->linkcategories == 'yes') {
                $linkcategories = true;
            }
        }
    }

    // Create a navigation object to use, we can't guarantee PAGE will be complete
    if (!isloggedin()) {
        $navigation->set_expansion_limit(navigation_node::TYPE_COURSE);
    } else {
        if (isset($block) && !empty($block->config->expansionlimit)) {
            $navigation->set_expansion_limit($block->config->expansionlimit);
        }
    }
    if (isset($block)) {
        $block->trim($navigation, $trimmode, $trimlength, ceil($trimlength/2));
    }
    $converter = new navigation_json();

    // Find the actual branch we are looking for
    if ($branchtype != 0) {
        $branch = $navigation->find($branchid, $branchtype);
    } else if ($branchid === 'mycourses' || $branchid === 'courses') {
        $branch = $navigation->find($branchid, navigation_node::TYPE_ROOTNODE);
    } else {
        throw new coding_exception('Invalid branch type/id passed to AJAX call to load branches.');
    }

    // Remove links to categories if required.
    if (!$linkcategories) {
        foreach ($branch->find_all_of_type(navigation_node::TYPE_CATEGORY) as $category) {
            $category->action = null;
        }
        foreach ($branch->find_all_of_type(navigation_node::TYPE_MY_CATEGORY) as $category) {
            $category->action = null;
        }
    }

    // Stop buffering errors at this point
    $html = ob_get_contents();
    ob_end_clean();
} catch (Exception $e) {
    die('Error: '.$e->getMessage());
}

// Check if the buffer contianed anything if it did ERROR!
if (trim($html) !== '') {
    die('Errors were encountered while producing the navigation branch'."\n\n\n".$html);
}
// Check that branch isn't empty... if it is ERROR!
if (empty($branch) || $branch->nodetype !== navigation_node::NODETYPE_BRANCH) {
    die('No further information available for this branch');
}

// Prepare an XML converter for the branch
$converter->set_expandable($navigation->get_expandable());
// Set XML headers
header('Content-type: text/plain; charset=utf-8');
// Convert and output the branch as XML
echo $converter->convert($branch);
