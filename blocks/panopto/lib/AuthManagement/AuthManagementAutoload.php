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
 * File to load generated classes once at once time
 * @package AuthManagement
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
/**
 * Includes for all generated classes files
 * @author Panopto
 * @version 20150429-01
 * @date 2017-05-25
 */
require_once dirname(__FILE__) . '/AuthManagementWsdlClass.php';
require_once dirname(__FILE__) . '/Report/Info/AuthManagementStructReportIntegrationInfo.php';
require_once dirname(__FILE__) . '/Get/Response/AuthManagementStructGetAuthenticatedUrlResponse.php';
require_once dirname(__FILE__) . '/Report/Response/AuthManagementStructReportIntegrationInfoResponse.php';
require_once dirname(__FILE__) . '/Get/Url/AuthManagementStructGetAuthenticatedUrl.php';
require_once dirname(__FILE__) . '/Get/Response/AuthManagementStructGetServerVersionResponse.php';
require_once dirname(__FILE__) . '/Log/Response/AuthManagementStructLogOnWithPasswordResponse.php';
require_once dirname(__FILE__) . '/Log/Password/AuthManagementStructLogOnWithPassword.php';
require_once dirname(__FILE__) . '/Log/Provider/AuthManagementStructLogOnWithExternalProvider.php';
require_once dirname(__FILE__) . '/Log/Response/AuthManagementStructLogOnWithExternalProviderResponse.php';
require_once dirname(__FILE__) . '/Get/Version/AuthManagementStructGetServerVersion.php';
require_once dirname(__FILE__) . '/Authentication/Info/AuthManagementStructAuthenticationInfo.php';
require_once dirname(__FILE__) . '/Log/AuthManagementServiceLog.php';
require_once dirname(__FILE__) . '/Get/AuthManagementServiceGet.php';
require_once dirname(__FILE__) . '/Report/AuthManagementServiceReport.php';
require_once dirname(__FILE__) . '/AuthManagementClassMap.php';
