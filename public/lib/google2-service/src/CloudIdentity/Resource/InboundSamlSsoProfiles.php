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

namespace Google\Service\CloudIdentity\Resource;

use Google\Service\CloudIdentity\InboundSamlSsoProfile;
use Google\Service\CloudIdentity\ListInboundSamlSsoProfilesResponse;
use Google\Service\CloudIdentity\Operation;

/**
 * The "inboundSamlSsoProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudidentityService = new Google\Service\CloudIdentity(...);
 *   $inboundSamlSsoProfiles = $cloudidentityService->inboundSamlSsoProfiles;
 *  </code>
 */
class InboundSamlSsoProfiles extends \Google\Service\Resource
{
  /**
   * Creates an InboundSamlSsoProfile for a customer. When the target customer has
   * enabled [Multi-party approval for sensitive
   * actions](https://support.google.com/a/answer/13790448), the `Operation` in
   * the response will have `"done": false`, it will not have a response, and the
   * metadata will have `"state": "awaiting-multi-party-approval"`.
   * (inboundSamlSsoProfiles.create)
   *
   * @param InboundSamlSsoProfile $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create(InboundSamlSsoProfile $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an InboundSamlSsoProfile. (inboundSamlSsoProfiles.delete)
   *
   * @param string $name Required. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * InboundSamlSsoProfile to delete. Format:
   * `inboundSamlSsoProfiles/{sso_profile_id}`
   * @param array $optParams Optional parameters.
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
   * Gets an InboundSamlSsoProfile. (inboundSamlSsoProfiles.get)
   *
   * @param string $name Required. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * InboundSamlSsoProfile to get. Format:
   * `inboundSamlSsoProfiles/{sso_profile_id}`
   * @param array $optParams Optional parameters.
   * @return InboundSamlSsoProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InboundSamlSsoProfile::class);
  }
  /**
   * Lists InboundSamlSsoProfiles for a customer.
   * (inboundSamlSsoProfiles.listInboundSamlSsoProfiles)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A [Common Expression
   * Language](https://github.com/google/cel-spec) expression to filter the
   * results. The only supported filter is filtering by customer. For example:
   * `customer=="customers/C0123abc"`. Omitting the filter or specifying a filter
   * of `customer=="customers/my_customer"` will return the profiles for the
   * customer that the caller (authenticated user) belongs to.
   * @opt_param int pageSize The maximum number of InboundSamlSsoProfiles to
   * return. The service may return fewer than this value. If omitted (or
   * defaulted to zero) the server will use a sensible default. This default may
   * change over time. The maximum allowed value is 100. Requests with page_size
   * greater than that will be silently interpreted as having this maximum value.
   * @opt_param string pageToken A page token, received from a previous
   * `ListInboundSamlSsoProfiles` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListInboundSamlSsoProfiles` must match the call that provided the page
   * token.
   * @return ListInboundSamlSsoProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listInboundSamlSsoProfiles($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInboundSamlSsoProfilesResponse::class);
  }
  /**
   * Updates an InboundSamlSsoProfile. When the target customer has enabled
   * [Multi-party approval for sensitive
   * actions](https://support.google.com/a/answer/13790448), the `Operation` in
   * the response will have `"done": false`, it will not have a response, and the
   * metadata will have `"state": "awaiting-multi-party-approval"`.
   * (inboundSamlSsoProfiles.patch)
   *
   * @param string $name Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the SAML SSO
   * profile.
   * @param InboundSamlSsoProfile $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, InboundSamlSsoProfile $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InboundSamlSsoProfiles::class, 'Google_Service_CloudIdentity_Resource_InboundSamlSsoProfiles');
