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

use Google\Service\CloudRun\GoogleCloudRunV2ListWorkerPoolsResponse;
use Google\Service\CloudRun\GoogleCloudRunV2WorkerPool;
use Google\Service\CloudRun\GoogleIamV1Policy;
use Google\Service\CloudRun\GoogleIamV1SetIamPolicyRequest;
use Google\Service\CloudRun\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\CloudRun\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\CloudRun\GoogleLongrunningOperation;

/**
 * The "workerPools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $runService = new Google\Service\CloudRun(...);
 *   $workerPools = $runService->projects_locations_workerPools;
 *  </code>
 */
class ProjectsLocationsWorkerPools extends \Google\Service\Resource
{
  /**
   * Creates a new WorkerPool in a given project and location.
   * (workerPools.create)
   *
   * @param string $parent Required. The location and project in which this worker
   * pool should be created. Format: `projects/{project}/locations/{location}`,
   * where `{project}` can be project id or number. Only lowercase characters,
   * digits, and hyphens.
   * @param GoogleCloudRunV2WorkerPool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool validateOnly Optional. Indicates that the request should be
   * validated and default values populated, without persisting the request or
   * creating any resources.
   * @opt_param string workerPoolId Required. The unique identifier for the
   * WorkerPool. It must begin with letter, and cannot end with hyphen; must
   * contain fewer than 50 characters. The name of the worker pool becomes
   * `{parent}/workerPools/{worker_pool_id}`.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudRunV2WorkerPool $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a WorkerPool. (workerPools.delete)
   *
   * @param string $name Required. The full name of the WorkerPool. Format:
   * `projects/{project}/locations/{location}/workerPools/{worker_pool}`, where
   * `{project}` can be project id or number.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag A system-generated fingerprint for this version of the
   * resource. May be used to detect modification conflict during updates.
   * @opt_param bool validateOnly Optional. Indicates that the request should be
   * validated without actually deleting any resources.
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
   * Gets information about a WorkerPool. (workerPools.get)
   *
   * @param string $name Required. The full name of the WorkerPool. Format:
   * `projects/{project}/locations/{location}/workerPools/{worker_pool}`, where
   * `{project}` can be project id or number.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2WorkerPool
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudRunV2WorkerPool::class);
  }
  /**
   * Gets the IAM Access Control policy currently in effect for the given Cloud
   * Run WorkerPool. This result does not include any inherited policies.
   * (workerPools.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Lists WorkerPools. Results are sorted by creation time, descending.
   * (workerPools.listProjectsLocationsWorkerPools)
   *
   * @param string $parent Required. The location and project to list resources
   * on. Location must be a valid Google Cloud region, and cannot be the "-"
   * wildcard. Format: `projects/{project}/locations/{location}`, where
   * `{project}` can be project id or number.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of WorkerPools to return in this call.
   * @opt_param string pageToken A page token received from a previous call to
   * ListWorkerPools. All other parameters must match.
   * @opt_param bool showDeleted If true, returns deleted (but unexpired)
   * resources along with active ones.
   * @return GoogleCloudRunV2ListWorkerPoolsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWorkerPools($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudRunV2ListWorkerPoolsResponse::class);
  }
  /**
   * Updates a WorkerPool. (workerPools.patch)
   *
   * @param string $name The fully qualified name of this WorkerPool. In
   * CreateWorkerPoolRequest, this field is ignored, and instead composed from
   * CreateWorkerPoolRequest.parent and CreateWorkerPoolRequest.worker_id. Format:
   * `projects/{project}/locations/{location}/workerPools/{worker_id}`
   * @param GoogleCloudRunV2WorkerPool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and if the WorkerPool
   * does not exist, it will create a new one. The caller must have
   * 'run.workerpools.create' permissions if this is set to true and the
   * WorkerPool does not exist.
   * @opt_param bool forceNewRevision Optional. If set to true, a new revision
   * will be created from the template even if the system doesn't detect any
   * changes from the previously deployed revision. This may be useful for cases
   * where the underlying resources need to be recreated or reinitialized. For
   * example if the image is specified by label, but the underlying image digest
   * has changed) or if the container performs deployment initialization work that
   * needs to be performed again.
   * @opt_param string updateMask Optional. The list of fields to be updated.
   * @opt_param bool validateOnly Optional. Indicates that the request should be
   * validated and default values populated, without persisting the request or
   * updating any resources.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudRunV2WorkerPool $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the IAM Access control policy for the specified WorkerPool. Overwrites
   * any existing policy. (workerPools.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GoogleIamV1SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, GoogleIamV1SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified Project. There are no
   * permissions required for making this API call.
   * (workerPools.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GoogleIamV1TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, GoogleIamV1TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], GoogleIamV1TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWorkerPools::class, 'Google_Service_CloudRun_Resource_ProjectsLocationsWorkerPools');
