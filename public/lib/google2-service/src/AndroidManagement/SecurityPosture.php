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

namespace Google\Service\AndroidManagement;

class SecurityPosture extends \Google\Collection
{
  /**
   * Unspecified. There is no posture detail for this posture value.
   */
  public const DEVICE_POSTURE_POSTURE_UNSPECIFIED = 'POSTURE_UNSPECIFIED';
  /**
   * This device is secure.
   */
  public const DEVICE_POSTURE_SECURE = 'SECURE';
  /**
   * This device may be more vulnerable to malicious actors than is recommended
   * for use with corporate data.
   */
  public const DEVICE_POSTURE_AT_RISK = 'AT_RISK';
  /**
   * This device may be compromised and corporate data may be accessible to
   * unauthorized actors.
   */
  public const DEVICE_POSTURE_POTENTIALLY_COMPROMISED = 'POTENTIALLY_COMPROMISED';
  protected $collection_key = 'postureDetails';
  /**
   * Device's security posture value.
   *
   * @var string
   */
  public $devicePosture;
  protected $postureDetailsType = PostureDetail::class;
  protected $postureDetailsDataType = 'array';

  /**
   * Device's security posture value.
   *
   * Accepted values: POSTURE_UNSPECIFIED, SECURE, AT_RISK,
   * POTENTIALLY_COMPROMISED
   *
   * @param self::DEVICE_POSTURE_* $devicePosture
   */
  public function setDevicePosture($devicePosture)
  {
    $this->devicePosture = $devicePosture;
  }
  /**
   * @return self::DEVICE_POSTURE_*
   */
  public function getDevicePosture()
  {
    return $this->devicePosture;
  }
  /**
   * Additional details regarding the security posture of the device.
   *
   * @param PostureDetail[] $postureDetails
   */
  public function setPostureDetails($postureDetails)
  {
    $this->postureDetails = $postureDetails;
  }
  /**
   * @return PostureDetail[]
   */
  public function getPostureDetails()
  {
    return $this->postureDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPosture::class, 'Google_Service_AndroidManagement_SecurityPosture');
