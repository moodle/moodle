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

class AutoprovisioningNodePoolDefaults extends \Google\Collection
{
  protected $collection_key = 'oauthScopes';
  /**
   * The Customer Managed Encryption Key used to encrypt the boot disk attached
   * to each node in the node pool. This should be of the form projects/[KEY_PRO
   * JECT_ID]/locations/[LOCATION]/keyRings/[RING_NAME]/cryptoKeys/[KEY_NAME].
   * For more information about protecting resources with Cloud KMS Keys please
   * see: https://cloud.google.com/compute/docs/disks/customer-managed-
   * encryption
   *
   * @var string
   */
  public $bootDiskKmsKey;
  /**
   * Size of the disk attached to each node, specified in GB. The smallest
   * allowed disk size is 10GB. If unspecified, the default disk size is 100GB.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * Type of the disk attached to each node (e.g. 'pd-standard', 'pd-ssd' or
   * 'pd-balanced') If unspecified, the default disk type is 'pd-standard'
   *
   * @var string
   */
  public $diskType;
  /**
   * The image type to use for NAP created node. Please see
   * https://cloud.google.com/kubernetes-engine/docs/concepts/node-images for
   * available image types.
   *
   * @var string
   */
  public $imageType;
  /**
   * DEPRECATED. Use NodePoolAutoConfig.NodeKubeletConfig instead.
   *
   * @var bool
   */
  public $insecureKubeletReadonlyPortEnabled;
  protected $managementType = NodeManagement::class;
  protected $managementDataType = '';
  /**
   * Deprecated. Minimum CPU platform to be used for NAP created node pools. The
   * instance may be scheduled on the specified or newer CPU platform.
   * Applicable values are the friendly names of CPU platforms, such as
   * minCpuPlatform: Intel Haswell or minCpuPlatform: Intel Sandy Bridge. For
   * more information, read [how to specify min CPU
   * platform](https://cloud.google.com/compute/docs/instances/specify-min-cpu-
   * platform). This field is deprecated, min_cpu_platform should be specified
   * using `cloud.google.com/requested-min-cpu-platform` label selector on the
   * pod. To unset the min cpu platform field pass "automatic" as field value.
   *
   * @deprecated
   * @var string
   */
  public $minCpuPlatform;
  /**
   * Scopes that are used by NAP when creating node pools.
   *
   * @var string[]
   */
  public $oauthScopes;
  /**
   * The Google Cloud Platform Service Account to be used by the node VMs.
   *
   * @var string
   */
  public $serviceAccount;
  protected $shieldedInstanceConfigType = ShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  protected $upgradeSettingsType = UpgradeSettings::class;
  protected $upgradeSettingsDataType = '';

  /**
   * The Customer Managed Encryption Key used to encrypt the boot disk attached
   * to each node in the node pool. This should be of the form projects/[KEY_PRO
   * JECT_ID]/locations/[LOCATION]/keyRings/[RING_NAME]/cryptoKeys/[KEY_NAME].
   * For more information about protecting resources with Cloud KMS Keys please
   * see: https://cloud.google.com/compute/docs/disks/customer-managed-
   * encryption
   *
   * @param string $bootDiskKmsKey
   */
  public function setBootDiskKmsKey($bootDiskKmsKey)
  {
    $this->bootDiskKmsKey = $bootDiskKmsKey;
  }
  /**
   * @return string
   */
  public function getBootDiskKmsKey()
  {
    return $this->bootDiskKmsKey;
  }
  /**
   * Size of the disk attached to each node, specified in GB. The smallest
   * allowed disk size is 10GB. If unspecified, the default disk size is 100GB.
   *
   * @param int $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return int
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Type of the disk attached to each node (e.g. 'pd-standard', 'pd-ssd' or
   * 'pd-balanced') If unspecified, the default disk type is 'pd-standard'
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * The image type to use for NAP created node. Please see
   * https://cloud.google.com/kubernetes-engine/docs/concepts/node-images for
   * available image types.
   *
   * @param string $imageType
   */
  public function setImageType($imageType)
  {
    $this->imageType = $imageType;
  }
  /**
   * @return string
   */
  public function getImageType()
  {
    return $this->imageType;
  }
  /**
   * DEPRECATED. Use NodePoolAutoConfig.NodeKubeletConfig instead.
   *
   * @param bool $insecureKubeletReadonlyPortEnabled
   */
  public function setInsecureKubeletReadonlyPortEnabled($insecureKubeletReadonlyPortEnabled)
  {
    $this->insecureKubeletReadonlyPortEnabled = $insecureKubeletReadonlyPortEnabled;
  }
  /**
   * @return bool
   */
  public function getInsecureKubeletReadonlyPortEnabled()
  {
    return $this->insecureKubeletReadonlyPortEnabled;
  }
  /**
   * Specifies the node management options for NAP created node-pools.
   *
   * @param NodeManagement $management
   */
  public function setManagement(NodeManagement $management)
  {
    $this->management = $management;
  }
  /**
   * @return NodeManagement
   */
  public function getManagement()
  {
    return $this->management;
  }
  /**
   * Deprecated. Minimum CPU platform to be used for NAP created node pools. The
   * instance may be scheduled on the specified or newer CPU platform.
   * Applicable values are the friendly names of CPU platforms, such as
   * minCpuPlatform: Intel Haswell or minCpuPlatform: Intel Sandy Bridge. For
   * more information, read [how to specify min CPU
   * platform](https://cloud.google.com/compute/docs/instances/specify-min-cpu-
   * platform). This field is deprecated, min_cpu_platform should be specified
   * using `cloud.google.com/requested-min-cpu-platform` label selector on the
   * pod. To unset the min cpu platform field pass "automatic" as field value.
   *
   * @deprecated
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
  /**
   * Scopes that are used by NAP when creating node pools.
   *
   * @param string[] $oauthScopes
   */
  public function setOauthScopes($oauthScopes)
  {
    $this->oauthScopes = $oauthScopes;
  }
  /**
   * @return string[]
   */
  public function getOauthScopes()
  {
    return $this->oauthScopes;
  }
  /**
   * The Google Cloud Platform Service Account to be used by the node VMs.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Shielded Instance options.
   *
   * @param ShieldedInstanceConfig $shieldedInstanceConfig
   */
  public function setShieldedInstanceConfig(ShieldedInstanceConfig $shieldedInstanceConfig)
  {
    $this->shieldedInstanceConfig = $shieldedInstanceConfig;
  }
  /**
   * @return ShieldedInstanceConfig
   */
  public function getShieldedInstanceConfig()
  {
    return $this->shieldedInstanceConfig;
  }
  /**
   * Specifies the upgrade settings for NAP created node pools
   *
   * @param UpgradeSettings $upgradeSettings
   */
  public function setUpgradeSettings(UpgradeSettings $upgradeSettings)
  {
    $this->upgradeSettings = $upgradeSettings;
  }
  /**
   * @return UpgradeSettings
   */
  public function getUpgradeSettings()
  {
    return $this->upgradeSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoprovisioningNodePoolDefaults::class, 'Google_Service_Container_AutoprovisioningNodePoolDefaults');
