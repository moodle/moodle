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

use Google\Service\Apigee\GoogleCloudApigeeV1ApiDoc;
use Google\Service\Apigee\GoogleCloudApigeeV1ApiDocDocumentation;
use Google\Service\Apigee\GoogleCloudApigeeV1ApiDocDocumentationResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1ApiDocResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1DeleteResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1ListApiDocsResponse;

/**
 * The "apidocs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $apidocs = $apigeeService->organizations_sites_apidocs;
 *  </code>
 */
class OrganizationsSitesApidocs extends \Google\Service\Resource
{
  /**
   * Creates a new catalog item. (apidocs.create)
   *
   * @param string $parent Required. Name of the portal. Use the following
   * structure in your request: `organizations/{org}/sites/{site}`
   * @param GoogleCloudApigeeV1ApiDoc $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiDocResponse
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1ApiDoc $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1ApiDocResponse::class);
  }
  /**
   * Deletes a catalog item. (apidocs.delete)
   *
   * @param string $name Required. Name of the catalog item. Use the following
   * structure in your request:
   * `organizations/{org}/sites/{site}/apidocs/{apidoc}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1DeleteResponse
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudApigeeV1DeleteResponse::class);
  }
  /**
   * Gets a catalog item. (apidocs.get)
   *
   * @param string $name Required. Name of the catalog item. Use the following
   * structure in your request:
   * `organizations/{org}/sites/{site}/apidocs/{apidoc}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiDocResponse
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1ApiDocResponse::class);
  }
  /**
   * Gets the documentation for the specified catalog item.
   * (apidocs.getDocumentation)
   *
   * @param string $name Required. Resource name of the catalog item
   * documentation. Use the following structure in your request:
   * `organizations/{org}/sites/{site}/apidocs/{apidoc}/documentation`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiDocDocumentationResponse
   * @throws \Google\Service\Exception
   */
  public function getDocumentation($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDocumentation', [$params], GoogleCloudApigeeV1ApiDocDocumentationResponse::class);
  }
  /**
   * Returns the catalog items associated with a portal.
   * (apidocs.listOrganizationsSitesApidocs)
   *
   * @param string $parent Required. Name of the portal. Use the following
   * structure in your request: `organizations/{org}/sites/{site}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return. The
   * service may return fewer than this value. If unspecified, at most 25 books
   * will be returned. The maximum value is 100; values above 100 will be coerced
   * to 100.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListApiDocs` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudApigeeV1ListApiDocsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSitesApidocs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListApiDocsResponse::class);
  }
  /**
   * Updates a catalog item. (apidocs.update)
   *
   * @param string $name Required. Name of the catalog item. Use the following
   * structure in your request:
   * `organizations/{org}/sites/{site}/apidocs/{apidoc}`
   * @param GoogleCloudApigeeV1ApiDoc $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiDocResponse
   * @throws \Google\Service\Exception
   */
  public function update($name, GoogleCloudApigeeV1ApiDoc $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleCloudApigeeV1ApiDocResponse::class);
  }
  /**
   * Updates the documentation for the specified catalog item. Note that the
   * documentation file contents will not be populated in the return message.
   * (apidocs.updateDocumentation)
   *
   * @param string $name Required. Resource name of the catalog item
   * documentation. Use the following structure in your request:
   * `organizations/{org}/sites/{site}/apidocs/{apidoc}/documentation`
   * @param GoogleCloudApigeeV1ApiDocDocumentation $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1ApiDocDocumentationResponse
   * @throws \Google\Service\Exception
   */
  public function updateDocumentation($name, GoogleCloudApigeeV1ApiDocDocumentation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateDocumentation', [$params], GoogleCloudApigeeV1ApiDocDocumentationResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSitesApidocs::class, 'Google_Service_Apigee_Resource_OrganizationsSitesApidocs');
