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

namespace Google\Service\Firebaseappcheck\Resource;

use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesRequest;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesResponse;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1ListResourcePoliciesResponse;
use Google\Service\Firebaseappcheck\GoogleFirebaseAppcheckV1ResourcePolicy;
use Google\Service\Firebaseappcheck\GoogleProtobufEmpty;

/**
 * The "resourcePolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseappcheckService = new Google\Service\Firebaseappcheck(...);
 *   $resourcePolicies = $firebaseappcheckService->projects_services_resourcePolicies;
 *  </code>
 */
class ProjectsServicesResourcePolicies extends \Google\Service\Resource
{
  /**
   * Atomically updates the specified ResourcePolicy configurations.
   * (resourcePolicies.batchUpdate)
   *
   * @param string $parent Required. The parent service name, in the format ```
   * projects/{project_number}/services/{service_id} ``` The parent collection in
   * the `name` field of any resource being updated must match this field, or the
   * entire batch fails.
   * @param GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdate($parent, GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', [$params], GoogleFirebaseAppcheckV1BatchUpdateResourcePoliciesResponse::class);
  }
  /**
   * Creates the specified ResourcePolicy configuration. (resourcePolicies.create)
   *
   * @param string $parent Required. The relative resource name of the parent
   * Service in which the specified ResourcePolicy will be created, in the format:
   * ``` projects/{project_number}/services/{service_id} ``` Note that the
   * `service_id` element must be a supported service ID. Currently, the following
   * service IDs are supported: * `oauth2.googleapis.com` (Google Identity for
   * iOS)
   * @param GoogleFirebaseAppcheckV1ResourcePolicy $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppcheckV1ResourcePolicy
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleFirebaseAppcheckV1ResourcePolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleFirebaseAppcheckV1ResourcePolicy::class);
  }
  /**
   * Deletes the specified ResourcePolicy configuration. (resourcePolicies.delete)
   *
   * @param string $name Required. The relative resource name of the
   * ResourcePolicy to delete, in the format: ``` projects/{project_number}/servic
   * es/{service_id}/resourcePolicies/{resource_policy_id} ```
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag The checksum to be validated against the current
   * ResourcePolicy, to ensure the client has an up-to-date value before
   * proceeding. This checksum is computed by the server based on the values of
   * fields in the ResourcePolicy object, and can be obtained from the
   * ResourcePolicy object received from the last CreateResourcePolicy,
   * GetResourcePolicy, ListResourcePolicies, UpdateResourcePolicy, or
   * BatchUpdateResourcePolicies call. This etag is strongly validated as defined
   * by RFC 7232.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets the requested ResourcePolicy configuration. (resourcePolicies.get)
   *
   * @param string $name Required. The relative resource name of the
   * ResourcePolicy to retrieve, in the format: ``` projects/{project_number}/serv
   * ices/{service_id}/resourcePolicies/{resource_policy_id} ``` Note that the
   * `service_id` element must be a supported service ID. Currently, the following
   * service IDs are supported: * `oauth2.googleapis.com` (Google Identity for
   * iOS)
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppcheckV1ResourcePolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirebaseAppcheckV1ResourcePolicy::class);
  }
  /**
   * Lists all ResourcePolicy configurations for the specified project and
   * service. (resourcePolicies.listProjectsServicesResourcePolicies)
   *
   * @param string $parent Required. The relative resource name of the parent
   * Service for which to list each associated ResourcePolicy, in the format: ```
   * projects/{project_number}/services/{service_id} ``` Note that the
   * `service_id` element must be a supported service ID. Currently, the following
   * service IDs are supported: * `oauth2.googleapis.com` (Google Identity for
   * iOS)
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filters the results by the specified rule.
   * For the exact syntax of this field, please consult the
   * [AIP-160](https://google.aip.dev/160) standard. Currently, since the only
   * fields in the ResourcePolicy resource are the scalar fields
   * `enforcement_mode` and `target_resource`, this method does not support the
   * traversal operator (`.`) or the has operator (`:`). Here are some examples of
   * valid filters: * `enforcement_mode = ENFORCED` * `target_resource =
   * "//oauth2.googleapis.com/projects/12345/oauthClients/"` * `enforcement_mode =
   * ENFORCED AND target_resource =
   * "//oauth2.googleapis.com/projects/12345/oauthClients/"`
   * @opt_param int pageSize The maximum number of ResourcePolicy objects to
   * return in the response. The server may return fewer than this at its own
   * discretion. If no value is specified (or too large a value is specified), the
   * server will impose its own limit.
   * @opt_param string pageToken Token returned from a previous call to
   * ListResourcePolicies indicating where in the set of ResourcePolicy objects to
   * resume listing. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to ListResourcePolicies must match
   * the call that provided the page token; if they do not match, the result is
   * undefined.
   * @return GoogleFirebaseAppcheckV1ListResourcePoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsServicesResourcePolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirebaseAppcheckV1ListResourcePoliciesResponse::class);
  }
  /**
   * Updates the specified ResourcePolicy configuration. (resourcePolicies.patch)
   *
   * @param string $name Required. Identifier. The relative name of the resource
   * policy object, in the format: ``` projects/{project_number}/services/{service
   * _id}/resourcePolicies/{resource_policy_id} ``` Note that the `service_id`
   * element must be a supported service ID. Currently, the following service IDs
   * are supported: * `oauth2.googleapis.com` (Google Identity for iOS)
   * `resource_policy_id` is a system-generated UID.
   * @param GoogleFirebaseAppcheckV1ResourcePolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. A comma-separated list of names of
   * fields in the ResourcePolicy to update. Example: `enforcement_mode`.
   * @return GoogleFirebaseAppcheckV1ResourcePolicy
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleFirebaseAppcheckV1ResourcePolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleFirebaseAppcheckV1ResourcePolicy::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsServicesResourcePolicies::class, 'Google_Service_Firebaseappcheck_Resource_ProjectsServicesResourcePolicies');
