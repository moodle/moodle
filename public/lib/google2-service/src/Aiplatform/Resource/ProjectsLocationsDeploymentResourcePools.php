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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1CreateDeploymentResourcePoolRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1DeploymentResourcePool;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListDeploymentResourcePoolsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1QueryDeployedModelsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "deploymentResourcePools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $deploymentResourcePools = $aiplatformService->projects_locations_deploymentResourcePools;
 *  </code>
 */
class ProjectsLocationsDeploymentResourcePools extends \Google\Service\Resource
{
  /**
   * Create a DeploymentResourcePool. (deploymentResourcePools.create)
   *
   * @param string $parent Required. The parent location resource where this
   * DeploymentResourcePool will be created. Format:
   * `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1CreateDeploymentResourcePoolRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1CreateDeploymentResourcePoolRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Delete a DeploymentResourcePool. (deploymentResourcePools.delete)
   *
   * @param string $name Required. The name of the DeploymentResourcePool to
   * delete. Format: `projects/{project}/locations/{location}/deploymentResourcePo
   * ols/{deployment_resource_pool}`
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
   * Get a DeploymentResourcePool. (deploymentResourcePools.get)
   *
   * @param string $name Required. The name of the DeploymentResourcePool to
   * retrieve. Format: `projects/{project}/locations/{location}/deploymentResource
   * Pools/{deployment_resource_pool}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1DeploymentResourcePool
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1DeploymentResourcePool::class);
  }
  /**
   * List DeploymentResourcePools in a location.
   * (deploymentResourcePools.listProjectsLocationsDeploymentResourcePools)
   *
   * @param string $parent Required. The parent Location which owns this
   * collection of DeploymentResourcePools. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of DeploymentResourcePools to
   * return. The service may return fewer than this value.
   * @opt_param string pageToken A page token, received from a previous
   * `ListDeploymentResourcePools` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListDeploymentResourcePools` must match the call that provided the page
   * token.
   * @return GoogleCloudAiplatformV1ListDeploymentResourcePoolsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeploymentResourcePools($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListDeploymentResourcePoolsResponse::class);
  }
  /**
   * Update a DeploymentResourcePool. (deploymentResourcePools.patch)
   *
   * @param string $name Immutable. The resource name of the
   * DeploymentResourcePool. Format: `projects/{project}/locations/{location}/depl
   * oymentResourcePools/{deployment_resource_pool}`
   * @param GoogleCloudAiplatformV1DeploymentResourcePool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1DeploymentResourcePool $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * List DeployedModels that have been deployed on this DeploymentResourcePool.
   * (deploymentResourcePools.queryDeployedModels)
   *
   * @param string $deploymentResourcePool Required. The name of the target
   * DeploymentResourcePool to query. Format: `projects/{project}/locations/{locat
   * ion}/deploymentResourcePools/{deployment_resource_pool}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of DeployedModels to return. The
   * service may return fewer than this value.
   * @opt_param string pageToken A page token, received from a previous
   * `QueryDeployedModels` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `QueryDeployedModels` must
   * match the call that provided the page token.
   * @return GoogleCloudAiplatformV1QueryDeployedModelsResponse
   * @throws \Google\Service\Exception
   */
  public function queryDeployedModels($deploymentResourcePool, $optParams = [])
  {
    $params = ['deploymentResourcePool' => $deploymentResourcePool];
    $params = array_merge($params, $optParams);
    return $this->call('queryDeployedModels', [$params], GoogleCloudAiplatformV1QueryDeployedModelsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeploymentResourcePools::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsDeploymentResourcePools');
