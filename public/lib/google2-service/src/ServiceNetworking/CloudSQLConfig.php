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

namespace Google\Service\ServiceNetworking;

class CloudSQLConfig extends \Google\Model
{
  /**
   * Required. Peering service used for peering with the Cloud SQL project.
   *
   * @var string
   */
  public $service;
  /**
   * Required. The name of the umbrella network in the Cloud SQL umbrella
   * project.
   *
   * @var string
   */
  public $umbrellaNetwork;
  /**
   * Required. The project number of the Cloud SQL umbrella project.
   *
   * @var string
   */
  public $umbrellaProject;

  /**
   * Required. Peering service used for peering with the Cloud SQL project.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Required. The name of the umbrella network in the Cloud SQL umbrella
   * project.
   *
   * @param string $umbrellaNetwork
   */
  public function setUmbrellaNetwork($umbrellaNetwork)
  {
    $this->umbrellaNetwork = $umbrellaNetwork;
  }
  /**
   * @return string
   */
  public function getUmbrellaNetwork()
  {
    return $this->umbrellaNetwork;
  }
  /**
   * Required. The project number of the Cloud SQL umbrella project.
   *
   * @param string $umbrellaProject
   */
  public function setUmbrellaProject($umbrellaProject)
  {
    $this->umbrellaProject = $umbrellaProject;
  }
  /**
   * @return string
   */
  public function getUmbrellaProject()
  {
    return $this->umbrellaProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudSQLConfig::class, 'Google_Service_ServiceNetworking_CloudSQLConfig');
