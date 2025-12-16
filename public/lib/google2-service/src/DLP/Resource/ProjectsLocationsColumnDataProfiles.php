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

use Google\Service\DLP\GooglePrivacyDlpV2ColumnDataProfile;
use Google\Service\DLP\GooglePrivacyDlpV2ListColumnDataProfilesResponse;

/**
 * The "columnDataProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dlpService = new Google\Service\DLP(...);
 *   $columnDataProfiles = $dlpService->projects_locations_columnDataProfiles;
 *  </code>
 */
class ProjectsLocationsColumnDataProfiles extends \Google\Service\Resource
{
  /**
   * Gets a column data profile. (columnDataProfiles.get)
   *
   * @param string $name Required. Resource name, for example
   * `organizations/12345/locations/us/columnDataProfiles/53234423`.
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2ColumnDataProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GooglePrivacyDlpV2ColumnDataProfile::class);
  }
  /**
   * Lists column data profiles for an organization.
   * (columnDataProfiles.listProjectsLocationsColumnDataProfiles)
   *
   * @param string $parent Required. Resource name of the organization or project,
   * for example `organizations/433245324/locations/europe` or `projects/project-
   * id/locations/asia`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Allows filtering. Supported syntax: * Filter
   * expressions are made up of one or more restrictions. * Restrictions can be
   * combined by `AND` or `OR` logical operators. A sequence of restrictions
   * implicitly uses `AND`. * A restriction has the form of `{field} {operator}
   * {value}`. * Supported fields: - `table_data_profile_name`: The name of the
   * related table data profile - `project_id`: The Google Cloud project ID
   * (REQUIRED) - `dataset_id`: The BigQuery dataset ID (REQUIRED) - `table_id`:
   * The BigQuery table ID (REQUIRED) - `field_id`: The ID of the BigQuery field -
   * `info_type`: The infotype detected in the resource - `sensitivity_level`:
   * HIGH|MEDIUM|LOW - `data_risk_level`: How much risk is associated with this
   * data - `status_code`: An RPC status code as defined in
   * https://github.com/googleapis/googleapis/blob/master/google/rpc/code.proto -
   * `profile_last_generated`: Date and time the profile was last generated * The
   * operator must be `=` for project_id, dataset_id, and table_id. Other filters
   * also support `!=`. The `profile_last_generated` filter also supports `<` and
   * `>`. The syntax is based on https://google.aip.dev/160. Examples: *
   * project_id = 12345 AND status_code = 1 * project_id = 12345 AND
   * sensitivity_level = HIGH * project_id = 12345 AND info_type = STREET_ADDRESS
   * * profile_last_generated < "2025-01-01T00:00:00.000Z" The length of this
   * field should be no more than 500 characters.
   * @opt_param string orderBy Comma-separated list of fields to order by,
   * followed by `asc` or `desc` postfix. This list is case insensitive. The
   * default sorting order is ascending. Redundant space characters are
   * insignificant. Only one order field at a time is allowed. Examples: *
   * `project_id asc` * `table_id` * `sensitivity_level desc` Supported fields
   * are: - `project_id`: The Google Cloud project ID. - `dataset_id`: The ID of a
   * BigQuery dataset. - `table_id`: The ID of a BigQuery table. -
   * `sensitivity_level`: How sensitive the data in a column is, at most. -
   * `data_risk_level`: How much risk is associated with this data. -
   * `profile_last_generated`: When the profile was last updated in epoch seconds.
   * @opt_param int pageSize Size of the page. This value can be limited by the
   * server. If zero, server returns a page of max size 100.
   * @opt_param string pageToken Page token to continue retrieval.
   * @return GooglePrivacyDlpV2ListColumnDataProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsColumnDataProfiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GooglePrivacyDlpV2ListColumnDataProfilesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsColumnDataProfiles::class, 'Google_Service_DLP_Resource_ProjectsLocationsColumnDataProfiles');
