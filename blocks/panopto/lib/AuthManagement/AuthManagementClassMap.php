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
 * File for the class which returns the class map definition
 * @package AuthManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
/**
 * Class which returns the class map definition by the static method AuthManagementClassMap::classMap()
 * @package AuthManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
class AuthManagementClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'AuthenticationInfo' => 'AuthManagementStructAuthenticationInfo',
  'GetAuthenticatedUrl' => 'AuthManagementStructGetAuthenticatedUrl',
  'GetAuthenticatedUrlResponse' => 'AuthManagementStructGetAuthenticatedUrlResponse',
  'GetServerVersion' => 'AuthManagementStructGetServerVersion',
  'GetServerVersionResponse' => 'AuthManagementStructGetServerVersionResponse',
  'LogOnWithExternalProvider' => 'AuthManagementStructLogOnWithExternalProvider',
  'LogOnWithExternalProviderResponse' => 'AuthManagementStructLogOnWithExternalProviderResponse',
  'LogOnWithPassword' => 'AuthManagementStructLogOnWithPassword',
  'LogOnWithPasswordResponse' => 'AuthManagementStructLogOnWithPasswordResponse',
  'ReportIntegrationInfo' => 'AuthManagementStructReportIntegrationInfo',
  'ReportIntegrationInfoResponse' => 'AuthManagementStructReportIntegrationInfoResponse',
);
    }
}
