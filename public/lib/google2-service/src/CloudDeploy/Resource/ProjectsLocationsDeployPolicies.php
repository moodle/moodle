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

namespace Google\Service\CloudDeploy\Resource;

use Google\Service\CloudDeploy\DeployPolicy;
use Google\Service\CloudDeploy\ListDeployPoliciesResponse;
use Google\Service\CloudDeploy\Operation;
use Google\Service\CloudDeploy\Policy;
use Google\Service\CloudDeploy\SetIamPolicyRequest;

/**
 * The "deployPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddeployService = new Google\Service\CloudDeploy(...);
 *   $deployPolicies = $clouddeployService->projects_locations_deployPolicies;
 *  </code>
 */
class ProjectsLocationsDeployPolicies extends \Google\Service\Resource
{
  /**
   * Creates a new DeployPolicy in a given project and location.
   * (deployPolicies.create)
   *
   * @param string $parent Required. The parent collection in which the
   * `DeployPolicy` must be created. The format is
   * `projects/{project_id}/locations/{location_name}`.
   * @param DeployPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string deployPolicyId Required. ID of the `DeployPolicy`.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set to true, the request is
   * validated and the user is provided with an expected result, but no actual
   * change is made.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, DeployPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single DeployPolicy. (deployPolicies.delete)
   *
   * @param string $name Required. The name of the `DeployPolicy` to delete. The
   * format is `projects/{project_id}/locations/{location_name}/deployPolicies/{de
   * ploy_policy_name}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, then deleting an
   * already deleted or non-existing `DeployPolicy` will succeed.
   * @opt_param string etag Optional. This checksum is computed by the server
   * based on the value of other fields, and may be sent on update and delete
   * requests to ensure the client has an up-to-date value before proceeding.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not actually post it.
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
   * Gets details of a single DeployPolicy. (deployPolicies.get)
   *
   * @param string $name Required. Name of the `DeployPolicy`. Format must be `pro
   * jects/{project_id}/locations/{location_name}/deployPolicies/{deploy_policy_na
   * me}`.
   * @param array $optParams Optional parameters.
   * @return DeployPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DeployPolicy::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (deployPolicies.getIamPolicy)
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
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists DeployPolicies in a given project and location.
   * (deployPolicies.listProjectsLocationsDeployPolicies)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * deploy policies. Format must be
   * `projects/{project_id}/locations/{location_name}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filter deploy policies to be returned. See
   * https://google.aip.dev/160 for more details. All fields can be used in the
   * filter.
   * @opt_param string orderBy Field to sort by. See
   * https://google.aip.dev/132#ordering for more details.
   * @opt_param int pageSize The maximum number of deploy policies to return. The
   * service may return fewer than this value. If unspecified, at most 50 deploy
   * policies will be returned. The maximum value is 1000; values above 1000 will
   * be set to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListDeployPolicies` call. Provide this to retrieve the subsequent page. When
   * paginating, all other provided parameters match the call that provided the
   * page token.
   * @return ListDeployPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeployPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDeployPoliciesResponse::class);
  }
  /**
   * Updates the parameters of a single DeployPolicy. (deployPolicies.patch)
   *
   * @param string $name Output only. Name of the `DeployPolicy`. Format is
   * `projects/{project}/locations/{location}/deployPolicies/{deployPolicy}`. The
   * `deployPolicy` component must match `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`
   * @param DeployPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, updating a
   * `DeployPolicy` that does not exist will result in the creation of a new
   * `DeployPolicy`.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten by the update in the `DeployPolicy` resource. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it's in the mask. If the user
   * doesn't provide a mask then all fields are overwritten.
   * @opt_param bool validateOnly Optional. If set to true, the request is
   * validated and the user is provided with an expected result, but no actual
   * change is made.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, DeployPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (deployPolicies.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeployPolicies::class, 'Google_Service_CloudDeploy_Resource_ProjectsLocationsDeployPolicies');
