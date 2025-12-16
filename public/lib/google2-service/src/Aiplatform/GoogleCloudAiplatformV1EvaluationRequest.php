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

class GoogleCloudAiplatformV1EvaluationRequest extends \Google\Collection
{
  protected $collection_key = 'candidateResponses';
  protected $candidateResponsesType = GoogleCloudAiplatformV1CandidateResponse::class;
  protected $candidateResponsesDataType = 'array';
  protected $goldenResponseType = GoogleCloudAiplatformV1CandidateResponse::class;
  protected $goldenResponseDataType = '';
  protected $promptType = GoogleCloudAiplatformV1EvaluationPrompt::class;
  protected $promptDataType = '';
  protected $rubricsType = GoogleCloudAiplatformV1RubricGroup::class;
  protected $rubricsDataType = 'map';

  /**
   * Optional. Responses from model under test and other baseline models for
   * comparison.
   *
   * @param GoogleCloudAiplatformV1CandidateResponse[] $candidateResponses
   */
  public function setCandidateResponses($candidateResponses)
  {
    $this->candidateResponses = $candidateResponses;
  }
  /**
   * @return GoogleCloudAiplatformV1CandidateResponse[]
   */
  public function getCandidateResponses()
  {
    return $this->candidateResponses;
  }
  /**
   * Optional. The Ideal response or ground truth.
   *
   * @param GoogleCloudAiplatformV1CandidateResponse $goldenResponse
   */
  public function setGoldenResponse(GoogleCloudAiplatformV1CandidateResponse $goldenResponse)
  {
    $this->goldenResponse = $goldenResponse;
  }
  /**
   * @return GoogleCloudAiplatformV1CandidateResponse
   */
  public function getGoldenResponse()
  {
    return $this->goldenResponse;
  }
  /**
   * Required. The request/prompt to evaluate.
   *
   * @param GoogleCloudAiplatformV1EvaluationPrompt $prompt
   */
  public function setPrompt(GoogleCloudAiplatformV1EvaluationPrompt $prompt)
  {
    $this->prompt = $prompt;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationPrompt
   */
  public function getPrompt()
  {
    return $this->prompt;
  }
  /**
   * Optional. Named groups of rubrics associated with this prompt. The key is a
   * user-defined name for the rubric group.
   *
   * @param GoogleCloudAiplatformV1RubricGroup[] $rubrics
   */
  public function setRubrics($rubrics)
  {
    $this->rubrics = $rubrics;
  }
  /**
   * @return GoogleCloudAiplatformV1RubricGroup[]
   */
  public function getRubrics()
  {
    return $this->rubrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRequest');
