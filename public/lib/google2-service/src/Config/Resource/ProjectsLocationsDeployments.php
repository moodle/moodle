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

namespace Google\Service\Config\Resource;

use Google\Service\Config\ConfigEmpty;
use Google\Service\Config\DeleteStatefileRequest;
use Google\Service\Config\Deployment;
use Google\Service\Config\ExportDeploymentStatefileRequest;
use Google\Service\Config\ImportStatefileRequest;
use Google\Service\Config\ListDeploymentsResponse;
use Google\Service\Config\LockDeploymentRequest;
use Google\Service\Config\LockInfo;
use Google\Service\Config\Operation;
use Google\Service\Config\Policy;
use Google\Service\Config\SetIamPolicyRequest;
use Google\Service\Config\Statefile;
use Google\Service\Config\TestIamPermissionsRequest;
use Google\Service\Config\TestIamPermissionsResponse;
use Google\Service\Config\UnlockDeploymentRequest;

/**
 * The "deployments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $configService = new Google\Service\Config(...);
 *   $deployments = $configService->projects_locations_deployments;
 *  </code>
 */
class ProjectsLocationsDeployments extends \Google\Service\Resource
{
  /**
   * Creates a Deployment. (deployments.create)
   *
   * @param string $parent Required. The parent in whose context the Deployment is
   * created. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}'.
   * @param Deployment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string deploymentId Required. The Deployment ID.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Deployment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a Deployment. (deployments.delete)
   *
   * @param string $name Required. The name of the Deployment in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string deletePolicy Optional. Policy on how resources actuated by
   * the deployment should be deleted. If unspecified, the default behavior is to
   * delete the underlying resources.
   * @opt_param bool force Optional. If set to true, any revisions for this
   * deployment will also be deleted. (Otherwise, the request will only work if
   * the deployment has no revisions.)
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Deletes Terraform state file in a given deployment. (deployments.deleteState)
   *
   * @param string $name Required. The name of the deployment in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param DeleteStatefileRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ConfigEmpty
   * @throws \Google\Service\Exception
   */
  public function deleteState($name, DeleteStatefileRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deleteState', [$params], ConfigEmpty::class);
  }
  /**
   * Exports the lock info on a locked deployment. (deployments.exportLock)
   *
   * @param string $name Required. The name of the deployment in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param array $optParams Optional parameters.
   * @return LockInfo
   * @throws \Google\Service\Exception
   */
  public function exportLock($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('exportLock', [$params], LockInfo::class);
  }
  /**
   * Exports Terraform state file from a given deployment.
   * (deployments.exportState)
   *
   * @param string $parent Required. The parent in whose context the statefile is
   * listed. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param ExportDeploymentStatefileRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Statefile
   * @throws \Google\Service\Exception
   */
  public function exportState($parent, ExportDeploymentStatefileRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportState', [$params], Statefile::class);
  }
  /**
   * Gets details about a Deployment. (deployments.get)
   *
   * @param string $name Required. The name of the deployment. Format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param array $optParams Optional parameters.
   * @return Deployment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Deployment::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (deployments.getIamPolicy)
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
   * Imports Terraform state file in a given deployment. The state file does not
   * take effect until the Deployment has been unlocked. (deployments.importState)
   *
   * @param string $parent Required. The parent in whose context the statefile is
   * listed. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param ImportStatefileRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Statefile
   * @throws \Google\Service\Exception
   */
  public function importState($parent, ImportStatefileRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importState', [$params], Statefile::class);
  }
  /**
   * Lists Deployments in a given project and location.
   * (deployments.listProjectsLocationsDeployments)
   *
   * @param string $parent Required. The parent in whose context the Deployments
   * are listed. The parent value is in the format:
   * 'projects/{project_id}/locations/{location}'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the Deployments that match the filter
   * expression. A filter expression filters the resources listed in the response.
   * The expression must be of the form '{field} {operator} {value}' where
   * operators: '<', '>', '<=', '>=', '!=', '=', ':' are supported (colon ':'
   * represents a HAS operator which is roughly synonymous with equality). {field}
   * can refer to a proto or JSON field, or a synthetic field. Field names can be
   * camelCase or snake_case. Examples: - Filter by name: name =
   * "projects/foo/locations/us-central1/deployments/bar - Filter by labels: -
   * Resources that have a key called 'foo' labels.foo:* - Resources that have a
   * key called 'foo' whose value is 'bar' labels.foo = bar - Filter by state: -
   * Deployments in CREATING state. state=CREATING
   * @opt_param string orderBy Field to use to sort the list.
   * @opt_param int pageSize When requesting a page of resources, 'page_size'
   * specifies number of resources to return. If unspecified, at most 500 will be
   * returned. The maximum value is 1000.
   * @opt_param string pageToken Token returned by previous call to
   * 'ListDeployments' which specifies the position in the list from where to
   * continue listing the resources.
   * @return ListDeploymentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeployments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDeploymentsResponse::class);
  }
  /**
   * Locks a deployment. (deployments.lock)
   *
   * @param string $name Required. The name of the deployment in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param LockDeploymentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function lock($name, LockDeploymentRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('lock', [$params], Operation::class);
  }
  /**
   * Updates a Deployment. (deployments.patch)
   *
   * @param string $name Identifier. Resource name of the deployment. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   * @param Deployment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask used to specify the fields
   * to be overwritten in the Deployment resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Deployment $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (deployments.setIamPolicy)
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
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (deployments.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
  /**
   * Unlocks a locked deployment. (deployments.unlock)
   *
   * @param string $name Required. The name of the deployment in the format:
   * 'projects/{project_id}/locations/{location}/deployments/{deployment}'.
   * @param UnlockDeploymentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function unlock($name, UnlockDeploymentRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unlock', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeployments::class, 'Google_Service_Config_Resource_ProjectsLocationsDeployments');
