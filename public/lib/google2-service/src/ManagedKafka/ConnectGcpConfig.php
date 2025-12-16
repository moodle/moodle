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

namespace Google\Service\ManagedKafka;

class ConnectGcpConfig extends \Google\Collection
{
  protected $collection_key = 'secretPaths';
  protected $accessConfigType = ConnectAccessConfig::class;
  protected $accessConfigDataType = '';
  /**
   * Optional. Secrets to load into workers. Exact SecretVersions from Secret
   * Manager must be provided -- aliases are not supported. Up to 32 secrets may
   * be loaded into one cluster. Format: projects//secrets//versions/
   *
   * @var string[]
   */
  public $secretPaths;

  /**
   * Required. Access configuration for the Kafka Connect cluster.
   *
   * @param ConnectAccessConfig $accessConfig
   */
  public function setAccessConfig(ConnectAccessConfig $accessConfig)
  {
    $this->accessConfig = $accessConfig;
  }
  /**
   * @return ConnectAccessConfig
   */
  public function getAccessConfig()
  {
    return $this->accessConfig;
  }
  /**
   * Optional. Secrets to load into workers. Exact SecretVersions from Secret
   * Manager must be provided -- aliases are not supported. Up to 32 secrets may
   * be loaded into one cluster. Format: projects//secrets//versions/
   *
   * @param string[] $secretPaths
   */
  public function setSecretPaths($secretPaths)
  {
    $this->secretPaths = $secretPaths;
  }
  /**
   * @return string[]
   */
  public function getSecretPaths()
  {
    return $this->secretPaths;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectGcpConfig::class, 'Google_Service_ManagedKafka_ConnectGcpConfig');
