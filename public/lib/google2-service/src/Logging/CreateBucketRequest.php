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

namespace Google\Service\Logging;

class CreateBucketRequest extends \Google\Model
{
  protected $bucketType = LogBucket::class;
  protected $bucketDataType = '';
  /**
   * Required. A client-assigned identifier such as "my-bucket". Identifiers are
   * limited to 100 characters and can include only letters, digits,
   * underscores, hyphens, and periods. Bucket identifiers must start with an
   * alphanumeric character.
   *
   * @var string
   */
  public $bucketId;
  /**
   * Required. The resource in which to create the log bucket:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]" For example:"projects/my-
   * project/locations/global"
   *
   * @var string
   */
  public $parent;

  /**
   * Required. The new bucket. The region specified in the new bucket must be
   * compliant with any Location Restriction Org Policy. The name field in the
   * bucket is ignored.
   *
   * @param LogBucket $bucket
   */
  public function setBucket(LogBucket $bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return LogBucket
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Required. A client-assigned identifier such as "my-bucket". Identifiers are
   * limited to 100 characters and can include only letters, digits,
   * underscores, hyphens, and periods. Bucket identifiers must start with an
   * alphanumeric character.
   *
   * @param string $bucketId
   */
  public function setBucketId($bucketId)
  {
    $this->bucketId = $bucketId;
  }
  /**
   * @return string
   */
  public function getBucketId()
  {
    return $this->bucketId;
  }
  /**
   * Required. The resource in which to create the log bucket:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]" For example:"projects/my-
   * project/locations/global"
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateBucketRequest::class, 'Google_Service_Logging_CreateBucketRequest');
