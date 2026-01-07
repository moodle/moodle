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

namespace Google\Service\Bigquery;

class TrainingRun extends \Google\Collection
{
  protected $collection_key = 'results';
  protected $classLevelGlobalExplanationsType = GlobalExplanation::class;
  protected $classLevelGlobalExplanationsDataType = 'array';
  protected $dataSplitResultType = DataSplitResult::class;
  protected $dataSplitResultDataType = '';
  protected $evaluationMetricsType = EvaluationMetrics::class;
  protected $evaluationMetricsDataType = '';
  protected $modelLevelGlobalExplanationType = GlobalExplanation::class;
  protected $modelLevelGlobalExplanationDataType = '';
  protected $resultsType = IterationResult::class;
  protected $resultsDataType = 'array';
  /**
   * Output only. The start time of this training run.
   *
   * @var string
   */
  public $startTime;
  protected $trainingOptionsType = TrainingOptions::class;
  protected $trainingOptionsDataType = '';
  /**
   * Output only. The start time of this training run, in milliseconds since
   * epoch.
   *
   * @deprecated
   * @var string
   */
  public $trainingStartTime;
  /**
   * The model id in the [Vertex AI Model
   * Registry](https://cloud.google.com/vertex-ai/docs/model-
   * registry/introduction) for this training run.
   *
   * @var string
   */
  public $vertexAiModelId;
  /**
   * Output only. The model version in the [Vertex AI Model
   * Registry](https://cloud.google.com/vertex-ai/docs/model-
   * registry/introduction) for this training run.
   *
   * @var string
   */
  public $vertexAiModelVersion;

  /**
   * Output only. Global explanation contains the explanation of top features on
   * the class level. Applies to classification models only.
   *
   * @param GlobalExplanation[] $classLevelGlobalExplanations
   */
  public function setClassLevelGlobalExplanations($classLevelGlobalExplanations)
  {
    $this->classLevelGlobalExplanations = $classLevelGlobalExplanations;
  }
  /**
   * @return GlobalExplanation[]
   */
  public function getClassLevelGlobalExplanations()
  {
    return $this->classLevelGlobalExplanations;
  }
  /**
   * Output only. Data split result of the training run. Only set when the input
   * data is actually split.
   *
   * @param DataSplitResult $dataSplitResult
   */
  public function setDataSplitResult(DataSplitResult $dataSplitResult)
  {
    $this->dataSplitResult = $dataSplitResult;
  }
  /**
   * @return DataSplitResult
   */
  public function getDataSplitResult()
  {
    return $this->dataSplitResult;
  }
  /**
   * Output only. The evaluation metrics over training/eval data that were
   * computed at the end of training.
   *
   * @param EvaluationMetrics $evaluationMetrics
   */
  public function setEvaluationMetrics(EvaluationMetrics $evaluationMetrics)
  {
    $this->evaluationMetrics = $evaluationMetrics;
  }
  /**
   * @return EvaluationMetrics
   */
  public function getEvaluationMetrics()
  {
    return $this->evaluationMetrics;
  }
  /**
   * Output only. Global explanation contains the explanation of top features on
   * the model level. Applies to both regression and classification models.
   *
   * @param GlobalExplanation $modelLevelGlobalExplanation
   */
  public function setModelLevelGlobalExplanation(GlobalExplanation $modelLevelGlobalExplanation)
  {
    $this->modelLevelGlobalExplanation = $modelLevelGlobalExplanation;
  }
  /**
   * @return GlobalExplanation
   */
  public function getModelLevelGlobalExplanation()
  {
    return $this->modelLevelGlobalExplanation;
  }
  /**
   * Output only. Output of each iteration run, results.size() <=
   * max_iterations.
   *
   * @param IterationResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return IterationResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * Output only. The start time of this training run.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. Options that were used for this training run, includes user
   * specified and default options that were used.
   *
   * @param TrainingOptions $trainingOptions
   */
  public function setTrainingOptions(TrainingOptions $trainingOptions)
  {
    $this->trainingOptions = $trainingOptions;
  }
  /**
   * @return TrainingOptions
   */
  public function getTrainingOptions()
  {
    return $this->trainingOptions;
  }
  /**
   * Output only. The start time of this training run, in milliseconds since
   * epoch.
   *
   * @deprecated
   * @param string $trainingStartTime
   */
  public function setTrainingStartTime($trainingStartTime)
  {
    $this->trainingStartTime = $trainingStartTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTrainingStartTime()
  {
    return $this->trainingStartTime;
  }
  /**
   * The model id in the [Vertex AI Model
   * Registry](https://cloud.google.com/vertex-ai/docs/model-
   * registry/introduction) for this training run.
   *
   * @param string $vertexAiModelId
   */
  public function setVertexAiModelId($vertexAiModelId)
  {
    $this->vertexAiModelId = $vertexAiModelId;
  }
  /**
   * @return string
   */
  public function getVertexAiModelId()
  {
    return $this->vertexAiModelId;
  }
  /**
   * Output only. The model version in the [Vertex AI Model
   * Registry](https://cloud.google.com/vertex-ai/docs/model-
   * registry/introduction) for this training run.
   *
   * @param string $vertexAiModelVersion
   */
  public function setVertexAiModelVersion($vertexAiModelVersion)
  {
    $this->vertexAiModelVersion = $vertexAiModelVersion;
  }
  /**
   * @return string
   */
  public function getVertexAiModelVersion()
  {
    return $this->vertexAiModelVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrainingRun::class, 'Google_Service_Bigquery_TrainingRun');
