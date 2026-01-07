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

use Google\Service\Safebrowsing\GoogleSecuritySafebrowsingV5BatchGetHashListsResponse;
use Google\Service\Safebrowsing\GoogleSecuritySafebrowsingV5ListHashListsResponse;

/**
 * The "hashLists" collection of methods.
 * Typical usage is:
 *  <code>
 *   $safebrowsingService = new Google\Service\Safebrowsing(...);
 *   $hashLists = $safebrowsingService->hashLists;
 *  </code>
 */
class HashLists extends \Google\Service\Resource
{
  /**
   * Get multiple hash lists at once. It is very common for a client to need to
   * get multiple hash lists. Using this method is preferred over using the
   * regular Get method multiple times. This is a standard batch Get method as
   * defined by https://google.aip.dev/231 and the HTTP method is also GET.
   * (hashLists.batchGet)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string names Required. The names of the particular hash lists. The
   * list MAY be a threat list, or it may be the Global Cache. The names MUST NOT
   * contain duplicates; if they did, the client will get an error.
   * @opt_param int sizeConstraints.maxDatabaseEntries Sets the maximum number of
   * entries that the client is willing to have in the local database for the
   * list. (The server MAY cause the client to store less than this number of
   * entries.) If omitted or zero, no database size limit is set.
   * @opt_param int sizeConstraints.maxUpdateEntries The maximum size in number of
   * entries. The update will not contain more entries than this value, but it is
   * possible that the update will contain fewer entries than this value. This
   * MUST be at least 1024. If omitted or zero, no update size limit is set.
   * @opt_param string version The versions of the hash list that the client
   * already has. If this is the first time the client is fetching the hash lists,
   * the field should be left empty. Otherwise, the client should supply the
   * versions previously received from the server. The client MUST NOT manipulate
   * those bytes. The client need not send the versions in the same order as the
   * corresponding list names. The client may send fewer or more versions in a
   * request than there are names. However the client MUST NOT send multiple
   * versions that correspond to the same name; if it did, the client will get an
   * error. Historical note: in V4 of the API, this was called `states`; it is now
   * renamed to `version` for clarity.
   * @return GoogleSecuritySafebrowsingV5BatchGetHashListsResponse
   * @throws \Google\Service\Exception
   */
  public function batchGet($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('batchGet', [$params], GoogleSecuritySafebrowsingV5BatchGetHashListsResponse::class);
  }
  /**
   * List hash lists. In the V5 API, Google will never remove a hash list that has
   * ever been returned by this method. This enables clients to skip using this
   * method and simply hard-code all hash lists they need. This is a standard List
   * method as defined by https://google.aip.dev/132 and the HTTP method is GET.
   * (hashLists.listHashLists)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of hash lists to return. The
   * service may return fewer than this value. If unspecified, the server will
   * choose a page size, which may be larger than the number of hash lists so that
   * pagination is not necessary.
   * @opt_param string pageToken A page token, received from a previous
   * `ListHashLists` call. Provide this to retrieve the subsequent page.
   * @return GoogleSecuritySafebrowsingV5ListHashListsResponse
   * @throws \Google\Service\Exception
   */
  public function listHashLists($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleSecuritySafebrowsingV5ListHashListsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HashLists::class, 'Google_Service_Safebrowsing_Resource_HashLists');
