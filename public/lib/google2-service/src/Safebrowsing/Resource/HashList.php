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

namespace Google\Service\Safebrowsing\Resource;

use Google\Service\Safebrowsing\GoogleSecuritySafebrowsingV5HashList;

/**
 * The "hashList" collection of methods.
 * Typical usage is:
 *  <code>
 *   $safebrowsingService = new Google\Service\Safebrowsing(...);
 *   $hashList = $safebrowsingService->hashList;
 *  </code>
 */
class HashList extends \Google\Service\Resource
{
  /**
   * Get the latest contents of a hash list. A hash list may either by a threat
   * list or a non-threat list such as the Global Cache. This is a standard Get
   * method as defined by https://google.aip.dev/131 and the HTTP method is also
   * GET. (hashList.get)
   *
   * @param string $name Required. The name of this particular hash list. It may
   * be a threat list, or it may be the Global Cache.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int sizeConstraints.maxDatabaseEntries Sets the maximum number of
   * entries that the client is willing to have in the local database for the
   * list. (The server MAY cause the client to store less than this number of
   * entries.) If omitted or zero, no database size limit is set.
   * @opt_param int sizeConstraints.maxUpdateEntries The maximum size in number of
   * entries. The update will not contain more entries than this value, but it is
   * possible that the update will contain fewer entries than this value. This
   * MUST be at least 1024. If omitted or zero, no update size limit is set.
   * @opt_param string version The version of the hash list that the client
   * already has. If this is the first time the client is fetching the hash list,
   * this field MUST be left empty. Otherwise, the client SHOULD supply the
   * version previously received from the server. The client MUST NOT manipulate
   * those bytes. **What's new in V5**: in V4 of the API, this was called
   * `states`; it is now renamed to `version` for clarity.
   * @return GoogleSecuritySafebrowsingV5HashList
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleSecuritySafebrowsingV5HashList::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HashList::class, 'Google_Service_Safebrowsing_Resource_HashList');
