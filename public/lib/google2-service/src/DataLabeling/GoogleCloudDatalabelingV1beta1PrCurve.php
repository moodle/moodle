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

class GoogleCloudDatalabelingV1beta1PrCurve extends \Google\Collection
{
  protected $collection_key = 'confidenceMetricsEntries';
  protected $annotationSpecType = GoogleCloudDatalabelingV1beta1AnnotationSpec::class;
  protected $annotationSpecDataType = '';
  /**
   * Area under the precision-recall curve. Not to be confused with area under a
   * receiver operating characteristic (ROC) curve.
   *
   * @var float
   */
  public $areaUnderCurve;
  protected $confidenceMetricsEntriesType = GoogleCloudDatalabelingV1beta1ConfidenceMetricsEntry::class;
  protected $confidenceMetricsEntriesDataType = 'array';
  /**
   * Mean average prcision of this curve.
   *
   * @var float
   */
  public $meanAveragePrecision;

  /**
   * The annotation spec of the label for which the precision-recall curve
   * calculated. If this field is empty, that means the precision-recall curve
   * is an aggregate curve for all labels.
   *
   * @param GoogleCloudDatalabelingV1beta1AnnotationSpec $annotationSpec
   */
  public function setAnnotationSpec(GoogleCloudDatalabelingV1beta1AnnotationSpec $annotationSpec)
  {
    $this->annotationSpec = $annotationSpec;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1AnnotationSpec
   */
  public function getAnnotationSpec()
  {
    return $this->annotationSpec;
  }
  /**
   * Area under the precision-recall curve. Not to be confused with area under a
   * receiver operating characteristic (ROC) curve.
   *
   * @param float $areaUnderCurve
   */
  public function setAreaUnderCurve($areaUnderCurve)
  {
    $this->areaUnderCurve = $areaUnderCurve;
  }
  /**
   * @return float
   */
  public function getAreaUnderCurve()
  {
    return $this->areaUnderCurve;
  }
  /**
   * Entries that make up the precision-recall graph. Each entry is a "point" on
   * the graph drawn for a different `confidence_threshold`.
   *
   * @param GoogleCloudDatalabelingV1beta1ConfidenceMetricsEntry[] $confidenceMetricsEntries
   */
  public function setConfidenceMetricsEntries($confidenceMetricsEntries)
  {
    $this->confidenceMetricsEntries = $confidenceMetricsEntries;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ConfidenceMetricsEntry[]
   */
  public function getConfidenceMetricsEntries()
  {
    return $this->confidenceMetricsEntries;
  }
  /**
   * Mean average prcision of this curve.
   *
   * @param float $meanAveragePrecision
   */
  public function setMeanAveragePrecision($meanAveragePrecision)
  {
    $this->meanAveragePrecision = $meanAveragePrecision;
  }
  /**
   * @return float
   */
  public function getMeanAveragePrecision()
  {
    return $this->meanAveragePrecision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1PrCurve::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1PrCurve');
