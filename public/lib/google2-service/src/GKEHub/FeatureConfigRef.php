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

namespace Google\Service\GKEHub;

class FeatureConfigRef extends \Google\Model
{
  /**
   * @var string
   */
  public $config;
  /**
   * @var string
   */
  public $configUpdateTime;
  /**
   * @var string
   */
  public $uuid;

  /**
   * @param string
   */
  public function setConfig($config)
  {
    $this->config = $config;
  }
  /**
   * @return string
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * @param string
   */
  public function setConfigUpdateTime($configUpdateTime)
  {
    $this->configUpdateTime = $configUpdateTime;
  }
  /**
   * @return string
   */
  public function getConfigUpdateTime()
  {
    return $this->configUpdateTime;
  }
  /**
   * @param string
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FeatureConfigRef::class, 'Google_Service_GKEHub_FeatureConfigRef');
