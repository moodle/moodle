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

class ConnectAccessConfig extends \Google\Collection
{
  protected $collection_key = 'networkConfigs';
  protected $networkConfigsType = ConnectNetworkConfig::class;
  protected $networkConfigsDataType = 'array';

  /**
   * Required. Virtual Private Cloud (VPC) networks that must be granted direct
   * access to the Kafka Connect cluster. Minimum of 1 network is required.
   * Maximum 10 networks can be specified.
   *
   * @param ConnectNetworkConfig[] $networkConfigs
   */
  public function setNetworkConfigs($networkConfigs)
  {
    $this->networkConfigs = $networkConfigs;
  }
  /**
   * @return ConnectNetworkConfig[]
   */
  public function getNetworkConfigs()
  {
    return $this->networkConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectAccessConfig::class, 'Google_Service_ManagedKafka_ConnectAccessConfig');
