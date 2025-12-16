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

namespace Google\Service\AndroidManagement\Resource;

use Google\Service\AndroidManagement\ProvisioningInfo as ProvisioningInfoModel;

/**
 * The "provisioningInfo" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidmanagementService = new Google\Service\AndroidManagement(...);
 *   $provisioningInfo = $androidmanagementService->provisioningInfo;
 *  </code>
 */
class ProvisioningInfo extends \Google\Service\Resource
{
  /**
   * Get the device provisioning information by the identifier provided in the
   * sign-in url. (provisioningInfo.get)
   *
   * @param string $name Required. The identifier that Android Device Policy
   * passes to the 3P sign-in page in the form of
   * provisioningInfo/{provisioning_info}.
   * @param array $optParams Optional parameters.
   * @return ProvisioningInfoModel
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ProvisioningInfoModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisioningInfo::class, 'Google_Service_AndroidManagement_Resource_ProvisioningInfo');
