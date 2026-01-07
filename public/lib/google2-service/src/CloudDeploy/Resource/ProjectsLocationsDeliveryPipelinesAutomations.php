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

namespace Google\Service\CloudDeploy\Resource;

use Google\Service\CloudDeploy\Automation;
use Google\Service\CloudDeploy\ListAutomationsResponse;
use Google\Service\CloudDeploy\Operation;

/**
 * The "automations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddeployService = new Google\Service\CloudDeploy(...);
 *   $automations = $clouddeployService->projects_locations_deliveryPipelines_automations;
 *  </code>
 */
class ProjectsLocationsDeliveryPipelinesAutomations extends \Google\Service\Resource
{
  /**
   * Creates a new Automation in a given project and location.
   * (automations.create)
   *
   * @param string $parent Required. The parent collection in which the
   * `Automation` must be created. The format is `projects/{project_id}/locations/
   * {location_name}/deliveryPipelines/{pipeline_name}`.
   * @param Automation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string automationId Required. ID of the `Automation`.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set to true, the request is
   * validated and the user is provided with an expected result, but no actual
   * change is made.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Automation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Automation resource. (automations.delete)
   *
   * @param string $name Required. The name of the `Automation` to delete. The
   * format is `projects/{project_id}/locations/{location_name}/deliveryPipelines/
   * {pipeline_name}/automations/{automation_name}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, then deleting an
   * already deleted or non-existing `Automation` will succeed.
   * @opt_param string etag Optional. The weak etag of the request. This checksum
   * is computed by the server based on the value of other fields, and may be sent
   * on update and delete requests to ensure the client has an up-to-date value
   * before proceeding.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * verify whether the resource exists, but do not actually post it.
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
   * Gets details of a single Automation. (automations.get)
   *
   * @param string $name Required. Name of the `Automation`. Format must be `proje
   * cts/{project_id}/locations/{location_name}/deliveryPipelines/{pipeline_name}/
   * automations/{automation_name}`.
   * @param array $optParams Optional parameters.
   * @return Automation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Automation::class);
  }
  /**
   * Lists Automations in a given project and location.
   * (automations.listProjectsLocationsDeliveryPipelinesAutomations)
   *
   * @param string $parent Required. The parent `Delivery Pipeline`, which owns
   * this collection of automations. Format must be `projects/{project_id}/locatio
   * ns/{location_name}/deliveryPipelines/{pipeline_name}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filter automations to be returned. All fields can be
   * used in the filter.
   * @opt_param string orderBy Field to sort by.
   * @opt_param int pageSize The maximum number of automations to return. The
   * service may return fewer than this value. If unspecified, at most 50
   * automations will be returned. The maximum value is 1000; values above 1000
   * will be set to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListAutomations` call. Provide this to retrieve the subsequent page. When
   * paginating, all other provided parameters match the call that provided the
   * page token.
   * @return ListAutomationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeliveryPipelinesAutomations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAutomationsResponse::class);
  }
  /**
   * Updates the parameters of a single Automation resource. (automations.patch)
   *
   * @param string $name Output only. Name of the `Automation`. Format is `project
   * s/{project}/locations/{location}/deliveryPipelines/{delivery_pipeline}/automa
   * tions/{automation}`.
   * @param Automation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, updating a
   * `Automation` that does not exist will result in the creation of a new
   * `Automation`.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten by the update in the `Automation` resource. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it's in the mask. If the user
   * doesn't provide a mask then all fields are overwritten.
   * @opt_param bool validateOnly Optional. If set to true, the request is
   * validated and the user is provided with an expected result, but no actual
   * change is made.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Automation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeliveryPipelinesAutomations::class, 'Google_Service_CloudDeploy_Resource_ProjectsLocationsDeliveryPipelinesAutomations');
