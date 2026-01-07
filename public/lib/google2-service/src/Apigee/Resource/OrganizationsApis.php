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
use Google\Service\Apigee\GoogleCloudApigeeV1ApiProxy;
use Google\Service\Apigee\GoogleCloudApigeeV1ApiProxyRevision;
use Google\Service\Apigee\GoogleCloudApigeeV1ListApiProxiesResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1MoveApiProxyRequest;

/**
 * The "apis" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $apis = $apigeeService->organizations_apis;
 *  </code>
 */
class OrganizationsApis extends \Google\Service\Resource
{
  /**
   * Creates an API proxy. The API proxy created will not be accessible at runtime
   * until it is deployed to an environment. Create a new API proxy by setting the
   * `name` query parameter to the name of the API proxy. Import an API proxy
   * configuration bundle stored in zip format on your local machine to your
   * organization by doing the following: * Set the `name` query parameter to the
   * name of the API proxy. * Set the `action` query parameter to `import`. * Set
   * the `Content-Type` header to `multipart/form-data`. * Pass as a file the name
   * of API proxy configuration bundle stored in zip format on your local machine
   * using the `file` form field. **Note**: To validate the API proxy
   * configuration bundle only without importing it, set the `action` query
   * parameter to `validate`. When importing an API proxy configuration bundle, if
   * the API proxy does not exist, it will be created. If the API proxy exists,
   * then a new revision is created. Invalid API proxy configurations are
   * rejected, and a list of validation errors is returned to the client.
   * (apis.create)
   *
   * @param string $parent Required. Name of the organization in the following
   * format: `organizations/{org}` If the API Proxy resource has the `space`
   * attribute set, IAM permissions are checked against the Space resource path.
   * To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param GoogleApiHttpBody $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string action Action to perform when importing an API proxy
   * configuration bundle. Set this parameter to one of the following values: *
   * `import` to import the API proxy configuration bundle. * `validate` to
   * validate the API proxy configuration bundle without importing it.
   * @opt_param string name Name of the API proxy. Restrict the characters used
   * to: A-Za-z0-9._-
   * @opt_param string space Optional. The ID of the space associated with this
   * proxy. Any IAM policies applied to the space will affect access to this
   * proxy. Note that this field is only respected when creating a new proxy. It
   * has no effect when creating a new revision for an existing proxy.
   * @opt_param bool validate Ignored. All uploads are validated regardless of the
   * value of this field. Maintained for compatibility with Apigee Edge API.
   * @return GoogleCloudApigeeV1ApiProxyRevision
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleApiHttpBody $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1ApiProxyRevision::class);
  }
  /**
   * Deletes an API proxy and all associated endpoints, policies, resources, and
   * revisions. The API proxy must be undeployed before you can delete it.
   * (apis.delete)
   *
   * @param string $name Required. Name of the API proxy in the following format:
   * `organizations/{org}/apis/{api}` If the API Proxy resource has the `space`
   * attribute set, IAM permissions are checked against the Space resource path.
   * To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiProxy
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudApigeeV1ApiProxy::class);
  }
  /**
   * Gets an API proxy including a list of existing revisions. (apis.get)
   *
   * @param string $name Required. Name of the API proxy in the following format:
   * `organizations/{org}/apis/{api}` If the API Proxy resource has the `space`
   * attribute set, IAM permissions are checked against the Space resource path.
   * To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiProxy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1ApiProxy::class);
  }
  /**
   * Lists the names of all API proxies in an organization. The names returned
   * correspond to the names defined in the configuration files for each API
   * proxy. If the resource has the `space` attribute set, the response may not
   * return all resources. To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview). (apis.listOrganizationsApis)
   *
   * @param string $parent Required. Name of the organization in the following
   * format: `organizations/{org}` If the resource has the `space` attribute set,
   * IAM permissions are checked against the Space resource path. To learn more,
   * read the [Apigee Spaces Overview](https://cloud.google.com/apigee/docs/api-
   * platform/system-administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool includeMetaData Flag that specifies whether to include API
   * proxy metadata in the response.
   * @opt_param bool includeRevisions Flag that specifies whether to include a
   * list of revisions in the response.
   * @opt_param string space Optional. The space ID to filter the list of proxies
   * (optional). If unspecified, all proxies in the organization will be listed.
   * @return GoogleCloudApigeeV1ListApiProxiesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsApis($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListApiProxiesResponse::class);
  }
  /**
   * Moves an API proxy to a different space. (apis.move)
   *
   * @param string $name Required. API proxy to move in the following format:
   * `organizations/{org}/apis/{api}`
   * @param GoogleCloudApigeeV1MoveApiProxyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiProxy
   * @throws \Google\Service\Exception
   */
  public function move($name, GoogleCloudApigeeV1MoveApiProxyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('move', [$params], GoogleCloudApigeeV1ApiProxy::class);
  }
  /**
   * Updates an existing API proxy. (apis.patch)
   *
   * @param string $name Required. API proxy to update in the following format:
   * `organizations/{org}/apis/{api}` If the resource has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param GoogleCloudApigeeV1ApiProxy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApigeeV1ApiProxy
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApigeeV1ApiProxy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApigeeV1ApiProxy::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsApis::class, 'Google_Service_Apigee_Resource_OrganizationsApis');
