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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/lib/evalmath/evalmath.class.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class report_base
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class report_base {

    /**
     * @var int
     */
    public int $id = 0;

    /**
     * @var array
     */
    public array $components = [];

    /**
     * @var object
     */
    public $finalreport;

    /**
     * @var int
     */
    public int $totalrecords = 0;

    /**
     * @var object|null
     */
    public ?object $currentuser;

    /**
     * @var int
     */
    public int $currentcourse = 0;

    /**
     * @var int
     */
    public int $starttime = 0;

    /**
     * @var int
     */
    public int $endtime = 0;

    /**
     * @var string
     */
    public string $sql = '';

    /**
     * @var null
     */
    public $filterform = null;

    /**
     * @var int
     */
    private int $currentcourseid = 0;

    /**
     * @var false|mixed|stdClass
     */
    public ?object $config;

    /**
     * reports_base
     *
     * @param object|int $report
     * @return void
     */
    public function reports_base($report): void {
        global $DB, $CFG, $USER, $remotedb;

        if (is_numeric($report)) {
            $this->config = $DB->get_record('block_configurable_reports', ['id' => $report]);
        } else {
            $this->config = $report;
        }

        $this->currentuser = $USER;
        $this->currentcourseid = $this->config->courseid;
        $this->init();

        // Use a custom $DB (and not current system's $DB)
        // TODO: major security issue.
        $remotedbhost = get_config('block_configurable_reports', 'dbhost');
        $remotedbname = get_config('block_configurable_reports', 'dbname');
        $remotedbuser = get_config('block_configurable_reports', 'dbuser');
        $remotedbpass = get_config('block_configurable_reports', 'dbpass');

        if (!empty($remotedbhost) && !empty($remotedbname) && !empty($remotedbuser) && !empty($remotedbpass) &&
            $this->config->remote) {
            $dbclass = get_class($DB);
            $remotedb = new $dbclass();
            $remotedb->connect($remotedbhost, $remotedbuser, $remotedbpass, $remotedbname, $CFG->prefix);
        } else {
            $remotedb = $DB;
        }

    }

    /**
     * __construct
     *
     * @param object|int $report
     */
    public function __construct($report) {
        $this->reports_base($report);
    }

    /**
     * Check permissions
     *
     * @param int $userid
     * @param context $context
     * @return bool|mixed|null
     */
    public function check_permissions(int $userid, context $context) {
        global $CFG;

        if (has_capability('block/configurable_reports:manageownreports', $context, $userid) && $this->config->ownerid == $userid) {
            return true;
        }

        if (has_capability('block/configurable_reports:managereports', $context, $userid)) {
            return true;
        }

        if (empty($this->config->visible)) {
            return false;
        }

        $components = cr_unserialize($this->config->components);
        $permissions = $components['permissions'] ?? [];

        if (empty($permissions['elements'])) {
            return has_capability('block/configurable_reports:viewreports', $context);
        }

        $i = 1;
        $cond = [];
        foreach ($permissions['elements'] as $p) {

            require_once($CFG->dirroot . '/blocks/configurable_reports/components/permissions/' . $p['pluginname'] .
                '/plugin.class.php');
            $classname = 'plugin_' . $p['pluginname'];
            $class = new $classname($this->config);
            $cond[$i] = $class->execute($userid, $context, $p['formdata']);
            $i++;
        }

        if (count($cond) === 1) {
            return $cond[1];
        }

        $m = new EvalMath;
        $orig = $dest = [];

        if (isset($permissions['config']->conditionexpr)) {
            $logic = trim($permissions['config']->conditionexpr);
            // Security
            // No more than: conditions * 10 chars.
            $logic = substr($logic, 0, count($permissions['elements']) * 10);
            $logic = str_replace(['and', 'or'], ['&&', '||'], strtolower($logic));
            // More Security Only allowed chars.
            $logic = preg_replace('/[^&c\d\s|()]/i', '', $logic);
            $logic = str_replace(['&&', '||'], ['*', '+'], $logic);

            for ($j = $i - 1; $j > 0; $j--) {
                $orig[] = 'c' . $j;
                $dest[] = ($cond[$j]) ? 1 : 0;
            }

            return $m->evaluate(str_replace($orig, $dest, $logic));
        }

        return false;
    }

    /**
     * add_filter_elements
     *
     * @param MoodleQuickForm $mform
     * @return void
     */
    public function add_filter_elements(MoodleQuickForm $mform): void {
        global $CFG;

        $components = cr_unserialize($this->config->components);
        $filters = $components['filters']['elements'] ?? [];

        require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');
        foreach ($filters as $f) {

            if (is_array($f['pluginname'])) {
                $f['pluginname'] = $f['pluginname'][0];
            }

            $filename = clean_filename($f['pluginname']);
            require_once($CFG->dirroot . '/blocks/configurable_reports/components/filters/' . $filename . '/plugin.class.php');
            $classname = 'plugin_' . $filename;
            $class = new $classname($this->config);

            $finalelements = $class->print_filter($mform, $f['formdata']);

        }
    }

    /**
     * check_filters_request
     *
     * @return void
     */
    public function check_filters_request(): void {

        $components = cr_unserialize($this->config->components);
        $filters = $components['filters']['elements'] ?? [];

        if (!empty($filters)) {

            $formdata = new stdclass;
            $request = array_merge($_POST, $_GET);
            if ($request) {
                foreach ($request as $key => $val) {
                    if (strpos($key, 'filter_') !== false) {
                        $key = clean_param($key, PARAM_CLEANHTML);
                        if (is_array($val)) {
                            $val = clean_param_array($val, PARAM_CLEANHTML);
                        } else {
                            $val = clean_param($val, PARAM_CLEANHTML);
                        }
                        $formdata->{$key} = $val;
                    }
                }
            }

            require_once('filter_form.php');
            $filterform = new report_edit_form(null, $this);

            $filterform->set_data($formdata);

            if ($filterform->is_cancelled()) {
                $params = ['id' => $this->config->id, 'courseid' => $this->config->courseid];
                redirect(new moodle_url('/blocks/configurable_reports/viewreport.php', $params));
                die;
            }
            $this->filterform = $filterform;
        }
    }

    /**
     * print_filters
     *
     * @return void
     */
    public function print_filters(): void {
        if ($this->filterform !== null) {
            $this->filterform->display();
        }
    }

    /**
     * print_graphs
     *
     * @param bool $return
     * @return string|true
     */
    public function print_graphs(bool $return = false) {
        $output = '';
        $graphs = $this->get_graphs($this->finalreport->table->data);

        if ($graphs) {
            foreach ($graphs as $g) {
                $output .= '<div class="centerpara">';
                $output .= ' <img src="' . $g . '" alt="' . s($this->config->name) . '"><br />';
                $output .= '</div>';
            }
        }
        if ($return) {
            return $output;
        }

        echo $output;

        return true;
    }

    /**
     * print_export_options
     *
     * @param bool $return
     * @return string|true
     */
    public function print_export_options(bool $return = false) {
        global $CFG;

        $wwwpath = $CFG->wwwroot;

        // TODO move to more Moodle approach.
        $request = array_merge($_POST, $_GET);

        if ($request) {
            $id = clean_param($request['id'], PARAM_INT);
            $wwwpath = 'viewreport.php?id=' . $id;
            unset($request['id']);

            foreach ($request as $key => $val) {

                $key = s(clean_param($key, PARAM_CLEANHTML));

                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $k = s(clean_param($k, PARAM_CLEANHTML));
                        $v = s(clean_param($v, PARAM_CLEANHTML));
                        $wwwpath .= "&{$key}[$k]=" . $v;
                    }
                } else {
                    $val = clean_param($val, PARAM_CLEANHTML);
                    $wwwpath .= "&$key=" . s($val);
                }
            }
        }

        $output = '';
        $export = explode(',', $this->config->export);

        if (!empty($this->config->export)) {
            $output .= '<br /><div class="centerpara">';
            $output .= get_string('downloadreport', 'block_configurable_reports') . ': ';

            foreach ($export as $e) {

                if (empty($e)) {
                    continue;
                }

                // TODO Use moodle_url.
                $output .= '<a href="' . s($wwwpath) . '&download=1&format=' . s($e) . '">
                                    <img src="' . $CFG->wwwroot . '/blocks/configurable_reports/export/' . s($e) . '/pix.gif"
                                     alt="' . s($e) . '">
                                    &nbsp;' . (s(strtoupper($e))) .
                    '</a>&nbsp;';
            }
            $output .= '</div>';
        }

        if ($return) {
            return $output;
        }

        echo $output;

        return true;
    }

    /**
     * Update conditions
     *
     * @param array $data
     * @param string $logic
     * @return bool|mixed|null
     */
    public function evaluate_conditions(array $data, string $logic) {
        global $CFG;

        require_once($CFG->dirroot . '/blocks/configurable_reports/reports/evalwise.class.php');

        $logic = strtolower(trim($logic));
        $logic = substr($logic, 0, count($data) * 10);
        $logic = str_replace(['or', 'and', 'not'], ['+', '*', '-'], $logic);
        $logic = preg_replace('/[^\*c\d\s\+\-()]/i', '', $logic);

        $orig = $dest = [];
        for ($j = count($data); $j > 0; $j--) {
            $orig[] = 'c' . $j;
            $dest[] = $j;
        }
        $logic = str_replace($orig, $dest, $logic);

        $m = new EvalWise();
        $m->set_data($data);

        return $m->evaluate($logic);
    }

    /**
     * get_graphs
     *
     * @param array $finalreport
     * @return array
     */
    public function get_graphs($finalreport): array {
        global $CFG;

        $components = cr_unserialize($this->config->components);
        $graphs = $components['plot']['elements'] ?? [];

        $reportgraphs = [];

        if (!empty($graphs)) {
            $series = [];

            foreach ($graphs as $g) {
                require_once($CFG->dirroot . '/blocks/configurable_reports/components/plot/' . $g['pluginname'] .
                    '/plugin.class.php');
                $classname = 'plugin_' . $g['pluginname'];
                $class = new $classname($this->config);
                $reportgraphs[] = $class->execute($g['id'], $g['formdata'], $finalreport);
            }
        }

        return $reportgraphs;
    }

    /**
     * get_calcs
     *
     * @param array $finaltable
     * @param array $tablehead
     * @return array
     */
    public function get_calcs(array $finaltable, array $tablehead): array {
        global $CFG;

        $components = cr_unserialize($this->config->components);
        $calcs = $components['calcs']['elements'] ?? [];

        // Calcs doesn't work with multi-rows so far.
        $columnscalcs = [];
        $finalcalcs = [];
        if (!empty($calcs)) {
            foreach ($calcs as $calc) {

                if (!isset($calc['formdata']->column)) {
                    continue;
                }

                $columnscalcs[$calc['formdata']->column] = [];
            }

            $columnstostore = array_keys($columnscalcs);

            foreach ($finaltable as $r) {
                foreach ($columnstostore as $c) {
                    if (isset($r[$c])) {
                        $columnscalcs[$c][] = $r[$c];
                    }
                }
            }

            foreach ($calcs as $calc) {

                if (is_array($calc['pluginname'])) {
                    $calc['pluginname'] = $calc['pluginname'][0];
                }

                $filename = clean_filename($calc['pluginname']);
                require_once($CFG->dirroot . '/blocks/configurable_reports/components/calcs/' . $filename . '/plugin.class.php');
                $classname = 'plugin_' . $filename;

                $class = new $classname($this->config);
                $result = $class->execute($columnscalcs[$calc['formdata']->column]);
                $finalcalcs[$calc['formdata']->column] = $result;
            }

            for ($i = 0, $imax = count($tablehead); $i < $imax; $i++) {
                if (!isset($finalcalcs[$i])) {
                    $finalcalcs[$i] = '';
                }
            }

            ksort($finalcalcs);

        }

        return $finalcalcs;
    }

    /**
     * elements_by_conditions
     *
     * @param array $conditions
     * @return bool|mixed|null
     */
    public function elements_by_conditions($conditions) {
        global $CFG;

        if (empty($conditions['elements'])) {
            return $this->get_all_elements();
        }

        $finalelements = [];
        $i = 1;
        foreach ($conditions['elements'] as $c) {
            require_once($CFG->dirroot . '/blocks/configurable_reports/components/conditions/' . $c['pluginname'] .
                '/plugin.class.php');
            $classname = 'plugin_' . $c['pluginname'];
            $class = new $classname($this->config);
            $elements[$i] = $class->execute($c['formdata'], $this->currentuser, $this->currentcourseid);
            $i++;
        }

        if (count($conditions['elements']) === 1) {
            $finalelements = $elements[1];
        } else {
            $logic = $conditions['config']->conditionexpr;
            $finalelements = $this->evaluate_conditions($elements, $logic);
            if ($finalelements === false) {
                return false;
            }
        }

        return $finalelements;
    }

    /**
     * Returns a report object
     */
    public function create_report(): bool {
        global $CFG;

        // Conditions.
        $components = cr_unserialize($this->config->components);

        $conditions = $components['conditions']['elements'] ?? [];
        $filters = $components['filters']['elements'] ?? [];
        $columns = $components['columns']['elements'] ?? [];
        $ordering = $components['ordering']['elements'] ?? [];

        $finalelements = [];

        if (!empty($conditions)) {
            $finalelements = $this->elements_by_conditions($components['conditions']);
        } else {
            // All elements.
            $finalelements = $this->get_all_elements();
        }

        // Filters.
        if (!empty($filters)) {
            foreach ($filters as $f) {
                require_once($CFG->dirroot . '/blocks/configurable_reports/components/filters/' . $f['pluginname'] .
                    '/plugin.class.php');
                $classname = 'plugin_' . $f['pluginname'];
                $class = new $classname($this->config);
                $finalelements = $class->execute($finalelements, $f['formdata']);
            }
        }

        // Ordering.

        $sqlorder = '';

        $orderingdata = [];
        if (!empty($ordering)) {
            foreach ($ordering as $o) {
                require_once($CFG->dirroot . '/blocks/configurable_reports/components/ordering/' . $o['pluginname'] .
                    '/plugin.class.php');
                $classname = 'plugin_' . $o['pluginname'];
                $classorder = new $classname($this->config);
                $orderingdata = $o['formdata'];
                if ($classorder->sql) {
                    $sqlorder = $classorder->execute($orderingdata);
                }
            }
        }

        // COLUMNS - FIELDS.

        $rows = $this->get_rows($finalelements, $sqlorder);

        if (!$sqlorder && isset($classorder)) {
            $rows = $classorder->execute($rows, $orderingdata);
        }

        $reporttable = [];
        $tablehead = [];
        $tablealign = [];
        $tablesize = [];
        $tablewrap = [];
        $firstrow = true;

        $pluginscache = [];

        if ($rows) {
            foreach ($rows as $r) {

                $tempcols = [];
                foreach ($columns as $c) {
                    if (empty($c)) {
                        continue;
                    }

                    require_once($CFG->dirroot . '/blocks/configurable_reports/components/columns/' . $c['pluginname'] .
                        '/plugin.class.php');
                    $classname = 'plugin_' . $c['pluginname'];

                    if (!isset($pluginscache[$classname])) {
                        $class = new $classname($this->config, $c);
                        $pluginscache[$classname] = $class;
                    } else {
                        $class = $pluginscache[$classname];
                    }

                    $tempcols[] = $class->execute(
                        $c['formdata'],
                        $r,
                        $this->currentuser,
                        $this->currentcourseid,
                        $this->starttime,
                        $this->endtime
                    );

                    if ($firstrow) {
                        $tablehead[] = $class->summary($c['formdata']);
                        [$align, $size, $wrap] = $class->colformat($c['formdata']);
                        $tablealign[] = $align;
                        $tablesize[] = $size;
                        $tablewrap[] = $wrap;
                    }

                }
                $firstrow = false;
                $reporttable[] = $tempcols;
            }
        }

        // EXPAND ROWS.
        $finaltable = [];

        foreach ($reporttable as $row) {
            $col = [];
            $multiple = false;
            $nrows = 0;
            $mrowsi = [];

            foreach ($row as $key => $cell) {
                if (!is_array($cell)) {
                    $col[] = $cell;
                } else {
                    $multiple = true;
                    $nrows = count($cell);
                    $mrowsi[] = $key;
                }
            }
            if ($multiple) {
                $newrows = [];
                for ($i = 0; $i < $nrows; $i++) {
                    $newrows[$i] = $row;
                    foreach ($mrowsi as $index) {
                        $newrows[$i][$index] = $row[$index][$i];
                    }
                }
                foreach ($newrows as $r) {
                    $finaltable[] = $r;
                }
            } else {
                $finaltable[] = $col;
            }
        }

        // CALCS.
        $finalcalcs = $this->get_calcs($finaltable, $tablehead);

        // Make the table, head, columns, etc...

        $table = new stdClass;
        $table->id = 'reporttable';
        $table->data = $finaltable;
        $table->head = $tablehead;
        $table->size = $tablesize;
        $table->align = $tablealign;
        $table->wrap = $tablewrap;
        $table->width = (isset($components['columns']['config'])) ? $components['columns']['config']->tablewidth : '';
        $table->summary = $this->config->summary;
        $table->tablealign = (isset($components['columns']['config'])) ? $components['columns']['config']->tablealign : 'center';
        $table->cellpadding = (isset($components['columns']['config'])) ? $components['columns']['config']->cellpadding : '5';
        $table->cellspacing = (isset($components['columns']['config'])) ? $components['columns']['config']->cellspacing : '1';
        $table->class = (isset($components['columns']['config'])) ? $components['columns']['config']->class : 'generaltable';

        $calcs = new html_table();
        $calcs->data = [$finalcalcs];
        $calcs->head = $tablehead;
        $calcs->size = $tablesize;
        $calcs->align = $tablealign;
        $calcs->wrap = $tablewrap;
        $calcs->summary = $this->config->summary;
        $calcs->attributes['class'] =
            (isset($components['columns']['config'])) ? $components['columns']['config']->class : 'generaltable';

        if (!$this->finalreport) {
            $this->finalreport = new stdClass;
        }
        $this->finalreport->name = $this->config->name;
        $this->finalreport->table = $table;
        $this->finalreport->calcs = $calcs;

        return true;

    }

    /**
     * add_jsordering
     *
     * @param moodle_page $moodlepage
     * @return void
     */
    public function add_jsordering(moodle_page $moodlepage): void {
        switch (get_config('block_configurable_reports', 'reporttableui')) {
            case 'datatables':
                cr_add_jsdatatables('#reporttable', $moodlepage);
                break;
            case 'jquery':
                cr_add_jsordering('#reporttable', $moodlepage);
                echo html_writer::tag(
                    'style',
                    '#page-blocks-configurable_reports-viewreport .generaltable {
                    overflow: auto;
                    width: 100%;
                    display: block;}'
                );
                break;
            case 'html':
                echo html_writer::tag(
                    'style',
                    '#page-blocks-configurable_reports-viewreport .generaltable {
                    overflow: auto;
                    width: 100%;
                    display: block;}'
                );
                break;
            default:
                break;
        }
    }

    /**
     * print_template
     *
     * @param object $config
     * @param moodle_page $moodlepage
     * @return void
     */
    public function print_template($config, moodle_page $moodlepage): void {
        global $OUTPUT;

        $pagecontents = [];
        $pagecontents['header'] = (isset($config->header) && $config->header) ? $config->header : '';
        $pagecontents['footer'] = (isset($config->footer) && $config->footer) ? $config->footer : '';

        $recordtpl = (isset($config->record) && $config->record) ? $config->record : '';

        $calculations = '';

        if (!empty($this->finalreport->calcs->data[0])) {
            $calculations = html_writer::table($this->finalreport->calcs);
        }

        $pagination = '';
        if ($this->config->pagination) {
            $page = optional_param('page', 0, PARAM_INT);
            $postfiltervars = '';
            $request = array_merge($_POST, $_GET);
            if ($request) {
                foreach ($request as $key => $val) {
                    if (strpos($key, 'filter_') !== false) {
                        $key = s(clean_param($key, PARAM_CLEANHTML));
                        if (is_array($val)) {
                            foreach ($val as $k => $v) {
                                $k = s(clean_param($k, PARAM_CLEANHTML));
                                $v = s(clean_param($v, PARAM_CLEANHTML));
                                $postfiltervars .= "&amp;{$key}[$k]=" . $v;
                            }
                        } else {
                            $val = s(clean_param($val, PARAM_CLEANHTML));
                            $postfiltervars .= "&amp;$key=" . $val;
                        }
                    }
                }
            }

            $this->totalrecords = count($this->finalreport->table->data);
            $pagingbar = new paging_bar(
                $this->totalrecords,
                $page,
                $this->config->pagination,
                "viewreport.php?id=" . s($this->config->id) . "&courseid=" . ((int) $this->config->courseid) .
                "$postfiltervars&amp;"
            );
            $pagingbar->pagevar = 'page';
            $pagination = $OUTPUT->render($pagingbar);
        }

        $search = [
            '##reportname##',
            '##reportsummary##',
            '##graphs##',
            '##exportoptions##',
            '##calculationstable##',
            '##pagination##',
        ];
        $replace = [
            format_string($this->config->name),
            format_text($this->config->summary),
            $this->print_graphs(true),
            $this->print_export_options(true),
            $calculations,
            $pagination,
        ];

        foreach ($pagecontents as $key => $p) {
            if ($p) {
                $pagecontents[$key] = str_ireplace($search, $replace, $p);
            }
        }

        if ($this->config->jsordering) {
            $this->add_jsordering($moodlepage);
        }
        $this->print_filters();

        echo "<div id=\"printablediv\">\n";
        // Print the header.
        if (is_array($pagecontents['header'])) {
            echo format_text($pagecontents['header']['text'], $pagecontents['header']['format']);
        } else {
            echo format_text($pagecontents['header'], FORMAT_HTML);
        }

        if ($this->config->displaytotalrecords) {
            $a = new \stdClass();
            $a->totalrecords = $this->totalrecords;
            echo \html_writer::tag('div', get_string('totalrecords', 'block_configurable_reports', $a), array('id' => 'totalrecords'));
        }

        if ($recordtpl) {
            if ($this->config->pagination) {
                $page = optional_param('page', 0, PARAM_INT);
                $this->totalrecords = count($this->finalreport->table->data);
                $this->finalreport->table->data =
                    array_slice($this->finalreport->table->data, $page * $this->config->pagination, $this->config->pagination);
            }

            foreach ($this->finalreport->table->data as $r) {
                if (is_array($recordtpl)) {
                    $recordtext = $recordtpl['text'];
                } else {
                    $recordtext = $recordtpl;
                }

                foreach ($this->finalreport->table->head as $key => $c) {
                    $recordtext = str_ireplace("[[$c]]", $r[$key], $recordtext);
                }
                echo format_text($recordtext, FORMAT_HTML);
            }
        }

        // Print the footer.
        if (is_array($pagecontents['footer'])) {
            echo format_text($pagecontents['footer']['text'], $pagecontents['footer']['format']);
        } else {
            echo format_text($pagecontents['footer'], FORMAT_HTML);
        }

        echo "</div>\n";
        if ($this->config->displayprintbutton) {
            echo '<div class="centerpara"><br />';
            echo $OUTPUT->pix_icon('print', get_string('printreport', 'block_configurable_reports'), 'block_configurable_reports');
            echo "&nbsp;<a href=\"javascript: printDiv('printablediv')\">".get_string('printreport', 'block_configurable_reports')."</a>";
            echo "</div>\n";
        }
    }

    /**
     * print_report_page
     *
     * @param moodle_page $moodlepage
     * @return true|void
     */
    public function print_report_page(moodle_page $moodlepage) {
        global $OUTPUT;

        if ($this->config->displayprintbutton) {
            cr_print_js_function();
        }
        $components = cr_unserialize($this->config->components);

        $template = (isset($components['template']['config']) && $components['template']['config']->enabled &&
            $components['template']['config']->record) ? $components['template']['config'] : false;

        if ($template) {
            $this->print_template($template, $moodlepage);

            return true;
        }

        // Debug.
        $debug = optional_param('debug', false, PARAM_BOOL);
        if ($debug || !empty($this->config->debug)) {
            echo html_writer::empty_tag('hr');
            echo html_writer::tag('div', $this->sql, ['id' => 'debug', 'style' => 'direction:ltr;text-align:left;']);
            echo html_writer::empty_tag('hr');
        }

        echo '<div class="centerpara">';
        echo format_text($this->config->summary);
        echo '</div>';

        $this->print_filters();
        if ($this->finalreport->table && !empty($this->finalreport->table->data[0])) {

            echo "<div id=\"printablediv\">\n";
            $this->print_graphs();

            if ($this->config->jsordering) {
                $this->add_jsordering($moodlepage);
            }

            $this->totalrecords = count($this->finalreport->table->data);
            if ($this->config->pagination) {
                $page = optional_param('page', 0, PARAM_INT);
                $this->totalrecords = count($this->finalreport->table->data);
                $this->finalreport->table->data =
                    array_slice($this->finalreport->table->data, $page * $this->config->pagination, $this->config->pagination);
            }

            cr_print_table($this->finalreport->table);

            if ($this->config->pagination) {
                $postfiltervars = '';
                $request = array_merge($_POST, $_GET);
                if ($request) {
                    foreach ($request as $key => $val) {
                        if (strpos($key, 'filter_') !== false) {
                            $key = s(clean_param($key, PARAM_CLEANHTML));
                            if (is_array($val)) {
                                foreach ($val as $k => $v) {
                                    $k = s(clean_param($k, PARAM_CLEANHTML));
                                    $v = s(clean_param($v, PARAM_CLEANHTML));
                                    $postfiltervars .= "&amp;{$key}[$k]=" . $v;
                                }
                            } else {
                                $val = s(clean_param($val, PARAM_CLEANHTML));
                                $postfiltervars .= "&amp;$key=" . $val;
                            }
                        }
                    }
                }

                $pagingbar = new paging_bar(
                    $this->totalrecords,
                    $page,
                    $this->config->pagination,
                    "viewreport.php?id=" . s($this->config->id) . "&courseid=" . s($this->config->courseid) . "$postfiltervars&amp;"
                );
                $pagingbar->pagevar = 'page';
                echo $OUTPUT->render($pagingbar);
            }

            // Report statistics.
            $a = new stdClass();
            $a->totalrecords = $this->totalrecords;
            echo html_writer::tag('div', get_string('totalrecords', 'block_configurable_reports', $a), ['id' => 'totalrecords']);

            echo html_writer::tag(
                'div',
                get_string('lastexecutiontime', 'block_configurable_reports', $this->config->lastexecutiontime / 1000),
                ['id' => 'lastexecutiontime']
            );

            if (!empty($this->finalreport->calcs->data[0])) {
                echo '<br /><br /><br /><div class="centerpara"><b>' .
                    get_string('columncalculations', 'block_configurable_reports') . '</b></div><br />';
                echo html_writer::table($this->finalreport->calcs);
            }
            echo "</div>";

            $this->print_export_options();
        } else {
            echo '<div class="centerpara">' . get_string('norecordsfound', 'block_configurable_reports') . '</div>';
        }

        if ($this->config->displayprintbutton) {
            echo '<div class="centerpara"><br />';
            echo $OUTPUT->pix_icon('print', get_string('printreport', 'block_configurable_reports'), 'block_configurable_reports');
            echo "&nbsp;<a href=\"javascript: printDiv('printablediv')\">".get_string('printreport', 'block_configurable_reports')."</a>";
            echo "</div>\n";
        }
    }

    /**
     * utf8_strrev
     *
     * @param string $str
     * @return string
     */
    public function utf8_strrev(string $str): string {
        preg_match_all('/./us', $str, $ar);

        return implode('', array_reverse($ar[0]));
    }

}
