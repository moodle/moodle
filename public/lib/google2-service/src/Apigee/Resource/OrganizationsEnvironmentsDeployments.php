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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1Deployment;
use Google\Service\Apigee\GoogleCloudApigeeV1ListDeploymentsResponse;
use Google\Service\Apigee\GoogleIamV1Policy;
use Google\Service\Apigee\GoogleIamV1SetIamPolicyRequest;
use Google\Service\Apigee\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\Apigee\GoogleIamV1TestIamPermissionsResponse;

/**
 * The "deployments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $deployments = $apigeeService->organizations_environments_deployments;
 *  </code>
 */
class OrganizationsEnvironmentsDeployments extends \Google\Service\Resource
{
  /**
   * Gets a particular deployment of Api proxy or a shared flow in an environment
   * (deployments.get)
   *
   * @param string $name Required. Name of the api proxy or the shared flow
   * deployment. Use the following structure in your request:
   * `organizations/{org}/environments/{env}/deployments/{deployment}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1Deployment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1Deployment::class);
  }
  /**
   * Gets the IAM policy on a deployment. For more information, see [Manage users,
   * roles, and permissions using the
   * API](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/manage-users-roles). You must have the
   * `apigee.deployments.getIamPolicy` permission to call this API.
   * (deployments.getIamPolicy)
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
   * Lists all deployments of API proxies or shared flows in an environment.
   * (deployments.listOrganizationsEnvironmentsDeployments)
   *
   * @param string $parent Required. Name of the environment for which to return
   * deployment information in the following format:
   * `organizations/{org}/environments/{env}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool sharedFlows Optional. Flag that specifies whether to return
   * shared flow or API proxy deployments. Set to `true` to return shared flow
   * deployments; set to `false` to return API proxy deployments. Defaults to
   * `false`.
   * @return GoogleCloudApigeeV1ListDeploymentsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsEnvironmentsDeployments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListDeploymentsResponse::class);
  }
  /**
   * Sets the IAM policy on a deployment, if the policy already exists it will be
   * replaced. For more information, see [Manage users, roles, and permissions
   * using the API](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/manage-users-roles). You must have the
   * `apigee.deployments.setIamPolicy` permission to call this API.
   * (deployments.setIamPolicy)
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
   * Tests the permissions of a user on a deployment, and returns a subset of
   * permissions that the user has on the deployment. If the deployment does not
   * exist, an empty permission set is returned (a NOT_FOUND error is not
   * returned). (deployments.testIamPermissions)
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
class_alias(OrganizationsEnvironmentsDeployments::class, 'Google_Service_Apigee_Resource_OrganizationsEnvironmentsDeployments');
