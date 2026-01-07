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

class BulkRestoreObjectsRequest extends \Google\Collection
{
  protected $collection_key = 'matchGlobs';
  /**
   * If false (default), the restore will not overwrite live objects with the
   * same name at the destination. This means some deleted objects may be
   * skipped. If true, live objects will be overwritten resulting in a
   * noncurrent object (if versioning is enabled). If versioning is not enabled,
   * overwriting the object will result in a soft-deleted object. In either
   * case, if a noncurrent object already exists with the same name, a live
   * version can be written without issue.
   *
   * @var bool
   */
  public $allowOverwrite;
  /**
   * If true, copies the source object's ACL; otherwise, uses the bucket's
   * default object ACL. The default is false.
   *
   * @var bool
   */
  public $copySourceAcl;
  /**
   * Restores only the objects that were created after this time.
   *
   * @var string
   */
  public $createdAfterTime;
  /**
   * Restores only the objects that were created before this time.
   *
   * @var string
   */
  public $createdBeforeTime;
  /**
   * Restores only the objects matching any of the specified glob(s). If this
   * parameter is not specified, all objects will be restored within the
   * specified time range.
   *
   * @var string[]
   */
  public $matchGlobs;
  /**
   * Restores only the objects that were soft-deleted after this time.
   *
   * @var string
   */
  public $softDeletedAfterTime;
  /**
   * Restores only the objects that were soft-deleted before this time.
   *
   * @var string
   */
  public $softDeletedBeforeTime;

  /**
   * If false (default), the restore will not overwrite live objects with the
   * same name at the destination. This means some deleted objects may be
   * skipped. If true, live objects will be overwritten resulting in a
   * noncurrent object (if versioning is enabled). If versioning is not enabled,
   * overwriting the object will result in a soft-deleted object. In either
   * case, if a noncurrent object already exists with the same name, a live
   * version can be written without issue.
   *
   * @param bool $allowOverwrite
   */
  public function setAllowOverwrite($allowOverwrite)
  {
    $this->allowOverwrite = $allowOverwrite;
  }
  /**
   * @return bool
   */
  public function getAllowOverwrite()
  {
    return $this->allowOverwrite;
  }
  /**
   * If true, copies the source object's ACL; otherwise, uses the bucket's
   * default object ACL. The default is false.
   *
   * @param bool $copySourceAcl
   */
  public function setCopySourceAcl($copySourceAcl)
  {
    $this->copySourceAcl = $copySourceAcl;
  }
  /**
   * @return bool
   */
  public function getCopySourceAcl()
  {
    return $this->copySourceAcl;
  }
  /**
   * Restores only the objects that were created after this time.
   *
   * @param string $createdAfterTime
   */
  public function setCreatedAfterTime($createdAfterTime)
  {
    $this->createdAfterTime = $createdAfterTime;
  }
  /**
   * @return string
   */
  public function getCreatedAfterTime()
  {
    return $this->createdAfterTime;
  }
  /**
   * Restores only the objects that were created before this time.
   *
   * @param string $createdBeforeTime
   */
  public function setCreatedBeforeTime($createdBeforeTime)
  {
    $this->createdBeforeTime = $createdBeforeTime;
  }
  /**
   * @return string
   */
  public function getCreatedBeforeTime()
  {
    return $this->createdBeforeTime;
  }
  /**
   * Restores only the objects matching any of the specified glob(s). If this
   * parameter is not specified, all objects will be restored within the
   * specified time range.
   *
   * @param string[] $matchGlobs
   */
  public function setMatchGlobs($matchGlobs)
  {
    $this->matchGlobs = $matchGlobs;
  }
  /**
   * @return string[]
   */
  public function getMatchGlobs()
  {
    return $this->matchGlobs;
  }
  /**
   * Restores only the objects that were soft-deleted after this time.
   *
   * @param string $softDeletedAfterTime
   */
  public function setSoftDeletedAfterTime($softDeletedAfterTime)
  {
    $this->softDeletedAfterTime = $softDeletedAfterTime;
  }
  /**
   * @return string
   */
  public function getSoftDeletedAfterTime()
  {
    return $this->softDeletedAfterTime;
  }
  /**
   * Restores only the objects that were soft-deleted before this time.
   *
   * @param string $softDeletedBeforeTime
   */
  public function setSoftDeletedBeforeTime($softDeletedBeforeTime)
  {
    $this->softDeletedBeforeTime = $softDeletedBeforeTime;
  }
  /**
   * @return string
   */
  public function getSoftDeletedBeforeTime()
  {
    return $this->softDeletedBeforeTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkRestoreObjectsRequest::class, 'Google_Service_Storage_BulkRestoreObjectsRequest');
