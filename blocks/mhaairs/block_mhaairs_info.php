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

require_once('../../config.php');
global $CFG;
require_once($CFG->libdir .'/accesslib.php');
require_once($CFG->libdir .'/datalib.php');
require_once($CFG->libdir .'/moodlelib.php');
require_once($CFG->dirroot.'/blocks/mhaairs/block_mhaairs_util.php');

global $CFG, $COURSE, $USER, $_SERVER;

$blockrequestbase = "/blocks/mhaairs/";

$testuserid = "moodleinstructor";
$testcourseid = "testcourse123";
$testtimestamp = MHUtil::get_time_stamp();

echo "<p>time stamp:<b>".$testtimestamp."</b></p>";
echo "<p>test user id:<b>".$testuserid."</b></p>";
echo "<p>test course id:<b>".$testcourseid."</b></p>";

$customer = $CFG->block_mhaairs_customer_number;
$sharedsecret = $CFG->block_mhaairs_sharedsecret;
$base = $CFG->block_mhaairs_base_address;
$requesttoken = MHUtil::create_token($testuserid);
$encodedrequesttoken = MHUtil::encode_token2($requesttoken, $sharedsecret);
echo "<p>the token is valid:<b>". (MHUtil::is_token_valid($encodedrequesttoken, $sharedsecret) ? "true" : "false"). "</b></p>";

$getuserinfourl = $blockrequestbase."block_mhaairs_action.php?action=GetUserInfo&token=".$encodedrequesttoken;

"<p>encoded request token:<b>".$encodedrequesttoken."</b></p>";
echo "<a href='".$getuserinfourl."' target='blank'>get user info</a>";
