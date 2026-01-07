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

namespace Google\Service\Storage\Resource;

use Google\Service\Storage\AnywhereCache as AnywhereCacheModel;
use Google\Service\Storage\AnywhereCaches;
use Google\Service\Storage\GoogleLongrunningOperation;

/**
 * The "anywhereCache" collection of methods.
 * Typical usage is:
 *  <code>
 *   $storageService = new Google\Service\Storage(...);
 *   $anywhereCache = $storageService->anywhereCache;
 *  </code>
 */
class AnywhereCache extends \Google\Service\Resource
{
  /**
   * Disables an Anywhere Cache instance. (anywhereCache.disable)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCacheModel
   */
  public function disable($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], AnywhereCacheModel::class);
  }
  /**
   * Returns the metadata of an Anywhere Cache instance. (anywhereCache.get)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCacheModel
   */
  public function get($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AnywhereCacheModel::class);
  }
  /**
   * Creates an Anywhere Cache instance. (anywhereCache.insert)
   *
   * @param string $bucket Name of the parent bucket.
   * @param AnywhereCacheModel $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   */
  public function insert($bucket, AnywhereCacheModel $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Returns a list of Anywhere Cache instances of the bucket matching the
   * criteria. (anywhereCache.listAnywhereCache)
   *
   * @param string $bucket Name of the parent bucket.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of items return in a single page of
   * responses. Maximum 1000.
   * @opt_param string pageToken A previously-returned page token representing
   * part of the larger set of results to view.
   * @return AnywhereCaches
   */
  public function listAnywhereCache($bucket, $optParams = [])
  {
    $params = ['bucket' => $bucket];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], AnywhereCaches::class);
  }
  /**
   * Pauses an Anywhere Cache instance. (anywhereCache.pause)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCacheModel
   */
  public function pause($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], AnywhereCacheModel::class);
  }
  /**
   * Resumes a paused or disabled Anywhere Cache instance. (anywhereCache.resume)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCacheModel
   */
  public function resume($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], AnywhereCacheModel::class);
  }
  /**
   * Updates the config(ttl and admissionPolicy) of an Anywhere Cache instance.
   * (anywhereCache.update)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param AnywhereCacheModel $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   */
  public function update($bucket, $anywhereCacheId, AnywhereCacheModel $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnywhereCache::class, 'Google_Service_Storage_Resource_AnywhereCache');
