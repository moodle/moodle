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

class Install extends \Google\Model
{
  public const INSTALL_STATE_installed = 'installed';
  public const INSTALL_STATE_installPending = 'installPending';
  /**
   * Install state. The state "installPending" means that an install request has
   * recently been made and download to the device is in progress. The state
   * "installed" means that the app has been installed. This field is read-only.
   *
   * @var string
   */
  public $installState;
  /**
   * The ID of the product that the install is for. For example,
   * "app:com.google.android.gm".
   *
   * @var string
   */
  public $productId;
  /**
   * The version of the installed product. Guaranteed to be set only if the
   * install state is "installed".
   *
   * @var int
   */
  public $versionCode;

  /**
   * Install state. The state "installPending" means that an install request has
   * recently been made and download to the device is in progress. The state
   * "installed" means that the app has been installed. This field is read-only.
   *
   * Accepted values: installed, installPending
   *
   * @param self::INSTALL_STATE_* $installState
   */
  public function setInstallState($installState)
  {
    $this->installState = $installState;
  }
  /**
   * @return self::INSTALL_STATE_*
   */
  public function getInstallState()
  {
    return $this->installState;
  }
  /**
   * The ID of the product that the install is for. For example,
   * "app:com.google.android.gm".
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
   * The version of the installed product. Guaranteed to be set only if the
   * install state is "installed".
   *
   * @param int $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return int
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Install::class, 'Google_Service_AndroidEnterprise_Install');
