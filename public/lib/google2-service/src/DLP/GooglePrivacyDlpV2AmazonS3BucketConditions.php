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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2AmazonS3BucketConditions extends \Google\Collection
{
  protected $collection_key = 'objectStorageClasses';
  /**
   * Optional. Bucket types that should be profiled. Optional. Defaults to
   * TYPE_ALL_SUPPORTED if unspecified.
   *
   * @var string[]
   */
  public $bucketTypes;
  /**
   * Optional. Object classes that should be profiled. Optional. Defaults to
   * ALL_SUPPORTED_CLASSES if unspecified.
   *
   * @var string[]
   */
  public $objectStorageClasses;

  /**
   * Optional. Bucket types that should be profiled. Optional. Defaults to
   * TYPE_ALL_SUPPORTED if unspecified.
   *
   * @param string[] $bucketTypes
   */
  public function setBucketTypes($bucketTypes)
  {
    $this->bucketTypes = $bucketTypes;
  }
  /**
   * @return string[]
   */
  public function getBucketTypes()
  {
    return $this->bucketTypes;
  }
  /**
   * Optional. Object classes that should be profiled. Optional. Defaults to
   * ALL_SUPPORTED_CLASSES if unspecified.
   *
   * @param string[] $objectStorageClasses
   */
  public function setObjectStorageClasses($objectStorageClasses)
  {
    $this->objectStorageClasses = $objectStorageClasses;
  }
  /**
   * @return string[]
   */
  public function getObjectStorageClasses()
  {
    return $this->objectStorageClasses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2AmazonS3BucketConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2AmazonS3BucketConditions');
