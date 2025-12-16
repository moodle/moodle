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

use Google\Service\ArtifactRegistry\UploadGoModuleMediaResponse;
use Google\Service\ArtifactRegistry\UploadGoModuleRequest;

/**
 * The "goModules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $artifactregistryService = new Google\Service\ArtifactRegistry(...);
 *   $goModules = $artifactregistryService->projects_locations_repositories_goModules;
 *  </code>
 */
class ProjectsLocationsRepositoriesGoModules extends \Google\Service\Resource
{
  /**
   * Directly uploads a Go module. The returned Operation will complete once the
   * Go module is uploaded. Package, Version, and File resources are created based
   * on the uploaded Go module. (goModules.upload)
   *
   * @param string $parent The resource name of the repository where the Go module
   * will be uploaded.
   * @param UploadGoModuleRequest $postBody
   * @param array $optParams Optional parameters.
   * @return UploadGoModuleMediaResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, UploadGoModuleRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], UploadGoModuleMediaResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesGoModules::class, 'Google_Service_ArtifactRegistry_Resource_ProjectsLocationsRepositoriesGoModules');
