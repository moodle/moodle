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

namespace Google\Service\CloudKMS\Resource;

use Google\Service\CloudKMS\KeyAccessJustificationsPolicyConfig;

/**
 * The "organizations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudkmsService = new Google\Service\CloudKMS(...);
 *   $organizations = $cloudkmsService->organizations;
 *  </code>
 */
class Organizations extends \Google\Service\Resource
{
  /**
   * Gets the KeyAccessJustificationsPolicyConfig for a given organization,
   * folder, or project. (organizations.getKajPolicyConfig)
   *
   * @param string $name Required. The name of the
   * KeyAccessJustificationsPolicyConfig to get.
   * @param array $optParams Optional parameters.
   * @return KeyAccessJustificationsPolicyConfig
   * @throws \Google\Service\Exception
   */
  public function getKajPolicyConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getKajPolicyConfig', [$params], KeyAccessJustificationsPolicyConfig::class);
  }
  /**
   * Updates the KeyAccessJustificationsPolicyConfig for a given organization,
   * folder, or project. (organizations.updateKajPolicyConfig)
   *
   * @param string $name Identifier. The resource name for this
   * KeyAccessJustificationsPolicyConfig in the format of
   * "{organizations|folders|projects}/kajPolicyConfig".
   * @param KeyAccessJustificationsPolicyConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return KeyAccessJustificationsPolicyConfig
   * @throws \Google\Service\Exception
   */
  public function updateKajPolicyConfig($name, KeyAccessJustificationsPolicyConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateKajPolicyConfig', [$params], KeyAccessJustificationsPolicyConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Organizations::class, 'Google_Service_CloudKMS_Resource_Organizations');
