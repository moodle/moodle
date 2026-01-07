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

use Google\Service\ArtifactRegistry\ListPackagesResponse;
use Google\Service\ArtifactRegistry\Operation;
use Google\Service\ArtifactRegistry\Package;

/**
 * The "packages" collection of methods.
 * Typical usage is:
 *  <code>
 *   $artifactregistryService = new Google\Service\ArtifactRegistry(...);
 *   $packages = $artifactregistryService->projects_locations_repositories_packages;
 *  </code>
 */
class ProjectsLocationsRepositoriesPackages extends \Google\Service\Resource
{
  /**
   * Deletes a package and all of its versions and tags. The returned operation
   * will complete once the package has been deleted. (packages.delete)
   *
   * @param string $name Required. The name of the package to delete.
   * @param array $optParams Optional parameters.
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
   * Gets a package. (packages.get)
   *
   * @param string $name Required. The name of the package to retrieve.
   * @param array $optParams Optional parameters.
   * @return Package
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Package::class);
  }
  /**
   * Lists packages. (packages.listProjectsLocationsRepositoriesPackages)
   *
   * @param string $parent Required. The name of the parent resource whose
   * packages will be listed.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. Filter rules are case insensitive. The fields eligible for
   * filtering are: * `name` * `annotations` Examples of using a filter: To filter
   * the results of your request to packages with the name `my-package` in project
   * `my-project` in the `us-central` region, in repository `my-repo`, append the
   * following filter expression to your request: * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/packages/my-package"` You
   * can also use wildcards to match any number of characters before or after the
   * value: * `name="projects/my-project/locations/us-central1/repositories/my-
   * repo/packages/my-*"` * `name="projects/my-project/locations/us-
   * central1/repositories/my-repo/packagespackage"` * `name="projects/my-
   * project/locations/us-central1/repositories/my-repo/packagespack*"` To filter
   * the results of your request to packages with the annotation key-value pair
   * [`external_link`: `external_link_value`], append the following filter
   * expression to your request": *
   * `"annotations.external_link:external_link_value"` To filter the results just
   * for a specific annotation key `external_link`, append the following filter
   * expression to your request: * `"annotations.external_link"` If the annotation
   * key or value contains special characters, you can escape them by surrounding
   * the value with backticks. For example, to filter the results of your request
   * to packages with the annotation key-value pair
   * [`external.link`:`https://example.com/my-package`], append the following
   * filter expression to your request: * ``
   * "annotations.`external.link`:`https://example.com/my-package`" `` You can
   * also filter with annotations with a wildcard to match any number of
   * characters before or after the value: * ``
   * "annotations.*_link:`*example.com*`" ``
   * @opt_param string orderBy Optional. The field to order the results by.
   * @opt_param int pageSize The maximum number of packages to return. Maximum
   * page size is 1,000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request, if any.
   * @return ListPackagesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesPackages($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPackagesResponse::class);
  }
  /**
   * Updates a package. (packages.patch)
   *
   * @param string $name The name of the package, for example:
   * `projects/p1/locations/us-central1/repositories/repo1/packages/pkg1`. If the
   * package ID part contains slashes, the slashes are escaped.
   * @param Package $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The update mask applies to the resource. For the
   * `FieldMask` definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   * @return Package
   * @throws \Google\Service\Exception
   */
  public function patch($name, Package $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Package::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesPackages::class, 'Google_Service_ArtifactRegistry_Resource_ProjectsLocationsRepositoriesPackages');
