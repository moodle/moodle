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

namespace Google\Service\Kmsinventory\Resource;

use Google\Service\Kmsinventory\GoogleCloudKmsInventoryV1ProtectedResourcesSummary;

/**
 * The "cryptoKeys" collection of methods.
 * Typical usage is:
 *  <code>
 *   $kmsinventoryService = new Google\Service\Kmsinventory(...);
 *   $cryptoKeys = $kmsinventoryService->projects_locations_keyRings_cryptoKeys;
 *  </code>
 */
class ProjectsLocationsKeyRingsCryptoKeys extends \Google\Service\Resource
{
  /**
   * Returns aggregate information about the resources protected by the given
   * Cloud KMS CryptoKey. Only resources within the same Cloud organization as the
   * key will be returned. The project that holds the key must be part of an
   * organization in order for this call to succeed.
   * (cryptoKeys.getProtectedResourcesSummary)
   *
   * @param string $name Required. The resource name of the CryptoKey.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudKmsInventoryV1ProtectedResourcesSummary
   * @throws \Google\Service\Exception
   */
  public function getProtectedResourcesSummary($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getProtectedResourcesSummary', [$params], GoogleCloudKmsInventoryV1ProtectedResourcesSummary::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsKeyRingsCryptoKeys::class, 'Google_Service_Kmsinventory_Resource_ProjectsLocationsKeyRingsCryptoKeys');
