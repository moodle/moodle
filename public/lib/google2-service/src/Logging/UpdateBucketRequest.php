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

class UpdateBucketRequest extends \Google\Model
{
  protected $bucketType = LogBucket::class;
  protected $bucketDataType = '';
  /**
   * Required. The full resource name of the bucket to update.
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" "organi
   * zations/[ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" "bil
   * lingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_I
   * D]" "folders/[FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" For
   * example:"projects/my-project/locations/global/buckets/my-bucket"
   *
   * @var string
   */
  public $name;
  /**
   * Required. Field mask that specifies the fields in bucket that need an
   * update. A bucket field will be overwritten if, and only if, it is in the
   * update mask. name and output only fields cannot be updated.For a detailed
   * FieldMask definition, see: https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.FieldMaskFor
   * example: updateMask=retention_days
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The updated bucket.
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
   * Required. The full resource name of the bucket to update.
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" "organi
   * zations/[ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" "bil
   * lingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_I
   * D]" "folders/[FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]" For
   * example:"projects/my-project/locations/global/buckets/my-bucket"
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Field mask that specifies the fields in bucket that need an
   * update. A bucket field will be overwritten if, and only if, it is in the
   * update mask. name and output only fields cannot be updated.For a detailed
   * FieldMask definition, see: https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.FieldMaskFor
   * example: updateMask=retention_days
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateBucketRequest::class, 'Google_Service_Logging_UpdateBucketRequest');
