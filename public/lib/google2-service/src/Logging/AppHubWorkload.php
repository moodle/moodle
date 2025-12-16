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

namespace Google\Service\Logging;

class AppHubWorkload extends \Google\Model
{
  /**
   * Workload criticality type Example: "CRITICAL"
   *
   * @var string
   */
  public $criticalityType;
  /**
   * Workload environment type Example: "DEV"
   *
   * @var string
   */
  public $environmentType;
  /**
   * Workload Id. Example: "my-workload"
   *
   * @var string
   */
  public $id;

  /**
   * Workload criticality type Example: "CRITICAL"
   *
   * @param string $criticalityType
   */
  public function setCriticalityType($criticalityType)
  {
    $this->criticalityType = $criticalityType;
  }
  /**
   * @return string
   */
  public function getCriticalityType()
  {
    return $this->criticalityType;
  }
  /**
   * Workload environment type Example: "DEV"
   *
   * @param string $environmentType
   */
  public function setEnvironmentType($environmentType)
  {
    $this->environmentType = $environmentType;
  }
  /**
   * @return string
   */
  public function getEnvironmentType()
  {
    return $this->environmentType;
  }
  /**
   * Workload Id. Example: "my-workload"
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppHubWorkload::class, 'Google_Service_Logging_AppHubWorkload');
