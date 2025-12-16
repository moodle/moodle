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

namespace Google\Service\ChromeManagement\Resource;

use Google\Service\ChromeManagement\GoogleChromeManagementV1CountActiveDevicesResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountChromeBrowsersNeedingAttentionResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountChromeCrashEventsResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountChromeDevicesReachingAutoExpirationDateResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountChromeDevicesThatNeedAttentionResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountChromeHardwareFleetDevicesResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountChromeVersionsResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountDevicesPerBootTypeResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountDevicesPerReleaseChannelResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountInstalledAppsResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountPrintJobsByPrinterResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1CountPrintJobsByUserResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1EnumeratePrintJobsResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementV1FindInstalledAppDevicesResponse;

/**
 * The "reports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $reports = $chromemanagementService->customers_reports;
 *  </code>
 */
class CustomersReports extends \Google\Service\Resource
{
  /**
   * Get a count of active devices per set time frames.
   * (reports.countActiveDevices)
   *
   * @param string $customer Required. Obfuscated customer ID prefixed with
   * "customers/C" or "customers/my_customer".
   * @param array $optParams Optional parameters.
   *
   * @opt_param int date.day Day of a month. Must be from 1 to 31 and valid for
   * the year and month, or 0 to specify a year by itself or a year and month
   * where the day isn't significant.
   * @opt_param int date.month Month of a year. Must be from 1 to 12, or 0 to
   * specify a year without a month and day.
   * @opt_param int date.year Year of the date. Must be from 1 to 9999, or 0 to
   * specify a date without a year.
   * @return GoogleChromeManagementV1CountActiveDevicesResponse
   * @throws \Google\Service\Exception
   */
  public function countActiveDevices($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countActiveDevices', [$params], GoogleChromeManagementV1CountActiveDevicesResponse::class);
  }
  /**
   * Count of Chrome Browsers that have been recently enrolled, have new policy to
   * be synced, or have no recent activity.
   * (reports.countChromeBrowsersNeedingAttention)
   *
   * @param string $customer Required. The customer ID or "my_customer" prefixed
   * with "customers/".
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orgUnitId Optional. The ID of the organizational unit. If
   * omitted, all data will be returned.
   * @return GoogleChromeManagementV1CountChromeBrowsersNeedingAttentionResponse
   * @throws \Google\Service\Exception
   */
  public function countChromeBrowsersNeedingAttention($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countChromeBrowsersNeedingAttention', [$params], GoogleChromeManagementV1CountChromeBrowsersNeedingAttentionResponse::class);
  }
  /**
   * Get a count of Chrome crash events. (reports.countChromeCrashEvents)
   *
   * @param string $customer Customer ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Query string to filter results, AND-separated fields
   * in EBNF syntax. Supported filter fields: * major_browser_version *
   * minor_browser_version * browser_channel * device_platform * past_number_days
   * Example: `major_browser_version = 'M115' AND past_number_days = '28'`.
   * @opt_param string orderBy Field used to order results. Supported order by
   * fields: * browser_version * count * date
   * @opt_param string orgUnitId If specified, only count the number of crash
   * events of the devices in this organizational unit.
   * @return GoogleChromeManagementV1CountChromeCrashEventsResponse
   * @throws \Google\Service\Exception
   */
  public function countChromeCrashEvents($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countChromeCrashEvents', [$params], GoogleChromeManagementV1CountChromeCrashEventsResponse::class);
  }
  /**
   * Generate report of the number of devices expiring in each month of the
   * selected time frame. Devices are grouped by auto update expiration date and
   * model. Further information can be found
   * [here](https://support.google.com/chrome/a/answer/10564947).
   * (reports.countChromeDevicesReachingAutoExpirationDate)
   *
   * @param string $customer Required. The customer ID or "my_customer" prefixed
   * with "customers/".
   * @param array $optParams Optional parameters.
   *
   * @opt_param string maxAueDate Optional. Maximum expiration date in format
   * yyyy-mm-dd in UTC timezone. If included returns all devices that have already
   * expired and devices with auto expiration date equal to or earlier than the
   * maximum date.
   * @opt_param string minAueDate Optional. Maximum expiration date in format
   * yyyy-mm-dd in UTC timezone. If included returns all devices that have already
   * expired and devices with auto expiration date equal to or later than the
   * minimum date.
   * @opt_param string orgUnitId Optional. The organizational unit ID, if omitted,
   * will return data for all organizational units.
   * @return GoogleChromeManagementV1CountChromeDevicesReachingAutoExpirationDateResponse
   * @throws \Google\Service\Exception
   */
  public function countChromeDevicesReachingAutoExpirationDate($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countChromeDevicesReachingAutoExpirationDate', [$params], GoogleChromeManagementV1CountChromeDevicesReachingAutoExpirationDateResponse::class);
  }
  /**
   * Counts of ChromeOS devices that have not synced policies or have lacked user
   * activity in the past 28 days, are out of date, or are not complaint. Further
   * information can be found here
   * https://support.google.com/chrome/a/answer/10564947
   * (reports.countChromeDevicesThatNeedAttention)
   *
   * @param string $customer Required. The customer ID or "my_customer" prefixed
   * with "customers/".
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orgUnitId Optional. The ID of the organizational unit. If
   * omitted, all data will be returned.
   * @opt_param string readMask Required. Mask of the fields that should be
   * populated in the returned report.
   * @return GoogleChromeManagementV1CountChromeDevicesThatNeedAttentionResponse
   * @throws \Google\Service\Exception
   */
  public function countChromeDevicesThatNeedAttention($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countChromeDevicesThatNeedAttention', [$params], GoogleChromeManagementV1CountChromeDevicesThatNeedAttentionResponse::class);
  }
  /**
   * Counts of devices with a specific hardware specification from the requested
   * hardware type (for example model name, processor type). Further information
   * can be found here https://support.google.com/chrome/a/answer/10564947
   * (reports.countChromeHardwareFleetDevices)
   *
   * @param string $customer Required. The customer ID or "my_customer".
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orgUnitId Optional. The ID of the organizational unit. If
   * omitted, all data will be returned.
   * @opt_param string readMask Required. Mask of the fields that should be
   * populated in the returned report.
   * @return GoogleChromeManagementV1CountChromeHardwareFleetDevicesResponse
   * @throws \Google\Service\Exception
   */
  public function countChromeHardwareFleetDevices($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countChromeHardwareFleetDevices', [$params], GoogleChromeManagementV1CountChromeHardwareFleetDevicesResponse::class);
  }
  /**
   * Generate report of installed Chrome versions. (reports.countChromeVersions)
   *
   * @param string $customer Required. Customer id or "my_customer" to use the
   * customer associated to the account making the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Query string to filter results, AND-separated fields
   * in EBNF syntax. Note: OR operations are not supported in this filter.
   * Supported filter fields: * last_active_date
   * @opt_param string orgUnitId The ID of the organizational unit.
   * @opt_param int pageSize Maximum number of results to return. Maximum and
   * default are 100.
   * @opt_param string pageToken Token to specify the page of the request to be
   * returned.
   * @return GoogleChromeManagementV1CountChromeVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function countChromeVersions($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countChromeVersions', [$params], GoogleChromeManagementV1CountChromeVersionsResponse::class);
  }
  /**
   * Get a count of devices per boot type. (reports.countDevicesPerBootType)
   *
   * @param string $customer Required. Obfuscated customer ID prefixed with
   * "customers/C" or "customers/my_customer".
   * @param array $optParams Optional parameters.
   *
   * @opt_param int date.day Day of a month. Must be from 1 to 31 and valid for
   * the year and month, or 0 to specify a year by itself or a year and month
   * where the day isn't significant.
   * @opt_param int date.month Month of a year. Must be from 1 to 12, or 0 to
   * specify a year without a month and day.
   * @opt_param int date.year Year of the date. Must be from 1 to 9999, or 0 to
   * specify a date without a year.
   * @return GoogleChromeManagementV1CountDevicesPerBootTypeResponse
   * @throws \Google\Service\Exception
   */
  public function countDevicesPerBootType($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countDevicesPerBootType', [$params], GoogleChromeManagementV1CountDevicesPerBootTypeResponse::class);
  }
  /**
   * Get a count of devices per channel. (reports.countDevicesPerReleaseChannel)
   *
   * @param string $customer Required. Obfuscated customer ID prefixed with
   * "customers/C" or "customers/my_customer".
   * @param array $optParams Optional parameters.
   *
   * @opt_param int date.day Day of a month. Must be from 1 to 31 and valid for
   * the year and month, or 0 to specify a year by itself or a year and month
   * where the day isn't significant.
   * @opt_param int date.month Month of a year. Must be from 1 to 12, or 0 to
   * specify a year without a month and day.
   * @opt_param int date.year Year of the date. Must be from 1 to 9999, or 0 to
   * specify a date without a year.
   * @return GoogleChromeManagementV1CountDevicesPerReleaseChannelResponse
   * @throws \Google\Service\Exception
   */
  public function countDevicesPerReleaseChannel($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countDevicesPerReleaseChannel', [$params], GoogleChromeManagementV1CountDevicesPerReleaseChannelResponse::class);
  }
  /**
   * Generate report of app installations. (reports.countInstalledApps)
   *
   * @param string $customer Required. Customer id or "my_customer" to use the
   * customer associated to the account making the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Query string to filter results, AND-separated fields
   * in EBNF syntax. Note: OR operations are not supported in this filter.
   * Supported filter fields: * app_name * app_type * install_type *
   * number_of_permissions * total_install_count * latest_profile_active_date *
   * permission_name * app_id * manifest_versions * risk_score
   * @opt_param string orderBy Field used to order results. Supported order by
   * fields: * app_name * app_type * install_type * number_of_permissions *
   * total_install_count * app_id * manifest_versions * risk_score
   * @opt_param string orgUnitId The ID of the organizational unit.
   * @opt_param int pageSize Maximum number of results to return. Maximum and
   * default are 100.
   * @opt_param string pageToken Token to specify the page of the request to be
   * returned.
   * @return GoogleChromeManagementV1CountInstalledAppsResponse
   * @throws \Google\Service\Exception
   */
  public function countInstalledApps($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countInstalledApps', [$params], GoogleChromeManagementV1CountInstalledAppsResponse::class);
  }
  /**
   * Get a summary of printing done by each printer.
   * (reports.countPrintJobsByPrinter)
   *
   * @param string $customer Required. Customer ID prefixed with "customers/" or
   * "customers/my_customer" to use the customer associated to the account making
   * the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Query string to filter results, AND-separated fields
   * in EBNF syntax. Note: OR operations are not supported in this filter. Note:
   * Only >= and <= comparators are supported in this filter. Supported filter
   * fields: * complete_time
   * @opt_param string orderBy Field used to order results. If omitted, results
   * will be ordered in ascending order of the 'printer' field. Supported order_by
   * fields: * printer * job_count * device_count * user_count
   * @opt_param int pageSize Maximum number of results to return. Maximum and
   * default are 100.
   * @opt_param string pageToken Token to specify the page of the response to be
   * returned.
   * @opt_param string printerOrgUnitId The ID of the organizational unit for
   * printers. If specified, only data for printers from the specified
   * organizational unit will be returned. If omitted, data for printers from all
   * organizational units will be returned.
   * @return GoogleChromeManagementV1CountPrintJobsByPrinterResponse
   * @throws \Google\Service\Exception
   */
  public function countPrintJobsByPrinter($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countPrintJobsByPrinter', [$params], GoogleChromeManagementV1CountPrintJobsByPrinterResponse::class);
  }
  /**
   * Get a summary of printing done by each user. (reports.countPrintJobsByUser)
   *
   * @param string $customer Required. Customer ID prefixed with "customers/" or
   * "customers/my_customer" to use the customer associated to the account making
   * the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Query string to filter results, AND-separated fields
   * in EBNF syntax. Note: OR operations are not supported in this filter. Note:
   * Only >= and <= comparators are supported in this filter. Supported filter
   * fields: * complete_time
   * @opt_param string orderBy Field used to order results. If omitted, results
   * will be ordered in ascending order of the 'user_email' field. Supported
   * order_by fields: * user_email * job_count * printer_count * device_count
   * @opt_param int pageSize Maximum number of results to return. Maximum and
   * default are 100.
   * @opt_param string pageToken Token to specify the page of the response to be
   * returned.
   * @opt_param string printerOrgUnitId The ID of the organizational unit for
   * printers. If specified, only print jobs initiated with printers from the
   * specified organizational unit will be counted. If omitted, all print jobs
   * will be counted.
   * @return GoogleChromeManagementV1CountPrintJobsByUserResponse
   * @throws \Google\Service\Exception
   */
  public function countPrintJobsByUser($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('countPrintJobsByUser', [$params], GoogleChromeManagementV1CountPrintJobsByUserResponse::class);
  }
  /**
   * Get a list of print jobs. (reports.enumeratePrintJobs)
   *
   * @param string $customer Required. Customer ID prefixed with "customers/" or
   * "customers/my_customer" to use the customer associated to the account making
   * the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Query string to filter results, AND-separated fields
   * in EBNF syntax. Note: OR operations are not supported in this filter. Note:
   * Only >= and <= comparators are supported for `complete_time`. Note: Only =
   * comparator supported for `user_id` and `printer_id`. Supported filter fields:
   * * complete_time * printer_id * user_id
   * @opt_param string orderBy Field used to order results. If not specified,
   * results will be ordered in descending order of the `complete_time` field.
   * Supported order by fields: * title * state * create_time * complete_time *
   * document_page_count * color_mode * duplex_mode * printer * user_email
   * @opt_param int pageSize The number of print jobs in the page from 0 to 100
   * inclusive, if page_size is not specified or zero, the size will be 50.
   * @opt_param string pageToken A page token received from a previous
   * `EnumeratePrintJobs` call. Provide this to retrieve the subsequent page. If
   * omitted, the first page of results will be returned. When paginating, all
   * other parameters provided to `EnumeratePrintJobs` must match the call that
   * provided the page token.
   * @opt_param string printerOrgUnitId The ID of the organizational unit for
   * printers. If specified, only print jobs submitted to printers from the
   * specified organizational unit will be returned.
   * @return GoogleChromeManagementV1EnumeratePrintJobsResponse
   * @throws \Google\Service\Exception
   */
  public function enumeratePrintJobs($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('enumeratePrintJobs', [$params], GoogleChromeManagementV1EnumeratePrintJobsResponse::class);
  }
  /**
   * Generate report of managed Chrome browser devices that have a specified app
   * installed. (reports.findInstalledAppDevices)
   *
   * @param string $customer Required. Customer id or "my_customer" to use the
   * customer associated to the account making the request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string appId Unique identifier of the app. For Chrome apps and
   * extensions, the 32-character id (e.g. ehoadneljpdggcbbknedodolkkjodefl). For
   * Android apps, the package name (e.g. com.evernote).
   * @opt_param string appType Type of the app. Optional. If not provided, an app
   * type will be inferred from the format of the app ID.
   * @opt_param string filter Query string to filter results, AND-separated fields
   * in EBNF syntax. Note: OR operations are not supported in this filter.
   * Supported filter fields: * last_active_date
   * @opt_param string orderBy Field used to order results. Supported order by
   * fields: * machine * device_id
   * @opt_param string orgUnitId The ID of the organizational unit.
   * @opt_param int pageSize Maximum number of results to return. Maximum and
   * default are 100.
   * @opt_param string pageToken Token to specify the page of the request to be
   * returned.
   * @return GoogleChromeManagementV1FindInstalledAppDevicesResponse
   * @throws \Google\Service\Exception
   */
  public function findInstalledAppDevices($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('findInstalledAppDevices', [$params], GoogleChromeManagementV1FindInstalledAppDevicesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersReports::class, 'Google_Service_ChromeManagement_Resource_CustomersReports');
