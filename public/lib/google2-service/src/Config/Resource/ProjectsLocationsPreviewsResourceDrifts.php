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

use Google\Service\Config\ListResourceDriftsResponse;
use Google\Service\Config\ResourceDrift;

/**
 * The "resourceDrifts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $configService = new Google\Service\Config(...);
 *   $resourceDrifts = $configService->projects_locations_previews_resourceDrifts;
 *  </code>
 */
class ProjectsLocationsPreviewsResourceDrifts extends \Google\Service\Resource
{
  /**
   * Get a ResourceDrift for a given preview. (resourceDrifts.get)
   *
   * @param string $name Required. The name of the resource drift to retrieve.
   * Format: 'projects/{project_id}/locations/{location}/previews/{preview}/resour
   * ceDrifts/{resource_drift}'.
   * @param array $optParams Optional parameters.
   * @return ResourceDrift
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ResourceDrift::class);
  }
  /**
   * List ResourceDrifts for a given preview.
   * (resourceDrifts.listProjectsLocationsPreviewsResourceDrifts)
   *
   * @param string $parent Required. The parent in whose context the
   * ResourceDrifts are listed. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}/previews/{preview}'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Lists the resource drifts that match the
   * filter expression. A filter expression filters the resource drifts listed in
   * the response. The expression must be of the form '{field} {operator} {value}'
   * where operators: '<', '>', '<=', '>=', '!=', '=', ':' are supported (colon
   * ':' represents a HAS operator which is roughly synonymous with equality).
   * {field} can refer to a proto or JSON field, or a synthetic field. Field names
   * can be camelCase or snake_case. Examples: - Filter by name: name =
   * "projects/foo/locations/us-central1/previews/dep/resourceDrifts/baz
   * @opt_param string orderBy Optional. Field to use to sort the list.
   * @opt_param int pageSize Optional. When requesting a page of resource drifts,
   * 'page_size' specifies number of resource drifts to return. If unspecified, at
   * most 500 will be returned. The maximum value is 1000.
   * @opt_param string pageToken Optional. Token returned by previous call to
   * 'ListResourceDrifts' which specifies the position in the list from where to
   * continue listing the resource drifts.
   * @return ListResourceDriftsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPreviewsResourceDrifts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListResourceDriftsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPreviewsResourceDrifts::class, 'Google_Service_Config_Resource_ProjectsLocationsPreviewsResourceDrifts');
