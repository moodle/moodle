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
 * LearnerScript Licence Settings.
 *
 * @package   block_learnerscript
 * @copyright 2018 Arun Kumar Mukka
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_learnerscript_licence_setting extends admin_setting_configtext {
    /**
     * Constructor.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param mixed $defaultsetting string or array depending on implementation
     * @param bool|null $duringstate
     */
    public function __construct($name, $visiblename, $description,
            $defaultsetting, $paramtype = PARAM_ALPHANUMEXT, $size = null) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        GLOBAL $CFG, $DB, $PAGE;

        if (empty($data)) {
            $result = $this->config_write($this->name, $data);
            set_config('ls_'.$this->name, $data, 'block_learnerscript');
            return '';
        }
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        $learnerscript = md5($data);
        $result = $this->config_write($this->name, $data);
        set_config('ls_'.$this->name, $learnerscript, 'block_learnerscript');
        $lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
        if (!$lsreportconfigstatus) {
            redirect($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1');
        } else {
            $reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reportdashboard', false);
            if ($reportdashboardblockexists) {
                redirect($CFG->wwwroot . '/blocks/reportdashboard/dashboard.php');
            } else {
                redirect($CFG->wwwroot . '/blocks/learnerscript/managereport.php');
            }
        }
        exit;
        return '';
    }
    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) { 
        GLOBAL $CFG, $PAGE;

        // Allow paramtype to be a custom regex if it is the form of /pattern/.
        if (preg_match('#^/.*/$#', $this->paramtype)) {
            if (preg_match($this->paramtype, $data)) {
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }
        } else {
            $cleaned = clean_param($data, $this->paramtype);
            if ("$data" === "$cleaned") { // Implicit conversion to string is needed to do exact comparison.
                $curl = new curl;
                $params['serial'] = $cleaned;
                $params['surl'] = $CFG->lssourceurl;
                $param = json_encode($params);
                $json = $curl->post('https://learnerscript.com?wc-api=custom_validate_serial_key', $param);
                for ($i = 0; $i <= 31; ++$i) { 
                    $json = str_replace(chr($i), "", $json); 
                }
                $json = str_replace(chr(127), "", $json);

                // This is the most common part
                // Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
                // here we detect it and we remove it, basically it's the first 3 characters 
                if (0 === strpos(bin2hex($json), 'efbbbf')) {
                   $json = substr($json, 3);
                }
                $jsondata = json_decode($json, true);
                if ($jsondata['success'] == 'true') {
                    return true;
                } else {
                    return $jsondata['message'];
                }
            } else {
                return get_string('validateerror', 'admin');
            }
        }
    }
    public function output_html($data, $query='') {
        global $PAGE, $CFG;
        $default = $this->get_defaultsetting();

        $pluginman = core_plugin_manager::instance();
        $reportdashboardpluginfo = $pluginman->get_plugin_info('block_reportdashboard');
        $reporttilespluginfo = $pluginman->get_plugin_info('block_reporttiles');
        $error = false;
        $errordata = array();
        $reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reportdashboard', false);
        // Make sure we know the plugin.
        if (is_null($reportdashboardpluginfo) || !$reportdashboardblockexists) {
            $error = true;
            $errordata[] = 'LearnerScript Widget';
        }
        $reporttilesblockexists = $PAGE->blocks->is_known_block_type('reporttiles', false);
        // Make sure we know the plugin.
        if (is_null($reporttilespluginfo) || !$reporttilesblockexists) {
            $error = true;
            $errordata[] = 'LearnerScript Report Tiles';
        }

        $return = '';
        $disabled = '';
        if ($error) {
            $errormsg = implode(', ', $errordata);
            $return .= '<div class="alert alert-notice">Install/Enable ' . $errormsg .
            ' plugin(s), Click Here to <a href="' . $CFG->wwwroot . '/admin/tool/installaddon/index.php" title="Install Plugins" >'
            . get_string('installplugins', 'block_learnerscript') . '</a></div>';
            $disabled = 'disabled';
        }

        $return .= format_admin_setting($this, $this->visiblename,
        '<div class="form-text defaultsnext"><input type="text" size="' . $this->size . '" id="' . $this->get_id() . '" name="'
        . $this->get_full_name() . '" value="' . s($data) . '" ' . $disabled . '/></div>',
        $this->description, true, '', $default, $query);
        return $return;
    }
}
