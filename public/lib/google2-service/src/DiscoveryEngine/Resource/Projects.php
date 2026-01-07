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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ProvisionProjectRequest;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "projects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $projects = $discoveryengineService->projects;
 *  </code>
 */
class Projects extends \Google\Service\Resource
{
  /**
   * Provisions the project resource. During the process, related systems will get
   * prepared and initialized. Caller must read the [Terms for data
   * use](https://cloud.google.com/retail/data-use-terms), and optionally specify
   * in request to provide consent to that service terms. (projects.provision)
   *
   * @param string $name Required. Full resource name of a Project, such as
   * `projects/{project_id_or_number}`.
   * @param GoogleCloudDiscoveryengineV1ProvisionProjectRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function provision($name, GoogleCloudDiscoveryengineV1ProvisionProjectRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provision', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Projects::class, 'Google_Service_DiscoveryEngine_Resource_Projects');
