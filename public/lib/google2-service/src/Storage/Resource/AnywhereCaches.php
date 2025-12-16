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

use Google\Service\Storage\AnywhereCache;
use Google\Service\Storage\AnywhereCaches as AnywhereCachesModel;
use Google\Service\Storage\GoogleLongrunningOperation;

/**
 * The "anywhereCaches" collection of methods.
 * Typical usage is:
 *  <code>
 *   $storageService = new Google\Service\Storage(...);
 *   $anywhereCaches = $storageService->anywhereCaches;
 *  </code>
 */
class AnywhereCaches extends \Google\Service\Resource
{
  /**
   * Disables an Anywhere Cache instance. (anywhereCaches.disable)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCache
   * @throws \Google\Service\Exception
   */
  public function disable($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], AnywhereCache::class);
  }
  /**
   * Returns the metadata of an Anywhere Cache instance. (anywhereCaches.get)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCache
   * @throws \Google\Service\Exception
   */
  public function get($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AnywhereCache::class);
  }
  /**
   * Creates an Anywhere Cache instance. (anywhereCaches.insert)
   *
   * @param string $bucket Name of the parent bucket.
   * @param AnywhereCache $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function insert($bucket, AnywhereCache $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Returns a list of Anywhere Cache instances of the bucket matching the
   * criteria. (anywhereCaches.listAnywhereCaches)
   *
   * @param string $bucket Name of the parent bucket.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of items to return in a single page of
   * responses. Maximum 1000.
   * @opt_param string pageToken A previously-returned page token representing
   * part of the larger set of results to view.
   * @return AnywhereCachesModel
   * @throws \Google\Service\Exception
   */
  public function listAnywhereCaches($bucket, $optParams = [])
  {
    $params = ['bucket' => $bucket];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], AnywhereCachesModel::class);
  }
  /**
   * Pauses an Anywhere Cache instance. (anywhereCaches.pause)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCache
   * @throws \Google\Service\Exception
   */
  public function pause($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], AnywhereCache::class);
  }
  /**
   * Resumes a paused or disabled Anywhere Cache instance. (anywhereCaches.resume)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param array $optParams Optional parameters.
   * @return AnywhereCache
   * @throws \Google\Service\Exception
   */
  public function resume($bucket, $anywhereCacheId, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], AnywhereCache::class);
  }
  /**
   * Updates the config(ttl and admissionPolicy) of an Anywhere Cache instance.
   * (anywhereCaches.update)
   *
   * @param string $bucket Name of the parent bucket.
   * @param string $anywhereCacheId The ID of requested Anywhere Cache instance.
   * @param AnywhereCache $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function update($bucket, $anywhereCacheId, AnywhereCache $postBody, $optParams = [])
  {
    $params = ['bucket' => $bucket, 'anywhereCacheId' => $anywhereCacheId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnywhereCaches::class, 'Google_Service_Storage_Resource_AnywhereCaches');
