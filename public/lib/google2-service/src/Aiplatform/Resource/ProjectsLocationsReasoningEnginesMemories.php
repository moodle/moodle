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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1GenerateMemoriesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListMemoriesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Memory;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1PurgeMemoriesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1RetrieveMemoriesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1RetrieveMemoriesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1RollbackMemoryRequest;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "memories" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $memories = $aiplatformService->projects_locations_reasoningEngines_memories;
 *  </code>
 */
class ProjectsLocationsReasoningEnginesMemories extends \Google\Service\Resource
{
  /**
   * Create a Memory. (memories.create)
   *
   * @param string $parent Required. The resource name of the ReasoningEngine to
   * create the Memory under. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1Memory $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Memory $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Delete a Memory. (memories.delete)
   *
   * @param string $name Required. The resource name of the Memory to delete.
   * Format: `projects/{project}/locations/{location}/reasoningEngines/{reasoning_
   * engine}/memories/{memory}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Generate memories. (memories.generate)
   *
   * @param string $parent Required. The resource name of the ReasoningEngine to
   * generate memories for. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1GenerateMemoriesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function generate($parent, GoogleCloudAiplatformV1GenerateMemoriesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generate', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Get a Memory. (memories.get)
   *
   * @param string $name Required. The resource name of the Memory. Format: `proje
   * cts/{project}/locations/{location}/reasoningEngines/{reasoning_engine}/memori
   * es/{memory}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Memory
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Memory::class);
  }
  /**
   * List Memories. (memories.listProjectsLocationsReasoningEnginesMemories)
   *
   * @param string $parent Required. The resource name of the ReasoningEngine to
   * list the Memories under. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The standard list filter. More detail in
   * [AIP-160](https://google.aip.dev/160). Supported fields: * `scope` (as a JSON
   * string with equality match only) * `topics` (i.e.
   * `topics.custom_memory_topic_label: "example topic" OR
   * topics.managed_memory_topic: USER_PREFERENCES`)
   * @opt_param string orderBy Optional. The standard list order by string. If not
   * specified, the default order is `create_time desc`. If specified, the default
   * sorting order of provided fields is ascending. More detail in
   * [AIP-132](https://google.aip.dev/132). Supported fields: * `create_time` *
   * `update_time`
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token.
   * @return GoogleCloudAiplatformV1ListMemoriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsReasoningEnginesMemories($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListMemoriesResponse::class);
  }
  /**
   * Update a Memory. (memories.patch)
   *
   * @param string $name Identifier. The resource name of the Memory. Format: `pro
   * jects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}/memo
   * ries/{memory}`
   * @param GoogleCloudAiplatformV1Memory $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Mask specifying which fields to
   * update. Supported fields: * `display_name` * `description` * `fact`
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Memory $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Purge memories. (memories.purge)
   *
   * @param string $parent Required. The resource name of the ReasoningEngine to
   * purge memories from. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1PurgeMemoriesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function purge($parent, GoogleCloudAiplatformV1PurgeMemoriesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('purge', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Retrieve memories. (memories.retrieve)
   *
   * @param string $parent Required. The resource name of the ReasoningEngine to
   * retrieve memories from. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1RetrieveMemoriesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1RetrieveMemoriesResponse
   * @throws \Google\Service\Exception
   */
  public function retrieve($parent, GoogleCloudAiplatformV1RetrieveMemoriesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('retrieve', [$params], GoogleCloudAiplatformV1RetrieveMemoriesResponse::class);
  }
  /**
   * Rollback Memory to a specific revision. (memories.rollback)
   *
   * @param string $name Required. The resource name of the Memory to rollback.
   * Format: `projects/{project}/locations/{location}/reasoningEngines/{reasoning_
   * engine}/memories/{memory}`
   * @param GoogleCloudAiplatformV1RollbackMemoryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function rollback($name, GoogleCloudAiplatformV1RollbackMemoryRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rollback', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsReasoningEnginesMemories::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsReasoningEnginesMemories');
