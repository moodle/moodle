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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListMemoryRevisionsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1MemoryRevision;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $revisions = $aiplatformService->projects_locations_reasoningEngines_memories_revisions;
 *  </code>
 */
class ProjectsLocationsReasoningEnginesMemoriesRevisions extends \Google\Service\Resource
{
  /**
   * Get a Memory Revision. (revisions.get)
   *
   * @param string $name Required. The resource name of the Memory Revision to
   * retrieve. Format: `projects/{project}/locations/{location}/reasoningEngines/{
   * reasoning_engine}/memories/{memory}/revisions/{revision}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1MemoryRevision
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1MemoryRevision::class);
  }
  /**
   * List Memory Revisions for a Memory.
   * (revisions.listProjectsLocationsReasoningEnginesMemoriesRevisions)
   *
   * @param string $parent Required. The resource name of the Memory to list
   * revisions for. Format: `projects/{project}/locations/{location}/reasoningEngi
   * nes/{reasoning_engine}/memories/{memory}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The standard list filter. More detail in
   * [AIP-160](https://google.aip.dev/160). Supported fields (equality match
   * only): * `labels`
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token.
   * @return GoogleCloudAiplatformV1ListMemoryRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsReasoningEnginesMemoriesRevisions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListMemoryRevisionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsReasoningEnginesMemoriesRevisions::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsReasoningEnginesMemoriesRevisions');
