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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoObjectTrackingMetrics extends \Google\Collection
{
  protected $collection_key = 'trackMetrics';
  /**
   * The single metric for bounding boxes evaluation: the `meanAveragePrecision`
   * averaged over all `boundingBoxMetrics`.
   *
   * @var float
   */
  public $boundingBoxMeanAveragePrecision;
  protected $boundingBoxMetricsType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsBoundingBoxMetrics::class;
  protected $boundingBoxMetricsDataType = 'array';
  /**
   * UNIMPLEMENTED. The total number of bounding boxes (i.e. summed over all
   * frames) the ground truth used to create this evaluation had.
   *
   * @var int
   */
  public $evaluatedBoundingBoxCount;
  /**
   * UNIMPLEMENTED. The number of video frames used to create this evaluation.
   *
   * @var int
   */
  public $evaluatedFrameCount;
  /**
   * UNIMPLEMENTED. The total number of tracks (i.e. as seen across all frames)
   * the ground truth used to create this evaluation had.
   *
   * @var int
   */
  public $evaluatedTrackCount;
  /**
   * UNIMPLEMENTED. The single metric for tracks accuracy evaluation: the
   * `meanAveragePrecision` averaged over all `trackMetrics`.
   *
   * @var float
   */
  public $trackMeanAveragePrecision;
  /**
   * UNIMPLEMENTED. The single metric for tracks bounding box iou evaluation:
   * the `meanBoundingBoxIou` averaged over all `trackMetrics`.
   *
   * @var float
   */
  public $trackMeanBoundingBoxIou;
  /**
   * UNIMPLEMENTED. The single metric for tracking consistency evaluation: the
   * `meanMismatchRate` averaged over all `trackMetrics`.
   *
   * @var float
   */
  public $trackMeanMismatchRate;
  protected $trackMetricsType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsTrackMetrics::class;
  protected $trackMetricsDataType = 'array';

  /**
   * The single metric for bounding boxes evaluation: the `meanAveragePrecision`
   * averaged over all `boundingBoxMetrics`.
   *
   * @param float $boundingBoxMeanAveragePrecision
   */
  public function setBoundingBoxMeanAveragePrecision($boundingBoxMeanAveragePrecision)
  {
    $this->boundingBoxMeanAveragePrecision = $boundingBoxMeanAveragePrecision;
  }
  /**
   * @return float
   */
  public function getBoundingBoxMeanAveragePrecision()
  {
    return $this->boundingBoxMeanAveragePrecision;
  }
  /**
   * The bounding boxes match metrics for each intersection-over-union threshold
   * 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99 and each label confidence threshold
   * 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99 pair.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsBoundingBoxMetrics[] $boundingBoxMetrics
   */
  public function setBoundingBoxMetrics($boundingBoxMetrics)
  {
    $this->boundingBoxMetrics = $boundingBoxMetrics;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsBoundingBoxMetrics[]
   */
  public function getBoundingBoxMetrics()
  {
    return $this->boundingBoxMetrics;
  }
  /**
   * UNIMPLEMENTED. The total number of bounding boxes (i.e. summed over all
   * frames) the ground truth used to create this evaluation had.
   *
   * @param int $evaluatedBoundingBoxCount
   */
  public function setEvaluatedBoundingBoxCount($evaluatedBoundingBoxCount)
  {
    $this->evaluatedBoundingBoxCount = $evaluatedBoundingBoxCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedBoundingBoxCount()
  {
    return $this->evaluatedBoundingBoxCount;
  }
  /**
   * UNIMPLEMENTED. The number of video frames used to create this evaluation.
   *
   * @param int $evaluatedFrameCount
   */
  public function setEvaluatedFrameCount($evaluatedFrameCount)
  {
    $this->evaluatedFrameCount = $evaluatedFrameCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedFrameCount()
  {
    return $this->evaluatedFrameCount;
  }
  /**
   * UNIMPLEMENTED. The total number of tracks (i.e. as seen across all frames)
   * the ground truth used to create this evaluation had.
   *
   * @param int $evaluatedTrackCount
   */
  public function setEvaluatedTrackCount($evaluatedTrackCount)
  {
    $this->evaluatedTrackCount = $evaluatedTrackCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedTrackCount()
  {
    return $this->evaluatedTrackCount;
  }
  /**
   * UNIMPLEMENTED. The single metric for tracks accuracy evaluation: the
   * `meanAveragePrecision` averaged over all `trackMetrics`.
   *
   * @param float $trackMeanAveragePrecision
   */
  public function setTrackMeanAveragePrecision($trackMeanAveragePrecision)
  {
    $this->trackMeanAveragePrecision = $trackMeanAveragePrecision;
  }
  /**
   * @return float
   */
  public function getTrackMeanAveragePrecision()
  {
    return $this->trackMeanAveragePrecision;
  }
  /**
   * UNIMPLEMENTED. The single metric for tracks bounding box iou evaluation:
   * the `meanBoundingBoxIou` averaged over all `trackMetrics`.
   *
   * @param float $trackMeanBoundingBoxIou
   */
  public function setTrackMeanBoundingBoxIou($trackMeanBoundingBoxIou)
  {
    $this->trackMeanBoundingBoxIou = $trackMeanBoundingBoxIou;
  }
  /**
   * @return float
   */
  public function getTrackMeanBoundingBoxIou()
  {
    return $this->trackMeanBoundingBoxIou;
  }
  /**
   * UNIMPLEMENTED. The single metric for tracking consistency evaluation: the
   * `meanMismatchRate` averaged over all `trackMetrics`.
   *
   * @param float $trackMeanMismatchRate
   */
  public function setTrackMeanMismatchRate($trackMeanMismatchRate)
  {
    $this->trackMeanMismatchRate = $trackMeanMismatchRate;
  }
  /**
   * @return float
   */
  public function getTrackMeanMismatchRate()
  {
    return $this->trackMeanMismatchRate;
  }
  /**
   * UNIMPLEMENTED. The tracks match metrics for each intersection-over-union
   * threshold 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99 and each label confidence
   * threshold 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99 pair.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsTrackMetrics[] $trackMetrics
   */
  public function setTrackMetrics($trackMetrics)
  {
    $this->trackMetrics = $trackMetrics;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsTrackMetrics[]
   */
  public function getTrackMetrics()
  {
    return $this->trackMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoObjectTrackingMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoObjectTrackingMetrics');
