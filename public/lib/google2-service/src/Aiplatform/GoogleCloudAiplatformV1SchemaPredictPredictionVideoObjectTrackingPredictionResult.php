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

class GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResult extends \Google\Collection
{
  protected $collection_key = 'frames';
  /**
   * The Model's confidence in correction of this prediction, higher value means
   * higher confidence.
   *
   * @var float
   */
  public $confidence;
  /**
   * The display name of the AnnotationSpec that had been identified.
   *
   * @var string
   */
  public $displayName;
  protected $framesType = GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResultFrame::class;
  protected $framesDataType = 'array';
  /**
   * The resource ID of the AnnotationSpec that had been identified.
   *
   * @var string
   */
  public $id;
  /**
   * The end, inclusive, of the video's time segment in which the object
   * instance has been detected. Expressed as a number of seconds as measured
   * from the start of the video, with fractions up to a microsecond precision,
   * and with "s" appended at the end.
   *
   * @var string
   */
  public $timeSegmentEnd;
  /**
   * The beginning, inclusive, of the video's time segment in which the object
   * instance has been detected. Expressed as a number of seconds as measured
   * from the start of the video, with fractions up to a microsecond precision,
   * and with "s" appended at the end.
   *
   * @var string
   */
  public $timeSegmentStart;

  /**
   * The Model's confidence in correction of this prediction, higher value means
   * higher confidence.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * The display name of the AnnotationSpec that had been identified.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * All of the frames of the video in which a single object instance has been
   * detected. The bounding boxes in the frames identify the same object.
   *
   * @param GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResultFrame[] $frames
   */
  public function setFrames($frames)
  {
    $this->frames = $frames;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResultFrame[]
   */
  public function getFrames()
  {
    return $this->frames;
  }
  /**
   * The resource ID of the AnnotationSpec that had been identified.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The end, inclusive, of the video's time segment in which the object
   * instance has been detected. Expressed as a number of seconds as measured
   * from the start of the video, with fractions up to a microsecond precision,
   * and with "s" appended at the end.
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
   * The beginning, inclusive, of the video's time segment in which the object
   * instance has been detected. Expressed as a number of seconds as measured
   * from the start of the video, with fractions up to a microsecond precision,
   * and with "s" appended at the end.
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
class_alias(GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResult');
