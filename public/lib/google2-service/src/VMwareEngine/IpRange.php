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

namespace Google\Service\VMwareEngine;

class IpRange extends \Google\Model
{
  /**
   * The name of an `ExternalAddress` resource. The external address must have
   * been reserved in the scope of this external access rule's parent network
   * policy. Provide the external address name in the form of `projects/{project
   * }/locations/{location}/privateClouds/{private_cloud}/externalAddresses/{ext
   * ernal_address}`. For example: `projects/my-project/locations/us-
   * central1-a/privateClouds/my-cloud/externalAddresses/my-address`.
   *
   * @var string
   */
  public $externalAddress;
  /**
   * A single IP address. For example: `10.0.0.5`.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * An IP address range in the CIDR format. For example: `10.0.0.0/24`.
   *
   * @var string
   */
  public $ipAddressRange;

  /**
   * The name of an `ExternalAddress` resource. The external address must have
   * been reserved in the scope of this external access rule's parent network
   * policy. Provide the external address name in the form of `projects/{project
   * }/locations/{location}/privateClouds/{private_cloud}/externalAddresses/{ext
   * ernal_address}`. For example: `projects/my-project/locations/us-
   * central1-a/privateClouds/my-cloud/externalAddresses/my-address`.
   *
   * @param string $externalAddress
   */
  public function setExternalAddress($externalAddress)
  {
    $this->externalAddress = $externalAddress;
  }
  /**
   * @return string
   */
  public function getExternalAddress()
  {
    return $this->externalAddress;
  }
  /**
   * A single IP address. For example: `10.0.0.5`.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * An IP address range in the CIDR format. For example: `10.0.0.0/24`.
   *
   * @param string $ipAddressRange
   */
  public function setIpAddressRange($ipAddressRange)
  {
    $this->ipAddressRange = $ipAddressRange;
  }
  /**
   * @return string
   */
  public function getIpAddressRange()
  {
    return $this->ipAddressRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IpRange::class, 'Google_Service_VMwareEngine_IpRange');
