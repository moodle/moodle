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

namespace Google\Service\FirebaseAppDistribution\Resource;

use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1BatchDeleteReleasesRequest;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1DistributeReleaseRequest;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1DistributeReleaseResponse;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1ListReleasesResponse;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1Release;
use Google\Service\FirebaseAppDistribution\GoogleProtobufEmpty;

/**
 * The "releases" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseappdistributionService = new Google\Service\FirebaseAppDistribution(...);
 *   $releases = $firebaseappdistributionService->projects_apps_releases;
 *  </code>
 */
class ProjectsAppsReleases extends \Google\Service\Resource
{
  /**
   * Deletes releases. A maximum of 100 releases can be deleted per request.
   * (releases.batchDelete)
   *
   * @param string $parent Required. The name of the app resource, which is the
   * parent of the release resources. Format:
   * `projects/{project_number}/apps/{app}`
   * @param GoogleFirebaseAppdistroV1BatchDeleteReleasesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function batchDelete($parent, GoogleFirebaseAppdistroV1BatchDeleteReleasesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Distributes a release to testers. This call does the following: 1. Creates
   * testers for the specified emails, if none exist. 2. Adds the testers and
   * groups to the release. 3. Sends new testers an invitation email. 4. Sends
   * existing testers a new release email. The request will fail with a
   * `INVALID_ARGUMENT` if it contains a group that doesn't exist.
   * (releases.distribute)
   *
   * @param string $name Required. The name of the release resource to distribute.
   * Format: `projects/{project_number}/apps/{app}/releases/{release}`
   * @param GoogleFirebaseAppdistroV1DistributeReleaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppdistroV1DistributeReleaseResponse
   * @throws \Google\Service\Exception
   */
  public function distribute($name, GoogleFirebaseAppdistroV1DistributeReleaseRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('distribute', [$params], GoogleFirebaseAppdistroV1DistributeReleaseResponse::class);
  }
  /**
   * Gets a release. (releases.get)
   *
   * @param string $name Required. The name of the release resource to retrieve.
   * Format: projects/{project_number}/apps/{app}/releases/{release}
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppdistroV1Release
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirebaseAppdistroV1Release::class);
  }
  /**
   * Lists releases. By default, sorts by `createTime` in descending order.
   * (releases.listProjectsAppsReleases)
   *
   * @param string $parent Required. The name of the app resource, which is the
   * parent of the release resources. Format:
   * `projects/{project_number}/apps/{app}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The expression to filter releases listed
   * in the response. To learn more about filtering, refer to [Google's AIP-160
   * standard](http://aip.dev/160). Supported fields: - `releaseNotes.text`
   * supports `=` (can contain a wildcard character (`*`) at the beginning or end
   * of the string) - `createTime` supports `<`, `<=`, `>` and `>=`, and expects
   * an RFC-3339 formatted string Examples: - `createTime <=
   * "2021-09-08T00:00:00+04:00"` - `releaseNotes.text="fixes" AND createTime >=
   * "2021-09-08T00:00:00.0Z"` - `releaseNotes.text="*v1.0.0-rc*"`
   * @opt_param string orderBy Optional. The fields used to order releases.
   * Supported fields: - `createTime` To specify descending order for a field,
   * append a "desc" suffix, for example, `createTime desc`. If this parameter is
   * not set, releases are ordered by `createTime` in descending order.
   * @opt_param int pageSize Optional. The maximum number of releases to return.
   * The service may return fewer than this value. The valid range is [1-100]; If
   * unspecified (0), at most 25 releases are returned. Values above 100 are
   * coerced to 100.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListReleases` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListReleases` must match the
   * call that provided the page token.
   * @return GoogleFirebaseAppdistroV1ListReleasesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsAppsReleases($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirebaseAppdistroV1ListReleasesResponse::class);
  }
  /**
   * Updates a release. (releases.patch)
   *
   * @param string $name The name of the release resource. Format:
   * `projects/{project_number}/apps/{app}/releases/{release}`
   * @param GoogleFirebaseAppdistroV1Release $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleFirebaseAppdistroV1Release
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleFirebaseAppdistroV1Release $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleFirebaseAppdistroV1Release::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsAppsReleases::class, 'Google_Service_FirebaseAppDistribution_Resource_ProjectsAppsReleases');
