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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1TouchScreenDevice extends \Google\Model
{
  /**
   * Output only. Touch screen device display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Touch screen device is stylus capable or not.
   *
   * @var bool
   */
  public $stylusCapable;
  /**
   * Output only. Number of touch points supported on the device.
   *
   * @var int
   */
  public $touchPointCount;

  /**
   * Output only. Touch screen device display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Touch screen device is stylus capable or not.
   *
   * @param bool $stylusCapable
   */
  public function setStylusCapable($stylusCapable)
  {
    $this->stylusCapable = $stylusCapable;
  }
  /**
   * @return bool
   */
  public function getStylusCapable()
  {
    return $this->stylusCapable;
  }
  /**
   * Output only. Number of touch points supported on the device.
   *
   * @param int $touchPointCount
   */
  public function setTouchPointCount($touchPointCount)
  {
    $this->touchPointCount = $touchPointCount;
  }
  /**
   * @return int
   */
  public function getTouchPointCount()
  {
    return $this->touchPointCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TouchScreenDevice::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TouchScreenDevice');
