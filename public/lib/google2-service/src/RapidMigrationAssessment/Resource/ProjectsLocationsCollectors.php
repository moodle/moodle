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

namespace Google\Service\RapidMigrationAssessment\Resource;

use Google\Service\RapidMigrationAssessment\Collector;
use Google\Service\RapidMigrationAssessment\ListCollectorsResponse;
use Google\Service\RapidMigrationAssessment\Operation;
use Google\Service\RapidMigrationAssessment\PauseCollectorRequest;
use Google\Service\RapidMigrationAssessment\RegisterCollectorRequest;
use Google\Service\RapidMigrationAssessment\ResumeCollectorRequest;

/**
 * The "collectors" collection of methods.
 * Typical usage is:
 *  <code>
 *   $rapidmigrationassessmentService = new Google\Service\RapidMigrationAssessment(...);
 *   $collectors = $rapidmigrationassessmentService->projects_locations_collectors;
 *  </code>
 */
class ProjectsLocationsCollectors extends \Google\Service\Resource
{
  /**
   * Create a Collector to manage the on-prem appliance which collects information
   * about Customer assets. (collectors.create)
   *
   * @param string $parent Required. Name of the parent (project+location).
   * @param Collector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string collectorId Required. Id of the requesting object.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Collector $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Collector - changes state of collector to "Deleting".
   * Background jobs does final deletion through producer API. (collectors.delete)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   *
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
   * Gets details of a single Collector. (collectors.get)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   * @return Collector
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Collector::class);
  }
  /**
   * Lists Collectors in a given project and location.
   * (collectors.listProjectsLocationsCollectors)
   *
   * @param string $parent Required. Parent value for ListCollectorsRequest.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results.
   * @opt_param string orderBy Hint for how to order the results.
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListCollectorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectors($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCollectorsResponse::class);
  }
  /**
   * Updates the parameters of a single Collector. (collectors.patch)
   *
   * @param string $name name of resource.
   * @param Collector $postBody
   * @param array $optParams Optional parameters.
   *
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
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Collector resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Collector $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Pauses the given collector. (collectors.pause)
   *
   * @param string $name Required. Name of the resource.
   * @param PauseCollectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function pause($name, PauseCollectorRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], Operation::class);
  }
  /**
   * Registers the given collector. (collectors.register)
   *
   * @param string $name Required. Name of the resource.
   * @param RegisterCollectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function register($name, RegisterCollectorRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('register', [$params], Operation::class);
  }
  /**
   * Resumes the given collector. (collectors.resume)
   *
   * @param string $name Required. Name of the resource.
   * @param ResumeCollectorRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resume($name, ResumeCollectorRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectors::class, 'Google_Service_RapidMigrationAssessment_Resource_ProjectsLocationsCollectors');
