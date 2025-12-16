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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaAclConfig extends \Google\Model
{
  protected $idpConfigType = GoogleCloudDiscoveryengineV1betaIdpConfig::class;
  protected $idpConfigDataType = '';
  /**
   * Immutable. The full resource name of the acl configuration. Format:
   * `projects/{project}/locations/{location}/aclConfig`. This field must be a
   * UTF-8 encoded string with a length limit of 1024 characters.
   *
   * @var string
   */
  public $name;

  /**
   * Identity provider config.
   *
   * @param GoogleCloudDiscoveryengineV1betaIdpConfig $idpConfig
   */
  public function setIdpConfig(GoogleCloudDiscoveryengineV1betaIdpConfig $idpConfig)
  {
    $this->idpConfig = $idpConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaIdpConfig
   */
  public function getIdpConfig()
  {
    return $this->idpConfig;
  }
  /**
   * Immutable. The full resource name of the acl configuration. Format:
   * `projects/{project}/locations/{location}/aclConfig`. This field must be a
   * UTF-8 encoded string with a length limit of 1024 characters.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaAclConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAclConfig');
