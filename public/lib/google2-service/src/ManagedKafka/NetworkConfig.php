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

class NetworkConfig extends \Google\Model
{
  /**
   * Required. Name of the VPC subnet in which to create Private Service Connect
   * (PSC) endpoints for the Kafka brokers and bootstrap address. Structured
   * like: projects/{project}/regions/{region}/subnetworks/{subnet_id} The
   * subnet must be located in the same region as the Kafka cluster. The project
   * may differ. Multiple subnets from the same parent network must not be
   * specified.
   *
   * @var string
   */
  public $subnet;

  /**
   * Required. Name of the VPC subnet in which to create Private Service Connect
   * (PSC) endpoints for the Kafka brokers and bootstrap address. Structured
   * like: projects/{project}/regions/{region}/subnetworks/{subnet_id} The
   * subnet must be located in the same region as the Kafka cluster. The project
   * may differ. Multiple subnets from the same parent network must not be
   * specified.
   *
   * @param string $subnet
   */
  public function setSubnet($subnet)
  {
    $this->subnet = $subnet;
  }
  /**
   * @return string
   */
  public function getSubnet()
  {
    return $this->subnet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_ManagedKafka_NetworkConfig');
