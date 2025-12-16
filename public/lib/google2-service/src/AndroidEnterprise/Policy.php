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

class Policy extends \Google\Collection
{
  /**
   * The auto update policy is not set.
   */
  public const AUTO_UPDATE_POLICY_autoUpdatePolicyUnspecified = 'autoUpdatePolicyUnspecified';
  /**
   * The user can control auto-updates.
   */
  public const AUTO_UPDATE_POLICY_choiceToTheUser = 'choiceToTheUser';
  /**
   * Apps are never auto-updated.
   */
  public const AUTO_UPDATE_POLICY_never = 'never';
  /**
   * Apps are auto-updated over WiFi only.
   */
  public const AUTO_UPDATE_POLICY_wifiOnly = 'wifiOnly';
  /**
   * Apps are auto-updated at any time. Data charges may apply.
   */
  public const AUTO_UPDATE_POLICY_always = 'always';
  /**
   * The device report policy is not set.
   */
  public const DEVICE_REPORT_POLICY_deviceReportPolicyUnspecified = 'deviceReportPolicyUnspecified';
  /**
   * Device reports are disabled.
   */
  public const DEVICE_REPORT_POLICY_deviceReportDisabled = 'deviceReportDisabled';
  /**
   * Device reports are enabled.
   */
  public const DEVICE_REPORT_POLICY_deviceReportEnabled = 'deviceReportEnabled';
  /**
   * Unspecified, applies the user available product set by default.
   */
  public const PRODUCT_AVAILABILITY_POLICY_productAvailabilityPolicyUnspecified = 'productAvailabilityPolicyUnspecified';
  /**
   * The approved products with product availability set to AVAILABLE in the
   * product policy are available.
   */
  public const PRODUCT_AVAILABILITY_POLICY_whitelist = 'whitelist';
  /**
   * All products are available except those explicitly marked as unavailable in
   * the product availability policy.
   */
  public const PRODUCT_AVAILABILITY_POLICY_all = 'all';
  protected $collection_key = 'productPolicy';
  /**
   * Controls when automatic app updates on the device can be applied.
   * Recommended alternative: autoUpdateMode which is set per app, provides
   * greater flexibility around update frequency. When autoUpdateMode is set to
   * AUTO_UPDATE_POSTPONED or AUTO_UPDATE_HIGH_PRIORITY, autoUpdatePolicy has no
   * effect. - choiceToTheUser allows the device's user to configure the app
   * update policy. - always enables auto updates. - never disables auto
   * updates. - wifiOnly enables auto updates only when the device is connected
   * to wifi. *Important:* Changes to app update policies don't affect updates
   * that are in progress. Any policy changes will apply to subsequent app
   * updates.
   *
   * @deprecated
   * @var string
   */
  public $autoUpdatePolicy;
  /**
   * Whether the device reports app states to the EMM. The default value is
   * "deviceReportDisabled".
   *
   * @var string
   */
  public $deviceReportPolicy;
  protected $maintenanceWindowType = MaintenanceWindow::class;
  protected $maintenanceWindowDataType = '';
  /**
   * An identifier for the policy that will be passed with the app install
   * feedback sent from the Play Store.
   *
   * @var string
   */
  public $policyId;
  /**
   * The availability granted to the device for the specified products. "all"
   * gives the device access to all products, regardless of approval status.
   * "all" does not enable automatic visibility of "alpha" or "beta" tracks.
   * "whitelist" grants the device access the products specified in
   * productPolicy[]. Only products that are approved or products that were
   * previously approved (products with revoked approval) by the enterprise can
   * be whitelisted. If no value is provided, the availability set at the user
   * level is applied by default.
   *
   * @var string
   */
  public $productAvailabilityPolicy;
  protected $productPolicyType = ProductPolicy::class;
  protected $productPolicyDataType = 'array';

  /**
   * Controls when automatic app updates on the device can be applied.
   * Recommended alternative: autoUpdateMode which is set per app, provides
   * greater flexibility around update frequency. When autoUpdateMode is set to
   * AUTO_UPDATE_POSTPONED or AUTO_UPDATE_HIGH_PRIORITY, autoUpdatePolicy has no
   * effect. - choiceToTheUser allows the device's user to configure the app
   * update policy. - always enables auto updates. - never disables auto
   * updates. - wifiOnly enables auto updates only when the device is connected
   * to wifi. *Important:* Changes to app update policies don't affect updates
   * that are in progress. Any policy changes will apply to subsequent app
   * updates.
   *
   * Accepted values: autoUpdatePolicyUnspecified, choiceToTheUser, never,
   * wifiOnly, always
   *
   * @deprecated
   * @param self::AUTO_UPDATE_POLICY_* $autoUpdatePolicy
   */
  public function setAutoUpdatePolicy($autoUpdatePolicy)
  {
    $this->autoUpdatePolicy = $autoUpdatePolicy;
  }
  /**
   * @deprecated
   * @return self::AUTO_UPDATE_POLICY_*
   */
  public function getAutoUpdatePolicy()
  {
    return $this->autoUpdatePolicy;
  }
  /**
   * Whether the device reports app states to the EMM. The default value is
   * "deviceReportDisabled".
   *
   * Accepted values: deviceReportPolicyUnspecified, deviceReportDisabled,
   * deviceReportEnabled
   *
   * @param self::DEVICE_REPORT_POLICY_* $deviceReportPolicy
   */
  public function setDeviceReportPolicy($deviceReportPolicy)
  {
    $this->deviceReportPolicy = $deviceReportPolicy;
  }
  /**
   * @return self::DEVICE_REPORT_POLICY_*
   */
  public function getDeviceReportPolicy()
  {
    return $this->deviceReportPolicy;
  }
  /**
   * The maintenance window defining when apps running in the foreground should
   * be updated.
   *
   * @param MaintenanceWindow $maintenanceWindow
   */
  public function setMaintenanceWindow(MaintenanceWindow $maintenanceWindow)
  {
    $this->maintenanceWindow = $maintenanceWindow;
  }
  /**
   * @return MaintenanceWindow
   */
  public function getMaintenanceWindow()
  {
    return $this->maintenanceWindow;
  }
  /**
   * An identifier for the policy that will be passed with the app install
   * feedback sent from the Play Store.
   *
   * @param string $policyId
   */
  public function setPolicyId($policyId)
  {
    $this->policyId = $policyId;
  }
  /**
   * @return string
   */
  public function getPolicyId()
  {
    return $this->policyId;
  }
  /**
   * The availability granted to the device for the specified products. "all"
   * gives the device access to all products, regardless of approval status.
   * "all" does not enable automatic visibility of "alpha" or "beta" tracks.
   * "whitelist" grants the device access the products specified in
   * productPolicy[]. Only products that are approved or products that were
   * previously approved (products with revoked approval) by the enterprise can
   * be whitelisted. If no value is provided, the availability set at the user
   * level is applied by default.
   *
   * Accepted values: productAvailabilityPolicyUnspecified, whitelist, all
   *
   * @param self::PRODUCT_AVAILABILITY_POLICY_* $productAvailabilityPolicy
   */
  public function setProductAvailabilityPolicy($productAvailabilityPolicy)
  {
    $this->productAvailabilityPolicy = $productAvailabilityPolicy;
  }
  /**
   * @return self::PRODUCT_AVAILABILITY_POLICY_*
   */
  public function getProductAvailabilityPolicy()
  {
    return $this->productAvailabilityPolicy;
  }
  /**
   * The list of product policies. The productAvailabilityPolicy needs to be set
   * to WHITELIST or ALL for the product policies to be applied.
   *
   * @param ProductPolicy[] $productPolicy
   */
  public function setProductPolicy($productPolicy)
  {
    $this->productPolicy = $productPolicy;
  }
  /**
   * @return ProductPolicy[]
   */
  public function getProductPolicy()
  {
    return $this->productPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policy::class, 'Google_Service_AndroidEnterprise_Policy');
