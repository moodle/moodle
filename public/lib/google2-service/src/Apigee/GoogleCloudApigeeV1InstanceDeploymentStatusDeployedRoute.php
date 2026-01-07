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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1InstanceDeploymentStatusDeployedRoute extends \Google\Model
{
  /**
   * Base path in the routing table.
   *
   * @var string
   */
  public $basepath;
  /**
   * Environment group where this route is installed.
   *
   * @var string
   */
  public $envgroup;
  /**
   * Destination environment. This will be empty if the route is not yet
   * reported.
   *
   * @var string
   */
  public $environment;
  /**
   * Percentage of ingress replicas reporting this route.
   *
   * @var int
   */
  public $percentage;

  /**
   * Base path in the routing table.
   *
   * @param string $basepath
   */
  public function setBasepath($basepath)
  {
    $this->basepath = $basepath;
  }
  /**
   * @return string
   */
  public function getBasepath()
  {
    return $this->basepath;
  }
  /**
   * Environment group where this route is installed.
   *
   * @param string $envgroup
   */
  public function setEnvgroup($envgroup)
  {
    $this->envgroup = $envgroup;
  }
  /**
   * @return string
   */
  public function getEnvgroup()
  {
    return $this->envgroup;
  }
  /**
   * Destination environment. This will be empty if the route is not yet
   * reported.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Percentage of ingress replicas reporting this route.
   *
   * @param int $percentage
   */
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  /**
   * @return int
   */
  public function getPercentage()
  {
    return $this->percentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1InstanceDeploymentStatusDeployedRoute::class, 'Google_Service_Apigee_GoogleCloudApigeeV1InstanceDeploymentStatusDeployedRoute');
