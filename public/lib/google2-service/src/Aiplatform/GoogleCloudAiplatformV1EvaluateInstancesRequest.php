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

class GoogleCloudAiplatformV1EvaluateInstancesRequest extends \Google\Collection
{
  protected $collection_key = 'metrics';
  protected $autoraterConfigType = GoogleCloudAiplatformV1AutoraterConfig::class;
  protected $autoraterConfigDataType = '';
  protected $bleuInputType = GoogleCloudAiplatformV1BleuInput::class;
  protected $bleuInputDataType = '';
  protected $coherenceInputType = GoogleCloudAiplatformV1CoherenceInput::class;
  protected $coherenceInputDataType = '';
  protected $cometInputType = GoogleCloudAiplatformV1CometInput::class;
  protected $cometInputDataType = '';
  protected $exactMatchInputType = GoogleCloudAiplatformV1ExactMatchInput::class;
  protected $exactMatchInputDataType = '';
  protected $fluencyInputType = GoogleCloudAiplatformV1FluencyInput::class;
  protected $fluencyInputDataType = '';
  protected $fulfillmentInputType = GoogleCloudAiplatformV1FulfillmentInput::class;
  protected $fulfillmentInputDataType = '';
  protected $groundednessInputType = GoogleCloudAiplatformV1GroundednessInput::class;
  protected $groundednessInputDataType = '';
  protected $instanceType = GoogleCloudAiplatformV1EvaluationInstance::class;
  protected $instanceDataType = '';
  /**
   * Required. The resource name of the Location to evaluate the instances.
   * Format: `projects/{project}/locations/{location}`
   *
   * @var string
   */
  public $location;
  protected $metricsType = GoogleCloudAiplatformV1Metric::class;
  protected $metricsDataType = 'array';
  protected $metricxInputType = GoogleCloudAiplatformV1MetricxInput::class;
  protected $metricxInputDataType = '';
  protected $pairwiseMetricInputType = GoogleCloudAiplatformV1PairwiseMetricInput::class;
  protected $pairwiseMetricInputDataType = '';
  protected $pairwiseQuestionAnsweringQualityInputType = GoogleCloudAiplatformV1PairwiseQuestionAnsweringQualityInput::class;
  protected $pairwiseQuestionAnsweringQualityInputDataType = '';
  protected $pairwiseSummarizationQualityInputType = GoogleCloudAiplatformV1PairwiseSummarizationQualityInput::class;
  protected $pairwiseSummarizationQualityInputDataType = '';
  protected $pointwiseMetricInputType = GoogleCloudAiplatformV1PointwiseMetricInput::class;
  protected $pointwiseMetricInputDataType = '';
  protected $questionAnsweringCorrectnessInputType = GoogleCloudAiplatformV1QuestionAnsweringCorrectnessInput::class;
  protected $questionAnsweringCorrectnessInputDataType = '';
  protected $questionAnsweringHelpfulnessInputType = GoogleCloudAiplatformV1QuestionAnsweringHelpfulnessInput::class;
  protected $questionAnsweringHelpfulnessInputDataType = '';
  protected $questionAnsweringQualityInputType = GoogleCloudAiplatformV1QuestionAnsweringQualityInput::class;
  protected $questionAnsweringQualityInputDataType = '';
  protected $questionAnsweringRelevanceInputType = GoogleCloudAiplatformV1QuestionAnsweringRelevanceInput::class;
  protected $questionAnsweringRelevanceInputDataType = '';
  protected $rougeInputType = GoogleCloudAiplatformV1RougeInput::class;
  protected $rougeInputDataType = '';
  protected $rubricBasedInstructionFollowingInputType = GoogleCloudAiplatformV1RubricBasedInstructionFollowingInput::class;
  protected $rubricBasedInstructionFollowingInputDataType = '';
  protected $safetyInputType = GoogleCloudAiplatformV1SafetyInput::class;
  protected $safetyInputDataType = '';
  protected $summarizationHelpfulnessInputType = GoogleCloudAiplatformV1SummarizationHelpfulnessInput::class;
  protected $summarizationHelpfulnessInputDataType = '';
  protected $summarizationQualityInputType = GoogleCloudAiplatformV1SummarizationQualityInput::class;
  protected $summarizationQualityInputDataType = '';
  protected $summarizationVerbosityInputType = GoogleCloudAiplatformV1SummarizationVerbosityInput::class;
  protected $summarizationVerbosityInputDataType = '';
  protected $toolCallValidInputType = GoogleCloudAiplatformV1ToolCallValidInput::class;
  protected $toolCallValidInputDataType = '';
  protected $toolNameMatchInputType = GoogleCloudAiplatformV1ToolNameMatchInput::class;
  protected $toolNameMatchInputDataType = '';
  protected $toolParameterKeyMatchInputType = GoogleCloudAiplatformV1ToolParameterKeyMatchInput::class;
  protected $toolParameterKeyMatchInputDataType = '';
  protected $toolParameterKvMatchInputType = GoogleCloudAiplatformV1ToolParameterKVMatchInput::class;
  protected $toolParameterKvMatchInputDataType = '';
  protected $trajectoryAnyOrderMatchInputType = GoogleCloudAiplatformV1TrajectoryAnyOrderMatchInput::class;
  protected $trajectoryAnyOrderMatchInputDataType = '';
  protected $trajectoryExactMatchInputType = GoogleCloudAiplatformV1TrajectoryExactMatchInput::class;
  protected $trajectoryExactMatchInputDataType = '';
  protected $trajectoryInOrderMatchInputType = GoogleCloudAiplatformV1TrajectoryInOrderMatchInput::class;
  protected $trajectoryInOrderMatchInputDataType = '';
  protected $trajectoryPrecisionInputType = GoogleCloudAiplatformV1TrajectoryPrecisionInput::class;
  protected $trajectoryPrecisionInputDataType = '';
  protected $trajectoryRecallInputType = GoogleCloudAiplatformV1TrajectoryRecallInput::class;
  protected $trajectoryRecallInputDataType = '';
  protected $trajectorySingleToolUseInputType = GoogleCloudAiplatformV1TrajectorySingleToolUseInput::class;
  protected $trajectorySingleToolUseInputDataType = '';

  /**
   * Optional. Autorater config used for evaluation.
   *
   * @param GoogleCloudAiplatformV1AutoraterConfig $autoraterConfig
   */
  public function setAutoraterConfig(GoogleCloudAiplatformV1AutoraterConfig $autoraterConfig)
  {
    $this->autoraterConfig = $autoraterConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AutoraterConfig
   */
  public function getAutoraterConfig()
  {
    return $this->autoraterConfig;
  }
  /**
   * Instances and metric spec for bleu metric.
   *
   * @param GoogleCloudAiplatformV1BleuInput $bleuInput
   */
  public function setBleuInput(GoogleCloudAiplatformV1BleuInput $bleuInput)
  {
    $this->bleuInput = $bleuInput;
  }
  /**
   * @return GoogleCloudAiplatformV1BleuInput
   */
  public function getBleuInput()
  {
    return $this->bleuInput;
  }
  /**
   * Input for coherence metric.
   *
   * @param GoogleCloudAiplatformV1CoherenceInput $coherenceInput
   */
  public function setCoherenceInput(GoogleCloudAiplatformV1CoherenceInput $coherenceInput)
  {
    $this->coherenceInput = $coherenceInput;
  }
  /**
   * @return GoogleCloudAiplatformV1CoherenceInput
   */
  public function getCoherenceInput()
  {
    return $this->coherenceInput;
  }
  /**
   * Translation metrics. Input for Comet metric.
   *
   * @param GoogleCloudAiplatformV1CometInput $cometInput
   */
  public function setCometInput(GoogleCloudAiplatformV1CometInput $cometInput)
  {
    $this->cometInput = $cometInput;
  }
  /**
   * @return GoogleCloudAiplatformV1CometInput
   */
  public function getCometInput()
  {
    return $this->cometInput;
  }
  /**
   * Auto metric instances. Instances and metric spec for exact match metric.
   *
   * @param GoogleCloudAiplatformV1ExactMatchInput $exactMatchInput
   */
  public function setExactMatchInput(GoogleCloudAiplatformV1ExactMatchInput $exactMatchInput)
  {
    $this->exactMatchInput = $exactMatchInput;
  }
  /**
   * @return GoogleCloudAiplatformV1ExactMatchInput
   */
  public function getExactMatchInput()
  {
    return $this->exactMatchInput;
  }
  /**
   * LLM-based metric instance. General text generation metrics, applicable to
   * other categories. Input for fluency metric.
   *
   * @param GoogleCloudAiplatformV1FluencyInput $fluencyInput
   */
  public function setFluencyInput(GoogleCloudAiplatformV1FluencyInput $fluencyInput)
  {
    $this->fluencyInput = $fluencyInput;
  }
  /**
   * @return GoogleCloudAiplatformV1FluencyInput
   */
  public function getFluencyInput()
  {
    return $this->fluencyInput;
  }
  /**
   * Input for fulfillment metric.
   *
   * @param GoogleCloudAiplatformV1FulfillmentInput $fulfillmentInput
   */
  public function setFulfillmentInput(GoogleCloudAiplatformV1FulfillmentInput $fulfillmentInput)
  {
    $this->fulfillmentInput = $fulfillmentInput;
  }
  /**
   * @return GoogleCloudAiplatformV1FulfillmentInput
   */
  public function getFulfillmentInput()
  {
    return $this->fulfillmentInput;
  }
  /**
   * Input for groundedness metric.
   *
   * @param GoogleCloudAiplatformV1GroundednessInput $groundednessInput
   */
  public function setGroundednessInput(GoogleCloudAiplatformV1GroundednessInput $groundednessInput)
  {
    $this->groundednessInput = $groundednessInput;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundednessInput
   */
  public function getGroundednessInput()
  {
    return $this->groundednessInput;
  }
  /**
   * The instance to be evaluated.
   *
   * @param GoogleCloudAiplatformV1EvaluationInstance $instance
   */
  public function setInstance(GoogleCloudAiplatformV1EvaluationInstance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationInstance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. The resource name of the Location to evaluate the instances.
   * Format: `projects/{project}/locations/{location}`
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The metrics used for evaluation. Currently, we only support evaluating a
   * single metric. If multiple metrics are provided, only the first one will be
   * evaluated.
   *
   * @param GoogleCloudAiplatformV1Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudAiplatformV1Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Input for Metricx metric.
   *
   * @param GoogleCloudAiplatformV1MetricxInput $metricxInput
   */
  public function setMetricxInput(GoogleCloudAiplatformV1MetricxInput $metricxInput)
  {
    $this->metricxInput = $metricxInput;
  }
  /**
   * @return GoogleCloudAiplatformV1MetricxInput
   */
  public function getMetricxInput()
  {
    return $this->metricxInput;
  }
  /**
   * Input for pairwise metric.
   *
   * @param GoogleCloudAiplatformV1PairwiseMetricInput $pairwiseMetricInput
   */
  public function setPairwiseMetricInput(GoogleCloudAiplatformV1PairwiseMetricInput $pairwiseMetricInput)
  {
    $this->pairwiseMetricInput = $pairwiseMetricInput;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseMetricInput
   */
  public function getPairwiseMetricInput()
  {
    return $this->pairwiseMetricInput;
  }
  /**
   * Input for pairwise question answering quality metric.
   *
   * @param GoogleCloudAiplatformV1PairwiseQuestionAnsweringQualityInput $pairwiseQuestionAnsweringQualityInput
   */
  public function setPairwiseQuestionAnsweringQualityInput(GoogleCloudAiplatformV1PairwiseQuestionAnsweringQualityInput $pairwiseQuestionAnsweringQualityInput)
  {
    $this->pairwiseQuestionAnsweringQualityInput = $pairwiseQuestionAnsweringQualityInput;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseQuestionAnsweringQualityInput
   */
  public function getPairwiseQuestionAnsweringQualityInput()
  {
    return $this->pairwiseQuestionAnsweringQualityInput;
  }
  /**
   * Input for pairwise summarization quality metric.
   *
   * @param GoogleCloudAiplatformV1PairwiseSummarizationQualityInput $pairwiseSummarizationQualityInput
   */
  public function setPairwiseSummarizationQualityInput(GoogleCloudAiplatformV1PairwiseSummarizationQualityInput $pairwiseSummarizationQualityInput)
  {
    $this->pairwiseSummarizationQualityInput = $pairwiseSummarizationQualityInput;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseSummarizationQualityInput
   */
  public function getPairwiseSummarizationQualityInput()
  {
    return $this->pairwiseSummarizationQualityInput;
  }
  /**
   * Input for pointwise metric.
   *
   * @param GoogleCloudAiplatformV1PointwiseMetricInput $pointwiseMetricInput
   */
  public function setPointwiseMetricInput(GoogleCloudAiplatformV1PointwiseMetricInput $pointwiseMetricInput)
  {
    $this->pointwiseMetricInput = $pointwiseMetricInput;
  }
  /**
   * @return GoogleCloudAiplatformV1PointwiseMetricInput
   */
  public function getPointwiseMetricInput()
  {
    return $this->pointwiseMetricInput;
  }
  /**
   * Input for question answering correctness metric.
   *
   * @param GoogleCloudAiplatformV1QuestionAnsweringCorrectnessInput $questionAnsweringCorrectnessInput
   */
  public function setQuestionAnsweringCorrectnessInput(GoogleCloudAiplatformV1QuestionAnsweringCorrectnessInput $questionAnsweringCorrectnessInput)
  {
    $this->questionAnsweringCorrectnessInput = $questionAnsweringCorrectnessInput;
  }
  /**
   * @return GoogleCloudAiplatformV1QuestionAnsweringCorrectnessInput
   */
  public function getQuestionAnsweringCorrectnessInput()
  {
    return $this->questionAnsweringCorrectnessInput;
  }
  /**
   * Input for question answering helpfulness metric.
   *
   * @param GoogleCloudAiplatformV1QuestionAnsweringHelpfulnessInput $questionAnsweringHelpfulnessInput
   */
  public function setQuestionAnsweringHelpfulnessInput(GoogleCloudAiplatformV1QuestionAnsweringHelpfulnessInput $questionAnsweringHelpfulnessInput)
  {
    $this->questionAnsweringHelpfulnessInput = $questionAnsweringHelpfulnessInput;
  }
  /**
   * @return GoogleCloudAiplatformV1QuestionAnsweringHelpfulnessInput
   */
  public function getQuestionAnsweringHelpfulnessInput()
  {
    return $this->questionAnsweringHelpfulnessInput;
  }
  /**
   * Input for question answering quality metric.
   *
   * @param GoogleCloudAiplatformV1QuestionAnsweringQualityInput $questionAnsweringQualityInput
   */
  public function setQuestionAnsweringQualityInput(GoogleCloudAiplatformV1QuestionAnsweringQualityInput $questionAnsweringQualityInput)
  {
    $this->questionAnsweringQualityInput = $questionAnsweringQualityInput;
  }
  /**
   * @return GoogleCloudAiplatformV1QuestionAnsweringQualityInput
   */
  public function getQuestionAnsweringQualityInput()
  {
    return $this->questionAnsweringQualityInput;
  }
  /**
   * Input for question answering relevance metric.
   *
   * @param GoogleCloudAiplatformV1QuestionAnsweringRelevanceInput $questionAnsweringRelevanceInput
   */
  public function setQuestionAnsweringRelevanceInput(GoogleCloudAiplatformV1QuestionAnsweringRelevanceInput $questionAnsweringRelevanceInput)
  {
    $this->questionAnsweringRelevanceInput = $questionAnsweringRelevanceInput;
  }
  /**
   * @return GoogleCloudAiplatformV1QuestionAnsweringRelevanceInput
   */
  public function getQuestionAnsweringRelevanceInput()
  {
    return $this->questionAnsweringRelevanceInput;
  }
  /**
   * Instances and metric spec for rouge metric.
   *
   * @param GoogleCloudAiplatformV1RougeInput $rougeInput
   */
  public function setRougeInput(GoogleCloudAiplatformV1RougeInput $rougeInput)
  {
    $this->rougeInput = $rougeInput;
  }
  /**
   * @return GoogleCloudAiplatformV1RougeInput
   */
  public function getRougeInput()
  {
    return $this->rougeInput;
  }
  /**
   * Rubric Based Instruction Following metric.
   *
   * @param GoogleCloudAiplatformV1RubricBasedInstructionFollowingInput $rubricBasedInstructionFollowingInput
   */
  public function setRubricBasedInstructionFollowingInput(GoogleCloudAiplatformV1RubricBasedInstructionFollowingInput $rubricBasedInstructionFollowingInput)
  {
    $this->rubricBasedInstructionFollowingInput = $rubricBasedInstructionFollowingInput;
  }
  /**
   * @return GoogleCloudAiplatformV1RubricBasedInstructionFollowingInput
   */
  public function getRubricBasedInstructionFollowingInput()
  {
    return $this->rubricBasedInstructionFollowingInput;
  }
  /**
   * Input for safety metric.
   *
   * @param GoogleCloudAiplatformV1SafetyInput $safetyInput
   */
  public function setSafetyInput(GoogleCloudAiplatformV1SafetyInput $safetyInput)
  {
    $this->safetyInput = $safetyInput;
  }
  /**
   * @return GoogleCloudAiplatformV1SafetyInput
   */
  public function getSafetyInput()
  {
    return $this->safetyInput;
  }
  /**
   * Input for summarization helpfulness metric.
   *
   * @param GoogleCloudAiplatformV1SummarizationHelpfulnessInput $summarizationHelpfulnessInput
   */
  public function setSummarizationHelpfulnessInput(GoogleCloudAiplatformV1SummarizationHelpfulnessInput $summarizationHelpfulnessInput)
  {
    $this->summarizationHelpfulnessInput = $summarizationHelpfulnessInput;
  }
  /**
   * @return GoogleCloudAiplatformV1SummarizationHelpfulnessInput
   */
  public function getSummarizationHelpfulnessInput()
  {
    return $this->summarizationHelpfulnessInput;
  }
  /**
   * Input for summarization quality metric.
   *
   * @param GoogleCloudAiplatformV1SummarizationQualityInput $summarizationQualityInput
   */
  public function setSummarizationQualityInput(GoogleCloudAiplatformV1SummarizationQualityInput $summarizationQualityInput)
  {
    $this->summarizationQualityInput = $summarizationQualityInput;
  }
  /**
   * @return GoogleCloudAiplatformV1SummarizationQualityInput
   */
  public function getSummarizationQualityInput()
  {
    return $this->summarizationQualityInput;
  }
  /**
   * Input for summarization verbosity metric.
   *
   * @param GoogleCloudAiplatformV1SummarizationVerbosityInput $summarizationVerbosityInput
   */
  public function setSummarizationVerbosityInput(GoogleCloudAiplatformV1SummarizationVerbosityInput $summarizationVerbosityInput)
  {
    $this->summarizationVerbosityInput = $summarizationVerbosityInput;
  }
  /**
   * @return GoogleCloudAiplatformV1SummarizationVerbosityInput
   */
  public function getSummarizationVerbosityInput()
  {
    return $this->summarizationVerbosityInput;
  }
  /**
   * Tool call metric instances. Input for tool call valid metric.
   *
   * @param GoogleCloudAiplatformV1ToolCallValidInput $toolCallValidInput
   */
  public function setToolCallValidInput(GoogleCloudAiplatformV1ToolCallValidInput $toolCallValidInput)
  {
    $this->toolCallValidInput = $toolCallValidInput;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolCallValidInput
   */
  public function getToolCallValidInput()
  {
    return $this->toolCallValidInput;
  }
  /**
   * Input for tool name match metric.
   *
   * @param GoogleCloudAiplatformV1ToolNameMatchInput $toolNameMatchInput
   */
  public function setToolNameMatchInput(GoogleCloudAiplatformV1ToolNameMatchInput $toolNameMatchInput)
  {
    $this->toolNameMatchInput = $toolNameMatchInput;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolNameMatchInput
   */
  public function getToolNameMatchInput()
  {
    return $this->toolNameMatchInput;
  }
  /**
   * Input for tool parameter key match metric.
   *
   * @param GoogleCloudAiplatformV1ToolParameterKeyMatchInput $toolParameterKeyMatchInput
   */
  public function setToolParameterKeyMatchInput(GoogleCloudAiplatformV1ToolParameterKeyMatchInput $toolParameterKeyMatchInput)
  {
    $this->toolParameterKeyMatchInput = $toolParameterKeyMatchInput;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolParameterKeyMatchInput
   */
  public function getToolParameterKeyMatchInput()
  {
    return $this->toolParameterKeyMatchInput;
  }
  /**
   * Input for tool parameter key value match metric.
   *
   * @param GoogleCloudAiplatformV1ToolParameterKVMatchInput $toolParameterKvMatchInput
   */
  public function setToolParameterKvMatchInput(GoogleCloudAiplatformV1ToolParameterKVMatchInput $toolParameterKvMatchInput)
  {
    $this->toolParameterKvMatchInput = $toolParameterKvMatchInput;
  }
  /**
   * @return GoogleCloudAiplatformV1ToolParameterKVMatchInput
   */
  public function getToolParameterKvMatchInput()
  {
    return $this->toolParameterKvMatchInput;
  }
  /**
   * Input for trajectory match any order metric.
   *
   * @param GoogleCloudAiplatformV1TrajectoryAnyOrderMatchInput $trajectoryAnyOrderMatchInput
   */
  public function setTrajectoryAnyOrderMatchInput(GoogleCloudAiplatformV1TrajectoryAnyOrderMatchInput $trajectoryAnyOrderMatchInput)
  {
    $this->trajectoryAnyOrderMatchInput = $trajectoryAnyOrderMatchInput;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryAnyOrderMatchInput
   */
  public function getTrajectoryAnyOrderMatchInput()
  {
    return $this->trajectoryAnyOrderMatchInput;
  }
  /**
   * Input for trajectory exact match metric.
   *
   * @param GoogleCloudAiplatformV1TrajectoryExactMatchInput $trajectoryExactMatchInput
   */
  public function setTrajectoryExactMatchInput(GoogleCloudAiplatformV1TrajectoryExactMatchInput $trajectoryExactMatchInput)
  {
    $this->trajectoryExactMatchInput = $trajectoryExactMatchInput;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryExactMatchInput
   */
  public function getTrajectoryExactMatchInput()
  {
    return $this->trajectoryExactMatchInput;
  }
  /**
   * Input for trajectory in order match metric.
   *
   * @param GoogleCloudAiplatformV1TrajectoryInOrderMatchInput $trajectoryInOrderMatchInput
   */
  public function setTrajectoryInOrderMatchInput(GoogleCloudAiplatformV1TrajectoryInOrderMatchInput $trajectoryInOrderMatchInput)
  {
    $this->trajectoryInOrderMatchInput = $trajectoryInOrderMatchInput;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryInOrderMatchInput
   */
  public function getTrajectoryInOrderMatchInput()
  {
    return $this->trajectoryInOrderMatchInput;
  }
  /**
   * Input for trajectory precision metric.
   *
   * @param GoogleCloudAiplatformV1TrajectoryPrecisionInput $trajectoryPrecisionInput
   */
  public function setTrajectoryPrecisionInput(GoogleCloudAiplatformV1TrajectoryPrecisionInput $trajectoryPrecisionInput)
  {
    $this->trajectoryPrecisionInput = $trajectoryPrecisionInput;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryPrecisionInput
   */
  public function getTrajectoryPrecisionInput()
  {
    return $this->trajectoryPrecisionInput;
  }
  /**
   * Input for trajectory recall metric.
   *
   * @param GoogleCloudAiplatformV1TrajectoryRecallInput $trajectoryRecallInput
   */
  public function setTrajectoryRecallInput(GoogleCloudAiplatformV1TrajectoryRecallInput $trajectoryRecallInput)
  {
    $this->trajectoryRecallInput = $trajectoryRecallInput;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryRecallInput
   */
  public function getTrajectoryRecallInput()
  {
    return $this->trajectoryRecallInput;
  }
  /**
   * Input for trajectory single tool use metric.
   *
   * @param GoogleCloudAiplatformV1TrajectorySingleToolUseInput $trajectorySingleToolUseInput
   */
  public function setTrajectorySingleToolUseInput(GoogleCloudAiplatformV1TrajectorySingleToolUseInput $trajectorySingleToolUseInput)
  {
    $this->trajectorySingleToolUseInput = $trajectorySingleToolUseInput;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectorySingleToolUseInput
   */
  public function getTrajectorySingleToolUseInput()
  {
    return $this->trajectorySingleToolUseInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluateInstancesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluateInstancesRequest');
