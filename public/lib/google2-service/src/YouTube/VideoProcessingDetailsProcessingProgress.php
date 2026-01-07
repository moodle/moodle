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

namespace Google\Service\YouTube;

class VideoProcessingDetailsProcessingProgress extends \Google\Model
{
  /**
   * The number of parts of the video that YouTube has already processed. You
   * can estimate the percentage of the video that YouTube has already processed
   * by calculating: 100 * parts_processed / parts_total Note that since the
   * estimated number of parts could increase without a corresponding increase
   * in the number of parts that have already been processed, it is possible
   * that the calculated progress could periodically decrease while YouTube
   * processes a video.
   *
   * @var string
   */
  public $partsProcessed;
  /**
   * An estimate of the total number of parts that need to be processed for the
   * video. The number may be updated with more precise estimates while YouTube
   * processes the video.
   *
   * @var string
   */
  public $partsTotal;
  /**
   * An estimate of the amount of time, in millseconds, that YouTube needs to
   * finish processing the video.
   *
   * @var string
   */
  public $timeLeftMs;

  /**
   * The number of parts of the video that YouTube has already processed. You
   * can estimate the percentage of the video that YouTube has already processed
   * by calculating: 100 * parts_processed / parts_total Note that since the
   * estimated number of parts could increase without a corresponding increase
   * in the number of parts that have already been processed, it is possible
   * that the calculated progress could periodically decrease while YouTube
   * processes a video.
   *
   * @param string $partsProcessed
   */
  public function setPartsProcessed($partsProcessed)
  {
    $this->partsProcessed = $partsProcessed;
  }
  /**
   * @return string
   */
  public function getPartsProcessed()
  {
    return $this->partsProcessed;
  }
  /**
   * An estimate of the total number of parts that need to be processed for the
   * video. The number may be updated with more precise estimates while YouTube
   * processes the video.
   *
   * @param string $partsTotal
   */
  public function setPartsTotal($partsTotal)
  {
    $this->partsTotal = $partsTotal;
  }
  /**
   * @return string
   */
  public function getPartsTotal()
  {
    return $this->partsTotal;
  }
  /**
   * An estimate of the amount of time, in millseconds, that YouTube needs to
   * finish processing the video.
   *
   * @param string $timeLeftMs
   */
  public function setTimeLeftMs($timeLeftMs)
  {
    $this->timeLeftMs = $timeLeftMs;
  }
  /**
   * @return string
   */
  public function getTimeLeftMs()
  {
    return $this->timeLeftMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoProcessingDetailsProcessingProgress::class, 'Google_Service_YouTube_VideoProcessingDetailsProcessingProgress');
