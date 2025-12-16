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

class XPSClassificationEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'confidenceMetricsEntries';
  /**
   * The Area under precision recall curve metric.
   *
   * @var float
   */
  public $auPrc;
  /**
   * The Area Under Receiver Operating Characteristic curve metric. Micro-
   * averaged for the overall evaluation.
   *
   * @var float
   */
  public $auRoc;
  /**
   * The Area under precision recall curve metric based on priors.
   *
   * @var float
   */
  public $baseAuPrc;
  protected $confidenceMetricsEntriesType = XPSConfidenceMetricsEntry::class;
  protected $confidenceMetricsEntriesDataType = 'array';
  protected $confusionMatrixType = XPSConfusionMatrix::class;
  protected $confusionMatrixDataType = '';
  /**
   * The number of examples used for model evaluation.
   *
   * @var int
   */
  public $evaluatedExamplesCount;
  /**
   * The Log Loss metric.
   *
   * @var float
   */
  public $logLoss;

  /**
   * The Area under precision recall curve metric.
   *
   * @param float $auPrc
   */
  public function setAuPrc($auPrc)
  {
    $this->auPrc = $auPrc;
  }
  /**
   * @return float
   */
  public function getAuPrc()
  {
    return $this->auPrc;
  }
  /**
   * The Area Under Receiver Operating Characteristic curve metric. Micro-
   * averaged for the overall evaluation.
   *
   * @param float $auRoc
   */
  public function setAuRoc($auRoc)
  {
    $this->auRoc = $auRoc;
  }
  /**
   * @return float
   */
  public function getAuRoc()
  {
    return $this->auRoc;
  }
  /**
   * The Area under precision recall curve metric based on priors.
   *
   * @param float $baseAuPrc
   */
  public function setBaseAuPrc($baseAuPrc)
  {
    $this->baseAuPrc = $baseAuPrc;
  }
  /**
   * @return float
   */
  public function getBaseAuPrc()
  {
    return $this->baseAuPrc;
  }
  /**
   * Metrics that have confidence thresholds. Precision-recall curve can be
   * derived from it.
   *
   * @param XPSConfidenceMetricsEntry[] $confidenceMetricsEntries
   */
  public function setConfidenceMetricsEntries($confidenceMetricsEntries)
  {
    $this->confidenceMetricsEntries = $confidenceMetricsEntries;
  }
  /**
   * @return XPSConfidenceMetricsEntry[]
   */
  public function getConfidenceMetricsEntries()
  {
    return $this->confidenceMetricsEntries;
  }
  /**
   * Confusion matrix of the evaluation. Only set for MULTICLASS classification
   * problems where number of annotation specs is no more than 10. Only set for
   * model level evaluation, not for evaluation per label.
   *
   * @param XPSConfusionMatrix $confusionMatrix
   */
  public function setConfusionMatrix(XPSConfusionMatrix $confusionMatrix)
  {
    $this->confusionMatrix = $confusionMatrix;
  }
  /**
   * @return XPSConfusionMatrix
   */
  public function getConfusionMatrix()
  {
    return $this->confusionMatrix;
  }
  /**
   * The number of examples used for model evaluation.
   *
   * @param int $evaluatedExamplesCount
   */
  public function setEvaluatedExamplesCount($evaluatedExamplesCount)
  {
    $this->evaluatedExamplesCount = $evaluatedExamplesCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedExamplesCount()
  {
    return $this->evaluatedExamplesCount;
  }
  /**
   * The Log Loss metric.
   *
   * @param float $logLoss
   */
  public function setLogLoss($logLoss)
  {
    $this->logLoss = $logLoss;
  }
  /**
   * @return float
   */
  public function getLogLoss()
  {
    return $this->logLoss;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSClassificationEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSClassificationEvaluationMetrics');
