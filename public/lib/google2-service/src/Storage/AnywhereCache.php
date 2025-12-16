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

namespace Google\Service\Storage;

class AnywhereCache extends \Google\Model
{
  /**
   * The cache-level entry admission policy.
   *
   * @var string
   */
  public $admissionPolicy;
  /**
   * The ID of the Anywhere cache instance.
   *
   * @var string
   */
  public $anywhereCacheId;
  /**
   * The name of the bucket containing this cache instance.
   *
   * @var string
   */
  public $bucket;
  /**
   * The creation time of the cache instance in RFC 3339 format.
   *
   * @var string
   */
  public $createTime;
  /**
   * The ID of the resource, including the project number, bucket name and
   * anywhere cache ID.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of item this is. For Anywhere Cache, this is always
   * storage#anywhereCache.
   *
   * @var string
   */
  public $kind;
  /**
   * True if the cache instance has an active Update long-running operation.
   *
   * @var bool
   */
  public $pendingUpdate;
  /**
   * The link to this cache instance.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The current state of the cache instance.
   *
   * @var string
   */
  public $state;
  /**
   * The TTL of all cache entries in whole seconds. e.g., "7200s".
   *
   * @var string
   */
  public $ttl;
  /**
   * The modification time of the cache instance metadata in RFC 3339 format.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The zone in which the cache instance is running. For example, us-
   * central1-a.
   *
   * @var string
   */
  public $zone;

  /**
   * The cache-level entry admission policy.
   *
   * @param string $admissionPolicy
   */
  public function setAdmissionPolicy($admissionPolicy)
  {
    $this->admissionPolicy = $admissionPolicy;
  }
  /**
   * @return string
   */
  public function getAdmissionPolicy()
  {
    return $this->admissionPolicy;
  }
  /**
   * The ID of the Anywhere cache instance.
   *
   * @param string $anywhereCacheId
   */
  public function setAnywhereCacheId($anywhereCacheId)
  {
    $this->anywhereCacheId = $anywhereCacheId;
  }
  /**
   * @return string
   */
  public function getAnywhereCacheId()
  {
    return $this->anywhereCacheId;
  }
  /**
   * The name of the bucket containing this cache instance.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * The creation time of the cache instance in RFC 3339 format.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The ID of the resource, including the project number, bucket name and
   * anywhere cache ID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The kind of item this is. For Anywhere Cache, this is always
   * storage#anywhereCache.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * True if the cache instance has an active Update long-running operation.
   *
   * @param bool $pendingUpdate
   */
  public function setPendingUpdate($pendingUpdate)
  {
    $this->pendingUpdate = $pendingUpdate;
  }
  /**
   * @return bool
   */
  public function getPendingUpdate()
  {
    return $this->pendingUpdate;
  }
  /**
   * The link to this cache instance.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The current state of the cache instance.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The TTL of all cache entries in whole seconds. e.g., "7200s".
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * The modification time of the cache instance metadata in RFC 3339 format.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * The zone in which the cache instance is running. For example, us-
   * central1-a.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnywhereCache::class, 'Google_Service_Storage_AnywhereCache');
