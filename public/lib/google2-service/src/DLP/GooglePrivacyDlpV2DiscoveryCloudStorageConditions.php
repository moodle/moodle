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

class GooglePrivacyDlpV2DiscoveryCloudStorageConditions extends \Google\Collection
{
  protected $collection_key = 'includedObjectAttributes';
  /**
   * Required. Only objects with the specified attributes will be scanned.
   * Defaults to [ALL_SUPPORTED_BUCKETS] if unset.
   *
   * @var string[]
   */
  public $includedBucketAttributes;
  /**
   * Required. Only objects with the specified attributes will be scanned. If an
   * object has one of the specified attributes but is inside an excluded
   * bucket, it will not be scanned. Defaults to [ALL_SUPPORTED_OBJECTS]. A
   * profile will be created even if no objects match the
   * included_object_attributes.
   *
   * @var string[]
   */
  public $includedObjectAttributes;

  /**
   * Required. Only objects with the specified attributes will be scanned.
   * Defaults to [ALL_SUPPORTED_BUCKETS] if unset.
   *
   * @param string[] $includedBucketAttributes
   */
  public function setIncludedBucketAttributes($includedBucketAttributes)
  {
    $this->includedBucketAttributes = $includedBucketAttributes;
  }
  /**
   * @return string[]
   */
  public function getIncludedBucketAttributes()
  {
    return $this->includedBucketAttributes;
  }
  /**
   * Required. Only objects with the specified attributes will be scanned. If an
   * object has one of the specified attributes but is inside an excluded
   * bucket, it will not be scanned. Defaults to [ALL_SUPPORTED_OBJECTS]. A
   * profile will be created even if no objects match the
   * included_object_attributes.
   *
   * @param string[] $includedObjectAttributes
   */
  public function setIncludedObjectAttributes($includedObjectAttributes)
  {
    $this->includedObjectAttributes = $includedObjectAttributes;
  }
  /**
   * @return string[]
   */
  public function getIncludedObjectAttributes()
  {
    return $this->includedObjectAttributes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryCloudStorageConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryCloudStorageConditions');
