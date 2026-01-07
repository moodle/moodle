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

namespace Google\Service\Looker\Resource;

use Google\Service\Looker\ExportInstanceRequest;
use Google\Service\Looker\ImportInstanceRequest;
use Google\Service\Looker\Instance;
use Google\Service\Looker\ListInstancesResponse;
use Google\Service\Looker\Operation;
use Google\Service\Looker\RestartInstanceRequest;
use Google\Service\Looker\RestoreInstanceRequest;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $lookerService = new Google\Service\Looker(...);
 *   $instances = $lookerService->projects_locations_instances;
 *  </code>
 */
class ProjectsLocationsInstances extends \Google\Service\Resource
{
  /**
   * Creates a new Instance in a given project and location. (instances.create)
   *
   * @param string $parent Required. Format:
   * `projects/{project}/locations/{location}`.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instanceId Required. The unique instance identifier. Must
   * contain only lowercase letters, numbers, or hyphens, with the first character
   * a letter and the last a letter or a number. 63 characters maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Instance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Delete instance. (instances.delete)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Whether to force cascading delete.
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
   * Export instance. (instances.export)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param ExportInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function export($name, ExportInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], Operation::class);
  }
  /**
   * Gets details of a single Instance. (instances.get)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param array $optParams Optional parameters.
   * @return Instance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Instance::class);
  }
  /**
   * Import instance. (instances.import)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param ImportInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function import($name, ImportInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], Operation::class);
  }
  /**
   * Lists Instances in a given project and location.
   * (instances.listProjectsLocationsInstances)
   *
   * @param string $parent Required. Format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of instances to return. If
   * unspecified at most 256 will be returned. The maximum possible value is 2048.
   * @opt_param string pageToken A page token received from a previous
   * ListInstancesRequest.
   * @return ListInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInstances($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancesResponse::class);
  }
  /**
   * Update Instance. (instances.patch)
   *
   * @param string $name Output only. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask used to specify the fields
   * to be overwritten in the Instance resource by the update. The fields
   * specified in the mask are relative to the resource, not the full request. A
   * field will be overwritten if it is in the mask.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Instance $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Restart instance. (instances.restart)
   *
   * @param string $name Required. Format:
   * `projects/{project}/locations/{location}/instances/{instance}`.
   * @param RestartInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restart($name, RestartInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restart', [$params], Operation::class);
  }
  /**
   * Restore Looker instance. (instances.restore)
   *
   * @param string $name Required. Instance being restored Format:
   * projects/{project}/locations/{location}/instances/{instance}
   * @param RestoreInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restore($name, RestoreInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInstances::class, 'Google_Service_Looker_Resource_ProjectsLocationsInstances');
