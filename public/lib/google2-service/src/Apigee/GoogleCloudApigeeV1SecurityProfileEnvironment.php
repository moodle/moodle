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

class GoogleCloudApigeeV1SecurityProfileEnvironment extends \Google\Model
{
  /**
   * Output only. Time at which environment was attached to the security
   * profile.
   *
   * @var string
   */
  public $attachTime;
  /**
   * Output only. Name of the environment.
   *
   * @var string
   */
  public $environment;

  /**
   * Output only. Time at which environment was attached to the security
   * profile.
   *
   * @param string $attachTime
   */
  public function setAttachTime($attachTime)
  {
    $this->attachTime = $attachTime;
  }
  /**
   * @return string
   */
  public function getAttachTime()
  {
    return $this->attachTime;
  }
  /**
   * Output only. Name of the environment.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityProfileEnvironment::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityProfileEnvironment');
