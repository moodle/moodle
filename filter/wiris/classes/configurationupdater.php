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
//

/**
 * This class implements com_wiris_plugin_configuration_ConfigurationUpdater interface
 * to use a custom Moodle configuration.
 *
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/filter/wiris/integration/lib/com/wiris/plugin/configuration/ConfigurationUpdater.interface.php');

class filter_wiris_configurationupdater implements com_wiris_plugin_configuration_ConfigurationUpdater {

    public $waseditorenabled;
    public $wascasenabled;
    public $waschemeditorenabled;
    private $oldconfiguration;

    public $editorplugin;

    public function __construct() {
        $scriptname = explode('/', $_SERVER["SCRIPT_FILENAME"]);
        $scriptname = array_pop($scriptname);

        if ($scriptname == 'showimage.php') {
            return;
        }

        global $CFG;

        $this->editorplugin = filter_wiris_pluginwrapper::get_wiris_plugin();
        $this->oldconfiguration = filter_wiris_pluginwrapper::get_old_configuration();

    }

    public function init($obj) {
    }

    private function get_latex_status() {
        global $CFG;

        $filters = filter_get_globally_enabled();
        // Since Moodle 2.5 key is 'tex' not 'filter/tex'.
        $status = ($CFG->version >= 2013051400) ? array_key_exists('tex', $filters) : array_key_exists('filter/tex', $filters);
        return $status;
    }

    private function eval_parameter($param) {
        return ($param == 1 || $param == "true");
    }

    // @codingStandardsIgnoreStart
    // Can't change implemented interface method name.
    public function updateConfiguration(&$configuration) {
    // @codingStandardsIgnoreEnd
        global $CFG;

        // Old configuration.ini.
        if ($this->oldconfiguration) {
            $configuration['wirisconfigurationpath'] = $this->editorplugin->path;
        }

        $scriptname = explode('/', $_SERVER["SCRIPT_FILENAME"]);
        $scriptname = array_pop($scriptname);

        com_wiris_system_CallWrapper::getInstance()->stop();

        // Configuration.ini wrapper.

        // Connection properties.

        if (get_config('filter_wiris', 'imageservicehost')) {
            $configuration['wirisimageservicehost'] = get_config('filter_wiris', 'imageservicehost');
        }

        if (get_config('filter_wiris', 'imageservicepath')) {
            $configuration['wirisimageservicepath'] = get_config('filter_wiris', 'imageservicepath');
        }

        if (get_config('filter_wiris', 'imageserviceprotocol')) {
            $configuration['wirisimageserviceprotocol'] = get_config('filter_wiris', 'imageserviceprotocol');
        }

        // Image properties.

        if (get_config('filter_wiris', 'imageformat')) {
            $configuration['wirisimageformat'] = get_config('filter_wiris', 'imageformat');
        }

        if (!get_config('filter_wiris', 'pluginperformance')) {
            $configuration['wirispluginperformance'] = 'false';
        }

        // Window Properties.

        if (get_config('filter_wiris', 'editormodalwindowfullscreen')) {
            $configuration['wiriseditormodalwindowfullscreen'] = 'true';
        }

        // Enabling access provider if has been setted on MathType filter settings.

        if (get_config('filter_wiris', 'access_provider_enabled')) {
            $configuration['wirisaccessproviderenabled'] = 'true';
        }

        // Inherit proxy configuration.

        $moodleproxyenabled = !empty($CFG->proxyhost);
        $proxyportenabled = !empty($CFG->proxyport);
        $proxyuserenabled = !empty($CFG->proxyuser);
        $proxypassenabled = !empty($CFG->proxypassword);

        if ($moodleproxyenabled) {
            $configuration['wirisproxy'] = "true";
            $configuration['wirisproxy_host'] = $CFG->proxyhost;
            $configuration['wirisproxy_port'] = $proxyportenabled ? $CFG->proxyport : null;
            $configuration['wirisproxy_user'] = $proxyuserenabled ? $CFG->proxyuser : null;
            $configuration['wirisproxy_password'] = $proxypassenabled ? $CFG->proxypassword : null;
        }

        if ($scriptname == 'showimage.php') { // Minimal conf showing images.
            if (optional_param('refererquery', null, PARAM_RAW) != null) {
                $refererquery = implode('&', explode('/', optional_param('refererquery', null, PARAM_RAW)));
                $configuration['wirisreferer'] = $CFG->wwwroot . $refererquery;
            }
            com_wiris_system_CallWrapper::getInstance()->start();
            return;
        }

        // Enable LaTeX.
        if ($this->get_latex_status()) {
            $configuration['wiriseditorparselatex'] = false;
        }
        // MathType.
        $filterenabled = filter_is_enabled('filter/wiris');
        $this->waseditorenabled = $this->eval_parameter($configuration['wiriseditorenabled']);
        if (get_config('filter_wiris', 'editor_enable')) {
            // We need to convert all boolean values to text because $configuration object expects as values
            // the same objects as configuration.ini (i.e strings). This is mandatory due to cross-technology.
            $wiriseditorenabled = ($this->waseditorenabled &&
                                   $this->eval_parameter(get_config('filter_wiris', 'editor_enable')) &&
                                   $filterenabled) ? "true" : "false";
            $configuration['wiriseditorenabled'] = $wiriseditorenabled;
        } else {
            $configuration['wiriseditorenabled'] = "false";
        }
        // Cas.
        $this->wascasenabled = $this->eval_parameter($configuration['wiriscasenabled']);
        if (isset($CFG->filter_wiris_cas_enable)) {
            $wiriscasenabled = ($this->wascasenabled &&
                                $this->eval_parameter($CFG->filter_wiris_cas_enable) && $filterenabled) ? "true" : "false";
            $configuration['wiriscasenabled'] = $wiriscasenabled;
        } else {
            $configuration['wiriscasenabled'] = false;
        }

        // ChemType.
        $this->waschemeditorenabled = $this->eval_parameter($configuration['wirischemeditorenabled']);
        if (get_config('filter_wiris', 'chem_editor_enable')) {
            $wirischemeditorenabled = $this->waschemeditorenabled &&
                                      $this->eval_parameter(get_config('filter_wiris', 'chem_editor_enable')) &&
                                      $filterenabled ? "true" : "false";
            $configuration['wirischemeditorenabled'] = $wirischemeditorenabled;
        } else {
            $configuration['wirischemeditorenabled'] = false;
        }

        // Where is the plugin.
        $configuration['wiriscontextpath'] = $CFG->wwwroot . '/filter/wiris/';
        // Encoded XML.
        $configuration['wiriseditorsavemode'] = 'safeXml';
        $configuration['wirishostplatform'] = 'Moodle';
        $configuration['wirisversionplatform'] = $CFG->version;
        // Referer.
        global $COURSE;
        $query = '';
        if (isset($COURSE->id)) {
            $query .= '?course=' . $COURSE->id;
        }
        if (isset($COURSE->category)) {
            $query .= empty($query) ? '?' : '&';
            $query .= 'category=' . $COURSE->category;
        }

        $configuration['wirisreferer'] = $CFG->wwwroot . $query;

        com_wiris_system_CallWrapper::getInstance()->start();
    }
}
