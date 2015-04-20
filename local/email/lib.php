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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib/vars.php');
require_once('lib/api.php');

function email_cron() {
    global $DB;

    // Delete emails older than 6 months to prevent the email table from clogging up the database.
    $halfyearagoish = time() - 6 * 30 * 24 * 60 * 60;
    $DB->delete_records_select('email', "modifiedtime < $halfyearagoish");

    // Send emails.
    if ($emails = $DB->get_records('email', array('sent' => null), null, '*')) {
        foreach ($emails as $email) {
            EmailTemplate::send_to_user($email);
            // Adding a sleep to ensure there is no processing confusion.
            sleep(10);
    
            $email->modifiedtime = $email->sent = time();
            $email->id = $email->id;
            $DB->update_record('email', $email);
        }
    }
}
