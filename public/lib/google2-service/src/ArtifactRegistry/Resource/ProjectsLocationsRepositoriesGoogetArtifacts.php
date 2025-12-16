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

use Google\Service\ArtifactRegistry\ImportGoogetArtifactsRequest;
use Google\Service\ArtifactRegistry\Operation;
use Google\Service\ArtifactRegistry\UploadGoogetArtifactMediaResponse;
use Google\Service\ArtifactRegistry\UploadGoogetArtifactRequest;

/**
 * The "googetArtifacts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $artifactregistryService = new Google\Service\ArtifactRegistry(...);
 *   $googetArtifacts = $artifactregistryService->projects_locations_repositories_googetArtifacts;
 *  </code>
 */
class ProjectsLocationsRepositoriesGoogetArtifacts extends \Google\Service\Resource
{
  /**
   * Imports GooGet artifacts. The returned Operation will complete once the
   * resources are imported. Package, Version, and File resources are created
   * based on the imported artifacts. Imported artifacts that conflict with
   * existing resources are ignored. (googetArtifacts.import)
   *
   * @param string $parent The name of the parent resource where the artifacts
   * will be imported.
   * @param ImportGoogetArtifactsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function import($parent, ImportGoogetArtifactsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], Operation::class);
  }
  /**
   * Directly uploads a GooGet artifact. The returned Operation will complete once
   * the resources are uploaded. Package, Version, and File resources are created
   * based on the imported artifact. Imported artifacts that conflict with
   * existing resources are ignored. (googetArtifacts.upload)
   *
   * @param string $parent The name of the parent resource where the artifacts
   * will be uploaded.
   * @param UploadGoogetArtifactRequest $postBody
   * @param array $optParams Optional parameters.
   * @return UploadGoogetArtifactMediaResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, UploadGoogetArtifactRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], UploadGoogetArtifactMediaResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesGoogetArtifacts::class, 'Google_Service_ArtifactRegistry_Resource_ProjectsLocationsRepositoriesGoogetArtifacts');
