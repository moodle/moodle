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

namespace Google\Service\Container;

class MasterAuthorizedNetworksConfig extends \Google\Collection
{
  protected $collection_key = 'cidrBlocks';
  protected $cidrBlocksType = CidrBlock::class;
  protected $cidrBlocksDataType = 'array';
  /**
   * Whether or not master authorized networks is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Whether master is accessible via Google Compute Engine Public IP addresses.
   *
   * @var bool
   */
  public $gcpPublicCidrsAccessEnabled;
  /**
   * Whether master authorized networks is enforced on private endpoint or not.
   *
   * @var bool
   */
  public $privateEndpointEnforcementEnabled;

  /**
   * cidr_blocks define up to 50 external networks that could access Kubernetes
   * master through HTTPS.
   *
   * @param CidrBlock[] $cidrBlocks
   */
  public function setCidrBlocks($cidrBlocks)
  {
    $this->cidrBlocks = $cidrBlocks;
  }
  /**
   * @return CidrBlock[]
   */
  public function getCidrBlocks()
  {
    return $this->cidrBlocks;
  }
  /**
   * Whether or not master authorized networks is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Whether master is accessible via Google Compute Engine Public IP addresses.
   *
   * @param bool $gcpPublicCidrsAccessEnabled
   */
  public function setGcpPublicCidrsAccessEnabled($gcpPublicCidrsAccessEnabled)
  {
    $this->gcpPublicCidrsAccessEnabled = $gcpPublicCidrsAccessEnabled;
  }
  /**
   * @return bool
   */
  public function getGcpPublicCidrsAccessEnabled()
  {
    return $this->gcpPublicCidrsAccessEnabled;
  }
  /**
   * Whether master authorized networks is enforced on private endpoint or not.
   *
   * @param bool $privateEndpointEnforcementEnabled
   */
  public function setPrivateEndpointEnforcementEnabled($privateEndpointEnforcementEnabled)
  {
    $this->privateEndpointEnforcementEnabled = $privateEndpointEnforcementEnabled;
  }
  /**
   * @return bool
   */
  public function getPrivateEndpointEnforcementEnabled()
  {
    return $this->privateEndpointEnforcementEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MasterAuthorizedNetworksConfig::class, 'Google_Service_Container_MasterAuthorizedNetworksConfig');
