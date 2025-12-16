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

use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1ChromeBrowserProfile;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1ListChromeBrowserProfilesResponse;
use Google\Service\ChromeManagement\GoogleProtobufEmpty;

/**
 * The "profiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $profiles = $chromemanagementService->customers_profiles;
 *  </code>
 */
class CustomersProfiles extends \Google\Service\Resource
{
  /**
   * Deletes the data collected from a Chrome browser profile. (profiles.delete)
   *
   * @param string $name Required. Format:
   * customers/{customer_id}/profiles/{profile_permanent_id}
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets a Chrome browser profile with customer ID and profile permanent ID.
   * (profiles.get)
   *
   * @param string $name Required. Format:
   * customers/{customer_id}/profiles/{profile_permanent_id}
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1ChromeBrowserProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleChromeManagementVersionsV1ChromeBrowserProfile::class);
  }
  /**
   * Lists Chrome browser profiles of a customer based on the given search and
   * sorting criteria. (profiles.listCustomersProfiles)
   *
   * @param string $parent Required. Format: customers/{customer_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter used to filter profiles. The
   * following fields can be used in the filter: - profile_id - display_name -
   * user_email - last_activity_time - last_policy_sync_time -
   * last_status_report_time - first_enrollment_time - os_platform_type -
   * os_version - browser_version - browser_channel - policy_count -
   * extension_count - identity_provider - affiliation_state - os_platform_version
   * - ouId Any of the above fields can be used to specify a filter, and filtering
   * by multiple fields is supported with AND operator. String type fields and
   * enum type fields support '=' and '!=' operators. The integer type and the
   * timestamp type fields support '=', '!=', '<', '>', '<=' and '>=' operators.
   * Timestamps expect an RFC-3339 formatted string (e.g.
   * 2012-04-21T11:30:00-04:00). Wildcard '*' can be used with a string type field
   * filter. In addition, string literal filtering is also supported, for example,
   * 'ABC' as a filter maps to a filter that checks if any of the filterable
   * string type fields contains 'ABC'. Organization unit number can be used as a
   * filtering criteria here by specifying 'ouId = ${your_org_unit_id}', please
   * note that only single OU ID matching is supported.
   * @opt_param string orderBy Optional. The fields used to specify the ordering
   * of the results. The supported fields are: - profile_id - display_name -
   * user_email - last_activity_time - last_policy_sync_time -
   * last_status_report_time - first_enrollment_time - os_platform_type -
   * os_version - browser_version - browser_channel - policy_count -
   * extension_count - identity_provider - affiliation_state - os_platform_version
   * By default, sorting is in ascending order, to specify descending order for a
   * field, a suffix " desc" should be added to the field name. The default
   * ordering is the descending order of last_status_report_time.
   * @opt_param int pageSize Optional. The maximum number of profiles to return.
   * The default page size is 100 if page_size is unspecified, and the maximum
   * page size allowed is 200.
   * @opt_param string pageToken Optional. The page token used to retrieve a
   * specific page of the listing request.
   * @return GoogleChromeManagementVersionsV1ListChromeBrowserProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomersProfiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleChromeManagementVersionsV1ListChromeBrowserProfilesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersProfiles::class, 'Google_Service_ChromeManagement_Resource_CustomersProfiles');
