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

class XPSEvaluationMetricsSet extends \Google\Collection
{
  protected $collection_key = 'evaluationMetrics';
  protected $evaluationMetricsType = XPSEvaluationMetrics::class;
  protected $evaluationMetricsDataType = 'array';
  protected $fileSpecType = XPSFileSpec::class;
  protected $fileSpecDataType = '';
  /**
   * Number of the evaluation metrics (usually one per label plus overall).
   *
   * @var string
   */
  public $numEvaluationMetrics;

  /**
   * Inline EvaluationMetrics - should be relatively small. For passing large
   * quantities of exhaustive metrics, use file_spec.
   *
   * @param XPSEvaluationMetrics[] $evaluationMetrics
   */
  public function setEvaluationMetrics($evaluationMetrics)
  {
    $this->evaluationMetrics = $evaluationMetrics;
  }
  /**
   * @return XPSEvaluationMetrics[]
   */
  public function getEvaluationMetrics()
  {
    return $this->evaluationMetrics;
  }
  /**
   * File spec containing evaluation metrics of a model, must point to RecordIO
   * file(s) of intelligence.cloud.automl.xps.EvaluationMetrics messages.
   *
   * @param XPSFileSpec $fileSpec
   */
  public function setFileSpec(XPSFileSpec $fileSpec)
  {
    $this->fileSpec = $fileSpec;
  }
  /**
   * @return XPSFileSpec
   */
  public function getFileSpec()
  {
    return $this->fileSpec;
  }
  /**
   * Number of the evaluation metrics (usually one per label plus overall).
   *
   * @param string $numEvaluationMetrics
   */
  public function setNumEvaluationMetrics($numEvaluationMetrics)
  {
    $this->numEvaluationMetrics = $numEvaluationMetrics;
  }
  /**
   * @return string
   */
  public function getNumEvaluationMetrics()
  {
    return $this->numEvaluationMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSEvaluationMetricsSet::class, 'Google_Service_CloudNaturalLanguage_XPSEvaluationMetricsSet');
