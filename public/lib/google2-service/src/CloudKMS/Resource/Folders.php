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

use Google\Service\CloudKMS\AutokeyConfig;
use Google\Service\CloudKMS\KeyAccessJustificationsPolicyConfig;

/**
 * The "folders" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudkmsService = new Google\Service\CloudKMS(...);
 *   $folders = $cloudkmsService->folders;
 *  </code>
 */
class Folders extends \Google\Service\Resource
{
  /**
   * Returns the AutokeyConfig for a folder. (folders.getAutokeyConfig)
   *
   * @param string $name Required. Name of the AutokeyConfig resource, e.g.
   * `folders/{FOLDER_NUMBER}/autokeyConfig`.
   * @param array $optParams Optional parameters.
   * @return AutokeyConfig
   * @throws \Google\Service\Exception
   */
  public function getAutokeyConfig($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getAutokeyConfig', [$params], AutokeyConfig::class);
  }
  /**
   * Gets the KeyAccessJustificationsPolicyConfig for a given organization,
   * folder, or project. (folders.getKajPolicyConfig)
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
   * Updates the AutokeyConfig for a folder. The caller must have both
   * `cloudkms.autokeyConfigs.update` permission on the parent folder and
   * `cloudkms.cryptoKeys.setIamPolicy` permission on the provided key project. A
   * KeyHandle creation in the folder's descendant projects will use this
   * configuration to determine where to create the resulting CryptoKey.
   * (folders.updateAutokeyConfig)
   *
   * @param string $name Identifier. Name of the AutokeyConfig resource, e.g.
   * `folders/{FOLDER_NUMBER}/autokeyConfig`
   * @param AutokeyConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Masks which fields of the
   * AutokeyConfig to update, e.g. `keyProject`.
   * @return AutokeyConfig
   * @throws \Google\Service\Exception
   */
  public function updateAutokeyConfig($name, AutokeyConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateAutokeyConfig', [$params], AutokeyConfig::class);
  }
  /**
   * Updates the KeyAccessJustificationsPolicyConfig for a given organization,
   * folder, or project. (folders.updateKajPolicyConfig)
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
class_alias(Folders::class, 'Google_Service_CloudKMS_Resource_Folders');
