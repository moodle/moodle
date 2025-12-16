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

use Google\Service\Apigee\GoogleCloudApigeeV1KeyValueEntry;
use Google\Service\Apigee\GoogleCloudApigeeV1ListKeyValueEntriesResponse;

/**
 * The "entries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $entries = $apigeeService->organizations_keyvaluemaps_entries;
 *  </code>
 */
class OrganizationsKeyvaluemapsEntries extends \Google\Service\Resource
{
  /**
   * Creates key value entries in a key value map scoped to an organization,
   * environment, or API proxy. **Note**: Supported for Apigee hybrid 1.8.x and
   * higher. (entries.create)
   *
   * @param string $parent Required. Scope as indicated by the URI in which to
   * create the key value map entry. Use **one** of the following structures in
   * your request: *
   * `organizations/{organization}/apis/{api}/keyvaluemaps/{keyvaluemap}`. * `orga
   * nizations/{organization}/environments/{environment}/keyvaluemaps/{keyvaluemap
   * }` * `organizations/{organization}/keyvaluemaps/{keyvaluemap}`. If the
   * KeyValueMap is under an API Proxy resource that has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param GoogleCloudApigeeV1KeyValueEntry $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueEntry
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1KeyValueEntry $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1KeyValueEntry::class);
  }
  /**
   * Deletes a key value entry from a key value map scoped to an organization,
   * environment, or API proxy. **Notes:** * After you delete the key value entry,
   * the policy consuming the entry will continue to function with its cached
   * values for a few minutes. This is expected behavior. * Supported for Apigee
   * hybrid 1.8.x and higher. (entries.delete)
   *
   * @param string $name Required. Scope as indicated by the URI in which to
   * delete the key value map entry. Use **one** of the following structures in
   * your request: * `organizations/{organization}/apis/{api}/keyvaluemaps/{keyval
   * uemap}/entries/{entry}`. * `organizations/{organization}/environments/{enviro
   * nment}/keyvaluemaps/{keyvaluemap}/entries/{entry}` *
   * `organizations/{organization}/keyvaluemaps/{keyvaluemap}/entries/{entry}`. If
   * the KeyValueMap is under an API Proxy resource that has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueEntry
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudApigeeV1KeyValueEntry::class);
  }
  /**
   * Get the key value entry value for a key value map scoped to an organization,
   * environment, or API proxy. **Note**: Supported for Apigee hybrid 1.8.x and
   * higher. (entries.get)
   *
   * @param string $name Required. Scope as indicated by the URI in which to fetch
   * the key value map entry/value. Use **one** of the following structures in
   * your request: * `organizations/{organization}/apis/{api}/keyvaluemaps/{keyval
   * uemap}/entries/{entry}`. * `organizations/{organization}/environments/{enviro
   * nment}/keyvaluemaps/{keyvaluemap}/entries/{entry}` *
   * `organizations/{organization}/keyvaluemaps/{keyvaluemap}/entries/{entry}`. If
   * the KeyValueMap is under an API Proxy resource that has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueEntry
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1KeyValueEntry::class);
  }
  /**
   * Lists key value entries for key values maps scoped to an organization,
   * environment, or API proxy. **Note**: Supported for Apigee hybrid 1.8.x and
   * higher. (entries.listOrganizationsKeyvaluemapsEntries)
   *
   * @param string $parent Required. Scope as indicated by the URI in which to
   * list key value maps. Use **one** of the following structures in your request:
   * * `organizations/{organization}/apis/{api}/keyvaluemaps/{keyvaluemap}`. * `or
   * ganizations/{organization}/environments/{environment}/keyvaluemaps/{keyvaluem
   * ap}` * `organizations/{organization}/keyvaluemaps/{keyvaluemap}`. If the
   * KeyValueMap is under an API Proxy resource that has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of key value entries to
   * return. If unspecified, at most 100 entries will be returned.
   * @opt_param string pageToken Optional. Page token. If provides, must be a
   * valid key value entry returned from a previous call that can be used to
   * retrieve the next page.
   * @return GoogleCloudApigeeV1ListKeyValueEntriesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsKeyvaluemapsEntries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListKeyValueEntriesResponse::class);
  }
  /**
   * Update key value entry scoped to an organization, environment, or API proxy
   * for an existing key. (entries.update)
   *
   * @param string $name Required. Scope as indicated by the URI in which to
   * create the key value map entry. Use **one** of the following structures in
   * your request: *
   * `organizations/{organization}/apis/{api}/keyvaluemaps/{keyvaluemap}`. * `orga
   * nizations/{organization}/environments/{environment}/keyvaluemaps/{keyvaluemap
   * }` * `organizations/{organization}/keyvaluemaps/{keyvaluemap}`. If the
   * KeyValueMap is under an API Proxy resource that has the `space` attribute
   * set, IAM permissions are checked against the Space resource path. To learn
   * more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param GoogleCloudApigeeV1KeyValueEntry $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1KeyValueEntry
   * @throws \Google\Service\Exception
   */
  public function update($name, GoogleCloudApigeeV1KeyValueEntry $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleCloudApigeeV1KeyValueEntry::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsKeyvaluemapsEntries::class, 'Google_Service_Apigee_Resource_OrganizationsKeyvaluemapsEntries');
