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
 * Privacy implementation
 *
 * @package    local_adminer
 * @author Andreas Grabs <moodle@grabs-edv.de>
 * @copyright  Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_adminer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * The local plugin adminer does not store any data.
 *
 * @copyright  2018 Andreas Grabs <moodle@grabs-edv.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class util {

    /** The string for $CFG->local_adminer_secret which mean "disabled" */
    public const DISABLED_SECRET = '!!!';

    /**
     * Get the adminer url with the current driver
     *
     * @return \moodle_url
     */
    public static function get_adminer_url() {
        global $CFG;

        $myconfig = get_config('local_adminer');

        switch ($CFG->dbtype) {
            case 'pgsql':
                $adminerdriver = 'pgsql';
                break;
            case 'sqlsrv':
            case 'mssql':
                $adminerdriver = 'mssql';
                break;
            case 'oci':
                $adminerdriver = 'oracle';
                break;
            default:
                $adminerdriver = 'server'; // This is for mysql.
                break;
        }

        $urloptions = [$adminerdriver => '', 'username' => ''];
        if (!empty($myconfig->startwithdb)) {
            $urloptions['db'] = $CFG->dbname;
        }

        return new \moodle_url('/local/adminer/lib/run_adminer.php', $urloptions);
    }

    /**
     * Check whether or not an additional secret is defined and stops if needed.
     *
     * @return bool It only returns true or it stops with a small moodleform.
     */
    public static function check_adminer_secret() {
        global $CFG, $SESSION, $OUTPUT, $PAGE, $FULLME;

        $adminersecret = $CFG->local_adminer_secret ?? '';
        if (empty($adminersecret)) {
            unset($SESSION->local_adminer_secret);
            return true;
        }

        $PAGE->set_context(\context_system::instance());
        $PAGE->set_url(new \moodle_url($FULLME));
        $PAGE->set_pagelayout('popup');

        // Check whether adminer is fully disabled.
        static::handle_disabled();

        $loadedsecret = $SESSION->local_adminer_secret ?? '';
        if ($loadedsecret !== $adminersecret) {
            $secretform = new \local_adminer\secret_form();
            if ($data = $secretform->get_data()) {
                if ($data->adminersecret === $adminersecret) {
                    $SESSION->local_adminer_secret = $data->adminersecret;
                    redirect(static::get_adminer_url());
                }
                \core\notification::error(get_string('wrong_adminer_secret', 'local_adminer'));
            }
        }

        $loadedsecret = $SESSION->local_adminer_secret ?? '';
        if ($loadedsecret !== $adminersecret) {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('pluginname', 'local_adminer'));
            $secretform->display();
            echo $OUTPUT->footer();
            die;
        }

        return true;
    }

    /**
     * If the special secret static::DISABLED_SECRET is defined, we print a suitable message and stop.
     *
     * @return void
     */
    public static function handle_disabled() {
        global $CFG, $OUTPUT;

        $adminersecret = $CFG->local_adminer_secret ?? '';

        // Check whether adminer is fully disabled.
        if ($adminersecret === static::DISABLED_SECRET) {
            \core\notification::error(get_string('adminer_is_disabled_by_admin', 'local_adminer'));
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('error'));
            echo $OUTPUT->footer();
            die;
        }
    }
}
