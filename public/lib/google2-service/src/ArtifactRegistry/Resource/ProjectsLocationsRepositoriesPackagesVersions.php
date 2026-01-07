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

namespace Google\Service\ArtifactRegistry\Resource;

use Google\Service\ArtifactRegistry\BatchDeleteVersionsRequest;
use Google\Service\ArtifactRegistry\ListVersionsResponse;
use Google\Service\ArtifactRegistry\Operation;
use Google\Service\ArtifactRegistry\Version;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $artifactregistryService = new Google\Service\ArtifactRegistry(...);
 *   $versions = $artifactregistryService->projects_locations_repositories_packages_versions;
 *  </code>
 */
class ProjectsLocationsRepositoriesPackagesVersions extends \Google\Service\Resource
{
  /**
   * Deletes multiple versions across a repository. The returned operation will
   * complete once the versions have been deleted. (versions.batchDelete)
   *
   * @param string $parent The name of the repository holding all requested
   * versions.
   * @param BatchDeleteVersionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function batchDelete($parent, BatchDeleteVersionsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params], Operation::class);
  }
  /**
   * Deletes a version and all of its content. The returned operation will
   * complete once the version has been deleted. (versions.delete)
   *
   * @param string $name The name of the version to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force By default, a version that is tagged may not be
   * deleted. If force=true, the version and any tags pointing to the version are
   * deleted.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets a version (versions.get)
   *
   * @param string $name The name of the version to retrieve.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view The view that should be returned in the response.
   * @return Version
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Version::class);
  }
  /**
   * Lists versions. (versions.listProjectsLocationsRepositoriesPackagesVersions)
   *
   * @param string $parent The name of the parent resource whose versions will be
   * listed.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. Filter rules are case insensitive. The fields eligible for
   * filtering are: * `name` * `annotations` Examples of using a filter: To filter
   * the results of your request to versions with the name `my-version` in project
   * `my-project` in the `us-central` region, in repository `my-repo`, append the
   * following filter expression to your request: * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/packages/my-
   * package/versions/my-version"` You can also use wildcards to match any number
   * of characters before or after the value: * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/packages/my-
   * package/versionsversion"` * `name="projects/my-project/locations/us-
   * central1/repositories/my-repo/packages/my-package/versions/my*"` *
   * `name="projects/my-project/locations/us-central1/repositories/my-
   * repo/packages/my-package/versionsversion*"` To filter the results of your
   * request to versions with the annotation key-value pair [`external_link`:
   * `external_link_value`], append the following filter expression to your
   * request: * `"annotations.external_link:external_link_value"` To filter just
   * for a specific annotation key `external_link`, append the following filter
   * expression to your request: * `"annotations.external_link"` If the annotation
   * key or value contains special characters, you can escape them by surrounding
   * the value with backticks. For example, to filter the results of your request
   * to versions with the annotation key-value pair
   * [`external.link`:`https://example.com/my-version`], append the following
   * filter expression to your request: * ``
   * "annotations.`external.link`:`https://example.com/my-version`" `` You can
   * also filter with annotations with a wildcard to match any number of
   * characters before or after the value: * ``
   * "annotations.*_link:`*example.com*`" ``
   * @opt_param string orderBy Optional. The field to order the results by.
   * @opt_param int pageSize The maximum number of versions to return. Maximum
   * page size is 1,000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request, if any.
   * @opt_param string view The view that should be returned in the response.
   * @return ListVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesPackagesVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListVersionsResponse::class);
  }
  /**
   * Updates a version. (versions.patch)
   *
   * @param string $name The name of the version, for example:
   * `projects/p1/locations/us-
   * central1/repositories/repo1/packages/pkg1/versions/art1`. If the package or
   * version ID parts contain slashes, the slashes are escaped.
   * @param Version $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The update mask applies to the resource. For the
   * `FieldMask` definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   * @return Version
   * @throws \Google\Service\Exception
   */
  public function patch($name, Version $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Version::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesPackagesVersions::class, 'Google_Service_ArtifactRegistry_Resource_ProjectsLocationsRepositoriesPackagesVersions');
