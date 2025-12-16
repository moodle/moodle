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

class Device extends \Google\Model
{
  public const MANAGEMENT_TYPE_managedDevice = 'managedDevice';
  public const MANAGEMENT_TYPE_managedProfile = 'managedProfile';
  public const MANAGEMENT_TYPE_containerApp = 'containerApp';
  public const MANAGEMENT_TYPE_unmanagedProfile = 'unmanagedProfile';
  /**
   * The Google Play Services Android ID for the device encoded as a lowercase
   * hex string. For example, "123456789abcdef0".
   *
   * @var string
   */
  public $androidId;
  /**
   * The internal hardware codename of the device. This comes from
   * android.os.Build.DEVICE. (field named "device" per
   * logs/wireless/android/android_checkin.proto)
   *
   * @var string
   */
  public $device;
  /**
   * The build fingerprint of the device if known.
   *
   * @var string
   */
  public $latestBuildFingerprint;
  /**
   * The manufacturer of the device. This comes from
   * android.os.Build.MANUFACTURER.
   *
   * @var string
   */
  public $maker;
  /**
   * Identifies the extent to which the device is controlled by a managed Google
   * Play EMM in various deployment configurations. Possible values include: -
   * "managedDevice", a device that has the EMM's device policy controller (DPC)
   * as the device owner. - "managedProfile", a device that has a profile
   * managed by the DPC (DPC is profile owner) in addition to a separate,
   * personal profile that is unavailable to the DPC. - "containerApp", no
   * longer used (deprecated). - "unmanagedProfile", a device that has been
   * allowed (by the domain's admin, using the Admin Console to enable the
   * privilege) to use managed Google Play, but the profile is itself not owned
   * by a DPC.
   *
   * @var string
   */
  public $managementType;
  /**
   * The model name of the device. This comes from android.os.Build.MODEL.
   *
   * @var string
   */
  public $model;
  protected $policyType = Policy::class;
  protected $policyDataType = '';
  /**
   * The product name of the device. This comes from android.os.Build.PRODUCT.
   *
   * @var string
   */
  public $product;
  protected $reportType = DeviceReport::class;
  protected $reportDataType = '';
  /**
   * Retail brand for the device, if set. See android.os.Build.BRAND
   *
   * @var string
   */
  public $retailBrand;
  /**
   * API compatibility version.
   *
   * @var int
   */
  public $sdkVersion;

  /**
   * The Google Play Services Android ID for the device encoded as a lowercase
   * hex string. For example, "123456789abcdef0".
   *
   * @param string $androidId
   */
  public function setAndroidId($androidId)
  {
    $this->androidId = $androidId;
  }
  /**
   * @return string
   */
  public function getAndroidId()
  {
    return $this->androidId;
  }
  /**
   * The internal hardware codename of the device. This comes from
   * android.os.Build.DEVICE. (field named "device" per
   * logs/wireless/android/android_checkin.proto)
   *
   * @param string $device
   */
  public function setDevice($device)
  {
    $this->device = $device;
  }
  /**
   * @return string
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * The build fingerprint of the device if known.
   *
   * @param string $latestBuildFingerprint
   */
  public function setLatestBuildFingerprint($latestBuildFingerprint)
  {
    $this->latestBuildFingerprint = $latestBuildFingerprint;
  }
  /**
   * @return string
   */
  public function getLatestBuildFingerprint()
  {
    return $this->latestBuildFingerprint;
  }
  /**
   * The manufacturer of the device. This comes from
   * android.os.Build.MANUFACTURER.
   *
   * @param string $maker
   */
  public function setMaker($maker)
  {
    $this->maker = $maker;
  }
  /**
   * @return string
   */
  public function getMaker()
  {
    return $this->maker;
  }
  /**
   * Identifies the extent to which the device is controlled by a managed Google
   * Play EMM in various deployment configurations. Possible values include: -
   * "managedDevice", a device that has the EMM's device policy controller (DPC)
   * as the device owner. - "managedProfile", a device that has a profile
   * managed by the DPC (DPC is profile owner) in addition to a separate,
   * personal profile that is unavailable to the DPC. - "containerApp", no
   * longer used (deprecated). - "unmanagedProfile", a device that has been
   * allowed (by the domain's admin, using the Admin Console to enable the
   * privilege) to use managed Google Play, but the profile is itself not owned
   * by a DPC.
   *
   * Accepted values: managedDevice, managedProfile, containerApp,
   * unmanagedProfile
   *
   * @param self::MANAGEMENT_TYPE_* $managementType
   */
  public function setManagementType($managementType)
  {
    $this->managementType = $managementType;
  }
  /**
   * @return self::MANAGEMENT_TYPE_*
   */
  public function getManagementType()
  {
    return $this->managementType;
  }
  /**
   * The model name of the device. This comes from android.os.Build.MODEL.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * The policy enforced on the device.
   *
   * @param Policy $policy
   */
  public function setPolicy(Policy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return Policy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The product name of the device. This comes from android.os.Build.PRODUCT.
   *
   * @param string $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @return string
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * The device report updated with the latest app states.
   *
   * @param DeviceReport $report
   */
  public function setReport(DeviceReport $report)
  {
    $this->report = $report;
  }
  /**
   * @return DeviceReport
   */
  public function getReport()
  {
    return $this->report;
  }
  /**
   * Retail brand for the device, if set. See android.os.Build.BRAND
   *
   * @param string $retailBrand
   */
  public function setRetailBrand($retailBrand)
  {
    $this->retailBrand = $retailBrand;
  }
  /**
   * @return string
   */
  public function getRetailBrand()
  {
    return $this->retailBrand;
  }
  /**
   * API compatibility version.
   *
   * @param int $sdkVersion
   */
  public function setSdkVersion($sdkVersion)
  {
    $this->sdkVersion = $sdkVersion;
  }
  /**
   * @return int
   */
  public function getSdkVersion()
  {
    return $this->sdkVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Device::class, 'Google_Service_AndroidEnterprise_Device');
