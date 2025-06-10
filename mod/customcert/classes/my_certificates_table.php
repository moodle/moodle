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
 * The report that displays the certificates the user has throughout the site.
 *
 * @package    mod_customcert
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class for the report that displays the certificates the user has throughout the site.
 *
 * @package    mod_customcert
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class my_certificates_table extends \table_sql {

    /**
     * @var int $userid The user id
     */
    protected $userid;

    /**
     * Sets up the table.
     *
     * @param int $userid
     * @param string|null $download The file type, null if we are not downloading
     */
    public function __construct($userid, $download = null) {
        parent::__construct('mod_customcert_report_table');

        $columns = [
            'name',
            'coursename',
            'timecreated',
            'code',
        ];
        $headers = [
            get_string('name'),
            get_string('course'),
            get_string('receiveddate', 'customcert'),
            get_string('code', 'customcert'),
        ];

        // Check if we were passed a filename, which means we want to download it.
        if ($download) {
            $this->is_downloading($download, 'customcert-report');
        }

        if (!$this->is_downloading()) {
            $columns[] = 'download';
            $headers[] = get_string('file');
        }

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->collapsible(false);
        $this->sortable(true);
        $this->no_sorting('code');
        $this->no_sorting('download');
        $this->is_downloadable(true);

        $this->userid = $userid;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_name($certificate) {
        $cm = get_coursemodule_from_instance('customcert', $certificate->id);
        $context = \context_module::instance($cm->id);

        return format_string($certificate->name, true, ['context' => $context]);
    }

    /**
     * Generate the course name column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_coursename($certificate) {
        $cm = get_coursemodule_from_instance('customcert', $certificate->id);
        $context = \context_module::instance($cm->id);

        return format_string($certificate->coursename, true, ['context' => $context]);
    }

    /**
     * Generate the certificate time created column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_timecreated($certificate) {
        return userdate($certificate->timecreated);
    }

    /**
     * Generate the code column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_code($certificate) {
        return $certificate->code;
    }

    /**
     * Generate the download column.
     *
     * @param \stdClass $certificate
     * @return string
     */
    public function col_download($certificate) {
        global $OUTPUT;

        $icon = new \pix_icon('download', get_string('download'), 'customcert');
        $link = new \moodle_url('/mod/customcert/my_certificates.php',
            ['userid' => $this->userid,
                  'certificateid' => $certificate->id,
                  'downloadcert' => '1']);

        return $OUTPUT->action_link($link, '', null, null, $icon);
    }

    /**
     * Query the reader.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $total = certificate::get_number_of_certificates_for_user($this->userid);

        $this->pagesize($pagesize, $total);

        $this->rawdata = certificate::get_certificates_for_user($this->userid, $this->get_page_start(),
            $this->get_page_size(), $this->get_sql_sort());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Download the data.
     */
    public function download() {
        \core\session\manager::write_close();
        $total = certificate::get_number_of_certificates_for_user($this->userid);
        $this->out($total, false);
        exit;
    }
}

