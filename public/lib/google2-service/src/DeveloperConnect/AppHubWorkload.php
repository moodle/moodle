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

namespace Google\Service\DeveloperConnect;

class AppHubWorkload extends \Google\Model
{
  /**
   * Output only. The criticality of the App Hub Workload.
   *
   * @var string
   */
  public $criticality;
  /**
   * Output only. The environment of the App Hub Workload.
   *
   * @var string
   */
  public $environment;
  /**
   * Required. Output only. Immutable. The name of the App Hub Workload. Format:
   * `projects/{project}/locations/{location}/applications/{application}/workloa
   * ds/{workload}`.
   *
   * @var string
   */
  public $workload;

  /**
   * Output only. The criticality of the App Hub Workload.
   *
   * @param string $criticality
   */
  public function setCriticality($criticality)
  {
    $this->criticality = $criticality;
  }
  /**
   * @return string
   */
  public function getCriticality()
  {
    return $this->criticality;
  }
  /**
   * Output only. The environment of the App Hub Workload.
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
   * Required. Output only. Immutable. The name of the App Hub Workload. Format:
   * `projects/{project}/locations/{location}/applications/{application}/workloa
   * ds/{workload}`.
   *
   * @param string $workload
   */
  public function setWorkload($workload)
  {
    $this->workload = $workload;
  }
  /**
   * @return string
   */
  public function getWorkload()
  {
    return $this->workload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppHubWorkload::class, 'Google_Service_DeveloperConnect_AppHubWorkload');
