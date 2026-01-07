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

class GoogleCloudAiplatformV1SchemaPredictInstanceVideoObjectTrackingPredictionInstance extends \Google\Model
{
  /**
   * The Google Cloud Storage location of the video on which to perform the
   * prediction.
   *
   * @var string
   */
  public $content;
  /**
   * The MIME type of the content of the video. Only the following are
   * supported: video/mp4 video/avi video/quicktime
   *
   * @var string
   */
  public $mimeType;
  /**
   * The end, exclusive, of the video's time segment on which to perform the
   * prediction. Expressed as a number of seconds as measured from the start of
   * the video, with "s" appended at the end. Fractions are allowed, up to a
   * microsecond precision, and "inf" or "Infinity" is allowed, which means the
   * end of the video.
   *
   * @var string
   */
  public $timeSegmentEnd;
  /**
   * The beginning, inclusive, of the video's time segment on which to perform
   * the prediction. Expressed as a number of seconds as measured from the start
   * of the video, with "s" appended at the end. Fractions are allowed, up to a
   * microsecond precision.
   *
   * @var string
   */
  public $timeSegmentStart;

  /**
   * The Google Cloud Storage location of the video on which to perform the
   * prediction.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The MIME type of the content of the video. Only the following are
   * supported: video/mp4 video/avi video/quicktime
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The end, exclusive, of the video's time segment on which to perform the
   * prediction. Expressed as a number of seconds as measured from the start of
   * the video, with "s" appended at the end. Fractions are allowed, up to a
   * microsecond precision, and "inf" or "Infinity" is allowed, which means the
   * end of the video.
   *
   * @param string $timeSegmentEnd
   */
  public function setTimeSegmentEnd($timeSegmentEnd)
  {
    $this->timeSegmentEnd = $timeSegmentEnd;
  }
  /**
   * @return string
   */
  public function getTimeSegmentEnd()
  {
    return $this->timeSegmentEnd;
  }
  /**
   * The beginning, inclusive, of the video's time segment on which to perform
   * the prediction. Expressed as a number of seconds as measured from the start
   * of the video, with "s" appended at the end. Fractions are allowed, up to a
   * microsecond precision.
   *
   * @param string $timeSegmentStart
   */
  public function setTimeSegmentStart($timeSegmentStart)
  {
    $this->timeSegmentStart = $timeSegmentStart;
  }
  /**
   * @return string
   */
  public function getTimeSegmentStart()
  {
    return $this->timeSegmentStart;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictInstanceVideoObjectTrackingPredictionInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictInstanceVideoObjectTrackingPredictionInstance');
