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

class BucketIamConfigurationBucketPolicyOnly extends \Google\Model
{
  /**
   * If set, access is controlled only by bucket-level or above IAM policies.
   *
   * @var bool
   */
  public $enabled;
  /**
   * The deadline for changing iamConfiguration.bucketPolicyOnly.enabled from
   * true to false in RFC 3339 format. iamConfiguration.bucketPolicyOnly.enabled
   * may be changed from true to false until the locked time, after which the
   * field is immutable.
   *
   * @var string
   */
  public $lockedTime;

  /**
   * If set, access is controlled only by bucket-level or above IAM policies.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * The deadline for changing iamConfiguration.bucketPolicyOnly.enabled from
   * true to false in RFC 3339 format. iamConfiguration.bucketPolicyOnly.enabled
   * may be changed from true to false until the locked time, after which the
   * field is immutable.
   *
   * @param string $lockedTime
   */
  public function setLockedTime($lockedTime)
  {
    $this->lockedTime = $lockedTime;
  }
  /**
   * @return string
   */
  public function getLockedTime()
  {
    return $this->lockedTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketIamConfigurationBucketPolicyOnly::class, 'Google_Service_Storage_BucketIamConfigurationBucketPolicyOnly');
