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

namespace Google\Service\PlayIntegrity\Resource;

use Google\Service\PlayIntegrity\WriteDeviceRecallRequest;
use Google\Service\PlayIntegrity\WriteDeviceRecallResponse;

/**
 * The "deviceRecall" collection of methods.
 * Typical usage is:
 *  <code>
 *   $playintegrityService = new Google\Service\PlayIntegrity(...);
 *   $deviceRecall = $playintegrityService->deviceRecall;
 *  </code>
 */
class DeviceRecall extends \Google\Service\Resource
{
  /**
   * Writes recall bits for the device where Play Integrity API token is obtained.
   * The endpoint is available to select Play partners in an early access program
   * (EAP). (deviceRecall.write)
   *
   * @param string $packageName Required. Package name of the app the attached
   * integrity token belongs to.
   * @param WriteDeviceRecallRequest $postBody
   * @param array $optParams Optional parameters.
   * @return WriteDeviceRecallResponse
   * @throws \Google\Service\Exception
   */
  public function write($packageName, WriteDeviceRecallRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('write', [$params], WriteDeviceRecallResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceRecall::class, 'Google_Service_PlayIntegrity_Resource_DeviceRecall');
