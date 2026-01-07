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

namespace Google\Service\StorageBatchOperations;

class DeleteObject extends \Google\Model
{
  /**
   * Required. Controls deletion behavior when versioning is enabled for the
   * object's bucket. If true both live and noncurrent objects will be
   * permanently deleted. Otherwise live objects in versioned buckets will
   * become noncurrent and objects that were already noncurrent will be skipped.
   * This setting doesn't have any impact on the Soft Delete feature. All
   * objects deleted by this service can be be restored for the duration of the
   * Soft Delete retention duration if enabled. If enabled and the manifest
   * doesn't specify an object's generation, a GetObjectMetadata call (a Class B
   * operation) will be made to determine the live object generation.
   *
   * @var bool
   */
  public $permanentObjectDeletionEnabled;

  /**
   * Required. Controls deletion behavior when versioning is enabled for the
   * object's bucket. If true both live and noncurrent objects will be
   * permanently deleted. Otherwise live objects in versioned buckets will
   * become noncurrent and objects that were already noncurrent will be skipped.
   * This setting doesn't have any impact on the Soft Delete feature. All
   * objects deleted by this service can be be restored for the duration of the
   * Soft Delete retention duration if enabled. If enabled and the manifest
   * doesn't specify an object's generation, a GetObjectMetadata call (a Class B
   * operation) will be made to determine the live object generation.
   *
   * @param bool $permanentObjectDeletionEnabled
   */
  public function setPermanentObjectDeletionEnabled($permanentObjectDeletionEnabled)
  {
    $this->permanentObjectDeletionEnabled = $permanentObjectDeletionEnabled;
  }
  /**
   * @return bool
   */
  public function getPermanentObjectDeletionEnabled()
  {
    return $this->permanentObjectDeletionEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeleteObject::class, 'Google_Service_StorageBatchOperations_DeleteObject');
