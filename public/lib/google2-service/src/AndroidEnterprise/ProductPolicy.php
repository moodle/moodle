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

namespace Google\Service\AndroidEnterprise;

class ProductPolicy extends \Google\Collection
{
  /**
   * Unspecified. Defaults to AUTO_UPDATE_DEFAULT.
   */
  public const AUTO_UPDATE_MODE_autoUpdateModeUnspecified = 'autoUpdateModeUnspecified';
  /**
   * The app is automatically updated with low priority to minimize the impact
   * on the user. The app is updated when the following constraints are met: *
   * The device is not actively used * The device is connected to an unmetered
   * network * The device is charging The device is notified about a new update
   * within 24 hours after it is published by the developer, after which the app
   * is updated the next time the constraints above are met.
   */
  public const AUTO_UPDATE_MODE_autoUpdateDefault = 'autoUpdateDefault';
  /**
   * The app is not automatically updated for a maximum of 90 days after the app
   * becomes out of date. 90 days after the app becomes out of date, the latest
   * available version is installed automatically with low priority (see
   * AUTO_UPDATE_DEFAULT). After the app is updated it is not automatically
   * updated again until 90 days after it becomes out of date again. The user
   * can still manually update the app from the Play Store at any time.
   */
  public const AUTO_UPDATE_MODE_autoUpdatePostponed = 'autoUpdatePostponed';
  /**
   * The app is updated as soon as possible. No constraints are applied. The
   * device is notified as soon as possible about a new app update after it is
   * published by the developer.
   */
  public const AUTO_UPDATE_MODE_autoUpdateHighPriority = 'autoUpdateHighPriority';
  protected $collection_key = 'tracks';
  protected $autoInstallPolicyType = AutoInstallPolicy::class;
  protected $autoInstallPolicyDataType = '';
  /**
   * The auto-update mode for the product. When autoUpdateMode is used, it
   * always takes precedence over the user's choice. So when a user makes
   * changes to the device settings manually, these changes are ignored.
   *
   * @var string
   */
  public $autoUpdateMode;
  protected $enterpriseAuthenticationAppLinkConfigsType = EnterpriseAuthenticationAppLinkConfig::class;
  protected $enterpriseAuthenticationAppLinkConfigsDataType = 'array';
  protected $managedConfigurationType = ManagedConfiguration::class;
  protected $managedConfigurationDataType = '';
  /**
   * The ID of the product. For example, "app:com.google.android.gm".
   *
   * @var string
   */
  public $productId;
  /**
   * Grants the device visibility to the specified product release track(s),
   * identified by trackIds. The list of release tracks of a product can be
   * obtained by calling Products.Get.
   *
   * @var string[]
   */
  public $trackIds;
  /**
   * Deprecated. Use trackIds instead.
   *
   * @var string[]
   */
  public $tracks;

  /**
   * The auto-install policy for the product.
   *
   * @param AutoInstallPolicy $autoInstallPolicy
   */
  public function setAutoInstallPolicy(AutoInstallPolicy $autoInstallPolicy)
  {
    $this->autoInstallPolicy = $autoInstallPolicy;
  }
  /**
   * @return AutoInstallPolicy
   */
  public function getAutoInstallPolicy()
  {
    return $this->autoInstallPolicy;
  }
  /**
   * The auto-update mode for the product. When autoUpdateMode is used, it
   * always takes precedence over the user's choice. So when a user makes
   * changes to the device settings manually, these changes are ignored.
   *
   * Accepted values: autoUpdateModeUnspecified, autoUpdateDefault,
   * autoUpdatePostponed, autoUpdateHighPriority
   *
   * @param self::AUTO_UPDATE_MODE_* $autoUpdateMode
   */
  public function setAutoUpdateMode($autoUpdateMode)
  {
    $this->autoUpdateMode = $autoUpdateMode;
  }
  /**
   * @return self::AUTO_UPDATE_MODE_*
   */
  public function getAutoUpdateMode()
  {
    return $this->autoUpdateMode;
  }
  /**
   * An authentication URL configuration for the authenticator app of an
   * identity provider. This helps to launch the identity provider's
   * authenticator app during the authentication happening in a private app
   * using Android WebView. Authenticator app should already be the default
   * handler for the authentication url on the device.
   *
   * @param EnterpriseAuthenticationAppLinkConfig[] $enterpriseAuthenticationAppLinkConfigs
   */
  public function setEnterpriseAuthenticationAppLinkConfigs($enterpriseAuthenticationAppLinkConfigs)
  {
    $this->enterpriseAuthenticationAppLinkConfigs = $enterpriseAuthenticationAppLinkConfigs;
  }
  /**
   * @return EnterpriseAuthenticationAppLinkConfig[]
   */
  public function getEnterpriseAuthenticationAppLinkConfigs()
  {
    return $this->enterpriseAuthenticationAppLinkConfigs;
  }
  /**
   * The managed configuration for the product.
   *
   * @param ManagedConfiguration $managedConfiguration
   */
  public function setManagedConfiguration(ManagedConfiguration $managedConfiguration)
  {
    $this->managedConfiguration = $managedConfiguration;
  }
  /**
   * @return ManagedConfiguration
   */
  public function getManagedConfiguration()
  {
    return $this->managedConfiguration;
  }
  /**
   * The ID of the product. For example, "app:com.google.android.gm".
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * Grants the device visibility to the specified product release track(s),
   * identified by trackIds. The list of release tracks of a product can be
   * obtained by calling Products.Get.
   *
   * @param string[] $trackIds
   */
  public function setTrackIds($trackIds)
  {
    $this->trackIds = $trackIds;
  }
  /**
   * @return string[]
   */
  public function getTrackIds()
  {
    return $this->trackIds;
  }
  /**
   * Deprecated. Use trackIds instead.
   *
   * @param string[] $tracks
   */
  public function setTracks($tracks)
  {
    $this->tracks = $tracks;
  }
  /**
   * @return string[]
   */
  public function getTracks()
  {
    return $this->tracks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductPolicy::class, 'Google_Service_AndroidEnterprise_ProductPolicy');
