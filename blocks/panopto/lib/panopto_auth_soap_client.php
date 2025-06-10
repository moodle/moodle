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
 * The auth soap client for Panopto
 *
 * @package block_panopto
 * @copyright Panopto 2009 - 2016 with contributions from Spenser Jones (sjones@ambrose.edu),
 * Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This can't be defined Moodle internal because it is called from Panopto to authorize login.
// @codingStandardsIgnoreLine
require_once(dirname(__FILE__) . '/AuthManagement/AuthManagementAutoload.php');
require_once(dirname(__FILE__) . '/panopto_data.php');
require_once(dirname(__FILE__) . '/block_panopto_lib.php');
require_once(dirname(__FILE__) . '/panopto_timeout_soap_client.php');

/**
 * Panopto auth soap client class.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panopto_auth_soap_client extends PanoptoTimeoutSoapClient {

    /**
     * @var array $authparam
     */
    private $authparam;

    /**
     * @var array $serviceparams the url used to get the service wsdl, as well as optional proxy options
     */
    private $serviceparams;

    /**
     * @var AuthManagementServiceReport authmanagementservicereport object used to call the auth report service
     */
    private $authmanagementservicereport;

    /**
     * @var AuthManagementServiceGet authmanagementserviceget object used to call the auth get service
     */
    private $authmanagementserviceget;

    /**
     * @var AuthManagementServiceLog authmanagementservicelog object used to call the auth log service
     */
    private $authmanagementservicelog;

    /**
     * @var array panoptoauthcookies - this array will contain the cookies retrieved from the logOnWithExternalProvider response
     * These cookies are used to call the REST API
     */
    public $panoptoauthcookies;

    /**
     * Main constructor
     *
     * @param string $servername
     * @param string $apiuseruserkey
     * @param string $apiuserauthcode
     */
    public function __construct($servername, $apiuseruserkey, $apiuserauthcode) {

        // Cache web service credentials for all calls requiring authentication.
        $this->authparam = new AuthManagementStructAuthenticationInfo(
            $apiuserauthcode,
            null,
            $apiuseruserkey
        );

        $this->serviceparams =
            panopto_generate_wsdl_service_params('https://'. $servername . '/Panopto/PublicAPI/4.2/Auth.svc?singlewsdl');
        $this->serviceparams['wsdl_trace'] = true;
    }

    /**
     * Gets the version of the server.
     */
    public function get_server_version() {
        $returnvalue = false;

        if (!isset($this->authmanagementserviceget)) {
            $this->authmanagementserviceget = new AuthManagementServiceGet($this->serviceparams);
        }

        if ($this->authmanagementserviceget->GetServerVersion()) {
            $returnvalue = $this->authmanagementserviceget->getResult()->GetServerVersionResult;
        } else {
            \panopto_data::print_log(var_export($this->authmanagementserviceget->getLastError(), true));
        }
        return $returnvalue;
    }

    /**
     * Returns the version number of the current Panopto server.
     *
     * @param string $idprovidername - instance name for current server IDP
     * @param string $moduleversion - current plug in version
     * @param string $targetplatformversion - Moodle version
     */
    public function report_integration_info($idprovidername, $moduleversion, $targetplatformversion) {
        $returnvalue = false;

        if (!isset($this->authmanagementservicereport)) {
            $this->authmanagementservicereport = new AuthManagementServiceReport($this->serviceparams);
        }

        $reportparams = new AuthManagementStructReportIntegrationInfo(
            $this->authparam,
            strval($idprovidername),
            strval($moduleversion),
            strval($targetplatformversion)
        );

        if ($this->authmanagementservicereport->ReportIntegrationInfo($reportparams)) {
            $returnvalue = true;
        } else {
            $lasterror = $this->authmanagementservicereport->getLastError()['AuthManagementServiceReport::ReportIntegrationInfo'];
            \panopto_data::print_log(var_export($lasterror, true));
        }

        return $returnvalue;
    }

    /**
     * Logs the user into the WS service and retrieves the cookie for later use.
     */
    public function log_on_with_external_provider() {
        $this->authmanagementservicelog = new AuthManagementServiceLog($this->serviceparams, true);

        $logonparams = new AuthManagementStructLogOnWithExternalProvider(
            $this->authparam->getUserKey(),
            $this->authparam->getAuthCode()
        );

        if ($this->authmanagementservicelog->LogOnWithExternalProvider($logonparams)) {
            $soapclient = $this->authmanagementservicelog->getSoapClient();

            $this->panoptoauthcookies = $soapclient->getpanoptocookies();
        } else {
            $lasterror = $this->authmanagementservicelog->getLastError()['AuthManagementServiceLog::LogOnWithExternalProvider'];
            \panopto_data::print_log(var_export($lasterror, true));
        }
    }
}
/* End of file panopto_auth_soap_client.php */
