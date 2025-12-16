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

class GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResultFrame extends \Google\Model
{
  /**
   * A time (frame) of a video in which the object has been detected. Expressed
   * as a number of seconds as measured from the start of the video, with
   * fractions up to a microsecond precision, and with "s" appended at the end.
   *
   * @var string
   */
  public $timeOffset;
  /**
   * The rightmost coordinate of the bounding box.
   *
   * @var float
   */
  public $xMax;
  /**
   * The leftmost coordinate of the bounding box.
   *
   * @var float
   */
  public $xMin;
  /**
   * The bottommost coordinate of the bounding box.
   *
   * @var float
   */
  public $yMax;
  /**
   * The topmost coordinate of the bounding box.
   *
   * @var float
   */
  public $yMin;

  /**
   * A time (frame) of a video in which the object has been detected. Expressed
   * as a number of seconds as measured from the start of the video, with
   * fractions up to a microsecond precision, and with "s" appended at the end.
   *
   * @param string $timeOffset
   */
  public function setTimeOffset($timeOffset)
  {
    $this->timeOffset = $timeOffset;
  }
  /**
   * @return string
   */
  public function getTimeOffset()
  {
    return $this->timeOffset;
  }
  /**
   * The rightmost coordinate of the bounding box.
   *
   * @param float $xMax
   */
  public function setXMax($xMax)
  {
    $this->xMax = $xMax;
  }
  /**
   * @return float
   */
  public function getXMax()
  {
    return $this->xMax;
  }
  /**
   * The leftmost coordinate of the bounding box.
   *
   * @param float $xMin
   */
  public function setXMin($xMin)
  {
    $this->xMin = $xMin;
  }
  /**
   * @return float
   */
  public function getXMin()
  {
    return $this->xMin;
  }
  /**
   * The bottommost coordinate of the bounding box.
   *
   * @param float $yMax
   */
  public function setYMax($yMax)
  {
    $this->yMax = $yMax;
  }
  /**
   * @return float
   */
  public function getYMax()
  {
    return $this->yMax;
  }
  /**
   * The topmost coordinate of the bounding box.
   *
   * @param float $yMin
   */
  public function setYMin($yMin)
  {
    $this->yMin = $yMin;
  }
  /**
   * @return float
   */
  public function getYMin()
  {
    return $this->yMin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResultFrame::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictPredictionVideoObjectTrackingPredictionResultFrame');
