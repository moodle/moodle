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

namespace Google\Service\Backupdr\Resource;

use Google\Service\Backupdr\InitializeServiceRequest;
use Google\Service\Backupdr\Operation;

/**
 * The "serviceConfig" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $serviceConfig = $backupdrService->projects_locations_serviceConfig;
 *  </code>
 */
class ProjectsLocationsServiceConfig extends \Google\Service\Resource
{
  /**
   * Initializes the service related config for a project.
   * (serviceConfig.initialize)
   *
   * @param string $name Required. The resource name of the serviceConfig used to
   * initialize the service. Format:
   * `projects/{project_id}/locations/{location}/serviceConfig`.
   * @param InitializeServiceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function initialize($name, InitializeServiceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('initialize', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServiceConfig::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsServiceConfig');
