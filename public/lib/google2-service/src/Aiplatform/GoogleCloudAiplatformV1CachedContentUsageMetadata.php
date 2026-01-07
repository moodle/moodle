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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1CachedContentUsageMetadata extends \Google\Model
{
  /**
   * Duration of audio in seconds.
   *
   * @var int
   */
  public $audioDurationSeconds;
  /**
   * Number of images.
   *
   * @var int
   */
  public $imageCount;
  /**
   * Number of text characters.
   *
   * @var int
   */
  public $textCount;
  /**
   * Total number of tokens that the cached content consumes.
   *
   * @var int
   */
  public $totalTokenCount;
  /**
   * Duration of video in seconds.
   *
   * @var int
   */
  public $videoDurationSeconds;

  /**
   * Duration of audio in seconds.
   *
   * @param int $audioDurationSeconds
   */
  public function setAudioDurationSeconds($audioDurationSeconds)
  {
    $this->audioDurationSeconds = $audioDurationSeconds;
  }
  /**
   * @return int
   */
  public function getAudioDurationSeconds()
  {
    return $this->audioDurationSeconds;
  }
  /**
   * Number of images.
   *
   * @param int $imageCount
   */
  public function setImageCount($imageCount)
  {
    $this->imageCount = $imageCount;
  }
  /**
   * @return int
   */
  public function getImageCount()
  {
    return $this->imageCount;
  }
  /**
   * Number of text characters.
   *
   * @param int $textCount
   */
  public function setTextCount($textCount)
  {
    $this->textCount = $textCount;
  }
  /**
   * @return int
   */
  public function getTextCount()
  {
    return $this->textCount;
  }
  /**
   * Total number of tokens that the cached content consumes.
   *
   * @param int $totalTokenCount
   */
  public function setTotalTokenCount($totalTokenCount)
  {
    $this->totalTokenCount = $totalTokenCount;
  }
  /**
   * @return int
   */
  public function getTotalTokenCount()
  {
    return $this->totalTokenCount;
  }
  /**
   * Duration of video in seconds.
   *
   * @param int $videoDurationSeconds
   */
  public function setVideoDurationSeconds($videoDurationSeconds)
  {
    $this->videoDurationSeconds = $videoDurationSeconds;
  }
  /**
   * @return int
   */
  public function getVideoDurationSeconds()
  {
    return $this->videoDurationSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CachedContentUsageMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CachedContentUsageMetadata');
