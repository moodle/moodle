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

namespace Google\Service\Dataform\Resource;

use Google\Service\Dataform\DataformEmpty;
use Google\Service\Dataform\ListWorkflowConfigsResponse;
use Google\Service\Dataform\WorkflowConfig;

/**
 * The "workflowConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataformService = new Google\Service\Dataform(...);
 *   $workflowConfigs = $dataformService->projects_locations_repositories_workflowConfigs;
 *  </code>
 */
class ProjectsLocationsRepositoriesWorkflowConfigs extends \Google\Service\Resource
{
  /**
   * Creates a new WorkflowConfig in a given Repository. (workflowConfigs.create)
   *
   * @param string $parent Required. The repository in which to create the
   * workflow config. Must be in the format `projects/locations/repositories`.
   * @param WorkflowConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string workflowConfigId Required. The ID to use for the workflow
   * config, which will become the final component of the workflow config's
   * resource name.
   * @return WorkflowConfig
   * @throws \Google\Service\Exception
   */
  public function create($parent, WorkflowConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], WorkflowConfig::class);
  }
  /**
   * Deletes a single WorkflowConfig. (workflowConfigs.delete)
   *
   * @param string $name Required. The workflow config's name.
   * @param array $optParams Optional parameters.
   * @return DataformEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DataformEmpty::class);
  }
  /**
   * Fetches a single WorkflowConfig. (workflowConfigs.get)
   *
   * @param string $name Required. The workflow config's name.
   * @param array $optParams Optional parameters.
   * @return WorkflowConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], WorkflowConfig::class);
  }
  /**
   * Lists WorkflowConfigs in a given Repository.
   * (workflowConfigs.listProjectsLocationsRepositoriesWorkflowConfigs)
   *
   * @param string $parent Required. The repository in which to list workflow
   * configs. Must be in the format `projects/locations/repositories`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of workflow configs to
   * return. The server may return fewer items than requested. If unspecified, the
   * server will pick an appropriate default.
   * @opt_param string pageToken Optional. Page token received from a previous
   * `ListWorkflowConfigs` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListWorkflowConfigs`, with
   * the exception of `page_size`, must match the call that provided the page
   * token.
   * @return ListWorkflowConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesWorkflowConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkflowConfigsResponse::class);
  }
  /**
   * Updates a single WorkflowConfig. **Note:** *This method does not fully
   * implement [AIP/134](https://google.aip.dev/134). The wildcard entry () is
   * treated as a bad request, and when the `field_mask` is omitted, the request
   * is treated as a full update on all modifiable fields.*
   * (workflowConfigs.patch)
   *
   * @param string $name Identifier. The workflow config's name.
   * @param WorkflowConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Specifies the fields to be updated in
   * the workflow config. If left unset, all fields will be updated.
   * @return WorkflowConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, WorkflowConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], WorkflowConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesWorkflowConfigs::class, 'Google_Service_Dataform_Resource_ProjectsLocationsRepositoriesWorkflowConfigs');
