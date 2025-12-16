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

class BucketSoftDeletePolicy extends \Google\Model
{
  /**
   * Server-determined value that indicates the time from which the policy, or
   * one with a greater retention, was effective. This value is in RFC 3339
   * format.
   *
   * @var string
   */
  public $effectiveTime;
  /**
   * The duration in seconds that soft-deleted objects in the bucket will be
   * retained and cannot be permanently deleted.
   *
   * @var string
   */
  public $retentionDurationSeconds;

  /**
   * Server-determined value that indicates the time from which the policy, or
   * one with a greater retention, was effective. This value is in RFC 3339
   * format.
   *
   * @param string $effectiveTime
   */
  public function setEffectiveTime($effectiveTime)
  {
    $this->effectiveTime = $effectiveTime;
  }
  /**
   * @return string
   */
  public function getEffectiveTime()
  {
    return $this->effectiveTime;
  }
  /**
   * The duration in seconds that soft-deleted objects in the bucket will be
   * retained and cannot be permanently deleted.
   *
   * @param string $retentionDurationSeconds
   */
  public function setRetentionDurationSeconds($retentionDurationSeconds)
  {
    $this->retentionDurationSeconds = $retentionDurationSeconds;
  }
  /**
   * @return string
   */
  public function getRetentionDurationSeconds()
  {
    return $this->retentionDurationSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketSoftDeletePolicy::class, 'Google_Service_Storage_BucketSoftDeletePolicy');
