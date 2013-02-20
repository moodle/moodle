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
 * This file contains classes for report_performance
 *
 * @package   report_performance
 * @copyright 2013 Rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Class defining issue result.
 *
 * @package   report_performance
 * @copyright 2013 Rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_performance_issue {
    /** @var string issue identifier */
    public $issue;
    /** @var string issue name */
    public $name;
    /** @var string shown as status */
    public $statusstr;
    /** @var string string defines issue status */
    public $status;
    /** @var string shown as comment */
    public $comment;
    /** @var string details aboout issue*/
    public $details;
    /** @var string link pointing to configuration */
    public $configlink;
}

/**
 * This contains functions to get list of issues and there results.
 *
 * @package   report_performance
 * @copyright 2013 Rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_performance {
    /**
     * This is used when issue is ok and there is no impact on performance.
     */
    const REPORT_PERFORMANCE_OK = 'ok';

    /**
     * This is used to notify that issue might impact performance.
     */
    const REPORT_PERFORMANCE_WARNING = 'warning';

    /**
     * This is used to notify if issue is serious and will impact performance.
     */
    const REPORT_PERFORMANCE_SERIOUS = 'serious';

    /**
     * This is used to notify if issue is critical and will significantly impact performance.
     */
    const REPORT_PERFORMANCE_CRITICAL = 'critical';

    /**
     * Return list of performance check function list.
     *
     * @return array list of performance issues.
     */
    public function get_issue_list() {
        return array(
            'report_performance_check_themedesignermode',
            'report_performance_check_cachejs',
            'report_performance_check_debugmsg',
            'report_performance_check_automatic_backup',
            'report_performance_check_enablestats'
        );
    }

    /**
     * Returns document link for performance issue
     *
     * @param string $issue string describing issue
     * @param string $name name of issue
     * @return string issue link pointing to docs page.
     */
    public function doc_link($issue, $name) {
        global $CFG, $OUTPUT;

        if (empty($CFG->docroot)) {
            return $name;
        }

        return $OUTPUT->doc_link('report/performance/'.$issue, $name);
    }

    /**
     * Helper function to add issue details to table.
     *
     * @param html_table $table table in which issue details should be added
     * @param report_performance_issues $issueresult issue result to be added
     * @param bool $detail true if issue if displayed in detail.
     */
    public function add_issue_to_table(&$table, $issueresult, $detailed = false) {
        global $OUTPUT;
        $statusarr = array(self::REPORT_PERFORMANCE_OK => 'statusok',
                        self::REPORT_PERFORMANCE_WARNING => 'statuswarning',
                        self::REPORT_PERFORMANCE_SERIOUS => 'statusserious',
                        self::REPORT_PERFORMANCE_CRITICAL => 'statuscritical');

        $row = array();
        if ($detailed) {
            $row[0] = $this->doc_link($issueresult->issue, $issueresult->name);
        } else {
            $url = new moodle_url('/report/performance/index.php', array('issue' => $issueresult->issue));
            $row[0] = html_writer::link($url, $issueresult->name);
        }
        $row[1] = html_writer::tag('span', $issueresult->statusstr, array('class' => $statusarr[$issueresult->status]));
        $row[2] = $issueresult->comment;
        if (!empty($issueresult->configlink)) {
            $editicon = html_writer::empty_tag('img', array('alt' => $issueresult->issue, 'class' => 'icon',
                'src' => $OUTPUT->pix_url('i/settings')));
            $row[3] = $OUTPUT->action_link($issueresult->configlink, $editicon);
        } else {
            $row[3] = '';
        }

        $table->data[] = $row;
    }

    /**
     * Verifies if theme designer mode is enabled.
     *
     * @return report_performance_issue result of themedesigner issue.
     */
    public static function report_performance_check_themedesignermode() {
        global $CFG;
        $issueresult = new report_performance_issue();
        $issueresult->issue = 'report_performance_check_themedesignermode';
        $issueresult->name = get_string('themedesignermode', 'admin');

        if (empty($CFG->themedesignermode)) {
            $issueresult->statusstr = get_string('disabled', 'report_performance');
            $issueresult->status = self::REPORT_PERFORMANCE_OK;
            $issueresult->comment = get_string('check_themedesignermode_comment_disable', 'report_performance');
        } else {
            $issueresult->statusstr = get_string('enabled', 'report_performance');
            $issueresult->status = self::REPORT_PERFORMANCE_CRITICAL;
            $issueresult->comment = get_string('check_themedesignermode_comment_enable', 'report_performance');
        }

        $issueresult->details = get_string('check_themedesignermode_details', 'report_performance');
        $issueresult->configlink = new moodle_url('/admin/search.php', array('query' => 'themedesignermode'));
        return $issueresult;
    }

    /**
     * Checks if javascript is cached.
     *
     * @return report_performance_issue result of cachejs issue.
     */
    public static function report_performance_check_cachejs() {
        global $CFG;
        $issueresult = new report_performance_issue();
        $issueresult->issue = 'report_performance_check_cachejs';
        $issueresult->name = get_string('cachejs', 'admin');

        if (empty($CFG->cachejs)) {
            $issueresult->statusstr = get_string('disabled', 'report_performance');
            $issueresult->status = self::REPORT_PERFORMANCE_CRITICAL;
            $issueresult->comment = get_string('check_cachejs_comment_disable', 'report_performance');
        } else {
            $issueresult->statusstr = get_string('enabled', 'report_performance');
            $issueresult->status = self::REPORT_PERFORMANCE_OK;
            $issueresult->comment = get_string('check_cachejs_comment_enable', 'report_performance');
        }

        $issueresult->details = get_string('check_cachejs_details', 'report_performance');
        $issueresult->configlink = new moodle_url('/admin/search.php', array('query' => 'cachejs'));
        return $issueresult;
    }

    /**
     * Checks debug config.
     *
     * @return report_performance_issue result of debugmsg issue.
     */
    public static function report_performance_check_debugmsg() {
        global $CFG;
        $issueresult = new report_performance_issue();
        $issueresult->issue = 'report_performance_check_debugmsg';
        $issueresult->name = get_string('debug', 'admin');
        $debugchoices = array(DEBUG_NONE  => 'debugnone',
                            DEBUG_MINIMAL => 'debugminimal',
                            DEBUG_NORMAL => 'debugnormal',
                            DEBUG_ALL => 'debugall',
                            DEBUG_DEVELOPER => 'debugdeveloper');
        // If debug is not set then consider it as 0.
        if (!isset($CFG->themedesignermode)) {
            $CFG->debug = DEBUG_NONE;
        }

        $issueresult->statusstr = get_string($debugchoices[$CFG->debug], 'admin');
        if ($CFG->debug != DEBUG_DEVELOPER) {
            $issueresult->status = self::REPORT_PERFORMANCE_OK;
            $issueresult->comment = get_string('check_debugmsg_comment_nodeveloper', 'report_performance');
        } else {
            $issueresult->status = self::REPORT_PERFORMANCE_WARNING;
            $issueresult->comment = get_string('check_debugmsg_comment_developer', 'report_performance');
        }

        $issueresult->details = get_string('check_debugmsg_details', 'report_performance');

        $issueresult->configlink = new moodle_url('/admin/settings.php', array('section' => 'debugging'));
        return $issueresult;
    }

    /**
     * Checks automatic backup config.
     *
     * @return report_performance_issue result of automatic backup issue.
     */
    public static function report_performance_check_automatic_backup() {
        global $CFG;
        $issueresult = new report_performance_issue();
        $issueresult->issue = 'report_performance_check_automatic_backup';
        $issueresult->name = get_string('check_backup', 'report_performance');

        if (!empty($CFG->backup_auto_active) && ($CFG->backup_auto_active == 1)) {
            $issueresult->statusstr = get_string('autoactiveenabled', 'backup');
            $issueresult->status = self::REPORT_PERFORMANCE_WARNING;
            $issueresult->comment = get_string('check_backup_comment_enable', 'report_performance');
        } else {
            if (empty($CFG->backup_auto_active)) {
                $issueresult->statusstr = get_string('autoactivedisabled', 'backup');
            } else {
                $issueresult->statusstr = get_string('autoactivemanual', 'backup');
            }
            $issueresult->status = self::REPORT_PERFORMANCE_OK;
            $issueresult->comment = get_string('check_backup_comment_disable', 'report_performance');
        }

        $issueresult->details = get_string('check_backup_details', 'report_performance');
        $issueresult->configlink = new moodle_url('/admin/search.php', array('query' => 'backup_auto_active'));
        return $issueresult;
    }

    /**
     * Checks if stats are enabled.
     */
    public static function report_performance_check_enablestats() {
        global $CFG;
        $issueresult = new report_performance_issue();
        $issueresult->issue = 'report_performance_check_enablestats';
        $issueresult->name = get_string('enablestats', 'admin');

        if (!empty($CFG->enablestats)) {
            $issueresult->statusstr = get_string('enabled', 'report_performance');
            $issueresult->status = self::REPORT_PERFORMANCE_WARNING;
            $issueresult->comment = get_string('check_enablestats_comment_enable', 'report_performance');
        } else {
            $issueresult->statusstr = get_string('disabled', 'report_performance');
            $issueresult->status = self::REPORT_PERFORMANCE_OK;
            $issueresult->comment = get_string('check_enablestats_comment_disable', 'report_performance');
        }

        $issueresult->details = get_string('check_enablestats_details', 'report_performance');
        $issueresult->configlink = new moodle_url('/admin/search.php', array('query' => 'enablestats'));
        return $issueresult;
    }
}
