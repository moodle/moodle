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

namespace Google\Service\AndroidProvisioningPartner;

class PerDeviceStatusInBatch extends \Google\Model
{
  /**
   * Invalid code. Shouldn't be used.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_UNSPECIFIED = 'SINGLE_DEVICE_STATUS_UNSPECIFIED';
  /**
   * Unknown error. We don't expect this error to occur here.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_UNKNOWN_ERROR = 'SINGLE_DEVICE_STATUS_UNKNOWN_ERROR';
  /**
   * Other error. We know/expect this error, but there's no defined error code
   * for the error.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_OTHER_ERROR = 'SINGLE_DEVICE_STATUS_OTHER_ERROR';
  /**
   * Success.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_SUCCESS = 'SINGLE_DEVICE_STATUS_SUCCESS';
  /**
   * Permission denied.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_PERMISSION_DENIED = 'SINGLE_DEVICE_STATUS_PERMISSION_DENIED';
  /**
   * Invalid device identifier.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_INVALID_DEVICE_IDENTIFIER = 'SINGLE_DEVICE_STATUS_INVALID_DEVICE_IDENTIFIER';
  /**
   * Invalid section type.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_INVALID_SECTION_TYPE = 'SINGLE_DEVICE_STATUS_INVALID_SECTION_TYPE';
  /**
   * This section is claimed by another company.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_SECTION_NOT_YOURS = 'SINGLE_DEVICE_STATUS_SECTION_NOT_YOURS';
  /**
   * Invalid pre-provisioning token.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_INVALID_TOKEN = 'SINGLE_DEVICE_STATUS_INVALID_TOKEN';
  /**
   * Revoked pre-provisioning token.
   */
  public const STATUS_SINGLE_DEVICE_STATUS_REVOKED_TOKEN = 'SINGLE_DEVICE_STATUS_REVOKED_TOKEN';
  /**
   * Status used to indicate a failure due to a device limit being exceeded
   */
  public const STATUS_SINGLE_DEVICE_STATUS_DEVICE_LIMIT_EXCEEDED = 'SINGLE_DEVICE_STATUS_DEVICE_LIMIT_EXCEEDED';
  /**
   * If processing succeeds, the device ID of the device.
   *
   * @var string
   */
  public $deviceId;
  /**
   * If processing fails, the error type.
   *
   * @var string
   */
  public $errorIdentifier;
  /**
   * If processing fails, a developer message explaining what went wrong.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The result status of the device after processing.
   *
   * @var string
   */
  public $status;

  /**
   * If processing succeeds, the device ID of the device.
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
   * If processing fails, the error type.
   *
   * @param string $errorIdentifier
   */
  public function setErrorIdentifier($errorIdentifier)
  {
    $this->errorIdentifier = $errorIdentifier;
  }
  /**
   * @return string
   */
  public function getErrorIdentifier()
  {
    return $this->errorIdentifier;
  }
  /**
   * If processing fails, a developer message explaining what went wrong.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * The result status of the device after processing.
   *
   * Accepted values: SINGLE_DEVICE_STATUS_UNSPECIFIED,
   * SINGLE_DEVICE_STATUS_UNKNOWN_ERROR, SINGLE_DEVICE_STATUS_OTHER_ERROR,
   * SINGLE_DEVICE_STATUS_SUCCESS, SINGLE_DEVICE_STATUS_PERMISSION_DENIED,
   * SINGLE_DEVICE_STATUS_INVALID_DEVICE_IDENTIFIER,
   * SINGLE_DEVICE_STATUS_INVALID_SECTION_TYPE,
   * SINGLE_DEVICE_STATUS_SECTION_NOT_YOURS, SINGLE_DEVICE_STATUS_INVALID_TOKEN,
   * SINGLE_DEVICE_STATUS_REVOKED_TOKEN,
   * SINGLE_DEVICE_STATUS_DEVICE_LIMIT_EXCEEDED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerDeviceStatusInBatch::class, 'Google_Service_AndroidProvisioningPartner_PerDeviceStatusInBatch');
