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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\GoogleChecksReportV1alphaListReportsResponse;
use Google\Service\ChecksService\GoogleChecksReportV1alphaReport;

/**
 * The "reports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $reports = $checksService->accounts_apps_reports;
 *  </code>
 */
class AccountsAppsReports extends \Google\Service\Resource
{
  /**
   * Gets a report. By default, only the name and results_uri fields are returned.
   * You can include other fields by listing them in the `fields` URL query
   * parameter. For example, `?fields=name,checks` will return the name and checks
   * fields. (reports.get)
   *
   * @param string $name Required. Resource name of the report. Example:
   * `accounts/123/apps/456/reports/789`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string checksFilter Optional. An
   * [AIP-160](https://google.aip.dev/160) filter string to filter checks within
   * the report. Only checks that match the filter string are included in the
   * response. Example: `state = FAILED`
   * @return GoogleChecksReportV1alphaReport
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleChecksReportV1alphaReport::class);
  }
  /**
   * Lists reports for the specified app. By default, only the name and
   * results_uri fields are returned. You can include other fields by listing them
   * in the `fields` URL query parameter. For example,
   * `?fields=reports(name,checks)` will return the name and checks fields.
   * (reports.listAccountsAppsReports)
   *
   * @param string $parent Required. Resource name of the app. Example:
   * `accounts/123/apps/456`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string checksFilter Optional. An
   * [AIP-160](https://google.aip.dev/160) filter string to filter checks within
   * reports. Only checks that match the filter string are included in the
   * response. Example: `state = FAILED`
   * @opt_param string filter Optional. An [AIP-160](https://google.aip.dev/160)
   * filter string to filter reports. Example: `appBundle.releaseType =
   * PRE_RELEASE`
   * @opt_param int pageSize Optional. The maximum number of reports to return. If
   * unspecified, at most 10 reports will be returned. The maximum value is 50;
   * values above 50 will be coerced to 50.
   * @opt_param string pageToken Optional. A page token received from a previous
   * `ListReports` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListReports` must match the
   * call that provided the page token.
   * @return GoogleChecksReportV1alphaListReportsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsAppsReports($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChecksReportV1alphaListReportsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsAppsReports::class, 'Google_Service_ChecksService_Resource_AccountsAppsReports');
