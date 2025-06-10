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

// Author: Adam Zapletal.

$string['my_picture:addinstance'] = 'Add a new my picture block';
$string['my_picture:myaddinstance'] = 'Add a new my picture block';

// Strings for block content.
$string['pluginname'] = 'My Profile Picture';
$string['reprocess'] = 'Reprocess';

// Strings for cron.
$string['start'] = 'Starting profile picture updates...';
$string['fetched'] = 'Fetching {$a} users...';
$string['completed'] = 'Completed {$a}...';
$string['finish'] = 'Finished {$a} profile picture update(s)';
$string['numsuccess'] = 'Successful updates';
$string['numnopic'] = 'Missing pictures';
$string['numbadid'] = 'Invalid idnumbers';
$string['numerror'] = 'Errors';
$string['elapsed'] = 'Elapsed time: {$a} seconds';
$string['misconfigured_message'] = 'The My Profile Picture block failed to contact the photos webservice. Please check the settings and verify the webservice is operating normally at the address {$a}';
$string['misconfigured_subject'] = "MyProfilePicture ERROR";
$string["cron_webservice_err"]   = "\nWebservice communication error.\n It is possible that the URLs are misconfigured in the Admin settings area.";
$string["cron_webservice_response"]   = "\n" . 'Webservice responded with error code: {$a->response} and content type: {$a->content} for {$a->idnumber}.' . "<br />\n";
$string["grab_mypictures"]   = "Grab my_picture images";

// Strings for reprocess.php.
$string['reprocess_title'] = 'Reprocess My Picture';
$string['badiduser'] = 'You do not have a valid LSU ID Number. <br />This number will be populated when you are actively enrolled in Moodle courses. The system will automatically load your latest photo on file with the LSU Auxilary Services Tiger Card office.';
$string['nopicuser'] = 'We were unable to find your photo within the LSU Auxilary Services Tiger Card system. <br />If you are unhappy with your current photo or do not have a photo, please consider uploading your photo to your Moodle user profile.';
$string['erroruser'] = 'An error has occurred';
$string['successuser'] = 'The photo above was fetched from the LSU Auxilary Services Tiger Card office. <br />Please clear your browser\'s cache, log out, log back in, and refresh the dashboard if your image isn\'t updated to match the photo above. <br />If you are unhappy with your current photo or do not have a photo, please consider uploading your photo to your Moodle user profile.';

// Strings for fetch_missing.php and reprocess_all.php.
$string['reprocess_all_title'] = 'Reprocess All Profile Pictures';
$string['fetch_missing_title'] = 'Fetch Missing Profile Pictures';
$string['fetching_start'] = 'Fetching missing pictures...';
$string['all_start'] = 'Reprocessing all pictures...';
$string['error_admin'] = 'Could not create picture on this server';
$string['bad_id_admin'] = 'Invalid idnumber';
$string['nopic_admin'] = 'Not found';
$string['success'] = 'Success';
$string['no_missing_pictures'] = 'There were no missing profile pictures in the system';

// Strings for settings.php.
$string['fetch'] = 'Fectch missing on cron';
$string['fetch_desc'] = 'At every cron interval, _My Profile Picture_ will fetch missing photos.';
$string['cron_users'] = 'Cron Users';
$string['cron_users_desc'] = 'Number of users to process per cron run';
$string['webservice_url'] = 'myPicture WebService URL';
$string['ready_url'] = 'myPicture Ready URL';
$string['update_url'] = 'myPicture Update URL';
$string['url'] = 'URL';
$string['reprocess_all'] = 'Reprocess all profile pictures';
$string['fetch_missing'] = 'Fetch all missing profile pictures';

// Reprocess Help.
$string['pluginname_help'] = '
Reprocess Your Profile Picture

Pressing Reprocess under the My Profile Picture block requests your latest photo from the Tiger Card office.

You can update your Moodle photo via your Moodle user profile.';
