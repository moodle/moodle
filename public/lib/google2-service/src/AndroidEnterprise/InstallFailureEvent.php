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

class InstallFailureEvent extends \Google\Model
{
  /**
   * Used whenever no better reason for failure can be provided.
   */
  public const FAILURE_REASON_unknown = 'unknown';
  /**
   * Used when the installation timed out. This can cover a number of
   * situations, for example when the device did not have connectivity at any
   * point during the retry period, or if the device is OOM.
   */
  public const FAILURE_REASON_timeout = 'timeout';
  /**
   * The Android ID of the device. This field will always be present.
   *
   * @var string
   */
  public $deviceId;
  /**
   * Additional details on the failure if applicable.
   *
   * @var string
   */
  public $failureDetails;
  /**
   * The reason for the installation failure. This field will always be present.
   *
   * @var string
   */
  public $failureReason;
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which the
   * install failure event occured. This field will always be present.
   *
   * @var string
   */
  public $productId;
  /**
   * The ID of the user. This field will always be present.
   *
   * @var string
   */
  public $userId;

  /**
   * The Android ID of the device. This field will always be present.
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * Additional details on the failure if applicable.
   *
   * @param string $failureDetails
   */
  public function setFailureDetails($failureDetails)
  {
    $this->failureDetails = $failureDetails;
  }
  /**
   * @return string
   */
  public function getFailureDetails()
  {
    return $this->failureDetails;
  }
  /**
   * The reason for the installation failure. This field will always be present.
   *
   * Accepted values: unknown, timeout
   *
   * @param self::FAILURE_REASON_* $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return self::FAILURE_REASON_*
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which the
   * install failure event occured. This field will always be present.
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
   * The ID of the user. This field will always be present.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstallFailureEvent::class, 'Google_Service_AndroidEnterprise_InstallFailureEvent');
