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

class GoogleCloudAiplatformV1EvaluationInstance extends \Google\Model
{
  protected $agentDataType = GoogleCloudAiplatformV1EvaluationInstanceAgentData::class;
  protected $agentDataDataType = '';
  protected $otherDataType = GoogleCloudAiplatformV1EvaluationInstanceMapInstance::class;
  protected $otherDataDataType = '';
  protected $promptType = GoogleCloudAiplatformV1EvaluationInstanceInstanceData::class;
  protected $promptDataType = '';
  protected $referenceType = GoogleCloudAiplatformV1EvaluationInstanceInstanceData::class;
  protected $referenceDataType = '';
  protected $responseType = GoogleCloudAiplatformV1EvaluationInstanceInstanceData::class;
  protected $responseDataType = '';
  protected $rubricGroupsType = GoogleCloudAiplatformV1RubricGroup::class;
  protected $rubricGroupsDataType = 'map';

  /**
   * Optional. Data used for agent evaluation.
   *
   * @param GoogleCloudAiplatformV1EvaluationInstanceAgentData $agentData
   */
  public function setAgentData(GoogleCloudAiplatformV1EvaluationInstanceAgentData $agentData)
  {
    $this->agentData = $agentData;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationInstanceAgentData
   */
  public function getAgentData()
  {
    return $this->agentData;
  }
  /**
   * Optional. Other data used to populate placeholders based on their key.
   *
   * @param GoogleCloudAiplatformV1EvaluationInstanceMapInstance $otherData
   */
  public function setOtherData(GoogleCloudAiplatformV1EvaluationInstanceMapInstance $otherData)
  {
    $this->otherData = $otherData;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationInstanceMapInstance
   */
  public function getOtherData()
  {
    return $this->otherData;
  }
  /**
   * Optional. Data used to populate placeholder `prompt` in a metric prompt
   * template.
   *
   * @param GoogleCloudAiplatformV1EvaluationInstanceInstanceData $prompt
   */
  public function setPrompt(GoogleCloudAiplatformV1EvaluationInstanceInstanceData $prompt)
  {
    $this->prompt = $prompt;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationInstanceInstanceData
   */
  public function getPrompt()
  {
    return $this->prompt;
  }
  /**
   * Optional. Data used to populate placeholder `reference` in a metric prompt
   * template.
   *
   * @param GoogleCloudAiplatformV1EvaluationInstanceInstanceData $reference
   */
  public function setReference(GoogleCloudAiplatformV1EvaluationInstanceInstanceData $reference)
  {
    $this->reference = $reference;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationInstanceInstanceData
   */
  public function getReference()
  {
    return $this->reference;
  }
  /**
   * Optional. Data used to populate placeholder `response` in a metric prompt
   * template.
   *
   * @param GoogleCloudAiplatformV1EvaluationInstanceInstanceData $response
   */
  public function setResponse(GoogleCloudAiplatformV1EvaluationInstanceInstanceData $response)
  {
    $this->response = $response;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationInstanceInstanceData
   */
  public function getResponse()
  {
    return $this->response;
  }
  /**
   * Optional. Named groups of rubrics associated with the prompt. This is used
   * for rubric-based evaluations where rubrics can be referenced by a key. The
   * key could represent versions, associated metrics, etc.
   *
   * @param GoogleCloudAiplatformV1RubricGroup[] $rubricGroups
   */
  public function setRubricGroups($rubricGroups)
  {
    $this->rubricGroups = $rubricGroups;
  }
  /**
   * @return GoogleCloudAiplatformV1RubricGroup[]
   */
  public function getRubricGroups()
  {
    return $this->rubricGroups;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationInstance');
