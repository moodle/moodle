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

class DefaultClusterConfig extends \Google\Model
{
  protected $binaryAuthorizationConfigType = BinaryAuthorizationConfig::class;
  protected $binaryAuthorizationConfigDataType = '';
  protected $securityPostureConfigType = SecurityPostureConfig::class;
  protected $securityPostureConfigDataType = '';

  /**
   * @param BinaryAuthorizationConfig
   */
  public function setBinaryAuthorizationConfig(BinaryAuthorizationConfig $binaryAuthorizationConfig)
  {
    $this->binaryAuthorizationConfig = $binaryAuthorizationConfig;
  }
  /**
   * @return BinaryAuthorizationConfig
   */
  public function getBinaryAuthorizationConfig()
  {
    return $this->binaryAuthorizationConfig;
  }
  /**
   * @param SecurityPostureConfig
   */
  public function setSecurityPostureConfig(SecurityPostureConfig $securityPostureConfig)
  {
    $this->securityPostureConfig = $securityPostureConfig;
  }
  /**
   * @return SecurityPostureConfig
   */
  public function getSecurityPostureConfig()
  {
    return $this->securityPostureConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultClusterConfig::class, 'Google_Service_GKEHub_DefaultClusterConfig');
