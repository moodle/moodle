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

namespace Google\Service\Backupdr;

class NetworkConfig extends \Google\Model
{
  /**
   * Peering mode not set.
   */
  public const PEERING_MODE_PEERING_MODE_UNSPECIFIED = 'PEERING_MODE_UNSPECIFIED';
  /**
   * Connect using Private Service Access to the Management Server. Private
   * services access provides an IP address range for multiple Google Cloud
   * services, including Cloud BackupDR.
   */
  public const PEERING_MODE_PRIVATE_SERVICE_ACCESS = 'PRIVATE_SERVICE_ACCESS';
  /**
   * Optional. The resource name of the Google Compute Engine VPC network to
   * which the ManagementServer instance is connected.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The network connect mode of the ManagementServer instance. For
   * this version, only PRIVATE_SERVICE_ACCESS is supported.
   *
   * @var string
   */
  public $peeringMode;

  /**
   * Optional. The resource name of the Google Compute Engine VPC network to
   * which the ManagementServer instance is connected.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. The network connect mode of the ManagementServer instance. For
   * this version, only PRIVATE_SERVICE_ACCESS is supported.
   *
   * Accepted values: PEERING_MODE_UNSPECIFIED, PRIVATE_SERVICE_ACCESS
   *
   * @param self::PEERING_MODE_* $peeringMode
   */
  public function setPeeringMode($peeringMode)
  {
    $this->peeringMode = $peeringMode;
  }
  /**
   * @return self::PEERING_MODE_*
   */
  public function getPeeringMode()
  {
    return $this->peeringMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_Backupdr_NetworkConfig');
