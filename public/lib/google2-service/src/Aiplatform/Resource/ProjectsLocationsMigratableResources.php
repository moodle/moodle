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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchMigrateResourcesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SearchMigratableResourcesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SearchMigratableResourcesResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "migratableResources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $migratableResources = $aiplatformService->projects_locations_migratableResources;
 *  </code>
 */
class ProjectsLocationsMigratableResources extends \Google\Service\Resource
{
  /**
   * Batch migrates resources from ml.googleapis.com, automl.googleapis.com, and
   * datalabeling.googleapis.com to Vertex AI. (migratableResources.batchMigrate)
   *
   * @param string $parent Required. The location of the migrated resource will
   * live in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1BatchMigrateResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchMigrate($parent, GoogleCloudAiplatformV1BatchMigrateResourcesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchMigrate', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Searches all of the resources in automl.googleapis.com,
   * datalabeling.googleapis.com and ml.googleapis.com that can be migrated to
   * Vertex AI's given location. (migratableResources.search)
   *
   * @param string $parent Required. The location that the migratable resources
   * should be searched from. It's the Vertex AI location that the resources can
   * be migrated to, not the resources' original location. Format:
   * `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1SearchMigratableResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1SearchMigratableResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, GoogleCloudAiplatformV1SearchMigratableResourcesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GoogleCloudAiplatformV1SearchMigratableResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMigratableResources::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsMigratableResources');
