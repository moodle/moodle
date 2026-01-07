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

class GoogleCloudAiplatformV1CustomCodeExecutionSpec extends \Google\Model
{
  /**
   * Required. Python function. Expected user to define the following function,
   * e.g.: def evaluate(instance: dict[str, Any]) -> float: Please include this
   * function signature in the code snippet. Instance is the evaluation
   * instance, any fields populated in the instance are available to the
   * function as instance[field_name]. Example: Example input: ``` instance=
   * EvaluationInstance( response=EvaluationInstance.InstanceData(text="The
   * answer is 4."), reference=EvaluationInstance.InstanceData(text="4") ) ```
   * Example converted input: ``` { 'response': {'text': 'The answer is 4.'},
   * 'reference': {'text': '4'} } ``` Example python function: ``` def
   * evaluate(instance: dict[str, Any]) -> float: if instance'response' ==
   * instance'reference': return 1.0 return 0.0 ``` CustomCodeExecutionSpec is
   * also supported in Batch Evaluation (EvalDataset RPC) and Tuning Evaluation.
   * Each line in the input jsonl file will be converted to dict[str, Any] and
   * passed to the evaluation function.
   *
   * @var string
   */
  public $evaluationFunction;

  /**
   * Required. Python function. Expected user to define the following function,
   * e.g.: def evaluate(instance: dict[str, Any]) -> float: Please include this
   * function signature in the code snippet. Instance is the evaluation
   * instance, any fields populated in the instance are available to the
   * function as instance[field_name]. Example: Example input: ``` instance=
   * EvaluationInstance( response=EvaluationInstance.InstanceData(text="The
   * answer is 4."), reference=EvaluationInstance.InstanceData(text="4") ) ```
   * Example converted input: ``` { 'response': {'text': 'The answer is 4.'},
   * 'reference': {'text': '4'} } ``` Example python function: ``` def
   * evaluate(instance: dict[str, Any]) -> float: if instance'response' ==
   * instance'reference': return 1.0 return 0.0 ``` CustomCodeExecutionSpec is
   * also supported in Batch Evaluation (EvalDataset RPC) and Tuning Evaluation.
   * Each line in the input jsonl file will be converted to dict[str, Any] and
   * passed to the evaluation function.
   *
   * @param string $evaluationFunction
   */
  public function setEvaluationFunction($evaluationFunction)
  {
    $this->evaluationFunction = $evaluationFunction;
  }
  /**
   * @return string
   */
  public function getEvaluationFunction()
  {
    return $this->evaluationFunction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CustomCodeExecutionSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CustomCodeExecutionSpec');
