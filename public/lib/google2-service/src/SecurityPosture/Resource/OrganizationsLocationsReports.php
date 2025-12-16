<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\SecurityPosture\Resource;

use Google\Service\SecurityPosture\CreateIaCValidationReportRequest;
use Google\Service\SecurityPosture\ListReportsResponse;
use Google\Service\SecurityPosture\Operation;
use Google\Service\SecurityPosture\Report;

/**
 * The "reports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitypostureService = new Google\Service\SecurityPosture(...);
 *   $reports = $securitypostureService->organizations_locations_reports;
 *  </code>
 */
class OrganizationsLocationsReports extends \Google\Service\Resource
{
  /**
   * Validates a specified infrastructure-as-code (IaC) configuration, and creates
   * a Report with the validation results. Only Terraform configurations are
   * supported. Only modified assets are validated.
   * (reports.createIaCValidationReport)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param CreateIaCValidationReportRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function createIaCValidationReport($parent, CreateIaCValidationReportRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('createIaCValidationReport', [$params], Operation::class);
  }
  /**
   * Gets details for a Report. (reports.get)
   *
   * @param string $name Required. The name of the report, in the format
   * `organizations/{organization}/locations/global/reports/{report_id}`.
   * @param array $optParams Optional parameters.
   * @return Report
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Report::class);
  }
  /**
   * Lists every Report in a given organization and location.
   * (reports.listOrganizationsLocationsReports)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to apply to the list of reports,
   * in the format defined in [AIP-160: Filtering](https://google.aip.dev/160).
   * @opt_param int pageSize Optional. The maximum number of reports to return.
   * The default value is `500`. If you exceed the maximum value of `1000`, then
   * the service uses the maximum value.
   * @opt_param string pageToken Optional. A pagination token returned from a
   * previous request to list reports. Provide this token to retrieve the next
   * page of results.
   * @return ListReportsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsReports($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListReportsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsReports::class, 'Google_Service_SecurityPosture_Resource_OrganizationsLocationsReports');
