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

use Google\Service\DLP\GooglePrivacyDlpV2FileStoreDataProfile;
use Google\Service\DLP\GooglePrivacyDlpV2ListFileStoreDataProfilesResponse;
use Google\Service\DLP\GoogleProtobufEmpty;

/**
 * The "fileStoreDataProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dlpService = new Google\Service\DLP(...);
 *   $fileStoreDataProfiles = $dlpService->organizations_locations_fileStoreDataProfiles;
 *  </code>
 */
class OrganizationsLocationsFileStoreDataProfiles extends \Google\Service\Resource
{
  /**
   * Delete a FileStoreDataProfile. Will not prevent the profile from being
   * regenerated if the resource is still included in a discovery configuration.
   * (fileStoreDataProfiles.delete)
   *
   * @param string $name Required. Resource name of the file store data profile.
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
   * Gets a file store data profile. (fileStoreDataProfiles.get)
   *
   * @param string $name Required. Resource name, for example
   * `organizations/12345/locations/us/fileStoreDataProfiles/53234423`.
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2FileStoreDataProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GooglePrivacyDlpV2FileStoreDataProfile::class);
  }
  /**
   * Lists file store data profiles for an organization.
   * (fileStoreDataProfiles.listOrganizationsLocationsFileStoreDataProfiles)
   *
   * @param string $parent Required. Resource name of the organization or project,
   * for example `organizations/433245324/locations/europe` or `projects/project-
   * id/locations/asia`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Allows filtering. Supported syntax: *
   * Filter expressions are made up of one or more restrictions. * Restrictions
   * can be combined by `AND` or `OR` logical operators. A sequence of
   * restrictions implicitly uses `AND`. * A restriction has the form of `{field}
   * {operator} {value}`. * Supported fields: - `project_id`: The Google Cloud
   * project ID - `account_id`: The AWS account ID - `file_store_path`: The path
   * like "gs://bucket" - `data_source_type`: The profile's data source type, like
   * "google/storage/bucket" - `data_storage_location`: The location where the
   * file store's data is stored, like "us-central1" - `sensitivity_level`:
   * HIGH|MODERATE|LOW - `data_risk_level`: HIGH|MODERATE|LOW -
   * `resource_visibility`: PUBLIC|RESTRICTED - `status_code`: an RPC status code
   * as defined in
   * https://github.com/googleapis/googleapis/blob/master/google/rpc/code.proto -
   * `profile_last_generated`: Date and time the profile was last generated * The
   * operator must be `=` or `!=`. The `profile_last_generated` filter also
   * supports `<` and `>`. The syntax is based on https://google.aip.dev/160.
   * Examples: * `project_id = 12345 AND status_code = 1` * `project_id = 12345
   * AND sensitivity_level = HIGH` * `project_id = 12345 AND resource_visibility =
   * PUBLIC` * `file_store_path = "gs://mybucket"` * `profile_last_generated <
   * "2025-01-01T00:00:00.000Z"` The length of this field should be no more than
   * 500 characters.
   * @opt_param string orderBy Optional. Comma-separated list of fields to order
   * by, followed by `asc` or `desc` postfix. This list is case insensitive. The
   * default sorting order is ascending. Redundant space characters are
   * insignificant. Only one order field at a time is allowed. Examples: *
   * `project_id asc` * `name` * `sensitivity_level desc` Supported fields are: -
   * `project_id`: The Google Cloud project ID. - `sensitivity_level`: How
   * sensitive the data in a table is, at most. - `data_risk_level`: How much risk
   * is associated with this data. - `profile_last_generated`: When the profile
   * was last updated in epoch seconds. - `last_modified`: The last time the
   * resource was modified. - `resource_visibility`: Visibility restriction for
   * this resource. - `name`: The name of the profile. - `create_time`: The time
   * the file store was first created.
   * @opt_param int pageSize Optional. Size of the page. This value can be limited
   * by the server. If zero, server returns a page of max size 100.
   * @opt_param string pageToken Optional. Page token to continue retrieval.
   * @return GooglePrivacyDlpV2ListFileStoreDataProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsFileStoreDataProfiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GooglePrivacyDlpV2ListFileStoreDataProfilesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsFileStoreDataProfiles::class, 'Google_Service_DLP_Resource_OrganizationsLocationsFileStoreDataProfiles');
