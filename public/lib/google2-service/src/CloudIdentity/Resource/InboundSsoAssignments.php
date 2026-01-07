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

use Google\Service\CloudIdentity\InboundSsoAssignment;
use Google\Service\CloudIdentity\ListInboundSsoAssignmentsResponse;
use Google\Service\CloudIdentity\Operation;

/**
 * The "inboundSsoAssignments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudidentityService = new Google\Service\CloudIdentity(...);
 *   $inboundSsoAssignments = $cloudidentityService->inboundSsoAssignments;
 *  </code>
 */
class InboundSsoAssignments extends \Google\Service\Resource
{
  /**
   * Creates an InboundSsoAssignment for users and devices in a `Customer` under a
   * given `Group` or `OrgUnit`. (inboundSsoAssignments.create)
   *
   * @param InboundSsoAssignment $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create(InboundSsoAssignment $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an InboundSsoAssignment. To disable SSO, Create (or Update) an
   * assignment that has `sso_mode` == `SSO_OFF`. (inboundSsoAssignments.delete)
   *
   * @param string $name Required. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * InboundSsoAssignment to delete. Format: `inboundSsoAssignments/{assignment}`
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
   * Gets an InboundSsoAssignment. (inboundSsoAssignments.get)
   *
   * @param string $name Required. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * InboundSsoAssignment to fetch. Format: `inboundSsoAssignments/{assignment}`
   * @param array $optParams Optional parameters.
   * @return InboundSsoAssignment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InboundSsoAssignment::class);
  }
  /**
   * Lists the InboundSsoAssignments for a `Customer`.
   * (inboundSsoAssignments.listInboundSsoAssignments)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A CEL expression to filter the results. The only
   * supported filter is filtering by customer. For example:
   * `customer==customers/C0123abc`. Omitting the filter or specifying a filter of
   * `customer==customers/my_customer` will return the assignments for the
   * customer that the caller (authenticated user) belongs to.
   * @opt_param int pageSize The maximum number of assignments to return. The
   * service may return fewer than this value. If omitted (or defaulted to zero)
   * the server will use a sensible default. This default may change over time.
   * The maximum allowed value is 100, though requests with page_size greater than
   * that will be silently interpreted as having this maximum value. This may
   * increase in the futue.
   * @opt_param string pageToken A page token, received from a previous
   * `ListInboundSsoAssignments` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListInboundSsoAssignments` must match the call that provided the page token.
   * @return ListInboundSsoAssignmentsResponse
   * @throws \Google\Service\Exception
   */
  public function listInboundSsoAssignments($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInboundSsoAssignmentsResponse::class);
  }
  /**
   * Updates an InboundSsoAssignment. The body of this request is the
   * `inbound_sso_assignment` field and the `update_mask` is relative to that. For
   * example: a PATCH to
   * `/v1/inboundSsoAssignments/0abcdefg1234567&update_mask=rank` with a body of
   * `{ "rank": 1 }` moves that (presumably group-targeted) SSO assignment to the
   * highest priority and shifts any other group-targeted assignments down in
   * priority. (inboundSsoAssignments.patch)
   *
   * @param string $name Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the Inbound SSO
   * Assignment.
   * @param InboundSsoAssignment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, InboundSsoAssignment $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InboundSsoAssignments::class, 'Google_Service_CloudIdentity_Resource_InboundSsoAssignments');
