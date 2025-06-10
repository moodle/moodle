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
 * Local Reportx
 *
 * @package   local_rpx
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot."/local/rpx/reports.php");
class report_coursesize_report extends local_rpx_report {

    public $plugintype;
    public $pluginname;

    /**
     * The entry point for the local plugin to get chart data.
     * @return array The data to display
     */
    // public function getChartHookData() {
    //  return $this->getChartData();
    // }

    /**
     * The entry point for the local plugin to get reporting data.
     * @param array params - variables passed in via ajax.
     * @return array The data to display.
     */
    public function getReportHookData($params = array()) {
        error_log("\n\n---------------------------------------------------------\n");
        error_log("\n getReportHookData() ----------->>> START <<<----------- \n");
        $this->plugintype = isset($params->pfname) ? $params->pfname : null;
        $this->pluginname = isset($params->pname) ? $params->pname : null;

        // the ReportHooks page already knows the folder and plugin name.
        // Pass the data to display.
        try {

            // The charts will be called via AJAX after initial page load.
            // Get the count to generate the number of canvas placeholders.
            $data = array(
                "smallcards" => $this->getSmallCardData(),
                "cards" => $this->getCardData(),
                "charts" => $this->getChartData(),
                "tables" => $this->getTableData(),
                "pfname" => $this->plugintype,
                "pname" => $this->pluginname
            );

        } catch (Exception $e) {
            $data = array(
                "smallcards" => array(),
                "cards" => array(),
                "charts" => array(),
                "tables" => array(),
                "pfname" => $this->plugintype,
                "pname" => $this->pluginname
            );
        }
        return $data;
    }

    /**
     * Show card stat info. 
     * @return array list of various stat cards.
     */
    public function getCardData() {
        error_log("\n\n ---------------------------------------------------------\n");
        error_log("\n getCardData() ----------->>> START <<<----------- \n");
        $final_set = array();            
        $final_set[] = $this->getTotalCourseCount();
        $final_set[] = $this->getTotalCourseSize();
        $final_set[] = $this->getTotalBackupSize();
        $final_set[] = $this->getBackadelSize();
        $final_set[] = $this->getFileDirSize();
            
        error_log("\n getCardData() ----------->>> END <<<----------- \n");
        return $final_set;
    }


    /**
     * Show small card stat info. 
     * @return array list of various small stat cards.
     */
    public function getSmallCardData() {
        
        return array();

        $min = 1; 
        $max = 999999;
    
        $rand_colors = array(
            "stat_blue",
            "stat_green",
            "stat_yellow",
            "stat_orange",
            "stat_salmon",
            "stat_danger",
            "stat_light_pink",
            "stat_purple",
            "stat_teal",
            "stat_charcoal",
            "stat_light_blue"
        );
        
        $data_set = array();
        for ($i = 0; $i < 5; $i++) {
            $data_set[] = array(
                "stat_name" => "mya_small_stat_". $i,
                "stat_title" => "Example ".$i.":",
                "stat_data" => rand($min,$max),
                "stat_color" => $rand_colors[$i]
            );
        }

        return $data_set;
    }

    
    public function getChartData() {
        error_log("\n\n ---------------------------------------------------------\n");
        error_log("\n getChartData() ----------->>> START <<<----------- \n");
        $charts = array();

        // $charts[] = array(
        //     "chartdata" => $this->getChart1(),
        //     "uniqid" => $this->plugintype."_".$this->pluginname."_1",
        //     "withtable" => "true"
        // );
        $charts[] = array(
            "chartdata" => $this->getChart2(),
            "uniqid" => $this->plugintype."_".$this->pluginname."_2",
            "withtable" => "true"
        );

        /*
        global $OUTPUT, $PAGE;
        $context = \context_system::instance();
        $PAGE->set_context($context);
        $chart = new \core\chart_bar(); // Create a bar chart instance.
        $series1 = new \core\chart_series('Series 1 (Bar)', [1000, 1170, 660, 1030]);
        $series2 = new \core\chart_series('Series 2 (Line)', [400, 460, 1120, 540]);
        $series2->set_type(\core\chart_series::TYPE_LINE); // Set the series type to line chart.
        $chart->add_series($series2);
        $chart->add_series($series1);
        $chart->set_labels(['2004', '2005', '2006', '2007']);
        $charty = $OUTPUT->render($chart);

        error_log("\n\nWhat is charty farty: ". $charty);
        return $charty;
        */
        
        

        // return array(json_encode($dataset));
        error_log("\n getChartData() ----------->>> END <<<----------- \n");
        return $charts;
    }

    public function getTableData() {
        error_log("\n\n ---------------------------------------------------------\n");
        error_log("\n getTableData() ----------->>> START <<<----------- \n");
        // $result = $this->QR->queryGuildMappings();
        global $DB, $CFG;
        // This (get_records_sql) returns an array of
        $result = $DB->get_records_sql(
            'SELECT *
            FROM mdl_user
            LIMIT 20'
        );
        
        error_log("\ngetTableData() -> What is result: ". print_r($result, true). "\n");
        $tablelist = array();
        $headers = array(
            array("header" => "First Name"),
            array("header" => "Last Name"),
            array("header" => "Username"),
            array("header" => "Email")
        );

        $table_header_count = count($headers);
        // Now build the student list.
        foreach ($result as $student) {
            $tablelist[] = array(
                "table_row" => array(
                    array("cell_data" => $student->firstname),
                    array("cell_data" => $student->lastname),
                    array("cell_data" => $student->username),
                    array("cell_data" => $student->email)
                )
            );
        }

        // error_log("\ngetGuildMappings() -> What is encoded: ". $encoded. "\n");
        // error_log("\ngetUsersOnlineToday() -> What is is beginOfDay: ". $beginOfDay. "\n");
        // error_log("\ngetUsersOnlineToday() -> What is is endOfDay: ". $endOfDay. "\n");
        // error_log("\getUsersOnlineToday() -> What is is time array: ". print_r($time_values, true). "\n");
        error_log("\n---------------------------------------------------------\n\n");
        $tables = array();
        $tables[] = array(
            "table_headers" => $headers,
            "table_header_count" => $table_header_count,
            "table_data" => $tablelist
        );
        error_log("\n getTableData() ----------->>> END <<<----------- \n");
        return $tables;
    }


    public function getDirectorySize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if ($path !== false && $path != '' && file_exists($path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }

    public function getTotalCourseCount() {

        global $DB, $CFG;

        $coursecount = $DB->count_records_sql(
            'SELECT COUNT(*)
            FROM mdl_course'
        );

        return array(
            "stat_name" => "xrep_course_count",
            "stat_title" => "Number of Courses",
            "stat_data" => $coursecount,
            "stat_color" => "stat_green"
        );
    }
    public function getTotalCourseSize() {
        global $DB, $CFG;

        $coursesize = $DB->get_records_sql(
            "SELECT c.id, c.shortname, c.category, ca.name, rc.filesize
            FROM mdl_course c
            JOIN (
                SELECT id AS course, SUM(filesize) AS filesize
                    FROM (
                        SELECT c.id, f.filesize
                        FROM mdl_course c
                        JOIN mdl_context cx ON cx.contextlevel = 50 AND cx.instanceid = c.id
                        JOIN mdl_files f ON f.contextid = cx.id

                        UNION ALL
                    
                        SELECT c.id, f.filesize
                        FROM mdl_block_instances bi
                        JOIN mdl_context cx1 ON cx1.contextlevel = 80 AND cx1.instanceid = bi.id
                        JOIN mdl_context cx2 ON cx2.contextlevel = 50 AND cx2.id = bi.parentcontextid
                        JOIN mdl_course c ON c.id = cx2.instanceid
                        JOIN mdl_files f ON f.contextid = cx1.id
                    
                        UNION ALL

                        SELECT c.id, f.filesize
                        FROM mdl_course_modules cm
                        JOIN mdl_context cx ON cx.contextlevel = 70 AND cx.instanceid = cm.id
                        JOIN mdl_course c ON c.id = cm.course
                        JOIN mdl_files f ON f.contextid = cx.id
                    ) x
                    GROUP BY id
            ) rc on rc.course = c.id JOIN mdl_course_categories ca on c.category = ca.id"
        );
        $totalsize = 0;
        foreach ($coursesize as $cc) {
            $totalsize += $cc->filesize;
        }
        return array(
            "stat_name" => "xrep_course_size",
            "stat_title" => "Total Courses Size",
            "stat_data" => $this->formatBytes($totalsize),
            "stat_color" => "stat_purple"
        );
    }

    public function getTotalBackupSize() {
        global $DB, $CFG;

        $backupsize = $DB->get_record_sql(
            "SELECT id AS course, SUM(filesize) AS filesize
            FROM (
                SELECT c.id, f.filesize
                FROM mdl_course c
                JOIN mdl_context cx ON cx.contextlevel = 50 AND cx.instanceid = c.id
                JOIN mdl_files f ON f.contextid = cx.id AND f.component = 'backup') x
            GROUP BY id"
        );

        error_log("What is the backupsize: ". $backupsize->filesize);
        return array(
            "stat_name" => "xrep_course_backupsize",
            "stat_title" =>"Total Course Backup Size",
            "stat_data" => $this->formatBytes($backupsize->filesize),
            "stat_color" => "stat_teal"
        );
    }

    public function getFileDirSize() {
        global $CFG;
        $dir = $CFG->dataroot;
        error_log("\n\n What is the filedir: ". $dir);
        $bytes = $this->getDirectorySize($dir);
        return array(
            "stat_name" => "xrep_filedir_size",
            "stat_title" =>"Moodle Data Size",
            "stat_data" => $this->formatBytes($bytes),
            "stat_color" => "stat_danger"
        );
    }
    public function getBackadelSize() {
        global $CFG;
        $dir = $CFG->dataroot.'/backadel/';
        $bytes = $this->getDirectorySize($dir);
        return array(
            "stat_name" => "xrep_backadel_size",
            "stat_title" =>"Semester Backups Size",
            "stat_data" => $this->formatBytes($bytes),
            "stat_color" => "stat_orange"
        );
    }

    public function getChart1() {
        // Get your data however you'd like and build based on chartjs data structure.
        $example1 = [
            "type" => "line",
            "series" => [
                [
                    "label" => "Sales",
                    "labels" => null,
                    "type" => null,
                    "values" => [1000,1170,660,1030],
                    "colors" => [],
                    "axes" => [
                        "x" => null,
                        "y" => null
                    ],
                    "smooth" => null
                ], [
                    "label" => "Expenses",
                    "labels" => null,
                    "type" => null,
                    "values" => [400,460,1120,540],
                    "colors" => [],
                    "axes" => [
                        "x" => null,
                        "y" => null
                    ],
                    "smooth" => null
                ],
            ],
            "labels" => ["2004","2005","2006","2007"],
            "title" => "SMOOTH LINES CHART",
            "axes" => [
                "x" => [],
                "y" => []
            ],
            "config_colorset" => null,
            "smooth" => true
        ];
        // $encoded = json_encode($example1);
        // return $encoded;
        return $example1;
    }
    
    private function getChart2() {
        $example1 = [
            "type" => "line",
            "series" => [
                [
                    "label" => "Sales",
                    "labels" => null,
                    "type" => null,
                    "values" => [1000,1170,660,1030],
                    "colors" => [],
                    "axes" => [
                        "x" => null,
                        "y" => null
                    ],
                    "smooth" => null
                ], [
                    "label" => "Expenses",
                    "labels" => null,
                    "type" => null,
                    "values" => [400,460,1120,540],
                    "colors" => [],
                    "axes" => [
                        "x" => null,
                        "y" => null
                    ],
                    "smooth" => null
                ],
            ],
            "labels" => ["2004","2005","2006","2007"],
            "title" => "xReport Example",
            "axes" => [
                "x" => [],
                "y" => []
            ],
            "config_colorset" => null,
            "smooth" => true
        ];
        $encoded = json_encode($example1);
        return $encoded;
    }
    
    private function getChart3() {
        return array();
    }
}