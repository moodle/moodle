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

namespace Google\Service\GKEHub\Resource;

use Google\Service\GKEHub\GkehubNamespace;
use Google\Service\GKEHub\ListScopeNamespacesResponse;
use Google\Service\GKEHub\Operation;

/**
 * The "namespaces" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkehubService = new Google\Service\GKEHub(...);
 *   $namespaces = $gkehubService->projects_locations_scopes_namespaces;
 *  </code>
 */
class ProjectsLocationsScopesNamespaces extends \Google\Service\Resource
{
  /**
   * Creates a fleet namespace. (namespaces.create)
   *
   * @param string $parent Required. The parent (project and location) where the
   * Namespace will be created. Specified in the format
   * `projects/locations/scopes`.
   * @param GkehubNamespace $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string scopeNamespaceId Required. Client chosen ID for the
   * Namespace. `namespace_id` must be a valid RFC 1123 compliant DNS label: 1. At
   * most 63 characters in length 2. It must consist of lower case alphanumeric
   * characters or `-` 3. It must start and end with an alphanumeric character
   * Which can be expressed as the regex: `[a-z0-9]([-a-z0-9]*[a-z0-9])?`, with a
   * maximum length of 63 characters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GkehubNamespace $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a fleet namespace. (namespaces.delete)
   *
   * @param string $name Required. The Namespace resource name in the format
   * `projects/locations/scopes/namespaces`.
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
   * Returns the details of a fleet namespace. (namespaces.get)
   *
   * @param string $name Required. The Namespace resource name in the format
   * `projects/locations/scopes/namespaces`.
   * @param array $optParams Optional parameters.
   * @return GkehubNamespace
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GkehubNamespace::class);
  }
  /**
   * Lists fleet namespaces. (namespaces.listProjectsLocationsScopesNamespaces)
   *
   * @param string $parent Required. The parent (project and location) where the
   * Features will be listed. Specified in the format `projects/locations/scopes`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. When requesting a 'page' of resources,
   * `page_size` specifies number of resources to return. If unspecified or set to
   * 0, all resources will be returned.
   * @opt_param string pageToken Optional. Token returned by previous call to
   * `ListFeatures` which specifies the position in the list from where to
   * continue listing the resources.
   * @return ListScopeNamespacesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsScopesNamespaces($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListScopeNamespacesResponse::class);
  }
  /**
   * Updates a fleet namespace. (namespaces.patch)
   *
   * @param string $name The resource name for the namespace
   * `projects/{project}/locations/{location}/namespaces/{namespace}`
   * @param GkehubNamespace $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GkehubNamespace $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsScopesNamespaces::class, 'Google_Service_GKEHub_Resource_ProjectsLocationsScopesNamespaces');
