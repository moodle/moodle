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

class GoogleCloudAiplatformV1RubricVerdict extends \Google\Model
{
  protected $evaluatedRubricType = GoogleCloudAiplatformV1Rubric::class;
  protected $evaluatedRubricDataType = '';
  /**
   * Optional. Human-readable reasoning or explanation for the verdict. This can
   * include specific examples or details from the evaluated content that
   * justify the given verdict.
   *
   * @var string
   */
  public $reasoning;
  /**
   * Required. Outcome of the evaluation against the rubric, represented as a
   * boolean. `true` indicates a "Pass", `false` indicates a "Fail".
   *
   * @var bool
   */
  public $verdict;

  /**
   * Required. The full rubric definition that was evaluated. Storing this
   * ensures the verdict is self-contained and understandable, especially if the
   * original rubric definition changes or was dynamically generated.
   *
   * @param GoogleCloudAiplatformV1Rubric $evaluatedRubric
   */
  public function setEvaluatedRubric(GoogleCloudAiplatformV1Rubric $evaluatedRubric)
  {
    $this->evaluatedRubric = $evaluatedRubric;
  }
  /**
   * @return GoogleCloudAiplatformV1Rubric
   */
  public function getEvaluatedRubric()
  {
    return $this->evaluatedRubric;
  }
  /**
   * Optional. Human-readable reasoning or explanation for the verdict. This can
   * include specific examples or details from the evaluated content that
   * justify the given verdict.
   *
   * @param string $reasoning
   */
  public function setReasoning($reasoning)
  {
    $this->reasoning = $reasoning;
  }
  /**
   * @return string
   */
  public function getReasoning()
  {
    return $this->reasoning;
  }
  /**
   * Required. Outcome of the evaluation against the rubric, represented as a
   * boolean. `true` indicates a "Pass", `false` indicates a "Fail".
   *
   * @param bool $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return bool
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RubricVerdict::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RubricVerdict');
