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

use Google\Service\Kmsinventory\GoogleCloudKmsInventoryV1ListCryptoKeysResponse;

/**
 * The "cryptoKeys" collection of methods.
 * Typical usage is:
 *  <code>
 *   $kmsinventoryService = new Google\Service\Kmsinventory(...);
 *   $cryptoKeys = $kmsinventoryService->projects_cryptoKeys;
 *  </code>
 */
class ProjectsCryptoKeys extends \Google\Service\Resource
{
  /**
   * Returns cryptographic keys managed by Cloud KMS in a given Cloud project.
   * Note that this data is sourced from snapshots, meaning it may not completely
   * reflect the actual state of key metadata at call time.
   * (cryptoKeys.listProjectsCryptoKeys)
   *
   * @param string $parent Required. The Google Cloud project for which to
   * retrieve key metadata, in the format `projects`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of keys to return. The
   * service may return fewer than this value. If unspecified, at most 1000 keys
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. Pass this into a subsequent request in
   * order to receive the next page of results.
   * @return GoogleCloudKmsInventoryV1ListCryptoKeysResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsCryptoKeys($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudKmsInventoryV1ListCryptoKeysResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsCryptoKeys::class, 'Google_Service_Kmsinventory_Resource_ProjectsCryptoKeys');
