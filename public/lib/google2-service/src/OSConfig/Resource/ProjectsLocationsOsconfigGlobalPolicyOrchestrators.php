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

namespace Google\Service\OSConfig\Resource;

use Google\Service\OSConfig\GoogleCloudOsconfigV2ListPolicyOrchestratorsResponse;
use Google\Service\OSConfig\GoogleCloudOsconfigV2PolicyOrchestrator;
use Google\Service\OSConfig\Operation;

/**
 * The "policyOrchestrators" collection of methods.
 * Typical usage is:
 *  <code>
 *   $osconfigService = new Google\Service\OSConfig(...);
 *   $policyOrchestrators = $osconfigService->projects_locations_global_policyOrchestrators;
 *  </code>
 */
class ProjectsLocationsOsconfigGlobalPolicyOrchestrators extends \Google\Service\Resource
{
  /**
   * Creates a new policy orchestrator under the given project resource. `name`
   * field of the given orchestrator are ignored and instead replaced by a product
   * of `parent` and `policy_orchestrator_id`. Orchestrator state field might be
   * only set to `ACTIVE`, `STOPPED` or omitted (in which case, the created
   * resource will be in `ACTIVE` state anyway). (policyOrchestrators.create)
   *
   * @param string $parent Required. The parent resource name in the form of: *
   * `organizations/{organization_id}/locations/global` *
   * `folders/{folder_id}/locations/global` *
   * `projects/{project_id_or_number}/locations/global`
   * @param GoogleCloudOsconfigV2PolicyOrchestrator $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string policyOrchestratorId Required. The logical identifier of
   * the policy orchestrator, with the following restrictions: * Must contain only
   * lowercase letters, numbers, and hyphens. * Must start with a letter. * Must
   * be between 1-63 characters. * Must end with a number or a letter. * Must be
   * unique within the parent.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudOsconfigV2PolicyOrchestrator $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an existing policy orchestrator resource, parented by a project.
   * (policyOrchestrators.delete)
   *
   * @param string $name Required. Name of the resource to be deleted.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the policy orchestrator.
   * If an etag is provided and does not match the current etag of the policy
   * orchestrator, deletion will be blocked and an ABORTED error will be returned.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Retrieves an existing policy orchestrator, parented by a project.
   * (policyOrchestrators.get)
   *
   * @param string $name Required. The resource name.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudOsconfigV2PolicyOrchestrator
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudOsconfigV2PolicyOrchestrator::class);
  }
  /**
   * Lists the policy orchestrators under the given parent project resource.
   * (policyOrchestrators.listProjectsLocationsOsconfigGlobalPolicyOrchestrators)
   *
   * @param string $parent Required. The parent resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return GoogleCloudOsconfigV2ListPolicyOrchestratorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsOsconfigGlobalPolicyOrchestrators($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudOsconfigV2ListPolicyOrchestratorsResponse::class);
  }
  /**
   * Updates an existing policy orchestrator, parented by a project.
   * (policyOrchestrators.patch)
   *
   * @param string $name Immutable. Identifier. In form of * `organizations/{organ
   * ization_id}/locations/global/policyOrchestrators/{orchestrator_id}` *
   * `folders/{folder_id}/locations/global/policyOrchestrators/{orchestrator_id}`
   * * `projects/{project_id_or_number}/locations/global/policyOrchestrators/{orch
   * estrator_id}`
   * @param GoogleCloudOsconfigV2PolicyOrchestrator $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to merge into the
   * existing policy orchestrator. A special ["*"] field mask can be used to
   * simply replace the entire resource. Otherwise, for all paths referenced in
   * the mask, following merge rules are used: * output only fields are ignored, *
   * primitive fields are replaced, * repeated fields are replaced, * map fields
   * are merged key by key, * message fields are cleared if not set in the
   * request, otherwise they are merged recursively (in particular - message
   * fields set to an empty message has no side effects) If field mask (or its
   * paths) is not specified, it is automatically inferred from the request using
   * following rules: * primitive fields are listed, if set to a non-default value
   * (as there is no way to distinguish between default and unset value), * map
   * and repeated fields are listed, * `google.protobuf.Any` fields are listed, *
   * other message fields are traversed recursively. Note: implicit mask does not
   * allow clearing fields.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudOsconfigV2PolicyOrchestrator $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsOsconfigGlobalPolicyOrchestrators::class, 'Google_Service_OSConfig_Resource_ProjectsLocationsOsconfigGlobalPolicyOrchestrators');
