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

namespace Google\Service\SecurityPosture\Resource;

use Google\Service\SecurityPosture\ListPostureDeploymentsResponse;
use Google\Service\SecurityPosture\Operation;
use Google\Service\SecurityPosture\PostureDeployment;

/**
 * The "postureDeployments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitypostureService = new Google\Service\SecurityPosture(...);
 *   $postureDeployments = $securitypostureService->organizations_locations_postureDeployments;
 *  </code>
 */
class OrganizationsLocationsPostureDeployments extends \Google\Service\Resource
{
  /**
   * Creates a new PostureDeployment in a given project and location.
   * (postureDeployments.create)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param PostureDeployment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string postureDeploymentId Required. An identifier for the posture
   * deployment.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, PostureDeployment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a PostureDeployment. (postureDeployments.delete)
   *
   * @param string $name Required. The name of the posture deployment, in the
   * format `organizations/{organization}/locations/global/postureDeployments/{pos
   * ture_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. An opaque identifier for the current version
   * of the posture deployment. If you provide this value, then it must match the
   * existing value. If the values don't match, then the request fails with an
   * ABORTED error. If you omit this value, then the posture deployment is deleted
   * regardless of its current `etag` value.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details for a PostureDeployment. (postureDeployments.get)
   *
   * @param string $name Required. The name of the PostureDeployment, in the
   * format `organizations/{organization}/locations/global/postureDeployments/{pos
   * ture_deployment_id}`.
   * @param array $optParams Optional parameters.
   * @return PostureDeployment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PostureDeployment::class);
  }
  /**
   * Lists every PostureDeployment in a project and location.
   * (postureDeployments.listOrganizationsLocationsPostureDeployments)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to apply to the list of postures,
   * in the format defined in [AIP-160: Filtering](https://google.aip.dev/160).
   * @opt_param int pageSize Optional. The maximum number of posture deployments
   * to return. The default value is `500`. If you exceed the maximum value of
   * `1000`, then the service uses the maximum value.
   * @opt_param string pageToken Optional. A pagination token returned from a
   * previous request to list posture deployments. Provide this token to retrieve
   * the next page of results.
   * @return ListPostureDeploymentsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsPostureDeployments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPostureDeploymentsResponse::class);
  }
  /**
   * Updates an existing PostureDeployment. To prevent concurrent updates from
   * overwriting each other, always follow the read-modify-write pattern when you
   * update a posture deployment: 1. Call GetPostureDeployment to get the current
   * version of the deployment. 2. Update the fields in the deployment as needed.
   * 3. Call UpdatePostureDeployment to update the deployment. Ensure that your
   * request includes the `etag` value from the GetPostureDeployment response.
   * **Important:** If you omit the `etag` when you call UpdatePostureDeployment,
   * then the updated deployment unconditionally overwrites the existing
   * deployment. (postureDeployments.patch)
   *
   * @param string $name Required. Identifier. The name of the posture deployment,
   * in the format `organizations/{organization}/locations/global/postureDeploymen
   * ts/{deployment_id}`.
   * @param PostureDeployment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The fields in the PostureDeployment to
   * update. You can update only the following fields: *
   * PostureDeployment.posture_id * PostureDeployment.posture_revision_id
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, PostureDeployment $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsPostureDeployments::class, 'Google_Service_SecurityPosture_Resource_OrganizationsLocationsPostureDeployments');
