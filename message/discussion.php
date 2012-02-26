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
 * This file was replaced by index.php in Moodle 2.0 and now simply redirects to index.php
 *
 * @package    core_message
 * @copyright  2005 Luis Rodrigues and Martin Dougiamas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require(dirname(dirname(__FILE__)) . '/config.php');
    require_once($CFG->dirroot . '/message/lib.php');

    //the same URL params as in 1.9
    $userid     = required_param('id', PARAM_INT);
    $noframesjs = optional_param('noframesjs', 0, PARAM_BOOL);

    $params = array('user2'=>$userid);
    if (!empty($noframesjs)) {
        $params['noframesjs'] = $noframesjs;
    }
    $url = new moodle_url('/message/index.php', $params);
    redirect($url);
?>
