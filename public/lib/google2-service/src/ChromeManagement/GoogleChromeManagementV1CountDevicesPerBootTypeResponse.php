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

class GoogleChromeManagementV1CountDevicesPerBootTypeResponse extends \Google\Model
{
  /**
   * Number of devices with dev boot type.
   *
   * @var string
   */
  public $devBootTypeCount;
  /**
   * Number of devices with unreported boot type.
   *
   * @var string
   */
  public $unreportedBootTypeCount;
  /**
   * Number of devices with verified boot type.
   *
   * @var string
   */
  public $verifiedBootTypeCount;

  /**
   * Number of devices with dev boot type.
   *
   * @param string $devBootTypeCount
   */
  public function setDevBootTypeCount($devBootTypeCount)
  {
    $this->devBootTypeCount = $devBootTypeCount;
  }
  /**
   * @return string
   */
  public function getDevBootTypeCount()
  {
    return $this->devBootTypeCount;
  }
  /**
   * Number of devices with unreported boot type.
   *
   * @param string $unreportedBootTypeCount
   */
  public function setUnreportedBootTypeCount($unreportedBootTypeCount)
  {
    $this->unreportedBootTypeCount = $unreportedBootTypeCount;
  }
  /**
   * @return string
   */
  public function getUnreportedBootTypeCount()
  {
    return $this->unreportedBootTypeCount;
  }
  /**
   * Number of devices with verified boot type.
   *
   * @param string $verifiedBootTypeCount
   */
  public function setVerifiedBootTypeCount($verifiedBootTypeCount)
  {
    $this->verifiedBootTypeCount = $verifiedBootTypeCount;
  }
  /**
   * @return string
   */
  public function getVerifiedBootTypeCount()
  {
    return $this->verifiedBootTypeCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountDevicesPerBootTypeResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountDevicesPerBootTypeResponse');
