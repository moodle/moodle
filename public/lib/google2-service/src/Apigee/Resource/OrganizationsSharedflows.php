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

use Google\Service\Apigee\GoogleApiHttpBody;
use Google\Service\Apigee\GoogleCloudApigeeV1ListSharedFlowsResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1MoveSharedFlowRequest;
use Google\Service\Apigee\GoogleCloudApigeeV1SharedFlow;
use Google\Service\Apigee\GoogleCloudApigeeV1SharedFlowRevision;

/**
 * The "sharedflows" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $sharedflows = $apigeeService->organizations_sharedflows;
 *  </code>
 */
class OrganizationsSharedflows extends \Google\Service\Resource
{
  /**
   * Uploads a ZIP-formatted shared flow configuration bundle to an organization.
   * If the shared flow already exists, this creates a new revision of it. If the
   * shared flow does not exist, this creates it. Once imported, the shared flow
   * revision must be deployed before it can be accessed at runtime. The size
   * limit of a shared flow bundle is 15 MB. (sharedflows.create)
   *
   * @param string $parent Required. The name of the parent organization under
   * which to create the shared flow. Must be of the form:
   * `organizations/{organization_id}` If the resource has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param GoogleApiHttpBody $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string action Required. Must be set to either `import` or
   * `validate`.
   * @opt_param string name Required. The name to give the shared flow
   * @opt_param string space Optional. The ID of the space to associated with this
   * shared flow. Any IAM policies applied to the space will affect access to this
   * shared flow. Note that this field is only respected when creating a new
   * shared flow. It has no effect when creating a new revision for an existing
   * shared flow.
   * @return GoogleCloudApigeeV1SharedFlowRevision
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleApiHttpBody $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1SharedFlowRevision::class);
  }
  /**
   * Deletes a shared flow and all it's revisions. The shared flow must be
   * undeployed before you can delete it. (sharedflows.delete)
   *
   * @param string $name Required. shared flow name of the form:
   * `organizations/{organization_id}/sharedflows/{shared_flow_id}` If the
   * resource has the `space` attribute set, IAM permissions are checked against
   * the Space resource path. To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SharedFlow
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudApigeeV1SharedFlow::class);
  }
  /**
   * Gets a shared flow by name, including a list of its revisions.
   * (sharedflows.get)
   *
   * @param string $name Required. The name of the shared flow to get. Must be of
   * the form: `organizations/{organization_id}/sharedflows/{shared_flow_id}` If
   * the resource has the `space` attribute set, IAM permissions are checked
   * against the Space resource path. To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SharedFlow
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1SharedFlow::class);
  }
  /**
   * Lists all shared flows in the organization. If the resource has the `space`
   * attribute set, the response may not return all resources. To learn more, read
   * the [Apigee Spaces Overview](https://cloud.google.com/apigee/docs/api-
   * platform/system-administration/spaces/apigee-spaces-overview).
   * (sharedflows.listOrganizationsSharedflows)
   *
   * @param string $parent Required. The name of the parent organization under
   * which to get shared flows. Must be of the form:
   * `organizations/{organization_id}` If the resource has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool includeMetaData Indicates whether to include shared flow
   * metadata in the response.
   * @opt_param bool includeRevisions Indicates whether to include a list of
   * revisions in the response.
   * @opt_param string space Optional. The space ID used to filter the list of
   * shared flows (optional). If unspecified, all shared flows in the organization
   * will be listed. To learn how Spaces can be used to manage resources, read the
   * [Apigee Spaces Overview](https://cloud.google.com/apigee/docs/api-
   * platform/system-administration/spaces/apigee-spaces-overview).
   * @return GoogleCloudApigeeV1ListSharedFlowsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSharedflows($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListSharedFlowsResponse::class);
  }
  /**
   * Moves an shared flow to a different space. (sharedflows.move)
   *
   * @param string $name Required. Shared Flow to move in the following format:
   * `organizations/{org}/sharedflows/{shared_flow}`
   * @param GoogleCloudApigeeV1MoveSharedFlowRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SharedFlow
   * @throws \Google\Service\Exception
   */
  public function move($name, GoogleCloudApigeeV1MoveSharedFlowRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('move', [$params], GoogleCloudApigeeV1SharedFlow::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSharedflows::class, 'Google_Service_Apigee_Resource_OrganizationsSharedflows');
