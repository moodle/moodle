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
 * Runs word and letter counts in php on the supplied text.
 *
 * @package    atto_count
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../../config.php');

$PAGE->set_url('/lib/editor/atto/plugins/count/ajax.php');
require_sesskey();

$alltext = required_param('alltext', PARAM_RAW);

$result = array(
    'allTextWords' => count_words($alltext),
    'allTextLetters' => count_letters($alltext)
);
echo json_encode($result);

die();
