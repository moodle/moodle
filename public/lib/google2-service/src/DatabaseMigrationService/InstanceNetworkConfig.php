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

namespace Google\Service\DatabaseMigrationService;

class InstanceNetworkConfig extends \Google\Collection
{
  protected $collection_key = 'authorizedExternalNetworks';
  protected $authorizedExternalNetworksType = AuthorizedNetwork::class;
  protected $authorizedExternalNetworksDataType = 'array';
  /**
   * Optional. Enabling an outbound public IP address to support a database
   * server sending requests out into the internet.
   *
   * @var bool
   */
  public $enableOutboundPublicIp;
  /**
   * Optional. Enabling public ip for the instance.
   *
   * @var bool
   */
  public $enablePublicIp;

  /**
   * Optional. A list of external network authorized to access this instance.
   *
   * @param AuthorizedNetwork[] $authorizedExternalNetworks
   */
  public function setAuthorizedExternalNetworks($authorizedExternalNetworks)
  {
    $this->authorizedExternalNetworks = $authorizedExternalNetworks;
  }
  /**
   * @return AuthorizedNetwork[]
   */
  public function getAuthorizedExternalNetworks()
  {
    return $this->authorizedExternalNetworks;
  }
  /**
   * Optional. Enabling an outbound public IP address to support a database
   * server sending requests out into the internet.
   *
   * @param bool $enableOutboundPublicIp
   */
  public function setEnableOutboundPublicIp($enableOutboundPublicIp)
  {
    $this->enableOutboundPublicIp = $enableOutboundPublicIp;
  }
  /**
   * @return bool
   */
  public function getEnableOutboundPublicIp()
  {
    return $this->enableOutboundPublicIp;
  }
  /**
   * Optional. Enabling public ip for the instance.
   *
   * @param bool $enablePublicIp
   */
  public function setEnablePublicIp($enablePublicIp)
  {
    $this->enablePublicIp = $enablePublicIp;
  }
  /**
   * @return bool
   */
  public function getEnablePublicIp()
  {
    return $this->enablePublicIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceNetworkConfig::class, 'Google_Service_DatabaseMigrationService_InstanceNetworkConfig');
