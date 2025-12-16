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

use Google\Service\Apigee\GoogleCloudApigeeV1KeyValueMap;

/**
 * The "keyvaluemaps" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $keyvaluemaps = $apigeeService->organizations_apis_keyvaluemaps;
 *  </code>
 */
class OrganizationsApisKeyvaluemaps extends \Google\Service\Resource
{
  /**
   * Creates a key value map in an API proxy. (keyvaluemaps.create)
   *
   * @param string $parent Required. Name of the environment in which to create
   * the key value map. Use the following structure in your request:
   * `organizations/{org}/apis/{api}` If the API Proxy resource has the `space`
   * attribute set, IAM permissions are checked against the Space resource path.
   * To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param GoogleCloudApigeeV1KeyValueMap $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueMap
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1KeyValueMap $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1KeyValueMap::class);
  }
  /**
   * Deletes a key value map from an API proxy. (keyvaluemaps.delete)
   *
   * @param string $name Required. Name of the key value map. Use the following
   * structure in your request:
   * `organizations/{org}/apis/{api}/keyvaluemaps/{keyvaluemap}` If the API Proxy
   * resource has the `space` attribute set, IAM permissions are checked against
   * the Space resource path. To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueMap
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudApigeeV1KeyValueMap::class);
  }
  /**
   * Get the key value map scoped to an organization, environment, or API proxy.
   * (keyvaluemaps.get)
   *
   * @param string $name Required. Scope as indicated by the URI in which to fetch
   * the key value map. Use **one** of the following structures in your request: *
   * `organizations/{organization}/apis/{api}/keyvaluemaps/{keyvaluemap}`. * `orga
   * nizations/{organization}/environments/{environment}/keyvaluemaps/{keyvaluemap
   * }` * `organizations/{organization}/keyvaluemaps/{keyvaluemap}`. If the
   * KeyValueMap is under an API Proxy resource that has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueMap
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1KeyValueMap::class);
  }
  /**
   * Update the key value map scoped to an organization, environment, or API
   * proxy. (keyvaluemaps.update)
   *
   * @param string $name Required. Scope as indicated by the URI in which to fetch
   * the key value map. Use **one** of the following structures in your request: *
   * `organizations/{organization}/apis/{api}/keyvaluemaps/{keyvaluemap}`. * `orga
   * nizations/{organization}/environments/{environment}/keyvaluemaps/{keyvaluemap
   * }` * `organizations/{organization}/keyvaluemaps/{keyvaluemap}`. If the
   * KeyValueMap is under an API Proxy resource that has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param GoogleCloudApigeeV1KeyValueMap $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueMap
   * @throws \Google\Service\Exception
   */
  public function update($name, GoogleCloudApigeeV1KeyValueMap $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleCloudApigeeV1KeyValueMap::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsApisKeyvaluemaps::class, 'Google_Service_Apigee_Resource_OrganizationsApisKeyvaluemaps');
