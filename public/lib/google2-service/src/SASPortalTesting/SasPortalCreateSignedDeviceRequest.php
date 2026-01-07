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

namespace Google\Service\SASPortalTesting;

class SasPortalCreateSignedDeviceRequest extends \Google\Model
{
  /**
   * Required. JSON Web Token signed using a CPI private key. Payload must be
   * the JSON encoding of the device. The user_id field must be set.
   *
   * @var string
   */
  public $encodedDevice;
  /**
   * Required. Unique installer id (CPI ID) from the Certified Professional
   * Installers database.
   *
   * @var string
   */
  public $installerId;

  /**
   * Required. JSON Web Token signed using a CPI private key. Payload must be
   * the JSON encoding of the device. The user_id field must be set.
   *
   * @param string $encodedDevice
   */
  public function setEncodedDevice($encodedDevice)
  {
    $this->encodedDevice = $encodedDevice;
  }
  /**
   * @return string
   */
  public function getEncodedDevice()
  {
    return $this->encodedDevice;
  }
  /**
   * Required. Unique installer id (CPI ID) from the Certified Professional
   * Installers database.
   *
   * @param string $installerId
   */
  public function setInstallerId($installerId)
  {
    $this->installerId = $installerId;
  }
  /**
   * @return string
   */
  public function getInstallerId()
  {
    return $this->installerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalCreateSignedDeviceRequest::class, 'Google_Service_SASPortalTesting_SasPortalCreateSignedDeviceRequest');
