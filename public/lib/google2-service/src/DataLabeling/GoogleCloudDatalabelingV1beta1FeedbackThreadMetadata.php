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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1FeedbackThreadMetadata extends \Google\Model
{
  public const STATUS_FEEDBACK_THREAD_STATUS_UNSPECIFIED = 'FEEDBACK_THREAD_STATUS_UNSPECIFIED';
  /**
   * Feedback thread is created with no reply;
   */
  public const STATUS_NEW = 'NEW';
  /**
   * Feedback thread is replied at least once;
   */
  public const STATUS_REPLIED = 'REPLIED';
  /**
   * When the thread is created
   *
   * @var string
   */
  public $createTime;
  /**
   * When the thread is last updated.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * @var string
   */
  public $status;
  /**
   * An image thumbnail of this thread.
   *
   * @var string
   */
  public $thumbnail;

  /**
   * When the thread is created
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
   * When the thread is last updated.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * An image thumbnail of this thread.
   *
   * @param string $thumbnail
   */
  public function setThumbnail($thumbnail)
  {
    $this->thumbnail = $thumbnail;
  }
  /**
   * @return string
   */
  public function getThumbnail()
  {
    return $this->thumbnail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1FeedbackThreadMetadata::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1FeedbackThreadMetadata');
