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

namespace Google\Service\CloudNaturalLanguage;

class XPSXpsOperationMetadata extends \Google\Model
{
  /**
   * Optional. XPS server can opt to provide example count of the long running
   * operation (e.g. training, data importing, batch prediction).
   *
   * @var string
   */
  public $exampleCount;
  protected $reportingMetricsType = XPSReportingMetrics::class;
  protected $reportingMetricsDataType = '';
  protected $tablesTrainingOperationMetadataType = XPSTablesTrainingOperationMetadata::class;
  protected $tablesTrainingOperationMetadataDataType = '';
  protected $videoBatchPredictOperationMetadataType = XPSVideoBatchPredictOperationMetadata::class;
  protected $videoBatchPredictOperationMetadataDataType = '';
  protected $videoTrainingOperationMetadataType = XPSVideoTrainingOperationMetadata::class;
  protected $videoTrainingOperationMetadataDataType = '';
  protected $visionTrainingOperationMetadataType = XPSVisionTrainingOperationMetadata::class;
  protected $visionTrainingOperationMetadataDataType = '';

  /**
   * Optional. XPS server can opt to provide example count of the long running
   * operation (e.g. training, data importing, batch prediction).
   *
   * @param string $exampleCount
   */
  public function setExampleCount($exampleCount)
  {
    $this->exampleCount = $exampleCount;
  }
  /**
   * @return string
   */
  public function getExampleCount()
  {
    return $this->exampleCount;
  }
  /**
   * Metrics for the operation. By the time the operation is terminated (whether
   * succeeded or failed) as returned from XPS, AutoML BE assumes the metrics
   * are finalized. AutoML BE transparently posts the metrics to Chemist if it's
   * not empty, regardless of the response content or error type. If user is
   * supposed to be charged in case of cancellation/error, this field should be
   * set. In the case where the type of LRO doesn't require any billing, this
   * field should be left unset.
   *
   * @param XPSReportingMetrics $reportingMetrics
   */
  public function setReportingMetrics(XPSReportingMetrics $reportingMetrics)
  {
    $this->reportingMetrics = $reportingMetrics;
  }
  /**
   * @return XPSReportingMetrics
   */
  public function getReportingMetrics()
  {
    return $this->reportingMetrics;
  }
  /**
   * @param XPSTablesTrainingOperationMetadata $tablesTrainingOperationMetadata
   */
  public function setTablesTrainingOperationMetadata(XPSTablesTrainingOperationMetadata $tablesTrainingOperationMetadata)
  {
    $this->tablesTrainingOperationMetadata = $tablesTrainingOperationMetadata;
  }
  /**
   * @return XPSTablesTrainingOperationMetadata
   */
  public function getTablesTrainingOperationMetadata()
  {
    return $this->tablesTrainingOperationMetadata;
  }
  /**
   * @param XPSVideoBatchPredictOperationMetadata $videoBatchPredictOperationMetadata
   */
  public function setVideoBatchPredictOperationMetadata(XPSVideoBatchPredictOperationMetadata $videoBatchPredictOperationMetadata)
  {
    $this->videoBatchPredictOperationMetadata = $videoBatchPredictOperationMetadata;
  }
  /**
   * @return XPSVideoBatchPredictOperationMetadata
   */
  public function getVideoBatchPredictOperationMetadata()
  {
    return $this->videoBatchPredictOperationMetadata;
  }
  /**
   * @param XPSVideoTrainingOperationMetadata $videoTrainingOperationMetadata
   */
  public function setVideoTrainingOperationMetadata(XPSVideoTrainingOperationMetadata $videoTrainingOperationMetadata)
  {
    $this->videoTrainingOperationMetadata = $videoTrainingOperationMetadata;
  }
  /**
   * @return XPSVideoTrainingOperationMetadata
   */
  public function getVideoTrainingOperationMetadata()
  {
    return $this->videoTrainingOperationMetadata;
  }
  /**
   * @param XPSVisionTrainingOperationMetadata $visionTrainingOperationMetadata
   */
  public function setVisionTrainingOperationMetadata(XPSVisionTrainingOperationMetadata $visionTrainingOperationMetadata)
  {
    $this->visionTrainingOperationMetadata = $visionTrainingOperationMetadata;
  }
  /**
   * @return XPSVisionTrainingOperationMetadata
   */
  public function getVisionTrainingOperationMetadata()
  {
    return $this->visionTrainingOperationMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSXpsOperationMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSXpsOperationMetadata');
