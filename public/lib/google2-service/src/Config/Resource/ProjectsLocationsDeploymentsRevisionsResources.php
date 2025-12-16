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

namespace Google\Service\Config\Resource;

use Google\Service\Config\ConfigResource;
use Google\Service\Config\ListResourcesResponse;

/**
 * The "resources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $configService = new Google\Service\Config(...);
 *   $resources = $configService->projects_locations_deployments_revisions_resources;
 *  </code>
 */
class ProjectsLocationsDeploymentsRevisionsResources extends \Google\Service\Resource
{
  /**
   * Gets details about a Resource deployed by Infra Manager. (resources.get)
   *
   * @param string $name Required. The name of the Resource in the format: 'projec
   * ts/{project_id}/locations/{location}/deployments/{deployment}/revisions/{revi
   * sion}/resource/{resource}'.
   * @param array $optParams Optional parameters.
   * @return ConfigResource
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ConfigResource::class);
  }
  /**
   * Lists Resources in a given revision.
   * (resources.listProjectsLocationsDeploymentsRevisionsResources)
   *
   * @param string $parent Required. The parent in whose context the Resources are
   * listed. The parent value is in the format: 'projects/{project_id}/locations/{
   * location}/deployments/{deployment}/revisions/{revision}'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the Resources that match the filter
   * expression. A filter expression filters the resources listed in the response.
   * The expression must be of the form '{field} {operator} {value}' where
   * operators: '<', '>', '<=', '>=', '!=', '=', ':' are supported (colon ':'
   * represents a HAS operator which is roughly synonymous with equality). {field}
   * can refer to a proto or JSON field, or a synthetic field. Field names can be
   * camelCase or snake_case. Examples: - Filter by name: name =
   * "projects/foo/locations/us-
   * central1/deployments/dep/revisions/bar/resources/baz
   * @opt_param string orderBy Field to use to sort the list.
   * @opt_param int pageSize When requesting a page of resources, 'page_size'
   * specifies number of resources to return. If unspecified, at most 500 will be
   * returned. The maximum value is 1000.
   * @opt_param string pageToken Token returned by previous call to
   * 'ListResources' which specifies the position in the list from where to
   * continue listing the resources.
   * @return ListResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeploymentsRevisionsResources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeploymentsRevisionsResources::class, 'Google_Service_Config_Resource_ProjectsLocationsDeploymentsRevisionsResources');
