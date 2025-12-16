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

namespace Google\Service\BeyondCorp\Resource;

use Google\Service\BeyondCorp\GoogleCloudBeyondcorpSecuritygatewaysV1Application;
use Google\Service\BeyondCorp\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\BeyondCorp\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\BeyondCorp\GoogleLongrunningOperation;

/**
 * The "applications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $beyondcorpService = new Google\Service\BeyondCorp(...);
 *   $applications = $beyondcorpService->projects_locations_global_securityGateways_applications;
 *  </code>
 */
class ProjectsLocationsBeyondcorpGlobalSecurityGatewaysApplications extends \Google\Service\Resource
{
  /**
   * Creates a new Application in a given project and location.
   * (applications.create)
   *
   * @param string $parent Required. The resource name of the parent
   * SecurityGateway using the form: `projects/{project_id}/locations/global/secur
   * ityGateways/{security_gateway_id}`
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1Application $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string applicationId Optional. User-settable Application resource
   * ID. * Must start with a letter. * Must contain between 4-63 characters from
   * `/a-z-/`. * Must end with a number or letter.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudBeyondcorpSecuritygatewaysV1Application $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Updates the parameters of a single Application. (applications.patch)
   *
   * @param string $name Identifier. Name of the resource.
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1Application $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request timed out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Mutable fields include: display_name.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudBeyondcorpSecuritygatewaysV1Application $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (applications.testIamPermissions)
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
class_alias(ProjectsLocationsBeyondcorpGlobalSecurityGatewaysApplications::class, 'Google_Service_BeyondCorp_Resource_ProjectsLocationsBeyondcorpGlobalSecurityGatewaysApplications');
