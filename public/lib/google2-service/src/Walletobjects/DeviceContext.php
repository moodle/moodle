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

namespace Google\Service\Walletobjects;

class DeviceContext extends \Google\Model
{
  /**
   * If set, redemption information will only be returned to the given device
   * upon activation of the object. This should not be used as a stable
   * identifier to trace a user's device. It can change across different passes
   * for the same device or even across different activations for the same
   * device. When setting this, callers must also set has_linked_device on the
   * object being activated.
   *
   * @var string
   */
  public $deviceToken;

  /**
   * If set, redemption information will only be returned to the given device
   * upon activation of the object. This should not be used as a stable
   * identifier to trace a user's device. It can change across different passes
   * for the same device or even across different activations for the same
   * device. When setting this, callers must also set has_linked_device on the
   * object being activated.
   *
   * @param string $deviceToken
   */
  public function setDeviceToken($deviceToken)
  {
    $this->deviceToken = $deviceToken;
  }
  /**
   * @return string
   */
  public function getDeviceToken()
  {
    return $this->deviceToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceContext::class, 'Google_Service_Walletobjects_DeviceContext');
