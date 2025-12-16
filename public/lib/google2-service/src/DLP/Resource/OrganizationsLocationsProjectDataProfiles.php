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

namespace Google\Service\DLP\Resource;

use Google\Service\DLP\GooglePrivacyDlpV2ListProjectDataProfilesResponse;
use Google\Service\DLP\GooglePrivacyDlpV2ProjectDataProfile;

/**
 * The "projectDataProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dlpService = new Google\Service\DLP(...);
 *   $projectDataProfiles = $dlpService->organizations_locations_projectDataProfiles;
 *  </code>
 */
class OrganizationsLocationsProjectDataProfiles extends \Google\Service\Resource
{
  /**
   * Gets a project data profile. (projectDataProfiles.get)
   *
   * @param string $name Required. Resource name, for example
   * `organizations/12345/locations/us/projectDataProfiles/53234423`.
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2ProjectDataProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GooglePrivacyDlpV2ProjectDataProfile::class);
  }
  /**
   * Lists project data profiles for an organization.
   * (projectDataProfiles.listOrganizationsLocationsProjectDataProfiles)
   *
   * @param string $parent Required. organizations/{org_id}/locations/{loc_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Allows filtering. Supported syntax: * Filter
   * expressions are made up of one or more restrictions. * Restrictions can be
   * combined by `AND` or `OR` logical operators. A sequence of restrictions
   * implicitly uses `AND`. * A restriction has the form of `{field} {operator}
   * {value}`. * Supported fields: - `project_id`: the Google Cloud project ID -
   * `sensitivity_level`: HIGH|MODERATE|LOW - `data_risk_level`: HIGH|MODERATE|LOW
   * - `status_code`: an RPC status code as defined in
   * https://github.com/googleapis/googleapis/blob/master/google/rpc/code.proto -
   * `profile_last_generated`: Date and time the profile was last generated * The
   * operator must be `=` or `!=`. The `profile_last_generated` filter also
   * supports `<` and `>`. The syntax is based on https://google.aip.dev/160.
   * Examples: * `project_id = 12345 AND status_code = 1` * `project_id = 12345
   * AND sensitivity_level = HIGH` * `profile_last_generated <
   * "2025-01-01T00:00:00.000Z"` The length of this field should be no more than
   * 500 characters.
   * @opt_param string orderBy Comma-separated list of fields to order by,
   * followed by `asc` or `desc` postfix. This list is case insensitive. The
   * default sorting order is ascending. Redundant space characters are
   * insignificant. Only one order field at a time is allowed. Examples: *
   * `project_id` * `sensitivity_level desc` Supported fields: - `project_id`:
   * Google Cloud project ID - `sensitivity_level`: How sensitive the data in a
   * project is, at most - `data_risk_level`: How much risk is associated with
   * this data - `profile_last_generated`: Date and time (in epoch seconds) the
   * profile was last generated
   * @opt_param int pageSize Size of the page. This value can be limited by the
   * server. If zero, server returns a page of max size 100.
   * @opt_param string pageToken Page token to continue retrieval.
   * @return GooglePrivacyDlpV2ListProjectDataProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsProjectDataProfiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GooglePrivacyDlpV2ListProjectDataProfilesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsProjectDataProfiles::class, 'Google_Service_DLP_Resource_OrganizationsLocationsProjectDataProfiles');
