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

namespace Google\Service\Baremetalsolution;

class ProvisioningQuota extends \Google\Model
{
  /**
   * The unspecified type.
   */
  public const ASSET_TYPE_ASSET_TYPE_UNSPECIFIED = 'ASSET_TYPE_UNSPECIFIED';
  /**
   * The server asset type.
   */
  public const ASSET_TYPE_ASSET_TYPE_SERVER = 'ASSET_TYPE_SERVER';
  /**
   * The storage asset type.
   */
  public const ASSET_TYPE_ASSET_TYPE_STORAGE = 'ASSET_TYPE_STORAGE';
  /**
   * The network asset type.
   */
  public const ASSET_TYPE_ASSET_TYPE_NETWORK = 'ASSET_TYPE_NETWORK';
  /**
   * The asset type of this provisioning quota.
   *
   * @var string
   */
  public $assetType;
  /**
   * The available count of the provisioning quota.
   *
   * @var int
   */
  public $availableCount;
  /**
   * The gcp service of the provisioning quota.
   *
   * @var string
   */
  public $gcpService;
  protected $instanceQuotaType = InstanceQuota::class;
  protected $instanceQuotaDataType = '';
  /**
   * The specific location of the provisioining quota.
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The name of the provisioning quota.
   *
   * @var string
   */
  public $name;
  /**
   * Network bandwidth, Gbps
   *
   * @var string
   */
  public $networkBandwidth;
  /**
   * Server count.
   *
   * @var string
   */
  public $serverCount;
  /**
   * Storage size (GB).
   *
   * @var string
   */
  public $storageGib;

  /**
   * The asset type of this provisioning quota.
   *
   * Accepted values: ASSET_TYPE_UNSPECIFIED, ASSET_TYPE_SERVER,
   * ASSET_TYPE_STORAGE, ASSET_TYPE_NETWORK
   *
   * @param self::ASSET_TYPE_* $assetType
   */
  public function setAssetType($assetType)
  {
    $this->assetType = $assetType;
  }
  /**
   * @return self::ASSET_TYPE_*
   */
  public function getAssetType()
  {
    return $this->assetType;
  }
  /**
   * The available count of the provisioning quota.
   *
   * @param int $availableCount
   */
  public function setAvailableCount($availableCount)
  {
    $this->availableCount = $availableCount;
  }
  /**
   * @return int
   */
  public function getAvailableCount()
  {
    return $this->availableCount;
  }
  /**
   * The gcp service of the provisioning quota.
   *
   * @param string $gcpService
   */
  public function setGcpService($gcpService)
  {
    $this->gcpService = $gcpService;
  }
  /**
   * @return string
   */
  public function getGcpService()
  {
    return $this->gcpService;
  }
  /**
   * Instance quota.
   *
   * @param InstanceQuota $instanceQuota
   */
  public function setInstanceQuota(InstanceQuota $instanceQuota)
  {
    $this->instanceQuota = $instanceQuota;
  }
  /**
   * @return InstanceQuota
   */
  public function getInstanceQuota()
  {
    return $this->instanceQuota;
  }
  /**
   * The specific location of the provisioining quota.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. The name of the provisioning quota.
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
  /**
   * Network bandwidth, Gbps
   *
   * @param string $networkBandwidth
   */
  public function setNetworkBandwidth($networkBandwidth)
  {
    $this->networkBandwidth = $networkBandwidth;
  }
  /**
   * @return string
   */
  public function getNetworkBandwidth()
  {
    return $this->networkBandwidth;
  }
  /**
   * Server count.
   *
   * @param string $serverCount
   */
  public function setServerCount($serverCount)
  {
    $this->serverCount = $serverCount;
  }
  /**
   * @return string
   */
  public function getServerCount()
  {
    return $this->serverCount;
  }
  /**
   * Storage size (GB).
   *
   * @param string $storageGib
   */
  public function setStorageGib($storageGib)
  {
    $this->storageGib = $storageGib;
  }
  /**
   * @return string
   */
  public function getStorageGib()
  {
    return $this->storageGib;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisioningQuota::class, 'Google_Service_Baremetalsolution_ProvisioningQuota');
