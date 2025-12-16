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

namespace Google\Service\ContainerAnalysis\Resource;

use Google\Service\ContainerAnalysis\ExportSBOMRequest;
use Google\Service\ContainerAnalysis\ExportSBOMResponse;

/**
 * The "resources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $containeranalysisService = new Google\Service\ContainerAnalysis(...);
 *   $resources = $containeranalysisService->projects_locations_resources;
 *  </code>
 */
class ProjectsLocationsResources extends \Google\Service\Resource
{
  /**
   * Generates an SBOM for the given resource. (resources.exportSBOM)
   *
   * @param string $name Required. The name of the resource in the form of
   * `projects/[PROJECT_ID]/resources/[RESOURCE_URL]`.
   * @param ExportSBOMRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExportSBOMResponse
   * @throws \Google\Service\Exception
   */
  public function exportSBOM($name, ExportSBOMRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportSBOM', [$params], ExportSBOMResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsResources::class, 'Google_Service_ContainerAnalysis_Resource_ProjectsLocationsResources');
