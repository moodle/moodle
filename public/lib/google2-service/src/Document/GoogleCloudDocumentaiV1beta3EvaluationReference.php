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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta3EvaluationReference extends \Google\Model
{
  protected $aggregateMetricsType = GoogleCloudDocumentaiV1beta3EvaluationMetrics::class;
  protected $aggregateMetricsDataType = '';
  protected $aggregateMetricsExactType = GoogleCloudDocumentaiV1beta3EvaluationMetrics::class;
  protected $aggregateMetricsExactDataType = '';
  /**
   * The resource name of the evaluation.
   *
   * @var string
   */
  public $evaluation;
  /**
   * The resource name of the Long Running Operation for the evaluation.
   *
   * @var string
   */
  public $operation;

  /**
   * An aggregate of the statistics for the evaluation with fuzzy matching on.
   *
   * @param GoogleCloudDocumentaiV1beta3EvaluationMetrics $aggregateMetrics
   */
  public function setAggregateMetrics(GoogleCloudDocumentaiV1beta3EvaluationMetrics $aggregateMetrics)
  {
    $this->aggregateMetrics = $aggregateMetrics;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3EvaluationMetrics
   */
  public function getAggregateMetrics()
  {
    return $this->aggregateMetrics;
  }
  /**
   * An aggregate of the statistics for the evaluation with fuzzy matching off.
   *
   * @param GoogleCloudDocumentaiV1beta3EvaluationMetrics $aggregateMetricsExact
   */
  public function setAggregateMetricsExact(GoogleCloudDocumentaiV1beta3EvaluationMetrics $aggregateMetricsExact)
  {
    $this->aggregateMetricsExact = $aggregateMetricsExact;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3EvaluationMetrics
   */
  public function getAggregateMetricsExact()
  {
    return $this->aggregateMetricsExact;
  }
  /**
   * The resource name of the evaluation.
   *
   * @param string $evaluation
   */
  public function setEvaluation($evaluation)
  {
    $this->evaluation = $evaluation;
  }
  /**
   * @return string
   */
  public function getEvaluation()
  {
    return $this->evaluation;
  }
  /**
   * The resource name of the Long Running Operation for the evaluation.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3EvaluationReference::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3EvaluationReference');
