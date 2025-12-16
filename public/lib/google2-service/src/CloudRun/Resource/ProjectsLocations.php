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

namespace Google\Service\CloudRun\Resource;

use Google\Service\CloudRun\GoogleCloudRunV2ExportImageRequest;
use Google\Service\CloudRun\GoogleCloudRunV2ExportImageResponse;
use Google\Service\CloudRun\GoogleCloudRunV2Metadata;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $runService = new Google\Service\CloudRun(...);
 *   $locations = $runService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Export image for a given resource. (locations.exportImage)
   *
   * @param string $name Required. The name of the resource of which image
   * metadata should be exported. Format: `projects/{project_id_or_number}/locatio
   * ns/{location}/services/{service}/revisions/{revision}` for Revision `projects
   * /{project_id_or_number}/locations/{location}/jobs/{job}/executions/{execution
   * }` for Execution
   * @param GoogleCloudRunV2ExportImageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2ExportImageResponse
   * @throws \Google\Service\Exception
   */
  public function exportImage($name, GoogleCloudRunV2ExportImageRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportImage', [$params], GoogleCloudRunV2ExportImageResponse::class);
  }
  /**
   * Export image metadata for a given resource. (locations.exportImageMetadata)
   *
   * @param string $name Required. The name of the resource of which image
   * metadata should be exported. Format: `projects/{project_id_or_number}/locatio
   * ns/{location}/services/{service}/revisions/{revision}` for Revision `projects
   * /{project_id_or_number}/locations/{location}/jobs/{job}/executions/{execution
   * }` for Execution
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2Metadata
   * @throws \Google\Service\Exception
   */
  public function exportImageMetadata($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('exportImageMetadata', [$params], GoogleCloudRunV2Metadata::class);
  }
  /**
   * Export generated customer metadata for a given resource.
   * (locations.exportMetadata)
   *
   * @param string $name Required. The name of the resource of which metadata
   * should be exported. Format:
   * `projects/{project_id_or_number}/locations/{location}/services/{service}` for
   * Service `projects/{project_id_or_number}/locations/{location}/services/{servi
   * ce}/revisions/{revision}` for Revision `projects/{project_id_or_number}/locat
   * ions/{location}/jobs/{job}/executions/{execution}` for Execution
   * {project_id_or_number} may contains domain-scoped project IDs
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2Metadata
   * @throws \Google\Service\Exception
   */
  public function exportMetadata($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('exportMetadata', [$params], GoogleCloudRunV2Metadata::class);
  }
  /**
   * Export generated customer metadata for a given project.
   * (locations.exportProjectMetadata)
   *
   * @param string $name Required. The name of the project of which metadata
   * should be exported. Format:
   * `projects/{project_id_or_number}/locations/{location}` for Project in a given
   * location.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2Metadata
   * @throws \Google\Service\Exception
   */
  public function exportProjectMetadata($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('exportProjectMetadata', [$params], GoogleCloudRunV2Metadata::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_CloudRun_Resource_ProjectsLocations');
