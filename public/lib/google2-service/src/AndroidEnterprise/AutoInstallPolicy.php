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

class AutoInstallPolicy extends \Google\Collection
{
  public const AUTO_INSTALL_MODE_autoInstallModeUnspecified = 'autoInstallModeUnspecified';
  /**
   * The product is not installed automatically, the user needs to install it
   * from the Play Store.
   */
  public const AUTO_INSTALL_MODE_doNotAutoInstall = 'doNotAutoInstall';
  /**
   * The product is automatically installed once, if the user uninstalls the
   * product it will not be installed again.
   */
  public const AUTO_INSTALL_MODE_autoInstallOnce = 'autoInstallOnce';
  /**
   * The product is automatically installed, if the user uninstalls the product
   * it will be installed again. On managed devices the DPC should block
   * uninstall.
   */
  public const AUTO_INSTALL_MODE_forceAutoInstall = 'forceAutoInstall';
  protected $collection_key = 'autoInstallConstraint';
  protected $autoInstallConstraintType = AutoInstallConstraint::class;
  protected $autoInstallConstraintDataType = 'array';
  /**
   * The auto-install mode. If unset, defaults to "doNotAutoInstall". An app is
   * automatically installed regardless of a set maintenance window.
   *
   * @var string
   */
  public $autoInstallMode;
  /**
   * The priority of the install, as an unsigned integer. A lower number means
   * higher priority.
   *
   * @var int
   */
  public $autoInstallPriority;
  /**
   * The minimum version of the app. If a lower version of the app is installed,
   * then the app will be auto-updated according to the auto-install
   * constraints, instead of waiting for the regular auto-update. You can set a
   * minimum version code for at most 20 apps per device.
   *
   * @var int
   */
  public $minimumVersionCode;

  /**
   * The constraints for auto-installing the app. You can specify a maximum of
   * one constraint.
   *
   * @param AutoInstallConstraint[] $autoInstallConstraint
   */
  public function setAutoInstallConstraint($autoInstallConstraint)
  {
    $this->autoInstallConstraint = $autoInstallConstraint;
  }
  /**
   * @return AutoInstallConstraint[]
   */
  public function getAutoInstallConstraint()
  {
    return $this->autoInstallConstraint;
  }
  /**
   * The auto-install mode. If unset, defaults to "doNotAutoInstall". An app is
   * automatically installed regardless of a set maintenance window.
   *
   * Accepted values: autoInstallModeUnspecified, doNotAutoInstall,
   * autoInstallOnce, forceAutoInstall
   *
   * @param self::AUTO_INSTALL_MODE_* $autoInstallMode
   */
  public function setAutoInstallMode($autoInstallMode)
  {
    $this->autoInstallMode = $autoInstallMode;
  }
  /**
   * @return self::AUTO_INSTALL_MODE_*
   */
  public function getAutoInstallMode()
  {
    return $this->autoInstallMode;
  }
  /**
   * The priority of the install, as an unsigned integer. A lower number means
   * higher priority.
   *
   * @param int $autoInstallPriority
   */
  public function setAutoInstallPriority($autoInstallPriority)
  {
    $this->autoInstallPriority = $autoInstallPriority;
  }
  /**
   * @return int
   */
  public function getAutoInstallPriority()
  {
    return $this->autoInstallPriority;
  }
  /**
   * The minimum version of the app. If a lower version of the app is installed,
   * then the app will be auto-updated according to the auto-install
   * constraints, instead of waiting for the regular auto-update. You can set a
   * minimum version code for at most 20 apps per device.
   *
   * @param int $minimumVersionCode
   */
  public function setMinimumVersionCode($minimumVersionCode)
  {
    $this->minimumVersionCode = $minimumVersionCode;
  }
  /**
   * @return int
   */
  public function getMinimumVersionCode()
  {
    return $this->minimumVersionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoInstallPolicy::class, 'Google_Service_AndroidEnterprise_AutoInstallPolicy');
